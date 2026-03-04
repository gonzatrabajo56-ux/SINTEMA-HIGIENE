<?php 
require_once 'auth_check.php';
require_once 'config/db.php';
require_once 'models/Usuario.php';
require_once 'controllers/AuthController.php';

// 1. Verificación estricta de Rol: Solo el administrador entra aquí
if ($_SESSION['usuario_rol'] !== 'admin') {
    header("Location: index.php?error=acceso_denegado");
    exit();
}

ob_start(); 
$mensaje = "";

// 2. Procesamiento del Formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = Database::getConnection();
    $controller = new AuthController($pdo);
    $mensaje = $controller->createUser($_POST);
}
?>



<div class="row justify-content-center">
    <div class="col-md-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Nuevo Usuario</li>
            </ol>
        </nav>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Personal
                </h5>
            </div>
            <div class="card-body p-4">
                
                <?= $mensaje ?>

                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Cédula de Identidad</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-card-text"></i></span>
                            <input type="text" name="cedula" class="form-control border-start-0 ps-0" placeholder="Ej: 12345678" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" name="nombre" class="form-control border-start-0 ps-0" placeholder="Ej: Juan Pérez" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Contraseña Inicial</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Mínimo 6 caracteres" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Rol de Acceso</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-tags"></i></span>
                            <select name="rol" class="form-select border-start-0 ps-0" required>
                                <option value="operador" selected>Operador (Insumos y Salidas)</option>
                                <option value="admin">Administrador (Control Total)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                            <i class="bi bi-save me-2"></i>Guardar Usuario
                        </button>
                        <a href="index.php" class="btn btn-link text-muted text-decoration-none small">Cancelar y volver</a>
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