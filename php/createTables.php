<?php


include "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();


$sqlFilesList=array();

foreach($sqlFilesList as $sqlFile){
	$sql= file_get_contents($sqlFile){
		mysqli_query($sql)
	}
}



?>