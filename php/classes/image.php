<?php

class Image{
	private $path;
	private $alt;

	public static $img1MinRateo = 1.3;
	public static $img1MaxRateo = 1.6;
	public static $img2MinRateo = 0.1;
	public static $img2MaxRateo = 0.5;

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