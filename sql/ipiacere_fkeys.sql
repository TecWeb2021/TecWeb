-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 27, 2021 at 10:20 PM
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
  `Age_range` enum('3','7','12','16','18') NOT NULL,
  `Review` text DEFAULT NULL,
  `Last_review_date` date NOT NULL,
  `Review_author` varchar(50) NOT NULL,
  `Image1` varchar(50) DEFAULT NULL,
  `Image2` varchar(50) NOT NULL,
  `Developer` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `games_consoles`
--

CREATE TABLE `games_consoles` (
  `Game` varchar(50) NOT NULL,
  `Console` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `games_genres`
--

CREATE TABLE `games_genres` (
  `Game` varchar(50) NOT NULL,
  `Genre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `Path` varchar(50) NOT NULL,
  `Alt` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `Title` varchar(200) NOT NULL,
  `User` varchar(50) NOT NULL,
  `Last_edit_date` date NOT NULL,
  `Content` varchar(10000) NOT NULL,
  `Image1` varchar(50) NOT NULL,
  `Image2` varchar(50) NOT NULL,
  `Category` enum('Hardware','Eventi','Giochi') NOT NULL,
  `Game` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `prequel_sequel`
--

CREATE TABLE `prequel_sequel` (
  `Prequel` varchar(50) NOT NULL,
  `Sequel` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `FK_Author` (`Author`),
  ADD KEY `FK_Game` (`Game`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`Name`),
  ADD KEY `FK_Review_author` (`Review_author`),
  ADD KEY `FK_Game_Image1` (`Image1`),
  ADD KEY `FK_Game_Image2` (`Image2`);

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
  ADD PRIMARY KEY (`Path`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`Title`),
  ADD KEY `FK_User` (`User`),
  ADD KEY `FK_Image2` (`Image2`),
  ADD KEY `FK_image1` (`Image1`),
  ADD KEY `FK_Image_Game` (`Game`);

--
-- Indexes for table `prequel_sequel`
--
ALTER TABLE `prequel_sequel`
  ADD PRIMARY KEY (`Prequel`),
  ADD KEY `FK_Sequel` (`Sequel`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Username`),
  ADD KEY `FK_Image` (`Image`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `FK_Author` FOREIGN KEY (`Author`) REFERENCES `users` (`Username`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Game` FOREIGN KEY (`Game`) REFERENCES `games` (`Name`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `FK_Game_Image1` FOREIGN KEY (`Image1`) REFERENCES `images` (`Path`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Game_Image2` FOREIGN KEY (`Image2`) REFERENCES `images` (`Path`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Review_author` FOREIGN KEY (`Review_author`) REFERENCES `users` (`Username`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `games_consoles`
--
ALTER TABLE `games_consoles`
  ADD CONSTRAINT `FK_Console_Game` FOREIGN KEY (`Game`) REFERENCES `games` (`Name`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `games_genres`
--
ALTER TABLE `games_genres`
  ADD CONSTRAINT `FK_Genre_Game` FOREIGN KEY (`Game`) REFERENCES `games` (`Name`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `FK_Image2` FOREIGN KEY (`Image2`) REFERENCES `images` (`Path`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Image_Game` FOREIGN KEY (`Game`) REFERENCES `games` (`Name`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_User` FOREIGN KEY (`User`) REFERENCES `users` (`Username`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_image1` FOREIGN KEY (`Image1`) REFERENCES `images` (`Path`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `prequel_sequel`
--
ALTER TABLE `prequel_sequel`
  ADD CONSTRAINT `FK_Prequel` FOREIGN KEY (`Prequel`) REFERENCES `games` (`Name`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Sequel` FOREIGN KEY (`Sequel`) REFERENCES `games` (`Name`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_Image` FOREIGN KEY (`Image`) REFERENCES `images` (`Path`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
