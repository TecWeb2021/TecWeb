<?php
include "dbConnection.php";

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

if(isset($_REQUEST['query'])){
	$query=$_REQUEST['query'];
	$res=$dbAccess->getResult($query);
	if($res==null){
		echo "null"."<br/>";
	}elseif($res==false){
		echo "false"."<br/>";
	}elseif(false && $res==true){
		echo "true"."<br/>";
	}else{
		echo "<table style=\"border-collapse: collapse;\">";
		for($i = 0; $i < mysqli_num_fields($res); $i++) {
		    $field_info = mysqli_fetch_field($res);
		    echo "<th>{$field_info->name}</th>";
		}
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

$htmlPage= file_get_contents("../html/templates/db_testingTemplate.html");

echo $htmlPage




?>

