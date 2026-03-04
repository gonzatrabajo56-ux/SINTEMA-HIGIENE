<?php
declare(strict_types=1);

/**
 * Modelo para reportes de inventario y consumibles
 */
class Reporte {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ==========================================
    // REPORTES DE CONSUMIBLES (LOTES)
    // ==========================================

    /**
     * Obtiene movimientos de consumibles con filtros
     */
    public function getMovimientosConsumibles(array $filtros): array {
        $sql = "SELECT m.*, l.nombre_producto, l.unidad, l.cantidad_actual, l.cantidad_inicial
                FROM movimientos m 
                JOIN lotes l ON m.lote_id = l.id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($filtros['tipo'])) {
            $sql .= " AND m.tipo_movimiento = ?";
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['buscar'])) {
            $sql .= " AND (l.nombre_producto LIKE ? OR m.responsable LIKE ? OR m.area_destino LIKE ?)";
            $buscar = "%{$filtros['buscar']}%";
            $params[] = $buscar;
            $params[] = $buscar;
            $params[] = $buscar;
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(m.fecha_movimiento) >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(m.fecha_movimiento) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $sql .= " ORDER BY m.fecha_movimiento DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene estadísticas de consumibles por producto
     */
    public function getEstadisticasConsumibles(): array {
        $sql = "SELECT 
                    l.id,
                    l.nombre_producto,
                    l.cantidad_inicial,
                    l.cantidad_actual,
                    l.unidad,
                    l.estado,
                    ROUND((l.cantidad_actual / l.cantidad_inicial * 100), 1) as porcentaje_restante,
                    (SELECT COUNT(*) FROM movimientos WHERE lote_id = l.id) as total_movimientos,
                    (SELECT SUM(cantidad_retirada) FROM movimientos WHERE lote_id = l.id AND tipo_movimiento = 'salida') as total_consumido
                FROM lotes l
                ORDER BY l.nombre_producto ASC";
        
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Obtiene consumo por área
     */
    public function getConsumoPorArea(): array {
        $sql = "SELECT 
                    area_destino as area,
                    SUM(cantidad_retirada) as total_consumido,
                    COUNT(*) as num_movimientos
                FROM movimientos 
                WHERE tipo_movimiento = 'salida'
                GROUP BY area_destino
                ORDER BY total_consumido DESC";
        
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Obtiene consumo por mes
     */
    public function getConsumoPorMes(int $anio = null): array {
        $anio = $anio ?? date('Y');
        
        $sql = "SELECT 
                    DATE_FORMAT(fecha_movimiento, '%Y-%m') as mes,
                    DATE_FORMAT(fecha_movimiento, '%b %Y') as mes_nombre,
                    SUM(cantidad_retirada) as total_consumido,
                    COUNT(*) as num_movimientos
                FROM movimientos 
                WHERE tipo_movimiento = 'salida' 
                AND YEAR(fecha_movimiento) = ?
                GROUP BY DATE_FORMAT(fecha_movimiento, '%Y-%m')
                ORDER BY mes ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$anio]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene productos con bajo stock
     */
    public function getProductosBajoStock(): array {
        $sql = "SELECT 
                    l.*,
                    ROUND((l.cantidad_actual / l.cantidad_inicial * 100), 1) as porcentaje
                FROM lotes l
                WHERE (l.cantidad_actual / l.cantidad_inicial) <= 0.2
                AND l.estado = 'activo'
                ORDER BY porcentaje ASC";
        
        return $this->pdo->query($sql)->fetchAll();
    }

    // ==========================================
    // REPORTES DE ACTIVOS FIJOS
    // ==========================================

    /**
     * Obtiene bienes con filtros
     */
    public function getBienes(array $filtros): array {
        $sql = "SELECT * FROM bienes_no_perecederos WHERE 1=1";
        $params = [];

        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['buscar'])) {
            $sql .= " AND (descripcion LIKE ? OR marca LIKE ? OR modelo LIKE ? OR numero_bien LIKE ? OR serial LIKE ?)";
            $buscar = "%{$filtros['buscar']}%";
            $params = array_merge($params, [$buscar, $buscar, $buscar, $buscar, $buscar]);
        }

        if (!empty($filtros['area'])) {
            $sql .= " AND area_asignada LIKE ?";
            $params[] = "%{$filtros['area']}%";
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene estadísticas de bienes por estado
     */
    public function getEstadisticasBienes(): array {
        $sql = "SELECT 
                    estado,
                    COUNT(*) as cantidad,
                    ROUND(COUNT(*) * 100 / (SELECT COUNT(*) FROM bienes_no_perecederos), 1) as porcentaje
                FROM bienes_no_perecederos
                GROUP BY estado
                ORDER BY cantidad DESC";
        
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Obtiene movimientos de activos con filtros
     */
    public function getMovimientosActivos(array $filtros): array {
        $sql = "SELECT m.*, b.numero_bien, b.descripcion, b.marca, b.modelo
                FROM movimientos_no_perecederos m
                JOIN bienes_no_perecederos b ON m.bien_id = b.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filtros['tipo'])) {
            $sql .= " AND m.tipo_movimiento = ?";
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(m.fecha_movimiento) >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(m.fecha_movimiento) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $sql .= " ORDER BY m.fecha_movimiento DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene bienes por área
     */
    public function getBienesPorArea(): array {
        $sql = "SELECT 
                    COALESCE(area_asignada, 'Sin asignar') as area,
                    COUNT(*) as cantidad
                FROM bienes_no_perecederos
                GROUP BY area_asignada
                ORDER BY cantidad DESC";
        
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Obtiene actividades por usuario
     */
    public function getActividadesPorUsuario(array $filtros): array {
        $sql = "SELECT 
                    usuario_registro as usuario,
                    COUNT(*) as total_operaciones,
                    SUM(CASE WHEN tipo_movimiento = 'asignacion' THEN 1 ELSE 0 END) as asignaciones,
                    SUM(CASE WHEN tipo_movimiento = 'devolucion' THEN 1 ELSE 0 END) as devoluciones
                FROM movimientos_no_perecederos
                WHERE 1=1";
        
        $params = [];

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(fecha_movimiento) >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(fecha_movimiento) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $sql .= " GROUP BY usuario_registro ORDER BY total_operaciones DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    /**
     * Obtiene resumen general del inventario
     */
    public function getResumenGeneral(): array {
        $resumen = [
            'total_bienes' => 0,
            'bienes_disponibles' => 0,
            'bienes_asignados' => 0,
            'bienes_mantenimiento' => 0,
            'total_lotes' => 0,
            'lotes_activos' => 0,
            'lotes_agotados' => 0,
            'bajo_stock' => 0
        ];

        // Bienes
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM bienes_no_perecederos");
        $resumen['total_bienes'] = (int)$stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM bienes_no_perecederos WHERE estado = 'disponible'");
        $resumen['bienes_disponibles'] = (int)$stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM bienes_no_perecederos WHERE estado = 'asignado'");
        $resumen['bienes_asignados'] = (int)$stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM bienes_no_perecederos WHERE estado = 'mantenimiento'");
        $resumen['bienes_mantenimiento'] = (int)$stmt->fetchColumn();

        // Lotes
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM lotes");
        $resumen['total_lotes'] = (int)$stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM lotes WHERE estado = 'activo'");
        $resumen['lotes_activos'] = (int)$stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM lotes WHERE estado = 'agotado'");
        $resumen['lotes_agotados'] = (int)$stmt->fetchColumn();

        // Bajo stock
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM lotes WHERE (cantidad_actual / cantidad_inicial) <= 0.2 AND estado = 'activo'");
        $resumen['bajo_stock'] = (int)$stmt->fetchColumn();

        return $resumen;
    }
}
