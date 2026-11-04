<?php
/**
 * =========================================================================
 * S.I.G.S.M. - CREACIÓN DE NUEVA ENCUESTA
 * =========================================================================
 * Este script permite a los administradores crear nuevas encuestas de satisfacción,
 * asignándoles un título, descripción, servicio y un conjunto personalizado de preguntas.
 */

// 1. CONTROL DE SESIÓN Y PERMISOS
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../conexion.php";

// Solo Administradores (rol 1) o Funcionarios (rol 3) pueden crear encuestas
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol']) || !in_array((int)$_SESSION['id_rol'], [1, 3])) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

// Generar el token CSRF único para este formulario
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener servicios activos desde la base de datos para cargar en el formulario
$servicios_activos = [];
$res_servicios = $conexion->query("SELECT nombre FROM servicio WHERE activo = 1 ORDER BY nombre ASC");
if ($res_servicios && $res_servicios->num_rows > 0) {
    while ($serv = $res_servicios->fetch_assoc()) {
        $servicios_activos[] = $serv['nombre'];
    }
}

$errores = [];
$nombre = $descripcion = $servicio = "";
$preguntas_input = [];

// 2. PROCESAMIENTO DEL FORMULARIO (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Validación del token CSRF de seguridad
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Acción no autorizada (Error de validación de seguridad CSRF).");
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $servicio = trim($_POST['servicio'] ?? '');
    $preguntas_input = $_POST['preguntas'] ?? [];
    $tipos_input = $_POST['tipos'] ?? [];

    // --- VALIDACIONES ---
    if (empty($nombre) || strlen($nombre) > 150) {
        $errores[] = "El título de la encuesta es obligatorio y debe tener máximo 150 caracteres.";
    }

    if (empty($servicio) || (!empty($servicios_activos) && !in_array($servicio, $servicios_activos))) {
        $errores[] = "Debe seleccionar un servicio hospitalario válido.";
    }

    if (empty($preguntas_input)) {
        $errores[] = "Debe incluir al menos una pregunta en la encuesta.";
    } else {
        foreach ($preguntas_input as $p) {
            if (empty(trim($p))) {
                $errores[] = "El texto de las preguntas no puede estar vacío.";
                break;
            }
        }
    }

    if (empty($errores)) {
        try {
            // Iniciamos transacción para asegurar integridad entre encuesta y preguntas
            $conexion->begin_transaction();

            // Insertar la encuesta principal con su servicio
            $stmt = $conexion->prepare("INSERT INTO encuesta (nombre, descripcion, servicio, estado) VALUES (?, ?, ?, 'Abierta')");
            $stmt->bind_param("sss", $nombre, $descripcion, $servicio);
            $stmt->execute();
            $id_encuesta = $conexion->insert_id;

            // Insertar cada pregunta enviada desde el formulario dinámico
            $stmt_pregunta = $conexion->prepare("INSERT INTO pregunta (id_encuesta, texto, tipo) VALUES (?, ?, ?)");
            
            foreach ($preguntas_input as $index => $texto_pregunta) {
                $texto_limpio = trim($texto_pregunta);
                $tipo_limpio = $tipos_input[$index] ?? 'Escala';
                
                if (!in_array($tipo_limpio, ['Escala', 'Texto'])) {
                    $tipo_limpio = 'Escala';
                }

                if ($texto_limpio !== "") {
                    $stmt_pregunta->bind_param("iss", $id_encuesta, $texto_limpio, $tipo_limpio);
                    $stmt_pregunta->execute();
                }
            }

            // Confirmar transacción
            $conexion->commit();

            // Redireccionar al listado de encuestas en vistas/
            header("Location: ../vistas/encuestas.php?ok=1");
            exit();

        } catch (mysqli_sql_exception $e) {
            $conexion->rollback();
            $errores[] = "Error al crear la encuesta en la base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Crear Encuesta</title>
    <!-- Framework Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../recursos/css/estilos.css">
</head>
<body class="bg-light">

    <!-- ========================================================================= -->
    <!-- CABECERA / HEADER INSTITUCIONAL -->
    <!-- ========================================================================= -->
    <header class="bg-white py-4 border-bottom shadow-sm">
        <div class="container text-center">
            <h1 class="h3 text-primary mb-1">Hospital de Clínicas - S.I.G.S.M.</h1>
            <p class="text-muted mb-0">Panel de Administración - Creación de Encuestas</p>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- CONTENEDOR PRINCIPAL Y FORMULARIO -->
    <!-- ========================================================================= -->
    <main class="container my-5" style="max-width: 800px;">
        
        <div class="mb-4">
            <a href="../vistas/encuestas.php" class="btn btn-outline-secondary">
                ← Volver al listado de encuestas
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0">Registrar Nueva Encuesta de Satisfacción</h4>
            </div>
            <div class="card-body p-4">

                <!-- Alertas de Error -->
                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errores as $err): ?>
                                <li><?php echo htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Formulario de Alta -->
                <form method="POST" action="crear_encuesta.php" id="formEncuesta">
                    
                    <!-- TOKEN CSRF DE SEGURIDAD -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <!-- Campo: Título de la Encuesta -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Título de la Encuesta</label>
                        <input type="text" 
                               name="nombre" 
                               class="form-control form-control-lg" 
                               placeholder="Ej: Satisfacción en Servicio de Urgencias" 
                               value="<?php echo htmlspecialchars($nombre); ?>" 
                               required>
                    </div>

                    <!-- Campo: Servicio (Clave para los filtros) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Servicio Hospitalario asociado</label>
                        <select name="servicio" class="form-select form-select-lg" required>
                            <option value="">Seleccione un servicio...</option>
                            <?php foreach ($servicios_activos as $s): ?>
                                <option value="<?php echo htmlspecialchars($s); ?>" <?php if ($servicio === $s) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($s); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Campo: Descripción -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción / Objetivo</label>
                        <textarea name="descripcion" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Breve detalle de la finalidad de la encuesta..."><?php echo htmlspecialchars($descripcion); ?></textarea>
                    </div>

                    <hr class="my-4">

                    <!-- SECCIÓN DINÁMICA DE PREGUNTAS -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-secondary">Preguntas de la Encuesta</h5>
                        <button type="button" id="btnAddPregunta" class="btn btn-outline-success btn-sm fw-bold">+ Agregar Pregunta</button>
                    </div>

                    <div id="contenedorPreguntas">
                        <!-- Pregunta por defecto 1 -->
                        <div class="card p-3 mb-3 bg-white border item-pregunta shadow-sm">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Texto de la Pregunta</label>
                                    <input type="text" name="preguntas[]" class="form-control" value="¿Cómo calificaría la atención recibida?" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Tipo de Respuesta</label>
                                    <select name="tipos[]" class="form-select">
                                        <option value="Escala" selected>Escala (1 al 5)</option>
                                        <option value="Texto">Texto libre</option>
                                    </select>
                                </div>
                                <div class="col-md-1 text-end pt-3">
                                    <button type="button" class="btn btn-outline-danger btn-sm btnEliminar" title="Eliminar pregunta">✕</button>
                                </div>
                            </div>
                        </div>

                        <!-- Pregunta por defecto 2 -->
                        <div class="card p-3 mb-3 bg-white border item-pregunta shadow-sm">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Texto de la Pregunta</label>
                                    <input type="text" name="preguntas[]" class="form-control" value="¿Desea agregar algún comentario o sugerencia?" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Tipo de Respuesta</label>
                                    <select name="tipos[]" class="form-select">
                                        <option value="Escala">Escala (1 al 5)</option>
                                        <option value="Texto" selected>Texto libre</option>
                                    </select>
                                </div>
                                <div class="col-md-1 text-end pt-3">
                                    <button type="button" class="btn btn-outline-danger btn-sm btnEliminar" title="Eliminar pregunta">✕</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="../vistas/encuestas.php" class="btn btn-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Guardar Encuesta</button>
                    </div>

                </form>

            </div>
        </div>
    </main>

    <!-- Script JavaScript para agregar y eliminar preguntas de forma dinámica -->
    <script>
        document.getElementById('btnAddPregunta').addEventListener('click', function() {
            const contenedor = document.getElementById('contenedorPreguntas');
            const nuevoItem = document.createElement('div');
            nuevoItem.className = 'card p-3 mb-3 bg-white border item-pregunta shadow-sm';
            nuevoItem.innerHTML = `
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Texto de la Pregunta</label>
                        <input type="text" name="preguntas[]" class="form-control" placeholder="Escriba la nueva pregunta..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tipo de Respuesta</label>
                        <select name="tipos[]" class="form-select">
                            <option value="Escala" selected>Escala (1 al 5)</option>
                            <option value="Texto">Texto libre</option>
                        </select>
                    </div>
                    <div class="col-md-1 text-end pt-3">
                        <button type="button" class="btn btn-outline-danger btn-sm btnEliminar" title="Eliminar pregunta">✕</button>
                    </div>
                </div>
            `;
            contenedor.appendChild(nuevoItem);
        });

        document.getElementById('contenedorPreguntas').addEventListener('click', function(e) {
            if (e.target.classList.contains('btnEliminar')) {
                const items = document.querySelectorAll('.item-pregunta');
                if (items.length > 1) {
                    e.target.closest('.item-pregunta').remove();
                } else {
                    alert('La encuesta debe tener al menos una pregunta.');
                }
            }
        });
    </script>

    <!-- ========================================================================= -->
    <!-- PIE DE PÁGINA / FOOTER -->
    <!-- ========================================================================= -->
    <footer class="text-center py-4 text-white mt-5" style="background-color: #0a2540;">
        <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>