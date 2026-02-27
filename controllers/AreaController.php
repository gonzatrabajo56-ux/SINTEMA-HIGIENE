<?php
declare(strict_types=1);

class AreaController {
    private PDO $pdo;
    private Area $areaModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->areaModel = new Area($pdo);
    }

    public function index(): array {
        $areas = $this->areaModel->getAll();
        return ['areas' => $areas];
    }

    public function store(array $data): string {
        $nombre = trim($data['nombre']);
        $descripcion = trim($data['descripcion'] ?? '');
        $icono = $data['icono'] ?? 'bi-geo-alt';
        
        if (empty($nombre)) {
            return "<div class='alert alert-danger shadow-sm'>Por favor, ingresa un nombre para el área.</div>";
        }

        try {
            $this->areaModel->create($nombre);
            return "<div class='alert alert-success shadow-sm'><i class='bi bi-check-circle-fill me-2'></i>Área <strong>$nombre</strong> creada correctamente.</div>";
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return "<div class='alert alert-warning shadow-sm'><i class='bi bi-exclamation-triangle-fill me-2'></i>Ya existe un área con ese nombre.</div>";
            } else {
                return "<div class='alert alert-danger shadow-sm'>Error: " . $e->getMessage() . "</div>";
            }
        }
    }

    public function toggle(int $id): void {
        $this->areaModel->toggleActive($id);
        header("Location: areas.php");
        exit();
    }

    public function delete(int $id): void {
        // 1. Obtener el nombre del área para verificar uso
        $stmt = $this->pdo->prepare("SELECT nombre FROM areas WHERE id = ?");
        $stmt->execute([$id]);
        $area = $stmt->fetch();

        if (!$area) {
            throw new Exception("El área no existe.");
        }
        
        $nombreArea = $area['nombre'];

        // 2. Verificar uso en movimientos de consumibles
        $stmtMov = $this->pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE area_destino = ?");
        $stmtMov->execute([$nombreArea]);
        $en_uso_mov = $stmtMov->fetchColumn();

        // 3. Verificar uso en activos fijos (Bienes)
        $stmtBienes = $this->pdo->prepare("SELECT COUNT(*) FROM bienes_no_perecederos WHERE area_asignada = ?");
        $stmtBienes->execute([$nombreArea]);
        $en_uso_bienes = $stmtBienes->fetchColumn();
        
        if ($en_uso_mov > 0 || $en_uso_bienes > 0) {
            throw new Exception("No se puede eliminar: esta área tiene movimientos o bienes asignados.");
        }
        
        // 4. Si no está en uso, eliminar
        $this->areaModel->delete($id);
    }

    public function getActive(): array {
        return $this->areaModel->getAllActive();
    }
}
