<?php

include "replacer.php";

$homePage=file_get_contents("../html/login.html");
$homePage=replace($homePage);

echo $homePage;

?>