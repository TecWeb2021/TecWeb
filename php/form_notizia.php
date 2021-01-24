

<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/formNotiziaTemplate.html");


$user=getLoggedUser($dbAccess);

if($user){
	if($user->isAdmin()){
		#mi salvo i dati ricevuti
		$title=null;
		$category=null;
		$imagePath=null;
		$imageAlt=null;
		$content=null;

		if(!isset($_FILES['immagine'])){
			echo "non hai caricato alcun file";
		}else{
			echo "userifileName: ".$_FILES['immagine']['name'];
		

			$imgSaveResult=saveImageFromFILES($dbAccess, "immagine");

			if ($imgSaveResult!=false) {
			  	//Se l'operazione è andata a buon fine...
			  	echo 'File inviato con successo.';
			  	$imagePath=$imgSaveResult;

				if(isset($_REQUEST['titolo'])){
					$title=$_REQUEST['titolo'];
					#sanitize
					$homePage=str_replace("<news_title_ph/>",$title,$homePage);
				}else{
					$homePage=str_replace("<news_title_ph/>","",$homePage);
				}
				if(isset($_REQUEST['tipologia'])){
					$category=$_REQUEST['tipologia'];
					#sanitize

					//$homePage=str_replace("<news_title_ph/>",$title,$homePage);
				}else{
					//$homePage=str_replace("<news_title_ph/>","",$homePage);
				}
				if(isset($_REQUEST['testo'])){
					$content=$_REQUEST['testo'];
					#sanitize
					$homePage=str_replace("<content_ph/>",$content,$homePage);
				}else{
					$homePage=str_replace("<content_ph/>","",$homePage);
				}
				if(isset($_REQUEST['alternativo'])){
					$imageAlt=$_REQUEST['alternativo'];
					#sanitize
					$homePage=str_replace("<img_alt_ph/>",$imageAlt,$homePage);
				}else{
					$homePage=str_replace("<img_alt_ph/>","",$homePage);
				}

				$image=null;
				if($imagePath){
					echo "<br/>image present";
					$image=new Image($imagePath,$imageAlt);
				}

				if($title==null && $content==null){
					echo "inserisci almeno titolo e contenuto";
				}else{
					$newNews=new News($title, $content, $user, date('Y-m-d'), $image, $category);
					$result=$dbAccess->addNews($newNews);
					if($result!=null){
						header('Location: home.php');
					}
				}
			
			}else{
			  //Se l'operazione è fallta...
			  echo 'Upload NON valido!'; 
			}

		}


		$homePage=str_replace("<news_title_ph/>","",$homePage);
		$homePage=str_replace("<content_ph/>","",$homePage);
		$homePage=str_replace("<img_alt_ph/>","",$homePage);

	}else{
		$homePage="non puoi accedere a questa pagina perchè non sei un amministratore";	
	}

}else{
	$homePage="non puoi accedere a questa pagina perchè non hai fatto il login";
}


$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", null, $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;

?>