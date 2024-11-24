<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/login.css">
    <style>
        .alert-error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="back-button">
            <a class="btn" href="index.php">Volver al Inicio</a>
        </div>
        <div class="login-container">
            <h2>Iniciar Sesión</h2>

            <?php
            session_start();
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-error'>" . $_SESSION['error_message'] . "</div>";
                unset($_SESSION['error_message']); 
            }
            ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login">Iniciar Sesión</button>
            </form>
            <a class="btn-link" href="olvido_contraseña.php">Olvidé mi contraseña</a>
        </div>

        <div class="table-container">
            <h2>Opciones de Registro</h2>
            <a class="btn" href="registro_alumno.php">Registrar como Alumno</a>
            <a class="btn" href="registro_profesor.php">Registrar como Profesor</a>
        </div>
    </div>
</body>
</html>
