<?php
require_once __DIR__ . '/../config/db.php';

class ProjectModel {
    private $db;
    
    private $id;
    private $name;
    private $description;
    private $location; // Usaremos 'location' que acabas de crear
    private $lat;
    private $lng;
    private $status;
    private $image;

    public function __construct(){
        $this->db = Database::connect();
    }

    // Setters
    public function setName($name) { $this->name = $this->db->real_escape_string($name); }
    public function setDescription($description) { $this->description = $this->db->real_escape_string($description); }
    
    // Setter estándar
    public function setLocation($location) { $this->location = $this->db->real_escape_string($location); }
    
    public function setLat($lat) { $this->lat = $lat; }
    public function setLng($lng) { $this->lng = $lng; }
    public function setStatus($status) { $this->status = $this->db->real_escape_string($status); }
    public function setImage($image) { $this->image = $image; }

    // --- MÉTODOS ---

    public function getAll(){
        $sql = "SELECT * FROM projects ORDER BY id DESC";
        return $this->db->query($sql);
    }

    public function save(){
        // Ahora sí guardamos en 'location'
        $sql = "INSERT INTO projects (name, description, location, lat, lng, status, image, created_at) 
                VALUES ('{$this->name}', '{$this->description}', '{$this->location}', '{$this->lat}', '{$this->lng}', '{$this->status}', '{$this->image}', NOW())";
        return $this->db->query($sql);
    }

    public function delete($id){
        $sql = "DELETE FROM projects WHERE id = {$id}";
        return $this->db->query($sql);
    }

    public function countActive(){
        $sql = "SELECT COUNT(*) as total FROM projects WHERE status = 'ACTIVO' OR status = 'EN_PROGRESO'";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_object()->total : 0;
    }
}
?>