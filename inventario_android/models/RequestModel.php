<?php
require_once __DIR__ . '/../config/db.php';

class RequestModel {
    private $db;
    
    // Propiedades correctas para Reportes/Solicitudes
    private $id;
    private $user_id;
    private $type;
    private $description;
    private $status;
    private $tool_id;

    public function __construct(){
        $this->db = Database::connect();
    }

    // Setters
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setType($type) { $this->type = $type; }
    public function setDescription($description) { $this->description = $this->db->real_escape_string($description); }
    public function setStatus($status) { $this->status = $status; }
    public function setToolId($tool_id) { $this->tool_id = $tool_id; }

    // =========================================================
    // 1. OBTENER TODO (PARA EL ADMIN)
    // =========================================================
    public function getAll(){
        // Usamos 'request_unique_id' para solucionar el problema de las notificaciones "undefined"
        $sql = "SELECT r.id AS request_unique_id, r.user_id, r.type, r.description, r.status, r.created_at, 
                       u.fullname, u.role 
                FROM requests r 
                INNER JOIN users u ON r.user_id = u.id 
                ORDER BY r.created_at DESC";
        return $this->db->query($sql);
    }

    // =========================================================
    // 2. OBTENER POR USUARIO (PARA EL PANEL DEL USER)
    // =========================================================
    public function getRequestsByUser($user_id){
        $sql = "SELECT * FROM requests WHERE user_id = {$user_id} ORDER BY created_at DESC";
        return $this->db->query($sql);
    }

    // =========================================================
    // 3. GUARDAR NUEVA SOLICITUD
    // =========================================================
    public function save(){
        $sql = "INSERT INTO requests (user_id, type, description, status, created_at) 
                VALUES ({$this->user_id}, '{$this->type}', '{$this->description}', 'PENDIENTE', NOW());";
        return $this->db->query($sql);
    }

    // =========================================================
    // 4. ACTUALIZAR ESTADO (APROBAR/RECHAZAR)
    // =========================================================
    public function updateStatus($id, $status){
        $id = (int)$id;
        $status = $this->db->real_escape_string($status);
        
        $sql = "UPDATE requests SET status='$status' WHERE id=$id";
        return $this->db->query($sql);
    }

    // =========================================================
    // 5. CONTAR PENDIENTES (PARA LA CAMPANITA)
    // =========================================================
    public function countPending(){
        $sql = "SELECT COUNT(*) as total FROM requests WHERE status = 'PENDIENTE'";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_object()->total : 0;
    }
}
?>
