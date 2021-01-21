<?php

class Review{
	private $content;
	private $author;
	private $last_edit_date_time;

	function __construct($_content, $_author, $_last_edit_date_time){
		$this->content=$_content;
		$this->author=$_author;
		$this->last_edit_date_time=$_last_edit_date_time;
	}

	function getContent(){
		return $this->content;
	}

	function getAuthor(){
		return $this->author;
	}

	function getLastEditDateTime(){
		return $this->last_edit_date;
	}
}

?>