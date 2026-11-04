<?php
/**
 * =========================================================================
 * S.I.G.S.M. - CONEXIÓN A LA BASE DE DATOS
 * =========================================================================
 * Este script establece la conexión principal a la base de datos MySQL/MariaDB
 * utilizando la extensión MySQLi y gestiona los posibles errores de acceso.
 */

// Parámetros de configuración del servidor de base de datos
$servidor = "localhost";
$usuario = "root";       
$contrasena = "";        
$base_datos = "hospital_db"; 

// Instancia de la conexión MySQLi
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Verificación de errores de conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
} 

?>