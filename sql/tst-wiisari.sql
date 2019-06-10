-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 10, 2019 at 10:40 AM
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
-- Database: `tst-wiisari`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `userID` varchar(75) COLLATE utf8_bin NOT NULL,
  `displayName` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `groupID` tinyint(11) UNSIGNED NOT NULL DEFAULT '0',
  `level` tinyint(1) NOT NULL DEFAULT '0',
  `adminPassword` varchar(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `inoutStatus` tinytext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`userID`, `displayName`, `groupID`, `level`, `adminPassword`, `inoutStatus`) VALUES
('admin', 'Administrator', 1, 3, '$2y$10$xnpz7RIvj4hosPazISFSCO8TW9oXDmUwlATVHVCWwNqoNBgKF2x82', '');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `groupID` int(10) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `officeID` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`groupID`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`groupID`, `groupName`, `officeID`) VALUES
(1, 'RyhmÃ¤1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
CREATE TABLE IF NOT EXISTS `info` (
  `punchID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userID` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `inout` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `timestamp` bigint(14) DEFAULT NULL,
  `notes` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`punchID`),
  KEY `info_fullname` (`userID`),
  KEY `info_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

DROP TABLE IF EXISTS `offices`;
CREATE TABLE IF NOT EXISTS `offices` (
  `officeID` int(10) NOT NULL AUTO_INCREMENT,
  `officeName` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`officeID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`officeID`, `officeName`) VALUES
(1, 'Toimisto1');

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

--
-- Dumping data for table `punchlist`
--

INSERT INTO `punchlist` (`punchitems`, `punchnext`, `color`, `in_or_out`) VALUES
('in', '', '#009900', 1),
('out', '', '#FF0000', 0);

-- --------------------------------------------------------

--
-- Table structure for table `supervises`
--

DROP TABLE IF EXISTS `supervises`;
CREATE TABLE IF NOT EXISTS `supervises` (
  `userID` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `groupID` int(10) NOT NULL,
  PRIMARY KEY (`userID`,`groupID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
