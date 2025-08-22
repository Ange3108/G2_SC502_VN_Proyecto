<?php
// include/eventos_funciones.php
// Requiere que include/conexion.php defina $mysqli y set_charset('utf8mb4').

function eventos_listar_por_mes($mysqli, $anio, $mes) {
    $sql = "SELECT id_evento, nombre_evento, descripcion, fecha_evento
            FROM Eventos
            WHERE YEAR(fecha_evento) = ? AND MONTH(fecha_evento) = ?
            ORDER BY fecha_evento ASC";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("ii", $anio, $mes);
    $stmt->execute();
    return $stmt->get_result();
}

function eventos_crear($mysqli, $nombre, $desc, $fecha) {
    $sql = "INSERT INTO Eventos (nombre_evento, descripcion, fecha_evento)
            VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("sss", $nombre, $desc, $fecha);
    return $stmt->execute();
}

function eventos_actualizar($mysqli, $id, $nombre, $desc, $fecha) {
    $sql = "UPDATE Eventos SET nombre_evento = ?, descripcion = ?, fecha_evento = ?
            WHERE id_evento = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("sssi", $nombre, $desc, $fecha, $id);
    return $stmt->execute();
}

function eventos_eliminar($mysqli, $id) {
    $sql = "DELETE FROM Eventos WHERE id_evento = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
