<?php
declare(strict_types=1);

class ReporteNoPerecederoController {
    private PDO $pdo;
    private BienNoPerecedero $bienModel;
    private MovimientoNoPerecedero $movimientoModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->bienModel = new BienNoPerecedero($pdo);
        $this->movimientoModel = new MovimientoNoPerecedero($pdo);
    }

    public function dashboard(): array {
        // Mejorado: Usar COUNT(*) en SQL es más eficiente que traer todos los registros
        $total_bienes = (int)$this->pdo->query("SELECT COUNT(*) FROM bienes_no_perecederos")->fetchColumn();
        
        // Asumiendo que getByEstado devuelve un array, count() está bien aquí si no son miles de registros
        $disponibles = count($this->bienModel->getByEstado('disponible'));
        $asignados = count($this->bienModel->getByEstado('asignado'));
        $mantenimiento = count($this->bienModel->getByEstado('mantenimiento'));

        return [
            'total_bienes' => $total_bienes,
            'disponibles' => $disponibles,
            'asignados' => $asignados,
            'mantenimiento' => $mantenimiento
        ];
    }

    public function movimientos(array $filters = []): array {
        $tipo = $filters['tipo'] ?? '';
        $fecha_desde = $filters['desde'] ?? '';
        $fecha_hasta = $filters['hasta'] ?? '';

        if (!empty($tipo)) {
            $movimientos = $this->movimientoModel->getByTipo($tipo);
        } elseif (!empty($fecha_desde) && !empty($fecha_hasta)) {
            $movimientos = $this->movimientoModel->getByDateRange($fecha_desde, $fecha_hasta);
        } else {
            $movimientos = $this->movimientoModel->getAll();
        }

        return ['movimientos' => $movimientos];
    }

    public function getIndicadores(string $fecha_desde = '', string $fecha_hasta = ''): array {
        // Total de bienes
        $total_bienes = $this->pdo->query("SELECT COUNT(*) FROM bienes_no_perecederos")->fetchColumn();
        
        // Bienes por estado
        $stmt = $this->pdo->prepare("SELECT estado, COUNT(*) as cantidad FROM bienes_no_perecederos GROUP BY estado");
        $stmt->execute();
        $estados = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Movimientos en el período
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM movimientos_no_perecederos WHERE DATE(fecha_movimiento) BETWEEN ? AND ?");
        $stmt->execute([$fecha_desde, $fecha_hasta]);
        $total_movimientos = $stmt->fetchColumn();
        
        return [
            'total_bienes' => $total_bienes,
            'disponibles' => $estados['disponible'] ?? 0,
            'asignados' => $estados['asignado'] ?? 0,
            'mantenimiento' => $estados['mantenimiento'] ?? 0,
            'desactivados' => $estados['desactivado'] ?? 0,
            'total_movimientos' => $total_movimientos
        ];
    }

    public function getMovimientosPorPeriodo(string $fecha_desde, string $fecha_hasta): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                m.*,
                b.descripcion as descripcion_bien,
                b.marca,
                b.modelo,
                b.serial
            FROM movimientos_no_perecederos m
            LEFT JOIN bienes_no_perecederos b ON m.bien_id = b.id
            WHERE DATE(m.fecha_movimiento) BETWEEN ? AND ?
            ORDER BY m.fecha_movimiento DESC
            LIMIT 50
        ");
        $stmt->execute([$fecha_desde, $fecha_hasta]);
        return $stmt->fetchAll();
    }

    public function getBienesPorEstado(): array {
        $stmt = $this->pdo->query("
            SELECT 
                b.*,
                (
                    SELECT fecha_movimiento 
                    FROM movimientos_no_perecederos 
                    WHERE bien_id = b.id 
                    ORDER BY fecha_movimiento DESC 
                    LIMIT 1
                ) as ultimo_movimiento
            FROM bienes_no_perecederos b
            ORDER BY b.id ASC
        ");
        return $stmt->fetchAll();
    }

    public function getActividadUsuarios(string $fecha_desde, string $fecha_hasta): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                usuario_registro as usuario,
                COUNT(*) as total_operaciones,
                SUM(CASE WHEN tipo_movimiento = 'asignacion' THEN 1 ELSE 0 END) as asignaciones,
                SUM(CASE WHEN tipo_movimiento = 'devolucion' THEN 1 ELSE 0 END) as devoluciones,
                MAX(fecha_movimiento) as ultima_actividad
            FROM movimientos_no_perecederos
            WHERE DATE(fecha_movimiento) BETWEEN ? AND ?
            GROUP BY usuario_registro
            ORDER BY total_operaciones DESC
            LIMIT 10
        ");
        $stmt->execute([$fecha_desde, $fecha_hasta]);
        return $stmt->fetchAll();
    }
}
