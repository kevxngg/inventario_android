<?php
require_once 'config/db.php';

class ToolModel {
    private $id;
    private $name;
    private $category;
    private $status;
    private $image;
    private $db;

    public function __construct(){
        $this->db = Database::connect();
    }

    // Setters
    public function setName($name) { $this->name = $this->db->real_escape_string($name); }
    public function setCategory($category) { $this->category = $this->db->real_escape_string($category); }
    public function setStatus($status) { $this->status = $this->db->real_escape_string($status); }
    public function setImage($image) { $this->image = $this->db->real_escape_string($image); }

    // 1. Obtener TODAS
    public function getAll(){
        $sql = "SELECT * FROM tools ORDER BY id DESC";
        $tools = $this->db->query($sql);
        return $tools;
    }

    // 2. Obtener SOLO DISPONIBLES
    public function getAllActive(){
        $sql = "SELECT * FROM tools WHERE status = 'DISPONIBLE' ORDER BY name ASC";
        $tools = $this->db->query($sql);
        return $tools;
    }

    // 3. Guardar Nueva Herramienta
    public function save(){
        $sql = "INSERT INTO tools VALUES(NULL, NULL, '{$this->name}', 'Descripción pendiente', '{$this->category}', '{$this->status}', CURDATE(), '{$this->image}', 1);";
        $save = $this->db->query($sql);
        return $save;
    }

    // 4. Cambiar Estado
    public function updateStatus($id, $status){
        $sql = "UPDATE tools SET status = '$status' WHERE id = $id";
        return $this->db->query($sql);
    }
    
    // 5. Contar TOTAL
    public function countAll(){
        $sql = "SELECT COUNT(*) as total FROM tools";
        $result = $this->db->query($sql);
        return $result->fetch_object()->total;
    }

    // 6. Contar MANTENIMIENTO
    public function countMaintenance(){
        $sql = "SELECT COUNT(*) as total FROM tools WHERE status = 'MANTENIMIENTO'";
        $result = $this->db->query($sql);
        return $result->fetch_object()->total;
    }

    // 7. Contar DISPONIBLES
    public function countAvailable(){
        $sql = "SELECT COUNT(*) as total FROM tools WHERE status = 'DISPONIBLE'";
        $result = $this->db->query($sql);
        return $result->fetch_object()->total;
    }

    // --- NUEVAS FUNCIONES PARA EDITAR Y ELIMINAR ---

    // 8. Obtener UNA sola herramienta por ID
    public function getOne($id){
        $sql = "SELECT * FROM tools WHERE id = {$id}";
        $tool = $this->db->query($sql);
        return $tool->fetch_object();
    }

    // 9. Eliminar herramienta (CORREGIDO: Borra historial primero)
    public function delete($id){
        // PASO 1: Eliminar solicitudes relacionadas en la tabla 'requests'
        $sql_requests = "DELETE FROM requests WHERE tool_id = {$id}";
        $this->db->query($sql_requests);

        // PASO 2: Ahora sí, eliminar la herramienta de la tabla 'tools'
        $sql = "DELETE FROM tools WHERE id = {$id}";
        return $this->db->query($sql);
    }

    // 10. Actualizar herramienta existente
    public function update($id){
        if($this->image != null){
            $sql = "UPDATE tools SET name='{$this->name}', category='{$this->category}', status='{$this->status}', image='{$this->image}' WHERE id={$id}";
        } else {
            $sql = "UPDATE tools SET name='{$this->name}', category='{$this->category}', status='{$this->status}' WHERE id={$id}";
        }
        return $this->db->query($sql);
    }
}
?>