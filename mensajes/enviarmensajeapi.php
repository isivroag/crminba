<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $to = $data['telefono_vendedor']; // Ejemplo: 5215551234567
    $nombre = $data['nombre'];
    $telefono = $data['telefono'];
    $correo = $data['correo'];
    $interes = $data['interes'];

    $token = getenv('WHATSAPP_API_TOKEN');
    $phoneNumberId = getenv('WHATSAPP_PHONE_NUMBER_ID');


  $payload = [
    "messaging_product" => "whatsapp",
    "to" => $to,
    "type" => "template",
    "template" => [
        "name" => "prospecto_nuevo",
        "language" => ["code" => "en"],
        "components" => [
            [
                "type" => "header",
                "parameters" => [
                    [
                        "type" => "image",
                        "image" => [
                            "link" => "https://inba.tecniem.com/img/logoBosque.png"
                        ]
                    ]
                ]
            ],
            [
                "type" => "body",
                "parameters" => [
                    ["type" => "text", "text" => $nombre],
                    ["type" => "text", "text" => $telefono],
                    ["type" => "text", "text" => $correo],
                    ["type" => "text", "text" => $interes],
                    ["type" => "text", "text" => $telefono]
                ]
            ]
        ]
    ]
];

/*$payload = [
    "messaging_product" => "whatsapp",
    "to" => $to,
    "type" => "template",
    "template" => [
        "name" => "prospecto_simple",
        "language" => ["code" => "es_MX"],
        "components" => [
            [
                "type" => "body",
                "parameters" => [
                    ["type" => "text", "text" => $nombre],
                    ["type" => "text", "text" => $telefono],
                    ["type" => "text", "text" => $correo],
                    ["type" => "text", "text" => $interes]
                ]
            ]
        ]
    ]
];*/
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($httpcode >= 200 && $httpcode < 300) {
        echo json_encode([
            "success" => true,
            "message" => "Mensaje enviado correctamente",
            "response" => json_decode($response, true)
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "http_code" => $httpcode,
            "error" => $error,
            "response" => json_decode($response, true)
        ]);
    }
}
