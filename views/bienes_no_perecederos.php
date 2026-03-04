<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] === 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><strong>Éxito:</strong> El bien no perecedero fue registrado correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['status'] === 'updated'): ?>
        <div class="alert alert-info alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-pencil-fill me-2"></i><strong>Actualizado:</strong> Los datos del bien fueron modificados.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['status'] === 'deleted'): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-trash-fill me-2"></i><strong>Eliminado:</strong> El bien fue removido del inventario.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['status'] === 'assigned'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-person-check-fill me-2"></i><strong>Asignado:</strong> El bien fue asignado correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['status'] === 'returned'): ?>
        <div class="alert alert-info alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-arrow-return-left me-2"></i><strong>Devuelto:</strong> El bien fue devuelto al inventario.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-boxes me-2 text-primary"></i>Inventario de Bienes No Perecederos</h4>
    <?php if ($data['es_admin']): ?>
        <a href="bienes_no_perecederos_agregar.php" class="btn btn-primary px-4 shadow-sm fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Bien
        </a>
    <?php endif; ?>
</div>

<?php if (empty($data['bienes'])): ?>
    <div class="col-12 text-center py-5">
        <i class="bi bi-archive text-muted opacity-25" style="font-size: 5rem;"></i>
        <p class="text-muted mt-3 fs-5">No hay bienes no perecederos registrados en este momento.</p>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($data['bienes'] as $bien): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title fw-bold text-dark mb-1"><?= htmlspecialchars($bien['descripcion']) ?></h5>
                                <span class="badge bg-light text-muted border">N° <?= htmlspecialchars($bien['numero_bien']) ?></span>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item py-2" href="bienes_no_perecederos_editar.php?id=<?= $bien['id'] ?>"><i class="bi bi-pencil me-2 text-primary"></i>Editar</a></li>
                                    <?php if ($bien['estado'] === 'disponible'): ?>
                                        <li><a class="dropdown-item py-2" href="bienes_no_perecederos_asignar.php?id=<?= $bien['id'] ?>"><i class="bi bi-person-plus me-2 text-success"></i>Asignar</a></li>
                                    <?php elseif ($bien['estado'] === 'asignado'): ?>
                                        <li><a class="dropdown-item py-2" href="bienes_no_perecederos_devolver.php?id=<?= $bien['id'] ?>" onclick="return confirm('¿Confirmar devolución?')"><i class="bi bi-arrow-return-left me-2 text-info"></i>Devolver</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2 text-danger" href="bienes_no_perecederos_eliminar.php?id=<?= $bien['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este bien?')"><i class="bi bi-trash me-2"></i>Eliminar</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Estado:</small>
                            <span class="badge
                                <?php if ($bien['estado'] === 'disponible'): ?>bg-success
                                <?php elseif ($bien['estado'] === 'asignado'): ?>bg-warning text-dark
                                <?php elseif ($bien['estado'] === 'mantenimiento'): ?>bg-info
                                <?php else: ?>bg-secondary<?php endif; ?>">
                                <?= ucfirst($bien['estado']) ?>
                            </span>
                        </div>

                        <?php if (!empty($bien['marca'])): ?>
                            <p class="mb-1"><strong>Marca:</strong> <?= htmlspecialchars($bien['marca']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($bien['modelo'])): ?>
                            <p class="mb-1"><strong>Modelo:</strong> <?= htmlspecialchars($bien['modelo']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($bien['serial'])): ?>
                            <p class="mb-1"><strong>Serial:</strong> <?= htmlspecialchars($bien['serial']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($bien['ubicacion_exacta'])): ?>
                            <p class="mb-1"><strong>Ubicación:</strong> <?= htmlspecialchars($bien['ubicacion_exacta']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($bien['area_asignada'])): ?>
                            <p class="mb-1"><strong>Área:</strong> <?= htmlspecialchars($bien['area_asignada']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($bien['responsable'])): ?>
                            <p class="mb-1"><strong>Responsable:</strong> <?= htmlspecialchars($bien['responsable']) ?></p>
                        <?php endif; ?>

                        <small class="text-muted">Registrado: <?= date('d/m/Y', strtotime($bien['fecha_ingreso'])) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
