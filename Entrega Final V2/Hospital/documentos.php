<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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
        <h1 class="display-5 mt-3">Documentos</h1>
        <p class="lead">Administración de documentos del sistema.</p>
    </div>
</header>

<main class="container my-5">

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="index.php" class="btn btn-outline-primary btn-lg px-5 py-3">
        ← Volver al inicio
    </a>
    <?php if (isset($_SESSION['id_rol']) && in_array((int)$_SESSION['id_rol'], [1, 3])): ?>
        <a href="alta_documento.php" class="btn btn-primary btn-lg px-4 py-3">
            + Nuevo Documento
        </a>
    <?php endif; ?>
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
        <?php if (!empty($_GET['buscar']) || !empty($_GET['tipo'])): ?>
            <a href="documentos.php" class="btn btn-outline-secondary">Limpiar</a>
        <?php endif; ?>
    </div>

    <div class="dropdown">
        <button type="button" class="btn btn-outline-secondary btn-lg dropdown-toggle" data-bs-toggle="dropdown">
            <?php 
                if (isset($_GET['tipo']) && $_GET['tipo'] === 'informacion') echo 'Información';
                elseif (isset($_GET['tipo']) && $_GET['tipo'] === 'indicacion') echo 'Indicación';
                else echo 'Filtrar';
            ?>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="documentos.php?tipo=informacion<?php echo isset($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : ''; ?>">Información</a></li>
            <li><a class="dropdown-item" href="documentos.php?tipo=indicacion<?php echo isset($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : ''; ?>">Indicación</a></li>
            <?php if (!empty($_GET['tipo']) || !empty($_GET['buscar'])): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="documentos.php">Quitar filtros</a></li>
            <?php endif; ?>
        </ul>
    </div>
</form>

<ul class="list-group">
<?php
require_once 'conexion.php';

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$tipo_filtro = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';

$sql = "SELECT * FROM documento WHERE activo = 1";
$params = [];
$types = "";

if (!empty($busqueda)) {
    $sql .= " AND (titulo LIKE ? OR cedula_paciente LIKE ?)";
    $param_busqueda = "%" . $busqueda . "%";
    $params[] = $param_busqueda;
    $params[] = $param_busqueda;
    $types .= "ss";
}

if (!empty($tipo_filtro)) {
    $sql .= " AND tipo = ?";
    $params[] = $tipo_filtro;
    $types .= "s";
}

$sql .= " ORDER BY fecha_emision DESC";

if (!empty($params)) {
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
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
        
        <?php if (isset($_SESSION['id_rol']) && in_array((int)$_SESSION['id_rol'], [1, 3])): ?>
            <a href="editar_documento.php?id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-outline-warning">Editar</a>
            
            <button type="button" 
                    class="btn btn-sm btn-outline-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalBaja" 
                    data-id="<?php echo $fila['id']; ?>" 
                    data-titulo="<?php echo htmlspecialchars($fila['titulo']); ?>">
                Baja
            </button>
        <?php endif; ?>
    </div>
</li>
<?php
    }
} else {
?>
<li class="list-group-item py-3 text-center text-muted">No se encontraron documentos con los filtros seleccionados.</li>
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

    <footer class="text-center py-4 text-white" style="background-color: #0a2540;">
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