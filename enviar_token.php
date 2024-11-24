<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $user_type = '';
    $user_found = false;

    $sql_alumno = "SELECT * FROM alumnos WHERE email=?";
    $stmt = $conn->prepare($sql_alumno);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result_alumno = $stmt->get_result();

    if ($result_alumno->num_rows === 1) {
        $user = $result_alumno->fetch_assoc();
        $user_type = 'alumno';
        $user_found = true;
    }

    if (!$user_found) {
        $sql_profesor = "SELECT * FROM profesores WHERE email=?";
        $stmt = $conn->prepare($sql_profesor);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_profesor = $stmt->get_result();

        if ($result_profesor->num_rows === 1) {
            $user = $result_profesor->fetch_assoc();
            $user_type = 'profesor';
            $user_found = true;
        }
    }


    if (!$user_found) {
        $sql_admin = "SELECT * FROM users WHERE email=?";
        $stmt = $conn->prepare($sql_admin);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_admin = $stmt->get_result();

        if ($result_admin->num_rows === 1) {
            $user = $result_admin->fetch_assoc();
            $user_type = 'admin';
            $user_found = true;
        }
    }


    if ($user_found) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + 3600; 


        $sql = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token=?, expires=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $email, $token, $expires, $token, $expires);
        $stmt->execute();


        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'emanuelschmer777@gmail.com';
            $mail->Password = 'ncvb zoqn ewpo liab'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('emanuelschmer777@gmail.com', 'Emanuel');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Restablecimiento de Contraseña';
            $mail->Body    = 'Haz clic en el siguiente enlace para restablecer tu contraseña: <a href="http://localhost/registro/recuperar_contrasena.php?token=' . htmlspecialchars($token) . '&user_type=' . $user_type . '">Restablecer Contraseña</a>';
            $mail->AltBody = 'Haz clic en el siguiente enlace para restablecer tu contraseña: http://localhost/registro/recuperar_contrasena.php?token=' . htmlspecialchars($token) . '&user_type=' . $user_type;

            $mail->send();
            echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Enlace Enviado</title>
    <link rel="stylesheet" href="css/enviar_enlace.css">
</head>
<body>
    <div class="message">Se ha enviado un enlace de restablecimiento a tu correo electrónico.</div>
</body>
</html>';
        } catch (Exception $e) {
            echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="css/enviar_enlace.css">
</head>
<body>
    <div class="message">No se pudo enviar el correo. Error: ' . $mail->ErrorInfo . '</div>
</body>
</html>';
        }
    } else {
        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="css/enviar_enlace.css">
</head>
<body>
    <div class="message">No se encontró un usuario con ese correo electrónico.</div>
</body>
</html>';
    }

    $stmt->close();
    $conn->close();
}

