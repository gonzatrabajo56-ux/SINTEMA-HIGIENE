<?php
declare(strict_types=1);

class Area {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllActive(): array {
        return $this->pdo->query("SELECT * FROM areas WHERE activa = 1 ORDER BY nombre ASC")->fetchAll();
    }

    public function getAll(): array {
        return $this->pdo->query("SELECT * FROM areas ORDER BY nombre ASC")->fetchAll();
    }

    public function create(string $nombre): int {
        $stmt = $this->pdo->prepare("INSERT INTO areas (nombre, activa) VALUES (?, 1)");
        $stmt->execute([$nombre]);
        return (int) $this->pdo->lastInsertId();
    }

    public function toggleActive(int $id): void {
        $stmt = $this->pdo->prepare("UPDATE areas SET activa = NOT activa WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM areas WHERE id = ?");
        $stmt->execute([$id]);
    }
}
