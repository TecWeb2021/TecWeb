<?php
// per adesso questo script contiene un mix di funzioni utili in vari altri script
// potrebbe convenire separare le funzionalità in più script diversi

require_once("dbConnection.php");
require_once("classes/user.php");

function replace($subject){
  $subject=preg_replace("/Giochi\.html/","giochi.php",$subject);
  $subject=preg_replace("/Home\.html/","home.php",$subject);
  $subject=preg_replace("/Notizie\.html/","notizie.php",$subject);
  $subject=preg_replace("/Forum\.html/","forum.php",$subject);
  $subject=preg_replace("/\"([A-Za-z]*)\.html\"/","\"$1.php\"",$subject);
  return $subject;
}

function createBasePage($templatePath, $page, $dbAccess, $pageParam = ""){
	if(isset($_REQUEST['logout'])){
		logout();
	}

	$user=getLoggedUser($dbAccess);

	$basePage=generatePageTopAndBottom($templatePath, $page, $user, $pageParam);
	
	$optionsListString=createGamesOptions($dbAccess);
	//$optionsListString = $optionsListString.""
	// inserisco i possibili valori per la barra di ricerca
	$basePage=str_replace("<opzioni_ph/>", $optionsListString, $basePage);

	return $basePage;

}

function generatePageTopAndBottom($templatePath, $page, $user, $pageParam = "", $defaultUserImagePath="images/login.png"){

	if(isset($_REQUEST['tendina']) && ($_REQUEST['tendina']=='true' || $_REQUEST['tendina']=='false')){
		$templatePath="../html/templates/top_and_bottomTemplateNoJS.html";
	}
	$base=file_get_contents($templatePath);
	$url=$_SERVER['REQUEST_URI'];

	if (strpos($url, '?') !== false) {
    	$url=$url."&";
	}else{
		$url=$url."?";
	}
	$base=str_replace("<this_url_ph/>", $url, $base);

	#completare i replacements
	$titleAndBreadcrumbReplacements = array(
		"home.php" => ["Home","Home"],
		"giochi.php" => ["Giochi","Giochi"],
		"notizie.php" => ["Notizie","Notizie"],
		"notizia.php" => ["<page_param_ph/>","<a class=\"link_breadcrumb\" href=\"notizie.php\">Notizie</a> > <page_param_ph/>"],
		"edit_gioco.php" => ["Modifica gioco - <page_param_ph/>","<a class=\"link_breadcrumb\" href=\"giochi.php\">Giochi</a> > Modifica <page_param_ph/>"],
		"edit_notizia.php" => ["Modifica notizia - <page_param_ph/>","<a class=\"link_breadcrumb\" href=\"notizie.php\">Notizie</a> > Modifica <page_param_ph/>"],
		"form_gioco.php" => ["Aggiungi gioco","<a class=\"link_breadcrumb\" href=\"home.php\">Home</a> > <a class=\"link_breadcrumb\" href=\"profilo.php\">Admin</a> > Aggiungi gioco"],
		"form_notizia.php" => ["Aggiungi notizia","<a class=\"link_breadcrumb\" href=\"home.php\">Home</a> > <a class=\"link_breadcrumb\" href=\"profilo.php\">Admin</a> > Aggiungi notizia"],
		"edit_profilo.php" => ["Modifica profilo","<a class=\"link_breadcrumb\" href=\"home.php\">Home</a> > <a class=\"link_breadcrumb\" href=\"profilo.php\">Profilo</a> > Modifica profilo"],
		"gioco_notizie.php" => ["Notizie - <page_param_ph/>","<a class=\"link_breadcrumb\" href=\"giochi.php\">Giochi</a> > <page_param_ph/> > Notizie"],
		"gioco_recensione.php" => ["Recensione - <page_param_ph/>","<a class=\"link_breadcrumb\" href=\"giochi.php\">Giochi</a> > <page_param_ph/> > Recensione"],
		"gioco_scheda.php" => ["Scheda gioco - <page_param_ph/>","<a class=\"link_breadcrumb\" href=\"giochi.php\">Giochi</a> > <page_param_ph/> > Scheda del gioco"],
		"lista_utenti.php" => ["Lista utenti","<a class=\"link_breadcrumb\" href=\"home.php\">Home</a> > <a class=\"link_breadcrumb\" href=\"profilo.php\">Admin</a> > Lista utenti"],
		"login.php" => ["Login","<a class=\"link_breadcrumb\" href=\"home.php\">Home</a> > Login"],
		"profilo.php" => ["Profilo","<a class=\"link_breadcrumb\" href=\"home.php\">Home</a> > Profilo"],
		"registrati.php" => ["Registrati","<a class=\"link_breadcrumb\" href=\"home.php\">Home</a> > Registrati"]
	);

	foreach ($titleAndBreadcrumbReplacements as $key => $value) {
		// il seguente confronto non è proprio una cosa giusta. Per esempio confrontare notizia e notizia_x avrebbe esito positivo.
		// 5 è la posizione del primo carattere dopo /php/
		if(strpos(explode("php/",$url)[1], $key) === 0){
			//costruisco le basi del titolo e del breadcrumb
			//aggiungo ad ogni titolo la scritta ALLGames, cioò il nome del sito"
			$title = $value[0]." - ALLGames";
			$breadcrumb = $value[1];
			//metto il parametro passato all'interno del titolo e del breadcrumb
			$title = str_replace("<page_param_ph/>", $pageParam, $title);
			$breadcrumb = str_replace("<page_param_ph/>", $pageParam, $breadcrumb);
			$base=str_replace("<page_title_ph/>", $title, $base);
			$base=str_replace("<page_breadcrumb_ph/>", $breadcrumb, $base);
		}
	}

	//mettere qui tutte le corrispondenze necessarie
	$onloadReplacements = array(
		"giochi.php" => "onload=\"preparaFiltri();\"",
		"notizia.php" => "onload=\"preparaFiltri();\"",
		"home.php" => "onload=\"removeNoJs();\"",
		"form_notizia.php" => "onload=\"handleClick();\"",
		"edit_notizia.php" => "onload=\"handleClick();\""
	);
	foreach ($onloadReplacements as $key => $value) {
		if(strpos($url, $key)!=false){
			$base=str_replace("<body_onload_ph/>", $value, $base);
		}
	}
	//se non ha matchato nessuna pagina tolgo semplicemente il placeholder
	$base=str_replace("<body_onload_ph/>", "", $base);


	$possiblePages=array("home","giochi","notizie");
	if(!in_array($page, $possiblePages)){
		$page=null;
	}
	$base=preg_replace("/\<\/$page\_active\>/", "", $base);
	$base=preg_replace("/\<$page\_active\>/", "", $base);

	foreach ($possiblePages as $value) {
		if($page!=$value){
			$base=preg_replace("/\<$value\_active\>class\=\"active\"\<\/$value\_active\>/", "", $base);
		}
	}

	
	if($user){
		if($user->getImage()){
			$base=str_replace("<user_img_path_ph/>", "../".getSafeImage($user->getImage()->getPath()), $base);
		}

		$replacements = array(
			"/\<not_logged_in\>.*\<\/not_logged_in\>/" => "",
			"/\<logged\_in\>/" => "",
			"/\<\/logged\_in\>/" => "",
			"/\<username\_ph\/\>/" => $user->getUsername(),
			"/\<profile\_pic\_redirect\_url\_ph\/\>/" => "profilo.php"
		);

		$base = preg_replace(array_keys($replacements), array_values($replacements), $base);
		
	}else{

		$replacements = array(
			"/\<logged_in\>.*\<\/logged_in\>/" => "",
			"/\<not\_logged\_in\>/" => "",
			"/\<\/not\_logged\_in\>/" => "",
			"/\<profile\_pic\_redirect\_url\_ph\/\>/" => "login.php"
		);

		$base = preg_replace(array_keys($replacements), array_values($replacements), $base);
	}
	#la riga qua sotto fa quello che deve solo se il tag non è giù stato sostiuito, quindi solo l'utente non ha un'immagine
	$base=str_replace("<user_img_path_ph/>","../".getSafeImage($defaultUserImagePath),$base);



	if(isset($_REQUEST['tendina'])){
		if($_REQUEST['tendina']=='true'){
			$base=str_replace("topnav","topnav responsive",$base);	
			$base=str_replace("<tendina_bool_ph/>","false",$base);
		}elseif($_REQUEST['tendina']=='false'){
			$base=str_replace("<tendina_bool_ph/>","true",$base);
		}
	}

	return $base;
}


function getHash($username, $password){
	$inputString=$username.$password;
	$hashValue=hash("md5",$inputString);
	return $hashValue;
}


function logout(){
	$logout=$_REQUEST['logout'];
	#sanitize;
	if($logout='true' && isset($_COOKIE['login'])){
		setcookie("login","");
		header("Refresh:0");
	}
}

function getLoggedUser($dbAccess){
	$user=null;
	if(isset($_COOKIE['login'])){
		$hash=$_COOKIE['login'];
		#sanitize
		$user = $dbAccess->getUserByHash($hash);
	}
	return $user;
}

function saveImageFromFILES($dbAccess, $imgReceiveName, $minResolutionRatio = 0, $maxResolutionRateo =INF, $uploaddir = '../images/'){
	//questa funzione ritorna il percorso in cui l'immagine è salvata
	// questa funzione non salva l'immagine nel db, la salva solamente nel filesystem, senza alt
	
	$image= isset($_FILES["$imgReceiveName"]) ? $_FILES["$imgReceiveName"] : null;
	

	//errore 4: non è stata caricata alcuna immagine
	if(!$image || $_FILES["$imgReceiveName"]['error'] == 4){
		return null;
	}


	$imageSizeDetails = getimagesize($_FILES[$imgReceiveName]['tmp_name']);
	$xSize = $imageSizeDetails[0];
	$ySize = $imageSizeDetails[1];
	$resRateo = $ySize / $xSize;
	
	if($resRateo < $minResolutionRatio || $resRateo > $maxResolutionRateo){
		return null;
	}
	#Recupero il percorso temporaneo del file
	$image_tmp_location = $image['tmp_name'];
	#recupero il nome originale del file caricato

	$originalName=$image['name'];

	#ricavo nome immagine col numero più alto presente nel database
	$rawNum = $dbAccess->getLastImageId();
	$maxNum = $rawNum !== null ? $rawNum : -1;
	

	#ricavo il nome da assegnare al nuovo file
	$newNumber=$maxNum+1;
	$extension=end(explode('.', $originalName));
	$newFileName=$newNumber.".".$extension;
	$fileDestination=$uploaddir . $newFileName;
	$imgSaveResult=move_uploaded_file($image_tmp_location, $fileDestination);

	if($imgSaveResult){
		$filePath="images"."/".$newFileName;
		return $filePath;
	}else{
		return false;
	}
}

// returns the path if it corresponds to an image saved on the server, a "not available" image else 
// the path is supposed to be given relativto the root directory
function getSafeImage($path, $defaultPath = "images/imagenotavailable.png"){
	if(is_file("../".$path)){
		return $path;
	}else{
		return $defaultPath;
	}
}

function createGamesOptions($dbAccess, $selectedName=null, $template="<option value=\"<option_name_ph/>\" <option_selected_ph/> />"){
		//questa funzione crea una stringa in html che rappresenta come opzioni per un campo input i nomi dei vari giochi

	$gamesList=$dbAccess->getGamesList();
	if(!$gamesList){
		return "";
	}
	$stringsArray=array();
	//se è selezionato il valore vuoto aggiungo un valore vuoto selezionato, supponendo che non ci siano valori vuoti tra i nomi dei giochi
	//il selectedName è utile solo se le opzioni verranno usate per un tag select
	if($selectedName===""){
		array_push($stringsArray, "<option value=\"\" selected=\"selected\" />");
	}
	foreach ($gamesList as $game) {
		$singleString=$template;
		$replacements = array(
			"<option_name_ph/>" => $game->getName(),
			"<option_selected_ph/>" => $game->getName() == $selectedName ? "selected=\"selected\"" : ""
		);

		$singleString = str_replace(array_keys($replacements), array_values($replacements), $singleString);
		array_push($stringsArray, $singleString);
	}
	$joinedItems = implode("", $stringsArray);
	return $joinedItems;
}


function createNewsOptions($dbAccess, $selectedName=null, $template="<option value=\"<option_name_ph/>\" <option_selected_ph/> />"){
		//questa funzione crea una stringa in html che rappresenta come opzioni per un campo input i nomi dei vari giochi

	$newsList=$dbAccess->getNewsList();
	if(!$newsList){
		return "";
	}
	$stringsArray=array();
	//se è selezionato il valore vuoto aggiungo un valore vuoto selezionato, supponendo che non ci siano valori vuoti tra i nomi dei giochi
	//il selectedName è utile solo se le opzioni verranno usate per un tag select
	if($selectedName===""){
		array_push($stringsArray, "<option value=\"\" selected=\"selected\" />");
	}
	foreach ($newsList as $news) {
		$singleString=$template;
		$replacements = array(
			"<option_name_ph/>" => $news->getTitle(),
			"<option_selected_ph/>" => $news->getTitle() == $selectedName ? "selected=\"selected\"" : ""
		);
		$singleString = str_replace(array_keys($replacements), array_values($replacements), $singleString);
		array_push($stringsArray, $singleString);
	}
	$joinedItems=implode("", $stringsArray);
	return $joinedItems;
}


// si può utilizzare per controllare la correttezza delle stringhe
function checkString($string, $type){

	$patterns = array(
	    "nome" => ["/^([\w\s]){2,20}$/", "Inserire il nome del gioco"],
	    "sviluppo" => ["/^([\w\s]){5,30}$/", "Inserire il nome della casa di sviluppo"],
	    "pegi" => ["/^(3|7)$|^1(2|6|8)$/", "Possibili valori di PEGI: 3,7,12,16,18"],
	    "voto" => ["/^([0-5]{1}|[0-4]{1}\.[1-9]{1})$/", "Voto da 0 a 5"],
	    "prequel" => ["/^([\w\s]){2,20}$/", "Inserire il nome del prequel"],
	    "sequel" => ["/^([\w\s]){2,20}$/", "Inserire il nome del sequel"],
	    "dlc" => ["/^([\w\s]){2,20}$/", "Inserire il nome del dlc"],
	    "data" => ["/.*/", "Data non valida"],

	    "descrizione" => ["/.{25,}/", "Inserire la descrizione"],
	    "recensione" => ["/.{25,}/", "Inserire la recensione"],
	    "alternativo" => ["/^([\w\s]){0,50}$/", "Alt lungo massimo 50 caratteri"],

	    "titolo" => ["/^([\w\s\'\,\.\"]){10,40}$/", "Inserire il titolo della notizia"],
	    "testo" => ["/.{25,}/", "Inserire il testo della notizia"],
	    "tipologia" => News::$possible_categories,
	    "immagine" => ["/./", "Nessun file selezionato"],

	    "nomeUtente" => ["/^([\w]){4,15}$/", "Inserire il nome utente"],
	    "password" => [ "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,20}$|^user$|^admin$/", "Inserire la password"],
	    "repeatpassword" => [ "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,20}$|^user$|^admin$/", "Le password non combaciano"],
	    "email" => ["/^\w{2}\w*(\.?\w+)*@\w{2}\w*(\.?\w+)*(\.\w{2,3})+$/", "Inserire la mail"]
	);

	if(!array_key_exists($type, $patterns)){
		return null;
	}
	$res = preg_match($patterns[$type][0], $string) === 1 ? true : false;
	if($res === false){
		return $patterns[$type][1];
	}
	return $res;
}

function getOriginPage(){
	if(isset($_SERVER['HTTP_REFERER'])){
		// sanitize
		return $_SERVER['HTTP_REFERER'];
	}else{
		return null;
	}
}

function dateToText($date){
	$parts = explode("-", $date);
	$monthString = "";
	switch($parts[1]){
		case "01":
			$monthString = "gennaio";
			break;
		case "02":
			$monthString = "febbraio";
			break;
		case "03":
			$monthString = "marzo";
			break;
		case "04":
			$monthString = "aprile";
			break;
		case "05":
			$monthString = "maggio";
			break;
		case "06":
			$monthString = "giugno";
			break;
		case "07":
			$monthString = "luglio";
			break;
		case "08":
			$monthString = "agosto";
			break;
		case "09":
			$monthString = "settembre";
			break;
		case "10":
			$monthString = "ottobre";
			break;
		case "11":
			$monthString = "novembre";
			break;
		case "12":
			$monthString = "dicembre";
			break;
	}
	return $parts[2] . " " . $monthString . " " . $parts[0];
}

function dateTimeToText($dateTime){
	$parts = explode(" ", $dateTime);
	return dateToText($parts[0]) . " " . $parts[1];
}




?>