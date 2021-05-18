<?php

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoSchedaTemplate.html");
$homePage=replace($homePage);

function replacePH($game, $isUserAdmin){
	global $homePage;

	$platforms= $game->getConsoles() ? implode(", ", $game->getConsoles()) : "";
	$genres= $game->getGenres() ? implode(", ", $game->getGenres()) : "";
	
	$replacements=array(
		"<img_path_ph/>" => "../".getSafeImage($game->getImage1()->getPath()),
		"<img_alt_ph/>" => $game->getImage1()->getAlt(),
		"<publication_date_ph/>" => dateToText($game->getPublicationDate()),
		"<game_name_ph/>" => $game->getName(),
		"<sinopsis_ph/>" => $game->getSinopsis(),
		"<platforms_ph/>" => $platforms,
		"<genres_ph/>" => $genres,
		"<age_range_ph/>" => $game->getAgeRange(),
		"<prequel_ph/>" => $game->getPrequel() ? $game->getPrequel() : "Nessuno",
		"<sequel_ph/>" => $game->getSequel() ? $game->getSequel() : "Nessuno",
		"<game_edit_ph/>" => "edit_gioco.php?game=".$game->getName(),
		"<developer_ph/>" => $game->getDeveloper()
	);

	$homePage=str_replace(array_keys($replacements), array_values($replacements), $homePage);

	if($isUserAdmin){
		$homePage=str_replace("<admin_func_ph>","",$homePage);
		$homePage=str_replace("</admin_func_ph>","",$homePage);
	}else{
		$homePage=preg_replace("/\<admin_func_ph\>.*\<\/admin_func_ph\>/","",$homePage);
	}
}

$game = null;

$user=getLoggedUser($dbAccess);
$isAdmin=$user && $user->isAdmin() ? true : false; 

if(isset($_REQUEST['game'])){
	$gameName=$_REQUEST['game'];
	#sanitize;
	$game=$dbAccess->getGame($gameName);
	if($game==null){
		$homePage = "il gioco specificato non è stato trovato";
	}else{
		replacePH($game, $isAdmin);
	}
}else{
	$homePage = "non è specificato un gioco";
}




$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $game ? $game->getName() : "");
$gameHomePage = createGameBasePage("scheda", $game ? $game->getName() : "");

$basePage=str_replace("<page_content_ph/>", $gameHomePage, $basePage);

$basePage=str_replace("<game_page_content_ph/>", $homePage, $basePage);


$basePage=replace($basePage);

echo $basePage;



?>