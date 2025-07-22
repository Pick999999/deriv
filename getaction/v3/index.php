<?php
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *'); 
ob_start();

   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);   
   $data = json_decode(file_get_contents('php://input'), true);
   if ($data) {      
      if ($data['Mode'] == 'getAction') { getActionV3($data); }
	  if ($data['Mode'] == 'getLastAction') { getActionV3($data); }
      return;
   } else {
	  echo 'No Data In GET Action V3';
      // getActionV3($data='');
   }

function getActionV3($data) {

$newUtilPath = '';
require_once( '../../iqlab/sortGetAction.php');
if ($data=='') {
  $candleData = getCandleData2() ;
} else {
  $candleData = $data['candles'] ;
}

require_once( '../../api/phpCandlestickIndy.php');
$clsStep1 = new TechnicalIndicators();   

require_once('../../api/phpAdvanceIndy.php');
$clsStep2 = new AdvancedIndicators();   
$result = $clsStep1->calculateIndicators($candleData);
$result2= $clsStep2->calculateAdvancedIndicators($result);
//$result2= Final_AdvanceIndy($result2)  ;

$stAnaly = JSON_ENCODE($result2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
/*
$myfile = fopen("deriv/newDerivObject/AnalyDataBig.json", "w") or die("Unable to open file!");
fwrite($myfile, $stAnaly);
fclose($myfile); 
*/
$macdThershold = 0.1 ; $lastMacdHeight = 0 ;
$sAr = array(); $winCon = 0 ; $lossCon = 0 ; $LotNo = 0 ;
$balance=0 ; $maxBalance= 0 ; $maxLossCon = 0 ;
$rowMaxLossCon = 0 ;
$MoneyTrade = array(1,2,4,8,16,54,162,160,320,640,1000,2500,6000,8000,4) ;
//$MoneyTrade = array(1,2,6,6,6,6,6,6,6,6,6,6,6,6,6) ;
$LossConAr = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$lotNoAr = array();
$stLossCon5 = '';

for ($i=0;$i<=count($result2)-1;$i++) {
  $sObj = new stdClass();
  $sObj->No   = $i+1 ;
  $sObj->candleID   = $result2[$i]["candleID"] ;

  $sObj->timefrom_unix   = $result2[$i]["timefrom_unix"] ;
  $sObj->thisColor = $result2[$i]["thisColor"] ;
  $sObj->PreviousTurnType =  $result2[$i]["PreviousTurnType"];
  $sObj->TurnMode999 =  $result2[$i]["TurnMode999"];
  $AnalyObj = $result2[$i] ;
  
  list($thisAction,$forecastColor,$actionReason)=
  getActionFromIDVerObject_Sorted($AnalyObj,$macdThershold,$lastMacdHeight) ;
  $sObj->thisAction = $thisAction ;
  $sObj->actionReason = $actionReason ;

  $fcColor = ($thisAction == 'CALL') ? 'Green' : 'Red';
  if ($i+1 < count($result2)-1) {  
    $resultColor = $result2[$i+1]['thisColor'] ;
  } else {
    $resultColor = '???' ;
  }

  $thisMoneyTrade = $MoneyTrade[$lossCon];  
  $winStatus = ($fcColor == $resultColor) ? 'Win' : '-';
  if ($thisAction !== 'Idle') {    
	  if ( $winStatus== 'Win' ) {
		  $LossConAr[$lossCon]++ ;
		  $LotNo++ ;
		  if ($lossCon ===5) {
			  $stLossCon5 .= $result2[$i]['timefrom_unix'] .';';
		  }		  
	  }
	  if ($winStatus === 'Win') { $profit=  $thisMoneyTrade *0.94 ;$winCon++  ; $lossCon = 0 ;}
	  if ($winStatus === '-')   { $profit=  $thisMoneyTrade *-1 ;$lossCon++ ; $winCon = 0 ; }

	  $balance = $balance + $profit ;
	  if ($balance > $maxBalance) {
		  $maxBalance = $balance;
	  } 

	  if ($lossCon > $maxLossCon) {
		 $maxLossCon = $lossCon ;
		 $timeMaxLossCon = $AnalyObj['timefrom_unix'] ;
		 $rowMaxLossCon = $i ;
	  }
  } else {
    $fcColor = 'Idle';
  }
  
  
  $sObj->forecastColor = $fcColor;
  $sObj->resultColor = $resultColor;
  $sObj->winStatus = $winStatus;
  $sObj->winCon =   $winCon;
  $sObj->lossCon =  $lossCon;
  $sObj->lotNo   =   $LotNo;
  $sObj->thisMoneyTrade   = $thisMoneyTrade ;
  $sObj->profit   = $profit ;
  $sObj->balance   = $balance ;


  $sAr[] = $sObj ;
   
} // end for 

$lastIndex = count($sAr)- 1 ;
$s = json_encode($sAr[$lastIndex],JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//echo '<pre>' . $s . '</pre>';
echo  $s;
  

} // end func 

function getCandleData2() {

 
 $sFileName =  'rawData.json';
 $st = '';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file); 
 $candleDataA = JSON_DECODE($st,true);

 
 //echo 'Len=' . count($candleDataA) . '<br>';
 return $candleDataA ;

} // end function



?>

