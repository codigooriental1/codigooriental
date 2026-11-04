<?php
/**
 * =========================================================================
 * S.I.G.S.M. - BAJA LÓGICA DE DOCUMENTO MÉDICO
 * =========================================================================
 * Este script procesa la desactivación lógica (baja) de un documento 
 * existente en el sistema, validando roles de administrador o funcionario,
 * comprobando el token CSRF de seguridad y actualizando el estado en la BD.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../conexion.php";

// Control de permisos: Solo Administradores (rol 1) y Funcionarios (rol 3)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol']) || !in_array((int)$_SESSION['id_rol'], [1, 3])) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Validación del token CSRF de seguridad
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Acción no autorizada (Error de validación de seguridad CSRF).");
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        $stmt = $conexion->prepare("UPDATE documento SET activo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: ../vistas/documentos.php?ok=3");
            exit();
        } else {
            header("Location: ../vistas/documentos.php?error=baja_fallida");
            exit();
        }
    }
}

header("Location: ../vistas/documentos.php");
exit();