<?php

header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *'); 
   ob_start();
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);   
   $data = json_decode(file_get_contents('php://input'), true);
   if ($data) {
      
      $sObj = new stdClass();
      $sObj->No   = 1 ;
      $sObj->candleID   = 'AAAA-ฺฺฺฺBBBB';
      echo JSON_ENCODE($sObj,JSON_UNESCAPED_UNICODE) ;

   } else {        
     echo "เวลาปัจจุบัน: " . date("H:i:s");
     echo "No Data USE Vercel IN API V22 " .  ;
   }
?>
    
