<?php
/**
 * =========================================================================
 * S.I.G.S.M. - PANEL DE ESTADÍSTICAS DE ENCUESTAS
 * =========================================================================
 * Este script muestra las estadísticas agrupadas por cada encuesta creada,
 * incorporando desplegables (acordeones), porcentajes visuales y la opción
 * con mayor cantidad de elecciones.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../conexion.php";

// Protección de ruta: Solo Administrador (1) o Funcionario (3)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol']) || !in_array((int)$_SESSION['id_rol'], [1, 3])) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

// 1. Obtener total general de envíos de encuestas
$res_total = $conexion->query("SELECT COUNT(*) AS total FROM envio_encuesta");
$total_envios = $res_total ? $res_total->fetch_assoc()['total'] : 0;

// 2. Obtener todas las encuestas registradas (Abiertas y Cerradas)
$sql_encuestas = "SELECT * FROM encuesta ORDER BY id_encuesta DESC";
$res_encuestas = $conexion->query($sql_encuestas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Estadísticas de Encuestas</title>
    <!-- Framework Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../recursos/css/estilos.css">
</head>
<body class="bg-light">

    <!-- Barra superior de contacto y sesión -->
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
                <span class="text-white fw-bold me-2 small">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </div>

<main class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <a href="encuestas.php" class="btn btn-outline-primary btn-lg px-4 py-2">← Volver a Encuestas</a>
        <h2 class="mb-0 fw-bold text-primary">📊 Estadísticas por Encuesta</h2>
    </div>

    <!-- Tarjeta Resumen General -->
    <div class="row mb-5">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white p-4" style="background: linear-gradient(135deg, #0d6efd, #0d2d4e);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase tracking-wider opacity-75 mb-1">Total de Envíos Global</h6>
                        <h2 class="display-5 fw-bold text-white mb-0"><?php echo $total_envios; ?></h2>
                    </div>
                    <div class="fs-1 opacity-50">📋</div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($res_encuestas && $res_encuestas->num_rows > 0): ?>
        <div class="accordion" id="accordionEncuestas">
            <?php 
            $index = 0;
            while ($enc = $res_encuestas->fetch_assoc()): 
                $id_enc = $enc['id_encuesta'];
                $index++;
                
                // Determinar el badge del estado del ciclo de vida de la encuesta
                $estado = $enc['estado'] ?? 'Abierta';
                $badge_estado = ($estado === 'Abierta') 
                    ? '<span class="badge bg-success px-2 py-1">Abierta</span>' 
                    : '<span class="badge bg-secondary px-2 py-1">Cerrada</span>';

                // Contar cuántos envíos tiene esta encuesta en particular
                $stmt_ev = $conexion->prepare("SELECT COUNT(*) as total_e FROM envio_encuesta WHERE id_encuesta = ?");
                $stmt_ev->bind_param("i", $id_enc);
                $stmt_ev->execute();
                $total_esta_encuesta = $stmt_ev->get_result()->fetch_assoc()['total_e'];
            ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <!-- Cabecera del Acordeón (Encuesta) -->
                    <div class="card-header bg-white border-0 p-4" id="heading<?php echo $index; ?>">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary">Servicio: <?php echo htmlspecialchars($enc['servicio'] ?? 'General'); ?></span>
                                    <?php echo $badge_estado; ?>
                                </div>
                                <h4 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($enc['nombre']); ?></h4>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($enc['descripcion']); ?></p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-info text-dark px-3 py-2 fs-6 rounded-pill">
                                    <?php echo $total_esta_encuesta; ?> respuestas recibidas
                                </span>
                                <!-- Botón Desplegable -->
                                <button class="btn btn-outline-secondary btn-sm fw-bold px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                                    Desplegar Datos ▼
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido Desplegable del Acordeón -->
                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#accordionEncuestas">
                        <div class="card-body px-4 pb-4 pt-0 bg-light">
                            <hr class="mt-0 mb-4">

                            <?php
                            $stmt_p = $conexion->prepare("SELECT id_pregunta, texto, tipo FROM pregunta WHERE id_encuesta = ?");
                            $stmt_p->bind_param("i", $id_enc);
                            $stmt_p->execute();
                            $res_preg = $stmt_p->get_result();

                            if ($res_preg && $res_preg->num_rows > 0):
                                while ($preg = $res_preg->fetch_assoc()):
                                    $id_p = $preg['id_pregunta'];
                            ?>
                                    <div class="card border-0 shadow-sm rounded-3 mb-3 p-4 bg-white">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0 fw-bold text-secondary"><?php echo htmlspecialchars($preg['texto']); ?></h5>
                                            <span class="badge bg-info text-dark px-3 py-1 rounded-pill"><?php echo $preg['tipo']; ?></span>
                                        </div>

                                        <?php if ($preg['tipo'] === 'Escala' || $preg['tipo'] === 'Opcion'): ?>
                                            <?php
                                            // 1. Obtener el total de respuestas para esta pregunta específica
                                            $stmt_tot_p = $conexion->prepare("SELECT COUNT(*) as total_resp FROM respuesta WHERE id_pregunta = ?");
                                            $stmt_tot_p->bind_param("i", $id_p);
                                            $stmt_tot_p->execute();
                                            $total_resp_pregunta = $stmt_tot_p->get_result()->fetch_assoc()['total_resp'];

                                            // 2. Obtener el conteo por cada valor ordenado de mayor a menor
                                            $stmt_conteo = $conexion->prepare("SELECT valor, COUNT(*) as cantidad FROM respuesta WHERE id_pregunta = ? GROUP BY valor ORDER BY cantidad DESC");
                                            $stmt_conteo->bind_param("i", $id_p);
                                            $stmt_conteo->execute();
                                            $res_conteo = $stmt_conteo->get_result();

                                            $max_cantidad = -1;
                                            $opciones_data = [];
                                            while ($row_c = $res_conteo->fetch_assoc()) {
                                                if ($max_cantidad == -1) {
                                                    $max_cantidad = $row_c['cantidad'];
                                                }
                                                $opciones_data[] = $row_c;
                                            }
                                            ?>

                                            <?php if ($total_resp_pregunta > 0): ?>
                                                <div class="mb-3 alert alert-secondary py-2 px-3 small">
                                                     <strong>Respuesta más popular / dada:</strong> 
                                                    <?php 
                                                    $ganadoras = [];
                                                    foreach ($opciones_data as $opt) {
                                                        if ($opt['cantidad'] == $max_cantidad) {
                                                            $ganadoras[] = htmlspecialchars($opt['valor']) . " (" . $opt['cantidad'] . " votos)";
                                                        }
                                                    }
                                                    echo implode(", ", $ganadoras);
                                                    ?>
                                                </div>

                                                <div class="d-flex flex-column gap-3">
                                                    <?php foreach ($opciones_data as $row): 
                                                        $porcentaje = ($total_resp_pregunta > 0) ? round(($row['cantidad'] / $total_resp_pregunta) * 100, 1) : 0;
                                                    ?>
                                                        <div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="fw-medium text-dark"><?php echo htmlspecialchars($row['valor']); ?></span>
                                                                <span class="text-muted small"><strong><?php echo $row['cantidad']; ?></strong> resp. (<?php echo $porcentaje; ?>%)</span>
                                                            </div>
                                                            <!-- Barra de Progreso Gráfica -->
                                                            <div class="progress" style="height: 12px;">
                                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $porcentaje; ?>%;" aria-valuenow="<?php echo $porcentaje; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-muted fst-italic mb-0">Sin respuestas registradas para esta pregunta.</p>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <!-- Preguntas de Texto libre / Comentarios -->
                                            <?php
                                            $stmt_textos = $conexion->prepare("SELECT r.valor, e.fecha_envio FROM respuesta r JOIN envio_encuesta e ON r.id_envio = e.id_envio WHERE r.id_pregunta = ? ORDER BY e.fecha_envio DESC");
                                            $stmt_textos->bind_param("i", $id_p);
                                            $stmt_textos->execute();
                                            $res_textos = $stmt_textos->get_result();
                                            ?>
                                            <div class="list-group list-group-flush">
                                                <?php if ($res_textos && $res_textos->num_rows > 0): ?>
                                                    <?php while ($row_t = $res_textos->fetch_assoc()): ?>
                                                        <div class="list-group-item px-0 py-2 border-bottom">
                                                            <p class="mb-1 text-dark fst-italic">"<?php echo htmlspecialchars($row_t['valor']); ?>"</p>
                                                            <small class="text-muted" style="font-size: 0.8rem;">Enviado el: <?php echo date("d/m/Y H:i", strtotime($row_t['fecha_envio'])); ?></small>
                                                        </div>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <p class="text-muted mb-0 py-1">No hay comentarios registrados todavía.</p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                            <?php 
                                endwhile;
                            else:
                                echo '<p class="text-muted text-center py-3">Esta encuesta no contiene preguntas asociadas.</p>';
                            endif;
                            ?>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-4">No hay encuestas registradas en el sistema para mostrar estadísticas.</div>
    <?php endif; ?>
</main>

<!-- Footer anclado abajo -->
<footer class="text-center py-4 text-white mt-auto" style="background-color: #0a2540;">
    <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>