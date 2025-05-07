<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$response = ['success' => false];

try {
    // Solo obtener el siguiente colaborador sin actualizar
    $consulta = "SELECT id_col, nombre 
                 FROM colaborador 
                 WHERE edo_col = 1
                 ORDER BY 
                    IFNULL(ultima_asignacion, '2000-01-01') ASC,
                    id_col ASC
                 LIMIT 1";
    
    $stmt = $conexion->prepare($consulta);
    $stmt->execute();
    $colaborador = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($colaborador) {
        $response = [
            'success' => true,
            'id_col' => $colaborador['id_col'],
            'nombre' => $colaborador['nombre']
        ];
    } else {
        $response['error'] = 'No hay colaboradores disponibles para asignación';
    }
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>