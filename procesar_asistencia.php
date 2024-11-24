<?php


require 'config.php';

if (isset($_GET['alumno_id'])) {
    $alumno_id = intval($_GET['alumno_id']);
    

    $fecha_actual = date('Y-m-d H:i:s');


    $query = $pdo->prepare("SELECT * FROM asistencia_alumnos WHERE alumno_id = :alumno_id AND DATE(fecha) = CURDATE()");
    $query->execute(['alumno_id' => $alumno_id]);
    $asistencia = $query->fetch();

    if ($asistencia) {

        echo "Ya has registrado tu asistencia hoy.";
    } else {

        $insert = $pdo->prepare("INSERT INTO asistencia_alumnos (alumno_id, fecha, estado) VALUES (:alumno_id, :fecha, 'presente')");
        $insert->execute(['alumno_id' => $alumno_id, 'fecha' => $fecha_actual]);
        echo "Asistencia registrada como presente.";
    }
} else {
    echo "No se ha proporcionado un ID de alumno.";
}

