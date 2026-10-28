<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Documentos</title>
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
        <img src="https://www.hc.edu.uy/imgnoticias/202609/1201.jpg" width="400" alt="Hospital de Clínicas" class="logo">
        <h1 class="display-5 mt-3">Documentos</h1>
        <p class="lead">Administración de documentos del sistema.</p>
    </div>
</header>

<main class="container my-5">

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="index.html" class="btn btn-outline-primary btn-lg px-5 py-3">
        ← Volver al inicio
    </a>
    <a href="alta_documento.php" class="btn btn-primary btn-lg px-4 py-3">
        + Nuevo Documento
    </a>
</div>

<?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php
            if ($_GET['ok'] == 1) echo "Documento creado correctamente.";
            elseif ($_GET['ok'] == 2) echo "Documento actualizado correctamente.";
            elseif ($_GET['ok'] == 3) echo "Documento eliminado correctamente.";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<section class="row justify-content-center">
<div class="col-12 col-xl-10">
<div class="card tarjeta shadow-sm">
<div class="card-body p-5">

<h3 class="card-title mb-4">Lista de documentos</h3>

<form method="GET" action="documentos.php" class="d-flex gap-3 mb-4">
    <div class="input-group input-group-lg">
        <input type="text" 
               name="buscar" 
               class="form-control" 
               placeholder="Buscar documento..." 
               value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
        <button type="submit" class="btn btn-outline-primary">Buscar</button>
        <?php if (!empty($_GET['buscar'])): ?>
            <a href="documentos.php" class="btn btn-outline-secondary">Limpiar</a>
        <?php endif; ?>
    </div>

    <div class="dropdown">
        <button type="button" class="btn btn-outline-secondary btn-lg dropdown-toggle" data-bs-toggle="dropdown">Filtrar</button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Información</a></li>
            <li><a class="dropdown-item" href="#">indicacion</a></li>
        </ul>
    </div>
</form>

<ul class="list-group">
<?php
require_once 'conexion.php';

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if (!empty($busqueda)) {
    $stmt = $conexion->prepare("SELECT * FROM documento WHERE activo = 1 AND (titulo LIKE ? OR cedula_paciente LIKE ?) ORDER BY fecha_emision DESC");
    $param_busqueda = "%" . $busqueda . "%";
    $stmt->bind_param("ss", $param_busqueda, $param_busqueda);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $sql = "SELECT * FROM documento WHERE activo = 1 ORDER BY fecha_emision DESC";
    $resultado = $conexion->query($sql);
}

if ($resultado && $resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $tipo_texto = ($fila['tipo'] === 'indicacion') ? 'Indicación' : 'Información';
        $badge_color = ($fila['tipo'] === 'indicacion') ? 'bg-info' : 'bg-secondary';
?>
<li class="list-group-item d-flex justify-content-between align-items-center py-3">
    <div>
        <strong><?php echo htmlspecialchars($fila['titulo']); ?></strong>
        <br>
        <small class="text-muted">
            C.I. Paciente: <?php echo htmlspecialchars($fila['cedula_paciente']); ?> | Fecha: <?php echo date("d/m/Y", strtotime($fila['fecha_emision'])); ?>
        </small>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <span class="badge <?php echo $badge_color; ?>"><?php echo $tipo_texto; ?></span>
        
        <a href="editar_documento.php?id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-outline-warning">Editar</a>
        
        <button type="button" 
                class="btn btn-sm btn-outline-danger" 
                data-bs-toggle="modal" 
                data-bs-target="#modalBaja" 
                data-id="<?php echo $fila['id']; ?>" 
                data-titulo="<?php echo htmlspecialchars($fila['titulo']); ?>">
            Baja
        </button>
    </div>
</li>
<?php
    }
} else {
?>
<li class="list-group-item py-3 text-center text-muted">No se encontraron documentos.</li>
<?php
}
?>
</ul>

</div>
</div>
</div>
</section>
</main>

<div class="modal fade" id="modalBaja" tabindex="-1" aria-labelledby="modalBajaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalBajaLabel">Confirmar Baja de Documento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>¿Está seguro de que desea dar de baja el siguiente documento?</p>
        <p class="fw-bold text-secondary" id="tituloDocumentoBaja"></p>
        <small class="text-muted">Esta acción marcará el registro como inactivo en el sistema.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form action="baja_documento.php" method="POST" class="d-inline">
            <input type="hidden" name="id" id="idDocumentoBaja" value="">
            <button type="submit" class="btn btn-danger">Confirmar Baja</button>
        </form>
      </div>
    </div>
  </div>
</div>

<footer class="text-center py-4">
<p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
const modalBaja = document.getElementById('modalBaja');
if (modalBaja) {
    modalBaja.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const titulo = button.getAttribute('data-titulo');
        
        document.getElementById('idDocumentoBaja').value = id;
        document.getElementById('tituloDocumentoBaja').textContent = titulo;
    });
}
</script>

</body>
</html>