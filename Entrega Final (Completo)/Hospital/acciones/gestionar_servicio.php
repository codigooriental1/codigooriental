<?php
/**
 * =========================================================================
 * S.I.G.S.M. - PROCESADOR DE GESTIÓN DE SERVICIOS
 * =========================================================================
 * Procesa el alta de nuevos servicios hospitalarios y su activación/desactivación.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../conexion.php";

// Exclusivo Administrador
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

    // 1. Alta de Servicio
    if ($accion === 'crear_servicio') {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (!empty($nombre)) {
            try {
                $stmt = $conexion->prepare("INSERT INTO servicio (nombre, descripcion, activo) VALUES (?, ?, 1)");
                $stmt->bind_param("ss", $nombre, $descripcion);
                if ($stmt->execute()) {
                    header("Location: ../vistas/gestion_usuarios.php?ok=3");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                header("Location: ../vistas/gestion_usuarios.php?error=1");
                exit();
            }
        }
    }

    // 2. Alternar Estado Servicio (Activar / Desactivar)
    $id_servicio = (int)($_POST['id_servicio'] ?? 0);
    $nuevo_estado = (int)($_POST['nuevo_estado'] ?? 1);

    if ($id_servicio > 0) {
        $stmt = $conexion->prepare("UPDATE servicio SET activo = ? WHERE id_servicio = ?");
        $stmt->bind_param("ii", $nuevo_estado, $id_servicio);
        $stmt->execute();
        header("Location: ../vistas/gestion_usuarios.php?ok=4");
        exit();
    }
}

header("Location: ../vistas/gestion_usuarios.php");
exit();
?>