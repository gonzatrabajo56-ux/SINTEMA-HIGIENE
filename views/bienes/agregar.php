<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary py-4 text-center">
                <i class="bi bi-laptop text-white display-4"></i>
                <h4 class="text-white fw-bold mt-2">Registrar Nuevo Bien</h4>
                <p class="text-white-50 small mb-0">Agregar activo fijo al inventario</p>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="index.php?page=bienes&action=store" method="POST">
                    <?php require_once 'helpers/CsrfHelper.php'; echo CsrfHelper::tokenField(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="numero_bien" class="form-control border-0 bg-light" id="nbien" placeholder="Ej: B001" required>
                                <label for="nbien">N° Bien / Código</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="descripcion" class="form-control border-0 bg-light" id="desc" placeholder="Ej: Laptop" required>
                                <label for="desc">Descripción</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="marca" class="form-control border-0 bg-light" id="marca" placeholder="Ej: Dell">ahora quiero que las vistas sean profecionales y frescas con movimi
                                <label for="marca">Marca</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="modelo" class="form-control border-0 bg-light" id="modelo" placeholder="Ej: Latitude">
                                <label for="modelo">Modelo</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="serial" class="form-control border-0 bg-light" id="serial" placeholder="Ej: SN123">
                                <label for="serial">Serial</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="ubicacion_exacta" class="form-control border-0 bg-light" id="ubic" placeholder="Ej: Oficina 1">
                                <label for="ubic">Ubicación Exacta</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="area_asignada" class="form-select border-0 bg-light" id="area">
                                    <option value="">Seleccionar área...</option>
                                    <?php foreach ($data['areas'] ?? [] as $area): ?>
                                        <option value="<?= htmlspecialchars($area['nombre']) ?>"><?= htmlspecialchars($area['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="area">Área</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="responsable" class="form-control border-0 bg-light" id="resp" placeholder="Responsable">
                                <label for="resp">Responsable</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="estado" class="form-select border-0 bg-light" id="estado">
                                    <option value="disponible" selected>Disponible</option>
                                    <option value="mantenimiento">En Mantenimiento</option>
                                </select>
                                <label for="estado">Estado Inicial</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" name="fecha_ingreso" class="form-control border-0 bg-light" id="fecha" value="<?= date('Y-m-d') ?>" required>
                                <label for="fecha">Fecha de Ingreso</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-bold py-3 shadow-sm rounded-3">
                            <i class="bi bi-check-circle me-1"></i> Registrar Bien
                        </button>
                        <a href="index.php?page=bienes" class="btn btn-link text-muted text-decoration-none">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>