<?php

include "replacer.php";
require_once("dbConnection.php");

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoRecensioneTemplate.html");


function replacePH($game, $isUserAdmin){
	global $homePage;

	// questa è la lista delle sostituzioni da applicare
	$replacements=array(
		"<gioco_scheda_ph/>" => "gioco_scheda.php?game=".strtolower($game->getName()),
		"<gioco_recensione_ph/>" => "gioco_recensione.php?game=".strtolower($game->getName()),
		"<gioco_notizie_ph/>" => "gioco_notizie.php?game=".strtolower($game->getName()),
		"<img_path_ph/>" => "../".$game->getImage()->getPath(),
		"<img_alt_ph/>" => $game->getImage()->getAlt(),
		"<review_content_ph/>" => $game->getReview(),
		"<review_date_ph/>" => dateToText($game->getPublicationDate()),
		"<game_vote_ph/>" => $game->getVote(),
		"<game_name_ph/>" => $game->getName(),
		"<game_edit_ph/>" => "edit_gioco.php?game=".$game->getName()
	);

	//applico le sostituzioni
	foreach ($replacements as $key => $value) {
		$homePage = str_replace($key, $value, $homePage);
	}

	if($isUserAdmin){
		$homePage=str_replace("<admin_func_ph>","",$homePage);
		$homePage=str_replace("</admin_func_ph>","",$homePage);
	}else{
		$homePage=preg_replace("/\<admin_func_ph\>.*\<\/admin_func_ph\>/","",$homePage);
	}

}


function generateGameCommentsDivs($gameName,$dbAccess){
	$commentTemplate=file_get_contents("../html/templates/commentDivTemplate.html");
	$commentsList=$dbAccess->getCommentsList($gameName);
	if(!$commentsList){
		return "";
	}
	$commentsString="";
	foreach ($commentsList as $com) {
		$author = $dbAccess->getUser($com->getAuthorName());
		$s=$commentTemplate;

		$replacements = array(
			"<comment_content_ph/>" => $com->getContent(),
			"<comment_author_profile_img_path_ph/>" => $author->getImage() ? $author->getImage()->getPath() : "../images/login.png",
			"<comment_author_ph/>" => $author->getUsername(),
			"<comment_date_ph/>" => dateTimeToText($com->getDateTime())
		);

		foreach ($replacements as $key => $value) {
			$s = str_replace($key, $value, $s);
		}

		$commentsString=$commentsString.$s;
	}
	return $commentsString;

}

$user=getLoggedUser($dbAccess);
$isAdmin=$user && $user->isAdmin() ? true : false; 

if(isset($_REQUEST['game'])){
	$gameName=$_REQUEST['game'];
	#sanitize;
	$game=$dbAccess->getGame($gameName);
	if($game){
		replacePH($game, $isAdmin);

		

		
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



$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $game ? $game->getName() : "");

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;



?>