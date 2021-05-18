<?php
require_once "replacer.php";
require_once "dbConnection.php";

$dbAccess=new DBAccess;
$dbAccess->openDBConnection();

$homePage=file_get_contents("../html/templates/notizieTemplate.html");

$homePage=replace($homePage);



function createNewsHTMLItem($news, $isUserAdmin=false){
	$item=file_get_contents("../html/templates/newsListItemTemplate.html");
	
	$replacements = array(
		"<news_date_ph/>" => dateToText($news->getLastEditDateTime()),
		"<news_url_ph/>" => "notizia.php?news=".$news->getTitle(),
		"<news_title_ph/>" => $news->getTitle(),
		"<news_author_ph/>" => $news->getAuthor()->getUsername(),
		"<img_path_ph/>" => "../".getSafeImage($news->getImage1()->getPath()),
		"<img_alt_ph/>" => $news->getImage1()->getAlt(),
		"<news_content_ph/>" => $news->getContent(),
		"<news_edit_ph/>" => "edit_notizia.php?news=".strtolower($news->getTitle())
	);

	$item = str_replace(array_keys($replacements), array_values($replacements), $item);

	if($isUserAdmin){
		$item=str_replace("<admin_func_ph>","",$item);
		$item=str_replace("</admin_func_ph>","",$item);
	}else{
		$item=preg_replace("/\<admin_func_ph\>.*\<\/admin_func_ph\>/","",$item);
	}
	
	return $item;
}



function createNewsList($list, $isUserAdmin=false){
	if(!$list){
		return "";
	}
	$stringsArray=array();
	foreach($list as $entry){
		$s=createNewsHTMLItem($entry, $isUserAdmin);
		array_push($stringsArray, $s);
	}
	$joinedItems=implode( " ", $stringsArray);
	return $joinedItems;
}


$user=getLoggedUser($dbAccess);
$isAdmin=$user && $user->isAdmin() ? true : false; 

$category = isset($_REQUEST['categoria']) ? $_REQUEST['categoria'] : null;


$newsPartName = isset($_REQUEST['searchbar']) ? $_REQUEST['searchbar'] : null;
if($newsPartName === null){

	$newsPartName = isset($_REQUEST['filtroSearchMemoria']) ? $_REQUEST['filtroSearchMemoria'] : null;
}
echo "newsPartName: {" . $newsPartName . "}<br/>";


if(!in_array($category, News::$possible_categories)){
	$category = null;
}

switch ($category) {
	case 'Hardware':
		$replacements = array(
			'<filtro_hardware_attivo_ph/>' => 'class="dropbtn_attivo"',
			'<filtro_giochi_attivo_ph/>' => 'class="dropbtn"',
			'<filtro_eventi_attivo_ph/>' => 'class="dropbtn"'
		);
		break;
	case 'Giochi':
		$replacements = array(
			'<filtro_hardware_attivo_ph/>' => 'class="dropbtn"',
			'<filtro_giochi_attivo_ph/>' => 'class="dropbtn_attivo"',
			'<filtro_eventi_attivo_ph/>' => 'class="dropbtn"'
		);
		break;
	case 'Eventi': // ordine cronologico e comunque di default
		$replacements = array(
			'<filtro_hardware_attivo_ph/>' => 'class="dropbtn"',
			'<filtro_giochi_attivo_ph/>' => 'class="dropbtn"',
			'<filtro_eventi_attivo_ph/>' => 'class="dropbtn_attivo"'
		);
		break;
	default:
		$replacements = array(
			'<filtro_hardware_attivo_ph/>' => 'class="dropbtn"',
			'<filtro_giochi_attivo_ph/>' => 'class="dropbtn"',
			'<filtro_eventi_attivo_ph/>' => 'class="dropbtn"'
		);
		break;
}


$replacements["<search_filter_memory_ph/>"] = $newsPartName;


$homePage = str_replace(array_keys($replacements), array_values($replacements), $homePage);

$homePage = str_replace("<opzioni_ph/>",createNewsOptions($dbAccess),$homePage);

$list=$dbAccess->getNewsList(null, $category, $newsPartName);
$newsListString=createNewsList($list, $isAdmin);
if($newsListString === ""){
	$newsListString = getErrorHtml("no_news");
}

$homePage=preg_replace("/\<news_divs_ph\/\>/",$newsListString,$homePage);

$basePage=createBasePage("../html/templates/top_and_bottomTemplate.html", "notizie", $dbAccess);

$basePage=str_replace("<page_content_ph/>", $homePage, $basePage);

$basePage=replace($basePage);

echo $basePage;


?>