<?php
require 'config.php'; 
require 'phpqrcode/qrlib.php'; 


if (isset($_GET['alumno_id'])) {
    $alumno_id = intval($_GET['alumno_id']);
    

    $sql = "SELECT id FROM alumnos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $alumno_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->close();

        $fecha_actual = date('Y-m-d H:i:s');
        $codigo_qr = "http://localhost/registro/procesar_asistencia.php?alumno_id=" . $alumno_id . "&fecha=" . urlencode($fecha_actual);


        $qr_filename = 'qr_asistencia_' . $alumno_id . '.png';
        $ruta_qr = __DIR__ . '/' . $qr_filename; 
        QRcode::png($codigo_qr, $ruta_qr, QR_ECLEVEL_L, 5);

    
        $sql = "INSERT INTO asistencia_alumnos (alumno_id, fecha, codigo_qr) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $alumno_id, $fecha_actual, $qr_filename);
        
        if ($stmt->execute()) {
            echo '<img src="' . $qr_filename . '" alt="QR Code">';
            echo "<br><a href='registrar_asistencia.php?alumno_id=" . $alumno_id . "' class='btn'>Registrar asistencia</a>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        
        echo "El ID de alumno proporcionado no existe.";
    }
} else {
    echo "No se ha proporcionado un ID de alumno.";
}

$conn->close(); 

