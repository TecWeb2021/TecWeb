<?php
/*
abstract class NewsCategory{
	const HARDWARE = 'hardware';
	const EVENTO = 'event';
}

abstract class ExtendedNewsCategory extends NewsCategory{
	const GIOCO = 'gioco';
}
*/
class News{
	protected $title;
	protected $content;
	protected $author;
	protected $last_edit_date_time;
	protected $image;
	protected $category;
	protected $gameName;

	public static $possible_categories = array("Hardware", "Giochi", "Eventi");

	function __construct($_title, $_content, $_author, $_last_edit_date_time, $_image, $_category, $_gameName = null){
		$this->title = $_title;
		$this->content = $_content;
		$this->author = $_author;
		$this->last_edit_date_time = $_last_edit_date_time;
		$this->image = $_image;
		$this->category = $_category;
		$this->gameName = $_gameName;
	}

	function getTitle(){
		return $this->title;
	}

	function getContent(){
		return $this->content;
	}

	function getAuthor(){
		return $this->author;
	}

	function getLastEditDateTime(){
		return $this->last_edit_date_time;
	}

	function getImage(){
		return $this->image;
	}

	function getCategory(){
		return $this->category;
	}

	function getGameName(){
		return $this->gameName;
	}
}

?>