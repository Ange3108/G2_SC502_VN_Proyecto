<?php
session_start();
require_once __DIR__ . '/../../include/conexion.php';

// Redirigir si el usuario no está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/login.php");
    exit();
}

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
        // Debug: mostrar datos recibidos
        if ($is_ajax) {
            echo "[DEBUG] id_proyecto: $id_proyecto, estado: $estado\n";
        }
        // Editar solo estado
        $sql = "UPDATE Proyectos SET estado = ? WHERE id_proyecto = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('si', $estado, $id_proyecto);
        $stmt->execute();
        if ($is_ajax) {
            echo "[DEBUG] affected_rows: " . $stmt->affected_rows . "\n";
        }
        $stmt->close();
        if ($is_ajax) {
            echo ($stmt->affected_rows > 0) ? "Estado actualizado correctamente." : "No se actualizó ningún proyecto.";
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

// 1. Cargar la lista de patrones disponibles para el formulario del modal
$patrones_result = $mysqli->query("SELECT id_patron, nombre_patron, descripcion, imagen_url, nivel_dificultad FROM patrones ORDER BY nombre_patron ASC");

// 2. Cargar los proyectos del usuario actual usando un JOIN para obtener los datos del patrón
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mis Proyectos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css" />
                        
</head>

<body>

    <div class="contenedor">
        <div class="patronesD">

            <h2 class="titulo text-center mb-3">Mis Proyectos</h2>

            <div class="text-end mb-4">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoProyecto">
                    + Iniciar Nuevo Proyecto
                </button>
            </div>

            <!-- Tabla de mis proyectos-->
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
                                    <!-- Botón para abrir modal de cambio de estado -->
                                    <button type="button" class="btn btn-warning btn-sm btnEditar"
                                        data-id="<?= $proyecto['id_proyecto'] ?>"
                                        data-estado="<?= $proyecto['estado'] ?>"
                                        data-bs-toggle="modal" data-bs-target="#formCambiarEstado">
                                        <i class='fas fa-edit'></i> Editar
                                    </button>
                                    <!-- Botón para eliminar proyecto -->
                                    <button class="btn-eliminar" data-id="<?= $proyecto['id_proyecto'] ?>">Eliminar</button>

                                </td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Formulario para iniciar un nuevo proyecto -->
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
    <!-- Mensajes para feedback -->
    <div id="mensaje" style="display:none;"></div>
    <!-- Modal para cambiar el estado del proyecto  -->
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
// Cerrar las conexiones y resultados
$stmt->close();
$patrones_result->close();
$mysqli->close();
?>