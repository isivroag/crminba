<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Obtener datos JSON
$input = json_decode(file_get_contents('php://input'), true);

$response = ['success' => false, 'message' => ''];

try {
    // Validar datos requeridos
    if (!$input['id_prospecto'] || !$input['id_lote'] || !$input['importe_apartado']) {
        throw new Exception("Faltan datos requeridos");
    }

    // Verificar que el lote esté disponible
    $consultaLote = "SELECT status FROM vlote WHERE id_lote = :id_lote";
    $stmtLote = $conexion->prepare($consultaLote);
    $stmtLote->bindParam(':id_lote', $input['id_lote']);
    $stmtLote->execute();
    $lote = $stmtLote->fetch(PDO::FETCH_ASSOC);

    if (!$lote) {
        throw new Exception("Lote no encontrado");
    }

    if ($lote['status'] !== 'DISPONIBLE') {
        throw new Exception("El lote no está disponible");
    }

    // Generar folio si no existe
    $folio = $input['folio'];
    if (empty($folio)) {
        $consultaFolio = "SELECT MAX(id_apartado) as ultimo FROM apartado";
        $stmtFolio = $conexion->prepare($consultaFolio);
        $stmtFolio->execute();
        $ultimoFolio = $stmtFolio->fetch(PDO::FETCH_ASSOC);
        $folio = ($ultimoFolio['ultimo'] ?? 0) + 1;
    }

    // Insertar apartado
    $consultaInsert = "INSERT INTO apartado (
        id_clie, 
        fecha_apartado, 
        id_lote, 
        id_proyecto, 
        id_manzana, 
        importe_apartado, 
        observaciones, 
        col_asignado, 
        id_usuario_alta,
        fecha_alta
    ) VALUES (
        :id_clie, 
        :fecha_apartado, 
        :id_lote, 
        :id_proyecto, 
        :id_manzana, 
        :importe_apartado, 
        :observaciones, 
        :col_asignado, 
        :id_usuario_alta,
        NOW()
    )";

    $stmt = $conexion->prepare($consultaInsert);
    $stmt->bindParam(':id_clie', $input['id_prospecto']);
    $stmt->bindParam(':fecha_apartado', $input['fecha_apartado']);
    $stmt->bindParam(':id_lote', $input['id_lote']);
    $stmt->bindParam(':id_proyecto', $input['id_proyecto']);
    $stmt->bindParam(':id_manzana', $input['id_manzana']);
    $stmt->bindParam(':importe_apartado', $input['importe_apartado']);
    $stmt->bindParam(':observaciones', $input['observaciones']);
    $stmt->bindParam(':col_asignado', $input['col_asignado']);
    $stmt->bindParam(':id_usuario_alta', $input['id_usuario']);

    if ($stmt->execute()) {
        $id_apartado = $conexion->lastInsertId();
        
        // Actualizar status del lote a APARTADO
        $updateLote = "UPDATE lote SET status = 'APARTADO' WHERE id_lote = :id_lote";
        $stmtUpdate = $conexion->prepare($updateLote);
        $stmtUpdate->bindParam(':id_lote', $input['id_lote']);
        $stmtUpdate->execute();

        $response['success'] = true;
        $response['message'] = 'Apartado guardado correctamente';
        $response['folio'] = $id_apartado;
    } else {
        throw new Exception("Error al guardar en la base de datos");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conexion = null;
?>