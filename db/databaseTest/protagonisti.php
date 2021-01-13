<?php
//require ogni volta che c'è il file viene importato
require_once "dbConnection.php";
//require_once invece importa solo se non è gia stato importato

$dbAccess = new DBAccess();
$connessioneRiuscita = $dbAccess->openDBConnection();

//La cosa che viene fatta qui sotto non va proprio bene perchè non viene data risposta all'utente. Meglio usare il try catch.
if($connessioneRiuscita == false) {
    die ("Errore nell'apertura del DB");
}else {
    $name="";
    if($_POST["fname"]){
        $name=$_POST["fname"];
    }
    $dbAccess->insertUser($name);

    $listaProtagonisti =$dbAccess->getListaPersonaggi();

    if ($listaProtagonisti != null) {
        // Creo parte di pagina HTML con elenco dei protagonisti
        $definitionListProtagonisti = '<dl id="charactersStory">';

        foreach ($listaProtagonisti as $protagonista) {
            $definitionListProtagonisti .= '<dt>' . $protagonista['Name'] . '</dt>';
            $definitionListProtagonisti .= '<dd>';
            $definitionListProtagonisti .= '<p class="aiutoTornaSu"><a href="#contentPagina">Torna su</a></p>';
            $definitionListProtagonisti .= '</dd>';
        }

        $definitionListProtagonisti = $definitionListProtagonisti . "</dl>";

    }else {
        // Messaggio che dice che non ci sono protagonisti nel DB

        $definitionListProtagonisti = "<p>Nessun personaggio presente</p>";
    }

    $paginaHTML = file_get_contents("page1.html");
    //echo $definitionListProtagonisti;
    echo str_replace("<listPersonaggi />", $definitionListProtagonisti, $paginaHTML);
}



?>