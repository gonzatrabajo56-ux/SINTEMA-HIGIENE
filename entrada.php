<?php 
require_once 'admin_check.php'; 
ob_start(); 
?>

<?php include 'views/entrada.php'; ?>

<?php 
$content = ob_get_clean(); 
include 'layout.php'; 
?>