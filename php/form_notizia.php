


<?php


require_once "replacer.php";
require_once "dbConnection.php";

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

	// ora devo raccogliere i valori che mi sono stati passati

	//se almeno un valore, non è settato, vuol dire che sono arrivato a questa pagina da un altra, e quindi non serve che mi metta a raccogliere i valori e a scriverli nel database

	//verifico che almeno uno dei campi sia stato passato
	//mettere qui solo i campi obbligatori
	if( isset($_REQUEST['titolo']) || isset($_REQUEST['testo']) || isset($_FILES['immagine']) || isset($_REQUEST['tipologia']) || isset($_REQUEST['alternativo'])){
		echo "almeno uno dei valori è stato rilevato<br/>";
		
		
		$new_newsTitle =  isset($_REQUEST['titolo']) ? $_REQUEST['titolo'] : null;
		$new_newsText = isset($_REQUEST['testo']) ? $_REQUEST['testo'] : null;
		$new_newsAuthor = $user;
		$new_newsEditDateTime = date("Y-m-d");
		$new_newsCategory = isset($_REQUEST['tipologia']) ? $_REQUEST['tipologia'] : null;
		$new_newsAlt = isset($_REQUEST['alternativo']) ? $_REQUEST['alternativo'] : null;
		$new_newsGame = null;
		if($new_newsCategory == "Giochi"){
			$new_newsGame = isset($_REQUEST['searchbar']) ? $_REQUEST['searchbar'] : null;
		}

		//il salvataggio dell'immagine potrebbe fallire quindi inserisco una variabile boolean a per gestire la cosa (sarebbe forse meglio gestire il tutto con le eccezioni)
		$imageSaved=true;

		$new_newsImage=null;
		$imagePath=saveImageFromFILES($dbAccess,'immagine');
		if($imagePath){
			$new_newsImage=new Image($imagePath,$new_newsAlt);
		}else{
			echo "salvataggio dell'immagine fallito"."<br/>";
			$imageSaved=false;
		}

		$error_messages = array(
			'titolo' => "Titolo non presente",
			'testo' => "Testo non presente",
			'tipologia' => "Tipologia non presente",
			'immagine' => "Immagine non presente",
			'alternativo' => "Testo alternativo dell'immagine non presente",
			'gioco' => "Gioco non inserito"
		);

		$error_message = "";

		//qui ci dovrò mettere anche un controllo dei campi
		if($new_newsTitle == null){
			$error_message = $error_message . $error_messages['titolo'] . "<br/>";
		}
		if($new_newsText == null){
			$error_message = $error_message . $error_messages['testo'] . "<br/>";
		}
		if($new_newsCategory == null){
			$error_message = $error_message . $error_messages['tipologia'] . "<br/>";
		}
		if($new_newsImage == null){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}
		if($new_newsAlt == null){
			$error_message = $error_message . $error_messages['alternativo'] . "<br/>";
		}
		if($new_newsCategory == "Giochi" && $new_newsGame== null){
			$error_message = $error_message . $error_messages['gioco'] . "<br/>";
		}

		//controllo se c'è stato almeno un errore
		if($error_message != ""){
			$homePage = str_replace("<messaggi_form_ph/>", $error_message, $homePage);
		}else{
			echo "non sono presenti errori";
			//se non ci sono stati errori procedo col salvataggio dei dati su db
			if($imageSaved){
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
		}
			

		
	
	
		

		//qui faccio i replacement dei placeholder in base a quello che mi è stato comunicato dall'utente
		//metto i valori che sono stati rilevati. Se quelcosa non è stato rilevato metto il nulla
		$replacements = array(
			"<news_title_ph/>" => $new_newsTitle ? $new_newsTitle : "",
			"<content_ph/>" => $new_newsText ? $new_newsText : "",
			"<img_alt_ph/>" => $new_newsAlt ? $new_newsAlt : "",
			"<opzioni_ph/>" => createGamesOptions($dbAccess),
			"<game_name_ph/>" => $new_newsGame ? $new_newsGame : ""
		);

		if($new_newsCategory=='Eventi'){
			$replacements['<checked_eventi_ph/>'] = "checked=\"checked\" ";
			$replacements['<checked_giochi_ph/>'] = "";
			$replacements['<checked_hardware_ph/>'] = ""; 
		}elseif($new_newsCategory=='Giochi'){
			$replacements['<checked_eventi_ph/>'] = "";
			$replacements['<checked_giochi_ph/>'] = "checked=\"checked\" ";
			$replacements['<checked_hardware_ph/>'] = "";
		}elseif($new_newsCategory=='Hardware'){
			$replacements['<checked_eventi_ph/>'] = "";
			$replacements['<checked_giochi_ph/>'] = "";
			$replacements['<checked_hardware_ph/>'] = "checked=\"checked\" ";
		}elseif($new_newsCategory==null){
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
		//i nuovi valori per il gioco non sono stati rilevati tutti, ritengo quindi che l'utente sia arrivato a questa pagina da un'altra e non abbia ancora potuto inviare le modifiche (o i dati già presenti, quelli scritti con la sostituzione dei placeholder)

		/*
		//controllo quale valore non è stato inserito
		if(!isset($_REQUEST['titolo'])){
			echo "titolo non inserito<br/>";
		}elseif(!isset($_REQUEST['testo'])){
			echo "testo non inserito<br/>";
		}elseif(!isset($_FILES['immagine'])){
			echo "immagine non inserita<br/>";
		}elseif(!isset($_REQUEST['alternativo'])){
			echo "alt non inserito<br/>";
		}*/


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