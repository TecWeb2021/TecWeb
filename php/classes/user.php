<?php

class User{
	private $username;
	private $hash;
	private $is_admin;
	private $image;

	function __construct($_username, $_hash, $_is_admin=0, $_image=null){
		$this->username=$_username;
		$this->hash=$_hash;
		$this->is_admin=$_is_admin;
		$this->image=$_image;
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
}

?>