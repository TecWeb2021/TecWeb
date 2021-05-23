<?php
require_once "replacer.php";
require_once "dbConnection.php";

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoNotizieTemplate.html");

$homePage=replace($homePage);

function createNewsHTMLItem($news, $isAdmin=false){
	$item=file_get_contents("../html/templates/giocoNotiziaTemplate.html");

	$replacements = array(
		"<news_date_ph/>" => dateToText($news->getLastEditDateTime()),
		"<news_url_ph/>" => "notizia.php?news=".$news->getTitle(),	
		"<news_title_ph/>" => $news->getTitle(),
		"<news_author_ph/>" => $news->getAuthor()->getUsername(),
		"<img_path_ph/>" => "../".getSafeImage($news->getImage1()->getPath()),
		"<img_alt_ph/>" => $news->getImage1()->getAlt(),
		"<news_content_ph/>" => getStringExtract($news->getContent(), 500, "notizia.php?news=".$news->getTitle()),
		"<news_edit_ph/>" => "edit_notizia.php?news=".strtolower($news->getTitle())
	);
	$item = str_replace(array_keys($replacements), array_values($replacements), $item);

	if($isAdmin){
		$item=str_replace("<admin_func_ph>","",$item);
		$item=str_replace("</admin_func_ph>","",$item);
	}else{
		$item=preg_replace("/\<admin_func_ph\>.*\<\/admin_func_ph\>/","",$item);
	}

	return $item;
}



function createNewsList($list, $isAdmin=false){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	foreach($list as $entry){
		$s=createNewsHTMLItem($entry, $isAdmin);
		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	return $joinedItems;
}

$newsListString = "";
$game = null;

$user=getLoggedUser($dbAccess);
$isAdmin=$user && $user->isAdmin() ? true : false; 

if(isset($_REQUEST['game'])){
	$gameName=$_REQUEST['game'];
	#sanitize;
	$game=$dbAccess->getGame($gameName);
	if($game){

		$list=$dbAccess->getNewsList($game->getName());
		$newsListString=createNewsList($list, $isAdmin);
		if($newsListString === ""){
			$newsListString = getErrorHtml("no_game_news");
		}

	}else{
		
		$homePage = getErrorHtml("game_not_existent");
	}
}else{
	$homePage = getErrorHtml("game_not_specified");
}





$homePage=preg_replace("/\<news_divs_ph\/\>/",$newsListString,$homePage);

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $game ? $game->getName() : "");

$gameHomePage = createGameBasePage("notizie", $game ? $game->getName() : "");

$basePage=str_replace("<page_content_ph/>", $gameHomePage, $basePage);

$basePage=str_replace("<game_page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;


?>




