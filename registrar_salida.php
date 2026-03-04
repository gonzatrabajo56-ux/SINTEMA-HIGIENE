<?php
require_once 'auth_check.php';
require_once 'config/db.php';
require_once 'models/Lote.php';
require_once 'controllers/LoteController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = Database::getConnection();
        $controller = new LoteController($pdo);
        $controller->registerSalida($_POST);
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        if (str_contains($e->getMessage(), 'Stock insuficiente')) {
            header("Location: index.php?error=stock_insuficiente");
        } else {
            die("Error crítico en el proceso de salida.");
        }
        exit();
    }
}