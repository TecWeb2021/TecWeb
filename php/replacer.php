<?php
require_once("dbConnection.php");
require_once("classes/user.php");

function replace($subject){
  $subject=preg_replace("/Giochi\.html/","giochi.php",$subject);
  $subject=preg_replace("/Home\.html/","home.php",$subject);
  $subject=preg_replace("/Notizie\.html/","notizie.php",$subject);
  $subject=preg_replace("/Forum\.html/","forum.php",$subject);
  $subject=preg_replace("/\"([A-Za-z]*)\.html\"/","\"$1.php\"",$subject);
  return $subject;
}

function createBasePage($templatePath, $page, $dbAccess){
	if(isset($_REQUEST['logout'])){
		logout();
	}

	$user=getLoggedUser($dbAccess);

	$basePage=generatePageTopAndBottom($templatePath, $page, $user);

	return $basePage;

}

function generatePageTopAndBottom($templatePath, $page, $user, $defaultUserImagePath="../images/login.png"){
	$base=file_get_contents($templatePath);

	$possiblePages=array("home","giochi","notizie");
	if(!in_array($page, $possiblePages)){
		$page=null;
	}
	$base=preg_replace("/\<$page\_active\/\>/", "", $base);
	$base=preg_replace("/\<$page\_active\>/", "", $base);

	foreach ($possiblePages as $value) {
		if($page!=$value){
			$base=preg_replace("/\<$value\_active\>class\=\"active\"\<$value\_active\/\>/", "", $base);
		}
	}

	
	if($user){
		if($user->getImage()){
			$base=str_replace("<user_img_path_ph/>", "../".$user->getImage()->getPath(), $base);
		}

		$base=preg_replace("/\<not_logged_in\>.*\<\/not_logged_in\>/","",$base);
		$base=str_replace("<logged_in>","",$base);
		$base=str_replace("</logged_in>","",$base);

		$base=str_replace("<username_ph/>", $user->getUsername(), $base);
		
	}else{
		$base=preg_replace("/\<logged_in\>.*\<\/logged_in\>/","",$base);
		$base=str_replace("<not_logged_in>","",$base);
		$base=str_replace("</not_logged_in>","",$base);
	}
	#la riga qua sotto è efficace( fa quello che deve) solo se il tag non è giù stato sostiuito, quindi solo l'utente non ha un'immagine
	$base=str_replace("<user_img_path_ph/>",$defaultUserImagePath,$base);


	return $base;
}


function getHash($username, $password){
	$inputString=$username.$password;
	$hashValue=hash("md5",$inputString);
	return $hashValue;
}


function logout(){
	$logout=$_REQUEST['logout'];
	#sanitize;
	if($logout='true' && isset($_COOKIE['login'])){
		setcookie("login","");
		echo "cookie unset";
		header("Refresh:0");
	}
}

function getLoggedUser($dbAccess){
	$user=null;
	if(isset($_COOKIE['login'])){
		$hash=$_COOKIE['login'];
		#sanitize
		$user=$dbAccess->getUserByHash($hash);
	}
	return $user;
}

?>