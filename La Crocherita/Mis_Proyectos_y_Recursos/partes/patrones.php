<?php
// 1. Incluir el archivo de conexión a la base de datos
require_once __DIR__ . '/../../include/conexion.php'; // Asegúrate que la ruta sea correcta


// =========== INICIO: LÓGICA PARA PROCESAR FORMULARIO DEL MODAL ===========
// Este bloque solo se ejecuta si se envía el formulario (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos de texto
    $nombre_patron = $_POST['nombre_patron'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $nivel_dificultad = $_POST['nivel_dificultad'] ?? '';
    $puntos_utilizados = $_POST['puntos_utilizados'] ?? '';
    $materiales = $_POST['materiales'] ?? '';
    
    $imagen_url_db = '';
    $pdf_url_db = '';
    $error_subida = false;

    // Manejar la subida de la IMAGEN
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $directorio_imagenes = 'uploads/imagenes/';
        if (!is_dir($directorio_imagenes)) { mkdir($directorio_imagenes, 0777, true); }
        $nombre_archivo_img = time() . '_' . basename($_FILES['imagen']['name']);
        $ruta_completa_img = $directorio_imagenes . $nombre_archivo_img;
        $tipo_archivo_img = strtolower(pathinfo($ruta_completa_img, PATHINFO_EXTENSION));
        $tipos_permitidos_img = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($tipo_archivo_img, $tipos_permitidos_img) && move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa_img)) {
            $imagen_url_db = $ruta_completa_img;
        } else {
            $error_subida = true;
        }
    }

    // Manejar la subida del PDF
    if (!$error_subida && isset($_FILES['pdf']) && $_FILES['pdf']['error'] === 0) {
        $directorio_pdfs = 'uploads/pdfs/';
        if (!is_dir($directorio_pdfs)) { mkdir($directorio_pdfs, 0777, true); }
        $nombre_archivo_pdf = time() . '_' . basename($_FILES['pdf']['name']);
        $ruta_completa_pdf = $directorio_pdfs . $nombre_archivo_pdf;
        $tipo_archivo_pdf = strtolower(pathinfo($ruta_completa_pdf, PATHINFO_EXTENSION));
        if ($tipo_archivo_pdf == 'pdf' && move_uploaded_file($_FILES['pdf']['tmp_name'], $ruta_completa_pdf)) {
            $pdf_url_db = $ruta_completa_pdf;
        } else {
            $error_subida = true;
        }
    }

    // Insertar en la Base de Datos solo si no hubo errores de subida
    if (!$error_subida) {
        $stmt = $mysqli->prepare("
            INSERT INTO patrones (nombre_patron, descripcion, imagen_url, nivel_dificultad, puntos_utilizados, materiales, pdf_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $nombre_patron, $descripcion, $imagen_url_db, $nivel_dificultad, $puntos_utilizados, $materiales, $pdf_url_db);
        if ($stmt->execute()) {
            // Redirigir a la misma página con un parámetro de éxito
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
            exit(); // Detener la ejecución del script es crucial después de redirigir
        }
        $stmt->close();
    }

    //validaciones
    if(empty($nombre_patron)){
        echo "El nombre del patrón es requerido";
        exit();
    }
    if(empty($descripcion)){
        echo "La descripción es requerida";
        exit();
    }
    if(empty($nivel_dificultad)){
        echo "El nivel de dificultad es requerido";
        exit();
    }
    if(empty($puntos_utilizados)){
        echo "Los puntos utilizados son requeridos";
        exit();
    }
    if(empty($materiales)){
        echo "Los materiales son requeridos";
        exit();
    }
    if(empty($imagen_url_db) && empty($pdf_url_db)){
        echo "Debe subir al menos una imagen o un PDF";
        exit();
    }
}
// =========== FIN: LÓGICA PARA PROCESAR FORMULARIO ===========


// 2. Preparar y ejecutar la consulta para obtener todos los patrones (TU CÓDIGO ORIGINAL INTACTO)
$query = "SELECT * FROM patrones ORDER BY nombre_patron ASC";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Patrones Digitales de Crochet</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header class="d-flex justify-content-between align-items-center p-3 bg-light shadow-sm">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
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
                <li class="nav-item"><a class="nav-link" href="../../Home/Home.php">Principal</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Comunidad/comunidad.html">Comunidad y Participación</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Mis_Proyectos_y_Recursos/Proyectos.html">Mis Proyectos y Recursos</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Herramienta%20de%20C%C3%A1lculo/calculadora.html">Herramientas De Cálculo</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Configuración%20y%20Ayuda/configuracion.html">Configuración y Ayuda</a></li>
                <li class="nav-item"><a class="nav-link" href="../../Login/Login.html">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
    
    <main>
        <div class="container py-5">
            <h2 class="titulo text-center">Patrones Digitales de Crochet</h2>

            <div class="text-end mb-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIngresarPatron">
                    <i class="fas fa-plus"></i> Agregar Nuevo Patrón
                </button>
            </div>
            <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ¡Patrón agregado exitosamente!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php
            // Tu bucle original para mostrar las tarjetas, sin modificaciones
            while ($patron = $result->fetch_assoc()):
            ?>
                <div class="tarjeta-patron">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <img src="<?= htmlspecialchars($patron['imagen_url']) ?>" alt="<?= htmlspecialchars($patron['nombre_patron']) ?>" class="imagen-patron">
                        </div>
                        <div class="col-md-8">
                            <h3><?= htmlspecialchars($patron['nombre_patron']) ?></h3>
                            <p><strong>Descripción:</strong> <?= htmlspecialchars($patron['descripcion']) ?></p>
                            <p><strong>Nivel:</strong> <?= htmlspecialchars($patron['nivel_dificultad']) ?></p>
                            <p><strong>Puntos utilizados:</strong> <?= htmlspecialchars($patron['puntos_utilizados']) ?></p>
                            <p><strong>Materiales:</strong> <?= htmlspecialchars($patron['materiales']) ?></p>
                            <?php if (!empty($patron['pdf_url'])): ?>
                                <a href="<?= htmlspecialchars($patron['pdf_url']) ?>" class="btn btn-primary" download>
                                    <i class="fas fa-download"></i> Descargar patrón PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php
            endwhile;
            ?>
        </div>
    </main>
    
    <footer>
        <div class="frase">Síguenos en nuestras redes sociales!</div>
        <div class="iconitos">
            <span><i class="fab fa-facebook"></i></span>
            <span><i class="fab fa-instagram"></i></span>
            <span><i class="fab fa-pinterest"></i></span>
        </div>
        <p>&copy; 2025 La Crocherita. Todos los derechos reservados.</p>
    </footer>

    <div class="modal fade" id="modalIngresarPatron" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Ingresar Nuevo Patrón</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" id="formIngresarPatron" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nombre_patron" class="form-label">Nombre del Patrón</label>
                            <input type="text" name="nombre_patron" id="nombre_patron" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="puntos_utilizados" class="form-label">Puntos Utilizados</label>
                                <input type="text" name="puntos_utilizados" id="puntos_utilizados" class="form-control" placeholder="Ej: Punto bajo, punto alto, anillo mágico">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nivel_dificultad" class="form-label">Nivel de Dificultad</label>
                                <select name="nivel_dificultad" id="nivel_dificultad" class="form-select" required>
                                    <option value="" disabled selected>-- Elige un nivel --</option>
                                    <option value="principiante">Principiante</option>
                                    <option value="intermedio">Intermedio</option>
                                    <option value="avanzado">Avanzado</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="materiales" class="form-label">Materiales</label>
                            <textarea name="materiales" id="materiales" class="form-control" rows="4" placeholder="Ej: Hilo acrílico, aguja 4mm, relleno, ojos de seguridad"></textarea>
                            <div class="form-text">Describe todos los materiales necesarios para el proyecto.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="imagen" class="form-label">Imagen del Patrón</label>
                                <input class="form-control" type="file" name="imagen" id="imagen" accept="image/jpeg, image/png, image/gif">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pdf" class="form-label">Archivo PDF del Patrón</label>
                                <input class="form-control" type="file" name="pdf" id="pdf" accept=".pdf">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" form="formIngresarPatron">Guardar Patrón</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Tu código original para cerrar la conexión, sin modificaciones
$result->close();
$mysqli->close();
?>