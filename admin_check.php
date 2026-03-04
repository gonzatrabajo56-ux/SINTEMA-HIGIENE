<?php
// admin_check.php
require_once 'auth_check.php';

if ($_SESSION['usuario_rol'] !== 'admin') {
    // Si no es admin, lo mandamos al index con un mensaje de error
    header("Location: index.php?error=acceso_denegado");
    exit();
}