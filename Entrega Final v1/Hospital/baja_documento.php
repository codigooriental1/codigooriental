<?php
require_once "conexion.php";

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