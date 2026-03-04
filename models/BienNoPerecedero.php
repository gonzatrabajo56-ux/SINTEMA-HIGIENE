<?php
declare(strict_types=1);

class BienNoPerecedero {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM bienes_no_perecederos ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM bienes_no_perecederos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

        public function create(array $data): int {
        $stmt = $this->pdo->prepare("INSERT INTO bienes_no_perecederos
            (numero_bien, descripcion, marca, modelo, color, serial, ubicacion_exacta, area_asignada, responsable, estado, fecha_ingreso, fecha_registro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        return (int) $stmt->execute([
            $data['numero_bien'],
            $data['descripcion'],
            $data['marca'] ?? null,
            $data['modelo'] ?? null,
            $data['color'] ?? null,
            $data['serial'] ?? null,
            $data['ubicacion_exacta'] ?? null,
            $data['area_asignada'] ?? null,
            $data['responsable'] ?? null,
            $data['estado'] ?? 'disponible',
            $data['fecha_ingreso'] ?? date('Y-m-d')
        ]);
    }
    public function updateEstado(int $id, string $estado): void {
        $stmt = $this->pdo->prepare("UPDATE bienes_no_perecederos SET estado = ? WHERE id = ?");
        $stmt->execute([$estado, $id]);
    }

    public function updateAreaYEstado(int $id, ?string $area, ?string $responsable, string $estado): void {
        $stmt = $this->pdo->prepare("UPDATE bienes_no_perecederos SET area_asignada = ?, responsable = ?, estado = ? WHERE id = ?");
        $stmt->execute([$area, $responsable, $estado, $id]);
    }

    public function update(int $id, array $data): void {
        $stmt = $this->pdo->prepare("UPDATE bienes_no_perecederos SET
            numero_bien = ?, descripcion = ?, marca = ?, modelo = ?, color = ?, serial = ?,
            ubicacion_exacta = ?, estado = ?, area_asignada = ?, responsable = ?
            WHERE id = ?");
        $stmt->execute([
            $data['numero_bien'],
            $data['descripcion'],
            $data['marca'] ?? null,
            $data['modelo'] ?? null,
            $data['color'] ?? null,
            $data['serial'] ?? null,
            $data['ubicacion_exacta'] ?? null,
            $data['estado'] ?? 'disponible',
            $data['area_asignada'] ?? null,
            $data['responsable'] ?? null,
            $id
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM bienes_no_perecederos WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function assign(int $id, string $area, string $responsable): void {
        $bien = $this->getById($id);
        if (!$bien) {
            throw new Exception('Bien no encontrado');
        }

        $this->pdo->beginTransaction();

        try {
            // Actualizar el bien
            $stmt = $this->pdo->prepare("UPDATE bienes_no_perecederos SET
                estado = 'asignado', area_asignada = ?, responsable = ? WHERE id = ?");
            $stmt->execute([$area, $responsable, $id]);

            // Registrar movimiento
            $movimientoModel = new MovimientoNoPerecedero($this->pdo);
            $movimientoModel->create([
                'bien_id' => $id,
                'tipo_movimiento' => 'asignacion',
                'area_anterior' => $bien['ubicacion_exacta'],
                'area_nueva' => $area,
                'responsable_anterior' => $bien['responsable'],
                'responsable_nuevo' => $responsable,
                'usuario_registro' => $_SESSION['usuario_nombre'] ?? 'Sistema'
            ]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function return(int $id): void {
        $bien = $this->getById($id);
        if (!$bien) {
            throw new Exception('Bien no encontrado');
        }

        $this->pdo->beginTransaction();

        try {
            // Actualizar el bien
            $stmt = $this->pdo->prepare("UPDATE bienes_no_perecederos SET
                estado = 'disponible', area_asignada = NULL, responsable = NULL WHERE id = ?");
            $stmt->execute([$id]);

            // Registrar movimiento
            $movimientoModel = new MovimientoNoPerecedero($this->pdo);
            $movimientoModel->create([
                'bien_id' => $id,
                'tipo_movimiento' => 'devolucion',
                'area_anterior' => $bien['area_asignada'],
                'area_nueva' => $bien['ubicacion_exacta'],
                'responsable_anterior' => $bien['responsable'],
                'responsable_nuevo' => null,
                'usuario_registro' => $_SESSION['usuario_nombre'] ?? 'Sistema'
            ]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getByEstado(string $estado): array {
        $stmt = $this->pdo->prepare("SELECT * FROM bienes_no_perecederos WHERE estado = ? ORDER BY id DESC");
        $stmt->execute([$estado]);
        return $stmt->fetchAll();
    }
}
