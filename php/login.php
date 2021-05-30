<?php

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di placeholder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$homePage=file_get_contents("../html/templates/loginTemplate.html");

$user=getLoggedUser($dbAccess);

$validation_error_messages = array();
$success_messages = array();
$failure_messages = array();

if($user){
	$homePage = getErrorHtml("already_logged");
}else{
	if(isset($_REQUEST['nomeUtente'])){
		echo "almeno un valore rilevato" . "<br/>";

		$username = getSafeInput('nomeUtente');
		$password = getSafeInput('password');

		// qui non vengono fatte validazioni perché l'utente non deve sapere se ha sbagliato il formato. Deve solo sapere se le credenziali sono giuste o meno.

		if(count($validation_error_messages) > 0){
			
		}else{
			$hashValue=getHash($username, $password);
			$user=$dbAccess->getUserByHash($hashValue);
			if($user){
				
				array_push($success_messages, "Login avvenuto con successo");
				setcookie("login",$hashValue);
				header('Location: home.php');
			}else{
				array_push($failure_messages, "Nome o password errati");
			}
			
		}

		$replacements=array("<username_ph/>"=>$username);

		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
	}else{
		$replacements=array("<username_ph/>"=>"");

		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);
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