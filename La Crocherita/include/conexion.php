<?php
// Control de conexión a base de datos para La Crocherita

// Activar reporte de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Datos de conexión
$host = '127.0.0.1';
$usuario = 'root';
$contrasenia = 'MaFERNANDA2023**'; // Debe agregar la contraseña de su respectivo MySQL
$base_datos = 'la_crocherita';
$port = 3306; 

// Crear conexión
$mysqli = new mysqli($host, $usuario, $contrasenia, $base_datos, $port);

// Verificar conexión
if ($mysqli->connect_error) {
    echo "<div class='alert alert-danger'>Error en la conexión a base de datos</div>";
} else {
    $mysqli->set_charset('utf8mb4');
}
?>
