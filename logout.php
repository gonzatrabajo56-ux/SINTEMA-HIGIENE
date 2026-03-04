<?php
// 1. Iniciar la sesión para poder manipularla
session_start();

// 2. Limpiar todas las variables de sesión
session_unset();

// 3. Destruir la sesión físicamente en el servidor
session_destroy();

// 4. Forzar al navegador a limpiar el caché inmediatamente al salir
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// 5. Redirigir al formulario de acceso
header("Location: login.php");
exit();
?>