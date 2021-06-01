-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 01, 2021 alle 12:42
-- Versione del server: 10.4.19-MariaDB
-- Versione PHP: 8.0.6

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
-- Struttura della tabella `comments`
--

CREATE TABLE `comments` (
  `Id` int(11) NOT NULL,
  `Author` varchar(50) NOT NULL,
  `Game` varchar(50) NOT NULL,
  `Date_time` datetime NOT NULL,
  `Content` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `comments`
--

INSERT INTO `comments` (`Id`, `Author`, `Game`, `Date_time`, `Content`) VALUES
(1, 'admin', 'Final Fantasy VII Remake', '2021-06-01 03:18:23', 'ciao ciao');

-- --------------------------------------------------------

--
-- Struttura della tabella `games`
--

CREATE TABLE `games` (
  `Name` varchar(50) NOT NULL,
  `Publication_date` date NOT NULL,
  `Vote` float NOT NULL,
  `Sinopsis` text NOT NULL,
  `Age_range` enum('3','7','12','16','18') NOT NULL,
  `Image1` varchar(50) DEFAULT NULL,
  `Image2` varchar(50) NOT NULL,
  `Developer` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `games`
--

INSERT INTO `games` (`Name`, `Publication_date`, `Vote`, `Sinopsis`, `Age_range`, `Image1`, `Image2`, `Developer`) VALUES
('DOOM Eternal', '2020-03-20', 3, 'Sono passati 8 mesi dagli eventi del capitolo precedente. Le forze dell&#39;Inferno hanno iniziato a invadere la Terra, sterminandone il 60% della popolazione anche grazie alla corrotta Union Aerospace Corporation, controllata da adoratori dei demoni che hanno sempre cercato durante la loro campagna di screditare e sminuire il Doom Slayer, poich molta gente, vedendo la sua forza impareggiabile nell&#39;abbattere orde intere di demoni quando inizi l&#39;invasione sulla Terra, ha iniziato ad adorarlo, soprannominandolo il Distruttore. Solo il Dottor Hayden, che pure aveva tradito lo Slayer alla fine del capitolo precedente, si  opposto allorda di demoni, fondando lARC (la resistenza umana). La missione del protagonista  quella di eliminare i tre Sacerdoti Infernali messi a capo dei demoni dalla Khan Maykr, un essere angelico il cui scopo  quello di sacrificare la Terra e gli esseri umani al fine di far prosperare il suo mondo natio, Urdak. Dopo il trailer iniziale, in cui si vede lo Slayer imbracciare il fucile allinterno di una fortezza orbitante mentre ascolta le richieste daiuto dei sopravvissuti umani, il gioco inizia allinterno di una prigione demoniaca, dove lo Slayer trova ed uccide il primo Sacerdote Deag Nilox a New York.', '12', 'images/13.jpg', 'images/14.jpg', 'id Software'),
('Final Fantasy VII Remake', '2020-04-10', 5, 'Cloud Strife, un mercenario ed ex membro dei SOLDIER (un gruppo di soldati d&#39;lite al servizio della Shinra), si unisce a un gruppo ecoterrorista chiamato Avalanche, guidato da Barret Wallace e di cui fanno parte anche Jessie, Wedge e Biggs, per assaltare il reattore Mako 1 di Midgar. Il gruppo tenta di far saltare in aria il reattore tramite una bomba di Jessie, che per risulta insufficiente;  il presidente Shinra, a quel punto, a ordinare segretamente di far esplodere il reattore, causando danni collaterali ingenti alla citt e alla popolazione, cos da accusare pubblicamente Avalanche di essere un violento gruppo terrorista. Cloud e gli altri fanno ritorno al quartier generale nei bassifondi del settore 7; lungo la strada, Cloud incontra Aerith Gainsborough, una fioraia che viene assalita da delle entit misteriose, ed ha una visione di Sephiroth, un SOLDIER leggendario con un misterioso legame col suo passato. D&#39;ora in poi le allucinazioni di Cloud saranno sempre pi frequenti, insieme a confusi ricordi del suo passato e a visioni di eventi di cui non capisce ancora il significato.', '16', 'images/9.jpg', 'images/10.jpg', 'Square Enix'),
('God of War', '2018-04-20', 5, 'Sono passati anni da quando Kratos si  vendicato degli di dell&#39;Olimpo uccidendoli uno dopo l&#39;altro e portando la Grecia alla rovina. Lo spartano, sopravvissuto allo scontro finale con il loro sovrano nonch suo padre Zeus, ha viaggiato fino all&#39;estremo nord raggiungendo la terra delle divinit norrene rifacendosi una nuova vita.\r\n\r\nLa storia si apre con Kratos che abbatte un albero da usare come pira per cremare il corpo di Faye, la sua defunta moglie, insieme ad Atreus, loro figlio, che avverte qualcosa di strano nell&#39;aria. Tornati all&#39;abitazione, i due completano gli ultimi rituali prima di cremare il corpo della donna e, una volta dato fuoco alla pira, rammentano come l&#39;ultimo desiderio di Faye fosse di spargere le sue ceneri sul monte pi alto dei Nove regni. Prima di iniziare il loro viaggio, Kratos insegna l&#39;arte della sopravvivenza ad Atreus durante una battuta di caccia al cervo, scontrandosi con dei draugr e un troll; il bambino rimane sconvolto dall&#39;improvvisa comparsa dei mostri, affermando che prima non si erano mai visti cos vicino alla loro casa.\r\n\r\nTornati a casa, Kratos raccoglie le ceneri della moglie in una sacca quando un uomo bussa alla porta: dopo aver ordinato al figlio di nascondersi, lo spartano lo affronta e lo sconosciuto afferma di sapere chi  e da dove viene. Dopo il tentativo fallito di Kratos di cacciarlo con la forza, lo sconosciuto mostra di possedere poteri divini ma, in seguito a un violento scontro, Kratos riesce a spezzare il collo dell&#39;avversario, dopo di che Padre e figlio si incamminano quindi verso la loro destinazione', '18', 'images/3.jpg', 'images/4.jpg', 'SIE Santa Monica Studio'),
('Horizon Zero Dawn 2 Forbidden West', '2021-12-31', 5, 'Horizon : Forbidden West si svolge sei mesi dopo gli eventi del primo titolo e continua la storia di Aloy, una cacciatrice di macchine della trib dei Nora, inviata alla ricerca di una frontiera misteriosa che attraversa l&#39;estremo occidente dello Utah fino alla West Coast, per trovare la fonte di una peste misteriosa che uccide tutto ci che infetta. Durante il suo viaggio per le terre inesplorate dell&#39;Ovest Proibito, la protagonista incontrer regioni ostili con minacce naturali come le tempeste devastanti, nemici pericolosi e macchine mortali, sia nuove che vecchie. Mentre Aloy tenta di esplorare le parti pi profonde e ampie di questa nuova regione ignota , scoprir l&#39;esistenza di una vasta gamma di ecosistemi ambientali differenti, tra cui valli rigogliose, deserti aridi, montagne innevate, spiagge tropicali e citt desolate, sia sopra che sotto il livello del mare.', '16', 'images/27.jpg', 'images/28.jpg', 'Guerrilla Games'),
('It Takes Two', '2021-03-26', 2, 'Cody e May, una coppia sposata, decidono di divorziare in seguito a una discussione tra i due, nella quale Cody incolpa May di lavorare tutto il tempo, mentre May ribatte affermando che Cody non mostra il bench minimo apprezzamento per il suo lavoro. Dopo aver detto alla figlia Rose del divorzio, la bambina porta due bambole fatte a mano, con la forma dei genitori, nel capanno di famiglia. Si rivolge poi a un libro di terapia relazionale, intitolato &#34;Book of Love&#34; e scritto dal Dr. Hakim, chiedendogli una mano per risolvere la situazione. Inizia a giocare poi con le bambole, inscenando una riappacificazione tra i due genitori, e distrutta dalla situazione inizia a piangere. Subito dopo, i genitori si ritrovano intrappolati nei corpi delle bambole, a causa di una lacrima di Rose caduta su di esse. Il libro, che si fa chiamare semplicemente Dr. Hakim, dice a May e Cody che gli  stato affidato il compito di cercare di sistemare la loro relazione, mentre che i due, ormai bambole, cercano di raggiungere Rose per ritornare com&#39;erano prima.', '12', 'images/15.jpg', 'images/16.jpg', 'Hazelight Studios'),
('Little Nightmares', '2017-04-28', 4, 'La protagonista del gioco  una bambina di nove anni di nome Six. Tutto inizia con la bambina che sogna una donna in kimono, subito dopo si sveglia ritrovandosi dentro una valigia in una enorme e surreale nave resort subacquea chiamata Le Fauci (The Maw nell&#39;originale). Six intraprende un viaggio attraverso il bizzarro e imprevedibile mondo de Le Fauci. La sua fuga tuttavia si dimostra molto difficile e ci la prova molto, sia fisicamente sia psicologicamente, visto che Six girovagando nei meandri delle Fauci, sar costretta a nutrirsi di cibo di scarto e di topi per poter sopravvivere. Ogni volta che mangia, un&#39;ombra dalle sue fattezze compare facendosi sempre pi nitida. A complicare le cose, Six se la deve vedere anche con creature come il Custode (un essere mostruoso che controlla i bambini rapiti dalle loro case e tenuti prigionieri per poi essere serviti come cibo prelibato ai clienti, che pagano per questa esperienza, e all&#39;occorrenza li cattura se provano a scappare) nella sua tana, e i Cuochi Gemelli nella cucina (che vorrebbero aggiungere Six come portata nel loro men o ucciderla).', '16', 'images/5.jpg', 'images/6.jpg', 'Tarsier Studios'),
('Little Nightmares 2', '2021-02-11', 4, 'Mono, un ragazzo che indossa un sacchetto di carta sulla testa, si risveglia dopo aver sognato un lungo corridoio al termine del quale si trovava una porta con su di essa un occhio. Viaggia quindi attraverso le Terre Selvagge ed entra in un vecchio capanno decrepito. Una volta entrato, libera Six, tenuta prigioniera dal Cacciatore mascherato che abita l. Quest&#39;ultimo insegue i due fino a metterli all&#39;angolo in una piccola cabina, dove riescono a staccare un fucile dal muro e a usarlo per sparargli, uccidendolo. Usando una porta di legno come zattera, giungono quindi nella Citt Pallida, avvolta da nebbia e pioggia e cosparsa di vecchi televisori. Nel corso del viaggio, Mono tenta pi volte di usare questi televisori come portali per accedere al corridoio che aveva visto in sogno, venendo per sempre tirato fuori di forza da Six prima di poter raggiungere la porta. Si imbatte anche in numerosi resti spettrali di bambini, che pu assorbire toccandoli.', '16', 'images/7.jpg', 'images/8.jpg', 'Tarsier Studios'),
('Monster Hunter Rise', '2021-03-26', 3, 'Nel villaggio di Kamura, il protagonista, un novizio cacciatore, scopre di essere stato appena ufficialmente nominato cacciatore dalla Gilda grazie alle due gemelle wyverniane Hinoa e Minoto. Le due lo conducono dal capo del villaggio, Fugen l&#39;anziano, con il protagonista che scorge per un misterioso mostro volare in lontananza. Fugen si congratula con lui, ma rivela di aver ricevuto notizia dell&#39;avvicinarsi della Furia, una misteriosa calamit che ebbe luogo cinquant&#39;anni fa durante la quale orde di mostri attaccano il villaggio fuori controllo. L&#39;anziano incarica quindi il giovane cacciatore di prepararsi all&#39;arrivo della Furia completando diverse missioni per aiutare il villaggio. Con la calamit ormai alle porte, al protagonista viene ordinato di dirigersi alla Fortezza rossa, una roccaforte costruita appositamente per tenere i mostri fuori dal villaggio; l, riesce a respingere due orde. Scampato il pericolo, tuttavia, il protagonista, la chef Yomogi e l&#39;assistente dei compagni Iori vengono attaccati da un misterioso mostro simile a una tigre che oltrepassa la barricata saltando; riescono a salvarsi per miracolo quando la creatura si distrae a causa di un mostro in fuga e si allontana. Ritornati al villaggio, scoprono che quel mostro  conosciuto come Magnamalo e appare ogni volta insieme alla Furia per nutrirsi dei mostri delle orde; cinquant&#39;anni prima, fu l&#39;unico mostro a riuscire a oltrepassare la fortezza e rase al suolo Kamura da solo, infliggendo danni devastanti. Grazie all&#39;aiuto di Fugen e del fabbro Hamon, viene fabbricato un congegno che allontana il Magnamalo dalle orde in arrivo, permettendo cos al protagonista di abbatterlo dopo uno scontro alle Rovine sacre. Ritornato al villaggio, Hinoa e Fugen si congratulano con lui, con quest&#39;ultimo che gli dona la sua spada lunga, tramandata a Kamura da generazioni.', '12', 'images/17.jpg', 'images/18.jpg', 'Capcom'),
('Persona 5 Royal', '2020-03-31', 4, 'Tentando di fermare un&#39;aggressione ai danni di una donna, il protagonista del gioco, un ragazzo sedicenne (il cui nome viene scelto dal giocatore), dopo aver ferito involontariamente l&#39;uomo colpevole, viene denunciato e arrestato dalla polizia. A causa di ci, la sua fedina penale viene macchiata e per questo viene espulso dalla sua vecchia scuola e costretto a trasferirsi a Tokyo, per frequentare la Shujin Academy, l&#39;unica scuola disposta ad accettarlo. Il giovane va dunque a vivere nel quartiere di Yongen-Jaya, al caff Leblanc gestito da Sjir Sakura, conoscente dei suoi genitori, che accetta di prenderlo sotto la sua custodia durante il suo anno di libert vigilata.\r\n\r\nDurante la sua prima notte a Tokyo, il Protagonista, dopo aver tentato di eliminare dal cellulare una strana app che aveva gi cancellato in precedenza, si risveglia allinterno della Stanza di Velluto&#34;, una misteriosa stanza sospesa tra la realt e il sogno dalle sembianze di una prigione, in cui si trovano Igor, il padrone della Stanza di Velluto, e le sue assistenti, le guardiane gemelle Justine e Caroline. Igor comunica al giocatore che lui  il Trickster, e che presto comincer la sua riabilitazione, dopodich il protagonista si sveglia.', '16', 'images/11.jpg', 'images/12.jpg', 'Atlus'),
('Persona 5 Strikers', '2021-02-23', 4, 'Quattro mesi dopo gli eventi di Persona 5 , il protagonista e Morgana tornano a Tokyo per una riunione con gli altri Phantom Thieves of Hearts per trascorrere le vacanze estive insieme in campeggio. Per determinare i preparativi per il campeggio, usano una popolare applicazione chiamata EMMA. Mentre si recano a Shibuya per acquistare gli strumenti necessari, passano accanto a un idolo emergente, Alice Hiiragi, che consegna al protagonista una carta chiedendo loro di inserire &#34;Paese delle meraviglie&#34; nell&#39;app EMMA per un evento speciale che sta organizzando. Dopo aver inserito la parola chiave, tuttavia, lui, Morgana e Ryuji vengono trasportati in una misteriosa versione alternativa di Tokyochiamato Jail, dove incontrano l&#39;Ombra di Alice, un sovrano chiamato Monarch. Il s Shadow di Alice vede il protagonista, Morgana e Ryuji gettati in un cassonetto sotto Shibuya dove incontrano un&#39;IA senziente di nome Sophia. Sophia si unisce alla festa e combatte facilmente le Ombre nel cassonetto finch non trovano la via d&#39;uscita. Dopo essere fuggiti dal cassonetto, il protagonista, Morgana, Ryuji e Sophia scoprono che EMMA consente loro di entrare nelle carceri in modo simile a come erano soliti entrare nei palazzi tramite il navigatore del metaverso. Inoltre, circolano voci secondo cui le persone sono state attaccate da Shadows in Jails, il che le fa comportare in modo anomalo nel mondo reale. In quanto tale, Joker e i suoi amici ristabiliscono i Phantom Thieves of Hearts.', '16', 'images/19.jpg', 'images/20.jpg', 'Atlus'),
('Resident Evil Village', '2021-05-07', 3, 'Ambientato tre anni dopo gli eventi di Resident Evil 7: Biohazard,[3] Ethan, Mia e la figlia Rosemary Winters si sono trasferiti in Romania dove vivono sereni dopo gli avvenimenti passati. Tuttavia, una notte, Chris Redfield e una squadra di soldati fanno irruzione nella loro casa, uccidono Mia e rapiscono Ethan e Rosemary. Ethan perde conoscenza e in seguito si sveglia accanto a un camion distrutto, insieme ai soldati che sono stati tutti uccisi. Confuso per il concatenarsi di avvenimenti tanto brutali, Ethan si imbatte in un misterioso villaggio dell&#39;Europa orientale,[4] infestato da creature mutanti e altamente aggressive simili a lupi mannari. Ethan guarda impotente gli abitanti sopravvissuti essere massacrati dai Lycan prima di essere catturato dai capi del villaggio: Madre Miranda e i suoi accoliti Alcina Dimitrescu, Donna Beneviento, Salvatore Moreau e Karl Heisenberg.', '18', 'images/23.jpg', 'images/24.jpg', 'Capcom');

-- --------------------------------------------------------

--
-- Struttura della tabella `games_consoles`
--

CREATE TABLE `games_consoles` (
  `Game` varchar(50) NOT NULL,
  `Console` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `games_consoles`
--

INSERT INTO `games_consoles` (`Game`, `Console`) VALUES
('DOOM Eternal', 'PS4'),
('DOOM Eternal', 'Switch'),
('DOOM Eternal', 'XboxOne'),
('Final Fantasy VII Remake', 'PS4'),
('Final Fantasy VII Remake', 'PS5'),
('God of War', 'PS4'),
('Horizon Zero Dawn 2 Forbidden West', 'PS4'),
('Horizon Zero Dawn 2 Forbidden West', 'PS5'),
('It Takes Two', 'PS4'),
('It Takes Two', 'PS5'),
('It Takes Two', 'XboxOne'),
('It Takes Two', 'XboxSeriesX'),
('Little Nightmares', 'PS4'),
('Little Nightmares', 'Switch'),
('Little Nightmares', 'XboxOne'),
('Little Nightmares 2', 'PS4'),
('Little Nightmares 2', 'Switch'),
('Little Nightmares 2', 'XboxOne'),
('Monster Hunter Rise', 'Switch'),
('Persona 5 Royal', 'PS4'),
('Persona 5 Strikers', 'PS4'),
('Persona 5 Strikers', 'Switch'),
('Resident Evil Village', 'PS4'),
('Resident Evil Village', 'PS5'),
('Resident Evil Village', 'XboxOne'),
('Resident Evil Village', 'XboxSeriesX');

-- --------------------------------------------------------

--
-- Struttura della tabella `games_genres`
--

CREATE TABLE `games_genres` (
  `Game` varchar(50) NOT NULL,
  `Genre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `games_genres`
--

INSERT INTO `games_genres` (`Game`, `Genre`) VALUES
('DOOM Eternal', 'FPS'),
('Final Fantasy VII Remake', 'Azione'),
('Final Fantasy VII Remake', 'GDR'),
('God of War', 'Avventura'),
('God of War', 'Azione'),
('Horizon Zero Dawn 2 Forbidden West', 'Avventura'),
('It Takes Two', 'Avventura'),
('It Takes Two', 'Puzzle'),
('Little Nightmares', 'Horror'),
('Little Nightmares', 'Puzzle'),
('Little Nightmares 2', 'Horror'),
('Little Nightmares 2', 'Puzzle'),
('Monster Hunter Rise', 'Azione'),
('Monster Hunter Rise', 'GDR'),
('Persona 5 Royal', 'GDR'),
('Persona 5 Strikers', 'Azione'),
('Persona 5 Strikers', 'GDR'),
('Resident Evil Village', 'Avventura'),
('Resident Evil Village', 'FPS'),
('Resident Evil Village', 'Horror');

-- --------------------------------------------------------

--
-- Struttura della tabella `images`
--

CREATE TABLE `images` (
  `Path` varchar(50) NOT NULL,
  `Alt` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `images`
--

INSERT INTO `images` (`Path`, `Alt`) VALUES
('images/1.png', 'immagine utente'),
('images/10.jpg', ''),
('images/11.jpg', ''),
('images/12.jpg', ''),
('images/13.jpg', ''),
('images/14.jpg', ''),
('images/15.jpg', ''),
('images/16.jpg', ''),
('images/17.jpg', ''),
('images/18.jpg', ''),
('images/19.jpg', ''),
('images/2.jpg', ''),
('images/20.jpg', ''),
('images/21.jpg', ''),
('images/22.jpg', ''),
('images/23.jpg', ''),
('images/24.jpg', ''),
('images/25.jpg', ''),
('images/26.jpg', ''),
('images/27.jpg', ''),
('images/28.jpg', ''),
('images/29.jpg', ''),
('images/3.jpg', ''),
('images/30.jpg', ''),
('images/31.jpg', ''),
('images/32.jpg', ''),
('images/33.jpg', ''),
('images/34.jpg', ''),
('images/4.jpg', ''),
('images/5.jpg', ''),
('images/6.jpg', ''),
('images/7.jpg', ''),
('images/8.jpg', ''),
('images/9.jpg', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `news`
--

CREATE TABLE `news` (
  `Title` varchar(200) NOT NULL,
  `User` varchar(50) DEFAULT NULL,
  `Last_edit_date` date NOT NULL,
  `Content` varchar(10000) NOT NULL,
  `Image1` varchar(50) DEFAULT NULL,
  `Image2` varchar(50) DEFAULT NULL,
  `Category` enum('Hardware','Eventi','Giochi') NOT NULL,
  `Game` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `news`
--

INSERT INTO `news` (`Title`, `User`, `Last_edit_date`, `Content`, `Image1`, `Image2`, `Category`, `Game`) VALUES
('Le migliori schede video del 2021', 'admin', '2021-06-01', 'Nei mesi scorsi Nvidia ha lanciato le nuove schede Ampere (serie RTX 3000), tra cui troviamo: 3060 Ti, 3070, 3080 e 3090, ma la gamma sar estesa verso il basso con nuovi diversi modelli a breve e, nel frattempo, si parla gi di una 3080 Ti con pi memoria, collocata a met tra l&#39;attuale 3080 e la top di gamma 3090.\\r\\n\\r\\nAMD invece ha presentato i modelli di schede RDNA 2 &#34;Big Navy&#34;: RX 6800, RX 6800 XT, mentre la RX 6900 XT non  ancora disponibile nei negozi.', 'images/31.jpg', 'images/32.jpg', 'Hardware', ''),
('Prossimo E3 dell&#39;anno 2021', 'admin', '2021-06-01', 'non so che mese sia ma in teoria sony non partecipa manco quest&#39;anno', 'images/33.jpg', 'images/34.jpg', 'Eventi', ''),
('State Of Play di Sony mostra il gameplay di Horizon Zero Dawn 2 Forbidden West', 'admin', '2021-06-01', 'Il gioco sembra davvero figo, molto fluido e grafica spettacolare non vedo l&#39;ora che esca', 'images/29.jpg', 'images/30.jpg', 'Giochi', 'Horizon Zero Dawn 2 Forbidden West');

-- --------------------------------------------------------

--
-- Struttura della tabella `prequel_sequel`
--

CREATE TABLE `prequel_sequel` (
  `Prequel` varchar(50) NOT NULL,
  `Sequel` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `prequel_sequel`
--

INSERT INTO `prequel_sequel` (`Prequel`, `Sequel`) VALUES
('Little Nightmares', 'Little Nightmares 2'),
('Persona 5 Royal', 'Persona 5 Strikers');

-- --------------------------------------------------------

--
-- Struttura della tabella `reviews`
--

CREATE TABLE `reviews` (
  `Game` varchar(50) NOT NULL,
  `Author` varchar(50) NOT NULL,
  `Date_time` date NOT NULL,
  `Content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `reviews`
--

INSERT INTO `reviews` (`Game`, `Author`, `Date_time`, `Content`) VALUES
('DOOM Eternal', 'admin', '2021-06-01', 'molto bello ve lo consiglio caldamente'),
('Final Fantasy VII Remake', 'admin', '2021-06-01', 'molto bello ve lo consiglio caldamente'),
('God of War', 'admin', '2021-06-01', 'molto bello ve lo consiglio vivamente\r\n'),
('Horizon Zero Dawn 2 Forbidden West', 'admin', '2021-06-01', 'gioco molto bello ma deve ancora uscire'),
('It Takes Two', 'admin', '2021-06-01', 'molto bello ve lo consiglio caldamente'),
('Little Nightmares', 'admin', '2021-06-01', 'molto bello ve lo consiglio caldamente'),
('Little Nightmares 2', 'admin', '2021-06-01', 'molto bello ve lo consiglio caldamente'),
('Monster Hunter Rise', 'admin', '2021-06-01', 'molto bello ve lo consiglio caldamente'),
('Persona 5 Royal', 'admin', '2021-06-01', 'molto bello ve lo consiglio caldamente'),
('Persona 5 Strikers', 'admin', '2021-06-01', 'molto bello ve lo consiglio caldamente'),
('Resident Evil Village', 'admin', '2021-06-01', 'molto bello ve lo consiglio caldamente');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `Username` varchar(50) NOT NULL,
  `Hash` varchar(128) NOT NULL,
  `IsAdmin` tinyint(1) NOT NULL,
  `Image` varchar(50) DEFAULT NULL,
  `Email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`Username`, `Hash`, `IsAdmin`, `Image`, `Email`) VALUES
('admin', 'c01a3f4cd31e4a8865e64c0d68598a70b1fff7d53aa93a1df8e1eb8ec0ee31daca16646cecb587f6925c0379d63caea3d48f0017472bdfc5646aa77d21c78bdc', 1, 'images/1.png', 'admin@gmail.com');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `FK_Author` (`Author`),
  ADD KEY `FK_Game` (`Game`);

--
-- Indici per le tabelle `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`Name`),
  ADD KEY `FK_Game_Image1` (`Image1`),
  ADD KEY `FK_Game_Image2` (`Image2`);

--
-- Indici per le tabelle `games_consoles`
--
ALTER TABLE `games_consoles`
  ADD PRIMARY KEY (`Game`,`Console`);

--
-- Indici per le tabelle `games_genres`
--
ALTER TABLE `games_genres`
  ADD PRIMARY KEY (`Game`,`Genre`);

--
-- Indici per le tabelle `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`Path`);

--
-- Indici per le tabelle `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`Title`),
  ADD KEY `FK_Image2` (`Image2`),
  ADD KEY `FK_News_Game` (`Game`),
  ADD KEY `FK_User` (`User`),
  ADD KEY `FK_Image1` (`Image1`);

--
-- Indici per le tabelle `prequel_sequel`
--
ALTER TABLE `prequel_sequel`
  ADD PRIMARY KEY (`Prequel`),
  ADD KEY `FK_Sequel` (`Sequel`);

--
-- Indici per le tabelle `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`Game`),
  ADD KEY `FK_Review_Author` (`Author`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Username`),
  ADD KEY `FK_Image` (`Image`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `comments`
--
ALTER TABLE `comments`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `FK_Author` FOREIGN KEY (`Author`) REFERENCES `users` (`Username`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Game` FOREIGN KEY (`Game`) REFERENCES `games` (`Name`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `FK_Game_Image1` FOREIGN KEY (`Image1`) REFERENCES `images` (`Path`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Game_Image2` FOREIGN KEY (`Image2`) REFERENCES `images` (`Path`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `games_consoles`
--
ALTER TABLE `games_consoles`
  ADD CONSTRAINT `FK_Console_Game` FOREIGN KEY (`Game`) REFERENCES `games` (`Name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `games_genres`
--
ALTER TABLE `games_genres`
  ADD CONSTRAINT `FK_Genre_Game` FOREIGN KEY (`Game`) REFERENCES `games` (`Name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `FK_Image1` FOREIGN KEY (`Image1`) REFERENCES `images` (`Path`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_User` FOREIGN KEY (`User`) REFERENCES `users` (`Username`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `prequel_sequel`
--
ALTER TABLE `prequel_sequel`
  ADD CONSTRAINT `FK_Prequel` FOREIGN KEY (`Prequel`) REFERENCES `games` (`Name`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Sequel` FOREIGN KEY (`Sequel`) REFERENCES `games` (`Name`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `FK_Review_Author` FOREIGN KEY (`Author`) REFERENCES `users` (`Username`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Review_Game` FOREIGN KEY (`Game`) REFERENCES `games` (`Name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_Image` FOREIGN KEY (`Image`) REFERENCES `images` (`Path`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
