<?php
include "replacer.php";

$homePage=file_get_contents("../html/forumTemplate.html");

$homePage=replace($homePage);

echo $homePage;


?>