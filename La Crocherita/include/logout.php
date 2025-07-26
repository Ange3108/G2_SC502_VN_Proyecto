<?php
// LaCrocherita/Login/logout.php
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión
header("Location: ../Login/Login.php"); // Redirige al login
exit();
?>