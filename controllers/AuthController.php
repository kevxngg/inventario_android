<?php
require_once 'models/UserModel.php';

class AuthController {

    // 1. Mostrar pantalla de Login
    public function login(){
        require_once 'views/auth/login.php';
    }

    // 2. Mostrar pantalla de Registro
    public function register(){
        require_once 'views/auth/register.php';
    }

    // 3. Procesar el Login (POST)
    public function authenticate(){
        if(isset($_POST)){
            $email = isset($_POST['email']) ? trim($_POST['email']) : false;
            $password = isset($_POST['password']) ? trim($_POST['password']) : false;

            if($email && $password){
                $user = new UserModel();
                $user->setEmail($email);
                $user->setPassword($password);

                // Intentamos loguear
                $identity = $user->login();
                
                if($identity && is_object($identity)){
                    // ¡Login Exitoso! Creamos las sesiones
                    $_SESSION['identity'] = $identity;
                    $_SESSION['role'] = $identity->role;
                    
                    // Redirección inteligente según el rol
                    if($identity->role == 'ADMIN'){
                        header("Location: " . base_url . "Admin/dashboard");
                    } else {
                        // Si es un usuario normal (Jefe de Obra)
                        header("Location: " . base_url . "User/dashboard"); 
                    }
                } else {
                    // Login fallido
                    $_SESSION['error_login'] = 'Identificación fallida: Datos incorrectos.';
                    header("Location: " . base_url . "Auth/login");
                }
            } else {
                $_SESSION['error_login'] = 'Por favor, llena todos los campos.';
                header("Location: " . base_url . "Auth/login");
            }
        }
    }

    // 4. Procesar el Registro (POST)
    public function save(){
        if(isset($_POST)){
            // Recoger datos
            $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : false;
            $email = isset($_POST['email']) ? $_POST['email'] : false;
            $password = isset($_POST['password']) ? $_POST['password'] : false;
            $company = isset($_POST['company']) ? $_POST['company'] : 'Sin Empresa'; // Evita el error si no llenan empresa

            if($fullname && $email && $password){
                $user = new UserModel();
                $user->setFullname($fullname);
                $user->setEmail($email);
                $user->setPassword($password);
                $user->setRole('USER'); // Por defecto se registra como usuario normal
                $user->setCompany($company);

                $save = $user->save();
                
                if($save){
                    $_SESSION['register'] = "complete";
                    // Opcional: Loguear automáticamente tras registro
                    // $this->authenticate(); 
                    // return;
                } else {
                    $_SESSION['register'] = "failed";
                    $_SESSION['errors']['general'] = "El correo ya está registrado o hubo un error.";
                }
            } else {
                $_SESSION['register'] = "failed";
            }
        } else {
            $_SESSION['register'] = "failed";
        }
        header("Location: " . base_url . "Auth/login"); // Redirigir al registro o login
    }

    // 5. Cerrar Sesión (MODIFICADO)
    public function logout(){
        if(isset($_SESSION['identity'])){
            unset($_SESSION['identity']);
        }
        if(isset($_SESSION['role'])){
            unset($_SESSION['role']);
        }
        session_destroy();
        
        // ANTES: header("Location: " . base_url . "Auth/login");
        // AHORA: Redirige a la página principal (Home)
        header("Location: " . base_url);
    }
}
?>