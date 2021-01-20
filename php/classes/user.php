<?php

class User{
	private $username;
	private $hash;
	private $is_admin;

	function __construct($_username, $_hash, $_is_admin){
		$this->username=$_username;
		$this->hash=$_hash;
		$this->is_admin=$_is_admin;
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
}

?>