<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//  testCut.php
/*
1.เวลาเริ่มต้น
2.เวลาสิ้นสุด 
3.warnCode ที่ได้ 
*/

require_once('newDerivObject/TradingConditionAnalyzer.php');  

$candleDataObj =  getCandleData(1) ;
$nBack = 1;
$clsRisk = new TradingConditionAnalyzer($candleDataObj);
$index = count($candleDataObj)-2;
$result=$clsRisk->analyzeTradingConditions($index, $tradeDirection = 'long') ;
//print_r($result);
echo '<hr>';
echo "<h2>ข้อมูล Lab -- newDerivObject/rawData.json</h2>";
echo 'จำนวนข้อมูล ' . count($candleDataObj)  . '<br>';
echo 'เวลา  :: '  . date('Y-m-d H:i:s',$result['timestamp']) . '<br>';
echo 'ราคาปิด  :: '  . $result['price'] . '<br>';
echo 'wCode  :: '  . $result['AllWCode']. '<br>' ;


$candleDataObj =  getCandleData(2) ;
$index = count($candleDataObj)-1;
$clsRisk = new TradingConditionAnalyzer($candleDataObj);
$result=$clsRisk->analyzeTradingConditions($index, $tradeDirection = 'long') ;
//print_r($result);
echo '<hr>';
echo "<h2>ข้อมูล Real Trade -- rawDataVerTrade.json</h2>";
echo 'จำนวนข้อมูล ' . count($candleDataObj)  . '<br>';
echo 'เวลา  :: '  . date('Y-m-d H:i:s',$result['timestamp']) . '<br>';
echo 'ราคาปิด  :: '  . $result['price'] . '<br>';
echo 'wCode  :: '  . $result['AllWCode']. '<br>' ;





function getCandleData($sType){


 $st = "";   
 if ($sType === 1) {
	 $sFileName = 'newDerivObject/rawData.json';
 }
 if ($sType === 2) {
	 $sFileName = 'rawDataVerTrade.json';
 }

 
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file);


 $candleObj = json_decode($st,true) ;
 return  $candleObj;
  


} // end function


?>