-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 24, 2025 at 08:27 AM
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
-- Table structure for table `SWX7neDE_postmeta`
--

CREATE TABLE `SWX7neDE_postmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `SWX7neDE_postmeta`
--

INSERT INTO `SWX7neDE_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(389, 66, '_edit_last', '2'),
(390, 66, '_case27_listing_type', 'plant-hire'),
(391, 66, '_featured', '0'),
(392, 66, '_claimed', '0'),
(393, 66, '_edit_lock', '1750753613:2'),
(405, 66, '_required-plant-hire-fields', ''),
(406, 66, '_listing-details', ''),
(407, 66, '_plant-type-eg-excavator-or-cherry-picker', ''),
(408, 66, '_contact-information', ''),
(409, 66, '_company-name', 'BuildSoftware'),
(410, 66, '_job_email', 'shane@buildsoftware.co.za'),
(411, 66, '_job_phone', '0604607122'),
(412, 66, '_job_website', 'https://www.buildsoftware.co.za'),
(413, 66, '_optional-plant-hire-details', ''),
(414, 66, '_hire-rate-pricing', ''),
(415, 66, '_weekly-hire-rate', ''),
(416, 66, '_form_heading', ''),
(417, 66, '_job_logo', 'a:0:{}'),
(418, 66, '_job_gallery', 'a:0:{}'),
(419, 66, '_attachments-available-for-hire', ''),
(420, 66, '_social-networks', ''),
(421, 66, '_links', 'a:2:{i:0;a:2:{s:7:\"network\";s:8:\"Facebook\";s:3:\"url\";s:19:\"http://url.facebook\";}i:1;a:2:{s:7:\"network\";s:8:\"LinkedIn\";s:3:\"url\";s:19:\"http://url.linkedIn\";}}'),
(422, 66, '_work-hours', ''),
(423, 66, '_work_hours', 'a:8:{s:6:\"Monday\";a:2:{s:6:\"status\";s:11:\"enter-hours\";i:0;a:2:{s:4:\"from\";s:5:\"07:30\";s:2:\"to\";s:5:\"18:00\";}}s:7:\"Tuesday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:9:\"Wednesday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:8:\"Thursday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:6:\"Friday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:8:\"Saturday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:6:\"Sunday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:8:\"timezone\";s:3:\"UTC\";}'),
(424, 66, '_location', ''),
(425, 66, '_', ''),
(426, 66, '_hire-rental', 'Plant Hire'),
(427, 66, '_forhire', ''),

--
-- Indexes for dumped tables
--

--
-- Indexes for table `SWX7neDE_postmeta`
--
ALTER TABLE `SWX7neDE_postmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `SWX7neDE_postmeta`
--
ALTER TABLE `SWX7neDE_postmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=432;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
