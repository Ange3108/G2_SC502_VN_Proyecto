<?php
session_start();
require_once __DIR__ . '/../../include/conexion.php';

// --- Verificar si el usuario está logueado ---
$id_usuario = $_SESSION['id_usuario'] ?? 0;
if ($id_usuario == 0) {
    echo "<script>alert('Debe iniciar sesión para reservar una clase');window.location='../../Login/Login.html';</script>";
    exit();
}

// --- Procesar reserva cuando venga por POST (AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $horario = $_POST['horario'] ?? '';

    if (empty($horario)) {
        echo "Error: Debe seleccionar un horario.";
        exit();
    }

    // El formato esperado es algo como "Lunes|18:00|20:00"
    $partes = explode("|", $horario);
    if (count($partes) !== 3) {
        echo "Error en el formato del horario.";
        exit();
    }

    $dia_semana = $partes[0];
    $hora_inicio = $partes[1];
    $hora_fin = $partes[2];
    $nombre_clase = "Clase de Crochet"; // puedes cambiarlo dinámico si quieres

    // Verificar que el usuario no tenga ya reserva en ese mismo horario
    $stmt = $mysqli->prepare("SELECT 1 FROM Clases WHERE id_usuario = ? AND dia_semana = ? AND hora_inicio = ?");
    $stmt->bind_param("iss", $id_usuario, $dia_semana, $hora_inicio);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Ya tienes reservada esta clase.";
        exit();
    }
    $stmt->close();

    // Insertar la nueva reserva
    $stmt = $mysqli->prepare("
        INSERT INTO Clases (id_usuario, nombre_clase, dia_semana, hora_inicio, hora_fin)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $id_usuario, $nombre_clase, $dia_semana, $hora_inicio, $hora_fin);

    if ($stmt->execute()) {
        echo "✅ Clase reservada exitosamente.";
    } else {
        echo "❌ Error al reservar: " . $stmt->error;
    }
    $stmt->close();
    $mysqli->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reservar Clase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
    <!-- Mantengo tu diseño -->
    <header class="d-flex justify-content-between align-items-center p-3 bg-light shadow-sm">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="h4 mb-0">La Crocherita</h1>
        <div></div>
    </header>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menú de Navegación</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item"><a class="nav-link" href="../../Home/Home.php">Principal</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Comunidad/comunidad.html">Comunidad</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Mis_Proyectos_y_Recursos/Proyectos.html">Proyectos</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Herramienta%20de%20Cálculo/calculadora.html">Herramientas</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Configuración%20y%20Ayuda/configuracion.html">Configuración</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Login/Login.html">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>

    <main class="seccion-clases">
        <h2 class="titulo-pagina">Reservá tu clase semanal de crochet 🧵</h2>

        <div class="horario-card">
            <h3>Seleccioná tu horario</h3>
            <select id="selectHorario" class="form-select mb-3">
                <option value="">-- Selecciona un horario --</option>
                <option value="Lunes|18:00|20:00">Lunes 6pm - 8pm</option>
                <option value="Miércoles|15:00|17:00">Miércoles 3pm - 5pm</option>
                <option value="Sábado|10:00|12:00">Sábado 10am - 12pm</option>
            </select>
            <button id="btnReservar" class="btn btn-primary">Reservar</button>
            <p id="mensajeReserva" class="mt-3"></p>
        </div>

        <p class="mensaje-sinpe">
            📌 Recordá que el pago se realiza vía SINPE Móvil al número:<br />
            <strong>XXXX-XXXX</strong>
        </p>
    </main>

    <footer>
        <div class="frase">Síguenos en nuestras redes sociales!</div>
        <div class="iconitos">
            <span><i class="fab fa-facebook"></i></span>
            <span><i class="fab fa-instagram"></i></span>
            <span><i class="fab fa-pinterest"></i></span>
        </div>
        <p>&copy; 2025 La Crocherita. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
    $(document).ready(function(){
        $("#btnReservar").click(function(){
            let horario = $("#selectHorario").val();
            if(horario === ""){
                $("#mensajeReserva").text("⚠️ Debes seleccionar un horario.").css("color","red");
                return;
            }
            $.post("reservar_clase.php", {horario: horario}, function(respuesta){
                $("#mensajeReserva").text(respuesta).css("color","green");
            }).fail(function(){
                $("#mensajeReserva").text("❌ Error al procesar la reserva.").css("color","red");
            });
        });
    });
    </script>
</body>
</html>
