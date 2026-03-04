<?php
// index.php - Punto de entrada principal
session_start();

// Cargar helpers
require_once 'helpers/RoleHelper.php';
require_once 'helpers/CsrfHelper.php';

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
    // En producción, solo mostrar mensaje genérico y loguear el error
    error_log("Error de conexión: " . $e->getMessage());
    die("Error interno del sistema. Por favor, contacte al administrador.");
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
            require_once 'controllers/AreaController.php';
            $controller = new BienNoPerecederoController($pdo);
            $areaController = new AreaController($pdo);
            $data['areas'] = $areaController->getActive();
            
            if ($action === 'agregar') {
                include 'views/bienes/agregar.php';
            } elseif ($action === 'store') {
                $controller->store(); 
            } elseif ($action === 'movimiento') {
                // Cargar datos del bien para el formulario de movimiento
                $bienModel = new BienNoPerecedero($pdo);
                $data['bien'] = $bienModel->getById((int)$_GET['id']);
                include 'views/bienes/movimientos.php';
            } elseif ($action === 'storeMovimiento') {
                // Procesar el guardado del movimiento
                $controller->storeMovimiento();
            } elseif ($action === 'edit') {
                // Cargar datos del bien para edición
                $bienModel = new BienNoPerecedero($pdo);
                $data['bien'] = $bienModel->getById((int)$_GET['id']);
                include 'views/bienes/editar.php';
            } elseif ($action === 'update') {
                // Procesar actualización del bien
                $controller->update((int)$_POST['id'], $_POST);
            } else {
                $data = $controller->listar(); 
                include 'views/bienes/index.php';
            }
            break;

        case 'lotes':
            require_once 'controllers/LoteController.php';
            $controller = new LoteController($pdo);
            
            if ($action === 'entrada') {
                // Cargar áreas para el formulario
                require_once 'controllers/AreaController.php';
                $areaController = new AreaController($pdo);
                $data['areas'] = $areaController->getActive();
                include 'views/lotes/entrada.php';
            } elseif ($action === 'store') {
                // Acción para guardar el nuevo lote
                $controller->store();
            } elseif ($action === 'salida') {
                require_once 'controllers/AreaController.php';
                $areaController = new AreaController($pdo);
                $data['areas'] = $areaController->getActive();
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
            require_once 'controllers/ReporteAvanzadoController.php';
            $reporteAvanzado = new ReporteAvanzadoController($pdo);
            
            if ($action === 'consumibles') {
                $c = new ReporteController($pdo);
                $data = $c->filter($_GET);
                include 'views/reportes/consumibles.php';
            } elseif ($action === 'activos') {
                $c = new ReporteNoPerecederoController($pdo);
                $data = $c->movimientos($_GET); 
                include 'views/reportes/activos.php';
            } elseif ($action === 'consumibles_avanzado') {
                $data = $reporteAvanzado->consumibles($_GET);
                include 'views/reportes/consumibles_avanzado.php';
            } elseif ($action === 'activos_avanzado') {
                $data = $reporteAvanzado->activos($_GET);
                include 'views/reportes/activos_avanzado.php';
            } elseif ($action === 'exportar_csv') {
                $reporteAvanzado->exportarCsv($_GET['tipo'] ?? 'consumibles', $_GET);
            } elseif ($action === 'pdf_consumibles') {
                $reporteAvanzado->generarPdfConsumibles($_GET);
            } elseif ($action === 'pdf_activos') {
                $reporteAvanzado->generarPdfActivos($_GET);
            } else {
                $data = $reporteAvanzado->index();
                include 'views/reportes/dashboard.php';
            }
            break;
        
        case 'areas':
            require_once 'controllers/AreaController.php';
            $controller = new AreaController($pdo);
            
            if ($action === 'store') {
                $data['mensaje'] = $controller->store($_POST);
                $data['areas'] = $controller->getAll();
                include 'views/areas/index.php';
            } elseif ($action === 'update') {
                $data['mensaje'] = $controller->update($_POST);
                $data['areas'] = $controller->getAll();
                include 'views/areas/index.php';
            } elseif ($action === 'toggle') {
                $controller->toggle($_POST);
            } elseif ($action === 'delete') {
                $data['mensaje'] = $controller->delete($_POST);
                $data['areas'] = $controller->getAll();
                include 'views/areas/index.php';
            } elseif ($action === 'edit') {
                $data['area'] = $controller->getById((int)$_GET['id']);
                $data['areas'] = $controller->getAll();
                include 'views/areas/editar.php';
            } else {
                $data = $controller->index();
                include 'views/areas/index.php';
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