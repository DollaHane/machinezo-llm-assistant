-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 23, 2025 at 12:54 PM
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
(172, 40, '_edit_last', '1'),
(173, 40, '_case27_listing_type', 'plant-hire'),
(174, 40, '_featured', '0'),
(175, 40, '_claimed', '0'),
(176, 40, '_edit_lock', '1750683261:1'),
(177, 40, '_job_expires', '2025-07-13'),
(178, 40, '_required-plant-hire-fields', ''),
(179, 40, '_listing-details', ''),
(180, 40, '_plant-type-eg-excavator-or-cherry-picker', ''),
(181, 40, '_contact-information', ''),
(182, 40, '_company-name', 'O\'Connell Plant'),
(183, 40, '_job_email', 'info@oconnellgroup.co.uk'),
(184, 40, '_job_phone', '+4420 7474 0109'),
(185, 40, '_job_website', 'https://www.oconnellgroup.co.uk/'),
(186, 40, '_optional-plant-hire-details', ''),
(187, 40, '_hire-rate-pricing', ''),
(188, 40, '_weekly-hire-rate', ''),
(189, 40, '_form_heading', ''),
(190, 40, '_job_logo', 'a:0:{}'),
(191, 40, '_job_gallery', 'a:0:{}'),
(192, 40, '_attachments-available-for-hire', ''),
(193, 40, '_social-networks', ''),
(194, 40, '_links', 'a:1:{i:0;a:2:{s:7:\"network\";s:8:\"Facebook\";s:3:\"url\";s:84:\"https://www.facebook.com/people/OConnell-Plant-Hire-Groundworks-LTD/100090927452394/\";}}'),
(195, 40, '_work-hours', ''),
(196, 40, '_work_hours', 'a:8:{s:6:\"Monday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:7:\"Tuesday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:9:\"Wednesday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:8:\"Thursday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:6:\"Friday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:8:\"Saturday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:6:\"Sunday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:8:\"timezone\";s:3:\"UTC\";}'),
(197, 40, '_location', ''),
(198, 40, '_', ''),
(199, 40, '_hire-rental', 'Operated Hire'),
(200, 40, '_forhire', 'For Hire'),
(204, 40, '_case27_review_count', '0'),
(399, 40, '_elementor_page_assets', 'a:0:{}');

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
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=400;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
