<?php
require_once "conexion.php";

$errores = [];
$titulo = $tipo = $cedula_paciente = $fecha_emision = $ruta_archivo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $cedula_paciente = trim($_POST['cedula_paciente'] ?? '');
    $fecha_emision = trim($_POST['fecha_emision'] ?? '');
    $ruta_archivo = trim($_POST['ruta_archivo'] ?? '');

    if (empty($titulo) || strlen($titulo) > 150) {
        $errores[] = "El título es obligatorio y debe tener como máximo 150 caracteres.";
    }

    if (!in_array($tipo, ['indicacion', 'informacion'])) {
        $errores[] = "Seleccione un tipo de documento válido.";
    }

    if (!preg_match('/^[0-9]{7,8}$/', $cedula_paciente)) {
        $errores[] = "La cédula debe contener entre 7 y 8 dígitos, sin puntos ni guiones.";
    }

    if (!empty($fecha_emision)) {
        $partes = explode('-', $fecha_emision);
        if (count($partes) === 3) {
            if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
                $errores[] = "La fecha ingresada no existe.";
            }
        } else {
            $errores[] = "Formato de fecha inválido.";
        }
    } else {
        $errores[] = "La fecha de emisión es obligatoria.";
    }

    if (!preg_match('/^documentos\/.*\.pdf$/', $ruta_archivo)) {
        $errores[] = "La ruta debe comenzar con 'documentos/' y terminar con '.pdf'.";
    }

    if (empty($errores)) {
        try {
            $stmt = $conexion->prepare("INSERT INTO documento (titulo, tipo, cedula_paciente, fecha_emision, ruta_archivo) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $titulo, $tipo, $cedula_paciente, $fecha_emision, $ruta_archivo);
            
            if ($stmt->execute()) {
                header("Location: documentos.php?ok=1");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062 || str_contains($e->getMessage(), '23000')) {
                $errores[] = "La ruta del archivo especificada ya está registrada en otro documento.";
            } else {
                $errores[] = "Error al guardar en la base de datos: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>S.I.G.S.M. | Nuevo Documento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Alta de Documento Médico</h4>
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

            <form method="POST" action="alta_documento.php">
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($titulo); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de Documento</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="indicacion" <?php if ($tipo === 'indicacion') echo 'selected'; ?>>Indicación médica</option>
                        <option value="informacion" <?php if ($tipo === 'informacion') echo 'selected'; ?>>Información general</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cédula del Paciente (sin puntos ni guion)</label>
                    <input type="text" name="cedula_paciente" class="form-control" value="<?php echo htmlspecialchars($cedula_paciente); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha de Emisión</label>
                    <input type="date" name="fecha_emision" class="form-control" value="<?php echo htmlspecialchars($fecha_emision); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ruta del Archivo (Ej: documentos/archivo.pdf)</label>
                    <input type="text" name="ruta_archivo" class="form-control" value="<?php echo htmlspecialchars($ruta_archivo); ?>" placeholder="documentos/..." required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="documentos.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>