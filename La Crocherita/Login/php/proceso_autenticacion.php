<?php
// LaCrocherita/Login/php/proceso_autenticacion.php
session_start();
require_once("../../Includes/conexion.php"); // Incluir ruta del archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? ''; // Determina si es 'login' o 'register'
    $mensaje = "";
    $tipo_alerta = "";

    switch ($action) {
        case 'login':
            $correo = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($correo) || empty($password)) {
                $mensaje = "Todos los campos son obligatorios.";
                $tipo_alerta = "danger";
            } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $mensaje = "Formato de correo inválido.";
                $tipo_alerta = "danger";
            } else {
                $stmt = $mysqli->prepare("SELECT id_usuario, nombre, correo, contraseña, rol FROM Usuarios WHERE correo = ?");
                if (!$stmt) {
                    $mensaje = "Error de base de datos al preparar la consulta de login.";
                    $tipo_alerta = "danger";
                    error_log("Error al preparar login: " . $mysqli->error);
                } else {
                    $stmt->bind_param("s", $correo);
                    $stmt->execute();
                    $resultado = $stmt->get_result();

                    if ($resultado->num_rows === 1) {
                        $usuario = $resultado->fetch_assoc();
                        if (password_verify($password, $usuario['contraseña'])) {
                            $_SESSION['user_id'] = $usuario['id_usuario'];
                            $_SESSION['user_name'] = $usuario['nombre'];
                            $_SESSION['user_email'] = $usuario['correo'];
                            $_SESSION['user_role'] = $usuario['rol'];
                            
                            // Redirigir al home principal del proyecto "La Crocherita"
                            // Asumiendo que Home.html es ahora Home.php o está en la raíz superior
                            header("Location: ../../Home/Home.php"); // cambiar Home.html a Home.php****
                            exit();
                        } else {
                            $mensaje = "Correo electrónico o contraseña incorrectos.";
                            $tipo_alerta = "danger";
                        }
                    } else {
                        $mensaje = "Correo electrónico o contraseña incorrectos.";
                        $tipo_alerta = "danger";
                    }
                    $stmt->close();
                }
            }
            break;

        case 'register':
            $nombre = $_POST['name'] ?? '';
            $correo = $_POST['emailRegister'] ?? '';
            $password = $_POST['passwordRegister'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';
            $rol = $_POST['role'] ?? 'estudiante'; // Por defecto, es 'estudiante' si no se especifica

            if (empty($nombre) || empty($correo) || empty($password) || empty($confirmPassword)) {
                $mensaje = "Todos los campos son obligatorios.";
                $tipo_alerta = "danger";
            } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $mensaje = "Formato de correo inválido.";
                $tipo_alerta = "danger";
            } elseif ($password !== $confirmPassword) {
                $mensaje = "Las contraseñas no coinciden.";
                $tipo_alerta = "danger";
            } else {
                // Verificar si el correo ya existe
                $stmt_check = $mysqli->prepare("SELECT COUNT(*) FROM Usuarios WHERE correo = ?");
                if (!$stmt_check) {
                    $mensaje = "Error de base de datos al verificar el correo.";
                    $tipo_alerta = "danger";
                    error_log("Error al preparar verificación de registro: " . $mysqli->error);
                } else {
                    $stmt_check->bind_param("s", $correo);
                    $stmt_check->execute();
                    $stmt_check->bind_result($count);
                    $stmt_check->fetch();
                    $stmt_check->close();

                    if ($count > 0) {
                        $mensaje = "Este correo electrónico ya está registrado.";
                        $tipo_alerta = "warning";
                    } else {
                        $hash_pass = password_hash($password, PASSWORD_DEFAULT);
                        $fecha_registro = date('Y-m-d'); // Fecha actual

                        $stmt = $mysqli->prepare("INSERT INTO Usuarios (nombre, correo, contraseña, rol, fecha_registro) VALUES (?, ?, ?, ?, ?)");
                        if (!$stmt) {
                            $mensaje = "Error de base de datos al preparar el registro.";
                            $tipo_alerta = "danger";
                            error_log("Error al preparar inserción de registro: " . $mysqli->error);
                        } else {
                            $stmt->bind_param("sssss", $nombre, $correo, $hash_pass, $rol, $fecha_registro);
                            $stmt->execute();

                            if ($stmt->sqlstate == '00000') {
                                $mensaje = "¡Registro exitoso! Redirigiendo al inicio de sesión...";
                                $tipo_alerta = "success";
                                // Redirige al login después de un registro exitoso
                                header("Location: ../html/Login.html?register=success");
                                exit();
                            } else {
                                $mensaje = "Error al crear la cuenta. Por favor, inténtalo de nuevo. Código: " . $stmt->sqlstate;
                                $tipo_alerta = "danger";
                            }
                            $stmt->close();
                        }
                    }
                }
            }
            break;

        case 'forgot_password':
            // solo se implementará la lógica del mensaje de confirmación como el JS actual.
            $emailRecovery = $_POST['email'] ?? '';
            if (!filter_var($emailRecovery, FILTER_VALIDATE_EMAIL)) {
                $mensaje = "Por favor, ingresa un correo electrónico válido.";
                $tipo_alerta = "danger";
            } else {
                // Aquí iría la lógica para enviar el correo de recuperación
                // Por ahora, solo confirmamos la recepción.
                $mensaje = "Si el correo electrónico '{$emailRecovery}' está registrado, recibirás instrucciones para restablecer tu contraseña en breve. Por favor, revisa tu bandeja de entrada y la carpeta de spam.";
                $tipo_alerta = "info"; // Usamos 'info' para un mensaje no-error
            }
            break;

        default:
            $mensaje = "Acción no válida.";
            $tipo_alerta = "danger";
            break;
    }
    // Devolver el mensaje para js
    echo json_encode(['message' => $mensaje, 'type' => $tipo_alerta]);
    $mysqli->close(); // Cerrar conexión al final del script
    exit();
} else {
    // Si se accede directamente a este archivo sin POST, redirigir
    header("Location: ../html/Login.html");
    exit();
}
?>