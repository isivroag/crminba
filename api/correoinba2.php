<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

require '../email/src/PHPMailer.php';
require '../email/src/SMTP.php';
require '../email/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);

$required = ['id_pros', 'nombre', 'telefono', 'correo', 'colaborador', 'email_colaborador'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Falta el campo: $field"]);
        exit;
    }
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'mail.tecniem.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'avisos@tecniem.com';
    $mail->Password = 'SrmCheca.2024';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->ContentType = 'text/html; charset=UTF-8';

    $mail->setFrom('avisos@tecniem.com', 'Sistema de Prospectos');
    $mail->addAddress($data['email_colaborador'], $data['colaborador']);

    $mail->Subject = 'Prospecto modificado: ' . $data['nombre'];
    $mail->AddEmbeddedImage('../assets/img/logoVerde.png', 'logo_empresa');

    $mail->Body = '
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #2d2d2d; background-color: #eaf1e5; }
            .container { background-color: #ffffff; padding: 20px; max-width: 800px; margin: auto; border: 1px solid #cddccf; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            .header { text-align: center; background-color: #153510; color: #ffffff; padding: 10px; }
            .content { padding: 20px; line-height: 1.6; }
            .info { background-color: #f2f7f0; padding: 15px; border-left: 5px solid #153510; margin: 15px 0; border-radius: 5px; }
            .footer { text-align: center; padding: 10px; font-size: 12px; color: #ffffff; background-color: #153510; }
            .titulo { text-align: center; color: #153510; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div style="background-color: #f2f7f0; padding: 10px; display: inline-block; border-radius: 10%;">
                    <img src="cid:logo_empresa" alt="Bosque de las Animas" style="height: 60px;">
                </div>
            </div>
            <div class="content">
                <div class="titulo">
                    <h1>Actualización de Prospecto</h1>
                </div>
                <p>Hola ' . htmlspecialchars($data['colaborador']) . ',</p>
                <p>Se ha actualizado la información del siguiente prospecto:</p>
                <div class="info">
                    <p><strong>Nombre:</strong> ' . htmlspecialchars($data['nombre']) . '</p>
                    <p><strong>Teléfono:</strong> <a href="tel:' . htmlspecialchars($data['telefono']) . '">' . htmlspecialchars($data['telefono']) . '</a></p>
                    <p><strong>Correo:</strong> <a href="mailto:' . htmlspecialchars($data['correo']) . '">' . htmlspecialchars($data['correo']) . '</a></p>
                </div>
                <p>Por favor revisa los cambios y realiza seguimiento si es necesario.</p>
            </div>
            <div class="footer">
                <p>© ' . date('Y') . ' INMOBILIARIA BOSQUE DE LAS ANIMAS SA DE CV. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>';

     //  Crear archivo vCard temporal
    $vcard = "BEGIN:VCARD\r\n";
    $vcard .= "VERSION:3.0\r\n";
    $vcard .= "FN:" . $data['nombre'] . "\r\n";
    $vcard .= "TEL;TYPE=CELL:" . $data['telefono'] . "\r\n";
    $vcard .= "EMAIL:" . $data['correo'] . "\r\n";
    $vcard .= "END:VCARD\r\n";

    $vcardPath = tempnam(sys_get_temp_dir(), 'vcard') . '.vcf';
    file_put_contents($vcardPath, $vcard);

    //  Adjuntar vCard al correo
    $mail->addAttachment($vcardPath, $data['nombre'] . '.vcf');

    $mail->send();

    echo json_encode(["success" => true, "message" => "Correo enviado por modificación del prospecto."]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al enviar el correo: " . $mail->ErrorInfo]);
}
?>
