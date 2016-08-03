-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2016 at 09:20 PM
-- Server version: 5.7.9
-- PHP Version: 5.6.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fluxster_clone`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(24) COLLATE utf8_romanian_ci NOT NULL,
  `password` varchar(256) COLLATE utf8_romanian_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_romanian_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99');

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
CREATE TABLE IF NOT EXISTS `badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `badge` varchar(32) CHARACTER SET latin1 NOT NULL,
  `description` varchar(128) CHARACTER SET latin1 NOT NULL,
  `img` varchar(64) CHARACTER SET latin1 NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bundles`
--

DROP TABLE IF EXISTS `bundles`;
CREATE TABLE IF NOT EXISTS `bundles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(16) CHARACTER SET latin1 NOT NULL,
  `name` varchar(32) CHARACTER SET latin1 NOT NULL,
  `date` date NOT NULL,
  `description` varchar(260) CHARACTER SET latin1 NOT NULL,
  `image` varchar(128) CHARACTER SET latin1 NOT NULL,
  `background` varchar(256) CHARACTER SET latin1 NOT NULL,
  `promotion` int(255) NOT NULL,
  `private` int(11) NOT NULL,
  `catagory` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `mentions` varchar(256) CHARACTER SET latin1 NOT NULL,
  `tag` varchar(256) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `to` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `uid` int(32) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `mentions` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `tag` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `media` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `video` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `soundcloud` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `repliedTo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `private`
--

DROP TABLE IF EXISTS `private`;
CREATE TABLE IF NOT EXISTS `private` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `read` int(11) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `relations`
--

DROP TABLE IF EXISTS `relations`;
CREATE TABLE IF NOT EXISTS `relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leader` int(11) NOT NULL,
  `follower` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `relations`
--

INSERT INTO `relations` (`id`, `leader`, `follower`) VALUES
(2, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `idm` int(32) NOT NULL,
  `user` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `title` varchar(64) NOT NULL,
  `perpage` int(11) NOT NULL,
  `ad1` varchar(2048) NOT NULL,
  `ad2` varchar(2048) NOT NULL,
  `censor` varchar(2048) CHARACTER SET utf8 NOT NULL,
  `captcha` int(11) NOT NULL,
  `public` varchar(128) NOT NULL,
  `private` varchar(128) NOT NULL,
  `inter` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `message` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `format` varchar(256) NOT NULL,
  `mail` int(11) NOT NULL,
  `sizemsg` int(11) NOT NULL,
  `formatmsg` varchar(256) NOT NULL,
  `sizebanner` int(11) NOT NULL,
  `formatbanner` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`title`, `perpage`, `ad1`, `ad2`, `censor`, `captcha`, `public`, `private`, `inter`, `time`, `message`, `size`, `format`, `mail`, `sizemsg`, `formatmsg`, `sizebanner`, `formatbanner`) VALUES
('Fluxster', 10, '<!--[if IE]>\r\n<div class="notification-box notification-box-disabled"> \r\n		<p>We see that you are using Internet Explorer. To better your experience on Fluxster, we recommend using <a href="http://www.mozilla.org/en-US/firefox/new/">Firefox</a> or <a href="https://www.google.com/intl/en/chrome/browser/">Google Chrome</a></p> \r\n</div>\r\n<![endif]--> \r\n\r\n', '', '', 0, '', '', 10000, 2, 160, 1048576, 'png,jpg,gif,bmp,PNG,JPG,jpeg', 1, 2097152, 'png,jpg,gif,bmp,PNG,JPG,jpeg', 1048576, 'png,jpg,bmp,PNG,JPG,jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `userreports`
--

DROP TABLE IF EXISTS `userreports`;
CREATE TABLE IF NOT EXISTS `userreports` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `uid` int(32) NOT NULL,
  `user` varchar(256) CHARACTER SET latin1 NOT NULL,
  `details` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `idu` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `badges` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `bio` varchar(260) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `facebook` varchar(256) DEFAULT NULL,
  `twitter` varchar(128) DEFAULT NULL,
  `youtube` varchar(256) DEFAULT NULL,
  `image` varchar(128) DEFAULT NULL,
  `private` int(11) DEFAULT NULL,
  `salted` varchar(256) DEFAULT NULL,
  `background` varchar(256) DEFAULT NULL,
  `promotion` varchar(2256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `accountype` int(11) DEFAULT NULL,
  `ws` varchar(256) DEFAULT NULL,
  `suspended` varchar(16) DEFAULT NULL,
  `verified` varchar(800) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `banner` varchar(256) DEFAULT NULL,
  `pbg` varchar(256) DEFAULT NULL,
  `betatester` varchar(16) DEFAULT NULL,
  `staff` varchar(16) DEFAULT NULL,
  `disableBadges` int(11) NOT NULL DEFAULT '0',
  `suspendDetail` text,
  `disabled` int(11) NOT NULL DEFAULT '0',
  `admin` int(11) NOT NULL DEFAULT '0',
  `hideEmail` int(11) NOT NULL DEFAULT '1',
  `searchResults` int(11) NOT NULL DEFAULT '0',
  `hideInfo` int(11) NOT NULL DEFAULT '0',
  `hidePosts` int(11) NOT NULL DEFAULT '0',
  `approveFollow` int(11) NOT NULL DEFAULT '0',
  `gender` int(11) NOT NULL DEFAULT '0',
  `fixedBG` int(11) NOT NULL DEFAULT '0',
  `repeatBG` int(11) NOT NULL DEFAULT '0',
  `key` varchar(32) DEFAULT NULL,
  `currentIP` varchar(80) DEFAULT NULL,
  `currentLogin` varchar(80) DEFAULT NULL,
  `currentOS` varchar(80) DEFAULT NULL,
  `currentBrowser` varchar(120) DEFAULT NULL,
  `lastIP` varchar(80) DEFAULT NULL,
  `lastLogin` varchar(80) DEFAULT NULL,
  `lastOS` varchar(80) DEFAULT NULL,
  `lastBrowser` varchar(120) DEFAULT NULL,
  `changedUsername` int(11) NOT NULL DEFAULT '0',
  `plusMember` int(11) NOT NULL DEFAULT '0',
  `requestedPlus` int(11) NOT NULL DEFAULT '0',
  `newsletter` int(11) NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`idu`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
