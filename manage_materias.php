<?php

include 'config.php';
session_start();


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("Acceso denegado.");
}


if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM materias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_materias.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ? intval($_POST['id']) : null;
    $nombre = $_POST['nombre'];
    $especialidad_id = intval($_POST['especialidad_id']);

    if ($id) {

        $sql = "UPDATE materias SET nombre = ?, especialidad_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $nombre, $especialidad_id, $id);
    } else {

        $sql = "INSERT INTO materias (nombre, especialidad_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombre, $especialidad_id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: manage_materias.php");
    exit();
}


$sql = "
    SELECT 
        materias.*, 
        especialidades.nombre AS especialidad_nombre 
    FROM 
        materias
    LEFT JOIN 
        especialidades ON materias.especialidad_id = especialidades.id
";
$materias_result = $conn->query($sql);


$especialidades_result = $conn->query("SELECT id, nombre FROM especialidades");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Materias</title>
    <link rel="stylesheet" href="css/gestion_materias.css">
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
        <h1>Gestionar Materias</h1>


        <form action="manage_materias.php" method="post">
            <input type="hidden" name="id" id="materia_id">

            <label>Nombre de la Materia:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label>Especialidad:</label>
            <select name="especialidad_id" id="especialidad_id" required>
                <option value="">Seleccione una especialidad</option>
                <?php while ($especialidad = $especialidades_result->fetch_assoc()): ?>
                    <option value="<?php echo $especialidad['id']; ?>">
                        <?php echo htmlspecialchars($especialidad['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Guardar</button>
        </form>

        <h2>Lista de Materias</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $materias_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['especialidad_nombre']); ?></td>
                    <td>
                        <button onclick="editMateria(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                        <a href="manage_materias.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta materia?');">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editMateria(materia) {
            document.getElementById('materia_id').value = materia.id;
            document.getElementById('nombre').value = materia.nombre;
            document.getElementById('especialidad_id').value = materia.especialidad_id;
        }
    </script>
</body>
</html>
