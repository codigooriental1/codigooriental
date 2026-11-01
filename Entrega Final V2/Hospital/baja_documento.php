<?php
session_start(); 
require_once "conexion.php";


if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol']) || !in_array((int)$_SESSION['id_rol'], [1, 3])) {
    header("Location: login.php?error=acceso_denegado");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $conexion->prepare("UPDATE documento SET activo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: documentos.php?ok=3");
            exit();
        }
    }
}

header("Location: documentos.php");
exit();