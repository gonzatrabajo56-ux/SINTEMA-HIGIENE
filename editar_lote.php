<?php 
require_once 'admin_check.php';
require_once 'config/db.php';
require_once 'models/Lote.php';
require_once 'controllers/LoteController.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

$pdo = Database::getConnection();
$controller = new LoteController($pdo);

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->update((int)$id, $_POST);
}

$lote = $controller->edit((int)$id);
if (!$lote) {
    header("Location: index.php");
    exit();
}

ob_start(); 
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Editar Lote #<?= $id ?></h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Producto</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($lote['nombre_producto']) ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Unidad de Medida</label>
                        <select name="unidad" class="form-select" required>
                            <option value="L" <?= $lote['unidad'] == 'L' ? 'selected' : '' ?>>Litros (L)</option>
                            <option value="Kg" <?= $lote['unidad'] == 'Kg' ? 'selected' : '' ?>>Kilogramos (Kg)</option>
                            <option value="g" <?= $lote['unidad'] == 'g' ? 'selected' : '' ?>>Gramos (g)</option>
                            <option value="Pz" <?= $lote['unidad'] == 'Pz' ? 'selected' : '' ?>>Piezas (Pz)</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-bold">Guardar Cambios</button>
                        <a href="index.php" class="btn btn-light text-muted">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
include 'layout.php'; 
?>