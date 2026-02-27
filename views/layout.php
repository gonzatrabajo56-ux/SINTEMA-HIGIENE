<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sintema Higiene - Fundación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #343a40; color: white; }
        .sidebar a { color: white; text-decoration: none; padding: 10px; display: block; }
        .sidebar a:hover { background: #495057; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar p-0">
                <div class="p-3"><h4>Sintema Higiene</h4></div>
                <a href="index.php?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="index.php?page=lotes"><i class="bi bi-box-seam"></i> Consumibles</a>
                <a href="index.php?page=bienes"><i class="bi bi-laptop"></i> Activos Fijos</a>
                <a href="index.php?page=reportes"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a>
                <a href="logout.php" class="mt-5 text-danger"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </nav>

            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
                <?php echo $content; // Aquí se inyecta la vista específica ?>
            </main>
        </div>
    </div>
</body>
</html>