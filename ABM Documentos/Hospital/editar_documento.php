<?php
require_once "conexion.php";

$errores = [];
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    header("Location: documentos.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $conexion->prepare("SELECT * FROM documento WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $doc = $res->fetch_assoc();
        $titulo = $doc['titulo'];
        $tipo = $doc['tipo'];
        $cedula_paciente = $doc['cedula_paciente'];
        $fecha_emision = $doc['fecha_emision'];
        $ruta_archivo = $doc['ruta_archivo'];
    } else {
        header("Location: documentos.php");
        exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $cedula_paciente = trim($_POST['cedula_paciente'] ?? '');
    $fecha_emision = trim($_POST['fecha_emision'] ?? '');
    $ruta_archivo = trim($_POST['ruta_archivo'] ?? '');

    if (empty($titulo) || strlen($titulo) > 150) {
        $errores[] = "El título es obligatorio y de máximo 150 caracteres.";
    }

    if (!in_array($tipo, ['indicacion', 'informacion'])) {
        $errores[] = "Tipo inválido.";
    }

    if (!preg_match('/^[0-9]{7,8}$/', $cedula_paciente)) {
        $errores[] = "Cédula inválida (debe tener 7 u 8 números).";
    }

    if (!empty($fecha_emision)) {
        $partes = explode('-', $fecha_emision);
        if (count($partes) !== 3 || !checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
            $errores[] = "Fecha inválida o inexistente.";
        }
    } else {
        $errores[] = "Fecha obligatoria.";
    }

    if (!preg_match('/^documentos\/.*\.pdf$/', $ruta_archivo)) {
        $errores[] = "La ruta debe empezar con 'documentos/' y terminar con '.pdf'.";
    }

    if (empty($errores)) {
        try {
            $stmt = $conexion->prepare("UPDATE documento SET titulo = ?, tipo = ?, cedula_paciente = ?, fecha_emision = ?, ruta_archivo = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $titulo, $tipo, $cedula_paciente, $fecha_emision, $ruta_archivo, $id);

            if ($stmt->execute()) {
                header("Location: documentos.php?ok=2");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062 || str_contains($e->getMessage(), '23000')) {
                $errores[] = "La ruta del archivo especificada ya existe en otro documento.";
            } else {
                $errores[] = "Error al actualizar: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>S.I.G.S.M. | Editar Documento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Editar Documento Médico</h4>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errores as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="editar_documento.php">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($titulo); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="indicacion" <?php if ($tipo === 'indicacion') echo 'selected'; ?>>Indicación médica</option>
                        <option value="informacion" <?php if ($tipo === 'informacion') echo 'selected'; ?>>Información general</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cédula del Paciente</label>
                    <input type="text" name="cedula_paciente" class="form-control" value="<?php echo htmlspecialchars($cedula_paciente); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha de Emisión</label>
                    <input type="date" name="fecha_emision" class="form-control" value="<?php echo htmlspecialchars($fecha_emision); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ruta del Archivo</label>
                    <input type="text" name="ruta_archivo" class="form-control" value="<?php echo htmlspecialchars($ruta_archivo); ?>" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="documentos.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-warning">Actualizar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>