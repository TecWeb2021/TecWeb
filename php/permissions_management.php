<?php
require_once("dbConnection.php");
function logout(){
	$logout=$_REQUEST['logout'];
	#sanitize;
	if($logout='true' && isset($_COOKIE['login'])){
		setcookie("login","");
		echo "cookie unset";
		header("Refresh:0");
	}
}

function getLoggedUser($dbAccess){
	if(isset($_COOKIE['login'])){
		$hash=$_COOKIE['login'];
		#sanitize
		$user=$dbAccess->getUserByHash($hash);
	}
	return $user;
}

?>