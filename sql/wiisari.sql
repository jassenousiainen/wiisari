-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 04, 2019 at 09:21 AM
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
-- Database: `wiisari`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `empfullname` varchar(50) COLLATE utf8_bin NOT NULL,
  `tstamp` bigint(14) DEFAULT NULL,
  `employee_passwd` varchar(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `displayname` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
  `barcode` varchar(75) COLLATE utf8_bin DEFAULT NULL,
  `groups` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' REFERENCES Groups(groupname),
  `office` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `reports` tinyint(1) NOT NULL DEFAULT '0',
  `time_admin` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `inout_status` tinytext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`empfullname`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`empfullname`, `tstamp`, `employee_passwd`, `displayname`, `email`, `barcode`, `groups`, `office`, `admin`, `reports`, `time_admin`, `disabled`, `inout_status`) VALUES
('admin', NULL, 'xy.RY2HT1QTc2', 'Administrator', '', NULL, '', '', 1, 1, 1, 0, ''),
('testaaja', 1559550220, 'xyw1.V0rbu5mQ', 'Testi KÃ¤yttÃ¤jÃ¤', '', '1234', 'RyhmÃ¤', 'ToimistÃ¶', 0, 0, 0, 0, 'out'),
('valvoja', NULL, 'xyGiEmpR0l/hg', 'Testi Valvoja', '', '12345', 'RyhmÃ¤', 'ToimistÃ¶', 0, 1, 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `groupid` int(10) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `officeid` int(10) NOT NULL DEFAULT '0' REFERENCES offices(officeid),
  PRIMARY KEY (`groupid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`groupid`, `groupname`, `officeid`) VALUES
(3, 'RyhmÃ¤', 1);

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

DROP TABLE IF EXISTS `offices`;
CREATE TABLE IF NOT EXISTS `offices` (
  `officeid` int(10) NOT NULL AUTO_INCREMENT,
  `officename` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`officeid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`officeid`, `officename`) VALUES
(1, 'ToimistÃ¶');

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
CREATE TABLE IF NOT EXISTS `info` (
  `newid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' REFERENCES employees(empfullname),
  `inout` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `timestamp` bigint(14) DEFAULT NULL,
  `notes` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `ipaddress` varchar(39) COLLATE utf8_bin NOT NULL DEFAULT '',
  `punchoffice` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`newid`),
  KEY `info_fullname` (`fullname`),
  KEY `info_timestamp` (`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=202 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
  `fullname` varchar(50) COLLATE utf8_unicode_ci NOT NULL REFERENCES employees(empfullname),
  `groupid` int(10) NOT NULL REFERENCES groups(groupid),
  PRIMARY KEY (`fullname`,`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `supervises`
--

INSERT INTO `supervises` (`fullname`, `groupId`) VALUES
('valvoja', 3);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
