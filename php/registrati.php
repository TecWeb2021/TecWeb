<?php

require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$homePage=file_get_contents("../html/templates/registratiTemplate.html");

$user=getLoggedUser($dbAccess);

if($user){
	$homePage="Sei già registrato e hai già fatto il login";
}else{
	if(isset($_REQUEST['nickname']) && isset($_REQUEST['password']) && isset($_REQUEST['email'])){
		$username=$_REQUEST['nickname'];
		$password=$_REQUEST['password'];
		$email=$_REQUEST['email'];
		#sanitize

		if(isset($_FILE['userfile'])){
			$name=$_FILE['userfile']['name'];
			#sanitize
			echo "nome file caricato: ".$name;
		}

		$hashValue=getHash($username, $password);
		$newUser=new User($username,$hashValue,0, null, $email);
		#controlla se è già registrato

		$result=$dbAccess->addUser($newUser);
		if($result==false){
			echo "operazione fallita";
		}else{
			setcookie('login',$hashValue);
			echo "<br/>operazione eseguita con successo<br/>tra 5 secondi verrai portato sulla pagina home";
			header( "refresh:5;url=home.php" );
		}
	}
}
/*
#rifaccio il controllo dell'utente dopo l'operazione di registrazione
$user=getLoggedUser($dbAccess);
if($user){
	$homePage="Sei già registrato e hai già fatto il login";
}
*/

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>