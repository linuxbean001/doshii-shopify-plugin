<?php
$con = new mysqli("localhost","doshii_shopify","{)=oxSIl&pHu","doshii_shopify_app");
// Check connection
if ($con -> connect_errno) {
  echo "Failed to connect to MySQL: " . $con -> connect_error;
  exit();
}
//session_start(); //start a session

?>