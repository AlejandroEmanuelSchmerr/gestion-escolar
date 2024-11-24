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
            
            $sql = "UPDATE alumnos SET nombre = ?, apellido = ?, dni = ?, especialidad_id = ?, año = ?, materia_id = ?, fecha_nacimiento = ?, username = ?, password = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiiissssi", $nombre, $apellido, $dni, $especialidad_id, $año, $materia_id, $fecha_nacimiento, $username, $hashed_password, $email, $id);
        } else {
            
            $sql = "INSERT INTO alumnos (nombre, apellido, dni, especialidad_id, año, materia_id, fecha_nacimiento, username, password, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiiissss", $nombre, $apellido, $dni, $especialidad_id, $año, $materia_id, $fecha_nacimiento, $username, $hashed_password, $email);
        }

        if ($stmt->execute()) {
            header("Location: manage_alumnos.php");
            exit();
        } else {
            echo "<div class='alert alert-error'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}


if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM alumnos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_alumnos.php");
    exit();
}

$alumnos = $conn->query("SELECT * FROM alumnos");
$especialidades = $conn->query("SELECT id, nombre FROM especialidades");
$materias = $conn->query("SELECT id, nombre FROM materias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Alumnos</title>
    <link rel="stylesheet" href="css/gestion_u.css">
</head>
<body>
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
        <h1>Gestión de Alumnos</h1>

        <form action="" method="post">
            <input type="hidden" name="id" id="alumno_id">
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
<input type="number" name="año" id="año" min="1" max="7" required>
<script>
    document.getElementById('año').addEventListener('input', function () {
        const value = parseInt(this.value, 10);
        if (value < 1) this.value = 1;
        if (value > 7) this.value = 7;
    });
</script>

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
                <?php while ($alumno = $alumnos->fetch_assoc()): ?>
                    <tr>
                        <td><?= $alumno['id']; ?></td>
                        <td><?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']); ?></td>
                        <td><?= htmlspecialchars($alumno['dni']); ?></td>
                        <td><?= htmlspecialchars($alumno['username']); ?></td>
                        <td><?= htmlspecialchars($alumno['email']); ?></td>
                        <td>
                            <button onclick="editAlumno(<?= htmlspecialchars(json_encode($alumno)); ?>)">Editar</button>
                            <a href="?delete_id=<?= $alumno['id']; ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editAlumno(alumno) {
            document.getElementById('alumno_id').value = alumno.id;
            document.getElementById('nombre').value = alumno.nombre;
            document.getElementById('apellido').value = alumno.apellido;
            document.getElementById('dni').value = alumno.dni;
            document.getElementById('especialidad').value = alumno.especialidad_id;
            document.getElementById('año').value = alumno.año;
            document.getElementById('materia').value = alumno.materia_id;
            document.getElementById('fecha_nacimiento').value = alumno.fecha_nacimiento;
            document.getElementById('username').value = alumno.username;
            document.getElementById('email').value = alumno.email;
        }
    </script>
</body>
</html>
