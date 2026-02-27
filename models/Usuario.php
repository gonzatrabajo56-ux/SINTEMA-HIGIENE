<?php
declare(strict_types=1);

class Usuario {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function authenticate(string $cedula, string $password): ?array {
        // Traemos el hash junto con los demás datos en una sola consulta
        $stmt = $this->pdo->prepare("SELECT id, nombre, rol, password_hash FROM usuarios WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $user = $stmt->fetch();

        // Verificamos si existe el usuario y si la contraseña coincide
        if ($user && password_verify($password, $user['password_hash'])) {
            // Quitamos el hash del array antes de devolverlo por seguridad
            unset($user['password_hash']);
            return $user;
        }
        return null;
    }

    private function getPasswordHash(string $cedula): string {
        $stmt = $this->pdo->prepare("SELECT password_hash FROM usuarios WHERE cedula = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetchColumn() ?: '';
    }
    public function getNombreById(int $id): string {
        $stmt = $this->pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() ?: 'Desconocido';
    }
    public function create(string $cedula, string $nombre, string $password, string $rol = 'operador'): int {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (cedula, nombre, password_hash, rol) VALUES (?, ?, ?, ?)");
        $stmt->execute([$cedula, $nombre, $hash, $rol]);
        return (int) $this->pdo->lastInsertId();
    }

    public function getAll(): array {
        return $this->pdo->query("SELECT id, cedula, nombre, rol, fecha_creacion FROM usuarios ORDER BY nombre ASC")->fetchAll();
    }
}
