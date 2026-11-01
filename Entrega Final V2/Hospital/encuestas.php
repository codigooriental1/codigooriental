<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

$mensaje_exito = "";
$mensaje_error = "";

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_encuesta = $_POST['id_encuesta'] ?? null;
    $respuestas = $_POST['respuestas'] ?? [];

    if ($id_encuesta) {
        // 1. Insertar el envío de la encuesta
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

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
                    <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
                <?php else: ?>
                    <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEmpleado">
                        Iniciar Sesion
                    </button>
                     <div class="modal fade" id="modalEmpleado" tabindex="-1" aria-labelledby="modalEmpleadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEmpleadoLabel">Acceso Exclusivo para Usuarios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    Esta sección es únicamente para usuarios ya registrados, si no te registraste, podés hacerlo con el botón de abajo.
                </div>
                <div class="modal-footer">
                    <a href="registro.php" class="btn btn-secondary">Registrarse</a>
                    <a href="login.php" class="btn btn-info text-white">Continuar al Login</a>
                </div>
            </div>
        </div>
    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <header class="bg-white py-4 border-bottom">
        <div class="container text-center">
            <img src="https://www.hc.edu.uy/imgnoticias/202609/1201.jpg" width="400" alt="Hospital de Clínicas" class="logo">
            <h1 class="display-5 mt-3">Encuestas</h1>
            <p class="lead">Encuesta de satisfacción del paciente.</p>
        </div>
    </header>

    <main class="container my-5">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <a href="index.php" class="btn btn-outline-primary btn-lg px-4 py-2">
        ← Volver al inicio
    </a>

    <?php if (isset($_SESSION['id_rol']) && in_array((int)$_SESSION['id_rol'], [1, 3])): ?>
        <a href="estadisticas_encuestas.php" class="btn btn-success btn-lg px-4 py-2">
            📊 Ver Panel de Estadísticas
        </a>
    <?php endif; ?>
</div>

        <section class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card tarjeta shadow-sm">
                    <div class="card-body p-5">

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
                        $sql = "SELECT id_encuesta, nombre, descripcion FROM encuesta WHERE estado = 'Abierta' LIMIT 1";
                        $resultado = $conexion->query($sql);

                        if ($resultado && $resultado->num_rows > 0) {
                            $encuesta = $resultado->fetch_assoc();
                        ?>

                        <h3 class="card-title mb-4"><?php echo htmlspecialchars($encuesta['nombre']); ?></h3>
                        <p class="text-muted mb-4"><?php echo htmlspecialchars($encuesta['descripcion']); ?></p>

                        <form method="POST" action="encuestas.php">
                            <input type="hidden" name="id_encuesta" value="<?php echo $encuesta['id_encuesta']; ?>">

                            <div class="mb-4">
                                <label class="form-label">Nombre Completo (opcional)</label>
                                <input type="text" name="nombre_opcional" class="form-control form-control-lg" placeholder="Ingrese su nombre">
                            </div>

                            <?php
                            $id_enc = $encuesta['id_encuesta'];
                            $sql_preguntas = "SELECT id_pregunta, texto, tipo FROM pregunta WHERE id_encuesta = ?";
                            $stmt_p = $conexion->prepare($sql_preguntas);
                            $stmt_p->bind_param("i", $id_enc);
                            $stmt_p->execute();
                            $res_preguntas = $stmt_p->get_result();

                            while($pregunta = $res_preguntas->fetch_assoc()) {
                                $id_preg = $pregunta['id_pregunta'];
                            ?>
                                <div class="mb-4">
                                    <label class="form-label"><?php echo htmlspecialchars($pregunta['texto']); ?></label>
                                    
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

                            <div class="d-grid">
                                <button class="btn btn-primary btn-lg" type="submit">Enviar encuesta</button>
                            </div>
                        </form>

                        <?php
                        } else {
                        ?>
                            <h3 class="card-title mb-4">Encuestas</h3>
                            <p class="text-muted">No hay encuestas activas disponibles en este momento.</p>
                        <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer class="text-center py-4 text-white" style="background-color: #0a2540;">
        <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
    </footer>

    <div class="modal fade" id="modalEmpleado" tabindex="-1" aria-labelledby="modalEmpleadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEmpleadoLabel">Acceso Exclusivo para Funcionales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    Esta sección es únicamente para uso del personal del Hospital de Clínicas. Si sos paciente, utilizá las opciones del menú principal.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="login.php" class="btn btn-info text-white">Continuar al Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/validaciones.js"></script>
</body>
</html>