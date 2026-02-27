<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-box-seam me-2"></i>Inventario de Consumibles</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=lotes&action=entrada" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Entrada
        </a>
    </div>
</div>

<div class="row">
    <?php if (empty($data['lotes'])): ?>
        <div class="col-12">
            <div class="alert alert-info">No hay lotes registrados actualmente.</div>
        </div>
    <?php else: ?>
        <?php foreach ($data['lotes'] as $l): ?>
        <?php 
            // Lógica de cálculo de porcentaje (mejor mover esto al controlador)
            $porcentaje = ($l['cantidad_inicial'] > 0) ? ($l['cantidad_actual'] / $l['cantidad_inicial']) * 100 : 0;
            $color = 'success';
            if ($porcentaje <= 50) $color = 'warning';
            if ($porcentaje <= 20) $color = 'danger';
        ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-primary"><?= htmlspecialchars($l['nombre_producto']) ?></h5>
                    <p class="text-muted small">Lote: <?= htmlspecialchars($l['id']) ?></p>
                    
                    <div class="bg-light rounded-3 p-3 text-center mb-3">
                        <h2 class="mb-0 fw-bold"><?= number_format($l['cantidad_actual'], 2) ?></h2>
                        <small class="text-muted text-uppercase fw-bold"><?= htmlspecialchars($l['unidad']) ?></small>
                    </div>

                    <div class="d-flex justify-content-between mb-1 small">
                        <span>Estado</span>
                        <span class="fw-bold text-<?= $color ?>"><?= number_format($porcentaje, 0) ?>%</span>
                    </div>

                    <div class="progress rounded-pill mb-4" style="height: 6px;">
                        <div class="progress-bar bg-<?= $color ?>" style="width: <?= $porcentaje ?>%"></div>
                    </div>

                    <a href="index.php?page=lotes&action=salida&id=<?= $l['id'] ?>" class="btn btn-<?= ($porcentaje <= 20) ? 'danger' : 'outline-primary' ?> w-100 rounded-3 fw-bold">
                        <i class="bi bi-box-arrow-right me-2"></i>Registrar Salida
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>