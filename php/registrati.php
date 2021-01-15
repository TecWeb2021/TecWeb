<?php

include "replacer.php";

$homePage=file_get_contents("../html/templates/registratiTemplate.html");
$homePage=replace($homePage);

echo $homePage;

?>