


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
	$homePage = getErrorHtml("not_logged");
	$authCheck=false;
}
if($authCheck && !$user->isAdmin()){
	$homePage = getErrorHtml("not_admin");
	$authCheck=false;
}



//allOk prende in carico le prossime verifiche e parte dal valore di $authCheck
$allOk=$authCheck;
// verifico che sia stato specificato un gioco
if($allOk && getSafeInput('game', 'string') === null ){
	$homePage= getErrorHtml("game_not_specified");
	$allOk=false;
}


if($allOk){
	$gameToBeModifiedName = getSafeInput('elimina', 'string');
}

if($allOk /*&& !correctFormat(gameName) (qui devo controllare che il nel nome del gioco non siano presenti comandi malevoli)*/ && false/*questo false serve per non entrare nell'if in fase di testing*/){
	$homePage="Formato del nome del gioco non corretto";
	$allOk=false;
}
// verifico che il gioco specificato esista
if($allOk && !$game=$dbAccess->getGame($gameToBeModifiedName)){
	$homePage=getErrorHtml("game_not_existent");
	$allOk=false;
}

//se c'è elimina non c'è il resto quindi succede solo quello che c'è nell'if qua sotto, almeno credo
if(($gameToBeDeletedName = getSafeInput('elimina', 'string')) !== null){
	echo "elimina: ".$gameToBeDeletedName."<br/>";
	$opResult = $dbAccess->deleteGame($gameToBeDeletedName);
	echo "delete result: ".$opResult."<br/>";
	if($opResult){
		$homePage = getErrorHtml("game_deleted");
	}else{
		$homePage = "eliminazione del gioco $gameToBeDeletedName fallita";
	}
}

$oldGame = null;

$validation_error_messages = array();
$success_messages = array();
$failure_messages = array();
	
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
		
		$new_gameName = getSafeInput('nome', 'string');
		$new_gamePublicationDate = getSafeInput('data');
		$new_gameAgeRange = getSafeInput('pegi');
		$new_gameSinopsis = getSafeInput('descrizione', 'string');
		$new_gameReview = getSafeInput('recensione', 'string');
		$new_gameLast_review_date = date("Y-m-d");
		$new_gameReview_author = $user;
		$new_gameAlt1 = getSafeInput('alternativo1', 'string');
		$new_gameAlt2 = getSafeInput('alternativo2', 'string');
		$new_gameVote = getSafeInput('voto');

		$new_gameConsoles = getSafeInput('console');
		$new_gameGenres = getSafeInput('genere');

		$new_gamePrequel = getSafeInput('prequel', 'string');
		$new_gameSequel = getSafeInput('sequel', 'string');
		$new_gameDeveloper = getSafeInput('sviluppo', 'string');

		$new_gameImage1 = null;
		$new_gameImage2 = null;

		$image1Ok = false;
		$image2Ok = false;
		//error 4: non è stata caricata alcuna immagine
		if(isset($_FILES['immagine1']) && $_FILES['immagine1']['error'] != 4){
			echo "rilevato campo immagine1"."<br/>";
			//prendo l'immagine inserita dall'utente
			$imagePath = saveImageFromFILES($dbAccess, "immagine1", Game::$img1MinRatio, Game::$img1MaxRatio);
			if($imagePath){
				echo "Salvataggio immagine1 riuscito nel percorso:".$imagePath."<br/>";
				$new_gameImage1 = new Image($imagePath,$new_gameAlt1);
				$dbAccess->addImage($new_gameImage1);
				$image1Ok=true;
				
			}else{
				echo "Salvataggio immagine fallito"."<br/>";
			}
		}

		//error 4: non è stata caricata alcuna immagine
		if(isset($_FILES['immagine2']) && $_FILES['immagine2']['error'] != 4){
			echo "rilevato campo immagine2"."<br/>";
			//prendo l'immagine inserita dall'utente
			$imagePath = saveImageFromFILES($dbAccess, "immagine2", Game::$img2MinRatio, Game::$img2MaxRatio);
			if($imagePath){
				echo "Salvataggio immagine2 riuscito nel percorso:".$imagePath."<br/>";
				$new_gameImage2 = new Image($imagePath,$new_gameAlt2);
				$dbAccess->addImage($new_gameImage2);
				$image2Ok=true;
				
			}else{
				echo "Salvataggio immagine fallito"."<br/>";
			}
		}
		


		// controllo i campi obbligatori

		$mandatory_fields = array(
			[$new_gameName,'nome'],
			[$new_gameDeveloper,'sviluppo'],
			[$new_gameAgeRange,'pegi'],
			[$new_gamePublicationDate,'data'],
			[$new_gameConsoles, 'consoles'],
			[$new_gameGenres, 'genres'],
			[$new_gameVote,'voto'],
			[$new_gameSinopsis,'descrizione']
		);
		foreach ($mandatory_fields as $value) {
			if($value[0] === null || validateValue($value[0], $value[1]) === false ){
				array_push($validation_error_messages, getValidationError($value[1]));
			}
		}

		// controllo i campi obbligatori derivati

		// controllo i campi opzionali

		/*
		if($new_gameImage1 !== null && $image1Ok === false){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}
		if($new_gameImage2 !== null && $image2Ok === false){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}*/

		$optional_fields = array(
			[$new_gamePrequel, 'prequel'],
			[$new_gameSequel, 'sequel'],
			[$new_gameReview, 'recensione'],
			[$new_gameAlt1, 'alternativo'],
			[$new_gameAlt2, 'alternativo']
		);
		foreach ($optional_fields as $value) {
			if($value[0] !== null && validateValue($value[0], $value[1]) === false ){
				array_push($validation_error_messages, getValidationError($value[1]));
			}
		}
		

		if(count($validation_error_messages) > 0){// sono presenti errori
			
		}else{
			// l'immagine è un caso particolare: se l'utente ne inserisce una 	devo creare un oggetto che la rappresenti, altrimenti, visto che 	non è stata messa nell'html durante le sostituzioni, devo 	prendermi l'oggetto immagine di $oldGame
			
			
			
			if($new_gameImage1 === null){
				echo "campo immagine1 non rilevato"."<br/>";
				//prendo l'immagine già presente per il gioco prima delle modifiche
				$new_gameImage1 = $oldGame->getImage1();
				$image1Ok = true;
			}
			if($new_gameImage2 === null){
				echo "campo immagine2 non rilevato"."<br/>";
				//prendo l'immagine già presente per il gioco prima delle modifiche
				$new_gameImage2 = $oldGame->getImage2();
				$image2Ok = true;
			}
			
			if($image1Ok && $image2Ok){

				

				$newGame = new Game($new_gameName, $new_gamePublicationDate, $new_gameVote, $new_gameSinopsis, $new_gameAgeRange, $new_gameImage1, $new_gameImage2, $new_gameConsoles, $new_gameGenres, $new_gamePrequel, $new_gameSequel, $new_gameDeveloper);
				
				$opResult1 = $dbAccess->overwriteGame($gameToBeModifiedName, $newGame);
				echo "risultato overwrite gioco false? ".($opResult1===false ? "yes" : "no")."<br/>";

				

				if($opResult1 === true || $opResult1 === null){
					array_push($success_messages, "Modifica gioco riuscita");
					$newGameReviewObj = null;
					$opResult2 = null;
					if($new_gameReview !== "" && $new_gameReview !== null){
						echo "inserting non empty review<br/>";
						if($dbAccess->getReview($new_gameName) !== null){
							$newGameReviewObj = new Review($new_gameName, $new_gameReview_author->getUsername(), $new_gameLast_review_date, $new_gameReview);
							$opResult2 = $dbAccess->overwriteReview($new_gameName, $newGameReviewObj);
						}else{
							$newGameReviewObj = new Review($new_gameName, $new_gameReview_author->getUsername(), $new_gameLast_review_date, $new_gameReview);
							$opResult2 = $dbAccess->addReview($newGameReviewObj);
						}
						
					}else{
						echo "inserting empty review<br/>";
						$opResult2 = $dbAccess->deleteReview($new_gameName, $oldGame->getName());
						$newGameReviewObj = null;
					}
					echo "review overwrite result is null? " . ($opResult2 === null ? "yes" :  "no") . "<br/>";
					if($opResult2 === true || $opResult2 === null){
						// header("Location: giochi.php");	
						array_push($success_messages, "Modifica recensione riuscita");
					}else{
						array_push($failure_messages, "Modifica recensione fallita");
					}
				}else{
					array_push($failure_messages, "Modifica gioco fallita");
				}
					
			
				
	
				
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

		$oldGameReviewObj = $dbAccess->getReview($oldGame->getName());
		$oldGameReview = $oldGameReviewObj ? $oldGameReviewObj->getContent() : "";


		// se sono stati inseriti valori accettabili li sostituisco ai placeholder, altrimenti ci metto i vecchi valori
		$replacements = array(
			"<game_name_ph/>" => $new_gameName ? $new_gameName : $oldGame->getName(),
			"<developer_ph/>" => $new_gameDeveloper ? $new_gameDeveloper : $oldGame->getDeveloper(),
			"<date_ph/>" => $new_gamePublicationDate ? $new_gamePublicationDate : $oldGame->getPublicationDate(),
			"<age_range_ph/>" => $new_gameAgeRange ? $new_gameAgeRange : $oldGame->getAgeRange(),
			"<img1_alt_ph/>" => $new_gameAlt1 ? $new_gameAlt1 : $oldGame->getImage1()->getAlt(), 
			"<img2_alt_ph/>" => $new_gameAlt2 ? $new_gameAlt2 : $oldGame->getImage2()->getAlt(), 
			"<vote_ph/>" => $new_gameVote ? $new_gameVote : $oldGame->getVote(),
			"<dlc_ph/>" => "dlcs del gioco",//non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<sinopsis_ph/>" => $new_gameSinopsis ? $new_gameSinopsis : $oldGame->getSinopsis(),
			"<review_ph/>" => $new_gameReview ? $new_gameReview  : $oldGameReview,
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
		
		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
		echo "replacements completati<br/>";

		//lo script per ora è fatto male: ogni volta che la pagina è stata caricata sovrascrivo il gioco sul database
		//Non controllo che l'utente abbia inserito valori diversi da quelli preesistenti
	}else{
		echo "nessun valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";


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

		$oldGameReviewObj = $dbAccess->getReview($oldGame->getName());
		$oldGameReview = $oldGameReviewObj ? $oldGameReviewObj->getContent() : "";

		//per ora mancano le sostituzioni rigaurdanti le checkbox perchè sono complicate
		$replacements = array(
			"<game_name_ph/>" => $oldGame->getName(),
			"<developer_ph/>" => $oldGame->getDeveloper(),
			"<date_ph/>" => $oldGame->getPublicationDate(),
			"<vote_ph/>" => $oldGame->getVote(),
			"<age_range_ph/>" => $oldGame->getAgeRange(),
			"<sinopsis_ph/>" => $oldGame->getSinopsis(),
			"<review_ph/>" => $oldGameReview,
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

		//se il vecchio gioco aveva un immagine1 inserisco il suo alt nel campo di input per l'alt
		if($oldImage1 = $oldGame->getImage1()){
			$replacements["<img1_alt_ph/>"] = $oldImage1->getAlt();
		}

		//se il vecchio gioco aveva un immagine2 inserisco il suo alt nel campo di input per l'alt
		if($oldImage2 = $oldGame->getImage2()){
			$replacements["<img2_alt_ph/>"] = $oldImage2->getAlt();
		}
		
		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
		echo "replacements completati<br/>";
	}

}

$jointValidation_error_message = getValidationErrorsHtml($validation_error_messages);
$jointSuccess_messages = getSuccessMessagesHtml($success_messages);
$jointFailure_messages = getFailureMessagesHtml($failure_messages);
$homePage = str_replace("<messaggi_form_ph/>", $jointValidation_error_message . "\n" . $jointSuccess_messages . "\n" . $jointFailure_messages, $homePage);
			


$basePage = createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $oldGame ? $oldGame->getName() : "");

$basePage = str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage = replace($basePage);

echo $basePage;

?>