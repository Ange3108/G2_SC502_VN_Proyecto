<?php
session_start();
// 1. Incluir el archivo de conexión a la base de datos
require_once __DIR__ . '/../../include/conexion.php';


// --- Lógica para añadir a favoritos (solo si POST tiene id_patron y NO nombre_patron, etc.) ---
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_patron_fav = isset($_POST['id_patron']) ? intval($_POST['id_patron']) : 0;
    $nombre_patron_post = $_POST['nombre_patron'] ?? '';
    // Si solo se recibe id_patron y NO nombre_patron, es favoritos
    if (!empty($id_patron_fav) && empty($nombre_patron_post)) {
        $id_usuario = $_SESSION['id_usuario'] ?? 0;
        if (!empty($id_usuario)) {
            // Toggle favoritos: si existe, elimina; si no, inserta
            $stmt = $mysqli->prepare("SELECT 1 FROM Favoritos_Patrones WHERE id_usuario = ? AND id_patron = ?");
            $stmt->bind_param("ii", $id_usuario, $id_patron_fav);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                // Ya es favorito, eliminar
                $stmt->close();
                $stmt = $mysqli->prepare("DELETE FROM Favoritos_Patrones WHERE id_usuario = ? AND id_patron = ?");
                $stmt->bind_param("ii", $id_usuario, $id_patron_fav);
                $stmt->execute();
                if ($is_ajax) {
                    echo "Patrón eliminado de favoritos.";
                    exit();
                } else {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?status=removed");
                    exit();
                }
            } else {
                // No es favorito, agregar
                $stmt->close();
                $stmt = $mysqli->prepare("INSERT INTO Favoritos_Patrones (id_usuario, id_patron) VALUES (?, ?)");
                $stmt->bind_param("ii", $id_usuario, $id_patron_fav);
                $stmt->execute();
                if ($is_ajax) {
                    echo "Patrón agregado a favoritos correctamente.";
                    exit();
                } else {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
                    exit();
                }
            }
        } else {
            if ($is_ajax) {
                echo "Error: usuario no autenticado.";
                exit();
            } else {
                echo "<script>alert('Error: usuario no autenticado');window.location='patrones.php';</script>";
                exit();
            }
        }
    }
// =========== INICIO: LÓGICA PARA PROCESAR FORMULARIO DEL MODAL ===========
// Este bloque solo se ejecuta si se envía el formulario (método POST)
    // Si viene nombre_patron, es alta/edición de patrón (flujo original)
    // Recoger datos de texto
    $nombre_patron = $_POST['nombre_patron'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $nivel_dificultad = $_POST['nivel_dificultad'] ?? '';
    $puntos_utilizados = $_POST['puntos_utilizados'] ?? '';
    $materiales = $_POST['materiales'] ?? '';
    $id_patron = $_POST['id_patron'] ?? '';

    $imagen_url_db = '';
    $pdf_url_db = '';
    $error_subida = false;

    // Manejar la subida de la IMAGEN
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $directorio_imagenes = 'uploads/imagenes/';
        if (!is_dir($directorio_imagenes)) {
            mkdir($directorio_imagenes, 0777, true);
        }
        $nombre_archivo_img = time() . '_' . basename($_FILES['imagen']['name']);
        $ruta_completa_img = $directorio_imagenes . $nombre_archivo_img;
        $tipo_archivo_img = strtolower(pathinfo($ruta_completa_img, PATHINFO_EXTENSION));
        $tipos_permitidos_img = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($tipo_archivo_img, $tipos_permitidos_img) && move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa_img)) {
            // Guardar solo la ruta relativa
            $imagen_url_db = $directorio_imagenes . $nombre_archivo_img;
        } else {
            $error_subida = true;
        }
    } elseif (!empty($id_patron)) {
        // Si no se sube nueva imagen y es edición, mantener la actual
        $stmt_img = $mysqli->prepare("SELECT imagen_url FROM patrones WHERE id_patron = ?");
        $stmt_img->bind_param("i", $id_patron);
        $stmt_img->execute();
        $stmt_img->bind_result($imagen_url_actual);
        if ($stmt_img->fetch()) {
            $imagen_url_db = $imagen_url_actual;
        }
        $stmt_img->close();
    }

    // Manejar la subida del PDF
    if (!$error_subida && isset($_FILES['pdf']) && $_FILES['pdf']['error'] === 0) {
        $directorio_pdfs = 'uploads/pdfs/';
        if (!is_dir($directorio_pdfs)) {
            mkdir($directorio_pdfs, 0777, true);
        }
        $nombre_archivo_pdf = time() . '_' . basename($_FILES['pdf']['name']);
        $ruta_completa_pdf = $directorio_pdfs . $nombre_archivo_pdf;
        $tipo_archivo_pdf = strtolower(pathinfo($ruta_completa_pdf, PATHINFO_EXTENSION));
        if ($tipo_archivo_pdf == 'pdf' && move_uploaded_file($_FILES['pdf']['tmp_name'], $ruta_completa_pdf)) {
            // Guardar solo la ruta relativa
            $pdf_url_db = $directorio_pdfs . $nombre_archivo_pdf;
        } else {
            $error_subida = true;
        }
    } elseif (!empty($id_patron)) {
        // Si no se sube nuevo PDF y es edición, mantener el actual
        $stmt_pdf = $mysqli->prepare("SELECT pdf_url FROM patrones WHERE id_patron = ?");
        $stmt_pdf->bind_param("i", $id_patron);
        $stmt_pdf->execute();
        $stmt_pdf->bind_result($pdf_url_actual);
        if ($stmt_pdf->fetch()) {
            $pdf_url_db = $pdf_url_actual;
        }
        $stmt_pdf->close();
    }

    //validaciones
    if (empty($nombre_patron)) {
        if ($is_ajax) {
            echo "El nombre del patrón es requerido";
            exit();
        } else {
            echo "<script>alert('El nombre del patrón es requerido');window.location='patrones.php';</script>";
            exit();
        }
    }
    if (empty($descripcion)) {
        if ($is_ajax) {
            echo "La descripción es requerida";
            exit();
        } else {
            echo "<script>alert('La descripción es requerida');window.location='patrones.php';</script>";
            exit();
        }
    }
    if (empty($nivel_dificultad)) {
        if ($is_ajax) {
            echo "El nivel de dificultad es requerido";
            exit();
        } else {
            echo "<script>alert('El nivel de dificultad es requerido');window.location='patrones.php';</script>";
            exit();
        }
    }
    if (empty($puntos_utilizados)) {
        if ($is_ajax) {
            echo "Los puntos utilizados son requeridos";
            exit();
        } else {
            echo "<script>alert('Los puntos utilizados son requeridos');window.location='patrones.php';</script>";
            exit();
        }
    }
    if (empty($materiales)) {
        if ($is_ajax) {
            echo "Los materiales son requeridos";
            exit();
        } else {
            echo "<script>alert('Los materiales son requeridos');window.location='patrones.php';</script>";
            exit();
        }
    }
    if (empty($imagen_url_db) && empty($pdf_url_db)) {
        if ($is_ajax) {
            echo "Debe subir al menos una imagen o un PDF";
            exit();
        } else {
            echo "<script>alert('Debe subir al menos una imagen o un PDF');window.location='patrones.php';</script>";
            exit();
        }
    }

    // Insertar o editar en la Base de Datos solo si no hubo errores de subida
    if (!$error_subida && empty($id_patron)) {
        // Insertar nuevo patrón
        $stmt = $mysqli->prepare("
            INSERT INTO patrones (nombre_patron, descripcion, imagen_url, nivel_dificultad, puntos_utilizados, materiales, pdf_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $nombre_patron, $descripcion, $imagen_url_db, $nivel_dificultad, $puntos_utilizados, $materiales, $pdf_url_db);
        if ($stmt->execute()) {
            if ($is_ajax) {
                echo "Patrón agregado correctamente.";
                exit();
            } else {
                header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
                exit();
            }
        }
        $stmt->close();
    } elseif (!$error_subida && !empty($id_patron)) {
        // Editar patrón existente
        $stmt = $mysqli->prepare("
            UPDATE patrones SET 
                nombre_patron = ?, 
                descripcion = ?, 
                imagen_url = ?, 
                nivel_dificultad = ?, 
                puntos_utilizados = ?, 
                materiales = ?, 
                pdf_url = ? 
            WHERE id_patron = ?
        ");
        $stmt->bind_param("sssssssi", $nombre_patron, $descripcion, $imagen_url_db, $nivel_dificultad, $puntos_utilizados, $materiales, $pdf_url_db, $id_patron);
        $stmt->execute();
        $stmt->close();
        if ($is_ajax) {
            echo "Patrón editado correctamente.";
            exit();
        } else {
            header("Location: patrones.php");
            exit();
        }
    }
}
// =========== FIN: LÓGICA PARA PROCESAR FORMULARIO ===========


// 2. Preparar y ejecutar la consulta para obtener todos los patrones (TU CÓDIGO ORIGINAL INTACTO)
$query = "SELECT * FROM patrones ORDER BY nombre_patron ASC";
$result = $mysqli->query($query);


//==================== Logica para eliminar un patron=====================//
// --- Lógica para eliminar un proyecto (GET) ---
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM Patrones WHERE id_patron = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($stmt->affected_rows > 0) {
        $msg = "Patrón: " . $id . " eliminado correctamente ";
    } else {
        $msg = "Error, patrón: " . $id . " no se eliminó o no existe.";
    }
    $stmt->close();
    $mysqli->close();
    if ($is_ajax) {
        echo $msg;
        exit();
    } else {
        echo "<script>alert('" . addslashes($msg) . "');window.location='patrones.php';</script>";
        exit();
    }
}







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
            // Obtener favoritos del usuario actual
            $favoritos = [];
            if (isset($_SESSION['id_usuario'])) {
                $id_usuario = $_SESSION['id_usuario'];
                $resFav = $mysqli->query("SELECT id_patron FROM Favoritos_Patrones WHERE id_usuario = $id_usuario");
                while ($rowFav = $resFav->fetch_assoc()) {
                    $favoritos[] = $rowFav['id_patron'];
                }
            }
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
                                <?php if ($_SESSION['id_usuario'] == 1): ?>
                                    <!-- Botón para abrir modal edicion de patron-->
                                    <button type="button" class="btn btn-warning btn-sm btnEditar"
                                        data-id_patron="<?= $patron['id_patron'] ?>"
                                        data-nombre_patron="<?= htmlspecialchars($patron['nombre_patron']) ?>"
                                        data-descripcion="<?= htmlspecialchars($patron['descripcion']) ?>"
                                        data-nivel_dificultad="<?= htmlspecialchars($patron['nivel_dificultad']) ?>"
                                        data-puntos_utilizados="<?= htmlspecialchars($patron['puntos_utilizados']) ?>"
                                        data-materiales="<?= htmlspecialchars($patron['materiales']) ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarPatron">
                                        <i class='fas fa-edit'></i> Editar
                                    </button>
                                    <!-- Botón para eliminar patrón -->
                                    <button type="button" class="btn btn-danger btn-sm btnEliminar" data-id="<?= $patron['id_patron'] ?>">
                                        <i class='fas fa-trash'></i> Eliminar
                                    </button>
                                
                                <?php endif; ?>
                                <!--Boton para añadir a favoritos el patron-->
                                <button type="button" class="btnFavoritos" data-id="<?= $patron['id_patron']?>">
                                    <i class="fas fa-heart<?= in_array($patron['id_patron'], $favoritos) ? ' text-danger' : ' text-secondary' ?>"></i>
                                </button>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php
            endwhile;
            ?>
        </div>
        <!-- Mensaje para feedback de acciones (solo uno, fuera del bucle) -->
        <div id="mensaje" style="display:none;"></div>
    </main>

    <!-- Modal para ingresar nuevo patrón -->
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
    <!-- Modal para editar patrón -->
    <div class="modal fade" id="modalEditarPatron" tabindex="-1" aria-labelledby="modalLabelEditar" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelEditar">Editar Patrón</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" id="formEditarPatron" enctype="multipart/form-data">
                        <input type="hidden" name="id_patron" id="id_patron_editar">
                        <div class="mb-3">
                            <label for="nombre_patron_editar" class="form-label">Nombre del Patrón</label>
                            <input type="text" name="nombre_patron" id="nombre_patron_editar" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion_editar" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion_editar" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="puntos_utilizados_editar" class="form-label">Puntos Utilizados</label>
                                <input type="text" name="puntos_utilizados" id="puntos_utilizados_editar" class="form-control" placeholder="Ej: Punto bajo, punto alto, anillo mágico">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nivel_dificultad_editar" class="form-label">Nivel de Dificultad</label>
                                <select name="nivel_dificultad" id="nivel_dificultad_editar" class="form-select" required>
                                    <option value="" disabled selected>-- Elige un nivel --</option>
                                    <option value="principiante">Principiante</option>
                                    <option value="intermedio">Intermedio</option>
                                    <option value="avanzado">Avanzado</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="materiales_editar" class="form-label">Materiales</label>
                            <textarea name="materiales" id="materiales_editar" class="form-control" rows="4" placeholder="Ej: Hilo acrílico, aguja 4mm, relleno, ojos de seguridad"></textarea>
                            <div class="form-text">Describe todos los materiales necesarios para el proyecto.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="imagen_editar" class="form-label">Imagen del Patrón</label>
                                <input class="form-control" type="file" name="imagen" id="imagen_editar" accept="image/jpeg, image/png, image/gif">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pdf_editar" class="form-label">Archivo PDF del Patrón</label>
                                <input class="form-control" type="file" name="pdf" id="pdf_editar" accept=".pdf">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" form="formEditarPatron">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>



    <div>
        <footer>
            <div class="frase">Síguenos en nuestras redes sociales!</div>
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
        </footer>
        <style>
        .btnFavoritos .fa-heart.text-danger {
            color: #e74c3c !important;
        }
        .btnFavoritos .fa-heart.text-secondary {
            color: #bbb !important;
        }
        </style>
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../js/funcionalidadPatrones.js"></script>
</body>

</html>
<?php
// Tu código original para cerrar la conexión, sin modificaciones
$result->close();
$mysqli->close();
?>
