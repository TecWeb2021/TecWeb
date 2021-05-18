-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 18, 2021 at 09:19 PM
-- Server version: 10.2.11-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ipiacere`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `Id` int(11) NOT NULL,
  `Author` varchar(50) NOT NULL,
  `Game` varchar(50) NOT NULL,
  `Date_time` datetime NOT NULL,
  `Content` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`Id`, `Author`, `Game`, `Date_time`, `Content`) VALUES
(22, 'admin', 'gioco2', '2021-05-17 18:20:01', 'dfgfgfg'),
(23, 'admin', 'gioco2', '2021-05-18 20:57:23', 'fdfdf');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `Name` varchar(50) NOT NULL,
  `Publication_date` date NOT NULL,
  `Vote` float NOT NULL,
  `Sinopsis` text NOT NULL,
  `Age_range` int(11) NOT NULL,
  `Review` text DEFAULT NULL,
  `Last_review_date` date NOT NULL,
  `Review_author` varchar(50) NOT NULL,
  `Image1` varchar(50) DEFAULT NULL,
  `Image2` varchar(50) NOT NULL,
  `Developer` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`Name`, `Publication_date`, `Vote`, `Sinopsis`, `Age_range`, `Review`, `Last_review_date`, `Review_author`, `Image1`, `Image2`, `Developer`) VALUES
('gioco1', '2021-05-19', 3, 'fd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf df', 3, NULL, '2021-05-14', 'admin', 'images/141.jpg', 'images/142.jpg', 'fdfd fdf '),
('gioco2', '2019-03-05', 4, 'gfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfg', 7, 'gfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfggfg fggfgfgfg', '2021-05-14', 'admin', 'images/150.jpg', 'images/151.jpg', 'gfgfg');

-- --------------------------------------------------------

--
-- Table structure for table `games_consoles`
--

CREATE TABLE `games_consoles` (
  `Game` varchar(50) NOT NULL,
  `Console` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `games_consoles`
--

INSERT INTO `games_consoles` (`Game`, `Console`) VALUES
('gioco1', 'XboxSeriesX'),
('gioco2', 'Switch');

-- --------------------------------------------------------

--
-- Table structure for table `games_genres`
--

CREATE TABLE `games_genres` (
  `Game` varchar(50) NOT NULL,
  `Genre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `games_genres`
--

INSERT INTO `games_genres` (`Game`, `Genre`) VALUES
('gioco1', 'FPS'),
('gioco2', 'Puzzle');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `Id` int(11) NOT NULL,
  `Path` varchar(50) NOT NULL,
  `Alt` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`Id`, `Path`, `Alt`) VALUES
(140, 'images/0.jpg', 'immagine utente'),
(141, 'images/141.jpg', ''),
(142, 'images/142.jpg', ''),
(143, 'images/143.jpg', 'immagine utente'),
(144, 'images/144.jpg', ''),
(145, 'images/145.jpg', ''),
(146, 'images/146.jpg', ''),
(147, 'images/147.jpg', ''),
(148, 'images/148.jpg', ''),
(149, 'images/149.jpg', ''),
(150, 'images/150.jpg', ''),
(151, 'images/151.jpg', ''),
(152, 'images/152.jpg', ''),
(153, 'images/153.jpg', ''),
(154, 'images/154.jpg', ''),
(155, 'images/155.jpg', ''),
(156, 'images/156.jpg', ''),
(157, 'images/157.jpg', ''),
(158, 'images/158.jpg', ''),
(159, 'images/159.jpg', ''),
(160, 'images/160.jpg', ''),
(161, 'images/161.jpg', ''),
(162, 'images/162.jpg', ''),
(163, 'images/163.jpg', ''),
(164, 'images/164.jpg', ''),
(165, 'images/165.jpg', ''),
(166, 'images/166.jpg', ''),
(167, 'images/167.jpg', ''),
(168, 'images/168.jpg', ''),
(169, 'images/169.jpg', ''),
(170, 'images/170.jpg', ''),
(171, 'images/171.jpg', ''),
(172, 'images/172.jpg', ''),
(173, 'images/173.jpg', ''),
(174, 'images/174.jpg', ''),
(175, 'images/175.jpg', ''),
(176, 'images/176.jpg', 'immagine utente'),
(177, 'images/177.jpg', 'immagine utente');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `Title` varchar(200) NOT NULL,
  `User` varchar(50) NOT NULL,
  `Last_edit_date` date NOT NULL,
  `Content` varchar(10000) NOT NULL,
  `Image1` varchar(50) DEFAULT NULL,
  `Image2` varchar(50) NOT NULL,
  `Category` enum('Hardware','Eventi','Giochi') NOT NULL,
  `Game` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`Title`, `User`, `Last_edit_date`, `Content`, `Image1`, `Image2`, `Category`, `Game`) VALUES
('notizia1notizia', 'admin', '2021-05-18', 'cciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ciao ', 'images/172.jpg', 'images/173.jpg', 'Giochi', 'gioco1'),
('notizia2notizia', 'admin', '2021-05-18', 'notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz notiz ', 'images/174.jpg', 'images/175.jpg', 'Hardware', '');

-- --------------------------------------------------------

--
-- Table structure for table `prequel_sequel`
--

CREATE TABLE `prequel_sequel` (
  `Prequel` varchar(50) DEFAULT NULL,
  `Sequel` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `prequel_sequel`
--

INSERT INTO `prequel_sequel` (`Prequel`, `Sequel`) VALUES
('gioco1', 'gioco1'),
('gioco1', 'gioco2');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Username` varchar(50) NOT NULL,
  `Hash` varchar(32) NOT NULL,
  `IsAdmin` tinyint(1) NOT NULL,
  `Image` varchar(50) DEFAULT NULL,
  `Email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Username`, `Hash`, `IsAdmin`, `Image`, `Email`) VALUES
('admin', 'f6fdffe48c908deb0f4c3bd36c032e72', 1, 'images/0.jpg', 'admin@gmail.com'),
('ciaocarissimo', 'f24e711c7ca4b25b908bb1a831220218', 1, '', 'admin2@gmail.com'),
('user', '5cc32e366c87c4cb49e4309b75f57d64', 0, 'images/143.jpg', 'user@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`Name`),
  ADD KEY `chiave_esterna_3` (`Image1`);

--
-- Indexes for table `games_consoles`
--
ALTER TABLE `games_consoles`
  ADD PRIMARY KEY (`Game`,`Console`);

--
-- Indexes for table `games_genres`
--
ALTER TABLE `games_genres`
  ADD PRIMARY KEY (`Game`,`Genre`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`Title`),
  ADD KEY `chiave_esterna_4` (`Image1`);

--
-- Indexes for table `prequel_sequel`
--
ALTER TABLE `prequel_sequel`
  ADD KEY `chiave_esterna_7` (`Prequel`),
  ADD KEY `chiave_esterna_8` (`Sequel`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Username`),
  ADD KEY `chiave_esterna_5` (`Image`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
