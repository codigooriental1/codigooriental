<?php
/**
 * =========================================================================
 * S.I.G.S.M. - PANEL DE GESTIÓN DE ADMINISTRADOR (SUPERVISOR)
 * =========================================================================
 * Permite a los administradores administrar los usuarios (cambio de rol,
 * edición de datos, alta y baja) y gestionar los servicios hospitalarios.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../conexion.php";

// Control de acceso estricto: Solo Administrador (Rol 1)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol']) || (int)$_SESSION['id_rol'] !== 1) {
    header("Location: ../vistas/encuestas.php?error=acceso_denegado");
    exit();
}

// Generar token CSRF para acciones de formulario
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener listado de usuarios con el nombre de su rol
$sql_usuarios = "SELECT u.*, r.nombre AS nombre_rol 
                 FROM usuario u 
                 LEFT JOIN rol r ON u.id_rol = r.id_rol 
                 ORDER BY u.id_usuario DESC";
$res_usuarios = $conexion->query($sql_usuarios);

// Obtener listado de servicios hospitalarios
$sql_servicios = "SELECT * FROM servicio ORDER BY id_servicio DESC";
$res_servicios = $conexion->query($sql_servicios);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Panel de Supervisor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../recursos/css/estilos.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- BARRA SUPERIOR DE SESIÓN -->
    <div class="py-2 border-bottom" style="background-color: #0d2d4e;">
        <div class="container d-flex justify-content-between align-items-center">
            <small class="text-white">S.I.G.S.M. - Panel Exclusivo de Administración</small>
            <div>
                <span class="text-white fw-bold me-2 small">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="container my-5 flex-grow-1">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <a href="../index.php" class="btn btn-outline-primary btn-lg px-4 py-2">← Volver al Inicio</a>
            <h2 class="mb-0 fw-bold text-primary"> Panel de Gestión de Supervisor</h2>
        </div>

        <!-- Alertas de estado -->
        <?php if (isset($_GET['ok'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                    if ($_GET['ok'] == 1) echo "Usuario registrado correctamente.";
                    elseif ($_GET['ok'] == 2) echo "Datos/Rol del usuario actualizados correctamente.";
                    elseif ($_GET['ok'] == 3) echo "Servicio hospitalario guardado correctamente.";
                    elseif ($_GET['ok'] == 4) echo "Estado del servicio actualizado.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                Ocurrió un error al procesar la solicitud. Verifique los datos.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- NAVEGACIÓN POR PESTAÑAS (TAB-NAVS) -->
        <ul class="nav nav-tabs mb-4" id="adminTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios-panel" type="button" role="tab">👥 Gestión de Usuarios</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="servicios-tab" data-bs-toggle="tab" data-bs-target="#servicios-panel" type="button" role="tab">🏥 Gestión de Servicios</button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabContent">
            
            <!-- ========================================================================= -->
            <!-- PESTAÑA 1: GESTIÓN DE USUARIOS -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade show active" id="usuarios-panel" role="tabpanel">
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold text-secondary mb-0">Usuarios Registrados</h4>
                    <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAltaUsuario">+ Registrar Nuevo Usuario</button>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre y Apellido</th>
                                    <th>Cédula</th>
                                    <th>Correo Electrónico</th>
                                    <th>Rol actual</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($res_usuarios && $res_usuarios->num_rows > 0): ?>
                                    <?php while ($user = $res_usuarios->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $user['id_usuario']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($user['nombre'] . ' ' . ($user['apellido'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars($user['cedula'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($user['correo'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge <?php echo ($user['id_rol'] == 1) ? 'bg-danger' : (($user['id_rol'] == 3) ? 'bg-warning text-dark' : 'bg-info text-dark'); ?>">
                                                    <?php echo htmlspecialchars($user['nombre_rol'] ?? 'Rol #' . $user['id_rol']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1 justify-content-end align-items-center">
                                                    <!-- Botón para Editar Datos -->
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm btn-editar-usuario"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEditarUsuario"
                                                            data-id="<?php echo $user['id_usuario']; ?>"
                                                            data-nombre="<?php echo htmlspecialchars($user['nombre']); ?>"
                                                            data-apellido="<?php echo htmlspecialchars($user['apellido'] ?? ''); ?>"
                                                            data-cedula="<?php echo htmlspecialchars($user['cedula'] ?? ''); ?>"
                                                            data-correo="<?php echo htmlspecialchars($user['correo'] ?? ''); ?>"
                                                            data-rol="<?php echo $user['id_rol']; ?>">
                                                        Editar
                                                    </button>

                                                    <!-- Formulario de Eliminación / Baja -->
                                                    <form action="../acciones/gestionar_usuario.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                        <input type="hidden" name="id_usuario" value="<?php echo $user['id_usuario']; ?>">
                                                        <button type="submit" name="accion" value="alternar_estado" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar definitivamente a este usuario?');">Baja</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- PESTAÑA 2: GESTIÓN DE SERVICIOS HOSPITALARIOS -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade" id="servicios-panel" role="tabpanel">
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold text-secondary mb-0">Servicios Institucionales</h4>
                    <button class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAltaServicio">+ Agregar Nuevo Servicio</button>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre del Servicio</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($res_servicios && $res_servicios->num_rows > 0): ?>
                                    <?php while ($serv = $res_servicios->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $serv['id_servicio']; ?></strong></td>
                                            <td class="fw-bold text-primary"><?php echo htmlspecialchars($serv['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($serv['descripcion'] ?? 'Sin descripción'); ?></td>
                                            <td>
                                                <?php if (($serv['activo'] ?? 1) == 1): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <form action="../acciones/gestionar_servicio.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="id_servicio" value="<?php echo $serv['id_servicio']; ?>">
                                                    <input type="hidden" name="nuevo_estado" value="<?php echo (($serv['activo'] ?? 1) == 1) ? 0 : 1; ?>">
                                                    
                                                    <?php if (($serv['activo'] ?? 1) == 1): ?>
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Desactivar este servicio?');">Desactivar</button>
                                                    <?php else: ?>
                                                        <button type="submit" class="btn btn-outline-success btn-sm">Activar</button>
                                                    <?php endif; ?>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No hay servicios registrados en la base de datos.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- MODAL ALTA USUARIO -->
    <div class="modal fade" id="modalAltaUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="../acciones/gestionar_usuario.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="accion" value="crear_usuario">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Registrar Nuevo Usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cédula de Identidad</label>
                            <input type="text" name="cedula" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Asignar Rol</label>
                            <select name="id_rol" class="form-select" required>
                                <option value="2">Paciente</option>
                                <option value="3">Funcionario</option>
                                <option value="1">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR USUARIO -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="../acciones/gestionar_usuario.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="accion" value="editar_usuario">
                    <input type="hidden" name="id_usuario" id="edit_id_usuario">

                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold">Editar Datos de Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" id="edit_apellido" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cédula de Identidad</label>
                            <input type="text" name="cedula" id="edit_cedula" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo" id="edit_correo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña (Opcional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la actual">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Asignar Rol</label>
                            <select name="id_rol" id="edit_id_rol" class="form-select" required>
                                <option value="2">Paciente</option>
                                <option value="3">Funcionario</option>
                                <option value="1">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning fw-bold">Actualizar Datos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL ALTA SERVICIO -->
    <div class="modal fade" id="modalAltaServicio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="../acciones/gestionar_servicio.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="accion" value="crear_servicio">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Agregar Nuevo Servicio</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Servicio</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Pediatría, Odontología..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Servicio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FOOTER INSTITUCIONAL -->
    <footer class="text-center py-4 text-white mt-auto" style="background-color: #0a2540;">
        <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cargar datos dinámicos en el Modal de Edición de Usuario
        document.querySelectorAll('.btn-editar-usuario').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('edit_id_usuario').value = this.getAttribute('data-id');
                document.getElementById('edit_nombre').value = this.getAttribute('data-nombre');
                document.getElementById('edit_apellido').value = this.getAttribute('data-apellido');
                document.getElementById('edit_cedula').value = this.getAttribute('data-cedula');
                document.getElementById('edit_correo').value = this.getAttribute('data-correo');
                document.getElementById('edit_id_rol').value = this.getAttribute('data-rol');
            });
        });
    </script>
</body>
</html>