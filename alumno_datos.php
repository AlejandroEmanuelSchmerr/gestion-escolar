<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'alumno') {
    header('Location: index.php');
    exit();
}

include 'config.php'; 


$username = $_SESSION['username'];


$sql = "SELECT nombre, apellido, dni, especialidad_id, año, materia_id, fecha_nacimiento FROM alumnos WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();
$alumno = $resultado->fetch_assoc();

if (!$alumno) {
    die("Alumno no encontrado.");
}

$especialidad_id = $alumno['especialidad_id'];
$materia_id = $alumno['materia_id'];


$sql_especialidad = "SELECT nombre FROM especialidades WHERE id = ?";
$stmt_especialidad = $conn->prepare($sql_especialidad);
$stmt_especialidad->bind_param("i", $especialidad_id);
$stmt_especialidad->execute();
$especialidad_result = $stmt_especialidad->get_result();
$especialidad_actual = $especialidad_result->fetch_assoc();

if (!$especialidad_actual) {
    die("Especialidad no encontrada.");
}


$descripciones_especialidades = [
    1 => [
        'nombre' => 'Programación',
        'descripcion' => 'En la especialidad de Programación aprenderás a desarrollar aplicaciones, sitios web y sistemas informáticos utilizando lenguajes de programación como Python, Java, PHP, entre otros.',
        'imagen' => 'imagenes/programacion.jpg'
    ],
    2 => [
        'nombre' => 'Electricidad',
        'descripcion' => 'En la especialidad de Electricidad estudiarás los principios y técnicas de la electricidad, así como el mantenimiento e instalación de sistemas eléctricos.',
        'imagen' => 'imagenes/electricidad.jpg'
    ],
    3 => [
        'nombre' => 'Automotor',
        'descripcion' => 'En la especialidad de Automotor, aprenderás el mantenimiento y reparación de vehículos, así como los sistemas mecánicos y eléctricos involucrados.',
        'imagen' => 'imagenes/automotor.jpg'
    ],
    4 => [
        'nombre' => 'Electrónica',
        'descripcion' => 'En Electrónica aprenderás a diseñar, analizar y reparar circuitos electrónicos, así como a trabajar con dispositivos electrónicos modernos.',
        'imagen' => 'imagenes/electronica.jpg'
    ]
];


$info_especialidad = $descripciones_especialidades[$especialidad_id];


$sql_materias = "SELECT id, nombre FROM materias WHERE especialidad_id = ?";
$stmt_materias = $conn->prepare($sql_materias);
$stmt_materias->bind_param("i", $especialidad_id);
$stmt_materias->execute();
$materias_result = $stmt_materias->get_result();


$sql_materia = "SELECT nombre FROM materias WHERE id = ?";
$stmt_materia = $conn->prepare($sql_materia);
$stmt_materia->bind_param("i", $materia_id);
$stmt_materia->execute();
$materia_result = $stmt_materia->get_result();
$materia_actual = $materia_result->fetch_assoc();

if (!$materia_actual) {
    die("Materia no encontrada.");
}


$sql_materiales = "SELECT * FROM materiales WHERE materia_id = ?";
$stmt_materiales = $conn->prepare($sql_materiales);
$stmt_materiales->bind_param("i", $materia_id);
$stmt_materiales->execute();
$materiales_result = $stmt_materiales->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_datos'])) {
    $nuevo_nombre = $_POST['nombre'] ?? $alumno['nombre'];
    $nuevo_apellido = $_POST['apellido'] ?? $alumno['apellido'];
    $nuevo_dni = $_POST['dni'] ?? $alumno['dni'];
    $nuevo_año = $_POST['año'] ?? $alumno['año'];
    $nueva_fecha_nacimiento = $_POST['fecha_nacimiento'] ?? $alumno['fecha_nacimiento'];
    $nueva_materia_id = $_POST['materia_id'] ?? $materia_id;
    $nueva_especialidad_id = $_POST['especialidad_id'] ?? $especialidad_id;

    $sql_update = "UPDATE alumnos SET nombre = ?, apellido = ?, dni = ?, año = ?, fecha_nacimiento = ?, especialidad_id = ?, materia_id = ? WHERE username = ?";
    $stmt_update = $conn->prepare($sql_update);

    if ($stmt_update) {
        $stmt_update->bind_param("sssssiis", $nuevo_nombre, $nuevo_apellido, $nuevo_dni, $nuevo_año, $nueva_fecha_nacimiento, $nueva_especialidad_id, $nueva_materia_id, $username);

        if ($stmt_update->execute()) {

            $_SESSION['alumno'] = [
                'nombre' => $nuevo_nombre,
                'apellido' => $nuevo_apellido,
                'dni' => $nuevo_dni,
                'año' => $nuevo_año,
                'fecha_nacimiento' => $nueva_fecha_nacimiento,
                'especialidad_id' => $nueva_especialidad_id,
                'materia_id' => $nueva_materia_id
            ];


            $alumno['nombre'] = $nuevo_nombre;
            $alumno['apellido'] = $nuevo_apellido;
            $alumno['dni'] = $nuevo_dni;
            $alumno['año'] = $nuevo_año;
            $alumno['fecha_nacimiento'] = $nueva_fecha_nacimiento;
            $alumno['especialidad_id'] = $nueva_especialidad_id;
            $alumno['materia_id'] = $nueva_materia_id;


            $stmt_especialidad->bind_param("i", $nueva_especialidad_id);
            $stmt_especialidad->execute();
            $especialidad_result = $stmt_especialidad->get_result();
            $especialidad_actual = $especialidad_result->fetch_assoc();

            $stmt_materia->bind_param("i", $nueva_materia_id);
            $stmt_materia->execute();
            $materia_result = $stmt_materia->get_result();
            $materia_actual = $materia_result->fetch_assoc();


            echo "<script>
                    alert('Datos actualizados correctamente.');
                    window.location.href = window.location.href;
                </script>";
        } else {
            echo "<script>alert('Error al actualizar los datos.');</script>";
        }
    } else {
        echo "<script>alert('Error en la preparación de la consulta.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis datos</title>
    <link rel="stylesheet" href="css/alumno_bienvenido.css">
    <script>
        function habilitarEdicion() {
            document.querySelectorAll('.editable').forEach(function(input) {
                input.style.display = 'block';
                input.removeAttribute('readonly');
            });
            document.querySelectorAll('.no-editable').forEach(function(span) {
                span.style.display = 'none';
            });
            document.getElementById('btnModificar').classList.add('hidden');
            document.getElementById('btnGuardar').classList.remove('hidden');
            document.getElementById('btnCancelar').classList.remove('hidden');
        }

        function deshabilitarEdicion() {
            document.querySelectorAll('.editable').forEach(function(input) {
                input.style.display = 'none';
                input.setAttribute('readonly', 'true');
            });
            document.querySelectorAll('.no-editable').forEach(function(span) {
                span.style.display = 'block';
            });
            document.getElementById('btnModificar').classList.remove('hidden');
            document.getElementById('btnGuardar').classList.add('hidden');
            document.getElementById('btnCancelar').classList.add('hidden');
        }

        function cancelarEdicion() {
            deshabilitarEdicion();
        }

        function actualizarMaterias() {
            const especialidad_id = document.getElementById('especialidad_id').value;
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `get_materias.php?especialidad_id=${especialidad_id}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const materias = JSON.parse(xhr.responseText);
                    const materiaSelect = document.getElementById('materia_id');
                    materiaSelect.innerHTML = '';
                    materias.forEach(function(materia) {
                        const option = document.createElement('option');
                        option.value = materia.id;
                        option.textContent = materia.nombre;
                        materiaSelect.appendChild(option);
                    });
                    materiaSelect.value = '<?php echo $materia_id; ?>';
                }
            };
            xhr.send();
        }

        function validarDNI(input) {
            const dniError = document.getElementById('dniError');
            const value = input.value;

            if (!/^\d{0,8}$/.test(value) || value.length !== 8) {
                dniError.style.display = 'block';
                input.setCustomValidity('');
            } else {
                dniError.style.display = 'none';
                input.setCustomValidity('');
            }
        }

        function validarAño(input) {
            const añoError = document.getElementById('añoError');
            const value = input.value;

            if (!/^[1-7]?$/.test(value)) {
                añoError.style.display = 'block';
                input.setCustomValidity('');
            } else {
                añoError.style.display = 'none';
                input.setCustomValidity('');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('especialidad_id').addEventListener('change', actualizarMaterias);
            actualizarMaterias();
        });
    </script>
</head>
<body>
    <header>
        <nav>
            <a href="alumno_bienvenido.php">Inicio</a>
            <a href="alumno_datos.php">Mis Datos</a>
            <a href="ver_notas.php">Mis Notas</a>
            <a href="logout.php" class="btn">Cerrar Sesión</a>
        </nav>
    </header>
    
    <main>
        <h1>Mis datos</h1>
        <section>
            <h2>Datos del Alumno</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <table>
                    <tr>
                        <th>Nombre:</th>
                        <td>
                            <input type="text" name="nombre" id="nombre" 
                                value="<?php echo htmlspecialchars($alumno['nombre']); ?>" 
                                readonly class="editable">
                        </td>
                    </tr>
                    <tr>
                        <th>Apellido:</th>
                        <td>
                            <input type="text" name="apellido" id="apellido" 
                                value="<?php echo htmlspecialchars($alumno['apellido']); ?>" 
                                readonly class="editable">
                        </td>
                    </tr>
                    <tr>
                        <th>DNI:</th>
                        <td>
                            <input type="text" name="dni" id="dni" 
                                value="<?php echo htmlspecialchars($alumno['dni']); ?>" 
                                readonly class="editable" 
                                oninput="validarDNI(this)" 
                                maxlength="8" pattern="\d{8}">
                            <small class="error" id="dniError" style="color: red; display: none;">El DNI debe contener 8 números.</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Año:</th>
                        <td>
                            <input type="text" name="año" id="año" 
                                value="<?php echo htmlspecialchars($alumno['año']); ?>" 
                                readonly class="editable" 
                                oninput="validarAño(this)" 
                                maxlength="1" pattern="[1-7]">
                            <small class="error" id="añoError" style="color: red; display: none;">El año debe estar entre 1 y 7.</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Fecha de Nacimiento:</th>
                        <td>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" 
                                value="<?php echo htmlspecialchars($alumno['fecha_nacimiento']); ?>" 
                                readonly class="editable">
                        </td>
                    </tr>
                    <tr>
                        <th>Especialidad:</th>
                        <td>
                            <select name="especialidad_id" id="especialidad_id" class="editable">
                                <?php
                                $sql_especialidades = "SELECT id, nombre FROM especialidades";
                                $result_especialidades = $conn->query($sql_especialidades);
                                while ($especialidad = $result_especialidades->fetch_assoc()) {
                                    $selected = ($especialidad['id'] == $especialidad_id) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($especialidad['id']) . "' $selected>" . htmlspecialchars($especialidad['nombre']) . "</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Materia:</th>
                        <td>
                            <select name="materia_id" id="materia_id" class="editable">
                        
                            </select>
                        </td>
                    </tr>
                </table>
                <a href="generate_pdf.php" class="btn" target="_blank">Descargar Datos en PDF</a>
                <button type="button" class="btn" id="btnModificar" onclick="habilitarEdicion()">Modificar Datos</button>
                <button type="submit" class="btn hidden" id="btnGuardar" name="guardar_datos">Guardar Datos</button>
                <button type="button" class="btn hidden" id="btnCancelar" onclick="cancelarEdicion()">Cancelar</button>
            </form>
        </section>
    </main>
    <footer>
        <p>© 2024 Escuela Tecnica</p>
    </footer>
</body>
</html>
