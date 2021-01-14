<?php

include "replacer.php";

$homePage=file_get_contents("../html/registrati.html");
$homePage=replace($homePage);

echo $homePage;

?>