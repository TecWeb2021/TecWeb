<?php

include "replacer.php";
require_once("dbConnection.php");

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoRecensioneTemplate.html");
$homePage=replace($homePage);

function replacePH($game){
	global $homePage;
	$homePage=str_replace("<img_path_ph/>", "../".$game->getImage()->getPath(),$homePage);
	$homePage=str_replace("<img_alt_ph/>", $game->getImage()->getAlt(),$homePage);
	$homePage=str_replace("<review_content_ph/>", $game->getReview(),$homePage);
	$homePage=str_replace("<game_vote_ph/>", $game->getVote(),$homePage);
	/*$homePage=str_replace("</>", $game->,$homePage);
	$homePage=str_replace("</>", $game->,$homePage);
	$homePage=str_replace("</>", $game->,$homePage);
	$homePage=str_replace("</>", $game->,$homePage);
	$homePage=str_replace("</>", $game->,$homePage);*/
}

if(isset($_REQUEST['game'])){
	$gameName=$_REQUEST['game'];
	#sanitize;
	$game=$dbAccess->getGame($gameName);
	if($game==null){
		echo "il gioco specificato non è stato trovato";
	}else{
		replacePH($game);
	}
}else{
	echo "non è specificato un gioco";
}



echo $homePage;


?>