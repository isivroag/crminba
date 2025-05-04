<?php
// Configuración de la conexión a la base de datos
$servername = "tecniem.com";  // Nombre del servidor MySQL
$username = "tecniemc_srmcheca";      // Nombre de usuario de MySQL
$password = "SrmCheca.2024";   // Contraseña de MySQL
$database = "tecniemc_srmcheca"; // Nombre de la base de datos
date_default_timezone_set('America/Mexico_City');


require '../email/src/PHPMailer.php';
require '../email/src/SMTP.php';
require '../email/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Función para procesar la solicitud
function process_request()
{
  global $conn;

  // Obtener los datos del cuerpo de la solicitud
  $data = json_decode(file_get_contents('php://input'), true);

  // Verificar si se recibieron los parámetros necesarios
  if (!isset($data['id_prov'], $data['email'])) {
    http_response_code(400);
    echo json_encode(array("message" => "Faltan parametros obligatorios."));
    return;
  }




  $id_prov = intval($data['id_prov']);
  $email = $conn->real_escape_string($data['email']);
  $nombre = $conn->real_escape_string($data['nombre']);


  $token = bin2hex(random_bytes(16));
  $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

  $sql = "INSERT INTO usuariotoken (id_prov,email, token, token_expiry) VALUES ('$id_prov','$email', '$token', '$token_expiry')";

  if ($conn->query($sql) === TRUE) {

    $url = "http://tecniem.com/srmcheca/registrop.php?token=$token";
    $subject = "Registro de cuenta";

  

    
    $mail = new PHPMailer(true);

    try {
      // Configuración del servidor SMTP
      $mail->isSMTP();
      $mail->Host = 'mail.tecniem.com'; // Cambia esto por tu servidor SMTP
      $mail->SMTPAuth = true;
      $mail->Username = 'avisos@tecniem.com'; // Tu correo
      $mail->Password = 'SrmCheca.2024';       // Tu contraseña
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // o PHPMailer::ENCRYPTION_SMTPS
      $mail->Port = '587'; // Cambia esto según tu proveedor (usualmente 587 o 465)
      $mail->CharSet = 'UTF-8';  // Asegurarse de que el correo esté en UTF-8
      $mail->ContentType = 'text/html; charset=UTF-8';  // Definir tipo de contenido

      // Configuración del correo
      $mail->setFrom('avisos@tecniem.com', 'CHECA S.A. DE C.V.');
      $mail->addAddress($email); // Destinatario
      $mail->isHTML(true);
     
      $mail->Subject = $subject;
      $mail->AddEmbeddedImage('../assets/img/logoempresa.jpg', 'logo_empresa');
      // Mensaje HTML
      $mail->Body = '
        <html>
          <head>
          <meta charset="UTF-8">  <!-- Definir codificación aquí -->
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
                <img src="cid:logo_empresa" alt="Logo de la empresa">  <!-- Usar cid en el src -->
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

      // Enviar correo
      $mail->send();
      http_response_code(200);
      echo json_encode(array("message" => "Correo electrónico enviado"));
    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(array("message" => "Error al enviar el correo: " . $mail->ErrorInfo));
    }
  } else {
    echo json_encode(array("Error: " . $sql . "<br>" . $conn->error));
  }

 
}

// Manejar el método de solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  process_request();
} else {
  http_response_code(405);
  echo json_encode(array("message" => "Metodo no permitido."));
}

// Cerrar conexión
$conn->close();
?>