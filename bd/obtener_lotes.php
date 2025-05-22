<?php
header('Content-Type: application/json');
include_once 'conexion.php';

$objeto = new conn();
$conexion = $objeto->connect();

$id_proy = (isset($_POST['id_proy'])) ? $_POST['id_proy'] : '';
$id_man = (isset($_POST['id_man'])) ? $_POST['id_man'] : '';

$consultalt = "SELECT * FROM lote WHERE id_proy=:id_proy and id_man=:id_man ORDER BY id_lote";
$resultadolt = $conexion->prepare($consultalt);
$resultadolt->bindParam(':id_proy', $id_proy, PDO::PARAM_INT);
$resultadolt->bindParam(':id_man', $id_man, PDO::PARAM_INT);
$resultadolt->execute();
$datalt = $resultadolt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($datalt);
?>