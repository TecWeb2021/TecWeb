
<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/editProfiloTemplate.html");

$user=getLoggedUser($dbAccess);

$validation_error_messages = array();
$success_messages = array();
$failure_messages = array();

if($user){

	if(isset($_REQUEST['elimina'])){
		$res = $dbAccess->deleteUser($user->getUsername());
		if($res !== false){
			$homePage = getErrorHtml("user_deleted");
		}else{
			array_push($failure_messages, "Eliminazione fallita");
		}
		
	}else{
		if(isset($_REQUEST['email'])){
			//echo "almeno un valore è stato inserito"."<br/>";

			$new_password = getSafeInput('password', 'string');
			$new_passwordRepeat = getSafeInput('repeatpassword', 'string');
			$new_email = getSafeInput('email', 'string');
			$new_imagePath = saveImageFromFILES($dbAccess, "immagine", User::$imgMinRatio, User::$imgMaxRatio);
			#sanitize

			// controllo i campi obbligatori

			$mandatory_fields = array(
				[$new_email, 'email']
			);
			foreach ($mandatory_fields as $value) {
				if($value[0] === null || validateValue($value[0], $value[1]) === false){
					array_push($validation_error_messages, getValidationError($value[1]));
				}
			}

			// controllo i campi opzionali

			$optional_fields = array(
				[$new_password, 'password'],
			);
			foreach ($mandatory_fields as $value) {
				if($value[0] !== null && validateValue($value[0], $value[1]) === false){
					array_push($validation_error_messages, getValidationError($value[1]));
				}
			}

			if( $new_imagePath === false){
				array_push($validation_error_messages, getValidationError('immagine'));
			}

			// controllo i campi obbligatori derivati

			if( $new_password !== null && $new_passwordRepeat !== $new_password){
				array_push($validation_error_messages, getValidationError('repeatpassword'));
			}
				

			
			if(count($validation_error_messages) > 0){
				//se c'è stato almeno un errore ...
				

			}else{
				//echo "non ci sono stati errori" . "<br/>";
				
				//se non è stata inserita una nuova immagine prendo quella vecchia
				$new_image = null;
				if($new_imagePath == false){
					echo "\$new_imagePath == false<br/>";
					$new_image = $user->getImage();
				}else{
					$new_image = new Image($new_imagePath, "immagine utente");
				}

				//se è stata inserita una nuova password la uso per creare il nuovo hash, altrimenti uso l'hash vecchio
				$new_hashValue = null;
				if($new_password == null){
					$new_hashValue = $user->getHash();
				}else{
					$new_hashValue = getHash($user->getUsername(), $new_password);
				}
				
				
				$newUser = new User($user->getUsername(),$new_hashValue, $user->isAdmin(), $new_image, $new_email);
		
				$result = $dbAccess->overwriteUser($newUser);
				if($result){
					array_push($success_messages, "Modifica avvenuta con successo");
					setcookie('login',$new_hashValue);
				}else{
					array_push($failure_messages, "Modifica fallita");
					$allOk = false;
				}
		

			}

			//faccio i replacement: dove possibile col valore nuovo, altrimenti con quello vecchio
			$replacements=array(
				"<email_ph/>"=>$new_email ? $new_email : $user->getEmail(),
	
				"<img_min_ratio/>" => User::$imgMinRatio,
				"<img_max_ratio/>" => User::$imgMaxRatio,
			);

			$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);

			
		}else{
			//echo "nessun valore è stato rilevato, probabilmente arrivo da un'altra pagina<br/>";

			//faccio i replacement coi valori vecchio
			$replacements=array(
				"<email_ph/>" => $user->getEmail(),
	
				"<img_min_ratio/>" => User::$imgMinRatio,
				"<img_max_ratio/>" => User::$imgMaxRatio,
			);

			$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
		}
	}
}else{
	$homePage = getErrorHtml("not_logged");
}

$jointValidation_error_message = getValidationErrorsHtml($validation_error_messages);
$jointSuccess_messages = getSuccessMessagesHtml($success_messages);
$jointFailure_messages = getFailureMessagesHtml($failure_messages);
$homePage = str_replace("<messaggi_form_ph/>", $jointValidation_error_message . "\n" . $jointSuccess_messages . "\n" . $jointFailure_messages, $homePage);





$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>