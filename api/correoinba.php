<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

require '../email/src/PHPMailer.php';
require '../email/src/SMTP.php';
require '../email/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Obtener datos del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Validar datos requeridos
$required = ['id_pros', 'nombre', 'telefono', 'correo', 'colaborador', 'email_colaborador'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Falta el campo: $field"]);
        exit;
    }
}

// Configurar PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'mail.tecniem.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'avisos@tecniem.com';
    $mail->Password = 'SrmCheca.2024';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->ContentType = 'text/html; charset=UTF-8';

    // Remitente y destinatario
    $mail->setFrom('avisos@tecniem.com', 'Sistema de Prospectos');
    $mail->addAddress($data['email_colaborador'], $data['colaborador']);
    
    // Asunto y cuerpo del mensaje
    $mail->Subject = 'Nuevo prospecto asignado: ' . $data['nombre'];
    
    // Agregar imagen incrustada
    $mail->AddEmbeddedImage('../assets/img/logoempresa.jpg', 'logo_empresa');
    
    // Cuerpo del mensaje HTML
    $mail->Body = '
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; background-color: #f4f4f4; }
            .container { background-color: #fff; padding: 20px; margin: 0 auto; box-shadow: 0 0 20px rgba(0,0,0,0.1); max-width: 800px; }
            .header { text-align: center; background-color: #021b38; padding: 10px; }
            .content { line-height: 1.6; padding: 20px; }
            .info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .footer { text-align: center; padding: 10px; font-size: 12px; color: #FFFFFF; background-color: #021b38; }
            .titulo { text-align: center; color: #021b38; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="cid:logo_empresa" alt="Logo de la empresa" style="max-width: 250px;">
            </div>
            <div class="content">
                <div class="titulo">
                    <h1>Nuevo Prospecto Asignado</h1>
                </div>
                
                <p>Hola ' . htmlspecialchars($data['colaborador']) . ',</p>
                <p>Se te ha asignado un nuevo prospecto:</p>
                
                <div class="info">
                    <p><strong>Nombre:</strong> ' . htmlspecialchars($data['nombre']) . '</p>
                    <p><strong>Teléfono:</strong> <a href="tel:' . htmlspecialchars($data['telefono']) . '">' . htmlspecialchars($data['telefono']) . '</a></p>
                    <p><strong>Correo:</strong> <a href="mailto:' . htmlspecialchars($data['correo']) . '">' . htmlspecialchars($data['correo']) . '</a></p>
                </div>
                
                <p>Por favor contacta al prospecto dentro de las próximas 24 horas.</p>
            </div>
            <div class="footer">
                <p>© ' . date('Y') . ' Sistema de Prospectos. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>';

    // Enviar correo
    $mail->send();
    
    echo json_encode(["success" => true, "message" => "Correo enviado exitosamente al colaborador"]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al enviar el correo: " . $mail->ErrorInfo]);
}
?>