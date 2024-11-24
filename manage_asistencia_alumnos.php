<?php

include 'config.php';
session_start();

if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'profesor'])) {
    die("Acceso denegado.");
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM asistencia_alumnos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_asistencia_alumnos.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ? intval($_POST['id']) : null;
    $alumno_id = intval($_POST['alumno_id']);
    $fecha = $_POST['fecha'];
    $estado = $_POST['estado']; 
    $codigo_qr = null;

    if (isset($_FILES['codigo_qr']) && $_FILES['codigo_qr']['error'] === UPLOAD_ERR_OK) {
        $codigo_qr_nombre = $_FILES['codigo_qr']['name'];
        $codigo_qr_temp = $_FILES['codigo_qr']['tmp_name'];
        $codigo_qr_destino = "uploads/" . $codigo_qr_nombre;

        if (move_uploaded_file($codigo_qr_temp, $codigo_qr_destino)) {
            $codigo_qr = $codigo_qr_destino;
        }
    }

    if ($id) {

        $sql = $codigo_qr ? 
            "UPDATE asistencia_alumnos SET alumno_id = ?, fecha = ?, estado = ?, codigo_qr = ? WHERE id = ?" :
            "UPDATE asistencia_alumnos SET alumno_id = ?, fecha = ?, estado = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($codigo_qr) {
            $stmt->bind_param("isssi", $alumno_id, $fecha, $estado, $codigo_qr, $id);
        } else {
            $stmt->bind_param("isssi", $alumno_id, $fecha, $estado, $id);
        }
    } else {

        $sql = "INSERT INTO asistencia_alumnos (alumno_id, fecha, estado, codigo_qr) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $alumno_id, $fecha, $estado, $codigo_qr);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: manage_asistencia_alumnos.php");
    exit();
}


$sql = "
    SELECT 
        asistencia_alumnos.*, 
        alumnos.nombre AS alumno_nombre
    FROM 
        asistencia_alumnos
    LEFT JOIN 
        alumnos ON asistencia_alumnos.alumno_id = alumnos.id
";
$asistencia_result = $conn->query($sql);


$alumnos = $conn->query("SELECT id, nombre FROM alumnos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Asistencias de Alumnos</title>
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
        <h1>Gestionar Asistencias de Alumnos</h1>


        <form action="manage_asistencia_alumnos.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" id="asistencia_id">

            
            <label>Alumno:</label>
            <select name="alumno_id" id="alumno_id" required>
                <option value="">Seleccione un alumno</option>
                <?php while ($alumno = $alumnos->fetch_assoc()): ?>
                    <option value="<?php echo $alumno['id']; ?>">
                        <?php echo htmlspecialchars($alumno['nombre']); ?>
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

            <label>Código QR (Imagen):</label>
            <input type="file" name="codigo_qr" id="codigo_qr" accept="image/*">

            <button type="submit">Guardar</button>
        </form>

        <h2>Lista de Asistencias de Alumnos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Alumno</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Código QR</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $asistencia_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['alumno_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($row['estado']); ?></td>
                    <td>
                        <?php if ($row['codigo_qr']): ?>
                            <a href="<?php echo htmlspecialchars($row['codigo_qr']); ?>" download>Descargar Código QR</a>
                        <?php else: ?>
                            Sin código QR
                        <?php endif; ?>
                    </td>
                    <td>
                        <button onclick="editAsistencia(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                        <a href="manage_asistencia_alumnos.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta asistencia?');">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>

        function editAsistencia(asistencia) {
            document.getElementById('asistencia_id').value = asistencia.id;
            document.getElementById('alumno_id').value = asistencia.alumno_id;
            document.getElementById('fecha').value = asistencia.fecha;
            document.getElementById('estado').value = asistencia.estado;
        }
    </script>
</body>
</html>
