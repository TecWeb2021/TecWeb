<?php
require_once "replacer.php";
require_once "dbConnection.php";

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/notizieTemplate.html");

$homePage=replace($homePage);



function createNewsHTMLItem($news, $isUserAdmin=false){
	$item=file_get_contents("../html/templates/newsListItemTemplate.html");
	
	$item=preg_replace("/\<news_date_ph\/\>/",$news->getLastEditDateTime(),$item);
	$item=preg_replace("/\<news_url_ph\/\>/","notizia.php?news=".$news->getTitle(),$item);	
	$item=preg_replace("/\<news_title_ph\/\>/",$news->getTitle(),$item);
	$item=preg_replace("/\<news_author_ph\/\>/",$news->getAuthor()->getUsername(),$item);
	$item=preg_replace("/\<img_path_ph\/\>/","../".$news->getImage()->getPath(),$item);
	$item=preg_replace("/\<img_alt_ph\/\>/",$news->getImage()->getAlt(),$item);
	$item=preg_replace("/\<news_content_ph\/\>/",$news->getContent(),$item);
	$item=preg_replace("/\<news_edit_ph\/\>/","edit_notizia.php?news=".strtolower($news->getTitle()),$item);

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
	foreach($list as $entry){
		$s=createNewsHTMLItem($entry, $isUserAdmin);
		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	return $joinedItems;
}
$user=getLoggedUser($dbAccess);

$isAdmin=$user && $user->isAdmin() ? true : false; 

$list=$dbAccess->getNewsList();
$newsListString=createNewsList($list, $isAdmin);

$homePage=preg_replace("/\<news_divs_ph\/\>/",$newsListString,$homePage);

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", "notizie", $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;


?>