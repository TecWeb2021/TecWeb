<?php
include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$profilo=file_get_contents("../html/templates/profilo_utenteTemplate.html");

#sistema qua sotto: controllare se l'utente è admin
if(isset($_COOKIE['login'])){
	$admin=file_get_contents("../html/templates/adminTemplate.html");
	$profilo=str_replace("<admin_placeholder_ph/>", $admin, $profilo);
}

$profilo=replace($profilo);




echo $profilo;
