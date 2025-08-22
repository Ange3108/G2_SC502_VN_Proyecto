<?php
session_start();
require_once __DIR__ . '/../../include/conexion.php'; // Tu archivo de conexión

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Login/Login.html");
    exit;
}

// Verificar que se envíen datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id_usuario'];
    $nombre_clase = $_POST['nombre_clase'];
    $dia_semana = $_POST['dia_semana']; // E.g., 'Lunes', 'Martes'
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    // Iniciar una transacción para asegurar la integridad de los datos
    $mysqli->begin_transaction();

    try {
        // 1. Insertar la nueva clase en la tabla `Clases`
        $sql_clase = "INSERT INTO Clases (id_usuario, nombre_clase, dia_semana, hora_inicio, hora_fin) VALUES (?, ?, ?, ?, ?)";
        $stmt_clase = $mysqli->prepare($sql_clase);
        $stmt_clase->bind_param("issss", $id_usuario, $nombre_clase, $dia_semana, $hora_inicio, $hora_fin);
        $stmt_clase->execute();

        // Obtener el ID de la clase que acabamos de insertar
        $id_clase_nueva = $mysqli->insert_id;
        $stmt_clase->close();

        // 2. Calcular las fechas de clase y generar la asistencia
        
        // Mapear días de la semana en español a inglés para que PHP los entienda
        $dias_map = [
            'Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miércoles' => 'Wednesday',
            'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sábado' => 'Saturday', 'Domingo' => 'Sunday'
        ];
        $dia_en_ingles = $dias_map[$dia_semana];

        // Preparar la consulta para insertar en la tabla `Asistencia`
        $sql_asistencia = "INSERT INTO Asistencia (id_usuario, id_clase, fecha, estado) VALUES (?, ?, ?, 'asistió')";
        $stmt_asistencia = $mysqli->prepare($sql_asistencia);
        
        // Generar asistencia para lo que resta del mes actual y los siguientes 2 meses
        $fecha_inicio = new DateTime();
        // El último día del mes dentro de 2 meses.
        $fecha_fin = new DateTime();
        $fecha_fin->modify('first day of +3 month')->modify('-1 day');

        // Buscar la primera ocurrencia del día de la semana
        $fecha_actual = new DateTime();
        if ($fecha_actual->format('l') != $dia_en_ingles) {
            $fecha_actual->modify("next $dia_en_ingles");
        }

        // Iterar cada semana hasta la fecha final
        while ($fecha_actual <= $fecha_fin) {
            $fecha_para_db = $fecha_actual->format('Y-m-d');
            
            // Insertar el registro de asistencia para esta fecha
            $stmt_asistencia->bind_param("iis", $id_usuario, $id_clase_nueva, $fecha_para_db);
            $stmt_asistencia->execute();
            
            // Avanzar a la siguiente semana
            $fecha_actual->modify('+1 week');
        }

        $stmt_asistencia->close();
        
        // 3. Si todo salió bien, confirmar la transacción
        $mysqli->commit();

        echo "¡Clase registrada y asistencia generada exitosamente!";
        // Redirigir al usuario a la página de asistencia o a donde necesites
        header("Location: ../Asistencia/asistencia.php");
        exit;

    } catch (Exception $e) {
        // Si algo falló, revertir todos los cambios
        $mysqli->rollback();
        // Mostrar un mensaje de error
        error_log($e->getMessage()); // Es buena práctica guardar el error en un log
        echo "Error al registrar la clase. Por favor, inténtelo de nuevo.";
    }
}
?>