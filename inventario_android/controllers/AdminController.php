<?php
require_once 'models/ToolModel.php';
require_once 'models/ProjectModel.php';
require_once 'models/UserModel.php'; 
require_once 'models/RequestModel.php'; 

class AdminController {

    public function __construct(){
        if(!isset($_SESSION['identity'])){
            header("Location: " . base_url . "Auth/login");
            exit();
        }
    }

    // --- DASHBOARD ---
    public function dashboard(){
        if($_SESSION['role'] != 'ADMIN'){ header("Location: " . base_url . "User/panel"); exit(); }
        
        $toolModel = new ToolModel();
        $projectModel = new ProjectModel();
        $requestModel = new RequestModel(); 
        
        $totalTools = $toolModel->countAll();
        $maintenance = $toolModel->countMaintenance();
        $activeProjects = $projectModel->countActive();
        $pendingRequests = $requestModel->countPending();
        
        require_once 'views/admin/dashboard.php';
    }

    // --- VISTAS ---
    public function tools(){
        $tool = new ToolModel(); $herramientas = $tool->getAll(); 
        require_once 'views/admin/tools.php';
    }
    public function map(){
        $project = new ProjectModel(); $obras = $project->getAll(); 
        require_once 'views/admin/map.php';
    }
    public function users(){
        $userModel = new UserModel(); $listaUsuarios = $userModel->getAll(); 
        require_once 'views/admin/users.php';
    }
    public function reports(){
        $requestModel = new RequestModel(); $reportes = $requestModel->getAll(); 
        require_once 'views/admin/reports.php';
    }

    // =======================================================
    // 1. NOTIFICACIONES (SIN UNDEFINED)
    // =======================================================
    public function getNotifications(){
        $requestModel = new RequestModel();
        $data = [];
        
        if($_SESSION['role'] == 'ADMIN'){
            $pendientes = $requestModel->getAll(); 
            if($pendientes){
                while($row = $pendientes->fetch_object()){
                    if($row->status == 'PENDIENTE'){
                        $row->is_admin = true;
                        
                        // FIX: ID y Texto correctos
                        if(isset($row->request_unique_id)) { $row->id = $row->request_unique_id; } 
                        elseif(isset($row->request_id)) { $row->id = $row->request_id; }
                        else { $row->id = $row->id; }

                        $row->equipment = $row->description; 
                        
                        $data[] = $row;
                    }
                }
            }
        } else {
            $userId = $_SESSION['identity']->id;
            $misSolicitudes = $requestModel->getRequestsByUser($userId);
            if($misSolicitudes){
                while($row = $misSolicitudes->fetch_object()){
                    $row->fullname = "Estado de solicitud";
                    $row->is_admin = false;
                    $data[] = $row;
                }
            }
        }
        echo json_encode($data);
    }

    // =======================================================
    // 2. MANEJAR ACCIÓN DE REPORTES
    // =======================================================
    public function handleRequest(){
        if(isset($_GET['id']) && isset($_GET['status'])){
            $id = $_GET['id'];
            $status = $_GET['status'];
            
            if($id != 'undefined'){
                $request = new RequestModel();
                $request->updateStatus($id, $status);
            }
        }
        echo '<script>window.location.href="' . base_url . 'Admin/reports";</script>';
        exit();
    }
    
    public function changeStatus(){ $this->handleRequest(); }

    // =======================================================
    // 3. CRUD USUARIOS (CORREGIDO "NO ELIMINA NI EDITA")
    // =======================================================
    public function saveUser(){
        if(isset($_POST['fullname'])){
            $u = new UserModel();
            $u->setFullname($_POST['fullname']); $u->setEmail($_POST['email']);
            $u->setPassword($_POST['password']); $u->setRole($_POST['role']);
            $u->save();
        }
        header("Location: " . base_url . "Admin/users");
    }

    public function deleteUser(){
        // RESTAURADO: Detector de ID para evitar pantalla blanca
        $id = $_GET['id'] ?? null;
        if(!$id){ $url=parse_url($_SERVER['REQUEST_URI']); if(isset($url['query'])){ parse_str($url['query'], $p); $id=$p['id']??null; } }

        if($id){ $u = new UserModel(); $u->delete($id); }
        header("Location: " . base_url . "Admin/users");
    }

    public function editUser(){
        // RESTAURADO: Detector de ID
        $id = $_GET['id'] ?? null;
        if(!$id){ $url=parse_url($_SERVER['REQUEST_URI']); if(isset($url['query'])){ parse_str($url['query'], $p); $id=$p['id']??null; } }

        if($id){ 
            $userModel = new UserModel(); $user = $userModel->getOne($id); 
            require_once 'views/admin/edit_user.php'; 
        } else {
            header("Location: " . base_url . "Admin/users");
        }
    }

    public function updateUser(){
        $id = $_GET['id'] ?? null;
        if(!$id){ $url=parse_url($_SERVER['REQUEST_URI']); if(isset($url['query'])){ parse_str($url['query'], $p); $id=$p['id']??null; } }

        if(isset($_POST) && $id){
            $u = new UserModel();
            $u->setFullname($_POST['fullname']); $u->setEmail($_POST['email']);
            $u->setPassword($_POST['password']); $u->setRole($_POST['role']);
            $u->update($id);
        }
        header("Location: " . base_url . "Admin/users");
    }
    
    // =======================================================
    // 4. CRUD HERRAMIENTAS (CORREGIDO)
    // =======================================================
    public function saveTool(){
        if(isset($_POST['name'])){
             $img = "default.png";
             if(isset($_FILES['image']) && $_FILES['image']['size']>0){ $img = $_FILES['image']['name']; move_uploaded_file($_FILES['image']['tmp_name'], 'assets/img/'.$img); }
             $t = new ToolModel(); $t->setName($_POST['name']); $t->setCategory($_POST['category']); $t->setStatus($_POST['status']); $t->setImage($img); $t->save();
        }
        header("Location: " . base_url . "Admin/tools");
    }

    public function deleteTool(){
        $id = $_GET['id'] ?? null;
        if(!$id){ $url=parse_url($_SERVER['REQUEST_URI']); if(isset($url['query'])){ parse_str($url['query'], $p); $id=$p['id']??null; } }

        if($id){ $t = new ToolModel(); $t->delete($id); }
        header("Location: " . base_url . "Admin/tools");
    }

    public function editTool(){
        $id = $_GET['id'] ?? null;
        if(!$id){ $url=parse_url($_SERVER['REQUEST_URI']); if(isset($url['query'])){ parse_str($url['query'], $p); $id=$p['id']??null; } }

        if($id){ 
            $toolModel = new ToolModel(); $tool = $toolModel->getOne($id); 
            require_once 'views/admin/edit_tool.php'; 
        } else {
            header("Location: " . base_url . "Admin/tools");
        }
    }

    public function updateTool(){
        $id = $_GET['id'] ?? null;
        if(!$id){ $url=parse_url($_SERVER['REQUEST_URI']); if(isset($url['query'])){ parse_str($url['query'], $p); $id=$p['id']??null; } }

        if(isset($_POST) && $id){
             $t = new ToolModel();
             if(isset($_FILES['image']) && $_FILES['image']['size']>0){ $img = $_FILES['image']['name']; move_uploaded_file($_FILES['image']['tmp_name'], 'assets/img/'.$img); $t->setImage($img); }
             $t->setName($_POST['name']); $t->setCategory($_POST['category']); $t->setStatus($_POST['status']); $t->update($id);
         }
         header("Location: " . base_url . "Admin/tools");
    }

    // =======================================================
    // 5. CRUD OBRAS (CORREGIDO ERROR 'ADDRESS' vs 'LOCATION')
    // =======================================================
    public function saveProject(){
        if(isset($_POST['name'])){
            $image = "default_project.png";
            if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){
                $image = $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], 'assets/img/'.$image);
            }
            
            $project = new ProjectModel();
            $project->setName($_POST['name']);
            $project->setDescription("Proyecto registrado");
            
            // IMPORTANTE: Aquí recogemos el campo 'address' del HTML y lo enviamos al modelo
            // El modelo lo guardará en la columna 'location' que creaste
            $project->setLocation($_POST['address']); 
            
            $project->setLat($_POST['lat']);
            $project->setLng($_POST['lng']);
            $project->setStatus($_POST['status']);
            $project->setImage($image);
            $project->save();
        }
        header("Location: " . base_url . "Admin/map");
    }

    public function deleteProject(){
        $id = $_GET['id'] ?? null;
        if(!$id){ $url=parse_url($_SERVER['REQUEST_URI']); if(isset($url['query'])){ parse_str($url['query'], $p); $id=$p['id']??null; } }
        
        if($id){ $p = new ProjectModel(); $p->delete($id); }
        header("Location: " . base_url . "Admin/map");
    }
}
?>