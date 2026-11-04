<?php
/**
 * =========================================================================
 * S.I.G.S.M. - INICIO DE SESIÓN / AUTENTICACIÓN DE USUARIOS
 * =========================================================================
 * Este script procesa el acceso de usuarios al sistema mediante correo o cédula,
 * verificando contraseñas hasheadas, aplicando control de token CSRF, límite
 * de intentos de inicio de sesión y verificación de CAPTCHA matemático local.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "conexion.php";

// Generar token CSRF para el formulario de login
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inicializar el contador de intentos de sesión si no existe
if (!isset($_SESSION['intentos_login'])) {
    $_SESSION['intentos_login'] = 0;
}

$mensaje_error = "";

// Comprobar si el acceso está bloqueado por superar el límite de intentos
if (isset($_SESSION['bloqueado_hasta']) && time() < $_SESSION['bloqueado_hasta']) {
    $minutos_restantes = ceil(($_SESSION['bloqueado_hasta'] - time()) / 60);
    $mensaje_error = "Demasiados intentos fallidos. Intente nuevamente en {$minutos_restantes} minuto(s).";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($mensaje_error)) {
    
    // Validación de seguridad CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Acción no autorizada (Error de validación de seguridad CSRF).");
    }

    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $respuesta_captcha = (int)($_POST['captcha_respuesta'] ?? 0);
    $resultado_esperado = $_SESSION['captcha_resultado'] ?? null;

    // Validar CAPTCHA matemático antes de consultar la base de datos
    if ($resultado_esperado === null || $respuesta_captcha !== $resultado_esperado) {
        $mensaje_error = "La respuesta a la prueba de seguridad (CAPTCHA) es incorrecta.";
    } elseif (!empty($usuario) && !empty($password)) {
        
        $stmt = $conexion->prepare("SELECT id_usuario, nombre, cedula, id_rol, contrasena FROM USUARIO WHERE correo = ? OR cedula = ?");
        
        if ($stmt) {
            $stmt->bind_param("ss", $usuario, $usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado && $resultado->num_rows === 1) {
                $datos_usuario = $resultado->fetch_assoc();

                if (password_verify($password, $datos_usuario['contrasena']) || $password === $datos_usuario['contrasena']) {
                    // Reiniciar intentos y CAPTCHA al autenticarse con éxito
                    $_SESSION['intentos_login'] = 0;
                    unset($_SESSION['bloqueado_hasta']);
                    unset($_SESSION['captcha_resultado']);

                    $_SESSION['usuario_id'] = $datos_usuario['id_usuario'];
                    $_SESSION['nombre']     = $datos_usuario['nombre'];
                    $_SESSION['cedula']     = $datos_usuario['cedula'];
                    $_SESSION['id_rol']     = $datos_usuario['id_rol'];
                    $_SESSION['correo']     = $usuario;

                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['intentos_login']++;
                    if ($_SESSION['intentos_login'] >= 5) {
                        $_SESSION['bloqueado_hasta'] = time() + (5 * 60); // Bloqueo de 5 minutos
                        $mensaje_error = "Demasiados intentos fallidos. Cuenta bloqueada por 5 minutos.";
                    } else {
                        $restantes = 5 - $_SESSION['intentos_login'];
                        $mensaje_error = "Credenciales incorrectas. Le quedan {$restantes} intento(s).";
                    }
                }
            } else {
                $_SESSION['intentos_login']++;
                if ($_SESSION['intentos_login'] >= 5) {
                    $_SESSION['bloqueado_hasta'] = time() + (5 * 60);
                    $mensaje_error = "Demasiados intentos fallidos. Cuenta bloqueada por 5 minutos.";
                } else {
                    $restantes = 5 - $_SESSION['intentos_login'];
                    $mensaje_error = "Credenciales incorrectas. Le quedan {$restantes} intento(s).";
                }
            }
            $stmt->close();
        } else {
            $mensaje_error = "Error al procesar la solicitud.";
        }
    } else {
        $mensaje_error = "Por favor, complete todos los campos.";
    }
}

// Generar nueva operación matemática aleatoria para el CAPTCHA
$num1 = rand(1, 9);
$num2 = rand(1, 9);
$_SESSION['captcha_resultado'] = $num1 + $num2;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Iniciar Sesión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="recursos/css/estilos.css">
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

            </div>

        </div>
    </div>

<header class="bg-white py-4 border-bottom">
    <div class="container text-center">
        <img src="https://www.hc.edu.uy/imgnoticias/202609/1201.jpg" width="400" alt="Hospital de Clínicas" class="logo">
        <h1 class="display-5 mt-3">Iniciar Sesión</h1>
        <p class="lead">Acceso exclusivo para usuarios registrados.</p>
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
                    <h3 class="card-title mb-4 text-center">Ingreso de usuarios</h3>

                    <div id="js-error-message" class="alert alert-danger" style="display: none;"></div>

                    <?php if (!empty($mensaje_error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje_error); ?></div>
                    <?php endif; ?>

                    <form id="loginForm" action="login.php" method="POST">
                        
                        <!-- TOKEN CSRF DE SEGURIDAD -->
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <div class="mb-4">
                            <label for="usuario" class="form-label">Usuario o Cédula</label>
                            <input type="text" id="usuario" name="usuario" class="form-control form-control-lg" placeholder="Ingrese su usuario" <?php if (isset($_SESSION['bloqueado_hasta']) && time() < $_SESSION['bloqueado_hasta']) echo 'disabled'; ?>>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Ingrese su contraseña" <?php if (isset($_SESSION['bloqueado_hasta']) && time() < $_SESSION['bloqueado_hasta']) echo 'disabled'; ?>>
                        </div>

                        <!-- CAPTCHA MATEMÁTICO LOCAL -->
                        <div class="mb-4 p-3 bg-light border rounded">
                            <label for="captcha_respuesta" class="form-label fw-bold text-secondary mb-1">
                                 Verificación de seguridad (CAPTCHA)
                            </label>
                            <p class="small text-muted mb-2">Resuelva la suma para confirmar que no es un robot:</p>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white fw-bold text-primary">
                                    ¿Cuánto es <?php echo "{$num1} + {$num2}"; ?>? =
                                </span>
                                <input type="number" id="captcha_respuesta" name="captcha_respuesta" class="form-control" placeholder="Resultado" required <?php if (isset($_SESSION['bloqueado_hasta']) && time() < $_SESSION['bloqueado_hasta']) echo 'disabled'; ?>>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button class="btn btn-primary btn-lg" type="submit" <?php if (isset($_SESSION['bloqueado_hasta']) && time() < $_SESSION['bloqueado_hasta']) echo 'disabled'; ?>>Ingresar</button>
                        </div>
                    </form>
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
    const form = document.getElementById('loginForm');
    const usuarioInput = document.getElementById('usuario');
    const passwordInput = document.getElementById('password');
    const captchaInput = document.getElementById('captcha_respuesta');
    const jsErrorDiv = document.getElementById('js-error-message');

    if (form) {
        form.addEventListener('submit', (e) => {
            let errores = [];
            jsErrorDiv.style.display = 'none';
            jsErrorDiv.textContent = '';

            if (usuarioInput.value.trim() === '') {
                errores.push('El campo de usuario es obligatorio.');
            }

            if (passwordInput.value.trim() === '') {
                errores.push('El campo de contraseña es obligatorio.');
            }

            if (captchaInput && captchaInput.value.trim() === '') {
                errores.push('Debe responder la prueba de verificación (CAPTCHA).');
            }

            if (errores.length > 0) {
                e.preventDefault();
                jsErrorDiv.textContent = errores.join(' ');
                jsErrorDiv.style.display = 'block';
            }
        });
    }
});
</script>

</body>
</html>