<?php
// Datos de configuración predeterminados de XAMPP
$servidor = "localhost";
$usuario = "root";       // XAMPP usa "root" como usuario por defecto
$contrasena = "";        // XAMPP no tiene contraseña por defecto (déjalo vacío)
$base_datos = "hospital_db"; // El nombre de la base de datos que creaste

// Intentar crear la conexión
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Verificar si hubo un error
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
} 

echo "¡Conexión exitosa a la base de datos del hospital!";
?>