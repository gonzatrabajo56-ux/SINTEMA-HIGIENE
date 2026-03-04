<?php
declare(strict_types=1);

class Area {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todas las áreas activas (para selects)
     */
    public function getAllActive(): array {
        return $this->pdo->query("SELECT * FROM areas WHERE activa = 1 ORDER BY nombre ASC")->fetchAll();
    }

    /**
     * Obtiene todas las áreas (para gestión)
     */
    public function getAll(): array {
        return $this->pdo->query("SELECT * FROM areas ORDER BY nombre ASC")->fetchAll();
    }

    /**
     * Obtiene un área por ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM areas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Crea un nueva área
     */
    public function create(string $nombre): int {
        $stmt = $this->pdo->prepare("INSERT INTO areas (nombre, activa) VALUES (?, 1)");
        $stmt->execute([$nombre]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Actualiza el nombre de un área
     */
    public function update(int $id, string $nombre): void {
        $stmt = $this->pdo->prepare("UPDATE areas SET nombre = ? WHERE id = ?");
        $stmt->execute([$nombre, $id]);
    }

    /**
     * Activa/desactiva un área
     */
    public function toggleActive(int $id): void {
        $stmt = $this->pdo->prepare("UPDATE areas SET activa = NOT activa WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Elimina un área
     */
    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM areas WHERE id = ?");
        $stmt->execute([$id]);
    }
}
