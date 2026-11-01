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
    <title>S.I.G.S.M. | Inicio</title>

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
                <?php endif; ?>
            </div>

        </div>
    </div>

<!-- BANNER CON IMAGEN LOCAL FIJA -->
    <header class="py-5 border-bottom shadow-sm" style="background: linear-gradient(rgba(13, 45, 78, 0.75), rgba(13, 45, 78, 0.75)), url('https://enperspectiva.uy/wp-content/uploads/2022/11/Hospital-de-Clinicas.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container text-center py-3">
            <img 
                src="https://www.hc.edu.uy/imgnoticias/202609/1201.jpg"
                width="450"
                alt="Hospital de Clínicas"
                class="logo bg-white p-3 rounded-4 shadow-sm mb-3">
            <p class="text-white-50 lead fw-bold mb-0" style="letter-spacing: 1px;">Sistema de Gestión y Servicios Médicos</p>
        </div>
    </header>
    <main class="container my-5">

        <section class="text-center mb-5">
            <h2 class="mb-3 text-primary fw-bold">Bienvenido</h2>
            <p class="fs-5 text-muted">Elegí la opción que necesitás.</p>
        </section>

        <section class="row justify-content-center g-4">

            <div class="col-12 col-md-8 col-lg-6">
                <div class="card tarjeta shadow-sm h-100 border-0 rounded-4">
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h3 class="card-title fw-bold text-dark">Documentos</h3>
                            <p class="card-text text-muted">Indicaciones médicas e información importante para el paciente.</p>
                        </div>
                        <a href="documentos.php" class="btn btn-info btn-lg px-5 py-3 w-100 text-white fw-bold shadow-sm mt-3">
                            Entrar
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-8 col-lg-6">
                <div class="card tarjeta shadow-sm h-100 border-0 rounded-4">
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h3 class="card-title fw-bold text-dark">Encuestas</h3>
                            <p class="card-text text-muted">Colaborar para mejorar la atención.</p>
                        </div>
                        <a href="encuestas.php" class="btn btn-info btn-lg px-5 py-3 w-100 text-white fw-bold shadow-sm mt-3">
                            Entrar
                        </a>
                    </div>
                </div>
            </div>

        </section>

    </main>

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

    <?php if (!isset($_SESSION['usuario_id'])): ?>
    <div class="container my-4">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-info text-white btn-lg px-4 py-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAcceso">
                 Acceso Pacientes
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="modal fade" id="modalAcceso" tabindex="-1" aria-labelledby="modalAccesoLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="modalAccesoLabel">Portal de Acceso - S.I.G.S.M.</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body text-center p-4">
            <p class="text-muted mb-4">Seleccione una opción para continuar con su gestión en el sistema:</p>
            
            <div class="d-grid gap-3 col-10 mx-auto">
              <a href="login.php" class="btn btn-outline-info text-dark btn-lg py-3 fw-bold">
                 Ya tengo cuenta (Iniciar Sesión)
              </a>
              
              <a href="registro.php" class="btn btn-info text-white btn-lg py-3 fw-bold">
                 No tengo cuenta (Registrarse)
              </a>
            </div>
          </div>

          <div class="modal-footer justify-content-center bg-light">
            <small class="text-muted">Hospital de Clínicas - Dr. Manuel Quintela</small>
          </div>

        </div>
      </div>
    </div>

    <footer class="text-center py-4 text-white m-0" style="background-color: #131246;">
        <p class="mb-0">
            S.I.G.S.M. - Hospital de Clínicas
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>