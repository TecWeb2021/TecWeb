<?php

class Game{
	private $name;
	private $publication_date;
	private $vote;
	private $sinopsis;
	private $age_range;
	private $review;
	private $image;

	function __construct($_name, $_publication_date, $_vote, $_sinopsis, $_age_range, $_review, $_image){
		$this->name=$_name;
		$this->publication_date=$_publication_date;
		$this->vote=$_vote;
		$this->sinopsis=$_sinopsis;
		$this->age_range=$_age_range;
		$this->review=$_review;
		$this->image=$_image;
	}

	function getName(){
		return $this->name;
	}

	function getPublicationDate(){
		return $this->publication_date;
	}

	function getVote(){
		return $this->vote;
	}

	function getSinopsis(){
		return $this->sinopsis;
	}

	function getAgeRange(){
		return $this->age_range;
	}

	function getReview(){
		return $this->review;
	}
	
	function getImage(){
		return $this->image;
	}
}

?>