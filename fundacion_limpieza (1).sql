-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 06:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fundacion_limpieza`
--

-- --------------------------------------------------------

--
-- Table structure for table `lotes`
--

CREATE TABLE `lotes` (
  `id` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `cantidad_inicial` decimal(10,2) NOT NULL,
  `cantidad_actual` decimal(10,2) NOT NULL,
  `unidad` enum('L','Kg','Pz') NOT NULL,
  `estado` enum('activo','agotado') DEFAULT 'activo',
  `fecha_ingreso` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lotes`
--

INSERT INTO `lotes` (`id`, `nombre_producto`, `cantidad_inicial`, `cantidad_actual`, `unidad`, `estado`, `fecha_ingreso`) VALUES
(5, 'cloro', 5.00, 4.93, 'L', 'activo', '2026-01-28 13:54:55');

-- --------------------------------------------------------

--
-- Table structure for table `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida') NOT NULL DEFAULT 'salida',
  `cantidad_retirada` decimal(10,2) NOT NULL,
  `area_destino` varchar(50) NOT NULL,
  `responsable` varchar(100) NOT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movimientos`
--

INSERT INTO `movimientos` (`id`, `lote_id`, `tipo_movimiento`, `cantidad_retirada`, `area_destino`, `responsable`, `fecha_movimiento`) VALUES
(1, 5, 'entrada', 5.00, 'Almacén', 'Sistema (Ingreso Inicial)', '2026-01-28 13:54:55'),
(2, 5, 'salida', 0.07, 'Cocina', 'gonzalo', '2026-01-28 13:56:04');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` enum('admin','operador') DEFAULT 'operador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `cedula`, `nombre`, `password_hash`, `fecha_creacion`, `rol`) VALUES
(1, '12345678', 'Administrador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-01-29 13:22:52', 'admin'),
(3, '31077912', 'Gonzalo Diaz', '$2y$10$xyz...', '2026-02-06 15:06:07', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lotes`
--
ALTER TABLE `lotes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lote` (`lote_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lotes`
--
ALTER TABLE `lotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `fk_lote` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
