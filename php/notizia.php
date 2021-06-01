<?php

include "replacer.php";
require_once("dbConnection.php");

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/notiziaTemplate.html");
$homePage=replace($homePage);

function replacePH($news, $isUserAdmin){
	global $homePage;

	$image1 = $news->getImage1();
	$imagePath1 = $image1 ? "../".getSafeImage($image1->getPath()) : "no_data";
	$imageAlt1 = $image1 ? $image1->getAlt() : "no_data";

	$image2 = $news->getImage2();
	$imagePath2 = $image2 ? "../".getSafeImage($image2->getPath()) : "no_data";
	$imageAlt2 = $image2 ? $image2->getAlt() : "no_data";

	$replacements=array(
		"<img_path_ph/>" => $imagePath2,
		"<img_alt_ph/>" => $imageAlt2,
		"<news_title_ph/>" => $news->getTitle(),
		"<news_author_ph/>" => $news->getAuthor()->getUsername(),
		"<news_publication_date_ph/>" => dateToText($news->getLastEditDateTime()),
		"<news_content_ph/>" => $news->getContent(),
		"<news_edit_ph/>" => "edit_notizia.php?news=".$news->getTitle()
	);
	$homePage=str_replace(array_keys($replacements), array_values($replacements), $homePage);

	//echo "isuseradmin: ".$isUserAdmin;
	if($isUserAdmin){
		$homePage = str_replace("<admin_func_ph>","",$homePage);
		$homePage = str_replace("</admin_func_ph>","",$homePage);
	}else{
		$homePage = preg_replace("/\<admin_func_ph\>.*\<\/admin_func_ph\>/","",$homePage);
	}
}

$user=getLoggedUser($dbAccess);
$isAdmin = $user && $user->isAdmin() ? true : false; 

$news = null;

if(isset($_REQUEST['news'])){
	$newsTitle=$_REQUEST['news'];
	#sanitize;
	$news = $dbAccess->getNews($newsTitle);
	if($news === null){
		$homePage = getErrorHtml("news_not_existent");
	}else{
		replacePH($news, $isAdmin);
	}
}else{
	$homePage = getErrorHtml("news_not_specified");
}



$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $news ? $news->getTitle() : "");

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;



?>