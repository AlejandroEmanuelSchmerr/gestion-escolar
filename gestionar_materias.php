<?php

include 'config.php';
session_start(); 

$profesor_id = intval($_SESSION['profesor_id']); 


$sql_materia = "SELECT id, nombre FROM materias WHERE id = (SELECT materia_id FROM profesores WHERE id = ?) LIMIT 1";
$stmt_materia = $conn->prepare($sql_materia);
$stmt_materia->bind_param("i", $profesor_id);
$stmt_materia->execute();
$stmt_materia->bind_result($materia_id, $materia_nombre);
$stmt_materia->fetch();
$stmt_materia->close();


if (!$materia_id) {
    die("Error: No se encontró ninguna materia asociada al profesor.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subir_material'])) {
    $titulo = htmlspecialchars($_POST['titulo']);
    $descripcion = htmlspecialchars($_POST['descripcion']);

    if (!empty($_FILES['archivo']['name'])) {
        $archivo = $_FILES['archivo']['name'];
        $ruta_archivo = 'uploads/' . basename($archivo);
        $archivo_tipo = mime_content_type($_FILES['archivo']['tmp_name']);


        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        $tipos_permitidos = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (in_array($archivo_tipo, $tipos_permitidos)) {
            if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivo)) {

                $sql_material = "INSERT INTO materiales (profesor_id, materia_id, titulo, descripcion, archivo, fecha) 
                                VALUES (?, ?, ?, ?, ?, CURDATE())";
                $stmt_material = $conn->prepare($sql_material);
                $stmt_material->bind_param("iisss", $profesor_id, $materia_id, $titulo, $descripcion, $archivo);

                if ($stmt_material->execute()) {
                    echo "Material subido con éxito.";
                } else {
                    echo "Error al subir el material: " . $stmt_material->error;
                }
            } else {
                echo "Error al mover el archivo.";
            }
        } else {
            echo "Tipo de archivo no permitido. Solo se permiten archivos PDF o DOCX.";
        }
    } else {
        echo "Por favor, sube un archivo.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Material</title>
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
    <h1>Subir Material</h1>

    <div class="table-container">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" required>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" rows="4" required></textarea>

            <label for="archivo">Subir archivo:</label>
            <input type="file" name="archivo" required>

            <button type="submit" name="subir_material">Subir Material</button>
        </form>
    </div>
</body>
</html>
