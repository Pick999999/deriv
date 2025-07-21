<?php
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *'); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);   
	 

  // Get parameters from URL
$param1 = isset($_GET['param1']) ? $_GET['param1'] : null;
$param2 = isset($_GET['param2']) ? $_GET['param2'] : null;
$param3 = isset($_GET['param3']) ? $_GET['param3'] : null;

//getAllCurPair(); return;
//$data = json_decode(file_get_contents('php://input'), true);

 // echo "param1="  . $param1 . ' <br>param2= ' . $param2 . ' <br>param3= ' . $param3 ;
//  echo "Post1= " . $_GET['timefromUnix'];

  //header('Access-Control-Allow-Methods: GET, POST');
  //header('Access-Control-Allow-Origin: *'); 
  //ob_start();
  //https://www.thaicreate.com/community/login-php-jquery-2encrypt.html
  //https://www.cyfence.com/article/design-secured-api/
  
     
	 
	 
	 
	 
     //$data = json_decode(file_get_contents('php://input'), true);
     if ($param1) {
        /*
        if ($param1 == 'getcandle') { getCandle('parent'); }
		if ($param1 == 'getema') { getema(); }
		if ($param1 == 'CreateAnalysisData') { CreateAnalysisData(); }
		*/
		if (strtolower($param1) == 'getallcurpair'  ) { getAllCurPair(); }
        return;
     }
  
function getCandle($callfrom) { 
  
	     $ErrMsg  = '';
		 $timeframe = (int)$_GET["timeframe"];
		 $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	     require_once($newUtilPath ."eaforex/clsCandlestickIndy.php"); 
		 $clsCandlestickIndy = new  CandlestickIndy('curpair',$timeframe	) ;
	     

		 $dbname = 'ddhousin_lab' ;
		 $pdo = getPDO2($dbname,true)  ;
		 
		 // FROM_UNIXTIME(timefrom) แปลง กรณีที่ timefrom ถูกเก็บเป็นแบบ int
		 
		 $sql = 'select * from RawData where date(FROM_UNIXTIME(timefrom)) >= ? and 
         date(FROM_UNIXTIME(timefrom)) <= ? '; 

		 $sql = 'select id,timefrom,timefrom as timestamp,open,close,max as high,min as low,volume from RawData where timefrom_unix >= ? and  timefrom_unix <= ? '; 


		 $params = array($_GET['timefromUnix'],$_GET["endtimefromUnix"]);
		 
		 $rs= pdogetMultiValue2($sql,$params,$pdo) ;		 
		 $oneMinuteCandles = [];
		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		     $dataObj = new stdClass();
		     foreach ($row as $key => $value) {
		         $dataObj->$key = $value;
		     }
		     $oneMinuteCandles[] = $dataObj;
				    
		 }

		 $json = json_encode($oneMinuteCandles);
         $oneMinuteCandles2 = json_decode($json, true); // Decode as associative array first



		 //$oneMinuteCandles2 =  array($oneMinuteCandles); //(array)$oneMinuteCandles;
		 $aggregatedCandles= $clsCandlestickIndy->aggregateCandles($oneMinuteCandles2,$timeframe) ;

		 if ($callfrom =='parent') {		 
  		   // แสดงผลข้อมูลในรูปแบบ JSON
		   echo json_encode($aggregatedCandles);
		 }

		 return $aggregatedCandles ;
		  
  
	     
  
} // end function

function getEMA() { 
         $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	     require_once($newUtilPath ."eaforex/clsCandlestickIndy.php"); 
		 $timeframe = $_GET["timeframe"];
		 $clsCandlestickIndy = new  CandlestickIndy('curpair',$timeframe	) ;
	     
         $aggregatedCandles = getCandle('ema') ;
		 $period = $_GET["period"];
         $closeprices = $clsCandlestickIndy->get_close_prices($aggregatedCandles) ;
		 $ema = $clsCandlestickIndy->calculateEMAStylePython($closeprices, $period)  ;

		 $lastIndex = count($aggregatedCandles)-1 ;
		 for ($i=0;$i<=count($aggregatedCandles)-1;$i++) {
		    $aggregatedCandles[$i]['ema'] = $ema[$i] ;
		 }

		 $result = new stdClass();
         $result->timeData = $aggregatedCandles[0]['fromTime'] . ' ถึง  ' .$aggregatedCandles[$lastIndex]['fromTime'] ;
		 $result->timeframe = $timeframe  ;
		 $result->period = $period  ;
		 $result->candledata = $aggregatedCandles  ;

		 $result->ema = $ema  ;
		 



		 echo json_encode($result, JSON_UNESCAPED_UNICODE);


} // end function



function MyPlan() {  ?>
 <h1>งานเตรียมข้อมูล (Service) </h1>
 <ol>
  <li>เรียก api ในรูปแบบ lovetoshopmall.com/api/candledata/param2</li>
  <li>การ return data กลับไปในรูป  json string</li>
  <li>ปลายทาง จะเอามาใช้ในรูปแบบ รับ json string แล้วแปลงให้เป็น json object  </li>
  <li> </li>
 </ol>
ประเภทงาน
 1.ตัวอย่างต้องการงาน 
    1.1 ดึงข้อมูลแล้วส่ง Output กลับเป็น Object การแสดงผล ให้ใช้ javascript แปลงเป็น  table
	1.2 ดึงข้อมูลแล้ว ประมวลผลก่อน แล้วส่ง Output กลับ เป็น  html
	1.3 

   
 

 
<?php
} // end function

function CreateAnalysisData() { 

         $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	     require_once($newUtilPath ."eaforex/clsCandlestickIndy.php"); 
		 $timeframe = $_GET["timeframe"];
		 $clsCandlestickIndy = new  CandlestickIndy('curpair',$timeframe) ;
	     
         $aggregatedCandles = getCandle('CreateAnalysis') ;
		 
		 $analysisObject = $clsCandlestickIndy->CreateAnalyisData($aggregatedCandles);
		 $st = json_encode($analysisObject, JSON_UNESCAPED_UNICODE) ;
		 $st = substr($st,1,strlen($st)-1);
		 $st = substr($st,0,strlen($st)-1);
         $st = '[' .   $st . ']';
		 /*$stObj = JSON_DECODE($st);
		 for ($i=0;$i<=count($stObj)-1;$i++) {
		    
		 }
		 */
		 echo $st ;

		 //echo json_encode($analysisObject, JSON_UNESCAPED_UNICODE);




} // end function

function getAllCurPair() { 
/*
$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
require_once($newUtilPath ."src/newutil.php"); 
$dbname = 'ddhousin_lab' ;
$pdo = getPDO2($dbname,false)  ;
*/




//ftp://thepaper@thepapers.in:2002/domains/thepapers.in/private_html/iqlab/newutil2.php

$newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/';
require_once($newUtilPath ."iqlab/newutil2.php"); 
$pdo = getPDONew();
$sql = 'select * from curpairMaster'; 
$params = array();

$rs= pdogetMultiValue2($sql,$params,$pdo) ;
while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
    $dataObj = new stdClass();
    foreach ($row as $key => $value) {
        $dataObj->$key = $value;
    }
    $results[] = $dataObj;		    
}
//print_r($results);
// แสดงผลข้อมูลในรูปแบบ JSON
//echo json_encode($results);
echo json_encode($results, JSON_UNESCAPED_UNICODE);

 
 



} // end function


 
/*
เขียนคำสั่ง  mql5 ให้ หา ema3,ema5 ของ  eurusd และเข้าทำการ Trade เมื่อ  ema3 มากกว่า ema5 หรือ เมื่อ ema5 > ema3 และ ยุติ order เมื่อ มีการ สลับกัน ของ เส้น ema3 และ ema5
*/




?>