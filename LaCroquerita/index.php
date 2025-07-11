<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$viewFile = "views/$page.php";

if (!file_exists($viewFile)) {
    $viewFile = "views/home.php"; // Fallback si no existe la vista
}

$view = $viewFile;
include 'layout.php';
?>
