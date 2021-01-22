<?php

include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$homePage=file_get_contents("../html/templates/registratiTemplate.html");
$homePage=replace($homePage);

if(isset($_REQUEST['nickname']) && isset($_REQUEST['password'])){
	$username=$_REQUEST['nickname'];
	$password=$_REQUEST['password'];
	#sanitize


	$inputString=$username.$password;
	$hashValue=hash("md5",$inputString);
	$newUser=new User($username,$hashValue,0);
	#controlla se è già registrato

	$result=$dbAccess->addUser($newUser);
	if($result==false){
		echo "operazione fallita";
	}
}

echo $homePage;

?>