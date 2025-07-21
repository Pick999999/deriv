<?php
//clsTrade.php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class clsTrade {

// เป็น Class ระดับ เพจ 
// Properties
 public $shopid;
 public $saleid;
 public $lang;

 public $shopnameurl;
 public $salename;
 public $pdo;
 

 public $pagename ;
 public $clsDataservice ;

function __construct() { 
/*
     $this->lang     = $lang;
	 $this->shopid   = $shopid ;
	 $this->shopnameurl = $shopnameurl ; 
	 $this->saleid = $saleid ; 
*/
	 
     $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	 $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	 require_once( "newutil2.php"); 

	 //require_once($newUtilPath ."iqlab/AjaxLabSideway.php"); 

	 
	 
	 
	 
	 $dbname = 'thepapers_lab' ;
	 $pdo = getPDONew()  ;
	 //$pdo->exec("set names utf8mb4") ;
	 $this->pdo = $pdo;

	 
	 
	 
	 


	


} // end __construct


  // Methods

function init_Data() { 


}


function getActionFromID($pdo,$tableName,$row,$macdThershold,$lastMacdHeight) { 


   $LockedAction = false ;
   //$slopeValue = $row['ema3SlopeValue'] ;
   $slopeDirection = $row['slopeDirection'] ;
   $row['MACDHeight']= $row['MACDHeight']*1000*1000 ;
   $macd = $row['MACDHeight'] ;
   
   $pipSize = round(abs($row['pip'])/10,2);
   $delTapip = abs(abs($row['pip']/10) - abs($row['previousPIP']/10)) ;

   
   
   $sql="SELECT a.id ,b.id,b.minuteno, a.ema3,b.ema3, (b.ema3-a.ema3)*1000*1000 as differ
    FROM  $tableName a INNER join $tableName b on b.id-1 = a.id 
    WHERE b.id = ?";
   $params = array($row['id']);   
   $rowDifferEMA = pdoRowSet($sql,$params,$this->pdo) ;      
   $slopeValue = $rowDifferEMA['differ']  ;


   if ($slopeValue < 0) {
      $slopeDirection = 'Down' ;
   } else {
      $slopeDirection = 'Up' ;
   }
   $slopeDirection = $row['slopeDirection'] ;
   
   
   


   if ($macd < $lastMacdHeight) {
	  $macdConver = 'Conver';
   } else {
      $macdConver = 'Diver';
   }
   $lastMacdHeight = $macd;

   //$macdConver = $row['MACDConvergence'] ;
   $thisAction = ''; $remark = '';
   $forecastColor = ''; 
   $forecastClass = '';
   $winStatus = '';
   //$slopeValue = $row['ema3SlopeValue'] ;
   $actionReason = '';

   $sql = "select thisColor  from $tableName where id=? "; 
   $params = array($row['id']+1);
   $nextColor =pdogetValue($sql,$params,$pdo) ;

   if ($nextColor=='Green') {
	   $nextColorClass ='bgGreen' ;
   }
   if ($nextColor=='Red') {
	   $nextColorClass ='bgRed' ;
   }
   if ($nextColor=='Equal') {
	   $nextColorClass ='bgGray' ;
   } 
//
// *******************   เริ่ม Case ตรงนี้   ************************
  // Step 1-1
   if ($slopeDirection=='Down') {
      $thisAction = 'PUT'; 
	  $forecastColor = 'Red';
	  $forecastClass = 'bgRed';
	  $ActionClass = 'bgRed';
	  $actionReason = 'Code1-1(R)';
   }
   if ($slopeDirection=='Up') {
      $thisAction = 'CALL'; 
	  $forecastColor = 'Green';
	  $forecastClass = 'bgGreen';
	  $ActionClass = 'bgGreen';
	  $actionReason = 'Code1-1(G)';
   }

   // Step 1-1-2
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown') {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-2(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-2(R)';
	 }
   }
   // Step 1-1-3
   if (
	   $row['slopeDirection'] =='Down' && 
	   $row['CutPointType'] =='3->5' && 
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown') {

    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-3(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-3(R)';
	 }
   }
   // Step 1-1-4
   if (
	   $row['slopeDirection'] =='Down' && 
	   $row['CutPointType'] =='N' && 
	   $row['PreviousTurnType'] =='' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='N') {

    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-4(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-4(R)';
	 }
   }
   // Step 1-1-5
   if (
	   $row['slopeDirection'] =='Down' && 
	   $row['CutPointType'] =='N' && 
	   $row['PreviousTurnType'] =='' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='N') {

    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-4(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-4(R)';
	 }
   }
   // Step 1-1-6
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='N' &&
       $row['PreviousTurnTypeBack4'] =='TurnUp' 
	   ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-6(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-6(R)';
	 }
   }
   // Step 1-1-7
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
       $row['CutPointType'] =='5->3' 
	   ) {
    
      if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-7(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-7(R)';
	 }
   }

   // Step 1-1-8
   if (
	   $row['slopeDirection'] =='Down' && 
	   $row['CutPointType'] =='3->5' && 
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
       $row['MACDHeight'] < 8 
   
   ) {

    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-3(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-3(R)';
	 }
   }

 
   // Step 1-1-9
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
       $row['PreviousTurnTypeBack4'] =='N' 
       
	   ) {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-9(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-9(R)';
	 }
   }

   // Step 1-1-10
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack4'] =='N' 
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-10(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-10(R)';
	 }
   }
   // Step 1-1-11
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='N' &&
	   $row['PreviousTurnTypeBack4'] =='TurnUp' 
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-11(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-11(R)';
	 }
   }
   // Step 1-1-12
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
       $row['CutPointType'] == '3->5'
	  ) 
  {
    
      if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-10(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-10(R)';
	 }
   }
   // Step 1-1-13
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
       $row['CutPointType'] == 'N'
	  ) 
  {
    
      if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-13(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-13(R)';
	 }
   }

/*

   if ($slopeDirection=='Up') {
      $thisAction = 'CALL'; 
	  $forecastColor = 'Green';
	  $forecastClass = 'bgGreen';
	  $ActionClass = 'bgGreen';
	  $actionReason = 'Code1-1(G)';
   }

*/

   if ($slopeDirection=='P') {
     $thisAction = 'Idle'; 
	 $remark = ' Slope ขนาน ';
   }

   if ($row['thisColor']=='Equal') {
     $thisAction = 'Idle'; 
	 $remark .= ' ,Equal ';
   }

   if (abs($row['MACDHeight']) < $macdThershold) {
      if ($actionReason !='Code2') {      
        $thisAction = 'Idle'; 
	    $remark .= ' ,MACD ..น้อยกว่า  ' .$macdThershold;
	  }
   }
 
    // Step 2-1
   if ($row['emaConflict'] == '3-5-R' && $row['MACDConvergence'] == 'Diver' && $row['slopeDirection'] != 'Up') {
		   $thisAction = 'PUT'; 
		   $forecastColor = 'Red';
		   $forecastClass = 'bgRed';
  	       $ActionClass = 'bgRed'; 
		   $actionReason .= '->Code2_1(R)';
   } 

   // Step 2-2
   if ($row['emaConflict'] == '3-5-R' && $macdConver == 'Conver'
      && $row['slopeDirection'] =='Down' && $row['PreviousTurnType']=='TurnDown'
   ) {
		   $thisAction = 'PUT'; 
		   $forecastColor = 'Red';
		   $forecastClass = 'bgRed';
  	       $ActionClass = 'bgRed'; 
		   $actionReason .= '->Code2_2(R)';
		   $LockedAction = false;

   } 

    
 
   // ตรวจสอบการ Sideway
      // Step 3-1
   if ($row['PreviousSlopeDirection'] !== $row['slopeDirection'] && 
	   $LockedAction == false && $row['PreviousSlopeDirection'] !=='N') {

	   // toggle thisColor
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code3-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code3-1(R)';
		}
   }

// เพิ่มเติมจาก Step-6 ใน Python 
   // Step 6-1
   if ($row['PreviousTurnType'] =='TurnUp' && $row['MACDConvergence'] =='Diver' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-1(R)';
		}
   }
   // Step 6-2
   if ($row['PreviousTurnType'] =='TurnDown' && $row['emaConflict'] =='3-5-R' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-2(R)';
		}
   }
   // Step 6-3
   if ($row['PreviousTurnType'] =='TurnDown' && $row['emaConflict'] =='N' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-3(R)';
		}
   }
   // Step 6-4
   if ($row['PreviousTurnType'] =='TurnUp' && $row['emaConflict'] =='5-3-G' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-4(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-4(R)';
		}
   }
   // Step 6-5
   //if ($row['PreviousTurnType'] =='TurnUp' && $row['TurnType'] =='TurnDown' ) {
   if ($row['PreviousTurnType'] =='TurnUp'  ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-5(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-5(R)';
		}
   }
   // Step 6-5-2
   //if ($row['PreviousTurnType'] =='TurnUp' && $row['TurnType'] =='TurnDown' ) {
   if ($row['PreviousTurnType'] =='TurnUp' && 
       $row['PreviousTurnTypeBack2'] =='TurnDown' && 
	   $row['slopeDirection']=='Up' && $row['emaConflict']=='N'  ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-5-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-5-2(R)';
		}
   }

   // Step 6-5-3
   //if ($row['PreviousTurnType'] =='TurnUp' && $row['TurnType'] =='TurnDown' ) {
   if ($row['PreviousTurnType'] =='TurnUp' && 
       $row['PreviousTurnTypeBack2'] =='TurnDown' && 
	   $row['PreviousTurnTypeBack3'] =='N' && 
	   $row['slopeDirection']=='Up' && $row['emaConflict']=='' && $row['ema3SlopeValue'] <10 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-5-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-5-3(R)';
		}
   }
   // Step 6-6
   //if ($row['PreviousTurnType'] =='TurnDown' && $row['TurnType'] =='TurnUp' ) {
   if ($row['PreviousTurnType'] =='TurnDown' && $row['emaConflict'] !='3-5-R' ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-6(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-6(R)';
		}
   }
   // Step 6-7
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-7(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-7(R)';
		}
   }

   // Step 6-7-1
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
   && $row['PreviousTurnTypeBack2'] == 'TurnUp'
   
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-7-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-7-1(R)';
		}
   }

    // Step 6-7-2
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
   && $row['PreviousTurnTypeBack2'] == 'TurnUp'
   && $row['CutPointType'] == '5->3'
   
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-7-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-7-1(R)';
		}
   }

   // Step 6-7-3
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
   && $row['PreviousTurnTypeBack2'] == 'TurnUp'
   && $row['PreviousTurnTypeBack3'] == 'TurnDown'
   
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-7-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-7-3(R)';
		}
   }

   // Step 6-8
   if ($row['slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R' ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-8(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-8(R)';
		}
   }

   // Step 6-8-2
   if ($row['slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R' &&
	   $row['PreviousTurnTypeBack2'] =='TurnDown' && $row['MACDHeight'] <=1
	   
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-8-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-8-2(R)';
		}
   }
   // Step 6-9
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='5-3-G' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9(G77)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9(R77)';
		}
   }
   // Step 6-9-1
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='5-3-G' 
   && $row['PreviousTurnType'] == 'N' 
   && $row['PreviousTurnTypeBack2'] == 'TurnUp' 
   && $row['PreviousTurnTypeBack3'] == 'N' 

	   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9-1(R)';
		}
   }

   // Step 6-9-2
   if ($row['slopeDirection'] =='Up' 
   && $row['emaConflict'] =='5-3-G' 
   && $row['PreviousTurnType'] == '' 
   && $row['PreviousTurnTypeBack2'] == 'TurnUp' 
   && $row['PreviousTurnTypeBack3'] == 'N' 
   && $row['MACDHeight'] < 4

	   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9-2(R)';
		}
   }

// Step 6-9-3
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='5-3-G'
   && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='TurnUp'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9-3(R)';
		}
   }

   // Step 6-9-4
   if ($row['slopeDirection'] =='Up' && 
	   $row['emaConflict'] =='5-3-G' &&
	   $row['PreviousTurnType'] == 'TurnUp'
	   
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9-4(G77)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9-4(R77)';
		}
   }

// Step 6-10
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnType'] =='TurnDown' && $row['emaConflict'] =='' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-10(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-10(R)';
		}
   }
   // Step 6-11
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' && $row['emaConflict'] =='N' ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-11(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-11(R)';
		}
   }
   // Step 6-12
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='TurnUp' && $row['emaConflict'] =='3-5-R' && abs($row['MACDHeight']*1000*1000) < 10 ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-12(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-12(R)';
		}
   }
   // Step 6-13
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N' && $row['emaConflict'] =='5-3-G' && $row['slopeDirection']=='Up') {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-13(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-13(R)';
		}
   }
   // Step 6-14
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' && abs($row['ema3SlopeValue']) < 0.9 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-14(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-14(R)';
		}
   }
   // Step 6-15
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' && abs($row['ema3SlopeValue']) < 0.9 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-15(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-15(R)';
		}
   }
   // Step 6-16
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' && abs($row['ema3SlopeValue']) > 0.9 ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-16(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-16(R)';
		}
   }

   // Step 6-16-2
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp'
   && $row['slopeDirection'] =='Up'
   && abs($row['ema3SlopeValue']) > 20 ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-16(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-16(R)';
		}
   }
   // Step 6-16-3
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp'
   && $row['slopeDirection'] =='Down'
   && abs($row['ema3SlopeValue']) > 20 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-16(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-16(R)';
		}
   }

    // Step 6-17
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='TurnDown' && ($row['emaConflict']) == '5-3-G' ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-17(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-17(R)';
		}
   }
    // Step 6-17@
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='TurnDown' && ($row['emaConflict']) == '5-3-G' && $row['slopeDirection'] =='Up') {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-@17(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-@17(R)';
		}
   }
   // Step 6-17-2
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='TurnDown' 
	   && $row['PreviousTurnTypeBack3'] =='N' 
       && $row['PreviousTurnTypeBack4'] =='TurnUp' 
	   && ($row['emaConflict']) == '5-3-G' && $row['slopeDirection'] =='Up') {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-17-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-17-2(R)';
		}
   }
    // Step 6-18
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N' && ($row['emaConflict']) == '5-3-G' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-18(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-18(R)';
		}
   }
   // Step 6-19
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' 
    && $row['emaConflict'] == '3-5-R') {
	   //&& abs($row['ema3SlopeValue']) < 0.9 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-19(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-19(R)';
		}
   }
   // Step 6-19-2
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' 
    && $row['emaConflict'] == 'N') {
	   //&& abs($row['ema3SlopeValue']) < 0.9 ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-19-2@(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-19-2@(R)';
		}
   }
// Step 6-20
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'  ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-20(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-20(R)';
		}
   }

// Step 6-20-2
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['MACDHeight'] < 4
	   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-20-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-20-2(R)';
		}
   }

// Step 6-21
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='TurnDown'
   && $row['emaConflict'] == '5-3-G'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-21(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-21(R)';
		}
   }
// Step 6-22
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['emaConflict'] == '3-5-R' && $row['slopeDirection'] =='Up'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-22(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-22(R)';
		}
   }

   // Step 6-22-1
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['emaConflict'] == '3-5-R' && $row['slopeDirection'] =='Up'
   && (abs($row['MACDHeight']) > 15)
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-22-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-22-1(R)';
		}
   }

   // Step 6-22-2
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='TurnUp' && $row['PreviousTurnTypeBack4'] =='TurnDown'

   && $row['emaConflict'] == '3-5-R' && $row['slopeDirection'] =='Up'

   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-22-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-22-2(R)';
		}
   }
   // Step 6-22-3
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='TurnUp'
   && $row['emaConflict'] == '3-5-R' && $row['slopeDirection'] =='Up'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-22-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-22-3(R)';
		}
   }

   // Step 6-23
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp'
   && abs($row['ema3SlopeValue']) < 0.8
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-5(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-5(R)';
		}  
   }
   // Step 6-24
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='5-3-G'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24(R)';
		}
   }

   // Step 6-24-2
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && abs($row['ema3SlopeValue']) < 8
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='5-3-G'

   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-2(R)';
		}
   }
   // Step 6-24-3
   if ($row['PreviousTurnType'] =='N' 
   && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='TurnDown'
   && $row['PreviousTurnTypeBack4'] =='TurnUp'
   && (abs($row['ema3SlopeValue']) < 5)
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='5-3-G'

   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-2(R)';
		}
   }

   // Step 6-24-4
   if ($row['PreviousTurnType'] =='N' 
   && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='TurnDown'
   && $row['slopeDirection'] =='Down' 
   && $row['emaConflict'] =='5-3-G'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-4(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-4(R)';
		}
   }

   // Step 6-24-5
   if ($row['PreviousTurnType'] =='TurnDown' 
   && $row['PreviousTurnTypeBack2'] =='TurnUp'
   
   
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-5(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-5(R)';
		}
   } 

   // Step 6-24-6
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack4'] =='TurnDown'
   && $row['slopeDirection'] =='Down' 
   && $row['emaConflict'] =='5-3-G'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-6(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-6(R)';
		}
   }



   // Step 6-25
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' 
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25(R)';
		}
   }
   // Step 6-25-2
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' && (abs($row['MACDHeight']) < 4)
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25-2(G)-'. abs($row['MACDHeight']);
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25-2(R)-'.abs($row['MACDHeight']);
		}
   }

   // Step 6-25-3
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5)
   ) {
	   if ( $row['thisColor'] =='Green') {	
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25-3(R)';
		}
   }
   // Step 6-25-32
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5) 
   && $row['emaConflict'] =='5-3-G'
   ) {
	   if ( $row['thisColor'] =='Red') {	
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25-32(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25-32(R)';
		}
   }


   // Step 6-25-4
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5)
   && $row['CutPointType'] == '5->3'
   ) {
	   if ( $row['thisColor'] =='Red') {	
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25-4(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25-4(R)';
		}
   }
   // Step 6-26
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' 
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26(R)';
		}
   }
   // Step 6-26-2
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' && $row['MACDHeight'] < 4
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-2(R)';
		}
   }

   // Step 6-26-3
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' && $row['CutPointType'] =='3->5'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-3(R)';
		}
   }

   // Step 6-26-4
   if ($row['PreviousTurnType'] =='N' 
   && $row['PreviousTurnTypeBack2'] =='TurnDown'
   && $row['PreviousTurnTypeBack3'] =='N'
   && $row['slopeDirection'] =='Down' 

   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-4(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-4(R)';
		}
   }
   // Step 6-26-5
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-5(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-5(R)';
		}
   }

   // Step 6-26-6
   if ($row['PreviousTurnType'] =='N' 
   && $row['PreviousTurnTypeBack2'] =='TurnDown'
   && $row['PreviousTurnTypeBack3'] =='N'
   && $row['slopeDirection'] =='Down' 
   && $row['emaConflict'] == '3-5-R'

   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-6(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-6(R)';
		}
   } 

   // Step 6-26-7
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp'
   && $row['PreviousTurnTypeBack3'] =='TurnDown'
   
   ) {
	   if ( $row['thisColor'] =='Red' || $row['thisColor'] =='Equal') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-7(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-7(R)';
		}
   }

   // Step 6-26-8
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='N'
   && $row['PreviousTurnTypeBack4'] =='N'
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R'
   && $row['MACDConvergence'] =='Conver'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-8(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-8(R)';
		}
   }

   // Step 6-27
   if ($row['PreviousTurnType'] == 'N' && 
	  $row['PreviousTurnTypeBack2'] == 'N' && 
	  $row['emaConflict'] == '3-5-R' && 
	  $row['slopeDirection'] =='Up'  && 
	  $row['PreviousTurnTypeBack4'] == 'TurnUp'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-27(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-27(R)';
		}
   }

    

/*
   if (abs($row['MACDHeight'] *1000*1000) < 1	) {
	   $thisAction = 'Idle'; 
	   $forecastColor = 'Gray';
	   $forecastClass = 'bgGray';
  	   $ActionClass = 'bgGray'; 
	   $actionReason .= '->Code6-21(E)';

   }

   if (abs($pipSize) <= 0.02	) {
	   $thisAction = 'Idle'; 
	   $forecastColor = 'Gray';
	   $forecastClass = 'bgGray';
  	   $ActionClass = 'bgGray'; 
	   $actionReason .= '->Code6-22(E)'. '=' . $pipSize;

   } 
*/
// *******************   สิ้นสุด Case ตรงนี้   ************************

 // 
   $thisColor = $row['thisColor'] ;
   // Step 6-7 ตรวจสอบ แท่งแดง UWick = 0 ; Lwick <=10% ให้ เป็นสีเขียว

   $sql = "select * from RawData where id=?"; 
   $params = array($row['id']);
   $rowRawCandle = pdoRowSet($sql,$params,$this->pdo) ;
   $pip= ($rowRawCandle['close'] > $rowRawCandle['open'])*10000;
   if ($rowRawCandle['close'] > $rowRawCandle['open']) { //Green
	   $totalHeight=abs(($rowRawCandle['max'] - $rowRawCandle['min'])*1000*1000);
	   $UHeight = abs(($rowRawCandle['max'] - $rowRawCandle['close'])*1000*1000);
	   $BodyUwick = abs($rowRawCandle['close'] - $rowRawCandle['open'])*1000*1000;
	   $Lwick = abs($rowRawCandle['open'] - $rowRawCandle['min'])*1000*1000;

   } else {
	   $totalHeight=abs(($rowRawCandle['max'] - $rowRawCandle['min'])*1000*1000);
	   $UHeight = abs(($rowRawCandle['max'] - $rowRawCandle['open'])*1000*1000);
	   $BodyUwick = abs($rowRawCandle['open'] - $rowRawCandle['close'])*1000*1000;
	   $Lwick = abs($rowRawCandle['close'] - $rowRawCandle['min'])*1000*1000;
	   $BodywickPercent = round(($BodyUwick/$totalHeight)*100,2) ;

   } 
    
 



return array($thisAction,$forecastColor,$forecastClass,$ActionClass,$actionReason,$nextColorClass,$remark,$macd,$macdConver,$slopeValue,$slopeDirection,$pipSize,$delTapip) ;

} // end function


function getActionFromIDVer2($pdo,$tableName,$row,$macdThershold,$lastMacdHeight) { 

   $LockedAction = false ;
   //$slopeValue = $row['ema3SlopeValue'] ;
   $slopeDirection = $row['slopeDirection'] ;
   $row['MACDHeight']= $row['MACDHeight']*1000*1000 ;
   $macd = $row['MACDHeight'] ; 
   //$tableName = 'AnalyEMA_HistoryForLab';
   
   $pipSize = round(abs($row['pip'])/10,2);
//   $delTapip = abs(abs($row['pip']/10) - abs($row['previousPIP']/10)) ;
   $delTapip = 0;

   //$sql = "select ema3 from ". $tableName ." where id=" . ($row['id']-1);    
   $sql="SELECT a.id ,b.id,b.minuteno, a.ema3,b.ema3, (b.ema3-a.ema3)*1000*1000 as differ
    FROM  $tableName a INNER join " .$tableName . " b on b.id-1 = a.id  WHERE b.id = ?";
   $params = array($row['id']);   
   $rowDifferEMA = pdoRowSet($sql,$params,$this->pdo) ;   
   //$previousEMA3 =pdogetValue($sql,$params,$pdo) ;
   
   //$slopeValue = ($previousEMA3 - $row['ema3'])*1000*1000 ;
   $slopeValue = $rowDifferEMA['differ']  ;

   if ($slopeValue < 0) {
      $slopeDirection = 'Down' ;
   } else {
      $slopeDirection = 'Up' ;
   }
   
   
   


   if ($macd < $lastMacdHeight) {
	  $macdConver = 'Conver';
   } else {
      $macdConver = 'Diver';
   }
   $lastMacdHeight = $macd;

   //$macdConver = $row['MACDConvergence'] ;
   $thisAction = ''; $remark = '';
   $forecastColor = ''; 
   $forecastClass = '';
   $winStatus = '';
   //$slopeValue = $row['ema3SlopeValue'] ;
   $actionReason = '';

   $sql = "select thisColor  from $tableName where id=? "; 
   $params = array($row['id']+1);
   $nextColor =pdogetValue($sql,$params,$pdo) ;

   if ($nextColor=='Green') {
	   $nextColorClass ='bgGreen' ;
   }
   if ($nextColor=='Red') {
	   $nextColorClass ='bgRed' ;
   }
   if ($nextColor=='Equal') {
	   $nextColorClass ='bgGray' ;
   } 
//
// *******************   เริ่ม Case ตรงนี้   ************************
  // Step 1-1
   if ($slopeDirection=='Down') {
      $thisAction = 'PUT'; 
	  $forecastColor = 'Red';
	  $forecastClass = 'bgRed';
	  $ActionClass = 'bgRed';
	  $actionReason = 'Code1-1(R)';
   }
   if ($slopeDirection=='Up') {
      $thisAction = 'CALL'; 
	  $forecastColor = 'Green';
	  $forecastClass = 'bgGreen';
	  $ActionClass = 'bgGreen';
	  $actionReason = 'Code1-1(G)';
   }

   // Step 1-1-2
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown') {
    
      if ($row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-2(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-2(R)';
	 }
   }
   // Step 1-1-3
   if (
	   $row['slopeDirection'] =='Down' && 
	   $row['CutPointType'] =='3->5' && 
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown') {

    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-3(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-3(R)';
	 }
   }
   // Step 1-1-4
   if (
	   $row['slopeDirection'] =='Down' && 
	   $row['CutPointType'] =='N' && 
	   $row['PreviousTurnType'] =='' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='N') {

    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-4(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-4(R)';
	 }
   }
   // Step 1-1-5
   if (
	   $row['slopeDirection'] =='Down' && 
	   $row['CutPointType'] =='N' && 
	   $row['PreviousTurnType'] =='' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='N') {

    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-4(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-4(R)';
	 }
   }
   // Step 1-1-6
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='N' &&
       $row['PreviousTurnTypeBack4'] =='TurnUp' 
	   ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-6(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-6(R)';
	 }
   }
   // Step 1-1-7
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
       $row['CutPointType'] =='5->3' 
	   ) {
    
      if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-7(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-7(R)';
	 }
   }

   // Step 1-1-8
   if (
	   $row['slopeDirection'] =='Down' && 
	   $row['CutPointType'] =='3->5' && 
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
       $row['MACDHeight'] < 8 
   
   ) {

    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-3(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-3(R)';
	 }
   } 

   // Step 1-1-9
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
       $row['PreviousTurnTypeBack4'] =='N' 
       
	   ) {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-9(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-9(R)';
	 }
   }

   // Step 1-1-10
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack4'] =='N' 
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-10(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-10(R)';
	 }
   }

 // Step 1-1-11
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='N' &&
	   $row['PreviousTurnTypeBack4'] =='TurnUp' 
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-11(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-11(R)';
	 }
   }
   // Step 1-1-12
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
       $row['CutPointType'] == '3->5'
	  ) 
  {
    
      if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-10(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-10(R)';
	 }
   }
   // Step 1-1-13
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
       $row['CutPointType'] == 'N'
	  ) 
  {
    
      if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-13(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-13(R)';
	 }
   }
   // Step 1-1-14
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack4'] =='TurnUp'  && 
       $row['CutPointType'] == 'N'
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-13(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-13(R)';
	 }
   }
   // Step 1-1-15
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
       $row['CutPointType'] == 'N'
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-15(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-15(R)';
	 }
   }
  // Step 1-1-16
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
       $row['CutPointType'] == 'N'
	  ) 
  {
    
      if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-16(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-16(R)';
	 }
   }

   // Step 1-1-15
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
       $row['CutPointType'] == 'N'
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-15(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-15(R)';
	 }
   }
  // Step 1-1-17
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='N' &&
	   $row['PreviousTurnTypeBack4'] =='TurnDown'  && 
       $row['CutPointType'] == '3->5'
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-17(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-17(R)';
	 }
   }

   // Step 1-1-18
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
	   $row['CutPointType'] == '5->3'
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-18A(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-18A(R)';
	 }
   }

   // Step 1-1-18-2
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
	   $row['CutPointType'] == '5->3' && 
	   $row['slopeDirection'] == 'Up' && 
       $row['MACDHeight'] > 10 
	  ) 
  {
    
      if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-18-2(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-18-2(R)';
	 }
   }

   // Step 1-1-19
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['PreviousTurnTypeBack3'] =='N' &&
	   $row['PreviousTurnTypeBack4'] =='TurnDown'  && 
	   $row['CutPointType'] == 'N'
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-18(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-18(R)';
	 }
   }

   // Step 1-1-20
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack3'] =='N' &&
	   $row['PreviousTurnTypeBack4'] =='N'  && 
	   $row['CutPointType'] == '5->3'
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-20(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-20(R)';
	 }
   }

   // Step 1-1-21
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack4'] =='TurnDown'  && 
	   $row['CutPointType'] == 'N'
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-21(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-21(R)';
	 }
   }

   // Step 1-1-22
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack4'] =='TurnDown'  && 
	   $row['CutPointType'] == 'N'
	  ) 
  {
    
      if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-22A(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-22A(R)';
	 }
   }

// Step 1-1-22B
   if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['PreviousTurnTypeBack3'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack4'] =='TurnDown'  && 
	   $row['CutPointType'] == 'N' && 
	   $row['slopeDirection'] == 'Down' && 
       $row['thisColor'] == 'Red' 


	  ) 
  {
    
      if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-22B(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-22B(R)';
	 }
   }




/*

   if ($slopeDirection=='Up') {
      $thisAction = 'CALL'; 
	  $forecastColor = 'Green';
	  $forecastClass = 'bgGreen';
	  $ActionClass = 'bgGreen';
	  $actionReason = 'Code1-1(G)';
   }

*/

   if ($slopeDirection=='P') {
     $thisAction = 'Idle'; 
	 $remark = ' Slope ขนาน ';
   }

   if ($row['thisColor']=='Equal') {
     $thisAction = 'Idle'; 
	 $remark .= ' ,Equal ';
   }

   if (abs($row['MACDHeight']) < $macdThershold) {
      if ($actionReason !='Code2') {      
        $thisAction = 'Idle'; 
	    $remark .= ' ,MACD ..น้อยกว่า  ' .$macdThershold;
	  }
   }
   
 
    // Step 2-1
   if ($row['emaConflict'] == '3-5-R' && $row['MACDConvergence'] == 'Diver' && $row['slopeDirection'] != 'Up') {
		   $thisAction = 'PUT'; 
		   $forecastColor = 'Red';
		   $forecastClass = 'bgRed';
  	       $ActionClass = 'bgRed'; 
		   $actionReason .= '->Code2_1(R)';
   } 

   // Step 2-2
   if ($row['emaConflict'] == '3-5-R' && $macdConver == 'Conver'
      && $row['slopeDirection'] =='Down' && $row['PreviousTurnType']=='TurnDown'
   ) {
		   $thisAction = 'PUT'; 
		   $forecastColor = 'Red';
		   $forecastClass = 'bgRed';
  	       $ActionClass = 'bgRed'; 
		   $actionReason .= '->Code2_2(R)';
		   $LockedAction = false;

   } 

    
 
   // ตรวจสอบการ Sideway
      // Step 3-1
   if ($row['PreviousSlopeDirection'] !== $row['slopeDirection'] && 
	   $LockedAction == false && $row['PreviousSlopeDirection'] !=='N') {

	   // toggle thisColor
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code3-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code3-1(R)';
		}
   }

   // Step 3-2
   if ($row['PreviousSlopeDirection'] !== $row['slopeDirection'] && 
	   $slopeDirection =='Down' && $row['PreviousSlopeDirection'] !=='N') {

	   // toggle thisColor
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code3-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code3-2(R)';
		}
   }

// เพิ่มเติมจาก Step-6 ใน Python 
   // Step 6-1
   if ($row['PreviousTurnType'] =='TurnUp' && $row['MACDConvergence'] =='Diver' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-1(R)';
		}
   }
   // Step 6-2
   if ($row['PreviousTurnType'] =='TurnDown' && $row['emaConflict'] =='3-5-R' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-2(R)';
		}
   }
   // Step 6-3
   if ($row['PreviousTurnType'] =='TurnDown' && $row['emaConflict'] =='N' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-3(R)';
		}
   }
   // Step 6-4
   if ($row['PreviousTurnType'] =='TurnUp' && $row['emaConflict'] =='5-3-G' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-4(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-4(R)';
		}
   }
   // Step 6-5
   //if ($row['PreviousTurnType'] =='TurnUp' && $row['TurnType'] =='TurnDown' ) {
   if ($row['PreviousTurnType'] =='TurnUp'  ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-5(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-5(R)';
		}
   }
   // Step 6-5-2
   //if ($row['PreviousTurnType'] =='TurnUp' && $row['TurnType'] =='TurnDown' ) {
   if ($row['PreviousTurnType'] =='TurnUp' && 
       $row['PreviousTurnTypeBack2'] =='TurnDown' && 
	   $row['slopeDirection']=='Up' && $row['emaConflict']=='N'  ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-5-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-5-2(R)';
		}
   }

   // Step 6-5-3
   //if ($row['PreviousTurnType'] =='TurnUp' && $row['TurnType'] =='TurnDown' ) {
   if ($row['PreviousTurnType'] =='TurnUp' && 
       $row['PreviousTurnTypeBack2'] =='TurnDown' && 
	   $row['PreviousTurnTypeBack3'] =='N' && 
	   $row['slopeDirection']=='Up' && $row['emaConflict']=='N' && $row['ema3SlopeValue'] <10 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-5-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-5-3(R)';
		}
   }
   // Step 6-6
   //if ($row['PreviousTurnType'] =='TurnDown' && $row['TurnType'] =='TurnUp' ) {
   if ($row['PreviousTurnType'] =='TurnDown' && $row['emaConflict'] !='3-5-R' ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-6(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-6(R)';
		}
   }
   // Step 6-7
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-7(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-7(R)';
		}
   }

   // Step 6-7-1
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
   && $row['PreviousTurnTypeBack2'] == 'TurnUp'
   
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-7-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-7-1(R)';
		}
   }

    // Step 6-7-2
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
   && $row['PreviousTurnTypeBack2'] == 'TurnUp'
   && $row['CutPointType'] == '5->3'
   
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-7-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-7-1(R)';
		}
   }

   // Step 6-7-3
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
   && $row['PreviousTurnTypeBack2'] == 'TurnUp'
   && $row['PreviousTurnTypeBack3'] == 'TurnDown'
   
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-7-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-7-3(R)';
		}
   }

   // Step 6-7-4
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
   && $row['PreviousTurnType'] == 'N'
   && $row['PreviousTurnTypeBack2'] == 'TurnUp'
   && $row['PreviousTurnTypeBack3'] == ''
   && $row['PreviousTurnTypeBack4'] == 'TurnDown'

   
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-7-4(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-7-4(R)';
		}
   }

   // Step 6-8
   if ($row['slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R' ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-8(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-8(R)';
		}
   }

   // Step 6-8-2
   if ($row['slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R' &&
	   $row['PreviousTurnTypeBack2'] =='TurnDown' && $row['MACDHeight'] <=1
	   
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-8-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-8-2(R)';
		}
   }
   // Step 6-9
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='5-3-G' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9(G77)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9(R77)';
		}
   }
   // Step 6-9-1
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='5-3-G' 
   && $row['PreviousTurnType'] == 'N' 
   && $row['PreviousTurnTypeBack2'] == 'TurnUp' 
   && $row['PreviousTurnTypeBack3'] == 'N' 

	   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9-1(R)';
		}
   }

   // Step 6-9-2
   if ($row['slopeDirection'] =='Up' 
   && $row['emaConflict'] =='5-3-G' 
   && $row['PreviousTurnType'] == 'N' 
   && $row['PreviousTurnTypeBack2'] == 'TurnUp' 
   && $row['PreviousTurnTypeBack3'] == 'N' 
   && $row['MACDHeight'] < 4

	   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9-2(R)';
		}
   }

// Step 6-9-3
   if ($row['slopeDirection'] =='Up' && $row['emaConflict'] =='5-3-G'
   && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='TurnUp'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9-3(R)';
		}
   }

   // Step 6-9-4
   if ($row['slopeDirection'] =='Up' && 
	   $row['emaConflict'] =='5-3-G' &&
	   $row['PreviousTurnType'] == 'TurnUp'
	   
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-9-4(G77)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-9-4(R77)';
		}
   }

// Step 6-10
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnType'] =='TurnDown' && $row['emaConflict'] =='N' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-10(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-10(R)';
		}
   }
   // Step 6-11
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' && $row['emaConflict'] =='N' ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-11(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-11(R)';
		}
   }
   // Step 6-12
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='TurnUp' && $row['emaConflict'] =='3-5-R' && abs($row['MACDHeight']*1000*1000) < 10 ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-12(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-12(R)';
		}
   }
   // Step 6-13
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N' && $row['emaConflict'] =='5-3-G' && $row['slopeDirection']=='Up') {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-13(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-13(R)';
		}
   }
   // Step 6-14
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' && abs($row['ema3SlopeValue']) < 0.9 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-14(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-14(R)';
		}
   }
   // Step 6-15
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' && abs($row['ema3SlopeValue']) < 0.9 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-15(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-15(R)';
		}
   }
   // Step 6-16
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' && abs($row['ema3SlopeValue']) > 0.9 ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-16(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-16(R)';
		}
   }

   // Step 6-16-2
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp'
   && $row['slopeDirection'] =='Up'
   && abs($row['ema3SlopeValue']) > 20 ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-16(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-16(R)';
		}
   }
   // Step 6-16-3
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp'
   && $row['slopeDirection'] =='Down'
   && abs($row['ema3SlopeValue']) > 20 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-16(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-16(R)';
		}
   }

    // Step 6-17
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='TurnDown' && ($row['emaConflict']) == '5-3-G' ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-17(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-17(R)';
		}
   }
    // Step 6-17@
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='TurnDown' && ($row['emaConflict']) == '5-3-G' && $row['slopeDirection'] =='Up') {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-@17(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-@17(R)';
		}
   }
   // Step 6-17-2
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='TurnDown' 
	   && $row['PreviousTurnTypeBack3'] =='N' 
       && $row['PreviousTurnTypeBack4'] =='TurnUp' 
	   && ($row['emaConflict']) == '5-3-G' && $row['slopeDirection'] =='Up') {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-17-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-17-2(R)';
		}
   }
    // Step 6-18
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N' && ($row['emaConflict']) == '5-3-G' ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-18(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-18(R)';
		}
   }
   // Step 6-19
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' 
    && $row['emaConflict'] == '3-5-R') {
	   //&& abs($row['ema3SlopeValue']) < 0.9 ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-19(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-19(R)';
		}
   }
   // Step 6-19-2
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp' 
    && $row['emaConflict'] == 'N') {
	   //&& abs($row['ema3SlopeValue']) < 0.9 ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-19-2@(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-19-2@(R)';
		}
   }
// Step 6-20
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'  ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-20(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-20(R)';
		}
   }

// Step 6-20-2
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N' &&
	   $row['MACDHeight'] < 4
	   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-20-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-20-2(R)';
		}
   }

// Step 6-21
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='TurnDown'
   && $row['emaConflict'] == '5-3-G'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-21(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-21(R)';
		}
   }
// Step 6-22
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['emaConflict'] == '3-5-R' && $row['slopeDirection'] =='Up'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-22(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-22(R)';
		}
   }

   // Step 6-22-1
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['emaConflict'] == '3-5-R' && $row['slopeDirection'] =='Up'
   && (abs($row['MACDHeight']) > 15)
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-22-1(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-22-1(R)';
		}
   }

   // Step 6-22-2
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='TurnUp' && $row['PreviousTurnTypeBack4'] =='TurnDown'

   && $row['emaConflict'] == '3-5-R' && $row['slopeDirection'] =='Up'

   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-22-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-22-2(R)';
		}
   }
   // Step 6-22-3
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='TurnUp'
   && $row['emaConflict'] == '3-5-R' && $row['slopeDirection'] =='Up'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-22-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-22-3(R)';
		}
   }


   // Step 6-23
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp'
   && abs($row['ema3SlopeValue']) < 0.8
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-5(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-5(R)';
		}  
   }
   // Step 6-24
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='5-3-G'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24(R)';
		}
   }



   // Step 6-24-2
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && abs($row['ema3SlopeValue']) < 8
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='5-3-G'

   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-2(R)';
		}
   }
   // Step 6-24-3
   if ($row['PreviousTurnType'] =='N' 
   && $row['PreviousTurnTypeBack2'] ==''
   && $row['PreviousTurnTypeBack3'] =='TurnDown'
   && $row['PreviousTurnTypeBack4'] =='TurnUp'
   && (abs($row['ema3SlopeValue']) < 5)
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='5-3-G'

   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-2(R)';
		}
   }

   // Step 6-24-4
   if ($row['PreviousTurnType'] =='N' 
   && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='TurnDown'
   && $row['slopeDirection'] =='Down' 
   && $row['emaConflict'] =='5-3-G'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-4(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-4(R)';
		}
   }

   // Step 6-24-5
   if ($row['PreviousTurnType'] =='TurnDown' 
   && $row['PreviousTurnTypeBack2'] =='TurnUp'
   
   
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-5(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-5(R)';
		}
   } 

   // Step 6-24-6
   if ($row['PreviousTurnType'] =='N' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack4'] =='TurnDown'
   && $row['slopeDirection'] =='Down' 
   && $row['emaConflict'] =='5-3-G'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-6(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-6(R)';
		}
   }



   // Step 6-25
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' 
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25(R)';
		}
   }
   // Step 6-25-2
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' && (abs($row['MACDHeight']) < 4)
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25-2(G)-'. abs($row['MACDHeight']);
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25-2(R)-'.abs($row['MACDHeight']);
		}
   }

   // Step 6-25-3
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5)
   &&  $row['CutPointType'] == ''
   ) {
	   if ( $row['thisColor'] =='Green') {	
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25-3(R)';
		}
   }
   // Step 6-25-32
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5) 
   && $row['emaConflict'] =='5-3-G'
   ) {
	   if ( $row['thisColor'] =='Red') {	
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25-32(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25-32(R)';
		}
   }

   // Step 6-25-33
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5) 
   && $row['emaConflict'] =='5-3-G'
   ) {
	   if ( $row['thisColor'] =='Green') {	
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25-33(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25-33(R)';
		}
   }




   // Step 6-25-4
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5)
   && $row['CutPointType'] == '5->3'
   ) {
	   if ( $row['thisColor'] =='Red') {	
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-25-4(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-25-4(R)';
		}
   }
   // Step 6-26
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' 
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26(R)';
		}
   }
   // Step 6-26-2
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' && $row['MACDHeight'] < 4
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-2(R)';
		}
   }

   // Step 6-26-3
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' && $row['CutPointType'] =='3->5'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-3(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-3(R)';
		}
   }
   // Step 6-26-3-2
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['thisColor'] =='Red'
   && $row['slopeDirection'] =='Down' && $row['CutPointType'] =='3->5'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-3-2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-3-2(R)';
		}
   }


   // Step 6-26-4
   if ($row['PreviousTurnType'] =='N' 
   && $row['PreviousTurnTypeBack2'] =='TurnDown'
   && $row['PreviousTurnTypeBack3'] =='N'
   && $row['slopeDirection'] =='Down' 

   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-4(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-4(R)';
		}
   }
   // Step 6-26-5
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-5(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-5(R)';
		}
   }

   

   // Step 6-26-6
   if ($row['PreviousTurnType'] =='N' 
   && $row['PreviousTurnTypeBack2'] =='TurnDown'
   && $row['PreviousTurnTypeBack3'] =='N'
   && $row['slopeDirection'] =='Down' 
   && $row['emaConflict'] == '3-5-R'

   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-6(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-6(R)';
		}
   } 

   // Step 6-26-7
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='TurnUp'
   && $row['PreviousTurnTypeBack3'] =='TurnDown'
   
   ) {
	   if ( $row['thisColor'] =='Red' || $row['thisColor'] =='Equal') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-7(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-7(R)';
		}
   }

   // Step 6-26-8
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='N'
   && $row['PreviousTurnTypeBack4'] =='N'
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R'
   && $row['MACDConvergence'] =='Conver'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-8(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-8(R)';
		}
   }

   // Step 6-26-9
   if ($row['PreviousTurnType'] =='TurnDown' && $row['PreviousTurnTypeBack2'] =='N'
   && $row['PreviousTurnTypeBack3'] =='N'
   && $row['PreviousTurnTypeBack4'] =='TurnUp'
   && $row['slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R'
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-26-9(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-26-9(R)';
		}
   }

   // Step 6-27
   if ($row['PreviousTurnType'] == 'N' && 
	  $row['PreviousTurnTypeBack2'] == '' && 
	  $row['emaConflict'] == '3-5-R' && 
	  $row['slopeDirection'] =='Up'  && 
	  $row['PreviousTurnTypeBack4'] == 'TurnUp'
   ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-27(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-27(R)';
		}
   }

   // Step 6-28
   if (
	  $row['PreviousTurnType'] == 'TurnDown' && 
	  $row['PreviousTurnTypeBack2'] == 'N' && 
	  $row['emaConflict'] == 'N' && 
      $row['thisColor'] != $row['previousColor']    
	  
   ) {
	   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-28(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-28(R)';
		}
   }
  

/*
   if (abs($row['MACDHeight'] *1000*1000) < 1	) {
	   $thisAction = 'Idle'; 
	   $forecastColor = 'Gray';
	   $forecastClass = 'bgGray';
  	   $ActionClass = 'bgGray'; 
	   $actionReason .= '->Code6-21(E)';

   }

   if (abs($pipSize) <= 0.02	) {
	   $thisAction = 'Idle'; 
	   $forecastColor = 'Gray';
	   $forecastClass = 'bgGray';
  	   $ActionClass = 'bgGray'; 
	   $actionReason .= '->Code6-22(E)'. '=' . $pipSize;

   } 
*/ 
// ***************************** Extra Check *************************
//	Code1-1(R) 
/*    
    $AnalyList=  $this->getBackAnalyDataObject($row['id'] );
	// มี 3 Object คือ  0,1,2 โดย เรียง คือ  id-3,id-2, id-1
	$lastIndex =count($AnalyList)-1 ;
	$previousColor = $AnalyList[1]->thisColor ;
    if ($actionReason=='Code1-1(R)') {
       if ($previousColor != $row['thisColor']) {
		   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code1-1-2@Ex(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code1-1-2@Ex(R)';
		}
     }

	 if ($actionReason=='Code6-24-5(G)') {
       if ($previousColor != $row['thisColor']) {
		   if ( $row['thisColor'] =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-24-5@(G)';
		   } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-24-5@(R)';
		  }
	   }
     }

	  


    }



 // 
   $thisColor = $row['thisColor'] ;
   // Step 6-7 ตรวจสอบ แท่งแดง UWick = 0 ; Lwick <=10% ให้ เป็นสีเขียว

   $sql = "select * from RawData where id=?"; 
   $params = array($row['id']);
   $rowRawCandle = pdoRowSet($sql,$params,$this->pdo) ;
   $pip= ($rowRawCandle['close'] > $rowRawCandle['open'])*10000;
   if ($rowRawCandle['close'] > $rowRawCandle['open']) { //Green
	   $totalHeight=abs(($rowRawCandle['max'] - $rowRawCandle['min'])*1000*1000);
	   $UHeight = abs(($rowRawCandle['max'] - $rowRawCandle['close'])*1000*1000);
	   $BodyUwick = abs($rowRawCandle['close'] - $rowRawCandle['open'])*1000*1000;
	   $Lwick = abs($rowRawCandle['open'] - $rowRawCandle['min'])*1000*1000;

   } else {
	   $totalHeight=abs(($rowRawCandle['max'] - $rowRawCandle['min'])*1000*1000);
	   $UHeight = abs(($rowRawCandle['max'] - $rowRawCandle['open'])*1000*1000);
	   $BodyUwick = abs($rowRawCandle['open'] - $rowRawCandle['close'])*1000*1000;
	   $Lwick = abs($rowRawCandle['close'] - $rowRawCandle['min'])*1000*1000;
	   $BodywickPercent = round(($BodyUwick/$totalHeight)*100,2) ;

   } 
    */
 
// *******************   สิ้นสุด Case ตรงนี้   ************************

$nextColorClass = '';
return array($thisAction,$forecastColor,$forecastClass,$ActionClass,$actionReason,$nextColorClass,$remark,$macd,$macdConver,$slopeValue,$slopeDirection,$pipSize,$delTapip) ;

} // end function

function SaveLab() { 


$sql="REPLACE INTO labSidewayDetail(CaseActionNo,Seriesname,curpair,no, id, minuteno, ADX, MACDHeight, MACDConverDiver,SlopeDirection,EMAConflict, CutPointType, PreviousTurnType,action, thisColor, forecastColor, tradeNo, winStatus,lossCon,remark) VALUES(" ;
$st = str_repeat('?,',20);
$sql = $sql .substr($st,0,strlen($st)-1) . ')';

$params = array($CaseActionNo, $Seriesname,$curpair,$no, $id, $minuteno, $ADX, $MACDHeight, 
	$MACDConverDiver,
	$SlopeDirection, $EMAConflict, $CutPointType, $PreviousTurnType, $action, $thisColor, $forecastColor, $tradeNo, $winStatus,$lossCon, $remark);

$sValue = '' ;
for ($i=0;$i<=count($params)-1;$i++) {
   $sValue .= '"' . $params[$i] . '",' ;
}


$sValue = substr($sValue,0,strlen($sValue)-1) . ')';
//echo $sValue;
//echo implode( ' - ' , $params) . '<hr>' ;
 
if (!pdoExecuteQuery($pdo,$sql,$params)) {
   echo 'Error' ;
   return false;
} else {
  //echo 'Insert Finished <hr>';
} 


} // end function


function CalWinByID($id,$vername,$isSaveLab='y',$timeframe=5) { 

 
  $pdo = $this->pdo;
  $timeframe == 15 ;
  //$pdo->exec("set names utf8mb4") ;
  if ($timeframe == 15) {  
    $tableName = 'AnalyEMA_HistoryForLab' ;
  }
  if ($timeframe == 5) {  
    $tableName = 'AnalyEMA_HistoryForLab_5M' ;
  }
  $tableName = 'AnalyEMA_HistoryForLab' ;
  
  $sql = "select * from $tableName WHERE id>= ? and id<=? "; 
  //echo '---->' . $sql ;
  $params = array($id,$id+30);

  $rs= pdogetMultiValue2($sql,$params,$pdo) ;
  if ($rs->rowCount() == 0) {
	  echo 'No Data Found ' ;
      return false;
  }
  
  $macdThershold = 8 ; $lastMacdHeight = 0 ;
  $numTrade= 0 ; $numLoss=0 ; $lossCon = 0 ;
  $allActionReason  = '';
  $CaseActionNo = $id;
  
  $Seriesname = 'ByClassTrade' ;
  $curpair= 2;
  $LotID = $id ;
  $no = 1 ; $totalUseMinute= 0 ; 
  //$vername = 'Ver2' ;
  $startMoney = 1; 
  $balance= 0 ;
  $MoneyTrade= $startMoney ;
  $useExtra2 = 'y'; $useExtra3 = 'n';

  
  while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
    //echo "<br>" . $row['id'] . ':: EmaConflict999 = ' . $row['emaConflict'];
    if ($vername == 'Ver1' ) {     
	  list($thisAction,$forecastColor,$forecastClass,$ActionClass,$actionReason,
	  $nextColorClass,$remark,$macd,$macdConver,$slopeValue,$slopeDirection,$pipSize,$delTapip) =$this->getActionFromID($pdo,$tableName,$row,$macdThershold,$lastMacdHeight)    ;
	}
    if ($vername == 'Ver2' ) {     
	  list($thisAction,$forecastColor,$forecastClass,$ActionClass,$actionReason,
	  $nextColorClass,$remark,$macd,$macdConver,$slopeValue,$slopeDirection,$pipSize,$delTapip) =$this->getActionFromIDVer2($pdo,$tableName,$row,$macdThershold,$lastMacdHeight);
      
      if ($useExtra2 == 'y') {      
		  list($thisAction2,$forecastColor2,$forecastClass2,$ActionClass2,$actionReason2)= $this->useExTra($row) ;
		  if ($thisAction2 !='') {
			$thisAction = $thisAction2 ;
			$forecastColor = $forecastColor2 ;
			$actionReason .= '->'. $actionReason2 ;
			
		  }
	  }
	  if ($useExtra3 == 'y') { 
		  require_once('labVer2.php'); 
		  $AnalyObj[] = $row;
		  list($action999,$actionCode) = getActionClassVTmp($AnalyObj,$i=0);
		  unset($AnalyObj);
		   
		  if ($action999 != '') {
			  $thisAction = $action999 ;
			  if ($action999 == 'CALL') {
				  $forecastColor ='Green';
			  }
			  if ($action999 == 'PUT') {
				  $forecastColor ='Red';
			  }
			  $actionReason .= '->vTmp::'. $actionCode ;
			  //echo '<br>'. "-->" . $actionReason ;
		  }
	  } // end if

	} // end if ver2




	$allActionReason .= $actionReason . ',' ;
	$lastActionReason = $actionReason;
	$sql = "select thisColor from $tableName where id=?"; 
	$nextID = $row['id']+1;
	$params = array($nextID);
	$resultColor =pdogetValue($sql,$params,$pdo) ;
	$profit = 0 ;
	if ($thisAction != 'Idle') {
        $numTrade++ ;	
		if ($resultColor == 'Equal') {
			$winStatus = '???' ;
		} else {
			if ($forecastColor == $resultColor) {
			   if ($resultColor !='Equal') {
				 $winStatus  = 'win' ;
				 $profit = $MoneyTrade* 0.85 ;
			   } else {
				 $winStatus = '???A' ;
				 $profit = 0 ;
			   }
			} else {
               if ($resultColor !='Equal') {  
				$winStatus = 'loss' ;
				$numLoss++ ;
				$profit = $MoneyTrade * (-1) ;
			   } else {
                 $winStatus = '???' ;
				 $profit = 0 ;
			   }
			}
		}
    } else {
        $winStatus= '???B' ;		
		$profit = 0 ;
	}  
	$remark = '';
	$curpair = 'EURUSD';
	$balance = $balance + $profit ;
	if ($winStatus == 'loss') {
		$MoneyTrade =  ceil(abs($balance) * 2);
	}
	
	if ($isSaveLab == 'y') {	
		//อยู่ในไฟล์  AjaxLabSideway.php ลงไปยัง  Table= labSidewayDetail
		/*
		SaveLabDetail($pdo,$vername,$tableName,$CaseActionNo,$Seriesname, $curpair,$LotID, $no,$row['id'], 
			$row['minuteno'], $row['ADX'], $macd, 
			$macdConver,$slopeDirection, $row['emaConflict'], $row['CutPointType'], $row['PreviousTurnType'], $thisAction, $row['thisColor'], $forecastColor, ($numTrade-1), $winStatus,$numLoss, $remark,$actionReason,"php") ;
        */ 
	}

   

	if ($winStatus== 'win') {
		break ;
	} else {
       $no++ ; 
	   $totalUseMinute=  $no ; 
	}

	 

 


  } // End While Loop

//  echo  $id .  ' = ' .$lastActionReason . "<br>"; 
  $sql = "UPDATE $tableName set totalTrade=?,totalLoss=? ,actionReason=? where id=? "; 
  $params = array($numTrade,$numLoss,$lastActionReason,$id);
  if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
     echo 'Error' ;
     return false;
  }  
/*
  $sql = "REPLACE INTO labSideway9999(
CaseActionNo,Extra1,Extra2,Extra3,SeriesName,
LotID,endID,totalUseMinute,numsideway, totalTrade,
totalLoss,className,actionReason) VALUES (
?,?,?,?,?,
?,?,?,?,?,
?,?,?)";

$lossCon = $numLoss ;
if ($lossCon == 0  ) { $className = 'bgGold'; }
if ($lossCon == 1  ) { $className = 'bgGray'; }
if ($lossCon == 2  ) { $className = 'bgBronze2'; }
if ($lossCon == 3 ) { $className = 'bgGreen'; }
if ($lossCon == 4 ) { $className = 'bgPink'; }
if ($lossCon =  5 ) { $className = 'bgRed'; }
if ($lossCon >= 6 ) { $className = 'bgBroke'; }

$data['extra1'] = $data['extra2'] = $data['extra3'] = 'n';
$SeriesName = $vername ;
$lastID = $LotID + $totalUseMinute ;
$numsideway = 0;
$tradeNo = $numTrade ;
$lossCon = $numLoss;

$CaseActionNo = $LotID;

$params = array(
$CaseActionNo,$data['extra1'] ,$data['extra2'],$data['extra3'],$SeriesName,
$LotID,$lastID,$totalUseMinute,$numsideway,$tradeNo,
$lossCon,$className,$actionReason);
if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
   echo 'Error' ;
   return false;
}

  $pdo->commit();
*/

    

  return array($numTrade,$numLoss,$allActionReason,$balance);
  /*
  $myObj = new stdClass();
  $myObj->result = 'win' ;
  $myObj->numTrade = $numTrade ;
  $myObj->numLoss = $numLoss ;
  $myJSON = json_encode($myObj);
  echo $myJSON;
  */
  
  



} // end function

function CalWinByMethod2($id) { 

  $dbname = 'ddhousin_lab' ;
  $pdo = getPDO2($dbname,true)  ;
  //$pdo->exec("set names utf8mb4") ;
  
  $sql = 'select * from AnalyEMA WHERE id>= ? and id<=? '; 
  $params = array($id,$id+30);

  $rs= pdogetMultiValue2($sql,$params,$pdo) ;
  
  $debug= false;
  $macdThershold = 8 ; $lastMacdHeight = 0 ;
  $numTrade= 0 ; $numLoss=0 ; $lossCon = 0 ;
  $allActionReason  = '';
  $CaseActionNo = $id;
  
  $Seriesname = 'ByMethod2' ;
  $curpair= 2;
  $LotID = $id ;
  $no = 1 ; $totalUseMinute= 0 ; 
  $vername = 'Method2' ;
  $LoopNo = 0 ;
  $stShow = '<table style="width:100%">';
  $stShow .= '<tr style="background:antiquewhite"><td colspan=20>Lab Trade By Method2 ID=' . $id . '</td>';
  $stShow .= '<tr style="background:azure">';
  $stShow .= '<td>No</td><td>ID</td><td>Code Check</td>';
  $stShow .= '<td>Equal</td><td>Red</td><td>Green</td>';
  $stShow .= '<td>Action</td><td>Forecast<br>Color</td><td>Result<br>Color</td>';
  $stShow .= '<td>Win Status</td><td>Num Loss</td></tr>';

  
  $TradeNo = 1;
  while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
      $thisID = $LoopNo + $LotID ;
	  $numEqual = 0 ; $numRed = 0 ;$numGreen = 0 ;
	  list($thisAction,$forecastColor,$codeToCheck,$numEqual,$numRed,$numGreen) =$this->getActionWithMethod2($pdo,$row)    ;	   
	  $stShow .='<tr>';
	  $stShow .= "<td>$TradeNo</td><td>$thisID</td><td>$codeToCheck</td>";
      $stShow .= "<td>$numEqual</td><td>$numRed</td><td>$numGreen</td>";
      $stShow .= "<td>$thisAction</td><td>$forecastColor</td>";
	  $TradeNo++ ;
	  

       
	  $sql = "select thisColor from AnalyEMA where id=?"; 
	  $params = array($thisID+1);
	  $resultColor =pdogetValue($sql,$params,$pdo) ;

       /*
	  if ($thisAction != 'Idle') {
        $numTrade++ ;	
		if ($resultColor == 'Equal') {
			$winStatus = '???' ;
		} else {
			if ($forecastColor == $resultColor) {
			   if ($resultColor !='Equal') {
				 $winStatus  = 'win' ;
		         if ($debug==true) {  
				   echo '<h3 style="color:blue">'. $thisID .' = ' . $winStatus . '</h3>';
				 }
			   } else {
				 $winStatus = '???' ;
			   }
			} else {
				$winStatus = 'loss' ;
		        if ($debug==true) {  
				 echo '<h3 style="color:red">' . $thisID . ' = ' . $winStatus . '</h3>';
				}
				$numLoss++ ;
			}
		}
      }  else {
        $winStatus= '???' ;
	  }
	  */
	  if ($resultColor != 'Equal') {	  
		  if ($forecastColor == $resultColor) {
			  $winStatus  = 'win' ;
		  } else {
			  $winStatus  = 'loss' ;
			  $numLoss++ ;
		  }
	  } else {
          $winStatus  = '???' ;
	  }


	
	$remark = '';
	$curpair = 'EURUSD';
	$stShow .="<td>$resultColor</td>";
     
    $stShow .= "<td>$winStatus</td><td>$numLoss</td></tr>";
	 
	if ($winStatus== 'win') {
		break ;
	} else {
       $no++ ; 
	   $totalUseMinute=  $no ; 
	}
	$LoopNo++ ;


  } // eND While Loop
   $stShow .='</table>';

  if ($debug==true) {  
    echo $LotID .' = ' . $numLoss . ' จาก ' .  $numTrade ;
  }

  
 
  $sql = 'UPDATE AnalyEMA set totalLoss_Method2=?,totalTrade_Method2=? where id=?'; 
  $params = array($numLoss,$numTrade,$id);

  if (!pdoExecuteQuery($pdo,$sql,$params)) {
     echo 'Error' ;
     return false;
  }  
  $pdo->commit();

  return array($numTrade,$numLoss,$allActionReason,$stShow);

 
/*
  $sql = "REPLACE INTO labSideway9999(
CaseActionNo,Extra1,Extra2,Extra3,SeriesName,
LotID,endID,totalUseMinute,numsideway, totalTrade,
totalLoss,className,actionReason) VALUES (
?,?,?,?,?,
?,?,?,?,?,
?,?,?)";

$lossCon = $numLoss ;
if ($lossCon <=2 ) { $className = 'bgWhite'; }
if ($lossCon <=3 ) { $className = 'bgGreen'; }
if ($lossCon ==4 ) { $className = 'bgPink'; }
if ($lossCon > 4 ) { $className = 'bgRed'; }
if ($lossCon >= 6 ) { $className = 'bgBroke'; }


$data['extra1'] = $data['extra2'] = $data['extra3'] = 'n';
$SeriesName = 'ByClassTrade' ;
$lastID = $LotID + $totalUseMinute ;
$numsideway = 0;
$tradeNo = $numTrade ;
$lossCon = $numLoss;

$params = array(
$CaseActionNo,$data['extra1'] ,$data['extra2'],$data['extra3'],$SeriesName,
$LotID,$lastID,$totalUseMinute,$numsideway,$tradeNo,
$lossCon,$className,$actionReason);
if (!pdoExecuteQuery($pdo,$sql,$params)) {
   echo 'Error' ;
   return false;
}

  $pdo->commit();


  echo 'NumTrade= ' . $numTrade . '-' . $numLoss . '<br>' ;
*/  


   
  
  



} // end function


function NOData() { 
/*
SELECT *  FROM `AnalyEMA` where 
PreviousTurnType = 'TurnDown' and 
PreviousTurnTypeBack2 = '' and 
PreviousTurnTypeBack3 = '' and 
PreviousTurnTypeBack4 = '' and 
MACDConvergence = 'Conver' and 
emaConflict = '3-5-R' AND slopeDirection='Down' 



update AnalyEMA a inner join AnalyEMA b 
on a.id+1=b.id 
set a.MACDConvergence = 'Conver'
WHERE abs(a.MACDHeight) < abs(b.MACDHeight) 


*/


} // end function


function SaveDetail999() { 
$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
require_once($newUtilPath ."src/newutil.php"); 
require_once($newUtilPath ."iqlab/AjaxLabSideway.php"); 

$dbname = 'ddhousin_lab' ;
$pdo = getPDO2($dbname,false)  ;

/*
SaveLabDetail($pdo,$CaseActionNo,$Seriesname, $curpair,$LotID, $no,$id, $minuteno, $ADX, $MACDHeight, 
	$MACDConverDiver,$SlopeDirection, $EMAConflict, $CutPointType, $PreviousTurnType, $action, $thisColor, $forecastColor, $tradeNo, $winStatus,$lossCon, $remark,$actionReason);



  /*
  
  $myObj = new stdClass();
  $myObj->result = 'Success' ;
  $myObj->numOpen = $numOpen ;
  $myJSON = json_encode($myObj);
  echo $myJSON;
  
  */



} // end function


function getBackAnalyDataObject($id) { 
$dbname = 'ddhousin_lab' ;
$pdo = getPDO2($dbname,true)  ;
//$pdo->exec("set names utf8mb4") ;
$startID = $id-1 ; $stopID= $id-2 ;
$sql = 'select * from AnalyEMA WHERE id>=? and  id <=?'; 
$params = array($stopID,$startID);

$rs= pdogetMultiValue2($sql,$params,$pdo) ;

$allData = array();
while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	   $rowdata= json_decode(json_encode($row, JSON_PRETTY_PRINT));
	   $allData[] = $rowdata ;
}

		return $allData ;
   


  /*
  
  $myObj = new stdClass();
  $myObj->result = 'Success' ;
  $myObj->numOpen = $numOpen ;
  $myJSON = json_encode($myObj);
  echo $myJSON;
  
  */



} // end function


function getActionWithMethod2_Ver_0($pdo,$row) { 

/*

SELECT nextColor,count(nextColor) as Count FROM `AnalyEMAAsCode` 
WHERE concat(previousMasterCode,'->',thismasterCode) = '148->15' 
GROUP by nextColor

*/

$debug = false;
//$debug = true;
if ($debug==true) {  
  echo '<h2>ID=' . $row['id'] . '</h2>';
}
$sql = "select concat(previousMasterCode,'->',thismastercode) from AnalyEMAAsCode where id=?"; 




$sql = "select thismastercode,previousMasterCode 
 from AnalyEMAAsCode where id=?" ; 

$params = array($row['id']);


$rowA = pdoRowSet($sql,$params,$pdo) ;
$codeToCheck = $rowA['previousMasterCode'] .'->'. $rowA['thismastercode'] ;
if ($debug==true) {  
  echo "Code To Check=" . $codeToCheck . '  ::: ' ;
}


$sql = "SELECT nextColor,count(nextColor) as Count FROM `AnalyEMAAsCode` 
WHERE concat(previousMasterCode,'->',thismasterCode) = '" . $codeToCheck."'
GROUP by nextColor"; 
$params = array();
$rs= pdogetMultiValue2($sql,$params,$pdo) ;
/*
if ($debug==true) {  
  echo $sql . '<br>';
  echo 'Total Rec=' .$rs->rowCount() . ';';
}
*/
$maxCount = -1 ; $maxColor = '';
if ($debug==true) {  
  echo '<table border=1><tr>' ;
}
$numRed = 0 ; $numEqual = 0 ; $numGreen = 0;
while($row2 = $rs->fetch( PDO::FETCH_ASSOC )) {
	if ($row2['nextColor'] == 'Equal') {
		$numEqual = $row2['Count'];

	}
	if ($row2['nextColor'] == 'Red') {
		$numRed = $row2['Count'];

	}
	if ($row2['nextColor'] == 'Green') {
		$numGreen = $row2['Count'];

	}
	if ($maxCount < $row2['Count']) {
		$maxCount = $row2['Count'] ;
		$maxColor = $row2['nextColor'] ;
	}  
	if ($debug==true) {  
	  echo '<td>' . $row2['nextColor']. ' = ' . $row2['Count'] . '</td>';
	}
	
	//echo $row2['nextColor'] ."---->" . $row2['Count'] . '<br>'; 
} // end while 

if ($debug==true) {  
  echo '</table>';
}

if ($debug==true) {  
	echo '************************' . '<br>';
	echo 'MaxCount =' . $maxCount . ' Max Color  = ' . $maxColor ;
	echo '<br>************************' . '<br>';
}
/*
if ($numGreen == $numEqual) {
    $maxColor = 'Green' ;
}
if ($numRed == $numEqual) {
    $maxColor = 'Green' ;
}
*/

//$thisAction ='Idle' ;

if ($maxColor=='Red') {
	$thisAction = 'PUT';
	$forecastColor='Red';
	$forecastClass=$ActionClass='bgRed';
    $actionReason = $thisAction ;
	$nextColorClass = '' ; $remark ='';
}
if ($maxColor=='Green') {
	$thisAction = 'CALL';
	$forecastColor  = 'Green';
	$forecastClass=$ActionClass='bgGreen';
    $actionReason = $thisAction ;
	$nextColorClass = '' ; $remark ='';
}
if ($maxColor=='Equal') {
	$thisAction = 'Idle';
	$forecastColor  = 'Gray';
	$forecastClass=$ActionClass='bgGray';
    $actionReason = $thisAction ;
	$nextColorClass = '' ; $remark ='';
}

return array($thisAction,$forecastColor,$codeToCheck,$numEqual,$numRed,$numGreen);
/*

$sql = "select thisColor from AnalyEMA where id=?"; 
$params = array($row['id']+1);
$resultColor = pdogetValue($sql,$params,$pdo) ;
echo '----->' . $thisAction . ' = '  . $forecastColor . ' :: ' . $resultColor ; 
if ($forecastColor == $resultColor) {
	echo '<span style="color:blue;font-size:22px"> Win</span>' ;
} else {
    echo '<span style="color:red"> Loss</span>' ;
}

echo '<hr>';
$sql = 'select * from AnalyEMA WHERE id=?'; 
$params = array($row['id']);
$row = pdoRowSet($sql,$params,$pdo) ;

if ($debug==true) {  
  echo  ''. 'Final==>' . $thisAction . '<br>';
}
//return array($maxColor,$thisAction) ;

return array($thisAction,$forecastColor);
/*
return array(
	  $thisAction,$forecastColor,$forecastClass,$ActionClass,$actionReason,
	  $nextColorClass,$remark,$row['MACDHeight'],
	  $row['MACDConvergence'],$row['ema3SlopeValue'],
	  $row['slopeDirection'],
	  $row['pip'],	  $row['pip2']);
*/



  /*
  
  $myObj = new stdClass();
  $myObj->result = 'Success' ;
  $myObj->numOpen = $numOpen ;
  $myJSON = json_encode($myObj);
  echo $myJSON;
  
  */



} // end function


function getActionWithMethod2($pdo,$row) { 


$debug = false;
//$debug = true;
if ($debug==true) {  
  echo '<h2>ID=' . $row['id'] . '</h2>';
}
$sql = "select concat(previousMasterCode,'->',thismastercode) from AnalyEMA where id=?"; 




$sql = "select thismastercode,previousMasterCode 
 from AnalyEMA where id=?" ; 

$params = array($row['id']);


$rowA = pdoRowSet($sql,$params,$pdo) ;
$thismastercode = $rowA['thismastercode'] ;
$codeToCheck = $rowA['previousMasterCode'] .'->'. $rowA['thismastercode'] ;
//$debug=true ;
if ($debug==true) {  
  echo "Code To Check=" . $codeToCheck . '  ::: ' ;
}

 

$sql = "SELECT nextColor,count(nextColor) as Count FROM `AnalyEMA` 
WHERE concat(previousMasterCode,'->',thismasterCode) = '" . $codeToCheck."'
and id < ?   GROUP by nextColor"; 

// เปลี่ยน qauery บนมาเป็น Check จาก field MixMasterCode
$codeToCheck = $rowA['previousMasterCode'] .':'. $rowA['thismastercode'] ;
$sql = "SELECT nextColor,count(nextColor) as Count FROM `AnalyEMA` 
WHERE  MixMasterCode  = '" . $codeToCheck."'
and year(timefrom_unix) = 2023 
and id < ? GROUP by nextColor"; 


$sql = "SELECT nextColor,count(nextColor) as Count FROM `AnalyEMA` 
WHERE  MixMasterCode  = '" . $codeToCheck."'
and id < ?  and timefrom_unix > ? and timefrom_unix < ? GROUP by nextColor"; 

$params = array($row['id'],'2023-12-01 00:00:00','2024-02-28 00:00:00');
$rs= pdogetMultiValue2($sql,$params,$pdo) ;

$maxCount = -1 ; $maxColor = '';
if ($debug==true) {  
  echo '<table border=1><tr>' ;
}
$numRed = 0 ; $numEqual = 0 ; $numGreen = 0; $thisAction = '';
if ($rs->rowCount() > 0) {
	while($row2 = $rs->fetch( PDO::FETCH_ASSOC )) {
		if ($row2['nextColor'] == 'Equal') { $numEqual = $row2['Count']; }
		if ($row2['nextColor'] == 'Red') { $numRed = $row2['Count']; }	
		if ($row2['nextColor'] == 'Green') { $numGreen = $row2['Count']; }		
		if ($maxCount < $row2['Count']) {
			$maxCount = $row2['Count'] ;
			$maxColor = $row2['nextColor'] ;
		}  
		 
		
	} // end while 
} else {
	$sql = "SELECT nextColor,count(nextColor) as Count FROM `AnalyEMA` 
    WHERE  thismasterCode = '" .  $thismastercode ."'
    and id < ? GROUP by nextColor"; 
    $params = array($row['id']);
    $rs= pdogetMultiValue2($sql,$params,$pdo) ;
	while($row2 = $rs->fetch( PDO::FETCH_ASSOC )) {
		if ($row2['nextColor'] == 'Equal') { $numEqual = $row2['Count']; }
		if ($row2['nextColor'] == 'Red') { $numRed = $row2['Count']; }	
		if ($row2['nextColor'] == 'Green') { $numGreen = $row2['Count']; }		
		if ($maxCount < $row2['Count']) {
			$maxCount = $row2['Count'] ;
			$maxColor = $row2['nextColor'] ;
		}  
		if ($debug==true) {  
		  echo '<td>' . $row2['nextColor']. ' = ' . $row2['Count'] . '</td>';
		}		
	} // end while 

}

if ($debug==true) {  
  echo '</table>';
}

if ($debug==true) {  
	echo '************************' . '<br>';
	echo 'MaxCount =' . $maxCount . ' Max Color  = ' . $maxColor ;
	echo '<br>************************' . '<br>';
}
/*
if ($numGreen == $numEqual) {
    $maxColor = 'Green' ;
}
if ($numRed == $numEqual) {
    $maxColor = 'Green' ;
}
*/

//$thisAction ='Idle' ;
if ($thisAction == '') {
	if ($maxColor=='Red') {
		$thisAction = 'PUT';
		$forecastColor='Red';
		$forecastClass=$ActionClass='bgRed';
		$actionReason = $thisAction ;
		$nextColorClass = '' ; $remark ='';
	}
	if ($maxColor=='Green') {
		$thisAction = 'CALL';
		$forecastColor  = 'Green';
		$forecastClass=$ActionClass='bgGreen';
		$actionReason = $thisAction ;
		$nextColorClass = '' ; $remark ='';
	}
	if ($maxColor=='Equal') {
		$thisAction = 'Idle';
		$forecastColor  = 'Gray';
		$forecastClass=$ActionClass='bgGray';
		$actionReason = $thisAction ;
		$nextColorClass = '' ; $remark ='';
	}
}

if (isset($forecastColor)) {
    // ถ้ามีตัวแปร $forecastColor จะทำอะไรบางอย่าง
   // echo "ค่าของ $forecastColor คือ: " . $forecastColor;
} else {
    // ถ้าไม่มีตัวแปร $forecastColor จะทำอะไรบางอย่าง
    echo "ตัวแปร $forecastColor ยังไม่ได้ถูกกำหนดค่า->" . $row['id'] . '<br>';
	$forecastColor = '';
}

return array($thisAction,$forecastColor,$codeToCheck,$numEqual,$numRed,$numGreen);
 


} // end function

function ToggleColor($thisColor,$actionReason,$thisactionReason) { 

      if ($thisColor =='Red') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 //$actionReason .= '->Code1-1-2(G)';
				 $actionReason .= $thisactionReason ;
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= $thisactionReason ;
	 } 

	 return array($thisAction,$forecastColor,$forecastClass,$ActionClass,$actionReason);

} // end function

function SameColor($thisColor,$actionReason,$thisactionReason) { 

      if ($thisColor =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 //$actionReason .= '->Code1-1-2(G)';
				 $actionReason .= $thisactionReason ;
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= $thisactionReason ;
	 } 

	 return array($thisAction,$forecastColor,$forecastClass,$ActionClass,$actionReason);

} // end function


function useExTra($row) { 

  $action =$forecastColor=$forecastClass=$ActionClass=$actionCode = '';

  $AnalyObj = $row;

  $i = 0 ;


   list($action,$actionCode) = $this->getActionClassV2FromlabVer2($AnalyObj);
  // echo "After sss action=" . $action . ' Code=' . $actionCode ;
   if ($action != '') {
	   
	   if ($action == 'CALL') {       
		   $forecastColor = 'Green' ;
		   $forecastClass = 'bgGreen';
		   $ActionClass = 'bgGreen' ;
	   } else {
		   $forecastColor = 'Red' ;
		   $forecastClass = 'bgRed';
		   $ActionClass = 'bgRed' ;

	   }
	   return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }

   

 
   if (abs($AnalyObj['pip']) <= 0.01) {
      $ss= abs($AnalyObj['pip']);
	  $ss = round($ss,3);
      $action = 'Idle'  ; $actionCode = 'EX-PIP0('. $ss . ')';
	  $forecastColor = 'Equal'; $ActionClass = 'bgGray';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }
/*
//  ***************ultraAction *************
require_once('clsultraAction.php');
$startID = $AnalyObj['id'] ;
$stopID =  $startID ;
$clsUltraAction = new clsUltraAction($debugMode=false,$startID);
$AnalyObjectTmp = $clsUltraAction->CreateUltraAnalyObject($startID ,$stopID);
list($action,$fcColor,$actionCode) = $clsUltraAction->getAction($AnalyObjectTmp,0) ;
if ($action !='') {       
       $forecastColor = $fcColor ;
	   $forecastClass = 'bgGreen';
	   $ActionClass = 'bgGreen' ;
		 
       return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
    }
*/

//  ***************ultraAction *************
   
   list($action,$forecastColor,$forecastClass,$ActionClass,$actionCode) = 
	$this->useExtra2($row);
    if ($action !='') {       
       //Palert('c-' . $actionCode); 
       return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
    }



if (
  $AnalyObj['slopeDirection'] == 'Down' && 
  $AnalyObj['previousColor'] == 'Red' && 
  $AnalyObj['previousColorBack2'] == 'Red' && 
  $AnalyObj['PreviousTurnType'] == 'N' && 
  $AnalyObj['emaAbove'] == 5 	
) {
	$action = 'PUT'  ; $actionCode = 'เสริม-1(--)';
    $forecastColor = 'Red'; $ActionClass = 'bgRed';
    return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
}

if (
  $AnalyObj['slopeDirection'] == 'Down' && 
  $AnalyObj['previousColor'] == 'Red' && 
  $AnalyObj['previousColorBack2'] == 'Red' && 
  $AnalyObj['PreviousTurnType'] == 'TurnUp'  
  
) {
	$action = 'CALL'  ; $actionCode = 'เสริม-2(--)';
    $forecastColor = 'Green'; $ActionClass = 'bgGreen';
    return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
}

   /*
 if (
	 abs($AnalyObj['pipGrowth']) > 4  &&  
	 $AnalyObj['previousColor'] == 'Red'&&  
	 $AnalyObj['thisColor'] == 'Green' )
	{
	  $action = 'PUT'  ; $actionCode = 'EX-PIPOVER0';
      $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);

	}
*/
if (
	 $AnalyObj['pipGrowth'] < 0.08  &&  
	 $AnalyObj['previousColor'] == 'Green'&&  
	 $AnalyObj['thisColor'] == 'Red' &&
	 $AnalyObj['emaAbove'] == 3 &&  
	 $AnalyObj['CutPointType'] == '5=>3'

	)
	{
	  $action = 'PUT'  ; $actionCode = 'EX-PIPOVER008';
      $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      

	}

if (
	 $AnalyObj['slopeDirection'] == 'Up'  &&  
	 $AnalyObj['previousColor'] == 'Green'&&  
	 $AnalyObj['thisColor'] == 'Red' &&
	 $AnalyObj['emaAbove'] == 3 

	)
	{
	  $action = 'CALL'  ; $actionCode = 'EX-WATFALL01';
      $forecastColor = 'Green'; $ActionClass = 'bgGreen';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      

	}
/*
if (
	 abs($AnalyObj['pipGrowth']) > 10  &&  
	 $AnalyObj['previousColor'] == 'Red'&&  
	 $AnalyObj['thisColor'] == 'Green' &&
	 $AnalyObj['emaAbove'] == 3 &&  
	 $AnalyObj['CutPointType'] == '5=>3'

	)
	{
	  $action = 'CALL'  ; $actionCode = 'EX-PIP10';
      $forecastColor = 'Green'; $ActionClass = 'bgGreen'; 
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);

	}
/*
 if ($AnalyObj['emaConflict'] == '53G' &&  $AnalyObj['TurnType'] == 'TurnUp') {
      $action = 'CALL'  ; $actionCode = 'EX-CT13B@';
      $forecastColor = 'Green'; $ActionClass = 'bgGreen'; 
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }
*/
if (
	 abs($AnalyObj['MACDHeight']) < 4  &&  
	 $AnalyObj['previousColor'] == 'Green'&&  
	 $AnalyObj['thisColor'] == 'Red' &&
	 $AnalyObj['PreviousTurnType'] == 'TurnDown'  &&
	 $AnalyObj['PreviousTurnTypeBack2'] == 'TurnUp' 
     

   )
	{
	  $action = 'CALL'  ; $actionCode = 'EX-ASWR';
	  $forecastColor = 'Green'; $ActionClass = 'bgGreen'; 
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);

	}
 
 if (
	 abs($AnalyObj['MACDHeight']) < 4  &&  
	 $AnalyObj['previousColor'] == 'Green'&&  
	 $AnalyObj['thisColor'] == 'Red' &&
	 abs($AnalyObj['ema3SlopeValue']) < 8  &&
     $AnalyObj['emaAbove'] == 5

   )
	{
	  $action = 'PUT'  ; $actionCode = 'EX-SLOPE1';
      $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);

	}


 if (
	 abs($AnalyObj['MACDHeight']) < 5  &&  
	 $AnalyObj['previousColor'] == 'Green'&&  
	 $AnalyObj['thisColor'] == 'Red' &&
	 abs($AnalyObj['ema3SlopeValue']) < 3 

   )
	{
	  $action = 'PUT'  ; $actionCode = 'EX-SLOPE0';
      $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);

	}
 
 


   // Check SideWay
if (
  
  ($AnalyObj['PreviousTurnType'] != $AnalyObj['PreviousTurnTypeBack2'] ) &&
  ($AnalyObj['PreviousTurnTypeBack2'] != $AnalyObj['PreviousTurnTypeBack3'] )	
) {
  if ($AnalyObj['thisColor'] == 'Red') {
      $action = 'CALL'  ; $actionCode = 'EX-SWR';
	  $forecastColor = 'Green'; $ActionClass = 'bgGreen';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      
  } 

  if (
	  $AnalyObj['thisColor'] == 'Green' &&
	 
	  $AnalyObj['PreviousTurnType'] == 'TurnUp'   
  ) {
      $action = 'CALL'  ; $actionCode = 'EX-SWR22';
	  $forecastColor = 'Green'; $ActionClass = 'bgGreen';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
  } 

  if (
	  $AnalyObj['thisColor'] == 'Green' &&
	  $AnalyObj['thisColor'] == 'Green'
	  
	  ) {
      $action = 'PUT'  ; $actionCode = 'EX-SWG';
      $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
  } 

}
 
   
    


   if ($AnalyObj['emaAbove'] == '3' &&  
	   
       $AnalyObj['PreviousTurnType'] == 'N' &&
	   $AnalyObj['slopeDirection'] == 'Up' 

   
   ) {
       $action = 'CALL'  ; $actionCode = 'EX-CT121@';
	   $forecastColor = 'Green'; $ActionClass = 'bgGreen';
       return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   } 




   if (
	   $AnalyObj['emaConflict'] == '53G' &&  
	   $AnalyObj['PreviousTurnType'] == 'N' &&
	   $AnalyObj['slopeDirection'] == 'Up'
   ) {
      $action = 'CALL'  ; $actionCode = 'EX-CT14@';
	  $forecastColor = 'Green'; $ActionClass = 'bgGreen';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }
	
   if ($AnalyObj['emaConflict'] == '53G' &&  $AnalyObj['PreviousTurnType'] == 'TurnUp') {
      $action = 'CALL'  ; $actionCode = 'EX-CT13@';
	  $forecastColor = 'Green'; $ActionClass = 'bgGreen';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }


if ($AnalyObj['emaConflict'] == '35R' &&  
    $AnalyObj['PreviousTurnType'] == 'N' &&
	$AnalyObj['PreviousTurnTypeBack2'] == 'N' &&
	$AnalyObj['PreviousTurnTypeBack3'] == 'TurnUp' &&
	$AnalyObj['previousColor'] == 'Green'&& 
	$AnalyObj['thisColor'] == 'Red'
	
   ) {
      $action = 'PUT'  ; $actionCode = 'EX-SLCT11#';	  
	  $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      
}
if ($AnalyObj['emaConflict'] == '35R' &&  
    $AnalyObj['PreviousTurnType'] == 'N' &&
	$AnalyObj['PreviousTurnTypeBack2'] == 'N' &&
	$AnalyObj['PreviousTurnTypeBack3'] == 'N' &&
	$AnalyObj['previousColor'] == 'Green'&& 
	$AnalyObj['thisColor'] == 'Red'
	
   ) {
      $action = 'PUT'  ; $actionCode = 'EX-SLCT12#';	  
	  $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      
}
//Palert('22');
if ($AnalyObj['thisColor'] != $AnalyObj['previousColor']  && $AnalyObj['previousColor'] != $AnalyObj['previousColorBack2'] &&
             $AnalyObj['previousColor'] != 'Equal' &&
			 $AnalyObj['previousColorBack2'] != 'Equal' 			 
		 ) {
			 if ($AnalyObj['thisColor'] == 'Red') {
			   $action = 'CALL'  ; $actionCode = 'EX-CTA1(Green)';
	           $forecastColor = 'Green'; $ActionClass = 'bgGreen';
			 } else {
			   $action = 'PUT'  ; $actionCode = 'EX-CTA1(Red)';
	           $forecastColor = 'Red'; $ActionClass = 'bgRed';
			 }
			 //$actionCode = 'extra-D3' ;
			 //Palert('1');
			 return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
}


   if ($AnalyObj['emaConflict'] == '53G' ) {
      $action = 'CALL'  ; $actionCode = 'EX-CT13B@';
	  $forecastColor = 'Green'; $ActionClass = 'bgGreen';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }

  if ($AnalyObj['emaConflict'] == 'N' ) {
   if ($AnalyObj['CutPointType'] == '3=>5' ) {     
     $action = 'PUT'  ; $actionCode = 'EX-CT0';
	 $forecastColor = 'Red'; $ActionClass = 'bgRed';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }
   if ($AnalyObj['CutPointType'] == '5=>3' ) {
     $action = 'CALL'  ; $actionCode = 'EX-CT1';
	 $forecastColor = 'Green'; $ActionClass = 'bgGreen';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }
   if ($AnalyObj['CutPointType'] == 'N' &&  $AnalyObj['PreviousTurnType'] == 'TurnUp') {
     $action = 'CALL'  ; $actionCode = 'EX-CTA2';
	 $forecastColor = 'Green'; $ActionClass = 'bgGreen';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }
}

if ($AnalyObj['emaConflict'] == 'N' &&
	abs($AnalyObj['MACDHeight']) < 10 && 
	$AnalyObj['thisColor'] == 'Red'  &&
	$AnalyObj['slopeDirection'] == 'Down'  
) {
     $action = 'PUT'  ; $actionCode = '@EX-CTB1';
	 $forecastColor = 'Red'; $ActionClass = 'bgRed';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
}

if ($AnalyObj['emaConflict'] == '35R' &&  
	  $AnalyObj['PreviousTurnType'] == 'N' &&
	  abs($AnalyObj['ema3SlopeValue']) < 0.2 	  
   ) {
      $action = 'PUT'  ; $actionCode = 'EX-SLCT11';
	  
	  $forecastColor = 'Red'; $ActionClass = 'bgRed';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      
}

if ($AnalyObj['emaConflict'] == 'N' && $AnalyObj['PreviousTurnType'] == 'N' &&
	$AnalyObj['emaAbove'] == '3') {
 	 $action = 'CALL'  ; $actionCode = 'EX-CT1%';
	 $forecastColor = 'Green'; $ActionClass = 'bgGreen';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);


}

if ($AnalyObj['emaConflict'] == 'N' ) {
   if ($AnalyObj['CutPointType'] == '3=>5' ) {
     $action = 'PUT'  ; $actionCode = 'EX-CT0';
	 $forecastColor = 'Red'; $ActionClass = 'bgRed';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }
   if ($AnalyObj['CutPointType'] == '5=>3' ) {
     $action = 'CALL'  ; $actionCode = 'EX-CT1';
	 $forecastColor = 'Green'; $ActionClass = 'bgGreen';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }
   if ($AnalyObj['CutPointType'] == 'N' &&  $AnalyObj['PreviousTurnType'] == 'TurnUp') {
     $action = 'CALL'  ; $actionCode = 'EX-CTA12';
	 $forecastColor = 'Green'; $ActionClass = 'bgGreen';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
   }
}

if (
	$AnalyObj['emaConflict'] == 'N' &&
	$AnalyObj['ema5'] > $AnalyObj['ema3'] &&
	$AnalyObj['slopeDirection'] == 'Down'  	
) {
 
     $action = 'PUT'  ; $actionCode = 'EX-D5';
	 $forecastColor = 'Red'; $ActionClass = 'bgRed';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
		 

}

if (
	$AnalyObj['emaConflict'] == '35R' &&
	$AnalyObj['slopeDirection'] == 'Down'  	
) {
 
     $action = 'PUT'  ; $actionCode = 'EX-D77';
	 $forecastColor = 'Red'; $ActionClass = 'bgRed';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
		 

}

if (
	$AnalyObj['emaConflict'] == '53G' &&
	$AnalyObj['slopeDirection'] == 'UP'  &&
	$AnalyObj['previousTurnType'] == 'TurnUp'  

) {
 
     $action = 'CALL'  ; $actionCode = 'EX-D88';
	 $forecastColor = 'Green'; $ActionClass = 'bgGreen';
     return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
		 
}





} // end function

function SaveTradeResult($data) { 


/* ตัวอย่างข้อมูล array ของ standard objects

$tradeObjects = [
    (object)[
        "tradeRunNo" => 1,
        "LotNo" => 101,
        "tradeno" => 12345,
        "id" => "T001",
        "timefrom" => "10:00",
        "action" => "BUY",
        "actionCode" => "B",
        "colorTrade" => "Green",
        "resultColor" => "",
        "winStatus" => "",
        "MoneyTrade" => 100.50,
        "profit" => 10.0,
        "balanceOnLot" => 110.50,
        "grandTotal" => 120.50
    ],
    (object)[
        "tradeRunNo" => 2,
        "LotNo" => 102,
        "tradeno" => 12346,
        "id" => "T002",
        "timefrom" => "10:05",
        "action" => "SELL",
        "actionCode" => "S",
        "colorTrade" => "Red",
        "resultColor" => "",
        "winStatus" => "",
        "MoneyTrade" => 200.75,
        "profit" => 15.0,
        "balanceOnLot" => 215.75,
        "grandTotal" => 225.75
    ]
];
*/
$tableName = $data['tableName'];
$saveData = $data['dataPost'] ;
// วนลูปแต่ละ object ใน array
foreach ($saveData as $trade) {
    //echo "<strong>Trade Details:</strong><br>";
    // วนลูปแต่ละฟิลด์ใน object
    foreach ($trade as $key => $value) {
        echo "$key: $value<br>";
    }
    echo "<br>";
}




} // end function

function createTable($data) { 

	
// กำหนดชื่อ Table
$tableName = "trade_data";

// สร้างคำสั่ง SQL สำหรับสร้าง table
$sql = "CREATE TABLE IF NOT EXISTS $tableName (";

// วนลูปสร้างฟิลด์ตามชนิดข้อมูลใน $EmptyTradeObject
foreach ($EmptyTradeObject as $field => $value) {
    // ตรวจสอบชนิดข้อมูลด้วย gettype()
    switch (gettype($value)) {
        case "integer":
            $sql .= "$field INT,";
            break;
        case "double":
            $sql .= "$field FLOAT(12, 2),";
            break;
        case "string":
            if (strpos($field, "time") !== false) {
                // กำหนดเป็น DATETIME หาก field มีคำว่า time
                $sql .= "$field DATETIME,";
            } else {
                $sql .= "$field VARCHAR(255),";
            }
            break;
        default:
            $sql .= "$field TEXT,";
            break;
    }
}

	// ลบเครื่องหมาย comma สุดท้ายออกแล้วเพิ่ม PRIMARY KEY
	$sql = rtrim($sql, ',') . ", PRIMARY KEY (tradeRunNo));";

	// เชื่อมต่อฐานข้อมูล
	$pdo = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");

	// สร้าง table
	try {
		$pdo->exec($sql);
		echo "Table $tableName created successfully.";
	} catch (PDOException $e) {
		echo "Error creating table: " . $e->getMessage();
	}


} // end function


function concludeDataTade($mainTradeNo,$pdo) { 


$sql = "select min(id) as minid,max(id) as maxid ,max(MoneyTrade) as MaxMoneyTrade,
min(timefrom) as minTime,max(timefrom) as maxTime from trade_data where MainTradeNo=?"; 


$params = array($mainTradeNo);
$row = pdoRowSet($sql,$params,$pdo) ;
$minID = $row['minid'] ; $maxID = $row['maxid'];
$minTime = $row['minTime'] ; $maxTime = $row['maxTime'];
$MaxMoneyTrade = $row['MaxMoneyTrade'];
$sql = "select grandTotal  from trade_data where id=? and MainTradeno=?"; 
$params = array($maxID,$mainTradeNo) ;
$grandTotal =pdogetValue($sql,$params,$pdo) ;
$numMaxLoss= 0 ;$MaxLossList = '';
$numTrade = $maxID - $minID;
$grandtotalBath = $grandTotal *33 ;

$sql = "REPLACE INTO `head_trade_data`(
`MainTradeNo`, `startID`, `endID`, `startTrade`, `endTrade`, 
`numTrade`, `MaxMoneyTrade`, `numMaxLoss`, `MaxLossList`, `grandtotal`,grandtotalBath) 
VALUES (?,?,?,?,?,?,?,?,?,?,?)"; 
$params=array(
	$mainTradeNo,$minID,$maxID,$minTime,$maxTime,
	$numTrade,$MaxMoneyTrade,$numMaxLoss,$MaxLossList,$grandTotal,$grandtotalBath);

if (!pdoExecuteQuery($pdo,$sql,$params)) {
   echo 'Error' ;
   return false;
}

//$pdo->commit();








} // end function

function getNewTradeNo($data,$pdo) { 

        //echo "------Step-2";
		$tableName = "head_trade_data";
		//$pdo->exec("set names utf8mb4") ;
		$sql = "select max(MainTradeNo)+1 from $tableName"; 
		$params = array();
		$mainTradeNo =pdogetValue($sql,$params,$pdo) ;
		return  $mainTradeNo;


} // end function


function InsertDataTradeDetail($data) { 


$dbname = 'ddhousin_lab' ;
$pdo = getPDO2($dbname,true)  ;



$DataDetailTrade = $data['dataPost'] ;

$saveData = json_decode($DataDetailTrade,true) ;
$mainTradeNo = 1; 
// วนลูปแต่ละ object ใน array
$sql = "REPLACE INTO $tableName('mainTradeNo',"; 
$sqlVal = ' VALUES('. $mainTradeNo.',' ;	

foreach ($saveData as $trade) {
    
	$sql = "replace INTO $tableName(mainTradeNo,"; 
    $sqlVal = ' VALUES('. $mainTradeNo.',' ;	
	$i= 0;
    foreach ($trade as $key => $value) {       
		$sql .=$key .',' ; 
		$sqlVal .= '"' . $value .'",';
		$params[] = $value;
		$i++ ;
		if ($i >= 30) {
			break;
		}
		
    }	

    $sqlVal = trim($sqlVal) ;
	$sql = substr(trim($sql),0,strlen($sql)-1) .')';
	$sqlVal = substr($sqlVal,0,strlen($sqlVal)-1) .')';
	
	$sqlInsert = $sql . ' ' .$sqlVal ;	
	$params = array();	
	echo '<br>' . $sqlInsert  .'<br>';
	if (!pdoExecuteQuery($pdo,$sqlInsert,$params)) {
	   echo 'Error' ;
	   return false;
	}
}	


//$pdo->commit();
echo "Success";


	

} // end function


function useExtra2($row) { 

$AnalyObj = $row; 
$action = '' ;
$forecastColor = '';
$forecastClass = '';
$ActionClass = '' ;
$actionCode='';


// Palert('1');
//echo "useExtra2<br>";

//echo $AnalyObj['emaConflict']  .'- ' . $AnalyObj['TurnType'] . '<br>' ;
/*
if ($AnalyObj['emaConflict'] == '53G' &&  $AnalyObj['TurnType'] == 'TurnUp') {
	  
      $action = 'CALL'  ; $actionCode = 'CT13B99@(Green)';
	  $forecastColor = 'Green'; $ActionClass = 'bgGreen';
      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
 }
*/
if (
	 $AnalyObj['PreviousTurnType'] =='TurnUp'  &&  
	 $AnalyObj['PreviousTurnTypeBack2'] == 'N'&&  	 
	 $AnalyObj['emaAbove'] == 5 &&
	 $AnalyObj['slopeDirection'] == 'Up' 


	)
	{
	  $action = 'PUT'  ; $actionCode = 'TTT001';
      $forecastColor = 'Red'; $ActionClass = 'bgRed';
	  /*$action = 'CALL'  ; $actionCode = 'EX2-TTT001';
      $forecastColor = 'Green'; $ActionClass = 'bgGreen';
	  $forecastClass = 'bgGreen';
	  */
	  


      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      

	}

if (
	 $AnalyObj['PreviousTurnType'] =='N'  &&  
	 $AnalyObj['PreviousTurnTypeBack2'] == 'N'&&  	 
	 $AnalyObj['emaAbove'] == 3 &&
	 $AnalyObj['slopeDirection'] == 'Up' 
	)
	{
	  $action = 'PUT'  ; $actionCode = 'TTT001';
      $forecastColor = 'Red'; $ActionClass = 'bgRed';
	  $action = 'CALL'  ; $actionCode = 'EX2-TTT001';
      $forecastColor = 'Green'; $ActionClass = 'bgGreen';
	  $forecastClass = 'bgGreen';


      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      

	}


if (
	 $AnalyObj['PreviousTurnType'] =='TurnDown'  &&  
	 $AnalyObj['PreviousTurnTypeBack2'] == 'N'&&  	 
	 $AnalyObj['emaAbove'] == 5 &&
	 $AnalyObj['slopeDirection'] == 'Down' 
	)
	{
	  $action = 'PUT'  ; $actionCode = 'TTT001';
      $forecastColor = 'Red'; $ActionClass = 'bgRed';
	  $forecastClass = 'bgRed';
	  /*$action = 'CALL'  ; $actionCode = 'EX2-TTT001';
      $forecastColor = 'Green'; $ActionClass = 'bgGreen';
	  $forecastClass = 'bgGreen';
	  */


      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      

	}
/*
$AnalyObj['PreviousSlopeDirection'] =='Down'  &&  
$AnalyObj['slopeDirection'] == 'Up'&&  	 
*/


if (
	 
	 $AnalyObj['PreviousTurnType'] =='TurnDown'  &&  
	 $AnalyObj['PreviousTurnTypeBack2'] == 'N'&&  	 
	 $AnalyObj['emaConflict'] == '53G' &&
	 $AnalyObj['emaAbove'] == 5 &&
	 $AnalyObj['slopeDirection'] == 'Down' 
	)
	{
	  /*$action = 'PUT'  ; $actionCode = 'TTT001';
      $forecastColor = 'Red'; $ActionClass = 'bgRed';
	  $forecastClass = 'bgRed';
	  */
	  $action = 'CALL'  ; $actionCode = 'EX2-TTT001';
      $forecastColor = 'Green'; $ActionClass = 'bgGreen';
	  $forecastClass = 'bgGreen';
	  


      return array($action,$forecastColor,$forecastClass,$ActionClass,$actionCode);
      

	}




} // end function

function getActionClassV2FromlabVer2($AnalyObj) { 

//return ;
//echo "EMAConflict on  labVer2 =  " .$AnalyObj['id']. '='. $AnalyObj['emaConflict'] . ' ' ;

$action = '' ; $actionCode  = '';
if ($AnalyObj['pip'] == 0 ) {
    $action = 'Idle'  ; $actionCode = 'PIP0';
	return array($action,$actionCode);

}
// Check SideWay
/* 
  ($AnalyObj['PreviousTurnType'] != $AnalyObj['PreviousTurnTypeBack2'] ) &&
  ($AnalyObj['PreviousTurnTypeBack2'] != $AnalyObj['PreviousTurnTypeBack3'] &&
   $AnalyObj['emaAbove'] == 3
   */





if ($AnalyObj['emaConflict'] != 'N' ) {
   //$action = 'Idle'  ; $actionCode = 'CF0' ;
   

   if ($AnalyObj['emaConflict'] == '35R' &&  
	  $AnalyObj['PreviousTurnType'] == 'N' &&
	  abs($AnalyObj['ema3SlopeValue']) < 0.2 

	   
   ) {
      $action = 'PUT'  ; $actionCode = 'SLCT11';
      return array($action,$actionCode);
   }


   if ($AnalyObj['emaConflict'] == '35R' &&  $AnalyObj['PreviousTurnType'] == 'TurnUp') {
      $action = 'CALL'  ; $actionCode = 'CT12@';
      return array($action,$actionCode);
   }

   if (
	$AnalyObj['emaConflict'] == '35R' &&  
	$AnalyObj['PreviousTurnType'] == 'TurnDown' &&
    $AnalyObj['slopeDirection'] == 'Down' 
	   
    ) {
      $action = 'PUT'  ; $actionCode = 'CT03B@-'. $AnalyObj['id'] ;
	  $forecastColor = 'Red'; $ActionClass = 'bgRed';
	  //echo '******* ' .$actionCode ;
      return array($action,$actionCode);
   }

   if ($AnalyObj['emaConflict'] == '53G' &&  $AnalyObj['PreviousTurnType'] == 'TurnUp') {
      $action = 'CALL'  ; $actionCode = 'CT13@';
      return array($action,$actionCode);
   }

   if ($AnalyObj['emaConflict'] == '53G' ) {
      $action = 'CALL'  ; $actionCode = 'CT13B@';
      return array($action,$actionCode);
   }
   if (
	   $AnalyObj['emaConflict'] == '53G' &&  
	   $AnalyObj['PreviousTurnType'] == 'N' &&
	   $AnalyObj['slopeDirection'] == 'Up'
   ) {
      $action = 'CALL'  ; $actionCode = 'CT14@';
      return array($action,$actionCode);
   }
   if (
	   $AnalyObj['emaConflict'] == '53G' &&  
	   $AnalyObj['PreviousTurnType'] == 'TurnUp' &&
	   $AnalyObj['slopeDirection'] == 'Up'
   ) {
      $action = 'CALL'  ; $actionCode = 'CT15@';
      return array($action,$actionCode);
   }

   return array($action,$actionCode);
}



if (
   ($AnalyObj['thisColor'] != $AnalyObj['previousColor'])  &&
   ($AnalyObj['previousColor'] != $AnalyObj['previousColorBack2'] ) 	  
) {
  if ($AnalyObj['thisColor'] == 'Red') {
      $action = 'CALL'  ; $actionCode = 'LabV2SideW(G)';
      return array($action,$actionCode);
  } 
  if ($AnalyObj['thisColor'] == 'Green') {
      $action = 'PUT'  ; $actionCode = 'V2-SWG(R)';
      return array($action,$actionCode);
  } 


}
if ($AnalyObj['emaConflict'] == 'N' ) {
   if ($AnalyObj['CutPointType'] == '3=>5' ) {
     $action = 'PUT'  ; $actionCode = 'V2-CT0';
     return array($action,$actionCode);
   }
   if ($AnalyObj['CutPointType'] == '5=>3' ) {
     $action = 'CALL'  ; $actionCode = 'V2-CT1';
     return array($action,$actionCode);
   }
   if ($AnalyObj['CutPointType'] == 'N' &&  $AnalyObj['PreviousTurnType'] == 'TurnUp') {
     $action = 'CALL'  ; $actionCode = 'V2-CTA1';
     return array($action,$actionCode);
   }
}

if ($AnalyObj['emaConflict'] == 'N' && $AnalyObj['PreviousTurnType'] == 'N' &&
	$AnalyObj['emaAbove'] == '3') {
 	 $action = 'CALL'  ; $actionCode = 'V2-CT1%';
     return array($action,$actionCode);
}

if ($AnalyObj['emaConflict'] == '35R' &&  
	  $AnalyObj['PreviousTurnType'] == 'TurnDown') {
      $action = 'PUT'  ; $actionCode = 'CT11';
	  echo '<h1>Return</h1>' ;
      return array($action,$actionCode);
 }


if ($AnalyObj['PreviousTurnType'] == 'N' && $AnalyObj['PreviousTurnTypeBack2'] =='TurnUp') {
	if ($AnalyObj['slopeDirection'] =='Up') {
        $action = 'CALL'  ; $actionCode = 'T0';
		return array($action,$actionCode);
	} else {
        $action = 'PUT'  ; $actionCode = 'T0B';
		return array($action,$actionCode);
	}

}

if (
	$AnalyObj['PreviousTurnType'] != $AnalyObj['PreviousTurnTypeBack2'] &&
	$AnalyObj['PreviousTurnTypeBack2'] != $AnalyObj['PreviousTurnTypeBack3'] 
	
) {
	  
	 if ($AnalyObj['thisColor'] == $AnalyObj['previousColor']) { 
	     if ($AnalyObj['thisColor'] == 'Red') {
		 	 $action = 'PUT'  ; $actionCode = 'T1.1';
		 }
		 if ($AnalyObj['thisColor'] == 'Green') {
		 	 $action = 'CALL'  ; $actionCode = 'T1.2';
		 }
	 } else {
        if ($AnalyObj['emaAbove'] == 3) { 
           $action = 'CALL'  ; $actionCode = 'T2.2';
        } else {
           $action = 'PUT'  ; $actionCode = 'T2.3';
		}
    
	 }
     return array($action,$actionCode);
}

         /*
		 if ($AnalyObj['emaAbove'] == 3) {
			 //list($action,$actionCode) = ManageTrendUp($AnalyObj,$i) ;
			 $actionCode .= 'U' . $actionCode ;

		 }  
		 if ($AnalyObj['emaAbove'] == 5) {			 
			 //list($action,$actionCode) = ManageTrendDown($AnalyObj,$i) ;
			 $actionCode .= 'D' . $actionCode ;
		 }  
 */
        
		//return array($action,$actionCode) ;

} // end function


} // end class

  

?>