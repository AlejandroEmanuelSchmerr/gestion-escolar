-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2024 a las 05:09:57
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_estudiantes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `especialidad_id` int(11) DEFAULT NULL,
  `año` int(11) DEFAULT NULL,
  `materia_id` int(11) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `user_type` enum('alumno') NOT NULL DEFAULT 'alumno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id`, `nombre`, `apellido`, `dni`, `especialidad_id`, `año`, `materia_id`, `fecha_nacimiento`, `username`, `password`, `email`, `user_type`) VALUES
(1, 'ema', 'Schmer', '431414', 1, 7, 1, '2005-01-31', 'y', '$2y$10$uRbWroFnVI0h0KLN5u71DuWomb2qRgzeOu6WxzNBGsa4GKtVPPS12', 'cordonaemanuel@gmail.com', 'alumno'),
(4, 'Josemires', 'Rodriguezzes', '22222222', 1, 7, 1, '2001-01-01', 'josemirrs', '$2y$10$h.jM8pmHQxlYiZLdi/bWrO28EHZLyMEnrKOpupHpPdrh/FUfq9e92', 'josemir2021344@gmail.com', 'alumno'),
(5, 'Alejandro', 'schmer', '45932670', 1, 7, 1, '2005-01-31', 'emanuel', '$2y$10$B3GCH8YrisfIorq1Mkx8f.YofRVKXgqzrnfatZMrCE3tvJjc3bw3m', 'emanuelschmer@hotmail.com', 'alumno'),
(8, 'carasd', 'asd', '34141444', 3, 7, 11, '2000-01-31', 'sexoo', '$2y$10$vw099prmQVJ78IZs66oUpOHb2fkG5sedLCLtEM4fQw7dQ3N7DK9ZC', 'asdasdad@gmail.com', 'alumno'),
(9, 'msnusad', 'sdaf', '14949149', 3, 6, 11, '2002-12-31', 'ser', '$2y$10$m4aiY8RIJwyTJ.MQV5HNp.GcyAMt5Rm7LUhtcKqYbJoAzJwGYPiee', 'asdasdasddsasda@gmail.com', 'alumno'),
(10, 'yasas', 'easd', '63463453', 4, 7, 16, '2000-01-31', 'yasa', '$2y$10$JI2XhFf/YI3B7JB7cSXGwu0/lm2IgetDxZWif6dbF8r5CZmVibSK2', 'asdads@gmail.com', 'alumno'),
(13, 'alejandro', 'diaz', '51513514', 2, 6, 6, '2000-01-31', 'alejandro', '$2y$10$N1AHddm7sf5h8pl1gkAirehf7Aqs2jmqRVymiOpiQBPXMPr9fxmXq', 'emanuelschmer777@gmail.com', 'alumno');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('Presente','Ausente','Justificado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_alumnos`
--

CREATE TABLE `asistencia_alumnos` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('presente','ausente') DEFAULT NULL,
  `codigo_qr` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia_alumnos`
--

INSERT INTO `asistencia_alumnos` (`id`, `alumno_id`, `fecha`, `estado`, `codigo_qr`) VALUES
(1, 5, '2024-11-23 17:18:21', NULL, 'qr_asistencia_5.png'),
(4, 13, '2024-11-24 05:00:40', NULL, 'qr_asistencia_13.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `especialidades`
--

INSERT INTO `especialidades` (`id`, `nombre`) VALUES
(1, 'Programacion'),
(2, 'Electricidad'),
(3, 'Automotor'),
(4, 'Electronica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) DEFAULT NULL,
  `materia_id` int(11) DEFAULT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `profesor_id`, `materia_id`, `titulo`, `descripcion`, `archivo`, `fecha`) VALUES
(23, 42, 1, 'Django', 't', 'uploads/Esteban García Suárez[1].pdf', '2024-11-24'),
(24, 42, 1, 'a', 's', 'DJANGO Apunte.pdf', '2024-11-23'),
(25, 43, 11, 'tp2ads', 'afs', '1_intro_java.pdf', '2024-11-23'),
(26, 45, 6, 'Django', 'as', 'Presentación de DIGITALWEB solution athlos (1).pdf', '2024-11-23'),
(27, 46, 16, 'tp2', 'r', 'Presentación de DIGITALWEB solution athlos.pdf', '2024-11-23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `especialidad_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `especialidad_id`) VALUES
(1, 'Programacion I', 1),
(6, 'Circuitos Electricos', 2),
(11, 'Motor de Combustion', 3),
(16, 'Circuitos Digitales', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `nota` decimal(5,2) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id`, `alumno_id`, `profesor_id`, `materia_id`, `nota`, `comentario`, `fecha`) VALUES
(24, 1, 42, 1, 5.00, 'as', '2024-11-24'),
(25, 8, 43, 11, 5.00, 'as', '2024-11-23'),
(26, 13, 45, 6, 6.00, 'as', '2024-11-23'),
(27, 10, 46, 16, 4.00, 'asd', '2024-11-23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires`, `created_at`) VALUES
(26, 'emanuelschmer@hotmail.com', '52f0897fb1b5fa604d59f9f2adda48583f59c2f8188ab822873b44d792ff3077', 1726975570, '2024-09-22 02:26:10'),
(27, 'cordonaemanuel@gmail.com', 'e195077cbbbbcd72359179bc47e21b14342849200e3fe29ebbac391f2159747a', 1727060851, '2024-09-23 02:07:31'),
(28, 'emanuelschmer@hotmail.com', '7bcc159df090cfe46c717d009efa92c671f26327e94921f510a567631213e732', 1727060981, '2024-09-23 02:09:41'),
(31, 'emanuelschmer@hotmail.com', 'e81860dc37a8cbbb7418be2fed4c243f3caa63943d05acbd726a3da63d381d99', 1727133638, '2024-09-23 22:20:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_type` enum('profesor') NOT NULL DEFAULT 'profesor',
  `especialidad_id` int(11) DEFAULT NULL,
  `materia_id` int(11) DEFAULT NULL,
  `apellido` varchar(255) NOT NULL,
  `dni` char(8) NOT NULL,
  `año` tinyint(1) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id`, `nombre`, `username`, `password`, `email`, `user_type`, `especialidad_id`, `materia_id`, `apellido`, `dni`, `año`, `fecha_nacimiento`) VALUES
(42, 'Emanuel', 'ese', '$2y$10$RJiavGdPmElbsj9pFCu17OoBYPcb0dOANUcULc.7sMc5Vk1C/FC4q', 'asdf@gmail.com', 'profesor', 1, 1, 'Schmer', '15155154', 5, '2000-01-31'),
(43, 'robert', 'carlos', '$2y$10$yfjZQzjeFbPl638jIsBXeOPZZ2rPDmgA2wOJo.3UF5WtRjXyxjngG', 'schmerroberto@gmail.com', 'profesor', 3, 11, 'asd', '45515555', 4, '2000-01-31'),
(45, 'robert', 'robert', '$2y$10$JZA.ArLC1jXtD.zcg.4Eq.ZiSWzHdJ4rhwAp9eMRz80zkTpKQ7Cr6', 'robert@gmail.com', 'profesor', 2, 6, 'Schmer', '34414441', 5, '2002-01-31'),
(46, 'jose', 'jose', '$2y$10$uPDnaosvwwX9pqITJvA.I.GzKUeAc2yfRNwxogbjTAAowNzf/jMym', 'jose@gmail.com', 'profesor', 4, 16, 'Rodriguez', '61616166', 5, '2000-01-31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('admin','profesor','alumno') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `user_type`) VALUES
(26, 'admin', '$2y$10$m4aiY8RIJwyTJ.MQV5HNp.GcyAMt5Rm7LUhtcKqYbJoAzJwGYPiee', 'emanuelschmer777@gmail.com', '2024-09-07 16:18:16', 'admin'),
(31, 'admin1', '$2y$10$91h5IWepLOFoLba0gUYrW.U2W1nin/PLqPVmxZs3xRcWkXAY8rCIG', 'cordonaemanuel@gmail.com', '2024-11-23 16:23:14', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `especialidad_id` (`especialidad_id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indices de la tabla `asistencia_alumnos`
--
ALTER TABLE `asistencia_alumnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`);

--
-- Indices de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `especialidad_id` (`especialidad_id`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `especialidad_id` (`especialidad_id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `asistencia_alumnos`
--
ALTER TABLE `asistencia_alumnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `alumnos_ibfk_1` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`),
  ADD CONSTRAINT `alumnos_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencia_ibfk_3` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asistencia_alumnos`
--
ALTER TABLE `asistencia_alumnos`
  ADD CONSTRAINT `asistencia_alumnos_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `materiales_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`);

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `fk_alumno_id` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`),
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`),
  ADD CONSTRAINT `notas_ibfk_3` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);

--
-- Filtros para la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD CONSTRAINT `profesores_ibfk_1` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`),
  ADD CONSTRAINT `profesores_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
