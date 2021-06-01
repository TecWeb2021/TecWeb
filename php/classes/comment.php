<?php

class Comment{
	protected $id;
	protected $authorName;
	protected $gameName;
	protected $date_time;
	protected $content;

	function __construct($_authorName,$_gameName, $_date_time, $_content, $_id = null){
		$this->id = $_id;
		$this->authorName=$_authorName;
		$this->gameName=$_gameName;
		$this->date_time=$_date_time;
		$this->content=$_content;
	}

	function getId(){
		return $this->id;
	}

	function getAuthorName(){
		return $this->authorName;
	}

	function getGameName(){
		return $this->gameName;
	}

	function getDateTime(){
		return $this->date_time;
	}

	function getContent(){
		return $this->content;
	}
}



?>