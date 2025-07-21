<?php
require_once '../vendor/autoload.php'; // Ajusta si composer.json está en raíz

use Twilio\Rest\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $to = $data['telefono_vendedor'];
    $nombre = $data['nombre'];
    $telefono = $data['telefono'];
    $correo = $data['correo'];
    $interes = $data['interes'];

 
    $sid = getenv('TWILIO_SID');
    $token = getenv('TWILIO_TOKEN');
    $from = getenv('TWILIO_WHATSAPP_NUMBER');

    $client = new Client($sid, $token);

    $mensaje = "*📣 Nuevo prospecto asignado*\n\n" .
           "🧍 *Nombre:* $nombre\n" .
           "📞 *Teléfono:* $telefono\n" .
           "✉️ *Correo:* $correo\n" .
           "🔗 *Mostró Interés en:* $interes\n\n" .
           "📲 Contacta al prospecto:\n👉 https://wa.me/52$telefono\n\n" .
           "🟢 ¡No pierdas tiempo!";

    try {
        $client->messages->create("whatsapp:$to", [
            'from' => $from,
            'body' => $mensaje,
            'mediaUrl' => ['https://inba.tecniem.com/img/logoBosque.png']
        ]);
        echo json_encode(['success' => true, 'message' => 'Mensaje enviado']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
