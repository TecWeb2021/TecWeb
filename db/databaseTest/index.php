<?php
require_once "dbConnection.php";


$dbAccess=new DBAccess();
echo $dbAccess->openDBConnection();


?>