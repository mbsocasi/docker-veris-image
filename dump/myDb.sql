-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-12-2023 a las 01:14:53
-- Versión del servidor: 5.7.14
-- Versión de PHP: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `veris`
--
 
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `IdConsulta` int NOT NULL AUTO_INCREMENT,
  `IdMedico` int NOT NULL,
  `IdPaciente` int NOT NULL,
  `FechaConsulta` date NOT NULL,
  `HI` time NOT NULL,
  `HF` time NOT NULL,
  `Diagnostico` text NOT NULL,
  PRIMARY KEY (`IdConsulta`),
  KEY `IdMedico_FK_idx` (`IdMedico`),
  KEY `IdPaciente_idx` (`IdPaciente`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;


--
-- Volcado de datos para la tabla `consultas`
--

INSERT INTO `consultas` (`IdConsulta`, `IdMedico`, `IdPaciente`, `FechaConsulta`, `HI`, `HF`, `Diagnostico`) VALUES
(1, 1, 1, '2023-01-10', '09:00:00', '10:00:00', 'Presion arterial alta'),
(2, 4, 2, '2023-02-15', '14:00:00', '15:00:00', 'Papiloma Humano'),
(3, 3, 3, '2023-03-20', '12:00:00', '13:00:00', 'Problemas cutaneos'),
(4, 4, 4, '2023-04-25', '16:00:00', '17:00:00', 'Examen ocular'),
(5, 3, 2, '2023-05-30', '10:00:00', '11:00:00', 'Chequeo ginecologico'),
(6, 3, 2, '2023-11-20', '08:00:00', '09:00:00', 'Mamografía'),
(7, 2, 2, '2023-11-22', '10:00:00', '11:00:00', 'Arritmias cardíacas'),
(8, 1, 4, '2023-11-17', '11:00:00', '12:00:00', 'Hipermetropía'),
(9, 4, 2, '2023-10-10', '15:00:00', '16:00:00', 'Acné'),
(10, 1, 1, '2023-09-11', '10:00:00', '11:00:00', 'Glaucoma'),
(11, 1, 4, '2023-08-07', '08:00:00', '09:00:00', 'Astigmatismo'),
(12, 2, 3, '2023-06-20', '11:00:00', '12:00:00', 'Hipertensión arterial'),
(13, 3, 3, '2023-11-07', '13:00:00', '14:00:00', 'Dermatitis atópica'),
(14, 2, 4, '2023-11-03', '08:00:00', '09:00:00', 'Colesterol Alto'),
(15, 4, 1, '2023-11-09', '14:00:00', '15:00:00', 'Acné');
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--
CREATE TABLE `especialidades` (
  `IdEsp` int NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL,
  `Dias` varchar(45) NOT NULL,
  `Franja_HI` time NOT NULL,
  `Franja_HF` time NOT NULL,
  PRIMARY KEY (`IdEsp`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `especialidades`
--

INSERT INTO `especialidades` (`IdEsp`, `Descripcion`, `Dias`, `Franja_HI`, `Franja_HF`) VALUES
(1, 'Cardiologia', 'MJV', '08:00:00', '12:00:00'),
(2, 'Pediatria', 'LMXJV', '08:00:00', '18:00:00'),
(3, 'Dermatologia', 'MJ', '12:00:00', '18:00:00'),
(4, 'Ginecologia', 'LXV', '08:00:00', '18:00:00'),
(5, 'Oftalmologia', 'LV', '08:00:00', '12:00:00'),
(6, 'Proctologo', 'LM', '08:00:00', '12:00:00');


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamentos`
--
CREATE TABLE `medicamentos` (
  `IdMedicamento` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Tipo` varchar(50) NOT NULL,
  PRIMARY KEY (`IdMedicamento`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `medicamentos`
--
INSERT INTO `medicamentos` (`IdMedicamento`, `Nombre`, `Tipo`) VALUES
(1, 'Paracetamol', 'Analgesico'),
(2, 'Amoxicilina', 'Antibiotico'),
(3, 'Loratadina', 'Antihistaminico'),
(4, 'Omeprazol', 'Antiacido'),
(5, 'Aspirina', 'Antiinflamatorio');


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicos`
--

CREATE TABLE  `medicos` (
  `IdMedico` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Especialidad` int NOT NULL,
  `IdUsuario` int NOT NULL,
  PRIMARY KEY (`IdMedico`),
  KEY `FK_IdUsuario_idx` (`IdUsuario`),
  KEY `FK_Especialidad_idx` (`Especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `medicos`
--

INSERT INTO `medicos` (`IdMedico`, `Nombre`, `Especialidad`, `IdUsuario`) VALUES
(1, 'Oscar Lopez', 5, 8),
(2, 'Juan Garcia', 1, 2),
(3, 'Ana Perez', 4, 5),
(4, 'Luis Quintana', 3, 9),
(6, 'Dr. Manotas', 6, 11);


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `IdPaciente` int NOT NULL AUTO_INCREMENT,
  `IdUsuario` int NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Cedula` int UNSIGNED NOT NULL,
  `Edad` int UNSIGNED NOT NULL,
  `Genero` varchar(50) NOT NULL,
  `Estatura` int UNSIGNED NOT NULL,
  `Peso` float UNSIGNED NOT NULL,
  PRIMARY KEY (`IdPaciente`),
  KEY `FK_IdUsuario_idx` (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pacientes`
--


INSERT INTO `pacientes` (`IdPaciente`, `IdUsuario`, `Nombre`, `Cedula`, `Edad`, `Genero`, `Estatura`, `Peso`) VALUES
(1, 3, 'Luis Torres', 1725001976, 20, 'Masculino', 198, 100),
(2, 6, 'Ana Gomez', 1798456712, 70, 'Femenino', 150, 90),
(3, 7, 'Oscar Uribe', 1725001984, 80, 'Masculino', 160, 105),
(4, 4, 'Alex Marquez', 1723547158, 70, 'Masculino', 195, 120);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas`
--

CREATE TABLE `recetas` (
  `IdReceta` int NOT NULL AUTO_INCREMENT,
  `IdConsulta` int NOT NULL,
  `IdMedicamento` int NOT NULL,
  `Cantidad` int NOT NULL,
  PRIMARY KEY (`IdReceta`),
  KEY `FK_IdConsulta_idx` (`IdConsulta`),
  KEY `FK_IdMedicamento_idx` (`IdMedicamento`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `recetas`
--

INSERT INTO `recetas` (`IdReceta`, `IdConsulta`, `IdMedicamento`, `Cantidad`) VALUES
(1, 1, 1, 2),
(2, 2, 2, 1),
(3, 3, 3, 3),
(4, 4, 4, 1),
(5, 5, 5, 2),
(6, 6, 1, 3),
(7, 7, 1, 1),
(8, 8, 5, 2),
(9, 9, 1, 3),
(10, 10, 4, 2),
(11, 11, 5, 2),
(12, 12, 1, 3),
(13, 13, 3, 5),
(14, 14, 4, 5),
(15, 15, 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE  `roles` (
  `IdRol` int NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Accion` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`IdRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`IdRol`, `Nombre`, `Accion`) VALUES
(1, 'Administrador', 'CRUD'),
(2, 'Medico', 'RU'),
(3, 'Paciente', 'RU');


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE  `usuarios` (
  `IdUsuario` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Password` varchar(64) NOT NULL,
  `Rol` int NOT NULL,
  `Foto` varchar(100) NOT NULL,
  PRIMARY KEY (`IdUsuario`),
  KEY `fk_rol_idx` (`Rol`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--
INSERT INTO `usuarios` (`IdUsuario`, `Nombre`, `Password`, `Rol`, `Foto`) VALUES
(1, 'ADM', '123', 1, 'usu01.jpg'),
(2, 'jgarcia', '123', 2, 'usu02.jpg'),
(3, 'ltorres', '123', 3, 'usu03.jpg'),
(4, 'amarquez', '123', 3, 'usu04.jpg'),
(5, 'aperez', '123', 2, 'usu05.jpg'),
(6, 'agomez', '123', 3, 'usu06.jpg'),
(7, 'ouribe', '123', 3, 'usu07.jpg'),
(8, 'olopez', '123', 2, 'usu08.jpg'),
(9, 'lquintana', '123', 2, 'usu09.jpg'),
(11, 'xxx', '123', 2, 'usu11.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `IdMedico_FK` FOREIGN KEY (`IdMedico`) REFERENCES `medicos` (`IdMedico`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `IdPaciente` FOREIGN KEY (`IdPaciente`) REFERENCES `pacientes` (`IdPaciente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD CONSTRAINT `FK_Especialidad` FOREIGN KEY (`Especialidad`) REFERENCES `especialidades` (`IdEsp`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `IdUsuario_FK` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD CONSTRAINT `FK_IdUsuario` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD CONSTRAINT `FK_IdConsulta` FOREIGN KEY (`IdConsulta`) REFERENCES `consultas` (`IdConsulta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_IdMedicamento` FOREIGN KEY (`IdMedicamento`) REFERENCES `medicamentos` (`IdMedicamento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_rol` FOREIGN KEY (`Rol`) REFERENCES `roles` (`IdRol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
