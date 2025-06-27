-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 27, 2025 at 11:11 AM
-- Server version: 10.3.39-MariaDB-0ubuntu0.20.04.2
-- PHP Version: 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wp_hrybt`
--

-- --------------------------------------------------------

--
-- Table structure for table `SWX7neDE_mylisting_locations`
--

CREATE TABLE `SWX7neDE_mylisting_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(300) NOT NULL,
  `lat` decimal(8,5) NOT NULL,
  `lng` decimal(8,5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `SWX7neDE_mylisting_locations`
--

INSERT INTO `SWX7neDE_mylisting_locations` (`id`, `listing_id`, `address`, `lat`, `lng`) VALUES
(1, 86, 'Cranford Road, Barton Seagrave, Kettering, UK', 52.38070, -0.68300),
(3, 99, '52.20298760214901, -0.24761661227370171', 52.20299, -0.24762),
(6, 101, 'Potton Road, St. Neots, Cambridgeshire PE19 6XJ, UK', 52.19892, -0.24928),
(7, 98, 'Potton Road, St. Neots, Cambridgeshire PE19 6XJ, UK', 52.19892, -0.24928);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `SWX7neDE_mylisting_locations`
--
ALTER TABLE `SWX7neDE_mylisting_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `lat` (`lat`),
  ADD KEY `lng` (`lng`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `SWX7neDE_mylisting_locations`
--
ALTER TABLE `SWX7neDE_mylisting_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `SWX7neDE_mylisting_locations`
--
ALTER TABLE `SWX7neDE_mylisting_locations`
  ADD CONSTRAINT `SWX7neDE_mylisting_locations_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `SWX7neDE_posts` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
