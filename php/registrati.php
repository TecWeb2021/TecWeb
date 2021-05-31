<?php

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$homePage=file_get_contents("../html/templates/registratiTemplate.html");

$user=getLoggedUser($dbAccess);

$validation_error_messages = array();
$success_messages = array();
$failure_messages = array();

if($user){
	$homePage = getErrorHtml("already_logged");
}else{
	

	$allOk = true;

	$error_message = "";
	if(isset($_REQUEST['email'])){
		echo "almeno un valore è stato inserito"."<br/>";
		
		$email = getSafeInput('email', 'string');
		#sanitize
		$username = getSafeInput('username', 'string');
		#sanitize
		$imagePath = saveImageFromFILES($dbAccess, "immagine", User::$imgMinRatio, User::$imgMaxRatio);
		#sanitize
		$password = getSafeInput('password', 'string');
		#sanitize
		$repeatPassword = getSafeInput('repeatpassword', 'string');
		#sanitize

		//controllo i campi obbligatori

		$mandatory_fields = array(
			[$username, "nomeUtente"],
			[$email, "email"],
			[$password, "password"]
		);
		foreach ($mandatory_fields as $value) {
			if($value[0] === null || validateValue($value[0], $value[1]) === false ){
				array_push($validation_error_messages, getValidationError($value[1]));
			}
		}

		//controllo se è false perchè è così che funziona la funzione saveImageFromFILES
		if($imagePath === false || $imagePath === null){
			array_push($validation_error_messages, getValidationError('immagine'));
		}

		if($password !== $repeatPassword){
			array_push($validation_error_messages, getValidationError('repeatpassword'));
		}

		// controllo i campi obbligatori derivati

		// controllo i campi opzionali

		
		

		if(count($validation_error_messages) > 0){

		}else{
			echo "non ci sono stati errori" . "<br/>";
			
			if($imagePath!=false){
				$image=new Image($imagePath, "immagine utente");
				$hashValue=getHash($username, $password);
				$newUser=new User($username,$hashValue,0, $image, $email);
				#controlla se è già registrato
	
				$result=$dbAccess->addUser($newUser);
				if($result){
					setcookie('login',$hashValue);
					array_push($success_messages, 'Registrazione completata con successo');
					header( "Location: home.php" );
				}else{
					array_push($failure_messages, 'Registrazione fallita');
					$allOk = false;
				}
	
			}

		}

		//faccio i replacement dove possibile, altrimenti metto valore vuoto
		$replacements=array(
			"<email_ph/>"=>$email ? $email : "",
			"<username_ph/>"=>$username ? $username : "",
	
			"<img_min_ratio/>" => User::$imgMinRatio,
			"<img_max_ratio/>" => User::$imgMinRatio,
		);

		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);

		
	}else{
		echo "nessun valore è stato inserito, probabilmente arrivo da un'altra pagina"."<br/>";

		//metto tutti i valori alla stringa vuota
		$replacements=array(
			"<email_ph/>" => "",
			"<username_ph/>" => "",
	
			"<img_min_ratio/>" => User::$imgMinRatio,
			"<img_max_ratio/>" => User::$imgMinRatio,
		);

		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);

		$allOk = false;
	}
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