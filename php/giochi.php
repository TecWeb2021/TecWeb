<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giochiTemplate.html");




function createGameHTMLItem($game, $isAdmin=false){
	$item=file_get_contents("../html/templates/gamesListItemTemplate.html");
	
	$replacements = array(
		"<game_name_ph/>" => $game->getName(),
		"<game_date_ph/>" => $game->getPublicationDate(),
		"<game_vote_ph/>" => $game->getVote(),
		"<game_sinossi_ph/>" => $game->getSinopsis(),
		"<img_path_ph/>" => "../".$game->getImage()->getPath(),
		"<img_alt_ph/>" => $game->getImage()->getAlt(),
		"<game_scheda_url_ph/>" => "gioco_scheda.php?game=".strtolower($game->getName()),
		"<game_edit_ph/>" => "edit_gioco.php?game=".strtolower($game->getName())
	);

	foreach ($replacements as $key => $value) {
		$item = str_replace($key, $value, $item);
	}
	
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


$yearRangeStart = isset($_REQUEST['year1']) ? $_REQUEST['year1'] : null;
#sanitize
$yearRangeEnd = isset($_REQUEST['year2']) ? $_REQUEST['year2'] : null;
#sanitize

//echo "anni: ".$yearRangeStart." - ".$yearRangeEnd."<br/>";

$consoles_pre =isset($_REQUEST['console']) ? $_REQUEST['console'] : null;
#sanitize
$genres_pre =isset($_REQUEST['genere']) ? $_REQUEST['genere'] : null;
#sanitize


# Chiedo al server una lista delle notizie
$list=$dbAccess->getGamesList($gameName, $yearRangeStart, $yearRangeEnd, $order, $consoles_pre, $genres_pre);
# Unisco le notizie in una lista html 
$gamesDivsString=createGamesDivs($list, $isAdmin);
# Metto la lista al posto del placeholder
$homePage=preg_replace("/\<games_divs_ph\/\>/",$gamesDivsString,$homePage);

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", "giochi", $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;
?>