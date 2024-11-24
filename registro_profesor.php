<?php
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_professor_user'])) {

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $año = $_POST['año'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];  
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $especialidad_id = $_POST['especialidad_id'];
    $materia_id = $_POST['materia_id'];
    $user_type = 'profesor'; 


    if (empty($nombre) || empty($apellido) || empty($dni) || empty($año) || empty($fecha_nacimiento) || empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($especialidad_id) || empty($materia_id)) {
        die("Todos los campos son obligatorios.");
    }

    if (!preg_match('/^\d{8}$/', $dni)) {
        die("El DNI debe tener exactamente 8 dígitos.");
    }

    if ($año < 1 || $año > 7) {
        die("El año debe estar entre 1 y 7.");
    }

    if ($password !== $confirm_password) {
        die("Las contraseñas no coinciden.");
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        die("La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.");
    }


    $sql_check_dni = "SELECT id FROM profesores WHERE dni = ?";
    $stmt_check_dni = $conn->prepare($sql_check_dni);
    $stmt_check_dni->bind_param("s", $dni);
    $stmt_check_dni->execute();
    $stmt_check_dni->store_result();

    if ($stmt_check_dni->num_rows > 0) {
        die("El DNI ingresado ya está registrado.");
    }
    $stmt_check_dni->close();


    $sql_check_materia = "SELECT id FROM profesores WHERE materia_id = ?";
    $stmt_check_materia = $conn->prepare($sql_check_materia);
    $stmt_check_materia->bind_param("i", $materia_id);
    $stmt_check_materia->execute();
    $stmt_check_materia->store_result();

    if ($stmt_check_materia->num_rows > 0) {
        echo "<div class='alert alert-error'>La materia ya tiene un profesor asignado. No hay vacante.</div>";
    } else {
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);


        $sql = "INSERT INTO profesores (nombre, apellido, dni, año, fecha_nacimiento, username, password, email, especialidad_id, materia_id, user_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        
        $stmt->bind_param(
            "ssisssssiis",
            $nombre,
            $apellido,
            $dni,
            $año,
            $fecha_nacimiento,
            $username,
            $hashed_password,
            $email,
            $especialidad_id,
            $materia_id,
            $user_type
        );

        if ($stmt->execute()) {
            
            header("Location: inicio_sesion.php");
            exit();
        } else {
            echo "<div class='alert alert-error'>Error al registrar profesor: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }

    $stmt_check_materia->close();
}


$especialidades = $conn->query("SELECT id, nombre FROM especialidades");
$materias = $conn->query("SELECT id, nombre FROM materias");
?>





<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Profesores</title>
    <link rel="stylesheet" href="css/registro_profe.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const especialidadSelect = document.getElementById('especialidad_id');
            const materiaSelect = document.getElementById('materia_id');
            const añoInput = document.getElementById('año');

            añoInput.addEventListener('input', function() {
                let value = parseInt(this.value, 10);
                if (value < 1 || value > 7) {
                    alert("Por favor ingresa un valor entre 1 y 7 para el año.");
                    this.value = '';  
                }
            });

            especialidadSelect.addEventListener('change', function() {
                const especialidadId = this.value;
                
                if (especialidadId) {
                    fetch(`get_materias.php?especialidad_id=${especialidadId}`)
                        .then(response => response.json())
                        .then(data => {
                            materiaSelect.innerHTML = '<option value="">Selecciona una materia</option>';
                            data.forEach(materia => {
                                const option = document.createElement('option');
                                option.value = materia.id;
                                option.textContent = materia.nombre;
                                materiaSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    materiaSelect.innerHTML = '<option value="">Selecciona una materia</option>';
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Registro de Profesores</h1>

        <div class="form-and-table">
            <div class="form-container">
                <h2>Registrar Profesor</h2>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre del Profesor:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido del Profesor:</label>
                        <input type="text" id="apellido" name="apellido" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>
                    <div class="form-group">
                        <label for="dni">DNI del Profesor:</label>
                        <input type="text" id="dni" name="dni" maxlength="8" pattern="\d{8}" title="Debe tener exactamente 8 dígitos" required>
                    </div>
                    <div class="form-group">
                        <label for="año">Año (1-7):</label>
                        <input type="number" id="año" name="año" min="1" max="7" required>
                    </div>
                    <div class="form-group">
                        <label for="especialidad_id">Especialidad:</label>
                        <select id="especialidad_id" name="especialidad_id" required>
                            <option value="">Selecciona una especialidad</option>
                            <?php while ($row = $especialidades->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="materia_id">Materia:</label>
                        <select id="materia_id" name="materia_id" required>
                            <option value="">Selecciona una materia</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="username">Usuario:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirma Contraseña:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" name="register_professor_user">Registrar Profesor</button>
                    <a href="inicio_sesion.php" class="btn">Volver</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
