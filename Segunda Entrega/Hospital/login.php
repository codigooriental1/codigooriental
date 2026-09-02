<?php
session_start();
require_once "conexion.php";

$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($usuario) && !empty($password)) {
        $stmt = $conexion->prepare("SELECT id_usuario, contrasena FROM USUARIO WHERE correo = ?");
        
        if ($stmt) {
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado && $resultado->num_rows === 1) {
                $datos_usuario = $resultado->fetch_assoc();

                if (password_verify($password, $datos_usuario['contrasena']) || $password === $datos_usuario['contrasena']) {
                    $_SESSION['usuario_id'] = $datos_usuario['id_usuario'];
                    $_SESSION['correo'] = $usuario;

                    header("Location: index.html");
                    exit();
                } else {
                    $mensaje_error = "Credenciales incorrectas.";
                }
            } else {
                $mensaje_error = "Credenciales incorrectas.";
            }
            $stmt->close();
        } else {
            $mensaje_error = "Error al procesar la solicitud.";
        }
    } else {
        $mensaje_error = "Por favor, complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.S.M. | Iniciar Sesión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<div class="bg-light border-bottom py-2">
    <div class="container">
        <small class="text-muted">
            📞 Atención al Usuario: <strong>1953 / 0800 1953</strong>
            <span class="mx-3">|</span>
            📧 atencionalusuario@hc.edu.uy
        </small>
    </div>
</div>

<header class="bg-white py-4 border-bottom">
    <div class="container text-center">
        <img src="https://hc.edu.uy/images/imagenesarticulos/Logo_Hc.png" width="400" alt="Hospital de Clínicas" class="logo">
        <h1 class="display-5 mt-3">Iniciar Sesión</h1>
        <p class="lead">Acceso exclusivo para el personal del hospital.</p>
    </div>
</header>

<main class="container my-5">

    <div class="mb-4">
        <a href="index.html" class="btn btn-outline-primary btn-lg px-5 py-3">
            ← Volver al inicio
        </a>
    </div>

    <section class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card tarjeta shadow-sm">
                <div class="card-body p-5">
                    <h3 class="card-title mb-4 text-center">Ingreso de Funcionarios</h3>

                    <div id="js-error-message" class="alert alert-danger" style="display: none;"></div>

                    <?php if (!empty($mensaje_error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje_error); ?></div>
                    <?php endif; ?>

                    <form id="loginForm" action="login.php" method="POST">
                        <div class="mb-4">
                            <label for="usuario" class="form-label">Usuario o Cédula</label>
                            <input type="text" id="usuario" name="usuario" class="form-control form-control-lg" placeholder="Ingrese su usuario">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Ingrese su contraseña">
                        </div>

                        <div class="d-grid mt-4">
                            <button class="btn btn-primary btn-lg" type="submit">Ingresar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<footer class="text-center py-4">
    <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');
    const usuarioInput = document.getElementById('usuario');
    const passwordInput = document.getElementById('password');
    const jsErrorDiv = document.getElementById('js-error-message');

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