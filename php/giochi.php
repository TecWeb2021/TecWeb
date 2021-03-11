<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giochiTemplate.html");




function createGameHTMLItem($game, $isAdmin=false){
	$item=file_get_contents("../html/templates/gamesListItemTemplate.html");
	
	$item=preg_replace("/\<game_name_ph\/\>/",$game->getName(),$item);
	$item=preg_replace("/\<game_date_ph\/\>/",$game->getPublicationDate(),$item);
	$item=preg_replace("/\<game_vote_ph\/\>/",$game->getVote(),$item);
	$item=preg_replace("/\<game_sinossi_ph\/\>/",$game->getSinopsis(),$item);
	$item=preg_replace("/\<img_path_ph\/\>/","../".$game->getImage()->getPath(),$item);
	$item=preg_replace("/\<img_alt_ph\/\>/",$game->getImage()->getAlt(),$item);
	$item=preg_replace("/\<game_scheda_url_ph\/\>/","gioco_scheda.php?game=".strtolower($game->getName()),$item);
	$item=preg_replace("/\<game_edit_ph\/\>/","edit_gioco.php?game=".strtolower($game->getName()),$item);
	
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
$possibleOrders=array("alfabetico", "voto", "data_uscita");
if(!in_array($order, $possibleOrders, true)){
	$order=null;
}

$yearRangeStart= isset($_REQUEST['year']) ? $_REQUEST['year'] : null;
#sanitize
$yearRangeEnd= isset($_REQUEST['year2']) ? $_REQUEST['year2'] : null;
#sanitize

$consoles_pre=isset($_REQUEST['console']) ? $_REQUEST['console'] : null;
#sanitize
$genres_pre=isset($_REQUEST['genere']) ? $_REQUEST['genere'] : null;
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