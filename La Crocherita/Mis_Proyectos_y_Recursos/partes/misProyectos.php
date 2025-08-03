<?php
session_start();
require_once __DIR__ . '/../../include/conexion.php';

// Redirigir si el usuario no está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// --- Lógica para procesar el formulario de un nuevo proyecto ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recoger los datos del formulario
    $id_patron = intval($_POST['id_patron'] ?? 0);
    $nombre_proyecto = $_POST['nombre_proyecto'] ?? '';
    $estado = 'en proceso'; // Los proyectos nuevos siempre inician 'en proceso'
    $puntos_utilizados = 0; // Valor inicial

    // 2. Validar que se seleccionó un patrón válido
    if ($id_patron > 0 && !empty($nombre_proyecto)) {
        // 3. Preparar la consulta INSERT con la nueva estructura
        $stmt = $mysqli->prepare("
            INSERT INTO proyectos 
            (id_usuario, id_patron, nombre_proyecto, tipo_reto, estado, puntos_utilizados)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        // 4. Vincular los parámetros
        $stmt->bind_param("iisssi", $id_usuario, $id_patron, $nombre_proyecto, $tipo_reto, $estado, $puntos_utilizados);
        
        // 5. Ejecutar y cerrar
        $stmt->execute();
        $stmt->close();

        // 6. Redirigir para evitar reenvío del formulario
        header("Location: misProyectos.php");
        exit();
    }
}

// --- Consultas para mostrar la página ---

// 1. Cargar la lista de patrones disponibles para el formulario del modal
$patrones_result = $mysqli->query("SELECT id_patron, nombre_patron FROM patrones ORDER BY nombre_patron ASC");

// 2. Cargar los proyectos del usuario actual usando un JOIN para obtener los datos del patrón
$stmt = $mysqli->prepare("
    SELECT 
        pat.id_patron,
        pat.nombre_patron,
        pat.descripcion,
        pat.imagen_url,
        pat.nivel_dificultad
    FROM
        patrones AS pat
    WHERE 
        pat.id_usuario = ?
    ORDER BY 
        pat.id_patron DESC
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$mis_proyectos_result = $stmt->get_result();

//3. Mostrar los proyectos ligados al usuario
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
        <div class="table-responsive">
            <table id="tabla-proyectos" class="table table-hover align-middle tabla-patrones">
                <thead class="table-light">
                    <tr>
                        <th>Imagen</th>
                        <th>Proyecto (Patrón)</th>
                        <th>Descripción del Patrón</th>
                        <th>Nivel</th>
                        <th>Tipo</th>
                        <th>Estado</th>
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
                            <td><?= htmlspecialchars($proyecto['tipo_reto']) ?></td>
                            <td>
                                <span class="badge bg-primary"><?= htmlspecialchars($proyecto['estado']) ?></span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoProyecto" tabindex="-1" aria-labelledby="modalNuevoProyectoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevoProyectoLabel">Iniciar Nuevo Proyecto</h5>
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

                    <div class="mb-3">
                        <label for="tipo_reto" class="form-label">3. Define el Tipo</label>
                        <select name="tipo_reto" id="tipo_reto" class="form-select" required>
                            <option value="personal">Personal</option>
                            <option value="reto">Reto</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success" form="formNuevoProyecto">Iniciar Proyecto</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
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
                "sLast":"Último",
                "sNext":"Siguiente",
                "sPrevious": "Anterior"
            },
            "sProcessing":"Procesando...",
        },
        "order": [[0, "desc"]]
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