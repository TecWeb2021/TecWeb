<?php
include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giochiTemplate.html");




function createGameHTMLItem($game){
	$item=file_get_contents("../html/templates/gamesListItemTemplate.html");
	
	$item=preg_replace("/\<game_name_ph\/\>/",$game->getName(),$item);
	$item=preg_replace("/\<game_date_ph\/\>/",$game->getPublicationDate(),$item);
	$item=preg_replace("/\<game_vote_ph\/\>/",$game->getVote(),$item);
	$item=preg_replace("/\<game_sinossi_ph\/\>/",$game->getSinopsis(),$item);
	$item=preg_replace("/\<img_path_ph\/\>/","../".$game->getImage()->getPath(),$item);
	$item=preg_replace("/\<img_alt_ph\/\>/",$game->getImage()->getAlt(),$item);
	$item=preg_replace("/\<game_scheda_url_ph\/\>/","gioco_scheda.php?game=".strtolower($game->getName()),$item);
	
	return $item;
}



function createGamesDivs($gamesList){
	if(!$gamesList){
		return "";
	}
	$stringsArray=array();
	foreach($gamesList as $entry){
		$s=createGameHTMLItem($entry);
		array_push($stringsArray, $s);
	}
	$joinedItems=implode(" ", $stringsArray);
	return $joinedItems;
}

$gameName=null;
if(isset($_REQUEST['searchbar'])){
	$gameName=$_REQUEST['searchbar'];
	#sanitize
}
# Chiedo al server una lista delle notizie
$list=$dbAccess->getGamesList($gameName);
# Unisco le notizie in una lista html 
$gamesDivsString=createGamesDivs($list);
# Metto la lista al posto del placeholder
$homePage=preg_replace("/\<games_divs_ph\/\>/",$gamesDivsString,$homePage);

$user=null;
if(isset($_COOKIE['login'])){
	$hash=$_COOKIE['login'];
	#sanitize
	$user=$dbAccess->getUserByHash($hash);
}

$basePage=generatePageTopAndBottom("../html/templates/top_and_bottomTemplate.html","giochi",$user);
$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>