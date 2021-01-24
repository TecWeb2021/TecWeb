<?php

include "replacer.php";
require_once("dbConnection.php");

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/notiziaTemplate.html");
$homePage=replace($homePage);

function replacePH($news){
	global $homePage;

	$image=$news->getImage();
	$imagePath=  $image ? "../".$image->getPath() : "no_data";
	$imageAlt=  $image ? $image->getAlt() : "no_data";
	$replacements=array(
		"<img_path_ph/>"=>$imagePath,
		"<img_alt_ph/>"=>$imageAlt,
		"<news_title_ph/>"=>$news->getTitle(),
		"<news_author_ph/>"=>$news->getAuthor()->getUsername(),
		"<news_publication_date_ph/>"=>$news->getLastEditDateTime(),
		"<news_content_ph/>"=>$news->getContent()
	);
	foreach ($replacements as $key => $value) {
		$homePage=str_replace($key, $value, $homePage);
	}
}


if(isset($_REQUEST['news'])){
	$newsTitle=$_REQUEST['news'];
	#sanitize;
	$news=$dbAccess->getNews($newsTitle);
	if($news==null){
		echo "la notizia specificata non è stata trovata";
	}else{
		replacePH($news);
	}
}else{
	echo "non è specificata una notizia";
}



$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;



?>