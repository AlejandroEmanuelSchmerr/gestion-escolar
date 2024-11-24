<?php

include 'config.php';
session_start();

if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin'])) {
    die("Acceso denegado.");
}


if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM notas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_notas.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ? intval($_POST['id']) : null;
    $alumno_id = intval($_POST['alumno_id']);
    $profesor_id = intval($_POST['profesor_id']);
    $materia_id = intval($_POST['materia_id']);
    $nota = floatval($_POST['nota']);
    $comentario = $_POST['comentario'];
    $fecha = date('Y-m-d H:i:s');

    if ($id) {

        $sql = "UPDATE notas SET alumno_id = ?, profesor_id = ?, materia_id = ?, nota = ?, comentario = ?, fecha = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiidssi", $alumno_id, $profesor_id, $materia_id, $nota, $comentario, $fecha, $id);
    } else {

        $sql = "INSERT INTO notas (alumno_id, profesor_id, materia_id, nota, comentario, fecha) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiidss", $alumno_id, $profesor_id, $materia_id, $nota, $comentario, $fecha);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: manage_notas.php");
    exit();
}


$sql = "
    SELECT 
        notas.*, 
        alumnos.nombre AS alumno_nombre, 
        profesores.nombre AS profesor_nombre, 
        materias.nombre AS materia_nombre
    FROM 
        notas
    LEFT JOIN 
        alumnos ON notas.alumno_id = alumnos.id
    LEFT JOIN 
        profesores ON notas.profesor_id = profesores.id
    LEFT JOIN 
        materias ON notas.materia_id = materias.id
";
$notas_result = $conn->query($sql);


$alumnos = $conn->query("SELECT id, nombre FROM alumnos");
$profesores = $conn->query("SELECT id, nombre FROM profesores");
$materias = $conn->query("SELECT id, nombre FROM materias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Notas</title>
    <link rel="stylesheet" href="css/gestion_notas.css">
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
        <h1>Gestionar Notas</h1>


        <form action="manage_notas.php" method="post">
            <input type="hidden" name="id" id="nota_id">

            <label>Alumno:</label>
            <select name="alumno_id" id="alumno_id" required>
                <option value="">Seleccione un alumno</option>
                <?php while ($alumno = $alumnos->fetch_assoc()): ?>
                    <option value="<?php echo $alumno['id']; ?>">
                        <?php echo htmlspecialchars($alumno['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Profesor:</label>
            <select name="profesor_id" id="profesor_id" required>
                <option value="">Seleccione un profesor</option>
                <?php while ($profesor = $profesores->fetch_assoc()): ?>
                    <option value="<?php echo $profesor['id']; ?>">
                        <?php echo htmlspecialchars($profesor['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>


            <label>Materia:</label>
            <select name="materia_id" id="materia_id" required>
                <option value="">Seleccione una materia</option>
                <?php while ($materia = $materias->fetch_assoc()): ?>
                    <option value="<?php echo $materia['id']; ?>">
                        <?php echo htmlspecialchars($materia['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Nota:</label>
            <input type="number" step="0.01" name="nota" id="nota" required>

            <label>Comentario:</label>
            <textarea type="comentario"name="comentario" id="comentario"></textarea>

            <button type="submit">Guardar</button>
        </form>

        <h2>Lista de Notas</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Alumno</th>
                    <th>Profesor</th>
                    <th>Materia</th>
                    <th>Nota</th>
                    <th>Comentario</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $notas_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['alumno_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['profesor_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['materia_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['nota']); ?></td>
                    <td><?php echo htmlspecialchars($row['comentario']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                    <td>
                        <button onclick="editNota(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                        <a href="manage_notas.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta nota?');">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>

        function editNota(nota) {
            document.getElementById('nota_id').value = nota.id;
            document.getElementById('alumno_id').value = nota.alumno_id;
            document.getElementById('profesor_id').value = nota.profesor_id;
            document.getElementById('materia_id').value = nota.materia_id;
            document.getElementById('nota').value = nota.nota;
            document.getElementById('comentario').value = nota.comentario;
        }
    </script>
</body>
</html>
