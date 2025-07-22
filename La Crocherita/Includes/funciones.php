<?php
// includes/funciones.php

/**
 * Este archivo contiene funciones reutilizables para interactuar con la base de datos
 * de este proyecto. Su propósito es centralizar operaciones comunes como
 * registrar usuarios, consultar patrones, obtener eventos, etc., de manera segura y organizada.
 *
 * Para usar estas funciones, simplemente incluí este archivo donde lo necesites:
 * include '../includes/funciones.php';
 *
 * Todas las funciones requieren que exista una conexión activa a la base de datos ($mysqli).
 */

/**
 * Registra un nuevo usuario en la base de datos.
 *
 * @param mysqli $mysqli Objeto de conexión a la base de datos.
 * @param string $nombre Nombre completo del usuario.
 * @param string $email Correo electrónico del usuario.
 * @param string $password (Se hasheará antes de guardar).
 * @return bool True si el registro fue exitoso, false en caso contrario.
 */
function registrarUsuario($mysqli, $nombre, $email, $password) {
    // CURDATE() es para obtener la fecha actual.
    $sql = "INSERT INTO Usuarios (nombre, correo, contraseña, rol, fecha_registro)
            VALUES (?, ?, ?, 'estudiante', CURDATE())"; // Rol por defecto 'estudiante'

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        error_log("Error al preparar la consulta de registro: " . $mysqli->error);
        return false; // No se pudo hacer la consulta
    }

    $hash = password_hash($password, PASSWORD_DEFAULT); // Hashear la contraseña

    // "sss" indica que se enlazan 3 strings: nombre, email, hash
    $stmt->bind_param("sss", $nombre, $email, $hash);

    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        error_log("Error al ejecutar la consulta de registro: " . $stmt->error);
        $stmt->close();
        return false;
    }
}

// Función para operaciones CRUD:
/*
function obtenerUsuarioPorEmail($mysqli, $email) {
    $stmt = $mysqli->prepare("SELECT id_usuario, nombre, correo, contraseña, rol FROM Usuarios WHERE correo = ?");
    if (!$stmt) return false;
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
*/
?>