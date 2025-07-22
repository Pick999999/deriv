<?php
//deriv/newDerivObject/AjaxGetSignal.php
  ob_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  $data = '';



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
        require_once('newutil2.php');
        if ($data['Mode'] == 'getSignal') { getSignal($data); }

		if ($data['Mode'] == 'AjaxgetSuggestSignal2') { getSignal($data); }
		if ($data['Mode'] == 'AjaxgetCalProfit') { getSignal($data); }
		if ($data['Mode'] == 'getNewTradeno') { getNewTradeno($data); }
		if ($data['Mode'] == 'saveTradeList') { saveTradeList($data); }

		if ($data['Mode'] == 'AjaxSaveCalProfit') { AjaxSaveCalProfit($data); }

		
		if ($data['Mode'] == 'getAnalysisData') { getAnalysisData($data); }
		if ($data['Mode'] == 'ScanSignal') { ScanSignal($data); }

		if ($data['Mode'] == 'FindSignal') { FindSignal($data); }


		
		

		
		//AjaxgetSuggestSignal2
        return;
     }
  
function AjaxSaveCalProfit($data) { 





        $pdo=  getPDONew(); 
		 /*
		 "asset" :  document.getElementById("asset").value ,
       "timestampSelected" : document.getElementById("timestampSelect").value,
       "AnalyData" : AnalysisData,
       "startPoint" : document.getElementById("startPoint").value,
       "stopPoint" : document.getElementById("stopPoint").value,
       "totalPoint" : totalPoint,
       "numWin" : numWin ,
	   "numLoss" : numLoss 
		 */

       $sql='REPLACE INTO tradeLab(asset, startTime, endTime, totalPoint, numWin, numLoss, AnalyData,balance) VALUES (?,?,?,?, ?,?,?,?)';
	   $balance = $data['numWin']-$data['numLoss'];

	   $params = array(
       $data['asset'],$data['startPoint'],$data['stopPoint'],
       $data['totalPoint'],$data['numWin'],$data['numLoss'],
       $data['AnalyData'],$balance
	   );

	   
	   
	   if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
         echo 'Error' ;
         return false;
       }
	   
	   
	   


} // end function


  function getSignal($data) {
	  
	      require_once('../api/phpCandlestickIndy.php');
          $clsStep1 = new TechnicalIndicators();   

          require_once('../api/phpAdvanceIndy.php');
          $clsStep2 = new AdvancedIndicators();   
          
	      $candleData = JSON_DECODE($data['candleData'],true) ;
         

		  $lastIndex = count($candleData)-1 ;

		 // echo date('H:i',$candleData[0]['time']);
		  //echo ' ->'.  date('H:i',$candleData[$lastIndex]['time']);

		  $result = $clsStep1->calculateIndicators($candleData);
          $result2= $clsStep2->calculateAdvancedIndicators($result);


		  for ($i=2;$i<=count($result2)-1;$i++) {
			  $curIndex = $i;
			  //echo $curIndex . '--' ;
              $previousIndex = $i-1 ;
			  $previousIndexBack2 = $i-2 ;
			  if (
				 $result2[$previousIndex]['ema3'] < $result2[$curIndex]['ema3'] &&
                 $result2[$previousIndex]['ema3'] < $result2[$previousIndexBack2]['ema3'] 
				 ) {
                 $result2[$curIndex]['PreviousTurnType'] = 'TurnUp' ;
				 $result[$curIndex]['lastTurnTypeCandleID'] = $result[$curIndex]['candleID'];
			  }
			  if (
				 $result2[$previousIndex]['ema3'] > $result2[$curIndex]['ema3'] &&
                 $result2[$previousIndex]['ema3'] > $result2[$previousIndexBack2]['ema3'] 
				 ) {
                 $result2[$curIndex]['PreviousTurnType'] = 'TurnDown' ;
				 $result[$curIndex]['lastTurnTypeCandleID'] = $result[$curIndex]['candleID'];
			  }			   
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
			  
		  }

		  for ($i=2;$i<=count($result2)-1;$i++) {
			  $result2[$i]['PreviousTurnTypeBack2'] = $result2[$i-1]['PreviousTurnType'] ; 
			  $result2[$i]['PreviousTurnTypeBack3'] = $result2[$i-2]['PreviousTurnType'] ; 
			  if ($i > 2) {
                $result2[$i]['PreviousTurnTypeBack4'] = $result2[$i-3]['PreviousTurnType'] ; 
			  }		     
		  }

		  $lastIndex =count($result2)-1;
		  $jsonDataString = JSON_ENCODE($result2[$lastIndex]);
		  //echo $jsonDataString; return;
          $thisAction = '???' ; $actionReason = '???';
		  $actionList =''; $WinTradeList = array();
          if ($data['Mode']=== 'getSignal' || $data['Mode']=== 'AjaxgetSuggestSignal2') { 
		    list($thisAction,$actionReason)  = testClassTradeByObject($result2[$lastIndex]);
		  }

		  if ($data['Mode']=== 'AjaxgetCalProfit' ) { 
		      $WinTradeList = getProfit($result2,$data);
		  }
		  //echo "Action List="  . $actionList ; return;

		  
		  $obj = new stdClass();
		  $obj->winTradeList  = $WinTradeList;
		  $obj->AnalysisData = $result2;
          $obj->lastTime = $result2[$lastIndex]['timestamp'] ;
		  $obj->thisAction = $thisAction ;
		  $obj->actionList = $actionList ;         
		  $obj->actionReason = $actionReason ;
		  $obj->rsi = $result2[$lastIndex-1]['rsi'] .','. $result2[$lastIndex]['rsi'] ;
/*
		  $myfile = fopen("rsi.txt", "w") or die("Unable to open file!");
          $txt = JSON_ENCODE($result2);
          fwrite($myfile, $txt);
          fclose($myfile);

		  $myfile = fopen("dataTest.json", "w") or die("Unable to open file!");
		  fwrite($myfile, JSON_ENCODE($result2));
          fclose($myfile);

*/

		  
		  

		  echo json_encode($obj);


		  
		  
		  //date('Y-m-d H:i:s',$startTimestamp) ;
		  
  
  
	     
  
} // end function

function testClassTradeByObject($jsonData) { 
 
// echo gettype($jsonData) . '<hr>';; 


 require_once("clsTrade_V2.php");
 $clsTrade = new clsTrade ;
 $pdo=  getPDONew();


 $macdThershold = 0.1 ; $lastMacdHeight = 0.1 ;

// $json_array = $jsonData;
$json_array = json_decode(json_encode($jsonData),true);
/*
list($thisAction,$actionReason,$nextColor,$remark)= $clsTrade->getActionFromIDVer2($json_array,$macdThershold) ;
*/
$lastActionCode = '';
list($thisAction,$actionReason) =$clsTrade->getActionFromClsTradeDeriv($json_array,$macdThershold,$lastActionCode);

//echo $thisAction . ' :: ' . $actionReason . '-->' . $nextColor;
//echo $thisAction . ' :: ' . $actionReason  . '-->' . $nextColor;; 
return array($thisAction,$actionReason)  ;


} // end function

function getProfit($AnalystData,$data) { 

require_once("clsTrade_V2.php");
 $clsTrade = new clsTrade ;

         $macdThershold = 1;
		 $startPoint = $data['startPoint'];
		 $stopPoint = $data['stopPoint'];
		// echo "Start Point=" . $startPoint;
		 $actionList ='';
		 $WinTradeList = array();
		 
		 for ($i=0;$i<=count($AnalystData)-1;$i++) {
			$CandleID = $AnalystData[$i]['candleID'] ;
			//echo "-->" . $CandleID ;
			$objTrade = $clsTrade->CalWinByIDVerObject($CandleID,$AnalystData) ;		   
			$WinTradeList[] = $objTrade ;
		 }

		 return $WinTradeList;
	     


} // end function

function getNewTradeno() { 

require_once('newutil2.php');
$dbname = 'thepaper_lab' ;
$pdo = getPDONew()  ;
//$pdo->exec("set names utf8mb4") ;

$sql = 'SELECT IFNULL(max(TradeNo) +1 ,1) as nextTradeNo FROM  TradeDeriv'; 
$params = array();
$sValue = pdogetValue($sql,$params,$pdo) ;

echo trim($sValue);


} // end function 

function saveTradeList($data) { 
/*
"{Mode":"saveTradeList","tradeNo":"","assetCode":"R_50","timeframe":"1","starttime":"3/3/2568 06:38:00","endtime":"3/3/2568 06:40:00","totalTrade":3,"maxLossCon":2,"TradeList":[{"subtradeno":"1","contractId":"274198027168","action":"CALL","assetCode":"R_50","MoneyTrade":1,"InitBuyPrice":200.4502,"CloseBuyPrice":200.611,"profit":0.95,"lossCon":0,"startTime":"3/3/2568 06:38:00","closeTime":"03/03/2025, 06:39:00 AM","closeStatus":"Y"},{"subtradeno":"2","contractId":"274198083168","action":"CALL","assetCode":"R_50","MoneyTrade":1,"InitBuyPrice":200.611,"CloseBuyPrice":200.5274,"profit":-0.65,"lossCon":1,"startTime":"3/3/2568 06:39:00","closeTime":"03/03/2025, 06:40:00 AM","closeStatus":"Y"},{"subtradeno":"3","contractId":"274198139308","action":"PUT","assetCode":"R_50","MoneyTrade":1,"InitBuyPrice":200.5589,"CloseBuyPrice":200.5737,"profit":-0.27,"lossCon":2,"startTime":"3/3/2568 06:40:00","closeTime":"03/03/2025, 06:41:00 AM","closeStatus":"N"}]}: 

*/
   $AlltradeList = $data['TradeList'] ;
   $sql = "REPLACE INTO TradeDeriv(TradeNo, startTime, endTime, AssetCode, timeFrame, profit,totalTrade, maxLossCon,tradeList) VALUES (?,?,?,?,?,?,?,?,?)"; 
   $data['totalProfit'] = 0 ;

   $params = array(
	 $data['tradeNo'],$data['starttime'],$data['endtime'],
     $data['assetCode'],$data['timeframe'],$data['grandProfit'],
	 $data['totalTrade'],	
	 $data['maxLossCon'],JSON_ENCODE($data['TradeList'])
   );

   
   $pdo = getPDONew()  ;   
   if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
      echo 'Error' ;
      return false;
   }
   
   //$pdo->commit();

} // end function

function getAnalysisData($data) { 


          require_once('../api/phpCandlestickIndy.php');
          $clsStep1 = new TechnicalIndicators();   

          require_once('../api/phpAdvanceIndy.php');
          $clsStep2 = new AdvancedIndicators();   
          
	      //$candleData = JSON_DECODE($data['candleData'],true) ;
		  $candleData0 = $data['candleData'] ;
		  $candleData = array();
		  for ($i=0;$i<=count($candleData0)-1;$i++) {
		     $candleData[] = $candleData0[$i];
		  }



		  $myfile = fopen("rawData.json", "w") or die("Unable to open file!");
		  fwrite($myfile, JSON_ENCODE($data['candleData']));
		  fclose($myfile);
         

		  $lastIndex = count($candleData)-1 ;

		 // echo date('H:i',$candleData[0]['time']);
		  //echo ' ->'.  date('H:i',$candleData[$lastIndex]['time']);

		  $result = $clsStep1->calculateIndicators($candleData);
          $result2= $clsStep2->calculateAdvancedIndicators($result);

          
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

		  $stResult2 = JSON_ENCODE($result2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
		  $myfile = fopen("AnalyData.json", "w") or die("Unable to open file!");
		  
		  fwrite($myfile, $stResult2);
		  fclose($myfile);
		  
/*
		  for ($i=1;$i<=count($result2)-1;$i++) {
			$previousADXDirect = $result2[$i-1]['adxDirection'] ;
			$ADXDirect =  $result2[$i]['adxDirection'] ;
			if ($ADXDirect === $previousADXDirect) {
			   $result2[$i]['adxDirectionCount'] = $result2[$i-1]['adxDirectionCount']+1 ;
			} else {
			   $result2[$i]['adxDirectionCount'] = 0;
			}		   
		  }

*/
          //$result2 = AnalyADX($result2) ;
		  //list($series1,$series2,$series3,$series4,$series5) = ScanSignal($result2);

		  $highRiskPeriods = getRiskAnalysisClaude();
		  $signalChecker   = getsignalCheckerDeepSeek();



		  $lastIndex =count($result2)-1;
		  $jsonDataString = JSON_ENCODE($result2);

		  $obj = new stdClass();		  
		  /*
		  $obj->exTra1 = implode(',',$series1) ;
		  $obj->exTra2 = implode(',',$series2) ;
		  $obj->exTra3 = implode(',',$series3) ;
		  $obj->exTra4 = implode(',',$series4) ;
		  $obj->exTra5 = implode(',',$series5) ;
		  */
          $obj->highRiskPeriods = $highRiskPeriods;
		  $obj->signalChecker   = $signalChecker;
		  $result3 = JSON_DECODE($jsonDataString) ;
		  for ($i=0;$i<=count($result3)-1;$i++) {
			  $result3[$i]->numRisk = 0 ;
			  for ($j=0;$j<=count($highRiskPeriods)-1;$j++) {
				  if ($highRiskPeriods[$j]['timestamp'] === intval($result3[$i]->timestamp) ) {
                    $result3[$i]->numRisk = $highRiskPeriods[$j]['warnings'];
				  }
			  }		     
		  }


		  $obj->AnalysisData = $result3  ;

          $obj->lastTime = $result3[$lastIndex]->timestamp ;
		  $st = json_encode($obj);

		  echo $st;

		  //echo json_encode($obj);
		  $myfile = fopen("dataTest.json", "w") or die("Unable to open file!");
		  
		  fwrite($myfile, $st);
		  fclose($myfile);

} // end function

function ScanSignal($AnalyData) { 

//         $AnalyData = JSON_DECODE($data['AnalyData']) ;		 
	     $series1 = array();
		 for ($i=1;$i<=count($AnalyData)-3;$i++) {
		   $turnList = getTurnList2Pair($AnalyData,$thisIndex=$i)  ;
		   if ($turnList==='TurnDown-TurnUp' || $turnList==='TurnUp-TurnDown' ) {
              $series1[] = $AnalyData[$i-1]['timefrom_unix'] .'@#@';
		   } 
		 }

		 $series2 = array();
		 for ($i=1;$i<=count($AnalyData)-3;$i++) {
		   $turnList = getTurnList4Pair($AnalyData,$thisIndex=$i)  ;
		   if ($turnList==='TurnDown-TurnUp-TurnDown-TurnUp'  ||
			   $turnList==='TurnUp-TurnDown-TurnUp-TurnDown'  
			   
		   ) {
              $series2[] = $AnalyData[$i]['timefrom_unix'] .'###';
		   } 
		 }

		 /*R-G-R-R-G--->D-U-D-D-U*/
		 /*G-R-G-G-R*/
		 $series3 = array();
		 for ($i=1;$i<=count($AnalyData)-3;$i++) {
		   $turnList = getTurnList5Pair($AnalyData,$thisIndex=$i)  ;
		   if ($turnList==='TurnDown-TurnUp-TurnDown-TurnDown-TurnUp'  ||
			   $turnList==='TurnUp-TurnDown-TurnUp--TurnUp-TurnDown'  			   
		   ) {
              $series3[] = $AnalyData[$i]['timefrom_unix'] .'###';
		   } 
		 }

		 $series4 = array();
		 for ($i=1;$i<=count($AnalyData)-3;$i++) {
		   list($MACDHeight,$SlopeValue) = getPararellList($AnalyData,$thisIndex=$i)  ;
		   if ($MACDHeight < 0.2 && $SlopeValue <= 0.3			   
		   ) {
              $series4[] = $AnalyData[$i]['timefrom_unix'] .'###';
		   } 
		 } 


		 
		
		$series5 = array();
		for ($i=1;$i<=count($AnalyData)-3;$i++) {
		   
		   if ($AnalyData[$i]['emaConflict'] != 'N') {
            $series5[] = $AnalyData[$i]['timefrom_unix'] .'-$-';
		   }

		}

		 
		 




		 return array($series1,$series2,$series3,$series4,$series5) ;


} // end function 


function getTurnList2Pair($AnalyData,$thisIndex) {

$turnList=  $AnalyData[$thisIndex]['TurnType'] .'-'. $AnalyData[$thisIndex]['PreviousTurnType']  ;
return $turnList;
			   


} // end function

function getTurnList4Pair($AnalyData,$thisIndex) {

$turnList=  $AnalyData[$thisIndex]['TurnType'] .'-'.			$AnalyData[$thisIndex]['PreviousTurnType']  .'-'.			
$AnalyData[$thisIndex]['PreviousTurnTypeBack2']   .'-'.			
$AnalyData[$thisIndex]['PreviousTurnTypeBack3']  
	
;
return $turnList;
			   


} // end function

function getTurnList5Pair($AnalyData,$thisIndex) {

$turnList=  $AnalyData[$thisIndex]['TurnType'] .'-'.			$AnalyData[$thisIndex]['PreviousTurnType']  .'-'.			
$AnalyData[$thisIndex]['PreviousTurnTypeBack2']   .'-'.			
$AnalyData[$thisIndex]['PreviousTurnTypeBack3'] .'-'.
$AnalyData[$thisIndex]['PreviousTurnTypeBack4']  
	
;
return $turnList;
			   


} // end function

function getPararellList($AnalyData,$thisIndex) {



$MACDHeight =  abs($AnalyData[$thisIndex]['MACDHeight'])  ;
$SlopeValue = abs($AnalyData[$thisIndex]['ema3SlopeValue'])  ;			
	
;
return array($MACDHeight,$SlopeValue);
			   


} // end function

function EMAProblem1($AnalyData,$thisIndex) {
// ตรวจสอบว่า แท่งปัจจุบัน  ค่า ema กำลังรอปรับตัวอยู่หรือไม่ 

        $emaAbove =  $AnalyData[$thisIndex]['MACDHeight'] ;
		$thisColor = $AnalyData[$thisIndex]['thisColor'] ;
		if ($emaAbove ==='3' && $thisColor = 'Green') {
           return ''; 
		}
		if ($emaAbove ==='5' && $thisColor = 'Red') {
           return ''; 
		}

		if ($emaAbove ==='3' && $thisColor = 'Red') {
           return 'Y'; 
		}
		if ($emaAbove ==='5' && $thisColor = 'Green') {
           return 'Y'; 
		}
		return 'Y';
} // end function

function adxFind($AnalyData,$thisIndex) {
/*

	     // ใช้ array_map เพื่อเพิ่มฟิลด์ discount 10% ให้กับทุกรายการ
         $mapped = array_map(function($item) {
           $item['discount_price'] = $item['price'] * 0.9;
           return $item;
         }, $dataObjArray);

*/     
	    $adxList = array();
	    for ($i=0;$i<=count($AnalyData)-1;$i++) {


	      
	       
	    }



} // end function



function FindSignal($dataA) { 
/* Init Data*/
         
		 $data = $dataA['data'] ;
         $AnalyData = $data['dataAnaly']  ;
		 $candleID = $data['startPoint'] ;
		 require_once('../phpClsTrade/ver3/clsTradeV3.php');
		 $clsTrade = new clsTradeV3($AnalyData) ;

		 $action = $clsTrade->MaingetAction($candleID) ;
		 echo $action ;

/* End Init Data*/
} // end function

function AnalyADX($data) {

$result = [];

// ตัวแปรสำหรับเก็บทิศทางปัจจุบันและจำนวนการเกิดต่อเนื่อง
$currentDirection = null;
$consecutiveCount = 0;

// วนลูปผ่านข้อมูลแต่ละรายการ
foreach ($data as $index => $item) {
    // สร้างรายการใหม่เริ่มจากข้อมูลเดิม
    $newItem = $item;
    
    // ตรวจสอบทิศทาง ADX โดยเปรียบเทียบกับข้อมูลก่อนหน้า
    if ($index > 0) {
        $previousAdx = $data[$index - 1]['adx'];
        $currentAdx = $item['adx'];
        
        // กำหนดทิศทาง Up หรือ Down
        if ($currentAdx > $previousAdx) {
            $direction = "Up";
        } else if ($currentAdx < $previousAdx) {
            $direction = "Down";
        } else {
            $direction = "Flat"; // ถ้าค่าเท่ากัน
        }
        
        // ตรวจสอบว่าทิศทางเหมือนเดิมหรือไม่
        if ($direction === $currentDirection) {
            $consecutiveCount++;
        } else {
            $currentDirection = $direction;
            $consecutiveCount = 1;
        }
    } else {
        // สำหรับแท่งแรก ไม่สามารถกำหนดทิศทางได้
        $direction = "Initial";
        $currentDirection = $direction;
        $consecutiveCount = 1;
    }
    
    // เพิ่มข้อมูลวิเคราะห์ลงในรายการ
    $newItem['adxDirection'] = $direction;
    $newItem['adxDirectionCon'] = $consecutiveCount;
    
    // เพิ่มลงในอาร์เรย์ผลลัพธ์
    $result[] = $newItem;
}

// แสดงผลลัพธ์เป็น JSON
//echo json_encode($result, JSON_PRETTY_PRINT);

return $result ;

} // end function 

function getRiskAnalysisClaude(){

	require_once('TradingConditionAnalyzer.php');
	$candleData = array();
	$clsRisk = new TradingConditionAnalyzer($candleData);
	list($candleCount,$highRiskPeriods) = scanForHighRiskPeriods('rawData.json', 'long');

	return $highRiskPeriods ;




} // end function


function getsignalCheckerDeepSeek() {

  require_once('TradingSignalCheckerV3.php'); 
  $historicalData = getAnalysisData22()   ;
  $signalChecker = new TradingSignalChecker($historicalData);
  $analysisData = $signalChecker->analyzeSignals();

  //echo '<h2>Count=' . count($analysisData) . '</h2>';

  return $analysisData ;

} // end function

function getAnalysisData22() {

 $st = "";   
 $sFileName = 'dataTest.json';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file); 

 $sdata = JSON_DECODE($st,true) ;
 $historicalDataA = $sdata['AnalysisData'] ;
 $historicalData = array();
 for ($i=0;$i<=count($historicalDataA)-1;$i++) {
	 //$sObj = new stdClass();
	 $sObj = array();

	 $sObj['date'] = intval($historicalDataA[$i]['timestamp']);
	 $sObj['ema3'] =  $historicalDataA[$i]['ema3'];
	 $sObj['ema5'] =  $historicalDataA[$i]['ema5'];
	 //$sCandle = new stdClass();
	 $sCandle = array();
	 $sCandle['open'] = $historicalDataA[$i]['open'];
	 $sCandle['high'] = $historicalDataA[$i]['high'];
	 $sCandle['low'] = $historicalDataA[$i]['low'];
	 $sCandle['close'] = $historicalDataA[$i]['close'];
	 $sObj['candle'] = $sCandle ;
	 $historicalData[] = $sObj ;
 }

 return $historicalData;

} // end function


?>