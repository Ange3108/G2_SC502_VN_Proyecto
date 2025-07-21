<?php
/**
 * Este archivo contiene funciones reutilizables para interactuar con la base de datos
 * de este proyecto. Su propósito es centralizar operaciones comunes como
 * registrar usuarios, consultar patrones, obtener eventos, etc., de manera segura y organizada.
 * 
 * Para usar estas funciones, simplemente incluí este archivo donde lo necesites:
 *    include '../includes/funciones.php';
 *
 * Todas las funciones requieren que exista una conexión activa a la base de datos ($mysqli).
 */

function registrarUsuario($mysqli, $nombre, $email, $fecha_nac, $password) {
    $sql = "INSERT INTO Usuarios (nombre, correo, fecha_registro, contraseña, rol)
            VALUES (?, ?, CURDATE(), ?, 'estudiante')";
    
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        return false; // No se pudo preparar la consulta
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $nombre, $email, $hash);

    return $stmt->execute();
}

