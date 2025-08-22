<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../include/conexion.php');

require_once("../../include/eventos_funciones.php");

$method = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? $_POST['accion'] ?? 'listar';

try {
  if ($method==='GET' && $accion==='listar') {
    $anio = intval($_GET['anio'] ?? date('Y'));
    $mes  = intval($_GET['mes']  ?? date('n')); // 1..12
    $res = eventos_listar_por_mes($mysqli, $anio, $mes);
    $data = [];
    if ($res) while ($row = $res->fetch_assoc()) {
      $row['fecha_evento'] = substr($row['fecha_evento'], 0, 10);
      $data[] = $row;
    }
    echo json_encode(['ok'=>true,'eventos'=>$data]); exit;
  }

  if ($method==='POST' && $accion==='crear') {
    $ok = ($_POST['nombre_evento'] ?? '') && ($_POST['fecha_evento'] ?? '');
    $ok = $ok ? eventos_crear($mysqli, trim($_POST['nombre_evento']), trim($_POST['descripcion'] ?? ''), trim($_POST['fecha_evento'])) : false;
    echo json_encode(['ok'=>$ok]); exit;
  }

  if ($method==='POST' && $accion==='actualizar') {
    $id = intval($_POST['id_evento'] ?? 0);
    $ok = $id && ($_POST['nombre_evento'] ?? '') && ($_POST['fecha_evento'] ?? '');
    $ok = $ok ? eventos_actualizar($mysqli, $id, trim($_POST['nombre_evento']), trim($_POST['descripcion'] ?? ''), trim($_POST['fecha_evento'])) : false;
    echo json_encode(['ok'=>$ok]); exit;
  }

  if ($method==='POST' && $accion==='eliminar') {
    $id = intval($_POST['id_evento'] ?? 0);
    $ok = $id ? eventos_eliminar($mysqli, $id) : false;
    echo json_encode(['ok'=>$ok]); exit;
  }

  echo json_encode(['ok'=>false,'error'=>'Acción no válida']);
} catch (Throwable $e) {
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
