<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$id_prov = $_POST['id_prov'];

$consulta = "SELECT d.id_doc, d.nombre, pd.archivo_url, pd.fecha_subida 
             FROM documento d 
             INNER JOIN proveedor_documento pd ON d.id_doc = pd.id_doc AND pd.id_prov = :id_prov";
$resultado = $conexion->prepare($consulta);
$resultado->bindParam(':id_prov', $id_prov, PDO::PARAM_INT);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>