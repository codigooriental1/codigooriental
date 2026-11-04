<?php
/**
 * =========================================================================
 * S.I.G.S.M. - CIERRE DE SESIÓN / CERRAR SESIÓN
 * =========================================================================
 * Este script procesa el cierre seguro de la sesión activa del usuario,
 * limpiando todas las variables guardadas en $_SESSION, destruyendo la
 * sesión del servidor y redirigiendo a la página de inicio principal.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

session_unset();
session_destroy();

header("Location: index.php");
exit();
?>