<?php
include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/giochiTemplate.html");
$homePage=replace($homePage);

function createListItem($game_year, $game_url, $game_name, $img_path, $img_alt, $game_content){
	$item=file_get_contents("../html/gamesListItemTemplate.html");
	
	$item=preg_replace("/\<game_year_ph\/\>/",$game_year,$item);
	$item=preg_replace("/\<game_url_ph\/\>/",$game_url,$item);
	$item=preg_replace("/\<game_name_ph\/\>/",$game_name,$item);
	$item=preg_replace("/\<img_path_ph\/\>/",$img_path,$item);
	$item=preg_replace("/\<img_alt_ph\/\>/",$img_alt,$item);
	$item=preg_replace("/\<game_content_ph\/\>/",$game_content,$item);
	return $item;
}



function createNewsList($list){
	$stringsArray=array();
	foreach($list as $entry){
		$s=createListItem($entry['Year'], "no_data", $entry['Name'], "no_data", "no_data", "no_data");
		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	$newsListTemplate=file_get_contents("../html/gamesListTemplate.html");
	$newsList=preg_replace("/\<games_list_items_ph\/\>/", $joinedItems, $newsListTemplate);
	return $newsList;
}

# Chiedo al server una lista delle notizie
$list=$dbAccess->getTableList("games");
# Unisco le notizie in una lista html 
$newsListString=createNewsList($list);
# Metto la lista al posto del placeholder
$homePage=preg_replace("/\<games_list_ph\/\>/",$newsListString,$homePage);



echo $homePage;

?>