<?php
session_start();
require_once __DIR__ . '/../../include/conexion.php'; // Tu archivo de conexión

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Login/Login.html");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Traer todas las asistencias del usuario con la clase
$sql = "
    SELECT a.fecha, a.estado, c.nombre_clase
    FROM Asistencia a
    INNER JOIN Clases c ON a.id_clase = c.id_clase
    WHERE a.id_usuario = ?
";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$asistencias = [];
while ($row = $result->fetch_assoc()) {
    $asistencias[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia de Estudiantes - La Crocherita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/calendar.css" />
    <style>
        main.container {
            flex-grow: 1;
            padding-top: 20px;
            padding-bottom: 40px;
        }
    </style>
</head>

<body>
    <header class="d-flex justify-content-between align-items-center p-3 bg-light shadow-sm">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="h4 mb-0">La Crocherita</h1>
        <div></div>
    </header>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
        aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menú de Navegación</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item">
                    <a class="nav-link" href="../../Home/Home.php">Principal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../Comunidad/comunidad.html">Comunidad y Participación</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../Mis_Proyectos_y_Recursos/Proyectos.html">Mis Proyectos y
                        Recursos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                        href="../../Herramienta%20de%20C%C3%A1lculo/calculadora.html">Herramientas De Cálculo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                        href="../../Configuración%20y%20Ayuda/configuracion.html">Configuración y Ayuda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../Login/Login.html">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>

    <main class="container mt-5">
        <h2 class="titulo-pagina text-center mb-4">
            Asistencia de Estudiantes 🧶📅
        </h2>

        <div class="d-flex justify-content-center mb-4">
            <select id="mes-selector" class="form-select w-auto">
                <option value="6">Julio</option>
                <option value="7">Agosto</option>
                <option value="8">Septiembre</option>
            </select>
        </div>

        <div class="asistencia-wrapper d-flex justify-content-center gap-4">
            <div>
                <div class="calendar-header">
                    <div>Lunes</div>
                    <div>Martes</div>
                    <div>Miércoles</div>
                    <div>Jueves</div>
                    <div>Viernes</div>
                    <div>Sábado</div>
                    <div>Domingo</div>
                </div>

                <div id="calendar" class="calendar-grid"></div>
            </div>

            <aside class="resumen-asistencia">
                <p><strong>Plan:</strong> <span id="plan-nombre">-</span></p>
                <p><strong>Precio base:</strong> ₡<span id="precio-plan">0</span></p>
                <p><strong>Días asistidos:</strong> <span id="total-dias">0</span></p>
                <p><strong>Total a pagar:</strong> ₡<span id="total-pago">0</span></p>
            </aside>
        </div>
    </main>

    <footer>
        <div class="frase">Siguenos en nuestras redes sociales!</div>
        <div class="iconitos">
            <span>
                <a href="https://www.facebook.com/profile.php?id=100076225050581" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-facebook"></i>
                </a>
            </span>
            <span>
                <a href="https://www.tiktok.com/@lacrocherita" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-tiktok"></i>
                </a>
            </span>
        </div>
        <p>&copy; 2025 La Crocherita. Todos los derechos reservados.</p>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const asistencias = <?php echo json_encode($asistencias); ?>;
    </script>
    <script src="../js/asistencia.js"></script>
</body>

</html>
