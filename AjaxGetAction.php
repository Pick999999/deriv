<?php
 ob_start();
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
// Test(); return ;
 header('Access-Control-Allow-Methods: GET, POST');
 header('Access-Control-Allow-Origin: *'); 
 ob_start();
 //https://www.thaicreate.com/community/login-php-jquery-2encrypt.html
 //https://www.cyfence.com/article/design-secured-api/
 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);   
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data) {
       //$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
       ///require_once($newUtilPath ."/src/newutil.php");	
       if ($data['Mode'] == 'getAction') { getAction($data); }
	   if ($data['Mode'] == 'getActionFromNewLabV2') { getActionFromNewLabV2($data); }

	   if ($data['Mode'] == 'CalWinById') { AjaxCalWinById($data); }
	   if ($data['Mode'] == 'SaveTrade') { SaveTrade($data); }

       if ($data['Mode'] == 'getTradeSectionNo') { getTradeSectionNo($data); }

	   if ($data['Mode'] == 'retriveTradeHistory') { retriveTradeHistory($data); }

	   if ($data['Mode'] == 'getPageStatus') { GetPageStatus($data); }

	   if ($data['Mode'] == 'setCloseTrade') { setCloseTrade($data); }
	   
	   
       return;
    }
 

 function getAction($data) { 
 
	      $candleData = $data['candleData'];
		  //echo 'Len='.  count($candleData);
		  //return;
		  /*
		  $sText = JSON_ENCODE($candleData);
		  $myfile = fopen("tmp/candlepython.json", "w") or die("Unable to open file!");
		  
		  fwrite($myfile, $sText);
		  fclose($myfile);
		  */
		  
		    
		  $timefrom_unix = date("H:i:s", $candleData[count($candleData)-1]['time']);
		  $thisColor = ''; $actionReason=''; $ActionCaseNo = 0 ;
		 // echo $formatted_time ;
		  //$suggestColor = getSuggestByCHATGPT($candleData);
		  //$suggestColor = getSuggestByClaude($candleData) ;
		  //$suggestColor = getSuggestByDeepSeek($candleData);
		  list($suggestColor,$timefrom_unix,$thisColor,$actionReason,$CaseNo)=getSuggestByClassTrade($candleData);
		 //echo $formatted_time . '-'.$suggestColor;
		 if ($suggestColor === 'Green') {
			 //echo "CALL" ;
			 $action= "CALL" ;
		 } else {
			 //echo "PUT" ;
			 $action = 'PUT';
		 } 
		 $sObj = new stdClass() ;
         $sObj->timefrom =  $timefrom_unix ;
		 $sObj->thisColor =  $thisColor ;
		 $sObj->action =  $action ;
		 $sObj->suggestColor =  $suggestColor ;

         $sObj->actionReason =  $actionReason ;
         $sObj->CaseNo =  $CaseNo ;
		 $sObj->lastClosePrice = $candleData[count($candleData)-1]['close'];

		 echo JSON_ENCODE($sObj);



		 return;

		 $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
		 $txt = JSON_ENCODE($candleData,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		 fwrite($myfile, $txt);
		 fclose($myfile);
			  
 
} // end function 

function getActionFromNewLabV2($data) { 
 
          
	      $candleData = $data['candleData'];
		  //echo count($candleData) . '***';
		  $foundIndex = count($candleData)-1 ;
		     
		  $timefrom_unix = date("H:i:s", $candleData[$foundIndex]['time']);
		  $thisColor = ''; $actionReason=''; $ActionCaseNo = 0 ;
		 
		  $indextoGetAction = 0 ;
		  
		  $emaShort = (isset($data['emaShort'])) ? $data['emaShort'] : 3 ;
		  $emaLong = (isset($data['emaLong'])) ? $data['emaLong'] : 5 ;
		  

list($suggestColor,$timefrom_unix,$thisColor,$actionReason,$CaseNo)=getSuggestByClassTrade($candleData,$foundIndex,$emaShort,$emaLong);
		 
		 if ($suggestColor === 'Green') {
			 //echo "CALL" ;
			 $action= "CALL" ;
		 } else {
			 //echo "PUT" ;
			 $action = 'PUT';
		 } 
		 $sObj = new stdClass() ;
         $sObj->timefrom =  $timefrom_unix ;
         $sObj->emaShort =  $emaShort ;
		 $sObj->emaLong =  $emaLong ;

		 $sObj->foundIndex = $foundIndex;		 		
		 $sObj->thisColor =  $thisColor ;
		 $sObj->action =  $action ;
		 $sObj->suggestColor =  $suggestColor ;

         $sObj->actionReason =  $actionReason ;
         $sObj->CaseNo =  $CaseNo ;
		 $sObj->lastClosePrice = $candleData[count($candleData)-1]['close'];

		 echo JSON_ENCODE($sObj);



		 return;

		 
			  
 
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
}


function getSuggestByClassTrade($candleData,$lastIndex=-1,$emaShort=3,$emaLong=5) { 

require_once('api/phpCandlestickIndy.php');
$clsStep1 = new TechnicalIndicators();   

require_once('api/phpAdvanceIndy.php');
$clsStep2 = new AdvancedIndicators();   
$result = $clsStep1->calculateIndicators($candleData,$emaShort,$emaLong);
$result2= $clsStep2->calculateAdvancedIndicators($result);
$result2= Final_AdvanceIndy($result2)  ;
/*
$stAnaly = JSON_ENCODE($result2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
$myfile = fopen("newDerivObject/AnalyData2.json", "w") or die("Unable to open file!");
fwrite($myfile, $stAnaly);
fclose($myfile); 

$sAr = array();
for ($i=0;$i<=count($result2)-1;$i++) {
  $sObj = new stdClass();
  $sObj->candleID   = $result2["candleID"] ;
  $sObj->timefrom_unix   = $result2["timefrom_unix"] ;
  //$sObj->thisColor = $result2["$"]

   
}
*/


/*
require_once("newutil2.php"); 
require_once("../iqlab/clsTradeVer0/clsTradeVer_Object.php"); 
$clsTrade = new clsTrade();
require_once('../iqlab/clsTradeVer0/getActionFromIDVerObject.php');
*/
require_once('../iqlab/sortGetAction.php');

// ถ้าไม่ระบุ lastIndex ส่งมาให้ก็จะใช้ lastIndex ตัวท้ายสุด
if ($lastIndex === -1) {
   $lastIndex = count($result2) -1 ;
}
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


function getTradeSectionNo($data) { 

require_once("newutil2.php"); 
$sql ="select ifnull(max(tradeSectionNoOfDay),1) from headLabDeriv where dayTrade= ?"; 
$today = date('Y-m-d');
$pdo = getPDONew()  ;	
$params = array($today);
$sValue = pdogetValue($sql,$params,$pdo) ;
if ($sValue===0) {
  $sValue = 1;
} else {
  $sValue += 1;
}
echo $sValue ;


return ;





} // end function


function SaveTrade($data) { 


	$st = JSON_ENCODE($data,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	$myfile = fopen("sTrade.txt", "w") or die("Unable to open file!");		 
	fwrite($myfile, $st);
	fclose($myfile);

    require_once('newutil2.php');

    $sqlInsertHead = 'REPLACE INTO headLabDeriv(dayTrade, tradeSectionNoOfDay, timeTradeStart, timeTradeStop, TotalTrade, totalBalance, maxLossCon) VALUES (?,?,?,?,?,?,?)';

    $pdo = getPDONew()  ;
	$dayTrade = date("Y:m:d") ;
	$tradeSectionNoOfDay = $data['tradeSectionNoOfDay'] ;
	
	$numberTrade =  count($data['ohlcList']);
	$totalBalance = 0 ;
	$totalTrade = count($data['ohlcList']);
	for ($i=0;$i<=count($data['ohlcList'])-1;$i++) {
	    $totalBalance += $data['ohlcList'][$i]['profit'] ;
		//echo $data['ohlcList'][$i]['profit']  . " : ";
	}
	$timeTradeStop  = $data['ohlcList'][$totalTrade-1]['startTimeTrade'];
	$timeTradeStart   = $data['ohlcList'][0]['startTimeTrade'];
	
	$maxLossCon =  1 ;
	$dayTradeStop = 
	//$lastUpdate  = 
	//$ohlc = $data['ohlcList'];
	$params = array(
	  $dayTrade,
      $tradeSectionNoOfDay,
      date('Y-m-d H:i:s',$timeTradeStart) ,
      date('Y-m-d H:i:s',$timeTradeStop),
      $totalTrade ,
      $totalBalance,
      $maxLossCon
  	  );
		
	 
	//print_r($params);
	
   if (!pdoExecuteQueryV2($pdo,$sqlInsertHead,$params)) {
      echo 'Error' ;
      return false;
   }

   $detailArray = $data['detailTrade'] ;
   $assetCode = $data['curpairCode'] ;
   saveDetailTrade($pdo,$assetCode,$detailArray);

   $sqlDeleteDetail = 'DELETE FROM  detailTradeLabV2 where dayTrade=? and tradeSectionNoOfDay = ? ';
   $params = array($dayTrade,$tradeSectionNoOfDay);
   if (!pdoExecuteQueryV2($pdo,$sqlDeleteDetail,$params)) {
      echo 'Error' ;
      return false;
   }


   $sqlInsertDetail = 'INSERT INTO detailTradeLabV2(dayTrade, tradeSectionNoOfDay, ohlcNo, ohlcList) VALUES (?,?,?,?)';
   for ($i=0;$i<=count($data['ohlcList'])-1;$i++) {
	  $params = array(
	  $dayTrade,
      $tradeSectionNoOfDay,
      $i+1 ,
      JSON_ENCODE($data['ohlcList'][$i])      
  	  );
	  if (!pdoExecuteQueryV2($pdo,$sqlInsertDetail,$params)) {
        echo 'Error' ;
        return false;
      }	  
   }

} // end function

function Test() { 

require_once('newutil2.php') ;
	
	 $st = "";   
	 
	 $sFileName = 'sTrade.txt';
	 $file = fopen($sFileName,"r");
	 while(! feof($file))  {
	   $st .= fgets($file) ;
	 }
	 fclose($file);
	 
	 
     $pdo = getPDONew()  ;	 
	 $jsonObj = JSON_DECODE($st,true) ;
	 $detailArray = $jsonObj['detailTrade'] ;
	 //$assetCode   =  $jsonObj['curpairCode'] ;
	 $assetCode = 'R_25'; 
	 saveDetailTrade($pdo,$assetCode,$detailArray);
	


} // end function

function saveDetailTrade($pdo,$assetCode,$detailArray) { 

	/*
	"tradeno": "5",
            "Lotno": "11",
            "contractID": "285876282288",
            "actionCode": "CALL::CodeNew-6-26-02G::CaseNo-44-0A-06:15-TurnUp",
            "caseNo": "CALL::CodeNew-6-26-02G::CaseNo-44-0A-06:15-TurnUp",
            "contractType": "CALL::CodeNew-6-26-02G::CaseNo-44-0A-06:15-TurnUp",
            "moneyTrade": "18",
            "timetrade": "06:15:01",
            "lastClosePrice": "2722.146",
            "entryPrice": "2722.08",
            "closeTradePrice": "2722.437",
            "winStatus": "",
            "profit": "17.02"
	
	*/

	$fname = 'tradeno,Lotno,
    contractID,assetCode,
    actionCode,caseNo,
    contractType,moneyTrade,
    timetrade,lastClosePrice,
    entryPrice,closeTradePrice,
    winStatus,profit';
	$fnameAr = explode(",",$fname);
	
	


	$sqlInsert='REPLACE INTO detailTrade (
    tradeno,Lotno,contractID,assetCode,actionCode,
    caseNo,contractType,moneyTrade,timetrade,lastClosePrice,
    entryPrice,closeTradePrice,winStatus,profit
    ) VALUES (?,?,?,?,?,
	?,?,?,?,?,
	?,?,?,?)';

	for ($i=0;$i<=count($detailArray)-1;$i++) {
		$sTmp = $detailArray[$i]; 		
        $params[] = $sTmp['tradeno'];
		$params[] = $sTmp['Lotno'];
		$params[] = $sTmp['contractID'];
		$params[] = $assetCode ;
		$params[] = $sTmp['actionCode'];

		$params[] = $sTmp['caseNo'];
		$params[] = $sTmp['contractType'];
		$params[] = $sTmp['moneyTrade'];
		$params[] = $sTmp['timetrade'];		
		$params[] = $sTmp['lastClosePrice'];

		$params[] = $sTmp['entryPrice'];
        $params[] = $sTmp['closeTradePrice'];
        $params[] = $sTmp['winStatus'];
        $params[] = $sTmp['profit'];
		echo count($params); 



         	
		//print_r($params); echo '<hr>' ;
		
		if (!pdoExecuteQueryV2($pdo,$sqlInsert,$params)) {
		   echo 'Error' ;
		   return false;
		}
		
		unset($params);
	
	}
    

} // end function


function getOHLCTime() { 

$sql = "SELECT 
    JSON_EXTRACT(`ohlcList`, '$.ohlcData[*].time') AS all_times
    FROM detailTradeLabV2 LIMIT 25"; 


} // end function

function retriveTradeHistory($data) { 

	
	require_once("newutil2.php"); 
	

    $pdo = getPDONew()  ;	
	$sql = 'select * from headLabDeriv where dayTrade=?'; 
	$params = array($data['dayTrade']);
	
	$rs= pdogetMultiValue2($sql,$params,$pdo) ;	
	echo "<h1>ข้อมูลจากตาราง your_table_name</h1>";
    echo "<table border='1'>";
	$isFirstRow = true;
	while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		// พิมพ์ header ของตารางในครั้งแรก
        if ($isFirstRow) {
            echo "<tr>";
            foreach ($row as $fieldName => $value) {
              if ($fieldName !=='lastUpdate') {              
                echo "<th>" . htmlspecialchars($fieldName) . "</th>";
			  }
            }
            echo "</tr>";
            $isFirstRow = false;
        }

        // เริ่มแถวใหม่ในตาราง HTML
        echo "<tr>";

        // วนลูปอ่านค่าแต่ละฟิลด์ (คอลัมน์) ในเรคคอร์ดปัจจุบัน
        foreach ($row as $fieldName => $fieldValue) {
            // แสดงชื่อฟิลด์และค่าของฟิลด์นั้น
            // echo "Field: " . htmlspecialchars($fieldName) . " - Value: " . htmlspecialchars($fieldValue) . "<br>";
			if ($fieldName !=='lastUpdate') {              
              echo "<td>" . htmlspecialchars($fieldValue) . "</td>";
			}
        }

		?>
         <td>  
		<button type='button' id='' class='mBtn' onclick="fff()">ดู กราฟ</button>
		</td>
		
		<?php
        echo "</tr>";
			    
	} 

	 echo "</table>";


} // end function

function CreateAnalyData($candleData) { 

	$candleDataString = JSON_ENCODE($candleData) ;
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

		  return $result2 ;
		  $lastIndex = count($result2)-1;
		  $turnMode999 = $result2[$lastIndex]['TurnMode999'];
		 // echo $turnMode999 . '-->' ;

} // end function

function getResultColorAA($jsonAnalyzed,$thisIndex) { 

	     return $jsonAnalyzed[$thisIndex+1]['thisColor'] ;

} // end function



function AjaxCalWinById($data) { 

         $emaShort = $data['emaShort'] ;
         $emaLong = $data['emaLong'] ;
		 $candleData = $data['candleData'];
		 $timeSelected = $data['timeSelected'] - (7*3600) ;
		 echo 'Total Data=' . count($candleData) . '<br>';
		 echo 'time Selected=' . $timeSelected  . date("H:i:s",$timeSelected) .'<br>';
		 $AnalyData = CreateAnalyData($candleData) ;
		 $ss  = $AnalyData[count($AnalyData)-1] ;
		// print_r($ss);
        // return;
		// echo $AnalyData[count($AnalyData)-1]['previousColor'];
		 //return;



		 $sFound = false; 
		 for ($i=0;$i<=count($AnalyData)-1;$i++) {
			 if (($AnalyData[$i]['timestamp']) == $timeSelected) {
				 echo 'Found at ' . $i ; 
				 echo ' Color=' . $AnalyData[$i]['thisColor'] ;
				 $sFound = true; $thisIndex = $i;
				 break ; 
			 }		    
		 }
		if (!$sFound) { echo 'Not Found' ; return ; }
		 
		require_once('../iqlab/sortGetAction.php'); 
        $winStatus = false ;  $numTrade= 0 ; $limitTrade= 10 ;

		while ($winStatus === false) {
		
			$row = $AnalyData[$thisIndex];
			$macdThershold = 0.5 ; $lastMacdHeight = 0 ;
			list($thisAction,$actionCode,$CaseNo) =getActionFromIDVerObject_Sorted($row,$macdThershold,$lastMacdHeight);
			$suggestColor = ($thisAction == 'CALL') ? 'Green' : 'Red';		
			echo ' Suggest Color=' . $thisAction ;
			$resultColor = getResultColorAA($AnalyData,$thisIndex) ;
			if ($suggestColor === $resultColor) {
				$winStatus = true;
			} else {
				$winStatus = false;
			}
			$numTrade++ ; 

			echo '--->' . $winStatus ;
			$thisIndex++ ;
		} // end while

		echo "<br>NumTrade = " . $numTrade;


} // end function 

function GetPageStatus() { 

	     require_once("newutil2.php"); 
		 
		 $pdo = getPDONew()  ;	
		 $sql = "select * from pageTradeStatus" ; 		 
		 $params = array();		 
		 $row = pdoRowSet($sql,$params,$pdo) ;
		 $sObj = new stdClass();
		 $sObj->assetCode = $row['assetCode'] ;
		 $sObj->isopenTrade = $row['isopenTrade'] ;
		 $sObj->isMartingale = $row['isMartingale'] ;
		 $sObj->moneyTrade = $row['moneyTrade'] ;
         $sObj->targetTrade = $row['targetTrade'] ;
		 echo JSON_ENCODE($sObj);


} // end function

function setCloseTrade() { 

         require_once("newutil2.php"); 		 
		 $pdo = getPDONew()  ;	
		 $sql = "update pageTradeStatus set isOpenTrade='N'" ; 		 
		 $params = array();
		 
	 	 if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
           echo 'Error' ;
           return false;
         }
         
		 


} // end function 





/* 
Total Data=60<br>time Selected=174935910012:05:00<br>Array
(
    [candleID] => 1749359460
    [timeframe] => 1m
    [id] => 60
    [timestamp] => 1749359460
    [timefrom_unix] => 12:11
    [high] => 6401.327
    [low] => 6400.06
    [open] => 6401.327
    [close] => 6400.291
    [thisColor] => Red
    [pip] => 1.04
    [ema3] => 6400.9061518914
    [ema5] => 6401.2557647422
    [BB] => Array
        (
            [upper] => 6,404.13
            [middle] => 6,401.41
            [lower] => 6,398.68
        )

    [rsi] => 35.41
    [atr] => 1.27
    [adx] => 24.15
    [candleWick] => stdClass Object
        (
            [upperWickPercent] => 0
            [bodyPercent] => 81.77
            [lowerWickPercent] => 18.23
            [candleType] => แท่งอ่อนแอ (Bearish)
            [force] => แรงขายสูง
            [trendText] => 
            [nextTrend] => มีแนวโน้มลงต่อ หรือ反弹เล็กน้อย
        )

    [CandleCode] => 5-Red-N-Diver-dis4-cutN-
    [MACDHeight] => -0.35
    [ema3SlopeValue] => -0.62
    [ema5SlopeValue] => -0.48
    [ema3slopeDirection] => Down
    [ema5slopeDirection] => Down
    [ema3Position] => betweenOpenClose
    [ema5Position] => betweenOpenClose
    [isBongton] => 
    [isPreviousBongton] => 
    [isPreviousBongtonBack2] => 
    [PreviousSlopeDirection] => Down
    [emaAbove] => 5
    [CutPointType] => N
    [emaConflict] => N
    [lastTurnID] => 1749359220
    [TurnType] => N
    [distance] => 4
    [PreviousTurnType] => N
    [PreviousTurnTypeBack2] => N
    [PreviousTurnTypeBack3] => N
    [PreviousTurnTypeBack4] => N
    [lastTurnTypeCandleID] => -1
    [exTra1] => 
    [adxDirection] => Down
    [adxDirectionCount] => 0
    [TurnMode999] => TurnDown
    [previousColor] => Green
    [previousColorBack2] => Red
    [previousColorBack3] => Green
    [previousColorBack4] => Red
    [macdconverValue] => 0.13
    [MACDConvergence] => Diver
)


*/


?>


