

<?php
include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/homeTemplate.html");
$homePage=replace($homePage);



function createNewsListItem($news){
	$item=file_get_contents("../html/templates/homeNewsTemplate.html");
	
	//$item=preg_replace("/\<news_url_ph\/\>/","no_data",$item);
	$item=preg_replace("/\<news_publication_date_time_ph\/\>/",$news->getLastEditDateTime(),$item);
	$item=preg_replace("/\<news_title_ph\/\>/",$news->getTitle(),$item);
	$item=preg_replace("/\<news_content_ph\/\>/",$news->getContent(),$item);
	$item=preg_replace("/\<news_author_ph\/\>/",$news->getAuthor()->getUsername(),$item);
	$item=preg_replace("/\<img_path_ph\/\>/","../".$news->getImage()->getPath(),$item);
	$item=preg_replace("/\<img_alt_ph\/\>/",$news->getImage()->getAlt(),$item);
	return $item;
}



function createNewsList($list){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	foreach($list as $news){
		$s=createNewsListItem($news);

		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	return $joinedItems;
}

function createTop5GamesItem($game){
	$item=file_get_contents("../html/templates/homeTop5GameTemplate.html");

	$item=preg_replace("/\<game_url_ph\/\>/","gioco_scheda.php?game=".strtolower($game->getName()),$item);
	$item=preg_replace("/\<game_name_ph\/\>/",$game->getName(),$item);
	$item=preg_replace("/\<img_path_ph\/\>/","../".$game->getImage()->getPath(),$item);
	$item=preg_replace("/\<img_alt_ph\/\>/",$game->getImage()->getAlt(),$item);
	return $item;
}

function createTop5Games($list){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	foreach($list as $game){
		$s=createTop5GamesItem($game);

		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	return $joinedItems;
}

function createGamesNamesListStrings($list){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	foreach ($list as $game) {
		$singleString="<option value=\"".$game->getName()."\"/>";
		array_push($stringsArray, $singleString);
	}
	$joinedItems=implode("", $stringsArray);
	return $joinedItems;
}

# Chiedo al server una lista delle notizie
$newsList=$dbAccess->getNewsList();

$top5GamesList=$dbAccess->getTop5Games();
$topGame=$dbAccess->getTopGame();

$gamesList=$dbAccess->getGamesList();
$optionsListString=createGamesNamesListStrings($gamesList);

# Unisco le notizie in una lista html 
$newsListString=createNewsList($newsList);
$top5GamesString=createTop5Games($top5GamesList);
# Metto la lista al posto del placeholder
$homePage=preg_replace("/\<news_divs_ph\/\>/",$newsListString,$homePage);
$homePage=preg_replace("/\<top_5_games_ph\/\>/",$top5GamesString,$homePage);


$homePage=preg_replace("/\<top_game_url_ph\/\>/","gioco_scheda.php?game=".strtolower($topGame->getName()),$homePage);
$homePage=preg_replace("/\<top_game_name_ph\/\>/",$topGame->getName(),$homePage);
$homePage=preg_replace("/\<top_game_img_path_ph\/\>/","../".$topGame->getImage()->getPath(),$homePage);
$homePage=preg_replace("/\<top_game_img_alt_ph\/\>/",$topGame->getImage()->getAlt(),$homePage)
;
$homePage=preg_replace("/\<top_game_vote_ph\/\>/",$topGame->getVote(),$homePage)
;
$homePage=preg_replace("/\<top_game_publication_date_ph\/\>/",$topGame->getPublicationDate(),$homePage)
;
$homePage=preg_replace("/\<top_game_age_range_ph\/\>/",$topGame->getAgeRange(),$homePage)
;


#opzioni

$homePage=preg_replace("/\<opzioni_ph\/\>/","$optionsListString",$homePage);


$basePage=generatePageTopAndBottom("../html/templates/top_and_bottomTemplate.html","home",null);
$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>