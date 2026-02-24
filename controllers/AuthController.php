<?php
require_once 'models/UserModel.php';
require_once 'config/MailService.php';

class AuthController {

    public function login(){ require_once 'views/auth/login.php'; }
    public function register(){ require_once 'views/auth/register.php'; }

    public function authenticate(){
        if(isset($_POST)){
            $email = isset($_POST['email']) ? trim($_POST['email']) : false;
            $password = isset($_POST['password']) ? trim($_POST['password']) : false;

            if($email && $password){
                $user = new UserModel();
                $user->setEmail($email);
                $user->setPassword($password);

                $identity = $user->login();
                
                if($identity === 'UNVERIFIED'){
                    $_SESSION['verify_email'] = $email;
                    header("Location: " . base_url . "Auth/verifyView");
                    exit();
                }

                if($identity && is_object($identity)){
                    $_SESSION['identity'] = $identity;
                    $_SESSION['role'] = $identity->role;
                    if($identity->role == 'ADMIN'){ header("Location: " . base_url . "Admin/dashboard"); } 
                    else { header("Location: " . base_url . "User/dashboard"); }
                } else {
                    $_SESSION['error_login'] = 'Identificación fallida: Datos incorrectos.';
                    header("Location: " . base_url . "Auth/login");
                }
            } else {
                $_SESSION['error_login'] = 'Por favor, llena todos los campos.';
                header("Location: " . base_url . "Auth/login");
            }
        }
    }

    public function save(){
        if(isset($_POST)){
            $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : false;
            $email = isset($_POST['email']) ? trim($_POST['email']) : false;
            $password = isset($_POST['password']) ? $_POST['password'] : false;
            $company = !empty($_POST['company_name']) ? $_POST['company_name'] : 'Sin Empresa'; 

            if($fullname && $email && $password){
                $user = new UserModel();
                $user->setFullname($fullname);
                $user->setEmail($email);
                $user->setPassword($password);
                $user->setRole('USER'); 
                $user->setCompany($company);

                $verification_code = $user->save();
                
                if($verification_code){
                    $mailer = new MailService();
                    $mailer->sendVerificationCode($email, $fullname, $verification_code);
                    $_SESSION['verify_email'] = $email;
                    header("Location: " . base_url . "Auth/verifyView");
                    exit();
                } else {
                    $_SESSION['register'] = "failed";
                    $_SESSION['errors']['general'] = "El correo ya está registrado o hubo un error.";
                }
            } else { $_SESSION['register'] = "failed"; }
        } else { $_SESSION['register'] = "failed"; }
        header("Location: " . base_url . "Auth/register");
    }

    public function verifyView(){
        if(!isset($_SESSION['verify_email'])){ header("Location: " . base_url . "Auth/login"); exit(); }
        require_once 'views/auth/verify.php';
    }

    public function processVerification(){
        if(isset($_POST['code']) && isset($_SESSION['verify_email'])){
            $code = $_POST['code'];
            $email = $_SESSION['verify_email'];
            $userModel = new UserModel();
            $status = $userModel->verifyCode($email, $code);

            if($status === 'SUCCESS'){
                unset($_SESSION['verify_email']);
                $_SESSION['register'] = "complete"; 
                header("Location: " . base_url . "Auth/login");
            } elseif ($status === 'EXPIRED') {
                $_SESSION['error_verify'] = "El código expiró. Pida soporte al administrador.";
                header("Location: " . base_url . "Auth/verifyView");
            } else {
                $_SESSION['error_verify'] = "Código de seguridad incorrecto.";
                header("Location: " . base_url . "Auth/verifyView");
            }
        } else { header("Location: " . base_url . "Auth/login"); }
    }

    // ====================================================================
    // NUEVAS RUTAS: RECUPERACIÓN DE CONTRASEÑA
    // ====================================================================
    public function forgotPassword(){
        require_once 'views/auth/forgot_password.php';
    }

    public function sendRecoveryCode(){
        if(isset($_POST['email'])){
            $email = trim($_POST['email']);
            $userModel = new UserModel();
            $recoveryData = $userModel->generateRecoveryCode($email);
            
            if($recoveryData){
                $mailer = new MailService();
                $mailer->sendPasswordRecovery($email, $recoveryData['name'], $recoveryData['code']);
                
                $_SESSION['recovery_email'] = $email;
                header("Location: " . base_url . "Auth/verifyRecoveryView");
                exit();
            } else {
                $_SESSION['error_recovery'] = "No existe ninguna cuenta registrada con este correo.";
                header("Location: " . base_url . "Auth/forgotPassword");
                exit();
            }
        }
    }

    public function verifyRecoveryView(){
        if(!isset($_SESSION['recovery_email'])){ header("Location: " . base_url . "Auth/login"); exit(); }
        require_once 'views/auth/verify_recovery.php';
    }

    public function processRecoveryCode(){
        if(isset($_POST['code']) && isset($_SESSION['recovery_email'])){
            $code = $_POST['code'];
            $email = $_SESSION['recovery_email'];
            $userModel = new UserModel();
            $status = $userModel->verifyRecoveryCode($email, $code);

            if($status === 'SUCCESS'){
                $_SESSION['recovery_authorized'] = true; // Permiso para cambiar la clave
                header("Location: " . base_url . "Auth/resetPasswordView");
                exit();
            } elseif ($status === 'EXPIRED') {
                $_SESSION['error_verify'] = "El código ha expirado.";
                header("Location: " . base_url . "Auth/verifyRecoveryView");
                exit();
            } else {
                $_SESSION['error_verify'] = "Código incorrecto.";
                header("Location: " . base_url . "Auth/verifyRecoveryView");
                exit();
            }
        }
    }

    public function resetPasswordView(){
        if(!isset($_SESSION['recovery_authorized']) || !isset($_SESSION['recovery_email'])){
            header("Location: " . base_url . "Auth/login");
            exit();
        }
        require_once 'views/auth/reset_password.php';
    }

    public function updateNewPassword(){
        if(isset($_POST['password']) && isset($_SESSION['recovery_email']) && isset($_SESSION['recovery_authorized'])){
            $password = $_POST['password'];
            $email = $_SESSION['recovery_email'];
            
            $userModel = new UserModel();
            $userModel->resetPasswordWithEmail($email, $password);
            
            // Limpiar sesiones
            unset($_SESSION['recovery_email']);
            unset($_SESSION['recovery_authorized']);
            
            $_SESSION['recovery_success'] = "Tu contraseña ha sido restablecida. Ya puedes iniciar sesión.";
            header("Location: " . base_url . "Auth/login");
            exit();
        }
    }

    public function logout(){
        if(isset($_SESSION['identity'])){ unset($_SESSION['identity']); }
        if(isset($_SESSION['role'])){ unset($_SESSION['role']); }
        session_destroy();
        header("Location: " . base_url);
    }
}
?>