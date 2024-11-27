
# Sistema de Gestión Escolar

**Autor:** Emanuel Schmer  
**Contacto:** emanuelschmer@hotmail.com  

## Descripción

Este proyecto es un **Sistema de Gestión Escolar** diseñado para administrar eficientemente las actividades de una institución educativa. Ofrece funcionalidades específicas para estudiantes, profesores y administradores, integrando herramientas modernas como códigos QR y generación de documentos PDF.

### Funcionalidades

#### Página principal
- Información sobre el colegio.
- Formulario de contacto funcional (PHP Mailer).
- Registro como profesor o alumno.
- Inicio de sesión.

#### Estudiantes
- Visualización de datos personales, notas y contenido de materias.
- Marcado de asistencia mediante un código QR.
- Descarga de información en formato PDF.
- Edición de algunos datos personales.
- Información sobre su especialidad y materias.
- Cierre de sesión.

#### Profesores
- Visualización y edición de datos personales.
- Marcado de asistencia y descarga de registros en PDF.
- Gestión de contenido de materias y notas de los estudiantes.
- Edición de datos de sus materias.
- Cierre de sesión.

#### Administradores
- Gestión completa de:
  - Profesores, alumnos y administradores.
  - Contenidos, notas, materias y especialidades.
- Visualización de tokens de contraseñas recuperadas.

---

## Tecnologías utilizadas

- **Lenguajes:** PHP, HTML, CSS.
- **Librerías y herramientas:**
  - PHP Mailer.
  - PHP QR Code.
  - FPDF y mMakeFont (para PDFs).
- **Base de datos:** MySQL.
- **Editor recomendado:** Visual Studio Code.
- **Servidor local:** XAMPP.

---

## Requisitos

1. **Software necesario:**
   - [XAMPP](https://www.apachefriends.org/index.html) (incluye Apache y MySQL).
   - [Visual Studio Code](https://code.visualstudio.com/).
   - Navegador web.

2. **Extensiones de PHP activas:**
   - `mysqli`
   - `mbstring`
   - `gd`

3. **Archivos del proyecto:** Descargar desde este repositorio.

---

## Instalación

1. Descarga el repositorio como archivo ZIP desde la opción **"Code"** en GitHub y descomprímelo.
2. Mueve la carpeta del proyecto a la carpeta `htdocs` de XAMPP.
3. Inicia XAMPP y activa los módulos **Apache** y **MySQL**.
4. Configura la base de datos:
   - Abre `phpMyAdmin` en tu navegador (`http://localhost/phpmyadmin`).
   - Crea una nueva base de datos llamada `sistema_estudiantes`.
   - Importa el archivo SQL que se encuentra en la carpeta `bd` llamado `sistema.estudiantes.sql`.

---

## Ejecución

1. Inicia el servidor desde XAMPP.
2. Accede al sistema en tu navegador ingresando a `http://localhost/nombre-de-tu-proyecto` (cambia "nombre-de-tu-proyecto" según el nombre de la carpeta donde se colocó el proyecto).

---

## Pendientes

El proyecto aún está en desarrollo y faltan algunas funcionalidades por implementar. Es importante estar pendiente de los próximos cambios y actualizaciones para completar el sistema.

---

## Agradecimientos

Gracias por revisar y considerar mi proyecto. Si tienes dudas o necesitas soporte, puedes contactarme en **emanuelschmer@hotmail.com**.  
