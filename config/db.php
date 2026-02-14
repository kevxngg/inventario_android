<?php
class Database {
    public static function connect(){
        // Parámetros: Servidor, Usuario, Contraseña, NombreBD
        $db = new mysqli("localhost", "root", "", "inventario_android");
        
        // Verificar errores de conexión
        if($db->connect_error){
            die("Error de conexión a la Base de Datos: " . $db->connect_error);
        }

        // Configurar codificación a UTF-8 (Para tildes y ñ)
        $db->query("SET NAMES 'utf8'");
        
        return $db;
    }
}