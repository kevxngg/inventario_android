<?php
// Define la URL base del proyecto
// IMPORTANTE: Asegúrate de que coincida con tu carpeta en htdocs
define("base_url", "http://localhost/inventario_android/");

// Define el controlador y acción por defecto (La Landing Page)
define("controller_default", "HomeController");
define("action_default", "index");

// Zona horaria
date_default_timezone_set('America/Bogota');