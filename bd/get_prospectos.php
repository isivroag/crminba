<?php
header('Content-Type: application/json');

 include_once 'conexion.php';
 $objeto = new conn();
 $conexion = $objeto->connect(); // Asegúrate de incluir tu archivo de conexión


try {
   $query = "SELECT id_pros, nombre FROM prospecto WHERE edo_pros = 1 ORDER BY nombre ASC";
    $stmt = $conexion->prepare($query);
   
     if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta");
    }

    $prospectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($prospectos);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>