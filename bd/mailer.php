<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Asegúrate de tener PHPMailer instalado via Composer

function enviarCorreo($destinatario, $nombre_destinatario, $asunto, $mensaje)
{
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'mail.tecniem.com'; // Cambia esto por tu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'avisos@tecniem.com'; // Tu correo
        $mail->Password = 'SrmCheca.2024';       // Tu contraseña
        $mail->SMTPSecure = ''; // o PHPMailer::ENCRYPTION_SMTPS
        $mail->Port = '2525'; // Cambia esto según tu proveedor (usualmente 587 o 465)
        $mail->CharSet = 'UTF-8';

        // Remitente
        $mail->setFrom('avisos@tecniem.com', 'Sistema de Prospectos');

        // Destinatario
        $mail->addAddress($destinatario, $nombre_destinatario);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;
        $mail->AltBody = strip_tags($mensaje);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Puedes registrar este error en un log
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}
