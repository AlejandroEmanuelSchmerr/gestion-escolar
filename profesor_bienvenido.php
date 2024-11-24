<?php
session_start();
include 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'profesor') {
    header('Location: index.php');
    exit();
}


$profesor_username = $_SESSION['username'];
$sql_profesor = "SELECT * FROM profesores WHERE username = ?";
$stmt = $conn->prepare($sql_profesor);
$stmt->bind_param("s", $profesor_username);
$stmt->execute();
$result_profesor = $stmt->get_result();
$profesor = $result_profesor->fetch_assoc();

if (!$profesor) {
    echo "<div class='alert alert-error'>Profesor no encontrado.</div>";
    exit();
}


$especialidad_id = $profesor['especialidad_id'];

$sql_materias = "SELECT id, nombre FROM materias WHERE especialidad_id = ?";
$stmt_materias = $conn->prepare($sql_materias);
$stmt_materias->bind_param("i", $especialidad_id);
$stmt_materias->execute();
$result_materias = $stmt_materias->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_professor'])) {
        
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $email = $_POST['email'];
        $dni = $_POST['dni'];
        $año = $_POST['año'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $materia_id = $_POST['materia_id'];  


        $sql_update_profesor = "UPDATE profesores SET nombre = ?, apellido = ?, email = ?, dni = ?, año = ?, fecha_nacimiento = ?, materia_id = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update_profesor);
        $stmt_update->bind_param("ssssssii", $nombre, $apellido, $email, $dni, $año, $fecha_nacimiento, $materia_id, $profesor['id']);

        if ($stmt_update->execute()) {
            echo "<div class='alert alert-success'>Datos del profesor actualizados correctamente.</div>";
        } else {
            echo "<div class='alert alert-error'>Error: " . $stmt_update->error . "</div>";
        }
    }

    if (isset($_POST['registrar_asistencia'])) {
        $fecha = $_POST['fecha'];
        $estado = $_POST['estado'];
        $profesor_id = $profesor['id'];

        $sql_registrar_asistencia = "INSERT INTO asistencia (profesor_id, fecha, estado) VALUES (?, ?, ?)";
        $stmt_registrar = $conn->prepare($sql_registrar_asistencia);
        $stmt_registrar->bind_param("iss", $profesor_id, $fecha, $estado);

        if ($stmt_registrar->execute()) {
            echo "<div class='alert alert-success'>Asistencia registrada correctamente.</div>";
        } else {
            echo "<div class='alert alert-error'>Error al registrar asistencia: " . $stmt_registrar->error . "</div>";
        }
    }

    if (isset($_POST['update_attendance'])) {
        $asistencia_id = $_POST['asistencia_id'];
        $estado = $_POST['estado'];

        $sql_update_asistencia = "UPDATE asistencia SET estado = ? WHERE id = ? AND profesor_id = ?";
        $stmt_update_asistencia = $conn->prepare($sql_update_asistencia);
        $stmt_update_asistencia->bind_param("sii", $estado, $asistencia_id, $profesor['id']);

        if ($stmt_update_asistencia->execute()) {
            echo "<div class='alert alert-success'>Asistencia actualizada correctamente.</div>";
        } else {
            echo "<div class='alert alert-error'>Error al actualizar asistencia: " . $stmt_update_asistencia->error . "</div>";
        }
    }
}

$sql_asistencias = "SELECT a.id, a.fecha, a.estado FROM asistencia a WHERE a.profesor_id = ?";
$stmt_asistencias = $conn->prepare($sql_asistencias);
$stmt_asistencias->bind_param("i", $profesor['id']);
$stmt_asistencias->execute();
$result_asistencias = $stmt_asistencias->get_result();


$materia_id = $profesor['materia_id'];
$sql_materia_info = "SELECT * FROM materias WHERE id = ?";
$stmt_materia_info = $conn->prepare($sql_materia_info);
$stmt_materia_info->bind_param("i", $materia_id);
$stmt_materia_info->execute();
$result_materia_info = $stmt_materia_info->get_result();
$materia_info = $result_materia_info->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido Profesor</title>
    <link rel="stylesheet" href="css/profesor_bienvenido.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="profesor_bienvenido.php">Inicio</a></li>
                <li><a href="gestionar_materias.php">Gestionar Contenido</a></li>
                <li><a href="gestionar_notas.php">Gestionar Notas</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <h1>Bienvenido Profesor, <?php echo htmlspecialchars($profesor['nombre']); ?></h1>

    <div class="table-container">
        <h2>Tus datos</h2>
        <form action="" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($profesor['nombre']); ?>" required>

            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" value="<?php echo htmlspecialchars($profesor['apellido']); ?>" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($profesor['email']); ?>" required>

            <label for="dni">DNI:</label>
            <input type="text" name="dni" value="<?php echo htmlspecialchars($profesor['dni']); ?>" required>

            <label for="año">Año:</label>
            <input type="text" name="año" value="<?php echo htmlspecialchars($profesor['año']); ?>" required>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($profesor['fecha_nacimiento']); ?>" required>

            <label for="materia_id">Materia:</label>
            <select name="materia_id" required>
                <option value="">Selecciona una materia</option>
                <?php while ($materia = $result_materias->fetch_assoc()): ?>
                    <option value="<?php echo $materia['id']; ?>" <?php if ($materia['id'] == $profesor['materia_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($materia['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" name="update_professor">Actualizar Datos</button>
        </form>


        <h2>Información sobre tu Materia</h2>
        <?php if ($materia_info): ?>
            <p><strong>Materia:</strong> <?php echo htmlspecialchars($materia_info['nombre']); ?></p>
        <?php else: ?>
            <p>No se encontró información sobre la materia.</p>
        <?php endif; ?>

        <h2>Registrar Asistencia</h2>
        <form action="" method="post">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" required>
            
            <label for="estado">Estado:</label>
            <select name="estado" required>
                <option value="Presente">Presente</option>
                <option value="Ausente">Ausente</option>
                <option value="Justificado">Justificado</option>
            </select>
            
            <button type="submit" name="registrar_asistencia">Registrar Asistencia</button>
        </form>


        <h2>Tus Asistencias</h2>
        <?php if ($result_asistencias->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_asistencias->fetch_assoc()): ?>
                        <tr>
                            <form action="" method="post">
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                <td>
                                    <select name="estado">
                                        <option value="Presente" <?php if ($row['estado'] == 'Presente') echo 'selected'; ?>>Presente</option>
                                        <option value="Ausente" <?php if ($row['estado'] == 'Ausente') echo 'selected'; ?>>Ausente</option>
                                        <option value="Justificado" <?php if ($row['estado'] == 'Justificado') echo 'selected'; ?>>Justificado</option>
                                    </select>
                                </td>
                                <input type="hidden" name="asistencia_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <td>
                                    <button type="submit" name="update_attendance">Modificar</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes asistencias registradas.</p>
        <?php endif; ?>


        <div class="actions">
            <a href="generate_professors_pdf.php?profesor_id=<?php echo htmlspecialchars($profesor['id']); ?>" class="btn">Generar PDF</a>
        </div>
    </div>
</body>
</html>
