<?php
require_once "replacer.php";
require_once "dbConnection.php";
require_once "classes/game.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giochiTemplate.html");

function createGameHTMLItem($game, $isAdmin=false){
	$item=file_get_contents("../html/templates/gamesListItemTemplate.html");
	
	echo "gamevoe: ".$game->getVote();
	$replacements = array(
		"<game_name_ph/>" => $game->getName(),
		"<game_date_ph/>" => dateToText($game->getPublicationDate()),
		"<game_vote_ph/>" => $game->getVote(),
		"<game_sinossi_ph/>" => $game->getSinopsis(),
		"<img_path_ph/>" => "../".getSafeImage($game->getImage1()->getPath()),
		"<img_alt_ph/>" => $game->getImage1()->getAlt(),
		"<game_scheda_url_ph/>" => "gioco_scheda.php?game=".strtolower($game->getName()),
		"<game_edit_ph/>" => "edit_gioco.php?game=".strtolower($game->getName())
	);

	$item = str_replace(array_keys($replacements), array_values($replacements), $item);

	/*foreach ($replacements as $key => $value) {
		$item = str_replace($key, $value, $item);
	}*/
	
	if($isAdmin){
		$item=str_replace("<admin_func_ph>","",$item);
		$item=str_replace("</admin_func_ph>","",$item);
	}else{
		$item=preg_replace("/\<admin_func_ph\>.*\<\/admin_func_ph\>/","",$item);
	}

	return $item;
}



function createGamesDivs($gamesList, $isAdmin=false){
	if(!$gamesList){
		return "";
	}
	$stringsArray=array();
	foreach($gamesList as $entry){
		
		$s=createGameHTMLItem($entry, $isAdmin);
		array_push($stringsArray, $s);
	}
	$joinedItems=implode(" ", $stringsArray);
	return $joinedItems;
}

function replaceConsoleCheckboxes($selectedArray, &$page){
	// print_r($selectedArray);
	// questo array deve corrispondere ai valori delle checkboxes, anche nell'ordine
	// $possible_consoles = array("PS4","PS5","Xbox One","Xbox Series X/S","Nintendo Switch");
	foreach (Game::$possible_consoles as $key => $value) {
		$isChecked = in_array($value, $selectedArray);
		$to_substitute = "";
		if($isChecked === true){
			echo $value ."<br/>";
			$to_substitute = "checked = \"checked\"";
		}
		$page = str_replace("<checked_console_".$key."/>", $to_substitute, $page);
	}

}

function replaceGenresCheckboxes($selectedArray, &$page){
	// questo array deve corrispondere ai valori delle checkboxes, anche nell'ordine
	// $possible_genres = array("FPS","Horror","GDR","Avventura","Puzzle","Azione");
	foreach (Game::$possible_genres as $key => $value) {
		$isChecked = in_array($value, $selectedArray);
		$to_substitute = "";
		if($isChecked === true){
			$to_substitute = "checked = \"checked\"";
			
		}
		$page = str_replace("<checked_genere_".$key."/>", $to_substitute, $page);
	}

}

function replaceYear1Checkboxes($selectedYear, &$page){
	// questo array deve corrispondere ai valori delle checkboxes, anche nell'ordine
	$possible_years = array("2020","2019","2018","2017","2016");
	foreach ($possible_years as $key => $value) {
		$isChecked = $value === $selectedYear;
		$to_substitute = "";
		if($isChecked === true){
			$to_substitute = "checked = \"checked\"";
		}
		$page = str_replace("<checked_anno1_".$key."/>", $to_substitute, $page);
	}

}

function replaceYear2Checkboxes($selectedYear, &$page){
	// questo array deve corrispondere ai valori delle checkboxes, anche nell'ordine
	$possible_years = array("2020","2019","2018","2017","2016");
	foreach ($possible_years as $key => $value) {
		$isChecked = $value === $selectedYear;
		$to_substitute = "";
		if($isChecked === true){
			$to_substitute = "checked = \"checked\"";
		}
		$page = str_replace("<checked_anno2_".$key."/>", $to_substitute, $page);
	}

}

$user=getLoggedUser($dbAccess);
$isAdmin=$user && $user->isAdmin() ? true : false; 


$gameName= isset($_REQUEST['searchbar']) ? $_REQUEST['searchbar'] : null;
#sanitize
$order= isset($_REQUEST['ordine']) ? $_REQUEST['ordine'] : null;
// possibili valori in input dall'html: "Alfabetico", "Voto 4+", "Ultimi usciti"

//converto gli input dell'utente in valori adatti alla funzione getGamesList
switch($order){
	case "Alfabetico":
		$order = "alfabetico";
		break;
	case "Voto 4+":
		$order = "voto";
		break;
	case "Ultimi usciti":
		$order = "data";
		break;
	default:
		$order = null;
		break;
}

echo "ordine giochi: ".$order."<br/>";

// FILTRI

$yearRangeStart = isset($_REQUEST['year1']) ? $_REQUEST['year1'] : null;
replaceYear1Checkboxes($yearRangeStart, $homePage);
#sanitize
$yearRangeEnd = isset($_REQUEST['year2']) ? $_REQUEST['year2'] : null;
replaceYear2Checkboxes($yearRangeEnd, $homePage);
#sanitize

//echo "anni: ".$yearRangeStart." - ".$yearRangeEnd."<br/>";

$consoles_pre = isset($_REQUEST['console']) ? $_REQUEST['console'] : array();
replaceConsoleCheckboxes($consoles_pre, $homePage);
#sanitize
$genres_pre = isset($_REQUEST['genere']) ? $_REQUEST['genere'] : array();
replaceGenresCheckboxes($genres_pre, $homePage);
#sanitize


# Chiedo al server una lista delle notizie
$list = $dbAccess->getGamesList($gameName, $yearRangeStart, $yearRangeEnd, $order, $consoles_pre, $genres_pre);
# Unisco le notizie in una lista html 
$gamesDivsString = createGamesDivs($list, $isAdmin);
# Metto la lista al posto del placeholder
$homePage = preg_replace("/\<games_divs_ph\/\>/",$gamesDivsString,$homePage);

$basePage = createBasePage("../html/templates/top_and_bottomTemplate.html", "giochi", $dbAccess);

$basePage = str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage = replace($basePage);

echo $basePage;
?>