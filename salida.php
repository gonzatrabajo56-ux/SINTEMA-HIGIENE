<?php 
require_once 'auth_check.php';
require_once 'config/db.php';
require_once 'models/Lote.php';
require_once 'models/Area.php';
require_once 'controllers/LoteController.php';
require_once 'controllers/AreaController.php';

try {
    $pdo = Database::getConnection();
    $loteController = new LoteController($pdo);
    $areaController = new AreaController($pdo);
    
    $lotes = $loteController->getForSalida();
    $areas = $areaController->getActive();
    
    $data = ['lotes' => $lotes, 'areas' => $areas];
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Error al cargar productos: " . $e->getMessage() . "</div>");
}

ob_start(); 
?>

<?php include 'views/salida.php'; ?>

<?php 
$content = ob_get_clean(); 
include 'layout.php'; 
?>