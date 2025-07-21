<?php
// Verificación inicial
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $verify_token = 'tecniem_messenger.2025'; // Igual que en Meta

    $mode = $_GET['hub_mode'] ?? '';
    $token = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? '';

    if ($mode === 'subscribe' && $token === $verify_token) {
        echo $challenge;
    } else {
        http_response_code(403);
    }
    exit;
}

// Recepción de mensajes
$input = json_decode(file_get_contents('php://input'), true);
file_put_contents("log_messenger.txt", json_encode($input) . PHP_EOL, FILE_APPEND);

// Verificamos si es un mensaje nuevo
if (isset($input['entry'][0]['messaging'][0])) {
    $msg = $input['entry'][0]['messaging'][0];
    $sender_id = $msg['sender']['id'];
    $mensaje = $msg['message']['text'] ?? null;

    if ($mensaje) {
        // Aquí iría la lógica de conversación tipo chatbot
        // Por ejemplo, guardar en sesión el progreso, o pedir nombre, correo, etc.

        // Ejemplo simple: responder automáticamente
        responder_messenger($sender_id, "Hola 👋, gracias por contactarnos. ¿Podrías decirnos tu nombre?");
    }
}

function responder_messenger($recipient_id, $mensaje) {
    $PAGE_ACCESS_TOKEN = 'TU_ACCESS_TOKEN_PAGINA';

    $url = "https://graph.facebook.com/v19.0/me/messages?access_token=$PAGE_ACCESS_TOKEN";

    $payload = [
        "recipient" => ["id" => $recipient_id],
        "message" => ["text" => $mensaje]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
