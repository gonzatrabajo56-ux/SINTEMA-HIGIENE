<?php 
require_once 'auth_check.php'; 
require_once 'db.php';

ob_start(); 

// 1. Capturar parámetros de filtrado
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_busqueda = $_GET['buscar'] ?? '';
$fecha_desde = $_GET['desde'] ?? '';
$fecha_hasta = $_GET['hasta'] ?? '';

// Función para resaltar coincidencias
function resaltar($texto, $busqueda) {
    if (empty($busqueda)) return htmlspecialchars($texto);
    return preg_replace('/(' . preg_quote($busqueda, '/') . ')/i', '<mark class="p-0 bg-warning text-dark">$1</mark>', htmlspecialchars($texto));
}

try {
    $pdo = Database::getConnection();
    
    $sql = "SELECT m.*, l.nombre_producto, l.unidad 
            FROM movimientos m 
            JOIN lotes l ON m.lote_id = l.id 
            WHERE 1=1";

    $params = [];

    if (!empty($filtro_tipo)) {
        $sql .= " AND m.tipo_movimiento = :tipo";
        $params[':tipo'] = $filtro_tipo;
    }

    if (!empty($filtro_busqueda)) {
        $sql .= " AND (l.nombre_producto LIKE :buscar OR m.responsable LIKE :buscar OR m.area_destino LIKE :buscar)";
        $params[':buscar'] = "%$filtro_busqueda%";
    }

    if (!empty($fecha_desde)) {
        $sql .= " AND DATE(m.fecha_movimiento) >= :desde";
        $params[':desde'] = $fecha_desde;
    }

    if (!empty($fecha_hasta)) {
        $sql .= " AND DATE(m.fecha_movimiento) <= :hasta";
        $params[':hasta'] = $fecha_hasta;
    }

    $sql .= " ORDER BY m.fecha_movimiento DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $movimientos = $stmt->fetchAll();

} catch (Exception $e) {
    die("<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>");
}
?>

<style>
    /* Estilos para optimizar la impresión del reporte */
    @media print {
        .navbar, .btn, .card-header form, .no-print { display: none !important; }
        body { background-color: white !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .main-container { padding-top: 0 !important; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .table th, .table td { font-size: 10pt !important; border: 1px solid #eee !important; }
    }
    .hover-shadow:hover { transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; }
</style>

<div class="card shadow-sm border-0 mb-4 no-print">
    <div class="card-body p-4">
        <h6 class="text-muted fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Herramientas de Búsqueda</h6>
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Producto o Responsable</label>
                <input type="text" name="buscar" class="form-control" placeholder="Buscar..." value="<?= htmlspecialchars($filtro_busqueda) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Operación</label>
                <select name="tipo" class="form-select">
                    <option value="">Todas</option>
                    <option value="entrada" <?= $filtro_tipo == 'entrada' ? 'selected' : '' ?>>Entradas</option>
                    <option value="salida" <?= $filtro_tipo == 'salida' ? 'selected' : '' ?>>Salidas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= $fecha_desde ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= $fecha_hasta ?>">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                <a href="historial.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 hover-shadow">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold text-dark">Movimientos Recientes</h5>
            <small class="text-muted"><?= count($movimientos) ?> operaciones registradas</small>
        </div>
        <div class="no-print">
            <button class="btn btn-success btn-sm me-2" onclick="window.print()">
                <i class="bi bi-file-earmark-pdf me-1"></i> Generar Reporte
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Fecha</th>
                    <th>Detalles del Lote</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-end">Cantidad</th>
                    <th class="ps-4">Ubicación / Responsable</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $m): 
                    $esEntrada = ($m['tipo_movimiento'] == 'entrada');
                ?>
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold"><?= date('d/m/Y', strtotime($m['fecha_movimiento'])) ?></div>
                        <div class="text-muted small"><?= date('h:i A', strtotime($m['fecha_movimiento'])) ?></div>
                    </td>
                    <td>
                        <div class="text-primary fw-bold"><?= resaltar($m['nombre_producto'], $filtro_busqueda) ?></div>
                        <span class="badge bg-light text-dark border fw-normal">Lote #<?= $m['lote_id'] ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill <?= $esEntrada ? 'bg-success' : 'bg-danger' ?> px-3" style="min-width: 85px;">
                            <?= $esEntrada ? 'ENTRADA' : 'SALIDA' ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <span class="fs-6 fw-bold <?= $esEntrada ? 'text-success' : 'text-danger' ?>">
                            <?= $esEntrada ? '+' : '-' ?> <?= number_format($m['cantidad_retirada'], 2) ?>
                        </span>
                        <small class="text-muted"><?= $m['unidad'] ?></small>
                    </td>
                    <td class="ps-4">
                        <div class="fw-bold"><i class="bi bi-geo-alt me-1 text-muted"></i><?= resaltar($m['area_destino'], $filtro_busqueda) ?></div>
                        <div class="small text-muted"><i class="bi bi-person-check me-1"></i><?= resaltar($m['responsable'], $filtro_busqueda) ?></div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($movimientos)): ?>
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-folder-x display-1 d-block mb-3 opacity-25"></i>
                        No se encontraron movimientos con los filtros seleccionados.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
include 'layout.php'; 
?>