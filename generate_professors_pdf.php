<?php
session_start(); 
require('fpdf.php');
include 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'profesor') {
    header('Location: index.php');
    exit();
}


$profesor_id = $_SESSION['profesor_id'];


$sql_profesor = "SELECT * FROM profesores WHERE id = ?";
$stmt = $conn->prepare($sql_profesor);
$stmt->bind_param("i", $profesor_id);
$stmt->execute();
$result_profesor = $stmt->get_result();
$profesor = $result_profesor->fetch_assoc();


$sql_asistencias = "SELECT a.fecha, a.estado, p.nombre, m.nombre AS materia
                    FROM asistencia a
                    JOIN profesores p ON a.profesor_id = p.id
                    JOIN materias m ON p.materia_id = m.id
                    WHERE a.profesor_id = ?";
$stmt_asistencias = $conn->prepare($sql_asistencias);
$stmt_asistencias->bind_param("i", $profesor_id);
$stmt_asistencias->execute();
$result_asistencias = $stmt_asistencias->get_result();


$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);


$pdf->Cell(190, 10, 'Listado de Asistencias del Profesor', 0, 1, 'C');
$pdf->Ln(10);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Nombre', 1);
$pdf->Cell(50, 10, 'Materia', 1);
$pdf->Cell(50, 10, 'Fecha', 1);
$pdf->Cell(50, 10, 'Estado', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
while ($asistencia = $result_asistencias->fetch_assoc()) {
    $pdf->Cell(40, 15, $asistencia['nombre'], 1);
    $pdf->Cell(50, 15, $asistencia['materia'], 1);
    $pdf->Cell(50, 15, $asistencia['fecha'], 1);
    $pdf->Cell(50, 15, $asistencia['estado'], 1);
    $pdf->Ln();
}

// Generar el archivo PDF
$pdf->Output();

// Después de generar el PDF, mostrar el botón "Volver"
echo "<br><a href='profesor_bienvenido.php'><button style='padding:10px 15px; background-color:#007BFF; color:white; border:none; border-radius:5px;'>Volver</button></a>";
?>
