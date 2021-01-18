<?php
include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/formGiocoTemplate.html");
$homePage=replace($homePage);


$userLevel="not_authenticated";
if(isset($_COOKIE['login'])){
	$loginHash=$_COOKIE['login'];
	$user=$dbAccess->getUserByHash($loginHash);
	if($user==null || $user[0]['IsAdmin']==false){
		echo "non puoi accedere a questa pagina";
	}else{
		echo $homePage;
	}
}






?>