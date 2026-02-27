<?php
declare(strict_types=1);

class BienNoPerecederoController {
    private PDO $pdo;
    private BienNoPerecedero $bienModel;
    private MovimientoNoPerecedero $movimientoModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->bienModel = new BienNoPerecedero($pdo);
        $this->movimientoModel = new MovimientoNoPerecedero($pdo);
    }

    public function store(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'numero_bien'      => $_POST['numero_bien'] ?? '',
            'descripcion'      => $_POST['descripcion'] ?? '',
            'marca'            => $_POST['marca'] ?? '',
            'modelo'           => $_POST['modelo'] ?? '',
            'serial'           => $_POST['serial'] ?? '',
            'ubicacion_exacta' => $_POST['ubicacion_exacta'] ?? '', // Corregido
            'estado'           => $_POST['estado'] ?? 'disponible',
            'fecha_ingreso'    => $_POST['fecha_ingreso'] ?? date('Y-m-d') // Corregido
        ];

        if ($this->bienModel->create($data)) {
            header("Location: index.php?page=bienes&status=success");
            exit();
        }
    }
}
    /**
     * Lista todos los bienes para la vista principal
     */
    public function listar(): array {
        $bienes = $this->bienModel->getAll(); 
        return ['bienes' => $bienes];
    }

    /**
     * Procesa la creación de un nuevo bien
     */
    // models/BienNoPerecedero.php
    public function create(array $data): int {
        // CORRECCIÓN: Se agregó 'fecha_ingreso' a la consulta SQL
        $stmt = $this->pdo->prepare("INSERT INTO bienes_no_perecederos
            (numero_bien, descripcion, marca, modelo, color, serial, ubicacion_exacta, estado, fecha_ingreso)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");                
        
        $stmt->execute([
            $data['numero_bien'],
            $data['descripcion'],
            $data['marca'] ?? null,
            $data['modelo'] ?? null,
            $data['color'] ?? 'N/A', // Campo faltante en form
            $data['serial'] ?? null,
            $data['ubicacion_exacta'] ?? null,                
            $data['estado'] ?? 'disponible',
            $data['fecha_ingreso'] // Nuevo campo
        ]);                
        return (int) $this->pdo->lastInsertId();                
    }
    

    /**
     * Procesa un movimiento (asignación o devolución)
     */
    public function storeMovimiento(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bien_id = (int)$_POST['bien_id'];
            $tipo    = $_POST['tipo_movimiento'];
            
            // 1. Registrar el movimiento en el histórico
            $this->movimientoModel->create([
                'bien_id'         => $bien_id,
                'tipo_movimiento' => $tipo,
                'fecha_movimiento'=> $_POST['fecha_movimiento'],
                'area_destino'    => $_POST['area_destino'],
                'responsable'     => $_POST['responsable'],
                'observaciones'   => $_POST['observaciones'],
                'usuario'         => $_SESSION['usuario_nombre'] ?? 'admin'
            ]);

            // 2. Actualizar el estado actual del bien
            $nuevoEstado = ($tipo === 'asignacion') ? 'asignado' : 'disponible';
            $this->bienModel->updateEstado($bien_id, $nuevoEstado);

            header("Location: index.php?page=bienes&status=assigned");
            exit();
        }
    }
}