<?php
require_once 'config/auth_check.php';
require_once 'config/db.php';
require_once 'models/BienNoPerecedero.php';
require_once 'controllers/BienNoPerecederoController.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: bienes_no_perecederos.php");
    exit();
}

$pdo = Database::getConnection();
$controller = new BienNoPerecederoController($pdo);

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->update((int)$id, $_POST);
}

$bien = $controller->edit((int)$id);
if (!$bien) {
    header("Location: bienes_no_perecederos.php");
    exit();
}

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Editar Bien No Perecedero
                </h5>
                <small class="text-muted">N° <?= htmlspecialchars($bien['numero_bien']) ?></small>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">N° Bien</label>
                            <input type="text" name="numero_bien" class="form-control" value="<?= htmlspecialchars($bien['numero_bien']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Descripción</label>
                            <input type="text" name="descripcion" class="form-control" value="<?= htmlspecialchars($bien['descripcion']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Marca</label>
                            <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($bien['marca'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Modelo</label>
                            <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($bien['modelo'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Color</label>
                            <input type="text" name="color" class="form-control" value="<?= htmlspecialchars($bien['color'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Serial</label>
                            <input type="text" name="serial" class="form-control" value="<?= htmlspecialchars($bien['serial'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ubicación Exacta</label>
                            <input type="text" name="ubicacion_exacta" class="form-control" value="<?= htmlspecialchars($bien['ubicacion_exacta'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="disponible" <?= $bien['estado'] === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                                <option value="asignado" <?= $bien['estado'] === 'asignado' ? 'selected' : '' ?>>Asignado</option>
                                <option value="mantenimiento" <?= $bien['estado'] === 'mantenimiento' ? 'selected' : '' ?>>En Mantenimiento</option>
                                <option value="baja" <?= $bien['estado'] === 'baja' ? 'selected' : '' ?>>Baja</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Área Asignada</label>
                            <input type="text" name="area_asignada" class="form-control" value="<?= htmlspecialchars($bien['area_asignada'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Responsable</label>
                            <input type="text" name="responsable" class="form-control" value="<?= htmlspecialchars($bien['responsable'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-bold">Guardar Cambios</button>
                        <a href="bienes_no_perecederos.php" class="btn btn-light text-muted">Cancelar</a>
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