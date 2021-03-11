<?php

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoSchedaTemplate.html");
$homePage=replace($homePage);

function replacePH($game){
	global $homePage;

	$platforms= $game->getConsoles() ? implode(", ", $game->getConsoles()) : "";
	$genres= $game->getConsoles() ? implode(", ", $game->getConsoles()) : "";
	
	$replacements=array(
		"<gioco_scheda_ph/>" => "../"."gioco_scheda.php?game=".strtolower($game->getName()),
		"<gioco_recensione_ph/>" => "gioco_recensione.php?game=".strtolower($game->getName()),
		"<gioco_notizie_ph/>" => "gioco_notizie.php?game=".strtolower($game->getName()),
		"<img_path_ph/>" => "../".$game->getImage()->getPath(),
		"<img_alt_ph/>" => $game->getImage()->getAlt(),
		"<publication_date_ph/>" => $game->getPublicationDate(),
		"<game_name_ph/>" => $game->getName(),
		"<sinopsis_ph/>" => $game->getSinopsis(),
		"<platforms_ph/>" => $platforms,
		"<genres_ph/>" => $genres
	);

	foreach ($replacements as $key => $value) {
		$homePage=str_replace($key, $value, $homePage);
	}
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


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;



?>