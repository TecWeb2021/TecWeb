


<?php


require_once "replacer.php";
require_once "dbConnection.php";
require_once "classes/news.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess = new DBAccess;
$dbAccess->openDBConnection();

$homePage = file_get_contents("../html/templates/formNotiziaTemplate.html");




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


	
if($allOk){

	// verifico che uno qualsiasi dei campi di testo sia stato passato. Se sì vuol dire che l'utente è tornato sulla pagina per inviare i dati, e non è appena arrivato da un altra pagina
	if( isset($_REQUEST['titolo']) ){
		echo "almeno uno dei valori è stato rilevato<br/>";
		
		$new_newsTitle =  isset($_REQUEST['titolo']) ? $_REQUEST['titolo'] : null;
		$new_newsCategory = isset($_REQUEST['tipologia']) ? $_REQUEST['tipologia'] : null;
		$new_newsAlt = isset($_REQUEST['alternativo']) ? $_REQUEST['alternativo'] : null;
		$new_newsText = isset($_REQUEST['testo']) ? $_REQUEST['testo'] : null;
		$new_newsGame = null;
		if($new_newsCategory == "Giochi"){
			$new_newsGame = isset($_REQUEST['searchbar']) ? $_REQUEST['searchbar'] : null;
		}
		$new_newsAuthor = $user;
		$new_newsEditDateTime = date("Y-m-d");

		//il salvataggio dell'immagine potrebbe fallire quindi inserisco una variabile booleana per gestire la cosa (sarebbe forse meglio gestire il tutto con le eccezioni)

		$new_newsImage = null;
		$imagePath = saveImageFromFILES($dbAccess,'immagine');
		if($imagePath){
			$new_newsImage = new Image($imagePath,$new_newsAlt);
		}else{
			echo "salvataggio dell'immagine fallito"."<br/>";
		}

		// ho raccolto tutti i dati che potevo raccogliere

		$error_messages = array(
			'titolo' => "Titolo non presente",
			'tipologia' => "Tipologia non presente o non corretta",
			'gioco' => "Gioco non inserito",
			'immagine' => "Immagine non presente",
			'alternativo' => "Testo alternativo dell'immagine non presente",
			'testo' => "Testo non presente"
		);

		$error_message = "";

		// controllo i campi obbligatori

		if( $new_newsTitle === null || ($errorText = checkString($new_newsTitle, 'titolo')) !== true){
			$error_message = $error_message . $error_messages['titolo'] . "<br/>";
		}
		if($new_newsCategory === null || !in_array($new_newsCategory, News::$possible_categories)){
			$error_message = $error_message . $error_messages['tipologia'] . "<br/>";
		}
		if($new_newsImage === null){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}
		
		if( $new_newsText === null || ($errorText = checkString($new_newsText, 'testo')) !== true){
			$error_message = $error_message . $error_messages['testo'] . "<br/>";
		}

		// controllo i campi obbligatori derivati

		if($new_newsCategory === "Giochi" && ($new_newsGame === "" || $new_newsGame === null)){
			$error_message = $error_message . $error_messages['gioco'] . "<br/>";
		}

		// controllo i campi opzionali

		if( $new_newsAlt !== null && strlen($new_newsAlt) > 0 && ($errorText = checkString($new_newsAlt, 'alternativo')) !== true){
			$error_message = $error_message . $error_messages['alternativo'] . "<br/>";
		}



		//controllo se c'è stato almeno un errore
		if($error_message != ""){
			$homePage = str_replace("<messaggi_form_ph/>", $error_message, $homePage);
		}else{
			echo "non sono presenti errori";
			//se non ci sono stati errori procedo col salvataggio dei dati su db
			$newNews=new News($new_newsTitle, $new_newsText, $new_newsAuthor, $new_newsEditDateTime, $new_newsImage, $new_newsCategory, $new_newsGame);
	
			$opResult = $dbAccess->addNews($newNews);
			if($opResult && $opResult!=false){
				echo "salvataggio su db riuscito"."<br/>";
			}else{
				echo "salvataggio su db fallito"."<br/>";
				//visto che l'operazione di salvataggio su db della news non è andata a buon fine rimuovo l'immagine sia dal db che dal filesystem
				$dbAccess->deleteImage($imagePath);
				unlink("../".$imagePath);
			}
		}
			


		//qui faccio i replacement dei placeholder in base a quello che mi è stato comunicato dall'utente
		//metto i valori che sono stati rilevati. Se qualcosa non è stato rilevato metto la stringa vuota
		$replacements = array(
			"<news_title_ph/>" => $new_newsTitle ? $new_newsTitle : "",
			"<content_ph/>" => $new_newsText ? $new_newsText : "",
			"<img_alt_ph/>" => $new_newsAlt ? $new_newsAlt : "",
			"<opzioni_ph/>" => createGamesOptions($dbAccess),
			"<game_name_ph/>" => $new_newsGame ? $new_newsGame : ""
		);


		if($new_newsCategory == 'Eventi'){
			$replacements['<checked_eventi_ph/>'] = "checked=\"checked\" ";
			$replacements['<checked_giochi_ph/>'] = "";
			$replacements['<checked_hardware_ph/>'] = ""; 
		}elseif($new_newsCategory == 'Giochi'){
			$replacements['<checked_eventi_ph/>'] = "";
			$replacements['<checked_giochi_ph/>'] = "checked=\"checked\" ";
			$replacements['<checked_hardware_ph/>'] = "";
		}elseif($new_newsCategory == 'Hardware'){
			$replacements['<checked_eventi_ph/>'] = "";
			$replacements['<checked_giochi_ph/>'] = "";
			$replacements['<checked_hardware_ph/>'] = "checked=\"checked\" ";
		}elseif($new_newsCategory == null){
			$replacements['<checked_eventi_ph/>'] = "";
			$replacements['<checked_giochi_ph/>'] = "";
			$replacements['<checked_hardware_ph/>'] = "";
		}
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
		echo "replacements completati<br/>";

		//lo script per ora è fatto male: ogni volta che la pagina è stata caricata sovrascrivo il gioco sul database
		//Se l'utente non ha modificato i valori sovrascrivo quelli vecchi con altri identici
	}else{
		echo "nessun valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";
		//un valore testuale di input non è stato rilevato. Ritengo quindi che l'utente sia arrivato da un altra pagina


		//faccio i seguenti replacements solo per togliere i placeholder
		$replacements = array(
			"<news_title_ph/>" => "",
			"<content_ph/>" => "",
			"<img_alt_ph/>" => "",
			"<opzioni_ph/>" => createGamesOptions($dbAccess),
			"<game_name_ph/>" => ""
		);
		$replacements['<checked_eventi_ph/>'] = "";
		$replacements['<checked_giochi_ph/>'] = "";
		$replacements['<checked_hardware_ph/>'] = "";
	
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
		echo "replacements di rimozione placeholder completati<br/>";
	}

}
			


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>