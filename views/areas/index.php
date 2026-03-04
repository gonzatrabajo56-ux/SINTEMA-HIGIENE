<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-diagram-3 me-2"></i>Gestión de Áreas</h1>
</div>

<!-- Formulario para nueva área -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-plus-circle me-2"></i>Nueva Área
    </div>
    <div class="card-body">
        <form action="index.php?page=areas&action=store" method="POST" class="row g-3">
            <?php require_once 'helpers/CsrfHelper.php'; echo CsrfHelper::tokenField(); ?>
            <div class="col-md-8">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre del área (ej: Cocina, Oficina, Baños)" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-lg me-2"></i>Crear Área
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Mensaje de resultado -->
<?php if (isset($data['mensaje'])): ?>
    <div class="mb-4"><?= $data['mensaje'] ?></div>
<?php endif; ?>

<!-- Lista de áreas -->
<div class="card shadow-sm">
    <div class="card-header">
        <i class="bi bi-list-ul me-2"></i>Áreas Registradas
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Área</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['areas'])): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                No hay áreas registradas
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['areas'] as $area): ?>
                            <tr>
                                <td><?= $area['id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($area['nombre']) ?></td>
                                <td>
                                    <?php if ($area['activa']): ?>
                                        <span class="badge bg-success">Activa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Toggle (Activar/Desactivar) -->
                                        <form action="index.php?page=areas&action=toggle" method="POST">
                                            <?php require_once 'helpers/CsrfHelper.php'; echo CsrfHelper::tokenField(); ?>
                                            <input type="hidden" name="id" value="<?= $area['id'] ?>">
                                            <button type="submit" 
                                                    class="btn btn-<?= $area['activa'] ? 'warning' : 'success' ?>"
                                                    title="<?= $area['activa'] ? 'Desactivar' : 'Activar' ?>">
                                                <i class="bi bi-<?= $area['activa'] ? 'eye-slash' : 'eye' ?>"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Editar - Ir a página de edición -->
                                        <a href="index.php?page=areas&action=edit&id=<?= $area['id'] ?>" class="btn btn-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <!-- Eliminar -->
                                        <form action="index.php?page=areas&action=delete" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta área?');">
                                            <?php require_once 'helpers/CsrfHelper.php'; echo CsrfHelper::tokenField(); ?>
                                            <input type="hidden" name="id" value="<?= $area['id'] ?>">
                                            <button type="submit" class="btn btn-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
