<?php

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
            return false;
        }else {
            return true;
        }

        //myswli_connect_error($this->connction)
        //si può usare come alternativa
        //restituisce un numero corrispondente all'errore, oppure 0 se è andata a buon fine
    }

    public function getUserList($name=null) {
        if($name==null){
            $querySelect ="SELECT * FROM users ORDER BY Nickname ASC";
        }else{
            $querySelect ="SELECT * FROM users WHERE name='$name' ORDER BY Nickname ASC";
        }
        //questa funz restituisce false se c'è un problema. Se funziona ma nella query non c'è un select, viene restituito true. Per tutte le altre query viene restituito un oggetto ... , che è sostanzialmente una tabella contenente i risultati. 
        $queryResult = mysqli_query($this->connection, $querySelect);
        /*echo "query result: ".$queryResult;*/
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $listaPersonaggi = array();

            //mysqli_fetch muove l'iteratore. Ogni volta che lo eseguo va alla successiva, fino a quando arriva alla fine e restituisce null.
            //mysqli_fetch_assoc (in maniera associativa)
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

    public function getNewsList($name=null) {
        if($name==null){
            $querySelect ="SELECT * FROM news";
        }else{
            $querySelect ="SELECT * FROM news WHERE Title='$name' ";
        }
        //questa funz restituisce false se c'è un problema. Se funziona ma nella query non c'è un select, viene restituito true. Per tutte le altre query viene restituito un oggetto ... , che è sostanzialmente una tabella contenente i risultati. 
        $queryResult = mysqli_query($this->connection, $querySelect);
        /*echo "query result: ".$queryResult;*/
        
        if(mysqli_num_rows($queryResult) == 0) {
            return null;
        }else {
            $listaPersonaggi = array();

            //mysqli_fetch muove l'iteratore. Ogni volta che lo eseguo va alla successiva, fino a quando arriva alla fine e restituisce null.
            //mysqli_fetch_assoc (in maniera associativa)
            while ($riga = mysqli_fetch_assoc($queryResult)) {
                $singoloPersonaggio = array(
                    "Title" => $riga['Title'],
                    "Text" => $riga['Text'],
                );
                array_push($listaPersonaggi, $singoloPersonaggio);
            }

            return $listaPersonaggi;
        }
    }
}
?>