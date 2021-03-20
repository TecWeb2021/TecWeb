
<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/formProfiloTemplate.html");

$user=getLoggedUser($dbAccess);

if($user){

	if(isset($_REQUEST['elimina'])){
		$dbAccess->deleteUser($user->getUsername());
		//qui devo ancora verificar ese effettivamente è stato eliminato con successo, per ora suppongo che succeda sempre
		$homePage = "profilo eliminato con successo";
	}else{
		$replacements=array(
			"<email_ph/>"=> $user->getEmail(),
			);
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}


		$failed=false;


		$newEmail=isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
		$newImageFile=isset($_FILES['immagine']) && $_FILES['immagine']['name']!="" ? $_FILES['immagine'] : null;
		$newPassword=isset($_REQUEST['password']) ? $_REQUEST['password'] : null;

		$updatedUser= User::copyConstruct($user);
		if($newEmail){
			$updatedUser->setEmail($newEmail);
		}
		if($newPassword){
			$updatedUser->setHashByPassword($newPassword);
		}

		$ok=true;
		if($newImageFile){
			$saveResult=saveImageFromFILES($dbAccess, "immagine");
			if($saveResult==false){
				$ok=false;
				echo "caricamento immagine fallito";
			}
			echo $saveResult;
			$newImage=new Image($saveResult, addslashes("Immagine profilo dell'utente"));
			$updatedUser->setImage($newImage);
		}
		print_r($_FILES);
		if($ok && ($newEmail || $newPassword || $newImageFile)){
			$updateResult=$dbAccess->updateUser($updatedUser);
			if($updateResult){
				if($newEmail || $newImageFile || $newPassword){
					setcookie('login',$updatedUser->getHash());
					echo "ti reindirizzo tra 0 secondi";
					header("refresh:0;url=profilo.php");
				}
			}else{
				echo "aggiornamento utente fallito";
			}
		}else{
			echo "non si può proseguire per manacanza di input o fallimento del caricamento immagine";
		}

		$replacements=array(
				"<email_ph/>"=> $newEmail ? $newEmail : $user->getEmail(),
				);
			foreach ($replacements as $key => $value) {
				$homePage=str_replace($key, $value, $homePage);
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