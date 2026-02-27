<?php
// auth_check.php
// Verificar si la sesión ya fue iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay un usuario en la sesión, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>