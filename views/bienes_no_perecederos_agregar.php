<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-box-seam me-2"></i>Registrar Nuevo Bien No Perecedero
                </h5>
                <small class="text-muted">Operador: <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></small>
            </div>
            <div class="card-body p-4">
                <form action="bienes_no_perecederos_guardar.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">N° Bien</label>
                            <input type="text" name="numero_bien" class="form-control" placeholder="Ej: B001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Descripción</label>
                            <input type="text" name="descripcion" class="form-control" placeholder="Ej: Computadora de escritorio" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Marca</label>
                            <input type="text" name="marca" class="form-control" placeholder="Ej: Dell">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Modelo</label>
                            <input type="text" name="modelo" class="form-control" placeholder="Ej: OptiPlex 3080">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Color</label>
                            <input type="text" name="color" class="form-control" placeholder="Ej: Negro">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Serial</label>
                            <input type="text" name="serial" class="form-control" placeholder="Ej: SN123456789">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ubicación Exacta</label>
                            <input type="text" name="ubicacion_exacta" class="form-control" placeholder="Ej: Oficina 101, Estante 2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado Inicial</label>
                            <select name="estado" class="form-select">
                                <option value="disponible" selected>Disponible</option>
                                <option value="asignado">Asignado</option>
                                <option value="mantenimiento">En Mantenimiento</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Registrar Bien
                        </button>
                        <a href="bienes_no_perecederos.php" class="btn btn-light text-muted">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>