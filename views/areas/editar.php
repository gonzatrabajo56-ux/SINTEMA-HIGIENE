<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-diagram-3 me-2"></i>Editar Área</h1>
    <a href="index.php?page=areas" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Volver
    </a>
</div>

<?php if (isset($data['mensaje'])): ?>
    <div class="mb-4"><?= $data['mensaje'] ?></div>
<?php endif; ?>

<?php if (empty($data['area'])): ?>
    <div class="alert alert-danger">El área no fue encontrada.</div>
    <a href="index.php?page=areas" class="btn btn-primary">Volver a Áreas</a>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-pencil me-2"></i>Editar Área
        </div>
        <div class="card-body">
            <form action="index.php?page=areas&action=update" method="POST" class="row g-3">
                <?php require_once 'helpers/CsrfHelper.php'; echo CsrfHelper::tokenField(); ?>
                <input type="hidden" name="id" value="<?= $data['area']['id'] ?>">
                
                <div class="col-md-12">
                    <label class="form-label">Nombre del Área</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($data['area']['nombre']) ?>" required>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                    <a href="index.php?page=areas" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
