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
    $mail->addAddress($data['email_colaborador'], $data['colaborador']);
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
    $mail->addAddress($data['email_colaborador'], $data['colaborador']);

    // Asunto y cuerpo del mensaje
    $mail->Subject = 'Prospecto Modificado: ' . $data['nombre'];

    // Agregar imagen incrustada (asegurarse que la ruta es correcta)
    $logoPath = '../assets/img/logoBosque.png';
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoBase64 = 'data:image/png;base64,' . $logoData;

    //$mail->AddEmbeddedImage('../assets/img/logoVerde.png', 'logo_empresa');

    // Cuerpo del mensaje HTML con protección contra modo oscuro
    // Configuración del servidor SMTP (igual que antes)
    $mail->isSMTP();

    $mail->Body = '
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
                        <img src="cid:logo_empresa" alt="Bosque de las Animas" class="logo-img" style="height: 50px; width: auto; max-width: 180px; display: block;">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="email-content" style="padding: 20px; line-height: 1.5; font-family: Arial, sans-serif; color: #2d2d2d;">
                    <h2 class="email-title" style="color: #153510; text-align: center; margin-bottom: 20px;">Actualización de Prospecto</h2>
                    
                    <p style="margin: 0 0 15px 0;">Hola ' . htmlspecialchars($data['colaborador']) . ',</p>
                    <p style="margin: 0 0 15px 0;">Se ha actualizado la infomración del siguiente prospecto:</p>
                    
                    <div class="info-box" style="background-color: #f2f7f0; padding: 15px; border-left: 5px solid #153510; margin: 15px 0; border-radius: 0 5px 5px 0;">
                        <p style="margin: 0 0 10px 0;"><strong>Nombre:</strong> ' . htmlspecialchars($data['nombre']) . '</p>
                        <p style="margin: 0 0 10px 0;"><strong>Teléfono:</strong> <a href="tel:' . htmlspecialchars($data['telefono']) . '" style="color: #2d2d2d; text-decoration: underline;">' . htmlspecialchars($data['telefono']) . '</a></p>
                        <p style="margin: 0;"><strong>Correo:</strong> <a href="mailto:' . htmlspecialchars($data['correo']) . '" style="color: #2d2d2d; text-decoration: underline;">' . htmlspecialchars($data['correo']) . '</a></p>
                    </div>
                    
                    <p style="margin: 15px 0 0 0;">Por favor revisa los cambios y realiza seguimiento si es necesario.</p>
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
    $mail->Body = str_replace(
        '<img src="cid:logo_empresa"',
        '<img src="' . $logoBase64 . '" style="background-color: #ffffff; padding: 5px;"',
        $mail->Body
    );

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
