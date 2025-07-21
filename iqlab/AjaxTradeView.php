<?php
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
        
        //require_once( "newutil.php");		
        if ($data['Mode'] == 'checkLossFromTradeview') { checkLossFromTradeview($data); }
		if ($data['Mode'] == 'checkProfitForex') { checkProfitForex($data); }

		if ($data['Mode'] == 'viewlossDetail') { viewlossDetail($data); }

		if ($data['Mode'] == 'getMonth') { getMonth($data); }

		if ($data['Mode'] == 'getActionForTrade') { getActionForTrade($data); }

		

		
        return;
    }
  
function checkLossFromTradeview($data) { 

	      $txt =  $data['dataCheck'];
  
          $myfile = fopen("dataCheck.txt", "w") or die("Unable to open file!");
          
          fwrite($myfile, $txt);
          fclose($myfile); 
		  require_once( "newutil2.php");	
		  InsertData_AnalyEMATmp($txt);
		  //echo 'Insert Finished<hr>';
		  AnalyLoss2($data); return;
		  return;
/*
          $jsonObjAll = JSON_DECODE($txt,true);
		  //echo 'aa'; return;
		  CompareField($jsonObjAll[10]); return;

		  $tablename = 'AnalyTmp';

		  $sqlInsert = generateSQL($txt,$tablename)  ;
		  $myObj = new stdClass();
	      $myObj->result = 'Success' ;
	      $myObj->sql = $sqlInsert ;
  
	      $myJSON = json_encode($myObj, JSON_UNESCAPED_UNICODE);
	      echo $myJSON; 
		  return;
*/

		 
  
	     
  
  } // end function

function generateSQL($txt,$tablename) { 

	$dataJson = JSON_DECODE($txt);
    $fname = array();
    foreach ($dataJson[10] as $key => $value) {
     // echo $key . '<br>';
      $fname[] = $key; 
    }
	

    $sql='REPLACE INTO ' . $tablename . '(' . implode(',',$fname) . ') values(';
	for ($i=0;$i<=count($fname)-1;$i++) {
		$sql .= '?,';	   
	}
	$sql = substr($sql,0,strlen($sql)-1). ')';
    //echo $sql ;

	return $sql ;




} // end function


function MapFieldName($pdo,$fieldName,$tableName) { 


try {
    
   /* // ชื่อตารางและ field ที่ต้องการตรวจสอบ
    $tableName = "your_table";
    $fieldName = "your_field";
*/
    // เตรียมคำสั่ง SQL
    $rs = $pdo->prepare("DESCRIBE $tableName");
    $rs->execute();

    // ตรวจสอบผลลัพธ์
    $found = false;
    while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Field'] === $fieldName) {
            $found = true;
            break;
        }
    }

    if ($found) {
        echo "Field '$fieldName' exists in table '$tableName'";
    } else {
        echo "Field '$fieldName' does not exist in table '$tableName'";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}


} // end function


function dd($pdo,$jsonObj) { 

$sqlInsert='INSERT INTO AnalyEMATmp(curpairID, timeframe, id, timestamp, timefrom, timefrom_unix, emaPatternCode, slopePatternCode, ColorPatternCode, TurnTypePatternCode, previousMasterCode, thismasterCode, MixMasterCode, previousMasterCode2, thismasterCode2, MixMasterCode2, AllCode, AllCode2, AllCode15, AllCode13, minuteno, previousPIP, pip, pip2, pipGrowth, code, previousEMA3, ema3, ema5, differEMA, ema3SlopeValue, ema5SlopeValue, PreviousSlopeDirection, slopeDirection, ema5slopeDirection, MACDHeight, MACDHeightCode, MACDConvergence, emaAbove, emaConflict, previousColorBack4, previousColorBack3, previousColorBack2, previousColor, thisColor, nextColor, CutPointType, TurnType, PreviousTurnType, PreviousTurnTypeBack2, PreviousTurnTypeBack3, PreviousTurnTypeBack4, bodyShape, resultColor, ADX, ADXShort, cci, Score, totalTrade, totalLoss, actionReason, totalTrade_Python, totalLoss_Python, actionFromPython, actionReason_Python, WinStatus, totalLoss_Method2, totalTrade_Method2) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
// 68 Fields



} // end function

function CompareField($jsonObj) { 

$fieldOnTable ='curpairID, timeframe, id, timestamp, timefrom, timefrom_unix, emaPatternCode, slopePatternCode, ColorPatternCode, TurnTypePatternCode, previousMasterCode, thismasterCode, MixMasterCode, previousMasterCode2, thismasterCode2, MixMasterCode2, AllCode, AllCode2, AllCode15, AllCode13, minuteno, previousPIP, pip, pip2, pipGrowth, code, previousEMA3, ema3, ema5, differEMA, ema3SlopeValue, ema5SlopeValue, PreviousSlopeDirection, slopeDirection, ema5slopeDirection, MACDHeight, MACDHeightCode, MACDConvergence, emaAbove, emaConflict, previousColorBack4, previousColorBack3, previousColorBack2, previousColor, thisColor, nextColor, CutPointType, TurnType, PreviousTurnType, PreviousTurnTypeBack2, PreviousTurnTypeBack3, PreviousTurnTypeBack4, bodyShape, resultColor, ADX, ADXShort, cci, Score, totalTrade, totalLoss, actionReason, totalTrade_Python, totalLoss_Python, actionFromPython, actionReason_Python, WinStatus, totalLoss_Method2, totalTrade_Method2';

$jsonObj['slopeDirection'] = $jsonObj['ema3slopeDirection'];
$sqlInsert = '';
$fieldMatch = '';
$fieldOnTableArray = explode(',',$fieldOnTable);
$stNoFound = 'Field ที่ไม่พบ ใน json ชุดใหม่ <br>';
for ($i=0;$i<=count($fieldOnTableArray)-1;$i++) {
	$found = false;
	foreach ($jsonObj as $key => $value) {
     // echo $key . '<br>';
	 if ( trim($fieldOnTableArray[$i]) == trim($key)) {
		 $fieldMatch .=  $fieldOnTableArray[$i]. ',';
		 $found = true; break;
	 }     
    }
	if ($found== false) {
		$stNoFound .= $fieldOnTableArray[$i] . ',' ;
	}	   
}
echo $stNoFound ;

$tablename = 'AnalyEMATmp' ;
$sql = "INSERT INTO $tablename(". $fieldMatch .') values(' ; 
$fieldMatchAr = explode(',',$fieldMatch);
$valueClause = str_repeat('?,',count($fieldMatchAr));
$valueClause = substr($valueClause,0,strlen($valueClause)-1) . ')';

$paramClause = '$params = array(';
for ($i=0;$i<=count($fieldMatchAr)-1;$i++) {
	if ($fieldMatchAr[$i] !='') {	
      $paramClause .= '$jsonObj[$i]["' . trim($fieldMatchAr[$i]) .'"],<br>';
	}
}

$paramClause = substr($paramClause,0,strlen($paramClause)-5) . ')';
echo "<hr>";

echo $sql . $valueClause;
echo "<hr>";
echo $paramClause;





} // end function


function InsertData_AnalyEMATmp($dataTxt) { 

require_once("newutil2.php");          
$pdo=getPDONew();


$sql='TRUNCATE AnalyEMATmp' ;
$params = array();
pdoExecuteQueryV2($pdo,$sql,$params) ;


$sqlInsert = 'INSERT INTO AnalyEMATmp(
curpairID, timeframe, id, timestamp, timefrom,
timefrom_unix,minuteno, previousPIP, pip, pip2, 
pipGrowth, code, previousEMA3, ema3, ema5, 
differEMA,ema3SlopeValue, ema5SlopeValue,PreviousSlopeDirection,ema3slopeDirection, ema5slopeDirection, MACDHeight, MACDHeightCode, MACDConvergence, emaAbove,
emaConflict,previousColorBack4,previousColorBack3,previousColorBack2,previousColor,
thisColor,nextColor,CutPointType,TurnType,PreviousTurnType,
PreviousTurnTypeBack2, PreviousTurnTypeBack3,PreviousTurnTypeBack4,bodyShape,resultColor, ADX,ADXShort, cci, Score) values(
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?
)';

$jsonObj = JSON_DECODE($dataTxt,true);


for ($i=0;$i<=count($jsonObj)-1;$i++) {
   
    $jsonObj[$i]["curpairID"] = $i;
	$jsonObj[$i]["ema3SlopeValue"] = $jsonObj[$i]["ema3SlopeValue"]=== null ? 0 : $jsonObj[$i]["ema3SlopeValue"];
    $jsonObj[$i]["ema3slopeDirection"] = $jsonObj[$i]["ema3slopeDirection"]=== null ? 'N' : $jsonObj[$i]["ema3slopeDirection"];

	$params = array(
	
	$jsonObj[$i]["curpairID"],
	$jsonObj[$i]["timeframe"],
	$jsonObj[$i]["id"],
	$jsonObj[$i]["timestamp"],
	$jsonObj[$i]["timefrom"],

	$jsonObj[$i]["timefrom_unix"],	
	$jsonObj[$i]["minuteno"],
	$jsonObj[$i]["previousPIP"],
	$jsonObj[$i]["pip"],
	$jsonObj[$i]["pip2"],

	$jsonObj[$i]["pipGrowth"],
	$jsonObj[$i]["code"],
	$jsonObj[$i]["previousEMA3"],
	$jsonObj[$i]["ema3"],
	$jsonObj[$i]["ema5"],

	$jsonObj[$i]["differEMA"],
	$jsonObj[$i]["ema3SlopeValue"] ,
	$jsonObj[$i]["ema5SlopeValue"],
	$jsonObj[$i]["PreviousSlopeDirection"],
	$jsonObj[$i]["ema3slopeDirection"],

	$jsonObj[$i]["ema5slopeDirection"],
	$jsonObj[$i]["MACDHeight"],
	$jsonObj[$i]["MACDHeightCode"],
	$jsonObj[$i]["MACDConvergence"],
	$jsonObj[$i]["emaAbove"],


	$jsonObj[$i]["emaConflict"],
	$jsonObj[$i]["previousColorBack4"],
	$jsonObj[$i]["previousColorBack3"],
	$jsonObj[$i]["previousColorBack2"],
	$jsonObj[$i]["previousColor"],

	$jsonObj[$i]["thisColor"],
	$jsonObj[$i]["nextColor"],
	$jsonObj[$i]["CutPointType"],
	$jsonObj[$i]["TurnType"],
	$jsonObj[$i]["PreviousTurnType"],

	$jsonObj[$i]["PreviousTurnTypeBack2"],
	$jsonObj[$i]["PreviousTurnTypeBack3"],
	$jsonObj[$i]["PreviousTurnTypeBack4"],
	$jsonObj[$i]["bodyShape"],
	$jsonObj[$i]["resultColor"],

	$jsonObj[$i]["ADX"],
	$jsonObj[$i]["ADXShort"],
	$jsonObj[$i]["cci"],
	$jsonObj[$i]["Score"]) ;
	//echo count($params) ; return ;

	$ErrMsg  = '';                     
       try {                     
          $rs = $pdo->prepare($sqlInsert);
          $rs->execute($params);          

		  
       } catch (PDOException $ex) {
          echo  $ex->getMessage();
		  return false ;
       
       } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
		  return false ;
       }

	
	unset($params);
} // end for 

} // end function

function AnalysisLoss() { 

         require_once("newutil2.php"); 
         require_once("clsTrade.php"); 
		 $clsTrade = new clsTrade;
		 $pdo=getPDONew();

		 $tablename= 'AnalyEMATmp';

		 /*$tablename= 'AnalyEMA_HistoryForLab_5M';
		 echo '<h1>ข้อมูล วันที่ '. $data['datecheck'] . ' TimeFrame 5 M </h1>';
		 */
		 $sql = "select min(timefrom) as minDate,max(timefrom) as maxDate,max(timeframe) as timeframe99 from " . $tablename ; 
		 $params = array();
		 $row=pdoRowSet($sql,$params,$pdo);

		 
		 echo '<h3>ข้อมูล วันที่ '. date('d-m-Y H:i',$row['minDate']) .' ถึง '.date('d-m-Y H:i',$row['maxDate']) . ' TimeFrame ' . $row['timeframe99'] .'</h3>';

		 $sql = "select * from " . $tablename ; 
		 $params = array();
		 
         $rs=pdogetMultiValue2($sql,$params,$pdo) ;
		 $lossNum = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		 $loopNo = 0 ;

		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) { 
			 //$sLabObj = new stdClass();
			 $id= $row['id'];
			 list($numTrade,$numLoss,$allActionReason,$balance,$objTrade)=$clsTrade->CalWinByID($id) ;
			 $lossNum[$numLoss]++ ;
			 if ($numLoss >= 6 ) {    
			   echo '<br><span style="color:red">' . $loopNo. '-> ID='. $id  . ' NumLoss = ' . $numLoss  . '</span>';
			 } else {
			   echo  '<br>'.$loopNo. '-> ID='. $id  . ' NumLoss = ' . $numLoss  . '';
		 	 }
			 print_r($objTrade);
	         $loopNo++ ;
			 
		 }
	      
	     /* สร้าง  Object ตอบกลับเป็น  Json */
		 $myObj = new stdClass();
	     $myObj->result = 'Success' ;
	     //$myObj->json = $mindmapData ;
  
	     $myJSON = json_encode($myObj, JSON_UNESCAPED_UNICODE);
	     echo $myJSON;
  

} // end function


function AnalyLoss2($data) { 


require_once("newutil2.php"); 
require_once("clsTrade.php"); 
$clsTrade = new clsTrade;
$pdo=getPDONew();

$tablename = 'AnalyEMATmp';

$sql = 'select min(timefrom) as minTime,max(timefrom) as maxTime from ' . $tablename . ' '; 
$params = array();
$row=pdogetRowSet($sql,$params,$pdo);
$startTrade = $row['minTime'] ;
$stopTrade = $row['maxTime'] ;
$timeframe =  $data['timeframe'];

$useExtra1 = $data['useExtra1'] ;
$useExtra2 = $data['useExtra2'] ;

$sql = 'select * from ' . $tablename . ' '; 
$params = array();
$rs= pdogetMultiValue2($sql,$params,$pdo) ;


$allTradeList = array();
$maxLoss = 0 ; $totalTrade= 0 ;
$idMaxLoss = '';
$lossList = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$TradeObjList = array();

while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
   $id= $row['candleID'];
   $OneTradeObj  = new stdClass();
   $OneTradeObj->id= $id;
   //echo "Start Trade ID= ". $row['candleID'];
   list($numTrade,$numLoss,$allActionReason,$balance,$timeframe,$objTrade)=$clsTrade->CalWinByID($id,$useExtra1,$useExtra2) ;
   //echo $objTrade->numLoss . '<br>';
   $totalTrade++ ;
   $numLoss = (int)$objTrade->numLoss ;
   $lossList[$numLoss]++ ;
   if ($numLoss > $maxLoss) {
	   //echo "ssss";
	   $maxLoss = $numLoss;
	   $idMaxLoss .= $objTrade->MainID .'=>'.$row['minuteno'] . ' ; ';
   }
   $OneTradeObj->minuteno  = $row['minuteno'] ;
   $OneTradeObj->timeframe = $timeframe ;
   $OneTradeObj->numLoss   = $numLoss ;
   $OneTradeObj->objTrade  = $objTrade ;
   $TradeObjList[] =  $OneTradeObj ;


    
   $allTradeList[] = $objTrade ;
   
 
} // end while Loop 

$txt =  JSON_ENCODE($TradeObjList, JSON_UNESCAPED_UNICODE);
/* Save $TradeObjList  to txt File */

$myfile = fopen("newfileTrade.txt", "w") or die("Unable to open file!");
fwrite($myfile, $txt);
fclose($myfile);


if ($timeframe == '1') { $fieldName = 'TF1' ;$fieldName2 = 'TF1_MaxLoss' ; }
if ($timeframe == '5') { $fieldName = 'TF5' ;$fieldName2 = 'TF5_MaxLoss' ;  }
if ($timeframe == '10') { $fieldName = 'TF10' ; $fieldName2 = 'TF10_MaxLoss' ;  }
if ($timeframe == '15') { $fieldName = 'TF15' ; $fieldName2 = 'TF15_MaxLoss' ;  }
if ($timeframe == '30') { $fieldName = 'TF30' ; $fieldName2 = 'TF30_MaxLoss' ; }
if ($timeframe == '60') { $fieldName = 'TF60' ;$fieldName2 = 'TF60_MaxLoss' ;  }

$startDateLab = date('Y-m-d H:i:s',$startTrade) ;
$idA = $data['id'] ;
$CurPairID = 1 ;
$sql = 'select id from resultLabByTimeFrame where startDateLab=? and CurPairID=?'; 
$params = array($startDateLab,$CurPairID);
$id =pdogetValue($sql,$params,$pdo) ;
 
if ($id == -1 || $idA == 'new') {
  $sql = "REPLACE INTO resultLabByTimeFrame(StartdateLab,EnddateLab,CurPairID," . $fieldName. "," . $fieldName2 . ") VALUES(?,?,?,?,?)"; 
  $params = array(date('Y-m-d H:i:s',$startTrade),date('Y-m-d H:i:s',$stopTrade),$CurPairID,$txt,$maxLoss);
  $thisMode = 'newData';
} else {
  $sql = "UPDATE resultLabByTimeFrame SET $fieldName= ? ,$fieldName2=? where id=?";
  $params = array($txt,$maxLoss,$idA);
  $thisMode = 'UpdateData';
}


//date('Y-m-d H:i:s',$startTimestamp) ;

if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
   echo 'Error' ;
   return false;
}
if ( $thisMode == 'newData') {
   $id = $pdo->lastInsertId();
} else {
   $id = $idA ;
}









$resultTrade  = new stdClass();

$resultTrade->id = $id ;
$resultTrade->totalTrade = $totalTrade ;
$resultTrade->timeframe = $timeframe ;
$resultTrade->startTime = date('Y-m-d H:i:s',$startTrade);
$resultTrade->stopTime = date('Y-m-d H:i:s',$stopTrade);

$resultTrade->lossList = implode(',', $lossList);
$resultTrade->maxLoss = $maxLoss ;
$resultTrade->idMaxLoss = $idMaxLoss ;
$resultTrade->allTradeList = $allTradeList ;



echo JSON_ENCODE($resultTrade, JSON_UNESCAPED_UNICODE);



} // end function


function checkProfitForex() { 


         require_once("newutil2.php"); 
         require_once("clsTrade.php"); 
		 $clsTrade = new clsTrade;
		 $pdo=getPDONew();

		 
		 $tablename= 'AnalyEMATmp';		 
		 $sql = "select * from " . $tablename ; 
		 $params = array();



} // end function

function getMonth($data) { 
	     
		 $dbname = 'ddhousin_lab' ;
		 $pdo = getPDO2($dbname,true)  ;
		 //$pdo->exec("set names utf8mb4") ;
		 
		 $sql = 'SELECT distinct(month(timefrom_unix)) as MonthSelected FROM `RawData` WHERE year(timefrom_unix) = ?'; 
		 $params = array($data['yearSelected']);
		  
		 $rs= pdogetMultiValue2($sql,$params,$pdo) ;		  
		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) { ?>
	      <button type='button' id='' class='mBtn' onclick="fff()"><?=$MonthSelected?></button> 
				    
		 <?php }
		 // แสดงผลข้อมูลในรูปแบบ JSON

} // end function

function viewlossDetail($data) { 

	     $id = $data['id'] ;
		 $numLossView = $data['numLossView'] ;
		 $newUtilPath = '/domains/thepapers.in/private_html/';
		 require_once("newutil2.php"); 

		 $fieldTimeFrame  = "TF" . $data['timeframe'] ;
		 $numLoss = $data['numLossView'] ;
		 $resultArray  = array();
 
		 $pdo = getPDONew();
		 $sql = "select $fieldTimeFrame from resultLabByTimeFrame where id=?"; 
		 $params = array($id);
		 $sDetailTrade = pdogetValue($sql,$params,$pdo) ;
		 //echo $sDetailTrade ; return;
         $obj = JSON_DECODE($sDetailTrade,true) ;
		 for ($i=0;$i<=count($obj)-1;$i++) {
			 if ($obj[$i]['numLoss'] == $numLoss ) {
				 $resultArray[] = $obj[$i]['objTrade']['noteTrade'];
			 }		    
		 }

		 echo JSON_ENCODE($resultArray, JSON_UNESCAPED_UNICODE);

		  
		 




} // end function

function getActionForTrade($data) { 

	     //echo "getActionForTrade"; 
require_once("newutil2.php"); 
require_once("../deriv/api/clsCandlestickIndy.php"); 
require_once("clsTrade.php"); 

$pdo= getPDONew() ;



$curpair = $data['curpair'] ;
$clsCandlestickIndy = new CandlestickIndy($curpair);
$clsTrade = new clsTrade();

$candleDataList = $data['candleData'] ;

$tableName = 'AnalyEMATmp';
$macdThershold = 0.5;
$lastMacdHeight = 0.001;
$sql = "select * from $tableName where candleID=?"; 
$params=array(15);
$row=pdogetRowSet($sql,$params,$pdo);

$analyData = $clsCandlestickIndy->CreateAnalyisData($candleDataList)  ;
$dataTxt = JSON_ENCODE($analyData);
$clsCandlestickIndy->InsertData_AnalyEMATmp($dataTxt);

list($thisAction,$forecastColor,$forecastClass,$ActionClass,$actionReason,
	  $nextColorClass,$remark,$macd,$macdConver,$slopeValue,$slopeDirection,$pipSize,$delTapip) =$clsTrade->getActionFromIDVer2($pdo,$tableName,$row,$macdThershold,$lastMacdHeight);

//echo JSON_ENCODE($analyData);
echo $thisAction;

} // end function


?>