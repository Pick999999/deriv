<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

 $candleDataA = getCandleData2();
 //$clsTradeList = getSuggestByClsTradeV2($candleDataA) ;
 //printJSON($clsTradeList) ;
 
 
  

 $deepSeekAnalyzed =  getSuggestByDeepSeek($candleDataA) ;
 
 $claudAnalysis = getSuggestByClaude($candleDataA) ;
 //printJSON($claudAnalysis); return ;
 $chatGPTAnalysis =  getSuggestByCHATGPT($candleDataA)  ;
 $signalWithCutRisk = getsignalWithCutRisk($candleDataA) ;

 

 $AllAnalysis = new stdClass();
 $AllAnalysis->deepSeekAnalyzed = $deepSeekAnalyzed ; 
 $AllAnalysis->claudAnalysis = $claudAnalysis ; 
 $AllAnalysis->chatGPTAnalysis = $chatGPTAnalysis ; 
 $AllAnalysis->signalWithCutRisk = $signalWithCutRisk ; 
 $AllAnalysis->signalByClassTrade = $clsTradeList ; 

 

 for ($i=0;$i<=count($clsTradeList)-1;$i++) {
	$deepSeekObject = findObject($deepSeekAnalyzed,$clsTradeList[$i]->timestamp) ;
	$clsTradeList[$i]->deepSeekObject = $deepSeekObject ;
    

 }
 printJSON($clsTradeList) ;
 




 // ******************** 
function getSuggestByDeepSeek($candleData) {

$newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/';
require_once($newUtilPath ."deriv/CandlestickAnalyzer_DeepSeek.php"); 


$analyzer = new AdvancedCandlestickAnalyzer($candleData);


// 1. ตัวชี้วัด
	$indicators = $analyzer->getIndicators();
	// 2. วิเคราะห์ลักษณะแท่งเทียน
	$patterns = $analyzer->analyzeCandlestickPatterns();
	// 3. วิเคราะห์แนวโน้ม
    $trend = $analyzer->analyzeTrend();
    // 4. ทำนายแท่งถัดไป
    $prediction = $analyzer->predictNextCandle();
    // 5. แนะนำ Indicator เพิ่มเติม
    $suggestions = $analyzer->suggestAdditionalIndicators();

    //print_r($prediction); return;
    $analyzed = new stdClass();
	//$analyzed->indicators = $indicators;
	//$analyzed->patterns = $patterns;
	$analyzed->trend = $trend;
	$analyzed->prediction = $prediction;
	$analyzed->suggestions = $suggestions;




	


$greenPercent = $prediction['green'] ;
$RedPercent = $prediction['red'] ;
$suggestColor =   ($greenPercent > $RedPercent) ? 'Green' : 'Red';

echo '<hr><hr><h2> By Deep Seek </h2>';
$lastIndex = count($candleData)-1 ;
$prettyJson = json_encode($analyzed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo "<pre>"; // ใช้แท็ก <pre> เพื่อรักษาการจัดรูปแบบ
echo $prettyJson;
echo "</pre>";
/*
echo "Green=" . $greenPercent . " Red=" . $RedPercent  . '<br>';
$suggestColor =   ($greenPercent > $RedPercent) ? 'Green' : 'Red';
echo 'Suggest Color= ' . $suggestColor;
*/
echo '<br>**********  end DeepSeek ***********<hr>';



return $analyzed;


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
$timeCandle = $prediction['green'] ;
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
echo '<hr><hr><h2> By Claude </h2>';
$claudAnalysis = $completeAnalysis  ;
$lastIndex = count($claudAnalysis['candlePatterns'])-2 ; 
$subAnalysis = new stdClass() ;
$subAnalysis->candlePattern = $claudAnalysis['candlePatterns'][$lastIndex] ; 
$subAnalysis->trend = $claudAnalysis['trend'] ; 
$subAnalysis->prediction = $claudAnalysis['prediction'] ; 
$subAnalysis->recommendedIndicators = $claudAnalysis['recommendedIndicators'] ; 
//return ;

$prettyJson = json_encode($subAnalysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo "<pre>"; // ใช้แท็ก <pre> เพื่อรักษาการจัดรูปแบบ
echo $prettyJson;
echo "</pre>";
 


return $completeAnalysis;


} // end function

function getSuggestByCHATGPT($candleData) {

$newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/';
require_once($newUtilPath ."deriv/candleAnalyzerChatGPT.php"); 

$tradeAnalyzer = new TradeAnalyzer($candleData);
//print_r($tradeAnalyzer->getIndicators());
$prediction = $tradeAnalyzer->predictNextCandle();
print_r($prediction) ;
//$predictionV2 = $tradeAnalyzer->predictNextCandleV2();
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


function getsignalWithCutRisk($data) { 
/*
ใช้ 3 Class
phpIndy,phpAdvanceIndy,TradingSignalCheckerV3.php
*/

$candleDataString = JSON_ENCODE($data) ;
$candleDataString = str_replace('epoch','time',$candleDataString);
$candleData  = JSON_DECODE($candleDataString,true) ;

          require_once('api/phpCandlestickIndy.php');
          $clsStep1 = new TechnicalIndicators();   

          require_once('api/phpAdvanceIndy.php');
          $clsStep2 = new AdvancedIndicators();   
		  $lastIndex = count($candleData)-1 ;
		  $result = $clsStep1->calculateIndicators($candleData);
          $result2= $clsStep2->calculateAdvancedIndicators($result);

		  $result2 = Final_AdvanceIndy($result2) ;
		  $lastIndex = count($result2)-1;
		  $turnMode999 = $result2[$lastIndex]['TurnMode999'];
		  //echo $turnMode999 . '-->' ;
          
		  $prettyJson = json_encode($result2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		  $myfile = fopen("AdvanceIndy.json", "w") or die("Unable to open file!");		 
		  fwrite($myfile, $prettyJson);
		  fclose($myfile);


		  

  
  
  //$historicalData = getAnalysisDataForCutRisk($result2)   ;
  $historicalData = $candleData ;
///home/thepaper/domains/thepapers.in/private_html/deriv/AjaxNewTrade.php
  require_once('newDerivObject/TradingConditionAnalyzer.php');  
  $analyzer = new TradingConditionAnalyzer($historicalData);

  // Analyze most recent candle
  $result = $analyzer->analyzeTradingConditions(null, $tradeDirection = 'long');
  //echo $result['totalWarnings'] ; 

  $lastIndex= count($result2) - 1;
  $s = new stdClass();
  $s->Mode = 'RealTrade';
  $s->candleId = $result2[$lastIndex]['candleID'];
  $s->timestamp = $result2[$lastIndex]['timestamp'];
  $s->timefrom_unix = $result2[$lastIndex]['timefrom_unix'];
  $s->open = $result2[$lastIndex]['open'];
  $s->close = $result2[$lastIndex]['close'];
  $s->thisColor = $result2[$lastIndex]['thisColor'];
  $s->ema3 = $result2[$lastIndex]['ema3'];
  $s->ema5 = $result2[$lastIndex]['ema5'];
  $s->TurnMode999 = $result2[$lastIndex]['TurnMode999'];
  $s->Analyzer = $result;
  $s->CodeWarning = $result['AllWCode']  ;
  $s->totalWarning = $result['totalWarnings']  ;

  //echo JSON_ENCODE($s, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  $prettyJson = json_encode($s, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

  echo "<pre>"; // ใช้แท็ก <pre> เพื่อรักษาการจัดรูปแบบ
  echo $prettyJson;
  echo "</pre>"; 

  return $s ;


  


 



   
	     

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

function Final_AdvanceIndy($result2) { 

          $lastTurnID = 0;  
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

function getSuggestByClsTradeV2($data) { 

 require_once("newutil2.php"); 
require_once("../iqlab/clsTradeVer0/clsTradeVer_Object.php"); 
$clsTrade = new clsTrade();

 $st = "";   
 
 
 $sFileName = '../deriv/AdvanceIndy.json';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file);
 $sObj = json_decode($st,true) ;
 echo 'Total Record =' . count($sObj)  . '<hr>';


 $thisIndex = count($sObj)- 9 ;
 $lossCon = 0 ; $maxLossCon = 0 ;
 $clsTradeList = array();
 for ($i=1;$i<=count($sObj)-1;$i++) {
     $thisIndex = $i ;
	 echo 'ThisIndex = ' . $thisIndex .'<br>';

	 $AnalyObj = $sObj[$thisIndex] ;
	// list($action,$actionCode) = $clsTrade->getActionClassV2FromlabVer2($AnalyObj) ; 
	//echo "Action=" . $action ;

	 require_once('../iqlab/clsTradeVer0/getActionFromIDVerObject.php');
	 list($thisAction,$actionReason) = getActionFromIDVerObject($AnalyObj ,$macdThershold=0.05,$lastMacdHeight=0);

	 echo "Action=" . $thisAction . ' ; Action Code=' . $actionReason;
	 $nextColor = getResultColor($sObj,$thisIndex)  ;
	 echo "<br>Result Color =" . $nextColor ;
	 if ($thisAction === 'CALL' ) {
		 if ($nextColor === 'Green') {
		 	 $winStatus = 'Win'; $lossCon = 0 ;
		 }
		 if ($nextColor === 'Red') {
			 $winStatus = 'Loss'; $lossCon++ ;
		 }
	 }
	 if ($thisAction === 'PUT' ) {
		 if ($nextColor === 'Green') {
			 $winStatus = 'Loss'; $lossCon++ ;
		 }
		 if ($nextColor === 'Red') {
			 $winStatus = 'Win'; $lossCon = 0 ;
		 }
	 }

     if ($lossCon > $maxLossCon) { $maxLossCon = $lossCon ; }

     
	 echo "<h2>". $AnalyObj["timefrom_unix"] . ' = '. $winStatus . ' :: LossCon = '. $lossCon .  '</h2><hr>';
	 $sObj2 = new stdClass();
	 $sObj2->timestamp = $AnalyObj["timestamp"] ;
     $sObj2->timefromunix = $AnalyObj["timefrom_unix"] ;
	 $sObj2->thisColor  = $AnalyObj["thisColor"] ;
	 $sObj2->action  = $thisAction ;
	 $sObj2->nextColor  = $nextColor ;
	 $sObj2->winStatus  = $winStatus ;
	 $sObj2->lossCon    = $lossCon ;
     $sObj2->maxlossCon = $maxLossCon ;
	 $clsTradeList[] = $sObj2 ;






 } 
 echo '<h2>Max Loss Con =' . $maxLossCon . '</h2>' ;
 return $clsTradeList ;

} // end function

function printJSON($jsonObject) { 

  $prettyJson = json_encode($jsonObject, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  echo "<pre>"; // ใช้แท็ก <pre> เพื่อรักษาการจัดรูปแบบ
  echo $prettyJson;
  echo "</pre>"; 

} // end function

function findObject($thisObject,$thisTimeStamp) { 

         for ($i=0;$i<=count($thisObject)-1;$i++) {
			 if ($thisObject[$i]['timestamp']=== $thisTimeStamp ) {
				 return $thisObject[$i] ;
			 }
         } 
		 return null ;



} // end function


?>