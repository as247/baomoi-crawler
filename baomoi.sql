-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2014 at 12:18 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `crawls_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `baomoi`
--

CREATE TABLE IF NOT EXISTS `baomoi` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(2000) NOT NULL,
  `url` varchar(2000) NOT NULL,
  `description` text NOT NULL,
  `content` longtext NOT NULL,
  `time` datetime NOT NULL,
  `original_id` bigint(20) unsigned NOT NULL,
  `tags` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `original_id` (`original_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=95326 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
