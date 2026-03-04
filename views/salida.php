<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary py-4 text-center">
                <i class="bi bi-box-seam text-white display-4"></i>
                <h4 class="text-white fw-bold mt-2">Registrar Nuevo Lote</h4>
                <p class="text-white-50 small mb-0">Complete la información para ingresar stock</p>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="guardar_lote.php" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="producto" class="form-control border-0 bg-light" id="prod" placeholder="Producto" required>
                        <label for="prod" class="text-muted">Nombre del Producto</label>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <div class="form-floating">
                                <input type="number" name="cantidad" step="0.01" class="form-control border-0 bg-light" id="cant" placeholder="0.00" required>
                                <label for="cant" class="text-muted">Cantidad</label>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-floating">
                                <select name="unidad" class="form-select border-0 bg-light" id="unit" required>
                                    <option value="L">Litros</option>
                                    <option value="Kg">Kilos</option>
                                    <option value="Pz">Piezas</option>
                                </select>
                                <label for="unit" class="text-muted">Unidad</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="date" name="fecha_entrada" class="form-control border-0 bg-light" id="date" value="<?= date('Y-m-d') ?>" required>
                        <label for="date" class="text-muted">Fecha de Ingreso</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm mb-3">
                        Confirmar Registro
                    </button>
                    <a href="index.php" class="btn btn-link w-100 text-muted text-decoration-none">Volver al panel</a>
                </form>
            </div>
        </div>
    </div>
</div>