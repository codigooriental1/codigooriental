-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-09-2026 a las 22:57:26
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
-- Base de datos: `sigsm`
--

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

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ruta_archivo` (`ruta_archivo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
