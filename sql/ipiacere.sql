-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 21, 2021 at 01:39 PM
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
(2, 'Dark Souls 3', '2016-04-12', 4.3, 'Nel regno di Lothric il suono di una campana preavvisa che l\'Era del Fuoco sta terminando: la Prima Fiamma, ravvivata per la prima volta dal primo Signore dei Tizzoni, Lord Gwyn, è sul punto di spegnersi per sempre. In conseguenza di questo riappare la maledizione dei non morti e le ere spazio-temporali convergono tutte nello stesso punto: Lothric. L\'Era del Fuoco potrà essere prolungata con un rituale in cui grandi signori ed eroi sacrificano le loro anime per ripristinare la Prima Fiamma. Tuttavia, il principe Lothric ha abbandonato il suo dovere e sceglie invece di guardare la fiamma morire in modo da mettere fine al ciclo e lasciare che giunga finalmente l\'Era Oscura. La campana è l\'ultima speranza per l\'Era del Fuoco, che fa risorgere dalle loro tombe i Signori dei Tizzoni, eroi che si sono sacrificati per vincolare il fuoco, per tentare nuovamente di ravvivare la Prima Fiamma. Tuttavia, un solo Signore dei Tizzoni si presenta sul suo rispettivo trono. Anche il protagonista si risveglia dal suo luogo di sepoltura al suono della campana. Una volta sveglio, affronterà il Giudice Gundyr per ottenere l\'accesso all\'Altare del Vincolo, zona centrale del gioco. Una volta giunto all\'Altare, il protagonista scoprirà di essere una Fiamma Sopita, ovvero il prescelto che dovrà riportare i Signori dei Tizzoni sui loro rispettivi troni, in modo che le loro anime, messe insieme, possano fungere da combustibile per la Prima Fiamma. Una volta che la Fiamma Sopita avrà inserito la spada a spirale nel falò dell\'Altare del Vincolo potrà teletrasportarsi a Lothric, dove inizierà il suo viaggio alla ricerca dei Signori dei Tizzoni: nel Forte Farron troverà i Guardiani dell\'Abisso, guerrieri della Legione dei non morti legati da un\'unica anima attraverso il sangue di lupo; nell\'antica Anor Londo Aldrich, divenuto un Signore dei Tizzoni non per le sue virtù quanto per il suo potere e che ha previsto un\'era proveniente dal mare profondo; nella Capitale profanata il gigante Yhorm, discendente di un gigante conquistatore, e infine il principe Lothric insieme al fratello Lorian. Portate le anime dei Signori dei Tizzoni sui loro troni, la Fiamma Sopita si reca nelle rovine della Fornace della Prima Fiamma, dove incontra l\'Anima dei Tizzoni, una fusione delle anime di tutti i Signori dei Tizzoni che si sono vincolati alla Prima Fiamma.', 16, NULL, 'images\\darksouls3.png');

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
('images\\darksouls3.png', 'Il protagonista di DarkSouls 3 che stringe un pugno di terra.'),
('images\\doom.png', 'Combattente che si difende da demoni');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `Id` int(11) NOT NULL,
  `Title` varchar(200) NOT NULL,
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
(2, 'DOOM Eternal, la recensione per Nintendo Switch: una conversione da applausi', 1, '2021-01-08 14:49:02', 'La recensione di DOOM Eternal per Nintendo Switch ci ha dato modo di sperimentare nuovamente l\'incredibile fascino della formula sparatutto messa a punto da id Software, che con questo nuovo episodio sembra aver raggiunto la piena maturità in un\'orgia di scontri a fuoco, esplosioni e scatti veloci. Si tratta tuttavia anche di una maniera per curiosare fra i risvolti tecnici di una conversione tanto ambiziosa, resa ancora una volta possibile grazie al ', 'images\\doom.png', 'Giochi');

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
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
