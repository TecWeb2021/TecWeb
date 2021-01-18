<?php

include "replacer.php";
include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/giocoTemplate.html");
$homePage=replace($homePage);

$sottopagine=array('scheda','recensione','notizie','extra');
if(isset($_GET['gioco'])){
	
	
	$sottopagina="scheda";
	if(isset($_GET['sottopagina'])){
		$tmp=$_GET['sottopagina'];		
		if(in_array($tmp, $sottopagine)){
			$sottopagina=$tmp;
		}
	}


	$subpage="";

	switch ($sottopagina) {
    	case "scheda":
			$subpage=file_get_contents("../html/templates/giocoSchedaTemplate.html");
			$subpage=preg_replace("/\<game_name_ph\/\>/", , subject)    
        	break;
    	case "recensione":
        
        	break;
    	case "notizie":
        
	        break;
	    case "extra":
    	
    		break;
	}

$homePage=preg_replace("/\<game_subpage_ph\/\>/",$subpage,$homePage);



}else{
	$homePage="specificare un gioco";
}


echo $homePage;

?>