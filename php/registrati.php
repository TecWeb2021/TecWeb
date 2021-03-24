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
	$username=isset($_REQUEST['username']) ? $_REQUEST['username'] : null;
	#sanitize
	$password=isset($_REQUEST['password']) ? $_REQUEST['password'] : null;
	#sanitize
	$email=isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
	#sanitize
	$imagePath=saveImageFromFILES($dbAccess, "immagine");
	#sanitize

	$allOk = true;

	$error_message = "";
	if($username || $email || $password || $imagePath){
		echo "almeno un valore è stato inserito"."<br/>";
		

		//non è chiaro scrivere solo non presente quando il problema potrebbe essere un altro
		$error_messages = array(
			'username' => "Username non presente",
			'email' => "Email non presente",
			'password' => "Password non presente",
			'immagine' => "Immagine non presente",
		);

		//eseguo i controlli sugli input
		if($username == null){
			$error_message = $error_message . $error_messages['username'] . "<br/>";
		}
		if($email == null){
			$error_message = $error_message . $error_messages['email'] . "<br/>";
		}
		//controllo di lunghezza temporaneo
		if($password == null || strlen($password) < 5){
			$error_message = $error_message . $error_messages['password'] . "<br/>";
		}
		//controllo se è false perchè è così che funziona la funzione saveImageFromFILES
		if($imagePath === false){
			$error_message = $error_message . $error_messages['immagine'] . "<br/>";
		}

		
		

		if($error_message != ""){
			//se c'è stato almeno un errore ...
			echo $error_message;

			
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
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}

		
	}else{
		echo "nessun valore è stato inserito, probabilmente arrivo da un'altra pagina"."<br/>";

		//metto tutti i valori alla stringa vuota
		$replacements=array(
		"<email_ph/>" => "",
		"<username_ph/>" => ""
		);

		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}

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