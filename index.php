<?php
/**
 * SISTEMA DE GESTIÓN DE INVENTARIO
 * Archivo: index.php
 * Función: Punto de entrada único (Front Controller)
 */

// 1. ACTIVAR BUFFER (Captura espacios en blanco accidentales)
ob_start();

// 2. INICIAR SESIÓN GLOBAL
session_start();

// 3. CARGAR CONFIGURACIONES BÁSICAS
require_once 'config/parameters.php';
require_once 'config/db.php';

// 4. AUTOLOAD (Carga automática de Controladores)
function controllers_autoload($classname){
    if(file_exists('controllers/' . $classname . '.php')){
        require_once 'controllers/' . $classname . '.php';
    }
}
spl_autoload_register('controllers_autoload');

// 5. LÓGICA DE ENRUTAMIENTO (ROUTING)

// A) Determinar el Controlador
if(isset($_GET['controller'])){
    $nombre_controlador = $_GET['controller'] . 'Controller';
} elseif(!isset($_GET['controller']) && !isset($_GET['action'])){
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
        $action_default = action_default;
        $controlador->$action_default();
    } else {
        show_error();
    }
} else {
    show_error();
}

function show_error(){
    $error = new HomeController();
    $error->index(); 
}

// 6. ENVIAR SALIDA FINAL
ob_end_flush();