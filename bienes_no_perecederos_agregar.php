<?php
require_once 'admin_check.php';
ob_start();
?>

<?php include 'views/bienes_no_perecederos_agregar.php'; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
?>