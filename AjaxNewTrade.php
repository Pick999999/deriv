<?php

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
      if ($data['Mode'] == 'getsignalWithCutRisk') { getsignalWithCutRisk($data); }
	  if ($data['Mode'] == 'saveTradeList') { saveTradeList($data); }

	  
      return;
   }

// https://lovetoshopmall.com/deriv/AjaxNewTrade.php
function getsignalWithCutRisk($data) { 
/*
ใช้ 3 Class
phpIndy,phpAdvanceIndy,TradingSignalCheckerV3.php
*/

$candleDataString = JSON_ENCODE($data['candles']) ;
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

  
  
  $historicalData = getAnalysisDataForCutRisk($result2)   ;
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

  echo JSON_ENCODE($s, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);


  



  //echo '->' . $result2[$lastIndex]['close'] ;
  
  $myfile = fopen("newfileTest.json", "w") or die("Unable to open file!");
  $txt = JSON_ENCODE($result2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  fwrite($myfile, $txt);
  fclose($myfile);

  $sFileName = 'dataTest/realTrade_' .$result2[$lastIndex]['timefrom_unix'] . '.json'  ;
  $myfile = fopen($sFileName, "w") or die("Unable to open file!");
  $txt = JSON_ENCODE($s, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  fwrite($myfile, $txt);
  fclose($myfile);




   
	     

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

function getAnalysisDataForCutRisk($candleWithAnalysis) {


 $historicalDataA = $candleWithAnalysis ;
 $historicalData = array();
 for ($i=0;$i<=count($historicalDataA)-2;$i++) {
	 //$sObj = new stdClass();
	 $sObj = array();
     
	 $sObj['time'] = intval($historicalDataA[$i]['timestamp']);
	 $sObj['timefrom_unix'] = date('H:i:s',intval($historicalDataA[$i]['timestamp']));
	 $sObj['ema3'] =  $historicalDataA[$i]['ema3'];
	 $sObj['ema5'] =  $historicalDataA[$i]['ema5'];
	 
	 
	 $sObj['open'] = $historicalDataA[$i]['open'];
	 $sObj['high'] = $historicalDataA[$i]['high'];
	 $sObj['low'] = $historicalDataA[$i]['low'];
	 $sObj['close'] = $historicalDataA[$i]['close'];
	 
	 $historicalData[] = $sObj ;
 }

 $myfile = fopen("rawDataVerTrade.json", "w") or die("Unable to open file!");
 $txt = JSON_ENCODE($historicalData) ;
 fwrite($myfile, $txt);
 fclose($myfile);
 
 return $historicalData;

} // end function


function saveTradeList($dataA) { 

    require_once("newutil2.php"); 
    $data = $dataA['tradeData'] ;
	//print_r($data);
	$sql='INSERT INTO subTrade(assetname, contract_id, sCode,contract_type, purchaseTime, profit, jsonDetail,analyzer) VALUES (?,?,?,?,?,?,?,?)';
	
	$pdo = getPDONew()  ;
	//$pdo->exec("set names utf8mb4") ;
	


	$params = array(
	  $data['asset'],
      $data['contract_id'],
      $data['sCode'],
      $data['contract_type'],
      date('Y-m-d H:i:s',$data['purchaseTime']) ,
      $data['profit'],
      JSON_ENCODE($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)  ,
      JSON_ENCODE($dataA['analyzer'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)  
  	  );
		
	 
	//print_r($params);
	
	if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
      echo 'Error' ;
      return false;
   }
	
	

} // end function
