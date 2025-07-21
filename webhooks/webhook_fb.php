<?php
// Ruta: /webhooks/webhook_fb.php
header("Content-Type: application/json");

// ✅ Verificación inicial para Webhook de Meta
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $verify_token = 'tecniem_messenger.2025'; // Igual que en la app de Meta

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

// ✅ POST: Procesar mensajes entrantes de Messenger
$input = json_decode(file_get_contents("php://input"), true);

// Para depuración
file_put_contents("log_messenger.txt", json_encode($input, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

if (!isset($input['entry'][0]['messaging'][0])) {
    exit;
}

// 📦 Cargar datos
$msg = $input['entry'][0]['messaging'][0];
$sender_id = $msg['sender']['id'];
$mensaje = $msg['message']['text'] ?? null;

// 🔌 Conectar a BD
include_once '../bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Si hay mensaje de texto...
if ($mensaje && $conexion) {
    // Buscar si ya existe este sender
    $sql = "SELECT * FROM prospectos_fb WHERE sender_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$sender_id]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        // Nuevo registro: iniciar conversación
        $sql = "INSERT INTO prospectos_fb (sender_id, paso_actual) VALUES (?, 'nombre')";
        $conexion->prepare($sql)->execute([$sender_id]);
        responder_messenger($sender_id, "Hola 👋, gracias por contactarnos. ¿Podrías decirnos tu *nombre completo*?");
    } else {
        // Ya existe, continuar con el flujo
        $paso = $datos['paso_actual'];

        switch ($paso) {
            case 'nombre':
                $sql = "UPDATE prospectos_fb SET nombre = ?, paso_actual = 'correo' WHERE sender_id = ?";
                $conexion->prepare($sql)->execute([$mensaje, $sender_id]);
                responder_messenger($sender_id, "Gracias, *$mensaje*. ¿Cuál es tu *correo electrónico*?");
                break;

            case 'correo':
                $sql = "UPDATE prospectos_fb SET correo = ?, paso_actual = 'telefono' WHERE sender_id = ?";
                $conexion->prepare($sql)->execute([$mensaje, $sender_id]);
                responder_messenger($sender_id, "Perfecto. ¿Cuál es tu *número de WhatsApp*?");
                break;

            case 'telefono':
                $sql = "UPDATE prospectos_fb SET telefono = ?, paso_actual = 'interes' WHERE sender_id = ?";
                $conexion->prepare($sql)->execute([$mensaje, $sender_id]);
                responder_messenger($sender_id, "Gracias. ¿En qué *proyecto o servicio* estás interesado?");
                break;

            case 'interes':
                $sql = "UPDATE prospectos_fb SET interes = ?, paso_actual = 'completo' WHERE sender_id = ?";
                $conexion->prepare($sql)->execute([$mensaje, $sender_id]);

                // Traer datos para enviar a CRM
                $sql = "SELECT * FROM prospectos_fb WHERE sender_id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$sender_id]);
                $prospecto = $stmt->fetch(PDO::FETCH_ASSOC);

                // Aquí podrías insertar directamente en tu tabla principal de prospectos
                // o usar un webhook a otra ruta que haga ese proceso.

                responder_messenger($sender_id, "¡Gracias por tu interés, {$prospecto['nombre']}! 🎉 Un asesor se pondrá en contacto contigo pronto.");
                break;

            case 'completo':
                responder_messenger($sender_id, "Ya registramos tu información. Un asesor te contactará en breve.");
                break;

            default:
                responder_messenger($sender_id, "Gracias por tu mensaje. Un asesor te atenderá en breve.");
        }
    }
}

// ✅ Función para enviar mensajes a Messenger
function responder_messenger($recipient_id, $mensaje) {
    $PAGE_ACCESS_TOKEN = 'EAARTedMGLvwBO3WiKEYdOkOlGL6PsPra6dSbN66MkAXrtJrGYUPSTU7EKVU37hfRcfWmI0xfOOqTDeKPP1OFG9gq1o8gNAJecnTTOqvGstBuXPoORnHCbas9kZCEWCWbYVbPJxKOIl7ZA5ytdGf4WGP9cLBQU1CPNvJeensDZB1UJ1l3UN5gE4lzRH9FFhYJVk3iuQAWM16QNBOWmzjrwZDZD';


    $url = "https://graph.facebook.com/v19.0/me/messages?access_token=$PAGE_ACCESS_TOKEN";

    $payload = [
        "recipient" => ["id" => $recipient_id],
        "message" => ["text" => $mensaje]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
