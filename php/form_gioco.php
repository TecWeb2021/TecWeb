


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
	$homePage="Non sei autenticato";
	$authCheck=false;
}
if($authCheck && !$user->isAdmin()){
	$homePage="Non sei un amministratore";
	$authCheck=false;
}



//allOk prende in carico le prossime verifiche e parte dal valore di $authCheck
$allOk=$authCheck;

	
if($allOk){
	//ora posso popolare la pagina con gli attributi del gioco

	// ora devo raccogliere i valori che mi sono stati passati
	// devono essere presenti tutti i valori tranne l'immagine
	// sovrascriverò i valori del gioco nel database anche se sono uguali a quelli già presenti

	//se almeno un valore, a parte l'immagine, non è settato, vuol dire che sono arrivato a questa pagina da un altra, e quindi non serve che mi metta a raccogliere i valori e a scriverli nel database

	//verifico che tutti i valori siano settati
	//devo ancora implementare la gestione dell'alt dell'immagine
	if(isset($_REQUEST['nome']) || isset($_REQUEST['data']) || isset($_REQUEST['pegi']) || isset($_REQUEST['descrizione']) || isset($_REQUEST['recensione']) || isset($_REQUEST['alternativo']) || isset($_REQUEST['voto']) || isset($_FILES['immagine']) || isset($_REQUEST['prequel']) || isset($_REQUEST['sequel']) || isset($_REQUEST['sviluppo']) ){

		echo "almeno un valore è stato rilevato<br/>";
		//i nuovi valori per il gioco sono stati tutti rilevati
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


		$new_gameImage=null;
		$imageOk=false;
		
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



		$error_messages = array(
			'nome' => "Nome non inserito",
			'data' => "Data non inserita",
			'pegi' => "Pegi non inserito",
			'descrizione' => "descrizione non inserita",
			'recensione' => "Recensione non inserita",
			'immagine' => "Immagine non inserita",
			'alternativo' => "Testo alternativo dell'immagine non inserito",
			'voto' => "Voto non inserito",
			'console' => "Console non inserita",
			'genere' => "Genere non inserito",
			'prequel' => "Prequel non inserito",
			'sequel' => "Sequel non inserito",
			'sviluppo' => "Sviluppatore non inserito"
		);

		$error_message = "";

		//qui ci dovrò mettere anche un controllo dei campi
		if($new_gameName == null){
			$error_message = $error_message . $error_messages['nome'] . "<br/>";
		}
		if($new_gamePublicationDate == null){
			$error_message = $error_message . $error_messages['data'] . "<br/>";
		}
		if($new_gameAgeRange == null){
			$error_message = $error_message . $error_messages['pegi'] . "<br/>";
		}
		if($new_gameSinopsis == null){
			$error_message = $error_message . $error_messages['descrizione'] . "<br/>";
		}
		if($new_gameReview == null){
			$error_message = $error_message . $error_messages['recensione'] . "<br/>";
		}
		if($new_gameImage == null){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}
		if($new_gameAlt == null){
			$error_message = $error_message . $error_messages['alternativo'] . "<br/>";
		}
		if($new_gameVote == null){
			$error_message = $error_message . $error_messages['voto'] . "<br/>";
		}
		if($new_gameConsoles == null){
			$error_message = $error_message . $error_messages['console'] . "<br/>";
		}
		if($new_gameGenres == null){
			$error_message = $error_message . $error_messages['genere'] . "<br/>";
		}
		if($new_gamePrequel == null){
			$error_message = $error_message . $error_messages['prequel'] . "<br/>";
		}
		if($new_gameSequel == null){
			$error_message = $error_message . $error_messages['sequel'] . "<br/>";
		}
		if($new_gameDeveloper == null){
			$error_message = $error_message . $error_messages['sviluppo'] . "<br/>";
		}


		//inizializzo questi due array che, anche se vuoti, mi serviranno più avanti
		$selected_consoles=array();
		$selected_genres=array();

		if($error_message != ""){
			$homePage = str_replace("<messaggi_form_ph/>", $error_message, $homePage);
		}else{

			
			//creao un array che per ogni posizione indica se la console in quella posizione è stata selezionata
			foreach (Game::$possible_consoles as $key => $value) {
	
				$selected_consoles[$key] = in_array($value, $new_gameConsoles);
				echo "$value is ".($selected_consoles[$key] ? "true" : "false")."<br/>";
			}
	
			
			
			//creao un array che per ogni posizione indica se il genere in quella posizione è stato selezionato
			foreach (Game::$possible_genres as $key => $value) {
				$selected_genres[$key] = in_array($value, $new_gameGenres);
			}
			
		
			
			
			if($imageOk){
			
				$newGame=new Game($new_gameName, $new_gamePublicationDate, $new_gameVote, $new_gameSinopsis, $new_gameAgeRange, $new_gameReview, $new_gameImage, $new_gameConsoles, $new_gameGenres, $new_gamePrequel, $new_gameSequel, $new_gameDeveloper);
	
				$opResult = $dbAccess->addGame($newGame);
				echo "risultato salvataggio gioco su db: ".($opResult==null ? "null" : $opResult)."<br/>";
			}

		}

		//qui faccio i replacement dei placeholder in base a quello che mi è stato comunicato dall'utente
		//metto i valori che sono stati rilevati. Se quelcosa non è stato rilevato metto il nulla
		$replacements = array(
			"<game_name_ph/>" => $new_gameName  ? $new_gameName  : "",
			"<developer_ph/>" => $new_gameDeveloper ? $new_gameDeveloper : "",
			"<date_ph/>" => $new_gamePublicationDate ? $new_gamePublicationDate : "",
			"<age_range_ph/>" => $new_gameAgeRange ? $new_gameAgeRange : "",
			"<img_alt_ph/>" => $new_gameAlt ? $new_gameAlt : "",
			"<vote_ph/>" => $new_gameVote ? $new_gameVote : "",
			"<dlc_ph/>" => "dlcs del gioco",//non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<sinopsis_ph/>" => $new_gameSinopsis ? $new_gameSinopsis : "",
			"<review_ph/>" => $new_gameReview ? $new_gameReview : "",
			"<prequel_ph/>" => $new_gamePrequel ? $new_gamePrequel : "",
			"<sequel_ph/>" => $new_gameSequel ? $new_gameSequel : "",

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
		//Se l'utente non ha modificato i valori sovrascrivo quelli vecchi con altri identici
	}else{
		echo "nessun valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";
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
		}elseif(!isset($_FILES['immagine'])){
			echo "immagine non inserita<br/>";
		}elseif(!isset($_REQUEST['prequel'])){
			echo "prequel non inserita<br/>";
		}elseif(!isset($_REQUEST['sequel'])){
			echo "sequel non inserita<br/>";
		}elseif(!isset($_REQUEST['sviluppo'])){
			echo "sviluppo non inserita<br/>";
		}
		*/
		

		$replacements = array(
			"<game_name_ph/>" => "",
			"<developer_ph/>" => "", 
			"<date_ph/>" => "",
			"<img_alt_ph/>" => "",
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