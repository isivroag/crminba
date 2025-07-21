<?php


 include_once 'conexion.php';
 $objeto = new conn();
 $conexion = $objeto->connect(); 

 $id_clie=(isset($_POST['id_clie'])) ? $_POST['id_clie'] : '';
 // Asegúrate de incluir tu archivo de conexión
 if($id_clie=='' || $id_clie==0){
     $query = "SELECT id_clie, nombre FROM cliente WHERE edo_clie = 1 ORDER BY nombre ASC";
     $stmt = $conexion->prepare($query);
       
 }else{
     $query = "SELECT id_clie, nombre FROM cliente WHERE edo_clie = 1  ORDER BY nombre ASC";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id_clie', $id_clie, PDO::PARAM_INT);

 }


try {
 
   if (!$stmt->execute()) {
       throw new Exception("Error al ejecutar la consulta");
   }

    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clientes);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>