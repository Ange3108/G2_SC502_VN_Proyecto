<?php
// LaCrocherita/Login/php/checkDeSesion.php
session_start();

if (!isset($_SESSION['user_id'])) {
    // No hay sesión activa, redirigir al login
    header("Location: ../Login/html/Login.html");
    exit();
}
// lógica para verificar roles, no se si dejarla...
// if ($_SESSION['user_role'] !== 'profesora' && $_SESSION['user_role'] !== 'estudiante') { ... }
?>