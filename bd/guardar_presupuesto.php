<?php
header('Content-Type: application/json');
include_once 'conexion.php';

$objeto = new conn();
$conexion = $objeto->connect();

$data = json_decode(file_get_contents("php://input"), true);

try {
    $conexion->beginTransaction();

    $query = "INSERT INTO presupuesto (
                id_lote, 
                id_proy,
                id_man,
                fecha_pres, 
                id_pros,
                nombre_pros,   
                tasa,
                inicial,
                importe, 
                descuento, 
                pordescuento, 
                valorop, 
                enganche,
                nenganche, 
                nmsi, 
                nmci,
                totalcapital,
                totalinteres,
                totalpagar,
                enganchepor,
                descuentopor

              ) VALUES (
                :id_lote, 
                :id_proy, 
                :id_man, 
                :fecha_pres, 
                :id_pros, 
                :nombre_pros, 
                :tasa, 
                :inicial,
                :importe,
                :descuento,
                :pordescuento,
                :valorop,
                :enganche,
                :nenganche,
                :nmsi,
                :nmci,
                :totalcapital,
                :totalinteres,
                :totalpagar,
                :enganchepor,
                :descuentopor

              )";

    $stmt = $conexion->prepare($query);
  
    // Asegúrate que todos estos campos existan en $data
    $stmt->bindParam(':id_lote', $data['id_lote']);
    $stmt->bindParam(':id_proy', $data['id_proy']);
    $stmt->bindParam(':id_man', $data['id_man']);
    $stmt->bindParam(':fecha_pres', $data['fecha_pres']);
    $stmt->bindParam(':id_pros', $data['id_pros']);
    $stmt->bindParam(':nombre_pros', $data['nombre_pros']);
    $stmt->bindParam(':tasa', $data['tasa']);
    $stmt->bindParam(':inicial', $data['inicial']);
    $stmt->bindParam(':importe', $data['importe']);
    $stmt->bindParam(':descuento', $data['descuento']);
    $stmt->bindParam(':pordescuento', $data['pordescuento']);
    $stmt->bindParam(':valorop', $data['valorop']);
    $stmt->bindParam(':enganche', $data['enganche']);
    $stmt->bindParam(':nenganche', $data['nenganche']);
    $stmt->bindParam(':nmsi', $data['nmsi']);
    $stmt->bindParam(':nmci', $data['nmci']);
    $stmt->bindParam(':totalcapital', $data['totalcapital']);
    $stmt->bindParam(':totalinteres', $data['totalinteres']);
    $stmt->bindParam(':totalpagar', $data['totalpagar']);
    $stmt->bindParam(':enganchepor', $data['enganchepor']);
    $stmt->bindParam(':descuentopor', $data['descuentopor']);

    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception(
            "Error en execute():\n" .
            "SQLSTATE: " . $errorInfo[0] . "\n" .
            "Código de error: " . $errorInfo[1] . "\n" .
            "Mensaje: " . $errorInfo[2] . "\n" .
            "Consulta: " . str_replace("\n", " ", $query)
        );
    }

    $id_pres = $conexion->lastInsertId();

    $conexion->commit();

    echo json_encode([
        'success' => true,
        'id_pres' => $id_pres,
        'message' => 'Encabezado guardado'
    ]);
} catch (PDOException $e) {
    $conexion->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage(),
        'error_details' => $stmt->errorInfo() // Para depuración
    ]);
} catch (Exception $e) {
    $conexion->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}