<?php


 include_once 'conexion.php';
 $objeto = new conn();
 $conexion = $objeto->connect(); 

 $id_col=(isset($_POST['id_col'])) ? $_POST['id_col'] : '';
 // Asegúrate de incluir tu archivo de conexión
 if($id_col=='' || $id_col==0){
     $query = "SELECT id_pros, nombre FROM prospecto WHERE edo_pros = 1 ORDER BY nombre ASC";
     $stmt = $conexion->prepare($query);
       
 }else{
     $query = "SELECT id_pros, nombre FROM prospecto WHERE edo_pros = 1 AND col_asignado = :id_col ORDER BY nombre ASC";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id_col', $id_col, PDO::PARAM_INT);

 }


try {
 
   if (!$stmt->execute()) {
       throw new Exception("Error al ejecutar la consulta");
   }

    $prospectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($prospectos);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>