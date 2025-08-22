<?php
session_start();
require_once __DIR__ . '/../../include/conexion.php';

// --- Verificar si el usuario está logueado ---
$id_usuario = $_SESSION['id_usuario'] ?? 0;
if ($id_usuario == 0) {
    // Es mejor no mezclar JS con PHP así, pero mantengo tu lógica original
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

    $partes = explode("|", $horario);
    if (count($partes) !== 3) {
        echo "Error en el formato del horario.";
        exit();
    }

    $dia_semana = $partes[0];
    $hora_inicio = $partes[1];
    $hora_fin = $partes[2];
    $nombre_clase = "Clase de Crochet"; // Puedes cambiarlo dinámico si quieres

    // Verificar que el usuario no tenga ya reserva en ese mismo horario
    $stmt_check = $mysqli->prepare("SELECT 1 FROM Clases WHERE id_usuario = ? AND dia_semana = ? AND hora_inicio = ?");
    $stmt_check->bind_param("iss", $id_usuario, $dia_semana, $hora_inicio);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "Ya tienes reservada esta clase.";
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();

    // ===== INICIO DE LA LÓGICA MODIFICADA (TRANSACCIÓN) =====
    
    // Iniciar una transacción para asegurar que todo se ejecute correctamente
    $mysqli->begin_transaction();

    try {
        // 1. Insertar la nueva reserva en la tabla `Clases`
        $stmt_clase = $mysqli->prepare("INSERT INTO Clases (id_usuario, nombre_clase, dia_semana, hora_inicio, hora_fin) VALUES (?, ?, ?, ?, ?)");
        $stmt_clase->bind_param("issss", $id_usuario, $nombre_clase, $dia_semana, $hora_inicio, $hora_fin);
        $stmt_clase->execute();
        
        // Obtener el ID de la clase que acabamos de insertar. ¡Es crucial!
        $id_clase_nueva = $mysqli->insert_id;
        $stmt_clase->close();

        // 2. Generar los registros de asistencia para las próximas semanas
        $dias_map = [
            'Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miércoles' => 'Wednesday',
            'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sábado' => 'Saturday', 'Domingo' => 'Sunday'
        ];
        $dia_en_ingles = $dias_map[$dia_semana];

        // Preparar la consulta para insertar en `Asistencia` una sola vez
        $stmt_asistencia = $mysqli->prepare("INSERT INTO Asistencia (id_usuario, id_clase, fecha, estado) VALUES (?, ?, ?, 'asistió')");

        // Generar asistencia para lo que resta del mes actual y los siguientes 2 meses
        $fecha_inicio = new DateTime();
        $fecha_fin = (new DateTime())->modify('first day of +3 month')->modify('-1 day');

        // Buscar la primera ocurrencia del día de la semana
        $fecha_actual = new DateTime();
        if ($fecha_actual->format('l') !== $dia_en_ingles) {
            $fecha_actual->modify("next $dia_en_ingles");
        }

        // Iterar cada semana hasta la fecha final y crear un registro de asistencia
        while ($fecha_actual <= $fecha_fin) {
            $fecha_para_db = $fecha_actual->format('Y-m-d');
            
            $stmt_asistencia->bind_param("iis", $id_usuario, $id_clase_nueva, $fecha_para_db);
            $stmt_asistencia->execute();
            
            // Avanzar a la siguiente semana
            $fecha_actual->modify('+1 week');
        }
        $stmt_asistencia->close();

        // 3. Si todo salió bien, confirmar los cambios en la base de datos
        $mysqli->commit();

        echo "✅ ¡Clase reservada y asistencia generada exitosamente!";

    } catch (Exception $e) {
        // Si algo falló, revertir todos los cambios
        $mysqli->rollback();
        // Mostrar un mensaje de error genérico y registrar el error real para depuración
        error_log("Error en reserva: " . $e->getMessage());
        echo "❌ Error al procesar la reserva. Por favor, inténtelo de nuevo.";
    }

    // ===== FIN DE LA LÓGICA MODIFICADA =====

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
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
    $(document).ready(function(){
        $("#btnReservar").click(function(){
            let horario = $("#selectHorario").val();
            if(horario === ""){
                $("#mensajeReserva").text("⚠️ Debes seleccionar un horario.").css("color","red");
                return;
            }
            // Añadimos un estado de "cargando" para mejor feedback al usuario
            $("#btnReservar").prop('disabled', true).text('Procesando...');
            
            $.post("reservar_clase.php", {horario: horario}, function(respuesta){
                $("#mensajeReserva").html(respuesta).css("color", respuesta.includes("❌") ? "red" : "green");
            }).fail(function(){
                $("#mensajeReserva").text("❌ Error de conexión al procesar la reserva.").css("color","red");
            }).always(function() {
                // Habilitar el botón de nuevo al finalizar
                $("#btnReservar").prop('disabled', false).text('Reservar');
            });
        });
    });
    </script>
</body>
</html>