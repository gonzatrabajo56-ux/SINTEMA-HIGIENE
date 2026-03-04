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

    /**
     * Procesa el guardado de un nuevo bien
     */
    public function store(array $data = []): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            // Validar CSRF
            require_once 'helpers/CsrfHelper.php';
            if (!CsrfHelper::validateToken($data['csrf_token'] ?? '')) {
                die("Token CSRF inválido");
            }

            $data = [
                'numero_bien'      => $data['numero_bien'] ?? '',
                'descripcion'      => $data['descripcion'] ?? '',
                'marca'            => $data['marca'] ?? '',
                'modelo'           => $data['modelo'] ?? '',
                'serial'           => $data['serial'] ?? '',
                'ubicacion_exacta' => $data['ubicacion_exacta'] ?? '',
                'color'            => $data['color'] ?? '',
                'area_asignada'    => $data['area_asignada'] ?? '',
                'responsable'      => $data['responsable'] ?? '',
                'estado'           => $data['estado'] ?? 'disponible',
                'fecha_ingreso'    => $data['fecha_ingreso'] ?? date('Y-m-d')
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
     * Procesa un movimiento (asignación o devolución)
     */
    public function storeMovimiento(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            // Validar CSRF
            require_once 'helpers/CsrfHelper.php';
            if (!CsrfHelper::validateToken($data['csrf_token'] ?? '')) {
                die("Token CSRF inválido");
            }
            
            // Validaciones
            $bien_id = (int)$data['bien_id'];
            $tipo = $data['tipo_movimiento'] ?? '';
            $area_destino = $data['area_destino'] ?? '';
            $responsable = $data['responsable'] ?? '';
            
            // Validar que el ID del bien sea válido
            if ($bien_id <= 0) {
                header("Location: index.php?page=bienes&status=error&message=" . urlencode("ID de bien inválido"));
                exit();
            }
            
            // Validar tipo de movimiento
            if (!in_array($tipo, ['asignacion', 'devolucion'])) {
                header("Location: index.php?page=bienes&status=error&message=" . urlencode("Tipo de movimiento inválido"));
                exit();
            }
            
            // Validar que el área esté seleccionada para asignaciones
            if ($tipo === 'asignacion' && empty($area_destino)) {
                header("Location: index.php?page=bienes&status=error&message=" . urlencode("Debe seleccionar un área para la asignación"));
                exit();
            }
            
            // Validar que el responsable esté presente para asignaciones
            if ($tipo === 'asignacion' && empty($responsable)) {
                header("Location: index.php?page=bienes&status=error&message=" . urlencode("Debe ingresar un responsable para la asignación"));
                exit();
            }
            
            // Obtener datos actuales del bien
            $bienActual = $this->bienModel->getById($bien_id);
            if (!$bienActual) {
                header("Location: index.php?page=bienes&status=error&message=" . urlencode("Bien no encontrado"));
                exit();
            }
            
            // Validar que no se pueda asignar un bien ya asignado (a menos que sea una reasignación)
            if ($tipo === 'asignacion' && $bienActual['estado'] === 'asignado' && empty($data['reasignar'])) {
                header("Location: index.php?page=bienes&status=error&message=" . urlencode("El bien ya está asignado. Use la opción de reasignación."));
                exit();
            }
            
            // 1. Registrar el movimiento en el histórico
            $this->movimientoModel->create([
                'bien_id'         => $bien_id,
                'tipo_movimiento' => $tipo,
                'area_anterior'   => $bienActual['area_asignada'] ?? null,
                'area_nueva'      => ($tipo === 'asignacion') ? $area_destino : null,
                'responsable_anterior' => $bienActual['responsable'] ?? null,
                'responsable_nuevo' => ($tipo === 'asignacion') ? $responsable : null,
                'usuario_registro' => $_SESSION['usuario_nombre'] ?? 'Sistema',
                'observaciones'   => $data['observaciones'] ?? null
            ]);

            // 2. Actualizar el estado Y el área del bien
            $nuevoEstado = ($tipo === 'asignacion') ? 'asignado' : 'disponible';
            $nuevaArea = ($tipo === 'asignacion') ? $area_destino : null;
            $nuevoResponsable = ($tipo === 'asignacion') ? $responsable : null;
            
            $this->bienModel->updateAreaYEstado($bien_id, $nuevaArea, $nuevoResponsable, $nuevoEstado);

            header("Location: index.php?page=bienes&status=assigned");
            exit();
        }
    }

    /**
     * Obtiene un bien por su ID para edición
     */
    public function edit(int $id): ?array {
        return $this->bienModel->getById($id);
    }

    /**
     * Actualiza los datos de un bien
     */
    public function update(int $id, array $data): void {
        // Validar CSRF
        require_once 'helpers/CsrfHelper.php';
        if (!CsrfHelper::validateToken($data['csrf_token'] ?? '')) {
            die("Token CSRF inválido");
        }
        
        // Validar ID
        if ($id <= 0) {
            header("Location: index.php?page=bienes&status=error&message=" . urlencode("ID inválido"));
            exit();
        }
        
        // Validar datos requeridos
        if (empty($data['numero_bien']) || empty($data['descripcion'])) {
            header("Location: index.php?page=bienes&status=error&message=" . urlencode("Número de bien y descripción son requeridos"));
            exit();
        }
        
        // Verificar que el bien exista
        $bien = $this->bienModel->getById($id);
        if (!$bien) {
            header("Location: index.php?page=bienes&status=error&message=" . urlencode("Bien no encontrado"));
            exit();
        }
        
        // Preparar datos para actualizar
        $updateData = [
            'numero_bien' => trim($data['numero_bien']),
            'descripcion' => trim($data['descripcion']),
            'marca' => $data['marca'] ?? '',
            'modelo' => $data['modelo'] ?? '',
            'serial' => $data['serial'] ?? '',
            'color' => $data['color'] ?? '',
            'ubicacion_exacta' => $data['ubicacion_exacta'] ?? '',
            'area_asignada' => $data['area_asignada'] ?? '',
            'responsable' => $data['responsable'] ?? '',
            'estado' => $data['estado'] ?? 'disponible',
            'fecha_ingreso' => $data['fecha_ingreso'] ?? date('Y-m-d')
        ];
        
        $this->bienModel->update($id, $updateData);
        header("Location: index.php?page=bienes&status=updated");
        exit();
    }
}
