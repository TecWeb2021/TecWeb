<?php
require_once "replacer.php";
require_once "dbConnection.php";

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoNotizieTemplate.html");

$homePage=replace($homePage);

function replacePH($game){
	global $homePage;

	$homePage=str_replace("<gioco_scheda_ph/>", "gioco_scheda.php?game=".strtolower($game->getName()),$homePage);
	$homePage=str_replace("<gioco_recensione_ph/>", "gioco_recensione.php?game=".strtolower($game->getName()),$homePage);
	$homePage=str_replace("<gioco_notizie_ph/>", "gioco_notizie.php?game=".strtolower($game->getName()),$homePage);
}

function createNewsHTMLItem($news, $isAdmin=false){
	$item=file_get_contents("../html/templates/giocoNotiziaTemplate.html");

	$replacements = array(
		"<news_date_ph/>" => dateToText($news->getLastEditDateTime()),
		"<news_url_ph/>" => "notizia.php?news=".$news->getTitle(),	
		"<news_title_ph/>" => $news->getTitle(),
		"<news_author_ph/>" => $news->getAuthor()->getUsername(),
		"<img_path_ph/>" => "../".$news->getImage1()->getPath(),
		"<img_alt_ph/>" => $news->getImage1()->getAlt(),
		"<news_content_ph/>" => $news->getContent(),
		"<news_edit_ph/>" => "edit_notizia.php?news=".strtolower($news->getTitle())
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

$user=getLoggedUser($dbAccess);
$isAdmin=$user && $user->isAdmin() ? true : false; 

if(isset($_REQUEST['game'])){
	$gameName=$_REQUEST['game'];
	#sanitize;
	$game=$dbAccess->getGame($gameName);
	if($game){
		replacePH($game);

		$list=$dbAccess->getNewsList($game->getName());
		$newsListString=createNewsList($list, $isAdmin);
	}else{
		
		echo "il gioco specificato non è stato trovato";
	}
}else{
	echo "non è specificato un gioco";
}





$homePage=preg_replace("/\<news_divs_ph\/\>/",$newsListString,$homePage);

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $game ? $game->getName() : "");

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;


?>




