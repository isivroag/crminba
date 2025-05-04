<?php  
 //filter.php  

 include_once 'conexion.php';
 $objeto = new conn();
 $conexion = $objeto->connect();
 
 // Recepción de los datos enviados mediante POST desde el JS   
 
 
 $rfc = (isset($_POST['rfc'])) ? $_POST['rfc'] : '';
 $opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
 $data=0;
 if ($opcion==1){
    $consulta = "SELECT * FROM proveedor WHERE rfc_prov='$rfc' AND edo_prov = 1";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    if($resultado->rowCount() >=1){
       $data=1;
    }
 }
 
 
 
 
 
 print json_encode($data, JSON_UNESCAPED_UNICODE);
 $conexion = NULL;  
 ?>