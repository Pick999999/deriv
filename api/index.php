<?php
 echo "Hello V2 " ;
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *'); 
ob_start();

   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);   
   $data = json_decode(file_get_contents('php://input'), true);
?>
    
