<?php

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di placeholder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$homePage=file_get_contents("../html/templates/loginTemplate.html");

$user=getLoggedUser($dbAccess);

if($user){
	$homePage="Hai già fatto il login";
}else{
	if(isset($_REQUEST['nomeUtente'])){
		echo "almeno un valore rilevato";

		$username = $_REQUEST['nomeUtente'];
		$password = $_REQUEST['password'];

		$error_message = "";
		
		$error_messages = array(
			'username' => "Nome utente non presente",
			'password' => "Password non presente"
		);

		// controllo i campi obbligatori

		if($username === null || $username === ""){
			$error_message = $error_message . $error_messages['username'] . "<br/>";
			echo "username";
		}
		if($password === null || $password === ""){
			$error_message = $error_message . $error_messages['password'] . "<br/>";
			echo "pass";
		}

		if($error_message !== ""){
			$homePage = str_replace("<messaggi_form_ph/>", $error_message, $homePage);
		}else{
			$hashValue=getHash($username, $password);
			$user=$dbAccess->getUserByHash($hashValue);
			if($user){
				
				$homePage = str_replace("<messaggi_form_ph/>", "Benvenuto" . $username, $homePage);
				setcookie("login",$hashValue);
				header('Location: home.php');
			}else{
				$homePage = str_replace("<messaggi_form_ph/>", "Nome utente o password non corretti", $homePage);
			}
			
		}

		$replacements=array("<username_ph/>"=>$username);
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
	}else{
		$replacements=array("<username_ph/>"=>"");
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}
	}

	

	
}



$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>