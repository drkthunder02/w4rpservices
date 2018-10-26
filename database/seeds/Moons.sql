-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 13, 2018 at 07:03 AM
-- Server version: 10.2.18-MariaDB-10.2.18+maria~xenial
-- PHP Version: 7.1.22-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moonrental`
--

-- --------------------------------------------------------

--
-- Table structure for table `Moons`
--

CREATE TABLE IF NOT EXISTS `Moons` (
  `id` int(10) NOT NULL,
  `System` varchar(10) DEFAULT NULL,
  `Planet` varchar(10) DEFAULT NULL,
  `Moon` varchar(10) DEFAULT NULL,
  `StructureName` varchar(100) DEFAULT 'No Name',
  `FirstOre` varchar(50) DEFAULT 'None',
  `FirstQuantity` int(3) DEFAULT 0,
  `SecondOre` varchar(50) DEFAULT 'None',
  `SecondQuantity` int(3) DEFAULT 0,
  `ThirdOre` varchar(50) DEFAULT 'None',
  `ThirdQuantity` int(3) DEFAULT 0,
  `FourthOre` varchar(50) DEFAULT 'None',
  `FourthQuantity` int(3) DEFAULT 0,
  `RentalCorp` varchar(50) DEFAULT NULL,
  `RentalEnd` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Moons`
--

INSERT INTO `Moons` (`id`, `System`, `Planet`, `Moon`, `StructureName`, `FirstOre`, `FirstQuantity`, `SecondOre`, `SecondQuantity`, `ThirdOre`, `ThirdQuantity`, `FourthOre`, `FourthQuantity`, `RentalCorp`, `RentalEnd`) VALUES
(50, 'LN-56V', '3', '1', 'No Name', 'Cubic Bistot', 35, 'Stable Veldspar', 35, 'Sylvite', 10, 'Zircon', 20, 'HYPNO', 1533081600),
(52, 'LN-56V', '5', '7', '5-7', 'Loparite', 21, 'Monazite', 20, 'Pellucid Crokite', 31, 'Scintillating Hemorphite', 29, 'UOS', 1515715200),
(53, 'LN-56V', '5', '15', 'PUB', 'Carnotite', 25, 'Opulent Pyroxeres', 45, 'Zircon', 31, 'None', 0, 'HYPNO', 1536624000),
(54, 'JDAS-0', '5', '13', 'No Name', 'Otavite', 41, 'Pellucid Crokite', 30, 'Zeolites', 8, 'Zircon', 20, NULL, NULL),
(55, 'JA-O6J', '10', '1', 'No Name', 'Dazzling Spodumain', 23, 'Flawless Arkonor', 23, 'Vanadinite', 18, 'Ytterbite', 36, NULL, NULL),
(56, 'CX65-5', '5', '13', 'No Name', 'Cinnabar', 33, 'Lustrous Hedbergite', 26, 'Opulent Pyroxeres', 8, 'Pellucid Crokite', 31, NULL, NULL),
(57, 'CX65-5', '5', '14', 'No Name', 'Glossy Scordite', 19, 'Pellucid Crokite', 57, 'Ytterbite', 22, 'None', 0, NULL, NULL),
(58, 'CX65-5', '6', '4', 'No Name', 'Cinnabar', 23, 'Immaculate Jaspet', 35, 'Sylvite', 8, 'Zeolites', 32, NULL, NULL),
(59, 'CX65-5', '7', '3', 'No Name', 'Cinnabar', 17, 'Flawless Arkonor', 44, 'Glossy Scordite', 26, 'Scheelite', 11, NULL, NULL),
(60, '6X7-JO', '7', '6', 'Highland Retreat', 'Chromite', 19, 'Pellucid Crokite', 23, 'Vanadinite', 18, 'Ytterbite', 40, 'HYPN0', 1536537600),
(61, 'OGL8-Q', '3', '10', 'Corkscrew', 'Brilliant Gneiss', 24, 'Cinnabar', 32, 'Resplendant Kernite', 18, 'Zircon', 26, 'OS88', 1536624000),
(62, 'OGL8-Q', '5', '2', 'Rental Palace', 'Brilliant Gneiss', 27, 'Cinnabar', 23, 'Scintillating Hemorphite', 39, 'Zeolites', 11, NULL, NULL),
(63, 'J-ODE7', '5', '15', 'No Name', 'Cinnabar', 20, 'Coesite', 44, 'Sperrylite', 35, 'None', 0, NULL, NULL),
(64, 'WQH-4K', '3', '2', 'No Name', 'Cubic Bistot', 21, 'Loparite', 10, 'Opulent Pyroxeres', 41, 'Sparkling Plagioclase', 29, NULL, NULL),
(65, 'WQH-4K', '6', '18', 'Hells Bells', 'Flawless Arkonor', 21, 'Immaculate Jaspet', 22, 'Loparite', 36, 'Sperrylite', 20, NULL, NULL),
(66, 'WQH-4K', '7', '2', 'No Name', 'Pellucid Crokite', 32, 'Resplendant Kernite', 15, 'Scintillating Hemorphite', 42, 'Ytterbite', 11, NULL, NULL),
(67, 'Q-S7ZD', '3', '1', 'Scotland The Brave', 'Cinnabar', 36, 'Lustrous Hedbergite', 26, 'Resplendant Kernite', 28, 'Sparkling Plagioclase', 10, NULL, NULL),
(68, 'Q-S7ZD', '5', '12', 'HYPN0 DOME', 'Platinoid Omber', 55, 'Ytterbite', 45, 'None', 0, 'None', 0, 'HYPNO', 1536624000),
(69, 'GJ0-OJ', '8', '14', 'Therapy Room', 'Opulent Pyroxeres', 31, 'Scintillating Hemorphite', 28, 'Stable Veldspar', 8, 'Cinnabar', 33, NULL, NULL),
(70, 'GJ0-OJ', '8', '17', 'No Name', 'Cinnabar', 20, 'Cubic Bistot', 29, 'Lustrous Hedbergite', 42, 'Scheelite', 8, NULL, NULL),
(71, 'XVV-21', '10', '4', 'No Name', 'Glossy Scordite', 17, 'Platinoid Omber', 20, 'Stable Veldspar', 22, 'Ytterbite', 42, NULL, NULL),
(72, 'O7-7UX', '3', '2', 'No Name', 'Dazzling Spodumain', 35, 'Euxenite', 10, 'Lustrous Hedbergite', 36, 'Zircon', 18, NULL, NULL),
(73, 'PPFB-U', '4', '15', 'No Name', 'Immaculate Jaspet', 42, 'Titanite', 9, 'Zeolites', 33, 'Zircon', 17, 'Not Rented', NULL),
(74, 'PPFB-U', '4', '1', 'No Name', 'Euxenite', 11, 'Immaculate Jaspet', 44, 'Platinoid Omber', 28, 'Zircon', 17, 'Not Rented', NULL),
(75, 'PPFB-U', '6', '23', 'No Name', 'Dazzling Spodumain', 38, 'Lustrous Hedbergite', 31, 'Scheelite', 11, 'Zircon', 20, 'Not Rented', NULL),
(76, 'Y-ORBJ', '10', '2', 'No Name', 'Jet Ochre', 31, 'Lustrous Hedbergite', 37, 'Sylvite', 10, 'Zircon', 23, 'Not Rented', NULL),
(77, 'S4-9DN', '5', '2', 'No Name', 'Cobaltite', 11, 'Opulent Pyroxeres', 27, 'Resplendant Kernite', 45, 'Zircon', 17, 'Not Rented', NULL),
(78, 'S4-9DN', '5', '9', 'No Name', 'Loparite', 16, 'Lustrous Hedbergite', 33, 'Monazite', 19, 'Sparkling Plagioclase', 32, 'Not Rented', NULL),
(79, 'S4-9DN', '9', '4', 'No Name', 'Immaculate Jaspet', 20, 'Lustrous Hedbergite', 17, 'Monazite', 46, 'Otavite', 17, 'Not Rented', NULL),
(80, 'WB-AYY', '4', '15', 'No Name', 'Euxenite', 11, 'Sperrylite', 40, 'Zeolites', 25, 'Zircon', 24, 'Not Rented', NULL),
(81, 'WB-AYY', '6', '11', 'No Name', 'Otavite', 19, 'Platinoid Omber', 22, 'Stable Veldspar', 16, 'Ytterbite', 44, 'Not Rented', NULL),
(82, 'QI-S9W', '7', '2', 'No Name', 'Cinnabar', 30, 'Coesite', 21, 'Opulent Pyroxeres', 19, 'Zircon', 30, NULL, NULL),
(83, 'B-A587', '6', '6', 'No Name', 'Chromite', 22, 'Opulent Pyroxeres', 17, 'Platinoid Omber', 17, 'Ytterbite', 44, NULL, NULL),
(84, 'LN-56V', '4', '8', 'No Name', 'Dazzling Spodumain', 10, 'Flawless Arkonor', 32, 'Glossy Scordite', 29, 'Zircon', 29, NULL, NULL);

--
-- AUTO_INCREMENT for table `Moons`
--
ALTER TABLE `Moons`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
