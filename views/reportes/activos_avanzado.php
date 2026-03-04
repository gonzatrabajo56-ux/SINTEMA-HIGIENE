<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-laptop me-2"></i>Reportes de Activos Fijos</h1>
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
            <input type="hidden" name="action" value="activos_avanzado">
            
            <div class="col-md-2">
                <label class="form-label small">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="disponible" <?= (isset($data['filtros']['estado']) && $data['filtros']['estado'] == 'disponible') ? 'selected' : '' ?>>Disponible</option>
                    <option value="asignado" <?= (isset($data['filtros']['estado']) && $data['filtros']['estado'] == 'asignado') ? 'selected' : '' ?>>Asignado</option>
                    <option value="mantenimiento" <?= (isset($data['filtros']['estado']) && $data['filtros']['estado'] == 'mantenimiento') ? 'selected' : '' ?>>Mantenimiento</option>
                    <option value="desactivado" <?= (isset($data['filtros']['estado']) && $data['filtros']['estado'] == 'desactivado') ? 'selected' : '' ?>>Desactivado</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Buscar</label>
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Descripción, marca, serial..." value="<?= $data['filtros']['buscar'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Área</label>
                <input type="text" name="area" class="form-control form-control-sm" placeholder="Área" value="<?= $data['filtros']['area'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Desde</label>
                <input type="date" name="fecha_desde" class="form-control form-control-sm" value="<?= $data['filtros']['fecha_desde'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="<?= $data['filtros']['fecha_hasta'] ?? '' ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i>
                    </button>
                    <a href="index.php?page=reportes&action=activos_avanzado" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Estadísticas y Gráficos -->
<div class="row mb-4">
    <!-- Gráfico de bienes por estado -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Bienes por Estado
            </div>
            <div class="card-body">
                <canvas id="graficoEstado"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de bienes por área -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Bienes por Área
            </div>
            <div class="card-body">
                <canvas id="graficoArea"></canvas>
            </div>
        </div>
    </div>

    <!-- Actividades por usuario -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-people me-2"></i>Actividad por Usuario
            </div>
            <div class="card-body">
                <?php if (empty($data['actividades_usuario'])): ?>
                    <p class="text-muted">Sin actividades registradas</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($data['actividades_usuario'] as $user): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?= htmlspecialchars($user['usuario'] ?? 'Sistema') ?></span>
                                <span class="badge bg-primary"><?= $user['total_operaciones'] ?> ops</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Bienes -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-laptop me-2"></i>Inventario de Activos</span>
        <div class="btn-group">
            <a href="index.php?page=reportes&action=exportar_csv&tipo=activos" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
            </a>
            <a href="index.php?page=reportes&action=pdf_activos" target="_blank" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>N° Bien</th>
                        <th>Descripción</th>
                        <th>Marca/Modelo</th>
                        <th>Serial</th>
                        <th>Estado</th>
                        <th>Área</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['bienes'])): ?>
                        <tr><td colspan="7" class="text-center py-4">No se encontraron bienes.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['bienes'] as $bien): ?>
                            <?php
                            $colorEstado = match($bien['estado']) {
                                'disponible' => 'success',
                                'asignado' => 'primary',
                                'mantenimiento' => 'warning',
                                'desactivado' => 'danger',
                                default => 'secondary'
                            };
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($bien['numero_bien']) ?></td>
                                <td><?= htmlspecialchars($bien['descripcion']) ?></td>
                                <td><?= htmlspecialchars($bien['marca'] ?? '') ?> <?= htmlspecialchars($bien['modelo'] ?? '') ?></td>
                                <td><?= htmlspecialchars($bien['serial'] ?? '-') ?></td>
                                <td><span class="badge bg-<?= $colorEstado ?>"><?= ucfirst($bien['estado']) ?></span></td>
                                <td><?= htmlspecialchars($bien['area_asignada'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($bien['responsable'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Movimientos de Activos -->
<div class="card shadow-sm">
    <div class="card-header">
        <i class="bi bi-arrow-left-right me-2"></i>Historial de Movimientos
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-secondary">
                    <tr>
                        <th>Fecha</th>
                        <th>Bien</th>
                        <th>Tipo</th>
                        <th>Área Anterior</th>
                        <th>Área Nueva</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['movimientos'])): ?>
                        <tr><td colspan="6" class="text-center py-3">No hay movimientos</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['movimientos'] as $m): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($m['fecha_movimiento'])) ?></td>
                                <td><?= htmlspecialchars($m['descripcion']) ?></td>
                                <td><span class="badge bg-info"><?= ucfirst($m['tipo_movimiento']) ?></span></td>
                                <td><?= htmlspecialchars($m['area_anterior'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($m['area_nueva'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($m['usuario_registro'] ?? 'Sistema') ?></td>
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
    // Gráfico de Bienes por Estado
    const ctxEstado = document.getElementById('graficoEstado');
    if (ctxEstado) {
        const estados = <?= json_encode(array_column($data['estadisticas_estado'] ?? [], 'estado')) ?>;
        const cantidades = <?= json_encode(array_column($data['estadisticas_estado'] ?? [], 'cantidad')) ?>;
        
        new Chart(ctxEstado, {
            type: 'pie',
            data: {
                labels: estados.length ? estados.map(e => e.charAt(0).toUpperCase() + e.slice(1)) : ['Sin datos'],
                datasets: [{
                    data: cantidades.length ? cantidades : [1],
                    backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545', '#6c757d']
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

    // Gráfico de Bienes por Área
    const ctxArea = document.getElementById('graficoArea');
    if (ctxArea) {
        const areas = <?= json_encode(array_column($data['bienes_area'] ?? [], 'area')) ?>;
        const cantidades = <?= json_encode(array_column($data['bienes_area'] ?? [], 'cantidad')) ?>;
        
        new Chart(ctxArea, {
            type: 'bar',
            data: {
                labels: areas.length ? areas : ['Sin datos'],
                datasets: [{
                    label: 'Cantidad',
                    data: cantidades.length ? cantidades : [0],
                    backgroundColor: '#0d6efd',
                    borderColor: '#0a58ca',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
