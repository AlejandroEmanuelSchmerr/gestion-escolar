<?php
include 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_student'])) {
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
    $user_type = 'alumno'; 

    if ($password !== $confirm_password) {
        echo "<div class='alert alert-error'>Las contraseñas no coinciden.</div>";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        echo "<div class='alert alert-error'>La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.</div>";
    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);


        $sql = "INSERT INTO alumnos (nombre, apellido, dni, especialidad_id, año, materia_id, fecha_nacimiento, username, password, email, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        

        $stmt->bind_param("sssiiisssss", $nombre, $apellido, $dni, $especialidad_id, $año, $materia_id, $fecha_nacimiento, $username, $hashed_password, $email, $user_type);
        
        if ($stmt->execute()) {
            
            header("Location: inicio_sesion.php");
            exit(); 
        } else {
            echo "<div class='alert alert-error'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}

$alumnos = $conn->query("SELECT * FROM alumnos");


$especialidades = $conn->query("SELECT id, nombre FROM especialidades");
$materias = $conn->query("SELECT id, nombre FROM materias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Alumnos</title>
    <link rel="stylesheet" href="css/registro_alumno.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const especialidadSelect = document.getElementById('especialidad');
            const materiaSelect = document.getElementById('materia');

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

        function validateDNI(input) {
            const dniError = document.getElementById('dniError');
            const value = input.value;

            if (!/^\d{8}$/.test(value)) {
                dniError.style.display = 'block';
                input.setCustomValidity('');
            } else {
                dniError.style.display = 'none';
                input.setCustomValidity('');
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Registro de Alumnos</h1>

        <form action="" method="post">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required>
            </div>
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" maxlength="8" required oninput="validateDNI(this)">
                <small class="error" id="dniError" style="color:red; display:none;">El DNI debe contener exactamente 8 números.</small>
            </div>
            <div class="form-group">
                <label for="especialidad">Especialidad:</label>
                <select id="especialidad" name="especialidad" required>
                    <option value="">Selecciona una especialidad</option>
                    <?php while ($row = $especialidades->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="año">Año:</label>
                <input type="number" id="año" name="año" required oninput="validateYear(this)">
                <small id="yearError" style="color:red; display:none;">Solo puedes ingresar un número entre 1 y 7.</small>
            </div>

            <script>
            function validateYear(input) {
                const yearError = document.getElementById('yearError');
                const value = input.value;
                if (value < 1 || value > 7 || value.length > 1) {
                    yearError.style.display = 'block';
                    input.value = ''; 
                } else {
                    yearError.style.display = 'none';
                }
            }
            </script>

            <div class="form-group">
                <label for="materia">Materia:</label>
                <select id="materia" name="materia" required>
                    <option value="">Selecciona una materia</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
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
            <button type="submit" name="register_student">Registrar Alumno</button>
            <a href="inicio_sesion.php" class="btn">Volver</a>
        </form>
    </div>
</body>
</html>
