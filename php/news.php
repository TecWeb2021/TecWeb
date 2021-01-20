<?php
/*
abstract class NewsCategory{
	const HARDWARE='hardware';
	const EVENTO='event';
}

abstract class ExtendedNewsCategory extends NewsCategory{
	const GIOCO='gioco';
}
*/
class News{
	private $title;
	private $content;
	private $author;
	private $last_edit_date_time;
	private $image;
	private $category;

	function __construct($_title, $_content, $_author, $_last_edit_date_time, /*$_image,*/ $_category){
		$this->title=$_title;
		$this->content=$_content;
		$this->author=$_author;
		$this->last_edit_date_time=$_last_edit_date_time;
		$this->image=$_image;
		$this->category=$_category;
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
}

?>