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

<div class="bg-light border-bottom py-2">
    <div class="container">
        <small class="text-muted">
            📞 Atención al Usuario:
            <strong>1953 / 0800 1953</strong>
            <span class="mx-3">|</span>
            📧 atencionalusuario@hc.edu.uy
        </small>
    </div>
</div>

<header class="bg-white py-4 border-bottom">
    <div class="container text-center">
        <img src="https://hc.edu.uy/images/imagenesarticulos/Logo_Hc.png" width="400" alt="Hospital de Clínicas" class="logo">
        <h1 class="display-5 mt-3">Encuestas</h1>
        <p class="lead">Encuesta de satisfacción del paciente.</p>
    </div>
</header>

<main class="container my-5">

<div class="mb-4">
    <a href="index.html" class="btn btn-outline-primary btn-lg px-5 py-3">
        ← Volver al inicio
    </a>
</div>

<section class="row justify-content-center">
<div class="col-12 col-xl-10">
<div class="card tarjeta shadow-sm">
<div class="card-body p-5">

<?php
include 'conexion.php';
$sql = "SELECT id_encuesta, nombre, descripcion FROM ENCUESTA WHERE estado = 'Abierta' LIMIT 1";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $encuesta = $resultado->fetch_assoc();
?>

<h3 class="card-title mb-4"><?php echo $encuesta['nombre']; ?></h3>
<p class="text-muted mb-4"><?php echo $encuesta['descripcion']; ?></p>

<form>
    <div class="mb-4">
        <label class="form-label">Nombre Completo (opcional)</label>
        <input type="text" class="form-control form-control-lg" placeholder="Ingrese su nombre">
    </div>

    <?php
    $id_encuesta = $encuesta['id_encuesta'];
    $sql_preguntas = "SELECT id_pregunta, texto, tipo FROM PREGUNTA WHERE id_encuesta = $id_encuesta";
    $res_preguntas = $conexion->query($sql_preguntas);

    while($pregunta = $res_preguntas->fetch_assoc()) {
    ?>
        <div class="mb-4">
            <label class="form-label"><?php echo $pregunta['texto']; ?></label>
            
            <?php if($pregunta['tipo'] == 'Escala' || $pregunta['tipo'] == 'Opcion'): ?>
                <select class="form-select form-select-lg">
                    <option selected disabled>Seleccione una opción</option>
                    <option>Excelente / 5</option>
                    <option>Muy buena / 4</option>
                    <option>Buena / 3</option>
                    <option>Regular / 2</option>
                    <option>Mala / 1</option>
                </select>
            <?php else: ?>
                <textarea class="form-control" rows="4" placeholder="Escriba aquí su respuesta..."></textarea>
            <?php endif; ?>
        </div>
    <?php } ?>

    <div class="d-grid">
        <button class="btn btn-primary btn-lg" type="submit">Enviar encuesta</button>
    </div>
</form>

<?php
} else {
?>
    <h3 class="card-title mb-4">Encuestas</h3>
    <p class="text-muted">No hay encuestas activas disponible en este momento.</p>
<?php
}
?>

</div>
</div>
</div>
</section>

</main>

<footer class="text-center py-4">
    <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
</footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/validaciones.js"></script>
</body>
</html>