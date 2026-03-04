<?php
declare(strict_types=1);

class LoteController {
    private PDO $pdo;
    private Lote $loteModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->loteModel = new Lote($pdo); // Asumiendo que tienes un modelo Lote
    }
    public function index(): array {
        // Obtenemos todos los lotes de la base de datos
        $lotes = $this->loteModel->getAll(); // O el método que uses para listar
        
        // Retornamos los datos para que el index.php los use
        return ['lotes' => $lotes];
    }
    public function store(array $data): void {
        require_once 'helpers/CsrfHelper.php';
        if (!CsrfHelper::validateToken($data['csrf_token'] ?? '')) {
            die("Token CSRF inválido");
        }
        
        $producto = trim($data['producto']);
        $cantidad = (float) $data['cantidad'];
        $unidad = $data['unidad'];
        $fecha_entrada = $data['fecha_entrada'];
        $usuario = $_SESSION['usuario_nombre'];

        $this->loteModel->create($producto, $cantidad, $unidad, $fecha_entrada, $usuario);
        header("Location: index.php?status=success");
        exit();
    }

    public function edit(int $id): ?array {
        return $this->loteModel->getById($id);
    }

    public function update(int $id, array $data): void {
        $producto = trim($data['nombre']);
        $unidad = $data['unidad'];

        $lote = $this->loteModel->getById($id);
        if (!$lote) {
            header("Location: index.php");
            exit();
        }

        $this->loteModel->update($id, $producto, $lote['cantidad_inicial'], $lote['cantidad_actual'], $unidad);
        header("Location: index.php?status=updated");
        exit();
    }

    public function deactivate(int $id): void {
        $this->loteModel->deactivate($id);
        header("Location: index.php?status=desactivado");
        exit();
    }

    public function registerSalida(array $data): void {
        require_once 'helpers/CsrfHelper.php';
        if (!CsrfHelper::validateToken($data['csrf_token'] ?? '')) {
            die("Token CSRF inválido");
        }
        
        $lote_id = (int) $data['lote_id'];
        $cantidad = (float) $data['cantidad_uso'];
        $area = $data['area'];
        $responsable = trim($data['responsable']);
        $fecha_salida = $data['fecha_salida'];

        // Mejora: Validación básica antes de procesar
        if ($cantidad <= 0) {
            header("Location: index.php?page=lotes&status=error_cantidad");
            exit();
        }

        try {
            $this->loteModel->registerSalida($lote_id, $cantidad, $area, $responsable, $fecha_salida);
            header("Location: index.php?page=lotes&status=success_out");
            exit();
        } catch (Exception $e) {
            // Manejar error de stock insuficiente u otro error del modelo
            header("Location: index.php?page=lotes&status=error&message=" . urlencode($e->getMessage()));
            exit();
        }
    }

    public function getForSalida(): array {
        return $this->loteModel->getAllForSalida();
    }
}
