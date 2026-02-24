<?php
require_once __DIR__ . '/../config/db.php';

class AssignmentModel {
    private $db;

    public function __construct(){
        $this->db = Database::connect();
    }

    // --- MÉTODOS DE LECTURA ---

    public function getOne($id){
        $sql = "SELECT * FROM assignments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $assignment = $result->fetch_object();
        $stmt->close();
        return $assignment;
    }

    public function getToolsByUser($user_id){
        $sql = "SELECT a.*, t.name, t.image, t.category 
                FROM assignments a 
                INNER JOIN tools t ON a.tool_id = t.id 
                WHERE a.user_id = ? AND a.status = 'ACTIVO'
                ORDER BY a.assigned_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
    
    // --- MÉTODOS DE ESCRITURA Y CONTROL ---

    public function assign($user_id, $tool_id, $quantity){
        $sql = "INSERT INTO assignments (user_id, tool_id, quantity, status) VALUES (?, ?, ?, 'ACTIVO')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iii", $user_id, $tool_id, $quantity);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function createAssignment($tool_id, $user_id, $quantity, $project_id = null, $request_id = null){
        $sql = "INSERT INTO assignments (tool_id, user_id, quantity, project_id, status) VALUES (?, ?, ?, ?, 'ACTIVO')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiii", $tool_id, $user_id, $quantity, $project_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function markAsReturned($id, $condition){
        $sql = "UPDATE assignments SET status = 'DEVUELTO', return_condition = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $condition, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
?>