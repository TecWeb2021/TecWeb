<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

function createUserHTMLItem($user, $thisUser){
	$template = file_get_contents("../html/templates/listaUtentiListItemTemplate.html");

	$image=$user->getImage();
	$imagePath= $image ? $image->getPath() : "../images/login.png";
	//echo "imagePath: ".$imagePath;
	$replacements=array(
		"<usr_img_path_ph/>"=>"../".getSafeImage($imagePath),
		"<username_ph/>"=>$user->getUsername()
	);
	$template = str_replace(array_keys($replacements), array_values($replacements), $template);
	return $template;
}

$dbAccess = new DBAccess;
$dbAccess->openDBConnection();


$user = null;
$homePage=file_get_contents("../html/templates/listaUtentiTemplate.html");

if(isset($_COOKIE['login'])){
	$hash=$_COOKIE['login'];
	#sanitize
	$user=$dbAccess->getUserByHash($hash);
}

if($user){
	
	if($user->isAdmin()){

		$usernameToDelete = getSafeInput('delete', 'string');
		if($usernameToDelete){
			if($usernameToDelete !== $user->getUsername()){
				$dbAccess->deleteUser($usernameToDelete);
			}else{
				// echo "non puoi eliminare il tuo profilo da questa pagina";
			}
		}
		

		$users=$dbAccess->getUsersList();

		$divsString="";
		foreach ($users as $singleUser) {
			$divsString=$divsString.createUserHTMLItem($singleUser, $user);
		}

		$replacements=array(
		"<users_divs_ph/>"=>$divsString
		);

		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);


	}else{
		$homePage = getErrorHtml("not_admin");
	}
}else{
	$homePage = getErrorHtml("not_logged");
}

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;
