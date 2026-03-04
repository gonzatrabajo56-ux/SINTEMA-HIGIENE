<?php
declare(strict_types=1);

class Lote {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
            // Cambiar fecha_ingreso por id o la columna real de esa tabla
            $stmt = $this->pdo->query("SELECT * FROM lotes ORDER BY id DESC");
            return $stmt->fetchAll();
        }

    public function getAllActive(): array {
        $stmt = $this->pdo->query("SELECT *, (cantidad_actual / cantidad_inicial * 100) as porcentaje FROM lotes WHERE cantidad_actual > 0 ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getTotalProductosActivos(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM lotes WHERE estado = 'activo'")->fetchColumn();
    }

    public function getBajoStock(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM lotes WHERE (cantidad_actual / cantidad_inicial) <= 0.2 AND estado = 'activo'")->fetchColumn();
    }

    public function create(string $producto, float $cantidad, string $unidad, string $fecha_entrada, string $usuario): int {
        $this->pdo->beginTransaction();

        try {
            // Insertar el lote
            $sqlLote = "INSERT INTO lotes (nombre_producto, cantidad_inicial, cantidad_actual, unidad, estado) 
                        VALUES (?, ?, ?, ?, 'activo')";
            $stmtLote = $this->pdo->prepare($sqlLote);
            $stmtLote->execute([$producto, $cantidad, $cantidad, $unidad]);
            
            $nuevo_lote_id = (int) $this->pdo->lastInsertId();

            // Registrar movimiento
            $sqlMov = "INSERT INTO movimientos (lote_id, cantidad_retirada, area_destino, responsable, tipo_movimiento, fecha_movimiento) 
                       VALUES (?, ?, 'Almacén', ?, 'entrada', ?)";
            $stmtMov = $this->pdo->prepare($sqlMov);
            $stmtMov->execute([
                $nuevo_lote_id, 
                $cantidad, 
                "Ingreso por: " . $usuario,
                $fecha_entrada
            ]);

            $this->pdo->commit();
            return $nuevo_lote_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM lotes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, string $producto, float $cantidad_inicial, float $cantidad_actual, string $unidad): void {
        $stmt = $this->pdo->prepare("UPDATE lotes SET nombre_producto = ?, cantidad_inicial = ?, cantidad_actual = ?, unidad = ? WHERE id = ?");
        $stmt->execute([$producto, $cantidad_inicial, $cantidad_actual, $unidad, $id]);
    }

    public function deactivate(int $id): void {
        $stmt = $this->pdo->prepare("UPDATE lotes SET estado = 'agotado' WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function registerSalida(int $lote_id, float $cantidad, string $area, string $responsable, string $fecha_salida): void {
        $this->pdo->beginTransaction();

        try {
            // Verificar stock
            $lote = $this->getById($lote_id);
            if (!$lote || $lote['cantidad_actual'] < $cantidad) {
                throw new Exception('Stock insuficiente');
            }

            // Actualizar cantidad
            $nueva_cantidad = $lote['cantidad_actual'] - $cantidad;
            $estado = ($nueva_cantidad <= 0) ? 'agotado' : 'activo';
            // Fíjate en el orden: ? , ? , ?
            $stmt = $this->pdo->prepare("UPDATE lotes SET cantidad_actual = ?, estado = ? WHERE id = ?");                
            // El orden debe ser:    nueva_cantidad, estado,    lote_id
            $stmt->execute([$nueva_cantidad, $estado, $lote_id]);

            // Registrar movimiento
            $fecha_movimiento = $fecha_salida . ' ' . date('H:i:s');
            $responsable_final = $responsable . " (Registrado por: " . $_SESSION['usuario_nombre'] . ")";
            $sqlMov = "INSERT INTO movimientos (lote_id, tipo_movimiento, cantidad_retirada, area_destino, responsable, fecha_movimiento) 
                       VALUES (?, 'salida', ?, ?, ?, ?)";
            $stmtMov = $this->pdo->prepare($sqlMov);
            $stmtMov->execute([$lote_id, $cantidad, $area, $responsable_final, $fecha_movimiento]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getAllForSalida(): array {
        return $this->pdo->query("SELECT * FROM lotes WHERE cantidad_actual > 0 AND estado = 'activo' ORDER BY nombre_producto ASC")->fetchAll();
    }
}
