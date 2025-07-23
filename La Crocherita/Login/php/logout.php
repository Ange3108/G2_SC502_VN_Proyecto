<?php
// LaCrocherita/Login/php/logout.php
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión
header("Location: ../html/Login.html"); // Redirige al login
exit();
?>