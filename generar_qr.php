<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'alumno') {
    header('Location: index.php');
    exit();
}

include 'config.php';

// Incluir archivos de PHPMailer manualmente desde la carpeta phpmailer
require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$username = $_SESSION['username'];


$sql = "SELECT id, nombre, apellido, email FROM alumnos WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();
$alumno = $resultado->fetch_assoc();

if (!$alumno) {
    die("Alumno no encontrado.");
}


$fecha = date('Y-m-d');


$sql_check_qr = "SELECT * FROM asistencia_alumnos WHERE alumno_id = ? AND fecha = ?";
$stmt_check = $conn->prepare($sql_check_qr);
$stmt_check->bind_param("is", $alumno['id'], $fecha);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {

    $url_qr = 'http://localhost/registro/registrar_asistencia.php?fecha=' . $fecha . '&alumno_id=' . $alumno['id'];

    $qrCodePath = 'qrs/qr_' . $alumno['id'] . '_' . $fecha . '.png';
    $qrCode = generateQRCode($url_qr, $qrCodePath);  

    if ($qrCode) {

        $sql_insert_qr = "INSERT INTO asistencia_alumnos (alumno_id, fecha, qr_codificado) VALUES (?, ?, ?)";
        $stmt_insert_qr = $conn->prepare($sql_insert_qr);
        $stmt_insert_qr->bind_param("iss", $alumno['id'], $fecha, $qrCodePath);
        $stmt_insert_qr->execute();

        
        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            
            $mail->setFrom('tu_email@gmail.com', 'ipet57');
            $mail->addAddress($alumno['email'], $alumno['nombre'] . ' ' . $alumno['apellido']);  // Correo del alumno

            
            $mail->isHTML(true);
            $mail->Subject = 'Asistencia registrada';
            $mail->Body    = 'Hola ' . $alumno['nombre'] . ',<br>Tu QR de asistencia para el día ' . $fecha . ' ha sido generado exitosamente. Puedes usar el siguiente enlace para escanearlo: <br><img src="http://localhost/mi_proyecto/' . $qrCodePath . '" alt="QR de asistencia">';


            $mail->send();
            echo 'Correo de confirmación enviado';
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }

        
        echo "<h1>QR para asistencia del día: $fecha</h1>";
        echo "<img src='$qrCodePath' alt='QR de asistencia'>";
    } else {
        echo 'Hubo un error al generar el QR.';
    }
} else {

    $qrData = $result_check->fetch_assoc();
    echo "<h1>QR para asistencia del día: $fecha</h1>";
    echo "<img src='" . $qrData['qr_codificado'] . "' alt='QR de asistencia'>";
}


function generateQRCode($data, $outputPath) {
    $size = 10;
    $margin = 2;

    // Crear la imagen del QR
    $image = imagecreatetruecolor($size * 21 + 2 * $margin, $size * 21 + 2 * $margin);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    imagefilledrectangle($image, 0, 0, $size * 21 + 2 * $margin, $size * 21 + 2 * $margin, $white);

    $qrCode = new \Endroid\QrCode\QrCode($data);
    $qrCode->setSize(210);
    $qrCode->setMargin(10);

    $outputImage = imagecreatefromstring($qrCode->writeString());
    imagecopyresampled($image, $outputImage, $margin, $margin, 0, 0, $size * 21, $size * 21, imagesx($outputImage), imagesy($outputImage));
    
    imagepng($image, $outputPath);
    imagedestroy($image);
    imagedestroy($outputImage);

    return $outputPath;
}
?>
