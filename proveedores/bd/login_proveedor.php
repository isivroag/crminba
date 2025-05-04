<?php
session_start();
include_once '../../bd/conexion.php';
$objeto = new conn();
date_default_timezone_set('America/Mexico_City');
$conexion = $objeto->connect();
if ($conexion != null) {
$username = $_POST['usuario'];
$password = $_POST['password'];
$pass = md5($password);

$consulta = "SELECT * FROM proveedor WHERE username = :username AND password = :pass and edo_usuario=1 and edo_prov=1";
$resultado = $conexion->prepare($consulta);
$resultado->bindParam(':username', $username, PDO::PARAM_STR);
$resultado->bindParam(':pass', $pass, PDO::PARAM_STR);
$resultado->execute();


if ($resultado->rowCount() >= 1) {
    $data = $resultado->fetch(PDO::FETCH_ASSOC);
    $_SESSION['id_prov'] = $data['id_prov'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['razon'] = $data['razon_prov'];
    print json_encode($data);
   
    
} else {
    $data = 1;
    print json_encode($data);
}
$conexion = null;
}
else {
    $data = 0;
    print json_encode($data);
}
?>