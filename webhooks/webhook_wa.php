<?php
// Verificación del webhook (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $verify_token = 'TokenDeVerificacionINBA2025.650'; // debe coincidir con el que pongas en Meta

    $mode = $_GET['hub_mode'] ?? '';
    $token = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? '';

    if ($mode === 'subscribe' && $token === $verify_token) {
        echo $challenge;
    } else {
        http_response_code(403);
        echo 'Token inválido';
    }
    exit;
}

// Recepción de mensajes entrantes (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    file_put_contents('log_whatsapp.txt', $input . PHP_EOL, FILE_APPEND);

    // Aquí puedes analizar y guardar en tu CRM si llega un mensaje
    if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
        $mensaje = $data['entry'][0]['changes'][0]['value']['messages'][0];
        $de = $mensaje['from']; // número del remitente
        $texto = $mensaje['text']['body'] ?? '';

        // Aquí podrías insertar el mensaje en tu CRM
        // Ejemplo:
        // insertarProspectoDesdeMensaje($de, $texto);
    }

    http_response_code(200);
    echo "EVENT_RECEIVED";
    exit;
}