


<?php
 
// una differenza tra questo script e quello per l'inserimento di un gioco nuovo sta nel fatto che qui viene accettato il "salva modifiche" anche se non è stata fornita una immagine in input, o almeno credo

//possibile modifica da fare:
//	per ora metto nei placeholder i valori del gioco che voglio modificare.
//	l'utente poi può modificare i valori e inviarli
//	se qualcosa va storto torno alla pagina, ma vengono rimessi nei placeholder i valori del vecchio gioco
// potrei fare così: se rilevo tutti valori richiesti nel $_REQUEST allora metto quelli, altrimenti metto quelli del vecchio gioco

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/editGiocoTemplate.html");




// verifico che l'utente abbia l'autorizzazione per modificare un gioco
$user=getLoggedUser($dbAccess);

$authCheck=true;

if(!$user){
	$homePage="Non sei autenticato";
	$authCheck=false;
}
if($authCheck && !$user->isAdmin()){
	$homePage="Non sei un amministratore";
	$authCheck=false;
}

//allOk prende in carico le prossime verifiche e parte dal valore di $authCheck
$allOk=$authCheck;
// verifico che sia stato specificato un gioco
if($allOk && !isset($_REQUEST['game'])){
	$homePage="Non è stato specificato alcun gioco";
	$allOk=false;
}


if($allOk){
	$gameToBeModifiedName=$_REQUEST['game'];
}

if($allOk /*&& !correctFormat(gameName) (qui devo controllare che il nel nome del gioco non siano presenti comandi malevoli)*/ && false/*questo false serve per non entrare nell'if in fase di testing*/){
	$homePage="Formato del nome del gioco non corretto";
	$allOk=false;
}
// verifico che il gioco specificato esista
if($allOk && !$game=$dbAccess->getGame($gameToBeModifiedName)){
	$homePage="Il gioco $gameToBeModifiedName specificato non esiste";
	$allOk=false;
}
	
if($allOk){
	//ora posso popolare la pagina con gli attributi del gioco
	//rinino il gioco a oldGame perchè è più chiaro nel contesto che c'è d'ora in poi
	$oldGame=$game;

	//per ora mancano le sostituzioni rigaurdanti le checkbox perchè sono complicate
	$replacements = array(
		"<game_name_ph/>" => $oldGame->getName(),
		"<developer_ph/>" => "casa di sviluppo", //non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
		"<date_ph/>" => $oldGame->getPublicationDate(),
		"<age_range_ph/>" => $oldGame->getAgeRange(),
		"<img_alt_ph/>" => "alt immagine", //non l'ho messo perchè non è detto che l'immagine esista quindi ci vuole un controllo
		"<dlc_ph/>" => "dlcs del gioco",//non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
		"<sinopsis_ph/>" => $oldGame->getSinopsis(),
		"<review_ph/>" => $oldGame->getReview()
	);

	foreach ($replacements as $key => $value) {
		$homePage=str_replace($key, $value, $homePage);
	}
	echo "replacements completati<br/>";



	// ora devo raccogliere i valori che mi sono stati passati
	// devono essere presenti tutti i valori tranne l'immagine
	// sovrascriverò i valori del gioco nel database anche se sono uguali a quelli già presenti

	//se almeno un valore, a parte l'immagine, non è settato, vuol dire che sono arrivato a questa pagina da un altra, e quindi non serve che mi metta a raccogliere i valori e a scriverli nel database

	//verifico che tutti i vaori siano settati
	//devo ancora implementare la gestione dell'alt dell'immagine
	if(!isset($_REQUEST['nome'])){
		echo "nome non inserito<br/>";
	}elseif(!isset($_REQUEST['data'])){
		echo "data non inserito<br/>";
	}elseif(!isset($_REQUEST['pegi'])){
		echo "pegi non inserito<br/>";
	}elseif(!isset($_REQUEST['descrizione'])){
		echo "descrizione non inserito<br/>";
	}elseif(!isset($_REQUEST['recensione'])){
		echo "recensione non inserito<br/>";
	}
	if(isset($_REQUEST['nome']) && isset($_REQUEST['data']) && isset($_REQUEST['pegi']) && isset($_REQUEST['descrizione']) && isset($_REQUEST['recensione'])  ){
		echo "i nuovi valori per il gioco sono stati tutti rilevati<br/>";
		//i nuovi valori per il gioco sono stati tutti rilevati
		$new_gameName=  $_REQUEST['nome'];
		$new_gamePublicationDate= $_REQUEST['data'];
		$new_gameAgeRange= $_REQUEST['pegi'];
		$new_gameSinopsis= $_REQUEST['descrizione'];
		$new_gameReview= $_REQUEST['recensione'];
	
		// l'immagine è un caso particolare: se l'utente ne inserisce una 	devo creare un oggetto che la rappresenti, altrimenti, visto che 	non è stata messa nell'html durante le sostituzioni, devo 	prendermi l'oggetto immagine di $oldGame
		$new_gameImage=null;
		if(/* metto un false perchè ho bisogno di inserire un'immagine nel form, perchè lo vuole lo script js, senza che venga rilevata dal php*/false && isset($_REQUEST['immagine'])){
			//qui devo creare il nuovo oggetto immagine, oltre che 	salvare l'immagine caricata
		}else{
			$new_gameImage=$oldGame->getImage();
		}
	
	
		$newGame=new Game($new_gameName, $new_gamePublicationDate, 2.5, $new_gameSinopsis, $new_gameAgeRange, $new_gameReview, $new_gameImage);

		$overwriteResult = $dbAccess->overwriteGame($gameToBeModifiedName, $newGame);
		echo "risultato overwrite: ".$overwriteResult;

		//lo script per ora è fatto male: ogni volta che la pagina è stata caricata sovrascrivo il gioco sul database
		//Se l'utente non ha modificato i valori sovrascrivo quelli vecchi con altri identici
	}else{
		echo "i nuovi valori per il gioco non sono stati rilevati tutti, probabilmente arrivo da un'altra pagina<br/>";
		//i nuovi valori per il gioco non sono stati rilevati tutti, ritengo quindi che l'utente sia arrivato a questa pagina da un'altra e non abbia ancora potuto inviare le modifiche (o i dati già presenti, quelli scritti con la sostituzione dei placeholder)
	}

}
			


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>