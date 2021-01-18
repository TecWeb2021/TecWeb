<?php

include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$homePage=file_get_contents("../html/templates/registratiTemplate.html");
$homePage=replace($homePage);

if(isset($_POST['nome_utente']) && isset($_POST['password'])){
	$username=$_POST['nome_utente'];
	$password=$_POST['password'];
	#sanitize

	#controlla se è già registrato

	$result=$dbAccess->addUser($username,$password,0);
	if($result==false){
		echo "operazione fallita";
	}
}

echo $homePage;

?>