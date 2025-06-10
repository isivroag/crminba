<?php
header('Content-Type: application/json');

 include_once 'conexion.php';
 $objeto = new conn();
 $conexion = $objeto->connect(); // Asegúrate de incluir tu archivo de conexión

$idProyecto = $_POST['id_proy'] ?? null;
$idManzana = $_POST['id_man'] ?? null;

if (!$idProyecto) {
    echo json_encode([]);
    exit;
}

try {
    $query = "SELECT id_lote, clave_lote, superficie, preciom, valortotal,frente,fondo,tipo,status 
              FROM vistalote 
              WHERE id_proy = :id_proy";
    
    $params = [':id_proy' => $idProyecto];
    
    if ($idManzana) {
        $query .= " AND id_man = :id_man";
        $params[':id_man'] = $idManzana;
    }
    
    $query .= " ORDER BY clave_lote";
    
    $stmt = $conexion->prepare($query);
    $stmt->execute($params);
    
    $lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($lotes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>