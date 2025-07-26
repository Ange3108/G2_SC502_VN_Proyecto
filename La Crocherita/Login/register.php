<?php

require_once("../include/conexion.php");

$mensaje = ""; // Variable para almacenar mensajes de éxito o error

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Recoger y satear los datos del formulario
    $nombre = trim($_POST['name'] ?? '');
    $email = trim($_POST['emailRegister'] ?? '');
    $password = $_POST['passwordRegister'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // 1. Validacion campos vacíos
    if (empty($nombre) || empty($email) || empty($password) || empty($confirmPassword)) {
        $mensaje = "<div class='alert alert-danger'>Todos los campos son obligatorios.</div>";
    } 
    // 2. Validar formato de email
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<div class='alert alert-danger'>El formato del correo electrónico es inválido.</div>";
    } 
    // 3. Validar que las contraseñas coincidan
    else if ($password !== $confirmPassword) {
        $mensaje = "<div class='alert alert-danger'>Las contraseñas no coinciden.</div>";
    } 
    // 4. Validar longitud de contraseña (ej. mínimo 6 caracteres)
    else if (strlen($password) < 3) {
        $mensaje = "<div class='alert alert-danger'>La contraseña debe tener al menos 6 caracteres.</div>";
    }
    else {
        // 5. Verificar si el correo ya existe en la base de datos
        $stmt = $mysqli->prepare("SELECT id_usuario FROM Usuarios WHERE correo = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result(); // Almacenar el resultado para poder usar num_rows

        if ($stmt->num_rows > 0) {
            $mensaje = "<div class='alert alert-danger'>Este correo electrónico ya está registrado.</div>";
        } else {
            // 6. Cifrar la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 7. Insertar nuevo usuario en la base de datos
            $stmt->close();
           
            $stmt = $mysqli->prepare("INSERT INTO Usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $email, $hashed_password);

            if ($stmt->execute()) {
                // Registro exitoso, redirigir a la página de login con un mensaje
                header("Location: Login.php?registro=exito");
                exit();
            } else {
                $mensaje = "<div class='alert alert-danger'>Error al registrar el usuario. Por favor, inténtalo de nuevo.</div>";
            }
        }
        $stmt->close();
    }
    $mysqli->close(); // Cerrar la conexión después de todas las operaciones
}

// Verificar si se viene de un registro exitoso (desde Login.php)
if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
    $mensaje = "<div class='alert alert-success'>¡Registro exitoso! Ya puedes iniciar sesión.</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro - La Crocherita</title>
    <link
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link rel="stylesheet" href="CSS/style.css" />
  </head>
  <body>
    <div
      class="container d-flex justify-content-center align-items-center min-vh-100"
    >
      <div class="card p-4 shadow-lg w-100" style="max-width: 400px">
        <h3 class="card-title text-center mb-4">Regístrate</h3>
        <?php if (!empty($mensaje)): ?>
            <?php echo $mensaje; ?>
        <?php endif; ?>
        
        <form id="registerForm" method="POST">
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input
              type="text"
              class="form-control"
              id="name"
              name="name"
              placeholder="Nombre completo"
              required
            />
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text"
              ><i class="fas fa-envelope"></i
            ></span>
            <input
              type="email"
              class="form-control"
              id="emailRegister"
              name="emailRegister"
              placeholder="Correo electrónico"
              required
            />
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input
              type="password"
              class="form-control"
              id="passwordRegister"
              name="passwordRegister"
              placeholder="Contraseña"
              required
            />
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input
              type="password"
              class="form-control"
              id="confirmPassword"
              name="confirmPassword"
              placeholder="Confirmar contraseña"
              required
            />
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-info text-white w-100">
              Crear cuenta
            </button>
          </div>
        </form>
        
        <p class="text-center mt-3">
          ¿Ya tienes una cuenta? <a href="Login.php">Inicia sesión aquí</a>
        </p>
        <div class="d-flex justify-content-between mt-3">
          <button
            onclick="window.history.back()"
            class="btn btn-secondary btn-sm"
          >
            Regresar
          </button>
        </div>
      </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    </body>
</html>