<?php

require_once 'config/db.php';
require_once 'models/Usuario.php';
require_once 'controllers/AuthController.php';
require_once 'helpers/CsrfHelper.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!CsrfHelper::validateToken($csrfToken)) {
        die("Token CSRF inválido. Por favor, intente nuevamente.");
    }
    
    try {
        $pdo = Database::getConnection();
        $controller = new AuthController($pdo);
        $controller->login($_POST);
    } catch (Exception $e) {
        die("Error en el sistema: " . $e->getMessage());
    }
} else {
    header("Location: login.php");
    exit();
}
