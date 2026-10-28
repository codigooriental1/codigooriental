<?php
session_start();
require_once "conexion.php";

$error = "";
$nombre_usuario = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($nombre_usuario) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Por favor, complete todos los campos.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $stmt_check = $conexion->prepare("SELECT id_usuario FROM USUARIO WHERE correo = ?");
        if ($stmt_check) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $error = "El correo electrónico ya se encuentra registrado.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $id_rol_paciente = 2; // Ajustá este número según el ID del rol "Paciente" en tu tabla rol

                $stmt = $conexion->prepare("INSERT INTO USUARIO (correo, contrasena, id_rol) VALUES (?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssi", $email, $password_hash, $id_rol_paciente);
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
        <img src="https://www.hc.edu.uy/imgnoticias/202609/1201.jpg" width="400" alt="Hospital de Clínicas" class="logo img-fluid">
        <h1 class="display-5 mt-3">S.I.G.S.M.</h1>
        <p class="lead">Sistema de Información e Indicaciones Médicas</p>
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
                    
                    <div class="mb-3">
                        <label for="nombre_usuario" class="form-label fw-bold">Nombre de usuario</label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="nombre_usuario" 
                               name="nombre_usuario" 
                               placeholder="Ej: jperez" 
                               value="<?php echo htmlspecialchars($nombre_usuario); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" 
                               class="form-control form-control-lg" 
                               id="email" 
                               name="email" 
                               placeholder="ejemplo@hc.edu.uy" 
                               value="<?php echo htmlspecialchars($email); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Contraseña</label>
                        <input type="password" 
                               class="form-control form-control-lg" 
                               id="password" 
                               name="password" 
                               placeholder="Mínimo 8 caracteres">
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-bold">Confirmar Contraseña</label>
                        <input type="password" 
                               class="form-control form-control-lg" 
                               id="confirm_password" 
                               name="confirm_password" 
                               placeholder="Repita la contraseña">
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

<footer class="text-center py-4">
    <p class="mb-0">S.I.G.S.M. - Hospital de Clínicas</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registroForm');
    const nombreInput = document.getElementById('nombre_usuario');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const jsErrorDiv = document.getElementById('js-error-message');

    form.addEventListener('submit', (e) => {
        let errores = [];
        jsErrorDiv.style.display = 'none';
        jsErrorDiv.textContent = '';

        if (nombreInput.value.trim() === '') {
            errores.push('El nombre de usuario es obligatorio.');
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