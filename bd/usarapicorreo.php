<?php
// Datos del servidor donde está alojada la API
//$api_url = 'http://tecniem.com/srmcheca/api/correoinba.php';
$api_url = 'http://intranet.bosquedelasanimas.com.mx/api/correoinba.php';
// Datos del formulario
$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(array("message" => "Error al decodificar los datos JSON: " . json_last_error_msg()));
    return;
}

// Verificar si se recibieron los parámetros necesarios
if (!isset($data['id_pros'], $data['nombre'], $data['colaborador'])) {
    echo json_encode(["success" => false, "message" => "Faltan parámetros obligatorios: id_pros, nombre o colaborador"]);
    return;
}

// Valida que al menos correo o teléfono estén presentes (pero no necesariamente ambos)
if (empty($data['telefono']) && empty($data['correo'])) {
    echo json_encode(["success" => false, "message" => "Debe proporcionar al menos un teléfono o un correo"]);
    return;
}
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

error_log("Conectado a la base de datos"); // Debug 5

$consulta = "SELECT c.correo FROM prospecto p 
             JOIN colaborador c ON p.col_asignado = c.id_col 
             WHERE p.id_pros = ?";
$stmt = $conexion->prepare($consulta);
$stmt->execute([$data['id_pros']]);
$colaborador = $stmt->fetch(PDO::FETCH_ASSOC);

// Debugging: imprimir los datos recibidos
error_log(print_r($data, true));

if (!$colaborador || empty($colaborador['correo'])) {
    $error = "No se encontró el correo del colaborador";
    error_log($error); // Debug 7
    echo json_encode(["success" => false, "message" => $error]);
    exit;
}

$data['email_colaborador'] = $colaborador['correo'];


// Crear una solicitud POST para enviar datos a la API
$options = array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-Type: application/json',
        'content' => json_encode($data)
    )
);

$context  = stream_context_create($options);
error_log("Enviando a la API: " . json_encode($data));
$response = file_get_contents($api_url, false, $context);

// Manejar la respuesta de la API
if ($response === false) {
    echo json_encode(array("message" => "Error al enviar los datos al servidor."));
} else {
    echo $response; // Devolver la respuesta de la API
}
?>
