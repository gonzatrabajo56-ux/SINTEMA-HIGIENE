<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary py-4 text-center">
                <i class="bi bi-pencil-square text-white display-4"></i>
                <h4 class="text-white fw-bold mt-2">Editar Bien</h4>
                <p class="text-white-50 small mb-0">
                    Editando: <strong><?= htmlspecialchars($data['bien']['descripcion']) ?></strong> 
                    (N°: <?= htmlspecialchars($data['bien']['numero_bien']) ?>)
                </p>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="index.php?page=bienes&action=update" method="POST">
                    <?php require_once 'helpers/CsrfHelper.php'; echo CsrfHelper::tokenField(); ?>
                    <input type="hidden" name="id" value="<?= $data['bien']['id'] ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="numero_bien" class="form-control border-0 bg-light" id="numero" value="<?= htmlspecialchars($data['bien']['numero_bien']) ?>" required>
                                <label for="numero">Número de Bien</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" name="fecha_ingreso" class="form-control border-0 bg-light" id="fecha" value="<?= htmlspecialchars($data['bien']['fecha_ingreso']) ?>" required>
                                <label for="fecha">Fecha de Ingreso</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" name="descripcion" class="form-control border-0 bg-light" id="desc" value="<?= htmlspecialchars($data['bien']['descripcion']) ?>" required>
                                <label for="desc">Descripción</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="marca" class="form-control border-0 bg-light" id="marca" value="<?= htmlspecialchars($data['bien']['marca'] ?? '') ?>">
                                <label for="marca">Marca</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="modelo" class="form-control border-0 bg-light" id="modelo" value="<?= htmlspecialchars($data['bien']['modelo'] ?? '') ?>">
                                <label for="modelo">Modelo</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="serial" class="form-control border-0 bg-light" id="serial" value="<?= htmlspecialchars($data['bien']['serial'] ?? '') ?>">
                                <label for="serial">Serial</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="color" class="form-control border-0 bg-light" id="color" value="<?= htmlspecialchars($data['bien']['color'] ?? '') ?>">
                                <label for="color">Color</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="area_asignada" class="form-select border-0 bg-light" id="area">
                                    <option value="">Sin asignar</option>
                                    <?php foreach ($data['areas'] ?? [] as $area): ?>
                                        <option value="<?= htmlspecialchars($area['nombre']) ?>" <?= ($data['bien']['area_asignada'] ?? '') === $area['nombre'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($area['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="area">Área</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="responsable" class="form-control border-0 bg-light" id="resp" value="<?= htmlspecialchars($data['bien']['responsable'] ?? '') ?>">
                                <label for="resp">Responsable</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" name="ubicacion_exacta" class="form-control border-0 bg-light" id="ubicacion" value="<?= htmlspecialchars($data['bien']['ubicacion_exacta'] ?? '') ?>">
                                <label for="ubicacion">Ubicación Exacta</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-bold py-3 shadow-sm rounded-3">
                            <i class="bi bi-save me-1"></i> Guardar Cambios
                        </button>
                        <a href="index.php?page=bienes" class="btn btn-link text-muted text-decoration-none">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
