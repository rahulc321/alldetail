-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 18, 2021 at 02:45 AM
-- Server version: 10.2.33-MariaDB-cll-lve
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pcmartin_22`
--

-- --------------------------------------------------------

--
-- Table structure for table `arcust`
--

CREATE TABLE `arcust` (
  `id` int(11) NOT NULL,
  `Organization_Number` varchar(255) DEFAULT NULL,
  `Organization_Name` varchar(255) DEFAULT NULL,
  `Address1` varchar(255) DEFAULT NULL,
  `Address2` varchar(255) DEFAULT NULL,
  `Address3` varchar(255) DEFAULT NULL,
  `Address4` varchar(255) DEFAULT NULL,
  `Attention` varchar(255) DEFAULT NULL,
  `Contact` varchar(255) DEFAULT NULL,
  `Primary_Phone` varchar(255) DEFAULT NULL,
  `Secondary_Phone` varchar(255) DEFAULT NULL,
  `Fax` varchar(255) DEFAULT NULL,
  `Primary_Email` varchar(255) DEFAULT NULL,
  `Area` varchar(255) DEFAULT NULL,
  `Agent` varchar(255) DEFAULT NULL,
  `Blacklist` varchar(255) DEFAULT NULL,
  `ROC` varchar(255) DEFAULT NULL,
  `GST` varchar(255) DEFAULT NULL,
  `Created_Time` varchar(255) DEFAULT NULL,
  `modified` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `arcust`
--

INSERT INTO `arcust` (`id`, `Organization_Number`, `Organization_Name`, `Address1`, `Address2`, `Address3`, `Address4`, `Attention`, `Contact`, `Primary_Phone`, `Secondary_Phone`, `Fax`, `Primary_Email`, `Area`, `Agent`, `Blacklist`, `ROC`, `GST`, `Created_Time`, `modified`) VALUES
(1, '3000/A01', 'A COMPANY SDN BHD', '111', '222', '333', NULL, 'MR LEE KIM SENG', NULL, '07-2345678', NULL, NULL, 'leekimseng@gmail.com', NULL, NULL, NULL, NULL, NULL, '1690708399', 0),
(2, '3000/B01', 'B COMPANY ENTERPRISE SDN BHD', NULL, NULL, NULL, NULL, 'MISS SARASWATY DO MUTUSAMY', NULL, '08-1234578', NULL, NULL, 'sarawaty@gmail.com', NULL, NULL, NULL, NULL, NULL, '1690708399', 0),
(3, '3000/C01', 'C COMPANY INTERNATIONAL ENGINEERING SDND', NULL, NULL, NULL, NULL, 'MR WAN AHMAD BIN WAN ALI', NULL, '03-12345678', NULL, NULL, 'wanahmad@gmail.com', NULL, NULL, NULL, NULL, NULL, '1690708399', 0),
(4, '3000/D01', 'D COMPANY BERHAD', NULL, NULL, NULL, NULL, 'MR TOMMY TAN', 'MR TOMMY TAN', '012-222 3333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1646743399', 0),
(5, '3000/E01', 'E Enterprise Sdn Bhd', NULL, NULL, NULL, NULL, 'Mr Alex Ng', 'Mr Alex Ng', '04-2222 4444', NULL, NULL, 'alexng@gmail.com', NULL, NULL, NULL, NULL, NULL, '1650893400', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arcust`
--
ALTER TABLE `arcust`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arcust`
--
ALTER TABLE `arcust`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
