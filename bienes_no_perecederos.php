<?php
require_once 'auth_check.php';
require_once 'config/db.php';
require_once 'models/BienNoPerecedero.php';
require_once 'controllers/BienNoPerecederoController.php';

ob_start();

try {
    $pdo = Database::getConnection();
    $controller = new BienNoPerecederoController($pdo);
    $data = $controller->index();
    $data['es_admin'] = ($_SESSION['usuario_rol'] ?? 'operador') === 'admin';
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Error crítico: " . $e->getMessage() . "</div>");
}

include 'views/bienes_no_perecederos.php';

$content = ob_get_clean();
include 'layout.php';
?>