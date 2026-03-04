<?php
require_once 'auth_check.php';
require_once 'config/db.php';
require_once 'models/Lote.php';
require_once 'controllers/LoteController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = Database::getConnection();
        $controller = new LoteController($pdo);
        $controller->store($_POST);
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        die("Error al guardar el lote.");
    }
}