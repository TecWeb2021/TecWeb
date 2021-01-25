<?php
require_once("dbConnection.php");
require_once("classes/user.php");

function replace($subject){
  $subject=preg_replace("/Giochi\.html/","giochi.php",$subject);
  $subject=preg_replace("/Home\.html/","home.php",$subject);
  $subject=preg_replace("/Notizie\.html/","notizie.php",$subject);
  $subject=preg_replace("/Forum\.html/","forum.php",$subject);
  $subject=preg_replace("/\"([A-Za-z]*)\.html\"/","\"$1.php\"",$subject);
  return $subject;
}

function createBasePage($templatePath, $page, $dbAccess){
	if(isset($_REQUEST['logout'])){
		logout();
	}

	$user=getLoggedUser($dbAccess);

	$basePage=generatePageTopAndBottom($templatePath, $page, $user);

	return $basePage;

}

function generatePageTopAndBottom($templatePath, $page, $user, $defaultUserImagePath="../images/login.png"){

	if(isset($_REQUEST['tendina']) && ($_REQUEST['tendina']=='true' || $_REQUEST['tendina']=='false')){
		$templatePath="../html/templates/top_and_bottomTemplateNoJS.html";
	}
	$base=file_get_contents($templatePath);
	$url=$_SERVER['REQUEST_URI'];

	if (strpos($url, '?') !== false) {
    	$url=$url."&";
	}else{
		$url=$url."?";
	}
	$base=str_replace("<this_url_ph/>", $url, $base);

	#completare i replacements
	$titleAndBreadcrumbReplacements=array(
		"home.php"=>["Home","Home"],
		"giochi.php"=>["Giochi",""],
		"notizie.php"=>["Notizie",""],
		"notizia.php"=>["Notizia","<a class=\"link_breadcrumb\" href=\"notizie.php\">Notizie</a> > Notizia"]
	);

	foreach ($titleAndBreadcrumbReplacements as $key => $value) {
		if(strpos($url, $key)!=false){
			//echo $key;
			$base=str_replace("<page_title_ph/>", $value[0]." - ALLGames", $base);
			$base=str_replace("<page_breadcrumb_ph/>", $value[1], $base);
		}
	}

	$possiblePages=array("home","giochi","notizie");
	if(!in_array($page, $possiblePages)){
		$page=null;
	}
	$base=preg_replace("/\<$page\_active\/\>/", "", $base);
	$base=preg_replace("/\<$page\_active\>/", "", $base);

	foreach ($possiblePages as $value) {
		if($page!=$value){
			$base=preg_replace("/\<$value\_active\>class\=\"active\"\<$value\_active\/\>/", "", $base);
		}
	}

	
	if($user){
		if($user->getImage()){
			$base=str_replace("<user_img_path_ph/>", "../".$user->getImage()->getPath(), $base);
		}

		$base=preg_replace("/\<not_logged_in\>.*\<\/not_logged_in\>/","",$base);
		$base=str_replace("<logged_in>","",$base);
		$base=str_replace("</logged_in>","",$base);

		$base=str_replace("<username_ph/>", $user->getUsername(), $base);
		
	}else{
		$base=preg_replace("/\<logged_in\>.*\<\/logged_in\>/","",$base);
		$base=str_replace("<not_logged_in>","",$base);
		$base=str_replace("</not_logged_in>","",$base);
	}
	#la riga qua sotto fa quello che deve solo se il tag non è giù stato sostiuito, quindi solo l'utente non ha un'immagine
	$base=str_replace("<user_img_path_ph/>",$defaultUserImagePath,$base);



	if(isset($_REQUEST['tendina'])){
		if($_REQUEST['tendina']=='true'){
			$base=str_replace("topnav","topnav responsive",$base);	
			$base=str_replace("<tendina_bool_ph/>","false",$base);
		}elseif($_REQUEST['tendina']=='false'){
			$base=str_replace("<tendina_bool_ph/>","true",$base);
		}
	}

	return $base;
}


function getHash($username, $password){
	$inputString=$username.$password;
	$hashValue=hash("md5",$inputString);
	return $hashValue;
}


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
	$user=null;
	if(isset($_COOKIE['login'])){
		$hash=$_COOKIE['login'];
		#sanitize
		$user=$dbAccess->getUserByHash($hash);
	}
	return $user;
}

function saveImageFromFILES($dbAccess, $imgReceiveName, $uploaddir='../images/'){
	//echo "saveImageFromFILES";
	//print_r($_FILES);
	$image= isset($_FILES["$imgReceiveName"]) ? $_FILES["$imgReceiveName"] : null;
	//print_r($image);
	if(!$image){
		return false;
	}
	echo "step1";
	#Recupero il percorso temporaneo del file
	$image_tmp_location = $image['tmp_name'];
	#recupero il nome originale del file caricato

	$originalName=$image['name'];

	#ricavo nome immagine col numero più alto presente nel database
	$imagesList=$dbAccess->getImages();
	$numArray=array();
	echo "step2";
	foreach ($imagesList as $image) {
		//echo $image->getPath()."<br/>";
		$a=explode("/",$image->getPath())[1];
		//echo $a."<br/>";
		$num= explode(".",$a)[0];
		array_push($numArray, $num);
	}
	print_r($numArray);
	echo "step3";
	$maxNum= count($numArray)>0 ? max($numArray) : -1;

	#ricavo il nome da assegnare al nuovo file
	$newNumber=$maxNum+1;
	echo "newNUmber :".$newNumber;
	$extension=end(explode('.', $originalName));
	$newFileName=$newNumber.".".$extension;
	$fileDestination=$uploaddir . $newFileName;
	$imgSaveResult=move_uploaded_file($image_tmp_location, $fileDestination);

	if($imgSaveResult){
		$filePath="images"."/".$newFileName;
		return $filePath;
	}else{
		return false;
	}
}

?>