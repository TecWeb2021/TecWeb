<?php
include "replacer.php";

$homePage=file_get_contents("../html/forum.html");

$homePage=replace($homePage);

echo $homePage;


?>