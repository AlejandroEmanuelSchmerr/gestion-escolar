<?php


require 'config.php';


$fecha_actual = date('Y-m-d H:i:s');


$query = $pdo->prepare("
    INSERT INTO asistencia_alumnos (alumno_id, fecha, estado)
    SELECT a.id, NOW(), 'ausente'
    FROM alumnos a
    LEFT JOIN asistencia_alumnos asis ON a.id = asis.alumno_id AND DATE(asis.fecha) = CURDATE()
    WHERE asis.id IS NULL
");
$query->execute();

echo "Los ausentes han sido registrados.";
?>
