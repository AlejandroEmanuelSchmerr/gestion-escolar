<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM password_resets WHERE token = ? AND expires > ?";
    $stmt = $conn->prepare($sql);
    $current_time = time();
    $stmt->bind_param("si", $token, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="css/recuperar_contrasena.css">
    <script>
        function validatePassword() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const errorMessage = document.getElementById("error_message");
            const regex = /^(?=.*[A-Z])(?=.*\\d).{8,}$/; // Al menos 8 caracteres, 1 mayúscula y 1 número

            if (!regex.test(password)) {
                errorMessage.textContent = "La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.";
                return false;
            }

            if (password !== confirmPassword) {
                errorMessage.textContent = "Las contraseñas no coinciden.";
                return false;
            }

            errorMessage.textContent = "";
            return true;
        }
    </script>
</head>
<body>
    <form action="actualizar_contraseña.php" method="post" onsubmit="return validatePassword()">
        <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
        <label for="password">Nueva contraseña:</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Confirma la nueva contraseña:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <small id="error_message" style="color:red;"></small>
        <button type="submit">Restablecer contraseña</button>
    </form>
</body>
</html>';
    } else {
        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="css/recuperar_contrasena.css">
</head>
<body>
    <div class="message">El enlace de restablecimiento es inválido o ha expirado.</div>
</body>
</html>';
    }

    $stmt->close();
    $conn->close();
}
