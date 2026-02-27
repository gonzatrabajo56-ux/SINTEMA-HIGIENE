<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-laptop me-2"></i>Activos Fijos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=bienes&action=agregar" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Registrar Nuevo Bien
        </a>
    </div>
</div>

<?php if (isset($_GET['status'])): ?>
    <?php 
    $alertas = [
        'success'  => ['class' => 'success', 'icon' => 'check-circle-fill', 'text' => 'El bien fue registrado correctamente.'],
        'updated'  => ['class' => 'info', 'icon' => 'pencil-fill', 'text' => 'Los datos fueron modificados.'],
        'assigned' => ['class' => 'success', 'icon' => 'person-check-fill', 'text' => 'El estado ha sido actualizado.']
    ];
    $status = $_GET['status'];
    if (isset($alertas[$status])): ?>
        <div class="alert alert-<?= $alertas[$status]['class'] ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-<?= $alertas[$status]['icon'] ?> me-2"></i><strong>Notificación:</strong> <?= $alertas[$status]['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="row">
    <?php if (empty($data['bienes'])): ?>
        <div class="col-12">
            <div class="alert alert-info">No hay bienes registrados actualmente.</div>
        </div>
    <?php else: ?>
        <?php foreach ($data['bienes'] as $bien): ?>
            <?php
            $colorBadge = match($bien['estado']) {
                'disponible' => 'success',
                'asignado'   => 'primary',
                'mantenimiento' => 'warning',
                'desactivado' => 'danger',
                default => 'secondary'
            };
            ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                        <span class="badge bg-<?= $colorBadge ?> rounded-pill text-uppercase small"><?= htmlspecialchars($bien['estado']) ?></span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=bienes&action=movimiento&id=<?= $bien['id'] ?>"><i class="bi bi-arrow-left-right me-2"></i>Gestionar Movimiento</a></li>
                                <li><a class="dropdown-item" href="index.php?page=bienes&action=editar&id=<?= $bien['id'] ?>"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($bien['descripcion']) ?></h5>
                        <p class="text-muted mb-3 small">N° Bien: <?= htmlspecialchars($bien['numero_bien']) ?></p>

                        <div class="small text-muted">
                            <p class="mb-1"><i class="bi bi-tag me-2"></i><strong>Marca:</strong> <?= htmlspecialchars($bien['marca'] ?: 'N/A') ?></p>
                            <p class="mb-1"><i class="bi bi-palette me-2"></i><strong>Color:</strong> <?= htmlspecialchars($bien['color'] ?: 'N/A') ?></p>
                            <p class="mb-1"><i class="bi bi-upc me-2"></i><strong>Serial:</strong> <?= htmlspecialchars($bien['serial'] ?: 'N/A') ?></p>
                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i><strong>Área:</strong> <?= htmlspecialchars($bien['area_asignada'] ?: 'Sin asignar') ?></p>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <small class="text-muted">Ingreso: <?= !empty($bien['fecha_ingreso']) ? date('d/m/Y', strtotime($bien['fecha_ingreso'])) : 'S/F' ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>