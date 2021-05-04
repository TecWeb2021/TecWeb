<?php

require_once("./classes/news.php");
require_once("./classes/game.php");
require_once("./classes/image.php");
require_once("./classes/user.php");
require_once("./classes/comment.php");
//namespace DB;

//my db interface is on localhost:80/phpmyadmin

//pwd_db_2020-21.txt : ni4vanaogh1Hai1O
class DBAccess {
    private const HOST_DB = "localhost";
    private const USERNAME ="root";
    private const PASSWORD ="1234";
    private const DATABASE_NAME ="ipiacere";

    private $connection;

    public function openDBConnection() {
        //mysqli è un libreria
        //questa funzione restituisce la connessione oppure false
        $this->connection = mysqli_connect(DBAccess::HOST_DB, DBAccess::USERNAME, DBAccess::PASSWORD, DBAccess::DATABASE_NAME);
        if (!$this->connection) {
            return false;
        }else {
            return true;
        }
    }


    #la funzione getResult deve ricevere in input una stringa già sanificata (sanitized)
    #altrimenti la sicurezza può essere compromessa
    // questa funzione fa solo la chiamata al database e restituisce qualunque cosa riceva
    public function getResult($query, $silent = true){
        $querySelect ="$query";
        if(!$silent){
            echo "db query: ".$querySelect."<br/>";
        }
        $queryResult = mysqli_query($this->connection, $querySelect);
        return $queryResult;
        /*
        echo "query result: <br/>";
        print_r($queryResult) . "<br/>";
        if($queryResult === true){
            return $queryResult;
        }

        if($queryResult==false && !$silent){
            echo mysqli_error($this->connection)."<br/>";
            return null;
        }

        if($queryResult==null || mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $resultList = array();

            //mysqli_fetch muove l'iteratore. Ogni volta che lo eseguo va alla successiva, fino a quando arriva alla fine e restituisce null.
            //mysqli_fetch_assoc (in maniera associativa)
            while ($row = mysqli_fetch_assoc($resultList)) {
                array_push($resultList, $row);
            }
            #restituisce un array di array. Gli array contenutivi sono le righe del database.
            return $resultList;
        }*/
    }


    // se possibile, converte un mysqli_result in un array associativo
    // ogni elemento dell'array sarà una riga del database, con i nomi delle colonne preservati
    /*public function mySqliResultToArray($result){
        if($result === null || $result === true || $result === false){
            return null;
        }
        $newArr = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($newArr, $row);
        }
        return $newArr;
    }*/

    ////////////////////
    // USER
    ///////////////////

    public function getUsersList(){
        $query="SELECT * FROM users LEFT JOIN images ON users.image=images.path";
        $result = $this->getResult($query);
        if($result === null){
            return null;
        }
        if($result === true){
            return null;
        }
        if($result === false){
            return null;
        }

        $usersList=array();
        while ($row = mysqli_fetch_assoc($result)) {
            $image= $row['Image']=="" ? null : new Image($row['Path'], $row['Alt']);
            $user=new User($row['Username'], $row['Hash'], $row['IsAdmin'], $image, $row['Email']);
            array_push($usersList, $user);
        }
        return $usersList;
    }

    public function getUser($username){
        $username = mysqli_real_escape_string($this->connection, $username);
        $query="SELECT * FROM users LEFT JOIN images ON users.image=images.path WHERE Username='$username'";
        $result = $this->getResult($query);

        if($result === null){
            return null;
        } elseif ($result === true){
            return null;
        } elseif ($result === false){
            return null;
        }

        if(mysqli_num_rows($result) > 0){
            $row=mysqli_fetch_assoc($result);
            $image= $row['Image']=="" ? null : new Image($row['Path'], $row['Alt']);
            $user=new User($row['Username'], $row['Hash'], $row['IsAdmin'], $image, $row['Email']);
            return $user;
        }else{
            return null;
        }
    }

    public function getUserByHash($hashValue){
        $hashValue = mysqli_real_escape_string($this->connection, $hashValue);
        $query="SELECT * FROM users LEFT JOIN images ON users.Image=images.Path WHERE hash='$hashValue'"; // ' ' or ''=' '
        // echo "getUserByHash query: " . $query. "<br/>";
        $queryResult = $this->getResult($query);
        
        if(mysqli_num_rows($queryResult) < 1) {
            return null;
        }else {
            $row = mysqli_fetch_assoc($queryResult);
            $image=null;
            if($row['Image']!=null){
                $image=new Image($row['Path'], $row['Alt']);
            }
            $user=new User($row['Username'], $row['Hash'], $row['IsAdmin'], $image, $row['Email']);

        return $user;
        }
    }

    public function addUser($user){
        $name = $user->getUsername();
		$name = mysqli_real_escape_string($this->connection, $name);
        $hash = $user->getHash();
		$hash = mysqli_real_escape_string($this->connection, $hash);
        $isAdmin = $user->IsAdmin();
		$isAdmin = mysqli_real_escape_string($this->connection, $isAdmin);
        $image = $user->getImage();
		$image = mysqli_real_escape_string($this->connection, $image);
        $email = $user->getEmail();
		$email = mysqli_real_escape_string($this->connection, $email);

        $imagePath = "";
        $imageAlt = "";

        #gestisco image in una maniera differente rispetto agli altri input poichè può essere nulla
        $result=null;
        if($image){
            $imagePath=$image->getPath();
            $imageAlt=$image->getAlt();
            echo "imageAlt: ".$imageAlt."<br/>";
            $query="INSERT INTO images VALUES ('$imagePath','$imageAlt');";
            $result=$this->getResult($query);
            if($result==null){
                return null;
            }
        }

        $imagePath = mysqli_real_escape_string($this->connection, $imagePath);
        $imageAlt = mysqli_real_escape_string($this->connection, $imageAlt);

        $query="INSERT INTO users VALUES ('$name','$hash', $isAdmin, '$imagePath', '$email');";
        echo "query: ".$query."<br/>";
        $result=$this->getResult($query);
        return $result;
    }

    public function deleteUser($username){
        $username = mysqli_real_escape_string($this->connection, $username);
        $query="DELETE FROM users WHERE Username='$username'";
        $queryResult = mysqli_query($this->connection, $query);
        $sq=$queryResult==null? "null":"not null";
        echo "delete query result: ".$sq."<br/>"."<br/>";
        echo mysqli_error($this->connection)."<br/>";
    }

    public function overwriteUser($user){
        $username=$user->getUsername();
		$username = mysqli_real_escape_string($this->connection, $username);
        $hash=$user->getHash();
		$hash = mysqli_real_escape_string($this->connection, $hash);
        $isAdmin=$user->isAdmin();
		$isAdmin = mysqli_real_escape_string($this->connection, $isAdmin);
        $image=$user->getImage();
		$image = mysqli_real_escape_string($this->connection, $image);
        $imagePath= $image ? $image->getPath() : null;
		$imagePath = mysqli_real_escape_string($this->connection, $imagePath);
        $email=$user->getEmail();
		$email = mysqli_real_escape_string($this->connection, $email);

        $this->addImage($image);

        $query="UPDATE users SET Hash='$hash', IsAdmin=$isAdmin, Image='$imagePath', Email='$email' WHERE Username='$username'";
        $result=$this->getResult($query);
        return $result;
    }

    //////////////////
    ////NEWS
    //////////////////

    /*public function getNewsList($gameName=null, $category=null, $newsName=null) {
        
        $query="SELECT * FROM news LEFT JOIN images ON news.image=images.path LEFT JOIN users ON news.User=users.Username";
        if($gameName != null){
            $query=$query." WHERE news.Category='Giochi' AND news.Game='$gameName'";
            if($category != null){
                $query=$query." AND news.Category='$category'";
            }
            if($newsName){
                $query=$query." AND news.Title LIKE '%$newsName%'";
            }

        }elseif($category != null){
            $query=$query." WHERE news.Category='$category'";
            if($newsName){
                $query=$query." AND news.Title LIKE '%$newsName%'";
            }
        }elseif($newsName){
            $query=$query." WHERE news.Title LIKE '%$newsName%'";
        }
        
        
        $result=$this->getResult($query);
        if($result==null){
            return null;
        }

        if(mysqli_num_rows($result) ==0){
            return null;
        }else{
            $newsList=array();
            while($row=mysqli_fetch_assoc($result)){
                $image=new Image($row['Path'], $row['Alt']);
                $user=new User($row['Username'], $row['Hash'], $row['IsAdmin'], null, $row['Email']);
                $news=new News($row['Title'], $row['Content'], $user, $row['Last_edit_date'], $image, $row['Category'], $row['Game']);
                array_push($newsList, $news);
            }
            return $newsList;
        }
    }*/
    public function getNewsList($gameName=null, $category=null, $newsName=null) {

        $gameName = mysqli_real_escape_string($this->connection, $gameName);
        $category = mysqli_real_escape_string($this->connection, $category);
        $newName = mysqli_real_escape_string($this->connection, $newsName);
        
        $query="SELECT * FROM news LEFT JOIN images ON news.image=images.path LEFT JOIN users ON news.User=users.Username";
        if($gameName != null){
            $query=$query." WHERE news.Category='Giochi' AND news.Game='$gameName'";
            if($category != null){
                $query=$query." AND news.Category='$category'";
            }
            if($newsName){
                $query=$query." AND news.Title LIKE '%$newsName%'";
            }

        }elseif($category != null){
            $query=$query." WHERE news.Category='$category'";
            if($newsName){
                $query=$query." AND news.Title LIKE '%$newsName%'";
            }
        }elseif($newsName){
            $query=$query." WHERE news.Title LIKE '%$newsName%'";
        }
        
        
        $result = $this->getResult($query);
        if($result === null){
            return null;
        }
        if($result === true){
            return null;
        } 
        if($result === false){
            return null;
        }

        $newsList = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $image=new Image($row['Path'], $row['Alt']);
            $user=new User($row['Username'], $row['Hash'], $row['IsAdmin'], null, $row['Email']);
            $news=new News($row['Title'], $row['Content'], $user, $row['Last_edit_date'], $image, $row['Category'], $row['Game']);
            array_push($newsList, $news);
            
        }
        return $newsList;
    }

    public function getNews($title){

        $title = mysqli_real_escape_string($this->connection, $title);

        $query="SELECT *, news.Image as newsImage FROM news LEFT JOIN users ON news.User=users.Username LEFT JOIN images ON news.Image=images.Path WHERE news.Title='$title'";

        $queryResult = mysqli_query($this->connection, $query);
        if($queryResult==false){
            echo mysqli_error($this->connection)."<br/>";
            return null;
        }

        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $row = mysqli_fetch_assoc($queryResult);
            //echo "Image: ".$row['Image']."<br/>";
            $image= $row['newsImage']=='' ? null : new Image($row['Path'],$row['Alt']);
            $author= new User($row['Username'], $row['Hash'], $row['IsAdmin'], null, $row['Email']);
            $news= new News($row['Title'], $row['Content'], $author, $row['Last_edit_date'], $image, $row['Category'], $row['Game']);

            return $news;
        }
    }

    public function addNews($news){
        $title = $news->getTitle();
		$title  = mysqli_real_escape_string($this->connection, $title );
        $content=$news->getContent()==null ? "NULL" : $news->getContent();
		$content = mysqli_real_escape_string($this->connection, $content);
        $author = $news->getAuthor();
		$author  = mysqli_real_escape_string($this->connection, $author );
        $authorUsername = $author->getUsername();
		$authorUsername  = mysqli_real_escape_string($this->connection, $authorUsername );
        $last_edit_date_time = $news->getLastEditDateTime();
        $image = $news->getImage();
		$image  = mysqli_real_escape_string($this->connection, $image );

        $imagePath = "NULL";
        $imageAlt = "NULL";
        if($image){
            $imagePath = $image->getPath();
            $imageAlt = $image->getAlt();
        }

        $imagePath  = mysqli_real_escape_string($this->connection, $imagePath );
        $imageAlt  = mysqli_real_escape_string($this->connection, $imageAlt );

        $category = $news->getCategory()==null ? "NULL" : $news->getCategory();
		$category  = mysqli_real_escape_string($this->connection, $category );
        $gameName = $news->getGameName();
		$gameName  = mysqli_real_escape_string($this->connection, $gameName );

        $query="INSERT INTO images VALUES ('$imagePath','$imageAlt');";
        echo "image insertion"."<br/>";
        $this->getResult($query);

        echo "news insertion"."<br/>";
        $content=addslashes($content);
        $query="INSERT INTO `news`(`Title`, `User`, `Last_edit_date`, `Content`, `Image`, `Category`, `Game`) VALUES ('$title','$authorUsername','$last_edit_date_time','$content','$imagePath','$category','$gameName')";

        echo "query: ".$query."<br/>";
        $result=$this->getResult($query);
        return $result;
    }

    function deleteNews($newsTitle){
        $newsTitle = mysqli_real_escape_string($this->connection, $newsTitle);

        $query="DELETE FROM news WHERE Title='$newsTitle'";
        $result=$this->getResult($query);
        return $result;
    }

    // questa funzione individua il gioco con nome $oldGameName e ne sovrascrive i dati con quelli di $newGame, anche il nome
    function overwriteNews($oldNewsTitle, $newNews){
        $oldNewsTitle = mysqli_real_escape_string($this->connection, $oldNewsTitle);
        
        $title=$newNews->getTitle();
		$title = mysqli_real_escape_string($this->connection, $title);
        $content=$newNews->getContent();
		$content = mysqli_real_escape_string($this->connection, $content);
        $author=$newNews->getAuthor()->getUsername();
		$author = mysqli_real_escape_string($this->connection, $author);
        $edit_date_time=$newNews->getLastEditDateTime();
		$edit_date_time = mysqli_real_escape_string($this->connection, $edit_date_time);
        $image=$newNews->getImage();
		$image = mysqli_real_escape_string($this->connection, $image);
        $category=$newNews->getCategory();
		$category = mysqli_real_escape_string($this->connection, $category);
        $game=$newNews->getGameName();
		$game = mysqli_real_escape_string($this->connection, $game);

        $this->addImage($image);
        
        $imagePath= $image ? $image->getPath() : null;
		$imagePath = mysqli_real_escape_string($this->connection, $imagePath);
        $imageAlt= $image ? $image->getAlt() : null;
		$imageAlt = mysqli_real_escape_string($this->connection, $imageAlt);

        //manca l'eventuale inserimento dell'immagine

        $query="UPDATE news SET Title='$title', User='$author', Last_edit_date='$edit_date_time', Content='$content', Image='$imagePath', Category='$category', Game='$game' WHERE Title='$oldNewsTitle'";
        $result=$this->getResult($query);
        return $result;
    }

    //////////////////
    ////GAME
    //////////////////

    public function getGamesList($gameName=null, $yearRangeStart=null, $yearRangeEnd=null, $order=null, $consoles=null, $genres=null){

        $gameName = mysqli_real_escape_string($this->connection, $gameName);
        $yearRangeStart = mysqli_real_escape_string($this->connection, $yearRangeStart);
        $yearRangeEnd = mysqli_real_escape_string($this->connection, $yearRangeEnd);
        $order = mysqli_real_escape_string($this->connection, $order);

        if($consoles){
            $sanConsoles = array();
            foreach ($consoles as $key => $value) {
                $sanConsoles[$key] = mysqli_real_escape_string($this->connection, $value);
            }
            $consoles = $sanConsoles;
        }

        if($genres){
            $sanGenres = array();
            foreach ($genres as $key => $value) {
                $sanGenres[$key] = mysqli_real_escape_string($this->connection, $value);
            }
            $genres = $sanGenres;
        }

        //le console effettive le cerco più in basso, però faccio il join con le rispettive tabelle anche qui perchè voglio trovare solo giochi che abbiano le console specificate (anche nessuna)
        $query="SELECT games.*, i1.Path as Path1, i1.Alt as Alt1, i2.Path as Path2, i2.Alt as Alt2 FROM games LEFT JOIN images as i1 ON games.Image1=i1.Path LEFT JOIN images as i2 ON games.Image2=i2.Path LEFT JOIN games_consoles ON games.Name=games_consoles.Game LEFT JOIN games_genres ON games.Name=games_genres.Game";

        $to_append_strings = array();


        // l'operatore LIKE trova valori che rispettano il pattern. In questo caso il pattern è %$gameName% che vuol dire qualsiasi stringa contenente $gameName ($gameName è il nome del parametro, al suo posto ci sarà il valore del parametro)
        //specifico un gioco
        $specifyGameNameAppend = $gameName ? " games.Name LIKE '%$gameName%'" : null;
        if($specifyGameNameAppend){
        	array_push($to_append_strings, $specifyGameNameAppend);
    	}

    	//specifico il range di anni
        $isYearRangeGiven= $yearRangeStart && $yearRangeEnd;
        $yearRangeStart=$yearRangeStart."-01-01";
        $yearRangeEnd=$yearRangeEnd."-12-31";
        $specifyYearRangeAppend= $isYearRangeGiven ? " games.Publication_date >= '$yearRangeStart' AND games.Publication_date <= '$yearRangeEnd'" : null;
        if($specifyYearRangeAppend){
        	array_push($to_append_strings, $specifyYearRangeAppend);
    	}

    	//specifico le console
        $specifyConsoles="";
        if($consoles && count($consoles)>0){
            $value=$consoles[0];
            $specifyConsoles = " (Console='$value'";
            for ($i=1;$i<count($consoles);$i++) {
                $value=$consoles[$i];
                $specifyConsoles=$specifyConsoles." OR Console='$value'";
            }
            $specifyConsoles = $specifyConsoles . " )";
        }
        if($specifyConsoles!==""){
        	array_push($to_append_strings, $specifyConsoles);
        }

        //specifico i generi
        $specifyGenres="";
        if($genres && count($genres)>0){
            $value=$genres[0];
            $specifyGenres = " (games_genres.Genre='$value'";
            for ($i=1;$i<count($genres);$i++) {
                $value=$genres[$i];
                $specifyGenres=$specifyGenres." OR games_genres.Genre='$value'";
            }
            $specifyGenres = $specifyGenres." )";
        }
        if($specifyGenres!==""){
        	array_push($to_append_strings, $specifyGenres);
        }


        $orderQueryAppend="";
        //specifico l'ordine. L'ORDER BY va aggiunto per ultimo insieme eventualmente al group by
        switch ($order) {
            case 'alfabetico':
                $orderQueryAppend=" ORDER BY games.Name ASC";
                
                break;
            
            case 'voto':
            	//considero solo i voti >= 4
            	$specifyTopVotes = " games.Vote >= 4";
            	array_push($to_append_strings, $specifyTopVotes);
                $orderQueryAppend=" ORDER BY games.Vote DESC";
                
                break;

            default:
                //questo caso si applica anche quando metto data come ordine
                $orderQueryAppend=" ORDER BY games.Publication_date DESC";
                
                break;
        }

        $assembledString = "";

        //compongo assembledString mettendo un WHERE all'inizio e AND per separare le clausole
        if(count($to_append_strings)>0){
        	$assembledString = " WHERE ".$to_append_strings[0];
        	for($i=1;$i<count($to_append_strings);$i++){
        		$assembledString = $assembledString . " AND " . $to_append_strings[$i];
        	}
        }

        
        $query = $query . " " . $assembledString;
        

        //credo che qui bisogni tenere l'ordine group by, order by, se no da errore.
        $groupByAppend = " GROUP BY games.Name ";
        $query = $query . " " . $groupByAppend;

        //qui metto order by che va alla fine. E' staccato dallo switch perchè lì metto solo il >= 4
        $query = $query . " " . $orderQueryAppend;


        $queryResult = $this->getResult($query);
        
        if($queryResult==false){
            echo mysqli_error($this->connection)."<br/>";
            return null;
        }

        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $gamesList = array();
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $consoles=$this->getConsoles($gameName);
                $genres=$this->getGenres($gameName);

                $image1 = new Image($row['Path1'],$row['Alt1']);
                $image2 = new Image($row['Path2'],$row['Alt2']);

                $game = new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $row['Review'],$image1 , $image2, $consoles, $genres, $row['Developer']);
                array_push($gamesList, $game);
            }

            return $gamesList;
        }
    }

    public function getGame($name){

        $name = mysqli_real_escape_string($this->connection, $name);
        //specifico i campi da visualizzare perchè non voglio avere 2 Prequel e 2 Sequel, così possono prendere il sequel semplicemente indicando ['Sequel'] e stessa cosa per il prequel
        $querySelect ="SELECT ps1.Prequel, Name, Publication_date, Vote, Sinopsis, Age_range, Review, Developer, ps2.Sequel, i1.Path as Path1, i1.Alt as Alt1, i2.Path as Path2, i2.Alt as Alt2 FROM prequel_sequel as ps1 RIGHT JOIN games ON ps1.Sequel=games.Name LEFT JOIN prequel_sequel as ps2 ON games.Name=ps2.Prequel LEFT JOIN images as i1 ON games.Image1=i1.Path LEFT JOIN images as i2 ON games.Image2=i2.Path WHERE games.Name='$name'";
        $queryResult = $this->getResult($querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $row = mysqli_fetch_assoc($queryResult);
            print_r($row);
            $consoles=$this->getConsoles($name);
            $genres=$this->getGenres($name);

            $image1 = new Image($row['Path1'],$row['Alt1']);
            $image2 = new Image($row['Path2'],$row['Alt2']);
            $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $row['Review'], $image1, $image2, $consoles, $genres, $row['Prequel'], $row['Sequel'], $row['Developer']);

        return $game;
        }
    }

    public function getConsoles($gameName){

        $gameName = mysqli_real_escape_string($this->connection, $gameName);
        if(!$gameName){
            return null;
        }
        $query="SELECT * FROM games_consoles WHERE Game='$gameName'";
        $result=$this->getResult($query);
        if(!$result){
            return null;
        }

        $consoles=array();
        if(mysqli_num_rows($result) == 0) {
            return null;
        }else {
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($consoles, $row['Console']);
            }

            return $consoles;
        }

    }

    public function getGenres($gameName){

        $gameName = mysqli_real_escape_string($this->connection, $gameName);

        if(!$gameName){
            return null;
        }
        $query="SELECT * FROM games_genres WHERE Game='$gameName'";
        $result=$this->getResult($query);
        if(!$result){
            return null;
        }

        $genres=array();
        if(mysqli_num_rows($result) == 0) {
            return null;
        }else {
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($genres, $row['Genre']);
            }

            return $genres;
        }

    }

    public function getTopGame(){
        $querySelect ="SELECT ps1.Prequel, Name, Publication_date, Vote, Sinopsis, Age_range, Review, Developer, ps2.Sequel, i1.Path as Path1, i1.Alt as Alt1, i2.Path as Path2, i2.Alt as Alt2 FROM prequel_sequel as ps1 RIGHT JOIN games ON ps1.Sequel=games.Name LEFT JOIN prequel_sequel as ps2 ON games.Name=ps2.Prequel LEFT JOIN images as i1 ON games.Image1=i1.Path LEFT JOIN images as i2 ON games.Image2=i2.Path LIMIT 1";
        $queryResult = $this->getResult($querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $row = mysqli_fetch_assoc($queryResult);
            // print_r($row);
            $consoles=$this->getConsoles($row['Name']);
            $genres=$this->getGenres($row['Name']);

            $image1 = new Image($row['Path1'],$row['Alt1']);
            $image2 = new Image($row['Path2'],$row['Alt2']);
            $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $row['Review'], $image1, $image2, $consoles, $genres, $row['Prequel'], $row['Sequel'], $row['Developer'] );

        return $game;
        }
    }

    public function getTop5Games(){
        $querySelect ="SELECT games.*, i1.Path, i2.Alt, i2.Path, i2.Alt FROM games LEFT JOIN images as i1 ON games.Image1=i1.Path LEFT JOIN images as i2 ON games.Image2=i2.Path ORDER BY games.Vote DESC LIMIT 5";
        $queryResult = mysqli_query($this->connection, $querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $gamesList = array();
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $image=new Image($row['Path'],$row['Alt']);

                $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $row['Review'],$image);
                array_push($gamesList, $game);
            }

            return $gamesList;
        }
    }

    public function addGame($game){
        $name = $game->getName();
		$name  = mysqli_real_escape_string($this->connection, $name );
        $date = $game->getPublicationDate();
		$date  = mysqli_real_escape_string($this->connection, $date );
        $vote = $game->getVote();
		$vote  = mysqli_real_escape_string($this->connection, $vote );
        $sinopsis = addslashes($game->getSinopsis());
		$sinopsis  = mysqli_real_escape_string($this->connection, $sinopsis );
        $age_range = $game->getAgeRange();
		$age_range  = mysqli_real_escape_string($this->connection, $age_range );
        $review = addslashes($game->getReview());
		$review  = mysqli_real_escape_string($this->connection, $review );
        $image = $game->getImage();
		$image  = mysqli_real_escape_string($this->connection, $image );
        $imagePath =  $image ? $image->getPath() : null;
		$imagePath  = mysqli_real_escape_string($this->connection, $imagePath );
        $imageAlt =  $image ? $image->getAlt() : null;
		$imageAlt  = mysqli_real_escape_string($this->connection, $imageAlt );

        $consoles = $game->getConsoles();
        $sanConsoles = array();
        foreach ($consoles as $key => $value) {
            $sanConsoles[$key] = mysqli_real_escape_string($this->connection, $value);
        }
        $consoles = $sanConsoles;

        $genres = $game->getGenres();
        $sanGenres = array();
        foreach ($genres as $key => $value) {
            $sanGenres[$key] = mysqli_real_escape_string($this->connection, $value);
        }
        $genres = $sanGenres;

        $prequel = $game->getPrequel();
		$prequel  = mysqli_real_escape_string($this->connection, $prequel );
        $sequel = $game->getSequel();
		$sequel  = mysqli_real_escape_string($this->connection, $sequel );
        $developer = $game->getDeveloper();
		$developer  = mysqli_real_escape_string($this->connection, $developer );

        if($image){
            $query="INSERT INTO images VALUES ('$imagePath', '$imageAlt')";
            $result=$this->getResult($query);
            if($result==null){
                return $result;
            }
        }

        

        $query="INSERT INTO games VALUES ('$name', '$date', '$vote', '$sinopsis', '$age_range', '$review', '$imagePath', '$developer')";
        $result=$this->getResult($query);
        if($result){
            if($consoles){
                foreach ($consoles as $value) {
                    $query="INSERT INTO games_consoles VALUES ('$name', '$value')";
                    $result=$this->getResult($query);
                    if(!$result){
                        break;
                    }
                }
            }
            if($genres){
                foreach ($genres as $value) {
                    $query="INSERT INTO games_genres VALUES ('$name', '$value')";
                    $result=$this->getResult($query);
                    if(!$result){
                        break;
                    }
                }
            }

            if($prequel){
                $query = "INSERT INTO prequel_sequel VALUES ('$prequel', '$name')";
                $result=$this->getResult($query);
            }
            if($sequel){
                $query = "INSERT INTO prequel_sequel VALUES ('$name', '$sequel')";
                $result=$this->getResult($query);
            }

        }
        return $result;
    }

    function deleteGame($gameName){
        $gameName = mysqli_real_escape_string($this->connection, $gameName);

        $query="DELETE FROM games WHERE Name='$gameName'";
        $result=$this->getResult($query);
        return $result;
    }

    //sarebbe una cosa buona mettere un count per vedere se c'è un gioco che verrà sovrascritto, per capire se l'operazione andrà a vuoto o se farà qualcosa
    function overwriteGame($oldGameName, $newGame){

        $oldGameName = mysqli_real_escape_string($this->connection, $oldGameName);
        // questa funzione individua il gioco con nome $oldGameName e ne sovrascrive i dati con quelli di $newGame, anche il nome
        $name = $newGame->getName();
		$name  = mysqli_real_escape_string($this->connection, $name );
        $date = $newGame->getPublicationDate();
		$date  = mysqli_real_escape_string($this->connection, $date );
        $vote = $newGame->getVote();
		$vote  = mysqli_real_escape_string($this->connection, $vote );
        $sinopsis = addslashes($newGame->getSinopsis());
		$sinopsis  = mysqli_real_escape_string($this->connection, $sinopsis );
        $age_range = $newGame->getAgeRange();
		$age_range  = mysqli_real_escape_string($this->connection, $age_range );
        $review = addslashes($newGame->getReview());
		$review  = mysqli_real_escape_string($this->connection, $review );
        $image = $newGame->getImage();
		$image  = mysqli_real_escape_string($this->connection, $image );
        
        $imagePath =  $image ? $image->getPath() : null;
		$imagePath  = mysqli_real_escape_string($this->connection, $imagePath );
        $imageAlt =  $image ? $image->getAlt() : null;
		$imageAlt  = mysqli_real_escape_string($this->connection, $imageAlt );

        $consoles = $newGame->getConsoles();
        $sanConsoles = array();
        foreach ($consoles as $key => $value) {
            $sanConsoles[$key] = mysqli_real_escape_string($this->connection, $value);
        }
        $consoles = $sanConsoles;

        $genres = $newGame->getGenres();
        $sanGenres = array();
        foreach ($genres as $key => $value) {
            $sanGenres[$key] = mysqli_real_escape_string($this->connection, $value);
        }
        $genres = $sanGenres;
        
        $prequel = $newGame->getPrequel();
		$prequel  = mysqli_real_escape_string($this->connection, $prequel );
        $sequel = $newGame->getSequel();
		$sequel  = mysqli_real_escape_string($this->connection, $sequel );
        $developer = $newGame->getDeveloper();
		$developer  = mysqli_real_escape_string($this->connection, $developer );

        $this->addImage($image);

        $result = true;

        

        if($result){
            $query="UPDATE games SET Name='$name', Publication_date='$date', Vote='$vote', Sinopsis='$sinopsis', Age_range='$age_range', Review='$review', Image='$imagePath', Developer='$developer' WHERE Name='$oldGameName'";
            $result=$this->getResult($query);
        }
        if($result){
            if($result){
                $query="DELETE FROM games_consoles WHERE Game='$oldGameName'";
                $result=$this->getResult($query);
            }

            if($result){
                $query="DELETE FROM games_genres WHERE Game='$oldGameName'";
                $result=$this->getResult($query);
            }


            if($result && $consoles){

                foreach ($consoles as $value) {

                    $query="INSERT INTO games_consoles VALUES ('$name', '$value')";
                    $result=$this->getResult($query);
                    if(!$result){
                        echo "problem"."<br/>";
                        break;
                    }
                }
            }
            if($result && $genres){
                foreach ($genres as $value) {
                    $query="INSERT INTO games_genres VALUES ('$name', '$value')";
                    $result=$this->getResult($query);
                    if(!$result){
                        break;
                    }
                }
            }

            if($result){
                $query = "DELETE FROM prequel_sequel WHERE Prequel='$name' OR Sequel='$name' ";
                $result=$this->getResult($query);
            }

            if($result && $prequel){
                $query = "INSERT INTO prequel_sequel VALUES ('$prequel', '$name')";
                $result=$this->getResult($query);
            }
            if($result && $sequel){
                $query = "INSERT INTO prequel_sequel VALUES ('$name', '$sequel')";
                $result=$this->getResult($query);
            }

            
        }

        return $result;
    }

    //////////////////
    ////IMAGE
    //////////////////

    public function getImages($order=null){

        $order = mysqli_real_escape_string($this->connection, $order);

        $query ="SELECT * FROM images";

        $orderQueryAppend= $order=="path asc" ? "ORDER BY images.Path ASC" : "";
        $query=$query." ".$orderQueryAppend;
        $queryResult = mysqli_query($this->connection, $query);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $imagesList = array();
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $image=new Image($row['Path'],$row['Alt']);
                array_push($imagesList, $image);
            }
            return $imagesList;
        }
    }


    public function addImage($image){
        if(!$image){
            return null;
        }
        $imagePath=$image->getPath();
		$imagePath = mysqli_real_escape_string($this->connection, $imagePath);
        $imageAlt=$image->getAlt();
		$imageAlt = mysqli_real_escape_string($this->connection, $imageAlt);

        $query="INSERT INTO images VALUES ('$imagePath', '$imageAlt')";
        $result=$this->getResult($query);
        return $result;
    }

    function deleteImage($imagePath){
        $imagePath = mysqli_real_escape_string($this->connection, $imagePath);

        $query="DELETE FROM images WHERE Path='$imagePath'";
        $result=$this->getResult($query);
        return $result;
    }

    ////////////////
    ///////COMMENT
    ////////////////

    function getCommentsList($gameName=null, $order="date_time desc"){
        $gameName = mysqli_real_escape_string($this->connection, $gameName);
        $oder = mysqli_real_escape_string($this->connection, $order);

        $query="SELECT * FROM comments";
        $gameNameQueryAppend= $gameName ? "WHERE comments.Game='$gameName'" : "";
        $orderQueryAppend= $order=="date_time desc" ? "ORDER BY comments.Date_time DESC" : "";
        $query=$query." ".$gameNameQueryAppend." ".$orderQueryAppend;
        $queryResult = $this->getResult($query);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $commentsList = array();
            echo "commentsDBResult: " . "";
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $comment=new Comment($row['Author'],$row['Game'], $row['Date_time'],$row['Content']);
                array_push($commentsList, $comment);
            }
            return $commentsList;
        }

    }

    function addComment($comment){
        $authorName = $comment->getAuthorName();
		$authorName  = mysqli_real_escape_string($this->connection, $authorName );
        $gameName = $comment->getGameName();
		$gameName  = mysqli_real_escape_string($this->connection, $gameName );
        $date_time = $comment->getDateTime();
		$date_time  = mysqli_real_escape_string($this->connection, $date_time );
        $content = addslashes( $comment->getContent() );
		$content  = mysqli_real_escape_string($this->connection, $content );

        $query="INSERT INTO comments VALUES (DEFAULT, '$authorName', '$gameName', '$date_time', '$content')";
        $result=$this->getResult($query);
        return $result;
    }
    
}
?>
