<?php
require_once __DIR__ . '/../config/db.php';

class RequestModel {
    private $db;
    private $user_id;
    private $tool_id; // Nuevo
    private $type;
    private $description;
    private $quantity; // Nuevo

    public function __construct(){
        $this->db = Database::connect();
    }

    // Setters
    public function setUserId($user_id) { $this->user_id = (int)$user_id; }
    public function setToolId($tool_id) { $this->tool_id = (int)$tool_id; }
    public function setType($type) { $this->type = $this->db->real_escape_string($type); }
    public function setDescription($description) { $this->description = $this->db->real_escape_string($description); }
    public function setQuantity($qty) { $this->quantity = (int)$qty; }

    // Obtener TODAS las solicitudes (Para Admin) con JOIN a tools para ver el nombre real
    public function getAll(){
        $sql = "SELECT r.id AS request_unique_id, r.user_id, r.tool_id, r.type, r.description, r.status, r.created_at, r.quantity,
                       u.fullname, u.role,
                       t.name as tool_name, t.image as tool_image
                FROM requests r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tools t ON r.tool_id = t.id
                ORDER BY r.created_at DESC";
        return $this->db->query($sql);
    }

    // Obtener solicitudes de UN usuario
    public function getRequestsByUser($user_id){
        $sql = "SELECT r.*, t.name as tool_name 
                FROM requests r 
                LEFT JOIN tools t ON r.tool_id = t.id
                WHERE r.user_id = {$user_id} AND r.visible_user = 1 
                ORDER BY r.created_at DESC";
        return $this->db->query($sql);
    }

    // Obtener UNA solicitud específica (NECESARIO PARA HANDLE REQUEST)
    public function getOne($id){
        $id = (int)$id;
        $sql = "SELECT * FROM requests WHERE id = {$id}";
        $result = $this->db->query($sql);
        return $result->fetch_object();
    }

    // Guardar nueva solicitud
    public function save(){
        $toolId = $this->tool_id ? $this->tool_id : 'NULL';
        $qty = $this->quantity ? $this->quantity : 1;
        
        $sql = "INSERT INTO requests (user_id, tool_id, type, description, quantity, status, created_at, visible_user) 
                VALUES ({$this->user_id}, {$toolId}, '{$this->type}', '{$this->description}', {$qty}, 'PENDIENTE', NOW(), 1);";
        return $this->db->query($sql);
    }

    public function updateStatus($id, $status){
        $id = (int)$id;
        $status = $this->db->real_escape_string($status);
        $sql = "UPDATE requests SET status='$status' WHERE id=$id";
        return $this->db->query($sql);
    }

    public function countPending(){
        $sql = "SELECT COUNT(*) as total FROM requests WHERE status = 'PENDIENTE'";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_object()->total : 0;
    }

    public function hideFromUser($id, $user_id){
        $id = (int)$id;
        $user_id = (int)$user_id;
        $sql = "UPDATE requests SET visible_user = 0 WHERE id = $id AND user_id = $user_id";
        return $this->db->query($sql);
    }

    public function deletePermanently($id){
        $id = (int)$id;
        $sql = "DELETE FROM requests WHERE id = $id"; 
        return $this->db->query($sql);
    }
}
?>