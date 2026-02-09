<?php
require_once __DIR__ . '/../config/db.php';

class ProjectModel {
    private $db;
    
    // Propiedades
    private $id;
    private $name;
    private $description;
    private $address; // CAMBIO: Antes decía location
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
    
    // CAMBIO: SetAddress en vez de SetLocation
    public function setAddress($address) { $this->address = $this->db->real_escape_string($address); }
    
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
        // CORRECCIÓN DEL ERROR FATAL:
        // Cambiamos 'location' por 'address' en la consulta SQL
        $sql = "INSERT INTO projects (name, description, address, lat, lng, status, image, created_at) 
                VALUES ('{$this->name}', '{$this->description}', '{$this->address}', '{$this->lat}', '{$this->lng}', '{$this->status}', '{$this->image}', NOW())";
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