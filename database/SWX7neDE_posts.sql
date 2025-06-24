-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 23, 2025 at 12:41 PM
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
-- Table structure for table `SWX7neDE_posts`
--

CREATE TABLE `SWX7neDE_posts` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `post_author` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(255) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext NOT NULL,
  `post_parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT 0,
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `SWX7neDE_posts`
--

INSERT INTO `SWX7neDE_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES
(40, 1, '2025-06-13 13:21:22', '2025-06-13 13:21:22', '', 'JCB3CX Hire in London', '', 'publish', 'open', 'closed', '', 'jcb3cx-hire-in-london', '', '', '2025-06-13 13:21:22', '2025-06-13 13:21:22', '', 0, 'https://machinezo.co.uk/?post_type=job_listing&#038;p=40', 0, 'job_listing', '', 0),
(65, 1, '2025-06-23 10:02:32', '2025-06-23 10:02:32', '<h1>Book Plant Equipment For Hire</h1>				\n					<h3>Search for machinery near you</h3>				\n					<h2>Our Latest Plant Hire Listings</h2>				\n<section\n	data-lf-config=\"{&quot;widget_id&quot;:&quot;ed12c38&quot;,&quot;template&quot;:&quot;grid&quot;,&quot;listing_types&quot;:&quot;&quot;,&quot;pagination&quot;:{&quot;enabled&quot;:false,&quot;type&quot;:&quot;prev-next&quot;},&quot;disable_isotope&quot;:false,&quot;filters&quot;:{&quot;page&quot;:null,&quot;posts_per_page&quot;:6,&quot;order_by&quot;:&quot;date&quot;,&quot;order&quot;:&quot;DESC&quot;,&quot;priority&quot;:&quot;&quot;,&quot;behavior&quot;:&quot;yes&quot;,&quot;authors&quot;:[],&quot;categories&quot;:&quot;&quot;,&quot;regions&quot;:&quot;&quot;,&quot;tags&quot;:&quot;&quot;,&quot;custom_taxonomies&quot;:{&quot;attachments&quot;:&quot;Attachments&quot;},&quot;listings&quot;:[]},&quot;listing_wrap&quot;:&quot;col-lg-4 col-md-4 col-sm-6 col-xs-12 grid-item&quot;,&quot;query&quot;:{&quot;method&quot;:&quot;filters&quot;,&quot;string&quot;:&quot;&quot;,&quot;cache_for&quot;:720}}\"\n>\n    <a href=\"https://machinezo.co.uk/?job_listing=jcb3cx-hire-in-london\">\n            <h4>\n                JCB3CX Hire in London                            </h4>\n<ul>\n	            <li >\n            	                O&#039;Connell Plant             </li>\n        </ul>\n    </a>\n                    For Hire                 \n                    England                 \n            <ul>\n                <li>\n                    <a href=\"https://machinezo.co.uk/?job_listing_category=backhoe-loaders\">\n                        Backhoe Loaders\n                    </a>\n                </li>\n                            </ul>\n                <ul>\n                                            <li>\n    <a aria-label=\"Quick view button\" href=\"#\" type=\"button\" data-id=\"40\">\n    </a>\n    Quick view\n</li>                                                                <li>\n    <a aria-label=\"Bookmark button\" href=\"#\"\n       data-listing-id=\"40\" onclick=\"MyListing.Handlers.Bookmark_Button(event, this)\">\n    </a>\n    Bookmark\n</li>                                                                    <li\n>\n    <a aria-label=\"Compare button\" href=\"#\" onclick=\"MyListing.Handlers.Compare_Button(event, this)\">\n    </a>\n    Add to comparison\n</li>                                        </ul>\n</section>', 'Machinezo Plant Booking Platform', '', 'inherit', 'closed', 'closed', '', '19-revision-v1', '', '', '2025-06-23 10:02:32', '2025-06-23 10:02:32', '', 19, 'https://machinezo.co.uk/?p=65', 0, 'revision', '', 0),
(66, 1, '2025-06-23 12:00:44', '0000-00-00 00:00:00', '', '', '', 'draft', 'open', 'closed', '', '', '', '', '2025-06-23 12:00:44', '2025-06-23 12:00:44', '', 0, 'https://machinezo.co.uk/?post_type=job_listing&#038;p=66', 0, 'job_listing', '', 0),
(67, 1, '2025-06-23 12:05:41', '0000-00-00 00:00:00', '', '', '', 'draft', 'open', 'closed', '', '', '', '', '2025-06-23 12:05:41', '2025-06-23 12:05:41', '', 0, 'https://machinezo.co.uk/?post_type=job_listing&#038;p=67', 0, 'job_listing', '', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `SWX7neDE_posts`
--
ALTER TABLE `SWX7neDE_posts`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `post_name` (`post_name`(191)),
  ADD KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  ADD KEY `post_parent` (`post_parent`),
  ADD KEY `post_author` (`post_author`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `SWX7neDE_posts`
--
ALTER TABLE `SWX7neDE_posts`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
