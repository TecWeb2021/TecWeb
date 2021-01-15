<?php

include "replacer.php";

$homePage=file_get_contents("../html/templates/loginTemplate.html");
$homePage=replace($homePage);

echo $homePage;

?>