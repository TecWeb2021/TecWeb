<?php

require_once("classes/user.php");

function replace($subject){
  $subject=preg_replace("/Giochi\.html/","giochi.php",$subject);
  $subject=preg_replace("/Home\.html/","home.php",$subject);
  $subject=preg_replace("/Notizie\.html/","notizie.php",$subject);
  $subject=preg_replace("/Forum\.html/","forum.php",$subject);
  $subject=preg_replace("/\"([A-Za-z]*)\.html\"/","\"$1.php\"",$subject);
  return $subject;
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

	$base=str_replace("<user_img_path_ph/>",$defaultUserImagePath,$base);
	if($user){
		#qua sotto si potrà decommentare la condizione e togliere il false quando la classe user avrà un attibuto image e una funzione getImage()
		if(false/*$image=$user->getImage()*/){
			$base=str_replace("<user_img_path_ph/>",$image->getPath(),$base);
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


	return $base;
}

?>