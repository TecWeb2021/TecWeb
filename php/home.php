

<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/homeTemplate.html");



function createNewsListItem($news, $isUserAdmin=false){
	
	$item=file_get_contents("../html/templates/homeNewsTemplate.html");
	
	$image = $news->getImage1();
	$imagePath = $image ? $image->getPath() : "no_image_present";
	$imageAlt = $image ? $image->getAlt(): "no_alt_present";

	$replacements = array(
		"<news_url_ph/>" => "notizia.php?news=".$news->getTitle(),
		"<news_publication_date_time_ph/>" => dateToText($news->getLastEditDateTime()),
		"<news_title_ph/>" => $news->getTitle(),
		"<news_content_ph/>" => getStringExtract($news->getContent(), 500, "notizia.php?news=".$news->getTitle()),
		"<news_author_ph/>" => $news->getAuthor()->getUsername(),
		"<img_path_ph/>" => "../".getSafeImage($imagePath),
		"<img_alt_ph/>" => $imageAlt,
		"<news_edit_ph/>" => "edit_notizia.php?news=".strtolower($news->getTitle())

	);

	$item = str_replace(array_keys($replacements), array_values($replacements), $item);

	if($isUserAdmin){
		$item=str_replace("<admin_func_ph>","",$item);
		$item=str_replace("</admin_func_ph>","",$item);
	}else{
		$item=preg_replace("/\<admin_func_ph\>.*\<\/admin_func_ph\>/","",$item);
	}

	return $item;
}



function createNewsList($list, $isUserAdmin=false){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	foreach($list as $news){
		$s=createNewsListItem($news, $isUserAdmin);
		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	return $joinedItems;
}

function createTop5GamesItem($game, $positionNumber){
	$item=file_get_contents("../html/templates/homeTop5GameTemplate.html");

	$replacements=array(
		"/\<game_url_ph\/\>/" => $game ? "gioco_scheda.php?game=".strtolower($game->getName()) : "#",
		"/\<game_position_ph\/\>/" => $positionNumber."°",
		"/\<game_name_ph\/\>/" => $game ? $game->getName() : "",
		"/\<img_path_ph\/\>/" => $game ? "../".getSafeImage($game->getImage2()->getPath()) : "",
		"/\<img_alt_ph\/\>/" => $game ? $game->getImage2()->getAlt() : ""
	);

	$item = preg_replace(array_keys($replacements), array_values($replacements), $item);

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


$user=getLoggedUser($dbAccess);
$isAdmin=$user && $user->isAdmin() ? true : false; 

# Chiedo al server una lista delle notizie
$newsList=$dbAccess->getNewsList();
# Unisco le notizie in una lista html 
$newsListString=createNewsList($newsList, $isAdmin);
if($newsListString === ""){
	$newsListString = getErrorHtml("no_news");
}
# Metto la lista al posto del placeholder
$homePage=preg_replace("/\<news_divs_ph\/\>/",$newsListString,$homePage);

$games = $dbAccess->getGamesList();
$gamesNum = $games !== null ? count($games) : 0;

if($gamesNum > 0){

	$top5GamesList=$dbAccess->getTop5Games();
	$topGame=$dbAccess->getTopGame();





	$top5GamesString=createTop5Games($top5GamesList);

	$homePage=preg_replace("/\<top_5_games_ph\/\>/",$top5GamesString,$homePage);

	//usa cosa da implementare: se il top game non esiste bisogna togliere il div relativo, non lasciarlo con i valori vuoti

	$consoles = $topGame ? $topGame->getConsoles() : null;
	$genres = $topGame ? $topGame->getGenres() : null;

	$prequel = $topGame ? $topGame->getPrequel() : null;
	$prequel = $prequel === "" || $prequel === null ? "Nessuno" : $prequel;
	$sequel = $topGame ? $topGame->getSequel() : null;
	$sequel = $sequel === "" || $sequel === null ? "Nessuno" : $sequel;


	//sostituzioni riguardanti il top_game
	$replacements=array(
			"<top_game_url_ph/>" => $topGame ? "gioco_scheda.php?game=".strtolower($topGame->getName()) : "#",
			"<top_game_name_ph/>" => $topGame ? $topGame->getName() : "",
			"<top_game_img_path_ph/>" => $topGame ? "../".getSafeImage($topGame->getImage1()->getPath()) : "",
			"<top_game_img_alt_ph/>" => $topGame ? $topGame->getImage1()->getAlt() : "",
			"<top_game_vote_ph/>" => $topGame ? $topGame->getVote() : "",
			"<top_game_publication_date_ph/>" => $topGame ? dateToText($topGame->getPublicationDate()) : "",
			"<top_game_age_range_ph/>" => $topGame ? $topGame->getAgeRange() : "",
			"<top_game_platforms_ph/>" => $consoles ? implode(", ",$consoles) : "Nessuna",
			"<top_game_genres_ph/>" => $genres ? implode(", ",$genres) : "Nessuno",
			"<top_game_developer/>" => $topGame ? $topGame->getDeveloper() : "Nessuno",
			"<top_game_prequel_ph/>" => $prequel,
			"<top_game_sequel_ph/>" => $sequel,

			"<top_games_ph>" => "", // rimuovo i placeholder al limite dei topgames
			"</top_games_ph>" => ""
	);

	$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);

}else{
	$homePage = preg_replace("/<top_games_ph>(.*\n)*.*<\/top_games_ph>/", "", $homePage);
}






$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", "home", $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>