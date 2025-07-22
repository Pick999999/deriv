<?php
  header('Access-Control-Allow-Methods: GET, POST');
  header('Access-Control-Allow-Origin: *'); 
  ob_start();
  ob_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  //https://www.thaicreate.com/community/login-php-jquery-2encrypt.html
  //https://www.cyfence.com/article/design-secured-api/
  
     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);   
     $data = json_decode(file_get_contents('php://input'), true);
	 //echo 'Mode=' .$data['Mode'] ;
     if ($data) {     
        if ($data['Mode'] == 'FixTrade') { 
			MainFixTrade($data); 
		}
        
     }
  

function getAnalyObject($candleStart) {

   $stData = "";      
   $sFileName = 'dataTest.json';
   $file = fopen($sFileName,"r");
   while(! feof($file))  {
     $stData .= fgets($file) ;
   }
   fclose($file);
   $AnalyDataArray = JSON_DECODE($stData,true);   
   for ($i=0;$i<=count($AnalyDataArray)-1;$i++) {
      if (intval($AnalyDataArray[$i]['candleID']) === $candleStart) {
		  $foundAt = $i ; break ;
      }
   }  
   $length = $foundAt  ;
   array_splice($AnalyDataArray, 0, $length);
   return $AnalyDataArray ;



} // end function


function getResultColor($AnalyData, $thisIndex) {

    if ($thisIndex + 1 < count($AnalyData)) {
        $thisColor = $AnalyData[$thisIndex + 1]['thisColor'];
    } else {
        $thisColor = 'No';
    }
    return $thisColor;
}


function MainFixTrade($data) { 
  
   $candleStart = intval($data['candleid']) ;
   $AnalyData = getAnalyObject($candleStart)  ;
   // array 1 คือ element ที่จะเริ่ม  trade fixed
   $foundAt = -1 ;   
   //echo $AnalyData[0]['candleID'];

   $totalTrade = 0 ; $numLoss = 0 ;
   for ($i=1;$i<=count($AnalyData)-1;$i++) {
       $SuggestColor = getSuggestColor($AnalyData, $i);
	   $ResultColor  = getResultColor($AnalyData, $i);
	   $totalTrade++ ; 
	   if ($SuggestColor !== $ResultColor) {
		   $numLoss++ ;
	   } else {
           break;
	   }

   }
   echo $totalTrade . ' Win at ' . $AnalyData[$i]['timefrom_unix'] ;
    	 
  
} // end function

function getSuggestColor($AnalyData, $thisIndex) {

         $emaAbove = $AnalyData[$thisIndex]['emaAbove'] ;
         $AnalyData2 = $AnalyData[$thisIndex];
         if ($AnalyData2['emaAbove'] ==='5') {
			 return 'Red' ;
         } else {
			 return 'Green' ;
		 }


} // end function


?>