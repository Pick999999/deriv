<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<style>
 .redBG { background-color:red; }
 td { text-align:center ; padding:4px}
</style>

<?php
function getAnalyObject() {

   $stData = "";      
   $sFileName = 'dataTest.json';
   $file = fopen($sFileName,"r");
   while(! feof($file))  {
     $stData .= fgets($file) ;
   }
   fclose($file);
   $AnalyData1 = JSON_DECODE($stData,true);
   $AnalyData =  $AnalyData1['AnalysisData'];
   ?>
    <input type="text" id="analyTextData" value='<?=$stData?>' style='width:100%'>
   <?php
   return $AnalyData ;



} // end function


function createHistoryObjTrade($AnalyData, $i, $SuggestColor, $resultColor, $numTrade, $numLoss,$numIdle) {
    $WinStatus = $SuggestColor === $resultColor ? 'y' : 'n';

    $pCut3 = 'null';
    if ( ($i-3) >=0 ) {
		$pCut3 =  $AnalyData[$i-3]['CutPointType'] ;
    }
	$pCut2 = 'null';
    if ( ($i-2) >=0 ) {
		$pCut2 =  $AnalyData[$i-2]['CutPointType'] ;
    }
	$pCut1 = 'null';
    if ( ($i-1) >=0 ) {
		$pCut1 =  $AnalyData[$i-1]['CutPointType'] ;
    }
	
	
	$CutPointType =  $AnalyData[$i]['CutPointType'] ;

	$CutList = $pCut3.' to '. $pCut2.' to '. $pCut1 .' to '.$CutPointType ;


    $sObj = [
        'TradeNo' => $numTrade,
        'CandleID' => $AnalyData[$i]['candleID'],
        'timefrom_unix' => $AnalyData[$i]['timefrom_unix'],
        'CutPointType' => $AnalyData[$i]['CutPointType'],
		'CutList' => $CutList,
        'emaAbove' => $AnalyData[$i]['emaAbove'],
        'SuggestColor' => $SuggestColor,
        'resultColor' => $resultColor,
        'WinStatus' => $WinStatus,
        'lossCon' => $numLoss,
		'numIdle'=>$numIdle
    ];
    return $sObj;
}



function getSuggestColor($AnalyData, $thisIndex) {
    $pip = 0.0;
    $SuggestColor = "";

    $pip = abs(floatval($AnalyData[$thisIndex]['pip']));
	$macd  = abs(floatval($AnalyData[$thisIndex]['MACDHeight']));
	$ema3SlopeValue  = abs(floatval($AnalyData[$thisIndex]['ema3SlopeValue']));

    if ($pip < 1.0 || $macd < 0.1  ) {
        $SuggestColor = "Idle";
		return 'Idle';
    }

	$previousCutPoint = $AnalyData[$thisIndex-1]['CutPointType'] ;
	$thisCutPoint = $AnalyData[$thisIndex]['CutPointType'] ;
	if ($previousCutPoint=='3->5' &&  $thisCutPoint === '5->3'  ) {
        $SuggestColor = "Idle";
		return 'Idle';
    }
	if ($previousCutPoint=='5->3' &&  $thisCutPoint === '3->5'  ) {
        $SuggestColor = "Idle";
		return 'Idle';
    }
	


    $emaAbove = $AnalyData[$thisIndex]['emaAbove'];

    if ($AnalyData[$thisIndex]['CutPointType'] === '3->5') {
        $SuggestColor = 'Red';
    }
    if ($AnalyData[$thisIndex]['CutPointType'] === '5->3') {
        $SuggestColor = 'Green';
    }
    if ($AnalyData[$thisIndex]['CutPointType'] === 'N') {
        $SuggestColor = $emaAbove === '3' ? "Green" : "Red";
    }

    //echo $pip . '=' . $SuggestColor . "\n";

    return $SuggestColor;
}

function getResultColor($AnalyData, $thisIndex) {
    if ($thisIndex + 1 < count($AnalyData)) {
        $thisColor = $AnalyData[$thisIndex + 1]['thisColor'];
    } else {
        $thisColor = 'No';
    }
    return $thisColor;
}

function numTradeToWin($AnalyData, $thisPoint, $CutPointType) {

    if ($CutPointType === '3->5') {
        $SuggestColor = 'Red';
    }
    if ($CutPointType === '5->3') {
        $SuggestColor = 'Green';
    }

    $numTrade = 0;
    $numLoss = 0;
    $numIdle = 0;
    $tradeList = [];

    for ($i = $thisPoint; $i <= count($AnalyData) - 1; $i++) { 	 
        $SuggestColor = getSuggestColor($AnalyData, $i);
        $resultColor = getResultColor($AnalyData, $i);
        //echo $SuggestColor . "\n";

        if ($SuggestColor !== 'Idle') {
            $numTrade++;
            $WinStatus = $SuggestColor === $resultColor ? true : false;
            if ($WinStatus) {
                // Do nothing
            } else {
                $numLoss++;
            }
        } else {
            $numIdle++;
           //echo 'Idle' . "\n";
        }

        $sObj = createHistoryObjTrade($AnalyData, $i, $SuggestColor, $resultColor, $numTrade, $numLoss,$numIdle);
        array_push($tradeList, $sObj);

        if ($SuggestColor === $resultColor) {
            return $tradeList;
        }
	  
    } // end for

   // echo $numTrade . "\n";
    echo '->' . $numTrade . '<br>';
    return array($tradeList,$numTrade) ;
}

function getSuggestColor2($AnalyData, $thisIndex) {
    $pip = 0.0;
    $SuggestColor = "";

    $pip = abs(floatval($AnalyData[$thisIndex]['pip']));
	$macd  = abs(floatval($AnalyData[$thisIndex]['MACDHeight']));
	$ema3SlopeValue  = abs(floatval($AnalyData[$thisIndex]['ema3SlopeValue']));

     

	 
	


    $emaAbove = $AnalyData[$thisIndex]['emaAbove'];

    if ($emaAbove === '5') {
        $SuggestColor = 'Red';
    }
    if ($emaAbove === '3') {
        $SuggestColor = 'Green';
    }

    //echo $pip . '=' . $SuggestColor . "\n";

    return $SuggestColor;
}

function fixedTrade($AnalyData,$thisIndex,&$tradeList) {
	 
	     echo "<br>--------------  Fix "; 
		 echo $AnalyData[$thisIndex]['candleID'] ;
		 echo "----------------<br><span style='color:red'>";
		 $balance = 0 ;
         $nextIndex = $thisIndex+1 ;
         for ($i=$thisIndex+1;$i<=count($AnalyData)-1;$i++) {
			 $suggestColor = getSuggestColor2($AnalyData, $i);
			 $resultColor = getResultColor($AnalyData, $i) ;
			 echo $AnalyData[$i]['candleID'] . ' = ' .$suggestColor ." vs " . $resultColor ;
			 $winStatus = '';
			 if ($suggestColor === $resultColor ) {
				 echo '--Win' . '<br></span>' ; //return;
				 $winStatus = 'y';

			 } else {
				 echo '--Loss' . '<br>' ;
				 $winStatus = 'n';
             } 
			 $sObj = new stdClass();
	         $sObj->candleID = $AnalyData[$i]['candleID'] ;
	         $sObj->timefrom_unix = $AnalyData[$i]['timefrom_unix'] ;
			 $sObj->RSI  = $AnalyData[$i]['rsi'] ;
	         $sObj->CutPointType = $AnalyData[$i]['CutPointType'] ;
	         $sObj->suggestColor = $suggestColor ;
	         $sObj->nextColor = $resultColor ;
             $sObj->winStatus = $winStatus;
	         $sObj->balance = $balance;
	         $tradeList[] = $sObj ;
			 if ($winStatus == 'y') {
				 return ;
			 }

         }
		 echo '</span>';
         


} // end function

function checkEverTrade($candleID,$tradeList){

    $foundEverTrade = false; 
	for ($i2=0;$i2<=count($tradeList)-1;$i2++) {
		   if ( $candleID  === $tradeList[$i2]->candleID ) {
               $foundEverTrade = true; 			  
			   break;
		   }          	      
	}
	return $foundEverTrade ; 

} // end function



function Main() {
 


$AnalyData = getAnalyObject() ;
$maxLossCon = 0 ;
echo "<h2>" . count($AnalyData) . " รายการ </h2>";
$n = 0 ; $nwin = 0 ; $balance = 0 ;
$tradeList = array();
for ($i=0;$i<=count($AnalyData)-1;$i++) {
   $pip =  floatval($AnalyData[$i]['pip']) ;
   $macd =  floatval($AnalyData[$i]['MACDHeight']) ;
   $thisHour = date('H',$AnalyData[$i]['timestamp'])  ;
   $turnList = $AnalyData[$i]['PreviousTurnType'].'->' .
   $AnalyData[$i]['PreviousTurnTypeBack2'] . '->' . 
   $AnalyData[$i]['PreviousTurnTypeBack3'] . '->' .
   $AnalyData[$i]['PreviousTurnTypeBack4'] ;
   

   
   if ($AnalyData[$i]['CutPointType'] !== 'N' ) {
     $EverTrade =  checkEverTrade($AnalyData[$i]['candleID'],$tradeList);
	 if ($EverTrade== false) {
	 
       if (!isset($oldHour)) {
           $oldHour = $thisHour ;
	   }
	   if ($oldHour != $thisHour) {
		   echo "<hr>";
	   } 
	   if ($AnalyData[$i]['CutPointType'] ==='3->5') {
	     $suggestColor = 'Red';
	   }
	   if ($AnalyData[$i]['CutPointType'] ==='5->3') {
	     $suggestColor = 'Green';
	   }
	   $foundEverTrade = false;
	   $thisCandleID = $AnalyData[$i]['candleID'] ;
	   for ($i2=0;$i2<=count($tradeList)-1;$i2++) {
		   if ( $thisCandleID  === $tradeList[$i2]->candleID ) {
               $foundEverTrade = true; 
			   //echo '<h2>Found</h2>';
			   break;
		   }          	      
	   }
	   $n++ ;
	   $nextColor = $AnalyData[$i+1]['thisColor'] ;
	   echo $n. ' ] '.$AnalyData[$i]['candleID']. '::' . $AnalyData[$i]['timefrom_unix'] . ' ' .$AnalyData[$i]['CutPointType']. ' pip='. $pip ;
       echo ' macd='. $macd ;
	   echo ' next color='	. $nextColor ;
	   
	    

       if ($AnalyData[$i]['CutPointType'] ==='3->5' && $nextColor =='Red') {
           echo '---->Win'  ; $nwin++ ;
		   $winStatus = 'y'; $balance =  $balance + 1 ;
		   echo " { Win=$nwin++} {Balance=$balance}";
       } 
	   if ($AnalyData[$i]['CutPointType'] ==='3->5' && $nextColor =='Green') { ?>
	      <button type='button' id='' class='mBtn' 
		  onclick="doAjaxFixTrade('<?=$AnalyData[$i]['candleID']?>')">
		  Get Fixed
		  </button>
		  <span id='resultFixed_<?=$AnalyData[$i]['candleID']?>' style='color:red;font-weight:bold;padding:10px'></span>
      
	   <?php 
	      $winStatus = 'n';
	      $balance =  $balance - 1 ;
		  //fixedTrade($AnalyData,$i,$tradeList);
	   }

	   if ($AnalyData[$i]['CutPointType'] ==='5->3' && $nextColor =='Green') {
           echo '---->Win'  ; $nwin++; $winStatus = 'y'; $balance =  $balance + 1 ;
		   echo " { Win=$nwin} {Balance=$balance}";
       } 
	   if ($AnalyData[$i]['CutPointType'] ==='5->3' && $nextColor =='Red') { ?>
          <button type='button' id='' class='mBtn' 
		  onclick="doAjaxFixTrade('<?=$AnalyData[$i]['candleID']?>')">
		  Get Fixed2
		  </button>
		  <span id='resultFixed_<?=$AnalyData[$i]['candleID']?>' style='color:red;font-weight:bold;padding:10px'></span>
	   <?php
         $balance =  $balance - 1 ; 
 	     $winStatus = 'n';
		 //fixedTrade($AnalyData,$i,$tradeList);
	   } 
	   echo '<br>'; 
	   
	   $oldHour = $thisHour; 
	   $sObj = new stdClass();
	   $sObj->candleID = $AnalyData[$i]['candleID'] ;
	   $sObj->timefrom_unix = $AnalyData[$i]['timefrom_unix'] ;
	   $sObj->RSI  = $AnalyData[$i]['rsi'] ;
	   $sObj->CutPointType = $AnalyData[$i]['CutPointType'] ;
	   $sObj->suggestColor = $suggestColor ;
	   $sObj->nextColor = $nextColor ;
       $sObj->winStatus = $winStatus;
	   $sObj->balance = $balance;
	   $sObj->turnList = $turnList ;
	   $tradeList[] = $sObj ;

	   if ($winStatus === 'n') {
		  fixedTrade($AnalyData,$i,$tradeList) ;
	   }


	 } // end if $EverTrade
	    
   } // end if check cutpoint
   
} 

echo "NumWin=" .$nwin   . '/' . count($AnalyData) . '<br>';
$balance2 = 0 ; $lossCon = 0 ;
for ($i=0;$i<=count($tradeList)-1;$i++) {
	if ($tradeList[$i]->winStatus ==='y') {
		$balance2 = $balance2+ 0.95 ;
		$lossCon = 0 ;
	} else {
		$balance2 = $balance2 -1  ;
		$lossCon++ ;
	}
	$tradeList[$i]->lossCon = $lossCon ;   
}
//print_r($tradeList);
echo JSON_ENCODE($tradeList);

$stTable ='<table border=1><tr><td>Num</td><td>CandleID</td><td>Timefrom_unix</td>';
$stTable .='<td>RSI</td><td>Win Status</td><td>Loss Con</td><td>Money Trade</td><td>Profit</td><td>Balance</td><td>Pocket Money</td><td>TurnList</td></tr>';
$MoneyTrade =1 ; $lossCon = 0; $lastWinStatus = ''; $balance= 0;
$PocketMoney = 40 ;
for ($i=0;$i<=count($tradeList)-1;$i++) {
  if ($tradeList[$i]->winStatus==='n') {
	  $classname = 'redBG' ; $lossCon++ ;
  } else {
	  $classname = '' ; $lossCon = 0 ;
  }
  $stTable .= '<tr>';
  $stTable .= '<td>'. ($i+1). '</td>';

  $stTable .='<td>'. $tradeList[$i]->candleID .'</td>';
  $stTable .='<td>'. $tradeList[$i]->timefrom_unix .'</td>';
  $stTable .='<td>'. $tradeList[$i]->RSI .'</td>';
  $stTable .= '<td class="'. $classname . '">' .$tradeList[$i]->winStatus. '</td>';
  $stTable .= '<td>' . $tradeList[$i]->lossCon .'</td>';
  if ($i>0) {  
    $lastLossCon = $tradeList[$i-1]->lossCon;
    $MoneyTrade = getMoneyTrade($lastLossCon) ;
  } else {
	  $MoneyTrade = 1;
  }
  $stTable .= '<td>' . $MoneyTrade .'</td>';
  if ($tradeList[$i]->winStatus==='n') {
	 $profit = $MoneyTrade*-1 ;
  } else {
    $profit = $MoneyTrade*0.95 ;
  }
  $stTable .= '<td>' . $profit .'</td>';
  $balance = $balance+ $profit;

  $stTable .= '<td>' . $balance .'</td>';
  $PocketMoney = $PocketMoney + $profit;
  $stTable .= '<td>' . $PocketMoney .'</td>';
  $stTable .= '<td>' . $turnList .'</td>';

  $stTable .= '</tr>';
} // end function Main
$stTable .= '</table>';
echo $stTable;


} // end func main

function getMoneyTrade($lossCon) {
 
         if ($lossCon ===0) {
			 return 1 ;
         }
		 if ($lossCon ===1) {
			 return 2 ;
         }
		 if ($lossCon ===2) {
			 return 6 ;
         }
		 if ($lossCon ===3) {
			 return 12 ;
         }
		 if ($lossCon ===4) {
			 return 20 ;
         }
		 if ($lossCon ===5) {
			 return 40 ;
         }
		 if ($lossCon ===6) {
			 return 100 ;
         }
		 if ($lossCon ===7) {
			 return 100 ;
         }
		 if ($lossCon ===8) {
			 return 100 ;
         }







} // end function


Main();
 
$AnalyObj = getAnalyObject();
echo '<hr>';
echo "<h2>Count=" . count($AnalyObj) . '</h2>';
//PreviousTurnType
$totalTurnUp = 0;$totalTurnDown = 0;
$turnList = array();
$numTurn = 0 ; $previousID = '';
for ($i=0;$i<=count($AnalyObj)-1;$i++) {
	if ($AnalyObj[$i]['PreviousTurnType'] !=='N') {		
		$sObj = new stdClass() ;
		$sObj->turnno = $numTurn++;
		$sObj->candleID = $AnalyObj[$i-1]['candleID'];
		$sObj->timefrom = $AnalyObj[$i-1]['timefrom_unix'];
		$sObj->TurnType = $AnalyObj[$i]['PreviousTurnType'];
		$sObj->previousID = $previousID;
		$numCandle= ($AnalyObj[$i-1]['candleID']- intval($previousID))/60 ;
		$sObj->numCandle = $numCandle;
		$turnList[] = $sObj;
		$previousID = $AnalyObj[$i-1]['candleID'];
		if ($AnalyObj[$i]['PreviousTurnType'] ==='TurnUp') {
	  	  $totalTurnDown++ ;
	    }   
	    if ($AnalyObj[$i]['PreviousTurnType'] ==='TurnDown') {
		  $totalTurnDown++ ;
	    } 
			
	}   
	  

} // end for
echo "<h2>Total TurnUp =" . $totalTurnUp . '</h2>';
echo "<h2>Total Turn Down =" . $totalTurnDown . '</h2>';
echo '<hr>';
echo  JSON_ENCODE($turnList);
/*
$start = 0 ;
for ($i=0;$i<=count($AnalyObj)-1;$i++) {
	if (intval($AnalyObj[$i]['candleID']) === 1737195540) {
		$start = $i ; break;
	}   	
}
echo "<br>Found IN $start" . '<br>';
$totalCandle = 0 ; $totalGreen = 0 ; $totalRed= 0 ;
for ($i=$start;$i<=count($AnalyObj)-1;$i++) {
	if ($AnalyObj[$i]['PreviousTurnType'] ==='TurnDown') {
		$stop = $i ; break;
	}  else {
		$totalCandle++ ;
	}
	if ($AnalyObj[$i]['thisColor'] ==='Green') {
		$totalGreen++;
	}
	if ($AnalyObj[$i]['thisColor'] ==='Red') {
		$totalRed++;
	}
}
echo "<br>Stop IN $stop" . '<br>';
echo "<h2>Total Candle in 1 Turn  =" . $totalCandle . '<h2>';
echo "<h2>Total Green in 1 Turn  =" . $totalGreen . '<h2>';
echo "<h2>Total Red in 1 Turn  =" . $totalRed . '<h2>';

$stTable ='<table><tr><td>Num</td><td>CandleID</td>' ;
$stTable ='<td>time</td><td>TurnType</td>' ;

for ($i=$start;$i<=count($AnalyObj)-1;$i++) {
	if ($AnalyObj[$i]['PreviousTurnType'] ==='TurnDown') {
		$stop = $i ; break;
	}  else {
		$totalCandle++ ;
	}
	if ($AnalyObj[$i]['thisColor'] ==='Green') {
		$totalGreen++;
	}
	if ($AnalyObj[$i]['thisColor'] ==='Red') {
		$totalRed++;
	}
}

*/



?>

<script>
async function doAjaxFixTrade(candleid) {

    let result ;
    let ajaxurl = 'AjaxFixTrade.php';
    let data = { 
	 "Mode": "FixTrade" ,
     "candleid" : candleid 
     //"AnalyData" : document.getElementById("analyTextData").value
    } ;
    data2 = JSON.stringify(data);
	//alert(data2);
    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
	        // dataType: "json",
            data: data2,
	    success: function(data, textStatus, jqXHR){
              console.log(textStatus + ": " + jqXHR.status);
              // do something with data
            },
            error: function(jqXHR, textStatus, errorThrown){
			  alert(textStatus + ": " + jqXHR.status + " " + errorThrown);	 
              console.log(textStatus + ": " + jqXHR.status + " " + errorThrown);
            }
        });
        //alert(result);
		id= 'resultFixed_' + candleid ;
		document.getElementById(id).innerHTML = result ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

</script>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
