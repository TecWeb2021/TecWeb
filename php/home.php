

<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/homeTemplate.html");



function createNewsListItem($news){
	
	$item=file_get_contents("../html/templates/homeNewsTemplate.html");
	
	$image=$news->getImage();
	$imagePath= $image ? $image->getPath() : "no_image_present";
	$imageAlt= $image ? $image->getAlt(): "no_alt_present";

	$replacements=array(
		"/\<news_url_ph\/\>/" => "notizia.php?news=".$news->getTitle(),
		"/\<news_publication_date_time_ph\/\>/" => $news->getLastEditDateTime(),
		"/\<news_title_ph\/\>/" => $news->getTitle(),
		"/\<news_content_ph\/\>/" => $news->getContent(),
		"/\<news_author_ph\/\>/" => $news->getAuthor()->getUsername(),
		"/\<img_path_ph\/\>/" => "../".$imagePath,
		"/\<img_alt_ph\/\>/" => $imageAlt

	);

	foreach ($replacements as $key => $value) {
		$item=preg_replace($key, $value, $item);
	}

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

function createTop5GamesItem($game, $positionNumber){
	$item=file_get_contents("../html/templates/homeTop5GameTemplate.html");

	$replacements=array(
		"/\<game_url_ph\/\>/" => "gioco_scheda.php?game=".strtolower($game->getName()),
		"/\<game_position_ph\/\>/" => $positionNumber."°",
		"/\<game_name_ph\/\>/" => $game->getName(),
		"/\<img_path_ph\/\>/" => "../".$game->getImage()->getPath(),
		"/\<img_alt_ph\/\>/" => $game->getImage()->getAlt()
	);

	foreach ($replacements as $key => $value) {
		$item=preg_replace($key, $value, $item);
	}

	return $item;
}

function createTop5Games($list){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	for($i=0;$i<min(5,count($list));$i++){
		$game=$list[$i];
		$s=createTop5GamesItem($game,$i+1);

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


//sostituzioni riguardanti il top_game
$replacements=array(
		"/\<top_game_url_ph\/\>/" => "gioco_scheda.php?game=".strtolower($topGame->getName()),
		"/\<top_game_name_ph\/\>/" => $topGame->getName(),
		"/\<top_game_img_path_ph\/\>/" => "../".$topGame->getImage()->getPath(),
		"/\<top_game_img_alt_ph\/\>/" => $topGame->getImage()->getAlt(),
		"/\<top_game_vote_ph\/\>/" => $topGame->getVote(),
		"/\<top_game_publication_date_ph\/\>/" => $topGame->getPublicationDate(),
		"/\<top_game_age_range_ph\/\>/" => $topGame->getAgeRange()
);

foreach ($replacements as $key => $value) {
	$homePage=preg_replace($key, $value, $homePage);
}


// inserisco i possibili valori per la barra di ricerca
$homePage=preg_replace("/\<opzioni_ph\/\>/","$optionsListString",$homePage);




$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", "home", $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>