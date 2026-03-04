<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-box-seam me-2"></i>Reportes de Consumibles</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=reportes" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="index.php" method="GET" class="row g-3">
            <input type="hidden" name="page" value="reportes">
            <input type="hidden" name="action" value="consumibles_avanzado">
            
            <div class="col-md-2">
                <label class="form-label small">Tipo</label>
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="entrada" <?= (isset($data['filtros']['tipo']) && $data['filtros']['tipo'] == 'entrada') ? 'selected' : '' ?>>Entrada</option>
                    <option value="salida" <?= (isset($data['filtros']['tipo']) && $data['filtros']['tipo'] == 'salida') ? 'selected' : '' ?>>Salida</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Buscar</label>
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Producto, responsable..." value="<?= $data['filtros']['buscar'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Desde</label>
                <input type="date" name="fecha_desde" class="form-control form-control-sm" value="<?= $data['filtros']['fecha_desde'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="<?= $data['filtros']['fecha_hasta'] ?? '' ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter me-1"></i>Filtrar
                    </button>
                    <a href="index.php?page=reportes&action=consumibles_avanzado" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Estadísticas y Gráficos -->
<div class="row mb-4">
    <!-- Productos con bajo stock -->
    <div class="col-md-4">
        <div class="card border-warning h-100">
            <div class="card-header bg-warning text-white">
                <i class="bi bi-exclamation-triangle me-2"></i>Productos Bajo Stock
            </div>
            <div class="card-body">
                <?php if (empty($data['bajo_stock'])): ?>
                    <p class="text-muted mb-0">No hay productos con bajo stock</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($data['bajo_stock'] as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($item['nombre_producto']) ?>
                                <span class="badge bg-danger"><?= number_format($item['porcentaje'], 0) ?>%</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Gráfico de consumo por área -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Consumo por Área
            </div>
            <div class="card-body">
                <canvas id="graficoArea"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de consumo por mes -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Consumo por Mes
            </div>
            <div class="card-body">
                <canvas id="graficoMes"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de movimientos -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table me-2"></i>Historial de Movimientos</span>
        <div class="btn-group">
            <a href="index.php?page=reportes&action=exportar_csv&tipo=consumibles" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
            </a>
            <a href="index.php?page=reportes&action=pdf_consumibles" target="_blank" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Área</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['movimientos'])): ?>
                        <tr><td colspan="6" class="text-center py-4">No se encontraron movimientos.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['movimientos'] as $m): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($m['fecha_movimiento'])) ?></td>
                                <td><?= htmlspecialchars($m['nombre_producto']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $m['tipo_movimiento'] == 'entrada' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($m['tipo_movimiento']) ?>
                                    </span>
                                </td>
                                <td><?= number_format($m['cantidad_retirada'], 2) ?> <?= htmlspecialchars($m['unidad']) ?></td>
                                <td><?= htmlspecialchars($m['area_destino']) ?></td>
                                <td><?= htmlspecialchars($m['responsable']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Consumo por Área
    const ctxArea = document.getElementById('graficoArea');
    if (ctxArea) {
        const areas = <?= json_encode(array_column($data['consumo_area'] ?? [], 'area')) ?>;
        const consumos = <?= json_encode(array_column($data['consumo_area'] ?? [], 'total_consumido')) ?>;
        
        new Chart(ctxArea, {
            type: 'doughnut',
            data: {
                labels: areas.length ? areas : ['Sin datos'],
                datasets: [{
                    data: consumos.length ? consumos : [1],
                    backgroundColor: [
                        '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107', '#198754'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Gráfico de Consumo por Mes
    const ctxMes = document.getElementById('graficoMes');
    if (ctxMes) {
        const meses = <?= json_encode(array_column($data['consumo_mes'] ?? [], 'mes_nombre')) ?>;
        const consumosMes = <?= json_encode(array_column($data['consumo_mes'] ?? [], 'total_consumido')) ?>;
        
        new Chart(ctxMes, {
            type: 'bar',
            data: {
                labels: meses.length ? meses : ['Sin datos'],
                datasets: [{
                    label: 'Consumo',
                    data: consumosMes.length ? consumosMes : [0],
                    backgroundColor: '#0d6efd',
                    borderColor: '#0a58ca',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
