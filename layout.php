<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fundación - Gestión, Seguimiento y control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* === BASE === */
        body { 
            background-color: #f8f9fa; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
            font-size: 16px;
        }
        
        /* === NAVBAR === */
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar-brand { font-weight: 800; color: #0d6efd !important; font-size: 1.1rem; }
        .nav-link { font-weight: 500; transition: color 0.2s; }
        .badge-role { font-size: 0.7rem; vertical-align: middle; padding: 4px 8px; }
        
        /* === MAIN CONTAINER === */
        .main-container { 
            flex: 1; 
            padding-top: 1.5rem; 
            padding-bottom: 2rem; 
            width: 100%;
        }
        
        /* === FOOTER === */
        .system-description { font-size: 0.85rem; color: #6c757d; max-width: 800px; margin: 0 auto; line-height: 1.4; }
        
        /* === TABLAS RESPONSIVE === */
        .table-responsive { 
            overflow-x: auto; 
            -webkit-overflow-scrolling: touch; 
        }
        .table { min-width: 600px; }
        
        /* === CARDS === */
        .card { border-radius: 0.75rem; }
        
        /* === BOTONES TOUCH-FRIENDLY === */
        .btn { min-height: 44px; }
        .btn-sm { min-height: 38px; }
        
        /* === FORMULARIOS === */
        .form-control, .form-select { 
            min-height: 44px; 
            font-size: 16px; /* Evita zoom en iOS */
        }
        
        /* ================================ */
        /* RESPONSIVE: TABLETS (768px-991px) */
        /* ================================ */
        @media (max-width: 991.98px) {
            .navbar-brand { font-size: 0.95rem; }
            .main-container { padding-top: 1rem; }
            
            /* Cards en 2 columnas */
            .col-lg-4 { flex: 0 0 50%; max-width: 50%; }
        }
        
        /* ================================ */
        /* RESPONSIVE: MÓVILES (<768px) */
        /* ================================ */
        @media (max-width: 767.98px) {
            /* Navbar */
            .navbar-brand { 
                font-size: 0.85rem; 
                max-width: 200px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            /* Contenedor principal */
            .main-container { 
                padding-left: 0.75rem; 
                padding-right: 0.75rem; 
            }
            
            /* Cards full width */
            .col-md-4, .col-md-5, .col-md-6, .col-lg-4 { 
                flex: 0 0 100%; 
                max-width: 100%; 
            }
            
            /* Tablas más compactas */
            .table { font-size: 0.85rem; min-width: 500px; }
            .table th, .table td { padding: 0.5rem; }
            
            /* Stats cards */
            .stat-card .display-6 { font-size: 1.5rem; }
            .stat-card h2 { font-size: 1.5rem; }
            
            /* Formularios */
            .row.g-3 > [class*="col-"] { margin-bottom: 0.5rem; }
            
            /* Botones full width en móvil */
            .btn-mobile-full { width: 100%; margin-bottom: 0.5rem; }
            
            /* Footer */
            .system-description { font-size: 0.8rem; padding: 0 1rem; }
            footer { padding: 2rem 0 !important; }
            footer h6 { font-size: 0.9rem; }
            
            /* Ocultar texto en botones pequeños */
            .hide-mobile-text { display: none; }
            
            /* Badges */
            .badge { font-size: 0.7rem; }
            
            /* Progress bars */
            .progress { height: 4px !important; }
            
            /* Cards header */
            .card-header { padding: 0.75rem 1rem; }
            .card-body { padding: 1rem; }
            
            /* Dropdown menus */
            .dropdown-menu { font-size: 0.9rem; }
        }
        
        /* ================================ */
        /* RESPONSIVE: MÓVILES PEQUEÑOS (<576px) */
        /* ================================ */
        @media (max-width: 575.98px) {
            .navbar-brand { 
                font-size: 0.75rem; 
                max-width: 150px;
            }
            
            /* Display numbers más pequeños */
            .display-5 { font-size: 1.75rem; }
            .display-6 { font-size: 1.25rem; }
            
            /* Formulario de filtros vertical */
            .filter-form .col-md-2,
            .filter-form .col-md-3,
            .filter-form .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            /* Nav pills scrollable */
            .nav-pills { 
                flex-wrap: nowrap; 
                overflow-x: auto; 
                -webkit-overflow-scrolling: touch;
                padding-bottom: 0.5rem;
            }
            .nav-pills .nav-item { flex-shrink: 0; }
            .nav-pills .nav-link { 
                font-size: 0.8rem; 
                padding: 0.5rem 0.75rem;
                white-space: nowrap;
            }
            
            /* Alertas */
            .alert { font-size: 0.85rem; padding: 0.75rem; }
            
            /* Cards de estadísticas */
            .row.g-4 > .col-md-3 { 
                flex: 0 0 50%; 
                max-width: 50%; 
            }
        }
        
        /* ================================ */
        /* UTILIDADES RESPONSIVE */
        /* ================================ */
        .text-truncate-mobile {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        @media (min-width: 768px) {
            .text-truncate-mobile { max-width: none; }
        }
        
        /* Scroll horizontal suave */
        .scroll-x {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        
        /* Ocultar scrollbar pero mantener funcionalidad */
        .scroll-x::-webkit-scrollbar { height: 4px; }
        .scroll-x::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
    </style>
</head>
<body>

    <?php 
    // Seguridad: Si no hay rol en la sesión por algún motivo, asumimos 'operador'
    $rol_actual = $_SESSION['usuario_rol'] ?? 'operador';
    $es_admin = ($rol_actual === 'admin');
    ?>

    <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-droplet-fill me-2"></i>SISTEMA DE GESTION, SEGUIMIENTO Y CONTROL 
            </a>
            
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link px-3" href="index.php"><i class="bi bi-house-door me-1"></i> Inicio</a>
                        </li>

                        <?php if ($es_admin): ?>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="entrada.php"><i class="bi bi-plus-circle me-1"></i> Nuevo Lote</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="bienes_no_perecederos.php"><i class="bi bi-boxes me-1"></i> Bienes No Perecederos</a>
                        </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link px-3" href="historial.php"><i class="bi bi-clock-history me-1"></i> Historial</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link px-3" href="reportes.php"><i class="bi bi-bar-chart-line me-1"></i> Reportes</a>
                        </li>
                        
                        <?php if ($es_admin): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-gear me-1"></i> Configuración
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item py-2" href="usuarios_nuevo.php">
                                        <i class="bi bi-person-plus me-2"></i> Crear Usuario
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2" href="areas.php">
                                        <i class="bi bi-geo-alt me-2"></i> Gestionar Áreas
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <li class="nav-item ms-lg-3 border-start ps-lg-3 d-flex align-items-center">
                            <div class="me-3 text-end d-none d-lg-block">
                                <div class="small fw-bold lh-1"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></div>
                                <span class="badge badge-role <?= $es_admin ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' ?> border text-uppercase mt-1">
                                    <?= $rol_actual ?>
                                </span>
                            </div>
                            <a class="btn btn-outline-danger btn-sm fw-bold px-3 shadow-sm" href="logout.php">
                                <i class="bi bi-box-arrow-right me-1"></i> Salir
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container main-container">
        <?php if (isset($_GET['error']) && $_GET['error'] === 'acceso_denegado'): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center" role="alert">
                <i class="bi bi-shield-lock-fill fs-4 me-3"></i>
                <div>
                    <strong>Acceso Restringido:</strong> No tienes permisos de administrador para entrar a esa sección.
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $content; ?>
    </main>

    <footer class="bg-white border-top py-5 mt-auto">
        <div class="container text-center">
            <div class="system-description mb-4">
                <h6 class="text-dark fw-bold">Sobre el Sistema de Gestión</h6>
                <p>
                    Plataforma administrativa para el registro, control de lotes y monitoreo de consumos de insumos de todo tipo y ambito  de la 
                    <strong>Fundación Castillo de San Antonio de la Eminencia</strong>. Cumaná, Venezuela. 
                </p>
            </div>
            <p class="text-muted mb-0 small fw-semibold">
                &copy; <?= date('Y') ?> Fundación Castillo de San Antonio de la Eminencia
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>