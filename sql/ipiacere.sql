-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 15, 2021 at 07:49 AM
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
('gioco1', '2021-05-19', 3, 'fd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf df', 3, 'fd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf dffd fd fdf df', '2021-05-14', 'admin', 'images/141.jpg', 'images/142.jpg', 'fdfd fdf '),
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
(151, 'images/151.jpg', '');

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
('notizia1notizia', 'admin', '2021-05-14', 'fdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdffdf dfd fdfdfdf', 'images/144.jpg', 'images/145.jpg', 'Eventi', ''),
('notizia2notizia', 'admin', '2021-05-14', 'dd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf ddd fdf dfdf d', 'images/148.jpg', 'images/149.jpg', 'Giochi', 'gioco1');

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
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
