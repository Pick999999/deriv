<?php
  ob_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  Case2(); 
  return;


function Case1() { 
global $dbname;
global $pdo ;




//  testClsTrade.php
require_once("newutil2.php"); 
require_once("clsTrade.php"); 
$clsTrade = new clsTrade;
$pdo=getPDONew();

$tablename = 'AnalyEMATmp';

$sql = 'select * from ' . $tablename . ' limit 5,30'; 

$sql = 'select min(timefrom) as minTime,max(timefrom) as maxTime from ' . $tablename . ' '; 
$params = array();
$row=pdogetRowSet($sql,$params,$pdo);
$startTrade = $row['minTime'] ;
$stopTrade = $row['maxTime'] ;



$sql = 'select * from ' . $tablename . ' '; 
$params = array();
$rs= pdogetMultiValue2($sql,$params,$pdo) ;
$timeframe = 1;
$allTradeList = array();
$maxLoss = 0 ; $totalTrade= 0 ;
$idMaxLoss = '';
$useExtra1 = true;$useExtra2 = true;
$lossList = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
   $id= $row['candleID'];
   //echo "Start Trade ID= ". $row['candleID'];
   list($numTrade,$numLoss,$allActionReason,$balance,$timeframe,$objTrade)=$clsTrade->CalWinByID($id,$useExtra1,$useExtra2) ;
   //echo $objTrade->numLoss . '<br>';
   
   $totalTrade++ ;
   $numLoss = (int)$objTrade->numLoss ;
   $lossList[$numLoss]++ ;
   if ($numLoss > $maxLoss) {
	   //echo "ssss";
	   $maxLoss = $numLoss;
	   $idMaxLoss = $objTrade->MainID;
   }
    
   $allTradeList[] = $objTrade ;
   
    
		    
/*
//print_r($objTrade);
echo '<hr>';
//for ($i=0;$i<=count($objTrade)-1;$i++) {
echo 'Main ID = ' . $objTrade->MainID . '<br>';
for ($i=0;$i<=count($objTrade->noteTrade)-1;$i++) {
	
   echo  '&nbsp;&nbsp;&nbsp;' . $objTrade->noteTrade[$i]->tradeOnID . '<br>';
   echo  '&nbsp;&nbsp;&nbsp;' . $objTrade->noteTrade[$i]->forecastColor . '<br>';
   echo  '&nbsp;&nbsp;&nbsp;' . $objTrade->noteTrade[$i]->resultColor . '<br>';
   echo  '&nbsp;&nbsp;&nbsp;' . $objTrade->noteTrade[$i]->winStatus . '<br>';
   echo  '&nbsp;&nbsp;&nbsp;Num Loss =' . $objTrade->noteTrade[$i]->numLoss . '<hr>';
   
}
// แสดงผลข้อมูลในรูปแบบ JSON
*/
} // end while Loop 

$resultTrade  = new stdClass();
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

function Case2() { 

require_once("newutil2.php"); 
require_once("clsTradeVer0/clsTradeVer_Object.php"); 
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
 for ($i=1;$i<=count($sObj)-1;$i++) {
     $thisIndex = $i ;
	 echo 'ThisIndex = ' . $thisIndex .'<br>';

	 $AnalyObj = $sObj[$thisIndex] ;
	// list($action,$actionCode) = $clsTrade->getActionClassV2FromlabVer2($AnalyObj) ; 
	//echo "Action=" . $action ;

	 require_once('clsTradeVer0/getActionFromIDVerObject.php');
	 list($thisAction,$actionReason) = getActionFromIDVerObject($AnalyObj ,$macdThershold=0.5,$lastMacdHeight=0);

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

     
	 echo "<h2>". $winStatus . '</h2><hr>';

 } 
 echo '<h2>Max Loss Con =' . $maxLossCon . '</h2>' ;

} // end function


?>