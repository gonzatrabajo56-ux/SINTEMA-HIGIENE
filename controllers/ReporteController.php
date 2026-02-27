<?php
declare(strict_types=1);

class ReporteController {
    private PDO $pdo;
    private Movimiento $movimientoModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->movimientoModel = new Movimiento($pdo);
    }

    public function filter(array $data): array {
        $tipo_filtro = $data['tipo'] ?? '';
        $busqueda = trim($data['buscar'] ?? '');
        $fecha_desde = $data['desde'] ?? '';
        $fecha_hasta = $data['hasta'] ?? '';

        if (!empty($fecha_desde) && !empty($fecha_hasta) && $fecha_desde > $fecha_hasta) {
            $temp = $fecha_desde;
            $fecha_desde = $fecha_hasta;
            $fecha_hasta = $temp;
        }

        // --- CORRECCIÓN AQUÍ: Asegurar seleccionar la columna de cantidad ---
        $sql = "SELECT m.*, l.nombre_producto, l.unidad, m.cantidad_retirada
                FROM movimientos m 
                JOIN lotes l ON m.lote_id = l.id 
                WHERE 1=1";
        // --------------------------------------------------------------------

        $params = [];

        if (!empty($tipo_filtro)) {
            $sql .= " AND m.tipo_movimiento = ?";
            $params[] = $tipo_filtro;
        }

        if (!empty($busqueda)) {
            $sql .= " AND (l.nombre_producto LIKE ? OR m.responsable LIKE ? OR m.area_destino LIKE ?)";
            $paramBusqueda = "%$busqueda%";
            $params[] = $paramBusqueda;
            $params[] = $paramBusqueda;
            $params[] = $paramBusqueda;
        }

        if (!empty($fecha_desde)) {
            $sql .= " AND DATE(m.fecha_movimiento) >= ?";
            $params[] = $fecha_desde;
        }

        if (!empty($fecha_hasta)) {
            $sql .= " AND DATE(m.fecha_movimiento) <= ?";
            $params[] = $fecha_hasta;
        }

        $sql .= " ORDER BY m.fecha_movimiento DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $movimientos = $stmt->fetchAll();

        return [
            'movimientos' => $movimientos,
            'tipo_filtro' => $tipo_filtro,
            'busqueda' => $busqueda,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta
        ];
    }
}