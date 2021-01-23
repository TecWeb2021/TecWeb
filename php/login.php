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
	if(isset($_POST['nomeLogin']) && isset($_POST['pw'])){
		$username=$_POST['nomeLogin'];
		$password=$_POST['pw'];
		$hashValue=getHash($username, $password);
		$user=$dbAccess->getUserByHash($hashValue);
		if($user){
			$username=$user->getUsername();
			
			echo "Benvenuto ".$username;
			setcookie("login",$hashValue);
			header('Location: home.php');
		}else{
			echo "Nome utente o password non corretti";
			$replacements=array("<username_placeholder_ph/>"=>$username);
			foreach ($replacements as $key => $value) {
				$homePage=str_replace($key, $value, $homePage);
			}
		}
	}else{
		$replacements=array("<username_placeholder_ph/>"=>"");
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