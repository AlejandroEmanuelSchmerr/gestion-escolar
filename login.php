<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];


    $sql_alumno = "SELECT * FROM alumnos WHERE username = ?";
    $stmt = $conn->prepare($sql_alumno);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result_alumno = $stmt->get_result();
    $alumno = $result_alumno->fetch_assoc();

    if ($alumno && password_verify($password, $alumno['password'])) {

        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $alumno['username'];
        $_SESSION['user_type'] = 'alumno';

        header('Location: alumno_bienvenido.php');
        exit();
    }

    $sql_profesor = "SELECT * FROM profesores WHERE username = ?";
    $stmt->prepare($sql_profesor);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result_profesor = $stmt->get_result();
    $profesor = $result_profesor->fetch_assoc();

    if ($profesor && password_verify($password, $profesor['password'])) {
        // Si es un profesor
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $profesor['username'];
        $_SESSION['user_type'] = 'profesor';
        $_SESSION['profesor_id'] = $profesor['id'];  

        header('Location: profesor_bienvenido.php');
        exit();
    }


    $sql_admin = "SELECT * FROM users WHERE username = ?";
    $stmt->prepare($sql_admin);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result_admin = $stmt->get_result();
    $admin = $result_admin->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {

        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $admin['username'];
        $_SESSION['user_type'] = 'admin';

        header('Location: admin_bienvenido.php');
        exit();
    }

    // Si no es ni alumno, profesor ni administrador
    $_SESSION['error_message'] = "Usuario o contraseña incorrectos.";
    header('Location: inicio_sesion.php');
    exit();
}

$stmt->close();
?>
