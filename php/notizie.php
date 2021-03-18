<?php
require_once "replacer.php";
require_once "dbConnection.php";

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/notizieTemplate.html");

$homePage=replace($homePage);



function createNewsHTMLItem($news, $isUserAdmin=false){
	$item=file_get_contents("../html/templates/newsListItemTemplate.html");
	
	$replacements = array(
		"/\<news_date_ph\/\>/" => $news->getLastEditDateTime(),
		"/\<news_url_ph\/\>/" => "notizia.php?news=".$news->getTitle(),
		"/\<news_title_ph\/\>/" => $news->getTitle(),
		"/\<news_author_ph\/\>/" => $news->getAuthor()->getUsername(),
		"/\<img_path_ph\/\>/" => "../".$news->getImage()->getPath(),
		"/\<img_alt_ph\/\>/" => $news->getImage()->getAlt(),
		"/\<news_content_ph\/\>/" => $news->getContent(),
		"/\<news_edit_ph\/\>/" => "edit_notizia.php?news=".strtolower($news->getTitle())
	);

	foreach ($replacements as $key => $value) {
		$item=preg_replace($key, $value, $item);
	}

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

$category= isset($_REQUEST['categoria']) ? $_REQUEST['categoria'] : null;

$newsPartName = isset($_REQUEST['searchbar']) ? $_REQUEST['searchbar'] : null;

if( !in_array($category, News::$possible_categories)){
	$category=null;
}

$list=$dbAccess->getNewsList(null, $category, $newsPartName);
$newsListString=createNewsList($list, $isAdmin);

$homePage=preg_replace("/\<news_divs_ph\/\>/",$newsListString,$homePage);

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", "notizie", $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;


?>