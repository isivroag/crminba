<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$response = ['success' => false, 'message' => ''];

try {
    $id_apartado = $_POST['id_apartado'] ?? null;
    $fecha_apartado = $_POST['fecha_apartado'] ?? null;
    $importe_apartado = $_POST['importe_apartado'] ?? null;
    $col_asignado = $_POST['col_asignado'] ?? null;
    $observaciones = $_POST['observaciones'] ?? null;

    if (!$id_apartado || !$fecha_apartado || !$importe_apartado || !$col_asignado) {
        throw new Exception("Faltan datos requeridos");
    }

    $consulta = "UPDATE apartado SET 
                 fecha_apartado = :fecha_apartado,
                 importe_apartado = :importe_apartado,
                 col_asignado = :col_asignado,
                 observaciones = :observaciones
                 WHERE id_apartado = :id_apartado";

    $stmt = $conexion->prepare($consulta);
    $stmt->bindParam(':id_apartado', $id_apartado);
    $stmt->bindParam(':fecha_apartado', $fecha_apartado);
    $stmt->bindParam(':importe_apartado', $importe_apartado);
    $stmt->bindParam(':col_asignado', $col_asignado);
    $stmt->bindParam(':observaciones', $observaciones);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Apartado actualizado correctamente';
    } else {
        throw new Exception("Error al actualizar en la base de datos");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conexion = null;
?>