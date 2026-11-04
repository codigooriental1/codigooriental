<?php
/**
 * =========================================================================
 * S.I.G.S.M. - PORTAL DE LISTADO Y RESPUESTA DE ENCUESTAS
 * =========================================================================
 * Este script permite listar las encuestas activas, filtrarlas por título
 * y servicio, responder una encuesta o darla de baja si es Admin/Funcionario.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../conexion.php';

// Generar token CSRF para las acciones que lo requieran
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje_exito = "";
$mensaje_error = "";

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 1) $mensaje_exito = "Encuesta creada correctamente.";
    elseif ($_GET['ok'] == 3) $mensaje_exito = "Encuesta dada de baja correctamente.";
}

// Procesar el envío de respuestas de una encuesta (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'responder') {
    $id_encuesta = $_POST['id_encuesta'] ?? null;
    $respuestas = $_POST['respuestas'] ?? [];

    if ($id_encuesta) {
        // 1. Insertar el envío general de la encuesta
        $stmt_envio = $conexion->prepare("INSERT INTO envio_encuesta (id_encuesta) VALUES (?)");
        if ($stmt_envio) {
            $stmt_envio->bind_param("i", $id_encuesta);
            if ($stmt_envio->execute()) {
                $id_envio = $conexion->insert_id;
                $stmt_envio->close();

                // 2. Insertar cada una de las respuestas asociadas
                $exito_respuestas = true;
                foreach ($respuestas as $id_pregunta => $valor) {
                    $valor_limpio = trim($valor);
                    if ($valor_limpio !== "") {
                        $stmt_resp = $conexion->prepare("INSERT INTO respuesta (valor, id_pregunta, id_envio) VALUES (?, ?, ?)");
                        if ($stmt_resp) {
                            $stmt_resp->bind_param("sii", $valor_limpio, $id_pregunta, $id_envio);
                            if (!$stmt_resp->execute()) {
                                $exito_respuestas = false;
                            }
                            $stmt_resp->close();
                        }
                    }
                }

                if ($exito_respuestas) {
                    $mensaje_exito = "¡Muchas gracias! Su encuesta ha sido enviada correctamente.";
                } else {
                    $mensaje_error = "Hubo un error al guardar algunas respuestas.";
                }
            } else {
                $mensaje_error = "Error al procesar el envío de la encuesta.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Encuestas</title>
    <!-- Framework Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../recursos/css/estilos.css">

    <style>
        .tarjeta, 
        .list-group-item, 
        .list-group-item-action {
            background-color: #ffffff !important;
            color: #212529 !important;
            transform: none !important;
            transition: none !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .tarjeta:hover, 
        .list-group-item:hover, 
        .list-group-item-action:hover {
            background-color: #ffffff !important;
            color: #212529 !important;
            transform: none !important;
            transition: none !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- BARRA SUPERIOR DE CONTACTO Y SESIÓN -->
    <div class="py-2 border-bottom" style="background-color: #0d2d4e;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">

            <small class="text-white d-flex align-items-center gap-3">
                <span class="d-flex align-items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.6 10.8a15.4 15.4 0 0 0 6.6 6.6l2.2-2.2a1.2 1.2 0 0 1 1.2-.3c1.3.4 2.7.6 4.1.6a1.2 1.2 0 0 1 1.2 1.2V20a1.2 1.2 0 0 1-1.2 1.2C10.6 21.2 2.8 13.4 2.8 3.5A1.2 1.2 0 0 1 4 2.3h3.3A1.2 1.2 0 0 1 8.5 3.5c0 1.4.2 2.8.6 4.1a1.2 1.2 0 0 1-.3 1.2L6.6 10.8z"/></svg>
                    <strong>1953 / 0800 1953</strong>
                </span>

                <span class="opacity-50">|</span>

                <a href="mailto:atencionalusuario@hc.edu.uy" class="text-white text-decoration-none d-flex align-items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 5h18v14H3z"/><path d="M3 5l9 7 9-7"/></svg>
                    <span>Contacto</span>
                </a>
            </small>

            <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center gap-2">
                    <a href="https://www.instagram.com/hclinicasuy" target="_blank" class="text-white text-decoration-none" aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="https://www.youtube.com/user/CETECIHCURUGUAY" target="_blank" class="text-white text-decoration-none" aria-label="YouTube">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M22.5 6.2a2.8 2.8 0 0 0-1.98-2C18.7 3.7 12 3.7 12 3.7s-6.7 0-8.52.5a2.8 2.8 0 0 0-1.98 2A29.5 29.5 0 0 0 1 12a29.5 29.5 0 0 0 .5 5.8 2.8 2.8 0 0 0 1.98 2c1.82.5 8.52.5 8.52.5s6.7 0 8.52-.5a2.8 2.8 0 0 0 1.98-2A29.5 29.5 0 0 0 23 12a29.5 29.5 0 0 0-.5-5.8zM9.8 15.5V8.5l6.1 3.5-6.1 3.5z"/></svg>
                    </a>
                    <a href="https://x.com/hcmquintela" target="_blank" class="text-white text-decoration-none" aria-label="X">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-6.8 7.8L23.3 22H16.6l-5.2-6.8L5.3 22H2.2l7.3-8.4L1.7 2h6.8l4.7 6.2L18.9 2zm-2.4 18h1.9L7.6 4H5.6l10.9 16z"/></svg>
                    </a>
                </div>

                <span class="text-white opacity-50">|</span>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span class="text-white fw-bold small">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                    <a href="../logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
                <?php else: ?>
                    <button type="button" class="btn btn-info btn-sm text-white fw-bold" data-bs-toggle="modal" data-bs-target="#modalEmpleado">
                        Iniciar Sesion
                    </button>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- CABECERA PRINCIPAL -->
    <header class="bg-white py-4 border-bottom">
        <div class="container text-center">
            <img src="https://www.hc.edu.uy/imgnoticias/202609/1201.jpg" width="400" alt="Hospital de Clínicas" class="logo img-fluid" style="max-width: 380px;">
            <h1 class="display-6 mt-3 fw-bold">Encuestas de Satisfacción</h1>
            <p class="lead text-muted fs-5">Seleccione una encuesta disponible para dejar su opinión.</p>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="container my-5 flex-grow-1">

        <!-- Barra de Navegación Superior -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <a href="../index.php" class="btn btn-outline-primary btn-lg px-4 py-2">
                ← Volver al inicio
            </a>

            <?php if (isset($_SESSION['id_rol']) && in_array((int)$_SESSION['id_rol'], [1, 3])): ?>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="../acciones/crear_encuesta.php" class="btn btn-primary btn-lg px-4 py-2">
                        + Nueva Encuesta
                    </a>
                    <a href="estadisticas_encuestas.php" class="btn btn-success btn-lg px-4 py-2">
                        📊 Ver Panel de Estadísticas
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <section class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card tarjeta shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <!-- Mensajes de éxito / error -->
                        <?php if (!empty($mensaje_exito)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?php echo htmlspecialchars($mensaje_exito); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($mensaje_error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php echo htmlspecialchars($mensaje_error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php 
                        $id_responder = isset($_GET['id']) ? (int)$_GET['id'] : 0;

                        if ($id_responder > 0): 
                            // FORMULARIO DE RESPUESTA
                            $stmt_enc = $conexion->prepare("SELECT * FROM encuesta WHERE id_encuesta = ? AND estado = 'Abierta'");
                            $stmt_enc->bind_param("i", $id_responder);
                            $stmt_enc->execute();
                            $res_enc = $stmt_enc->get_result();

                            if ($res_enc && $res_enc->num_rows > 0):
                                $encuesta = $res_enc->fetch_assoc();
                        ?>
                                <div class="mb-3">
                                    <a href="encuestas.php" class="btn btn-outline-secondary btn-sm">← Volver al listado de encuestas</a>
                                </div>

                                <h3 class="card-title mb-3 fw-bold"><?php echo htmlspecialchars($encuesta['nombre']); ?></h3>
                                <span class="badge bg-secondary mb-2">Servicio: <?php echo htmlspecialchars($encuesta['servicio'] ?? 'General'); ?></span>
                                <p class="text-muted mb-4"><?php echo htmlspecialchars($encuesta['descripcion']); ?></p>

                                <form method="POST" action="encuestas.php?id=<?php echo $id_responder; ?>">
                                    <input type="hidden" name="accion" value="responder">
                                    <input type="hidden" name="id_encuesta" value="<?php echo $encuesta['id_encuesta']; ?>">

                                    <div class="mb-4">
                                        <label class="form-label">Nombre Completo (opcional)</label>
                                        <input type="text" name="nombre_opcional" class="form-control form-control-lg" placeholder="Ingrese su nombre">
                                    </div>

                                    <?php
                                    $sql_preguntas = "SELECT id_pregunta, texto, tipo FROM pregunta WHERE id_encuesta = ?";
                                    $stmt_p = $conexion->prepare($sql_preguntas);
                                    $stmt_p->bind_param("i", $id_responder);
                                    $stmt_p->execute();
                                    $res_preguntas = $stmt_p->get_result();

                                    while($pregunta = $res_preguntas->fetch_assoc()) {
                                        $id_preg = $pregunta['id_pregunta'];
                                    ?>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold"><?php echo htmlspecialchars($pregunta['texto']); ?></label>
                                            
                                            <?php if($pregunta['tipo'] === 'Escala' || $pregunta['tipo'] === 'Opcion'): ?>
                                                <select name="respuestas[<?php echo $id_preg; ?>]" class="form-select form-select-lg" required>
                                                    <option value="" selected disabled>Seleccione una opción</option>
                                                    <option value="Excelente / 5">Excelente / 5</option>
                                                    <option value="Muy buena / 4">Muy buena / 4</option>
                                                    <option value="Buena / 3">Buena / 3</option>
                                                    <option value="Regular / 2">Regular / 2</option>
                                                    <option value="Mala / 1">Mala / 1</option>
                                                </select>
                                            <?php else: ?>
                                                <textarea name="respuestas[<?php echo $id_preg; ?>]" class="form-control" rows="4" placeholder="Escriba aquí su respuesta..."></textarea>
                                            <?php endif; ?>
                                        </div>
                                    <?php 
                                    }
                                    $stmt_p->close();
                                    ?>

                                    <div class="d-grid mt-4">
                                        <button class="btn btn-primary btn-lg" type="submit">Enviar encuesta</button>
                                    </div>
                                </form>
                        <?php 
                            else:
                                echo '<div class="alert alert-warning">La encuesta solicitada no existe o ya no se encuentra abierta.</div>';
                                echo '<a href="encuestas.php" class="btn btn-primary mt-2">Ver listado de encuestas</a>';
                            endif;

                        else: 
                            // LISTADO GENERAL CON FILTROS
                        ?>

                            <h3 class="card-title mb-4 fw-bold">Lista de Encuestas Disponibles</h3>

                            <!-- BARRA DE BÚSQUEDA Y FILTROS -->
                            <form method="GET" action="encuestas.php" class="d-flex flex-column flex-md-row gap-3 mb-4">
                                <div class="input-group input-group-lg flex-grow-1">
                                    <input type="text" 
                                           name="buscar" 
                                           class="form-control" 
                                           placeholder="Buscar encuesta por título..." 
                                           value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                                    <button type="submit" class="btn btn-outline-primary">Buscar</button>
                                    <?php if (!empty($_GET['buscar']) || !empty($_GET['servicio'])): ?>
                                        <a href="encuestas.php" class="btn btn-outline-secondary">Limpiar</a>
                                    <?php endif; ?>
                                </div>

                                <div class="dropdown">
                                    <button type="button" class="btn btn-outline-secondary btn-lg dropdown-toggle" data-bs-toggle="dropdown">
                                        <?php 
                                            if (isset($_GET['servicio']) && !empty($_GET['servicio'])) {
                                                echo "Servicio: " . htmlspecialchars($_GET['servicio']);
                                            } else {
                                                echo "Filtrar por servicio";
                                            }
                                        ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="encuestas.php?servicio=Emergencia<?php echo isset($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : ''; ?>">Emergencia</a></li>
                                        <li><a class="dropdown-item" href="encuestas.php?servicio=CTI<?php echo isset($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : ''; ?>">CTI</a></li>
                                        <li><a class="dropdown-item" href="encuestas.php?servicio=Medica A<?php echo isset($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : ''; ?>">Médica A</a></li>
                                        <li><a class="dropdown-item" href="encuestas.php?servicio=General<?php echo isset($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : ''; ?>">General</a></li>
                                        <?php if (!empty($_GET['servicio']) || !empty($_GET['buscar'])): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="encuestas.php">Quitar filtros</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </form>

                            <!-- LISTADO DE TARJETAS DE ENCUESTAS -->
                            <div class="list-group">
                                <?php
                                $busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
                                $servicio_filtro = isset($_GET['servicio']) ? trim($_GET['servicio']) : '';

                                $sql_list = "SELECT * FROM encuesta WHERE estado = 'Abierta'";
                                $params = [];
                                $types = "";

                                if (!empty($busqueda)) {
                                    $sql_list .= " AND nombre LIKE ?";
                                    $params[] = "%" . $busqueda . "%";
                                    $types .= "s";
                                }

                                if (!empty($servicio_filtro)) {
                                    $sql_list .= " AND servicio = ?";
                                    $params[] = $servicio_filtro;
                                    $types .= "s";
                                }

                                $sql_list .= " ORDER BY fecha_creacion DESC";

                                if (!empty($params)) {
                                    $stmt_l = $conexion->prepare($sql_list);
                                    $stmt_l->bind_param($types, ...$params);
                                    $stmt_l->execute();
                                    $res_list = $stmt_l->get_result();
                                } else {
                                    $res_list = $conexion->query($sql_list);
                                }

                                if ($res_list && $res_list->num_rows > 0) {
                                    while ($row_enc = $res_list->fetch_assoc()) {
                                ?>
                                        <div class="list-group-item list-group-item-action d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center py-3 mb-2 border rounded shadow-sm gap-3">
                                            <div>
                                                <h5 class="mb-1 text-primary fw-bold"><?php echo htmlspecialchars($row_enc['nombre']); ?></h5>
                                                <p class="mb-1 text-muted small"><?php echo htmlspecialchars($row_enc['descripcion']); ?></p>
                                                <span class="badge bg-secondary">Servicio: <?php echo htmlspecialchars($row_enc['servicio'] ?? 'General'); ?></span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 w-100 w-md-auto justify-content-end">
                                                <a href="encuestas.php?id=<?php echo $row_enc['id_encuesta']; ?>" class="btn btn-primary px-3">
                                                    Responder
                                                </a>

                                                <?php if (isset($_SESSION['id_rol']) && in_array((int)$_SESSION['id_rol'], [1, 3])): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm px-3" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalBajaEncuesta" 
                                                            data-id="<?php echo $row_enc['id_encuesta']; ?>" 
                                                            data-nombre="<?php echo htmlspecialchars($row_enc['nombre']); ?>">
                                                        Baja
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo '<div class="text-center py-4 text-muted">No se encontraron encuestas activas con los filtros seleccionados.</div>';
                                }
                                ?>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer anclado abajo -->
    <footer class="text-center py-4 text-white mt-auto" style="background-color: #0a2540;">
        <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
    </footer>

    <!-- MODAL DE EMPLEADO -->
    <div class="modal fade" id="modalEmpleado" tabindex="-1" aria-labelledby="modalEmpleadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEmpleadoLabel">Acceso Exclusivo para Usuarios</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted mb-0">Esta sección es únicamente para usuarios ya registrados. Si no te registraste, podés hacerlo con el botón de abajo.</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <a href="../registro.php" class="btn btn-secondary">Registrarse</a>
                    <a href="../login.php" class="btn btn-info text-white fw-bold">Continuar al Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE BAJA DE ENCUESTA -->
    <div class="modal fade" id="modalBajaEncuesta" tabindex="-1" aria-labelledby="modalBajaEncuestaLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="modalBajaEncuestaLabel">Confirmar Baja de Encuesta</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <p>¿Está seguro de que desea dar de baja la siguiente encuesta?</p>
            <p class="fw-bold text-secondary" id="nombreEncuestaBaja"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <form action="../acciones/baja_encuesta.php" method="POST" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id_encuesta" id="idEncuestaBaja" value="">
                <button type="submit" class="btn btn-danger">Confirmar Baja</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalBajaEncuesta = document.getElementById('modalBajaEncuesta');
        if (modalBajaEncuesta) {
            modalBajaEncuesta.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('idEncuestaBaja').value = button.getAttribute('data-id');
                document.getElementById('nombreEncuestaBaja').textContent = button.getAttribute('data-nombre');
            });
        }
    </script>
</body>
</html>