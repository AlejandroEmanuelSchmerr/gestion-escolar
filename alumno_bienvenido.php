<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'alumno') {
    header('Location: index.php');
    exit();
}

include 'config.php'; 



$username = $_SESSION['username'];


$sql = "SELECT id, nombre, apellido, dni, especialidad_id, año, materia_id, fecha_nacimiento FROM alumnos WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();
$alumno = $resultado->fetch_assoc();
$alumno_id = $alumno['id']; 



if (!$alumno) {
    die("Alumno no encontrado.");
}

$especialidad_id = $alumno['especialidad_id'];
$materia_id = $alumno['materia_id'];


$sql_especialidad = "SELECT nombre FROM especialidades WHERE id = ?";
$stmt_especialidad = $conn->prepare($sql_especialidad);
$stmt_especialidad->bind_param("i", $especialidad_id);
$stmt_especialidad->execute();
$especialidad_result = $stmt_especialidad->get_result();
$especialidad_actual = $especialidad_result->fetch_assoc();

if (!$especialidad_actual) {
    die("Especialidad no encontrada.");
}


$descripciones_especialidades = [
    1 => [
        'nombre' => 'Programación',
        'descripcion' => 'En la especialidad de Programación aprenderás a desarrollar aplicaciones, sitios web y sistemas informáticos utilizando lenguajes de programación como Python, Java, PHP, entre otros.',
        'imagen' => 'img/programacion.png'
    ],
    2 => [
        'nombre' => 'Electricidad',
        'descripcion' => 'En la especialidad de Electricidad estudiarás los principios y técnicas de la electricidad, así como el mantenimiento e instalación de sistemas eléctricos.',
        'imagen' => 'img/electricidad.jfif'
    ],
    3 => [
        'nombre' => 'Automotor',
        'descripcion' => 'En la especialidad de Automotor, aprenderás el mantenimiento y reparación de vehículos, así como los sistemas mecánicos y eléctricos involucrados.',
        'imagen' => 'img/automotor.jpg'
    ],
    4 => [
        'nombre' => 'Electrónica',
        'descripcion' => 'En Electrónica aprenderás a diseñar, analizar y reparar circuitos electrónicos, así como a trabajar con dispositivos electrónicos modernos.',
        'imagen' => 'img/electronica.jfif'
    ]
];


$info_especialidad = $descripciones_especialidades[$especialidad_id];


$sql_materias = "SELECT id, nombre FROM materias WHERE especialidad_id = ?";
$stmt_materias = $conn->prepare($sql_materias);
$stmt_materias->bind_param("i", $especialidad_id);
$stmt_materias->execute();
$materias_result = $stmt_materias->get_result();


$sql_materia = "SELECT nombre FROM materias WHERE id = ?";
$stmt_materia = $conn->prepare($sql_materia);
$stmt_materia->bind_param("i", $materia_id);
$stmt_materia->execute();
$materia_result = $stmt_materia->get_result();
$materia_actual = $materia_result->fetch_assoc();

if (!$materia_actual) {
    die("Materia no encontrada.");
}

$sql_materiales = "SELECT * FROM materiales WHERE materia_id = ?";
$stmt_materiales = $conn->prepare($sql_materiales);
$stmt_materiales->bind_param("i", $materia_id);
$stmt_materiales->execute();
$materiales_result = $stmt_materiales->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_datos'])) {
    $nuevo_nombre = $_POST['nombre'] ?? $alumno['nombre'];
    $nuevo_apellido = $_POST['apellido'] ?? $alumno['apellido'];
    $nuevo_dni = $_POST['dni'] ?? $alumno['dni'];
    $nuevo_año = $_POST['año'] ?? $alumno['año'];
    $nueva_fecha_nacimiento = $_POST['fecha_nacimiento'] ?? $alumno['fecha_nacimiento'];
    $nueva_materia_id = $_POST['materia_id'] ?? $materia_id;
    $nueva_especialidad_id = $_POST['especialidad_id'] ?? $especialidad_id;

    $sql_update = "UPDATE alumnos SET nombre = ?, apellido = ?, dni = ?, año = ?, fecha_nacimiento = ?, especialidad_id = ?, materia_id = ? WHERE username = ?";
    $stmt_update = $conn->prepare($sql_update);

    if ($stmt_update) {
        $stmt_update->bind_param("sssssiis", $nuevo_nombre, $nuevo_apellido, $nuevo_dni, $nuevo_año, $nueva_fecha_nacimiento, $nueva_especialidad_id, $nueva_materia_id, $username);

        if ($stmt_update->execute()) {
        
            $_SESSION['alumno'] = [
                'nombre' => $nuevo_nombre,
                'apellido' => $nuevo_apellido,
                'dni' => $nuevo_dni,
                'año' => $nuevo_año,
                'fecha_nacimiento' => $nueva_fecha_nacimiento,
                'especialidad_id' => $nueva_especialidad_id,
                'materia_id' => $nueva_materia_id
            ];

            $alumno['nombre'] = $nuevo_nombre;
            $alumno['apellido'] = $nuevo_apellido;
            $alumno['dni'] = $nuevo_dni;
            $alumno['año'] = $nuevo_año;
            $alumno['fecha_nacimiento'] = $nueva_fecha_nacimiento;
            $alumno['especialidad_id'] = $nueva_especialidad_id;
            $alumno['materia_id'] = $nueva_materia_id;


            $stmt_especialidad->bind_param("i", $nueva_especialidad_id);
            $stmt_especialidad->execute();
            $especialidad_result = $stmt_especialidad->get_result();
            $especialidad_actual = $especialidad_result->fetch_assoc();

            $stmt_materia->bind_param("i", $nueva_materia_id);
            $stmt_materia->execute();
            $materia_result = $stmt_materia->get_result();
            $materia_actual = $materia_result->fetch_assoc();

            
            echo "<script>
                    alert('Datos actualizados correctamente.');
                    window.location.href = window.location.href;
                  </script>";
        } else {
            echo "<script>alert('Error al actualizar los datos.');</script>";
        }
    } else {
        echo "<script>alert('Error en la preparación de la consulta.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>
    <link rel="stylesheet" href="css/alumno_bienvenido.css">
</head>
<body>
<header>
<nav>
    <a href="alumno_bienvenido.php">Inicio</a>
    <a href="alumno_datos.php">Mis Datos</a>
    <a href="ver_notas.php">Mis Notas</a>
    <a href="registrar_asistencia.php?alumno_id=<?php echo $alumno_id; ?>&fecha=<?php echo date('Y-m-d'); ?>" class="btn">Registrar asistencia</a>

    <a href="logout.php" class="btn">Cerrar Sesión</a>
</nav>
  
</header>
<h1>Bienvenido, <?php echo htmlspecialchars($alumno['nombre']); ?></h1>

<main>
    <section>
        <h2>Información de la Especialidad: <?php echo htmlspecialchars($info_especialidad['nombre']); ?></h2>
        <p><?php echo htmlspecialchars($info_especialidad['descripcion']); ?></p>
        <img src="<?php echo htmlspecialchars($info_especialidad['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($info_especialidad['nombre']); ?>">
    </section>
    <section>
    <h2>Materiales de la Materia: <?php echo htmlspecialchars($materia_actual['nombre']); ?></h2>
    <?php if ($materiales_result->num_rows > 0): ?>
        <ul>
            <?php while ($material = $materiales_result->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($material['titulo']); ?></strong><br>
                    <?php echo htmlspecialchars($material['descripcion']); ?><br>
                    <a href="uploads/<?php echo htmlspecialchars($material['archivo']); ?>" target="_blank" class="btn-download">Descargar PDF</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay materiales disponibles para esta materia.</p>
    <?php endif; ?>
</section>
</main>
<footer>
    <p>© 2024 Escuela Tecnica</p>
</footer>
</body>
</html>
