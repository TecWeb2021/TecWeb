<?php
include "replacer.php";

$homePage=file_get_contents("../html/giochi.html");

$homePage=replace($homePage);

echo $homePage;


?>