<?php
require_once 'config/db.php';
try {
    $pdo = Database::getConnection();
    $result = $pdo->query('SHOW TABLES LIKE "bienes_no_perecederos"')->fetch();
    if ($result) {
        echo 'Tabla bienes_no_perecederos existe' . PHP_EOL;
    } else {
        echo 'Tabla bienes_no_perecederos NO existe' . PHP_EOL;
    }

    $result2 = $pdo->query('SHOW TABLES LIKE "movimientos_no_perecederos"')->fetch();
    if ($result2) {
        echo 'Tabla movimientos_no_perecederos existe' . PHP_EOL;
    } else {
        echo 'Tabla movimientos_no_perecederos NO existe' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>