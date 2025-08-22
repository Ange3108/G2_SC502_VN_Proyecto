<?php
session_start();

/*
// Verificar si el usuario está logueado
if (!isset($_SESSION['nombre']) || !isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/Login.php");
    exit();
}
*/

// Incluir conexión a la base de datos
require_once '../include/conexion.php';

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consulta'])) {
    $consulta = trim($_POST['consulta']);
    $id_usuario = $_SESSION['id_usuario'];
    
    if (!empty($consulta)) {
        try {
            // Preparar la consulta SQL
            $stmt = $mysqli->prepare("INSERT INTO consultas_ayuda (id_usuario, consulta) VALUES (?, ?)");
            $stmt->bind_param("is", $id_usuario, $consulta);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                $mensaje = 'Tu consulta ha sido enviada exitosamente. Nuestro equipo te contactará pronto.';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Hubo un error al enviar tu consulta. Por favor, inténtalo de nuevo.';
                $tipo_mensaje = 'error';
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $mensaje = 'Error del sistema. Por favor, inténtalo más tarde.';
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = 'Por favor, escribe tu consulta antes de enviar.';
        $tipo_mensaje = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ayuda - La Crocherita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        .ayuda-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
            min-height: calc(100vh - 200px);
        }
        
        .titulo-ayuda {
            color: #6b4c7a;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .ayuda-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border: 2px solid #f0d4f0;
        }
        
        .seccion-titulo {
            color: #6b4c7a;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .formulario-ayuda {
            margin-bottom: 2rem;
        }
        
        .input-ayuda {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            margin-bottom: 1rem;
            transition: border-color 0.3s ease;
            resize: vertical;
            min-height: 120px;
        }
        
        .input-ayuda:focus {
            border-color: #6b4c7a;
            outline: none;
            box-shadow: 0 0 0 3px rgba(107, 76, 122, 0.1);
        }
        
        .btn-enviar {
            background: linear-gradient(135deg, #6b4c7a, #8e6ba8);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            width: 100%;
        }
        
        .btn-enviar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(107, 76, 122, 0.3);
        }
        
        .btn-enviar:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .contacto-info {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .contacto-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f0d4f0;
            border-radius: 10px;
            border-left: 4px solid #6b4c7a;
        }
        
        .contacto-icono {
            font-size: 1.5rem;
            color: #6b4c7a;
            min-width: 30px;
        }
        
        .contacto-texto {
            flex: 1;
        }
        
        .contacto-label {
            font-weight: 600;
            color: #6b4c7a;
            margin-bottom: 0.3rem;
        }
        
        .contacto-valor {
            color: #7f8c8d;
            font-size: 0.95rem;
        }
        
        /* Alertas personalizadas */
        .alert-custom {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .ayuda-container {
                padding: 1rem;
            }
            
            .ayuda-card {
                padding: 1.5rem;
            }
            
            .titulo-ayuda {
                font-size: 1.5rem;
            }
            
            .contacto-item {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }
    </style>
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
                <li class="nav-item">
                    <a class="nav-link" href="../Home/Home.php">Principal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Comunidad/comunidad.html">Comunidad y Participación</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Mis_Proyectos_y_Recursos/Proyectos.html">Mis Proyectos y Recursos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Herramienta%20de%20C%C3%A1lculo/calculadora.html">Herramientas De Cálculo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Configuración y Ayuda/configuracion.html">Configuración y Ayuda</a>
                </li>
            </ul>
        </div>
    </div>

    <main class="ayuda-container">
        <h2 class="titulo-ayuda">Centro de Ayuda y Soporte</h2>
        
        <!-- Mostrar mensajes de resultado -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert-custom alert-<?php echo $tipo_mensaje; ?>">
                <i class="fas fa-<?php echo ($tipo_mensaje === 'success') ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulario de ayuda -->
        <div class="ayuda-card">
            <h3 class="seccion-titulo">
                <i class="fas fa-question-circle"></i>
                Enviar Consulta o Reporte
            </h3>
            <form class="formulario-ayuda" method="POST" action="">
                <textarea 
                    name="consulta" 
                    id="consultaTexto" 
                    class="input-ayuda" 
                    placeholder="Describe tu consulta, problema o sugerencia aquí. Nuestro equipo te responderá lo antes posible..."
                    required
                ></textarea>
                <button type="submit" class="btn-enviar">
                    <i class="fas fa-paper-plane"></i> Enviar Consulta
                </button>
            </form>
        </div>

        <!-- Información de contacto -->
        <div class="ayuda-card">
            <h3 class="seccion-titulo">
                <i class="fas fa-address-book"></i>
                Información de Contacto
            </h3>
            <div class="contacto-info">
                <div class="contacto-item">
                    <i class="fas fa-envelope contacto-icono"></i>
                    <div class="contacto-texto">
                        <div class="contacto-label">Correo de Soporte Técnico</div>
                        <div class="contacto-valor">soportecnicolacrocherita@gmail.com</div>
                    </div>
                </div>
                
                <div class="contacto-item">
                    <i class="fas fa-phone contacto-icono"></i>
                    <div class="contacto-texto">
                        <div class="contacto-label">Teléfono de Atención</div>
                        <div class="contacto-valor">+506 8888-9999</div>
                    </div>
                </div>
                
                <div class="contacto-item">
                    <i class="fas fa-clock contacto-icono"></i>
                    <div class="contacto-texto">
                        <div class="contacto-label">Horario de Atención</div>
                        <div class="contacto-valor">Lunes a Viernes: 8:00 AM - 6:00 PM</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer style="background-color: #f0d4f0; padding: 2rem 1rem; text-align: center; color: #6b4c7a; font-style: italic; margin-top: auto;">
        <div style="margin-bottom: 1rem; font-size: 1rem;">Síguenos en nuestras redes sociales!</div>
        <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 1rem;">
            <i class="fab fa-facebook" style="font-size: 1.5rem; color: #6b4c7a;"></i>
            <i class="fab fa-instagram" style="font-size: 1.5rem; color: #6b4c7a;"></i>
            <i class="fab fa-pinterest" style="font-size: 1.5rem; color: #6b4c7a;"></i>
        </div>
        <p style="margin: 0; font-size: 0.9rem;">&copy; 2025 La Crocherita. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>