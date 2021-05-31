<?php

include "replacer.php";
require_once("dbConnection.php");

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoRecensioneTemplate.html");


function replacePH($game, $isUserAdmin){
	global $homePage;
	global $dbAccess;

	$image = $game ? $game->getImage2() : null;
	$review = $game ? $dbAccess->getReview($game->getName()) : null;
	// questa è la lista delle sostituzioni da applicare
	$replacements=array(
		"<img_path_ph/>" => "../". ( $image ? getSafeImage($image->getPath()) : getSafeImage("")),
		"<img_alt_ph/>" => $image ? $image->getAlt() : "",
		"<review_content_ph/>" => $review ? $review->getContent() : "",
		"<review_author_ph/>" => $review ? $review->getAuthorName() : "",
		"<review_date_ph/>" => $review ? dateToText($review->getDateTime()) : "",
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

		if($dbAccess->getReview($game->getName()) === null){
			$noReviewErrorReplacements = array(
				"<game_name_ph/>" => $game->getName()
			);
			$homePage = getErrorHtml("no_review", $isAdmin, $noReviewErrorReplacements);
		}else{
			replacePH($game, $isAdmin);
			
			$write = getSafeInput('write');
			#sanitize;
			if($write){
				$user = getLoggedUser($dbAccess);
				if($user){
					$comment=new Comment($user->getUsername(), $game->getName(), date('Y-m-d H:i:s'), $write); #2021-01-13 02:14:49
					$result=$dbAccess->addComment($comment);
					if($result){
						echo "commento inserito<br/>";
						header("Location: gioco_recensione.php?game=" . $game->getName());
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
		$homePage = getErrorHtml("game_not_existent");
	}
}else{
	$homePage = getErrorHtml("game_not_specified");
	header('Location: home.php');
}

unset($_GET['write']);
unset($_POST['write']);
unset($_REQUEST['write']);



$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess, $game ? $game->getName() : "");

$gameHomePage = createGameBasePage("recensione", $game ? $game->getName() : "");

$basePage=str_replace("<page_content_ph/>", $gameHomePage, $basePage);

$basePage=str_replace("<game_page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;



?>