<?php

include 'config.php';
session_start();


if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin'])) {
    die("Acceso denegado.");
}


if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM materiales WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_materiales.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ? intval($_POST['id']) : null;
    $profesor_id = intval($_POST['profesor_id']);
    $materia_id = intval($_POST['materia_id']);
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = date('Y-m-d H:i:s');
    $archivo = null;


    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivo_nombre = $_FILES['archivo']['name'];
        $archivo_temp = $_FILES['archivo']['tmp_name'];
        $archivo_destino = "uploads/" . $archivo_nombre;

        if (move_uploaded_file($archivo_temp, $archivo_destino)) {
            $archivo = $archivo_destino;
        }
    }

    if ($id) {

        $sql = $archivo ? 
            "UPDATE materiales SET profesor_id = ?, materia_id = ?, titulo = ?, descripcion = ?, archivo = ?, fecha = ? WHERE id = ?" :
            "UPDATE materiales SET profesor_id = ?, materia_id = ?, titulo = ?, descripcion = ?, fecha = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($archivo) {
            $stmt->bind_param("iissssi", $profesor_id, $materia_id, $titulo, $descripcion, $archivo, $fecha, $id);
        } else {
            $stmt->bind_param("iisssi", $profesor_id, $materia_id, $titulo, $descripcion, $fecha, $id);
        }
    } else {

        $sql = "INSERT INTO materiales (profesor_id, materia_id, titulo, descripcion, archivo, fecha) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissss", $profesor_id, $materia_id, $titulo, $descripcion, $archivo, $fecha);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: manage_materiales.php");
    exit();
}


$sql = "
    SELECT 
        materiales.*, 
        profesores.nombre AS profesor_nombre, 
        materias.nombre AS materia_nombre 
    FROM 
        materiales
    LEFT JOIN 
        profesores ON materiales.profesor_id = profesores.id
    LEFT JOIN 
        materias ON materiales.materia_id = materias.id
";
$materiales_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Materiales</title>
    <link rel="stylesheet" href="css/gestion_materiales.css">
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
        <h1>Gestionar Materiales</h1>


        <form action="manage_materiales.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" id="material_id">

            <label>Profesor:</label>
            <select name="profesor_id" id="profesor_id" required>
                <option value="">Seleccione un profesor</option>
                <?php
                $profesores_result = $conn->query("SELECT id, nombre FROM profesores");
                while ($profesor = $profesores_result->fetch_assoc()):
                ?>
                    <option value="<?php echo $profesor['id']; ?>">
                        <?php echo htmlspecialchars($profesor['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>


            <label>Materia:</label>
            <select name="materia_id" id="materia_id" required>
                <option value="">Seleccione una materia</option>
                <?php
                $materias_result = $conn->query("SELECT id, nombre FROM materias");
                while ($materia = $materias_result->fetch_assoc()):
                ?>
                    <option value="<?php echo $materia['id']; ?>">
                        <?php echo htmlspecialchars($materia['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Título:</label>
            <input type="text" name="titulo" id="titulo" required>
            <label>Descripción:</label>
            <textarea type="descripcion" name="descripcion" id="descripcion" required></textarea>
            <label>Archivo:</label>
            <input type="file" name="archivo" id="archivo">
            <button type="submit">Guardar</button>
        </form>

        <h2>Lista de Materiales</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profesor</th>
                    <th>Materia</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Archivo</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $materiales_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['profesor_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['materia_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                    <td>
                        <?php if ($row['archivo']): ?>
                            <a href="download.php?file=<?php echo urlencode(basename($row['archivo'])); ?>">Descargar Archivo</a>
                        <?php else: ?>
                            Sin archivo
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                    <td>
                        <button onclick="editMaterial(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                        <a href="manage_materiales.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este material?');">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>

        function editMaterial(material) {
            document.getElementById('material_id').value = material.id;
            document.getElementById('profesor_id').value = material.profesor_id;
            document.getElementById('materia_id').value = material.materia_id;
            document.getElementById('titulo').value = material.titulo;
            document.getElementById('descripcion').value = material.descripcion;
            document.getElementById('archivo').value = ''; // Limpiar el archivo
        }
    </script>
</body>
</html>
