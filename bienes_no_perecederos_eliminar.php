<?php
require_once 'admin_check.php';
require_once 'config/db.php';
require_once 'models/BienNoPerecedero.php';
require_once 'controllers/BienNoPerecederoController.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: bienes_no_perecederos.php");
    exit();
}

try {
    $pdo = Database::getConnection();
    $controller = new BienNoPerecederoController($pdo);
    $controller->delete((int)$id);
} catch (Exception $e) {
    error_log("Error al eliminar bien: " . $e->getMessage());
    header("Location: bienes_no_perecederos.php?error=delete_failed");
    exit();
}
?>