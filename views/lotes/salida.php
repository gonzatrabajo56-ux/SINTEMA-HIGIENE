<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-warning py-4 text-center">
                <i class="bi bi-box-arrow-right text-dark display-4"></i>
                <h4 class="text-dark fw-bold mt-2">Registrar Salida</h4>
                <p class="text-dark-50 small mb-0">Consumo de material: <?= htmlspecialchars($data['lote']['nombre_producto'] ?? 'Producto') ?></p>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="index.php?page=lotes&action=registerSalida" method="POST">
                    <?php require_once 'helpers/CsrfHelper.php'; echo CsrfHelper::tokenField(); ?>
                    <input type="hidden" name="lote_id" value="<?= $data['lote']['id'] ?? '' ?>">
                    
                    <div class="form-floating mb-3">
                        <input type="number" name="cantidad_uso" step="0.01" max="<?= $data['lote']['cantidad_actual'] ?? 0 ?>" class="form-control border-0 bg-light" id="cant" placeholder="0.00" required>
                        <label for="cant" class="text-muted">Cantidad a Usar (Max: <?= $data['lote']['cantidad_actual'] ?? 0 ?>)</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select name="area" class="form-select border-0 bg-light" id="area" required>
                            <option value="">Seleccionar área...</option>
                            <?php foreach ($data['areas'] ?? [] as $area): ?>
                                <option value="<?= htmlspecialchars($area['nombre']) ?>"><?= htmlspecialchars($area['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="area" class="text-muted">Área de Destino</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" name="responsable" class="form-control border-0 bg-light" id="resp" placeholder="Responsable" required>
                        <label for="resp" class="text-muted">Responsable</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="date" name="fecha_salida" class="form-control border-0 bg-light" id="date" value="<?= date('Y-m-d') ?>" required>
                        <label for="date" class="text-muted">Fecha de Salida</label>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-3 shadow-sm mb-3">
                        Confirmar Salida
                    </button>
                    <a href="index.php?page=lotes" class="btn btn-link w-100 text-muted text-decoration-none">Volver</a>
                </form>
            </div>
        </div>
    </div>
</div>