-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 25, 2021 at 12:12 AM
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
('images/0.jpg', 'hgfhgfhgfhfgh'),
('images/0.png', 'ggggggggggggggggggg'),
('images/1.jpg', 'jgjghjghjghjghjgjghgh'),
('images/14.jpg', 'fdfdfdfdfdfdfdfdfdfdfdfffdfdff'),
('images/15.jpg', ''),
('images/16.jpg', 'fdfdfdfdfdfdfdfdffdfdfdf'),
('images/17.jpg', 'Maschera tenuta in mano'),
('images/18.jpg', 'Maschera tenuta in mano'),
('images/19.jpg', 'jjjjjjjjjjjjjjjjjjjjjjjj'),
('images/20.jpg', 'Maschera tenuta in mano'),
('images/21.jpg', 'Persone'),
('images/22.jpg', 'gggfgfgfgfgfgf'),
('images/23.png', 'ggggggggggggggggggg'),
('images/24.png', 'ggggggggggggggggggg'),
('images/25.png', 'Panorama anni 80'),
('images/26.png', 'Panorama anni 80'),
('images/27.png', 'Panorama anni 80'),
('images/8.jpg', 'fdfdfdfdfdfdfdfdfdfdfdfffdfdff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`Path`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
