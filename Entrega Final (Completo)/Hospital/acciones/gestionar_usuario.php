<?php
/**
 * =========================================================================
 * S.I.G.S.M. - PROCESADOR DE GESTIÓN DE USUARIOS
 * =========================================================================
 * Procesa la creación, modificación completa de datos, cambio de rol 
 * y eliminación lógica/física de usuarios.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../conexion.php";

// Exclusivo Administrador (Rol 1)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol']) || (int)$_SESSION['id_rol'] !== 1) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validar CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Acción no autorizada (Error CSRF).");
    }

    $accion = $_POST['accion'] ?? '';

    // 1. Alta de Usuario
    if ($accion === 'crear_usuario') {
        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $cedula   = trim($_POST['cedula'] ?? '');
        $correo   = trim($_POST['correo'] ?? '');
        $pass     = trim($_POST['password'] ?? '');
        $id_rol   = (int)($_POST['id_rol'] ?? 2);

        if (!empty($nombre) && !empty($apellido) && !empty($cedula) && !empty($correo) && !empty($pass)) {
            $pass_hash = password_hash($pass, PASSWORD_BCRYPT);
            
            try {
                $stmt = $conexion->prepare("INSERT INTO usuario (nombre, apellido, cedula, correo, contrasena, id_rol) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssi", $nombre, $apellido, $cedula, $correo, $pass_hash, $id_rol);
                
                if ($stmt->execute()) {
                    header("Location: ../vistas/gestion_usuarios.php?ok=1");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                die("Error al registrar usuario: " . $e->getMessage());
            }
        } else {
            header("Location: ../vistas/gestion_usuarios.php?error=campos_vacios");
            exit();
        }
    }

    // 2. Edición Completa de Datos del Usuario
    if ($accion === 'editar_usuario') {
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);
        $nombre     = trim($_POST['nombre'] ?? '');
        $apellido   = trim($_POST['apellido'] ?? '');
        $cedula     = trim($_POST['cedula'] ?? '');
        $correo     = trim($_POST['correo'] ?? '');
        $pass       = trim($_POST['password'] ?? '');
        $id_rol     = (int)($_POST['id_rol'] ?? 2);

        if ($id_usuario > 0 && !empty($nombre) && !empty($apellido) && !empty($cedula) && !empty($correo)) {
            try {
                // Si el Administrador ingresó una nueva contraseña, la actualizamos
                if (!empty($pass)) {
                    $pass_hash = password_hash($pass, PASSWORD_BCRYPT);
                    $stmt = $conexion->prepare("UPDATE usuario SET nombre = ?, apellido = ?, cedula = ?, correo = ?, contrasena = ?, id_rol = ? WHERE id_usuario = ?");
                    $stmt->bind_param("sssssii", $nombre, $apellido, $cedula, $correo, $pass_hash, $id_rol, $id_usuario);
                } else {
                    // Si no ingresó contraseña, mantenemos la anterior
                    $stmt = $conexion->prepare("UPDATE usuario SET nombre = ?, apellido = ?, cedula = ?, correo = ?, id_rol = ? WHERE id_usuario = ?");
                    $stmt->bind_param("ssssii", $nombre, $apellido, $cedula, $correo, $id_rol, $id_usuario);
                }

                if ($stmt->execute()) {
                    header("Location: ../vistas/gestion_usuarios.php?ok=2");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                die("Error al actualizar usuario: " . $e->getMessage());
            }
        } else {
            header("Location: ../vistas/gestion_usuarios.php?error=datos_invalidos");
            exit();
        }
    }

    // 3. Eliminar / Baja de Usuario
    if ($accion === 'alternar_estado') {
        $id_usuario = (int)($_POST['id_usuario'] ?? 0);

        if ($id_usuario > 0) {
            $stmt = $conexion->prepare("DELETE FROM usuario WHERE id_usuario = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            header("Location: ../vistas/gestion_usuarios.php?ok=2");
            exit();
        }
    }
}

header("Location: ../vistas/gestion_usuarios.php");
exit();
?>