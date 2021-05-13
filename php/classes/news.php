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
	protected $image1;
	protected $image2;
	protected $category;
	protected $gameName;
	
	public static $img1MinRatio = 1.3;
	public static $img1MaxRatio = 1.6;
	public static $img2MinRatio = 0.1;
	public static $img2MaxRatio = 0.5;
	public static $possible_categories = array("Hardware", "Giochi", "Eventi");

	function __construct($_title, $_content, $_author, $_last_edit_date_time, $_image1, $_image2, $_category, $_gameName = null){
		$this->title = $_title;
		$this->content = $_content;
		$this->author = $_author;
		$this->last_edit_date_time = $_last_edit_date_time;
		$this->image1 = $_image1;
		$this->image2 = $_image2;
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

	function getImage1(){
		return $this->image1;
	}

	function getImage2(){
		return $this->image2;
	}

	function getCategory(){
		return $this->category;
	}

	function getGameName(){
		return $this->gameName;
	}
}

?>