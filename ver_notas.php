<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'alumno') {
    header('Location: index.php');
    exit();
}

include 'config.php'; 


$username = $_SESSION['username'];


$sql_alumno = "SELECT id, nombre, apellido, materia_id FROM alumnos WHERE username = ?";
$stmt_alumno = $conn->prepare($sql_alumno);
$stmt_alumno->bind_param("s", $username);
$stmt_alumno->execute();
$resultado_alumno = $stmt_alumno->get_result();
$alumno = $resultado_alumno->fetch_assoc();

if (!$alumno) {
    die("Alumno no encontrado.");
}


$alumno_id = $alumno['id'];
$materia_id = $alumno['materia_id'];


if (!$materia_id) {
    die("Este alumno no tiene una materia asignada.");
}


$sql_materia = "SELECT nombre FROM materias WHERE id = ?";
$stmt_materia = $conn->prepare($sql_materia);
$stmt_materia->bind_param("i", $materia_id);
$stmt_materia->execute();
$resultado_materia = $stmt_materia->get_result();
$materia = $resultado_materia->fetch_assoc();

if (!$materia) {
    die("Materia no encontrada.");
}


$sql_notas = "SELECT n.id, n.nota, n.comentario, n.fecha, CONCAT(p.nombre, ' ', p.apellido) AS profesor_nombre
              FROM notas n
              JOIN profesores p ON n.profesor_id = p.id
              WHERE n.alumno_id = ? AND n.materia_id = ?";
$stmt_notas = $conn->prepare($sql_notas);
$stmt_notas->bind_param("ii", $alumno_id, $materia_id);
$stmt_notas->execute();
$resultado_notas = $stmt_notas->get_result();


if ($resultado_notas->num_rows === 0) {
    echo "No se encontraron notas para esta materia.";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Notas</title>
    <link rel="stylesheet" href="css/alumno_bienvenido.css">
</head>
<body>
    <header>
        <nav>
        <a href="alumno_bienvenido.php">Inicio</a>
        <a href="alumno_datos.php">Mis Datos</a>
        <a href="ver_notas.php">Mis Notas</a>
        <a href="logout.php" class="btn">Cerrar Sesión</a>
        </nav>
    </header>
    <h1>Mis Notas</h1>
    <main>
        <section>
            <h2>Notas de la Materia: <?php echo htmlspecialchars($materia['nombre']); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nota</th>
                        <th>Comentario</th>
                        <th>Profesor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado_notas->num_rows > 0): ?>
                        <?php while ($nota = $resultado_notas->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($nota['fecha']); ?></td>
                                <td><?php echo htmlspecialchars($nota['nota']); ?></td>
                                <td><?php echo htmlspecialchars($nota['comentario']); ?></td>
                                <td><?php echo htmlspecialchars($nota['profesor_nombre']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No se encontraron notas para esta materia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>
        <p>© 2024 Escuela Tecnica</p>
    </footer>
</body>
</html>
