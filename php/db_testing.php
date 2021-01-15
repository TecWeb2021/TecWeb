<?php
include "dbConnection.php";

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

if(isset($_GET['query'])){
	$query=$_GET['query'];
	$res=$dbAccess->getResult($query);
	if($res==null){
		echo "null";
	}else{
		echo "<table style=\"border-collapse: collapse;\">";
		foreach($res as $r){
			echo "<tr>";
			foreach($r as $s){
				echo "<td style=\" border: 1px solid black;\">";
				echo $s;
				echo "</td>";
			}
			echo "</tr>";
		}

		echo "</table>";
	}
}

$htmlPage= file_get_contents("../html/queryForm.html");

echo $htmlPage




?>

