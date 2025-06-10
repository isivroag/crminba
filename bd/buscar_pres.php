<?php





include_once 'conexion.php';

$objeto = new conn();
$conexion = $objeto->connect();


$folio = $_POST['folio'] ?? '';

$sql = "SELECT * FROM vpresupuesto WHERE id_pres = :folio";

$consulta = $conexion->prepare($sql);

$consulta->bindParam(':folio', $folio, PDO::PARAM_INT);

$consulta->execute();

$resultado = $consulta->fetch(PDO::FETCH_ASSOC);
if ($resultado) {
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
} else {
    $resultado=0;
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
}
$conexion = null;
?>
