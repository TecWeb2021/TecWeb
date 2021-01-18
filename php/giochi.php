<?php
include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giochiTemplate.html");
$homePage=replace($homePage);

function createListItem($game_scheda_url, $img_path, $img_alt, $game_name, $game_date, $game_vote, $game_sinossi){
	$item=file_get_contents("../html/templates/gamesListItemTemplate.html");
	
	$item=preg_replace("/\<game_scheda_url_ph\/\>/",$game_scheda_url,$item);
	$item=preg_replace("/\<img_path_ph\/\>/",$img_path,$item);
	$item=preg_replace("/\<img_alt_ph\/\>/",$img_alt,$item);
	$item=preg_replace("/\<game_name_ph\/\>/",$game_name,$item);
	$item=preg_replace("/\<game_date_ph\/\>/",$game_date,$item);
	$item=preg_replace("/\<game_vote_ph\/\>/",$game_vote,$item);
	$item=preg_replace("/\<game_sinossi_ph\/\>/",$game_sinossi,$item);
	
	return $item;
}



function createGamesDivs($list){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	foreach($list as $entry){
		$s=createListItem("gioco.php?gioco=".$entry['Name'], "../".$entry['Path'], $entry['Alt'], $entry['Name'], "no_data", "no_data", "no_data");
		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	return $joinedItems;
}

# Chiedo al server una lista delle notizie
$list=$dbAccess->getGamesWithImages();
# Unisco le notizie in una lista html 
$gamesDivsString=createGamesDivs($list);
# Metto la lista al posto del placeholder
$homePage=preg_replace("/\<games_divs_ph\/\>/",$gamesDivsString,$homePage);



echo $homePage;

?>