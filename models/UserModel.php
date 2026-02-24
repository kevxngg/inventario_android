<?php
require_once __DIR__ . '/../config/db.php';

class UserModel {
    private $db;
    private $id;
    private $fullname;
    private $email;
    private $password;
    private $role;
    private $company;
    private $image; 
    
    private $verification_code;
    private $code_expires_at;

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
    public function setImage($image) { $this->image = $this->db->real_escape_string(trim($image)); }

    // LOGIN
    public function login(){
        $result = false;
        $email = $this->email;
        $password = $this->password;
        
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $login = $this->db->query($sql);
        
        if($login && $login->num_rows == 1){
            $user = $login->fetch_object();
            
            if(password_verify($password, $user->password) || $password == $user->password){
                if($user->is_verified == 0){ return 'UNVERIFIED'; }
                $this->updateActivity($user->id); // Actualizar actividad al loguear
                $result = $user;
            }
        }
        return $result;
    }

    public function getAll(){ return $this->db->query("SELECT * FROM users ORDER BY id DESC"); }
    public function getOne($id){ return $this->db->query("SELECT * FROM users WHERE id = {$id}")->fetch_object(); }

    // ====================================================================
    // NUEVAS FUNCIONES: ACTIVIDAD (EN LÍNEA / ÚLTIMA VEZ) (CON SEGURO)
    // ====================================================================
    public function updateActivity($id) {
        $sql = "UPDATE users SET last_activity = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) { // Seguro anti-caídas
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function getUserStatus($id) {
        $sql = "SELECT last_activity FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) { // Seguro anti-caídas
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result && $result->num_rows == 1) {
                $user = $result->fetch_object();
                if (!isset($user->last_activity) || !$user->last_activity) return "Desconectado";

                date_default_timezone_set('America/Bogota');
                $last = strtotime($user->last_activity);
                $now = time();
                $diff = round(abs($now - $last) / 60); 

                if ($diff < 5) {
                    return "En línea";
                } elseif ($diff < 60) {
                    return "Últ. vez hoy hace $diff min";
                } elseif ($diff < 1440) { 
                    $horas = floor($diff / 60);
                    return "Últ. vez hoy hace $horas hr" . ($horas > 1 ? 's' : '');
                } else {
                    return "Últ. vez el " . date('d/m/Y', $last);
                }
            }
        }
        return "Desconocido";
    }

    // RESTO DE FUNCIONES (CRUD y Recuperación)
    public function save(){
        $check = $this->db->query("SELECT id FROM users WHERE email = '{$this->email}'");
        if($check && $check->num_rows > 0){ return false; }

        $this->verification_code = rand(100000, 999999);
        date_default_timezone_set('America/Bogota');
        $this->code_expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 4]);
        
        $sql = "INSERT INTO users (fullname, email, password, role, company, image, verification_code, code_expires_at, is_verified, created_at, last_activity) 
                VALUES ('{$this->fullname}', '{$this->email}', '{$password_hash}', '{$this->role}', '{$this->company}', 'default_user.png', '{$this->verification_code}', '{$this->code_expires_at}', 0, CURDATE(), NOW());";
        
        if($this->db->query($sql)){ return $this->verification_code; }
        return false;
    }

    public function verifyCode($email, $code){
        date_default_timezone_set('America/Bogota');
        $now = date('Y-m-d H:i:s');
        $email = $this->db->real_escape_string($email);
        $code = $this->db->real_escape_string($code);

        $sql = "SELECT * FROM users WHERE email = '$email' AND verification_code = '$code' AND is_verified = 0";
        $result = $this->db->query($sql);

        if($result && $result->num_rows == 1){
            $user = $result->fetch_object();
            if($now <= $user->code_expires_at){
                $this->db->query("UPDATE users SET is_verified = 1, verification_code = NULL, code_expires_at = NULL WHERE email = '$email'");
                return 'SUCCESS';
            } else { return 'EXPIRED'; }
        }
        return 'INVALID'; 
    }

    public function generateRecoveryCode($email){
        $email = $this->db->real_escape_string($email);
        $check = $this->db->query("SELECT * FROM users WHERE email = '$email'");
        if($check && $check->num_rows == 1){
            $user = $check->fetch_object();
            $code = rand(100000, 999999);
            date_default_timezone_set('America/Bogota');
            $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $this->db->query("UPDATE users SET verification_code = '$code', code_expires_at = '$expires' WHERE email = '$email'");
            return ['code' => $code, 'name' => $user->fullname];
        }
        return false;
    }

    public function verifyRecoveryCode($email, $code){
        date_default_timezone_set('America/Bogota');
        $now = date('Y-m-d H:i:s');
        $email = $this->db->real_escape_string($email);
        $code = $this->db->real_escape_string($code);

        $sql = "SELECT * FROM users WHERE email = '$email' AND verification_code = '$code'";
        $result = $this->db->query($sql);

        if($result && $result->num_rows == 1){
            $user = $result->fetch_object();
            if($now <= $user->code_expires_at){ return 'SUCCESS'; } 
            else { return 'EXPIRED'; }
        }
        return 'INVALID';
    }

    public function resetPasswordWithEmail($email, $newPassword){
        $email = $this->db->real_escape_string($email);
        $password_hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 4]);
        $sql = "UPDATE users SET password='$password_hash', verification_code=NULL, code_expires_at=NULL WHERE email='$email'";
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
    public function updateProfile($id){
        $sql = "UPDATE users SET fullname='{$this->fullname}'";
        if($this->image != null){ $sql .= ", image='{$this->image}'"; }
        $sql .= " WHERE id={$id}";
        return $this->db->query($sql);
    }
    public function updatePassword($id){
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 4]);
        $sql = "UPDATE users SET password='{$password_hash}' WHERE id={$id}";
        return $this->db->query($sql);
    }
    public function delete($id){ return $this->db->query("DELETE FROM users WHERE id = {$id}"); }
}
?>