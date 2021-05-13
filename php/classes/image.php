<?php

class Image{
	private $path;
	private $alt;

	function __construct($_path,$_alt){
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