<?php
require_once __DIR__ . '/../models/ToolModel.php';
require_once __DIR__ . '/../models/ProjectModel.php';
require_once __DIR__ . '/../models/UserModel.php'; 
require_once __DIR__ . '/../models/RequestModel.php'; 

class AdminController {

    public function __construct(){
        if(!isset($_SESSION['identity'])){
            header("Location: " . base_url . "Auth/login");
            exit();
        }
    }

    // --- DASHBOARD (MÉTRICAS REALES DE INVENTARIO) ---
    public function dashboard(){
        if($_SESSION['role'] != 'ADMIN'){ header("Location: " . base_url . "User/panel"); exit(); }
        
        $toolModel = new ToolModel(); 
        $projectModel = new ProjectModel(); 
        $requestModel = new RequestModel(); 
        
        // 1. Datos básicos
        // countAll() ahora devuelve la suma total de ítems físicos (ej: 500), no solo tipos de herramientas (ej: 10)
        $totalTools = $toolModel->countAll(); 
        
        // Herramientas marcadas como "MANTENIMIENTO" (Conteo de filas/tipos)
        $maintenance = $toolModel->countMaintenance();
        
        $activeProjects = $projectModel->countActive(); 
        $pendingRequests = $requestModel->countPending();
        
        // 2. Métricas de Stock (Cantidades Reales)
        $available = $toolModel->countAvailable(); // Suma de stock_available
        $inUse = $toolModel->countInUse();         // (Total - Disponible)
        
        // 3. Datos para Gráfica de Categorías
        $categoryStats = $toolModel->getStatsByCategory();
        $catLabels = [];
        $catData = [];
        
        if($categoryStats){
            while($cat = $categoryStats->fetch_object()){
                $cleanName = ucwords(strtolower(str_replace('_', ' ', $cat->category)));
                $catLabels[] = $cleanName;
                $catData[] = $cat->total;
            }
        }

        require_once 'views/admin/dashboard.php';
    }

    // --- VISTAS PRINCIPALES ---
    public function tools(){
        $tool = new ToolModel(); 
        $herramientas = $tool->getAll(); // Ahora ordena por stock disponible
        require_once 'views/admin/tools.php';
    }

    public function map(){
        $project = new ProjectModel(); 
        $obras = $project->getAll(); 
        require_once 'views/admin/map.php';
    }

    public function users(){
        $userModel = new UserModel(); 
        $listaUsuarios = $userModel->getAll(); 
        require_once 'views/admin/users.php';
    }

    public function reports(){
        $requestModel = new RequestModel(); 
        $reportes = $requestModel->getAll(); 
        require_once 'views/admin/reports.php';
    }

    // --- NOTIFICACIONES Y SOLICITUDES PENDIENTES ---
    public function getNotifications(){
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');

        $requestModel = new RequestModel();
        $data = [];
        
        try {
            if($_SESSION['role'] == 'ADMIN'){
                $pendientes = $requestModel->getAll(); 
                if($pendientes){
                    while($row = $pendientes->fetch_object()){
                        if($row->status == 'PENDIENTE'){
                            $row->is_admin = true;
                            
                            // Normalización de ID
                            $row->id = $row->request_unique_id ?? $row->id ?? 0;

                            // Nombre de herramienta (Si existe el JOIN en el modelo, usa tool_name, sino description)
                            $row->tool_name = $row->tool_name ?? $row->description ?? 'Sin detalle';
                            $row->fullname = $row->fullname ?? 'Usuario Eliminado';
                            
                            // Cantidad solicitada (Visual)
                            if(isset($row->quantity) && $row->quantity > 1){
                                $row->tool_name .= " (x{$row->quantity})";
                            }

                            $row->request_date = isset($row->created_at) ? date('d/m/Y', strtotime($row->created_at)) : '--/--';
                            
                            $data[] = $row;
                        }
                    }
                }
            } else {
                // Para usuario normal
                $userId = $_SESSION['identity']->id;
                $misSolicitudes = $requestModel->getRequestsByUser($userId);
                if($misSolicitudes){
                    while($row = $misSolicitudes->fetch_object()){
                        $row->fullname = "Estado de solicitud";
                        $row->is_admin = false;
                        $row->tool_name = $row->description ?? 'Sin detalle';
                        $row->request_date = isset($row->created_at) ? date('d/m/Y', strtotime($row->created_at)) : '--/--';
                        $data[] = $row;
                    }
                }
            }
        } catch (Exception $e) {
            $data = [];
        }
        
        echo json_encode($data);
        exit();
    }

    // --- PROCESAR SOLICITUD (CORE DEL SISTEMA ENTERPRISE) ---
    public function handleRequest(){
        // Silenciar errores para respuesta JSON limpia
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');

        $processed = false;
        $msg = "Error desconocido";

        if(isset($_GET['id']) && isset($_GET['status'])){
            $requestId = (int)$_GET['id'];
            $newStatus = $_GET['status'];
            
            $requestModel = new RequestModel();
            $toolModel = new ToolModel();
            
            // 1. Obtener detalles de la solicitud (Qué herramienta, Cuántas, Quién)
            $request = $requestModel->getOne($requestId); 

            if($request && $request->status == 'PENDIENTE'){
                
                if($newStatus == 'APROBADO'){
                    // LOGICA DE APROBACIÓN: Descontar inventario
                    $toolId = $request->tool_id;
                    $qty = $request->quantity;
                    $userId = $_SESSION['identity']->id; // Admin que aprueba (responsable del movimiento)

                    // Validamos que exista la herramienta vinculada
                    if($toolId){
                        // Intentamos realizar el movimiento (Salida por préstamo)
                        // registerMovement validará si hay stock suficiente
                        $success = $toolModel->registerMovement($toolId, $userId, 'SALIDA_PRESTAMO', $qty, $requestId);

                        if($success){
                            $requestModel->updateStatus($requestId, 'APROBADO');
                            $processed = true;
                            $msg = "Solicitud aprobada. Stock actualizado.";
                        } else {
                            $processed = false;
                            $msg = "Error: Stock insuficiente para aprobar.";
                        }
                    } else {
                        // Si es una solicitud antigua sin tool_id, solo cambiamos estado
                        $requestModel->updateStatus($requestId, 'APROBADO');
                        $processed = true;
                        $msg = "Aprobado (Sin descuento de inventario - Solicitud antigua)";
                    }

                } else {
                    // Si es RECHAZADO, no tocamos el inventario
                    $requestModel->updateStatus($requestId, 'RECHAZADO');
                    $processed = true;
                    $msg = "Solicitud rechazada.";
                }
            } else {
                $msg = "La solicitud ya fue procesada o no existe.";
            }
        }

        // Respuesta para AJAX
        if(isset($_GET['ajax'])){
            echo json_encode(['status' => $processed ? 'success' : 'error', 'msg' => $msg]);
            exit();
        }

        header("Location: " . base_url . "Admin/reports");
        exit();
    }
    
    public function changeStatus(){ $this->handleRequest(); }

    // --- GESTIÓN DE HERRAMIENTAS (CRUD ACTUALIZADO) ---
    public function saveTool(){
        if(isset($_POST['name'])){
             $img = "default.png"; 
             if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){ 
                 $img = $_FILES['image']['name']; 
                 move_uploaded_file($_FILES['image']['tmp_name'], 'assets/img/'.$img); 
             }
             
             $t = new ToolModel(); 
             $t->setName($_POST['name']); 
             $t->setCategory($_POST['category']); 
             $t->setStatus($_POST['status']); 
             $t->setImage($img); 
             
             // NUEVOS CAMPOS DE INVENTARIO
             // Si no envían nada, asumimos 1 unidad
             $stock = isset($_POST['stock_total']) && $_POST['stock_total'] > 0 ? (int)$_POST['stock_total'] : 1;
             $min = isset($_POST['stock_min']) ? (int)$_POST['stock_min'] : 5;
             
             $t->setStockTotal($stock);
             $t->setStockAvailable($stock); // Al crear, todo está disponible
             $t->setStockMin($min);

             $t->save();
        }
        header("Location: " . base_url . "Admin/tools");
    }

    public function updateTool(){
        $id = $_GET['id'] ?? null; 
        if(!$id && isset($_POST['id'])) $id = $_POST['id']; // Soporte extra por si acaso
        
        if(isset($_POST) && $id){
             $t = new ToolModel(); 
             if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){ 
                 $img = $_FILES['image']['name']; 
                 move_uploaded_file($_FILES['image']['tmp_name'], 'assets/img/'.$img); 
                 $t->setImage($img); 
             }
             $t->setName($_POST['name']); 
             $t->setCategory($_POST['category']); 
             $t->setStatus($_POST['status']);
             
             // Actualizar Stock Total y Mínimo
             if(isset($_POST['stock_total'])) $t->setStockTotal($_POST['stock_total']);
             if(isset($_POST['stock_min'])) $t->setStockMin($_POST['stock_min']);
             
             $t->update($id);
         }
         header("Location: " . base_url . "Admin/tools");
    }

    public function deleteTool(){
        $id = $_GET['id'] ?? null; 
        if($id){ 
            $t = new ToolModel(); 
            $t->delete($id); 
        } 
        header("Location: " . base_url . "Admin/tools");
    }

    public function editTool(){
        $id = $_GET['id'] ?? null; 
        if($id){ 
            $toolModel = new ToolModel(); 
            $tool = $toolModel->getOne($id); 
            require_once 'views/admin/edit_tool.php'; 
        } else { 
            header("Location: " . base_url . "Admin/tools"); 
        }
    }

    // --- GESTIÓN DE USUARIOS ---
    public function saveUser(){
        if(isset($_POST['fullname'])){
            $u = new UserModel(); $u->setFullname($_POST['fullname']); $u->setEmail($_POST['email']);
            $u->setPassword($_POST['password']); $u->setRole($_POST['role']); $u->save();
        }
        header("Location: " . base_url . "Admin/users");
    }
    
    public function deleteUser(){
        $id = $_GET['id'] ?? null; 
        if($id){ $u = new UserModel(); $u->delete($id); } 
        header("Location: " . base_url . "Admin/users");
    }
    
    public function editUser(){
        $id = $_GET['id'] ?? null;
        if($id){ $userModel = new UserModel(); $user = $userModel->getOne($id); require_once 'views/admin/edit_user.php'; } 
        else { header("Location: " . base_url . "Admin/users"); }
    }
    
    public function updateUser(){
        $id = $_GET['id'] ?? null;
        if(isset($_POST) && $id){
            $u = new UserModel(); $u->setFullname($_POST['fullname']); $u->setEmail($_POST['email']); 
            $u->setPassword($_POST['password']); $u->setRole($_POST['role']); 
            $u->update($id);
        }
        header("Location: " . base_url . "Admin/users");
    }

    // --- GESTIÓN DE PROYECTOS (ACTUALIZADO) ---
    public function saveProject(){
        if(isset($_POST['name'])){
            $image = "default_project.png"; 
            if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){ 
                $image = $_FILES['image']['name']; 
                move_uploaded_file($_FILES['image']['tmp_name'], 'assets/img/'.$image); 
            }
            
            // CAPTURA DE DATOS REALES (Enterprise)
            // Antes estaban "hardcodeados", ahora vienen del formulario
            $infoData = [
                'cliente' => $_POST['company_client'] ?? 'No especificado',
                'tipo' => $_POST['type_work'] ?? 'General',
                'fecha' => $_POST['start_date'] ?? date('Y-m-d'),
                'presupuesto' => !empty($_POST['budget']) ? number_format($_POST['budget'], 2) : '0.00',
                'autor' => 'Administrador'
            ];

            $project = new ProjectModel(); 
            $project->setName($_POST['name']); 
            // Guardamos el JSON estructurado
            $project->setDescription(json_encode($infoData, JSON_UNESCAPED_UNICODE));
            
            $project->setLocation($_POST['address']); 
            $project->setLat($_POST['lat']); 
            $project->setLng($_POST['lng']); 
            $project->setStatus($_POST['status']); 
            $project->setImage($image);
            $project->save();
        }
        header("Location: " . base_url . "Admin/map");
    }

    public function updateProject(){
        $id = $_GET['id'] ?? null; 
        if(isset($_POST['name']) && $id){
            $project = new ProjectModel();
            $infoData = [
                'cliente' => $_POST['company_client'] ?? '',
                'tipo' => $_POST['type_work'] ?? '',
                'fecha' => $_POST['start_date'] ?? '',
                'presupuesto' => $_POST['budget'] ?? '',
                'autor' => 'Editado por Admin'
            ];
            $project->setName($_POST['name']);
            $project->setDescription(json_encode($infoData, JSON_UNESCAPED_UNICODE));
            $project->setLocation($_POST['address']);
            $project->setStatus($_POST['status']);
            if(!empty($_POST['lat']) && !empty($_POST['lng'])){
                $project->setLat($_POST['lat']);
                $project->setLng($_POST['lng']);
            }
            $project->update($id);
        }
        header("Location: " . base_url . "Admin/map");
    }

    public function deleteProject(){
        $id = $_GET['id'] ?? null; 
        if($id){ $p = new ProjectModel(); $p->delete($id); } 
        header("Location: " . base_url . "Admin/map");
    }

    // --- REPORTES ---
    public function deleteReports(){
        if(isset($_POST['ids'])){
            $ids = $_POST['ids'];
            $requestModel = new RequestModel();
            $count = 0;
            if(is_array($ids)){
                foreach($ids as $id){
                    $delete = $requestModel->deletePermanently($id);
                    if($delete) $count++;
                }
            }
            echo json_encode(['status' => 'success', 'msg' => "$count registros eliminados definitivamente."]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Nada seleccionado.']);
        }
    }

    public function printReports(){
        $requestModel = new RequestModel();
        $reportes = $requestModel->getAll(); 
        require_once 'views/admin/print_reports.php';
    }
}
?>