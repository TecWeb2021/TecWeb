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
    public function getResult($query, $silent=false){
        $querySelect ="$query";
        echo "db query: ".$querySelect."<br/>";
        $queryResult = mysqli_query($this->connection, $querySelect);
        /*echo "query result: ".$queryResult;*/
        if($queryResult==true){
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
            while ($row = mysql_fetch_assoc($resultList)) {
                array_push($resultList, $row);
            }
            #restituisce un array di array. Gli array contenutivi sono le righe del database.
            return $resultList;
        }

    }

    public function getUsersList(){
        $query="SELECT * FROM users LEFT JOIN images ON users.image=images.path";
        $result=mysqli_query($this->connection, $query);

        if(mysqli_num_rows($result) ==0){
            return null;
        }else{
            $usersList=array();
            while($row=mysqli_fetch_assoc($result)){
                //print_r($row);
                $image= $row['Image']=="" ? null : new Image($row['Path'], $row['Alt']);
                $user=new User($row['Username'], $row['Hash'], $row['IsAdmin'], $image, $row['Email']);
                array_push($usersList, $user);
            }
            return $usersList;
        }
    }

    public function insertUser($name=null){
        if($name==null){
            return;
        }
        $querySelect="SELECT MAX(id) FROM users WHERE true";
        $queryResult = mysqli_query($this->connection, $querySelect);
        $riga = mysqli_fetch_assoc($queryResult);
        foreach ($riga as $field => $value) { // I you want you can right this line like this: foreach($row as $value) {
            echo "<td>" ."ciao". $value . "</td>"; // I just did not use "htmlspecialchars()" function. 
            $maxId=$value;
        }
        $querySelect ="INSERT INTO users (id,name) VALUES ('$maxId'+1,'$name');";
        $queryResult = mysqli_query($this->connection, $querySelect);
    }

    public function deleteUser($username){
        $query="DELETE FROM users WHERE Username='$username'";
        $queryResult = mysqli_query($this->connection, $query);
        $sq=$queryResult==null? "null":"not null";
        echo "delete query result: ".$sq."<br/>"."<br/>";
        echo mysqli_error($this->connection)."<br/>";
    }

    public function getNewsList($gameName=null) {
        $specifyGameNameAppend="";
        if($gameName!=null){
            $specifyGameNameAppend="WHERE news.Game='$gameName'";
        }
        $query="SELECT * FROM news LEFT JOIN images ON news.image=images.path LEFT JOIN users ON news.User=users.Username";
        $query=$query." ".$specifyGameNameAppend;
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
                $news=new News($row['Title'], $row['Content'], $user, $row['Last_edit_date'], $image, $row['Category']);
                array_push($newsList, $news);
            }
            return $newsList;
        }
    }

    public function getNews($title){
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
            $news= new News($row['Title'], $row['Content'], $author, $row['Last_edit_date'], $image, $row['Category']);

            return $news;
        }
    }

    public function getTableList($name){
        $name=preg_replace("/[^a-zA-Z0-9_]/","",$name);
        $query ="SELECT * FROM $name";
        $result=$this->getResult($query);
        return $result;
    }

    public function getGamesList($gameName=null, $yearRangeStart=null, $yearRangeEnd=null, $order=null, $consoles=null, $genres=null){
        //le console effettive le cerco più in basso, però faccio il join con le rispettive tabelle anche qui perchè voglio trovare solo giochi che abbiano le console specificate (anche nessuna)
        $query="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path LEFT JOIN games_consoles ON games.Name=games_consoles.Game LEFT JOIN games_genres ON games.Name=games_genres.Game";

        
        // l'operatore LIKE trova valori che rispettano il pattern. In questo caso il pattern è %$gameName% che vuol dire qualsiasi stringa contenente $gameName ($gameName è il nome del parametro, al suo posto ci sarà il valore del parametro)
        $specifyGameNameAppend= $gameName ? "games.Name LIKE '%$gameName%'" : null;

        $isYearRangeGiven= $yearRangeStart && $yearRangeEnd;
        $yearRangeStart=$yearRangeStart."-01-01";
        $yearRangeEnd=$yearRangeEnd."-12-31";
        $specifyYearRangeAppend= $isYearRangeGiven ? "games.Publication_date >= '$yearRangeStart' AND games.Publication_date <= '$yearRangeEnd'" : null;
        $specifyConsoles="";
        if($consoles && count($consoles)>0){
            $value=$consoles[0];
            $specifyConsoles="WHERE Console='$value'";
            for ($i=1;$i<count($consoles);$i++) {
                $value=$consoles[$i];
                $specifyConsoles=$specifyConsoles." "."OR Console='$value'";
            }
            $query=$query." ".$specifyConsoles;
        }

        $specifyGenres="";
        if($genres && count($genres)>0){
            $value=$genres[0];
            $specifyGenres="WHERE games_genres.Genre='$value'";
            for ($i=1;$i<count($genres);$i++) {
                $value=$genres[$i];
                $specifyGenres=$specifyGenres." "."OR games_genres.Genre='$value'";
            }
            $query=$query." ".$specifyGenres;
        }

        switch ($order) {
            case 'alfabetico':
                $orderQueryAppend="ORDER BY games.Name ASC";
                break;
            
            case 'voto':
                $orderQueryAppend="ORDER BY games.Vote ASC";
                break;

            default:
                $orderQueryAppend="ORDER BY games.Publication_date DESC";
                break;
        }

        if($specifyGameNameAppend){
            $query=$query." WHERE ".$specifyGameNameAppend; 

            if($specifyYearRangeAppend){
                $query=$query." AND ".$specifyYearRangeAppend;
            }
        }else{
            if($specifyYearRangeAppend){
                $query=$query." WHERE ".$specifyYearRangeAppend;
            }
        }

        //credo che qui bisogni tenere l'ordine group by, order by, se no da errore.
        $query = $query . " GROUP BY games.Name ";

        

        $query=$query." ".$orderQueryAppend;
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

                $image = new Image($row['Path'],$row['Alt']);

                $game = new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $row['Review'],$image, $consoles, $genres);
                array_push($gamesList, $game);
            }

            return $gamesList;
        }
    }

    public function getGame($name){
        $querySelect ="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path WHERE games.Name='$name'";
        $queryResult = mysqli_query($this->connection, $querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $row = mysqli_fetch_assoc($queryResult);
            $consoles=$this->getConsoles($name);
            $genres=$this->getGenres($name);

            $image=new Image($row['Path'],$row['Alt']);
            $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $row['Review'], $image, $consoles, $genres);

        return $game;
        }
    }

    public function getConsoles($gameName){
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
        $querySelect ="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path ORDER BY games.Vote DESC LIMIT 1";
        $queryResult = mysqli_query($this->connection, $querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $row = mysqli_fetch_assoc($queryResult);
            $image=new Image($row['Path'],$row['Alt']);
            $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $row['Review'], $image);

        return $game;
        }
    }

    public function getTop5Games(){
        $querySelect ="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path ORDER BY games.Vote DESC LIMIT 5";
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

    public function getImages($order=null){

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

    public function getUserByHash($hashValue){
        $query="SELECT * FROM users LEFT JOIN images ON users.Image=images.Path WHERE hash='$hashValue'";
        $queryResult = mysqli_query($this->connection, $query);
        
        if(mysqli_num_rows($queryResult) == 0) {
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
        $name=$user->getUsername();
        $hash=$user->getHash();
        $isAdmin=$user->IsAdmin();
        $image=$user->getImage();
        $email=$user->getEmail();

        $imagePath="";
        $imageAlt="";
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

        $query="INSERT INTO users VALUES ('$name','$hash', $isAdmin, '$imagePath', '$email');";
        echo "query: ".$query."<br/>";
        $result=$this->getResult($query);
        return $result;
    }

    public function updateUser($user){
        $username=$user->getUsername();
        $hash=$user->getHash();
        $isAdmin=$user->isAdmin();
        $image=$user->getImage();
        $imagePath= $image ? $image->getPath() : null;
        $this->addImage($image);
        $email=$user->getEmail();

        $query="UPDATE users SET Hash='$hash', IsAdmin=$isAdmin, Image='$imagePath', Email='$email' WHERE Username='$username'";
        $result=$this->getResult($query);
        return $result;
    }

    public function addImage($image){
        if(!$image){
            return null;
        }
        $imagePath=$image->getPath();
        $imageAlt=$image->getAlt();
        $query="INSERT INTO images VALUES ('$imagePath', '$imageAlt')";
        $result=$this->getResult($query);
        return $result;
    }

    public function addNews($news){
        $title=$news->getTitle();
        $content=$news->getContent()==null ? "NULL" : $news->getContent();
        $author=$news->getAuthor();
        $authorUsername=$author->getUsername();
        $last_edit_date_time=$news->getLastEditDateTime();
        $image=$news->getImage();
        $imagePath="NULL";
        $imageAlt="NULL";
        if($image){
            $imagePath=$image->getPath();
            $imageAlt=$image->getAlt();
        }
        $category=$news->getCategory()==null ? "NULL" : $news->getCategory();

        $query="INSERT INTO images VALUES ('$imagePath','$imageAlt');";
        echo "image insertion"."<br/>";
        $this->getResult($query);

        echo "news insertion"."<br/>";
        $content=addslashes($content);
        $query="INSERT INTO `news`(`Title`, `User`, `Last_edit_date`, `Content`, `Image`, `Category`) VALUES ('$title','$authorUsername','$last_edit_date_time','$content','$imagePath','$category')";

        echo "query: ".$query."<br/>";
        $result=$this->getResult($query);
        return $result;
    }

    public function updateNews($news){
        $title=$news->getTitle();
        $content=$news->getContent()==null ? "NULL" : $news->getContent();
        $author=$news->getAuthor();
        $authorUsername=$author->getUsername();
        $last_edit_date_time=$news->getLastEditDateTime();
        $image=$news->getImage();
        $imagePath="NULL";
        $imageAlt="NULL";
        if($image){
            $imagePath=$image->getPath();
            $imageAlt=$image->getAlt();
        }
        $category=$news->getCategory()==null ? "NULL" : $news->getCategory();

        $query="INSERT INTO images VALUES ('$imagePath','$imageAlt');";
        echo "image insertion"."<br/>";
        $this->getResult($query);

        echo "news insertion"."<br/>";
        $content=addslashes($content);
        $query="UPDATE news SET User='$authorUsername', Last_edit_date='$last_edit_date_time', Image='$imagePath', Category='$category', Content='$content' WHERE Title='$title'";

        echo "query: ".$query."<br/>";
        $result=$this->getResult($query);
        return $result;
    }

    public function addGameNews(){
        $title=$news->getTitle();
        $content=$news->getContent()==null ? "NULL" : $news->getContent();
        $author=$news->getAuthor();
        $authorUsername=$author->getUsername();
        $last_edit_date_time=$news->getLastEditDateTime();
        $image=$news->getImage();
        $imagePath="NULL";
        $imageAlt="NULL";
        if($image){
            $imagePath=$image->getPath();
            $imageAlt=$image->getAlt();
        }
        $category=$news->getCategory()==null ? "NULL" : $news->getCategory();
        $gameName=$news->getGameName()==null ? "NULL" : $news->getGameName();

        $query="INSERT INTO images VALUES ('$imagePath','$imageAlt');";
        echo "image insertion"."<br/>";
        $this->getResult($query);

        echo "news insertion"."<br/>";
        $content=addslashes($content);
        $query="INSERT INTO `news`(`Id`,`Title`, `User`, `Last_edit_date`, `Content`, `Image`, `Category`) VALUES (DEFAULT,'$title','$authorUsername','$last_edit_date_time','$content','$imagePath','$category')";

        echo "query: ".$query."<br/>";
        $result=$this->getResult($query);
        if($result==null){
            $query="UPDATE news SET Title='$title', User='$authorUsername', Last_edit_date='$last_edit_date_time', Content='$content', Image='$imagePath', Category='$category' WHERE Title='$title'";
            $result=$this->getResult($query);
        }
        return $result;
    }

    public function addGame($game){
        $name=$game->getName();
        $date=$game->getPublicationDate();
        $vote=$game->getVote();
        $sinopsis=$game->getSinopsis();
        $age_range=$game->getAgeRange();
        $review=$game->getReview();
        $image=$game->getImage();
        $imagePath= $image ? $image->getPath() : null;
        $imageAlt= $image ? $image->getAlt() : null;

        $consoles=$game->getConsoles();
        $genres=$game->getGenres();

        if($image){
            $query="INSERT INTO images VALUES ('$imagePath', '$imageAlt')";
            $result=$this->getResult($query);
            if($result==null){
                return $result;
            }
        }

        

        $query="INSERT INTO games VALUES ('$name', '$date', '$vote', '$sinopsis', '$age_range', '$review', '$imagePath')";
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
        }
        return $result;
    }

    //sarebbe una cosa buona mettere un count per vedere se c'è un gioco che verrà sovrascritto, per capire se l'operazione andrà a vuoto o se farà qualcosa
    function overwriteGame($oldGameName, $newGame){
        // questa funzione individua il gioco con nome $oldGameName e ne sovrascrive i dati con quelli di $newGame, anche il nome
        $name=$newGame->getName();
        $date=$newGame->getPublicationDate();
        $vote=$newGame->getVote();
        $sinopsis=$newGame->getSinopsis();
        $age_range=$newGame->getAgeRange();
        $review=$newGame->getReview();
        $image=$newGame->getImage();
        $this->addImage($image);
        $imagePath= $image ? $image->getPath() : null;
        $imageAlt= $image ? $image->getAlt() : null;

        $consoles=$newGame->getConsoles();
        $genres=$newGame->getGenres();
        print_r($consoles);
        echo "<br/>";

        $result=true;

        

        if($result){
            $query="UPDATE games SET Name='$name', Publication_date='$date', Vote='$vote', Sinopsis='$sinopsis', Age_range='$age_range', Review='$review', Image='$imagePath' WHERE Name='$oldGameName'";
            $result=$this->getResult($query);
        }

        echo "step1"."<br/>";
        if($result){
            if($result){
                echo "step2"."<br/>";
                $query="DELETE FROM games_consoles WHERE Game='$oldGameName'";
                $result=$this->getResult($query);
            }
            echo "partial result".($result==true ? "true" : "false")."<br/>";
            if($result){
                echo "step3"."<br/>";
                $query="DELETE FROM games_genres WHERE Game='$oldGameName'";
                $result=$this->getResult($query);
            }

            echo "comincio a inserire i nuovi valori per le console e i generi"."<br/>";
            if($result && $consoles){
                echo "step4"."<br/>";
                foreach ($consoles as $value) {
                    echo "$name : $value"."<br/>";
                    $query="INSERT INTO games_consoles VALUES ('$name', '$value')";
                    $result=$this->getResult($query);
                    if(!$result){
                        echo "problem"."<br/>";
                        break;
                    }
                }
            }
            if($result && $genres){
                echo "step5"."<br/>";
                foreach ($genres as $value) {
                    echo "$name : $value"."<br/>";
                    $query="INSERT INTO games_genres VALUES ('$name', '$value')";
                    $result=$this->getResult($query);
                    if(!$result){
                        echo "problem"."<br/>";
                        break;
                    }
                }
            }

            
        }

        return $result;
    }

    /*
    $query="INSERT INTO `news`(`Title`, `User`, `Last_edit_date`, `Content`, `Image`, `Category`) VALUES ('$title','$authorUsername','$last_edit_date_time','$content','$imagePath','$category')";
    */

    function overwriteNews($oldNewsTitle, $newNews){
        // questa funzione individua il gioco con nome $oldGameName e ne sovrascrive i dati con quelli di $newGame, anche il nome
        $title=$newNews->getTitle();
        $content=$newNews->getContent();
        $author=$newNews->getAuthor()->getUsername();
        $edit_date_time=$newNews->getLastEditDateTime();
        $image=$newNews->getImage();
        $this->addImage($image);
        $category=$newNews->getCategory();
        
        $imagePath= $image ? $image->getPath() : null;
        $imageAlt= $image ? $image->getAlt() : null;

        //manca l'eventuale inserimento dell'immagine

        $query="UPDATE news SET Title='$title', User='$author', Last_edit_date='$edit_date_time', Content='$content', Image='$imagePath', Category='$category' WHERE Title='$oldNewsTitle'";
        $result=$this->getResult($query);
        return $result;
    }


    function addComment($comment){
        $authorName=$comment->getAuthorName();
        $gameName=$comment->getGameName();
        $date_time=$comment->getDateTime();
        $content=$comment->getContent();

        $query="INSERT INTO comments VALUES (DEFAULT, '$authorName', '$gameName', '$date_time', '$content')";
        $result=$this->getResult($query);
        return $result;
    }

    function getCommentsList($gameName=null, $order="date_time desc"){
        $query="SELECT * FROM comments";
        $gameNameQueryAppend= $gameName ? "WHERE comments.Game='$gameName'" : "";
        $orderQueryAppend= $order=="date_time desc" ? "ORDER BY comments.Date_time DESC" : "";
        $query=$query." ".$gameNameQueryAppend." ".$orderQueryAppend;
        $queryResult = $this->getResult($query);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $commentsList = array();
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $comment=new Comment($row['Author'],$row['Game'], $row['Date_time'],$row['Content']);
                array_push($commentsList, $comment);
            }
            return $commentsList;
        }


    }

    //the three functions below just remove things from the db
    function deleteNews($newsTitle){
        $query="DELETE FROM news WHERE Title='$newsTitle'";
        $result=$this->getResult($query);
        return $result;
    }

    function deleteGame($gameName){
        $query="DELETE FROM games WHERE Name='$gameName'";
        $result=$this->getResult($query);
        return $result;
    }

    function deleteImage($imagePath){
        $query="DELETE FROM images WHERE Path='$imagePath'";
        $result=$this->getResult($query);
        return $result;
    }

}
?>
