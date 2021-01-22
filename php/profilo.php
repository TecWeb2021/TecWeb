<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$user=null;

$user=null;
$homePage="<p>Non sei autenticato</p>";

if(isset($_COOKIE['login'])){
	$hash=$_COOKIE['login'];
	#sanitize
	$user=$dbAccess->getUserByHash($hash);
}

if($user){
	$homePage=file_get_contents("../html/templates/profilo_utenteTemplate.html");
	if($user->isAdmin()){
		$admin=file_get_contents("../html/templates/adminTemplate.html");
		$homePage=str_replace("<admin_placeholder_ph/>", $admin, $homePage);
	}
	$imagePath="";
	if($user->getImage()){
		$imagePath=$user->getImage()->getPath();
	}else{
		$imagePath="images/login.png";
	}
	$homePage=str_replace("<user_image_ph/>","../".$imagePath, $homePage);
}else{
	$homePage="non puoi accedere a questa pagina";
}



$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;
