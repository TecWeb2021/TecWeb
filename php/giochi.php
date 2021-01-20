<?php
include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giochiTemplate.html");
$homePage=replace($homePage);

function createGameHTMLItem($game){
	$item=file_get_contents("../html/templates/gamesListItemTemplate.html");
	
	$item=preg_replace("/\<game_name_ph\/\>/",$game->getName(),$item);
	$item=preg_replace("/\<game_date_ph\/\>/",$game->getPublicationDate(),$item);
	$item=preg_replace("/\<game_vote_ph\/\>/",$game->getVote(),$item);
	$item=preg_replace("/\<game_sinossi_ph\/\>/",$game->getSinopsis(),$item);
	
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
	$joinedItems=implode( " ", $stringsArray);
	return $joinedItems;
}

# Chiedo al server una lista delle notizie
$list=$dbAccess->getGames();
# Unisco le notizie in una lista html 
$gamesDivsString=createGamesDivs($list);
# Metto la lista al posto del placeholder
$homePage=preg_replace("/\<games_divs_ph\/\>/",$gamesDivsString,$homePage);



echo $homePage;

?>