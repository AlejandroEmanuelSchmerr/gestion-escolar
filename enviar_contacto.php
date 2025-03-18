<?php
session_start(); // Iniciar la sesión

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Enviar el correo original
        $mail->setFrom('emanuelschmer777@gmail.com', 'Emanuel');
        $mail->addAddress('destinatario@ejemplo.com'); 
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Nuevo Mensaje de Contacto';
        $mail->Body    = "Nombre: $name<br>Correo: $email<br>Mensaje:<br>$message";
        $mail->AltBody = "Nombre: $name\nCorreo: $email\nMensaje:\n$message";
        $mail->send();


        $mail->clearAddresses(); 
        $mail->addAddress($email); 
        $mail->Subject = 'Gracias por tu mensaje';
        $mail->Body    = "Hola $name,<br><br>Gracias por ponerte en contacto con nosotros. Hemos recibido tu mensaje y nos comunicaremos contigo pronto.<br><br>Saludos,<br>Equipo de Soporte";
        $mail->AltBody = "Hola $name,\n\nGracias por ponerte en contacto con nosotros. Hemos recibido tu mensaje y nos comunicaremos contigo pronto.\n\nSaludos,\nColegio";
        $mail->send();

        
        $_SESSION['success_message'] = 'Mensaje enviado exitosamente.';


        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } catch (Exception $e) {

        $_SESSION['error_message'] = 'Error al enviar el mensaje: ' . $mail->ErrorInfo;


        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

