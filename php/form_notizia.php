


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

	// verifico che uno qualsiasi dei campi di testo sia stato passato. Se sì vuol dire che l'utente è tornato sulla pagina per inviare i dati, e non è appena arrivato da un altra pagina
	if( isset($_REQUEST['titolo']) ){
		echo "almeno uno dei valori è stato rilevato<br/>";
		
		$new_newsTitle =  getSafeInput('titolo', 'string');
		$new_newsCategory = getSafeInput('tipologia', 'string');
		$new_newsAlt1 = getSafeInput('alternativo1', 'string');
		$new_newsAlt2 = getSafeInput('alternativo2', 'string');
		$new_newsText = getSafeInput('testo', 'string');
		$new_newsGame = null;
		if($new_newsCategory == "Giochi"){
			$new_newsGame = getSafeInput('searchbar', 'string');
		}
		$new_newsAuthor = $user;
		$new_newsEditDateTime = date("Y-m-d");

		$new_newsImage1 = null;
		$imagePath1 = saveImageFromFILES($dbAccess, 'immagine1', News::$img1MinRatio, News::$img1MaxRatio);
		if($imagePath1){
			$new_newsImage1 = new Image($imagePath1,$new_newsAlt1);
			$result1 = $dbAccess->addImage($new_newsImage1);
		}else{
			echo "salvataggio dell'immagine1 fallito"."<br/>";
		}

		$new_newsImage2 = null;
		$imagePath2 = saveImageFromFILES($dbAccess, 'immagine2', News::$img2MinRatio, News::$img2MaxRatio);
		if($imagePath2){
			$new_newsImage2 = new Image($imagePath2,$new_newsAlt2);
			$dbAccess->addImage($new_newsImage2);
		}else{
			echo "salvataggio dell'immagine2 fallito"."<br/>";
		}

		// ho raccolto tutti i dati che potevo raccogliere

		$error_message = "";

		// controllo i campi obbligatori

		if( $new_newsTitle === null || validateValue($new_newsTitle, 'titolo') === false){
			array_push($validation_error_messages, getValidationError('titolo'));
		}
		if($new_newsCategory === null || validateValue($new_newsCategory, 'tipologia') === false){
			array_push($validation_error_messages, getValidationError('tipologia'));
		}
		if($new_newsImage1 === null){
			array_push($validation_error_messages, getValidationError('immagine'));
		}
		if($new_newsImage2 === null){
			array_push($validation_error_messages, getValidationError('immagine'));
		}
		
		if( $new_newsText === null || validateValue($new_newsText, 'testo') === false){
			array_push($validation_error_messages, getValidationError('testo'));
		}

		// controllo i campi obbligatori derivati

		if($new_newsCategory === "Giochi" && ($new_newsGame === null || validateValue($new_newsGame, 'nome_gioco_notizia') === false)) {
			array_push($validation_error_messages, getValidationError('nome_gioco_notizia'));
		}

		// controllo i campi opzionali

		if( $new_newsAlt1 !== null && (validateValue($new_newsAlt1, 'alternativo') === false)) {
			array_push($validation_error_messages, getValidationError('alternativo'));
		}
		if( $new_newsAlt2 !== null && (validateValue($new_newsAlt2, 'alternativo') === false)){
			array_push($validation_error_messages, getValidationError('alternativo'));
		}



		//controllo se c'è stato almeno un errore
		if(count($validation_error_messages) > 0){
			
		}else{
			echo "non sono presenti errori<br/>";
			$newNews=new News($new_newsTitle, $new_newsText, $new_newsAuthor, $new_newsEditDateTime, $new_newsImage1, $new_newsImage2, $new_newsCategory, $new_newsGame);
	
			$opResult = $dbAccess->addNews($newNews);
			if($opResult && $opResult!=false){
				echo "salvataggio su db riuscito"."<br/>";
				header("Location: notizie.php");
			}else{
				echo "salvataggio su db fallito"."<br/>";
			}
		}
			


		//qui faccio i replacement dei placeholder in base a quello che mi è stato comunicato dall'utente
		//metto i valori che sono stati rilevati. Se qualcosa non è stato rilevato metto la stringa vuota
		$replacements = array(
			"<news_title_ph/>" => $new_newsTitle ? $new_newsTitle : "",
			"<content_ph/>" => $new_newsText ? $new_newsText : "",
			"<img1_alt_ph/>" => $new_newsAlt1 ? $new_newsAlt1 : "",
			"<img2_alt_ph/>" => $new_newsAlt2 ? $new_newsAlt2 : "",
			"<opzioni_form_ph/>" => createGamesOptions($dbAccess),
			"<game_name_ph/>" => $new_newsGame ? $new_newsGame : "",

			"<img1_min_ratio/>" => News::$img1MinRatio,
			"<img1_max_ratio/>" => News::$img1MaxRatio,
			"<img2_min_ratio/>" => News::$img2MinRatio,
			"<img2_max_ratio/>" => News::$img2MaxRatio
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

		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
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
			"<img1_alt_ph/>" => "",
			"<img2_alt_ph/>" => "",
			"<opzioni_form_ph/>" => createGamesOptions($dbAccess),
			"<game_name_ph/>" => "",

			"<img1_min_ratio/>" => News::$img1MinRatio,
			"<img1_max_ratio/>" => News::$img1MaxRatio,
			"<img2_min_ratio/>" => News::$img2MinRatio,
			"<img2_max_ratio/>" => News::$img2MaxRatio
		);
		$replacements['<checked_eventi_ph/>'] = "";
		$replacements['<checked_giochi_ph/>'] = "";
		$replacements['<checked_hardware_ph/>'] = "";
		
		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
		echo "replacements di rimozione placeholder completati<br/>";
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