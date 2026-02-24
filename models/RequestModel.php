<?php
require_once __DIR__ . '/../config/db.php';

class RequestModel {
    private $db;
    
    private $user_id;
    private $tool_id;
    private $project_id; 
    private $type;
    private $description;
    private $quantity;
    
    // --- NUEVAS PROPIEDADES LOGÍSTICAS ---
    private $expected_date;
    private $return_date;
    private $order_notes;

    public function __construct(){
        $this->db = Database::connect();
    }

    // --- SETTERS ORIGINALES ---
    public function setUserId($user_id) { $this->user_id = (int)$user_id; }
    public function setToolId($tool_id) { $this->tool_id = (int)$tool_id; }
    public function setProjectId($project_id) { $this->project_id = (int)$project_id; }
    public function setType($type) { $this->type = trim($type); }
    public function setDescription($description) { $this->description = trim($description); }
    public function setQuantity($qty) { $this->quantity = (int)$qty; }

    // --- NUEVOS SETTERS LOGÍSTICOS ---
    public function setExpectedDate($date) { $this->expected_date = !empty($date) ? trim($date) : null; }
    public function setReturnDate($date) { $this->return_date = !empty($date) ? trim($date) : null; }
    public function setOrderNotes($notes) { $this->order_notes = !empty($notes) ? trim($notes) : null; }

    // --- MÉTODOS DE LECTURA ---
    
    public function getAll(){
        $sql = "SELECT r.id AS request_unique_id, r.user_id, r.tool_id, r.project_id, r.type, r.description, r.admin_reply, r.status, 
                       r.expected_date, r.return_date, r.order_notes, r.created_at, r.quantity,
                       u.fullname, u.role,
                       t.name as tool_name, t.image as tool_image
                FROM requests r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tools t ON r.tool_id = t.id
                ORDER BY r.created_at DESC";
        return $this->db->query($sql);
    }

    public function getRequestsByUser($user_id){
        $sql = "SELECT r.*, t.name as tool_name 
                FROM requests r 
                LEFT JOIN tools t ON r.tool_id = t.id
                WHERE r.user_id = ? AND r.visible_user = 1 
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getOne($id){
        $sql = "SELECT r.*, u.fullname, u.email FROM requests r JOIN users u ON r.user_id = u.id WHERE r.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_object();
        $stmt->close();
        return $request;
    }

    // ====================================================================
    // MÉTODOS AVANZADOS PARA EL CHAT DE SOPORTE (ESTILO WHATSAPP)
    // ====================================================================
    
    // Obtener mensajes filtrando los que hayan sido eliminados por este rol
    public function getChatMessages($request_id, $role = 'ADMIN') {
        $filter = ($role == 'USER') ? "AND c.deleted_by_user = 0" : "AND c.deleted_by_admin = 0";
        
        $sql = "SELECT c.*, u.fullname, u.role, u.image 
                FROM ticket_chat c 
                INNER JOIN users u ON c.sender_id = u.id 
                WHERE c.request_id = ? $filter 
                ORDER BY c.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    // Marca como leídos los mensajes donde el lector NO fue el remitente
    public function markMessagesAsRead($request_id, $reader_id) {
        $sql = "UPDATE ticket_chat SET is_read = 1 WHERE request_id = ? AND sender_id != ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $request_id, $reader_id);
        $stmt->execute();
        $stmt->close();
    }

    // Borrado independiente de chat
    public function clearChat($request_id, $role) {
        $field = ($role == 'USER') ? 'deleted_by_user' : 'deleted_by_admin';
        $sql = "UPDATE ticket_chat SET $field = 1 WHERE request_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $result = $stmt->execute();
        $stmt->close();
        
        // Optimización: Si ambos lo borraron, se elimina definitivamente para no ocupar espacio
        $this->db->query("DELETE FROM ticket_chat WHERE deleted_by_user = 1 AND deleted_by_admin = 1");
        
        return $result;
    }

    // Obtener cantidad de mensajes no leídos (Globo de notificación)
    public function getUnreadCount($request_id, $user_id) {
        $sql = "SELECT COUNT(*) as unread FROM ticket_chat WHERE request_id = ? AND sender_id != ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $request_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_object()->unread;
        $stmt->close();
        return $count;
    }

    public function saveChatMessage($request_id, $sender_id, $message) {
        $sql = "INSERT INTO ticket_chat (request_id, sender_id, message, is_read, deleted_by_user, deleted_by_admin, created_at) 
                VALUES (?, ?, ?, 0, 0, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $request_id, $sender_id, $message);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // --- MÉTODOS DE ESCRITURA ---

    public function save(){
        $sql = "INSERT INTO requests (user_id, tool_id, project_id, type, description, quantity, status, expected_date, return_date, order_notes, created_at, visible_user) 
                VALUES (?, ?, ?, ?, ?, ?, 'PENDIENTE', ?, ?, ?, NOW(), 1)";
        $stmt = $this->db->prepare($sql);
        
        $tId = $this->tool_id ?: null;
        $pId = $this->project_id ?: null;
        $qty = $this->quantity ?: 1;
        
        $stmt->bind_param("iiississs", 
            $this->user_id, 
            $tId, 
            $pId, 
            $this->type, 
            $this->description, 
            $qty,
            $this->expected_date,
            $this->return_date,
            $this->order_notes
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function solveIncident($id, $reply) {
        $sql = "UPDATE requests SET admin_reply = ?, status = 'RESUELTO' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $reply, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateStatus($id, $status){
        $sql = "UPDATE requests SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function hideFromUser($id, $user_id){
        $sql = "UPDATE requests SET visible_user = 0 WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function deletePermanently($id){
        $sql = "DELETE FROM requests WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function countPending(){
        $sql = "SELECT COUNT(*) as total FROM requests WHERE status = 'PENDIENTE'";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_object()->total : 0;
    }
}
?>