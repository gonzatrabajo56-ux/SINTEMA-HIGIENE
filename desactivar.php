<?php
require_once 'admin_check.php';
require_once 'config/db.php';
require_once 'models/Lote.php';
require_once 'controllers/LoteController.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php?error=lote_no_encontrado");
    exit();
}

try {
    $pdo = Database::getConnection();
    $controller = new LoteController($pdo);
    $controller->deactivate((int)$id);
} catch (Exception $e) {
    error_log("Error al desactivar lote: " . $e->getMessage());
    header("Location: index.php?error=lote_no_encontrado");
    exit();
}
