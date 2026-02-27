<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Historial de Consumibles</h1>
    <a href="index.php?page=reportes" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="index.php" method="GET" class="row g-3">
            <input type="hidden" name="page" value="reportes">
            <input type="hidden" name="action" value="consumibles">
            
            <div class="col-md-3">
                <label class="form-label small">Tipo</label>
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="entrada" <?= (isset($data['tipo_filtro']) && $data['tipo_filtro'] == 'entrada') ? 'selected' : '' ?>>Entrada</option>
                    <option value="salida" <?= (isset($data['tipo_filtro']) && $data['tipo_filtro'] == 'salida') ? 'selected' : '' ?>>Salida</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="<?= $data['fecha_desde'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="<?= $data['fecha_hasta'] ?? '' ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-filter me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Responsable</th>
                        <th>Área/Destino</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['movimientos'])): ?>
                        <tr><td colspan="6" class="text-center">No se encontraron movimientos.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['movimientos'] as $m): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($m['fecha_movimiento'])) ?></td>
                                <td><?= htmlspecialchars($m['nombre_producto']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $m['tipo_movimiento'] == 'entrada' ? 'success' : 'warning' ?>">
                                        <?= ucfirst(htmlspecialchars($m['tipo_movimiento'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= number_format($m['cantidad_retirada'] ?? 0, 2) ?> 
                                    <?= htmlspecialchars($m['unidad'] ?? '') ?>
                                </td>
                                <td><?= htmlspecialchars($m['responsable'] ?? '') ?></td>
                                <td><?= htmlspecialchars($m['area_destino'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>