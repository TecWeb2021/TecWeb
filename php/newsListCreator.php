<?php

$newsTemplate="<li><div class=\"notizia\"></div></li>";
function createListItem($content){
	$item="<li><div class=\"notizia\"></div></li>";
	$item=preg_replace("/\<div class\=\"notizia\"\>/","<div class=\"notizia\">".$content,$item);
	return $item;
}

#$newsListTemplate="\<ul\>\<ul\/\>";

function addListItem($item,$baseList="<ul></ul>"){
	$newsList=$baseList;
	$newsList=preg_replace("/\<ul\>/","<ul>".$item,$newsList);
	return $newsList;
}



?>