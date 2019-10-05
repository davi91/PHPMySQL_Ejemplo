-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-10-2019 a las 12:30:43
-- Versión del servidor: 10.4.6-MariaDB
-- Versión de PHP: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdresidenciasescolares`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cuentaResidencias` (IN `pnomUniversidad` VARCHAR(30), IN `pprecioMensual` INT, OUT `cantResi` INT, OUT `cantResiPrecio` INT)  begin
		select count(*) into cantResi from residencias 
		inner join universidades on universidades.codUniversidad = residencias.codUniversidad
		where nomUniversidad = pnomUniversidad;
		
		select count(*) into cantResiPrecio from residencias
		inner join universidades on universidades.codUniversidad = residencias.codUniversidad
		where nomUniversidad = pnomUniversidad and precioMensual < pprecioMensual;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_estuEstancias` (IN `dnic` CHAR(9))  begin
	select residencias.nomResidencia, universidades.nomUniversidad,fechaInicio, fechaFin, preciopagado from estancias
	inner join residencias on residencias.codResidencia = estancias.codResidencia
	inner join estudiantes on estudiantes.codEstudiante = estancias.codEstudiante
	inner join universidades on universidades.codUniversidad = residencias.codUniversidad
	where dni like dnic
	order by fechaInicio;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertResidencia` (IN `pnombreResidencia` VARCHAR(30), IN `pcodUniversidad` CHAR(6), IN `pprecioMensual` INT, IN `pcomedor` BOOLEAN, OUT `uExiste` BOOLEAN, OUT `resiInsertada` BOOLEAN)  begin

	set resiInsertada = 0;
	IF exists (select codUniversidad from universidades where codUniversidad = pcodUniversidad) then
	
		set uExiste = 1;
		
		insert residencias values ( NULL, pnombreResidencia, pcodUniversidad, pprecioMensual,
									pcomedor );	
		set resiInsertada = 1;
									
	ELSE
			set uExiste = 0;
			set resiInsertada = 0;
	end if;
		
end$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_tiempoResidencias` (`pdni` CHAR(9)) RETURNS INT(11) begin
	DECLARE meses int;
	
	select SUM(TIMESTAMPDIFF( MONTH, fechaInicio, fechaFin )) into meses from estancias
	inner join estudiantes on estudiantes.codEstudiante = estancias.codEstudiante
	where dni = pdni;
	
	
	return meses;
	
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estancias`
--

CREATE TABLE `estancias` (
  `codEstudiante` int(11) NOT NULL,
  `codResidencia` int(11) NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date DEFAULT NULL,
  `preciopagado` int(11) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `estancias`
--

INSERT INTO `estancias` (`codEstudiante`, `codResidencia`, `fechaInicio`, `fechaFin`, `preciopagado`) VALUES
(1, 1, '1991-01-01', '1992-01-01', 50),
(2, 1, '1991-02-01', '1992-01-01', 150),
(3, 1, '1993-02-01', '1994-01-01', 200),
(3, 2, '1991-05-01', '1992-01-01', 50),
(3, 3, '1992-04-01', '1992-06-01', 120),
(3, 4, '1991-08-01', '1992-01-01', 135),
(4, 1, '1996-02-01', '1997-01-01', 50),
(4, 2, '1994-02-01', '1995-01-01', 150),
(4, 5, '1998-02-01', '1999-01-01', 500),
(6, 1, '1997-02-01', '1998-01-01', 50);

--
-- Disparadores `estancias`
--
DELIMITER $$
CREATE TRIGGER `tr_cambioFecha` BEFORE INSERT ON `estancias` FOR EACH ROW begin
	DECLARE TMP date;
	IF (NEW.fechaInicio > NEW.fechaFin) then	
		SET TMP = NEW.fechaInicio;
		SET NEW.fechaInicio = NEW.fechaFin;
		SET NEW.fechaFin = TMP;
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_cambioFechaUpdate` BEFORE UPDATE ON `estancias` FOR EACH ROW begin
	DECLARE TMP date;
	IF (NEW.fechaInicio > NEW.fechaFin) then	
		SET TMP = NEW.fechaInicio;
		SET NEW.fechaInicio = NEW.fechaFin;
		SET NEW.fechaFin = TMP;
	end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `codEstudiante` int(11) NOT NULL,
  `nomEstudiante` varchar(50) DEFAULT NULL,
  `dni` char(9) DEFAULT NULL,
  `telefornoEstudiante` char(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`codEstudiante`, `nomEstudiante`, `dni`, `telefornoEstudiante`) VALUES
(1, 'estu1', '100111222', '222111000'),
(2, 'estu2', '200111223', '222111001'),
(3, 'estu3', '300111224', '222111002'),
(4, 'estu4', '400111223', '232111005'),
(5, 'estu5', '500111226', '222111005'),
(6, 'estu6', '600111227', '222111008');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `residencias`
--

CREATE TABLE `residencias` (
  `codResidencia` int(11) NOT NULL,
  `nomResidencia` varchar(30) DEFAULT NULL,
  `codUniversidad` char(6) DEFAULT NULL,
  `precioMensual` int(11) DEFAULT 900,
  `comedor` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `residencias`
--

INSERT INTO `residencias` (`codResidencia`, `nomResidencia`, `codUniversidad`, `precioMensual`, `comedor`) VALUES
(1, 'resi1', '000001', 500, 1),
(2, 'resi2', '000002', 900, 0),
(3, 'resi3', '000003', 250, 0),
(4, 'resi4', '000004', 300, 1),
(5, 'resi5', '000005', 900, 0),
(6, 'resi6', '000006', 100, 0),
(7, 'resiExtra', '000001', 1000, 1),
(8, 'resiExtra2', '000004', 1200, 0);

--
-- Disparadores `residencias`
--
DELIMITER $$
CREATE TRIGGER `tr_residenciaEscolar` BEFORE INSERT ON `residencias` FOR EACH ROW begin
	IF (NEW.precioMensual< 900 ) then
		signal sqlstate '45000' set message_text='El precio mensual debe ser superior a 900'; 
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_residenciaEscolarUpdate` BEFORE UPDATE ON `residencias` FOR EACH ROW begin
	IF (NEW.precioMensual< 900 ) then
		signal sqlstate '45000' set message_text='El precio mensual debe ser superior a 900'; 
	end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `universidades`
--

CREATE TABLE `universidades` (
  `codUniversidad` char(6) NOT NULL,
  `nomUniversidad` varchar(30) DEFAULT 'La Laguna'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `universidades`
--

INSERT INTO `universidades` (`codUniversidad`, `nomUniversidad`) VALUES
('000001', 'uni1'),
('000002', 'uni2'),
('000003', 'uni3'),
('000004', 'uni4'),
('000005', 'uni5'),
('000006', 'uni6');

--
-- Disparadores `universidades`
--
DELIMITER $$
CREATE TRIGGER `tr_protectUniversity` BEFORE DELETE ON `universidades` FOR EACH ROW begin
	signal sqlstate '45000' set message_text='No se puede eliminar ninguna universidad'; 
end
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `estancias`
--
ALTER TABLE `estancias`
  ADD PRIMARY KEY (`codEstudiante`,`codResidencia`,`fechaInicio`),
  ADD KEY `fk_resi` (`codResidencia`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`codEstudiante`),
  ADD UNIQUE KEY `u_dni` (`dni`),
  ADD UNIQUE KEY `u_telfEstu` (`telefornoEstudiante`);

--
-- Indices de la tabla `residencias`
--
ALTER TABLE `residencias`
  ADD PRIMARY KEY (`codResidencia`),
  ADD KEY `fk_uni` (`codUniversidad`);

--
-- Indices de la tabla `universidades`
--
ALTER TABLE `universidades`
  ADD PRIMARY KEY (`codUniversidad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `residencias`
--
ALTER TABLE `residencias`
  MODIFY `codResidencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `estancias`
--
ALTER TABLE `estancias`
  ADD CONSTRAINT `fk_estu` FOREIGN KEY (`codEstudiante`) REFERENCES `estudiantes` (`codEstudiante`),
  ADD CONSTRAINT `fk_resi` FOREIGN KEY (`codResidencia`) REFERENCES `residencias` (`codResidencia`);

--
-- Filtros para la tabla `residencias`
--
ALTER TABLE `residencias`
  ADD CONSTRAINT `fk_uni` FOREIGN KEY (`codUniversidad`) REFERENCES `universidades` (`codUniversidad`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
