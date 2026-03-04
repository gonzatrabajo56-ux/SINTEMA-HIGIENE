<?php
// 1. Forzar al navegador a no guardar copia de la página en el historial
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Si no hay sesión, expulsar al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}