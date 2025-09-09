<?php
include_once "conexion.php";
$objeto = new conn();
$conexion = $objeto->connect();

$estado = $_POST['estado'] ?? 'activos';

if ($estado == "todos") {
     $consulta = "SELECT p.*, c.nombre as nombre_colaborador 
                 FROM prospecto p
                 JOIN colaborador c ON p.col_asignado = c.id_col
                 WHERE p.edo_pros IN (1,2)
                 and p.edo_seguimiento <> 3
                 ORDER BY p.id_pros";
} else {
      $consulta = "SELECT p.*, c.nombre as nombre_colaborador 
                 FROM prospecto p
                 JOIN colaborador c ON p.col_asignado = c.id_col
                 WHERE p.edo_pros = 1
                 ORDER BY p.id_pros";
}

$resultado = $conexion->query($consulta);
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["data" => $data]);
?>