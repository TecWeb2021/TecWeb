<?php
include "replacer.php";

$homePage=file_get_contents("../html/templates/forumTemplate.html");

$homePage=replace($homePage);

echo $homePage;


?>