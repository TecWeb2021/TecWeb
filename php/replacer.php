<?php

function replace($subject){
  $subject=preg_replace("/Giochi\.html/","giochi.php",$subject);
  $subject=preg_replace("/Home\.html/","home.php",$subject);
  $subject=preg_replace("/Notizie\.html/","notizie.php",$subject);
  $subject=preg_replace("/Forum\.html/","forum.php",$subject);
  $subject=preg_replace("/\"([A-Za-z]*)\.html\"/","\"$1.php\"",$subject);
  return $subject;
}

function generatePageTopAndBottom($templatePath, $page, $user){
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

	return $base;
}

?>