<?php

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un'altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$homePage=file_get_contents("../html/templates/registratiTemplate.html");

$user=getLoggedUser($dbAccess);

if($user){
	$homePage="Sei già registrato e hai già fatto il login";
}else{
	

	$allOk = true;

	$error_message = "";
	if(isset($_REQUEST['email'])){
		echo "almeno un valore è stato inserito"."<br/>";
		
		$email=isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
		#sanitize
		$username=isset($_REQUEST['username']) ? $_REQUEST['username'] : null;
		#sanitize
		$imagePath=saveImageFromFILES($dbAccess, "immagine", User::$imgMinRatio, User::$imgMaxRatio);
		#sanitize
		$password=isset($_REQUEST['password']) ? $_REQUEST['password'] : null;
		#sanitize
		$repeatPassword=isset($_REQUEST['repeatpassword']) ? $_REQUEST['repeatpassword'] : null;
		#sanitize

		//non è chiaro scrivere solo non presente quando il problema potrebbe essere un altro
		$error_messages = array(
			'username' => "Username non presente",
			'email' => "Email non presente",
			'password' => "Password non presente",
			'repeatpassword' => "Le password non combaciano",
			'immagine' => "Immagine non presente",
		);

		//controllo i campi obbligatori

		if($username === null || ($errorText = checkString($username, "nomeUtente")) !== true){
			$error_message = $error_message . $error_messages['username'] . "<br/>";
		}
		if($email === null || ($errorText = checkString($email, "email")) !== true){
			$error_message = $error_message . $error_messages['email'] . "<br/>";
		}
		//controllo se è false perchè è così che funziona la funzione saveImageFromFILES
		if($imagePath === false){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}
		if($password === null  || ($errorText = checkString($password, "password")) !== true){

			$error_message = $error_message . $error_messages['password'] . "<br/>";
		}
		

		// controllo i campi obbligatori derivati

		if($password !== null && $repeatPassword !== $password){
			$error_message = $error_message . $error_messages['repeatpassword'] . "<br/>";
		}

		
		

		if($error_message != ""){
			$homePage = str_replace("<messaggi_form_ph/>", $error_message, $homePage);

			
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
					$redirectInterval = 600;
					$homePage =  "<br/>operazione eseguita con successo<br/>tra ".$redirectInterval." secondi verrai portato sulla pagina home";
					header( "refresh:".$redirectInterval.";url=home.php" );
				}else{
					 echo "salvataggio utente fallito" . "<br/>";
					$allOk = false;
				}
	
			}

		}

		//faccio i replacement dove possibile, altrimenti metto valore vuoto
		$replacements=array(
		"<email_ph/>"=>$email ? $email : "",
		"<username_ph/>"=>$username ? $username : ""
		);

		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);

		
	}else{
		echo "nessun valore è stato inserito, probabilmente arrivo da un'altra pagina"."<br/>";

		//metto tutti i valori alla stringa vuota
		$replacements=array(
		"<email_ph/>" => "",
		"<username_ph/>" => ""
		);

		$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);

		$allOk = false;
	}

	
}

//rifaccio il controllo dell'utente dopo l'operazione di registrazione
//credo che questo qua sotto non funzioni perchè il cookie settato può essere rilevato dal php se lo script viene richiamato
/*
$user=getLoggedUser($dbAccess);
echo "user: ".($user ? $user->getUsername() : "nouserfound")."<br/>";
if($user){
	$homePage="Sei già registrato e hai già fatto il login";
}*/


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>