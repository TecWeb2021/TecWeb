

<?php
include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/homeTemplate.html");
$homePage=replace($homePage);

function createListItem($news_url, $news_title, $news_content){
	$item=file_get_contents("../html/templates/homeNewsListItemTemplate.html");
	
	$item=preg_replace("/\<news_url_ph\/\>/",$news_url,$item);
	$item=preg_replace("/\<news_title_ph\/\>/",$news_title,$item);
	$item=preg_replace("/\<news_content_ph\/\>/",$news_content,$item);
	return $item;
}



function createNewsList($list){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	foreach($list as $entry){
		$s=createListItem("notizia.php?id_notizia=".$entry['Id'], $entry['Title'], $entry['Content']);
		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	$newsListTemplate=file_get_contents("../html/templates/homeNewsListTemplate.html");
	$newsList=preg_replace("/\<news_list_items_ph\/\>/", $joinedItems, $newsListTemplate);
	return $newsList;
}

# Chiedo al server una lista delle notizie
$list=$dbAccess->getTableList("news");
# Unisco le notizie in una lista html 
$newsListString=createNewsList($list);
# Metto la lista al posto del placeholder
$homePage=preg_replace("/\<news_list_ph\/\>/",$newsListString,$homePage);



echo $homePage;

?>