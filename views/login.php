<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Sintema Higiene</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                            <h3 class="fw-bold mt-2">Sintema Higiene</h3>
                            <p class="text-muted">Inicie sesión para continuar</p>
                        </div>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>Cédula o contraseña incorrectas</div>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" name="cedula" class="form-control border-0 bg-light" id="cedula" placeholder="Cédula" required>
                                <label for="cedula">Cédula de Identidad</label>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" name="password" class="form-control border-0 bg-light" id="password" placeholder="Contraseña" required>
                                <label for="password">Contraseña</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>