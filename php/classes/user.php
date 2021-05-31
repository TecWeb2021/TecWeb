<?php

class User{
	private $username;
	private $hash;
	private $is_admin;
	private $image;
	private $email;

	public static $imgMinRatio = 0;
	public static $imgMaxRatio = INF;

	function __construct($_username, $_hash, $_is_admin=0, $_image=null, $_email){
		$this->username=$_username;
		$this->hash=$_hash;
		$this->is_admin=$_is_admin;
		$this->image=$_image;
		$this->email=$_email;

	}

	static function copyConstruct($_user){
		return new User($_user->getUsername(), $_user->getHash(), $_user->isAdmin(), $_user->getImage(), $_user->getEmail());
	}

	function getUsername(){
		return $this->username;
	}

	function getHash(){
		return $this->hash;
	}

	function isAdmin(){
		return $this->is_admin;
	}

	function getImage(){
		return $this->image;
	}

	function getEmail(){
		return $this->email;
	}


	function setHashByPassword($password){
		$inputString=$this->username.$password;
		$hashValue=hash("md5",$inputString);
		$this->hash=$hashValue;
	}

	function setEmail($email){
		$this->email=$email;
	}

	function setImage($image){
		$this->image=$image;
	}
}

?>