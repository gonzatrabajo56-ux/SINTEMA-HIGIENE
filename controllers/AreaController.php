<?php
declare(strict_types=1);

require_once 'models/Area.php';
require_once 'helpers/CsrfHelper.php';

class AreaController {
    private PDO $pdo;
    private Area $areaModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->areaModel = new Area($pdo);
    }

    /**
     * Lista todas las áreas (para gestión)
     */
    public function index(): array {
        return ['areas' => $this->areaModel->getAll()];
    }

    /**
     * Crea un nueva área
     */
    public function store(array $data): string {
        // Validar CSRF
        if (!CsrfHelper::validateToken($data['csrf_token'] ?? '')) {
            return "<div class='alert alert-danger'>Token CSRF inválido.</div>";
        }
        
        $nombre = trim($data['nombre'] ?? '');
        
        if (empty($nombre)) {
            return "<div class='alert alert-danger'>El nombre del área es requerido.</div>";
        }

        try {
            $this->areaModel->create($nombre);
            return "<div class='alert alert-success'>Área '<strong>$nombre</strong>' creada correctamente.</div>";
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return "<div class='alert alert-warning'>Ya existe un área con ese nombre.</div>";
            }
            return "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }

    /**
     * Actualiza un área
     */
    public function update(array $data): string {
        if (!CsrfHelper::validateToken($data['csrf_token'] ?? '')) {
            return "<div class='alert alert-danger'>Token CSRF inválido.</div>";
        }
        
        $id = (int)($data['id'] ?? 0);
        $nombre = trim($data['nombre'] ?? '');
        
        if (empty($nombre) || $id <= 0) {
            return "<div class='alert alert-danger'>Datos inválidos.</div>";
        }

        try {
            $this->areaModel->update($id, $nombre);
            return "<div class='alert alert-success'>Área actualizada correctamente.</div>";
        } catch (Exception $e) {
            return "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }

    /**
     * Activa/desactiva un área
     */
    public function toggle(array $data): void {
        // Validar CSRF
        if (!CsrfHelper::validateToken($data['csrf_token'] ?? '')) {
            header("Location: index.php?page=areas&error=csrf");
            exit();
        }
        
        $id = (int)($data['id'] ?? 0);
        if ($id > 0) {
            $this->areaModel->toggleActive($id);
        }
        header("Location: index.php?page=areas");
        exit();
    }

    /**
     * Elimina un área
     */
    public function delete(array $data): string {
        if (!CsrfHelper::validateToken($data['csrf_token'] ?? '')) {
            return "<div class='alert alert-danger'>Token CSRF inválido.</div>";
        }
        
        $id = (int)($data['id'] ?? 0);
        
        if ($id <= 0) {
            return "<div class='alert alert-danger'>ID inválido.</div>";
        }

        // Verificar uso en movimientos
        $area = $this->areaModel->getById($id);
        if (!$area) {
            return "<div class='alert alert-danger'>El área no existe.</div>";
        }

        // Verificar uso en movimientos de consumibles
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE area_destino = ?");
        $stmt->execute([$area['nombre']]);
        $en_uso_mov = (int)$stmt->fetchColumn();

        // Verificar uso en bienes
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM bienes_no_perecederos WHERE area_asignada = ?");
        $stmt->execute([$area['nombre']]);
        $en_uso_bienes = (int)$stmt->fetchColumn();
        
        if ($en_uso_mov > 0 || $en_uso_bienes > 0) {
            return "<div class='alert alert-warning'>No se puede eliminar: el área está en uso.</div>";
        }
        
        try {
            $this->areaModel->delete($id);
            return "<div class='alert alert-success'>Área eliminada correctamente.</div>";
        } catch (Exception $e) {
            return "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }

    /**
     * Obtiene áreas activas (para selects en formularios)
     */
    public function getActive(): array {
        return $this->areaModel->getAllActive();
    }

    /**
     * Obtiene todas las áreas (para selects)
     */
    public function getAll(): array {
        return $this->areaModel->getAll();
    }

    /**
     * Obtiene un área por ID
     */
    public function getById(int $id): ?array {
        return $this->areaModel->getById($id);
    }
}
