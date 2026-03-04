<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-success py-4 text-center">
                <i class="bi bi-arrow-left-right text-white display-4"></i>
                <h4 class="text-white fw-bold mt-2">Gestionar Movimiento</h4>
                <p class="text-white-50 small mb-0">
                    Bien: <strong><?= htmlspecialchars($data['bien']['descripcion']) ?></strong> 
                    (N°: <?= htmlspecialchars($data['bien']['numero_bien']) ?>)
                </p>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="index.php?page=bienes&action=storeMovimiento" method="POST">
                    <?php require_once 'helpers/CsrfHelper.php'; echo CsrfHelper::tokenField(); ?>
                    <input type="hidden" name="bien_id" value="<?= $data['bien']['id'] ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="tipo_movimiento" class="form-select border-0 bg-light" id="tipo" required>
                                    <option value="asignacion">Asignación</option>
                                    <option value="devolucion">Devolución / Mantenimiento</option>
                                </select>
                                <label for="tipo">Tipo de Movimiento</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" name="fecha_movimiento" class="form-control border-0 bg-light" id="fecha" value="<?= date('Y-m-d') ?>" required>
                                <label for="fecha">Fecha del Movimiento</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="area_destino" class="form-select border-0 bg-light" id="area" required>
                                    <option value="">Seleccionar área...</option>
                                    <?php foreach ($data['areas'] ?? [] as $area): ?>
                                        <option value="<?= htmlspecialchars($area['nombre']) ?>"><?= htmlspecialchars($area['nombre']) ?></option>
                                    <?php endforeach; ?>
                                    <option value="Otro">Otro (especificar en observaciones)</option>
                                </select>
                                <label for="area">Área / Ubicación</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="responsable" class="form-control border-0 bg-light" id="resp" placeholder="Responsable">
                                <label for="resp">Responsable</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="observaciones" class="form-control border-0 bg-light" id="obs" style="height: 100px" placeholder="Observaciones"></textarea>
                                <label for="obs">Observaciones / Motivo</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success fw-bold py-3 shadow-sm rounded-3">
                            <i class="bi bi-save me-1"></i> Registrar Movimiento
                        </button>
                        <a href="index.php?page=bienes" class="btn btn-link text-muted text-decoration-none">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>