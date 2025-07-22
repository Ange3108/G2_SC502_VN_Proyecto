<?php
// php/proceso_autenticacion.php
session_start(); // Iniciar la sesión al principio del script

require_once '../includes/conexion.php'; // Incluir la conexión MySQLi
require_once '../includes/funciones.php'; // Incluir tus funciones

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'login':
                handleLogin($mysqli); // se pasa $mysqli a la función
                break;
            case 'register':
                handleRegister($mysqli); // se pasa $mysqli a la función
                break;
            case 'forgot_password':
                handleForgotPassword();
                break;
            default:
                header("Location: ../Login/Login.html?error=invalid_action");
                exit();
        }
    } else {
        header("Location: ../Login/Login.html?error=no_action");
        exit();
    }
} else {
    // Si se intentara acceder directamente al archivo sin POST
    header("Location: ../Login/Login.html");
    exit();
}

/**
 * Maneja el proceso de inicio de sesión.
 * @param mysqli $mysqli Objeto de conexión a la base de datos.
 */
function handleLogin($mysqli) {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: ../Login/Login.html?error=empty_fields");
        exit();
    }

    // Preparar la consulta para evitar inyección SQL
    $stmt = $mysqli->prepare("SELECT id_usuario, nombre, correo, contraseña, rol FROM Usuarios WHERE correo = ?");
    if (!$stmt) {
        error_log("Error al preparar login: " . $mysqli->error);
        header("Location: ../Login/Login.html?error=db_error");
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result(); // Obtener los resultados
    $user = $result->fetch_assoc(); // Obtener la fila como array
    $stmt->close();

    if ($user && password_verify($password, $user['contraseña'])) {
        // Contraseña correcta, iniciar sesión
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_email'] = $user['correo'];
        $_SESSION['user_role'] = $user['rol'];

        header("Location: ../Home/Home.html"); // Redirigir al home
        exit();
    } else {
        header("Location: ../Login/Login.html?error=invalid_credentials");
        exit();
    }
}

/**
 * Maneja el proceso de registro de un nuevo usuario.
 * @param mysqli $mysqli Objeto de conexión a la base de datos.
 */
function handleRegister($mysqli) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        header("Location: ../Login/register.html?error=empty_fields");
        exit();
    }

    if ($password !== $confirmPassword) {
        header("Location: ../Login/register.html?error=password_mismatch");
        exit();
    }

    // Validar formato de email
    if (!$email) {
        header("Location: ../Login/register.html?error=invalid_email_format");
        exit();
    }

    // Verificar si el correo ya existe (usando MySQL)
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM Usuarios WHERE correo = ?");
    if (!$stmt) {
        error_log("Error al preparar verificación de email: " . $mysqli->error);
        header("Location: ../Login/register.html?error=db_error");
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count); // Enlazar el resultado a una variable
    $stmt->fetch(); // Obtener el resultado
    $stmt->close();

    if ($count > 0) {
        header("Location: ../Login/register.html?error=email_exists");
        exit();
    }

    // Usar función registrarUsuario desde includes/funciones.php
    if (registrarUsuario($mysqli, $name, $email, $password)) {
        header("Location: ../Login/Login.html?register=success");
        exit();
    } else {
        header("Location: ../Login/register.html?error=registration_failed");
        exit();
    }
}


function handleForgotPassword() {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
    header("Location: ../Login/contra_olvidada.html?status=sent&email=" . urlencode($email));
    exit();
}
?>