<?php
header('Access-Control-Allow-Methods: GET, POST');
//header('Access-Control-Allow-Origin: *'); 
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);  

require_once('../iqlab/sortGetAction.php') ;

$data = json_decode(file_get_contents('php://input'), true);
if ($data) {
   Main($data);
   return;
}

?>
<style>
 td { padding:5px}
</style>

<?php
$candleDataA = getCandleData2();
$lastIndex = count($candleDataA)-1 ;

$testIndex = 12 ; //$lastIndex = $testIndex-1 ;
$st='<table border=1>
<tr>
	<th>ลำดับ</th>
	<th>Time</th>
	<th>Claude</th>
	<th>DeepSeek</th>
	<th>ChatGPT4</th>
	<th>Pick</th>
    <th>TIMEPK</th>
	<th>Result</th>
	<th>Cl</th>
	<th>DS</th>
	<th>C4</th>
	<th>PK</th>
	<th>T-Cl</th>
	<th>T-DS</th>
	<th>T-C4</th>
	<th>T-PK</th>
	<th>เทรดตานี้ </th>
	<th>เทรด ครั้งที่ </th>
	<th>Total Loss</th>
	<th>Total Win</th>


</tr>';

$win1 = 0 ; $win2= 0 ; $win3 = 0 ; $win4 = 0 ;
$Tradeno = 0 ; $TotalLoss = 0 ; $TotalWin = 0 ; 
$numwinInThisTrade = 0 ; $sWant = 0 ;
$testIndex = 250 ; $lastIndex = $testIndex + 50  ;
for ($i=$testIndex;$i<= $lastIndex-1;$i++) {   

  $candleData =  array_slice($candleDataA, 0,$i);
  $timeTrade = date('Y-m-d H:i:s',$candleDataA[$i]['time']) ;
//  $timeTrade = date('H:i',$candleDataA[$i]['time']) ;

  $close = $candleDataA[$i+1]['close'] ;
  $open = $candleDataA[$i+1]['open'] ;
  if ($open > $close ) {
	  $nextColor = 'Red';
  } else {
      $nextColor = 'Green';
  }
  

  $thisColor = getColor($candleDataA,$i)  ;
  $nextColor = getColor($candleDataA,$i+1)  ;
  
  $suggestColor1 = getSuggestByClaude($candleData);  
  $suggestColor2 =  getSuggestByDeepSeek($candleData);
  $suggestColor3 =  getSuggestByChatGPT($candleData);  

  if ($suggestColor1 === $suggestColor2 && $suggestColor2==$suggestColor3) {
	 // $suggestColor4 = $suggestColor1 ;
  } else {
    list($suggestColor4,$timeClsTrade,$clsTradeColor,$actionReason,$CaseNo) =  getSuggestByClassTrade($candleData);  
  }
  //list($suggestColor4,$timeClsTrade,$clsTradeColor) =  getSuggestByClassTrade($candleData);  
  

  
  
  $numwinInThisTrade = 0 ;
  $success1 = ($suggestColor1 == $nextColor) ? 'Y' : '';
  $success2 = ($suggestColor2 == $nextColor) ? 'Y' : '';
  $success3 = ($suggestColor3 == $nextColor) ? 'Y' : '';
  $success4 = ($suggestColor4 == $nextColor) ? 'Y' : '';
  
  if ($success1 === 'Y') { $win1++ ; $numwinInThisTrade++ ;}
  if ($success2 === 'Y') { $win2++ ; $numwinInThisTrade++ ;}
  if ($success3 === 'Y') { $win3++ ; $numwinInThisTrade++ ;}
  if ($success4 === 'Y') { $win4++ ; $numwinInThisTrade ++;}

  if ($numwinInThisTrade >= 3 ) {
	  $sWant++ ; 
  }

  if (
	  $suggestColor1 === $suggestColor2 && 
	  $suggestColor2 === $suggestColor3 && 
	  $suggestColor3===$suggestColor4
  ) {
	  $TradeOnThis = 'Y'; $Tradeno++ ;
  } else  {
	   $TradeOnThis = '-';
  }

   if (
	  $suggestColor1 === $suggestColor2 && 
	  $suggestColor2 === $suggestColor3 && 
	  $suggestColor3===$suggestColor4
  ) {

	  if (
		  $success1 === 'Y' && 
		  $success2 === 'Y' && 
		  $success3=== 'Y' && $success4 ==='Y'
	  ) {
		  $TotalWin++ ;
	  } else  {
		  $TotalLoss++ ;
	  }
  }


  


	  
  $lineno = ($i-$testIndex+1) ;	   
  $st .='<tr id="tr_' . $lineno . '" onclick="setBGRow(this.id)">';
  $st .=
  "
	<td>$lineno</td>
    <td>$timeTrade</td>
	<td>$suggestColor1</td>
	<td>$suggestColor2</td>
	<td>$suggestColor3</td>
	<td>$suggestColor4</td>
	<td>$timeClsTrade->$thisColor</td>
	<td style='background:#ff80ff'>$nextColor</td>
	<td>$success1</td>
	<td>$success2</td>
	<td>$success3</td>
	<td>$success4</td>
	<td>$win1</td>
	<td>$win2</td>
	<td>$win3</td>
	<td>$win4</td>
	<td>$TradeOnThis</td>
	<td>$actionReason</td>
	<td>$CaseNo</td>
	 



</tr>";



  //echo '---------------> <h2>Next Color = ' . $nextColor . '</h2>';
}
$st .='</table>'; 
echo $st ;


function getColor($candleDataA,$thisIndex) {

  $close = $candleDataA[$thisIndex]['close'] ;
  $open = $candleDataA[$thisIndex]['open'] ;
  if ($open > $close ) {
	  $Color = 'Red';
  } else {
      $Color = 'Green';
  }
  if ($open === $close ) {
	  $Color = 'Gray';
  }

  return $Color ;


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


function getSuggestByClaude($candleData) {

$newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/';
require_once($newUtilPath ."deriv/candleAnalyzerClaude.php"); 


$analyzer = new CandlestickAnalyzerClaude($candleData);

// Get all analyses
$completeAnalysis = $analyzer->getCompleteAnalysis(); 
$prediction = $analyzer->getNextCandlePrediction();
//print_r($prediction);
//$recommendedIndicators = $analyzer->getRecommendedIndicators();
$greenPercent = $prediction['green'] ;
$RedPercent = $prediction['red'] ;
$suggestColor =   ($greenPercent > $RedPercent) ? 'Green' : 'Red';

/*
echo '<hr><hr><h2> By Claude </h2>';
echo "Green=" . $greenPercent . " Red=" . $RedPercent  . '<br>';
$suggestColor =   ($greenPercent > $RedPercent) ? 'Green' : 'Red';
echo 'Suggest Color= ' . $suggestColor;
echo '<br>**********  end claude ***********<br>';
*/

return $suggestColor;


} // end function


function getSuggestByDeepSeek($candleData) {

$newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/';
require_once($newUtilPath ."deriv/CandlestickAnalyzer_DeepSeek.php"); 


$analyzer = new AdvancedCandlestickAnalyzer($candleData);


// 1. ตัวชี้วัด
	//$indicators = $analyzer->getIndicators();
	// 2. วิเคราะห์ลักษณะแท่งเทียน
	//$patterns = $analyzer->analyzeCandlestickPatterns();
	// 3. วิเคราะห์แนวโน้ม
    //$trend = $analyzer->analyzeTrend();
    // 4. ทำนายแท่งถัดไป
    $prediction = $analyzer->predictNextCandle();
    // 5. แนะนำ Indicator เพิ่มเติม
    //$suggestions = $analyzer->suggestAdditionalIndicators();

    //print_r($prediction);


$greenPercent = $prediction['green'] ;
$RedPercent = $prediction['red'] ;
$suggestColor =   ($greenPercent > $RedPercent) ? 'Green' : 'Red';
/*
echo '<hr><hr><h2> By Deep Seek </h2>';

echo "Green=" . $greenPercent . " Red=" . $RedPercent  . '<br>';
$suggestColor =   ($greenPercent > $RedPercent) ? 'Green' : 'Red';
echo 'Suggest Color= ' . $suggestColor;
echo '<br>**********  end DeepSeek ***********<hr>';
*/


return $suggestColor;


} // end function


function getSuggestByCHATGPT($candleData) {

$newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/';
require_once($newUtilPath ."deriv/candleAnalyzerChatGPT.php"); 

$tradeAnalyzer = new TradeAnalyzer($candleData);

//print_r($tradeAnalyzer->getIndicators());
$prediction = $tradeAnalyzer->predictNextCandle();
if ($prediction['green'] > $prediction['red']) {
	$suggestColor = 'Green';
} else {
	$suggestColor = 'Red';
}
return $suggestColor;
/*
echo '<h2> By CHATGPT </h2>';
echo "Probability of Green: " . $prediction['green'] . "<br>";
echo "Probability of Red: " . $prediction['red'] . "<br>";
*/

} // end function


function getSuggestByClassTrade($candleData) { 

require_once('api/phpCandlestickIndy.php');
$clsStep1 = new TechnicalIndicators();   

require_once('api/phpAdvanceIndy.php');
$clsStep2 = new AdvancedIndicators();   
$result = $clsStep1->calculateIndicators($candleData);
$result2= $clsStep2->calculateAdvancedIndicators($result);
$result2= Final_AdvanceIndy($result2)  ;



$stAnaly = JSON_ENCODE($result2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
$myfile = fopen("newDerivObject/AnalyData2.json", "w") or die("Unable to open file!");
fwrite($myfile, $stAnaly);
fclose($myfile); 

$sAr = array();
for ($i=0;$i<=count($result2)-1;$i++) {
  $sObj = new stdClass();
  $sObj->candleID   = $result2[$i]["candleID"] ;
  $sObj->timefrom_unix   = $result2[$i]["timefrom_unix"] ;
  //$sObj->thisColor = $result2["$"]

   
}



require_once("newutil2.php"); 
require_once("../iqlab/clsTradeVer0/clsTradeVer_Object.php"); 
$clsTrade = new clsTrade();
require_once('../iqlab/clsTradeVer0/getActionFromIDVerObject.php');


$lastIndex = count($result2) -1 ;
$AnalyObj = $result2[$lastIndex] ;
/*
list($thisAction,$actionReason) = getActionFromIDVerObject($AnalyObj ,$macdThershold=0.5,$lastMacdHeight=0);
*/

list($thisAction,$actionReason,$CaseNo) = getActionFromIDVerObject_Sorted($AnalyObj ,$macdThershold=0.5,$lastMacdHeight=0);


$suggestColor = ($thisAction == 'CALL') ? 'Green' : 'Red';
//echo $AnalyObj['timefrom_unix'] . '@#';
$thisColor = $AnalyObj['thisColor'] ;
return array($suggestColor,$AnalyObj['timefrom_unix'],$thisColor,$actionReason,$CaseNo) ;



} // end function

function Final_AdvanceIndy($result2) { 

          $lastTurnID = 0 ;  
		  for ($i=2;$i<=count($result2)-1;$i++) {
			  $curIndex = $i;
              $previousIndex = $i-1 ;
			  $previousIndexBack2 = $i-2 ;
			  if (
				 $result2[$previousIndex]['ema3'] < $result2[$curIndex]['ema3'] &&
                 $result2[$previousIndex]['ema3'] < $result2[$previousIndexBack2]['ema3'] 
				 ) {
                 $result2[$curIndex]['PreviousTurnType'] = 'TurnUp' ;
				 $result2[$curIndex-1]['TurnType'] = 'TurnUp' ;
				 $lastTurnID = $result2[$curIndex]['candleID'] ;
				 //$result2[$curIndex]['lastTurnID'] = $lastTurnID;
			  }
			  if (
				 $result2[$previousIndex]['ema3'] > $result2[$curIndex]['ema3'] &&
                 $result2[$previousIndex]['ema3'] > $result2[$previousIndexBack2]['ema3'] 
				 ) {
                 $result2[$curIndex]['PreviousTurnType'] = 'TurnDown' ;
				 $result2[$curIndex-1]['TurnType'] = 'TurnDown' ;
				 $lastTurnID = $result2[$curIndex]['candleID'] ;
				 
			  } 
			  $result2[$curIndex]['lastTurnID'] = $lastTurnID;			   
		  }
		  

          //ปรับค่า
		  for ($i=0;$i<=count($result2)-1;$i++) {
                
               $pip = $result2[$i]['open'] - $result2[$i]['close'];
               $pip = number_format($pip , 2) ;
			   $previousColor = null ;$previousColorBack2 = null;
			   $previousColorBack3 = null ;$previousColorBack4 = null; 

			   //$previousTurnType = null ;$previousTurnTypeBack2 = null;
			  // $previousTurnTypeBack3 = null ;$previousTurnTypeBack4 = null; 

			   $macdconverValue = 0.0 ;
			   $MACDConvergence = '';

			   if ($i >= 1) {
				   $previousColor = $result2[$i-1]['thisColor'] ;
				   $previousTurnType = $result2[$i-1]['PreviousTurnType'] ;
				   $macdconverValue = abs($result2[$i]['MACDHeight']) - abs($result2[$i-1]['MACDHeight']);
				   if ($macdconverValue < 0) {
					   $MACDConvergence ='Conver';
				   }
				   if ($macdconverValue > 0) {
					   $MACDConvergence ='Diver';
				   }
				   if ($macdconverValue == 0) {
					   $MACDConvergence ='P';
				   }

			   }
			   if ($i >= 2) {
				   $previousColorBack2 = $result2[$i-2]['thisColor'] ;
				   //$previousTurnTypeBack2 = $result2[$i-1]['PreviousTurnType'] ;
			   }
			   if ($i >= 3) {
				   $previousColorBack3 = $result2[$i-3]['thisColor'] ;
				   //$previousTurnTypeBack3 = $result2[$i-1]['PreviousTurnType'] ;
			   }
			   if ($i >= 4) {
				   $previousColorBack4 = $result2[$i-4]['thisColor'] ;
				   //$previousTurnTypeBack4 = $result2[$i-1]['PreviousTurnType'] ;
			   }
               $result2[$i]['pip'] = $pip ;
			   $result2[$i]['previousColor'] = $previousColor;
			   $result2[$i]['previousColorBack2'] = $previousColorBack2;
			   $result2[$i]['previousColorBack3'] = $previousColorBack3;
			   $result2[$i]['previousColorBack4'] = $previousColorBack4;

			   
			   $result2[$i]['macdconverValue'] = $macdconverValue ; 			   
			   $result2[$i]['MACDConvergence'] = $MACDConvergence ; 

			   $result2[$i]['timefrom_unix'] =  date('H:i',$result2[$i]['timestamp']); 

			   if ($result2[$i]['TurnType'] === 'TurnUp' || 
				   $result2[$i]['TurnType'] === 'TurnDown' ) {
				    $result2[$i]['lastTurnID'] = $result2[$i]['candleID'] ;
			   } else {
				   if ($i-1 > 0) {				   
                     $result2[$i]['lastTurnID'] = $result2[$i-1]['lastTurnID'];
				   }
			   }			  
		  }
		  for ($i=2;$i<=count($result2)-1;$i++) {
			  $result2[$i]['PreviousTurnTypeBack2'] = $result2[$i-1]['PreviousTurnType'] ; 
			  $result2[$i]['PreviousTurnTypeBack3'] = $result2[$i-2]['PreviousTurnType'] ; 
			  if ($i > 2) {
                $result2[$i]['PreviousTurnTypeBack4'] = $result2[$i-3]['PreviousTurnType'] ; 
			  }
		  }
		  for ($i=1;$i<=count($result2)-1;$i++) {
			  $distance= ($result2[$i]['candleID'] - $result2[$i]['lastTurnID'])/60 ; 
			  $result2[$i]['distance'] = $distance ; 
		  } 

		  for ($i=2;$i<=count($result2)-1;$i++) {
			  $candleCode = $result2[$i]['emaAbove'].'-' . $result2[$i]['thisColor'].'-'.
              $result2[$i]['emaConflict'].'-' . $result2[$i]['MACDConvergence'].'-' ;
			  $candleCode .= 'dis'.$result2[$i]['distance'].'-' ;
			  $candleCode .= 'cut'.$result2[$i]['CutPointType'].'-' ;
			  //$candleCode .= $result2[$i]['candleWick']['candleType'].'-' ;
			  $result2[$i]['CandleCode'] = $candleCode;
		  } 

		 for ($i=1;$i<=count($result2)-1;$i++) {
			$previousADX = floatval($result2[$i-1]['adx']) ;
			$ADX = floatval($result2[$i]['adx']) ;
			if ($ADX > $previousADX) {
			   $result2[$i]['adxDirection'] = 'Up';
			} else {
			   $result2[$i]['adxDirection'] = 'Down';
			}	
			
			
		  }

		  for ($i=1;$i<=count($result2)-1;$i++) {
			 
               if ($result2[$i]['PreviousTurnType'] ==='' || $i==1) {				  
			   } 
			   $result2[$i]['PreviousSlopeDirection'] = $result2[$i-1]['ema3slopeDirection'];
             
			   if ($result2[$i]['PreviousTurnType'] ==='TurnUp') {
			      $result2[$i]['TurnMode999'] = 'TurnUp';
			   }
			   if ($result2[$i]['PreviousTurnType'] ==='TurnDown') {
			      $result2[$i]['TurnMode999'] = 'TurnDown';
			   }
			   if ($result2[$i]['PreviousTurnType'] ==='N') {
			      $result2[$i]['TurnMode999'] = $result2[$i-1]['TurnMode999']  ;
			   } 
               
			   
			   if ($result2[$i]['TurnMode999'] ==='' || $result2[$i]['TurnMode999'] ==='I') {				  
			      if ($result2[$i]['emaAbove'] ==='3') {				  
				    $result2[$i]['TurnMode999'] = 'TurnUp';
				  } else {
                    $result2[$i]['TurnMode999'] = 'TurnDown';
				  }
			   }
		  } 

		  return $result2;


} // end function 
?>
<input type="text" id="lastRowSelected" value="tr_1">
<style>
 tr { cursor:pointer; }
 .markRowSelected { background:#80ff80 ; }
</style>
<link href="" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

<script>
function setBGRow(thisid) {

//	alert(thisid);
	lastRowSelected = document.getElementById("lastRowSelected").value ;

	$("#"+ lastRowSelected).removeClass('markRowSelected');
	$("#"+ thisid).addClass('markRowSelected');
	document.getElementById("lastRowSelected").value  = thisid;



}
</script>

<?php

function Main($data) { 


	$candleDataA = $data['data'] ;
    $candleDataA = getCandleData2();
	
	$win1 = 0 ; $win2= 0 ; $win3 = 0 ; $win4 = 0 ;
	$Tradeno = 0 ; $TotalLoss = 0 ; $TotalWin = 0 ; 
	$numwinInThisTrade = 0 ; $sWant = 0 ;
	
	$testIndex = 0 ; $lastIndex = count($candleDataA)-1 ;
	
	$tradeList[] = '';
	for ($i= 10 ;$i<= 60;$i++) {   
	  
	  $candleData =  $candleDataA[$i] ;
	  $candleData =  array_slice($candleDataA, 0,$i);
	  $timeTrade = date('Y-m-d H:i:s',$candleDataA[$i]['time']) ;
	//  $timeTrade = date('H:i',$candleDataA[$i]['time']) ;

	  $sObj = new stdClass();
	  $sObj->timestamp = $candleDataA[$i]['time'] ;
	  $sObj->timeTrade = $timeTrade ;
	  


	  $close = $candleDataA[$i+1]['close'] ;
	  $open = $candleDataA[$i+1]['open'] ;
	  if ($open > $close ) {
		  $nextColor = 'Red';
	  } else {
		  $nextColor = 'Green';
	  }
	  
	  

	  $thisColor = getColor($candleDataA,$i)  ;
	  $nextColor = getColor($candleDataA,$i+1)  ;
	  $sObj->thisColor = $thisColor ;
	  $sObj->nextColor = $nextColor ;
	  
	  $suggestColor1 = getSuggestByClaude($candleData);  
	  $suggestColor2 =  getSuggestByDeepSeek($candleData);
	  //$suggestColor3 =  getSuggestByChatGPT($candleData);  
	  $suggestColor3 = '-';

	  $sObj->ClaudesuggestColor = $suggestColor1;
	  $sObj->DeepSeeksuggestColor = $suggestColor2;
	  $sObj->ChatGPTsuggestColor = $suggestColor3;

	/*
	  if ($suggestColor1 === $suggestColor2 && $suggestColor2==$suggestColor3) {
		 // $suggestColor4 = $suggestColor1 ;
	  } else {
		list($suggestColor4,$timeClsTrade,$clsTradeColor,$actionReason,$CaseNo) =  getSuggestByClassTrade($candleData);  
	  }
	  */

	  list($suggestColor4,$timeClsTrade,$clsTradeColor,$actionReason,$CaseNo) =  getSuggestByClassTrade($candleData);  

	  $sObj->PicksuggestColor = $suggestColor4;

	  //list($suggestColor4,$timeClsTrade,$clsTradeColor) =  getSuggestByClassTrade($candleData);  
	  

	  
	  
	  $numwinInThisTrade = 0 ;
	  $success1 = ($suggestColor1 == $nextColor) ? 'Y' : '';
	  $success2 = ($suggestColor2 == $nextColor) ? 'Y' : '';
	  $success3 = ($suggestColor3 == $nextColor) ? 'Y' : '';
	  $success4 = ($suggestColor4 == $nextColor) ? 'Y' : '';

	  $sObj->ClaudeSuccess = $success1 ;
	  $sObj->DeepSeekSuccess = $success2 ;
	  $sObj->ChatGPTSuccess = $success3 ;
	  $sObj->PickSuccess = $success4 ;

	  
	  if ($success1 === 'Y') { $win1++ ; $numwinInThisTrade++ ;}
	  if ($success2 === 'Y') { $win2++ ; $numwinInThisTrade++ ;}
	  if ($success3 === 'Y') { $win3++ ; $numwinInThisTrade++ ;}
	  if ($success4 === 'Y') { $win4++ ; $numwinInThisTrade ++;}

	  if ($numwinInThisTrade >= 3 ) {
		  $sWant++ ; 
	  }

	  if (
		  $suggestColor1 === $suggestColor2 && 
		  $suggestColor2 === $suggestColor3 && 
		  $suggestColor3===$suggestColor4
	  ) {
		  $TradeOnThis = 'Y'; $Tradeno++ ;
	  } else  {
		   $TradeOnThis = '-';
	  }

	   if (
		  $suggestColor1 === $suggestColor2 && 
		  $suggestColor2 === $suggestColor3 && 
		  $suggestColor3===$suggestColor4
	  ) {

		  if (
			  $success1 === 'Y' && 
			  $success2 === 'Y' && 
			  $success3=== 'Y' && $success4 ==='Y'
		  ) {
			  $TotalWin++ ;
		  } else  {
			  $TotalLoss++ ;
		  }
	  }

	  $tradeList[] = $sObj;
	 
	} // end for loop 

	 $stAnaly = JSON_ENCODE($tradeList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
	 echo $stAnaly;

} // end function




 
?>