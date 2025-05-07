<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$response = ['success' => false, 'message' => ''];

$id_prospecto = $_POST['id_prospecto'] ?? null;
$rfc = $_POST['rfc'] ?? null;
$direccion = $_POST['direccion'] ?? null;
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;

try {
    $conexion->beginTransaction();
    
    // 1. Crear registro en tabla cliente
    $consulta = "INSERT INTO cliente (id_prospecto, rfc, direccion, fecha_nacimiento) 
                 VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($consulta);
    $stmt->execute([$id_prospecto, $rfc, $direccion, $fecha_nacimiento]);
    
    $id_cliente = $conexion->lastInsertId();
    
    // 2. Actualizar prospecto
    $consulta = "UPDATE prospecto SET 
                 edo_pros = 2, id_cliente = ?
                 WHERE id_pros = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->execute([$id_cliente, $id_prospecto]);
    
    $conexion->commit();
    
    $response = [
        'success' => true,
        'message' => 'Prospecto convertido a cliente exitosamente'
    ];
} catch (PDOException $e) {
    $conexion->rollBack();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>