<?php
require_once 'config/db.php';

try {
    $pdo = Database::getConnection();
    $sql = file_get_contents('add_table.sql');
    $pdo->exec($sql);
    echo 'Tabla creada exitosamente';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>