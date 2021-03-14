


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

$homePage=file_get_contents("../html/templates/formGiocoTemplate.html");




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
	//ora posso popolare la pagina con gli attributi del gioco

	// ora devo raccogliere i valori che mi sono stati passati
	// devono essere presenti tutti i valori tranne l'immagine
	// sovrascriverò i valori del gioco nel database anche se sono uguali a quelli già presenti

	//se almeno un valore, a parte l'immagine, non è settato, vuol dire che sono arrivato a questa pagina da un altra, e quindi non serve che mi metta a raccogliere i valori e a scriverli nel database

	//verifico che tutti i valori siano settati
	//devo ancora implementare la gestione dell'alt dell'immagine
	if(isset($_REQUEST['nome']) && isset($_REQUEST['data']) && isset($_REQUEST['pegi']) && isset($_REQUEST['descrizione']) && isset($_REQUEST['recensione']) && isset($_REQUEST['alternativo']) && isset($_REQUEST['voto']) && isset($_FILES['immagine'])){
		echo "i nuovi valori per il gioco sono stati tutti rilevati<br/>";
		//i nuovi valori per il gioco sono stati tutti rilevati
		$new_gameName = $_REQUEST['nome'];
		$new_gamePublicationDate = $_REQUEST['data'];
		$new_gameAgeRange = $_REQUEST['pegi'];
		$new_gameSinopsis = $_REQUEST['descrizione'];
		$new_gameReview = $_REQUEST['recensione'];
		$new_gameAlt = $_REQUEST['alternativo'];
		$new_gameVote = $_REQUEST['voto'];
		$new_gameConsoles = isset($_REQUEST['console']) ? $_REQUEST['console'] : array();
		$new_gameGenres = isset($_REQUEST['genere']) ? $_REQUEST['genere'] : array();
		echo "console: ";
		print_r($new_gameConsoles);
		echo "<br/>";
		echo "generi: ";
		print_r($new_gameGenres);
		echo "<br/>";


		
		$selected_consoles=array();
		//creao un array che per ogni posizione indica se la console in quella posizione è stata selezionata
		foreach (Game::$possible_consoles as $key => $value) {

			$selected_consoles[$key] = in_array($value, $new_gameConsoles);
			echo "$value is ".($selected_consoles[$key] ? "true" : "false")."<br/>";
		}

		
		$selected_genres=array();
		//creao un array che per ogni posizione indica se il genere in quella posizione è stato selezionato
		foreach (Game::$possible_genres as $key => $value) {
			$selected_genres[$key] = in_array($value, $new_gameGenres);
		}
		
	
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
		
		if($imageOk){
		
			$newGame=new Game($new_gameName, $new_gamePublicationDate, $new_gameVote, $new_gameSinopsis, $new_gameAgeRange, $new_gameReview, $new_gameImage, $new_gameConsoles, $new_gameGenres);

			$opResult = $dbAccess->addGame($newGame);
			echo "risultato salvataggio gioco su db: ".($opResult==null ? "null" : $opResult)."<br/>";
		}

		$replacements = array(
			"<game_name_ph/>" => $new_gameName,
			"<developer_ph/>" => "casa di sviluppo", //non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<date_ph/>" => $new_gamePublicationDate,
			"<age_range_ph/>" => $new_gameAgeRange,
			"<img_alt_ph/>" => $new_gameAlt, //non l'ho messo perchè non è detto che l'immagine esista quindi ci vuole un controllo
			"<vote_ph/>" => $new_gameVote,
			"<dlc_ph/>" => "dlcs del gioco",//non l'ho messo perchè per ora non ha una controparte tra gli attributi del gioco
			"<sinopsis_ph/>" => $new_gameSinopsis,
			"<review_ph/>" => $new_gameReview
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
		echo "i nuovi valori per il gioco non sono stati rilevati tutti, probabilmente arrivo da un'altra pagina<br/>";
		//i nuovi valori per il gioco non sono stati rilevati tutti, ritengo quindi che l'utente sia arrivato a questa pagina da un'altra e non abbia ancora potuto inviare le modifiche (o i dati già presenti, quelli scritti con la sostituzione dei placeholder)

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
		}

		

		$replacements = array(
			"<game_name_ph/>" => "",
			"<developer_ph/>" => "", 
			"<date_ph/>" => "",
			"<img_alt_ph/>" => "",
			"<vote_ph/>" => "",
			"<age_range_ph/>" => "",
			"<dlc_ph/>" => "",
			"<sinopsis_ph/>" => "",
			"<review_ph/>" => ""
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