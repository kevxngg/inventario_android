<?php
require_once 'config/db.php';

class AssignmentModel {
    private $db;

    public function __construct(){
        $this->db = Database::connect();
    }

    // Obtener herramientas asignadas a un usuario (ACTIVAS)
    public function getToolsByUser($user_id){
        // Hacemos JOIN con la tabla tools para traer el nombre y la foto
        $sql = "SELECT a.*, t.name, t.image, t.category 
                FROM assignments a 
                INNER JOIN tools t ON a.tool_id = t.id 
                WHERE a.user_id = {$user_id} AND a.status = 'ACTIVO'
                ORDER BY a.assigned_at DESC";
        return $this->db->query($sql);
    }
    
    // Asignar herramienta (Para el futuro módulo Admin)
    public function assign($user_id, $tool_id, $quantity){
        $sql = "INSERT INTO assignments (user_id, tool_id, quantity) VALUES ({$user_id}, {$tool_id}, {$quantity})";
        return $this->db->query($sql);
    }
}
?>