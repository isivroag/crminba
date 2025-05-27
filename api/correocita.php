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

// Validar campos requeridos
$required = ['id_pros', 'nombre', 'telefono', 'correo', 'nom_colaborador', 'correo_colaborador', 'fecha_seg', 'tipo_seg', 'observaciones'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Falta el campo: $field"]);
        exit;
    }
}

// Crear objeto PHPMailer
$mail = new PHPMailer(true);

try {
    /*
    $mail->isSMTP();
    $mail->Host = 'mail.tecniem.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'avisos@tecniem.com';
    $mail->Password = 'SrmCheca.2024';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->ContentType = 'text/html; charset=UTF-8';

    $mail->setFrom('avisos@tecniem.com', 'INBA. NOTIFICACIONES');
    $mail->addAddress($data['correo_colaborador'], $data['nom_colaborador']);
*/
    // Configuración del servidor SMTP 2
    $mail->isSMTP();
    $mail->Host = 'mail.bosquedelasanimas.com.mx';
    $mail->SMTPAuth = true;
    $mail->Username = 'notificaciones@bosquedelasanimas.com.mx';
    $mail->Password = 'SistemaCRMBosque.2025';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->ContentType = 'text/html; charset=UTF-8';
    // Remitente y destinatario
    $mail->setFrom('notificaciones@bosquedelasanimas.com.mx', 'INBA. NOTIFICACIONES');
    $mail->addAddress($data['correo_colaborador'], $data['nom_colaborador']);

    $mail->Subject = 'Seguimiento agendado: ' . $data['nombre'];

    // Agregar imagen incrustada (asegurarse que la ruta es correcta)
    $logoPath = '../assets/img/logoBosque.png';
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoBase64 = 'data:image/png;base64,' . $logoData;

    // Crear contenido del correo en HTML
    $mail->isHTML(true);
    $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style type="text/css">
        /* Reset para clientes de email */
        body, html {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
            background-color: #ffffff !important;
        }
        
        /* Contenedor principal */
        .email-wrapper {
            width: 100% !important;
            background-color: #ffffff !important;
            padding: 20px 0 !important;
        }
        
        /* Contenedor del contenido */
        .email-container {
            max-width: 600px !important;
            width: 100% !important;
            margin: 0 auto !important;
            background-color: #ffffff !important;
            border: 1px solid #cddccf !important;
            border-collapse: collapse !important;
        }
        
        /* Cabecera */
        .email-header {
            background-color: #153510 !important;
            padding: 15px !important;
            text-align: center !important;
        }
        
        /* Contenedor del logo con fondo blanco */
        .logo-wrapper {
            background-color: #ffffff !important;
            padding: 10px !important;
            display: inline-block !important;
            border-radius: 8px !important;
            margin: 5px 0 !important;
        }
        
        /* Imagen del logo */
        .logo-img {
            height: 50px !important;
            width: auto !important;
            max-width: 180px !important;
            display: block !important;
            border: 0 !important;
            outline: none !important;
            text-decoration: none !important;
        }
        
        /* Contenido principal */
        .email-content {
            padding: 20px !important;
            line-height: 1.5 !important;
            font-family: Arial, sans-serif !important;
            color: #2d2d2d !important;
        }
        
        /* Sección de información */
        .info-box {
            background-color: #f2f7f0 !important;
            padding: 15px !important;
            border-left: 5px solid #153510 !important;
            margin: 15px 0 !important;
            border-radius: 0 5px 5px 0 !important;
        }
        
        /* Pie de página */
        .email-footer {
            background-color: #153510 !important;
            color: #ffffff !important;
            text-align: center !important;
            padding: 10px !important;
            font-size: 12px !important;
        }
        
        /* Título */
        .email-title {
            color: #153510 !important;
            text-align: center !important;
            margin-bottom: 20px !important;
        }
        
        /* Media queries para móviles */
        @media screen and (max-width: 480px) {
            .email-container {
                width: 100% !important;
                min-width: 100% !important;
            }
            .logo-img {
                height: 40px !important;
                max-width: 150px !important;
            }
            .email-content {
                padding: 15px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #ffffff;">
    <!--[if mso]>
    <table role="presentation" border="0" cellspacing="0" cellpadding="0" width="100%" style="background-color: #ffffff;">
    <tr>
    <td>
    <![endif]-->
    
    <div class="email-wrapper" style="width: 100%; background-color: #ffffff; padding: 20px 0;">
        <table class="email-container" align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto; background-color: #ffffff; border: 1px solid #cddccf;">
            <tr>
                <td class="email-header" style="background-color: #153510; padding: 15px; text-align: center;">
                    <div class="logo-wrapper" style="background-color: #ffffff; padding: 10px; display: inline-block; border-radius: 8px; margin: 5px 0;">
                        <img src="' . $logoBase64 . '" alt="Bosque de las Animas" class="logo-img" style="height: 50px; width: auto; max-width: 180px; display: block;">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="email-content" style="padding: 20px; line-height: 1.5; font-family: Arial, sans-serif; color: #2d2d2d;">
                    <h2 class="email-title" style="color: #153510; text-align: center; margin-bottom: 20px;">Seguimiento Agendado</h2>
                    
                    <p style="margin: 0 0 15px 0;">Hola <strong>' . htmlspecialchars($data['nom_colaborador']) . '</strong>,</p>
                    <p style="margin: 0 0 15px 0;">Se ha registrado un seguimiento para el siguiente prospecto:</p>
                    
                    <div class="info-box" style="background-color: #f2f7f0; padding: 15px; border-left: 5px solid #153510; margin: 15px 0; border-radius: 0 5px 5px 0;">
                        <p style="margin: 0 0 10px 0;"><strong>Nombre:</strong> ' . htmlspecialchars($data['nombre']) . '</p>
                        <p style="margin: 0 0 10px 0;"><strong>Teléfono:</strong> <a href="tel:' . htmlspecialchars($data['telefono']) . '" style="color: #2d2d2d; text-decoration: underline;">' . htmlspecialchars($data['telefono']) . '</a></p>
                        <p style="margin: 0 0 10px 0;"><strong>Correo:</strong> <a href="mailto:' . htmlspecialchars($data['correo']) . '" style="color: #2d2d2d; text-decoration: underline;">' . htmlspecialchars($data['correo']) . '</a></p>
                        <p style="margin: 0 0 10px 0;"><strong>Fecha del seguimiento:</strong> ' . htmlspecialchars($data['fecha_seg']) . '</p>
                        <p style="margin: 0 0 10px 0;"><strong>Tipo de seguimiento:</strong> ' . htmlspecialchars($data['tipo_seg']) . '</p>
                        <p style="margin: 0;"><strong>Observaciones:</strong> ' . nl2br(htmlspecialchars($data['observaciones'])) . '</p>
                    </div>
                    
                    <p style="margin: 15px 0 0 0;">Se adjunta una cita para que puedas agregarla a tu calendario.</p>
                </td>
            </tr>
            <tr>
                <td class="email-footer" style="background-color: #153510; color: #ffffff; text-align: center; padding: 10px; font-size: 12px;">
                    <p style="margin: 0;">© ' . date('Y') . ' INMOBILIARIA BOSQUE DE LAS ANIMAS SA DE CV. Todos los derechos reservados.</p>
                </td>
            </tr>
        </table>
    </div>
    
    <!--[if mso]>
    </td>
    </tr>
    </table>
    <![endif]-->
</body>
</html>';

    // Crear archivo ICS
    $icsContent = "BEGIN:VCALENDAR\r\n";
    $icsContent .= "VERSION:2.0\r\n";
    $icsContent .= "PRODID:-//Tecniem//Seguimiento Prospecto//ES\r\n";
    $icsContent .= "BEGIN:VEVENT\r\n";
    $icsContent .= "UID:" . uniqid() . "@tecniem.com\r\n";
    $icsContent .= "DTSTAMP:" . date('Ymd\THis\Z') . "\r\n";

    $start = date('Ymd\THis', strtotime($data['fecha_seg']));
    $end = date('Ymd\THis', strtotime($data['fecha_seg'] . ' +1 hour'));

    $icsContent .= "DTSTART:$start\r\n";
    $icsContent .= "DTEND:$end\r\n";
    $icsContent .= "SUMMARY:Seguimiento - {$data['tipo_seg']} - {$data['nombre']}\r\n";
    $icsContent .= "DESCRIPTION:" . preg_replace("/\r\n|\r|\n/", ' ', $data['observaciones']) . "\r\n";
    $icsContent .= "LOCATION:Vía telefónica o correo\r\n";
    $icsContent .= "STATUS:CONFIRMED\r\n";
    $icsContent .= "END:VEVENT\r\n";
    $icsContent .= "END:VCALENDAR\r\n";

    $icsPath = tempnam(sys_get_temp_dir(), 'seguimiento_') . '.ics';
    file_put_contents($icsPath, $icsContent);
    $mail->addAttachment($icsPath, 'seguimiento_' . $data['nombre'] . '.ics');

    $mail->send();

    echo json_encode(["success" => true, "message" => "Correo con cita enviado exitosamente"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al enviar el correo: " . $mail->ErrorInfo]);
}
