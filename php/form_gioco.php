


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

$error_message = "";

	
if($allOk){

	//verifico che un valore testuale qualsiasi sia settato
	if(isset($_REQUEST['nome'])){

		echo "almeno un valore è stato rilevato<br/>";

		$new_gameName = isset($_REQUEST['nome']) ? $_REQUEST['nome'] : null;
		$new_gameDeveloper = isset($_REQUEST['sviluppo']) ? $_REQUEST['sviluppo'] : null;
		$new_gameAgeRange = isset($_REQUEST['pegi']) ? $_REQUEST['pegi'] : null;
		$new_gamePublicationDate = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;

		$new_gameConsoles = isset($_REQUEST['console']) ? $_REQUEST['console'] : array();
		$new_gameGenres = isset($_REQUEST['genere']) ? $_REQUEST['genere'] : array();

		$new_gameAlt1 = isset($_REQUEST['alternativo1']) ? $_REQUEST['alternativo1'] : null;
		$new_gameAlt2 = isset($_REQUEST['alternativo2']) ? $_REQUEST['alternativo2'] : null;
		$new_gameVote = isset($_REQUEST['voto']) ? $_REQUEST['voto'] : null;
		$new_gamePrequel = isset($_REQUEST['prequel']) ? $_REQUEST['prequel'] : null;
		$new_gameSequel = isset($_REQUEST['sequel']) ? $_REQUEST['sequel'] : null;
		$new_gameSinopsis = isset($_REQUEST['descrizione']) ? $_REQUEST['descrizione'] : null;
		$new_gameReview = isset($_REQUEST['recensione']) ? $_REQUEST['recensione'] : null;
		$new_gameLast_review_date = date("Y-m-d");
		$new_gameReview_author = $user->getUsername();



		$new_gameImage1=null;
		$image1Ok=false;

		$new_gameImage2=null;
		$image2Ok=false;
		
		echo "rilevato campo immagine"."<br/>";
		//prendo l'immagine inserita dall'utente
		$imagePath1 = saveImageFromFILES($dbAccess, "immagine1", Game::$img1MinRatio, Game::$img1MaxRatio);
		if($imagePath1){
			echo "Salvataggio immagine1 riuscito nel percorso:".$imagePath1."<br/>";
			$new_gameImage1 = new Image($imagePath1,$new_gameAlt1);
			$dbAccess->addImage($new_gameImage1);
			$image1Ok=true;
			
		}else{
			echo "Salvataggio immagine1 fallito"."<br/>";
		}

		$imagePath2 = saveImageFromFILES($dbAccess, "immagine2", Game::$img2MinRatio, Game::$img2MaxRatio);
		if($imagePath2){
			echo "Salvataggio immagine2 riuscito nel percorso:".$imagePath2."<br/>";
			$new_gameImage2 = new Image($imagePath2,$new_gameAlt2);
			$dbAccess->addImage($new_gameImage2);
			$image2Ok=true;
			
		}else{
			echo "Salvataggio immagine2 fallito"."<br/>";
		}



		$error_messages = array(
			'nome' => "Nome non inserito",
			'data' => "Data non inserita",
			'pegi' => "Pegi non inserito",
			'descrizione' => "descrizione non inserita",
			'recensione' => "Recensione non inserita",
			'immagine1' => "Immagine1 non inserita",
			'immagine2' => "Immagine2 non inserita",
			'alternativo1' => "Testo alternativo dell'immagine1 non inserito",
			'alternativo2' => "Testo alternativo dell'immagine2 non inserito",
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
		if($new_gameImage1 === null){
			$error_message = $error_message . $error_messages['immagine1'] . "<br/>";
		}
		if($new_gameImage2 === null){
			$error_message = $error_message . $error_messages['immagine2'] . "<br/>";
		}
		if($new_gameVote === null || ($errorText = checkString($new_gameVote,'voto')) !== true){
			$error_message = $error_message . $error_messages['voto'] . "<br/>";
		}
		if($new_gameSinopsis === null || ($errorText = checkString($new_gameSinopsis,'descrizione')) !== true){
			$error_message = $error_message . $error_messages['descrizione'] . "<br/>";
		}

		// controllo i campi obbligatori derivati

		// controllo i campi opzionali
		
		if($new_gamePrequel !== null && strlen($new_gamePrequel) > 0 && ($errorText = checkString($new_gamePrequel, 'prequel')) !== true){
			$error_message = $error_message . $error_messages['prequel'] . "<br/>";
		}
		if($new_gameSequel !== null && strlen($new_gameSequel) > 0 &&($errorText = checkString($new_gameSequel, 'sequel')) !== true){
			$error_message = $error_message . $error_messages['sequel'] . "<br/>";
		}

		if($new_gameReview !== null && strlen($new_gameReview) > 0 && ($errorText = checkString($new_gameReview, 'recensione')) !== true){
			$error_message = $error_message . $error_messages['recensione'] . "<br/>";
		}
		
		if($new_gameAlt1 !== null && strlen($new_gameAlt1) > 0 && ($errorText = checkString($new_gameAlt1, 'alternativo')) !== true){
			$error_message = $error_message . $error_messages['alternativo1'] . "<br/>";
		}

		if($new_gameAlt2 !== null && strlen($new_gameAlt2) > 0 && ($errorText = checkString($new_gameAlt2, 'alternativo')) !== true){
			$error_message = $error_message . $error_messages['alternativo2'] . "<br/>";
		}
		
		
		
		


		//inizializzo questi due array che, anche se vuoti, mi serviranno più avanti
		$selected_consoles=array();
		$selected_genres=array();

		//creo un array che per ogni posizione indica se la console in quella posizione è stata selezionata
		foreach (Game::$possible_consoles as $key => $value) {
			$selected_consoles[$key] = in_array($value, $new_gameConsoles);
			echo "$value is ".($selected_consoles[$key] ? "true" : "false")."<br/>";
		}
		
		//creo un array che per ogni posizione indica se il genere in quella posizione è stato selezionato
		foreach (Game::$possible_genres as $key => $value) {
			$selected_genres[$key] = in_array($value, $new_gameGenres);
		}


		if($error_message != ""){
			
		}else{
			$newGame=new Game($new_gameName, $new_gamePublicationDate, $new_gameVote, $new_gameSinopsis, $new_gameAgeRange, $new_gameImage1, $new_gameImage2, $new_gameConsoles, $new_gameGenres, $new_gamePrequel, $new_gameSequel, $new_gameDeveloper);
				
			$opResult1 = $dbAccess->addGame($newGame);
			echo "risultato salvataggio gioco su db: ".($opResult1==null ? "null" : $opResult1)."<br/>";

			
			
			if($opResult1 === true){
				$newGameReviewObj = "none";
				$opResult2 = null;
				if($newGameReviewObj !== "" && $newGameReviewObj !== null){
					$newGameReviewObj = new Review($new_gameName, $new_gameReview_author, $new_gameLast_review_date, $new_gameReview);
					$opResult2 = $dbAccess->addReview($newGameReviewObj);
				}else{
					$newGameReviewObj = null;
				}
				
				if($opResult2 === true){
					header("Location: giochi.php");	
				}
			}else{
				echo "Caricamento review fallito<br/>";
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
			"<dlc_ph/>" => "dlcs del gioco",//non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<sinopsis_ph/>" => $new_gameSinopsis ? $new_gameSinopsis : "",
			"<review_ph/>" => $new_gameReview ? $new_gameReview : "",
			"<prequel_ph/>" => $new_gamePrequel ? $new_gamePrequel : "",
			"<sequel_ph/>" => $new_gameSequel ? $new_gameSequel : "",

			"<opzioni_prequel_ph/>" => createGamesOptions($dbAccess),
			"<opzioni_sequel_ph/>" => createGamesOptions($dbAccess)
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
		echo "replacements completati<br/>";

		//lo script per ora è fatto male: ogni volta che la pagina è stata caricata sovrascrivo il gioco sul database
		//Se l'utente non ha modificato i valori sovrascrivo quelli vecchi con altri identici
	}else{
		echo "nessun valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";
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
			"<opzioni_sequel_ph/>" => createGamesOptions($dbAccess)
		);



		//aggiungo ai replacement quelli delle checkboxes
		for($i=0;$i<count(Game::$possible_consoles);$i++){
			$replacements["<checked_console_".$i."/>"] = "";
		}
		for($i=0;$i<count(Game::$possible_genres);$i++){
			$replacements["<checked_genere_".$i."/>"] = "";
		}
		
		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
		echo "replacements completati<br/>";
	}

}

$homePage = str_replace("<messaggi_form_ph/>", $error_message, $homePage);
			


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>