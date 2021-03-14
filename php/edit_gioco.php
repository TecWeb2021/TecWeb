


<?php
 
// una differenza tra questo script e quello per l'inserimento di un gioco nuovo sta nel fatto che qui viene accettato il "salva modifiche" anche se non è stata fornita una immagine in input, o almeno credo

//se viene cambiato il nome del gioco le relazioni con le console e i generi vengono redirette al gioco col nome cambiato, perchè ogni volta che un gioco viene modificato le sue console e generi vengono rimossi dal db e riscritti, eventualmente collegati al nuovo nome

//ATTENZIONE: questo script ha bisogno che ci sia un input nel form dell'html che invii il nome del gioco. Questo input può essere hidden.

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
	//rinomino il gioco a oldGame perchè è più chiaro nel contesto che c'è d'ora in poi
	$oldGame=$game;

	// ora devo raccogliere i valori che mi sono stati passati
	// devono essere presenti tutti i valori tranne l'immagine
	// sovrascriverò i valori del gioco nel database anche se sono uguali a quelli già presenti

	//se almeno un valore, a parte l'immagine, non è settato, vuol dire che sono arrivato a questa pagina da un altra, e quindi non serve che mi metta a raccogliere i valori e a scriverli nel database

	//verifico che tutti i valori siano settati
	//devo ancora implementare la gestione dell'alt dell'immagine
	if(isset($_REQUEST['nome']) && isset($_REQUEST['data']) && isset($_REQUEST['pegi']) && isset($_REQUEST['descrizione']) && isset($_REQUEST['recensione']) && isset($_REQUEST['alternativo']) && isset($_REQUEST['voto']) ){
		echo "i nuovi valori per il gioco sono stati tutti rilevati<br/>";
		//i nuovi valori per il gioco sono stati tutti rilevati
		$new_gameName = $_REQUEST['nome'];
		$new_gamePublicationDate = $_REQUEST['data'];
		$new_gameAgeRange = $_REQUEST['pegi'];
		$new_gameSinopsis = $_REQUEST['descrizione'];
		$new_gameReview = $_REQUEST['recensione'];
		$new_gameAlt = $_REQUEST['alternativo'];
		$new_gameVote = $_REQUEST['voto'];
		$new_gameConsoles = isset($_REQUEST['console']) ? $_REQUEST['console'] : array();
		$new_gameGenres = isset($_REQUEST['genere']) ? $_REQUEST['genere'] : array();
		echo "console: ";
		print_r($new_gameConsoles);
		echo "<br/>";
		echo "generi: ";
		print_r($new_gameGenres);
		echo "<br/>";


		
		$selected_consoles=array();
		//creao un array che per ogni posizione indica se la console in quella posizione è stata selezionata
		foreach (Game::$possible_consoles as $key => $value) {

			$selected_consoles[$key] = in_array($value, $new_gameConsoles);
			echo "$value is ".($selected_consoles[$key] ? "true" : "false")."<br/>";
		}

		
		$selected_genres=array();
		//creao un array che per ogni posizione indica se il genere in quella posizione è stato selezionato
		foreach (Game::$possible_genres as $key => $value) {
			$selected_genres[$key] = in_array($value, $new_gameGenres);
		}
		
	
		// l'immagine è un caso particolare: se l'utente ne inserisce una 	devo creare un oggetto che la rappresenti, altrimenti, visto che 	non è stata messa nell'html durante le sostituzioni, devo 	prendermi l'oggetto immagine di $oldGame
		$new_gameImage=null;
		$imageOk=false;
		
		if(isset($_FILES['immagine'])){
			echo "rilevato campo immagine"."<br/>";
			//prendo l'immagine inserita dall'utente
			$imagePath = saveImageFromFILES($dbAccess, "immagine");
			if($imagePath){
				echo "Salvataggio immagine riuscito nel percorso:".$imagePath."<br/>";
				$new_gameImage = new Image($imagePath,$new_gameAlt);
				$imageOk=true;
				
			}else{
				echo "Salvataggio immagine fallito"."<br/>";
			}
		}else{
			echo "campo immagine non rilevato"."<br/>";
			//prendo l'immagine già presente per il gioco prima delle modifiche
			$new_gameImage=$oldGame->getImage();
			$imageOk=true;
		}
		
		if($imageOk){
		
			$newGame=new Game($new_gameName, $new_gamePublicationDate, $new_gameVote, $new_gameSinopsis, $new_gameAgeRange, $new_gameReview, $new_gameImage, $new_gameConsoles, $new_gameGenres);

			$overwriteResult = $dbAccess->overwriteGame($gameToBeModifiedName, $newGame);
			echo "risultato overwrite: ".($overwriteResult==null ? "null" : $overwriteResult)."<br/>";
		}

		$replacements = array(
			"<game_name_ph/>" => $new_gameName,
			"<developer_ph/>" => "casa di sviluppo", //non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<date_ph/>" => $new_gamePublicationDate,
			"<age_range_ph/>" => $new_gameAgeRange,
			"<img_alt_ph/>" => $new_gameAlt, //non l'ho messo perchè non è detto che l'immagine esista quindi ci vuole un controllo
			"<vote_ph/>" => $new_gameVote,
			"<dlc_ph/>" => "dlcs del gioco",//non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<sinopsis_ph/>" => $new_gameSinopsis,
			"<review_ph/>" => $new_gameReview
		);

		//aggiungo ai replacement quelli delle checkboxes
		foreach ($selected_consoles as $key => $value) {
			$replacements["<checked_console_".$key."/>"] = $value ? "checked=\"checked\"" : "";
		}
		foreach ($selected_genres as $key => $value) {
			$replacements["<checked_genere_".$key."/>"] = $value ? "checked=\"checked\"" : "";
		}
	
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
		echo "replacements completati<br/>";

		//lo script per ora è fatto male: ogni volta che la pagina è stata caricata sovrascrivo il gioco sul database
		//Se l'utente non ha modificato i valori sovrascrivo quelli vecchi con altri identici
	}else{
		echo "i nuovi valori per il gioco non sono stati rilevati tutti, probabilmente arrivo da un'altra pagina<br/>";
		//i nuovi valori per il gioco non sono stati rilevati tutti, ritengo quindi che l'utente sia arrivato a questa pagina da un'altra e non abbia ancora potuto inviare le modifiche (o i dati già presenti, quelli scritti con la sostituzione dei placeholder)

		// controllo quale valore non è stato inserito
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
		}elseif(!isset($_REQUEST['alternativo'])){
			echo "alt non inserito<br/>";
		}elseif(!isset($_REQUEST['voto'])){
			echo "voto non inserito<br/>";
		}

		$old_gameConsoles = $oldGame->getConsoles();
		$old_gameGenres = $oldGame->getGenres();

		$selected_consoles=array();
		//creao un array che per ogni posizione indica se la console in quella posizione è stata selezionata
		foreach (Game::$possible_consoles as $key => $value) {
			$selected_consoles[$key] = $old_gameConsoles ? in_array($value, $old_gameConsoles) : false;
		}

		
		$selected_genres=array();
		//creao un array che per ogni posizione indica se il genere in quella posizione è stato selezionato
		foreach (Game::$possible_genres as $key => $value) {
			$selected_genres[$key] = $old_gameGenres ? in_array($value, $old_gameGenres) : false;
		}

		//per ora mancano le sostituzioni rigaurdanti le checkbox perchè sono complicate
		$replacements = array(
			"<game_name_ph/>" => $oldGame->getName(),
			"<developer_ph/>" => "casa di sviluppo", //non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<date_ph/>" => $oldGame->getPublicationDate(),
			"<vote_ph/>" => $oldGame->getVote(),
			"<age_range_ph/>" => $oldGame->getAgeRange(),
			"<dlc_ph/>" => "dlcs del gioco",//non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<sinopsis_ph/>" => $oldGame->getSinopsis(),
			"<review_ph/>" => $oldGame->getReview()
		);

		//aggiungo ai replacement quelli delle checkboxes
		foreach ($selected_consoles as $key => $value) {
			$replacements["<checked_console_".$key."/>"] = $value ? "checked=\"checked\"" : "";
		}
		foreach ($selected_genres as $key => $value) {
			$replacements["<checked_genere_".$key."/>"] = $value ? "checked=\"checked\"" : "";
		}

		//se il vecchio gioco aveva un immagine inserisco il suo alt nel campo di input per l'alt
		if($oldImage=$oldGame->getImage()){
			$replacements["<img_alt_ph/>"] = $oldImage->getAlt();
		}
	
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
		echo "replacements completati<br/>";
	}

}
			


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>