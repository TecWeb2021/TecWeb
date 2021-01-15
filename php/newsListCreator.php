<?php

include "dbConnection.php";
$dbAccess=new DBAccess;
$dbAccess->openDBConnection();



function createListItem($news_date, $news_url, $news_title, $news_author, $img_path, $img_alt, $news_content){
	$item=file_get_contents("../html/newsListItemTemplate.html");
	
	$item=preg_replace("/<news_date_ph/>/",$news_date,$item);
	$item=preg_replace("/<news_url_ph/>/",$news_url,$item);
	$item=preg_replace("/<news_title_ph/>/",$news_title,$item);
	$item=preg_replace("/<news_author_ph/>/",$news_author,$item);
	$item=preg_replace("/<img_path_ph/>/",$img_path,$item);
	$item=preg_replace("/<img_alt_ph/>/",$img_alt,$item);
	$item=preg_replace("/<news_content_ph/>/",$news_content,$item);
	return $item;
}



function createNewsList($list){
	$stringsArray=array();
	for($list as $entry){
		$s=createListItem("no data", "no data", $entry['Title'], "no data", "no data", "no data" , $entry['Text'])
		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringArray);
	echo $joinedItems;
	return $newsList;
}

$list=$dbAccess.getNewsList();
$newsListString=$createNewsList($list);



?>