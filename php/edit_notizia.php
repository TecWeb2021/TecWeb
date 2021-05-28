


<?php
 
// una differenza tra questo script e quello per l'inserimento di un gioco nuovo sta nel fatto che qui viene accettato il "salva modifiche" anche se non è stata fornita una immagine in input, o almeno credo

//possibile modifica da fare:
//	per ora metto nei placeholder i valori del gioco che voglio modificare.
//	l'utente poi può modificare i valori e inviarli
//	se qualcosa va storto torno alla pagina, ma vengono rimessi nei placeholder i valori del vecchio gioco
// potrei fare così: se rilevo tutti valori richiesti nel $_REQUEST allora metto quelli, altrimenti metto quelli del vecchio gioco


//una buona organizzazione:
//	se mi mandi tutti i valori:
//		se va a buon fine: ti mostro i valori che mi hai mandato, che sono anche quelli del gioco come è scritto sul db dopo le modifiche
//		se non va a buon fine: ti mostro i valori che mi hai inserito, così non perdi quello che stavi scrivendo
//		(quindi ti mostro sempre quello che mi hai mandato)
//	se non mi mandi tutto (arrivi da un'altra pagina):
//		ti mostro i valori del gioco specificato

//ATTENZIONE: questo script ha bisogno che ci sia un input nel form dell'html che invii il nome del gioco. Questo input può essere hidden.

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/editNotiziaTemplate.html");




// verifico che l'utente abbia l'autorizzazione per modificare un gioco
$user = getLoggedUser($dbAccess);

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
if($allOk && !isset($_REQUEST['news'])){
	$homePage = getErrorHtml("news_not_existent");
	$allOk=false;
}


if($allOk){
	$newsToBeModifiedName=$_REQUEST['news'];
}

if($allOk /*&& !correctFormat(gameName) (qui devo controllare che il nel nome del gioco non siano presenti comandi malevoli)*/ && false/*questo false serve per non entrare nell'if in fase di testing*/){
	$homePage="Formato del nome della notizia non corretto";
	$allOk=false;
}
// verifico che il gioco specificato esista
if($allOk && !$news=$dbAccess->getNews($newsToBeModifiedName)){
	$homePage = getErrorHtml("news_not_specified");
	$allOk=false;
}

//se c'è elimina non c'è il resto quindi succede solo quello che c'è nell'if qua sotto, almeno credo
if(isset($_REQUEST['elimina'])){
	$newsToBeDeletedName=$_REQUEST["elimina"];
	//echo "elimina: ".$newsToBeDeletedName."<br/>";
	$opResult=$dbAccess->deleteNews($newsToBeDeletedName);
	if($opResult){
		$homePage = getErrorHtml("news_deleted");
	}else{
		$homePage="eliminazione della notizia $newsToBeDeletedName fallita";
	}
}

$error_message = "";

$oldNews = null;

$validation_error_messages = array();
$success_messages = array();
	
if($allOk){
	//ora posso popolare la pagina con gli attributi del gioco
	//rinino il gioco a oldGame perchè è più chiaro nel contesto che c'è d'ora in poi
	$oldNews=$news;

	// ora devo raccogliere i valori che mi sono stati passati
	// devono essere presenti tutti i valori tranne l'immagine
	// sovrascriverò i valori del gioco nel database anche se sono uguali a quelli già presenti

	//se almeno un valore, a parte l'immagine, non è settato, vuol dire che sono arrivato a questa pagina da un altra, e quindi non serve che mi metta a raccogliere i valori e a scriverli nel database

	//verifico che tutti i valori siano settati
	//devo ancora implementare la gestione dell'alt dell'immagine
	if( isset($_REQUEST['titolo']) || isset($_REQUEST['testo']) || isset($_REQUEST['tipologia']) || isset($_REQUEST['alternativo1']) || isset($_REQUEST['alternativo2']) ){
		//echo "almeno un valore è stato rilevato<br/>";
		//i nuovi valori per il gioco sono stati tutti rilevati
		$new_newsTitle =  getSafeInput('titolo', 'string');
		$new_newsText = getSafeInput('testo', 'string');
		// alcuni valori li riprendo dalla vecchia notizia
		$new_newsAuthor = $user;
		$new_newsEditDateTime = date("Y-m-d");
		$new_newsCategory = getSafeInput('tipologia', 'string');
		$new_newsAlt1 = getSafeInput('alternativo1', 'string');
		$new_newsAlt2 = getSafeInput('alternativo2', 'string');
		$new_newsGame = null;
		if($new_newsCategory == "Giochi"){
			$new_newsGame = getSafeInput('searchbar', 'string');
		}
	
		// l'immagine è un caso particolare: se l'utente ne inserisce una 	devo creare un oggetto che la rappresenti, altrimenti, visto che 	non è stata messa nell'html durante le sostituzioni, devo 	prendermi l'oggetto immagine di $oldGame
		$new_newsImage1 = null;
		$image1Ok = false;

		$new_newsImage2 = null;
		$image2Ok = false;

		//errore 4: non è stata caricata alcuna immagine
		if(isset($_FILES['immagine1']) && $_FILES['immagine1']['error']!=4 ){
			//echo "l'utente ha inserito una nuova immagine1"."<br/>";
			

			$imagePath=saveImageFromFILES($dbAccess,'immagine1', News::$img1MinRatio, News::$img1MaxRatio);
			if($imagePath){
				$new_newsImage1=new Image($imagePath,$new_newsAlt1);
				$dbAccess->addImage($new_newsImage1);
				$image1Ok=true;
			}else{
				//echo "salvataggio dell'immagine1 fallito"."<br/>";

			}
		}

		//errore 4: non è stata caricata alcuna immagine
		if(isset($_FILES['immagine2']) && $_FILES['immagine2']['error']!=4 ){
			//echo "l'utente ha inserito una nuova immagine2"."<br/>";
			

			$imagePath=saveImageFromFILES($dbAccess,'immagine2', News::$img2MinRatio, News::$img2MaxRatio);
			if($imagePath){
				$new_newsImage2=new Image($imagePath,$new_newsAlt2);
				$dbAccess->addImage($new_newsImage2);
				$image2Ok=true;
			}else{
				//echo "salvataggio dell'immagine2 fallito"."<br/>";
			}
		}

		//controllo i campi obbligatori

		if( $new_newsTitle === null || validateValue($new_newsTitle, 'titolo') === false){
			array_push($validation_error_messages, getValidationError('titolo'));
		}
		if( $new_newsText === null || validateValue($new_newsText, 'testo') === false){
			array_push($validation_error_messages, getValidationError('testo'));
		}
		if($new_newsCategory === null || validateValue($new_newsCategory, 'tipologia') === false){
			array_push($validation_error_messages, getValidationError('tipologia'));
		}

		// controllo i campi obbligatori derivati

		if($new_newsCategory == "Giochi" && ($new_newsGame == null || validateValue($new_newsGame, 'nome_gioco_notizia') === false) ){
			array_push($validation_error_messages, getValidationError('nome_gioco_notizia'));
		}

		// controllo i campi opzionali

		if( $new_newsImage1 !== null && $image1Ok === false){
			array_push($validation_error_messages, getValidationError('immagine'));
		}

		if( $new_newsImage2 !== null && $image2Ok === false){
			array_push($validation_error_messages, getValidationError('immagine'));
		}

		if( $new_newsAlt1 !== null && validateValue($new_newsAlt1, 'alternativo') === false){
			array_push($validation_error_messages, getValidationError('alternativo'));
		}

		if( $new_newsAlt2 !== null && validateValue($new_newsAlt2, 'alternativo') === false){
			array_push($validation_error_messages, getValidationError('alternativo'));
		}


		if(count($validation_error_messages) > 0){
			
		}else{

			if($new_newsImage1 == null){
				//echo "l'utente non ha inserito una nuova immagine1"."<br/>";
				//prendo la vecchia immagine
				$new_newsImage1=$oldNews->getImage1();
				$image1Ok=true;
			}

			if($new_newsImage2 == null){
				//echo "l'utente non ha inserito una nuova immagine2"."<br/>";
				//prendo la vecchia immagine
				$new_newsImage2=$oldNews->getImage2();
				$image2Ok=true;
			}

			$newNews = new News($new_newsTitle, $new_newsText, $new_newsAuthor, $new_newsEditDateTime, $new_newsImage1, $new_newsImage2, $new_newsCategory, $new_newsGame);
			$overwriteResult = $dbAccess->overwriteNews($newsToBeModifiedName, $newNews);
			if($overwriteResult == true){
				array_push($success_messages, "overwrite su db riuscito");
			}else{
				//echo "overwrite su db fallito" . "<br/>";
			}
		}

		//qui faccio i replacement dei placeholder in base a quello che mi è stato comunicato dall'utente
		//se c'è una stringa data dall'utente metto quella, altrimenti metto quella vechia, presa dal db
		$replacements = array(
			"<news_title_ph/>" => $new_newsTitle ? $new_newsTitle : $oldNews->getTitle(),
			"<content_ph/>" => $new_newsText ? $new_newsText : $oldNews->getContent(),
			"<img1_alt_ph/>" => $new_newsAlt1 ? $new_newsAlt1 : ($oldNews->getImage1() ? $oldNews->getImage1()->getAlt() : ""),
			"<img2_alt_ph/>" => $new_newsAlt2 ? $new_newsAlt2 : ($oldNews->getImage2() ? $oldNews->getImage2()->getAlt() : ""),
			"<opzioni_form_ph/>" => createGamesOptions($dbAccess),
			"<game_name_ph/>" => $new_newsGame ? $new_newsGame : $oldNews->getGameName()
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
		//echo "replacements completati<br/>";

		//lo script per ora è fatto male: ogni volta che la pagina è stata caricata sovrascrivo il gioco sul database
		//Se l'utente non ha modificato i valori sovrascrivo quelli vecchi con altri identici
	}else{
		//echo "nessun valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";


		$replacements = array(
			"<news_title_ph/>" => $oldNews->getTitle(),
			"<content_ph/>" => $oldNews->getContent(),
			"<opzioni_form_ph/>" => createGamesOptions($dbAccess),
			"<game_name_ph/>" => $oldNews->getGameName() ? $oldNews->getGameName() : ""
		);

		if($oldImage1 = $oldNews->getImage1()){
			$replacements['<img1_alt_ph/>'] = $oldImage1->getAlt();
		}else{
			$replacements['<img1_alt_ph/>'] = "";
		}

		if($oldImage2 = $oldNews->getImage2()){
			$replacements['<img2_alt_ph/>'] = $oldImage2->getAlt();
		}else{
			$replacements['<img2_alt_ph/>'] = "";
		}

		if($oldNews->getCategory()=="Eventi"){
			$replacements['<checked_eventi_ph/>'] = "checked=\"checked\" ";
			$replacements['<checked_giochi_ph/>'] = "";
			$replacements['<checked_hardware_ph/>'] = ""; 
		}elseif($oldNews->getCategory()=="Giochi"){
			$replacements['<checked_eventi_ph/>'] = "";
			$replacements['<checked_giochi_ph/>'] = "checked=\"checked\" ";
			$replacements['<checked_hardware_ph/>'] = "";
		}elseif($oldNews->getCategory()=="Hardware"){
			$replacements['<checked_eventi_ph/>'] = "";
			$replacements['<checked_giochi_ph/>'] = "";
			$replacements['<checked_hardware_ph/>'] = "checked=\"checked\" ";
		}

		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
		// echo "replacements completati<br/>";
	}

}

$jointValidation_error_message = getValidationErrorsHtml($validation_error_messages);
$jointSuccess_error_messages = getSuccessMessagesHtml($success_messages);
$homePage = str_replace("<messaggi_form_ph/>", $jointValidation_error_message . "\n" . $jointSuccess_error_messages, $homePage);


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $oldNews ? $oldNews->getTitle() : "");

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>