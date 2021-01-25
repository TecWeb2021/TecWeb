<?php
require_once("news.php");
/*
abstract class NewsCategory{
	const HARDWARE='hardware';
	const EVENTO='event';
}

abstract class ExtendedNewsCategory extends NewsCategory{
	const GIOCO='gioco';
}
*/



class GameNews extends News{
	protected $gameName;

	function __construct($_title, $_content, $_author, $_last_edit_date_time, $_image, $_category, $_gameName){
		News::__constructor($_title, $_content, $_author, $_last_edit_date_time, $_image, $_category);
		$this->gameName=$_gameName;
	}

	function getGameName(){
		return $this->gameName;
	}
}

?>