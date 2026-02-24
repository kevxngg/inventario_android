<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ToolModel.php';
require_once __DIR__ . '/../models/ProjectModel.php';
require_once __DIR__ . '/../models/RequestModel.php';

class ApiController {

    public function __construct() {
        // 1. Configuración Estricta de Cabeceras para API REST
        // Esto permite que el móvil consuma los datos sin bloqueos de seguridad (CORS)
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, GET");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    /**
     * Método auxiliar para capturar los datos JSON enviados desde Android (Retrofit / Volley)
     */
    private function getJsonInput() {
        return json_decode(file_get_contents("php://input"), true);
    }

    // =================================================================
    // 1. ENDPOINT: AUTENTICACIÓN MÓVIL (LOGIN)
    // =================================================================
    public function login() {
        $data = $this->getJsonInput();
        
        if (!isset($data['email']) || !isset($data['password'])) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan credenciales de acceso']);
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->login($data['email'], $data['password']);

        if ($user && is_object($user)) {
            echo json_encode([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'fullname' => $user->fullname,
                    'role' => $user->role,
                    'email' => $user->email
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas o usuario no registrado']);
        }
    }

    // =================================================================
    // 2. ENDPOINT: SINCRONIZACIÓN DE CATÁLOGO (OFFLINE/ONLINE)
    // =================================================================
    public function getTools() {
        $toolModel = new ToolModel();
        $tools = $toolModel->getAll();
        
        $result = [];
        if ($tools) {
            while ($row = $tools->fetch_assoc()) { 
                $result[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    // =================================================================
    // 3. ENDPOINT: DESCARGAR OBRAS ACTIVAS
    // =================================================================
    public function getProjects() {
        $projectModel = new ProjectModel();
        $projects = $projectModel->getAll();
        
        $result = [];
        if ($projects) {
            while ($row = $projects->fetch_assoc()) {
                $result[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    // =================================================================
    // 4. ENDPOINT: MOTOR DE LECTURA QR (ESCANEO EN OBRA)
    // =================================================================
    public function scanQR() {
        $data = $this->getJsonInput();
        
        if (!isset($data['tool_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID de activo no proporcionado por el escáner']);
            return;
        }

        $toolModel = new ToolModel();
        $tool = $toolModel->getOne($data['tool_id']);

        if ($tool) {
            echo json_encode(['status' => 'success', 'data' => $tool]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'El Código QR no corresponde a un activo registrado en SICOT']);
        }
    }

    // =================================================================
    // 5. ENDPOINT: CREAR SOLICITUD / CHECK-OUT DESDE EL MÓVIL
    // =================================================================
    public function requestTool() {
        $data = $this->getJsonInput();
        
        if (!isset($data['user_id']) || !isset($data['tool_id']) || !isset($data['quantity'])) {
            echo json_encode(['status' => 'error', 'message' => 'Paquete de datos incompleto']);
            return;
        }

        $project_id = isset($data['project_id']) && !empty($data['project_id']) ? $data['project_id'] : null;
        $description = isset($data['description']) ? $data['description'] : 'Requisición generada mediante App Android';

        // Corrección: Usar Setters y la función save() de la clase RequestModel
        $request = new RequestModel();
        $request->setUserId($data['user_id']);
        $request->setToolId($data['tool_id']);
        $request->setQuantity($data['quantity']);
        $request->setType('SOLICITUD_HERRAMIENTA');
        $request->setDescription($description);
        
        if ($project_id) {
            $request->setProjectId($project_id);
        }

        // Insertar en la base de datos
        $result = $request->save();

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Solicitud transmitida exitosamente a la Bodega Central']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Fallo en la comunicación con el servidor ERP']);
        }
    }
}
?>