<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);  

$CandleObj = getCandleObject() ;
echo "total Candle=" . count($CandleObj) . '<hr>';

$start = 2 ; 
echo 'Ema Above AT Start=' . $CandleObj[$start]['emaAbove']  .'<br>';
//for ($i=$start;$i<=count($CandleObj)-1;$i++) {
$i = $start;
$thisIndex= $start ;
$i2 =0 ; $grandtotal = 0 ;
while ($i <= count($CandleObj)-2) {

  $startOfLoop = $i ;
  list($lastNo,$list,$balance) = getInterval($CandleObj,$thisIndex) ; 
  $grandtotal = $grandtotal + $balance;
  $lastIndex = count($list)-1 ;

  if ($balance < 0) {
    echo '<span style="color:red">';
    echo $startOfLoop. ' ถึง ' . $lastNo. '=' . ( $lastNo-$startOfLoop) . ' รายการ  '; 
	echo $CandleObj[$startOfLoop]['timefrom_unix'] . ' ถึง  ';
	echo $CandleObj[$lastNo]['timefrom_unix'] . ' = ' .$CandleObj[$lastNo]['emaAbove'] .  ' Balance = ' . $balance . ' Grand ='.$grandtotal . '</span><br>';
	  
  } else {
    echo $startOfLoop. ' ถึง ' . $lastNo. '=' . ( $lastNo-$startOfLoop) . ' รายการ  '; 
	echo $CandleObj[$startOfLoop]['timefrom_unix'] . ' ถึง  ';
	echo $CandleObj[$lastNo]['timefrom_unix'] . ' = ' .$CandleObj[$lastNo]['emaAbove'] .  ' Balance = ' . $balance . ' Grand ='.$grandtotal . '<br>';
  }
  $thisIndex= $lastNo ;
  $i= $lastNo ;
  $i2++ ;
  if ($i2 > count($CandleObj)-2) {
	  break;
  }
  
} 

echo '<h2>Grand Total = ' . $grandtotal . '</h2>'  ;


function getInterval($CandleObj,$start) {

    $checkEMA = $CandleObj[$start]['emaAbove'] ;
	$list = array();
	$thisMoney  = 1 ;$profit =  0 ; $balance = 0 ;
	$lossCon = 0 ;
	$MoneyTradeAr = array(1,2,6,18,54,62);
    for ($i=$start;$i<=count($CandleObj)-1;$i++) {       

		if ($CandleObj[$i]['emaAbove'] === $checkEMA) {
			if ($CandleObj[$i]['emaAbove'] ==='3') {
				$suggestColor = 'Green';
			} else {
                $suggestColor = 'Red';
			}
			$resultColor = getNextColor($CandleObj,$i);
			$profit =  0 ;
			$thisMoney = $MoneyTradeAr[$lossCon];
			if ($suggestColor === $resultColor) {
				$profit = $thisMoney  *0.95 ;
				$lossCon= 0 ;
			} else  {
               $profit = $thisMoney  * -1 ;
			   $lossCon++;
		    }
			$balance = $balance + $profit;
            
			$sTmp = new stdClass();
			$sTmp->no = $i; 
			$sTmp->timefrom_unix = $CandleObj[$i]['timefrom_unix']; 
            $sTmp->emaAbove = $CandleObj[$i]['emaAbove'] ; 
			$sTmp->profit = $profit ;
			$sTmp->balance = $balance ;
			$list[] = $sTmp ;
		} else {
			$lastno = $i ;
			break ;
		}
	}
	return array($lastno,$list,$balance) ;

} // end function

function getNextColor($CandleObj,$index) {

         $nextColor = $CandleObj[$index+1]['thisColor'];
		 return $nextColor ;

 



} // end function






function getCandleObject() {

	
	 $st = "";   
	 
	 $sFileName = 'newDerivObject/AnalyDataBig.json';
	 $file = fopen($sFileName,"r");
	 while(! feof($file))  {
	   $st .= fgets($file) ;
	 }
	 fclose($file);

	 $candleObj = JSON_DECODE($st,true);
	 return $candleObj ;



} // end function


?>