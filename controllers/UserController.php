<?php
require_once 'models/ProjectModel.php';
require_once 'models/ToolModel.php';
require_once 'models/RequestModel.php';

class UserController {
    
    public function __construct(){
        // Validación de sesión de seguridad
        if(!isset($_SESSION['role']) || $_SESSION['role'] != 'USER'){
            header("Location: " . base_url . "Auth/login");
            exit();
        }
    }

    // --- 1. DASHBOARD DE USUARIO ---
    public function dashboard(){
        $userId = $_SESSION['identity']->id;
        $projectModel = new ProjectModel();
        $requestModel = new RequestModel();

        // Obtener datos para las tarjetas y gráficas
        $misObras = $projectModel->getAll(); // En un futuro podrías filtrar por usuario asignado
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

    // --- 2. PANEL DE GESTIÓN (Mapa y Lista) ---
    public function panel(){
        $userId = $_SESSION['identity']->id;
        
        $project = new ProjectModel();
        $misObras = $project->getAll(); 
        
        $requestModel = new RequestModel();
        $misSolicitudes = $requestModel->getRequestsByUser($userId);
        
        require_once 'views/user/panel.php';
    }

    // --- 3. CATÁLOGO DE HERRAMIENTAS (NUEVO) ---
    public function catalog(){
        $tool = new ToolModel();
        // Usamos getAllActive() para mostrar solo lo que tiene stock > 0 o está disponible
        $disponibles = $tool->getAllActive(); 
        require_once 'views/user/catalog.php';
    }

    // --- 4. PROCESAR SOLICITUD DE HERRAMIENTA (NÚCLEO ENTERPRISE) ---
    public function requestTool(){
        // Limpiamos buffer por si acaso
        error_reporting(0);
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');

        if(isset($_POST['tool_id']) && isset($_POST['quantity'])){
            $toolId = (int)$_POST['tool_id'];
            $qty = (int)$_POST['quantity'];
            $userId = $_SESSION['identity']->id;

            // Validaciones básicas
            if($qty <= 0){
                echo json_encode(['status' => 'error', 'msg' => 'La cantidad debe ser mayor a 0.']);
                exit();
            }

            // 1. Verificar disponibilidad real en Base de Datos (Seguridad Backend)
            $toolModel = new ToolModel();
            $tool = $toolModel->getOne($toolId);

            if(!$tool){
                echo json_encode(['status' => 'error', 'msg' => 'La herramienta no existe.']);
                exit();
            }

            // Validar si hay suficiente stock disponible
            if($qty > $tool->stock_available){
                echo json_encode([
                    'status' => 'error', 
                    'msg' => "Stock insuficiente. Solo quedan {$tool->stock_available} unidades disponibles."
                ]);
                exit();
            }

            // 2. Crear la solicitud estructurada
            $request = new RequestModel();
            $request->setUserId($userId);
            $request->setToolId($toolId);      // Guardamos el ID numérico
            $request->setQuantity($qty);       // Guardamos la cantidad numérica
            $request->setType('SOLICITUD_HERRAMIENTA');
            
            // Descripción automática para compatibilidad visual
            $desc = "Solicitud: {$tool->name} (x{$qty})";
            $request->setDescription($desc);
            
            $save = $request->save();

            if($save){
                // NOTA IMPORTANTE:
                // En un sistema Enterprise de Inventario Físico, el stock NO se descuenta 
                // al momento de pedir, sino al momento de APROBAR/ENTREGAR (AdminController).
                // Esto evita que usuarios malintencionados bloqueen el inventario solicitando todo sin permiso.
                echo json_encode(['status' => 'success', 'msg' => 'Solicitud enviada. Esperando aprobación del admin.']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Error en base de datos al guardar.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos.']);
        }
        exit();
    }

    // --- 5. REPORTE DE DAÑOS ---
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
                // Reportes de daño no llevan tool_id ni quantity obligatorios en este flujo simple
                // pero podrías agregarlos si quisieras hacer un reporte específico por ítem
                $request->save();
                
                $_SESSION['alert_message'] = "Reporte enviado correctamente.";
                $_SESSION['alert_icon'] = "success";
            }
        }
        header("Location: " . base_url . "User/panel");
    }

    // --- 6. GESTIÓN DE PROYECTOS (USUARIO) ---
    public function saveProject(){
        if(isset($_POST['name'])){
            $image = "default_project.png"; 
            if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){ 
                $image = $_FILES['image']['name']; 
                move_uploaded_file($_FILES['image']['tmp_name'], 'assets/img/'.$image); 
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
        }
        header("Location: " . base_url . "User/panel");
    }

    // --- 7. ELIMINAR SOLICITUDES (SOLO OCULTAR) ---
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

            echo json_encode([
                'status' => $count > 0 ? 'success' : 'error', 
                'msg' => $count > 0 ? "$count registros eliminados." : 'No se pudieron eliminar.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'No has seleccionado nada.']);
        }
    }

    public function reports(){
        $user_id = $_SESSION['identity']->id;
        $requestModel = new RequestModel();
        $misReportes = $requestModel->getRequestsByUser($user_id);
        require_once 'views/user/reports.php';
    }
}
?>