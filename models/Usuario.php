<?php
declare(strict_types=1);

class Usuario {
    private PDO $pdo;
    private ?bool $hasPasswordHashColumn = null;
    private ?string $loginColumn = null;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    private function tableHasColumn(string $table, string $column): bool {
        if ($this->hasPasswordHashColumn !== null && $table === 'usuarios' && $column === 'password_hash') {
            return $this->hasPasswordHashColumn;
        }

        $stmtDb = $this->pdo->query("SELECT DATABASE()");
        $dbName = (string) ($stmtDb->fetchColumn() ?: '');

        if ($dbName === '') {
            return false;
        }

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
        ");
        $stmt->execute([$dbName, $table, $column]);
        $exists = ((int) $stmt->fetchColumn()) > 0;

        if ($table === 'usuarios' && $column === 'password_hash') {
            $this->hasPasswordHashColumn = $exists;
        }

        return $exists;
    }

    private function getLoginColumn(): string {
        if ($this->loginColumn !== null) {
            return $this->loginColumn;
        }

        // Orden de preferencia (ajusta si tu BD usa otro nombre)
        $candidates = ['cedula', 'usuario', 'username', 'user', 'email', 'correo', 'documento', 'dni'];
        foreach ($candidates as $col) {
            if ($this->tableHasColumn('usuarios', $col)) {
                $this->loginColumn = $col;
                return $col;
            }
        }

        throw new Exception("La tabla 'usuarios' no tiene una columna de login conocida (por ejemplo: cedula/usuario/email).");
    }

    private function getCreatedAtColumnOrNullExpr(): string {
        if ($this->tableHasColumn('usuarios', 'fecha_creacion')) {
            return '`fecha_creacion`';
        }
        if ($this->tableHasColumn('usuarios', 'created_at')) {
            return '`created_at`';
        }
        return 'NULL';
    }

    private function verifyStoredPassword(string $inputPassword, string $storedValue): bool {
        // Si parece un hash bcrypt/argon/etc, usamos password_verify. Si no, comparamos como texto plano.
        $info = password_get_info($storedValue);
        if (($info['algo'] ?? 0) !== 0) {
            return password_verify($inputPassword, $storedValue);
        }
        return hash_equals($storedValue, $inputPassword);
    }

    public function authenticate(string $cedula, string $password): ?array {
        $loginCol = $this->getLoginColumn();
        $useHash = $this->tableHasColumn('usuarios', 'password_hash');

        if ($useHash) {
            // Traemos el hash junto con los demás datos en una sola consulta
            $stmt = $this->pdo->prepare("SELECT id, nombre, rol, password_hash FROM usuarios WHERE `$loginCol` = ?");
            $stmt->execute([$cedula]);
            $user = $stmt->fetch();

            if ($user && isset($user['password_hash']) && $this->verifyStoredPassword($password, (string) $user['password_hash'])) {
                unset($user['password_hash']);
                return $user;
            }

            return null;
        }

        // Esquema viejo: columna `password` (texto plano o hash)
        $stmt = $this->pdo->prepare("SELECT id, nombre, rol, password FROM usuarios WHERE `$loginCol` = ?");
        $stmt->execute([$cedula]);
        $user = $stmt->fetch();

        // Verificamos si existe el usuario y si la contraseña coincide
        if ($user && isset($user['password']) && $this->verifyStoredPassword($password, (string) $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return null;
    }

    private function getPasswordHash(string $cedula): string {
        $loginCol = $this->getLoginColumn();
        if ($this->tableHasColumn('usuarios', 'password_hash')) {
            $stmt = $this->pdo->prepare("SELECT password_hash FROM usuarios WHERE `$loginCol` = ?");
            $stmt->execute([$cedula]);
            return (string) ($stmt->fetchColumn() ?: '');
        }

        if ($this->tableHasColumn('usuarios', 'password')) {
            $stmt = $this->pdo->prepare("SELECT password FROM usuarios WHERE `$loginCol` = ?");
            $stmt->execute([$cedula]);
            return (string) ($stmt->fetchColumn() ?: '');
        }

        return '';
    }
    public function getNombreById(int $id): string {
        $stmt = $this->pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() ?: 'Desconocido';
    }
    public function create(string $cedula, string $nombre, string $password, string $rol = 'operador'): int {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $loginCol = $this->getLoginColumn();

        if ($this->tableHasColumn('usuarios', 'password_hash')) {
            $stmt = $this->pdo->prepare("INSERT INTO usuarios (`$loginCol`, nombre, password_hash, rol) VALUES (?, ?, ?, ?)");
            $stmt->execute([$cedula, $nombre, $hash, $rol]);
        } else {
            // Esquema viejo
            $stmt = $this->pdo->prepare("INSERT INTO usuarios (`$loginCol`, nombre, password, rol) VALUES (?, ?, ?, ?)");
            $stmt->execute([$cedula, $nombre, $hash, $rol]);
        }
        return (int) $this->pdo->lastInsertId();
    }

    public function getAll(): array {
        $loginCol = $this->getLoginColumn();
        $createdAtExpr = $this->getCreatedAtColumnOrNullExpr();
        $sql = "SELECT id, `$loginCol` AS cedula, nombre, rol, $createdAtExpr AS fecha_creacion FROM usuarios ORDER BY nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
}
