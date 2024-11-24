<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'alumno') {
    header('Location: index.php');
    exit();
}

include 'config.php';  
require('fpdf.php');  


$username = $_SESSION['username'];


$sql = "SELECT * FROM alumnos WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $alumno = $result->fetch_assoc();  
} else {
    echo "No se encontraron datos del alumno.";
    exit();
}


$especialidad_sql = "SELECT nombre FROM especialidades WHERE id = ?";
$stmt = $conn->prepare($especialidad_sql);
$stmt->bind_param("i", $alumno['especialidad_id']);
$stmt->execute();
$especialidad_result = $stmt->get_result();
$especialidad = $especialidad_result->fetch_assoc();


$materia_sql = "SELECT nombre FROM materias WHERE id = ?";
$stmt = $conn->prepare($materia_sql);
$stmt->bind_param("i", $alumno['materia_id']);
$stmt->execute();
$materia_result = $stmt->get_result();
$materia = $materia_result->fetch_assoc();


$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, 'Datos del Alumno', 0, 1, 'C');


$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, 'Nombre:', 1);
$pdf->Cell(60, 10, $alumno['nombre'] . ' ' . $alumno['apellido'], 1);
$pdf->Ln();

$pdf->Cell(60, 10, 'DNI:', 1);
$pdf->Cell(60, 10, $alumno['dni'], 1);
$pdf->Ln();

$pdf->Cell(60, 10, 'Especialidad:', 1);
$pdf->Cell(60, 10, $especialidad['nombre'], 1);
$pdf->Ln();

$pdf->Cell(60, 10, 'Curso:', 1);
$pdf->Cell(60, 10, $alumno['año'], 1);
$pdf->Ln();

$pdf->Cell(60, 10, 'Materia:', 1);
$pdf->Cell(60, 10, $materia['nombre'], 1);
$pdf->Ln();

$pdf->Cell(60, 10, 'Fecha de Nacimiento:', 1);
$pdf->Cell(60, 10, $alumno['fecha_nacimiento'], 1);
$pdf->Ln();

// Descargar el PDF
$pdf->Output('D', 'Datos_Alumno.pdf');  
