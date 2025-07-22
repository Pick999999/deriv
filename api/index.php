<?php
 
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *'); 
ob_start();

   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);   
   $data = json_decode(file_get_contents('php://input'), true);
   echo "Hello V3 " ;
  if ($data) {
      //if ($data['Mode'] == 'getAction') { getActionV3($data); }
	     //if ($data['Mode'] == 'getLastAction') { getActionV3($data); }
      return;   
   } else {
      //$candleData = getActionV3($data='');
      $candleData = getCandleData2();
      echo ' Len= ' . len($candleData) ;
   }

function getCandleData2() {

 
 $sFileName =  'rawData.json';
 $st = '';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file); 
 $candleDataA = JSON_DECODE($st,true);

 for ($i=0;$i<=60;$i++) {
	 $candleDataB[] = $candleDataA[$i] ;
 }
 

 
 echo 'Len=' . count($candleDataB) . '<br>';
 return $candleDataB ;

 
 

} // end function


  
?>
    
