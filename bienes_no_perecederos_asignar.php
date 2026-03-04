<?php
require_once 'auth_check.php';
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
    $controller->assign((int)$id, $_POST);
}

$bien = $controller->edit((int)$id);
if (!$bien || $bien['estado'] !== 'disponible') {
    header("Location: bienes_no_perecederos.php");
    exit();
}

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 text-success fw-bold">
                    <i class="bi bi-person-plus me-2"></i>Asignar Bien
                </h5>
                <small class="text-muted"><?= htmlspecialchars($bien['descripcion']) ?> (N° <?= htmlspecialchars($bien['numero_bien']) ?>)</small>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Área de Destino</label>
                        <input type="text" name="area" class="form-control" placeholder="Ej: Oficina Administrativa" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Persona Responsable</label>
                        <input type="text" name="responsable" class="form-control" placeholder="Nombre completo" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success fw-bold">Asignar Bien</button>
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