<?php

ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function getActionFromIDVerObject($row,$macdThershold,$lastMacdHeight) { 


   $LockedAction = false ;
   //$slopeValue = $row['ema3SlopeValue'] ;
   $slopeDirection = $row['ema3slopeDirection'] ;
   $row['MACDHeight']= $row['MACDHeight']*1000*1000 ;
   $macd = $row['MACDHeight'] ;
   
   $pipSize = round(abs($row['pip'])/10,2);
   //$delTapip = abs(abs($row['pip']/10) - abs($row['previousPIP']/10)) ;

   
   /*
   $sql="SELECT a.id ,b.id,b.minuteno, a.ema3,b.ema3, (b.ema3-a.ema3)*1000*1000 as differ
    FROM  $tableName a INNER join $tableName b on b.id-1 = a.id 
    WHERE b.id = ?";
   $params = array($row['id']);   
   $rowDifferEMA = pdoRowSet($sql,$params,$this->pdo) ;      

   $slopeValue = $rowDifferEMA['differ']  ;
*/
   
   $slopeValue = $row['ema3SlopeValue']  ;
   if ($slopeValue < 0) {
      $slopeDirection = 'Down' ;
   } else {
      $slopeDirection = 'Up' ;
   }
   $slopeDirection = $row['ema3slopeDirection'] ;
   
   
   /*


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
   */
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
	   $row['ema3slopeDirection'] =='Down' && 
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
	   $row['ema3slopeDirection'] =='Down' && 
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
	   $row['ema3slopeDirection'] =='Down' && 
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
	   $row['ema3slopeDirection'] =='Down' && 
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
   if ($row['emaConflict'] == '3-5-R' && $row['MACDConvergence'] == 'Diver' && $row['ema3slopeDirection'] != 'Up') {
		   $thisAction = 'PUT'; 
		   $forecastColor = 'Red';
		   $forecastClass = 'bgRed';
  	       $ActionClass = 'bgRed'; 
		   $actionReason .= '->Code2_1(R)';
   } 

   // Step 2-2
   if ($row['emaConflict'] == '3-5-R' && $macdConver == 'Conver'
      && $row['ema3slopeDirection'] =='Down' && $row['PreviousTurnType']=='TurnDown'
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
   if ($row['PreviousSlopeDirection'] !== $row['ema3slopeDirection'] && 
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

   if ($row['PreviousTurnType'] =='TurnUp' && 
       $row['PreviousTurnTypeBack2'] =='TurnDown' && 
	   $row['ema3slopeDirection']=='Up' && $row['emaConflict']=='N'  ) {
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

   if ($row['PreviousTurnType'] =='TurnUp' && 
	   $row['ema3slopeDirection']=='Up' && $row['emaConflict']=='N'  ) {
	   if ( $row['thisColor'] =='Green') {
				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->Code6-5-2.2(G)';
		} else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->Code6-5-2.2(R)';
		}
		
   }

   // Step 6-5-3
   if ($row['PreviousTurnType'] =='TurnUp' && 
       $row['PreviousTurnTypeBack2'] =='TurnDown' && 
	   $row['PreviousTurnTypeBack3'] =='N' && 
	   $row['ema3slopeDirection']=='Up' && $row['emaConflict']=='' && $row['ema3SlopeValue'] <10 ) {
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
   if ($row['ema3slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R' ) {
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
   if ($row['ema3slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
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
   if ($row['ema3slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
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
   if ($row['ema3slopeDirection'] =='Up' && $row['emaConflict'] =='3-5-R'
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
   if ($row['ema3slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R' ) {
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
   if ($row['ema3slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R' &&
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
   if ($row['ema3slopeDirection'] =='Up' && $row['emaConflict'] =='5-3-G' ) {
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
   if ($row['ema3slopeDirection'] =='Up' && $row['emaConflict'] =='5-3-G' 
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
   if ($row['ema3slopeDirection'] =='Up' 
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
   if ($row['ema3slopeDirection'] =='Up' && $row['emaConflict'] =='5-3-G'
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
   if ($row['ema3slopeDirection'] =='Up' && 
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
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='N' && $row['emaConflict'] =='5-3-G' && $row['ema3slopeDirection']=='Up') {
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
   && $row['ema3slopeDirection'] =='Up'
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
   && $row['ema3slopeDirection'] =='Down'
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
   if ($row['PreviousTurnType'] =='TurnUp' && $row['PreviousTurnTypeBack2'] =='TurnDown' && ($row['emaConflict']) == '5-3-G' && $row['ema3slopeDirection'] =='Up') {
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
	   && ($row['emaConflict']) == '5-3-G' && $row['ema3slopeDirection'] =='Up') {
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
   && $row['emaConflict'] == '3-5-R' && $row['ema3slopeDirection'] =='Up'
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
   && $row['emaConflict'] == '3-5-R' && $row['ema3slopeDirection'] =='Up'
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

   && $row['emaConflict'] == '3-5-R' && $row['ema3slopeDirection'] =='Up'

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
   && $row['emaConflict'] == '3-5-R' && $row['ema3slopeDirection'] =='Up'
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
   && $row['ema3slopeDirection'] =='Down' && $row['emaConflict'] =='5-3-G'
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
   && $row['ema3slopeDirection'] =='Down' && $row['emaConflict'] =='5-3-G'

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
   && $row['ema3slopeDirection'] =='Down' && $row['emaConflict'] =='5-3-G'

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
   && $row['ema3slopeDirection'] =='Down' 
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
   && $row['ema3slopeDirection'] =='Down' 
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
   && $row['ema3slopeDirection'] =='Up' 
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
   && $row['ema3slopeDirection'] =='Up' && (abs($row['MACDHeight']) < 4)
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
   && $row['ema3slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5)
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
   && $row['ema3slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5) 
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
   && $row['ema3slopeDirection'] =='Up' && (abs($row['MACDHeight']) > 5)
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
   && $row['ema3slopeDirection'] =='Down' 
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
   && $row['ema3slopeDirection'] =='Down' && $row['MACDHeight'] < 4
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
   && $row['ema3slopeDirection'] =='Down' && $row['CutPointType'] =='3->5'
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
   && $row['ema3slopeDirection'] =='Down' 

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
   && $row['ema3slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R'
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
   && $row['ema3slopeDirection'] =='Down' 
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
   && $row['ema3slopeDirection'] =='Down' && $row['emaConflict'] =='3-5-R'
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
	  $row['ema3slopeDirection'] =='Up'  && 
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
// Case New ตรวจสอบการลงต่อเนื่อง
if (
	    
	   $row['PreviousTurnType'] =='N' &&
       $row['PreviousTurnTypeBack2'] =='N' ) {
    
      if ( $row['thisColor'] =='Green') {

				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->CodeNew-1-1(G)';
	  } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->CodeNew-1-1(R)';
	 }
} // end if

// Case New ตรวจสอบสี Conflict
    if (	    
	   $row['emaConflict'] =='53G' &&
       $row['PreviousTurnType'] =='N' &&
	   $row['emaAbove'] =='5' 
	   ) {
    
			 $thisAction = 'PUT'; 
	         $forecastColor = 'Red';
			 $forecastClass = 'bgRed';
  	         $ActionClass = 'bgRed'; 
			 $actionReason .= '->CodeNew-2-1(R)';

     } // end if

// Case New ตรวจสอบสี Conflict+ MACD
    if (	    
	   $row['emaConflict'] =='N' &&
       $row['PreviousTurnType'] =='TurnUp' &&
	   $row['PreviousTurnTypeBack2'] =='TurnDown' &&
	   $row['previousColor'] =='Green'  
	   ) {
    
		 $thisAction = 'CALL'; 
		 $forecastColor = 'Green';
		 $forecastClass = 'bgGreen';
  	     $ActionClass = 'bgGreen'; 
		 $actionReason .= '->CodeNew-1-1(G)';

     } // end if

	 if (
		 $row["previousColor"] === $row["previousColorBack2"] 
        ) {
		 if ( $row['thisColor'] =='Green') {

				 $thisAction = 'CALL'; 
		         $forecastColor = 'Green';
				 $forecastClass = 'bgGreen';
  	             $ActionClass = 'bgGreen'; 
				 $actionReason .= '->CodeNew-1-1(G)';
	     } else {
				 $thisAction = 'PUT'; 
		         $forecastColor = 'Red';
				 $forecastClass = 'bgRed';
  	             $ActionClass = 'bgRed'; 
				 $actionReason .= '->CodeNew-1-1(R)';
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
/*
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


return array($thisAction,$actionReason);

} // end function

function getResultColor($AnalyObj,$thisIndex) { 

           
         $nextColor = $AnalyObj[$thisIndex+1]['thisColor'] ;
		 return $nextColor;


} // end function


?>