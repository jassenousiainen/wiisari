-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 16, 2018 at 07:20 PM
-- Server version: 5.7.23
-- PHP Version: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `timeclock`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit`
--

DROP TABLE IF EXISTS `audit`;
CREATE TABLE IF NOT EXISTS `audit` (
  `modified_when` bigint(14) DEFAULT NULL,
  `modified_from` bigint(14) NOT NULL,
  `modified_to` bigint(14) NOT NULL,
  `modified_by_ip` varchar(39) COLLATE utf8_bin NOT NULL DEFAULT '',
  `modified_by_user` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `modified_why` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `modified_office` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `user_modified` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  KEY `audit_modified_when` (`modified_when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `dbversion`
--

DROP TABLE IF EXISTS `dbversion`;
CREATE TABLE IF NOT EXISTS `dbversion` (
  `dbversion` decimal(5,1) NOT NULL,
  PRIMARY KEY (`dbversion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `empfullname` varchar(50) COLLATE utf8_bin NOT NULL,
  `tstamp` bigint(14) DEFAULT NULL,
  `employee_passwd` varchar(25) COLLATE utf8_bin NOT NULL DEFAULT '',
  `displayname` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
  `barcode` varchar(75) COLLATE utf8_bin DEFAULT NULL,
  `groups` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `office` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `reports` tinyint(1) NOT NULL DEFAULT '0',
  `time_admin` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`empfullname`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `groupid` int(10) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `officeid` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`groupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
CREATE TABLE IF NOT EXISTS `info` (
  `newid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `inout` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `timestamp` bigint(14) DEFAULT NULL,
  `notes` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `ipaddress` varchar(39) COLLATE utf8_bin NOT NULL DEFAULT '',
  `punchoffice` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`newid`),
  KEY `info_fullname` (`fullname`),
  KEY `info_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `metars`
--

DROP TABLE IF EXISTS `metars`;
CREATE TABLE IF NOT EXISTS `metars` (
  `station` varchar(4) COLLATE utf8_bin NOT NULL,
  `metar` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL,
  PRIMARY KEY (`station`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

DROP TABLE IF EXISTS `offices`;
CREATE TABLE IF NOT EXISTS `offices` (
  `officeid` int(10) NOT NULL AUTO_INCREMENT,
  `officename` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`officeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `punchlist`
--

DROP TABLE IF EXISTS `punchlist`;
CREATE TABLE IF NOT EXISTS `punchlist` (
  `punchitems` varchar(50) COLLATE utf8_bin NOT NULL,
  `punchnext` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `color` varchar(7) COLLATE utf8_bin NOT NULL DEFAULT '',
  `in_or_out` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`punchitems`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
