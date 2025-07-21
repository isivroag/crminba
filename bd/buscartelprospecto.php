<?php  
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de los datos enviados mediante POST desde el JS   
$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : '';
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';

$data = 0;

// Normalización adicional en el backend
$telefonoBusqueda = $telefono;
// Si es un número nacional sin código (solo dígitos), agregamos +52
if (preg_match('/^[0-9]{10}$/', $telefono)) {
    $telefonoBusqueda = '+52' . $telefono;
}

if ($opcion == 1) {
    $consulta = "SELECT * FROM prospecto WHERE (telefono = :telefono OR telefono = :telefonoAlternativo) AND edo_pros = 1";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':telefono', $telefono);
    // Buscar también la versión con/sin código de país
    $resultado->bindParam(':telefonoAlternativo', $telefonoBusqueda); 
    $resultado->execute();
    
    if($resultado->rowCount() >= 1) {
        $data = 1;
    }
} else {
    $consulta = "SELECT * FROM prospecto WHERE (telefono = :telefono OR telefono = :telefonoAlternativo) AND edo_pros = 1 AND id_pros <> :id";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':telefono', $telefono);
    $resultado->bindParam(':telefonoAlternativo', $telefonoBusqueda);
    $resultado->bindParam(':id', $id);
    $resultado->execute();
    
    if($resultado->rowCount() >= 1) {
        $data = 1;
    }
}

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;  
?>