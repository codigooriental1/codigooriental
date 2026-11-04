<?php
/**
 * =========================================================================
 * S.I.G.S.M. - LISTADO Y GESTIÓN DE DOCUMENTOS
 * =========================================================================
 * Este script lista los documentos disponibles filtrados por servicio o título,
 * aplicando controles de visibilidad según el rol y la cédula del usuario.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../conexion.php';

// Generar token CSRF para las acciones que lo requieran
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener servicios activos desde la base de datos para el filtro desplegable
$servicios_filtro = [];
$res_serv_filtro = $conexion->query("SELECT nombre FROM servicio WHERE activo = 1 ORDER BY nombre ASC");
if ($res_serv_filtro && $res_serv_filtro->num_rows > 0) {
    while ($s = $res_serv_filtro->fetch_assoc()) {
        $servicios_filtro[] = $s['nombre'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Documentos</title>
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

<header class="bg-white py-4 border-bottom">
    <div class="container text-center">
        <img src="https://www.hc.edu.uy/imgnoticias/202609/1201.jpg" width="400" alt="Hospital de Clínicas" class="logo img-fluid" style="max-width: 380px;">
        <h1 class="display-6 mt-3 fw-bold">Documentos</h1>
        <p class="lead text-muted fs-5">Administración e información de documentos del sistema.</p>
    </div>
</header>

<main class="container my-5 flex-grow-1">

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="../index.php" class="btn btn-outline-primary btn-lg px-4 py-2">
        ← Volver al inicio
    </a>
    <?php if (isset($_SESSION['id_rol']) && in_array((int)$_SESSION['id_rol'], [1, 3])): ?>
        <a href="../acciones/alta_documento.php" class="btn btn-primary btn-lg px-4 py-2">
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
<div class="card tarjeta shadow-sm border-0 rounded-4">
<div class="card-body p-4 p-md-5">

<h3 class="card-title mb-4 fw-bold">Lista de documentos</h3>

<!-- Formulario de filtro dinámico por servicio y búsqueda -->
<form method="GET" action="documentos.php" class="d-flex flex-column flex-md-row gap-3 mb-4">
    <div class="input-group input-group-lg flex-grow-1">
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
                if (isset($_GET['tipo']) && !empty($_GET['tipo'])) {
                    echo "Servicio: " . htmlspecialchars($_GET['tipo']);
                } else {
                    echo "Filtrar por servicio";
                }
            ?>
        </button>
        <ul class="dropdown-menu">
            <?php if (!empty($servicios_filtro)): ?>
                <?php foreach ($servicios_filtro as $sf): ?>
                    <li>
                        <a class="dropdown-item" href="documentos.php?tipo=<?php echo urlencode($sf); ?><?php echo isset($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : ''; ?>">
                            <?php echo htmlspecialchars($sf); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li><span class="dropdown-item text-muted">No hay servicios disponibles</span></li>
            <?php endif; ?>

            <?php if (!empty($_GET['tipo']) || !empty($_GET['buscar'])): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="documentos.php">Quitar filtros</a></li>
            <?php endif; ?>
        </ul>
    </div>
</form>

<ul class="list-group">
<?php
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$tipo_filtro = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';

$rol_usuario = $_SESSION['id_rol'] ?? 0;
$cedula_usuario = $_SESSION['cedula'] ?? '';

if (in_array((int)$rol_usuario, [1, 3])) {
    $sql = "SELECT * FROM documento WHERE activo = 1";
    $params = [];
    $types = "";
} else {
    $sql = "SELECT * FROM documento WHERE activo = 1 AND (cedula_paciente IS NULL OR cedula_paciente = '' OR cedula_paciente = ?)";
    $params = [$cedula_usuario];
    $types = "s";
}

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
        $tipo_texto = htmlspecialchars($fila['tipo'] ?? 'General');
        $badge_color = 'bg-secondary';
        if ($tipo_texto === 'Emergencia') $badge_color = 'bg-danger';
        elseif ($tipo_texto === 'CTI') $badge_color = 'bg-dark';
        elseif ($tipo_texto === 'Médica A' || $tipo_texto === 'Medica A') $badge_color = 'bg-primary';
        
        if (!empty($fila['cedula_paciente'])) {
            $destinatario = '<span class="badge bg-warning text-dark me-1">Privado</span> C.I. Paciente: ' . htmlspecialchars($fila['cedula_paciente']);
        } else {
            $destinatario = '<span class="badge bg-success me-1">Público</span> Documento Institucional';
        }

        // Obtener el nombre básico del archivo guardado en BD y resolver la ruta relativa en minúsculas
        $nombre_archivo = basename($fila['ruta_archivo']);
        $ruta_relativa = '../recursos/documentos/' . $nombre_archivo;
?>
<li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center py-3 gap-3">
    <div>
        <strong class="fs-5"><?php echo htmlspecialchars($fila['titulo']); ?></strong>
        <br>
        <small class="text-muted">
            <?php echo $destinatario; ?> | Fecha: <?php echo date("d/m/Y", strtotime($fila['fecha_emision'])); ?>
        </small>
    </div>
    
    <div class="d-flex align-items-center flex-wrap gap-2 w-100 w-md-auto justify-content-end">
        <span class="badge <?php echo $badge_color; ?> fs-6"><?php echo $tipo_texto; ?></span>

       <?php if (!empty($nombre_archivo) && file_exists($ruta_relativa)): ?>
            <a href="<?php echo htmlspecialchars($ruta_relativa); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                Ver / Descargar
            </a>
        <?php else: ?>
            <span class="badge bg-danger" title="El archivo físico no se encuentra en el servidor o está dañado">Archivo No Disponible</span>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['id_rol']) && in_array((int)$_SESSION['id_rol'], [1, 3])): ?>
            <a href="../acciones/editar_documento.php?id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-outline-warning">Editar</a>
            
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
<li class="list-group-item py-4 text-center text-muted">No se encontraron documentos disponibles.</li>
<?php
}
?>
</ul>

</div>
</div>
</div>
</section>
</main>

    <!-- Modales -->
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
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <form action="../acciones/baja_documento.php" method="POST" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="idDocumentoBaja" value="">
                <button type="submit" class="btn btn-danger">Confirmar Baja</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer anclado abajo -->
    <footer class="text-center py-4 text-white mt-auto" style="background-color: #0a2540;">
        <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
const modalBaja = document.getElementById('modalBaja');
if (modalBaja) {
    modalBaja.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('idDocumentoBaja').value = button.getAttribute('data-id');
        document.getElementById('tituloDocumentoBaja').textContent = button.getAttribute('data-titulo');
    });
}
</script>
</body>
</html>