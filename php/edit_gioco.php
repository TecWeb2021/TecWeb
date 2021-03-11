


<?php
 
// una differenza tra questo script e quello per l'inserimento di un gioco nuovo sta nel fatto che qui viene accettato il "salva modifiche" anche se non è stata fornita una immagine in input, o almeno credo

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


$gameToBeModifiedName=$_REQUEST['game'];

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


	// ora devo raccogliere i valori che mi sono stati passati
	// devono essere presenti tutti i valori tranne l'immagine
	// sovrascriverò i valori del gioco nel database anche se sono uguali a quelli già presenti
	$new_gameName= isset($_REQUEST['']) ? $_REQUEST[''] : null;
	$new_gamePublicationDate= isset($_REQUEST['']) ? $_REQUEST[''] : null;
	$new_gameAgeRange= isset($_REQUEST['']) ? $_REQUEST[''] : null;
	$new_gameSinopsis= isset($_REQUEST['']) ? $_REQUEST[''] : null;
	$new_gameReview= isset($_REQUEST['']) ? $_REQUEST[''] : null;

	// l'immagine è un caso particolare: se l'utente ne inserisce una devo creare un oggetto che la rappresenti, altrimenti, visto che non è stata messa nell'html durante le sostituzioni, devo prendermi l'oggetto immagine di $oldGame
	$new_gameImage=null;
	if(isset($_REQUEST['immagine'])){
		//qui devo creare il nuovo oggetto immagine, oltre che salvare l'immagine caricata
	}else{
		
	}

	$newGame=new Game($new_gameName, $new_gamePublicationDate, 2.5, $new_gameSinopsis, $new_gameAgeRange, $new_gameReview)
}
			


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>