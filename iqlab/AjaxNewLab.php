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
        
        if ($data['Mode'] == 'findloss') { findloss($data); }

		if ($data['Mode'] == 'SaveCalendar') { SaveCalendar($data); }
		if ($data['Mode'] == 'SaveCandle') { SaveCandle($data); }

		if ($data['Mode'] == 'getAnalyticCandleValue') { getAnalyticCandleValue($data); }

		
		
        return;
     }
  
  function findloss($data) { 
  
	     require_once("newutil2.php"); 
         require_once("clsTradeVer0/clsTrade.php"); 
		 $clsTrade = new clsTrade;
		 $pdo=getPDONew();

		 /*$tablename= 'AnalyEMA_HistoryForLab_5M';
		 echo '<h1>ข้อมูล วันที่ '. $data['datecheck'] . ' TimeFrame 5 M </h1>';
		 */
         $timeframe  = 15 ; 
		 if ($timeframe == 5) {
			 $tablename= 'AnalyEMA_HistoryForLab_5M';
		 } 
		 if ($timeframe == 15) {
			 $tablename= 'AnalyEMA_HistoryForLab';
		 } 
		 
		 echo '<h1>ข้อมูล วันที่ '. $data['datecheck'] . ' TimeFrame ' . $timeframe .'M </h1>';

		 $sql = "select * from " . $tablename . " where DATE(timefrom_unix)=?"; 
		 $params = array($data['datecheck']);
		 //echo "$sql" . $data['datecheck'] ;
		 
		 
         $rs=pdogetMultiValue2($sql,$params,$pdo) ;
		 $lossNum = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		 $loopNo = 0 ;
		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) { 
			 $id= $row['id'];
			 list($numTrade,$numLoss,$allActionReason,$balance)=$clsTrade->CalWinByID($id,$vername='Ver2',$timeframe) ;
			 echo  'ID=' . $id . "-- Num Loss = ->" . $numLoss . '<hr>';
			 
			 $lossNum[$numLoss]++ ;
			 if ($numLoss >= 6 ) {    
			   //echo '<br><span style="color:red">' . $loopNo. '-> ID='. $id  . ' NumLoss = ' . $numLoss  . '</span>';
			 } else {
			   //echo  '<br>'.$loopNo. '-> ID='. $id  . ' NumLoss = ' . $numLoss  . '';
		 	 }
	         $loopNo++ ;
		 }
		 echo '<hr>';
		 $totalData= $rs->rowCount();
         $st ='ข้อมูลวันที่ ' . $data['datecheck'] . ' จำนวนข้อมูล ทั้งหมด ' . $rs->rowCount(). '<hr>';

		 $st .='<table border=1>';
         $st .='<tr>';
         $st .='<td>Loss No</td>';
		 for ($i=0;$i<=count($lossNum)-1;$i++) {
		     $st .='<td style="padding:10px">'. $i .'</td>';
		 }
		 $st .='</tr>';
		 $st .='<tr>';
		 $st .='<td>จำนวน </td>';
		 for ($i=0;$i<=count($lossNum)-1;$i++) {
		   if ($lossNum[$i] > 0) {			 
		     $st .='<td style="padding:10px">'. $lossNum[$i] .'</td>';
		   } else {
             $st .='<td style="padding:10px"> &nbsp;</td>';
		   }
		 }
		 $st .='</tr>';
		 $st .='</table>';
		 echo $st ;
		 $over = 0 ;
		 for ($i=6;$i<=count($lossNum)-1;$i++) {
			 $over = $over + $lossNum[$i] ;		    
		 }
		 $st2 = '  จำนวนข้อมูล Loss > 6 = ' . $over.  '/'.$totalData ;
         $st2 .= ' เปอร์เซนต์  Loss = ' . 100* (round($over/$totalData,2)) . ' %';
		 echo $st2 ;
		 echo "<h3>แนวคิด อีกอย่างคือ เมื่อ Loss =4 แล้วให้หาว่า";
		 echo "หลังจาก เว้นไปสัก 5 นาที แล้วมาเริ่มเงินที่ 10";
		 echo " USD จะขาดทุนไหม</h3>";

		 //print_r($lossNum);

return; 
		 
$id= 524119+62 ; 

$loopNo = 1 ;
for ($i=$id;$i<=$id+62;$i++) {   
	$sql = "select * from AnalyEMA_HistoryForLab_5M where id=$i"; 
	$row = getData_Row($sql) ;
	//echo $row->id ;
	$id= $row->id;

	$clsTrade = new clsTrade;

	list($numTrade,$numLoss,$allActionReason,$balance)=$clsTrade->CalWinByID($id,$vername='Ver2') ;

    if ($numLoss >= 6 ) {    
	  echo '<br><span style="color:red">' . $loopNo. '-> ID='. $id  . ' NumLoss = ' . $numLoss  . '</span>';
	} else {
      echo  '<br>'.$loopNo. '-> ID='. $id  . ' NumLoss = ' . $numLoss  . '';
	}
	$loopNo++ ;

}

  
  } // end function


function SaveCalendar($data) { 

   require_once("newutil2.php"); 
   $dateData = JSON_DECODE($data['dataCalendar'],true);
   $yearData = $data['year'];
   $pdo=getPDONew();
   $sql='REPLACE INTO curpairCalendar(id,year, month, dayString, monthString,weekno, dateCandle) VALUES (?,?,?,?,?,?,?)' ;
  // echo  JSON_ENCODE($dateData[0]);
   //print_r($dateData) ;
   $ss= $dateData[0] ;
   echo JSON_ENCODE($ss);
   //return;


   for ($i=0;$i<=count($dateData)-1;$i++) {
      $params = array(
      $dateData[$i]['dayno'],
      $yearData,  	  
	  'm',
	  $dateData[$i]['day'],
	  $dateData[$i]['month'],
      $dateData[$i]['weekno'],
      $dateData[$i]['date']
	  ); 
	  if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
		 echo 'Error' ;
		 return false;
	  }  
   }
  // $pdo->commit();



} // end function


function SaveCandle($data){ 

   require_once("newutil2.php");   
   $pdo=getPDONew(); 

   $sql = 'select count(*) as totalRec from RawData'; 
   $params = array();
   $TotalRec1 = pdogetValue($sql,$params,$pdo) ;



   $curpair = $data['curpair'];
   $sql = "select id from curpairMaster where symbol =?"; 
   $params = array($curpair);
   $curpairID = pdogetValue($sql,$params,$pdo); 
   if ($curpairID == -1) {
	   echo "No Found Curpair"; return ;
   }

   //echo count($data['dataCandle1']['candles']); return;
 
   

   $dataMix   = array_merge($data['dataCandle1']['candles'],$data['dataCandle2']['candles'],
	   $data['dataCandle3']['candles'],
	   $data['dataCandle4']['candles'] );

   //echo "-->" . count($dataMix);
   /*
   $st= JSON_ENCODE($dataMix) ;
   $myfile = fopen("jsonMix.txt", "w") or die("Unable to open file!");
   fwrite($myfile, $st);
   fclose($myfile);
   return;
   */
  



// "curpair":"frxEURUSD", 
// {"close":1.03595,"epoch":1734670500,"high":1.036,"low":1.03595,"open":1.03599}
$sql='
REPLACE INTO RawData(
curpairID, timefrom_unix, timefrom, weekno,MinuteNo, 
timeto, high,low, open, close,
timeframe,pipSize ) VALUES (
?,?,?,?,?,
?,?,?,?,
?,?,?
)';

//$dataCandle = $data['dataCandle'] ;
$dataCandle = $dataMix;
$timeframe = $data['timeframe'] ;
$weekno = $data['weekno'] ;
$pipSize = $data['pipSize'] ;

for ($i=0;$i<=count($dataCandle)-1;$i++) {
   $timefromUnix = date('Y-m-d H:i:s',$dataCandle[$i]['epoch']) ;
   $MinuteNo = date('H:i:',$dataCandle[$i]['epoch']) ;
   
    
   $params=array(
    $curpairID,$timefromUnix,$dataCandle[$i]['epoch'],$weekno,$MinuteNo, 
    $dataCandle[$i]['epoch']+60,$dataCandle[$i]['high'],
    $dataCandle[$i]['low'],$dataCandle[$i]['open'],$dataCandle[$i]['close'],
    $timeframe,$pipSize);
    if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
		 echo 'Error' ;
		 return false;
    }  
	

}


$sql = 'select count(*) as totalRec from RawData'; 
$params = array();
$TotalRec2 = pdogetValue($sql,$params,$pdo) ;
echo ' จำนวน Record ' . $TotalRec1 .' => ' . $TotalRec2 ;

$sql = 'select max(weekno) as maxweek from RawData WHERE curpairID=?'; 
$params = array($curpairID);
$maxweek = pdogetValue($sql,$params,$pdo) ;
echo ' Max Week = ' . $maxweek ;








} // end function


function getAnalyticCandleValue($data) { 
 
	$id = $data['candleid'] ;

	//echo "Result->" . $id ;

	require_once("newutil2.php"); 	
	$pdo = getPDONew();
	$sql = 'select * from AnalyEMATmp where id = ? '; 
	$params = array($id);
	
	$rs= pdogetMultiValue2($sql,$params,$pdo) ;	

	$results = [];
	while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	    $dataObj = new stdClass();
	    foreach ($row as $key => $value) {
	        $dataObj->$key = $value;
	    }
	    $results[] = $dataObj;
		//print_r($dataObj) ;
			    
	}
	// แสดงผลข้อมูลในรูปแบบ JSON
	echo json_encode($results, JSON_UNESCAPED_UNICODE);

} // end function


?>