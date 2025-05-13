<?php
$api_url = 'http://tecniem.com/srmcheca/api/correoinba2.php';

$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(array("message" => "Error al decodificar los datos JSON: " . json_last_error_msg()));
    return;
}

if (!isset($data['id_pros'], $data['nombre'],$data['telefono'],$data['correo'],$data['colaborador'])) {
    echo json_encode(array("message" => "Faltan parametros obligatorios."));
    return;
}

include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$consulta = "SELECT c.correo FROM prospecto p 
             JOIN colaborador c ON p.col_asignado = c.id_col 
             WHERE p.id_pros = ?";
$stmt = $conexion->prepare($consulta);
$stmt->execute([$data['id_pros']]);
$colaborador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$colaborador || empty($colaborador['correo'])) {
    echo json_encode(["success" => false, "message" => "No se encontró el correo del colaborador"]);
    exit;
}

$data['email_colaborador'] = $colaborador['correo'];

$options = array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-Type: application/json',
        'content' => json_encode($data)
    )
);

$context  = stream_context_create($options);
$response = file_get_contents($api_url, false, $context);

if ($response === false) {
    echo json_encode(array("message" => "Error al enviar los datos al servidor."));
} else {
    echo $response;
}
?>
