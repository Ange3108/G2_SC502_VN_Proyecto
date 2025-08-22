<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/login.php"); 
    exit(); 
}

require_once __DIR__ . '/../../include/conexion.php';

$id_usuario = $_SESSION['id_usuario'];

// --- Lógica para eliminar un proyecto (GET) ---
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM Proyectos WHERE id_proyecto = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($stmt->affected_rows > 0) {
        $msg = "Proyecto: " . $id . " eliminado correctamente ";
    } else {
        $msg = "Error, proyecto: " . $id . " no se eliminó o no existe.";
    }
    $stmt->close();
    $mysqli->close();
    if ($is_ajax) {
        echo $msg;
        exit();
    } else {
        echo "<script>alert('" . addslashes($msg) . "');window.location='misProyectos.php';</script>";
        exit();
    }
}


// --- Lógica para agregar o editar proyecto (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_proyecto = $_POST['id_proyecto'] ?? '';
    $id_patron = intval($_POST['id_patron'] ?? 0);
    $nombre_proyecto = $_POST['nombre_proyecto'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $estados_validos = ['en proceso', 'terminado'];
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (!empty($id_proyecto) && in_array($estado, $estados_validos)) {
        // Editar solo estado
        $sql = "UPDATE Proyectos SET estado = ? WHERE id_proyecto = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('si', $estado, $id_proyecto);
        $stmt->execute();
        $stmt->close();
        if ($is_ajax) {
            echo ($mysqli->affected_rows > 0) ? "Estado actualizado correctamente." : "No se actualizó ningún proyecto.";
            exit();
        } else {
            header("Location: misProyectos.php");
            exit();
        }
    } elseif (empty($id_proyecto) && $id_patron > 0 && !empty($nombre_proyecto) && in_array($estado, $estados_validos)) {
        // Agregar nuevo proyecto
        $stmt = $mysqli->prepare("
            INSERT INTO Proyectos 
            (id_usuario, id_patron, nombre_proyecto, estado)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiss", $id_usuario, $id_patron, $nombre_proyecto, $estado);
        $stmt->execute();
        $stmt->close();
        if ($is_ajax) {
            echo "Proyecto agregado correctamente.";
            exit();
        } else {
            header("Location: misProyectos.php");
            exit();
        }
    }
}

// --- Consultas para mostrar la página ---
$patrones_result = $mysqli->query("SELECT id_patron, nombre_patron, descripcion, imagen_url, nivel_dificultad FROM patrones ORDER BY nombre_patron ASC");
$stmt = $mysqli->prepare("
    SELECT 
        proy.id_proyecto,
        proy.nombre_proyecto,
        proy.estado,
        pat.nombre_patron,
        pat.descripcion AS patron_descripcion,
        pat.imagen_url AS patron_imagen_url,
        pat.nivel_dificultad AS patron_nivel_dificultad
    FROM 
        proyectos AS proy
    JOIN 
        patrones AS pat ON proy.id_patron = pat.id_patron
    WHERE 
        proy.id_usuario = ?
    ORDER BY 
        proy.id_proyecto DESC
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$mis_proyectos_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Crocherita - Mis Proyectos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        /* Estilos CSS para centrar el contenido */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .contenedor {
            max-width: 900px; /* Ancho máximo para el contenido de la tabla */
            width: 100%;
            margin: 2rem auto;
            padding: 1rem;
        }
        .header-container {
            width: 100%;
        }
        .footer-container {
            width: 100%;
        }
        .modal {
            text-align: left;
        }
    </style>
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
                    <a class="nav-link" href="../../Home/Home.php">Principal</a> 
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../Comunidad/comunidad.html">Comunidad y Participación</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../../Mis_Proyectos_y_Recursos/Proyectos.html">Mis Proyectos y Recursos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../Herramienta de Cálculo/calculadora.html">Herramientas De Cálculo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../Configuración y Ayuda/configuracion.html">Configuración y Ayuda</a>
                </li>
            </ul>
        </div>
    </div>
    
    <main>
        <div class="contenedor patronesD">
            <h2 class="titulo text-center mb-3">Mis Proyectos</h2>
            <div class="text-end mb-4">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoProyecto">
                    + Iniciar Nuevo Proyecto
                </button>
            </div>

            <div class="table-responsive">
                <table id="tabla-proyectos" class="table table-hover align-middle tabla-patrones">
                    <thead class="table-light">
                        <tr>
                            <th>Imagen</th>
                            <th>Proyecto (Patrón)</th>
                            <th>Descripción del Patrón</th>
                            <th>Nivel</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($proyecto = $mis_proyectos_result->fetch_assoc()): ?>
                            <tr>
                                <td><img src="<?= htmlspecialchars($proyecto['patron_imagen_url']) ?>" class="tabla-img" alt="<?= htmlspecialchars($proyecto['nombre_patron']) ?>"></td>
                                <td>
                                    <strong><?= htmlspecialchars($proyecto['nombre_proyecto']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($proyecto['nombre_patron']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($proyecto['patron_descripcion']) ?></td>
                                <td><?= htmlspecialchars($proyecto['patron_nivel_dificultad']) ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($proyecto['estado']) ?></span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm btnEditar"
                                        data-id="<?= $proyecto['id_proyecto'] ?>"
                                        data-estado="<?= $proyecto['estado'] ?>"
                                        data-bs-toggle="modal" data-bs-target="#formCambiarEstado">
                                        <i class='fas fa-edit'></i> Editar
                                    </button>
                                    <button class="btn-eliminar" data-id="<?= $proyecto['id_proyecto'] ?>">Eliminar</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalNuevoProyecto" tabindex="-1" aria-labelledby="modalNuevoProyectoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoProyectoLabel">Añadir Nuevo Proyecto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formNuevoProyecto" action="misProyectos.php">
                        <div class="mb-3">
                            <label for="id_patron" class="form-label">1. Elige un Patrón para Empezar</label>
                            <select name="id_patron" id="id_patron" class="form-select" required>
                                <option value="" disabled selected>-- Selecciona un patrón --</option>
                                <?php
                                $patrones_result->data_seek(0);
                                while ($patron = $patrones_result->fetch_assoc()):
                                ?>
                                    <option value="<?= $patron['id_patron'] ?>">
                                        <?= htmlspecialchars($patron['nombre_patron']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_proyecto" class="form-label">2. Dale un Nombre a tu Proyecto</label>
                            <input type="text" name="nombre_proyecto" id="nombre_proyecto" class="form-control" placeholder="Ej: Suéter para mi sobrino" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select name="estado" id="estado" class="form-select" required>
                                <option value="" disabled selected>-- Elige un estado --</option>
                                <option value="en proceso">En Proceso</option>
                                <option value="terminado">Terminado</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" form="formNuevoProyecto">Añadir Proyecto</button>
                </div>
            </div>
        </div>
    </div>
    <div id="mensaje" style="display:none;"></div>
    <div class="modal fade" id="formCambiarEstado" tabindex="-1" aria-labelledby="modalCambiarEstadoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCambiarEstadoLabel">Cambiar el estado del proyecto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="formCambiarEstado" action="misProyectos.php">
                    <div class="modal-body">
                        <input type="hidden" id="id_proyecto_modal" name="id_proyecto" value="">
                        <div class="mb-3">
                            <label for="estado_modal" class="form-label">Estado</label>
                            <select name="estado" id="estado_modal" class="form-select" required>
                                <option value="en proceso">En Proceso</option>
                                <option value="terminado">Terminado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <footer class="footer-container">
        <div class="contact-button-container">
            <a href="#" class="btn-contacto-profesora" data-bs-toggle="modal" data-bs-target="#contactaProfesoraModal">
                <i class="fas fa-chalkboard-teacher"></i> Contacto con la Profesora
            </a>
        </div>

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

        <div class="text-center mt-4">
            <div class="frase">Siguenos en nuestras redes sociales!</div>
            <div class="iconitos mt-2">
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
            <p class="mt-2">&copy; 2025 La Crocherita. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="../js/funcionalidadMP.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabla-proyectos').DataTable({
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                },
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$patrones_result->close();
$mysqli->close();
?>