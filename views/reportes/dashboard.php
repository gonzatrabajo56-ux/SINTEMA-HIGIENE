<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-file-earmark-bar-graph me-2"></i>Centro de Reportes</h1>
</div>

<!-- Resumen General -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Bienes</h6>
                        <h2 class="mb-0"><?= $data['resumen']['total_bienes'] ?? 0 ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-laptop display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Bienes Disponibles</h6>
                        <h2 class="mb-0"><?= $data['resumen']['bienes_disponibles'] ?? 0 ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">En Uso</h6>
                        <h2 class="mb-0"><?= $data['resumen']['bienes_asignados'] ?? 0 ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-person display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Bajo Stock</h6>
                        <h2 class="mb-0"><?= $data['resumen']['bajo_stock'] ?? 0 ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-triangle display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Opciones de Reportes -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm border-0 rounded-4 hover-shadow">
            <div class="card-body text-center p-5">
                <i class="bi bi-box-seam text-primary display-1 mb-3"></i>
                <h3 class="card-title fw-bold">Reporte de Consumibles</h3>
                <p class="card-text text-muted mb-4">
                    historial de entradas y salidas de productos de limpieza.<br>
                    Incluye gráficos, filtros avanzados y exportación.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="index.php?page=reportes&action=consumibles_avanzado" class="btn btn-primary rounded-pill px-4">
                        Ver Reporte <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm border-0 rounded-4 hover-shadow">
            <div class="card-body text-center p-5">
                <i class="bi bi-laptop text-success display-1 mb-3"></i>
                <h3 class="card-title fw-bold">Reporte de Activos Fijos</h3>
                <p class="card-text text-muted mb-4">
                    Movimientos, asignaciones y estado de equipos.<br>
                    Incluye gráficos, filtros y exportación.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="index.php?page=reportes&action=activos_avanzado" class="btn btn-success rounded-pill px-4">
                        Ver Reporte <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas Rápidas -->
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-box-seam me-2"></i>Resumen Consumibles
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h3 class="text-primary"><?= $data['resumen']['total_lotes'] ?? 0 ?></h3>
                        <small class="text-muted">Total Productos</small>
                    </div>
                    <div class="col-6">
                        <h3 class="text-success"><?= $data['resumen']['lotes_activos'] ?? 0 ?></h3>
                        <small class="text-muted">Activos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-laptop me-2"></i>Resumen Activos
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h3 class="text-primary"><?= $data['resumen']['bienes_disponibles'] ?? 0 ?></h3>
                        <small class="text-muted">Disponibles</small>
                    </div>
                    <div class="col-4">
                        <h3 class="text-warning"><?= $data['resumen']['bienes_asignados'] ?? 0 ?></h3>
                        <small class="text-muted">Asignados</small>
                    </div>
                    <div class="col-4">
                        <h3 class="text-danger"><?= $data['resumen']['bienes_mantenimiento'] ?? 0 ?></h3>
                        <small class="text-muted">Mantenimiento</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover { 
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; 
        transition: 0.3s; 
    }
</style>
