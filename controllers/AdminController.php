<?php
require_once __DIR__ . '/../models/ToolModel.php';
require_once __DIR__ . '/../models/ProjectModel.php';
require_once __DIR__ . '/../models/UserModel.php'; 
require_once __DIR__ . '/../models/RequestModel.php'; 
require_once __DIR__ . '/../models/AssignmentModel.php'; 
require_once __DIR__ . '/../models/MaintenanceModel.php';
require_once __DIR__ . '/../models/AuditModel.php'; 

class AdminController {

    public function __construct(){
        if(!isset($_SESSION['identity'])){
            header("Location: " . base_url . "Auth/login");
            exit();
        }
        // Actualizar el estado "En línea" del administrador en cada interacción
        $userModel = new UserModel();
        $userModel->updateActivity($_SESSION['identity']->id);
    }

    public function auditTrail(){
        if($_SESSION['role'] != 'ADMIN'){ header("Location: " . base_url . "User/panel"); exit(); }
        
        $auditModel = new AuditModel();
        $logs = $auditModel->getAllLogs();
        
        require_once 'views/admin/audit_logs.php';
    }

    public function dashboard(){
        if($_SESSION['role'] != 'ADMIN'){ header("Location: " . base_url . "User/panel"); exit(); }
        
        $toolModel = new ToolModel(); 
        $projectModel = new ProjectModel(); 
        $requestModel = new RequestModel(); 
        
        $totalTools = $toolModel->countAll(); 
        $maintenance = $toolModel->countMaintenance();
        $activeProjects = $projectModel->countActive(); 
        $pendingRequests = $requestModel->countPending();
        
        $available = $toolModel->countAvailable();
        $inUse = $toolModel->countInUse();        
        
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

    public function tools(){
        $tool = new ToolModel(); 
        $herramientas = $tool->getAll(); 
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

    // ==========================================================
    // NUEVA FUNCIÓN: MESA DE AYUDA (INTERFAZ TIPO WHATSAPP)
    // ==========================================================
    public function helpDesk(){
        if($_SESSION['role'] != 'ADMIN'){ header("Location: " . base_url . "User/panel"); exit(); }
        
        $requestModel = new RequestModel(); 
        $todosLosReportes = $requestModel->getAll(); 
        $adminId = $_SESSION['identity']->id;
        
        $tickets = [];
        if($todosLosReportes) {
            while($row = $todosLosReportes->fetch_object()){
                if($row->type == 'REPORTE_DAÑO' || strpos($row->type, 'REPORTE') !== false){
                    // Calculamos mensajes no leídos para este ticket y este administrador
                    $row->unread_count = $requestModel->getUnreadCount($row->request_unique_id, $adminId);
                    $tickets[] = $row;
                }
            }
        }
        require_once 'views/admin/help_desk.php';
    }

    public function workshop(){
        if($_SESSION['role'] != 'ADMIN'){ header("Location: " . base_url . "User/panel"); exit(); }
        
        $maintModel = new MaintenanceModel();
        $activeMaintenance = $maintModel->getActive();
        $historyMaintenance = $maintModel->getHistory();
        $totalCost = $maintModel->getTotalCost();
        $activeCount = $maintModel->getCountActive();
        
        require_once 'views/admin/workshop.php';
    }

    public function resolveMaintenance() {
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST['maintenance_id']) && isset($_POST['resolution'])) {
            $maintId = (int)$_POST['maintenance_id'];
            $resolution = $_POST['resolution']; 
            $cost = isset($_POST['cost']) ? (float)$_POST['cost'] : 0.00;
            $adminId = $_SESSION['identity']->id;

            $maintModel = new MaintenanceModel();
            $toolModel = new ToolModel();
            $auditModel = new AuditModel(); 

            $maintenance = $maintModel->getOne($maintId);

            if($maintenance && $maintenance->status == 'EN_TALLER') {
                $toolId = $maintenance->tool_id;

                if($resolution == 'REPARADO') {
                    $maintModel->finishRepair($maintId, $cost);
                    $toolModel->updateStatus($toolId, 'DISPONIBLE');
                    $auditModel->logAction($adminId, 'TALLER', 'REPARACION_COMPLETADA', "Se reparó activo ID $toolId. Costo: $$cost.");
                    echo json_encode(['status' => 'success', 'msg' => 'Reparación auditada. Activo reincorporado a stock bodega.']);
                } elseif($resolution == 'IRREPARABLE') {
                    $maintModel->markIrreparable($maintId);
                    $toolModel->updateStatus($toolId, 'AGOTADO');
                    $toolModel->registerMovement($toolId, $adminId, 'BAJA_DAÑO', 1, $maintId, 'Pérdida Total auditada en Taller');
                    $auditModel->logAction($adminId, 'TALLER', 'BAJA_ACTIVO', "Se dio de baja por daño irreparable al activo ID $toolId.");
                    echo json_encode(['status' => 'success', 'msg' => 'Activo dado de baja del sistema por pérdida total.']);
                }
            } else { echo json_encode(['status' => 'error', 'msg' => 'Expediente no válido.']); }
        } else { echo json_encode(['status' => 'error', 'msg' => 'Faltan parámetros de auditoría.']); }
        exit();
    }

    public function qrCatalog(){
        if($_SESSION['role'] != 'ADMIN'){ header("Location: " . base_url . "User/panel"); exit(); }
        $toolModel = new ToolModel(); 
        $herramientas = $toolModel->getAll(); 
        require_once 'views/admin/qr_catalog.php';
    }

    public function getNotifications(){
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        $requestModel = new RequestModel();
        $data = [];
        try {
            if($_SESSION['role'] == 'ADMIN'){
                $pendientes = $requestModel->getAll(); 
                if($pendientes){
                    while($row = $pendientes->fetch_object()){
                        if($row->status == 'PENDIENTE'){
                            $row->is_admin = true;
                            $row->id = $row->request_unique_id ?? $row->id ?? 0;
                            $row->tool_name = $row->tool_name ?? $row->description ?? 'Sin detalle';
                            $row->fullname = $row->fullname ?? 'Usuario Eliminado';
                            if(isset($row->quantity) && $row->quantity > 1){ $row->tool_name .= " (x{$row->quantity})"; }
                            $row->request_date = isset($row->created_at) ? date('d/m/Y', strtotime($row->created_at)) : '--/--';
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
                        $row->tool_name = $row->description ?? 'Sin detalle';
                        $row->request_date = isset($row->created_at) ? date('d/m/Y', strtotime($row->created_at)) : '--/--';
                        $data[] = $row;
                    }
                }
            }
        } catch (Exception $e) { $data = []; }
        echo json_encode($data);
        exit();
    }

    public function handleRequest(){
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        $processed = false;
        $msg = "Error desconocido";

        if(isset($_GET['id']) && isset($_GET['status'])){
            $requestId = (int)$_GET['id'];
            $newStatus = $_GET['status'];
            
            $requestModel = new RequestModel();
            $toolModel = new ToolModel();
            $assignModel = new AssignmentModel();
            $userModel = new UserModel(); 
            $auditModel = new AuditModel(); 
            
            $request = $requestModel->getOne($requestId); 

            if($request && $request->status == 'PENDIENTE'){
                
                $targetUser = $userModel->getOne($request->user_id);
                $targetEmail = $targetUser->email ?? '';
                $targetName = $targetUser->fullname ?? 'Usuario';
                $toolNameStr = $request->tool_name ?? $request->description ?? 'Herramienta solicitada';

                if($newStatus == 'APROBADO'){
                    $toolId = $request->tool_id;
                    $qty = $request->quantity;
                    $projectId = $request->project_id ?? null; 
                    $requestUserId = $request->user_id; 
                    $adminId = $_SESSION['identity']->id; 

                    if($toolId){
                        $success = $toolModel->registerMovement($toolId, $adminId, 'SALIDA_PRESTAMO', $qty, $requestId);
                        if($success){
                            $requestModel->updateStatus($requestId, 'APROBADO');
                            if(method_exists($assignModel, 'createAssignment')) {
                                $assignModel->createAssignment($toolId, $requestUserId, $qty, $projectId, $requestId);
                            }
                            $processed = true;
                            $msg = "Solicitud aprobada y asignada a obra correctamente.";

                            $auditModel->logAction($adminId, 'SOLICITUDES', 'APROBACION', "Aprobó préstamo de '$toolNameStr' (Cant: $qty) para $targetName.");

                            if(file_exists('config/MailService.php') && !empty($targetEmail)){
                                require_once 'config/MailService.php';
                                $mailer = new MailService();
                                $mailer->sendRequestNotification($targetEmail, $targetName, $toolNameStr, 'APROBADO');
                            }
                        } else {
                            $processed = false;
                            $msg = "Error: Stock insuficiente u ocupado por otra transacción.";
                        }
                    } else {
                        $requestModel->updateStatus($requestId, 'APROBADO');
                        $processed = true;
                        $msg = "Aprobado (Sin descuento - Herramienta no vinculada)";
                    }

                } else {
                    $requestModel->updateStatus($requestId, 'RECHAZADO');
                    $processed = true;
                    $msg = "Solicitud rechazada.";
                    
                    $auditModel->logAction($_SESSION['identity']->id, 'SOLICITUDES', 'RECHAZO', "Rechazó préstamo de '$toolNameStr' para $targetName.");

                    if(file_exists('config/MailService.php') && !empty($targetEmail)){
                        require_once 'config/MailService.php';
                        $mailer = new MailService();
                        $mailer->sendRequestNotification($targetEmail, $targetName, $toolNameStr, 'RECHAZADO');
                    }
                }
            } else { $msg = "La solicitud ya fue procesada o no existe."; }
        }

        if(isset($_GET['ajax'])){ echo json_encode(['status' => $processed ? 'success' : 'error', 'msg' => $msg]); exit(); }
        header("Location: " . base_url . "Admin/reports");
        exit();
    }
    
    // ==========================================================
    // LÓGICA DEL CHAT DE SOPORTE AVANZADO (ESTILO WHATSAPP)
    // ==========================================================
    public function loadChat() {
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_GET['id'])) {
            $reqId = (int)$_GET['id'];
            $adminId = $_SESSION['identity']->id;
            
            $reqModel = new RequestModel();
            $userModel = new UserModel();

            // Averiguar de quién es el ticket para mostrar si está "En línea"
            $ticket = $reqModel->getOne($reqId);
            $userStatus = "Desconocido";
            if($ticket) {
                $userStatus = $userModel->getUserStatus($ticket->user_id);
            }

            // Marcar mensajes como leídos (doble check azul) ANTES de cargar
            $reqModel->markMessagesAsRead($reqId, $adminId);

            // Cargar los mensajes (filtrando si el admin vació su chat antes)
            $messages = $reqModel->getChatMessages($reqId, 'ADMIN'); 
            
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
                'user_status' => $userStatus // Retornamos si está En línea
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
            $audit = new AuditModel();

            $saved = $reqModel->saveChatMessage($reqId, $senderId, $msg);
            
            if($saved) {
                $audit->logAction($senderId, 'MESA DE AYUDA', 'MENSAJE_ENVIADO', "Respondió en el ticket de soporte #$reqId.");

                $incident = $reqModel->getOne($reqId);
                
                if($incident->status == 'PENDIENTE') {
                    $reqModel->updateStatus($reqId, 'RESUELTO'); 
                }

                if($incident && !empty($incident->email)) {
                    require_once 'config/MailService.php';
                    $mailer = new MailService();
                    $mailer->sendIncidentReply($incident->email, $incident->fullname, $incident->description, $msg);
                }

                echo json_encode(['status' => 'success', 'msg' => 'Mensaje enviado.']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Error al guardar el mensaje.']);
            }
        }
        exit();
    }

    // Vaciar el historial del chat solo para el administrador
    public function clearChat() {
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST['request_id'])) {
            $reqId = (int)$_POST['request_id'];
            $reqModel = new RequestModel();
            
            if($reqModel->clearChat($reqId, 'ADMIN')) {
                echo json_encode(['status' => 'success', 'msg' => 'Se ha vaciado el chat en tu dispositivo.']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Fallo al intentar vaciar el chat.']);
            }
        }
        exit();
    }

    public function changeStatus(){ $this->handleRequest(); }

    public function processReturn() {
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        
        if(isset($_POST['assignment_id']) && isset($_POST['return_condition'])){
            $assignmentId = (int)$_POST['assignment_id'];
            $condition = $_POST['return_condition']; 
            $adminId = $_SESSION['identity']->id;
            
            $assignModel = new AssignmentModel();
            $assignment = $assignModel->getOne($assignmentId); 
            $auditModel = new AuditModel();
            
            if($assignment && $assignment->status == 'ACTIVO'){
                $toolModel = new ToolModel();
                
                if($condition == 'BUENO') {
                    $toolModel->registerMovement($assignment->tool_id, $adminId, 'DEVOLUCION', $assignment->quantity, $assignmentId, 'Retorno Operativo Estándar');
                    $auditModel->logAction($adminId, 'AUDITORIA', 'RETORNO_ACTIVO', "Recibió activo #$assignment->tool_id en estado BUENO (Asignación #$assignmentId).");
                } 
                elseif ($condition == 'PERDIDO' || $condition == 'DESTRUIDO') {
                    $toolModel->registerMovement($assignment->tool_id, $adminId, 'BAJA_DAÑO', $assignment->quantity, $assignmentId, "Baja por auditoría: $condition");
                    $auditModel->logAction($adminId, 'AUDITORIA', 'PERDIDA_DESTRUCCION', "Reportó pérdida/destrucción de activo #$assignment->tool_id (Asignación #$assignmentId).");
                }
                elseif ($condition == 'DAÑADO') {
                    $toolModel->registerMovement($assignment->tool_id, $adminId, 'DEVOLUCION', $assignment->quantity, $assignmentId, 'Retorno con fallas técnicas');
                    $toolModel->updateStatus($assignment->tool_id, 'MANTENIMIENTO');
                    
                    $maintModel = new MaintenanceModel();
                    $maintModel->create($assignment->tool_id, $adminId, "Daño reportado en Acta de Recepción (Asignación #{$assignmentId}).");
                    $auditModel->logAction($adminId, 'AUDITORIA', 'ENVIO_TALLER', "Envió activo #$assignment->tool_id a taller por daño reportado.");
                }
                
                if(method_exists($assignModel, 'markAsReturned')) { $assignModel->markAsReturned($assignmentId, $condition); }
                echo json_encode(['status' => 'success', 'msg' => 'Auditoría de retorno registrada correctamente.']);
            } else { echo json_encode(['status' => 'error', 'msg' => 'Asignación no válida o ya retornada.']); }
        } else { echo json_encode(['status' => 'error', 'msg' => 'Faltan parámetros de auditoría.']); }
        exit();
    }

    public function saveTool(){
        if(isset($_POST['name'])){
             $img = "default.png"; 
             if(isset($_FILES['image']) && $_FILES['image']['size'] > 0 && $_FILES['image']['error'] == 0){ 
                 $fileTmpPath = $_FILES['image']['tmp_name'];
                 $fileName = $_FILES['image']['name'];
                 $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                 $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                 if(in_array($fileExt, $allowedExts) && getimagesize($fileTmpPath) !== false){
                     $img = uniqid('tool_') . '.' . $fileExt; 
                     move_uploaded_file($fileTmpPath, 'assets/img/'.$img); 
                 }
             }
             
             $t = new ToolModel(); 
             $t->setName($_POST['name']); 
             $t->setCategory($_POST['category']); 
             $t->setStatus($_POST['status']); 
             $t->setImage($img); 
             $stock = isset($_POST['stock_total']) && $_POST['stock_total'] > 0 ? (int)$_POST['stock_total'] : 1;
             $min = isset($_POST['stock_min']) ? (int)$_POST['stock_min'] : 5;
             $t->setStockTotal($stock);
             $t->setStockAvailable($stock); 
             $t->setStockMin($min);
             $t->save();

             $auditModel = new AuditModel();
             $auditModel->logAction($_SESSION['identity']->id, 'HERRAMIENTAS', 'CREACION', "Ingresó nueva herramienta: {$_POST['name']} al sistema.");
        }
        header("Location: " . base_url . "Admin/tools");
    }

    public function updateTool(){
        $id = $_GET['id'] ?? null; 
        if(!$id && isset($_POST['id'])) $id = $_POST['id'];
        
        if(isset($_POST) && $id){
             $t = new ToolModel(); 
             if(isset($_FILES['image']) && $_FILES['image']['size'] > 0 && $_FILES['image']['error'] == 0){ 
                 $fileTmpPath = $_FILES['image']['tmp_name'];
                 $fileName = $_FILES['image']['name'];
                 $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                 $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                 if(in_array($fileExt, $allowedExts) && getimagesize($fileTmpPath) !== false){
                     $img = uniqid('tool_') . '.' . $fileExt; 
                     move_uploaded_file($fileTmpPath, 'assets/img/'.$img); 
                     $t->setImage($img); 
                 }
             }

             $t->setName($_POST['name']); 
             $t->setCategory($_POST['category']); 
             $t->setStatus($_POST['status']);
             if(isset($_POST['stock_total'])) $t->setStockTotal($_POST['stock_total']);
             if(isset($_POST['stock_min'])) $t->setStockMin($_POST['stock_min']);
             $t->update($id);

             $auditModel = new AuditModel();
             $auditModel->logAction($_SESSION['identity']->id, 'HERRAMIENTAS', 'EDICION', "Modificó ficha técnica de la herramienta ID: $id ({$_POST['name']}).");
         }
         header("Location: " . base_url . "Admin/tools");
    }

    public function deleteTool(){
        $id = $_GET['id'] ?? null; 
        if($id){ 
            $t = new ToolModel(); 
            $auditModel = new AuditModel();
            $auditModel->logAction($_SESSION['identity']->id, 'HERRAMIENTAS', 'ELIMINACION', "Eliminó irreversiblemente la herramienta con ID: $id del sistema.");
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
        } else { header("Location: " . base_url . "Admin/tools"); }
    }

    public function saveUser(){
        if(isset($_POST['fullname'])){
            $u = new UserModel(); $u->setFullname($_POST['fullname']); $u->setEmail($_POST['email']);
            $u->setPassword($_POST['password']); $u->setRole($_POST['role']); $u->save();

            $auditModel = new AuditModel();
            $auditModel->logAction($_SESSION['identity']->id, 'PERSONAL', 'CREACION_USUARIO', "Creó el usuario {$_POST['fullname']} con rol {$_POST['role']}.");
        }
        header("Location: " . base_url . "Admin/users");
    }

    // ====================================================================
    // ELIMINACIÓN DE USUARIO CON MANEJO DE EXCEPCIONES (TRY-CATCH)
    // ====================================================================
    public function deleteUser(){
        $id = $_GET['id'] ?? null; 
        if($id){ 
            try {
                $u = new UserModel(); 
                // Intentamos borrar al usuario
                $u->delete($id); 
                
                // Si funciona, registramos en la caja negra
                $auditModel = new AuditModel();
                $auditModel->logAction($_SESSION['identity']->id, 'PERSONAL', 'ELIMINACION_USUARIO', "Eliminó del sistema al usuario con ID: $id.");
                
                $_SESSION['alert_message'] = "Usuario eliminado exitosamente del sistema.";
                $_SESSION['alert_icon'] = "success";

            } catch (mysqli_sql_exception $e) {
                // ATRAMAPOS EL ERROR: Si tiene historial, la base de datos lanza la excepción y caemos aquí
                $_SESSION['alert_message'] = "No se puede eliminar este usuario porque tiene herramientas asignadas o historial de reportes. Pida que devuelva los activos primero.";
                $_SESSION['alert_icon'] = "error";
            }
        } 
        header("Location: " . base_url . "Admin/users");
        exit();
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

            $auditModel = new AuditModel();
            $auditModel->logAction($_SESSION['identity']->id, 'PERSONAL', 'MODIFICACION_USUARIO', "Alteró credenciales o permisos del usuario ID: $id ({$_POST['fullname']}).");
        }
        header("Location: " . base_url . "Admin/users");
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
                'autor' => 'Administrador'
            ];
            $project = new ProjectModel(); 
            $project->setName($_POST['name']); 
            $project->setDescription(json_encode($infoData, JSON_UNESCAPED_UNICODE));
            $project->setLocation($_POST['address']); 
            $project->setLat($_POST['lat']); 
            $project->setLng($_POST['lng']); 
            $project->setStatus($_POST['status']); 
            $project->setImage($image);
            $project->save();

            $auditModel = new AuditModel();
            $auditModel->logAction($_SESSION['identity']->id, 'PROYECTOS', 'CREACION', "Creó el frente de obra: {$_POST['name']}.");
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

            $auditModel = new AuditModel();
            $auditModel->logAction($_SESSION['identity']->id, 'PROYECTOS', 'EDICION', "Editó datos del proyecto ID: $id ({$_POST['name']}).");
        }
        header("Location: " . base_url . "Admin/map");
    }

    public function deleteProject(){
        $id = $_GET['id'] ?? null; 
        if($id){ 
            $p = new ProjectModel(); 
            $auditModel = new AuditModel();
            $auditModel->logAction($_SESSION['identity']->id, 'PROYECTOS', 'ELIMINACION', "Eliminó del mapa el proyecto ID: $id.");
            $p->delete($id); 
        } 
        header("Location: " . base_url . "Admin/map");
    }

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
            $auditModel = new AuditModel();
            $auditModel->logAction($_SESSION['identity']->id, 'SISTEMA', 'PURGA_DATOS', "Purgó $count registros del historial de solicitudes.");

            echo json_encode(['status' => 'success', 'msg' => "$count registros purgados."]);
        } else { echo json_encode(['status' => 'error', 'msg' => 'Nada seleccionado.']); }
    }

    public function printReports(){
        $requestModel = new RequestModel();
        $reportes = $requestModel->getAll(); 
        require_once 'views/admin/print_reports.php';
    }

    public function exportExcel(){
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();

        $auditModel = new AuditModel();
        $auditModel->logAction($_SESSION['identity']->id, 'REPORTES', 'DESCARGA_DATOS', "Exportó el reporte de auditoría completo a formato CSV.");

        $requestModel = new RequestModel();
        $reportes = $requestModel->getAll();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Auditoria_SICOT_ERP_' . date('Y_m_d_His') . '.csv');
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen('php://output', 'w');
        fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($output, ['ID AUDITORIA', 'FUNCIONARIO', 'RANGO', 'CLASIFICACION', 'DETALLE DEL MOVIMIENTO', 'FECHA DE REGISTRO', 'ESTADO ACTUAL'], ';');

        if($reportes && $reportes->num_rows > 0){
            while($row = $reportes->fetch_object()){
                $idReq = $row->request_unique_id ?? $row->id ?? 'N/A';
                $user = $row->fullname ?? 'Usuario Borrado';
                $role = $row->role ?? 'N/A';
                $type = $row->type ?? 'INDEFINIDO';
                $desc = $row->description ?? 'Sin detalles';
                $date = isset($row->created_at) ? date('d/m/Y H:i:s', strtotime($row->created_at)) : 'N/A';
                $status = $row->status ?? 'N/A';
                fputcsv($output, ["#".$idReq, $user, $role, $type, $desc, $date, $status], ';');
            }
        } else { fputcsv($output, ['No existen registros en el historial.'], ';'); }
        fclose($output);
        exit();
    }

    public function getToolHistory() {
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_GET['id'])){
            $toolId = (int)$_GET['id'];
            $toolModel = new ToolModel();
            $tool = $toolModel->getOne($toolId);
            if(!$tool) { echo json_encode(['status' => 'error', 'msg' => 'Herramienta no encontrada.']); exit(); }

            $historyResult = $toolModel->getToolHistory($toolId);
            $history = [];
            if($historyResult) {
                while($row = $historyResult->fetch_assoc()) {
                    $row['created_at_formatted'] = date('d M Y - h:i A', strtotime($row['created_at']));
                    $history[] = $row;
                }
            }
            echo json_encode([
                'status' => 'success',
                'tool' => [
                    'name' => $tool->name,
                    'category' => str_replace('_', ' ', $tool->category),
                    'stock_total' => $tool->stock_total,
                    'stock_available' => $tool->stock_available,
                    'image' => $tool->image
                ],
                'data' => $history
            ]);
        } else { echo json_encode(['status' => 'error', 'msg' => 'ID no proporcionado.']); }
        exit();
    }

    public function profile(){
        $userId = $_SESSION['identity']->id;
        $userModel = new UserModel();
        $user = $userModel->getOne($userId);
        require_once 'views/admin/profile.php';
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

            $save = $userModel->updateProfile($id);
            if($save){
                $_SESSION['identity']->fullname = $_POST['fullname'];
                $_SESSION['alert_message'] = "Los datos de su perfil han sido actualizados con éxito.";
                $_SESSION['alert_icon'] = "success";
            }
        }
        header("Location: " . base_url . "Admin/profile");
    }

    public function updatePassword(){
        if(isset($_POST['password']) && !empty($_POST['password'])){
            $id = $_SESSION['identity']->id;
            $userModel = new UserModel();
            $userModel->setPassword($_POST['password']);
            $save = $userModel->updatePassword($id);
            if($save){
                $_SESSION['alert_message'] = "Su contraseña ha sido modificada correctamente.";
                $_SESSION['alert_icon'] = "success";
            }
        }
        header("Location: " . base_url . "Admin/profile");
    }
}
?>