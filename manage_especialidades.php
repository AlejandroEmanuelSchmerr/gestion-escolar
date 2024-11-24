<?php

include 'config.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("Acceso denegado.");
}


if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM especialidades WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_especialidades.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ? intval($_POST['id']) : null;
    $nombre = $_POST['nombre'];

    if ($id) {

        $sql = "UPDATE especialidades SET nombre = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombre, $id);
    } else {

        $sql = "INSERT INTO especialidades (nombre) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: manage_especialidades.php");
    exit();
}


$sql = "SELECT * FROM especialidades";
$especialidades_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Especialidades</title>
    <link rel="stylesheet" href="css/gestion_especialidades.css">
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
        <h1>Gestionar Especialidades</h1>
        <form action="manage_especialidades.php" method="post">
            <input type="hidden" name="id" id="especialidad_id">

            <label>Nombre de la Especialidad:</label>
            <input type="text" name="nombre" id="nombre" required>

            <button type="submit">Guardar</button>
        </form>

        <h2>Lista de Especialidades</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $especialidades_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td>
                        <button onclick="editEspecialidad(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                        <a href="manage_especialidades.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta especialidad?');">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        
        function editEspecialidad(especialidad) {
            document.getElementById('especialidad_id').value = especialidad.id;
            document.getElementById('nombre').value = especialidad.nombre;
        }
    </script>
</body>
</html>
