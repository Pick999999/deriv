<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
/*
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *'); 
*/

   $data = json_decode(file_get_contents('php://input'), true);
   
   if ($data) {
      
      require_once("../newutil2.php");	
      if ($data['Mode'] == 'AjaxNewCalProfit' && $data['SubMode']==='') {  AjaxNewCalProfit($data); 
	  }

	  if ($data['Mode'] == 'AjaxNewCalProfit' && $data['SubMode']==='2Point') { AjaxCal2Point($data); }

      return;
   }

function AjaxNewCalProfit($data) { 



//$AnalyList = getWantData($data) ;
$AnalyList = gerAnalyDataFromTxtFile() ;
for ($i=1;$i<=count($AnalyList)-1;$i++) {
	
	//$AnalyList[$i-1]['TurnType'] = $AnalyList[$i]['PreviousTurnType'];
   
} 


//echo count($AnalyList);
$lossCon = 0 ;  $maxLossCon= 0;
$totalWin = 0 ; $totalLoss= 0 ; $balance = 0 ; $startMoney = 1;
$MoneyTrade = $startMoney ;
$timeLoss = array();
$timeString = '';
$loss4Str = '';
$Idle= false ; $numIdle =  0 ; $realTrade = 0 ;
$IdleList = '';
$tradeTimeline = array();
$thisAction = ''; $winStatus = ''; $profit = 0 ; $balance = 0 ;
$maxbalance = 0 ;
$CandleCode1 = ''; $CandleCode2 = '';
//MoneyList= 1,2,4,6,8,12
for ($i=0;$i<=count($AnalyList)-1;$i++) {
  //$Idle = CheckIdleCase($AnalyList,$i);

  $Idle = false ;
  list($actionCode,$SuggestColor,$CaseTurn)= getSuggestColor($AnalyList,$i) ;
  if ($actionCode ==='Idle') {
      $Idle = true ;
  }
  if ($Idle === false) {   
	   $emaAbove = $AnalyList[$i]['emaAbove'] ;
	   if ($i >= 1) {	   
	     list($CandleCode1,$CandleCode2)=getCandleCode($AnalyList,$thisIndex=$i) ;
	   }
	   list($actionCode,$SuggestColor)= getSuggestColor($AnalyList,$thisIndex=$i) ;
	   
	   //echo $SuggestColor . '@#';
	   $resultColor =  getResultColor($AnalyList,$thisIndex=$i) ;
	   if ($lossCon ==0) { $MoneyTrade= 1; }
	   if ($lossCon ==1) { $MoneyTrade= 2; }
	   if ($lossCon ==2) { $MoneyTrade= 6; }
	   if ($lossCon ==3) { $MoneyTrade= 15; }
	   if ($lossCon ==4) { $MoneyTrade= 30; }
	   if ($lossCon ==5) { $MoneyTrade= 54; }
	   if ($lossCon ==6) { $MoneyTrade= 100; }
	   if ($lossCon ==7) { $MoneyTrade= 220; }
	   

	   $profit = ($SuggestColor == $resultColor) ? $MoneyTrade*0.95 : $MoneyTrade*-1 ;
	   $n = ($SuggestColor == $resultColor) ? $totalWin++ : $totalLoss++ ;
	   if ($SuggestColor != $resultColor) {
		   $timeLoss[] = $AnalyList[$i]['timestamp'] ;
		   $winStatus = 'N';
		   $timeString .= $AnalyList[$i]['timefrom_unix'] . '@@' ;
		   $lossCon++ ;
		   if ($lossCon >=4 ) {
			  $loss4Str .=  $AnalyList[$i]['timestamp'] . '##' ;
		   }
	   } else {
		   $winStatus = 'Y';
		   $lossCon = 0 ;
	   }
	   if ($maxLossCon < $lossCon) {
		   $maxLossCon = $lossCon ;
	   }   
	   $balance +=  $profit ; 
	   if ($balance > $maxbalance) {
		   $maxbalance = $balance ;
	   }
	   $realTrade++;
  } else {
	  $numIdle++ ;
	  $SuggestColor  = ''; $resultColor = '';
      $winStatus = 'N'; 
	  $IdleList .= $AnalyList[$i]['timestamp'] . '%$' ;
  }

  if ($SuggestColor =='Green') { $thisAction = 'Call' ;}
  if ($SuggestColor =='Red') { $thisAction = 'Put'; }
  if ($Idle === true) { $actionCode = 'Idle' ;$thisAction = 'Idle'  ; $profit=0;}
     
  
      
  
  $tradeObj  = new stdClass ;
  $tradeObj->timestamp     = $AnalyList[$i]['timestamp'] ;
  $tradeObj->timefromunix     =  date('H:i',$AnalyList[$i]['timestamp']) ;
  //$tradeObj->CandleCode    = $CandleCode1 ;
  $tradeObj->Turn999       = $AnalyList[$i]['TurnMode999'] ;
  $tradeObj->action        = $thisAction  ;
  $tradeObj->ActionCode    = $actionCode  ;
  $tradeObj->CaseTurn      = $CaseTurn  ;
  $tradeObj->SuggestColor  = $SuggestColor  ;
  $tradeObj->resultColor   = $resultColor  ;
  $tradeObj->lossCon = $lossCon;
  $tradeObj->winStatus = $winStatus;
  $tradeObj->MoneyTrade = $MoneyTrade ;
  $tradeObj->profit = $profit ;
  $tradeObj->Balance = round($balance,2) ;
  $tradeTimeline[] =  $tradeObj ;


}
/*
echo "Total Trade= " . count($AnalyList)-1  ;
echo ' Win = ' . $totalWin .  '- Loss= ' . $totalLoss . '---> Balance = ' . $balance ;
echo ' Max Loss Con=' .$maxLossCon;
*/

$sObj = new stdClass();
$sObj->totalIdle = $numIdle ;

$sObj->totalTrade = $realTrade ;
$sObj->totalWin = $totalWin ;
$sObj->totalLoss = $totalLoss ;
$sObj->maxLossCon = $maxLossCon ;
$sObj->Balance = round($balance,2) ;
$sObj->MaxBalance = round($maxbalance,2) ;
$sObj->TimeLoss = implode(';', $timeLoss) ;
$sObj->timeString = $timeString ;
$sObj->loss4Str = $loss4Str;
$sObj->IdleList  = $IdleList ;
$sObj->tradeTimeline = $tradeTimeline;

echo JSON_ENCODE($sObj);



	      
	     

} // end function

function getWantData($data) { 

    return;
	//$AnalyObj = JSON_DECODE($data['AnalyData'],true);
	$AnalyObj = $data['AnalyData'];
	$TmpObjList = array();
	//$startPoint = intval($data['startPoint']) ;
	for ($i=0;$i<=count($AnalyObj)-1;$i++) {
	 	
		if (
		  intval($AnalyObj[$i]['timestamp']) >= intval($data['startPoint']) && 
		  intval($AnalyObj[$i]['timestamp']) <= intval($data['stopPoint'])
		) {
		   $TmpObjList[] =  $AnalyObj[$i];
		}  
	}

	 return $TmpObjList;

} // end function

function getResultColor($AnalyList,$thisIndex) { 

         if ($thisIndex+1 < count($AnalyList)) {         
	        return trim($AnalyList[$thisIndex+1]['thisColor']);
		 } else {
            return 'Wait';
		 }


} // end function

function getSuggestColor($AnalyList,$thisIndex) { 

 $TurnMode = $AnalyList[$thisIndex]['TurnMode999']; 
 $PreviousTurnMode = '';  $PreviousTurnModeBack2 = '';
 $CaseTurn = '';

 if ($thisIndex >= 4) { 
	 /*
   $PreviousTurnMode = $AnalyList[$thisIndex-1]['TurnMode999']; 
   $PreviousTurnModeBack2 = $AnalyList[$thisIndex-2]['TurnMode999']; 
   */
   $CaseTurn = checkCaseTurn($AnalyList,$thisIndex) ;
 }

 if ( $CaseTurn != '' ) {	
    $SuggestColor = '';
    $actionCode = 'Idle';
 } else {
	 $SuggestColor = ($TurnMode === 'TurnUp') ? 'Green' : 'Red';
	 $actionCode = '';
	 if ($TurnMode === 'TurnUp') {
		 $actionCode = '3G';
	 }
	 if ($TurnMode === 'TurnDown') {
		 $actionCode = '5R';
	 } 
 }


 return array($actionCode,$SuggestColor,$CaseTurn) ;




 $emaAbove = $AnalyList[$thisIndex]['emaAbove'];
 // Step1 
 $SuggestColor = ($emaAbove === '3') ? 'Green' : 'Red';
 if ($emaAbove === '3') {
	 $actionCode = '3G';
 }
 if ($emaAbove === '5') {
	 $actionCode = '5R';
 }
 $adxCon = $AnalyList[$thisIndex]['adxDirectionCon'];
 if ($adxCon < 5) {
   $actionCode = 'Idle';
 }
 
 return array($actionCode,$SuggestColor) ;
 /*
  เข้าที่จุด TurnPoint โดยตรวจว่าเป็น จุด turndown,turnup โดยเพิ่ม TurnMode
   - ตรวจสอบว่า จุดติดกัน ไม่เป็น จุด turn ถ้าเป็นก็ Idle
   - ตรวจสอบ  Slope ต้องไม่เป็น Pararell
   - 

    

 */
 


} // end function

function CheckIdleCase($AnalyList,$thisIndex) { 

/*
TheresHold
R_75 : macdHeight = 8 ;

*/
	     
		 $MACDThereshold = 10 ;
         $Idle = false ;
		 
		 if (abs(floatval($AnalyList[$thisIndex]['MACDHeight'])) < $MACDThereshold ) {
		   $Idle = true ;
	     } else {
		   $Idle = false ;
		 }
		 if (abs(floatval($AnalyList[$thisIndex]['ema3SlopeValue'])) < 2 ) {
		  // $Idle = true ;
		 }
		 if ($AnalyList[$thisIndex]['emaConflict'] ==='Y') {
            $Idle = true ;
		 }

		 if ($AnalyList[$thisIndex]['emaAbove'] ==='3' && $AnalyList[$thisIndex]['thisColor']==='Red') {
            $Idle = true ;
		 }
		 if ($AnalyList[$thisIndex]['emaAbove'] ==='5' && $AnalyList[$thisIndex]['thisColor']==='Green') {
            $Idle = true ;
		 }


		 $PreviousTurnType =  $AnalyList[$thisIndex]['PreviousTurnType'] ;
         $PreviousTurnTypeBack2 =$AnalyList[$thisIndex]['PreviousTurnTypeBack2'] ;
		 $PreviousTurnTypeBack3 =$AnalyList[$thisIndex]['PreviousTurnTypeBack3'] ;
		 $PreviousTurnTypeBack4 =$AnalyList[$thisIndex]['PreviousTurnTypeBack4'] ;
		 if ($PreviousTurnType=='TurnDown' && $PreviousTurnTypeBack2=='TurnUp') {
            $Idle = true ;
		 }

         if ($PreviousTurnType=='TurnUp' && $PreviousTurnTypeBack2=='TurnDown') {
            $Idle = true ;
		 }
		 if ($PreviousTurnTypeBack2=='TurnUp' && $PreviousTurnTypeBack3=='TurnDown') {
            $Idle = true ;
		 }



		 
		 return $Idle;


} // end function

function getCandleCode($AnalyList,$thisIndex) { 
$lastTurn = 0 ;
$numCandle = 0 ;
$CandleID = '' ;
	     for ($i=$thisIndex;$i>= 0;$i--) {
			 if ($AnalyList[$i]['PreviousTurnType'] === 'N') {
				 $numCandle++ ;
			 } else {
				 $CandleID = $AnalyList[$i-1]['timefrom_unix'] ;
				 break;
			 }	        
	     } 
		 $thisColor = $AnalyList[$thisIndex]['thisColor'] . '-' . $numCandle;

         //$CandleID = $AnalyList[$i]['candleID'] ;
         return array($CandleID,$thisColor) ;  
		 return array($numCandle,$thisColor) ;


} // end function

function getSuggestColor2($AnalyList,$thisIndex) { 
           
         $action = 'Idle'; $suggestColor = '';
		 $slope=  abs(floatval($AnalyList[$thisIndex]['ema5SlopeValue']));
	     if ( $slope >= 30) {
			 if ( $AnalyList[$thisIndex]['emaAbove'] === '3') {
				 $action = 'CALL';
				 $suggestColor = 'Green';
			 }
			 if ($AnalyList[$thisIndex]['emaAbove'] === '5') {
				 $action = 'PUT';
				 $suggestColor = 'Red';
			 }
	     } 
		 return array($action,$suggestColor) ;

	     


} // end function




function AjaxCal2Point($data){
 

 $startPoint = intval($data['startPoint']) ;
 $stopPoint = intval($data['stopPoint']) ;
 $dataAll = gerAnalyDataFromTxtFile();

 $sLab = array();
 for ($i=0;$i<=count($dataAll)-1;$i++) {
	 //echo $dataAll[$i]['candleID'] . '<br>';
	 if ( 
		 intval($dataAll[$i]['candleID']) >= $startPoint && 
		 intval($dataAll[$i]['candleID']) <= $stopPoint ) {
         $sLab[] = $dataAll[$i] ;
	 }    
 }

 //echo $startPoint . '-' . $stopPoint . ' = '. count($sLab);
 $lastIndex = count($sLab) -1 ;
 //echo  $sLab[0]['timefrom_unix'] ."-" . $sLab[$lastIndex]['timefrom_unix'] ;  
 for ($i=0;$i<=count($sLab)-1;$i++) {
    list($suggestColor,$suggestCode) = getSuggestColorA($sLab,$thisIndex=$i) ;
	if ($suggestColor==='Idle') { $bgColor= 'gray' ; }
	if ($suggestColor==='CALL') { $bgColor= 'blue' ; }
    if ($suggestColor==='PUT') { $bgColor= 'Red' ; }
	echo '<span style="color:' . $bgColor . '">';
	echo $sLab[$i]['timefrom_unix']  . ' -> ' . $suggestColor . ' :: '.$suggestCode .
	'<span style="color:#ff0080;font-size:22px"> | </span>' ;
	echo '<span>';   
    
 }



} // end function

function getSuggestColorA($sLab,$thisIndex) {

 $macdThereshold = 0.5 ;
 $PIPThereshold = 0.5 ;
 $slopeThereshold = 1 ;
 $adxThereshold = 20 ;


 $thisMACD = abs($sLab[$thisIndex]['MACDHeight']) ;
 $thisPIP  = abs($sLab[$thisIndex]['pip']) ;
 $thisSlope  = abs($sLab[$thisIndex]['ema3SlopeValue']) ;
 $thisemaAbove  = $sLab[$thisIndex]['emaAbove'] ;
 $thisADX  =  0 ;



if ($thisIndex >= 1) {

	$previousADX  = $sLab[$thisIndex-1]['adx'] ;
	$thisADX  = $sLab[$thisIndex]['adx'] ;
	if ($thisADX > $previousADX ) {
		$adxSlopeDirection = 'U';
	} else {
		$adxSlopeDirection = 'D';
	}
}



 $suggestCode  =  '' ; $suggestColor =  '';
 if ($thisMACD <= $macdThereshold) {
	 $suggestColor = 'Idle' ;
	 $suggestCode = 'Macd-' ;
 }
 if ($thisPIP <= $PIPThereshold) {
	 $suggestColor = 'Idle' ;
	 $suggestCode .= 'Pip-' ;
 }

 if ($thisSlope <= $slopeThereshold) {
	 $suggestColor = 'Idle' ;
	 $suggestCode .= 'Slope' ;
 } 

 /*if ($thisADX <= $adxThereshold) {
	 $suggestColor = 'Idle' ;
	 $suggestCode .= 'adx' ;
 } 
 */
 

 if ( $suggestColor === 'Idle') {
	 return array($suggestColor,$suggestCode);
 } 

 if ($thisemaAbove ==='3') {
	 $suggestColor = 'CALL' ;
	 $suggestCode .= 'E3' ;
 }

 if ($thisemaAbove ==='5') {
	 $suggestColor = 'PUT' ;
	 $suggestCode .= 'E5' ;
 }

return array($suggestColor,$suggestCode . '-'. $thisADX);








} // end function


function gerAnalyDataFromTxtFile() {

	
	 $st = "";   	 
	 $sFileName = 'dataTest.json';
	 $file = fopen($sFileName,"r");
	 while(! feof($file))  {
	   $st .= fgets($file) ;
	 }
	 fclose($file);
	// echo $st ;

     $sObj =  JSON_DECODE($st,true) ;

	 return $sObj['AnalysisData'] ;
	

} // end function

function checkCaseTurn($AnalyList,$thisIndex) {

   $PB1 = $AnalyList[$thisIndex-1]['TurnMode999']; 
   $PB2 = $AnalyList[$thisIndex-2]['TurnMode999']; 
   $PB3 = $AnalyList[$thisIndex-3]['TurnMode999']; 
   $PB4 = $AnalyList[$thisIndex-4]['TurnMode999']; 

   $TurnMode = $AnalyList[$thisIndex]['TurnMode999']; 
   $PreviousTurnMode = $AnalyList[$thisIndex-1]['TurnMode999']; 
   $PreviousTurnModeBack2  = $PB2 ;
   $pip = abs(floatval($AnalyList[$thisIndex]['pip'])); 
   $slope = abs(floatval($AnalyList[$thisIndex]['ema3SlopeValue'])); 
   $macd = abs(floatval($AnalyList[$thisIndex]['MACDHeight'])); 
   $emaConflict = $AnalyList[$thisIndex]['emaConflict']; 

   $CaseTurn = ''; 
   if ($PB1 != $PB2 && $PB2 != $PB3 && $PB3 != $PB4) {
	   $CaseTurn = 'T1';
   }
   
   if (
       $PreviousTurnMode != '' && 
	   $PreviousTurnMode !== $TurnMode && 
       $PreviousTurnMode !=   $PreviousTurnModeBack2 
   ) {
     $CaseTurn = 'T2';
   }

   if ($macd <= 0.2 ) {
      $CaseTurn = 'M1';
   }
   if ($emaConflict  ==='Y') {
      $CaseTurn = 'E1';
   }
   if ($pip < 0.3) {
      $CaseTurn = 'P1';
   }
   if ($slope < 0.2) {
      $CaseTurn = 'S1';
   }

   return $CaseTurn ;

	     


} // end function


?>
