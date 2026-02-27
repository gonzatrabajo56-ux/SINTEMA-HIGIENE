<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Historial de Activos Fijos</h1>
    <a href="index.php?page=reportes" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="index.php?page=reportes&action=activos" method="GET" class="row g-3">
            <input type="hidden" name="page" value="reportes">
            <input type="hidden" name="action" value="activos">
            
            <div class="col-md-3">
                <label class="form-label small">Tipo Movimiento</label>
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="asignacion">Asignación</option>
                    <option value="devolucion">Devolución</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-success btn-sm w-100">
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
                        <th>Bien</th>
                        <th>Tipo</th>
                        <th>Área</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['movimientos'])): ?>
                        <tr><td colspan="5" class="text-center">No se encontraron movimientos.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['movimientos'] as $m): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($m['fecha_movimiento'])) ?></td>
                                <td><?= htmlspecialchars($m['descripcion_bien']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $m['tipo_movimiento'] == 'asignacion' ? 'primary' : 'info' ?>">
                                        <?= ucfirst($m['tipo_movimiento']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($m['area_destino'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($m['responsable']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>