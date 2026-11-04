<?php
/**
 * =========================================================================
 * S.I.G.S.M. - ALTA DE NUEVO DOCUMENTO MÉDICO
 * =========================================================================
 * Este script permite a los administradores y funcionarios registrar 
 * nuevos documentos médicos o institucionales, asociándolos a un servicio,
 * asignándolos opcionalmente a un paciente por su cédula y subiendo el PDF.
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

// Generar el token CSRF único para este formulario
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener la lista de servicios activos desde la base de datos
$servicios_activos = [];
$res_servicios = $conexion->query("SELECT nombre FROM servicio WHERE activo = 1 ORDER BY nombre ASC");
if ($res_servicios && $res_servicios->num_rows > 0) {
    while ($serv = $res_servicios->fetch_assoc()) {
        $servicios_activos[] = $serv['nombre'];
    }
}

$errores = [];
$titulo = $tipo = $cedula_paciente = $fecha_emision = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Validación del token CSRF de seguridad
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Acción no autorizada (Error de validación de seguridad CSRF).");
    }

    $titulo = trim($_POST['titulo'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $cedula_paciente = trim($_POST['cedula_paciente'] ?? '');
    $fecha_emision = trim($_POST['fecha_emision'] ?? '');

    // Validaciones de campos obligatorios
    if (empty($titulo) || strlen($titulo) > 150) {
        $errores[] = "El título es obligatorio y debe tener como máximo 150 caracteres.";
    }

    // Validación dinámicamente contra los servicios activos de la base de datos
    if (empty($tipo) || (!empty($servicios_activos) && !in_array($tipo, $servicios_activos))) {
        $errores[] = "Seleccione una categoría o servicio válido.";
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

    // Validación y subida física del archivo PDF
    $ruta_archivo = "";
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['archivo']['tmp_name'];
        $fileName = $_FILES['archivo']['name'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        if ($fileExtension !== 'pdf') {
            $errores[] = "Solo se permiten archivos con extensión .pdf";
        } else {
            $newFileName = md5(time() . $fileName) . '.pdf';
            // Apuntamos a la carpeta de recursos de documentos en minúsculas
            $uploadFileDir = '../recursos/documentos/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $ruta_archivo = 'recursos/documentos/' . $newFileName;
            } else {
                $errores[] = "Hubo un error al mover el archivo subido al servidor.";
            }
        }
    } else {
        $errores[] = "Debe seleccionar un archivo PDF válido.";
    }

    // Inserción en la base de datos si no hay errores
    if (empty($errores)) {
        try {
            $cedula_db = !empty($cedula_paciente) ? $cedula_paciente : NULL;
            $id_funcionario = $_SESSION['usuario_id'];

            $stmt = $conexion->prepare("INSERT INTO documento (titulo, tipo, cedula_paciente, fecha_emision, ruta_archivo, id_usuario) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $titulo, $tipo, $cedula_db, $fecha_emision, $ruta_archivo, $id_funcionario);
            
            if ($stmt->execute()) {
                header("Location: ../vistas/documentos.php?ok=1");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $errores[] = "Error al guardar en la base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Nuevo Documento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../recursos/css/estilos.css">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white py-3 rounded-top-4">
            <h4 class="mb-0">Alta de Documento Médico</h4>
        </div>
        <div class="card-body p-4 p-md-5">
            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errores as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="alta_documento.php" enctype="multipart/form-data">
                
                <!-- TOKEN CSRF DE SEGURIDAD -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($titulo); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Servicio / Categoría</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Seleccione un servicio...</option>
                        <?php foreach ($servicios_activos as $s): ?>
                            <option value="<?php echo htmlspecialchars($s); ?>" <?php if ($tipo === $s) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($s); ?>
                            </option>
                        <?php endforeach; ?>
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

                <div class="mb-4">
                    <label class="form-label">Archivo PDF</label>
                    <input type="file" name="archivo" class="form-control" accept=".pdf" required>
                    <div class="form-text">Seleccione un archivo en formato PDF desde su equipo.</div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../vistas/documentos.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>