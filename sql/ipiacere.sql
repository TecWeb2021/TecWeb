-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 20, 2021 at 08:54 PM
-- Server version: 10.2.36-MariaDB
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
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `Id` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Publication_date` date NOT NULL,
  `Vote` float NOT NULL,
  `Sinopsis` varchar(10000) NOT NULL,
  `Age_range` int(11) NOT NULL,
  `Review` int(11) DEFAULT NULL,
  `Image` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`Id`, `Name`, `Publication_date`, `Vote`, `Sinopsis`, `Age_range`, `Review`, `Image`) VALUES
(1, 'Game1', '2021-01-05', 3.5555, 'Sinossi di Game1', 19, NULL, 'images\\darksouls3.png');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `Path` varchar(50) NOT NULL,
  `Alt` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`Path`, `Alt`) VALUES
('images\\darksouls3.png', 'Il protagonista di DarkSouls 3 che stringe un pugno di terra.');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `Id` int(11) NOT NULL,
  `Title` varchar(50) NOT NULL,
  `User` int(11) NOT NULL,
  `Last_edit_date` datetime NOT NULL,
  `Content` varchar(10000) NOT NULL,
  `Image` varchar(50) NOT NULL,
  `Category` enum('Hardware','Evento','Giochi') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`Id`, `Title`, `User`, `Last_edit_date`, `Content`, `Image`, `Category`) VALUES
(1, 'Prima notizia', 0, '2021-01-05 21:55:24', 'Non so cosa dire  ma qulcosa c\'è di sicuro.', 'images\\darksouls3.png', 'Hardware');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `Id` int(11) NOT NULL,
  `Content` varchar(10000) NOT NULL,
  `Author` varchar(50) NOT NULL,
  `Last_edit_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Username` varchar(20) NOT NULL,
  `Hash` varchar(32) NOT NULL,
  `IsAdmin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Username`, `Hash`, `IsAdmin`) VALUES
('Ivan', '69ca25354d2f267b909611ad61924869', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `game_to_review` (`Review`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`Path`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `game_to_review` FOREIGN KEY (`Review`) REFERENCES `reviews` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
