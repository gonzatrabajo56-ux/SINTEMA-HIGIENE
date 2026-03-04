<?php 
require_once 'auth_check.php'; 
require_once 'config/db.php';
require_once 'models/BienNoPerecedero.php';
require_once 'models/MovimientoNoPerecedero.php';
require_once 'controllers/ReporteNoPerecederoController.php';

ob_start(); 

// Capturar parámetros de filtrado
$tipo_reporte = $_GET['tipo'] ?? 'resumen';
$fecha_desde = $_GET['desde'] ?? date('Y-m-01'); // Primer día del mes actual
$fecha_hasta = $_GET['hasta'] ?? date('Y-m-d');
$area_filtro = $_GET['area'] ?? '';

try {
    $pdo = Database::getConnection();
    
    // ===============================
    // DATOS PARA REPORTE RESUMEN GENERAL
    // ===============================
    
    // Total de productos activos
    $total_productos = $pdo->query("SELECT COUNT(*) FROM lotes WHERE estado = 'activo'")->fetchColumn();
    
    // Productos en stock crítico (< 20%)
    $bajo_stock = $pdo->query("SELECT COUNT(*) FROM lotes WHERE (cantidad_actual / cantidad_inicial) <= 0.2 AND estado = 'activo'")->fetchColumn();
    
    // Total de entradas en el período
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(cantidad_retirada), 0) FROM movimientos WHERE tipo_movimiento = 'entrada' AND DATE(fecha_movimiento) BETWEEN ? AND ?");
    $stmt->execute([$fecha_desde, $fecha_hasta]);
    $total_entradas = $stmt->fetchColumn();
    
    // Total de salidas en el período
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(cantidad_retirada), 0) FROM movimientos WHERE tipo_movimiento = 'salida' AND DATE(fecha_movimiento) BETWEEN ? AND ?");
    $stmt->execute([$fecha_desde, $fecha_hasta]);
    $total_salidas = $stmt->fetchColumn();
    
    // Número de operaciones
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE DATE(fecha_movimiento) BETWEEN ? AND ?");
    $stmt->execute([$fecha_desde, $fecha_hasta]);
    $total_operaciones = $stmt->fetchColumn();

    // ===============================
    // REPORTE: INVENTARIO ACTUAL
    // ===============================
    $inventario = $pdo->query("
        SELECT *, 
               (cantidad_actual / cantidad_inicial * 100) as porcentaje,
               CASE 
                   WHEN (cantidad_actual / cantidad_inicial) <= 0.2 THEN 'Crítico'
                   WHEN (cantidad_actual / cantidad_inicial) <= 0.5 THEN 'Bajo'
                   ELSE 'Normal'
               END as nivel_stock
        FROM lotes 
        WHERE estado = 'activo' 
        ORDER BY porcentaje ASC
    ")->fetchAll();

    // ===============================
    // REPORTE: CONSUMO POR ÁREA
    // ===============================
    $stmt = $pdo->prepare("
        SELECT 
            area_destino,
            COUNT(*) as total_movimientos,
            SUM(cantidad_retirada) as total_consumido
        FROM movimientos 
        WHERE tipo_movimiento = 'salida' 
          AND DATE(fecha_movimiento) BETWEEN ? AND ?
        GROUP BY area_destino
        ORDER BY total_consumido DESC
    ");
    $stmt->execute([$fecha_desde, $fecha_hasta]);
    $consumo_area = $stmt->fetchAll();

    // ===============================
    // REPORTE: PRODUCTOS MÁS CONSUMIDOS
    // ===============================
    $stmt = $pdo->prepare("
        SELECT 
            l.nombre_producto,
            l.unidad,
            COUNT(m.id) as veces_retirado,
            SUM(m.cantidad_retirada) as total_consumido
        FROM movimientos m
        JOIN lotes l ON m.lote_id = l.id
        WHERE m.tipo_movimiento = 'salida'
          AND DATE(m.fecha_movimiento) BETWEEN ? AND ?
        GROUP BY m.lote_id
        ORDER BY total_consumido DESC
        LIMIT 10
    ");
    $stmt->execute([$fecha_desde, $fecha_hasta]);
    $productos_consumidos = $stmt->fetchAll();

    // ===============================
    // REPORTE: MOVIMIENTOS DIARIOS (para gráfico)
    // ===============================
    $stmt = $pdo->prepare("
        SELECT 
            DATE(fecha_movimiento) as fecha,
            tipo_movimiento,
            SUM(cantidad_retirada) as total
        FROM movimientos
        WHERE DATE(fecha_movimiento) BETWEEN ? AND ?
        GROUP BY DATE(fecha_movimiento), tipo_movimiento
        ORDER BY fecha ASC
    ");
    $stmt->execute([$fecha_desde, $fecha_hasta]);
    $movimientos_diarios = $stmt->fetchAll();

    // ===============================
    // REPORTE: ACTIVIDAD POR USUARIO
    // ===============================
    $stmt = $pdo->prepare("
        SELECT 
            responsable,
            COUNT(*) as total_operaciones,
            SUM(CASE WHEN tipo_movimiento = 'entrada' THEN 1 ELSE 0 END) as entradas,
            SUM(CASE WHEN tipo_movimiento = 'salida' THEN 1 ELSE 0 END) as salidas
        FROM movimientos
        WHERE DATE(fecha_movimiento) BETWEEN ? AND ?
        GROUP BY responsable
        ORDER BY total_operaciones DESC
        LIMIT 10
    ");
    $stmt->execute([$fecha_desde, $fecha_hasta]);
    $actividad_usuarios = $stmt->fetchAll();

    // ===============================
    // REPORTE: PRODUCTOS AGOTADOS
    // ===============================
    $productos_agotados = $pdo->query("
        SELECT * FROM lotes 
        WHERE estado = 'agotado' OR cantidad_actual <= 0
        ORDER BY fecha_ingreso DESC
    ")->fetchAll();

    // ===============================
    // LISTA DE ÁREAS (para filtro)
    // ===============================
    $areas = $pdo->query("SELECT DISTINCT area_destino FROM movimientos WHERE area_destino != 'Almacén' ORDER BY area_destino")->fetchAll(PDO::FETCH_COLUMN);

    // ===============================
    // DATOS PARA NO PERECEDEROS
    // ===============================
    
    // Instanciar controlador de reportes no perecederos
    $reporteNoPerecederoController = new ReporteNoPerecederoController($pdo);
    
    // Obtener indicadores de no perecederos
    $indicadores_no_perecederos = $reporteNoPerecederoController->getIndicadores($fecha_desde, $fecha_hasta);
    
    // Obtener movimientos de no perecederos para el período
    $movimientos_no_perecederos = $reporteNoPerecederoController->getMovimientosPorPeriodo($fecha_desde, $fecha_hasta);
    
    // Obtener bienes no perecederos por estado
    $bienes_por_estado = $reporteNoPerecederoController->getBienesPorEstado();
    
    // Obtener actividad por usuario para no perecederos
    $actividad_usuarios_no_perecederos = $reporteNoPerecederoController->getActividadUsuarios($fecha_desde, $fecha_hasta);

} catch (Exception $e) {
    die("<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>");
}
?>

<style>
    @media print {
        .navbar, .no-print, .card-header form { display: none !important; }
        body { background-color: white !important; font-size: 11pt; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; page-break-inside: avoid; }
        .main-container { padding: 0 !important; }
        .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
        .table { font-size: 10pt; }
    }
    .print-header { display: none; }
    .stat-card { transition: transform 0.2s; cursor: pointer; }
    .stat-card:hover { transform: translateY(-3px); }
    .progress-thin { height: 6px; border-radius: 3px; }
    .table-report th { background-color: #f8f9fa; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-stock-critico { background-color: #dc3545; }
    .badge-stock-bajo { background-color: #ffc107; color: #000; }
    .badge-stock-normal { background-color: #198754; }
    .nav-pills .nav-link.active { background-color: #0d6efd; }
    .nav-pills .nav-link { color: #495057; }
</style>

<!-- Encabezado para impresión -->
<div class="print-header">
    <h3 class="fw-bold">Sistema de Gestión - Fundación Castillo San Antonio</h3>
    <p class="text-muted">Reporte generado el <?= date('d/m/Y H:i') ?></p>
    <hr>
</div>

<!-- Filtros de fecha -->
<div class="card shadow-sm border-0 mb-4 no-print">
    <div class="card-body p-4">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_reporte) ?>">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Fecha Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= $fecha_desde ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Fecha Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= $fecha_hasta ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Área (opcional)</label>
                <select name="area" class="form-select">
                    <option value="">Todas las áreas</option>
                    <?php foreach($areas as $area): ?>
                        <option value="<?= htmlspecialchars($area) ?>" <?= $area_filtro == $area ? 'selected' : '' ?>><?= htmlspecialchars($area) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Aplicar Filtros
                </button>
                <button type="button" class="btn btn-success" onclick="window.print()">
                    <i class="bi bi-printer"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Navegación de reportes -->
<ul class="nav nav-pills mb-4 no-print">
    <li class="nav-item">
        <a class="nav-link <?= $tipo_reporte == 'resumen' ? 'active' : '' ?>" href="?tipo=resumen&desde=<?= $fecha_desde ?>&hasta=<?= $fecha_hasta ?>">
            <i class="bi bi-speedometer2 me-1"></i> Resumen General
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipo_reporte == 'inventario' ? 'active' : '' ?>" href="?tipo=inventario&desde=<?= $fecha_desde ?>&hasta=<?= $fecha_hasta ?>">
            <i class="bi bi-box-seam me-1"></i> Inventario
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipo_reporte == 'consumo' ? 'active' : '' ?>" href="?tipo=consumo&desde=<?= $fecha_desde ?>&hasta=<?= $fecha_hasta ?>">
            <i class="bi bi-pie-chart me-1"></i> Consumo por Área
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipo_reporte == 'productos' ? 'active' : '' ?>" href="?tipo=productos&desde=<?= $fecha_desde ?>&hasta=<?= $fecha_hasta ?>">
            <i class="bi bi-graph-up me-1"></i> Top Productos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipo_reporte == 'usuarios' ? 'active' : '' ?>" href="?tipo=usuarios&desde=<?= $fecha_desde ?>&hasta=<?= $fecha_hasta ?>">
            <i class="bi bi-people me-1"></i> Actividad Usuarios
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipo_reporte == 'agotados' ? 'active' : '' ?>" href="?tipo=agotados&desde=<?= $fecha_desde ?>&hasta=<?= $fecha_hasta ?>">
            <i class="bi bi-exclamation-triangle me-1"></i> Agotados
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipo_reporte == 'no_perecederos' ? 'active' : '' ?>" href="?tipo=no_perecederos&desde=<?= $fecha_desde ?>&hasta=<?= $fecha_hasta ?>">
            <i class="bi bi-tools me-1"></i> No Perecederos
        </a>
    </li>
</ul>

<?php if ($tipo_reporte == 'resumen'): ?>
<!-- ================================ -->
<!-- REPORTE: RESUMEN GENERAL -->
<!-- ================================ -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Productos Activos</h6>
                        <h2 class="mb-0 fw-bold"><?= $total_productos ?></h2>
                    </div>
                    <i class="bi bi-box-seam display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100 <?= $bajo_stock > 0 ? 'bg-danger' : 'bg-success' ?> text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Stock Crítico</h6>
                        <h2 class="mb-0 fw-bold"><?= $bajo_stock ?></h2>
                    </div>
                    <i class="bi bi-exclamation-triangle display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Entradas (Período)</h6>
                        <h2 class="mb-0 fw-bold"><?= number_format($total_entradas, 2) ?></h2>
                    </div>
                    <i class="bi bi-arrow-down-circle display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100 bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Salidas (Período)</h6>
                        <h2 class="mb-0 fw-bold"><?= number_format($total_salidas, 2) ?></h2>
                    </div>
                    <i class="bi bi-arrow-up-circle display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Consumo por Área (mini) -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2 text-primary"></i>Consumo por Área</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Área</th>
                                <th class="text-center">Movimientos</th>
                                <th class="text-end pe-4">Total Consumido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($consumo_area)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Sin datos en el período</td></tr>
                            <?php else: ?>
                                <?php foreach(array_slice($consumo_area, 0, 5) as $ca): ?>
                                <tr>
                                    <td class="ps-4 fw-medium"><?= htmlspecialchars($ca['area_destino']) ?></td>
                                    <td class="text-center"><span class="badge bg-secondary"><?= $ca['total_movimientos'] ?></span></td>
                                    <td class="text-end pe-4 fw-bold text-danger">-<?= number_format($ca['total_consumido'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos más consumidos (mini) -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2 text-success"></i>Productos Más Consumidos</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Producto</th>
                                <th class="text-center">Retiros</th>
                                <th class="text-end pe-4">Consumido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($productos_consumidos)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Sin datos en el período</td></tr>
                            <?php else: ?>
                                <?php foreach(array_slice($productos_consumidos, 0, 5) as $pc): ?>
                                <tr>
                                    <td class="ps-4 fw-medium"><?= htmlspecialchars($pc['nombre_producto']) ?></td>
                                    <td class="text-center"><span class="badge bg-info"><?= $pc['veces_retirado'] ?></span></td>
                                    <td class="text-end pe-4 fw-bold"><?= number_format($pc['total_consumido'], 2) ?> <?= $pc['unidad'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php elseif ($tipo_reporte == 'inventario'): ?>
<!-- ================================ -->
<!-- REPORTE: INVENTARIO COMPLETO -->
<!-- ================================ -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Inventario Actual</h5>
            <small class="text-muted"><?= count($inventario) ?> productos activos</small>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-report">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Producto</th>
                    <th class="text-center">Unidad</th>
                    <th class="text-end">Inicial</th>
                    <th class="text-end">Actual</th>
                    <th style="width: 150px;">Nivel</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end pe-4">Ingreso</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($inventario as $item): 
                    $porcentaje = $item['porcentaje'];
                    $colorBarra = $porcentaje <= 20 ? 'danger' : ($porcentaje <= 50 ? 'warning' : 'success');
                ?>
                <tr>
                    <td class="ps-4 text-muted">#<?= $item['id'] ?></td>
                    <td class="fw-bold"><?= htmlspecialchars($item['nombre_producto']) ?></td>
                    <td class="text-center"><span class="badge bg-light text-dark"><?= $item['unidad'] ?></span></td>
                    <td class="text-end"><?= number_format($item['cantidad_inicial'], 2) ?></td>
                    <td class="text-end fw-bold"><?= number_format($item['cantidad_actual'], 2) ?></td>
                    <td>
                        <div class="progress progress-thin">
                            <div class="progress-bar bg-<?= $colorBarra ?>" style="width: <?= $porcentaje ?>%"></div>
                        </div>
                        <small class="text-<?= $colorBarra ?>"><?= round($porcentaje) ?>%</small>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-stock-<?= strtolower($item['nivel_stock']) ?>"><?= $item['nivel_stock'] ?></span>
                    </td>
                    <td class="text-end pe-4 text-muted small"><?= date('d/m/Y', strtotime($item['fecha_ingreso'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($tipo_reporte == 'consumo'): ?>
<!-- ================================ -->
<!-- REPORTE: CONSUMO POR ÁREA -->
<!-- ================================ -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2 text-primary"></i>Consumo por Área de Destino</h5>
        <small class="text-muted">Período: <?= date('d/m/Y', strtotime($fecha_desde)) ?> - <?= date('d/m/Y', strtotime($fecha_hasta)) ?></small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-report">
                <tr>
                    <th class="ps-4">#</th>
                    <th>Área de Destino</th>
                    <th class="text-center">Total Movimientos</th>
                    <th class="text-end">Total Consumido</th>
                    <th style="width: 200px;" class="pe-4">Participación</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_general = array_sum(array_column($consumo_area, 'total_consumido'));
                $i = 1;
                foreach($consumo_area as $ca): 
                    $participacion = $total_general > 0 ? ($ca['total_consumido'] / $total_general * 100) : 0;
                ?>
                <tr>
                    <td class="ps-4 text-muted"><?= $i++ ?></td>
                    <td class="fw-bold">
                        <i class="bi bi-geo-alt me-2 text-primary"></i><?= htmlspecialchars($ca['area_destino']) ?>
                    </td>
                    <td class="text-center"><span class="badge bg-secondary rounded-pill px-3"><?= $ca['total_movimientos'] ?></span></td>
                    <td class="text-end fw-bold text-danger"><?= number_format($ca['total_consumido'], 2) ?></td>
                    <td class="pe-4">
                        <div class="progress progress-thin">
                            <div class="progress-bar bg-primary" style="width: <?= $participacion ?>%"></div>
                        </div>
                        <small class="text-muted"><?= round($participacion, 1) ?>%</small>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr class="fw-bold">
                    <td colspan="2" class="ps-4">TOTAL GENERAL</td>
                    <td class="text-center"><?= array_sum(array_column($consumo_area, 'total_movimientos')) ?></td>
                    <td class="text-end text-danger"><?= number_format($total_general, 2) ?></td>
                    <td class="pe-4">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php elseif ($tipo_reporte == 'productos'): ?>
<!-- ================================ -->
<!-- REPORTE: TOP PRODUCTOS -->
<!-- ================================ -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2 text-success"></i>Top 10 Productos Más Consumidos</h5>
        <small class="text-muted">Período: <?= date('d/m/Y', strtotime($fecha_desde)) ?> - <?= date('d/m/Y', strtotime($fecha_hasta)) ?></small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-report">
                <tr>
                    <th class="ps-4 text-center" style="width: 60px;">Ranking</th>
                    <th>Producto</th>
                    <th class="text-center">Unidad</th>
                    <th class="text-center">Veces Retirado</th>
                    <th class="text-end pe-4">Total Consumido</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $ranking = 1;
                foreach($productos_consumidos as $pc): 
                    $medalla = '';
                    if ($ranking == 1) $medalla = '🥇';
                    elseif ($ranking == 2) $medalla = '🥈';
                    elseif ($ranking == 3) $medalla = '🥉';
                ?>
                <tr>
                    <td class="ps-4 text-center">
                        <?php if ($medalla): ?>
                            <span style="font-size: 1.5rem;"><?= $medalla ?></span>
                        <?php else: ?>
                            <span class="badge bg-light text-dark"><?= $ranking ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="fw-bold"><?= htmlspecialchars($pc['nombre_producto']) ?></td>
                    <td class="text-center"><span class="badge bg-secondary"><?= $pc['unidad'] ?></span></td>
                    <td class="text-center"><span class="badge bg-info rounded-pill px-3"><?= $pc['veces_retirado'] ?> retiros</span></td>
                    <td class="text-end pe-4">
                        <span class="fs-5 fw-bold text-primary"><?= number_format($pc['total_consumido'], 2) ?></span>
                        <small class="text-muted"><?= $pc['unidad'] ?></small>
                    </td>
                </tr>
                <?php $ranking++; endforeach; ?>
                
                <?php if (empty($productos_consumidos)): ?>
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-1 d-block mb-3 opacity-25"></i>
                        No hay consumos registrados en este período.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($tipo_reporte == 'usuarios'): ?>
<!-- ================================ -->
<!-- REPORTE: ACTIVIDAD POR USUARIO -->
<!-- ================================ -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-info"></i>Actividad por Usuario/Responsable</h5>
        <small class="text-muted">Período: <?= date('d/m/Y', strtotime($fecha_desde)) ?> - <?= date('d/m/Y', strtotime($fecha_hasta)) ?></small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-report">
                <tr>
                    <th class="ps-4">Responsable</th>
                    <th class="text-center">Total Operaciones</th>
                    <th class="text-center">Entradas</th>
                    <th class="text-center">Salidas</th>
                    <th class="text-end pe-4">Proporción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($actividad_usuarios as $au): 
                    $proporcion_salidas = $au['total_operaciones'] > 0 ? ($au['salidas'] / $au['total_operaciones'] * 100) : 0;
                ?>
                <tr>
                    <td class="ps-4">
                        <i class="bi bi-person-circle me-2 text-muted"></i>
                        <span class="fw-medium"><?= htmlspecialchars($au['responsable']) ?></span>
                    </td>
                    <td class="text-center"><span class="badge bg-dark rounded-pill px-3"><?= $au['total_operaciones'] ?></span></td>
                    <td class="text-center"><span class="badge bg-success"><?= $au['entradas'] ?></span></td>
                    <td class="text-center"><span class="badge bg-danger"><?= $au['salidas'] ?></span></td>
                    <td class="pe-4">
                        <div class="progress progress-thin">
                            <div class="progress-bar bg-success" style="width: <?= 100 - $proporcion_salidas ?>%"></div>
                            <div class="progress-bar bg-danger" style="width: <?= $proporcion_salidas ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($actividad_usuarios)): ?>
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-person-x display-1 d-block mb-3 opacity-25"></i>
                        No hay actividad registrada en este período.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($tipo_reporte == 'agotados'): ?>
<!-- ================================ -->
<!-- REPORTE: PRODUCTOS AGOTADOS -->
<!-- ================================ -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Productos Agotados / Desactivados</h5>
        <small class="text-muted"><?= count($productos_agotados) ?> productos requieren atención</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-report">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Producto</th>
                    <th class="text-center">Unidad</th>
                    <th class="text-end">Cantidad Inicial</th>
                    <th class="text-end">Cantidad Final</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end pe-4">Fecha Ingreso</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos_agotados as $pa): ?>
                <tr class="table-danger bg-opacity-10">
                    <td class="ps-4 text-muted">#<?= $pa['id'] ?></td>
                    <td class="fw-bold"><?= htmlspecialchars($pa['nombre_producto']) ?></td>
                    <td class="text-center"><span class="badge bg-secondary"><?= $pa['unidad'] ?></span></td>
                    <td class="text-end"><?= number_format($pa['cantidad_inicial'], 2) ?></td>
                    <td class="text-end fw-bold text-danger"><?= number_format($pa['cantidad_actual'], 2) ?></td>
                    <td class="text-center"><span class="badge bg-danger">AGOTADO</span></td>
                    <td class="text-end pe-4 text-muted small"><?= date('d/m/Y', strtotime($pa['fecha_ingreso'])) ?></td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($productos_agotados)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle display-1 d-block mb-3 text-success opacity-50"></i>
                        <p class="mb-0">¡Excelente! No hay productos agotados.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if ($tipo_reporte == 'no_perecederos'): ?>
<!-- ================================ -->
<!-- REPORTE: NO PERECEDEROS -->
<!-- ================================ -->

<!-- Indicadores de No Perecederos -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="display-4 text-primary mb-2">
                    <i class="bi bi-tools"></i>
                </div>
                <h3 class="h4 mb-1"><?= $indicadores_no_perecederos['total_bienes'] ?? 0 ?></h3>
                <p class="text-muted mb-0 small">Total Bienes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="display-4 text-success mb-2">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h3 class="h4 mb-1"><?= $indicadores_no_perecederos['disponibles'] ?? 0 ?></h3>
                <p class="text-muted mb-0 small">Disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="display-4 text-warning mb-2">
                    <i class="bi bi-person-check"></i>
                </div>
                <h3 class="h4 mb-1"><?= $indicadores_no_perecederos['asignados'] ?? 0 ?></h3>
                <p class="text-muted mb-0 small">Asignados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="display-4 text-info mb-2">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <h3 class="h4 mb-1"><?= $indicadores_no_perecederos['total_movimientos'] ?? 0 ?></h3>
                <p class="text-muted mb-0 small">Movimientos (<?= $fecha_desde ?> - <?= $fecha_hasta ?>)</p>
            </div>
        </div>
    </div>
</div>

<!-- Estado de Bienes No Perecederos -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2 text-primary"></i>Estado de Bienes</h5>
            </div>
            <div class="card-body">
                <canvas id="chartEstadoBienes" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2 text-success"></i>Movimientos por Mes</h5>
            </div>
            <div class="card-body">
                <canvas id="chartMovimientosMensuales" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Bienes No Perecederos -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2 text-info"></i>Inventario de Bienes No Perecederos</h5>
        <small class="text-muted">Lista completa de equipos y mobiliario</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-report">
                <tr>
                    <th class="ps-4">N° BIEN</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Serial</th>
                    <th class="text-center">Estado</th>
                    <th>Ubicación</th>
                    <th class="text-end pe-4">Último Movimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bienes_por_estado)): ?>
                    <?php foreach($bienes_por_estado as $bien): ?>
                    <tr>
                        <td class="ps-4 text-muted">#<?= $bien['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($bien['descripcion']) ?></td>
                        <td><?= htmlspecialchars($bien['marca'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($bien['modelo'] ?? '-') ?></td>
                        <td class="font-monospace small"><?= htmlspecialchars($bien['serial'] ?? '-') ?></td>
                        <td class="text-center">
                            <?php
                            $estado_class = match($bien['estado']) {
                                'disponible' => 'bg-success',
                                'asignado' => 'bg-warning',
                                'mantenimiento' => 'bg-danger',
                                'desactivado' => 'bg-secondary',
                                default => 'bg-light text-dark'
                            };
                            $estado_text = match($bien['estado']) {
                                'disponible' => 'Disponible',
                                'asignado' => 'Asignado',
                                'mantenimiento' => 'Mantenimiento',
                                'desactivado' => 'Desactivado',
                                default => ucfirst($bien['estado'])
                            };
                            ?>
                            <span class="badge <?= $estado_class ?>"><?= $estado_text ?></span>
                        </td>
                        <td class="small"><?= htmlspecialchars($bien['ubicacion_exacta'] ?? 'No especificada') ?></td>
                        <td class="text-end pe-4 text-muted small">
                            <?php if ($bien['ultimo_movimiento']): ?>
                                <?= date('d/m/Y H:i', strtotime($bien['ultimo_movimiento'])) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-tools display-1 d-block mb-3 text-muted opacity-50"></i>
                        <p class="mb-0">No hay bienes no perecederos registrados.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Actividad de Usuarios en No Perecederos -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-warning"></i>Actividad por Usuario (No Perecederos)</h5>
        <small class="text-muted">Usuarios más activos en asignaciones/devoluciones</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-report">
                <tr>
                    <th class="ps-4">Usuario</th>
                    <th class="text-center">Total Operaciones</th>
                    <th class="text-center">Asignaciones</th>
                    <th class="text-center">Devoluciones</th>
                    <th class="text-end pe-4">Última Actividad</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($actividad_usuarios_no_perecederos)): ?>
                    <?php foreach($actividad_usuarios_no_perecederos as $usuario): ?>
                    <tr>
                        <td class="ps-4 fw-bold">
                            <i class="bi bi-person-circle me-2 text-muted"></i>
                            <?= htmlspecialchars($usuario['usuario']) ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary fs-6 px-3 py-2"><?= $usuario['total_operaciones'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success fs-6 px-3 py-2"><?= $usuario['asignaciones'] ?? 0 ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info fs-6 px-3 py-2"><?= $usuario['devoluciones'] ?? 0 ?></span>
                        </td>
                        <td class="text-end pe-4 text-muted small">
                            <?= date('d/m/Y H:i', strtotime($usuario['ultima_actividad'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <p class="mb-0">No hay actividad registrada en el período seleccionado.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Movimientos Recientes de No Perecederos -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-secondary"></i>Movimientos Recientes (No Perecederos)</h5>
        <small class="text-muted">Últimas asignaciones y devoluciones</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-report">
                <tr>
                    <th class="ps-4">Fecha/Hora</th>
                    <th>Bien</th>
                    <th>Tipo</th>
                    <th>Usuario</th>
                    <th>Área</th>
                    <th>Responsable</th>
                    <th class="text-end pe-4">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($movimientos_no_perecederos)): ?>
                    <?php foreach($movimientos_no_perecederos as $mov): ?>
                    <tr>
                        <td class="ps-4 text-muted small">
                            <?= date('d/m/Y H:i', strtotime($mov['fecha_movimiento'])) ?>
                        </td>
                        <td class="fw-bold">
                            <span class="badge bg-light text-dark">#<?= $mov['bien_id'] ?></span>
                            <?= htmlspecialchars($mov['descripcion_bien']) ?>
                        </td>
                        <td>
                            <?php if ($mov['tipo_movimiento'] == 'asignacion'): ?>
                                <span class="badge bg-warning"><i class="bi bi-arrow-right me-1"></i>Asignación</span>
                            <?php else: ?>
                                <span class="badge bg-success"><i class="bi bi-arrow-left me-1"></i>Devolución</span>
                            <?php endif; ?>
                        </td>
                        <td class="small"><?= htmlspecialchars($mov['usuario']) ?></td>
                        <td class="small"><?= htmlspecialchars($mov['area_destino'] ?? 'N/A') ?></td>
                        <td class="small"><?= htmlspecialchars($mov['responsable'] ?? 'N/A') ?></td>
                        <td class="text-end pe-4 small text-muted">
                            <?= htmlspecialchars($mov['observaciones'] ?? '-') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <p class="mb-0">No hay movimientos registrados en el período seleccionado.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Gráfico de Estado de Bienes
const ctxEstado = document.getElementById('chartEstadoBienes').getContext('2d');
new Chart(ctxEstado, {
    type: 'doughnut',
    data: {
        labels: ['Disponibles', 'Asignados', 'Mantenimiento', 'Desactivados'],
        datasets: [{
            data: [
                <?= $indicadores_no_perecederos['disponibles'] ?? 0 ?>,
                <?= $indicadores_no_perecederos['asignados'] ?? 0 ?>,
                <?= $indicadores_no_perecederos['mantenimiento'] ?? 0 ?>,
                <?= $indicadores_no_perecederos['desactivados'] ?? 0 ?>
            ],
            backgroundColor: ['#198754', '#ffc107', '#dc3545', '#6c757d'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de Movimientos Mensuales (simulado con datos del período)
const ctxMovimientos = document.getElementById('chartMovimientosMensuales').getContext('2d');
new Chart(ctxMovimientos, {
    type: 'line',
    data: {
        labels: ['<?= date('M', strtotime($fecha_desde)) ?>', '<?= date('M', strtotime($fecha_hasta)) ?>'],
        datasets: [{
            label: 'Movimientos',
            data: [<?= $indicadores_no_perecederos['total_movimientos'] ?? 0 ?>, <?= $indicadores_no_perecederos['total_movimientos'] ?? 0 ?>],
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php endif; ?>

<?php 
$content = ob_get_clean(); 
include 'layout.php'; 
?>
