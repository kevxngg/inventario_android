<?php
require_once 'models/ProjectModel.php';
require_once 'models/ToolModel.php';
require_once 'models/RequestModel.php';

class UserController {
    
    public function __construct(){
        if(!isset($_SESSION['role']) || $_SESSION['role'] != 'USER'){
            header("Location: " . base_url . "Auth/login");
            exit();
        }
    }

    // 1. Panel Principal
    public function panel(){
        $userId = $_SESSION['identity']->id;
        
        $project = new ProjectModel();
        $misObras = $project->getAll(); 

        $requestModel = new RequestModel();
        $misSolicitudes = $requestModel->getRequestsByUser($userId);

        require_once 'views/user/panel.php';
    }

    // 2. Guardar Reporte (Desde el Panel - Modal Rojo)
    public function saveReport(){
        if(isset($_POST)){
            // Si viene del modal del panel, tomamos el tipo y descripción
            $type = $_POST['type']; 
            $description = $_POST['description'];
            $user_id = $_SESSION['identity']->id;

            if($description){
                $request = new RequestModel();
                $request->setUserId($user_id);
                $request->setType($type);
                $request->setDescription($description);
                $request->save();
            }
        }
        header("Location: " . base_url . "User/panel");
    }

    // 3. Solicitar Herramienta (Desde el CATÁLOGO / Menú Lateral)
    // ESTA FUNCIÓN CONECTA EL CATÁLOGO CON EL ADMIN
    public function requestTool(){
        if(isset($_POST['tool_id'])){
            $toolId = $_POST['tool_id'];
            $userId = $_SESSION['identity']->id;

            // 1. Buscamos el nombre de la herramienta
            $toolModel = new ToolModel();
            $tool = $toolModel->getOne($toolId);

            if($tool){
                // 2. Creamos la solicitud en la tabla 'requests' para que el Admin la vea
                $request = new RequestModel();
                $request->setUserId($userId);
                $request->setType('SOLICITUD_HERRAMIENTA');
                // Guardamos el nombre como descripción
                $request->setDescription("Solicitud de Equipo: " . $tool->name); 
                $save = $request->save();

                if($save){
                    echo json_encode(['status' => 'success', 'msg' => 'Solicitud enviada al administrador']);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Error al guardar']);
                }
            }
        }
    }

    // 4. Vista de Catálogo
    public function catalog(){
        $tool = new ToolModel();
        $disponibles = $tool->getAllActive(); 
        require_once 'views/user/catalog.php';
    }

    // 5. Vista de Reportes (Opcional)
    public function reports(){
        $user_id = $_SESSION['identity']->id;
        $requestModel = new RequestModel();
        $misReportes = $requestModel->getRequestsByUser($user_id);
        require_once 'views/user/reports.php';
    }
}
?>