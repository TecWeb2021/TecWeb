<?php
$myfile = fopen("output.txt", "w") or die("Unable to open file!");
fwrite($myfile, $TOKEN);
fclose($myfile);
?>