<?php
require_once __DIR__ . '/../config/db.php';

class ProjectModel {
    private $db;
    
    // Propiedades
    private $id;
    private $name;
    private $description;
    private $location; 
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

    public function getOne($id){
        $sql = "SELECT * FROM projects WHERE id = {$id}";
        $result = $this->db->query($sql);
        return $result->fetch_object();
    }

    public function save(){
        $sql = "INSERT INTO projects (name, description, location, lat, lng, status, image) 
                VALUES ('{$this->name}', '{$this->description}', '{$this->location}', '{$this->lat}', '{$this->lng}', '{$this->status}', '{$this->image}')";
        return $this->db->query($sql);
    }

    // NUEVO MÉTODO: ACTUALIZAR
    public function update($id){
        // Si hay coordenadas nuevas, actualizamos todo
        if($this->lat != null && $this->lng != null){
            $sql = "UPDATE projects SET name='{$this->name}', location='{$this->location}', lat='{$this->lat}', lng='{$this->lng}', status='{$this->status}' WHERE id={$id}";
        } else {
            // Si solo cambiamos texto y estado, mantenemos coordenadas
            $sql = "UPDATE projects SET name='{$this->name}', location='{$this->location}', status='{$this->status}' WHERE id={$id}";
        }
        return $this->db->query($sql);
    }

    public function delete($id){
        $sql = "DELETE FROM projects WHERE id = {$id}";
        return $this->db->query($sql);
    }

    public function countActive(){
        $sql = "SELECT COUNT(*) as total FROM projects WHERE status = 'EN_EJECUCION' OR status = 'PLANIFICACION'";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_object()->total : 0;
    }
}
?>