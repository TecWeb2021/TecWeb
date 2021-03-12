


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
	if( isset($_REQUEST['titolo']) && isset($_REQUEST['testo']) ){
		echo "i nuovi valori per la notizia sono stati tutti rilevati<br/>";
		//i nuovi valori per il gioco sono stati tutti rilevati
		$new_newsTitle =  $_REQUEST['titolo'];
		$new_newsText = $_REQUEST['testo'];
		// alcuni valori li riprendo dalla vecchia notizia
		$new_newsAuthor = $oldNews->getAuthor();
		$new_newsEditDateTime = $oldNews->getLastEditDateTime();
		$new_newsCategory = $oldNews->getCategory();
	
		// l'immagine è un caso particolare: se l'utente ne inserisce una 	devo creare un oggetto che la rappresenti, altrimenti, visto che 	non è stata messa nell'html durante le sostituzioni, devo 	prendermi l'oggetto immagine di $oldGame
		$new_newsImage=null;
		if(/* metto un false perchè ho bisogno di inserire un'immagine nel form, perchè lo vuole lo script js, senza che venga rilevata dal php*/false && isset($_REQUEST['immagine'])){
			//qui devo creare il nuovo oggetto immagine, oltre che 	salvare l'immagine caricata
		}else{
			$new_newsImage=$oldNews->getImage();
		}
	
	
		$newNews=new News($new_newsTitle, $new_newsText, $new_newsAuthor, $new_newsEditDateTime, $new_newsImage, $new_newsCategory);

		$overwriteResult = $dbAccess->overwriteNews($newsToBeModifiedName, $newNews);
		echo "risultato overwrite: ".$overwriteResult."<br/>";

		//mancano i replacement delle checkboxes
		$replacements = array(
			"<news_title_ph/>" => $new_newsTitle,
			"<content_ph/>" => $new_newsText
		);
	
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
		echo "replacements completati<br/>";

		//lo script per ora è fatto male: ogni volta che la pagina è stata caricata sovrascrivo il gioco sul database
		//Se l'utente non ha modificato i valori sovrascrivo quelli vecchi con altri identici
	}else{
		echo "i nuovi valori per il gioco non sono stati rilevati tutti, probabilmente arrivo da un'altra pagina<br/>";
		//i nuovi valori per il gioco non sono stati rilevati tutti, ritengo quindi che l'utente sia arrivato a questa pagina da un'altra e non abbia ancora potuto inviare le modifiche (o i dati già presenti, quelli scritti con la sostituzione dei placeholder)

		// controllo quale valore non è stato inserito
		if(!isset($_REQUEST['titolo'])){
			echo "titolo non inserito<br/>";
		}elseif(!isset($_REQUEST['testo'])){
			echo "testo non inserito<br/>";
		}

		//per ora mancano le sostituzioni rigaurdanti le checkbox perchè sono complicate
		$replacements = array(
			"<news_title_ph/>" => $oldNews->getTitle(),
			"<content_ph/>" => $oldNews->getContent()
		);
	
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