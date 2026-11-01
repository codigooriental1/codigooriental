<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "conexion.php";

// Protección de ruta: Solo Administrador (1) o Funcionario (3)[cite: 11, 13]
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol']) || !in_array((int)$_SESSION['id_rol'], [1, 3])) {
    header("Location: login.php?error=acceso_denegado");
    exit();
}

// 1. Obtener total de respuestas enviadas[cite: 11]
$res_total = $conexion->query("SELECT COUNT(*) AS total FROM envio_encuesta");
$total_envios = $res_total ? $res_total->fetch_assoc()['total'] : 0;

// 2. Obtener preguntas y el detalle de sus respuestas[cite: 11]
$sql_preguntas = "SELECT id_pregunta, texto, tipo FROM pregunta ORDER BY id_pregunta ASC";
$res_preguntas = $conexion->query($sql_preguntas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Estadísticas de Encuestas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="bg-light">

   <div class="py-2 border-bottom" style="background-color: #0d2d4e;">
        <div class="container d-flex justify-content-between align-items-center">
            <small class="text-white">S.I.G.S.M. - Panel de Control</small>
            <div>
                <span class="text-white fw-bold me-2 small">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </div>

<main class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <a href="index.php" class="btn btn-outline-primary btn-lg px-4 py-2">← Volver al inicio</a>
        <h2 class="mb-0 fw-bold text-primary">📊 Estadísticas de Encuestas</h2>
    </div>

    <!-- Tarjeta Resumen -->
    <div class="row mb-5">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white p-4" style="background: linear-gradient(135deg, #0d6efd, #0d2d4e);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase tracking-wider opacity-75 mb-1">Total de Encuestas</h6>
                        <h2 class="display-5 fw-bold mb-0"><?php echo $total_envios; ?></h2>
                    </div>
                    <div class="fs-1 opacity-50">📋</div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($res_preguntas && $res_preguntas->num_rows > 0): ?>
        <?php while ($preg = $res_preguntas->fetch_assoc()): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold text-secondary"><?php echo htmlspecialchars($preg['texto']); ?></h5>
                        <span class="badge bg-info text-dark px-3 py-2 rounded-pill text-capitalize"><?php echo $preg['tipo']; ?></span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <?php if ($preg['tipo'] === 'Escala' || $preg['tipo'] === 'Opcion'): ?>
                        <?php
                        $id_p = $preg['id_pregunta'];
                        $sql_conteo = "SELECT valor, COUNT(*) as cantidad FROM respuesta WHERE id_pregunta = $id_p GROUP BY valor ORDER BY cantidad DESC";
                        $res_conteo = $conexion->query($sql_conteo);
                        ?>
                        <ul class="list-group list-group-flush">
                            <?php while ($row = $res_conteo->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <span class="fw-medium"><?php echo htmlspecialchars($row['valor']); ?></span>
                                    <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo $row['cantidad']; ?> respuesta(s)</span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <?php
                        $id_p = $preg['id_pregunta'];
                        $sql_textos = "SELECT r.valor, e.fecha_envio FROM respuesta r JOIN envio_encuesta e ON r.id_envio = e.id_envio WHERE r.id_pregunta = $id_p ORDER BY e.fecha_envio DESC";
                        $res_textos = $conexion->query($sql_textos);
                        ?>
                        <div class="list-group list-group-flush">
                            <?php if ($res_textos && $res_textos->num_rows > 0): ?>
                                <?php while ($row = $res_textos->fetch_assoc()): ?>
                                    <div class="list-group-item px-0 py-3">
                                        <p class="mb-1 text-dark fst-italic">"<?php echo htmlspecialchars($row['valor']); ?>"</p>
                                        <small class="text-muted">Enviado el: <?php echo date("d/m/Y H:i", strtotime($row['fecha_envio'])); ?></small>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-muted mb-0 py-2">No hay comentarios registrados todavía.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center py-4">No hay preguntas registradas en el sistema.</div>
    <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>