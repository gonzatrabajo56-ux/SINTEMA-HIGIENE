<?php
declare(strict_types=1);

class Movimiento {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function registrarSalida(array $data): bool {
        try {
            $this->pdo->beginTransaction();

            // 1. Insertar el movimiento
            $stmt = $this->pdo->prepare("INSERT INTO movimientos 
                (lote_id, tipo_movimiento, cantidad_retirada, area_destino, responsable) 
                VALUES (?, 'salida', ?, ?, ?)");
            $stmt->execute([
                $data['lote_id'],
                $data['cantidad'],
                $data['area_destino'],
                $data['responsable']
            ]);

            // 2. Descontar del stock actual en la tabla lotes
            $stmtUpdate = $this->pdo->prepare("UPDATE lotes 
                SET cantidad_actual = cantidad_actual - ? 
                WHERE id = ? AND cantidad_actual >= ?");
            $stmtUpdate->execute([$data['cantidad'], $data['lote_id'], $data['cantidad']]);

            // Si no se afectaron filas, es porque no había stock suficiente
            if ($stmtUpdate->rowCount() === 0) {
                throw new Exception("Stock insuficiente para realizar la salida.");
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT m.*, l.nombre_producto, l.unidad 
            FROM movimientos m 
            JOIN lotes l ON m.lote_id = l.id 
            ORDER BY m.fecha_movimiento DESC
        ");
        return $stmt->fetchAll();
    }

    public function getByDateRange(string $fecha_inicio, string $fecha_fin): array {
        $stmt = $this->pdo->prepare("
            SELECT m.*, l.nombre_producto, l.unidad 
            FROM movimientos m 
            JOIN lotes l ON m.lote_id = l.id 
            WHERE DATE(m.fecha_movimiento) BETWEEN ? AND ?
            ORDER BY m.fecha_movimiento DESC
        ");
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll();
    }

    public function getByArea(string $area): array {
        $stmt = $this->pdo->prepare("
            SELECT m.*, l.nombre_producto, l.unidad 
            FROM movimientos m 
            JOIN lotes l ON m.lote_id = l.id 
            WHERE m.area_destino = ?
            ORDER BY m.fecha_movimiento DESC
        ");
        $stmt->execute([$area]);
        return $stmt->fetchAll();
    }
}
