<?php

include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/notiziaTemplate.html");
$homePage=replace($homePage);

$newsSubpage="";
if(isset($_GET['id_notizia'])){

	$news_id=$_GET['id_notizia'];
	#sanitize

	$news=$dbAccess->getNewsList($news_id);

	if($news){
		$newsSubpage=$news[0]->getTitle()."<br/>".$news[0]->getContent();
	}else{

		$newsSubpage="non esiste una notizia con questo Id";
	}




$homePage=preg_replace("/\<single_new_ph\/\>/",$newsSubpage,$homePage);



}else{
	$homePage="specificare un gioco";
}


echo $homePage;

?>