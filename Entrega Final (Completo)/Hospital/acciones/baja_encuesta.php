<?php
/**
 * =========================================================================
 * S.I.G.S.M. - BAJA / ELIMINACIÓN DE ENCUESTA
 * =========================================================================
 * Este script procesa la baja o eliminación lógica de una encuesta,
 * verificando los permisos de usuario (Admin/Funcionario) y el token CSRF.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../conexion.php";

// Control de acceso: Solo Administradores (rol 1) y Funcionarios (rol 3)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol']) || !in_array((int)$_SESSION['id_rol'], [1, 3])) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Acción no autorizada (Error de validación de seguridad CSRF).");
    }

    $id_encuesta = (int)($_POST['id_encuesta'] ?? 0);

    if ($id_encuesta > 0) {
        try {
            // Cambiar estado a 'Cerrada' para baja lógica
            $stmt = $conexion->prepare("UPDATE encuesta SET estado = 'Cerrada' WHERE id_encuesta = ?");
            $stmt->bind_param("i", $id_encuesta);

            if ($stmt->execute()) {
                header("Location: ../vistas/encuestas.php?ok=3");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            header("Location: ../vistas/encuestas.php?error=db");
            exit();
        }
    }
}

header("Location: ../vistas/encuestas.php");
exit();
?>