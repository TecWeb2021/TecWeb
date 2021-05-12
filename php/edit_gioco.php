


<?php
 
// una differenza tra questo script e quello per l'inserimento di un gioco nuovo sta nel fatto che qui viene accettato il "salva modifiche" anche se non è stata fornita una immagine in input, o almeno credo

//se viene cambiato il nome del gioco le relazioni con le console e i generi vengono redirette al gioco col nome cambiato, perchè ogni volta che un gioco viene modificato le sue console e generi vengono rimossi dal db e riscritti, eventualmente collegati al nuovo nome

//ATTENZIONE: questo script ha bisogno che ci sia un input nel form dell'html che invii il nome del gioco. Questo input può essere hidden.

/*lo script è strutturato così:
	- identifico il gioco che si vuole modificare
	- raccolgo tutti i valori che mi vengono passati
	  se almeno un valore mi viene passato
		- per ogni valore non passato o non corretto mostro un errore
		- se non ci sono errori eseguo il salvataggio dei dati sul db
		- faccio le sostituzioni dei placeholder così: se è stato inserito un valore valido uso quello, altrimenti uso quello del gioco selezionato prima di essere modificato
	  se nessun valore mi viene passato
	    - sostituisco tutti i placeholder con i valori del gioco selezionato
*/
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/editGiocoTemplate.html");


$originPage = getOriginPage();
echo "originPage: ".$originPage;

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
	$homePage="Non è stato specificato alcun gioco da modificare";
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

//se c'è elimina non c'è il resto quindi succede solo quello che c'è nell'if qua sotto, almeno credo
if(isset($_REQUEST['elimina'])){
	$gameToBeDeletedName = $_REQUEST["elimina"];
	echo "elimina: ".$gameToBeDeletedName."<br/>";
	$opResult = $dbAccess->deleteGame($gameToBeDeletedName);
	echo "delete result: ".$opResult."<br/>";
	if($opResult){
		$homePage = "eliminazione del gioco $gameToBeDeletedName riuscita";
	}else{
		$homePage = "eliminazione del gioco $gameToBeDeletedName fallita";
	}
}

$oldGame = null;
	
if($allOk){
	//ora posso popolare la pagina con gli attributi del gioco
	//rinomino il gioco a oldGame perchè è più chiaro nel contesto che c'è d'ora in poi
	$oldGame = $game;

	// ora devo raccogliere i valori che mi sono stati passati
	// devono essere presenti tutti i valori tranne l'immagine
	// sovrascriverò i valori del gioco nel database anche se sono uguali a quelli già presenti

	//se almeno un valore, a parte l'immagine, non è settato, vuol dire che sono arrivato a questa pagina da un altra, e quindi non serve che mi metta a raccogliere i valori e a scriverli nel database

	//verifico che tutti i valori siano settati
	//devo ancora implementare la gestione dell'alt dell'immagine
	if(isset($_REQUEST['nome']) || isset($_REQUEST['data']) || isset($_REQUEST['pegi']) || isset($_REQUEST['descrizione']) || isset($_REQUEST['recensione']) || isset($_REQUEST['alternativo1']) || isset($_REQUEST['alternativo2']) || isset($_REQUEST['voto']) || isset($_REQUEST['prequel']) || isset($_REQUEST['sequel']) || isset($_REQUEST['sviluppo'])){
		echo "almeno un valore è stato rilevato<br/>";
		
		$new_gameName = isset($_REQUEST['nome']) ? $_REQUEST['nome'] : null;
		$new_gamePublicationDate = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;
		$new_gameAgeRange = isset($_REQUEST['pegi']) ? $_REQUEST['pegi'] : null;
		$new_gameSinopsis = isset($_REQUEST['descrizione']) ? $_REQUEST['descrizione'] : null;
		$new_gameReview = isset($_REQUEST['recensione']) ? $_REQUEST['recensione'] : null;
		$new_gameAlt = isset($_REQUEST['alternativo']) ? $_REQUEST['alternativo'] : null;
		$new_gameVote = isset($_REQUEST['voto']) ? $_REQUEST['voto'] : null;

		$new_gameConsoles = isset($_REQUEST['console']) ? $_REQUEST['console'] : array();
		$new_gameGenres = isset($_REQUEST['genere']) ? $_REQUEST['genere'] : array();

		$new_gamePrequel = isset($_REQUEST['prequel']) ? $_REQUEST['prequel'] : null;
		$new_gameSequel = isset($_REQUEST['sequel']) ? $_REQUEST['sequel'] : null;
		$new_gameDeveloper = isset($_REQUEST['sviluppo']) ? $_REQUEST['sviluppo'] : null;

		$new_gameImage1 = null;
		$new_gameImage2 = null;

		$image1Ok = false;
		$image2Ok = false;
		//error 4: non è stata caricata alcuna immagine
		if(isset($_FILES['immagine']) && $_FILES['immagine']['error'] != 4){
			echo "rilevato campo immagine"."<br/>";
			//prendo l'immagine inserita dall'utente
			$imagePath = saveImageFromFILES($dbAccess, "immagine");
			if($imagePath){
				echo "Salvataggio immagine riuscito nel percorso:".$imagePath."<br/>";
				$new_gameImage1 = new Image($imagePath,$new_gameAlt);
				$image1Ok=true;
				
			}else{
				echo "Salvataggio immagine fallito"."<br/>";
			}
		}

		//error 4: non è stata caricata alcuna immagine
		if(isset($_FILES['immagine']) && $_FILES['immagine']['error'] != 4){
			echo "rilevato campo immagine"."<br/>";
			//prendo l'immagine inserita dall'utente
			$imagePath = saveImageFromFILES($dbAccess, "immagine");
			if($imagePath){
				echo "Salvataggio immagine riuscito nel percorso:".$imagePath."<br/>";
				$new_gameImage2 = new Image($imagePath,$new_gameAlt);
				$image2Ok=true;
				
			}else{
				echo "Salvataggio immagine fallito"."<br/>";
			}
		}
		
		

		$error_messages = array(
			'nome' => "Nome non inserito",
			'data' => "Data non inserita",
			'pegi' => "Pegi non inserito",
			'descrizione' => "descrizione non inserita",
			'recensione' => "Recensione non inserita",
			'immagine1' => "Immagine1 non inserita",
			'immagine2' => "Immagine2 non inserita",
			'alternativo' => "Testo alternativo dell'immaagine non inserito",
			'voto' => "Voto non inserito",
			'console' => "Console non inserita",
			'genere' => "Genere non inserito",
			'prequel' => "Prequel non inserito",
			'sequel' => "Sequel non inserito",
			'sviluppo' => "Sviluppatore non inserito"
		);

		$error_message = "";

		// controllo i campi obbligatori

		if($new_gameName === null || ($errorText = checkString($new_gameName,'nome')) !== true ){
			$error_message = $error_message . $error_messages['nome'] . "<br/>";
		}
		if($new_gameDeveloper === null || ($errorText = checkString($new_gameDeveloper,'sviluppo')) !== true){
			$error_message = $error_message . $error_messages['sviluppo'] . "<br/>";
		}
		if($new_gameAgeRange === null || ($errorText = checkString($new_gameAgeRange,'pegi')) !== true){
			$error_message = $error_message . $error_messages['pegi'] . "<br/>";
		}
		if($new_gamePublicationDate === null || ($errorText = checkString($new_gamePublicationDate,'data')) !== true){
			$error_message = $error_message . $error_messages['data'] . "<br/>";
		}
		if(count($new_gameConsoles) === 0){
			$error_message = $error_message . $error_messages['console'] . "<br/>";
		}
		if(count($new_gameGenres) === 0){
			$error_message = $error_message . $error_messages['genere'] . "<br/>";
		}
		if($new_gameVote === null || ($errorText = checkString($new_gameVote,'voto')) !== true){
			$error_message = $error_message . $error_messages['voto'] . "<br/>";
		}
		if($new_gameSinopsis === null || ($errorText = checkString($new_gameSinopsis,'descrizione')) !== true){
			$error_message = $error_message . $error_messages['descrizione'] . "<br/>";
		}

		// controllo i campi obbligatori derivati

		// controllo i campi opzionali

		if($new_gameImage1 !== null && $image1Ok === false){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}
		if($new_gameImage2 !== null && $image2Ok === false){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}
		if($new_gamePrequel !== null && strlen($new_gamePrequel) > 0 && ($errorText = checkString($new_gamePrequel, 'prequel')) !== true){
			$error_message = $error_message . $error_messages['prequel'] . "<br/>";
		}
		if($new_gameSequel !== null && strlen($new_gameSequel) > 0 &&($errorText = checkString($new_gameSequel, 'sequel')) !== true){
			$error_message = $error_message . $error_messages['sequel'] . "<br/>";
		}

		if($new_gameReview !== null && strlen($new_gameReview) > 0 && ($errorText = checkString($new_gameReview, 'recensione')) !== true){
			$error_message = $error_message . $error_messages['recensione'] . "<br/>";
		}
		
		if($new_gameAlt !== null && strlen($new_gameAlt) > 0 && ($errorText = checkString($new_gameAlt, 'alternativo')) !== true){
			$error_message = $error_message . $error_messages['alternativo'] . "<br/>";
		}
		

		if($error_message != ""){// sono presenti errori
			$homePage = str_replace("<messaggi_form_ph/>", $error_message, $homePage);
		}else{
			// l'immagine è un caso particolare: se l'utente ne inserisce una 	devo creare un oggetto che la rappresenti, altrimenti, visto che 	non è stata messa nell'html durante le sostituzioni, devo 	prendermi l'oggetto immagine di $oldGame
			$new_gameImage1 = null;
			$new_gameImage2 = null;
			
			
			
			if($new_gameImage1 == null){
				echo "campo immagine1 non rilevato"."<br/>";
				//prendo l'immagine già presente per il gioco prima delle modifiche
				$new_gameImage1 = $oldGame->getImage1();
				$image1Ok = true;
			}
			if($new_gameImage2 == null){
				echo "campo immagine2 non rilevato"."<br/>";
				//prendo l'immagine già presente per il gioco prima delle modifiche
				$new_gameImage2 = $oldGame->getImage2();
				$image2Ok = true;
			}
			
			if($image1Ok && $image2Ok){
			
				$newGame=new Game($new_gameName, $new_gamePublicationDate, $new_gameVote, $new_gameSinopsis, $new_gameAgeRange, $new_gameReview, $new_gameImage1, $new_gameImage2, $new_gameConsoles, $new_gameGenres, $new_gamePrequel, $new_gameSequel, $new_gameDeveloper);
	
				$overwriteResult = $dbAccess->overwriteGame($gameToBeModifiedName, $newGame);
				echo "risultato overwrite: ".($overwriteResult==null ? "null" : $overwriteResult)."<br/>";
			}
		}
	
		
		$selected_consoles = array();

		//se non sono state selezionate console uso quelle del vecchio gioco
		if(count($new_gameConsoles) == 0){
			$new_gameConsoles = $oldGame->getConsoles();
		}
		//creao un array che per ogni posizione indica se la console in quella posizione è stata selezionata
		foreach (Game::$possible_consoles as $key => $value) {
			$selected_consoles[$key] = in_array($value, $new_gameConsoles);
		}

		
		$selected_genres=array();

		//se non sono stati selezionati generi uso quelli del vecchio gioco
		if(count($new_gameGenres) == 0){
			$new_gameGenres = $oldGame->getGenres();
		}
		//creao un array che per ogni posizione indica se il genere in quella posizione è stato selezionato
		foreach (Game::$possible_genres as $key => $value) {
			$selected_genres[$key] = in_array($value, $new_gameGenres);
		}


		// se sono stati inseriti valori accettabili li sostituisco ai placeholder, altrimenti ci metto i vecchi valori
		$replacements = array(
			"<game_name_ph/>" => $new_gameName ? $new_gameName : $oldGame->getName(),
			"<developer_ph/>" => $new_gameDeveloper ? $new_gameDeveloper : $oldGame->getDeveloper(),
			"<date_ph/>" => $new_gamePublicationDate ? $new_gamePublicationDate : $oldGame->getPublicationDate(),
			"<age_range_ph/>" => $new_gameAgeRange ? $new_gameAgeRange : $oldGame->getAgeRange(),
			"<img1_alt_ph/>" => $new_gameAlt ? $new_gameAlt : $oldGame->getAlt(), //non l'ho messo perchè non è detto che l'immagine esista quindi ci vuole un controllo
			"<vote_ph/>" => $new_gameVote ? $new_gameVote : $oldGame->getVote(),
			"<dlc_ph/>" => "dlcs del gioco",//non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<sinopsis_ph/>" => $new_gameSinopsis ? $new_gameSinopsis : $oldGame->getSinopsis(),
			"<review_ph/>" => $new_gameReview ? $new_gameReview  : $oldGame->getReview(),
			"<prequel_ph/>" => $new_gamePrequel ? $new_gamePrequel : $oldGame->getPrequel(),
			"<sequel_ph/>" => $new_gameSequel ? $new_gameSequel : $oldGame->getSequel(),

			"<opzioni_prequel_ph/>" => createGamesOptions($dbAccess),
			"<opzioni_sequel_ph/>" => createGamesOptions($dbAccess)
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
		//Non controllo che l'utente abbia inserito valori diversi da quelli preesistenti
	}else{
		echo "nessu valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";
		//i nuovi valori per il gioco non sono stati rilevati tutti, ritengo quindi che l'utente sia arrivato a questa pagina da un'altra e non abbia ancora potuto inviare le modifiche (o i dati già presenti, quelli scritti con la sostituzione dei placeholder)

		/*
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
		}elseif(!isset($_REQUEST['prequel'])){
			echo "prequel non inserita<br/>";
		}elseif(!isset($_REQUEST['sequel'])){
			echo "sequel non inserita<br/>";
		}elseif(!isset($_REQUEST['sviluppo'])){
			echo "sviluppo non inserita<br/>";
		}
		*/


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
			"<developer_ph/>" => $oldGame->getDeveloper(),
			"<date_ph/>" => $oldGame->getPublicationDate(),
			"<vote_ph/>" => $oldGame->getVote(),
			"<age_range_ph/>" => $oldGame->getAgeRange(),
			"<dlc_ph/>" => "dlcs del gioco",//non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<sinopsis_ph/>" => $oldGame->getSinopsis(),
			"<review_ph/>" => $oldGame->getReview(),
			"<prequel_ph/>" => $oldGame->getPrequel(),
			"<sequel_ph/>" => $oldGame->getSequel(),
			"<opzioni_prequel_ph/>" => createGamesOptions($dbAccess),
			"<opzioni_sequel_ph/>" => createGamesOptions($dbAccess)
		);

		//aggiungo ai replacement quelli delle checkboxes
		foreach ($selected_consoles as $key => $value) {
			$replacements["<checked_console_".$key."/>"] = $value ? "checked=\"checked\"" : "";
		}
		foreach ($selected_genres as $key => $value) {
			$replacements["<checked_genere_".$key."/>"] = $value ? "checked=\"checked\"" : "";
		}

		//se il vecchio gioco aveva un immagine inserisco il suo alt nel campo di input per l'alt
		if($oldImage = $oldGame->getImage()){
			$replacements["<img_alt_ph/>"] = $oldImage->getAlt();
		}
	
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
		echo "replacements completati<br/>";
	}

}
			


$basePage = createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $oldGame ? $oldGame->getName() : "");

$basePage = str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage = replace($basePage);

echo $basePage;

?>