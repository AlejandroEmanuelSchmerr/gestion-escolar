<?php
include 'config.php';


if (isset($_GET['especialidad_id']) && is_numeric($_GET['especialidad_id'])) {
    $especialidad_id = intval($_GET['especialidad_id']);

    $stmt = $conn->prepare("SELECT * FROM materias WHERE especialidad_id = ?");
    $stmt->bind_param("i", $especialidad_id);
    $stmt->execute();
    $result = $stmt->get_result();

    
    $materias = [];
    while ($row = $result->fetch_assoc()) {
        $materias[] = $row;
    }

    
    header('Content-Type: application/json');
    echo json_encode($materias);
} else {

    echo json_encode([]);
}

