<?php
/**
 * SISTEMA DE GESTIÓN DE INVENTARIO
 * Archivo: index.php
 * Función: Punto de entrada único (Front Controller)
 */

// 1. INICIAR SESIÓN GLOBAL
session_start();

// 2. CARGAR CONFIGURACIONES BÁSICAS
require_once 'config/parameters.php';
require_once 'config/db.php';

// 3. AUTOLOAD (Carga automática de Controladores)
// Esto evita tener que hacer "require" de cada archivo manualmente.
function controllers_autoload($classname){
    // Solo cargamos si termina en "Controller"
    if(file_exists('controllers/' . $classname . '.php')){
        require_once 'controllers/' . $classname . '.php';
    }
}
spl_autoload_register('controllers_autoload');

// 4. LÓGICA DE ENRUTAMIENTO (ROUTING)

// A) Determinar el Controlador
if(isset($_GET['controller'])){
    $nombre_controlador = $_GET['controller'] . 'Controller';
} elseif(!isset($_GET['controller']) && !isset($_GET['action'])){
    // Si no hay nada en la URL, cargar la Landing Page
    $nombre_controlador = controller_default;
} else {
    show_error();
    exit();
}

// B) Comprobar si existe la clase del Controlador
if(class_exists($nombre_controlador)){
    $controlador = new $nombre_controlador();
    
    // C) Determinar la Acción (Método)
    if(isset($_GET['action']) && method_exists($controlador, $_GET['action'])){
        $action = $_GET['action'];
        $controlador->$action();
    } elseif(!isset($_GET['action']) && !isset($_GET['controller'])){
        // Acción por defecto (index)
        $action_default = action_default;
        $controlador->$action_default();
    } else {
        show_error();
    }
} else {
    show_error();
}

// FUNCIÓN DE AYUDA PARA ERRORES (404)
function show_error(){
    // Puedes crear una vista bonita para el 404 después
    $error = new HomeController();
    // Por ahora, redirigimos al inicio para no asustar al usuario
    $error->index(); 
}
?>