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

function createNewsHTMLItem($news){
	$item=file_get_contents("../html/templates/giocoNotiziaTemplate.html");
	
	$item=preg_replace("/\<news_date_ph\/\>/",$news->getLastEditDateTime(),$item);
	$item=preg_replace("/\<news_url_ph\/\>/","notizia.php?news=".$news->getTitle(),$item);	
	$item=preg_replace("/\<news_title_ph\/\>/",$news->getTitle(),$item);
	$item=preg_replace("/\<news_author_ph\/\>/",$news->getAuthor()->getUsername(),$item);
	$item=preg_replace("/\<img_path_ph\/\>/","../".$news->getImage()->getPath(),$item);
	$item=preg_replace("/\<img_alt_ph\/\>/",$news->getImage()->getAlt(),$item);
	$item=preg_replace("/\<news_content_ph\/\>/",$news->getContent(),$item);
	$item=preg_replace("/\<news_edit_ph\/\>/","edit_notizia.php?news=".strtolower($news->getTitle()),$item);
	return $item;
}



function createNewsList($list){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	foreach($list as $entry){
		$s=createNewsHTMLItem($entry);
		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	return $joinedItems;
}

if(isset($_REQUEST['game'])){
	$gameName=$_REQUEST['game'];
	#sanitize;
	$game=$dbAccess->getGame($gameName);
	if($game){
		replacePH($game);

		$list=$dbAccess->getNewsList($game->getName());
		$newsListString=createNewsList($list);
	}else{
		
		echo "il gioco specificato non è stato trovato";
	}
}else{
	echo "non è specificato un gioco";
}





$homePage=preg_replace("/\<news_divs_ph\/\>/",$newsListString,$homePage);

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;


?>




