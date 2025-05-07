<?php
require '../email/src/PHPMailer.php';
require '../email/src/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'mail.tecniem.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'avisos@tecniem.com';
    $mail->Password = 'SrmCheca.2024';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    
    $mail->setFrom('avisos@tecniem.com', 'Test');
    $mail->addAddress('isivroag@hotmail.com');
    $mail->Subject = 'Prueba SMTP';
    $mail->Body = 'Esto es una prueba';
    
    $mail->send();
    echo "Correo enviado!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}