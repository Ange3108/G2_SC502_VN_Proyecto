<?php
session_start();

require_once("../include/conexion.php"); 

$mensaje = ""; // Variable para almacenar mensajes de éxito o error

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validar datos
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Correo electrónico inválido.";
    } else {
        // Buscar si el correo existe en la base de datos 'la_crocherita'
        $stmt = $mysqli->prepare("SELECT id_usuario, nombre, correo, contraseña FROM Usuarios WHERE correo = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            // Verificar la contraseña
            if(password_verify($password, $usuario['contraseña'])){
                // Inicio de sesión exitoso
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['email'] = $usuario['correo'];
                
                // Redirección a Home/Home.php
                header("Location: ../Home/Home.php"); 
                exit();
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "Correo electrónico no registrado.";
        }
        $stmt->close();
    }
    $mysqli->close(); // Cerrar la conexión aquí, después de todas las operaciones de la DB.
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - La Crocherita</title>
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
        <h3 class="card-title text-center mb-4">Login</h3>
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger text-center" role="alert"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <form id="loginForm" method="POST">
          <div class="input-group mb-3">
            <span class="input-group-text"
              ><i class="fas fa-envelope"></i
            ></span>
            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              placeholder="Correo electrónico"
              required
            />
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input
              type="password"
              class="form-control"
              id="password"
              name="password"
              required
              placeholder="Contraseña"
            />
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-info text-white w-100">
              Iniciar sesión
            </button>
          </div>
        </form>
        
        <p class="text-center mt-3">
          ¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>
        </p>
      </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  </body>
</html>