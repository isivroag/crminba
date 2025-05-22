<?php


 include_once 'conexion.php';
 $objeto = new conn();
 $conexion = $objeto->connect();// Asegúrate de incluir tu archivo de conexión

$idProyecto = $_POST['id_proy'] ?? null;

if (!$idProyecto) {
    echo json_encode([]);
    exit;
}

try {

     $consulta = "SELECT id_man,descripcion FROM manzana WHERE id_proy = :id_proy";

    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id_proy', $idProyecto, PDO::PARAM_INT);
    $resultado->execute();


    if ($resultado->rowCount() > 0) {
        $manzanas = $resultado->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($manzanas);
    } else {
        echo json_encode([]);
    }


    
    // Cerrar la conexión
    $conexion = null;

    
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>