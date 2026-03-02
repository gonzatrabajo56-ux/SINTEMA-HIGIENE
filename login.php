<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Usuario.php';
require_once __DIR__ . '/controllers/AuthController.php';

try {
    $pdo = Database::getConnection();
} catch (Exception $e) {
    http_response_code(500);
    die("Error de conexión: " . $e->getMessage());
}

$controller = new AuthController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login($_POST);
    exit();
}

include __DIR__ . '/views/login.php';

