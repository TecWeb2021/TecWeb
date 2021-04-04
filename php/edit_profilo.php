
<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/editProfiloTemplate.html");

$user=getLoggedUser($dbAccess);

if($user){

	if(isset($_REQUEST['elimina'])){
		$dbAccess->deleteUser($user->getUsername());
		//qui devo ancora verificar ese effettivamente è stato eliminato con successo, per ora suppongo che succeda sempre
		$homePage = "profilo eliminato con successo";
	}else{


		$new_password=isset($_REQUEST['password']) ? $_REQUEST['password'] : null;
		#sanitize
		$new_email=isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
		#sanitize
		$new_imagePath=saveImageFromFILES($dbAccess, "immagine");
		#sanitize

		
		if($new_email || $new_password || $new_imagePath){
			echo "almeno un valore è stato inserito"."<br/>";
			
			$error_message = "";

			//non è chiaro scrivere solo non presente quando il problema potrebbe essere un altro
			$error_messages = array(
				'email' => "Email non presente",
				'password' => "Password non presente",
				'immagine' => "Immagine non presente",
			);

			if($new_email == null){
				$error_message = $error_message . $error_messages['email'] . "<br/>";
			}
			//controllo di lunghezza temporaneo
			if(false /*$new_password == null || strlen($new_password) < 5*/){
				$error_message = $error_message . $error_messages['password'] . "<br/>";
			}
			//controllo se è false perchè è così che funziona la funzione saveImageFromFILES
			if(false /*$new_imagePath === false*/){
				$error_message = $error_message . $error_messages['immagine'] . "<br/>";
			}

			
			if($error_message != ""){
				//se c'è stato almeno un errore ...
				echo $error_message;

			}else{
				echo "non ci sono stati errori" . "<br/>";
				
				//se non è stata inserita una nuova immagine prendo quella vecchia
				$new_image = null;
				if($new_imagePath == false){
					$user->getImage();
				}else{
					$new_image = new Image($new_imagePath, "immagine utente");
				}

				//se non è stata inserita una nuova password la uso per creare il nuovo hash, altrimenti uso l'hash vecchio
				$new_hashValue = null;
				if($new_password == null){
					$new_hashValue = $user->gethash();
				}else{
					$new_hashValue = getHash($user->getUsername(), $new_password);
				}
				
				
				$newUser = new User($user->getUsername(),$new_hashValue, $user->isAdmin(), $new_image, $new_email);
		
				$result = $dbAccess->overwriteUser($newUser);
				if($result){
					echo "risultato overwrite: " . $result . "<br>/";
					setcookie('login',$new_hashValue);
				}else{
					echo "risultato overwrite: " . $result . "<br>/";
					$allOk = false;
				}
		

			}

			//faccio i replacement: dove possibile col valore nuovo, altrimenti con quello vecchio
			$replacements=array(
			"<email_ph/>"=>$new_email ? $new_email : $user->getEmail(),
			);
			foreach ($replacements as $key => $value) {
				$homePage=str_replace($key, $value, $homePage);
			}

			
		}else{
			echo "nessun valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";

			//faccio i replacement coi valori vecchio
			$replacements=array(
			"<email_ph/>" => $user->getEmail(),
			);
			foreach ($replacements as $key => $value) {
				$homePage=str_replace($key, $value, $homePage);
			}
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