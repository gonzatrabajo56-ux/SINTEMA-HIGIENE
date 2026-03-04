<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAC - Sistema Integral de Activos</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --sidebar-width: 260px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand h4 {
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0;
            color: #a5b4fc;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            gap: 0.75rem;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu a.active {
            background: rgba(79, 70, 229, 0.3);
            color: #a5b4fc;
            border-left-color: #818cf8;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="bi bi-hospital me-2"></i>SIAC</h4>
        </div>
        
        <div class="sidebar-menu">
            <a href="index.php?page=dashboard" class="<?= ($_GET['page'] ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="index.php?page=lotes" class="<?= ($_GET['page'] ?? '') === 'lotes' ? 'active' : '' ?>">
                <i class="bi bi-box-seam"></i> Consumibles
            </a>
            <a href="index.php?page=bienes" class="<?= ($_GET['page'] ?? '') === 'bienes' ? 'active' : '' ?>">
                <i class="bi bi-laptop"></i> Activos Fijos
            </a>
            <a href="index.php?page=areas" class="<?= ($_GET['page'] ?? '') === 'areas' ? 'active' : '' ?>">
                <i class="bi bi-diagram-3"></i> Áreas
            </a>
            <a href="index.php?page=reportes" class="<?= ($_GET['page'] ?? '') === 'reportes' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph"></i> Reportes
            </a>
        </div>
        
        <div class="sidebar-footer">
            <a href="logout.php" class="text-danger text-decoration-none d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php echo $content; ?>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
