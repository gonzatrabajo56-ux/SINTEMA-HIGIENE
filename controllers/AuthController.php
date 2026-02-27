<?php
declare(strict_types=1);

class AuthController {
    private PDO $pdo;
    private Usuario $usuarioModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->usuarioModel = new Usuario($pdo);
    }

    public function login(array $data): void {
        $cedula = trim($data['cedula']);
        $password = $data['password'];

        $user = $this->usuarioModel->authenticate($cedula, $password);

        if ($user) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            $_SESSION['usuario_rol'] = $user['rol'];
            header("Location: index.php");
            exit();
        } else {
            header("Location: login.php?error=1");
            exit();
        }
    }

    public function logout(): void {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    public function createUser(array $data): string {
        $cedula = trim($data['cedula']);
        $nombre = trim($data['nombre']);
        $password = $data['password'];
        
        // 1. Validación de rol permitido
        $rol = $data['rol'] ?? 'operador';
        $rolesValidos = ['admin', 'operador'];
        
        if (!in_array($rol, $rolesValidos)) {
            $rol = 'operador'; // Valor por defecto seguro si envían algo inválido
        }

        // 2. Validación básica de campos vacíos (opcional pero recomendado)
        if (empty($cedula) || empty($nombre) || empty($password)) {
            return "<div class='alert alert-warning shadow-sm'><i class='bi bi-exclamation-triangle-fill me-2'></i>Todos los campos son obligatorios.</div>";
        }

        try {
            // 3. Llamada al modelo con datos validados
            $this->usuarioModel->create($cedula, $nombre, $password, $rol);
            return "<div class='alert alert-success shadow-sm'><i class='bi bi-check-circle-fill me-2'></i>Usuario <strong>$nombre</strong> ($rol) registrado correctamente.</div>";
        } catch (Exception $e) {
            // 4. Manejo de error de cédula duplicada
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return "<div class='alert alert-warning shadow-sm'><i class='bi bi-exclamation-triangle-fill me-2'></i>Error: La cédula <strong>$cedula</strong> ya está registrada en el sistema.</div>";
            } else {
                // Loguear el error real en producción
                error_log("Error al crear usuario: " . $e->getMessage());
                return "<div class='alert alert-danger shadow-sm'><i class='bi bi-x-circle-fill me-2'></i>Ocurrió un error inesperado al registrar el usuario.</div>";
            }
        }
    }
}
