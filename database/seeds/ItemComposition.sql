-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2018 at 10:07 PM
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
-- Table structure for table `ItemComposition`
--

CREATE TABLE `item_composition` (
  `Name` varchar(31) DEFAULT NULL,
  `ItemId` int(10) NOT NULL,
  `m3Size` decimal(10,2) NOT NULL DEFAULT 0.00,
  `BatchSize` int(12) NOT NULL DEFAULT 100,
  `Tritanium` int(12) DEFAULT 0,
  `Pyerite` int(12) DEFAULT 0,
  `Mexallon` int(12) DEFAULT 0,
  `Isogen` int(12) DEFAULT 0,
  `Nocxium` int(12) DEFAULT 0,
  `Zydrine` int(12) DEFAULT 0,
  `Megacyte` int(12) DEFAULT 0,
  `Morphite` int(12) DEFAULT 0,
  `HeavyWater` int(11) NOT NULL DEFAULT 0,
  `LiquidOzone` int(11) NOT NULL DEFAULT 0,
  `NitrogenIsotopes` int(11) NOT NULL DEFAULT 0,
  `HeliumIsotopes` int(11) NOT NULL DEFAULT 0,
  `HydrogenIsotopes` int(11) NOT NULL DEFAULT 0,
  `OxygenIsotopes` int(11) NOT NULL DEFAULT 0,
  `StrontiumClathrates` int(11) NOT NULL DEFAULT 0,
  `AtmosphericGases` int(11) NOT NULL DEFAULT 0,
  `EvaporiteDeposits` int(11) NOT NULL DEFAULT 0,
  `Hydrocarbons` int(11) NOT NULL DEFAULT 0,
  `Silicates` int(11) NOT NULL DEFAULT 0,
  `Cobalt` int(11) NOT NULL DEFAULT 0,
  `Scandium` int(11) NOT NULL DEFAULT 0,
  `Titanium` int(11) NOT NULL DEFAULT 0,
  `Tungsten` int(11) NOT NULL DEFAULT 0,
  `Cadmium` int(11) NOT NULL DEFAULT 0,
  `Platinum` int(11) NOT NULL DEFAULT 0,
  `Vanadium` int(11) NOT NULL DEFAULT 0,
  `Chromium` int(11) NOT NULL DEFAULT 0,
  `Technetium` int(11) NOT NULL DEFAULT 0,
  `Hafnium` int(11) NOT NULL DEFAULT 0,
  `Caesium` int(11) NOT NULL DEFAULT 0,
  `Mercury` int(11) NOT NULL DEFAULT 0,
  `Dysprosium` int(11) NOT NULL DEFAULT 0,
  `Neodymium` int(11) NOT NULL DEFAULT 0,
  `Promethium` int(11) NOT NULL DEFAULT 0,
  `Thulium` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ItemComposition`
--

INSERT INTO `item_composition` (`Name`, `ItemId`, `m3Size`, `BatchSize`, `Tritanium`, `Pyerite`, `Mexallon`, `Isogen`, `Nocxium`, `Zydrine`, `Megacyte`, `Morphite`, `HeavyWater`, `LiquidOzone`, `NitrogenIsotopes`, `HeliumIsotopes`, `HydrogenIsotopes`, `OxygenIsotopes`, `StrontiumClathrates`, `AtmosphericGases`, `EvaporiteDeposits`, `Hydrocarbons`, `Silicates`, `Cobalt`, `Scandium`, `Titanium`, `Tungsten`, `Cadmium`, `Platinum`, `Vanadium`, `Chromium`, `Technetium`, `Hafnium`, `Caesium`, `Mercury`, `Dysprosium`, `Neodymium`, `Promethium`, `Thulium`) VALUES
('Flawless Arkonor', 46678, '16.00', 100, 24200, 0, 2750, 0, 0, 0, 352, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Cubic Bistot', 46676, '16.00', 100, 0, 13800, 0, 0, 0, 518, 115, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Pellucid Crokite', 46677, '16.00', 100, 24150, 0, 0, 0, 874, 155, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Jet Ochre', 46675, '8.00', 100, 11500, 0, 0, 1840, 130, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Brilliant Gneiss', 46679, '5.00', 100, 0, 2530, 2760, 345, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Lustrous Hedbergite', 46680, '3.00', 100, 0, 1150, 0, 230, 115, 22, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Scintillating Hemorphite', 46681, '3.00', 100, 2530, 0, 0, 115, 138, 17, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Immaculate Jaspet', 46682, '2.00', 100, 0, 0, 403, 0, 86, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Resplendant Kernite', 46683, '12.00', 100, 154, 0, 307, 154, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Platinoid Omber', 46684, '0.60', 100, 920, 115, 0, 98, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Sparkling Plagioclase', 46685, '0.35', 100, 123, 245, 123, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Opulent Pyroxeres', 46686, '0.31', 100, 404, 29, 58, 0, 6, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Glossy Scordite', 46687, '0.15', 100, 398, 199, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Dazzling Spodumain', 46688, '16.00', 100, 64400, 13858, 2415, 518, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Stable Veldspar', 46689, '0.10', 100, 477, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Zeolites', 45490, '10.00', 100, 4000, 8000, 400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 65, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Sylvite', 45491, '10.00', 100, 8000, 4000, 400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 65, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Bitumens', 45492, '10.00', 100, 6000, 6000, 400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 65, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Coesite', 45493, '10.00', 100, 10000, 2000, 400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 65, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Cobaltite', 45494, '10.00', 100, 7500, 10000, 500, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Euxenite', 45495, '10.00', 100, 10000, 7500, 500, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Titanite', 45496, '10.00', 100, 15000, 2500, 500, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Scheelite', 45497, '10.00', 100, 12500, 5000, 500, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Otavite', 45498, '10.00', 100, 5000, 0, 1500, 500, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Sperrylite', 45499, '10.00', 100, 10000, 0, 2000, 2000, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Vanadinite', 45500, '10.00', 100, 0, 5000, 750, 1250, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Chromite', 45501, '10.00', 100, 0, 5000, 1250, 750, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0),
('Carnotite', 45502, '10.00', 100, 0, 0, 1000, 1250, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0),
('Zircon', 45503, '10.00', 100, 0, 0, 1750, 500, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 15, 0, 0, 10, 0, 0, 0, 0, 0, 0, 50, 0, 0, 0, 0, 0, 0),
('Pollucite', 45504, '10.00', 100, 0, 0, 1250, 1000, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 15, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 50, 0, 0, 0, 0, 0),
('Cinnabar', 45506, '10.00', 100, 0, 0, 1500, 750, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 50, 0, 0, 0, 0),
('Xenotime', 45510, '10.00', 100, 0, 0, 0, 0, 200, 100, 50, 0, 0, 0, 0, 0, 0, 0, 0, 20, 0, 0, 0, 20, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 22, 0, 0, 0),
('Monazite', 45511, '10.00', 100, 0, 0, 0, 0, 50, 150, 150, 0, 0, 0, 0, 0, 0, 0, 0, 0, 20, 0, 0, 0, 0, 0, 20, 0, 0, 0, 10, 0, 0, 0, 0, 0, 22, 0, 0),
('Loparite', 45512, '10.00', 100, 0, 0, 0, 0, 100, 200, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 20, 0, 0, 20, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 22, 0),
('Ytterbite', 45513, '10.00', 100, 0, 0, 0, 0, 50, 100, 200, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 20, 0, 0, 20, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 22);

--
-- Indexes for table `ItemComposition`
--
ALTER TABLE `item_composition`
  ADD UNIQUE KEY `oreName` (`Name`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
