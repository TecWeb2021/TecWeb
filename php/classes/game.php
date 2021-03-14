<?php

class Game{
	protected $name;
	protected $publication_date;
	protected $vote;
	protected $sinopsis;
	protected $age_range;
	protected $review;
	protected $image;
	protected $consoles;
	protected $genres;

	//è importante che i seguenti valori corrispondano a quelli presenti nell'html
	public static $possible_consoles=array("PS4","PS5","XboxOne","XboxSeriesX");
	public static $possible_genres=array("Avventura","Azione","Platform","Picchiaduro","Simulazione","Sparatutto");


	function __construct($_name, $_publication_date, $_vote, $_sinopsis, $_age_range, $_review, $_image, $_consoles=null, $_genres=null){
		$this->name=$_name;
		$this->publication_date=$_publication_date;
		$this->vote=$_vote;
		$this->sinopsis=$_sinopsis;
		$this->age_range=$_age_range;
		$this->review=$_review;
		$this->image=$_image;
		$this->consoles=$_consoles;
		$this->genres=$_genres;
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

	function getConsoles(){
		return $this->consoles;
	}
	
	function getGenres(){
		return $this->genres;
	}
}

?>