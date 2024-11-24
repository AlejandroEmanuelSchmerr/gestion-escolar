<?php

include 'config.php';
session_start();

if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'profesor'])) {
    die("Acceso denegado.");
}


if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM asistencia WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_asistencia_profesores.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ? intval($_POST['id']) : null;
    $profesor_id = intval($_POST['profesor_id']);
    $fecha = $_POST['fecha'];
    $estado = $_POST['estado']; 

    if ($id) {

        $sql = "UPDATE asistencia SET profesor_id = ?, fecha = ?, estado = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $profesor_id, $fecha, $estado, $id);
    } else {

        $sql = "INSERT INTO asistencia (profesor_id, fecha, estado) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $profesor_id, $fecha, $estado);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: manage_asistencia_profesores.php");
    exit();
}

$sql = "
    SELECT 
        asistencia.*, 
        profesores.nombre AS profesor_nombre
    FROM 
        asistencia
    LEFT JOIN 
        profesores ON asistencia.profesor_id = profesores.id
";
$asistencia_result = $conn->query($sql);

$profesores = $conn->query("SELECT id, nombre FROM profesores");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Asistencias de Profesores</title>
    <link rel="stylesheet" href="css/gestion_u.css">
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
        <h1>Gestionar Asistencias de Profesores</h1>

        <form action="manage_asistencia_profesores.php" method="post">
            <input type="hidden" name="id" id="asistencia_id">


            <label>Profesor:</label>
            <select name="profesor_id" id="profesor_id" required>
                <option value="">Seleccione un profesor</option>
                <?php while ($profesor = $profesores->fetch_assoc()): ?>
                    <option value="<?php echo $profesor['id']; ?>">
                        <?php echo htmlspecialchars($profesor['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Fecha:</label>
            <input type="date" name="fecha" id="fecha" required>

            <label>Estado:</label>
            <select name="estado" id="estado" required>
                <option value="">Seleccione el estado</option>
                <option value="Presente">Presente</option>
                <option value="Ausente">Ausente</option>
                <option value="Tarde">Tarde</option>
            </select>

            <button type="submit">Guardar</button>
        </form>

        <h2>Lista de Asistencias</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profesor</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $asistencia_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['profesor_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($row['estado']); ?></td>
                    <td>
                        <button onclick="editAsistencia(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                        <a href="manage_asistencia_profesores.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta asistencia?');">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>

        function editAsistencia(asistencia) {
            document.getElementById('asistencia_id').value = asistencia.id;
            document.getElementById('profesor_id').value = asistencia.profesor_id;
            document.getElementById('fecha').value = asistencia.fecha;
            document.getElementById('estado').value = asistencia.estado;
        }
    </script>
</body>
</html>
