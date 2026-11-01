-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-10-2026 a las 19:43:46
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
-- Base de datos: `hospital_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`, `descripcion`) VALUES
(1, 'General', 'Documentos generales del hospital');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `tipo` enum('indicacion','informacion') NOT NULL,
  `cedula_paciente` varchar(8) NOT NULL,
  `fecha_emision` date NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `documento`
--

INSERT INTO `documento` (`id`, `titulo`, `tipo`, `cedula_paciente`, `fecha_emision`, `ruta_archivo`, `activo`) VALUES
(1, 'Indicaciones para Análisis de Sangre', 'indicacion', '45678912', '2026-03-10', 'documentos/indicacion_45678912_001.pdf', 1),
(2, 'Horarios de Visita Sala 3', 'informacion', '12345678', '2026-03-15', 'documentos/informacion_12345678_002.pdf', 1),
(3, 'Dieta preoperatoria CTI', 'indicacion', '87654321', '2026-03-20', 'documentos/indicacion_87654321_003.pdf', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuesta`
--

CREATE TABLE `encuesta` (
  `id_encuesta` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'Abierta',
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encuesta`
--

INSERT INTO `encuesta` (`id_encuesta`, `nombre`, `descripcion`, `estado`, `fecha_creacion`) VALUES
(1, 'Encuesta de Satisfacción del Paciente', 'Nos interesa conocer tu opinión sobre la atención recibida en nuestras instalaciones.', 'Abierta', '2026-08-30 23:48:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envio_encuesta`
--

CREATE TABLE `envio_encuesta` (
  `id_envio` int(11) NOT NULL,
  `fecha_envio` datetime DEFAULT current_timestamp(),
  `id_encuesta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `envio_encuesta`
--

INSERT INTO `envio_encuesta` (`id_envio`, `fecha_envio`, `id_encuesta`) VALUES
(1, '2026-08-30 23:48:32', 1),
(2, '2026-10-30 19:44:02', 1),
(3, '2026-10-30 20:06:43', 1),
(4, '2026-10-30 20:18:10', 1),
(5, '2026-10-30 20:19:40', 1),
(6, '2026-10-30 22:53:13', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE `pregunta` (
  `id_pregunta` int(11) NOT NULL,
  `texto` text NOT NULL,
  `tipo` enum('Texto','Opcion','Escala') NOT NULL,
  `id_encuesta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`id_pregunta`, `texto`, `tipo`, `id_encuesta`) VALUES
(1, '¿Cómo calificaría la atención recibida?', 'Escala', 1),
(2, '¿Desea agregar algún comentario o sugerencia?', 'Texto', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `qr`
--

CREATE TABLE `qr` (
  `id_qr` int(11) NOT NULL,
  `codigo_qr` varchar(255) NOT NULL,
  `fecha_generacion` datetime DEFAULT current_timestamp(),
  `ubicacion` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta`
--

CREATE TABLE `respuesta` (
  `id_respuesta` int(11) NOT NULL,
  `valor` text NOT NULL,
  `id_pregunta` int(11) NOT NULL,
  `id_envio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta`
--

INSERT INTO `respuesta` (`id_respuesta`, `valor`, `id_pregunta`, `id_envio`) VALUES
(1, 'Excelente / 5', 1, 1),
(2, 'Muy buena atención en el sector de recepción.', 2, 1),
(3, 'Excelente / 5', 1, 2),
(4, 'Estuvo perfecto, buen cafe hay!', 2, 2),
(5, 'Excelente / 5', 1, 3),
(6, 'Muy buena atencion en medica B.', 2, 3),
(7, 'Buena / 3', 1, 4),
(8, 'En general buen servicio.', 2, 4),
(9, 'Mala / 1', 1, 5),
(10, 'No me gusto el cafe.', 2, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Control total'),
(2, 'Paciente', 'Usuario paciente del sistema'),
(3, 'Funcionario', 'Personal autorizado para gestionar, subir y dar de baja documentos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `cedula` varchar(8) DEFAULT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `cedula`, `correo`, `contrasena`, `id_rol`) VALUES
(1, 'Funcionario', 'Prueba', '12345678', 'funcionario@hospital.com', '1234', 3),
(2, 'Administrador', 'General', '87654321', 'admin@hc.edu.uy', 'admin123', 1),
(8, 'Nahuel', 'Tercia', '57037731', 'cntercia@gmail.com', '$2y$10$hVotSHLkLfNWyZABBZUa6Ohv2hxWn6XXJchMyt/1ot0j8aRR6C8a.', 2),
(9, 'Adriana', 'Peralta', '25500681', 'peraltadri@gmail.com', '$2y$10$NZ6.CNWpaJiqWjUEcriZF.eXZl8O1T3BZCxiAQOJpDUxIrKoM27hW', 2),
(12, 'Avril', 'Lutzen', '57684485', 'avrillarchive02@gmail.com', '$2y$10$sYynmFZ.tBUzoW9iIbXHyur0B1n8InQgT8BVXE4iWBMvNc59UDk3a', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ruta_archivo` (`ruta_archivo`);

--
-- Indices de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  ADD PRIMARY KEY (`id_encuesta`);

--
-- Indices de la tabla `envio_encuesta`
--
ALTER TABLE `envio_encuesta`
  ADD PRIMARY KEY (`id_envio`),
  ADD KEY `id_encuesta` (`id_encuesta`);

--
-- Indices de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD PRIMARY KEY (`id_pregunta`),
  ADD KEY `id_encuesta` (`id_encuesta`);

--
-- Indices de la tabla `qr`
--
ALTER TABLE `qr`
  ADD PRIMARY KEY (`id_qr`),
  ADD UNIQUE KEY `codigo_qr` (`codigo_qr`);

--
-- Indices de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_pregunta` (`id_pregunta`),
  ADD KEY `id_envio` (`id_envio`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `correo_2` (`correo`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  MODIFY `id_encuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `envio_encuesta`
--
ALTER TABLE `envio_encuesta`
  MODIFY `id_envio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `id_pregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `qr`
--
ALTER TABLE `qr`
  MODIFY `id_qr` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `envio_encuesta`
--
ALTER TABLE `envio_encuesta`
  ADD CONSTRAINT `envio_encuesta_ibfk_1` FOREIGN KEY (`id_encuesta`) REFERENCES `encuesta` (`id_encuesta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD CONSTRAINT `pregunta_ibfk_1` FOREIGN KEY (`id_encuesta`) REFERENCES `encuesta` (`id_encuesta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD CONSTRAINT `respuesta_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `pregunta` (`id_pregunta`) ON DELETE CASCADE,
  ADD CONSTRAINT `respuesta_ibfk_2` FOREIGN KEY (`id_envio`) REFERENCES `envio_encuesta` (`id_envio`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
