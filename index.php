<?php
// index.php - Punto de entrada principal corregido
session_start();

// 1. SEGURIDAD: Si no existe sesión y no estamos en el login, redirigir
$page = $_GET['page'] ?? null;
if (!isset($_SESSION['usuario_id']) && $page !== 'login') {
    header("Location: login.php");
    exit();
}

// Permitir renderizar el login desde el router (sin layout)
if ($page === 'login') {
    include 'views/login.php';
    exit();
}

// Cargar configuración y conexión a la base de datos
require_once 'config/db.php';

// Cargar modelos base
require_once 'models/Lote.php';
require_once 'models/BienNoPerecedero.php';
require_once 'models/Usuario.php';
require_once 'models/MovimientoNoPerecedero.php';
require_once 'models/Movimiento.php';

// Obtener la conexión PDO
try {
    $pdo = Database::getConnection();
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// 3. Enrutador mejorado
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

ob_start();

try {
    switch ($page) {
        case 'dashboard':
            require_once 'controllers/DashboardController.php';
            $controller = new DashboardController($pdo);
            $data = $controller->index();
            include 'views/dashboard.php';
            break;

        case 'bienes':
            require_once 'controllers/BienNoPerecederoController.php';
            $controller = new BienNoPerecederoController($pdo);
            
            if ($action === 'agregar') {
                include 'views/bienes/agregar.php';
            } elseif ($action === 'store') {
                // ¡ESTO ES LO QUE FALTABA PARA GUARDAR!
                $controller->store(); 
            } elseif ($action === 'movimiento') {
                // Cargar datos del bien para el formulario de movimiento
                $bienModel = new BienNoPerecedero($pdo);
                $data['bien'] = $bienModel->getById((int)$_GET['id']);
                include 'views/bienes/movimientos.php';
            } elseif ($action === 'storeMovimiento') {
                // Procesar el guardado del movimiento
                $controller->storeMovimiento();
            } else {
                $data = $controller->listar(); 
                include 'views/bienes/index.php';
            }
            break;

        case 'lotes':
            require_once 'controllers/LoteController.php';
            $controller = new LoteController($pdo);
            
            if ($action === 'entrada') {
                include 'views/lotes/entrada.php';
            } elseif ($action === 'store') {
                // Acción para guardar el nuevo lote
                $controller->store();
            } elseif ($action === 'salida') {
                include 'views/lotes/salida.php';
            } else {
                // Aquí se cargan los consumibles
                $data = $controller->index();
                include 'views/lotes/index.php';
            }
            break;
            
        case 'reportes':
            require_once 'controllers/ReporteController.php';
            require_once 'controllers/ReporteNoPerecederoController.php';
            
            if ($action === 'consumibles') {
                $c = new ReporteController($pdo);
                $data = $c->filter($_GET);
                include 'views/reportes/consumibles.php';
            } elseif ($action === 'activos') {
                $c = new ReporteNoPerecederoController($pdo);
                $data = $c->movimientos($_GET); 
                include 'views/reportes/activos.php';
            } else {
                include 'views/reportes/index.php';
            }
            break;

        default:
            echo "<h1>404 - Página no encontrada</h1>";
            break;
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}

$content = ob_get_clean();
include 'views/layout.php';