<?php

include "replacer.php";
require_once("dbConnection.php");

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoRecensioneTemplate.html");


function replacePH($game, $isUserAdmin){
	global $homePage;

	$image = $game ? $game->getImage2() : null;
	// questa è la lista delle sostituzioni da applicare
	$replacements=array(
		"<img_path_ph/>" => "../". ( $image ? getSafeImage($image->getPath()) : getSafeImage("")),
		"<img_alt_ph/>" => $image ? $image->getAlt() : "",
		"<review_content_ph/>" => $game->getReview(),
		"<review_author_ph/>" => $game->getReview_author(),
		"<review_date_ph/>" => dateToText($game->getLast_review_date()),
		"<game_vote_ph/>" => $game->getVote(),
		"<game_name_ph/>" => $game->getName(),
		"<game_edit_ph/>" => "edit_gioco.php?game=".$game->getName()
	);

	//applico le sostituzioni
	$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);

	if($isUserAdmin){
		$homePage=str_replace("<admin_func_ph>","",$homePage);
		$homePage=str_replace("</admin_func_ph>","",$homePage);
	}else{
		$homePage=preg_replace("/\<admin_func_ph\>.*\<\/admin_func_ph\>/","",$homePage);
	}

}


function generateGameCommentsDivs($gameName, $dbAccess, $isUserAdmin){
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
			"<comment_author_profile_img_path_ph/>" => $author->getImage() ? "../".getSafeImage($author->getImage()->getPath()) : "../". getSafeImage("images/login.png"),
			"<comment_author_ph/>" => $author->getUsername(),
			"<comment_date_ph/>" => dateTimeToText($com->getDateTime())
		);

		$s = str_replace(array_keys($replacements), array_values($replacements), $s);

		if($isUserAdmin){
			$replacements = array(
				'<admin_func_ph>' => '',
				'<admin_func_ph/>' => '',
				'<comment_delete_ph/>' => $com->getId()
			);
			$s = str_replace(array_keys($replacements), array_values($replacements), $s);
		}else{
			$replacements = array(
				"/\<admin_func_ph\>.*\<\/admin_func_ph\>/" => ""
			);
			$s = preg_replace(array_keys($replacements), array_values($replacements), $s);
		}

		$commentsString = $commentsString.$s;
	}
	return $commentsString;

}

$game = null;

$user=getLoggedUser($dbAccess);
$isAdmin=$user && $user->isAdmin() ? true : false; 

if(isset($_REQUEST['game'])){
	$gameName=$_REQUEST['game'];
	#sanitize;
	$game=$dbAccess->getGame($gameName);
	if($game){

		if($game->getReview() === "" || $game->getReview() === null){
			$homePage = getErrorHtml("no_review");
		}else{
			replacePH($game, $isAdmin);
			
			$write = isset($_REQUEST['write']) ? $_REQUEST['write'] : null;
			#sanitize;
			if($write){
				$user = getLoggedUser($dbAccess);
				if($user){
					$comment=new Comment($user->getUsername(), $game->getName(), date('Y-m-d H:i:s'), $write); #2021-01-13 02:14:49
					$result=$dbAccess->addComment($comment);
					if($result){
						//echo "commento inserito<br/>";
					}else{
						//echo "commento non inserito<br/>";
					}
				}else{
					//echo "Per commentare devi essere autenticato";
				}
				
			}

			$deleteComment = isset($_REQUEST['deleteComment']) ? $_REQUEST['deleteComment'] : null;
			if($isAdmin){
				$result = $dbAccess->deleteComment($deleteComment);
				if($result){
					//echo "commento eleiminato<br/>";
				}else{
					//echo "commento non eleiminato<br/>";
				}
			}else{
				//echo "Per eliminare commenti devi essere autenticato come amministratore";
			}

			$commentsDivs=generateGameCommentsDivs($game->getName(), $dbAccess, $isAdmin);
			$homePage=str_replace("<comments_divs_ph/>", $commentsDivs, $homePage);
		}

	}else{
		$homePage = "il gioco specificato non è stato trovato";
	}
}else{
	$homePage = "non è specificato un gioco";
	header('Location: home.php');
}



$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $game ? $game->getName() : "");

$gameHomePage = createGameBasePage("recensione", $game ? $game->getName() : "");

$basePage=str_replace("<page_content_ph/>", $gameHomePage, $basePage);

$basePage=str_replace("<game_page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;



?>