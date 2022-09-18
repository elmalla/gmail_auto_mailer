-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2022 at 04:17 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ci_linkedemails_git`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category` text,
  `parent_num` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ci_cookies`
--

CREATE TABLE `ci_cookies` (
  `id` int(11) NOT NULL,
  `cookie_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `netid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orig_page_requested` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `php_session_id` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT '0',
  `telephone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `emails` int(5) DEFAULT NULL,
  `source_id` int(10) DEFAULT NULL,
  `activities` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `created_at` varchar(21) COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` varchar(21) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_category`
--

CREATE TABLE `company_category` (
  `id` int(10) NOT NULL,
  `category` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `source_id` int(11) NOT NULL,
  `created_at` varchar(22) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` varchar(22) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `country_codes`
--

CREATE TABLE `country_codes` (
  `id` int(255) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `country_code` varchar(10) NOT NULL
) ENGINE=MyISAM AVG_ROW_LENGTH=24 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `country_details`
--

CREATE TABLE `country_details` (
  `id` int(11) NOT NULL,
  `iso` char(2) NOT NULL,
  `name` varchar(80) NOT NULL,
  `nicename` varchar(80) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `phonecode` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `emails_master`
--

CREATE TABLE `emails_master` (
  `email_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `conflict_flag` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL DEFAULT '1',
  `company_id` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `rank` int(3) DEFAULT '0',
  `short_desc` mediumtext COLLATE utf8_unicode_ci,
  `updated_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emails_ntvalidated`
--

CREATE TABLE `emails_ntvalidated` (
  `email_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rank` int(3) DEFAULT '0',
  `created_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_id` int(11) NOT NULL DEFAULT '1',
  `source_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `short_desc` mediumtext COLLATE utf8_unicode_ci,
  `updated_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emails_txt`
--

CREATE TABLE `emails_txt` (
  `email_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `Last_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rank` int(3) DEFAULT '0',
  `insertdate` date DEFAULT NULL,
  `owner_id` int(11) NOT NULL DEFAULT '1',
  `category_id` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `short_desc` mediumtext COLLATE utf8_unicode_ci,
  `updatedate` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emails_upload_log`
--

CREATE TABLE `emails_upload_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `source_id` int(11) NOT NULL,
  `company_start_id` int(11) DEFAULT NULL,
  `company_end_id` int(11) DEFAULT NULL,
  `emailv_start_id` int(11) NOT NULL,
  `emailv_end_id` int(11) NOT NULL,
  `emailnv_start_id` int(11) NOT NULL,
  `emailnv_end_id` int(11) NOT NULL,
  `source_lines` int(7) NOT NULL,
  `company_inserts` int(7) DEFAULT NULL,
  `emailv_inserts` int(7) DEFAULT NULL,
  `emailnv_inserts` int(7) DEFAULT NULL,
  `created_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_owners`
--

CREATE TABLE `email_owners` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `Last_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT '0',
  `mobile` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_id` int(11) DEFAULT '1',
  `owner_id` int(11) DEFAULT NULL,
  `created_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_owner_titles`
--

CREATE TABLE `email_owner_titles` (
  `id` int(10) NOT NULL,
  `owner` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_source`
--

CREATE TABLE `email_source` (
  `id` int(10) NOT NULL,
  `source` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(7) NOT NULL,
  `comment` mediumtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `free_email_providers`
--

CREATE TABLE `free_email_providers` (
  `id` int(10) UNSIGNED NOT NULL,
  `company` varchar(25) CHARACTER SET latin1 DEFAULT NULL,
  `domain` varchar(25) CHARACTER SET latin1 DEFAULT NULL,
  `country` varchar(25) CHARACTER SET latin1 DEFAULT NULL,
  `created_at` varchar(21) CHARACTER SET latin1 DEFAULT NULL,
  `updated_at` varchar(21) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailer_job_scheduler`
--

CREATE TABLE `mailer_job_scheduler` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `query` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `rows_count` int(11) DEFAULT NULL,
  `status` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `created_at` varchar(21) CHARACTER SET latin1 DEFAULT NULL,
  `updated_at` varchar(21) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailer_log`
--

CREATE TABLE `mailer_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `email_id` int(7) NOT NULL,
  `country` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mailer_schedule_id` int(7) NOT NULL,
  `letter` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `attachment` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `comment` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `created_at` varchar(21) CHARACTER SET latin1 DEFAULT NULL,
  `updated_at` varchar(21) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailer_names_statistics`
--

CREATE TABLE `mailer_names_statistics` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(21) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(6) NOT NULL,
  `created_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailer_schedule_log`
--

CREATE TABLE `mailer_schedule_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `cron_scheduler_id` int(11) NOT NULL,
  `total_scheduled_emails` int(11) NOT NULL,
  `emails_sent` int(3) NOT NULL,
  `emails_nt_sent` int(3) DEFAULT NULL,
  `emails_sent_earlier` int(3) DEFAULT NULL,
  `clauses` text COLLATE utf8_unicode_ci,
  `debug_info` text COLLATE utf8_unicode_ci,
  `emails_scheduled_next_run` int(7) DEFAULT NULL,
  `smtp_host` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailing_servents`
--

CREATE TABLE `mailing_servents` (
  `id` int(11) UNSIGNED NOT NULL,
  `users_id` int(11) NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8 NOT NULL,
  `username` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `salt` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_emails`
--

CREATE TABLE `scheduled_emails` (
  `id` int(10) UNSIGNED NOT NULL,
  `email_id` int(11) NOT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `conflict_flag` int(11) NOT NULL,
  `sending_status` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `review_status` bit(1) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `created_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstName` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastName` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tagline` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teamId` int(11) DEFAULT NULL,
  `isAdmin` tinyint(1) DEFAULT NULL,
  `created_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `ci_cookies`
--
ALTER TABLE `ci_cookies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_activity_idx` (`timestamp`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `email` (`company_name`),
  ADD UNIQUE KEY `email_id` (`company_id`);

--
-- Indexes for table `company_category`
--
ALTER TABLE `company_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cateogry` (`category`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `country_codes`
--
ALTER TABLE `country_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `country_code` (`country_code`);

--
-- Indexes for table `country_details`
--
ALTER TABLE `country_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emails_master`
--
ALTER TABLE `emails_master`
  ADD PRIMARY KEY (`email_id`),
  ADD UNIQUE KEY `email_id` (`email_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `emails_ntvalidated`
--
ALTER TABLE `emails_ntvalidated`
  ADD PRIMARY KEY (`email_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_id` (`email_id`);

--
-- Indexes for table `emails_txt`
--
ALTER TABLE `emails_txt`
  ADD PRIMARY KEY (`email_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_id` (`email_id`);

--
-- Indexes for table `emails_upload_log`
--
ALTER TABLE `emails_upload_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`id`);

--
-- Indexes for table `email_owners`
--
ALTER TABLE `email_owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`id`);

--
-- Indexes for table `email_owner_titles`
--
ALTER TABLE `email_owner_titles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cateogry` (`owner`);

--
-- Indexes for table `email_source`
--
ALTER TABLE `email_source`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cateogry` (`source`),
  ADD UNIQUE KEY `source` (`source`),
  ADD UNIQUE KEY `source_2` (`source`);

--
-- Indexes for table `free_email_providers`
--
ALTER TABLE `free_email_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`id`);

--
-- Indexes for table `mailer_job_scheduler`
--
ALTER TABLE `mailer_job_scheduler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`id`);

--
-- Indexes for table `mailer_log`
--
ALTER TABLE `mailer_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`id`);

--
-- Indexes for table `mailer_names_statistics`
--
ALTER TABLE `mailer_names_statistics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`id`);

--
-- Indexes for table `mailer_schedule_log`
--
ALTER TABLE `mailer_schedule_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`id`);

--
-- Indexes for table `mailing_servents`
--
ALTER TABLE `mailing_servents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scheduled_emails`
--
ALTER TABLE `scheduled_emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_id` (`id`),
  ADD UNIQUE KEY `email_id_2` (`email_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_cookies`
--
ALTER TABLE `ci_cookies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108807;

--
-- AUTO_INCREMENT for table `company_category`
--
ALTER TABLE `company_category`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2545;

--
-- AUTO_INCREMENT for table `country_codes`
--
ALTER TABLE `country_codes`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;

--
-- AUTO_INCREMENT for table `country_details`
--
ALTER TABLE `country_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `emails_master`
--
ALTER TABLE `emails_master`
  MODIFY `email_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298420;

--
-- AUTO_INCREMENT for table `emails_ntvalidated`
--
ALTER TABLE `emails_ntvalidated`
  MODIFY `email_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51958;

--
-- AUTO_INCREMENT for table `emails_txt`
--
ALTER TABLE `emails_txt`
  MODIFY `email_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13602;

--
-- AUTO_INCREMENT for table `emails_upload_log`
--
ALTER TABLE `emails_upload_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `email_owners`
--
ALTER TABLE `email_owners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_source`
--
ALTER TABLE `email_source`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `free_email_providers`
--
ALTER TABLE `free_email_providers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mailer_job_scheduler`
--
ALTER TABLE `mailer_job_scheduler`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mailer_log`
--
ALTER TABLE `mailer_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58505;

--
-- AUTO_INCREMENT for table `mailer_names_statistics`
--
ALTER TABLE `mailer_names_statistics`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mailer_schedule_log`
--
ALTER TABLE `mailer_schedule_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1180;

--
-- AUTO_INCREMENT for table `mailing_servents`
--
ALTER TABLE `mailing_servents`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `scheduled_emails`
--
ALTER TABLE `scheduled_emails`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120562;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
