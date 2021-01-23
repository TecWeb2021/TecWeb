<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

function createUserHTMLItem($user){
	$template=file_get_contents("../html/templates/listaUtentiListItemTemplate.html");
	$image=$user->getImage();
	$imagePath= $image ? $image->getPath() : "../images/login.php";
	$replacements=array(
		"<usr_img_path_ph/>"=>$imagePath,
		"<username_ph/>"=>$user->getUsername()
	);
	foreach ($replacements as $key => $value) {
		$template=str_replace($key, $value, $template);
	}

	return $template;
}

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$user=null;
$homePage="<p>Non sei autenticato</p>";

if(isset($_COOKIE['login'])){
	$hash=$_COOKIE['login'];
	#sanitize
	$user=$dbAccess->getUserByHash($hash);
}

if($user){
	
	if($user->isAdmin()){

		if(isset($_REQUEST['delete'])){
			$usernameToDelete=$_REQUEST['delete'];
			#sanitize
			if($usernameToDelete!=$user->getUsername()){
				$dbAccess->deleteUser($usernameToDelete);
			}else{
				echo "non puoi eliminare il tuo profilo da questa pagina";
			}
		}
		$homePage=file_get_contents("../html/templates/listaUtentiTemplate.html");

		$users=$dbAccess->getUsersList();

		$divsString="";
		foreach ($users as $singleUser) {
			$divsString=$divsString.createUserHTMLItem($singleUser);
		}

		$replacements=array(
		"<users_divs_ph/>"=>$divsString
		);
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}


	}else{
		$homePage="non puoi accedere a questa pagina perchè non sei un amministratore";
	}
}else{
	$homePage="non puoi accedere a questa pagina perchè non sei autenticato";
}






$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;
