

<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/formGiocoTemplate.html");


$user=getLoggedUser($dbAccess);

if($user){
	if($user->isAdmin()){
		#valori in input
		$name= isset($_REQUEST['nome']) ? $_REQUEST['nome'] : null;
		#sanitize
		$developer= isset($_REQUEST['sviluppo']) ? $_REQUEST['sviluppo'] : null;
		#sanitize
		$date= isset($_REQUEST['data']) ? $_REQUEST['data'] : null;
		#sanitize
		$age_range= isset($_REQUEST['pegi']) ? $_REQUEST['pegi'] : null;
		#sanitize
		$consoles= isset($_REQUEST['console']) ? $_REQUEST['console'] : null;
		#sanitize
		$genres= isset($_REQUEST['genere']) ? $_REQUEST['genere'] : null;
		#sanitize
		$image= isset($_FILES['immagine']) ? $_FILES['immagine'] : null;
		#sanitize
		$imageAlt= isset($_REQUEST['alternativo']) ? $_REQUEST['alternativo'] : null;
		#sanitize
		$prequel= isset($_REQUEST['prequel']) ? $_REQUEST['prequel'] : null;
		#sanitize
		$sequel= isset($_REQUEST['sequel']) ? $_REQUEST['sequel'] : null;
		#sanitize
		$dlc= isset($_REQUEST['dlc']) ? $_REQUEST['dlc'] : null;
		#sanitize
		$sinopsis= isset($_REQUEST['descrizione']) ? $_REQUEST['descrizione'] : null;
		#sanitize
		$review= isset($_REQUEST['recensione']) ? $_REQUEST['recensione'] : null;
		#sanitize

		$phMapping=array(
			"<game_name_ph/>"=>$name,
			"<developer_ph/>"=>$developer,
			"<date_ph/>"=>$date,
			"<age_range_ph/>"=>$age_range,
			"<img_alt_ph/>"=>$imageAlt,
			"<dlc_ph/>"=>$dlc,
			"<sinopsis_ph/>"=>$sinopsis,
			"<review_ph/>"=>$review
		);

		$requiredValues=array($name , $developer , $date , $age_range , $consoles , $genres , $image , $imageAlt , $prequel , $sequel , $dlc , $sinopsis , $review);

		$areAllAssigned=true;
		for($i=0;$i<count($requiredValues);$i++){
			if(!$requiredValues[$i]){
				$areAllAssigned=false;
				echo "$i is null";
			}
		}
		if($areAllAssigned){
#function __construct($_name, $_publication_date, $_vote, $_sinopsis, $_age_range, $_review, $_image){

			#upload dell'immagine
			$uploaddir = '../images/';
			#Recupero il percorso temporaneo del file
			$image_tmp_location = $image['tmp_name'];
			#recupero il nome originale del file caricato

			$originalName=$image['name'];
			$imagesList=$dbAccess->getImages();
			$imagesCount=count($imagesList);
			$extension=end(explode('.', $originalName));
			$newFileName=$imagesCount.".".$extension;
			$fileDestination=$uploaddir . $newFileName;

			if (move_uploaded_file($image_tmp_location, $fileDestination)) {
			  	#Se l'operazione è andata a buon fine...
			  	$imagePath="images"."/".$newFileName;
			  	$newImage=new Image($imagePath, $imageAlt);
			  	$newGame=new Game($name, $date, 0, $sinopsis, $age_range, $review, $newImage);
				$result=$dbAccess->addGame($newGame);
				if($result!=null){
					header('Location: home.php');
				}else{
					
					echo "salvataggio del gioco fallito";
				}
			}else{
				echo "caricamento dell'immagine fallito";
			}
		}else{
			echo "inserire i valori";

		}
		foreach ($phMapping as $key => $value) {
			$homePage=str_replace($key, $value, $homePage);
		}

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