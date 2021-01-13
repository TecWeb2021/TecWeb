<?php
include "replacer.php";
include "dbConnection.php";
include "newsListCreator.php";

$homePage=file_get_contents("../html/home.html");

$homePage=replace($homePage);

$dbAccess=new DBAccess;
if($dbAccess){
	echo "db success<br>";
}else{
	echo "db fail";
}

echo "open connection: ".($dbAccess->openDBConnection())."<br>";

$list=addListItem("");
$newsList=$dbAccess->getNewsList();
for ($i=0;$i<count($newsList);$i++) {
	$listItem=createListItem($newsList[$i]["Title"]."<br/>".$newsList[$i]["Text"]);
	$list=addListItem($listItem,$list);
}
#echo "ciao".$list;
$homePage=preg_replace("/\<dude\/\>/",$list,$homePage);
echo $homePage;

?>