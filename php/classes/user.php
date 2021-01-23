<?php

class User{
	private $username;
	private $hash;
	private $is_admin;
	private $image;
	private $email;

	function __construct($_username, $_hash, $_is_admin=0, $_image=null, $_email){
		$this->username=$_username;
		$this->hash=$_hash;
		$this->is_admin=$_is_admin;
		$this->image=$_image;
		$this->email=$_email;

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
}

?>