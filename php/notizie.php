<?php
include "replacer.php";

$homePage=file_get_contents("../html/notizie.html");

$homePage=replace($homePage);

echo $homePage;


?>