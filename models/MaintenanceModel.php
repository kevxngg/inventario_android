<?php
require_once __DIR__ . '/../config/db.php';

class MaintenanceModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getActive() {
        $sql = "SELECT m.*, t.name as tool_name, t.image, t.stock_total, u.fullname as user_name 
                FROM maintenance_logs m 
                INNER JOIN tools t ON m.tool_id = t.id 
                LEFT JOIN users u ON m.user_id = u.id 
                WHERE m.status = 'EN_TALLER'
                ORDER BY m.start_date DESC";
        return $this->db->query($sql);
    }

    public function getHistory() {
        $sql = "SELECT m.*, t.name as tool_name, t.image, u.fullname as user_name 
                FROM maintenance_logs m 
                INNER JOIN tools t ON m.tool_id = t.id 
                LEFT JOIN users u ON m.user_id = u.id 
                WHERE m.status IN ('REPARADO', 'IRREPARABLE')
                ORDER BY m.end_date DESC";
        return $this->db->query($sql);
    }

    public function getOne($id) {
        $sql = "SELECT * FROM maintenance_logs WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_object();
        $stmt->close();
        return $data;
    }

    public function create($tool_id, $user_id, $issue_description) {
        $sql = "INSERT INTO maintenance_logs (tool_id, user_id, issue_description, status, start_date) VALUES (?, ?, ?, 'EN_TALLER', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $tool_id, $user_id, $issue_description);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function finishRepair($id, $cost) {
        $sql = "UPDATE maintenance_logs SET status = 'REPARADO', repair_cost = ?, end_date = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("di", $cost, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function markIrreparable($id) {
        $sql = "UPDATE maintenance_logs SET status = 'IRREPARABLE', end_date = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getTotalCost() {
        $sql = "SELECT SUM(repair_cost) as total FROM maintenance_logs WHERE status = 'REPARADO'";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_object()->total : 0;
    }
    
    public function getCountActive() {
        $sql = "SELECT COUNT(*) as total FROM maintenance_logs WHERE status = 'EN_TALLER'";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_object()->total : 0;
    }
}
?>