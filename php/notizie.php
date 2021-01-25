<?php
require_once "replacer.php";
require_once "dbConnection.php";

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/notizieTemplate.html");

$homePage=replace($homePage);



function createNewsHTMLItem($news){
	$item=file_get_contents("../html/templates/newsListItemTemplate.html");
	
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

$list=$dbAccess->getNewsList();
$newsListString=createNewsList($list);

$homePage=preg_replace("/\<news_divs_ph\/\>/",$newsListString,$homePage);

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", "notizie", $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;


?>