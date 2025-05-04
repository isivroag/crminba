<?php
// Datos del servidor donde está alojada la API
$api_url = 'http://tecniem.com/srmcheca/api/correo2.php';

// Datos del formulario
$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(array("message" => "Error al decodificar los datos JSON: " . json_last_error_msg()));
    return;
}

// Verificar si se recibieron los parámetros necesarios
if (!isset($data['id_prov'], $data['email'],$data['nombre'])) {
    echo json_encode(array("message" => "Faltan parametros obligatorios."));
    return;
}

// Debugging: imprimir los datos recibidos
error_log(print_r($data, true));

// Crear una solicitud POST para enviar datos a la API
$options = array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-Type: application/json',
        'content' => json_encode($data)
    )
);

$context  = stream_context_create($options);
$response = file_get_contents($api_url, false, $context);

// Manejar la respuesta de la API
if ($response === false) {
    echo json_encode(array("message" => "Error al enviar los datos al servidor."));
} else {
    echo $response; // Devolver la respuesta de la API
}
?>