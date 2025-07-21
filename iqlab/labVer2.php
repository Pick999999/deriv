<?php

/*
แนวคิด
  1. มองย้อนกลับไป ข้างหลัง หาจุด Turn ของ  ema5 ที่ใกล้ที่สุด ว่าเป็นจุด  TurnUp หรือ Turn Down
  2. Trade ตาม Trend 
  3. จากนั้นค่อย เพิ่ม การวิเคราะห์ ด้วย ema3 

*/
header('Access-Control-Allow-Methods: GET, POST');
//header('Access-Control-Allow-Origin: *'); 
ob_start();
//https://www.thaicreate.com/community/login-php-jquery-2encrypt.html
//https://www.cyfence.com/article/design-secured-api/
?>

<?php

   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);   
   $data = json_decode(file_get_contents('php://input'), true);
   $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
   require_once("newutil2.php");
   $ErrMsg  = '';
   $dbname = 'thepaper_lab' ;
   $pdo = getPDONew()  ;
		 //$pdo->exec("set names utf8mb4") ;

   if ($data) {
      
	  
      if ($data['Mode'] == 'TradeByClass2') { 
		  $id= $data['candleid']  ;
		  $maxLoss = 0 ;
		  $numLoss = TradeByClass2($pdo,$id); 
	  }

	  if ($data['Mode'] == 'getLabByClassAll2') { getLabByClassAll2($data); }

	  if ($data['Mode'] == 'getLabByClassAll22') { getLabByClassAll22($data); }
      return;
   } else {
     //main($pdo);
   }


function getLabByClassAll22($data) { 
/*
	     "dateselected" : document.getElementById("datepicker").value ,
         "hourselected" :

*/
         $dateselectedAR = explode('/',$data['dateselected']) ;
		 $sDate = $dateselectedAR[2] .'-' .$dateselectedAR[1].'-'.$dateselectedAR[0];
		 $hourselected = $data['hourselected'] ;
		 $nexthourSelected = (int)($data['hourselected']) +1 ;

		 if ($hourselected < 10) {
            $thisHour= "0" . $hourselected . ':00:00' ;
		 } else {
           $thisHour=  $hourselected . ':00:00' ;
		 }
		 if ($nexthourSelected < 10) {
           $nexthourSelected = "0" . $nexthourSelected . ':00:00' ;
		 } else {
           $nexthourSelected =  $nexthourSelected . ':00:00' ;
		 }


		 $sDateTime1 = $sDate .' ' . $thisHour ;
		 $sDateTime2 = $sDate .' ' . $nexthourSelected ;
		 //echo $sDateTime ;
         $dbname = 'ddhousin_lab' ;
         $pdo = getPDO2($dbname,true)  ;
         //$pdo->exec("set names utf8mb4") ;
         
         $sql = 'select * from AnalyEMA_HistoryForLab where timefrom_unix >="'.$sDateTime1. '" and timefrom_unix <="'. $sDateTime2 . '" limit 0,120'; 
		 $stmt = $pdo->query($sql);
/*
		 $stmt->execute();
         $firstRow = $stmt->fetch(PDO::FETCH_ASSOC);		 
         $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $lastRow = end($rows);          
		 echo ($firstRow['id']) . '-'.($lastRow['id']);
		 
		 //$lastRecord = $results[count($results) - 1];
		 //$firstRow = $results[0];
*/
         $params = array(); 
         $rs = pdogetMultiValue2($sql,$params,$pdo) ;         
		 
		 $maxLoss = 0 ; $maxid = '';


         while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			list($myObj,$numLoss) = TradeByClass2($pdo,$row['id'],$showOutPut=false) ;
            if ($numLoss > $maxLoss) {
              $maxLoss = $numLoss ; 
			  if ($numLoss >=5) {			  
 			    $maxid .= $row['id'] .';' ;
			  }
            }
              
         }
         // แสดงผลข้อมูลในรูปแบบ JSON
         //echo json_encode($results);
         

  echo '@#<h1>Max Loss = '. $maxLoss . '::' . $maxid . '</h1>@#'; 

  $myObj = new stdClass();
  $myObj->result = 'Success' ;
  //$myObj->sDateTime = $sDateTime;
  $myObj->firstID =  $lastRow['id'] ;

  $myJSON = json_encode($myObj);   
  $myObj->maxLoss =  $maxLoss . '-' . $maxid;

  
   return;

 // echo $myJSON;
  
  



} // end function


function Conclude($obj1All,$obj2All) { 

	     $st = '<table border=1><tr>';
		 $st .= '<td>ID</td>Minute No<td></td><td>Loss Method1</td><td>Loss Method2</td>';
         $st .= '</tr>';
		// print_r($obj1All); return;


         for ($i=0;$i<=count($obj1All)-1;$i++) {
		   $st .= '</tr>';
		   $st .= '<td>' .$obj1All[$i]->startID . '</td>' ;
		   $st .= '<td>' .$obj1All[$i]->startMinute . '</td>' ;
		   $st .= '<td>' .$obj1All[$i]->numLoss . '</td>' ;
		   $st .= '<td>' .$obj2All[$i]->numLoss . '</td>' ;

           $st .= '</tr>';
            
         } 
		 $st .= '</table>';
		 echo $st ;
  



} // end function


function getLabByClassAll2($data) { 

 

} // end function



function TradeByClass2($pdo,$id) { 
		 
		 $AnalyObj = getAnalyEMA_HistoryForLabObject($pdo,$id) ;

         $startID = $AnalyObj[0]->id ;
		 $startMinute =  $AnalyObj[0]->minuteno ;
         $startColor = $AnalyObj[0]->thisColor ;
		 $thisID = $AnalyObj[0]->id ; 
		 $numTrade=  0 ; $numLoss = 0 ;
          
		 $idList = '';$actionList = '';
		 $actionCodeList = '';
		 for ($i=0;$i<=count($AnalyObj)-1;$i++) {
          
             $thisID = $AnalyObj[$i]->id ; 
			 $idList .= $thisID . ',';
             list($thisAction,$actionCode) = getActionClassV2($AnalyObj,$i) ;

			 $actionList .=  $thisAction . ',';
			 $actionCodeList .=  $actionCode . ',';

			 list($profit,$forecastColor,$numLoss) = Trade($thisAction,$AnalyObj[$i],$numLoss) ;
			 $numTrade++ ;
			 
			 if ($profit > 0) {
				 $winStatus= true;				 
			 } else {
				 $winStatus= false;
			 }
			 if ($winStatus== true) { 
                 break;
			  
			 }
		 }





	      
	     /* สร้าง  Object ตอบกลับเป็น  Json */
		 $myObj = new stdClass();
	     $myObj->result = 'Success' ;
		 $myObj->startID = $startID ;
		 $myObj->startColor = $startColor;
		 $myObj->startMinute = $startMinute ;
		 $myObj->idList = $idList ;
		 $myObj->actionList = $actionList ;
         $myObj->actionCodeList = $actionCodeList ;
	     $myObj->winonid = $thisID ;
		 $myObj->numTrade = $numLoss . ' / '.$numTrade;
		 $myObj->numLoss =  '<span style="color:red;font-size:22px">' . $numLoss . '</span>';

	     $myJSON = json_encode($myObj, JSON_UNESCAPED_UNICODE);
         // ข้อมูลที แถวเดียว แต่ ต้องทำให้เป็น Array เพื่อจะได้ for each ได้
		 $sAr = [$myObj] ;
		 Output($sAr) ;
//	     echo $myJSON; 

         return array($myObj,$numLoss) ; 
	     

} // end function

function TradeByClass2B($pdo,$id,$showOutPut) { 
		 
		 $AnalyObj = getAnalyEMA_HistoryForLabObject($pdo,$id) ;

         $startID = $AnalyObj[0]->id ;
		 $startMinute =  $AnalyObj[0]->minuteno ;
         $startColor = $AnalyObj[0]->thisColor ;
		 $thisID = $AnalyObj[0]->id ; 
		 $numTrade=  0 ; $numLoss = 0 ;
          
		 $idList = '';$actionList = '';
		 $actionCodeList = '';
		 for ($i=0;$i<=count($AnalyObj)-1;$i++) {
          
             $thisID = $AnalyObj[$i]->id ; 
			 $idList .= $thisID . ',';
            // list($thisAction,$actionCode) = getActionClassV2($AnalyObj,$i) ;
			 list($thisAction,$fcColor,$actionCode) = AnalystByBackWard($AnalyObj,$i);

			 $actionList .=  $thisAction . ',';
			 $actionCodeList .=  $actionCode . ',';

			 list($profit,$fcColor,$numLoss) = Trade($thisAction,$AnalyObj[$i],$numLoss) ;
			 $numTrade++ ;
			 
			 if ($profit > 0) {
				 $winStatus= true;				 
			 } else {
				 $winStatus= false;
			 }
			 if ($winStatus== true) { 
                 break;
			  
			 }
		 }





	      
	     /* สร้าง  Object ตอบกลับเป็น  Json */
		 $myObj = new stdClass();
	     $myObj->result = 'Success' ;
		 $myObj->startID = $startID ;
		 $myObj->startColor = $startColor;
		 $myObj->startMinute = $startMinute ;
		 $myObj->idList = $idList ;
		 $myObj->actionList = $actionList ;
         $myObj->actionCodeList = $actionCodeList ;
	     $myObj->winonid = $thisID ;
		 $myObj->numTrade = $numLoss . ' / '.$numTrade;
		 $myObj->numLoss =  '<span style="color:red;font-size:22px">' . $numLoss . '</span>';

	     $myJSON = json_encode($myObj, JSON_UNESCAPED_UNICODE);
		 if ($showOutPut) {		 
  		   $sAr = [$myObj] ;
		   Output($sAr) ;
		 }
//	     echo $myJSON; 

         return array($myObj,$numLoss) ; 
	     

} // end function


function getAnalyEMA_HistoryForLabObject($pdo,$id) { 
 

         $sql = 'select * from AnalyEMA_HistoryForLab where id>=? and id<=?'; 
         $params = array($id,$id+10);
         $rs= pdogetMultiValue2($sql,$params,$pdo) ;         
		 $results = [];

         while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			 $user = new stdClass();
             foreach ($row as $key => $value) {
               $user->$key = $value;
             }
             $results[] = $user;
				    
         } 	     
		 //print_r($results);

         return $results ;
		 return json_encode($results);
 



} // end function



function ManageTrendUp($AnalyObj,$i) {
	
// 3 อยู่ เหนือ  5
$previousColor = $AnalyObj[$i]->previousColor ;
$thisColor = $AnalyObj[$i]->thisColor ;

$PreviousTurnType = $AnalyObj[$i]->PreviousTurnType ;
$ema3SlopeDirection = $AnalyObj[$i]->slopeDirection ;
$emaConflictType = $AnalyObj[$i]->emaConflict ;
//$TurnTypePatternCode = $AnalyObj[$i]->TurnTypePatternCode ;

/*
Case TrendUp
  - เมื่อเจอแท่งแดงแรก เราจะเรียกว่าเป็น  Break Point โอกาศที่แท่งต่อไปจะเป็น แดงหรือเขียว ยังไม่แน่ ให Idle ไปก่อน
  - พอแท่งถัดไปออกมา ถ้า เป็น 
      Red(R->R) จะทำให้แดงแรกเปลี่ยนเป็น Turn Down ทันที ให้ Trade Red(PUT)
	  Red(R->G) จะทำให้แดงแรกเปลี่ยนเป็น Break Point  ให้ Trade Green(CALL)
    



*/

         $action = 'CALL';
		 $actionCode = 'U' ;
		 if ($emaConflictType== '35R' && $PreviousTurnType =='N') { 
			 if ($previousColor !=$thisColor) {
				 $action = 'Idle';
				 $actionCode = 'U1' ;
			 }			 
		 }

		 if ($emaConflictType== '35R' && $PreviousTurnType =='TurnDown') { 
			 //if ($previousColor !=$thisColor) {
				 $action = 'PUT';
				 $actionCode = 'U2' ;
			// }			 
		 }
		  


		 
         return array($action,$actionCode) ;

} // end function

function ManageTrendDown($AnalyObj,$i) { 

// 5 อยู่ เหนือ  3

$previousColorBack2 = $AnalyObj[$i]->previousColorBack2 ;
$previousColor = $AnalyObj[$i]->previousColor ;
$thisColor = $AnalyObj[$i]->thisColor ;

$PreviousTurnType = $AnalyObj[$i]->PreviousTurnType ;
$PreviousTurnTypeBack2 = $AnalyObj[$i]->PreviousTurnTypeBack2 ;
$PreviousTurnTypeBack3 = $AnalyObj[$i]->PreviousTurnTypeBack3 ;

$ema3SlopeDirection = $AnalyObj[$i]->slopeDirection ;
$emaConflictType = $AnalyObj[$i]->emaConflict ;

if ($emaConflictType == '53G') {
    $action = 'Idle';
    $actionCode = 'CF1' ;
	return array($action,$actionCode) ;
}

if (CheckSideWayColor($AnalyObj,$i) == true) {
    $action = '';  $actionCode = '';
	if ($AnalyObj[$i]->thisColor == 'Green' ) {
		$action = 'PUT';
		$actionCode = 'C1G'  ;
	}
	if ($AnalyObj[$i]->thisColor == 'Red' ) {
		$action = 'CALL';
		$actionCode = 'C1R@'  ;
	}
    return array($action,$actionCode) ;
}
/*
if ($thisColor == $previousColor) {
   $action = 'PUT';	$actionCode = 'C9G'  ;
   return array($action,$actionCode) ;
}
*/
// 53G,35R
         $action = 'PUT'; 
		 $actionCode = 'D' ;
         if ($emaConflictType== '53G' && $PreviousTurnType =='N') { 
			 if ($previousColor !=$thisColor) {
				 $action = 'Idle';
				 $actionCode = 'D1' ;
			 }	
			 return array($action,$actionCode) ;
		 }
		 if ($emaConflictType== '53G' && $previousColor !== $thisColor && $PreviousTurnType =='TurnUp' ) {
				 $action = 'CALL';
				 $actionCode = 'D2' ;
				 return array($action,$actionCode) ;
		 }
		 if ($emaConflictType== '53G' &&  $PreviousTurnType =='TurnUp' ) {
				 $action = 'CALL';
				 $actionCode = 'D22' ;
				 return array($action,$actionCode) ;
		 }

		 if ($thisColor != $previousColor  && $previousColor != $previousColorBack2 &&
             $previousColor != 'Equal' &&
			 $previousColorBack2 != 'Equal' 			 
		 ) {
			 if ($thisColor == 'Red') {
			   $action = 'CALL';
			 } else {
			   $action = 'PUT';
			 }
			 $actionCode = 'D3' ;
			 return array($action,$actionCode) ;
		 }

		 if ($PreviousTurnType  !=$PreviousTurnTypeBack2 ) {
			 if ($thisColor == 'Red') {
			   $action = 'CALL';
			 } else {
			   $action = 'PUT';
			 }
			 $actionCode = 'D4' ;

		 }
		 if ($PreviousTurnType  == 'N' ) {
			 if ($thisColor == 'Green') {
			   $action = 'CALL';
			 } else {
			   $action = 'PUT';
			 }
			 $actionCode = 'D5' ;

		 }


         
 


         return array($action,$actionCode) ;

} // end function


function CheckSideWayColor($AnalyObj,$i) { 
  
         if (
          $AnalyObj[$i]->thisColor != $AnalyObj[$i]->previousColor &&
		  $AnalyObj[$i]->previousColor != $AnalyObj[$i]->previousColorBack2  &&
          $AnalyObj[$i]->previousColorBack2 != $AnalyObj[$i]->previousColorBack3
		 ) {
           return true ;
         } else {
           return false ;
		 }




} // end function


function Trade($thisAction,$AnalyObj,$numLoss) { 

         if ($thisAction == 'CALL') {
			 $forecastColor = 'Green';
         }
		 if ($thisAction == 'PUT') {
			 $forecastColor = 'Red';
         }
		 if ($thisAction == 'Idle') {
			 $forecastColor = '???';
         }
		 if ($AnalyObj->nextColor == 'Equal') {
			 return 0 ;
		 } 



         if ($forecastColor != '???') {          
			 if ($AnalyObj->nextColor == $forecastColor) {
				 $profit = 0.85 ;
			 } else {
				 $profit = -1 ;
				 $numLoss++ ;
			 } 
         } else {
           // Case Idle
		   $profit = 0 ;
		 }
 		 return array($profit,$forecastColor,$numLoss);




} // end function

function Output($myObj) { 
/*
        print_r($myObj); return;
  	    $jsonArray = json_decode($myObj, true);
*/
	    $st = '<table border=1><tr>'; 
		$st .= '<th></th><th>ID</th><th>Color</th><th>MinuteNo</th>'; 
		$st .= '<th>Win AT ID</th><th>Action List</th><th>Action CodeList</th><th>Forecast<br>Color</th><th>จำนวนการ Trade</th><th>จำนวนการ Loss</th>'; 

		$st .='</tr>';

       echo 'จำนวนข้อมูล =' .count($myObj);
       foreach ($myObj as $person) {
          foreach ($person as $key => $value) {
            // echo "$key: $value<br>";
			$st .='<td >' . $value . '</td>';
          }
        }
		 
/*
		// วนลูปเพื่อแสดงผลข้อมูล
        foreach ($myObj as $obj) {
           foreach ($obj as $key => $value) {
           echo "<strong>$key</strong>: $value <br>";
        }
        echo "<hr>";  // เส้นคั่นแต่ละ object
}
*/
		$st .= '</tr></table>'; 
		echo $st;


         

} // end function

function AnalystByBackWard($AnalyObj,$i) { 
/*
Concept 
  ขั้นตอนแรก 
    ดูว่า ema3 > ema5 หรือไม่ 
	 กรณี  ema3 > ema5 (UpTrend)
	  1.ซึ่งตามปกติ ถ้า 3 > 5 เราจะเลือก Green
      2.ตรวจสอบว่า มี emaConflict หรือไม่ ถ้า  Conflict ไปข้อ 3 ถ้าไม่ emaConflict เราจะเลือก Green
	  3.ให้ดูว่า ema3 มี Slope ลงหรือไม่ ถ้าลง  เราจะเลือก  Red
	  4.กรณี Sideway
	     ถ้า  สีสลับ ให้เข้า  Case Sideway

*/
   $actionCode = '???'; 

   

   if (CheckSideWayColor($AnalyObj,$i) == true) {
	   if ($AnalyObj[$i]->thisColor =='Red') {
		   $action = 'CALL' ; $fcColor = 'Green'; 
		   $actionCode = 'BS1' ;
	   } 
	   if ($AnalyObj[$i]->thisColor =='Green') {
		   $action = 'PUT' ; $fcColor = 'Red'; 
		   $actionCode = 'BS2' ;
	   } 
       return array($action,$fcColor,$actionCode);
   }

   if ($AnalyObj[$i]->emaConflict != 'N')  {
	   $action = 'Idle' ; $fcColor = '???'; 
	   $actionCode = 'BS2' ;
       return array($action,$fcColor,$actionCode);
   }




   if ($AnalyObj[$i]->emaAbove == 3) { 
	   $action = 'CALL' ; $fcColor = 'Green';  $actionCode = 'B0';
	   if ($AnalyObj[$i]->PreviousTurnType  == 'TurnDown' && $AnalyObj[$i]->PreviousTurnTypeBack2  == 'N') {
           $action = 'PUT' ; $fcColor = 'Red'; 
		   $actionCode = 'B1' ;
	   }
	   if ($AnalyObj[$i]->emaConflict  == '35R' && 
		   $AnalyObj[$i]->slopeDirection  == 'Up' &&
		   $AnalyObj[$i]->thisColor  == $AnalyObj[$i]->previousColor
	   ) {
           $action = 'PUT' ; $fcColor = 'Red'; 
		   $actionCode = 'B2';
	   }
	   if ($AnalyObj[$i]->emaConflict  == '35R' && 
		   $AnalyObj[$i]->slopeDirection  == 'Up' &&
		   $AnalyObj[$i]->thisColor  != $AnalyObj[$i]->previousColor
	   ) {
           $action = 'CALL' ; $fcColor = 'Green'; 
		   $actionCode = 'B3';
	   }




   } else {
       $action = 'PUT' ;  $fcColor = 'Red'; 
	   if ($AnalyObj[$i]->PreviousTurnType  == 'TurnUp' && $AnalyObj[$i]->PreviousTurnTypeBack2  == 'N') {
           $action = 'CALL' ; $fcColor = 'Green'; 
           $actionCode = 'B3';
	   }


   }

   
   return array($action,$fcColor,$actionCode);





} // end function

function isWaterFall($AnalyObj,$i) { 




} // end function


function main($pdo) { 
	     
		 $maxLoss = 0 ;
		 $id = 2462732  + ( 30 * 20 ) ;
		 echo "Main--->" . $id . '<hr>';
		 $obj1All = [] ;$obj2all = [] ;


		 for ($i=0;$i<=28;$i++) {
           $thisID = $id + $i ;
		   list($obj,$numLoss) = TradeByClass2($pdo,$thisID);    
		   $obj1All[] = $obj ;
		   if ($numLoss > $maxLoss) {
			   $maxLoss = $numLoss;
		   }
		 }

		 echo '<hr><h1>Max Loss=' . $maxLoss . '</h1>';

		 $maxLoss = 0 ;
		 //$id = 2459853 + ( 30 * 30 ) ;
		 echo "Main--->" . $id . '<hr>';
		 for ($i=0;$i<=28;$i++) {
           $thisID = $id + $i ;
		   list($obj2,$numLoss) = TradeByClass2B($pdo,$thisID,$showOutPut=true);    
		   $obj2All[] = $obj2 ;
		   if ($numLoss > $maxLoss) {
			   $maxLoss = $numLoss;
		   }
		 }

		 echo '<hr><h1>Max Loss=' . $maxLoss . '</h1>';

		 Conclude($obj1All,$obj2All) ;

		 




return;
$fixID = 2460666 ;
/*
echo "<h1>ซ่อมงาน $fixID </h1>";


$FixAnalyObj = getAnalyEMA_HistoryForLabObject($pdo,$fixID) ;
$total =  count($FixAnalyObj) ;
echo $FixAnalyObj[0]->id . "<br>";

for ($i=0;$i<=($total-1) ;$i++) {

     list($action,$fcColor) = AnalystByBackWard($FixAnalyObj,$i);
	 echo $FixAnalyObj[$i]->id . ' : '  .$FixAnalyObj[$i]->minuteno  . ' Action =' . $action . '-'.$fcColor ;
	 if ($FixAnalyObj[$i]->nextColor == $fcColor ) {
		 echo  $i . '= Win' . '<br>' ;
	 } else {
		 echo  $i . '= Loss' . '<br>' ;
	 }

}

*/
		 
		 

  



} // end function




/*
SELECT 
a.id,minuteno,TurnTypePatternCode,b.TurnTypePattern,
c.id,a.ColorPatternCode,
c.ColorPattern
FROM 
`AnalyEMA_HistoryForLab` a 
INNER join turnTypeMaster b  on a.TurnTypePatternCode = b.id
INNER join ColorPattern c  on a.ColorPatternCode = c.id

WHERE a.id=2456095

*/
?>

<?php

function getActionClassV2($AnalyObj,$i) { 

//print_r($AnalyObj); return;


/*
$st = json_encode($AnalyObjA[$i]);
$AnalyObj = json_decode($st,true) ;
*/
// Check SideWay
/*
if (
 
  ($AnalyObj['PreviousTurnType'] != $AnalyObj['PreviousTurnTypeBack2'] ) &&
  ($AnalyObj['PreviousTurnTypeBack2'] != $AnalyObj['PreviousTurnTypeBack3'] &&
   $AnalyObj['emaAbove'] == 3 )
  

) {
  if ($AnalyObj['thisColor'] == 'Red') {
      $action = 'CALL'  ; $actionCode = 'SWR';
      return array($action,$actionCode);
  } 
  if ($AnalyObj['thisColor'] == 'Green') {
      $action = 'PUT'  ; $actionCode = 'V2-SWG';
      return array($action,$actionCode);
  } 


}
*/
/*
if ($AnalyObj[$i]->pip == 0 ) {
    $action = 'Idle'  ; $actionCode = 'PIP0';
	return array($action,$actionCode);
}
*/
/*
// ตรวจสอบชนิดของตัวแปร
if (gettype($myObject) === "object") {
    // แปลง Object เป็น Array
    $myArray = get_object_vars($myObject);

    // พิมพ์ผลลัพธ์ออกมาเพื่อตรวจสอบ
    print_r($myArray);
} else {
    echo "ตัวแปรไม่ใช่ชนิด Object";
}
*/

if (gettype($AnalyObj[$i]) === "array") {
    // แปลง Array เป็น Object
    $AnalyObj[$i] = (object) $AnalyObj[$i];
} else {
    echo "ตัวแปรไม่ใช่ชนิด Array";
}


if (
	$AnalyObj[$i]->thisColor == 'Red' &&
	$AnalyObj[$i]->previousColor == 'Green' &&
	$AnalyObj[$i]->previousColorBack2 == 'Red' &&
	$AnalyObj[$i]->PreviousTurnType == 'TurnDown' &&
	$AnalyObj[$i]->emaAbove == 5

	
	
) {

      $action = 'PUT'  ; $actionCode = 'CT01@#';
      return array($action,$actionCode);

}


if ($AnalyObj[$i]->emaConflict != 'N' ) {
   $action = 'Idle'  ; $actionCode = 'CF0' ;
   if ($AnalyObj[$i]->emaConflict == '35R' &&  
	  $AnalyObj[$i]->PreviousTurnType == 'TurnDown') {
      $action = 'PUT'  ; $actionCode = 'CT11';
      return array($action,$actionCode);
   }

   if ($AnalyObj[$i]->emaConflict == '35R' &&  
	  $AnalyObj[$i]->PreviousTurnType == 'N' &&
	  abs($AnalyObj[$i]->slopeValue) < 0.2 

	   
   ) {
      $action = 'PUT'  ; $actionCode = 'SLCT11';
      return array($action,$actionCode);
   }


   if ($AnalyObj[$i]->emaConflict == '35R' &&  $AnalyObj[$i]->PreviousTurnType == 'TurnUp') {
      $action = 'CALL'  ; $actionCode = 'CT12@';
      return array($action,$actionCode);
   }

   if ($AnalyObj[$i]->emaConflict == '35R' &&  
	$AnalyObj[$i]->PreviousTurnType == 'TurnDown' &&
    $AnalyObj[$i]->slopeDirection == 'Down' 
	   
    ) {
      $action = 'PUT'  ; $actionCode = 'CT03B@';
	  $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$actionCode);
   }

   if ($AnalyObj[$i]->emaConflict == '53G' &&  $AnalyObj[$i]->PreviousTurnType == 'TurnUp') {
      $action = 'CALL'  ; $actionCode = 'CT13@';
      return array($action,$actionCode);
   }

   if ($AnalyObj[$i]->emaConflict == '53G' ) {
      $action = 'CALL'  ; $actionCode = 'CT13B@';
      return array($action,$actionCode);
   }
   if (
	   $AnalyObj[$i]->emaConflict == '53G' &&  
	   $AnalyObj[$i]->PreviousTurnType == 'N' &&
	   $AnalyObj[$i]->slopeDirection == 'Up'
   ) {
      $action = 'CALL'  ; $actionCode = 'CT14@';
      return array($action,$actionCode);
   }
   if (
	   $AnalyObj[$i]->emaConflict == '53G' &&  
	   $AnalyObj[$i]->PreviousTurnType == 'TurnUp' &&
	   $AnalyObj[$i]->slopeDirection == 'Up'
   ) {
      $action = 'CALL'  ; $actionCode = 'CT15@';
      return array($action,$actionCode);
   }

   

   



   return array($action,$actionCode);
}


if ($AnalyObj[$i]->emaConflict == 'N' ) {
   if ($AnalyObj[$i]->CutPointType == '3=>5' ) {
     $action = 'PUT'  ; $actionCode = 'CT0';
     return array($action,$actionCode);
   }
   if ($AnalyObj[$i]->CutPointType == '5=>3' ) {
     $action = 'CALL'  ; $actionCode = 'CT1';
     return array($action,$actionCode);
   }
   if ($AnalyObj[$i]->CutPointType == 'N' &&  $AnalyObj[$i]->PreviousTurnType == 'TurnUp') {
     $action = 'CALL'  ; $actionCode = 'CTA1';
     return array($action,$actionCode);
   }
}

if ($AnalyObj[$i]->emaConflict == 'N' && $AnalyObj[$i]->PreviousTurnType == 'N' &&
	$AnalyObj[$i]->emaAbove == '3') {
 	 $action = 'CALL'  ; $actionCode = 'CT1%';
     return array($action,$actionCode);


}



if ($AnalyObj[$i]->PreviousTurnType == 'N' && $AnalyObj[$i]->PreviousTurnTypeBack2 =='TurnUp') {
	if ($AnalyObj[$i]->slopeDirection =='Up') {
        $action = 'CALL'  ; $actionCode = 'T0';
		return array($action,$actionCode);
	} else {
        $action = 'PUT'  ; $actionCode = 'T0B';
		return array($action,$actionCode);
	}

}

if (
	$AnalyObj[$i]->PreviousTurnType != $AnalyObj[$i]->PreviousTurnTypeBack2 &&
	$AnalyObj[$i]->PreviousTurnTypeBack2 != $AnalyObj[$i]->PreviousTurnTypeBack3 
	
) {
	  
	 if ($AnalyObj[$i]->thisColor == $AnalyObj[$i]->previousColor) { 
	     if ($AnalyObj[$i]->thisColor == 'Red') {
		 	 $action = 'PUT'  ; $actionCode = 'T1.1';
		 }
		 if ($AnalyObj[$i]->thisColor == 'Green') {
		 	 $action = 'CALL'  ; $actionCode = 'T1.2';
		 }
	 } else {
        if ($AnalyObj[$i]->emaAbove == 3) { 
           $action = 'CALL'  ; $actionCode = 'T2.2';
        } else {
           $action = 'PUT'  ; $actionCode = 'T2.3';
		}
    
	 }
     return array($action,$actionCode);
}

         
		 if ($AnalyObj[$i]->emaAbove == 3) {
			 list($action,$actionCode) = ManageTrendUp($AnalyObj,$i) ;
			 $actionCode .= 'U' . $actionCode ;
		 }  
		 if ($AnalyObj[$i]->emaAbove == 5) {			 
			 list($action,$actionCode) = ManageTrendDown($AnalyObj,$i) ;
			 $actionCode .= 'D' . $actionCode ;
		 }  
 
        
		return array($action,$actionCode) ;

} // end function 


function getActionClassVTmp($AnalyObj,$i) { 

 $action = ''; $actionCode  = '';
//print_r($AnalyObj); return;
if (gettype($AnalyObj) === "object") {
    // แปลง Object เป็น Array
    $AnalyObj = get_object_vars($AnalyObj);

    // พิมพ์ผลลัพธ์ออกมาเพื่อตรวจสอบ
  //  print_r($myArray);
} else {
    //echo "ตัวแปรไม่ใช่ชนิด Object";
}

if (gettype($AnalyObj) === "array") {
    // แปลง Array เป็น Object
    //echo "เป็น ตัวแปร ชนิด Array";
    $AnalyObj[] = (object) $AnalyObj;
} else {
    //echo "ตัวแปรไม่ใช่ชนิด Array";
}



/*
$st = json_encode($AnalyObjA[$i]);
$AnalyObj = json_decode($st,true) ;
*/
// Check SideWay
/*
if (
 
  ($AnalyObj['PreviousTurnType'] != $AnalyObj['PreviousTurnTypeBack2'] ) &&
  ($AnalyObj['PreviousTurnTypeBack2'] != $AnalyObj['PreviousTurnTypeBack3'] &&
   $AnalyObj['emaAbove'] == 3 )
  

) {
  if ($AnalyObj['thisColor'] == 'Red') {
      $action = 'CALL'  ; $actionCode = 'SWR';
      return array($action,$actionCode);
  } 
  if ($AnalyObj['thisColor'] == 'Green') {
      $action = 'PUT'  ; $actionCode = 'V2-SWG';
      return array($action,$actionCode);
  } 


}
*/
/*
if ($AnalyObj[$i]->pip == 0 ) {
    $action = 'Idle'  ; $actionCode = 'PIP0';
	return array($action,$actionCode);
}
*/
/*
// ตรวจสอบชนิดของตัวแปร
if (gettype($myObject) === "object") {
    // แปลง Object เป็น Array
    $myArray = get_object_vars($myObject);

    // พิมพ์ผลลัพธ์ออกมาเพื่อตรวจสอบ
    print_r($myArray);
} else {
    echo "ตัวแปรไม่ใช่ชนิด Object";
}
*/


if (gettype($AnalyObj[$i]) === "array") {
    // แปลง Array เป็น Object
    $AnalyObj[$i] = (object) $AnalyObj[$i];
} else {
    echo "ตัวแปรไม่ใช่ชนิด Array";
}


//echo "<br>เริ่ม  getActionClassVTmp ID= " .  $AnalyObj[$i]->id . '::' .$AnalyObj[$i]->MACDHeight ;

if ($i > 1 && $AnalyObj[$i]->emaAbove == 3  && $AnalyObj[$i-1]->emaAbove == 3 ) {
      $action = 'CALL'  ; $actionCode = 'CT02@#';
      return array($action,$actionCode);
}

if ( $i >= 3 &&
	$AnalyObj[$i]->thisColor == 'Red' &&
	$AnalyObj[$i-1]->thisColor == 'Green' &&
	$AnalyObj[$i-2]->thisColor == 'Red'  
) {

      $action = 'CALL'  ; $actionCode = 'CT01@#';
      return array($action,$actionCode);

}

if ( $i > 2 && 
	$AnalyObj[$i]->PreviousTurnType != 'TurnDown' &&
	$AnalyObj[$i-1]->PreviousTurnType == 'TurnUp' &&
	$AnalyObj[$i-2]->PreviousTurnTypeBack3 == 'TurnDown' 

) {

      $action = 'PUT'  ; $actionCode = 'CT02@#';
      return array($action,$actionCode);

}


if (
	$AnalyObj[$i]->thisColor == 'Red' &&
	$AnalyObj[$i]->previousColor == 'Green' &&
	$AnalyObj[$i]->previousColorBack2 == 'Red' &&
	$AnalyObj[$i]->emaAbove == 5

) {

      $action = 'PUT'  ; $actionCode = 'CT01@#';
      return array($action,$actionCode);

}

if (
	$AnalyObj[$i]->PreviousTurnType == 'TurnDown' &&
	$AnalyObj[$i]->PreviousTurnTypeBack2 == 'TurnUp' &&
	$AnalyObj[$i]->PreviousTurnTypeBack3 == 'TurnDown' 

) {

      $action = 'CALL'  ; $actionCode = 'CT02@#';
      return array($action,$actionCode);

}

if (
	$AnalyObj[$i]->PreviousTurnType == 'TurnuP' &&
	$AnalyObj[$i]->PreviousTurnTypeBack2 == 'TurnDown' &&
	$AnalyObj[$i]->PreviousTurnTypeBack3 == 'TurnUp'  &&
	$AnalyObj[$i]->CutPoint == '5=>3'  


) {

      $action = 'CALL'  ; $actionCode = 'CT02@#';
      return array($action,$actionCode);

}
/*
if (
	$AnalyObj[$i]->thisColor == 'Red' &&
	$AnalyObj[$i]->previousColor == 'Green' &&
	$AnalyObj[$i]->previousColorBack2 == 'Red' 
	

) {

      $action = 'CALL'  ; $actionCode = 'CT02@#';
      return array($action,$actionCode);

}
*/

if ($AnalyObj[$i]->emaConflict != 'N' ) {
   $action = 'Idle'  ; $actionCode = 'CF0' ;
   if ($AnalyObj[$i]->emaConflict == '35R' &&  
	  $AnalyObj[$i]->PreviousTurnType == 'TurnDown') {
      $action = 'PUT'  ; $actionCode = 'CT11';
      return array($action,$actionCode);
   }

   if ($AnalyObj[$i]->emaConflict == '35R' &&  
	  $AnalyObj[$i]->PreviousTurnType == 'N' &&
	  abs($AnalyObj[$i]->ema3SlopeValue) < 0.2 

	   
   ) {
      $action = 'PUT'  ; $actionCode = 'SLCT11';
      return array($action,$actionCode);
   }


   if ($AnalyObj[$i]->emaConflict == '35R' &&  $AnalyObj[$i]->PreviousTurnType == 'TurnUp') {
      $action = 'CALL'  ; $actionCode = 'CT12@';
      return array($action,$actionCode);
   }

   if ($AnalyObj[$i]->emaConflict == '35R' &&  
	$AnalyObj[$i]->PreviousTurnType == 'TurnDown' &&
    $AnalyObj[$i]->slopeDirection == 'Down' 
	   
    ) {
      $action = 'PUT'  ; $actionCode = 'CT03B@';
	  $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$actionCode);
   }

   if ($AnalyObj[$i]->emaConflict == '53G' &&  $AnalyObj[$i]->PreviousTurnType == 'TurnUp') {
      $action = 'CALL'  ; $actionCode = 'CT13@';
      return array($action,$actionCode);
   }

   

   if (
	   $AnalyObj[$i]->emaConflict == '53G' &&  
	   $AnalyObj[$i]->PreviousTurnType == 'N' &&
	   $AnalyObj[$i]->slopeDirection == 'Up'
   ) {
      $action = 'CALL'  ; $actionCode = 'CT14@';
      return array($action,$actionCode);
   }
   if (
	   $AnalyObj[$i]->emaConflict == '53G' &&  
	   $AnalyObj[$i]->PreviousTurnType == 'TurnUp' &&
	   $AnalyObj[$i]->slopeDirection == 'Up'
   ) {
      $action = 'CALL'  ; $actionCode = 'CT15@';
      return array($action,$actionCode);
   }

   if ($AnalyObj[$i]->emaConflict == '53G' ) {
      $action = 'CALL'  ; $actionCode = '**CT13B@';
      return array($action,$actionCode);
   }

   

   



   return array($action,$actionCode);
}


if ($AnalyObj[$i]->emaConflict == 'N' ) {
   if ($AnalyObj[$i]->CutPointType == '3=>5' ) {
     $action = 'PUT'  ; $actionCode = 'CT0';
     return array($action,$actionCode);
   }
   if ($AnalyObj[$i]->CutPointType == '5=>3' ) {
     $action = 'CALL'  ; $actionCode = 'CT1';
     return array($action,$actionCode);
   }
   if ($AnalyObj[$i]->CutPointType == 'N' &&  $AnalyObj[$i]->PreviousTurnType == 'TurnUp') {
     $action = 'CALL'  ; $actionCode = 'CTA1';
     return array($action,$actionCode);
   }
}

if ($AnalyObj[$i]->emaConflict == 'N' && $AnalyObj[$i]->PreviousTurnType == 'N' &&
	$AnalyObj[$i]->emaAbove == '3') {
 	 $action = 'CALL'  ; $actionCode = 'CT1%';
     return array($action,$actionCode);


}



if ($AnalyObj[$i]->PreviousTurnType == 'N' && $AnalyObj[$i]->PreviousTurnTypeBack2 =='TurnUp') {
	if ($AnalyObj[$i]->slopeDirection =='Up') {
        $action = 'CALL'  ; $actionCode = 'T0';
		return array($action,$actionCode);
	} else {
        $action = 'PUT'  ; $actionCode = 'T0B';
		return array($action,$actionCode);
	}

}

if (
	$AnalyObj[$i]->PreviousTurnType != $AnalyObj[$i]->PreviousTurnTypeBack2 &&
	$AnalyObj[$i]->PreviousTurnTypeBack2 != $AnalyObj[$i]->PreviousTurnTypeBack3 
	
) {
	  
	 if ($AnalyObj[$i]->thisColor == $AnalyObj[$i]->previousColor) { 
	     if ($AnalyObj[$i]->thisColor == 'Red') {
		 	 $action = 'PUT'  ; $actionCode = 'V2Tmp::T1.1';
		 }
		 if ($AnalyObj[$i]->thisColor == 'Green') {
		 	 $action = 'CALL'  ; $actionCode = 'V2Tmp::T1.2';
		 }
	 } else {
        if ($AnalyObj[$i]->emaAbove == 3) { 
           $action = 'CALL'  ; $actionCode = 'T2.2';
        } else {
           $action = 'PUT'  ; $actionCode = 'T2.3';
		}
    
	 }
     return array($action,$actionCode);
}

         
		 if ($AnalyObj[$i]->emaAbove == 3) {
			 list($action,$actionCode) = ManageTrendUp($AnalyObj,$i) ;
			 $actionCode .= 'U' . $actionCode ;
		 }  
		 if ($AnalyObj[$i]->emaAbove == 5) {			 
			 list($action,$actionCode) = ManageTrendDown($AnalyObj,$i) ;
			 $actionCode .= 'D' . $actionCode ;
		 }  
 
        
		return array($action,$actionCode) ;

} // end function

?>