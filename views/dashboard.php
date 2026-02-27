<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard Principal</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3 shadow">
            <div class="card-body">
                <h5 class="card-title">Productos Activos</h5>
                <h2><?php echo $data['total_productos']; ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-warning mb-3 shadow">
            <div class="card-body">
                <h5 class="card-title">Bajo Stock</h5>
                <h2><?php echo $data['bajo_stock']; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mt-4">
    <div class="card-header">Últimos Lotes Ingresados</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad Actual</th>
                    <th>Unidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['lotes'] as $lote): ?>
                <tr>
                    <td><?php echo $lote['nombre_producto']; ?></td>
                    <td><?php echo $lote['cantidad_actual']; ?></td>
                    <td><?php echo $lote['unidad']; ?></td>
                    <td>
                        <span class="badge bg-<?php echo $lote['estado'] == 'activo' ? 'success' : 'danger'; ?>">
                            <?php echo $lote['estado']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>