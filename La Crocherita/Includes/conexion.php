<?php
// Control de conexión a base de datos para La Crocherita

// Activar reporte de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Datos de conexión
$host = 'localhost';
$usuario = 'root';
$contrasenia = ''; // Debe agregar la contraseña de su respectivo MySQL
$base_datos = 'la_crocherita';

// Crear conexión
$mysqli = new mysqli($host, $usuario, $contrasenia, $base_datos);

// Verificar conexión
if ($mysqli->connect_error) {
    echo "<div class='alert alert-danger'>Error en la conexión a base de datos</div>";
} else {
    $mysqli->set_charset('utf8mb4');
}
?>
