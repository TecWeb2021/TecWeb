


<?php

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/formGiocoTemplate.html");




// verifico che l'utente abbia l'autorizzazione per inserire un nuovo gioco
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

$validation_error_messages = array();
$success_messages = array();
$failure_messages = array();

	
if($allOk){

	//verifico che un valore testuale qualsiasi sia settato
	if(isset($_REQUEST['nome'])){

		// echo "almeno un valore è stato rilevato<br/>";

		$new_gameName = getSafeInput('nome', 'string');
		$new_gameDeveloper = getSafeInput('sviluppo', 'string');
		$new_gameAgeRange = getSafeInput('pegi');
		$new_gamePublicationDate = getSafeInput('data');

		$new_gameConsoles = getSafeInput('console');
		$new_gameGenres = getSafeInput('genere');

		$new_gameAlt1 = getSafeInput('alternativo1', 'string');
		$new_gameAlt2 = getSafeInput('alternativo2', 'string');
		$new_gameVote = getSafeInput('voto');
		$new_gamePrequel = getSafeInput('prequel', 'string');
		$new_gameSequel = getSafeInput('sequel', 'string');
		$new_gameSinopsis = getSafeInput('descrizione', 'string');
		$new_gameReview = getSafeInput('recensione', 'string');
		$new_gameLast_review_date = date("Y-m-d");
		$new_gameReview_author = $user->getUsername();


		$imagePath1 = getSafeInput('immagine1', 'image', $dbAccess);
		$imagePath2 = getSafeInput('immagine2', 'image', $dbAccess, 1);


		// controllo i campi obbligatori

		$mandatory_fields = array(
			[$new_gameName, 'nome'],
			[$new_gameDeveloper, 'sviluppo'],
			[$new_gameAgeRange, 'pegi'],
			[$new_gamePublicationDate, 'data'],
			[$new_gameConsoles, 'consoles'],
			[$new_gameGenres, 'genres'],
			[$new_gameVote, 'voto'],
			[$new_gameSinopsis, 'descrizione']
		);

		foreach ($mandatory_fields as $value) {
			if($value[0] === null || validateValue($value[0], $value[1]) === false ){
				array_push($validation_error_messages, getValidationError($value[1]));
			}
		}

		if( $imagePath1 === null){
			array_push($validation_error_messages, getValidationError("immagine"));
		}
		if( $imagePath1 !== null && validateValue($imagePath1,"immagine1_gioco_ratio") === false){
			// echo "validating imagePath1 <br/>";
			array_push($validation_error_messages, getValidationError("immagine1_gioco_ratio"));
		}

		if( $imagePath2 === null){
			array_push($validation_error_messages, getValidationError("immagine"));
		}
		if( $imagePath2 !== null && validateValue($imagePath2,"immagine2_gioco_ratio") === false){
			array_push($validation_error_messages, getValidationError("immagine2_gioco_ratio"));
		}

		// controllo i campi obbligatori derivati

		// controllo i campi opzionali

		$optional_fields = array(
			[$new_gamePrequel, 'prequel'],
			[$new_gamePrequel, 'gioco_esistente'],
			[$new_gameSequel, 'sequel'],
			[$new_gameSequel, 'gioco_esistente'],
			[$new_gameReview, 'recensione'],
			[$new_gameAlt1, 'alternativo'],
			[$new_gameAlt2, 'alternativo']
		);

		foreach ($optional_fields as $value) {
			if($value[0] !== null && validateValue($value[0], $value[1], $dbAccess) === false ){
				array_push($validation_error_messages, getValidationError($value[1]));
			}
		}
		

		//inizializzo questi due array che, anche se vuoti, mi serviranno più avanti
		$selected_consoles=array();
		$selected_genres=array();

		//creo un array che per ogni posizione indica se la console in quella posizione è stata selezionata
		if($new_gameConsoles){
			foreach (Game::$possible_consoles as $key => $value) {
				$selected_consoles[$key] = in_array($value, $new_gameConsoles);
			}
		}else{
			foreach (Game::$possible_consoles as $key => $value) {
				$selected_consoles[$key] = false;
			}
		}
		
		//creo un array che per ogni posizione indica se il genere in quella posizione è stato selezionato
		if($new_gameGenres){
			foreach (Game::$possible_genres as $key => $value) {
				$selected_genres[$key] = in_array($value, $new_gameGenres);
			}
		}else{
			foreach (Game::$possible_genres as $key => $value) {
				$selected_genres[$key] = false;
			}
		}


		if(count($validation_error_messages) > 0){
			if($imagePath1 !== null){
				unlink('../' . $imagePath1);
			}
			if($imagePath2 !== null){
				unlink('../' . $imagePath2);
			}
		}else{

			$new_gameImage1 = null;
			if($imagePath1){
				// echo "Salvataggio immagine1 riuscito nel percorso:".$imagePath1."<br/>";
				$new_gameImage1 = new Image($imagePath1,$new_gameAlt1);
				$dbAccess->addImage($new_gameImage1);
				
			}

			$new_gameImage2 = null;
			if($imagePath2){
				// echo "Salvataggio immagine2 riuscito nel percorso:".$imagePath2."<br/>";
				$new_gameImage2 = new Image($imagePath2,$new_gameAlt2);
				$dbAccess->addImage($new_gameImage2);
				$image2Ok=true;
				
			}

			$newGame=new Game($new_gameName, $new_gamePublicationDate, $new_gameVote, $new_gameSinopsis, $new_gameAgeRange, $new_gameImage1, $new_gameImage2, $new_gameConsoles, $new_gameGenres, $new_gamePrequel, $new_gameSequel, $new_gameDeveloper);
				
			$opResult1 = $dbAccess->addGame($newGame);
			if($opResult1){
				array_push($success_messages, "Caricamento gioco riuscito");
			}
			
			if($opResult1 === true){
				$opResult2 = null;
				if($new_gameReview !== "" && $new_gameReview !== null){
					// echo "review not empty<br/>";
					$newGameReviewObj = new Review($new_gameName, $new_gameReview_author, $new_gameLast_review_date, $new_gameReview);
					$opResult2 = $dbAccess->addReview($newGameReviewObj);
				}else{
					$newGameReviewObj = null;
					$opResult2 = true;
				}
				
				if($opResult2 === true){
					array_push($success_messages, "Caricamento recensione riuscito");
					header("Location: giochi.php");	
				}else{
					array_push($failure_messages, "Caricamento recensione fallito");
					if($imagePath1 !== null){
						unlink('../' . $imagePath1);
					}
					if($imagePath2 !== null){
						unlink('../' . $imagePath2);
					}
				}
			}else{
				array_push($failure_messages, "Caricamento gioco fallito");
				if($imagePath1 !== null){
					unlink('../' . $imagePath1);
				}
				if($imagePath2 !== null){
					unlink('../' . $imagePath2);
				}
			}

			

		}

		//qui faccio i replacement dei placeholder in base a quello che mi è stato comunicato dall'utente
		//metto i valori che sono stati rilevati. Se qualcosa non è stato rilevato metto il nulla
		$replacements = array(
			"<game_name_ph/>" => $new_gameName  ? $new_gameName  : "",
			"<developer_ph/>" => $new_gameDeveloper ? $new_gameDeveloper : "",
			"<date_ph/>" => $new_gamePublicationDate ? $new_gamePublicationDate : "",
			"<age_range_ph/>" => $new_gameAgeRange ? $new_gameAgeRange : "",
			"<img1_alt_ph/>" => $new_gameAlt1 ? $new_gameAlt1 : "",
			"<img2_alt_ph/>" => $new_gameAlt2 ? $new_gameAlt2 : "",
			"<vote_ph/>" => $new_gameVote ? $new_gameVote : "",
			"<sinopsis_ph/>" => $new_gameSinopsis ? $new_gameSinopsis : "",
			"<review_ph/>" => $new_gameReview ? $new_gameReview : "",
			"<prequel_ph/>" => $new_gamePrequel ? $new_gamePrequel : "",
			"<sequel_ph/>" => $new_gameSequel ? $new_gameSequel : "",

			"<opzioni_prequel_ph/>" => createGamesOptions($dbAccess),
			"<opzioni_sequel_ph/>" => createGamesOptions($dbAccess),

			"<img1_min_ratio/>" => Game::$img1MinRatio,
			"<img1_max_ratio/>" => Game::$img1MaxRatio,
			"<img2_min_ratio/>" => Game::$img2MinRatio,
			"<img2_max_ratio/>" => Game::$img2MaxRatio
		);

		print_r($selected_consoles);

		//aggiungo ai replacement quelli delle checkboxes
		foreach ($selected_consoles as $key => $value) {
			$replacements["<checked_console_".$key."/>"] = $value ? "checked=\"checked\"" : "";
		}
		foreach ($selected_genres as $key => $value) {
			$replacements["<checked_genere_".$key."/>"] = $value ? "checked=\"checked\"" : "";
		}


		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
		// echo "replacements completati<br/>";

		//lo script per ora è fatto male: ogni volta che la pagina è stata caricata sovrascrivo il gioco sul database
		//Se l'utente non ha modificato i valori sovrascrivo quelli vecchi con altri identici
	}else{
		// echo "nessun valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";
		//i nuovi valori per il gioco non sono stati rilevati tutti, ritengo quindi che l'utente sia arrivato a questa pagina da un'altra e non abbia ancora potuto inviare le modifiche (o i dati già presenti, quelli scritti con la sostituzione dei placeholder)
		

		$replacements = array(
			"<game_name_ph/>" => "",
			"<developer_ph/>" => "", 
			"<date_ph/>" => "",
			"<img1_alt_ph/>" => "",
			"<img2_alt_ph/>" => "",
			"<vote_ph/>" => "",
			"<age_range_ph/>" => "",
			"<dlc_ph/>" => "",
			"<sinopsis_ph/>" => "",
			"<review_ph/>" => "",
			"<prequel_ph/>" => "",
			"<sequel_ph/>" => "",
			"<opzioni_prequel_ph/>" => createGamesOptions($dbAccess),
			"<opzioni_sequel_ph/>" => createGamesOptions($dbAccess),

			"<img1_min_ratio/>" => Game::$img1MinRatio,
			"<img1_max_ratio/>" => Game::$img1MaxRatio,
			"<img2_min_ratio/>" => Game::$img2MinRatio,
			"<img2_max_ratio/>" => Game::$img2MaxRatio
		);



		//aggiungo ai replacement quelli delle checkboxes
		for($i=0;$i<count(Game::$possible_consoles);$i++){
			$replacements["<checked_console_".$i."/>"] = "";
		}
		for($i=0;$i<count(Game::$possible_genres);$i++){
			$replacements["<checked_genere_".$i."/>"] = "";
		}
		
		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
		// echo "replacements completati<br/>";
	}

}

$jointValidation_error_message = getValidationErrorsHtml($validation_error_messages);
$jointSuccess_messages = getSuccessMessagesHtml($success_messages);
$jointFailure_messages = getFailureMessagesHtml($failure_messages);
$homePage = str_replace("<messaggi_form_ph/>", $jointValidation_error_message . "\n" . $jointSuccess_messages . "\n" . $jointFailure_messages, $homePage);
			


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>