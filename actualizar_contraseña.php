<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM password_resets WHERE token = ? AND expires > ?";
    $stmt = $conn->prepare($sql);
    $current_time = time();
    $stmt->bind_param("si", $token, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $reset = $result->fetch_assoc();
        $email = $reset['email'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE alumnos SET password=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE profesores SET password=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        $sql = "DELETE FROM password_resets WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        header('Location: index.php');
        exit();
    } else {
        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="css/actualizar_contrasena.css">
</head>
<body>
    <div class="message">El enlace de restablecimiento es inválido o ha expirado.</div>
</body>
</html>';
    }

    $stmt->close();
    $conn->close();
}
