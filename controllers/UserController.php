<?php
require_once 'models/ProjectModel.php';
require_once 'models/ToolModel.php';
require_once 'models/RequestModel.php';
require_once 'models/UserModel.php';
require_once 'models/AuditModel.php'; 

class UserController {
    
    public function __construct(){
        if(!isset($_SESSION['role']) || $_SESSION['role'] != 'USER'){
            header("Location: " . base_url . "Auth/login");
            exit();
        }
        // Actualizar el estado "En línea" del usuario en cada interacción
        $userModel = new UserModel();
        $userModel->updateActivity($_SESSION['identity']->id);
    }

    public function dashboard(){
        $userId = $_SESSION['identity']->id;
        $projectModel = new ProjectModel();
        $requestModel = new RequestModel();

        $misObras = $projectModel->getAll(); 
        $totalObras = $misObras ? $misObras->num_rows : 0;

        $misSolicitudes = $requestModel->getRequestsByUser($userId);
        $totalSolicitudes = 0;
        $pendientes = 0;
        $aprobados = 0;
        $rechazados = 0;

        if($misSolicitudes){
            $totalSolicitudes = $misSolicitudes->num_rows;
            while($req = $misSolicitudes->fetch_object()){
                if($req->status == 'PENDIENTE') $pendientes++;
                if($req->status == 'APROBADO') $aprobados++;
                if($req->status == 'RECHAZADO') $rechazados++;
            }
        }

        require_once 'views/user/dashboard.php';
    }

    public function panel(){
        $userId = $_SESSION['identity']->id;
        $project = new ProjectModel();
        $misObras = $project->getAll(); 
        $requestModel = new RequestModel();
        $misSolicitudes = $requestModel->getRequestsByUser($userId);
        
        require_once 'views/user/panel.php';
    }

    public function catalog(){
        $tool = new ToolModel();
        $disponibles = $tool->getAllActive(); 
        
        $projectModel = new ProjectModel();
        $obras = $projectModel->getAll();

        require_once 'views/user/catalog.php';
    }

    public function requestTool(){
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST['tool_id']) && isset($_POST['quantity']) && isset($_POST['project_id'])){
            $toolId = (int)$_POST['tool_id'];
            $qty = (int)$_POST['quantity'];
            $projectId = (int)$_POST['project_id'];
            $userId = $_SESSION['identity']->id;

            if($qty <= 0){ echo json_encode(['status' => 'error', 'msg' => 'Cantidad inválida.']); exit(); }

            $toolModel = new ToolModel();
            $tool = $toolModel->getOne($toolId);

            if(!$tool || $qty > $tool->stock_available){
                echo json_encode(['status' => 'error', 'msg' => "Error de stock físico."]); exit();
            }

            $request = new RequestModel();
            $request->setUserId($userId);
            $request->setToolId($toolId);      
            $request->setProjectId($projectId); 
            $request->setQuantity($qty);       
            $request->setType('SOLICITUD_HERRAMIENTA');
            $request->setDescription("Solicitud: {$tool->name} (x{$qty})");
            
            if($request->save()){
                $audit = new AuditModel();
                $audit->logAction($userId, 'OPERACIONES', 'NUEVA_SOLICITUD', "Solicitó despacho de $qty u. de: $tool->name.");
                echo json_encode(['status' => 'success', 'msg' => 'Solicitud enviada.']);
            } else { echo json_encode(['status' => 'error', 'msg' => 'Fallo del sistema.']); }
        }
        exit();
    }

    public function requestCart(){
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents('php://input'), true);
        
        if(isset($data['cart']) && isset($data['project_id'])){
            $projectId = (int)$data['project_id'];
            $userId = $_SESSION['identity']->id;
            $cart = $data['cart'];

            $expectedDate = !empty($data['expected_date']) ? $data['expected_date'] : null;
            $returnDate = !empty($data['return_date']) ? $data['return_date'] : null;
            $orderNotes = !empty($data['order_notes']) ? $data['order_notes'] : null;

            if(empty($cart)) { 
                echo json_encode(['status'=>'error', 'msg'=>'El carrito de herramientas está vacío.']); 
                exit(); 
            }

            $toolModel = new ToolModel();
            $requestModel = new RequestModel();
            $audit = new AuditModel();

            $successCount = 0;
            $toolNames = [];

            foreach($cart as $item) {
                $toolId = (int)$item['id'];
                $qty = (int)$item['qty'];
                $tool = $toolModel->getOne($toolId);

                if($tool && $qty > 0 && $qty <= $tool->stock_available){
                    $requestModel->setUserId($userId);
                    $requestModel->setToolId($toolId);
                    $requestModel->setProjectId($projectId);
                    $requestModel->setQuantity($qty);
                    $requestModel->setType('SOLICITUD_HERRAMIENTA');
                    $requestModel->setDescription("Despacho requerido: {$tool->name} (x{$qty})");
                    
                    $requestModel->setExpectedDate($expectedDate);
                    $requestModel->setReturnDate($returnDate);
                    $requestModel->setOrderNotes($orderNotes);
                    
                    if($requestModel->save()){
                        $successCount++;
                        $toolNames[] = "{$tool->name} (x{$qty})";
                    }
                }
            }

            if($successCount > 0){
                $toolsStr = implode(', ', $toolNames);
                $fechaStr = $expectedDate ? $expectedDate : 'No definida';
                $audit->logAction($userId, 'OPERACIONES', 'PEDIDO_MULTIPLE', "Envió orden de trabajo para: $toolsStr (Entrega: $fechaStr).");
                
                echo json_encode(['status' => 'success', 'msg' => "Orden Logística Generada. $successCount activo(s) solicitados correctamente al administrador."]);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'No se pudo procesar la orden. Verifique el stock disponible de los artículos seleccionados.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos o corruptos.']);
        }
        exit();
    }

    public function reportView(){
        require_once 'views/user/report_damage.php';
    }

    public function saveReport(){
        if(isset($_POST)){
            $type = $_POST['type']; 
            $description = $_POST['description'];
            $user_id = $_SESSION['identity']->id;

            if($description){
                $request = new RequestModel();
                $request->setUserId($user_id);
                $request->setType($type);
                $request->setDescription($description);
                $request->save();
                
                $audit = new AuditModel();
                $audit->logAction($user_id, 'MESA DE AYUDA', 'NUEVO_REPORTE', "Generó un reporte de estado tipo: $type.");

                $_SESSION['alert_message'] = "El reporte de estado ha sido registrado en el sistema.";
                $_SESSION['alert_icon'] = "success";
            }
        }
        header("Location: " . base_url . "User/panel");
    }

    public function saveProject(){
        if(isset($_POST['name'])){
            $image = "default_project.png"; 

            if(isset($_FILES['image']) && $_FILES['image']['size'] > 0 && $_FILES['image']['error'] == 0){ 
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if(in_array($fileExt, $allowedExts) && getimagesize($fileTmpPath) !== false){
                    $image = uniqid('proj_') . '.' . $fileExt; 
                    move_uploaded_file($fileTmpPath, 'assets/img/'.$image); 
                }
            }

            $infoData = [
                'cliente' => $_POST['company_client'] ?? 'No especificado',
                'tipo' => $_POST['type_work'] ?? 'General',
                'fecha' => $_POST['start_date'] ?? date('Y-m-d'),
                'presupuesto' => !empty($_POST['budget']) ? number_format($_POST['budget'], 2) : '0.00',
                'autor' => $_SESSION['identity']->fullname
            ];

            $jsonDescription = json_encode($infoData, JSON_UNESCAPED_UNICODE);

            $project = new ProjectModel(); 
            $project->setName($_POST['name']); 
            $project->setDescription($jsonDescription);
            $project->setLocation($_POST['address']); 
            $project->setLat($_POST['lat']); 
            $project->setLng($_POST['lng']); 
            $project->setStatus('PLANIFICACION'); 
            $project->setImage($image);
            $project->save();

            $audit = new AuditModel();
            $audit->logAction($_SESSION['identity']->id, 'PROYECTOS', 'CREACION_EXTERNA', "Dio de alta la obra: {$_POST['name']}.");
        }
        header("Location: " . base_url . "User/panel");
    }

    public function deleteRequests(){
        if(isset($_POST['ids'])){
            $ids = $_POST['ids'];
            $userId = $_SESSION['identity']->id;
            $requestModel = new RequestModel();
            
            $count = 0;
            if(is_array($ids)){
                foreach($ids as $id){
                    $delete = $requestModel->hideFromUser($id, $userId);
                    if($delete) $count++;
                }
            }

            echo json_encode(['status' => $count > 0 ? 'success' : 'error', 'msg' => $count > 0 ? "$count registros han sido archivados." : 'No se pudo completar la operación.']);
        } else { echo json_encode(['status' => 'error', 'msg' => 'No se han seleccionado parámetros válidos.']); }
    }

    public function reports(){
        $user_id = $_SESSION['identity']->id;
        $requestModel = new RequestModel();
        $misReportes = $requestModel->getRequestsByUser($user_id);
        require_once 'views/user/reports.php';
    }

    // ==========================================================
    // MESA DE AYUDA (USUARIO)
    // ==========================================================
    public function helpDesk(){
        $userId = $_SESSION['identity']->id;
        $requestModel = new RequestModel(); 
        
        $misSolicitudes = $requestModel->getRequestsByUser($userId);
        
        $tickets = [];
        if($misSolicitudes) {
            while($row = $misSolicitudes->fetch_object()){
                if($row->type == 'REPORTE_DAÑO' || strpos($row->type, 'REPORTE') !== false){
                    $row->request_unique_id = $row->id; 
                    $row->fullname = "Reporte #" . str_pad($row->id, 5, '0', STR_PAD_LEFT); 
                    // Calcular mensajes no leídos por este usuario para este ticket
                    $row->unread_count = $requestModel->getUnreadCount($row->id, $userId);
                    $tickets[] = $row;
                }
            }
        }
        require_once 'views/user/help_desk.php';
    }

    // ==========================================================
    // LÓGICA DEL CHAT DE SOPORTE AVANZADO (USUARIO)
    // ==========================================================
    public function loadChat() {
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_GET['id'])) {
            $reqId = (int)$_GET['id'];
            $userId = $_SESSION['identity']->id;
            
            $reqModel = new RequestModel();
            $userModel = new UserModel();

            // Para el usuario, el estatus que importa es el de algún ADMIN
            // Aquí podríamos buscar el status del admin principal, o dejar un estado genérico.
            // Para mantenerlo simple en la vista del usuario, mostraremos "Soporte Activo"
            $adminStatus = "Soporte Centralizado"; 

            // Marcar mensajes del admin como leídos
            $reqModel->markMessagesAsRead($reqId, $userId);

            // Cargar mensajes (filtrando si el usuario vació su chat antes)
            $messages = $reqModel->getChatMessages($reqId, 'USER');
            
            $data = [];
            if($messages) {
                while($m = $messages->fetch_object()){
                    $m->time = date('h:i A', strtotime($m->created_at));
                    $data[] = $m;
                }
            }
            echo json_encode([
                'status' => 'success', 
                'data' => $data,
                'admin_status' => $adminStatus
            ]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'ID de reporte no provisto.']);
        }
        exit();
    }

    public function sendChatMessage() {
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST['request_id']) && isset($_POST['message'])) {
            $reqId = (int)$_POST['request_id'];
            $msg = trim($_POST['message']);
            $senderId = $_SESSION['identity']->id;
            
            $reqModel = new RequestModel();

            $saved = $reqModel->saveChatMessage($reqId, $senderId, $msg);
            
            if($saved) {
                $reqModel->updateStatus($reqId, 'PENDIENTE');

                $audit = new AuditModel();
                $audit->logAction($senderId, 'MESA DE AYUDA', 'MENSAJE_ENVIADO', "El usuario respondió en el ticket #$reqId.");
                
                echo json_encode(['status' => 'success', 'msg' => 'Mensaje enviado a la central.']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Error al enviar el mensaje.']);
            }
        }
        exit();
    }

    // Vaciar el historial del chat solo para el usuario
    public function clearChat() {
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST['request_id'])) {
            $reqId = (int)$_POST['request_id'];
            $reqModel = new RequestModel();
            
            if($reqModel->clearChat($reqId, 'USER')) {
                echo json_encode(['status' => 'success', 'msg' => 'Chat vaciado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Fallo al intentar vaciar el chat.']);
            }
        }
        exit();
    }

    // --- 8. MIS HERRAMIENTAS ---
    public function myTools(){
        $userId = $_SESSION['identity']->id;
        $db = Database::connect();
        $sql = "SELECT a.*, t.name as tool_name, t.image, t.category, p.name as project_name 
                FROM assignments a 
                INNER JOIN tools t ON a.tool_id = t.id 
                LEFT JOIN projects p ON a.project_id = p.id 
                WHERE a.user_id = {$userId} AND a.status = 'ACTIVO'
                ORDER BY a.assigned_at DESC";
        $misHerramientas = $db->query($sql);

        require_once 'views/user/my_tools.php';
    }

    public function initiateReturn(){
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST['assignment_id'])){
            $assignmentId = (int)$_POST['assignment_id'];
            $userId = $_SESSION['identity']->id;
            
            $db = Database::connect();
            $check = $db->query("SELECT * FROM assignments WHERE id = $assignmentId AND user_id = $userId AND status = 'ACTIVO'");
            
            if($check && $check->num_rows > 0){
                $assign = $check->fetch_object();
                $request = new RequestModel();
                $request->setUserId($userId);
                $request->setToolId($assign->tool_id);
                $request->setProjectId($assign->project_id);
                $request->setQuantity($assign->quantity);
                $request->setType('OTRO'); 
                $request->setDescription("DEVOLUCIÓN PENDIENTE: El usuario entregó en bodega el activo (Asignación #{$assignmentId}). Requiere Check-In.");
                $request->save();
                
                $audit = new AuditModel();
                $audit->logAction($userId, 'OPERACIONES', 'INICIO_RETORNO', "Notificó en sistema la devolución de la asignación #$assignmentId.");

                echo json_encode(['status' => 'success', 'msg' => 'Notificación enviada. Entregue el equipo en bodega.']);
            } else { echo json_encode(['status' => 'error', 'msg' => 'La asignación no es válida o ya fue procesada.']); }
        }
        exit();
    }

    // --- 10. PERFIL ---
    public function profile(){
        $userId = $_SESSION['identity']->id;
        $userModel = new UserModel();
        $user = $userModel->getOne($userId);
        require_once 'views/user/profile.php';
    }

    public function updateProfile(){
        if(isset($_POST['fullname'])){
            $id = $_SESSION['identity']->id;
            $userModel = new UserModel();
            $userModel->setFullname($_POST['fullname']);
            
            if(isset($_FILES['image']) && $_FILES['image']['size'] > 0 && $_FILES['image']['error'] == 0){
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

                if(in_array($fileExt, $allowedExts) && getimagesize($fileTmpPath) !== false){
                    $newFileName = uniqid('user_') . '.' . $fileExt;
                    if(move_uploaded_file($fileTmpPath, 'assets/img/'.$newFileName)){
                        $userModel->setImage($newFileName);
                        $_SESSION['identity']->image = $newFileName;
                    }
                }
            }

            if($userModel->updateProfile($id)){
                $_SESSION['identity']->fullname = $_POST['fullname'];
                $_SESSION['alert_message'] = "Tu perfil ha sido actualizado correctamente.";
                $_SESSION['alert_icon'] = "success";
            }
        }
        header("Location: " . base_url . "User/profile");
    }

    public function updatePassword(){
        if(isset($_POST['password']) && !empty($_POST['password'])){
            $id = $_SESSION['identity']->id;
            $userModel = new UserModel();
            $userModel->setPassword($_POST['password']);
            
            if($userModel->updatePassword($id)){
                $_SESSION['alert_message'] = "Tu contraseña ha sido actualizada con éxito.";
                $_SESSION['alert_icon'] = "success";
            }
        }
        header("Location: " . base_url . "User/profile");
    }
}
?>