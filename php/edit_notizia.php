


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
if($allOk && !isset($_REQUEST['news'])){
	$homePage="Non è stata specificata alcuna notizia";
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
	$homePage="La notizia $newsToBeModifiedName specificata non esiste";
	$allOk=false;
}

//se c'è elimina non c'è il resto quindi succede solo quello che c'è nell'if qua sotto, almeno credo
if(isset($_REQUEST['elimina'])){
	$newsToBeDeletedName=$_REQUEST["elimina"];
	echo "elimina: ".$newsToBeDeletedName."<br/>";
	$opResult=$dbAccess->deleteNews($newsToBeDeletedName);
	if($opResult){
		$homePage="eliminazione della notizia $newsToBeDeletedName riuscita";
	}else{
		$homePage="eliminazione della notizia $newsToBeDeletedName fallita";
	}
}
	
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
	if( isset($_REQUEST['titolo']) || isset($_REQUEST['testo']) || isset($_REQUEST['tipologia']) || isset($_REQUEST['alternativo']) ){
		echo "almeno un valore è stato rilevato<br/>";
		//i nuovi valori per il gioco sono stati tutti rilevati
		$new_newsTitle =  isset($_REQUEST['titolo']) ? $_REQUEST['titolo'] : null;
		$new_newsText = isset($_REQUEST['testo']) ? $_REQUEST['testo'] : null;
		// alcuni valori li riprendo dalla vecchia notizia
		$new_newsAuthor = $oldNews->getAuthor();
		$new_newsEditDateTime = $oldNews->getLastEditDateTime();
		$new_newsCategory = isset($_REQUEST['tipologia']) ? $_REQUEST['tipologia'] : null;
		$new_newsAlt = isset($_REQUEST['alternativo']) ? $_REQUEST['alternativo'] : null;
		$new_newsGame = null;
		if($new_newsCategory == "Giochi"){
			$new_newsGame = isset($_REQUEST['searchbar']) ? $_REQUEST['searchbar'] : null;
		}
	
		// l'immagine è un caso particolare: se l'utente ne inserisce una 	devo creare un oggetto che la rappresenti, altrimenti, visto che 	non è stata messa nell'html durante le sostituzioni, devo 	prendermi l'oggetto immagine di $oldGame
		$new_newsImage=null;
		$imageOk=false;

		//errore 4: non è stata caricata alcuna immagine
		if(isset($_FILES['immagine']) && $_FILES['immagine']['error']!=4 ){
			echo "l'utente ha inserito una nuova immagine"."<br/>";
			

			$imagePath=saveImageFromFILES($dbAccess,'immagine');
			if($imagePath){
				$new_newsImage=new Image($imagePath,$new_newsAlt);
				$imageOk=true;
			}else{
				echo "salvataggio dell'immagine fallito"."<br/>";

			}
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
		if(false /*$new_newsImage == null*/){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}
		if($new_newsAlt == null){
			$error_message = $error_message . $error_messages['alternativo'] . "<br/>";
		}
		if($new_newsCategory == "Giochi" && $new_newsGame== null){
			$error_message = $error_message . $error_messages['gioco'] . "<br/>";
		}

		if($error_message != ""){
			echo $error_message;
		}else{

			if($new_newsImage == null){
				echo "l'utente non ha inserito una nuova immagine"."<br/>";
				//prendo la vecchia immagine
				$new_newsImage=$oldNews->getImage();
				$imageOk=true;
			}

		}

		if($imageOk){
			$newNews=new News($new_newsTitle, $new_newsText, $new_newsAuthor, $new_newsEditDateTime, $new_newsImage, $new_newsCategory, $new_newsGame);
			$overwriteResult = $dbAccess->overwriteNews($newsToBeModifiedName, $newNews);
			if($overwriteResult==true){
				echo "overwrite su db riuscito"."<br/>";
			}else{
				echo "overwrite su db fallito"."<br/>";
			}
			
		}
	
		
		

		//qui faccio i replacement dei placeholder in base a quello che mi è stato comunicato dall'utente
		//mancano i replacement delle checkboxes
		$replacements = array(
			"<news_title_ph/>" => $new_newsTitle ? $new_newsTitle : $oldNews->getTitle(),
			"<content_ph/>" => $new_newsText ? $new_newsText : $oldNews->getContent(),
			"<img_alt_ph/>" => $new_newsAlt ? $new_newsAlt : $oldNews->getAlt(),
			"<opzioni_ph/>" => createGamesOptions($dbAccess),
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
	
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
		echo "replacements completati<br/>";

		//lo script per ora è fatto male: ogni volta che la pagina è stata caricata sovrascrivo il gioco sul database
		//Se l'utente non ha modificato i valori sovrascrivo quelli vecchi con altri identici
	}else{
		echo "nessun valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";

		/*
		// controllo quale valore non è stato inserito
		if(!isset($_REQUEST['titolo'])){
			echo "titolo non inserito<br/>";
		}elseif(!isset($_REQUEST['testo'])){
			echo "testo non inserito<br/>";
		}elseif(!isset($_REQUEST['alternativo'])){
			echo "alt non inserito<br/>";
		}
		*/

		//qui faccio i replacement dei placeholder in base ai valori del gioco che si vuole modificare
		//per ora mancano le sostituzioni rigaurdanti le checkbox perchè sono complicate
		$replacements = array(
			"<news_title_ph/>" => $oldNews->getTitle(),
			"<content_ph/>" => $oldNews->getContent(),
			"<opzioni_ph/>" => createGamesOptions($dbAccess),
			"<game_name_ph/>" => $oldNews->getGameName() ? $oldNews->getGameName() : ""
		);

		if($oldImage=$oldNews->getImage()){
			$replacements['<img_alt_ph/>'] = $oldImage->getAlt();
		}else{
			$replacements['<img_alt_ph/>'] = "";
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

	
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
		echo "replacements completati<br/>";
	}

}
			


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $oldNews ? $oldNews->getTitle() : "");

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>