<?php

class Comment{
	protected $authorName;
	protected $gameName;
	protected $date_time;
	protected $content;

	function __construct($_authorName,$_gameName, $_date_time, $_content){
		$this->authorName=$_authorName;
		$this->gameName=$_gameName;
		$this->date_time=$_date_time;
		$this->content=$_content;
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