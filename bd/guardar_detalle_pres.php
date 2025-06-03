<?php
header('Content-Type: application/json');
include_once 'conexion.php';

$objeto = new conn();
$conexion = $objeto->connect();

 //Verificar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener y validar datos JSON
$input = file_get_contents("php://input");
if (empty($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos vacíos']);
    exit;
}

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON inválido: '.json_last_error_msg()]);
    exit;
}


try {
    $conexion->beginTransaction();
    
    foreach ($data['pagos'] as $pago) {
        $query = "INSERT INTO detalle_pres (
                    id_pres, 
                    id_reg, 
                    fecha, 
                    capital, 
                    interes, 
                    importe, 
                    tipo, 
                    saldo
                  ) VALUES (
                    :id_pres, 
                    :numero_pago, 
                    :fecha_pago, 
                    :capital, 
                    :interes, 
                    :total, 
                    :tipo_pago, 
                    :saldo_insoluto
                  )";
        
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id_pres', $data['id_pres']);
        $stmt->bindParam(':numero_pago', $pago['numero']);
        
        // Convertir fecha de dd/mm/yyyy a yyyy-mm-dd
        $fecha_mysql = DateTime::createFromFormat('d/m/Y', $pago['fecha'])->format('Y-m-d');
        $stmt->bindParam(':fecha_pago', $fecha_mysql);
        
        $stmt->bindParam(':capital', $pago['capital']);
        $stmt->bindParam(':interes', $pago['interes']);
        $stmt->bindParam(':total', $pago['total']);
        $stmt->bindParam(':tipo_pago', $pago['tipo']);
        $stmt->bindParam(':saldo_insoluto', $pago['saldo']);
        
        $stmt->execute();
    }
    
    $conexion->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Detalles guardados',
        'pagos_guardados' => count($data['pagos'])
    ]);
    
} catch (PDOException $e) {
    $conexion->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>