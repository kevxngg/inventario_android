<?php
require_once 'config/db.php';

class ToolModel {
    private $id;
    private $name;
    private $category;
    private $status;
    private $image;
    
    // Propiedades para control de stock
    private $stock_total;
    private $stock_available;
    private $stock_min;
    
    private $db;

    public function __construct(){
        $this->db = Database::connect();
    }

    // --- SETTERS ---
    public function setName($name) { 
        $this->name = $this->db->real_escape_string($name); 
    }
    public function setCategory($category) { 
        $this->category = $this->db->real_escape_string($category); 
    }
    public function setStatus($status) { 
        $this->status = $this->db->real_escape_string($status); 
    }
    public function setImage($image) { 
        $this->image = $this->db->real_escape_string($image); 
    }
    
    // Setters numéricos
    public function setStockTotal($qty) { 
        $this->stock_total = (int)$qty; 
    }
    public function setStockAvailable($qty) { 
        $this->stock_available = (int)$qty; 
    }
    public function setStockMin($qty) { 
        $this->stock_min = (int)$qty; 
    }

    // --- MÉTODOS DE LECTURA ---

    public function getAll(){
        // Para el Admin: Trae absolutamente todo
        $sql = "SELECT * FROM tools ORDER BY stock_available DESC, name ASC";
        return $this->db->query($sql);
    }

    // --- CORRECCIÓN CRÍTICA AQUÍ ---
    public function getAllActive(){
        // Esta consulta asegura que se traigan los AGOTADOS para que el usuario los vea (aunque bloqueados)
        // Solo excluimos 'MANTENIMIENTO' si así lo deseas, o ítems eliminados.
        $sql = "SELECT * FROM tools 
                WHERE status IN ('DISPONIBLE', 'AGOTADO', 'EN_OBRA') 
                ORDER BY FIELD(status, 'DISPONIBLE', 'EN_OBRA', 'AGOTADO'), name ASC";
        
        return $this->db->query($sql);
    }

    public function getOne($id){
        $sql = "SELECT * FROM tools WHERE id = {$id}";
        $result = $this->db->query($sql);
        return $result->fetch_object();
    }

    // --- LÓGICA CORE: MOVIMIENTOS DE INVENTARIO (KARDEX) ---
    public function registerMovement($toolId, $userId, $type, $quantity, $referenceId = null, $comments = null) {
        $currentTool = $this->getOne($toolId);
        if (!$currentTool) return false;

        $stockBefore = (int)$currentTool->stock_available;
        $stockAfter = 0;
        $canProceed = false;

        switch ($type) {
            case 'ENTRADA':             
            case 'DEVOLUCION':          
            case 'AJUSTE_INVENTARIO':   
                $stockAfter = $stockBefore + $quantity;
                if($type == 'DEVOLUCION' && $stockAfter > $currentTool->stock_total){
                    $stockAfter = $currentTool->stock_total; 
                }
                $canProceed = true;
                break;

            case 'SALIDA_PRESTAMO':     
            case 'BAJA_DAÑO':           
                if ($stockBefore >= $quantity) {
                    $stockAfter = $stockBefore - $quantity;
                    $canProceed = true;
                } else {
                    return false; 
                }
                break;
        }

        if ($canProceed) {
            // Actualizar tabla tools
            // LÓGICA AUTOMÁTICA DE ESTADO:
            // Si llega a 0, forzamos AGOTADO. Si sube de 0, vuelve a DISPONIBLE.
            // Respetamos MANTENIMIENTO si ya estaba en ese estado.
            
            $newStatus = 'DISPONIBLE';
            if($stockAfter == 0) $newStatus = 'AGOTADO';
            
            if($currentTool->status == 'MANTENIMIENTO') $newStatus = 'MANTENIMIENTO';

            $updateSql = "UPDATE tools SET 
                          stock_available = {$stockAfter}, 
                          status = '$newStatus' 
                          WHERE id = {$toolId}";
            
            $update = $this->db->query($updateSql);

            if ($update) {
                $refId = $referenceId ? $referenceId : 'NULL';
                $comms = $comments ? "'".$this->db->real_escape_string($comments)."'" : 'NULL';
                
                $kardexSql = "INSERT INTO inventory_movements 
                              (tool_id, user_id, type, quantity, stock_before, stock_after, reference_id, comments, created_at) 
                              VALUES ({$toolId}, {$userId}, '{$type}', {$quantity}, {$stockBefore}, {$stockAfter}, {$refId}, {$comms}, NOW())";
                
                $this->db->query($kardexSql);
                return true;
            }
        }
        return false;
    }

    // --- MÉTODOS CRUD ---

    public function save(){
        $sql = "INSERT INTO tools (name, description, category, status, stock_total, stock_available, stock_min, image) 
                VALUES('{$this->name}', 'Descripción pendiente', '{$this->category}', '{$this->status}', {$this->stock_total}, {$this->stock_available}, {$this->stock_min}, '{$this->image}');";
        return $this->db->query($sql);
    }

    public function update($id){
        // Lógica para recalcular disponible si el admin edita el total manualmente
        $oldTool = $this->getOne($id);
        
        $difference = $this->stock_total - $oldTool->stock_total;
        $newAvailable = $oldTool->stock_available + $difference;
        
        // Protección contra negativos
        if($newAvailable < 0) $newAvailable = 0;

        // Auto-actualización de estado según el nuevo stock disponible
        $newStatus = $this->status; // El que viene del select del formulario
        
        // Si el admin no lo puso en mantenimiento manualmente, el sistema decide:
        if($newStatus != 'MANTENIMIENTO') {
            if($newAvailable == 0) {
                $newStatus = 'AGOTADO';
            } else {
                $newStatus = 'DISPONIBLE';
            }
        }

        $sql = "UPDATE tools SET 
                name='{$this->name}', 
                category='{$this->category}', 
                stock_total={$this->stock_total}, 
                stock_available={$newAvailable}, 
                stock_min={$this->stock_min},
                status='{$newStatus}'
                WHERE id={$id}";
        
        if($this->image != null){
            $this->db->query("UPDATE tools SET image='{$this->image}' WHERE id={$id}");
        }
        
        return $this->db->query($sql);
    }

    public function updateStatus($id, $status){
        $sql = "UPDATE tools SET status = '$status' WHERE id = $id";
        return $this->db->query($sql);
    }

    public function delete($id){
        $this->db->query("DELETE FROM assignments WHERE tool_id = {$id}");
        $this->db->query("DELETE FROM inventory_movements WHERE tool_id = {$id}");
        $sql = "DELETE FROM tools WHERE id = {$id}";
        return $this->db->query($sql);
    }
    
    // --- MÉTRICAS ---
    public function countAll(){
        $sql = "SELECT SUM(stock_total) as total FROM tools";
        $result = $this->db->query($sql);
        $data = $result->fetch_object();
        return $data->total ?? 0;
    }
    public function countAvailable(){
        $sql = "SELECT SUM(stock_available) as total FROM tools";
        $result = $this->db->query($sql);
        $data = $result->fetch_object();
        return $data->total ?? 0;
    }
    public function countInUse(){
        $sql = "SELECT SUM(stock_total - stock_available) as total FROM tools";
        $result = $this->db->query($sql);
        $data = $result->fetch_object();
        return $data->total ?? 0;
    }
    public function countMaintenance(){
        $sql = "SELECT COUNT(*) as total FROM tools WHERE status = 'MANTENIMIENTO'";
        $result = $this->db->query($sql);
        return $result->fetch_object()->total ?? 0;
    }
    public function getLowStockAlerts(){
        $sql = "SELECT * FROM tools WHERE stock_available <= stock_min";
        return $this->db->query($sql);
    }
    public function getStatsByCategory(){
        $sql = "SELECT category, COUNT(*) as total FROM tools GROUP BY category";
        return $this->db->query($sql);
    }
}
?>