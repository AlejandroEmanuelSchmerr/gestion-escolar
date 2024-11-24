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


$sql_materia = "SELECT m.id, m.nombre
                FROM materias m
                WHERE m.especialidad_id = ?";  
$stmt_materia = $conn->prepare($sql_materia);
$stmt_materia->bind_param("i", $profesor['especialidad_id']);
$stmt_materia->execute();
$result_materia = $stmt_materia->get_result();


if ($result_materia->num_rows == 0) {
    echo "<p class='alert error'>Error: No se ha encontrado una materia asociada al profesor.</p>";
    exit;
}

$materia_actual = $result_materia->fetch_assoc();  

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_nota'])) {
    $alumno_id = $_POST['alumno_id'];
    $materia_id = $materia_actual['id']; 
    $nota = $_POST['nota'];
    $comentario = $_POST['comentario'];

    $sql_insert_nota = "INSERT INTO notas (alumno_id, profesor_id, materia_id, nota, comentario, fecha)
                        VALUES (?, ?, ?, ?, ?, CURDATE())";
    $stmt_insert = $conn->prepare($sql_insert_nota);
    $stmt_insert->bind_param("iiids", $alumno_id, $profesor['id'], $materia_id, $nota, $comentario);

    if ($stmt_insert->execute()) {
        echo "<p class='alert'>Nota guardada con éxito.</p>";
    } else {
        echo "<p class='alert error'>Error al guardar la nota: " . $stmt_insert->error . "</p>";
    }
}

$sql_alumnos = "SELECT a.id, a.nombre, a.apellido 
                FROM alumnos a 
                JOIN materias m ON a.materia_id = m.id 
                WHERE m.especialidad_id = ?";
$stmt_alumnos = $conn->prepare($sql_alumnos);
$stmt_alumnos->bind_param("i", $profesor['especialidad_id']);
$stmt_alumnos->execute();
$result_alumnos = $stmt_alumnos->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Notas</title>
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
    <div class="container">
        <h1>Gestionar Notas</h1>
        <form method="post">
            <label for="alumno_id">Alumno:</label>
            <select name="alumno_id" required>
                <option value="">Selecciona un alumno</option>
                <?php while ($alumno = $result_alumnos->fetch_assoc()): ?>
                    <option value="<?php echo $alumno['id']; ?>"><?php echo htmlspecialchars($alumno['nombre']) . ' ' . htmlspecialchars($alumno['apellido']); ?></option>
                <?php endwhile; ?>
            </select>

            <input type="hidden" name="materia_id" value="<?php echo $materia_actual['id']; ?>">

            <label for="nota">Nota:</label>
            <input type="number" name="nota" step="0.01" required>

            <label for="comentario">Comentario:</label>
            <textarea name="comentario"></textarea>

            <button type="submit" name="guardar_nota">Guardar Nota</button>
        </form>

        <h2>Notas Registradas</h2>
        <?php

        $sql_notas = "SELECT n.id, a.nombre AS alumno_nombre, a.apellido AS alumno_apellido, m.nombre AS materia_nombre, n.nota, n.comentario, n.fecha
                      FROM notas n
                      JOIN alumnos a ON n.alumno_id = a.id
                      JOIN materias m ON n.materia_id = m.id
                      WHERE n.profesor_id = ? AND n.materia_id = ?";
        $stmt_notas = $conn->prepare($sql_notas);
        $stmt_notas->bind_param("ii", $profesor['id'], $materia_actual['id']);
        $stmt_notas->execute();
        $result_notas = $stmt_notas->get_result();

        if ($result_notas->num_rows > 0): ?>
            <table class="notas-table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Materia</th>
                        <th>Nota</th>
                        <th>Comentario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($nota = $result_notas->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($nota['alumno_nombre']) . ' ' . htmlspecialchars($nota['alumno_apellido']); ?></td>
                            <td><?php echo htmlspecialchars($nota['materia_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($nota['nota']); ?></td>
                            <td><?php echo htmlspecialchars($nota['comentario']); ?></td>
                            <td><?php echo htmlspecialchars($nota['fecha']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay notas registradas.</p>
        <?php endif; ?>
    </div>
</body>
</html>
