<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$response = ['success' => false, 'message' => ''];

try {
    $id_apartado = $_POST['id_apartado'] ?? null;

    if (!$id_apartado) {
        throw new Exception("ID de apartado requerido");
    }

    $conexion->beginTransaction();

    // Obtener información del apartado
    $consultaInfo = "SELECT id_lote FROM apartado WHERE id_apartado = :id_apartado";
    $stmtInfo = $conexion->prepare($consultaInfo);
    $stmtInfo->bindParam(':id_apartado', $id_apartado);
    $stmtInfo->execute();
    $apartado = $stmtInfo->fetch(PDO::FETCH_ASSOC);

    if (!$apartado) {
        throw new Exception("Apartado no encontrado");
    }

    // Actualizar status del apartado
    $consultaApartado = "UPDATE apartado SET status = 'CONVERTIDO' WHERE id_apartado = :id_apartado";
    $stmtApartado = $conexion->prepare($consultaApartado);
    $stmtApartado->bindParam(':id_apartado', $id_apartado);
    $stmtApartado->execute();

    // Actualizar status del lote a VENDIDO
    $consultaLote = "UPDATE lote SET status = 'VENDIDO' WHERE id_lote = :id_lote";
    $stmtLote = $conexion->prepare($consultaLote);
    $stmtLote->bindParam(':id_lote', $apartado['id_lote']);
    $stmtLote->execute();

    $conexion->commit();
    
    $response['success'] = true;
    $response['message'] = 'Apartado convertido a venta correctamente';

} catch (Exception $e) {
    $conexion->rollBack();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conexion = null;
?>