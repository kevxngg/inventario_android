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

    // --- SETTERS (Limpios, la seguridad la da prepare statement) ---
    public function setName($name) { 
        $this->name = trim($name); 
    }
    public function setCategory($category) { 
        $this->category = trim($category); 
    }
    public function setStatus($status) { 
        $this->status = trim($status); 
    }
    public function setImage($image) { 
        $this->image = trim($image); 
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

    public function getAllActive(){
        // Esta consulta asegura que se traigan los AGOTADOS para que el usuario los vea (aunque bloqueados)
        $sql = "SELECT * FROM tools 
                WHERE status IN ('DISPONIBLE', 'AGOTADO', 'EN_OBRA') 
                ORDER BY FIELD(status, 'DISPONIBLE', 'EN_OBRA', 'AGOTADO'), name ASC";
        
        return $this->db->query($sql);
    }

    public function getOne($id){
        $sql = "SELECT * FROM tools WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tool = $result->fetch_object();
        $stmt->close();
        return $tool;
    }

    // --- LÓGICA CORE: MOVIMIENTOS DE INVENTARIO (KARDEX BLINDADO CON TRANSACCIONES) ---
    public function registerMovement($toolId, $userId, $type, $quantity, $referenceId = null, $comments = null) {
        // Iniciamos la transacción para evitar datos a medias
        $this->db->begin_transaction();

        try {
            // Bloqueamos la fila (FOR UPDATE) para que nadie más la toque mientras se actualiza el stock
            $stmtLock = $this->db->prepare("SELECT stock_total, stock_available, status FROM tools WHERE id = ? FOR UPDATE");
            $stmtLock->bind_param("i", $toolId);
            $stmtLock->execute();
            $resLock = $stmtLock->get_result();

            if ($resLock->num_rows === 0) {
                throw new Exception("Herramienta no encontrada");
            }

            $currentTool = $resLock->fetch_object();
            $stmtLock->close();

            $stockBefore = (int)$currentTool->stock_available;
            $stockTotal = (int)$currentTool->stock_total;
            $stockAfter = 0;
            $canProceed = false;

            switch ($type) {
                case 'ENTRADA':             
                case 'DEVOLUCION':          
                case 'AJUSTE_INVENTARIO':   
                    $stockAfter = $stockBefore + $quantity;
                    if($type == 'DEVOLUCION' && $stockAfter > $stockTotal){
                        $stockAfter = $stockTotal; 
                    }
                    $canProceed = true;
                    break;

                case 'SALIDA_PRESTAMO':     
                case 'BAJA_DAÑO':           
                    if ($stockBefore >= $quantity) {
                        $stockAfter = $stockBefore - $quantity;
                        $canProceed = true;
                    } else {
                        throw new Exception("Stock insuficiente"); 
                    }
                    break;
            }

            if ($canProceed) {
                // LÓGICA AUTOMÁTICA DE ESTADO
                $newStatus = 'DISPONIBLE';
                if($stockAfter == 0) $newStatus = 'AGOTADO';
                if($currentTool->status == 'MANTENIMIENTO') $newStatus = 'MANTENIMIENTO';

                // 1. Actualizar Inventario (Tools)
                $stmtUpdate = $this->db->prepare("UPDATE tools SET stock_available = ?, status = ? WHERE id = ?");
                $stmtUpdate->bind_param("isi", $stockAfter, $newStatus, $toolId);
                $stmtUpdate->execute();
                $stmtUpdate->close();

                // 2. Insertar en Kardex (Inventory Movements)
                $refId = $referenceId ? $referenceId : null;
                $stmtMove = $this->db->prepare("INSERT INTO inventory_movements 
                              (tool_id, user_id, type, quantity, stock_before, stock_after, reference_id, comments, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                
                // Tipos: i (int), s (string) -> iisiiiis (8 parámetros)
                $stmtMove->bind_param("iisiiiis", $toolId, $userId, $type, $quantity, $stockBefore, $stockAfter, $refId, $comments);
                $stmtMove->execute();
                $stmtMove->close();

                // Confirmar transacción si todo salió perfecto
                $this->db->commit();
                return true;
            }
            
            return false;

        } catch (Exception $e) {
            // Revertir todos los cambios si hubo un error (Ej: stock insuficiente)
            $this->db->rollback();
            return false;
        }
    }

    // --- MÉTODOS CRUD BLINDADOS ---

    public function save(){
        $sql = "INSERT INTO tools (name, description, category, status, stock_total, stock_available, stock_min, image) 
                VALUES(?, 'Descripción pendiente', ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssiiis", $this->name, $this->category, $this->status, $this->stock_total, $this->stock_available, $this->stock_min, $this->image);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function update($id){
        // Lógica para recalcular disponible si el admin edita el total manualmente
        $oldTool = $this->getOne($id);
        
        $difference = $this->stock_total - $oldTool->stock_total;
        $newAvailable = $oldTool->stock_available + $difference;
        
        // Protección contra negativos
        if($newAvailable < 0) $newAvailable = 0;

        // Auto-actualización de estado según el nuevo stock disponible
        $newStatus = $this->status; 
        
        if($newStatus != 'MANTENIMIENTO') {
            if($newAvailable == 0) {
                $newStatus = 'AGOTADO';
            } else {
                $newStatus = 'DISPONIBLE';
            }
        }

        // Actualizamos los datos (sin imagen)
        $sql = "UPDATE tools SET name=?, category=?, stock_total=?, stock_available=?, stock_min=?, status=? WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssiiisi", $this->name, $this->category, $this->stock_total, $newAvailable, $this->stock_min, $newStatus, $id);
        $res = $stmt->execute();
        $stmt->close();
        
        // Actualizamos la imagen por separado solo si se subió una nueva
        if($this->image != null){
            $stmtImg = $this->db->prepare("UPDATE tools SET image=? WHERE id=?");
            $stmtImg->bind_param("si", $this->image, $id);
            $stmtImg->execute();
            $stmtImg->close();
        }
        
        return $res;
    }

    public function updateStatus($id, $status){
        $sql = "UPDATE tools SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function delete($id){
        // Borramos en cascada manual asegurando protección (Assignments)
        $stmt1 = $this->db->prepare("DELETE FROM assignments WHERE tool_id = ?");
        $stmt1->bind_param("i", $id);
        $stmt1->execute();
        $stmt1->close();

        // Borramos en cascada manual (Movements)
        $stmt2 = $this->db->prepare("DELETE FROM inventory_movements WHERE tool_id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $stmt2->close();

        // Borramos la herramienta
        $stmt3 = $this->db->prepare("DELETE FROM tools WHERE id = ?");
        $stmt3->bind_param("i", $id);
        $res = $stmt3->execute();
        $stmt3->close();
        
        return $res;
    }
    
    // --- MÉTRICAS (Safe Queries) ---
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

    // ====================================================================
    // NUEVO MÉTODO: OBTENER EL KARDEX (HISTORIAL DE VIDA) DE LA HERRAMIENTA
    // ====================================================================
    public function getToolHistory($toolId) {
        $sql = "SELECT m.*, u.fullname, u.role
                FROM inventory_movements m
                LEFT JOIN users u ON m.user_id = u.id
                WHERE m.tool_id = ?
                ORDER BY m.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $toolId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
?>