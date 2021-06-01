<?php

class Review{
	protected $gameName;
	protected $authorName;
	protected $date_time;
	protected $content;

	function __construct($_gameName, $_authorName, $_date_time, $_content, $_id = null){
		$this->authorName=$_authorName;
		$this->gameName=$_gameName;
		$this->date_time=$_date_time;
		$this->content=$_content;
	}

	function getGameName(){
		return $this->gameName;
	}

	function getAuthorName(){
		return $this->authorName;
	}

	function getDateTime(){
		return $this->date_time;
	}

	function getContent(){
		return $this->content;
	}
}



?>