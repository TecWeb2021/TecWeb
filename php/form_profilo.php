
<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/formProfiloTemplate.html");

$user=getLoggedUser($dbAccess);

if($user){
	$replacements=array(
		"<email_ph/>"=>$user->getEmail(),
		);
	foreach ($replacements as $key => $value) {
		$homePage=str_replace($key, $value, $homePage);
	}

}else{
	$homePage="non puoi accedere a questa pagina perchè non hai fatto il login";
}





$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>