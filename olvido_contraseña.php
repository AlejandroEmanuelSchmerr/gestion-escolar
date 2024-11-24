<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Olvidé mi contraseña</title>
    <link rel="stylesheet" href="css/olvido_c.css">
</head>
<body>

<div class="container">
    <h2>Restablecer Contraseña</h2>
    <form action="enviar_token.php" method="post">
        <label for="email">Introduce tu correo electrónico:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Enviar enlace de restablecimiento</button>
    </form>
    <a href="inicio_sesion.php" class="btn-volver">Volver</a>
</div>

</body>
</html>
