<?php

require_once("./classes/news.php");
require_once("./classes/game.php");
require_once("./classes/image.php");
require_once("./classes/user.php");
require_once("./classes/review.php");
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

        //myswli_connect_error($this->connction)
        //si può usare come alternativa
        //restituisce un numero corrispondente all'errore, oppure 0 se è andata a buon fine
    }
    /*
    public function closeConnection(){
        $this->connection->close();
    }
    */

    #la funzione getResult deve ricevere in input una stringa già sanificata (sanitized)
    #altrimenti la sicurezza può essere compromessa
    public function getResult($query){
        $querySelect ="$query";
        $queryResult = mysqli_query($this->connection, $querySelect);
        /*echo "query result: ".$queryResult;*/
        if($queryResult==true){
            return $queryResult;
        }

        if($queryResult==false){
            echo mysqli_error($this->connection);
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
/*
    public function getUserList($name=null) {
        if($name==null){
            $querySelect ="SELECT * FROM users ORDER BY Nickname ASC";
        }else{
            $querySelect ="SELECT * FROM users WHERE name='$name' ORDER BY Nickname ASC";
        }
        $queryResult = mysqli_query($this->connection, $querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $listaPersonaggi = array();
            while ($riga = mysqli_fetch_assoc($queryResult)) {
                $singoloPersonaggio = array(
                    "Name" => $riga['Name'],
                );
                array_push($listaPersonaggi, $singoloPersonaggio);
            }

            return $listaPersonaggi;
        }
    }
*/
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
        echo "delete query result: ".$sq;
        echo mysqli_error($this->connection);
    }

    public function getNewsList() {
        $query="SELECT * FROM news LEFT JOIN images ON news.image=images.path LEFT JOIN users ON news.User=users.Username";
        $result=mysqli_query($this->connection, $query);

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
            echo mysqli_error($this->connection);
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

    public function getGamesList($gameName=null){
        if($gameName==null){
            $querySelect ="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path LEFT JOIN reviews ON games.Review=reviews.Id";
        }else{
            $querySelect ="SELECT * FROM (games LEFT JOIN images ON games.Image=images.Path LEFT JOIN reviews ON games.Review=reviews.Id) WHERE games.Name='$gameName' ";
        }

        $queryResult = mysqli_query($this->connection, $querySelect);
        
        if($queryResult==false){
            echo mysqli_error($this->connection);
            return null;
        }

        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $gamesList = array();
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $image=new Image($row['Path'],$row['Alt']);
                $review=new Review($row['Content'], $row['Author'], $row['Last_edit_date_time']);

                $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $review,$image);
                array_push($gamesList, $game);
            }

            return $gamesList;
        }
    }

    public function getGame($name){
        $querySelect ="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path AND games.Name='$name' LEFT JOIN reviews ON games.Review=reviews.Id";
        $queryResult = mysqli_query($this->connection, $querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $row = mysqli_fetch_assoc($queryResult);
            $image=new Image($row['Path'],$row['Alt']);
            $review=new Review($row['Content'], $row['Author'], $row['Last_edit_date_time']);
            $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $review, $image);

        return $game;
        }
    }

    public function getTopGame(){
        $querySelect ="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path LEFT JOIN reviews ON games.Review=reviews.Id ORDER BY games.Vote DESC LIMIT 1";
        $queryResult = mysqli_query($this->connection, $querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $row = mysqli_fetch_assoc($queryResult);
            $image=new Image($row['Path'],$row['Alt']);
            $review=new Review($row['Content'], $row['Author'], $row['Last_edit_date_time']);
            $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $review, $image);

        return $game;
        }
    }

    public function getTop5Games(){
        $querySelect ="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path LEFT JOIN reviews ON games.Review=reviews.Id ORDER BY games.Vote DESC LIMIT 5";
        $queryResult = mysqli_query($this->connection, $querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $gamesList = array();
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $image=new Image($row['Path'],$row['Alt']);
                $review=new Review($row['Content'], $row['Author'], $row['Last_edit_date_time']);

                $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $review,$image);
                array_push($gamesList, $game);
            }

            return $gamesList;
        }
    }

    public function getImages(){
        $querySelect ="SELECT * FROM images";
        $queryResult = mysqli_query($this->connection, $querySelect);
        
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
        if($image){
            $imagePath=$image->getPath();
            $imageAlt=$image->getAlt();
            $query="INSERT INTO images VALUES ('$imagePath','$imageAlt');";
        }
        
        if($image){
            $image=$imagePath;
        }else{
            $image="NULL";
        }

        $query="INSERT INTO users VALUES ('$name','$hash', $isAdmin, $image, '$email');";
        echo "query: ".$query;
        $result=$this->getResult($query);
        if($result==null){
            $result="null";
        }
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
        echo "<br/>image insertion";
        $this->getResult($query);

        echo "<br/>news insertion";
        $content=addslashes($content);
        $query="INSERT INTO `news`(`Id`,`Title`, `User`, `Last_edit_date`, `Content`, `Image`, `Category`) VALUES (DEFAULT,'$title','$authorUsername','$last_edit_date_time','$content','$imagePath','$category')";

        echo "<br/>query: ".$query;
        $result=$this->getResult($query);
        if($result==null){
            $result="null";
        }
        return $result;
    }

}
?>
