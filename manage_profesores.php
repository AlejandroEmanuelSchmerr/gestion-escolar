<?php
include 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $especialidad_id = $_POST['especialidad'];
    $año = $_POST['año'];
    $materia_id = $_POST['materia'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];

    if (!preg_match('/^\d{8}$/', $dni)) {
        echo "<div class='alert alert-error'>El DNI debe contener exactamente 8 números.</div>";
    } elseif ($password !== $confirm_password) {
        echo "<div class='alert alert-error'>Las contraseñas no coinciden.</div>";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        echo "<div class='alert alert-error'>La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.</div>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($id) {
        
            $sql = "UPDATE profesores SET nombre = ?, apellido = ?, dni = ?, especialidad_id = ?, año = ?, materia_id = ?, fecha_nacimiento = ?, username = ?, password = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiiissssi", $nombre, $apellido, $dni, $especialidad_id, $año, $materia_id, $fecha_nacimiento, $username, $hashed_password, $email, $id);
        } else {
        
            $sql = "INSERT INTO profesores (nombre, apellido, dni, especialidad_id, año, materia_id, fecha_nacimiento, username, password, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiiissss", $nombre, $apellido, $dni, $especialidad_id, $año, $materia_id, $fecha_nacimiento, $username, $hashed_password, $email);
        }

        if ($stmt->execute()) {
            header("Location: manage_profesores.php");
            exit();
        } else {
            echo "<div class='alert alert-error'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}


if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM profesores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_profesores.php");
    exit();
}


$profesores = $conn->query("SELECT * FROM profesores");
$especialidades = $conn->query("SELECT id, nombre FROM especialidades");
$materias = $conn->query("SELECT id, nombre FROM materias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Profesores</title>
    <link rel="stylesheet" href="css/gestion_u.css">
</head>
<body>
<script>
    function validateAnio(input) {
        const value = parseInt(input.value, 10);
        if (isNaN(value) || value < 1) {
            input.value = 1; 
        } else if (value > 7) {
            input.value = 7; 
        }
    }
</script>

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
        <h1>Gestión de Profesores</h1>

        
        <form action="" method="post">
            <input type="hidden" name="id" id="profesor_id">
            <label>Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>
            <label>Apellido:</label>
            <input type="text" name="apellido" id="apellido" required>
            <label>DNI:</label>
            <input type="text" name="dni" id="dni" maxlength="8" required>
            <label>Especialidad:</label>
            <select name="especialidad" id="especialidad" required>
                <option value="">Seleccionar</option>
                <?php while ($row = $especialidades->fetch_assoc()): ?>
                    <option value="<?= $row['id']; ?>"><?= $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
            <label>Año:</label>
            <input type="number" name="año" id="año" min="1" max="7" oninput="validateAnio(this)" required>

            <label>Materia:</label>
            <select name="materia" id="materia" required>
                <option value="">Seleccionar</option>
                <?php while ($row = $materias->fetch_assoc()): ?>
                    <option value="<?= $row['id']; ?>"><?= $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
            <label>Fecha de Nacimiento:</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
            <label>Usuario:</label>
            <input type="text" name="username" id="username" required>
            <label>Correo:</label>
            <input type="email" name="email" id="email" required>
            <label>Contraseña:</label>
            <input type="password" name="password" id="password" required>
            <label>Confirmar Contraseña:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
            <button type="submit">Guardar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($profesor = $profesores->fetch_assoc()): ?>
                    <tr>
                        <td><?= $profesor['id']; ?></td>
                        <td><?= htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']); ?></td>
                        <td><?= htmlspecialchars($profesor['dni']); ?></td>
                        <td><?= htmlspecialchars($profesor['username']); ?></td>
                        <td><?= htmlspecialchars($profesor['email']); ?></td>
                        <td>
                            <button onclick="editProfesor(<?= htmlspecialchars(json_encode($profesor)); ?>)">Editar</button>
                            <a href="?delete_id=<?= $profesor['id']; ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editProfesor(profesor) {
            document.getElementById('profesor_id').value = profesor.id;
            document.getElementById('nombre').value = profesor.nombre;
            document.getElementById('apellido').value = profesor.apellido;
            document.getElementById('dni').value = profesor.dni;
            document.getElementById('especialidad').value = profesor.especialidad_id;
            document.getElementById('año').value = profesor.año;
            document.getElementById('materia').value = profesor.materia_id;
            document.getElementById('fecha_nacimiento').value = profesor.fecha_nacimiento;
            document.getElementById('username').value = profesor.username;
            document.getElementById('email').value = profesor.email;
        }
    </script>
</body>
</html>
