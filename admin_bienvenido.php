<?php

include 'config.php';
session_start();


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("Acceso denegado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="css/admin_bienvenido.css">
</head>
<body>
    <div class="container">
        <h1>Panel de Administración</h1>
        <ul>
            <li><a href="manage_alumnos.php">Gestionar Alumnos</a></li>
            <li><a href="manage_profesores.php">Gestionar Profesores</a></li>
            <li><a href="manage_users.php">Gestionar Usuarios</a></li>
            <li><a href="manage_materiales.php">Gestionar Materiales</a></li>
            <li><a href="manage_materias.php">Gestionar Materias</a></li>
            <li><a href="manage_especialidades.php">Gestionar Especialidades</a></li>
            <li><a href="manage_notas.php">Gestionar Notas</a></li>
            <li><a href="manage_asistencia_profesores.php">Gestionar Asistencia Profes</a></li>
            <li><a href="manage_asistencia_alumnos.php">Gestionar Asistenica Alumnos</a></li>
        </ul>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</body>
</html>
