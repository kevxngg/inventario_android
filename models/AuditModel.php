<?php
require_once 'config/db.php';

class AuditModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Función para registrar una acción silenciosamente
    public function logAction($user_id, $module, $action_type, $details) {
        $user_id = (int)$user_id;
        $module = $this->db->real_escape_string($module);
        $action_type = $this->db->real_escape_string($action_type);
        $details = $this->db->real_escape_string($details);
        $ip_address = $_SERVER['REMOTE_ADDR']; // Captura la IP desde donde se hizo el cambio

        // Configurar zona horaria de Colombia
        date_default_timezone_set('America/Bogota');
        $created_at = date('Y-m-d H:i:s');

        $sql = "INSERT INTO audit_logs (user_id, module, action_type, details, ip_address, created_at) 
                VALUES ($user_id, '$module', '$action_type', '$details', '$ip_address', '$created_at')";
        
        return $this->db->query($sql);
    }

    // Obtener todo el historial para mostrarlo al Administrador
    public function getAllLogs() {
        $sql = "SELECT a.*, u.fullname, u.role, u.image 
                FROM audit_logs a 
                INNER JOIN users u ON a.user_id = u.id 
                ORDER BY a.created_at DESC";
        return $this->db->query($sql);
    }
}
?>