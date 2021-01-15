<?php

function replace($subject){
  $subject=preg_replace("/\.html\"/",".php\"",$subject);
  return $subject;
}

?>