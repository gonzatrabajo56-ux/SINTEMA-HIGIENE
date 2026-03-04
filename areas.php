<?php 
require_once 'admin_check.php';
require_once 'config/db.php';
require_once 'models/Area.php';
require_once 'controllers/AreaController.php';

ob_start();
$mensaje = "";

try {
    $pdo = Database::getConnection();
    $controller = new AreaController($pdo);
    
    // Procesar eliminación
    if (isset($_GET['eliminar'])) {
        try {
            $controller->delete((int)$_GET['eliminar']);
            $mensaje = "<div class='alert alert-success shadow-sm'><i class='bi bi-check-circle-fill me-2'></i>Área eliminada correctamente.</div>";
        } catch (Exception $e) {
            $mensaje = "<div class='alert alert-warning shadow-sm'><i class='bi bi-exclamation-triangle-fill me-2'></i>" . $e->getMessage() . "</div>";
        }
    }
    
    // Procesar toggle activa
    if (isset($_GET['toggle'])) {
        $controller->toggle((int)$_GET['toggle']);
        header("Location: areas.php");
        exit();
    }
    
    // Procesar creación
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $mensaje = $controller->store($_POST);
    }
    
    $data = $controller->index();
    $areas = $data['areas'];

} catch (Exception $e) {
    $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    $areas = [];
}

// Lista de iconos disponibles
$iconos = [
    'bi-geo-alt' => 'Ubicación',
    'bi-house' => 'Casa',
    'bi-building' => 'Edificio',
    'bi-door-open' => 'Puerta',
    'bi-cup-hot' => 'Cocina',
    'bi-droplet' => 'Baño/Agua',
    'bi-basket' => 'Lavandería',
    'bi-tools' => 'Mantenimiento',
    'bi-heart-pulse' => 'Enfermería',
    'bi-book' => 'Oficina/Archivo',
    'bi-people' => 'Área común',
    'bi-car-front' => 'Estacionamiento',
    'bi-tree' => 'Jardín/Exterior',
    'bi-box-seam' => 'Almacén',
];
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Gestión de Áreas</li>
            </ol>
        </nav>

        <?= $mensaje ?>

        <div class="row g-4">
            <!-- Formulario de creación -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 80px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-plus-circle me-2"></i>Nueva Área
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Nombre del Área</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Cocina, Baños, etc." required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Descripción (opcional)</label>
                                <textarea name="descripcion" class="form-control" rows="2" placeholder="Breve descripción del área..."></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">Icono</label>
                                <select name="icono" class="form-select">
                                    <?php foreach($iconos as $clase => $nombre): ?>
                                        <option value="<?= $clase ?>"><?= $nombre ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">El icono se mostrará junto al nombre del área.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="bi bi-save me-1"></i> Guardar Área
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Lista de áreas -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-list-ul me-2"></i>Áreas Registradas
                            </h5>
                            <small class="text-muted"><?= count($areas) ?> áreas configuradas</small>
                        </div>
                    </div>
                    
                    <?php if (empty($areas)): ?>
                        <div class="card-body text-center py-5">
                            <i class="bi bi-geo-alt display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">No hay áreas registradas.<br>Crea la primera usando el formulario.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Área</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Movimientos</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($areas as $area): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi <?= htmlspecialchars($area['icono'] ?? 'bi-geo-alt') ?> text-primary"></i>
                                                </div>
                                                <span class="fw-bold"><?= htmlspecialchars($area['nombre']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-muted small"><?= htmlspecialchars($area['descripcion'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary rounded-pill px-3"><?= $area['total_movimientos'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($area['activa'] ?? 1): ?>
                                                <span class="badge bg-success">Activa</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactiva</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <?php if ($area['total_movimientos'] == 0): ?>
                                                <a href="?eliminar=<?= $area['id'] ?>" 
                                                   class="btn btn-outline-danger btn-sm"
                                                   onclick="return confirm('¿Eliminar el área <?= htmlspecialchars($area['nombre']) ?>?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary btn-sm" disabled title="No se puede eliminar: tiene movimientos asociados">
                                                    <i class="bi bi-lock"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Información adicional -->
                <div class="alert alert-info border-0 mt-4 shadow-sm">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                        <div>
                            <strong>¿Cómo funcionan las áreas?</strong>
                            <p class="mb-0 small mt-1">
                                Las áreas que crees aquí aparecerán en el formulario de <strong>Registrar Salida</strong> 
                                como opciones de destino. Esto te permite organizar y filtrar los reportes por ubicación.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
include 'layout.php'; 
?>
