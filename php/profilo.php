<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$user=null;

$user = null;

if(isset($_COOKIE['login'])){
	$hash=$_COOKIE['login'];
	#sanitize
	$user=$dbAccess->getUserByHash($hash);
}

if($user){
	$homePage = file_get_contents("../html/templates/profilo_utenteTemplate.html");
	if($user->isAdmin()){
		$admin = file_get_contents("../html/templates/adminTemplate.html");
		$homePage = str_replace("<admin_placeholder_ph/>", $admin, $homePage);
	}else{
		$homePage = str_replace("<admin_placeholder_ph/>", "", $homePage);
	}
	$imagePath="";
	if($user->getImage()){
		$imagePath=$user->getImage()->getPath();
	}else{
		$imagePath="images/login.png";
	}
	$homePage=str_replace("<user_image_ph/>","../".getSafeImage($imagePath), $homePage);

	$homePage=str_replace("<username_ph/>",$user->getUsername(), $homePage);
	$homePage=str_replace("<email_ph/>",$user->getEmail(), $homePage);
}else{
	$homePage = getErrorHtml("not_logged");
}






$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;
