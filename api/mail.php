<?php

require '../email/src/PHPMailer.php';
require '../email/src/SMTP.php';
require '../email/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$url = "http://tecniem.com/srmcheca/registro.php";
$subject = "Registro de cuenta";

$mail = new PHPMailer(true);
$id_prov = 2;
$email = 'isivroag@hotmail.com';
$nombre = 'ISRAEL IVAN ROMERO AGUILAR';

try {
    // Configuración del servidor SMTP

 

    $mail->isSMTP();
    $mail->Host = 'mail.tecniem.com'; // Cambia esto por tu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'avisos@tecniem.com'; // Tu correo
    $mail->Password = 'SrmCheca.2024';       // Tu contraseña
    $mail->SMTPSecure = ''; // o PHPMailer::ENCRYPTION_SMTPS
    $mail->Port = '2525'; // Cambia esto según tu proveedor (usualmente 587 o 465)

    // Configuración del correo
    $mail->setFrom('avisos@tecniem.com', 'CHECA S.A. DE C.V.');
    $mail->addAddress($email); // Destinatario
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body=' <html>
      <head>
        <title>Registro de usuario</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
          }
          .container {
            background-color: #fff;
            padding: 20px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 800px;
          }
          .header {
            text-align: center;
            background-color:#021b38;
          }
          .header img {
            padding:10px;
            max-width: 250px;
          }
          .content {
            line-height: 1.6;
          }
          .content a {
            color: #007bff;
            text-decoration: none;
          }
          .footer {
            text-align: center;
            padding: 5px;
            font-size: 12px;
            color: #FFFFFF;
            background-color:#021b38;
          }
          .titulo{
            justify-content: center;
            text-align:center;
          }
        </style>
      </head>
      <body>
        <div class="container">
          <div class="header">
                <img src="https://www.tecniem.com/srmcheca/assets/img/logoempresa.png" alt="Logo de la empresa">
              </div>
          <div class="content">
            <div class="titulo">
              <h1>CHECA S.A. DE C.V.</h1>      
            </div>
            <h2><strong>' . htmlspecialchars($nombre) . '<strong></h2>
            <p>Para registrar su usuario y contraseña, haga clic en el siguiente enlace:</p>
            <p><a href="' . htmlspecialchars($url) . '">Registrar usuario</a></p>
          </div>
          <div class="footer">
            <p>© 2025 CHECA S.A DE C.V. Todos los derechos reservados.</p>
          </div>
        </div>
      </body>
    </html>';
/*
    // Mensaje HTML
    $mail->Body = '
    <html>
      <head>
        <title>Registro de usuario</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
          }
          .container {
            background-color: #fff;
            padding: 20px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 800px;
          }
          .header {
            text-align: center;
            background-color:#021b38;
          }
          .header img {
            padding:10px;
            max-width: 250px;
          }
          .content {
            line-height: 1.6;
          }
          .content a {
            color: #007bff;
            text-decoration: none;
          }
          .footer {
            text-align: center;
            padding: 5px;
            font-size: 12px;
            color: #FFFFFF;
            background-color:#021b38;
          }
          .titulo{
            justify-content: center;
            text-align:center;
          }
        </style>
      </head>
      <body>
        <div class="container">
          <div class="header">
            <img src="https://www.tecniem.com/srmcheca/assets/img/logo.png" alt="Logo de la empresa">
          </div>
          <div class="content">
            <div class="titulo">
              <h1>CHECA S.A. DE C.V.</h1>      
            </div>
            <h2><strong>' . htmlspecialchars($nombre) . '<strong></h2>
            <p>Para registrar su usuario y contraseña, haga clic en el siguiente enlace:</p>
            <p><a href="' . htmlspecialchars($url) . '">Registrar usuario</a></p>
          </div>
          <div class="footer">
            <p>© 2025 CHECA S.A DE C.V. Todos los derechos reservados.</p>
          </div>
        </div>
      </body>
    </html>';
*/
    // Enviar correo
    $mail->send();
    http_response_code(200);
    echo json_encode(array("message" => "Correo electrónico enviado"));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Error al enviar el correo: " . $mail->ErrorInfo));
}
