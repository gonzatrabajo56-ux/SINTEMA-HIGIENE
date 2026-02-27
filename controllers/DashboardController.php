<?php
declare(strict_types=1);

class DashboardController {
    private PDO $pdo;
    private Lote $loteModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->loteModel = new Lote($pdo);
    }

    public function index(): array {
        // Instanciar otros modelos necesarios
        $bienModel = new BienNoPerecedero($this->pdo);

        // Métricas de Consumibles (Lotes)
        $total_productos = $this->loteModel->getTotalProductosActivos();
        $bajo_stock = $this->loteModel->getBajoStock();
        $lotes = $this->loteModel->getAllActive();

        // NUEVO: Métricas de Bienes (Activos Fijos)
        $total_bienes = count($bienModel->getAll());
        $bienes_mantenimiento = count($bienModel->getByEstado('mantenimiento'));

        return [
            'total_productos' => $total_productos,
            'bajo_stock' => $bajo_stock,
            'lotes' => $lotes,
            'total_bienes' => $total_bienes,
            'bienes_mantenimiento' => $bienes_mantenimiento,
            'rol_usuario' => $_SESSION['usuario_rol'] ?? 'operador',
            'es_admin' => ($_SESSION['usuario_rol'] ?? 'operador') === 'admin'
        ];
    }
}
