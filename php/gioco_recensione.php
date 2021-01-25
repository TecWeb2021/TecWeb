<?php

include "replacer.php";
require_once("dbConnection.php");

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoRecensioneTemplate.html");

function replacePH($game){
	global $homePage;
	$homePage=str_replace("<gioco_scheda_ph/>", "gioco_scheda.php?game=".strtolower($game->getName()),$homePage);
	$homePage=str_replace("<gioco_recensione_ph/>", "gioco_recensione.php?game=".strtolower($game->getName()),$homePage);
	$homePage=str_replace("<gioco_notizie_ph/>", "gioco_notizie.php?game=".strtolower($game->getName()),$homePage);
	$homePage=str_replace("<img_path_ph/>", "../".$game->getImage()->getPath(),$homePage);
	$homePage=str_replace("<img_alt_ph/>", $game->getImage()->getAlt(),$homePage);
	$homePage=str_replace("<review_content_ph/>", $game->getReview(),$homePage);
	$homePage=str_replace("<game_vote_ph/>", $game->getVote(),$homePage);
	$homePage=str_replace("<game_name_ph/>", $game->getName(),$homePage);
	/*$homePage=str_replace("</>", $game->,$homePage);
	$homePage=str_replace("</>", $game->,$homePage);
	$homePage=str_replace("</>", $game->,$homePage);
	$homePage=str_replace("</>", $game->,$homePage);
	$homePage=str_replace("</>", $game->,$homePage);
	$homePage=str_replace("</>", $game->,$homePage);*/
}

function generateGameCommentsDivs($gameName,$dbAccess){
	$commentTemplate=file_get_contents("../html/templates/commentDivTemplate.html");
	$commentsList=$dbAccess->getCommentsList($gameName);
	if(!$commentsList){
		return "";
	}
	$commentsString="";
	foreach ($commentsList as $com) {
		$s=$commentTemplate;
		$s=str_replace("<comment_content_ph/>", $com->getContent(), $s);
		$commentsString=$commentsString.$s;
	}
	return $commentsString;

}

if(isset($_REQUEST['game'])){
	$gameName=$_REQUEST['game'];
	#sanitize;
	$game=$dbAccess->getGame($gameName);
	if($game){
		replacePH($game);

		

		
		$write=isset($_REQUEST['write']) ? $_REQUEST['write'] : null;
		#sanitize;
		if($write){
			$user=getLoggedUser($dbAccess);
			if($user){
				$comment=new Comment($user->getUsername(), $game->getName(), date('Y-m-d H:i:s'), $write); #2021-01-13 02:14:49
				$result=$dbAccess->addComment($comment);
				if($result){
					echo "commento inserito<br/>";
				}else{
					echo "commento non inserito<br/>";
				}
			}else{
				echo "Per commentare devi essere autenticato";
			}
			
		}

		$commentsDivs=generateGameCommentsDivs($game->getName(), $dbAccess);
		$homePage=str_replace("<comments_divs_ph/>", $commentsDivs, $homePage);

	}else{
		echo "il gioco specificato non è stato trovato";
	}
}else{
	echo "non è specificato un gioco";
}



$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;



?>