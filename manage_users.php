<?php

include 'config.php';
session_start();


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("Acceso denegado.");
}


if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ? intval($_POST['id']) : null;
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; 
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];


    $errors = [];

    if ($password && !preg_match('/^[A-Z]/', $password)) {
        $errors[] = "La contraseña debe comenzar con una letra mayúscula.";
    }
    if ($password && !preg_match('/[a-z]/', $password)) {
        $errors[] = "La contraseña debe contener al menos una letra minúscula.";
    }
    if ($password && !preg_match('/[0-9]/', $password)) {
        $errors[] = "La contraseña debe contener al menos un número.";
    }
    if ($password && $password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    if (empty($errors)) {
        if ($password) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            if ($id) {
                $sql = "UPDATE users SET username = ?, password = ?, email = ?, user_type = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $username, $password, $email, $user_type, $id);
            } else {
                $sql = "INSERT INTO users (username, password, email, user_type) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $username, $password, $email, $user_type);
            }
        } else {
            if ($id) {
                $sql = "UPDATE users SET username = ?, email = ?, user_type = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $username, $email, $user_type, $id);
            } else {
                $sql = "INSERT INTO users (username, email, user_type) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $username, $email, $user_type);
            }
        }
        $stmt->execute();
        $stmt->close();
        header("Location: manage_users.php"); 
        exit();
    }
}


$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios</title>
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
        <h1>Gestionar Usuarios</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <form action="manage_users.php" method="post">
            <input type="hidden" name="id" id="user_id" value="<?php echo isset($_GET['edit_id']) ? intval($_GET['edit_id']) : ''; ?>">
            <label>Nombre de Usuario:</label>
            <input type="text" name="username" id="username" required>
            <label>Contraseña:</label>
            <input type="password" name="password" id="password">
            <label>Confirmar Contraseña:</label>
            <input type="password" name="confirm_password" id="confirm_password">
            <label>Email:</label>
            <input type="email" name="email" id="email" required>
            <label>Tipo de Usuario:</label>
            <select name="user_type" id="user_type">
                <option value="admin">Administrador</option>
            </select>
            <button type="submit">Guardar</button>
        </form>

        <h2>Lista de Usuarios</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Email</th>
                    <th>Tipo de Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td>

                        <button onclick="editUser(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                        <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>

        function editUser(user) {
            document.getElementById('user_id').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('password').value = '';
            document.getElementById('confirm_password').value = ''; 
            document.getElementById('email').value = user.email;
            document.getElementById('user_type').value = user.user_type;
        }
    </script>
</body>
</html>
