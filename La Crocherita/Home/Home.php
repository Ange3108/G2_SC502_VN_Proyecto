<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../Home/Home.php"); 
    exit(); 
}

// Conexión a la base de datos
require_once __DIR__ . '/../include/conexion.php';
$patrones = array();
$sql = "SELECT nombre_patron, imagen_url FROM patrones ORDER BY id_patron DESC LIMIT 10";
$result = $mysqli->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patrones[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Crocherita - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="../Comunidad/css/style.css" />

    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>
    <header class="d-flex justify-content-between align-items-center p-3 bg-light shadow-sm header-container">
    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
    </button>
    <div class="flex-grow-1 text-center">
        <h1 class="h4 mb-0">La Crocherita</h1>
    </div>
    <div style="width: 42px;"></div>
    </header>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menú de Navegación</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="home.php">Principal</a> </li>

                <li class="nav-item">
                    <a class="nav-link" href="../Comunidad/comunidad.html">Comunidad y Participación</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Mis_Proyectos_y_Recursos/Proyectos.html">Mis Proyectos y Recursos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Herramienta de Cálculo/calculadora.html">Herramientas De Cálculo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Configuración y Ayuda/configuracion.html">Configuración y Ayuda</a>
                </li>
            </ul>
        </div>
    </div>
    
    <main>
        <div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
            <div style="max-width: 450px; width: 100%; ">
                <div id="Carrusel" class="carousel slide">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#Carrusel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#Carrusel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#Carrusel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner">
                        <!--Primer objeto del carrusel-->

    <div id="Carrusel" class="carousel slide">
        <div class="carousel-indicators">
            <?php foreach ($patrones as $i => $patron): ?>
            <button type="button" data-bs-target="#Carrusel" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo ($i === 0 ? 'active' : ''); ?>" aria-current="<?php echo ($i === 0 ? 'true' : 'false'); ?>" aria-label="Slide <?php echo ($i+1); ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($patrones as $i => $patron): ?>
                    <?php
                        // Siempre usar la ruta absoluta del proyecto para las imágenes
                        $nombreArchivo = basename($patron['imagen_url']);
                        $img = '/G2_SC502_VN_Proyecto/La Crocherita/Mis_Proyectos_y_Recursos/partes/uploads/img/' . $nombreArchivo;
                    ?>
<div class="carousel-item <?php echo ($i === 0 ? 'active' : ''); ?>">
    <img src="<?php echo htmlspecialchars($img); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($patron['nombre_patron']); ?>">
    <div class="carousel-caption d-none d-md-block">
        <h5><?php echo htmlspecialchars($patron['nombre_patron']); ?></h5>
    </div>
</div>
            <?php endforeach; ?>
            <?php if (empty($patrones)): ?>
                <div class="carousel-item active">
                    <img src="./Imagenes/Pinguino.jpg" class="d-block w-100" alt="Sin patrones">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>No hay patrones disponibles</h5>
                    </div>
                </div>
            <?php endif; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#Carrusel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#Carrusel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="contenedor">
            <section class="Patrones">
                <h2>Patrones populares</h2>
                <h4>Pukka y Garu</h4>
                <div class="flex-contenido-img">
                    <ul>
                        <strong>Tiempo aproximado:</strong>
                        <li>15h</li>
                        <strong>Materiales:</strong>
                        <li>Hilo de algodón, abuelita</li>
                        <li>Ganchillo de 3.5 mm</li>
                    </ul>
                    <a href="../Mis_Proyectos_y_Recursos/partes/patrones." class="btnImagen">
                        <img src="./Imagenes/Pukka-Garu.jpg" alt="Pukka y Garu" class="imagen">
                    </a>
                </div>
                <h4>Muñeca mercedita</h4>
                <div class="flex-contenido-img">
                    <ul>
                        <strong>Tiempo aproximado:</strong>
                        <li>10h</li>
                        <strong>Materiales:</strong>
                        <li>Hilo de algodón, marca sinfonia</li>
                        <li>Ganchillo de 3.5 mm</li>
                    </ul>
                    <a href="../Mis_Proyectos_y_Recursos/partes/patrones.html" class="btnImagen">
                        <img src="./Imagenes/Mercedita.jpg" alt="Muñeca Mercedita" class="imagen">
                    </a>
                </div>
            </section>
            <section class="Eventos">
                <div class="eventos-grid">
                    <h2>Eventos</h2>
                    <button class="btnEvento" id="btnEvento" onclick="window.location.href='../Comunidad/secciones/eventos.html'">
                        <strong>Cafecito Del Día De Las Madres</strong>
                    </button>
                    <p>¡Este 22 de agosto trae algo para compartir!</p>

                    <button class="btnEvento" id="btnEvento" onclick="window.location.href='../Comunidad/secciones/eventos.html'">
                        <strong>Fiesta de la Alegría con temática del día de las Madres</strong>
                    </button>
                    <p>Con una cuota de 5 mil, sorprende a una persona con algo relacionado al crochet. ¡Que esperas para participar!</p>

                </div>
            </section>
        </div>

        <div class="contact-button-container">
            <a href="#" class="btn-contacto-profesora" data-bs-toggle="modal" data-bs-target="#contactaProfesoraModal">
                <i class="fas fa-chalkboard-teacher"></i> Contacto con la Profesora
            </a>
        </div>
    </main>

    <div class="modal fade" id="contactaProfesoraModal" tabindex="-1" aria-labelledby="contactaProfesoraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactaProfesoraModalLabel">Contacto de la Profesora</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="contact-info">
                        <p><i class="fas fa-user"></i> Profesora Vanessa</p>
                        <p><i class="fas fa-phone"></i> Teléfono: <a href="tel:+50687824009">+506 8782-4009</a></p>
                        <p><i class="fas fa-envelope"></i> Correo: <a href="mailto:info@lacrocherita.com">info@lacrocherita.com</a></p>
                        <p><i class="fas fa-globe"></i> Página: <a href="#">La Crocherita</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
</body>

</html>