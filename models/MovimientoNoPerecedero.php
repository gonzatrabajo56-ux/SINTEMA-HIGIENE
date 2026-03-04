<?php
declare(strict_types=1);

class MovimientoNoPerecedero {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(array $data): int {
        $stmt = $this->pdo->prepare("INSERT INTO movimientos_no_perecederos
            (bien_id, tipo_movimiento, area_anterior, area_nueva, responsable_anterior, responsable_nuevo, usuario_registro, observaciones)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['bien_id'],
            $data['tipo_movimiento'],
            $data['area_anterior'] ?? null,
            $data['area_nueva'] ?? null,
            $data['responsable_anterior'] ?? null,
            $data['responsable_nuevo'] ?? null,
            $data['usuario_registro'],
            $data['observaciones'] ?? null
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT m.*, b.numero_bien, b.descripcion, b.marca, b.modelo, b.area_asignada
            FROM movimientos_no_perecederos m
            JOIN bienes_no_perecederos b ON m.bien_id = b.id
            ORDER BY m.fecha_movimiento DESC
        ");
        return $stmt->fetchAll();
    }

    public function getByBien(int $bien_id): array {
        $stmt = $this->pdo->prepare("
            SELECT m.*, b.numero_bien, b.descripcion, b.area_asignada
            FROM movimientos_no_perecederos m
            JOIN bienes_no_perecederos b ON m.bien_id = b.id
            WHERE m.bien_id = ?
            ORDER BY m.fecha_movimiento DESC
        ");
        $stmt->execute([$bien_id]);
        return $stmt->fetchAll();
    }

    public function getByDateRange(string $fecha_inicio, string $fecha_fin): array {
        $stmt = $this->pdo->prepare("
            SELECT m.*, b.numero_bien, b.descripcion, b.marca, b.modelo, b.area_asignada
            FROM movimientos_no_perecederos m
            JOIN bienes_no_perecederos b ON m.bien_id = b.id
            WHERE DATE(m.fecha_movimiento) BETWEEN ? AND ?
            ORDER BY m.fecha_movimiento DESC
        ");
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll();
    }

    public function getByTipo(string $tipo): array {
        $stmt = $this->pdo->prepare("
            SELECT m.*, b.numero_bien, b.descripcion, b.marca, b.modelo, b.area_asignada
            FROM movimientos_no_perecederos m
            JOIN bienes_no_perecederos b ON m.bien_id = b.id
            WHERE m.tipo_movimiento = ?
            ORDER BY m.fecha_movimiento DESC
        ");
        $stmt->execute([$tipo]);
        return $stmt->fetchAll();
    }
}
