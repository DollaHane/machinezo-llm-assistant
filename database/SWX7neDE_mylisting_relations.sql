-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 28, 2025 at 10:00 AM
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
-- Table structure for table `SWX7neDE_mylisting_relations`
--

CREATE TABLE `SWX7neDE_mylisting_relations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_listing_id` bigint(20) UNSIGNED NOT NULL,
  `child_listing_id` bigint(20) UNSIGNED NOT NULL,
  `field_key` varchar(96) NOT NULL,
  `item_order` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `SWX7neDE_mylisting_relations`
--

INSERT INTO `SWX7neDE_mylisting_relations` (`id`, `parent_listing_id`, `child_listing_id`, `field_key`, `item_order`) VALUES
(3, 99, 98, 'related_listing', 0),
(7, 101, 99, 'related_listing', 0),
(8, 111, 40, 'related_listing', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `SWX7neDE_mylisting_relations`
--
ALTER TABLE `SWX7neDE_mylisting_relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_listing_id` (`parent_listing_id`),
  ADD KEY `child_listing_id` (`child_listing_id`),
  ADD KEY `field_key` (`field_key`),
  ADD KEY `item_order` (`item_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `SWX7neDE_mylisting_relations`
--
ALTER TABLE `SWX7neDE_mylisting_relations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `SWX7neDE_mylisting_relations`
--
ALTER TABLE `SWX7neDE_mylisting_relations`
  ADD CONSTRAINT `SWX7neDE_mylisting_relations_ibfk_1` FOREIGN KEY (`parent_listing_id`) REFERENCES `SWX7neDE_posts` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `SWX7neDE_mylisting_relations_ibfk_2` FOREIGN KEY (`child_listing_id`) REFERENCES `SWX7neDE_posts` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
