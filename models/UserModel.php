<?php
require_once 'config/db.php';

class UserModel {
    private $db;
    private $id;
    private $fullname;
    private $email;
    private $password;
    private $role;
    private $company;

    public function __construct(){
        $this->db = Database::connect();
    }

    // SETTERS
    public function setId($id) { $this->id = $id; }
    public function setFullname($fullname) { $this->fullname = $this->db->real_escape_string(trim($fullname)); }
    public function setEmail($email) { $this->email = $this->db->real_escape_string(trim($email)); }
    public function setPassword($password) { $this->password = trim($password); }
    public function setRole($role) { $this->role = $this->db->real_escape_string($role); }
    public function setCompany($company) { $this->company = $this->db->real_escape_string(trim($company)); }

    // LOGIN BLINDADO
    public function login(){
        $result = false;
        $email = $this->email;
        $password = $this->password;
        
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $login = $this->db->query($sql);
        
        if($login && $login->num_rows == 1){
            $user = $login->fetch_object();
            
            // Verifica hash (nuevo) O texto plano (antiguo)
            if(password_verify($password, $user->password) || $password == $user->password){
                $result = $user;
            }
        }
        return $result;
    }

    // CRUD
    public function getAll(){
        $sql = "SELECT * FROM users ORDER BY id DESC";
        return $this->db->query($sql);
    }

    public function getOne($id){
        $sql = "SELECT * FROM users WHERE id = {$id}";
        $user = $this->db->query($sql);
        return $user->fetch_object();
    }

    // GUARDAR CON PROTECCIÓN DE DUPLICADOS
    public function save(){
        // 1. Verificar si existe antes de intentar guardar
        $check = $this->db->query("SELECT id FROM users WHERE email = '{$this->email}'");
        if($check && $check->num_rows > 0){
            return false; // Retorna falso si ya existe (evita el Fatal Error)
        }

        // 2. Guardar
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 4]);
        $sql = "INSERT INTO users (fullname, email, password, role, company, created_at) 
                VALUES ('{$this->fullname}', '{$this->email}', '{$password_hash}', '{$this->role}', '{$this->company}', CURDATE());";
        
        return $this->db->query($sql);
    }

    public function update($id){
        if(!empty($this->password)){
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 4]);
            $sql = "UPDATE users SET fullname='{$this->fullname}', email='{$this->email}', role='{$this->role}', password='{$password_hash}' WHERE id={$id}";
        } else {
            $sql = "UPDATE users SET fullname='{$this->fullname}', email='{$this->email}', role='{$this->role}' WHERE id={$id}";
        }
        return $this->db->query($sql);
    }

    public function delete($id){
        $sql = "DELETE FROM users WHERE id = {$id}";
        return $this->db->query($sql);
    }
}
?>