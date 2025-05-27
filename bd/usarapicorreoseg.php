<?php
// Datos del servidor donde está alojada la API
//$api_url = 'http://tecniem.com/srmcheca/api/correocita.php';
$api_url = 'http://intranet.bosquedelasanimas.com.mx/api/correocita.php';

// Datos del formulario
$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(array("message" => "Error al decodificar los datos JSON: " . json_last_error_msg()));
    return;
}

// Verificar si se recibieron los parámetros necesarios
if (!isset($data['id_seg'])) {
    echo json_encode(array("message" => "Faltan parametros obligatorios."));
    return;
}

include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

error_log("Conectado a la base de datos"); // Debug 5

$consulta = "SELECT * from vseg_pros 
             WHERE id_seg = ?"; 
$stmt = $conexion->prepare($consulta);
$stmt->execute([$data['id_seg']]);
$colaborador = $stmt->fetch(PDO::FETCH_ASSOC);

// Debugging: imprimir los datos recibidos
error_log(print_r($data, true));

if (!$colaborador || empty($colaborador['correo_colaborador'])) {
    $error = "No se encontró el correo del colaborador";
    error_log($error); // Debug 7
    echo json_encode(["success" => false, "message" => $error]);
    exit;
}
$data['id_pros'] = $colaborador['id_pros'];
$data['nombre'] = $colaborador['nombre'];


$data['telefono'] = $colaborador['telefono'];
$data['correo'] = $colaborador['correo'];
$data['nom_colaborador'] = $colaborador['nombre_col'];
$data['correo_colaborador'] = $colaborador['correo_colaborador'];

$data['fecha_seg'] = $colaborador['fecha_seg'];
$data['tipo_seg'] = $colaborador['tipo_seg'];
$data['observaciones'] = $colaborador['observaciones'];




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
