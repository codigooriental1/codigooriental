<?php
/**
 * =========================================================================
 * S.I.G.S.M. - REGISTRO DE NUEVO USUARIO (PACIENTE)
 * =========================================================================
 * Este script procesa el registro público de nuevos pacientes en el sistema,
 * validando los datos obligatorios, protegiendo contra duplicados de cédula o correo,
 * aplicando hashing seguro de contraseñas, validando el token CSRF y asignando el rol de paciente.
 */

session_start();
require_once "conexion.php";

// Generar el token CSRF único para este formulario de registro
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$nombre = "";
$apellido = "";
$cedula = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Validación del token CSRF de seguridad
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Acción no autorizada (Error de validación de seguridad CSRF).");
    }

    $nombre           = trim($_POST['nombre'] ?? '');
    $apellido         = trim($_POST['apellido'] ?? '');
    $cedula           = trim($_POST['cedula'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($nombre) || empty($apellido) || empty($cedula) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Por favor, complete todos los campos.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // VERIFICAR SI YA EXISTE EL CORREO O LA CÉDULA EN LA BASE DE DATOS
        $stmt_check = $conexion->prepare("SELECT id_usuario FROM usuario WHERE correo = ? OR cedula = ?");
        if ($stmt_check) {
            $stmt_check->bind_param("ss", $email, $cedula);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $error = "El correo electrónico o la cédula de identidad ya se encuentran registrados.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $id_rol_paciente = 2; // Rol Paciente

                $stmt = $conexion->prepare("INSERT INTO usuario (nombre, apellido, cedula, correo, contrasena, id_rol) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sssssi", $nombre, $apellido, $cedula, $email, $password_hash, $id_rol_paciente);
                    if ($stmt->execute()) {
                        header("Location: login.php?ok=1");
                        exit();
                    } else {
                        $error = "Error al registrar el usuario en la base de datos.";
                    }
                    $stmt->close();
                } else {
                    $error = "Error en la consulta de inserción.";
                }
            }
            $stmt_check->close();
        } else {
            $error = "Error al procesar la solicitud.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body class="bg-light">

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
            </div>
        </div>
</div>

<header class="bg-white py-4 border-bottom">
    <div class="container text-center">
        <img src="https://www.hc.edu.uy/imgnoticias/202609/1201.jpg" width="400" alt="Hospital de Clínicas" class="logo img-fluid">
        <h1 class="display-5 mt-3">S.I.G.S.M.</h1>
        <p class="lead">Sistema de Información e Indicaciones Médicas</p>
    </div>
</header>

<main class="container my-5">

<div class="mb-4">
    <a href="index.php" class="btn btn-outline-primary btn-lg px-5 py-3">
        ← Volver al inicio
    </a>
</div>

<section class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card tarjeta shadow-sm">
            <div class="card-body p-5">

                <h3 class="card-title text-center mb-4">Registro de Usuario</h3>
                <p class="text-muted text-center mb-4">Complete los datos para crear su cuenta en el sistema.</p>

                <div id="js-error-message" class="alert alert-danger" style="display: none;"></div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form id="registroForm" action="registro.php" method="POST">
                    
                    <!-- TOKEN CSRF DE SEGURIDAD -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">Nombre</label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="nombre" 
                               name="nombre" 
                               placeholder="Ej: Juan" 
                               value="<?php echo htmlspecialchars($nombre); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="apellido" class="form-label fw-bold">Apellido</label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="apellido" 
                               name="apellido" 
                               placeholder="Ej: Pérez" 
                               value="<?php echo htmlspecialchars($apellido); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="cedula" class="form-label fw-bold">Cédula de Identidad</label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="cedula" 
                               name="cedula" 
                               placeholder="Ej: 45678912" 
                               value="<?php echo htmlspecialchars($cedula); ?>" required maxlength="8">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" 
                               class="form-control form-control-lg" 
                               id="email" 
                               name="email" 
                               placeholder="ejemplo@hc.edu.uy" 
                               value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Contraseña</label>
                        <input type="password" 
                               class="form-control form-control-lg" 
                               id="password" 
                               name="password" 
                               placeholder="Mínimo 8 caracteres" required>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-bold">Confirmar Contraseña</label>
                        <input type="password" 
                               class="form-control form-control-lg" 
                               id="confirm_password" 
                               name="confirm_password" 
                               placeholder="Repita la contraseña" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-info text-white btn-lg py-3">Registrarme</button>
                    </div>

                </form>

                <hr class="my-4">

                <div class="text-center">
                    <small class="text-muted">¿Ya tenés una cuenta?</small>
                    <br>
                    <a href="login.php" class="fw-bold text-decoration-none">Iniciar Sesión</a>
                </div>

            </div>
        </div>
    </div>
</section>

</main>

    <footer class="text-center py-4 text-white" style="background-color: #0a2540;">
        <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registroForm');
    const nombreInput = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const cedulaInput = document.getElementById('cedula');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const jsErrorDiv = document.getElementById('js-error-message');

    form.addEventListener('submit', (e) => {
        let errores = [];
        jsErrorDiv.style.display = 'none';
        jsErrorDiv.textContent = '';

        if (nombreInput.value.trim() === '') {
            errores.push('El nombre es obligatorio.');
        }
        if (apellidoInput.value.trim() === '') {
            errores.push('El apellido es obligatorio.');
        }
        if (cedulaInput.value.trim() === '') {
            errores.push('La cédula es obligatoria.');
        }
        if (emailInput.value.trim() === '') {
            errores.push('El correo electrónico es obligatorio.');
        }
        if (passwordInput.value.trim() === '') {
            errores.push('La contraseña es obligatoria.');
        } else if (passwordInput.value.length < 8) {
            errores.push('La contraseña debe tener al menos 8 caracteres.');
        }
        if (passwordInput.value !== confirmPasswordInput.value) {
            errores.push('Las contraseñas no coinciden.');
        }

        if (errores.length > 0) {
            e.preventDefault();
            jsErrorDiv.textContent = errores.join(' ');
            jsErrorDiv.style.display = 'block';
        }
    });
});
</script>

</body>
</html>