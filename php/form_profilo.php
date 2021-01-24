
<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/formProfiloTemplate.html");

$user=getLoggedUser($dbAccess);

if($user){


	$replacements=array(
		"<email_ph/>"=> $user->getEmail(),
		);
	foreach ($replacements as $key => $value) {
		$homePage=str_replace($key, $value, $homePage);
	}


	$failed=false;


	$newEmail=isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
	$newImageFile=isset($_FILES['immagine']) ? $_FILES['immagine'] : null;
	$newPassword=isset($_REQUEST['password']) ? $_REQUEST['password'] : null;

	$updatedUser= User::copyConstruct($user);
	if($newEmail){
		$updatedUser->setEmail($newEmail);
	}
	if($newPassword){
		$updatedUser->setHashByPassword($newPassword);
	}
	if($newImageFile){
		$saveResult=saveImageFromFILES($dbAccess, "immagine");
		if($saveResult!=false){
			$newImage=new Image($newImagePath, "Immagine profilo dell'utente");
			$updatedUser->setImage($newImage);
		}else{
			$failed=true;
		}
	}

	if(!$failed){
		$updateResult=$dbAccess->updateUser($updatedUser);
		$failed= $updateResult ? true : false;
	}

	if($failed){
		$replacements=array(
			"<email_ph/>"=> $newEmail ? $newEmail : $user->getEmail(),
			);
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
	}else{
		if($newEmail || $newImageFile || $newPassword){
			header('Location: profilo.php');
		}
	}
	

}else{
	$homePage="non puoi accedere a questa pagina perchè non hai fatto il login";
}





$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>