<?php
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email']; 
    
    if (empty($username) || empty($password) || empty($email)) {
        die("El nombre de usuario, la contraseña y el email son obligatorios.");
    }

    if ($password !== $confirm_password) {
        die("Las contraseñas no coinciden.");
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        die("La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    $sql = "INSERT INTO users (username, password, email, user_type) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $user_type = 'admin';
    $stmt->bind_param("ssss", $username, $hashed_password, $email, $user_type);

    if ($stmt->execute()) {
    
        header("Location: inicio_sesion.php");
        exit();
    } else {
        echo "Error al registrar el administrador: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Administrador</title>
    <link rel="stylesheet" href="css/registro_a.css">
    <script>
    
        window.onload = function() {
            document.getElementById("username").value = '';
            document.getElementById("email").value = '';
            document.getElementById("password").value = '';
            document.getElementById("confirm_password").value = '';
        };
    </script>
</head>
<body>
    <div class="container">
        <h1>Registrar Administrador</h1>
        <form action="registro_admin.php" method="post">
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirma Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Registrar</button>
        </form>
    </div>
</body>
</html>
