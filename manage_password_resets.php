<?php
// Incluye la configuración de la base de datos
include 'config.php';
session_start();

// Verifica si el usuario está logueado como administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("Acceso denegado.");
}

// Recuperar los restablecimientos de contraseñas
$sql = "SELECT id, email, token, expires, created_at FROM password_resets";
$password_resets_result = $conn->query($sql);

// Verifica si hay resultados
if ($password_resets_result->num_rows == 0) {
    $message = "No hay solicitudes de restablecimiento de contraseñas.";
} else {
    $message = "";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/gestion_u.css">
    <title>Gestionar Restablecimiento de Contraseñas</title>
</head>
<body>
    <header>
        <nav>
            <a href="manage_users.php">Gestionar Administradores</a>
            <a href="manage_alumnos.php">Gestionar Alumnos</a>
            <a href="manage_profesores.php">Gestionar Profesores</a>
            <a href="manage_materiales.php">Gestionar Materiales</a>
            <a href="manage_materias.php">Gestionar Materias</a>
            <a href="manage_especialidades.php">Gestionar Especialidades</a>
            <a href="manage_notas.php">Gestionar Notas</a>
            <a href="manage_asistencia_alumnos.php">Gestionar Asistencia Alumnos</a>
            <a href="manage_asistencia_profesores.php">Gestionar Asistencia Profesores</a>
            <a href="manage_password_resets.php">Gestionar Restablecimientos</a>
            <a href="logout.php" class="btn">Cerrar Sesión</a>
        </nav>
    </header>

    <div class="container">
        <h1>Gestionar Restablecimientos de Contraseña</h1>

        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>


        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Token</th>
                    <th>Expiración</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $password_resets_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['token']); ?></td>
                        <td><?php echo htmlspecialchars($row['expires']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <a href="delete_password_reset.php?id=<?php echo $row['id']; ?>"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar esta solicitud de restablecimiento?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql_delete = "DELETE FROM password_resets WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: manage_password_resets.php");
        exit();
    } else {
        echo "Error al eliminar el registro.";
    }

    $stmt->close();
}
?>
