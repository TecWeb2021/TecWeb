

<?php
require_once "replacer.php";
require_once "dbConnection.php";

# Nei vari template ph è acronimo di place holder, cioè una cosa che tiene il posto per un altra.

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/editNotiziaTemplate.html");


$user=getLoggedUser($dbAccess);

if($user){
	if($user->isAdmin()){

		#mi salvo i dati ricevuti
		$title=isset($_REQUEST['titolo']) ? $_REQUEST['titolo'] : null;
		$category=isset($_REQUEST['tipologia']) ? $_REQUEST['tipologia'] : null;
		$image=isset($_FILES['immagine']) && $_FILES['immagine']['name']!="" ? $_FILES['immagine'] : null;
		$imageAlt=isset($_REQUEST['alternativo']) ? $_REQUEST['alternativo'] : null;
		$content=isset($_REQUEST['testo']) ? $_REQUEST['testo'] : null;

		$delete=isset($_REQUEST['delete']) ? $_REQUEST['delete'] : null;
		#sanitize
		if($delete){
			$dbAccess->deleteNews($delete);
			header("refresh=0;url=home.php");
		}

		$required=array($title, $category, $image, $imageAlt, $content);

		$onePresent=false;
		$allPresent=true;
		foreach ($required as $value) {
			if($value){
				$onePresent=true;
				break;
			}
		}
		foreach ($required as $value) {
			if(!$value){
				$allPresent=false;
				break;
			}
		}

		$newsTitle=isset($_REQUEST['news']) ? $_REQUEST['news'] : null;
		if(!$newsTitle){
			$newsTitle=isset($_REQUEST['titolo']) ? $_REQUEST['titolo'] : null;
		}
		if($newsTitle){
			$news=$dbAccess->getNews($newsTitle);
			if($news){
				
				$title=isset($_REQUEST['titolo']) ? $_REQUEST['titolo'] : $news->getTitle();
				$category=isset($_REQUEST['tipologia']) ? $_REQUEST['tipologia'] : $news->getCategory();
				$imageFile=isset($_FILES['immagine']) && $_FILES['immagine']['name']!="" ? $_FILES['immagine'] : null;
				$image=null;
				$imageAlt="";
				if(!$imageFile){
					$image=$news->getImage();
					$imageAlt=isset($_REQUEST['alternativo']) ? $_REQUEST['alternativo'] : null;
				}else{
					$imagePath=saveImageFromFILES($dbAccess, "immagine");
					if($imagePath!=false){
						$image=new Image($imagePath,$imageAlt);
					}else{
						echo "errore nel caricamento immagine";
					}
					
				}
				$content=isset($_REQUEST['testo']) ? $_REQUEST['testo'] : $news->getContent();
				$author=$news->getAuthor();
				$date=$news->getLastEditDateTime();

			}else{
				echo "non è presente la notizia selezionata";
			}

		}else{
			echo "non è specificata una notizia";
		}
#$_title, $_content, $_author, $_last_edit_date_time, $_image, $_category
		$newNews=new News($title, $content, $author, $date, $image, $category);

		$result=$dbAccess->updateNews($newNews);
		echo "esito operazione: ".$result==true ? "true" : "false";

		$replacements=array(
			"<news_title_ph/>"=>$title,
			"<content_ph/>"=>$content,
			"<eventi_checked_ph/>"=>$category=="Eventi" ? "checked=\"checked\"" : "",
			"<giochi_checked_ph/>"=>$category=="Giochi" ? "checked=\"checked\"" : "",
			"<hardware_checked_ph/>"=>$category=="Hardware" ? "checked=\"checked\"" : "",
			"<content_ph/>"=>$content,
			"<img_alt_ph/>"=>$image ? $image->getAlt() : ""
		);

		foreach ($replacements as $key => $value) {
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