<?php
/**
 * =========================================================================
 * S.I.G.S.M. - EDICIÓN DE DOCUMENTO MÉDICO
 * =========================================================================
 * Este script permite modificar los datos y el archivo PDF de un documento
 * existente, validando permisos de rol, incorporando seguridad por token CSRF 
 * y manteniendo la consistencia de servicios dentro de la nueva estructura.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../conexion.php";

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol']) || !in_array((int)$_SESSION['id_rol'], [1, 3])) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

// Generar el token CSRF único para este formulario
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errores = [];
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    header("Location: ../vistas/documentos.php");
    exit();
}

$titulo = $tipo = $cedula_paciente = $fecha_emision = $ruta_archivo = "";

// Carga inicial de datos mediante GET
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
        header("Location: ../vistas/documentos.php");
        exit();
    }
} 
// Procesamiento de actualización mediante POST
elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Validación del token CSRF de seguridad
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Acción no autorizada (Error de validación de seguridad CSRF).");
    }

    $titulo = trim($_POST['titulo'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $cedula_paciente = trim($_POST['cedula_paciente'] ?? '');
    $fecha_emision = trim($_POST['fecha_emision'] ?? '');
    $ruta_archivo_actual = trim($_POST['ruta_archivo_actual'] ?? '');
    $ruta_archivo = $ruta_archivo_actual;

    if (empty($titulo) || strlen($titulo) > 150) {
        $errores[] = "El título es obligatorio y debe tener como máximo 150 caracteres.";
    }

    if (!in_array($tipo, ['Emergencia', 'CTI', 'Medica A', 'General'])) {
        $errores[] = "Seleccione un servicio o categoría válida.";
    }

    if (!empty($cedula_paciente)) {
        if (!preg_match('/^[0-9]{7,8}$/', $cedula_paciente)) {
            $errores[] = "La cédula debe contener entre 7 y 8 dígitos, sin puntos ni guiones.";
        }
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

    // Subida opcional de nuevo archivo PDF a la carpeta de recursos
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['archivo']['tmp_name'];
        $fileName = $_FILES['archivo']['name'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        if ($fileExtension !== 'pdf') {
            $errores[] = "Solo se permiten archivos con extensión .pdf";
        } else {
            $newFileName = md5(time() . $fileName) . '.pdf';
            $uploadFileDir = '../recursos/documentos/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $ruta_archivo = 'recursos/documentos/' . $newFileName;
            } else {
                $errores[] = "Hubo un error al mover el nuevo archivo subido al servidor.";
            }
        }
    }

    if (empty($errores)) {
        try {
            $cedula_db = !empty($cedula_paciente) ? $cedula_paciente : NULL;

            $stmt = $conexion->prepare("UPDATE documento SET titulo = ?, tipo = ?, cedula_paciente = ?, fecha_emision = ?, ruta_archivo = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $titulo, $tipo, $cedula_db, $fecha_emision, $ruta_archivo, $id);

            if ($stmt->execute()) {
                header("Location: ../vistas/documentos.php?ok=2");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $errores[] = "Error al actualizar en la base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Editar Documento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../recursos/css/estilos.css">
</head>
<body class="bg-light">

    <header class="py-3 bg-white border-bottom shadow-sm mb-4">
        <div class="container text-center">
            <h2 class="h4 text-primary mb-0">Hospital de Clínicas - S.I.G.S.M.</h2>
        </div>
    </header>

    <main class="container my-5" style="max-width: 600px;">
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

                <form method="POST" action="editar_documento.php" enctype="multipart/form-data">
                    
                    <!-- TOKEN CSRF DE SEGURIDAD -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                    <input type="hidden" name="ruta_archivo_actual" value="<?php echo htmlspecialchars($ruta_archivo); ?>">

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($titulo); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Servicio / Categoría</label>
                        <select name="tipo" class="form-select" required>
                            <option value="Emergencia" <?php if ($tipo === 'Emergencia') echo 'selected'; ?>>Emergencia</option>
                            <option value="CTI" <?php if ($tipo === 'CTI') echo 'selected'; ?>>CTI</option>
                            <option value="Medica A" <?php if ($tipo === 'Medica A') echo 'selected'; ?>>Médica A</option>
                            <option value="General" <?php if ($tipo === 'General') echo 'selected'; ?>>General</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cédula del Paciente (Opcional - sin puntos ni guion)</label>
                        <input type="text" name="cedula_paciente" class="form-control" value="<?php echo htmlspecialchars($cedula_paciente); ?>" placeholder="Dejar en blanco si es de acceso general">
                        <div class="form-text">Si ingresa una cédula, el documento solo será accesible para ese paciente.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha de Emisión</label>
                        <input type="date" name="fecha_emision" class="form-control" value="<?php echo htmlspecialchars($fecha_emision); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Archivo PDF (Opcional)</label>
                        <input type="file" name="archivo" class="form-control" accept=".pdf">
                        <div class="form-text">Archivo actual: <code><?php echo htmlspecialchars($ruta_archivo); ?></code> (Deje en blanco si no desea cambiarlo).</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="../vistas/documentos.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning text-dark fw-bold">Actualizar Documento</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-white mt-5" style="background-color: #0a2540;">
        <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>