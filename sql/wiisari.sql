-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 07, 2019 at 12:09 PM
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
  `userID` varchar(50) COLLATE utf8_bin NOT NULL,
  `barcode` varchar(75) COLLATE utf8_bin DEFAULT NULL,
  `displayname` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `officeID` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `groupID` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `level` tinyint(1) NOT NULL DEFAULT '0',
  `adminPassword` varchar(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `inoutStatus` tinytext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`userID`, `barcode`, `displayname`, `officeID`, `groupID`, `level`, `adminPassword`, `inoutStatus`) VALUES
('admin', '0000', 'Administrator', 'ToimistÃ¶', 'RyhmÃ¤', 3, 'xyw1.V0rbu5mQ', ''),
('testaaja', '1234', 'Testi KÃ¤yttÃ¤jÃ¤', 'ToimistÃ¶', 'RyhmÃ¤4', 0, 'xyw1.V0rbu5mQ', 'out'),
('valvoja', '12345', 'Testi Valvoja', 'ToimistÃ¶', 'RyhmÃ¤', 0, 'xyGiEmpR0l/hg', ''),
('pitkÃ¤user', 'SYINXBSC7B', 'PitkÃ¤etunimi PitkÃ¤sukunimi', 'ToimistÃ¶', 'RyhmÃ¤', 0, 'xyw1.V0rbu5mQ', ''),
('abcde123456', 'ABCDEFGH', 'abcde', 'ToimistÃ¶', 'RyhmÃ¤', 0, 'xyw1.V0rbu5mQ', 'out'),
('qwerty', 'QWERTY', 'qwert yuiop', 'ToimistÃ¶', 'RyhmÃ¤3', 0, '', 'out');

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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`groupid`, `groupname`, `officeid`) VALUES
(3, 'RyhmÃ¤', 1),
(4, 'RyhmÃ¤2', 1),
(5, 'RyhmÃ¤3', 1),
(6, 'RyhmÃ¤4', 1),
(7, 'RyhmÃ¤5', 3);

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
) ENGINE=MyISAM AUTO_INCREMENT=206 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `info`
--

INSERT INTO `info` (`newid`, `fullname`, `inout`, `timestamp`, `notes`, `ipaddress`, `punchoffice`) VALUES
(1, 'testaaja', 'in', 1548355369, 'ensimmã¤inen sisã¤ã¤ntulo', '', ''),
(2, 'testaaja', 'out', 1548355638, 'UlÃ¶s', '', ''),
(3, 'testaaja', 'in', 1548356052, 'viestiviesti', '', ''),
(4, 'testaaja', 'out', 1548357021, '', '', ''),
(5, 'testaaja', 'in', 1548357423, 'Ã¤Ã¤Ã¤Ã¤Ã¤Ã¤Ã¶Ã¶Ã¶Ã¶Ã¶Ã¶Ã¶Ã¥Ã¥Ã¥Ã¥Ã¥Ã¥Ã¥', '', ''),
(6, 'testaaja', 'out', 1548357470, '', '', ''),
(7, 'testaaja', 'in', 1548411745, 'testiviesti', '', ''),
(8, 'testaaja', 'out', 1548412220, '', '', ''),
(9, 'testaaja', 'in', 1548412270, 'sisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤nsisÃ¤Ã¤n', '', ''),
(10, 'testaaja', 'out', 1548431435, 'pÃ¶Ã¶', '', ''),
(11, 'testaaja', 'in', 1548431729, 'viesti', '', ''),
(12, 'testaaja', 'out', 1548436021, '', '', ''),
(13, 'testaaja', 'in', 1548448522, '', '', ''),
(14, 'koekani', 'in', 1548448941, '', '', ''),
(15, 'testaaja', 'out', 1548449093, '', '', ''),
(16, 'koekani', 'out', 1548449103, '', '', ''),
(17, 'testaaja', 'in', 1548449110, '', '', ''),
(18, 'testaaja', 'out', 1548449126, 'ulos', '', ''),
(19, 'testaaja', 'in', 1548520772, '', '', ''),
(20, 'testaaja', 'out', 1548520785, '', '', ''),
(21, 'testaaja', 'in', 1548520912, '', '', ''),
(22, 'testaaja', 'out', 1548527076, '', '', ''),
(23, 'testaaja', 'in', 1548528636, '', '', ''),
(24, 'testaaja', 'out', 1548528842, '', '', ''),
(25, 'testaaja', 'in', 1548528861, '', '', ''),
(26, 'testaaja', 'out', 1548528888, '', '', ''),
(27, 'testaaja', 'in', 1548528897, '', '', ''),
(28, 'testaaja', 'out', 1548529126, '', '', ''),
(29, 'testaaja', 'in', 1548529467, '', '', ''),
(30, 'testaaja', 'out', 1548529573, '', '', ''),
(31, 'testaaja', 'in', 1548530099, '', '', ''),
(32, 'testaaja', 'out', 1548530418, '', '', ''),
(33, 'testaaja', 'in', 1548530514, '', '', ''),
(34, 'testaaja', 'out', 1548530528, '', '', ''),
(35, 'testaaja', 'in', 1548751318, '', '', ''),
(36, 'testaaja', 'out', 1548751460, '', '', ''),
(37, 'testaaja', 'in', 1548876437, '', '', ''),
(38, 'testaaja', 'out', 1548876497, 'ulos', '', ''),
(39, 'testaaja', 'in', 1548960237, '', '', ''),
(40, 'testaaja', 'out', 1548960322, '', '', ''),
(41, 'testaaja', 'in', 1548960381, '', '', ''),
(42, 'testaaja', 'out', 1548960420, '', '', ''),
(43, 'testaaja', 'in', 1548960599, '', '', ''),
(44, 'testaaja', 'out', 1548962903, '', '', ''),
(45, 'testaaja', 'in', 1548965288, '', '', ''),
(46, 'testaaja', 'out', 1548965293, '', '', ''),
(47, 'testaaja', 'in', 1548965349, '', '', ''),
(48, 'testaaja', 'out', 1548965403, '', '', ''),
(49, 'testaaja', 'in', 1548965410, '', '', ''),
(50, 'testaaja', 'out', 1548965415, '', '', ''),
(51, 'testaaja', 'in', 1548969894, '', '', ''),
(52, 'testaaja', 'out', 1548969904, '', '', ''),
(53, 'testaaja', 'in', 1549012259, '', '', ''),
(54, 'testaaja', 'out', 1549012458, '', '', ''),
(55, 'testaaja', 'in', 1549012498, '', '', ''),
(56, 'testaaja', 'out', 1549016402, '', '', ''),
(57, 'testaaja', 'in', 1549016409, '', '', ''),
(58, 'testaaja', 'out', 1549022460, '', '', ''),
(59, 'testaaja', 'in', 1549025410, '', '', ''),
(60, 'testaaja', 'out', 1549025487, '', '', ''),
(61, 'testaaja', 'in', 1549029815, '', '', ''),
(62, 'testaaja', 'out', 1549029835, 'omasivutesti', '', ''),
(63, 'testaaja', 'in', 1549042963, 'testitesti', '', ''),
(64, 'testaaja', 'out', 1549043037, '', '', ''),
(65, 'testaaja', 'in', 1549043062, '', '', ''),
(66, 'testaaja', 'out', 1549043136, '', '', ''),
(67, 'testaaja', 'in', 1549051644, '', '', ''),
(68, 'testaaja', 'out', 1549051729, '', '', ''),
(69, 'testaaja', 'in', 1549056635, '', '', ''),
(70, 'testaaja', 'out', 1549056786, '', '', ''),
(71, 'testaaja', 'in', 1549057093, '', '', ''),
(73, 'testaaja', 'out', 1549058340, 'korjaus', '', ''),
(74, 'testaaja', 'in', 1549058893, '', '', ''),
(75, 'testaaja', 'out', 1549119793, '', '', ''),
(76, 'testaaja', 'in', 1547362800, '', '', ''),
(77, 'testaaja', 'out', 1547380800, '', '', ''),
(78, 'testaaja', 'in', 1547449200, '', '', ''),
(79, 'testaaja', 'out', 1547467200, '', '', ''),
(80, 'testaaja', 'in', 1547276400, '', '', ''),
(81, 'testaaja', 'out', 1547303400, '', '', ''),
(82, 'testaaja', 'in', 1549122129, '', '', ''),
(83, 'testaaja', 'out', 1549122329, '', '', ''),
(84, 'testaaja', 'in', 1549140467, '', '', ''),
(85, 'testaaja', 'out', 1549141226, '', '', ''),
(86, 'testaaja', 'in', 1549141588, '', '', ''),
(87, 'testaaja', 'out', 1549141772, '', '', ''),
(88, 'testaaja', 'in', 1549142695, '', '', ''),
(89, 'testaaja', 'out', 1549143418, '', '', ''),
(90, 'testaaja', 'in', 1549188246, '', '', ''),
(91, 'testaaja', 'out', 1549188328, '', '', ''),
(92, 'testaaja', 'in', 1549445851, '', '', ''),
(93, 'testaaja', 'out', 1549655842, '', '', ''),
(94, 'testaaja', 'out', 1549468800, 'unohtunut kirjaus', '', ''),
(95, 'testaaja', 'in', 1549627200, '', '', ''),
(96, 'testaaja', 'in', 1549656155, '', '', ''),
(97, 'koekani', 'in', 1549656452, '', '', ''),
(98, 'testaaja', 'out', 1549656686, '', '', ''),
(99, 'koekani', 'out', 1549656732, '', '', ''),
(100, 'testaaja', 'in', 1549709154, '', '', ''),
(101, 'testaaja', 'out', 1549710527, '', '', ''),
(184, 'testaaja', 'in', 1550998800, '', '', ''),
(172, 'testaaja', 'in', 1550570400, '', '', ''),
(104, 'testaaja', 'in', 1550310493, '', '', ''),
(105, 'testaaja', 'out', 1550350662, '', '', ''),
(158, 'testaaja', 'in', 1550865600, '', '', ''),
(157, 'testaaja', 'out', 1550818800, '', '', ''),
(156, 'testaaja', 'in', 1550815200, '', '', ''),
(155, 'koekani', 'in', 1550818800, '', '', ''),
(153, 'testaaja', 'out', 1550948400, '', '', ''),
(173, 'testaaja', 'in', 1550656800, '', '', ''),
(150, 'testaaja', 'out', 1550577600, '', '', ''),
(154, 'koekani', 'out', 1550829600, '', '', ''),
(145, 'testaaja', 'in', 1550916000, '', '', ''),
(159, 'testaaja', 'out', 1550869200, '', '', ''),
(166, 'testaaja', 'out', 1550484000, '', '', ''),
(178, 'testaaja', 'in', 1550952000, '', '', ''),
(174, 'testaaja', 'out', 1550664000, '', '', ''),
(185, 'testaaja', 'out', 1550999800, '', '', ''),
(175, 'testaaja', 'in', 1550473200, '', '', ''),
(179, 'testaaja', 'out', 1550953809, '', '', ''),
(186, 'testaaja', 'in', 1551008097, '', '', ''),
(190, 'testaaja', 'out', 1551024000, '', '', ''),
(192, 'testaaja', 'in', 1551299045, 'test', '', ''),
(193, 'testaaja', 'out', 1551304740, '', '', ''),
(194, 'testaaja', 'in', 1551333600, '', '', ''),
(195, 'testaaja', 'out', 1551345813, 'testiviesti', '', ''),
(197, 'testaaja', 'in', 1551345900, '', '', ''),
(199, 'testaaja', 'out', 1551380400, '', '', ''),
(200, 'testaaja', 'in', 1559546655, '', '', ''),
(201, 'testaaja', 'out', 1559550220, '', '', ''),
(202, 'testaaja', 'in', 1559630626, '', '', ''),
(203, 'testaaja', 'out', 1559630633, '', '', ''),
(204, 'testaaja', 'in', 1559804400, '', '', ''),
(205, 'testaaja', 'out', 1559817854, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

DROP TABLE IF EXISTS `offices`;
CREATE TABLE IF NOT EXISTS `offices` (
  `officeid` int(10) NOT NULL AUTO_INCREMENT,
  `officename` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`officeid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`officeid`, `officename`) VALUES
(1, 'ToimistÃ¶'),
(3, 'Toimisto2');

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
  `fullname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `groupid` int(10) NOT NULL,
  PRIMARY KEY (`fullname`,`groupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `supervises`
--

INSERT INTO `supervises` (`fullname`, `groupid`) VALUES
('abcde123456', 6),
('abcde123456', 7),
('admin', 3),
('admin', 4),
('admin', 5),
('admin', 6),
('admin', 7);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
