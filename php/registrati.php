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
	$username=isset($_REQUEST['username']) ? $_REQUEST['username'] : null;
	#sanitize
	$password=isset($_REQUEST['password']) ? $_REQUEST['password'] : null;
	#sanitize
	$email=isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
	#sanitize
	$image=isset($_FILES['immagine']) ? $_FILES['immagine'] : null;
	#sanitize

	if($username && $password && $email && $image){
		
		

		if(isset($_FILE['userfile'])){
			$name=$_FILE['userfile']['name'];
			#sanitize
			echo "nome file caricato: ".$name;
		}

		$imagePath=saveImageFromFILES($dbAccess, "immagine");
		if($imagePath!=false){
			$image=new Image($imagePath, "immagine utente");
			$hashValue=getHash($username, $password);
			$newUser=new User($username,$hashValue,0, $image, $email);
			#controlla se è già registrato

			$result=$dbAccess->addUser($newUser);
			if($result){
				setcookie('login',$hashValue);
				echo "<br/>operazione eseguita con successo<br/>tra 5 secondi verrai portato sulla pagina home";
				header( "refresh:60;url=home.php" );
			}else{
				echo "salvataggio utente fallito";
				
			}

		}else{
			echo "immagine non caricata";
		}

		
	}else{
		echo "inserire i valori";
	}
	$replacements=array(
		"<email_ph/>"=>$email,
		"<username_ph/>"=>$username
		);
		foreach ($replacements as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
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