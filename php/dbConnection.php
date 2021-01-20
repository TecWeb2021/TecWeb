<?php

require_once("./classes/news.php");
require_once("./classes/game.php");
require_once("./classes/image.php");
require_once("./classes/user.php");
//namespace DB;

//my db interface is on localhost:80/phpmyadmin
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
            echo "connection failed";
            return false;
        }else {
            echo "connection accomplished";
            return true;
        }

        //myswli_connect_error($this->connction)
        //si può usare come alternativa
        //restituisce un numero corrispondente all'errore, oppure 0 se è andata a buon fine
    }

    #la funzione getResult deve ricevere in input una stringa già sanificata (sanitized)
    #altrimenti la sicurezza può essere compromessa
    public function getResult($query){
        $querySelect ="$query";
        $queryResult = mysqli_query($this->connection, $querySelect);
        /*echo "query result: ".$queryResult;*/
        if($queryResult==true){
            return $queryResult;
        }
        if($queryResult==null || $queryResult==false || mysqli_num_rows($queryResult) == 0) {
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

    public function getNewsList() {
        $query="SELECT * FROM news LEFT JOIN images ON news.image=images.path LEFT JOIN users ON news.User=users.Username";
        $result=mysqli_query($this->connection, $query);

        if(mysqli_num_rows($result) ==0){
            return null;
        }else{
            $newsList=array();
            while($row=mysqli_fetch_assoc($result)){
                $image=new Image($row['Path'], $row['Alt']);
                $user=new User($row['Username'], $row['Hash'], $row['IsAdmin']);
                $news=new News($row['Title'], $row['Content'], $user, $row['Last_edit_date'], $image, $row['Category']);
                array_push($newsList, $news);
            }
            return $newsList;
        }
    }

    public function getTableList($name){
        $name=preg_replace("/[^a-zA-Z0-9_]/","",$name);
        $query ="SELECT * FROM $name";
        $result=$this->getResult($query);
        return $result;
    }

    public function getGamesList(){
        $querySelect ="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path";
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

    public function getGame($name){
        $querySelect ="SELECT * FROM games LEFT JOIN images ON games.Image=images.Path AND games.Name='$name' ";
        $queryResult = mysqli_query($this->connection, $querySelect);
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $row = mysqli_fetch_assoc($queryResult);
            $image=new Image($row['Path'],$row['Alt']);
            $game=new Game($row['Name'], $row['Publication_date'], $row['Vote'],$row['Sinopsis'],$row['Age_range'], $row['Review'],$image);

        return $game;
        }
    }

    public function getUserByHash($hashValue){
        $query="SELECT * FROM users WHERE hash=\"$hashValue\"";
        $result=$this->getResult($query);
        return $result;
    }

    public function addUser($username, $password,$is_admin){
        $hashValue=hash("md5",$username.$password);
        $query="INSERT INTO users VALUES ('$username','$hashValue',$is_admin);";
        echo "query: ".$query;
        $result=$this->getResult($query);
        if($result==null){
            $result="null";
        }
        echo "addUser_result: ".$result;

    }

}
?>
