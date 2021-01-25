<?php

class Image{
	protected $authorName;
	protected $gameName;
	protected $date_time;
	protected $content;

	function __construct($_authorName,$_alt){
		$this->path=$_path;
		$this->alt=$_alt;
	}

	function getPath(){
		return $this->path;
	}

	function getAlt(){
		return $this->alt;
	}
}



?>