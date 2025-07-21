<?php
//AjaxPredictAll.php
  header('Access-Control-Allow-Methods: GET, POST');
  //header('Access-Control-Allow-Origin: *'); 
  ob_start();
  //https://www.thaicreate.com/community/login-php-jquery-2encrypt.html
  //https://www.cyfence.com/article/design-secured-api/
  
     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);   
     $data = json_decode(file_get_contents('php://input'), true);
     if ($data) {
        $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
        require_once($newUtilPath ."/src/newutil.php");		
        if ($data['Mode'] == 'getSuggest') { getSuggest($data); }
        return;
     }  else {
		 getSuggestDeepSeek();

	 }
  
function getSuggestDeepSeek($data='') { 
/*
DeepSeek วิเคราะห์ ให้ 2 อย่าง  
 1.Trend
 2.Candle Patterns

*/
	     $candleData = getCandleData2() ;
		 $st = JSON_ENCODE($candleData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;

         $timeWant = '22:18:00';
		 $candleDataB = filterCandle($candleData,$timeWant) ;
		 $lastIndex = count($candleDataB)-1 ;
		 


		 $newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/';
         require_once($newUtilPath ."deriv/CandlestickAnalyzer_DeepSeek.php"); 

         $analyzer = new AdvancedCandlestickAnalyzer($candleDataB);
         $trend = $analyzer->analyzeTrend();
		 $pattern = $analyzer->analyzeCandlestickPatterns();
		 $predict = $analyzer->predictNextCandle();

		 $st = JSON_ENCODE($pattern[$lastIndex], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
		 $st = JSON_ENCODE($trend, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
		 //$st = JSON_ENCODE($predict, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
		 echo '<pre>';
		 echo $st ;
		 echo '</pre>';

		 return ;
		 
		 
     


  
	      
	     /* สร้าง  Object ตอบกลับเป็น  Json */
		 $myObj = new stdClass();
	     $myObj->DeepSeek = $analyzer ;

  
	     $myJSON = json_encode($myObj, JSON_UNESCAPED_UNICODE);
	     //echo $myJSON;
  
  
	     
  
} // end function

function getCandleData2() {

 $newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/deriv/newDerivObject/';
 $sFileName =  $newUtilPath.'rawData.json';
 $st = '';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file); 
 $candleDataA = JSON_DECODE($st,true);

 
 echo 'Len=' . count($candleDataA) . '<br>';
 return $candleDataA ;

} // end function

function filterCandle($candleData,$timeWant) {

	     for ($i=0;$i<=count($candleData)-1;$i++) {
			 if (date('H:i:s',$candleData[$i]['time'])=== $timeWant ) {
				 echo 'Found At ' . $i . ' =' . date('H:i:s',$candleData[$i]['time']) ;
				 $candleDataB = array_slice($candleData,0,$i);
				 return $candleDataB ;
			 }
	     } 
		 echo "Not Found <br>";

} // end function


?>