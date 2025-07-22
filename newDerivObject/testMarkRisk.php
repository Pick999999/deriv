<style>
 td { text-align:center }
 .bgBlue { background:#99ccff ; color:white; } 
 .bgRed { background:#ff0080 ; color:white; }
 .bgGray { background:gray ; color:white; }
</style>
<link href="" rel="stylesheet">
<?php
  //testMarkRisk.php
  ob_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
 
  $st = "";   
  

  $sFileName = 'dataTest.json';
  $file = fopen($sFileName,"r");
  while(! feof($file))  {
    $st .= fgets($file) ;
  }
  fclose($file);

  $jDataA = JSON_DECODE($st,true) ;
  $jData =  $jDataA['AnalysisData'] ;
  $RiskData =  $jDataA['highRiskPeriods'] ;
  $tradeNo = 0 ;
  $stTable= '';
  $stTable .= '<table border=1><tr><td>TradeNo</td><td>Time Trade</td>';
  $stTable .= '<td>Warn Code</td>';
  $stTable .= '<td>Open Price</td>';
  $stTable .= '<td>Close Price</td>';
  $stTable .= '<td>NumRisk</td>';
  $stTable .= '<td>TurnType</td>';
  $stTable .= '<td>thisColor</td>';
  $stTable .= '<td>SuggestColor</td>';
  $stTable .= '<td>ResultColor</td><td>Result</td><td>Win Con</td><td>Loss Con</td>';
  $stTable .= '<td>MoneyTrade</td><td>Profit</td><td>Balance</td>';
  $stTable .= '</tr>';

 
  $numWin= 0; $numLoss= 0; $winCon = 0 ; $lossCon = 0 ; 
  $balance = 0 ; $MoneyMul = 10 ;
  $MoneyTrade = 1 * $MoneyMul; 
  $tradeList = array();
  for ($i=0;$i<=count($jData)-2;$i++) {
	  if ($jData[$i]['numRisk'] <= 12 ) {
		  $tradeNo++ ;
		  $numRisk = $jData[$i]['numRisk'] ;
		  $WarnCode =  '';
		  for ($i2=0;$i2<=count($RiskData)-1;$i2++) {
			  if ($RiskData[$i2]['timestamp'] === intval($jData[$i]['timestamp'])) {
				  $WarnCode = $RiskData[$i2]['AllWcode'] ;
			  }
		     
		  }
		  

		  $ClosePrice = $jData[$i]['close'] ;
		  $OpenPrice = $jData[$i]['open'] ;
		  $objTrade= OnTrade($jData,$i,$tradeNo,$stTable,$numWin,$numLoss,$winCon,$lossCon,
		  $MoneyTrade,$balance,$numRisk,$OpenPrice,$ClosePrice,$WarnCode);
		  if ($lossCon==0) {  $MoneyTrade= 1 *$MoneyMul; }
		  if ($lossCon==1) {  $MoneyTrade= 2* $MoneyMul; }
		  if ($lossCon==2) {  $MoneyTrade= 4 *$MoneyMul; }
		  if ($lossCon==3) {  $MoneyTrade= 8* $MoneyMul; }
		  if ($lossCon==4) {  $MoneyTrade= 16 * $MoneyMul; }
		  $tradeList[] = $objTrade;
		  
	  }
      
  }
  $stTable .= '</table>' ;
  echo 'Total Win = ' . $numWin . '<br>';
  echo 'Total Loss = ' . $numLoss . '<br>';
  echo "Balance Win/Loss = " . $numWin-$numLoss . '<br>';
  echo "Balance Bath= " . ($balance)*33 . '<br>';

  echo $stTable;

  //SaveTrade($tradeList);


function OnTrade($jData,$i,$tradeNo,&$stTable,&$numWin,&$numLoss,&$winCon,&$lossCon,$MoneyTrade,&$balance,$numRisk,$OpenPrice,$ClosePrice,$WarnCode ) {

         if ($jData[$i+1]['TurnMode999'] === 'TurnDown') {
           $suggestColor = "Red";
         } else {
	       $suggestColor = "Green";
		 }
		 $resultColor = $jData[$i+1]['thisColor'];
		 if (trim($resultColor) ==='') {
			 $result = 'Wait';
		 } else {
			 $result = '';
		 }
		 if ($result !=='Wait') {		 
			 if ($suggestColor === $resultColor) {
				// echo $tradeNo . ' ) '. $jData[$i]['timefrom_unix'].  '-->Win' . '<br>';
				 $result = 'Win'; $numWin++ ; $winCon++; $lossCon = 0;
				 $profit = $MoneyTrade *0.9 ;
				 $clsName = 'bgBlue';
			 } else {
				//echo $tradeNo . ' ) '. $jData[$i]['timefrom_unix'].  '-->Loss' . '<br>';
				$result = 'Loss'; $numLoss++ ; $winCon=0 ; $lossCon++ ;
				$profit = $MoneyTrade *-1 ;
				$clsName ='bgRed' ;
			 }
			 $balance = $balance+$profit ;
			 if ($numRisk > 2) {
				 $clsName ='bgGray' ;
			 }
		 }

		 $stTable .= '<tr class="'. $clsName . '"><td>' .$tradeNo. '</td>';
		 $turnMode = trim($jData[$i+1]['TurnMode999']);
		 if ($turnMode === '') {
			 if ($jData[$i]['emaAbove']===3) {
				 $turnMode = 'TurnUp';
			 } else {
                 $turnMode = 'TurnDown';
			 }            
		 }
		 
		 
		 $stTable .= '<td>'. $jData[$i]['timefrom_unix'] .'</td>';		 
         $stTable .= '<td>'. $WarnCode .'</td>';
		 $stTable .= '<td>'. $OpenPrice .'</td>';
		 $stTable .= '<td>'. $ClosePrice .'</td>';
		 $stTable .= '<td>'. $numRisk .'</td>';
         $stTable .= '<td>' .$turnMode .'</td>';
		 $stTable .= '<td>' .$jData[$i]['thisColor'] .'</td>';
		 $stTable .= '<td>' . $suggestColor .'</td>';
         $stTable .= '<td>' . $resultColor . '</td>';
		 $stTable .= '<td>' . $result . '</td>';
		 $stTable .= '<td>' . $winCon . '</td>';
		 $stTable .= '<td>' . $lossCon . '</td>';
		 $stTable .= '<td>' . $MoneyTrade . '</td>';
		 $stTable .= '<td>' . $profit . '</td>';
		 $stTable .= '<td>' . $balance . '</td>';
		 $stTable .= '</tr>';

		 $sObj = new stdClass();
		 $sObj->tradeNo = $tradeNo ;
		 $sObj->timefrom_unix = $jData[$i]['timefrom_unix'];
		 $sObj->TurnMode999 = $jData[$i]['TurnMode999'];
		 $sObj->thisColor = $jData[$i]['thisColor'];
		 $sObj->suggestColor = $suggestColor;
		 $sObj->resultColor = $resultColor;


		 $sObj->WinStatus = $result;
		 $sObj->WinCon = $winCon;
		 $sObj->LossCon = $lossCon;
		 $sObj->MoneyTrade = $MoneyTrade;
		 $sObj->profit = $profit ;
		 $sObj->balance = $balance ;

		 return $sObj ;









} // end function

 
function SaveTrade($tradeList){


//$newUtilPath = '/domains/thepapers.in/private_html/';
//require_once($newUtilPath ."iqlab/newutil2.php"); 
require_once("newutil2.php"); 


$pdo = getPDONew();
$sql = 'REPLACE INTO DetailTradeLab(
CurPairCode, DayTrade, startTimeTrade, 
tradeno, TimeTrade, TurnType, 
thisColor, SuggestColor, resultColor,
tradeResult, winCon, lossCon, 
MoneyTrade, profit, Balance)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'; 

for ($i=0;$i<=count($tradeList)-1;$i++) {
   
   $params = array(
   $curpairCode,$dayTrade,
   $tradeList[$i]['timefrom_unix'],
   $tradeList[$i]['timefrom_unix'],
   $tradeList[$i]['tradeNo'],

   
   );
   if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
      echo 'Error' ;
      return false;
   }
}

 



} // end function



?>