<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);  

require_once('../iqlab/sortGetAction.php');

$candleData = getCandleData2() ;
require_once('api/phpCandlestickIndy.php');
$clsStep1 = new TechnicalIndicators();   

require_once('api/phpAdvanceIndy.php');
$clsStep2 = new AdvancedIndicators();   
$result = $clsStep1->calculateIndicators($candleData);
$result2= $clsStep2->calculateAdvancedIndicators($result);
$result2= Final_AdvanceIndy($result2)  ;

$stAnaly = JSON_ENCODE($result2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
$myfile = fopen("newDerivObject/AnalyDataBig.json", "w") or die("Unable to open file!");
fwrite($myfile, $stAnaly);
fclose($myfile); 
$macdThershold = 0.1 ; $lastMacdHeight = 0 ;
$sAr = array(); $winCon = 0 ; $lossCon = 0 ; $LotNo = 0 ;
$balance=0 ; $maxBalance= 0 ; $maxLossCon = 0 ;
$rowMaxLossCon = 0 ;
$MoneyTrade = array(1,2,4,8,16,54,162,160,320,640,1000,2500,6000,8000,4) ;
//$MoneyTrade = array(1,2,6,6,6,6,6,6,6,6,6,6,6,6,6) ;
$LossConAr = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$lotNoAr = array();
$stLossCon5 = '';
for ($i=0;$i<=count($result2)-1;$i++) {
  $sObj = new stdClass();
  $sObj->No   = $i+1 ;
  $sObj->candleID   = $result2[$i]["candleID"] ;

  $sObj->timefrom_unix   = $result2[$i]["timefrom_unix"] ;
  $sObj->thisColor = $result2[$i]["thisColor"] ;
  $sObj->PreviousTurnType =  $result2[$i]["PreviousTurnType"];
  $sObj->TurnMode999 =  $result2[$i]["TurnMode999"];
  $AnalyObj = $result2[$i] ;
  list($thisAction,$forecastColor,$actionReason)=
  getActionFromIDVerObject_Sorted($AnalyObj,$macdThershold,$lastMacdHeight) ;
  $sObj->thisAction = $thisAction ;
  $sObj->actionReason = $actionReason ;

  $fcColor = ($thisAction == 'CALL') ? 'Green' : 'Red';
  if ($i+1 < count($result2)-1) {  
    $resultColor = $result2[$i+1]['thisColor'] ;
  } else {
    $resultColor = '???' ;
  }

  $thisMoneyTrade = $MoneyTrade[$lossCon];  
  $winStatus = ($fcColor == $resultColor) ? 'Win' : '-';
  if ($thisAction !== 'Idle') {    
	  if ( $winStatus== 'Win' ) {
		  $LossConAr[$lossCon]++ ;
		  $LotNo++ ;
		  if ($lossCon ===5) {
			  $stLossCon5 .= $result2[$i]['timefrom_unix'] .';';
		  }
		  /*
		  $sObjLotNo=new stdClass(); 
		  $sObjLotNo->LotNo = $LotNo ;$sObj->timefrom_unix = $result2[$i+1]['timefrom_unix'] ;
		  */

	  }
	  if ($winStatus === 'Win') { $profit=  $thisMoneyTrade *0.94 ;$winCon++  ; $lossCon = 0 ;}
	  if ($winStatus === '-')   { $profit=  $thisMoneyTrade *-1 ;$lossCon++ ; $winCon = 0 ; }

	  $balance = $balance + $profit ;
	  if ($balance > $maxBalance) {
		  $maxBalance = $balance;
	  } 

	  if ($lossCon > $maxLossCon) {
		 $maxLossCon = $lossCon ;
		 $timeMaxLossCon = $AnalyObj['timefrom_unix'] ;
		 $rowMaxLossCon = $i ;
	  }
  } else {
    $fcColor = 'Idle';
  }
  
  $sObj->forecastColor = $fcColor;
  $sObj->resultColor = $resultColor;
  $sObj->winStatus = $winStatus;
  $sObj->winCon =   $winCon;
  $sObj->lossCon =  $lossCon;
  $sObj->lotNo   =   $LotNo;
  $sObj->thisMoneyTrade   = $thisMoneyTrade ;
  $sObj->profit   = $profit ;
  $sObj->balance   = $balance ;



  $sAr[] = $sObj ;
   
}

$stAnaly = JSON_ENCODE($sAr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
$myfile = fopen("newDerivObject/AnalyDataSmall.json", "w") or die("Unable to open file!");
fwrite($myfile, $stAnaly);
fclose($myfile);  

$totalWin = 0 ; 
$totalTrade = 0 ;


/*
echo "<pre>";
echo $stAnaly;
echo "</pre>";
*/

$array = array_map(function($obj) {
    return (array) $obj;
}, $sAr);
?>
<style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
			cursor:pointer;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            /*background-color: #f2f2f2;*/
			background-color: #0080ff;

        }
        tr:nth-child(even) {
            //background-color: #f9f9f9;
        } 
		.rowSelected { background-color: #80ff80; }
    </style>

<div id="tableContainer" style="height: 1300px; overflow: auto;">
<table id='myTable'>
 
	<?php writeHeadTable();?>  
    <?php foreach ($array as $row):	
	      $totalTrade++ ;
		  if ($totalTrade % 6 ===0) {
             writeHeadTable();
		  }
          if ($row['winStatus'] ==='Win') { $totalWin++ ; }	       
	?>
    <tr id='tr_<?=$row['candleID']?>' onclick=setRowSelected(this.id)>
        <td><?php echo htmlspecialchars($row['No']); ?></td>
        <td><?php echo htmlspecialchars($row['timefrom_unix']); ?></td>
        <td><?php echo htmlspecialchars($row['thisColor']); ?></td>
        <td><?php echo htmlspecialchars($row['PreviousTurnType']); ?></td>
        <td><?php echo htmlspecialchars($row['TurnMode999']); ?></td>
        <td><?php echo htmlspecialchars($row['thisAction']); ?></td>
        <td><?php echo htmlspecialchars($row['actionReason']); ?></td>
        <td><?php echo htmlspecialchars($row['forecastColor']); ?></td>
		<td><?php echo htmlspecialchars($row['resultColor']); ?></td>
		<td><?php echo htmlspecialchars($row['winStatus']); ?></td>
		<td><?php echo htmlspecialchars($row['winCon']); ?></td>
		<?php 
		  if ($row['lossCon'] < 5) { ?>		  
 		    <td style='background:whitesmoke'>
			  <?php echo htmlspecialchars($row['lossCon']); ?>
			 </td>			
         <?php } else {  ?>
		 <td style='background:#ff0080'><?php echo htmlspecialchars($row['lossCon']); 
			?></td>

		 <?php } ?>
		<td><?php echo htmlspecialchars($row['lotNo']); ?></td>

		<td><?php echo htmlspecialchars($row['thisMoneyTrade']); ?></td>
		<td><?php echo htmlspecialchars($row['profit']); ?></td>
		<td><?php echo htmlspecialchars($row['balance']); ?></td>

		
    </tr>
    <?php endforeach; ?>
</table>
<?php

$totalTrade = count($array)-1 ;
$MaxLotNo =  $array[$totalTrade]['lotNo'] ;
//$lossConArray = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)  ;


?>

</div>


<h2>Total Win = <?=$totalWin;?> จาก <?=$totalTrade?>
 Percent Win = <?=($totalWin/$totalTrade)*100;?> %</h2>
<h2>Max Loss Con = <?=$maxLossCon;?>-<?=$timeMaxLossCon;?> Max LoT No = <?=$MaxLotNo;?></h2>

<h2>Max Balance = <?=$maxBalance;?> Max Balance Bath = <?=$maxBalance*33;?></h2>
<table>
<tr>
	<td width=80>Go Time</td>
	<td width=120><input type="text" id="timewant" style='padding:8px;border:1px solid lightgray'></td>
	<td><button type='button' id='' class='mBtn' onclick="scroll2(-1)">Go Time Of Table</button></td>
	<td></td>
</tr>
</table>

<button type='button' id='' class='mBtn' onclick="scroll2('<?=$timeMaxLossCon;?>')">Button1</button>
<?php
echo 'LossCon5 = ' .$stLossCon5 ;

?>

<?php 
//print_r($LossConAr); 
$stTable = '<table><tr>';
for ($i=0;$i<=count($LossConAr)-1;$i++) {
   $stTable .= '<td>' . $i . '</td>';
}
$stTable .= '</tr><tr>';
$total = 0 ;
for ($i=0;$i<=count($LossConAr)-1;$i++) {
  if ($LossConAr[$i] > 0) {
	$stTable .= '<td>' . $LossConAr[$i] . '</td>';
	$total = $total + $LossConAr[$i] ;
  } else {
    $stTable .= '<td>-</td>';
  }
}
$stTable .= '<td>' . $total . '</td>';
$stTable .= '</tr><tr>';
for ($i=0;$i<=count($LossConAr)-1;$i++) {
  if ($LossConAr[$i] > 0) {
	$stTable .= '<td>' . (round($LossConAr[$i]/$total,4))*100 . '</td>';
	$total = $total + $LossConAr[$i] ;
  } else {
    $stTable .= '<td>-</td>';
  }
}
$stTable .= '<td>-</td>';

$stTable .= '</tr></table>';
echo  $stTable;



?>

<?php

function getCandleData2() {

 $newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/deriv/newDerivObject/';
 $sFileName =  $newUtilPath.'rawData.json';
 $st = '';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file); 
 $candleDataA = JSON_DECODE($st,true);

 
 echo 'Len=' . count($candleDataA) . '<br>';
 return $candleDataA ;

} // end function


function Final_AdvanceIndy($result2) { 

          $lastTurnID = 0;  
		  for ($i=2;$i<=count($result2)-1;$i++) {
			  $curIndex = $i;
              $previousIndex = $i-1 ;
			  $previousIndexBack2 = $i-2 ;
			  if (
				 $result2[$previousIndex]['ema3'] < $result2[$curIndex]['ema3'] &&
                 $result2[$previousIndex]['ema3'] < $result2[$previousIndexBack2]['ema3'] 
				 ) {
                 $result2[$curIndex]['PreviousTurnType'] = 'TurnUp' ;
				 $result2[$curIndex-1]['TurnType'] = 'TurnUp' ;
				 $lastTurnID = $result2[$curIndex]['candleID'] ;
				 //$result2[$curIndex]['lastTurnID'] = $lastTurnID;
			  }
			  if (
				 $result2[$previousIndex]['ema3'] > $result2[$curIndex]['ema3'] &&
                 $result2[$previousIndex]['ema3'] > $result2[$previousIndexBack2]['ema3'] 
				 ) {
                 $result2[$curIndex]['PreviousTurnType'] = 'TurnDown' ;
				 $result2[$curIndex-1]['TurnType'] = 'TurnDown' ;
				 $lastTurnID = $result2[$curIndex]['candleID'] ;
				 
			  } 
			  $result2[$curIndex]['lastTurnID'] = $lastTurnID;			   
		  }
		  

          //ปรับค่า
		  for ($i=0;$i<=count($result2)-1;$i++) {
                
               $pip = $result2[$i]['open'] - $result2[$i]['close'];
               $pip = number_format($pip , 2) ;
			   $previousColor = null ;$previousColorBack2 = null;
			   $previousColorBack3 = null ;$previousColorBack4 = null; 

			   //$previousTurnType = null ;$previousTurnTypeBack2 = null;
			  // $previousTurnTypeBack3 = null ;$previousTurnTypeBack4 = null; 

			   $macdconverValue = 0.0 ;
			   $MACDConvergence = '';

			   if ($i >= 1) {
				   $previousColor = $result2[$i-1]['thisColor'] ;
				   $previousTurnType = $result2[$i-1]['PreviousTurnType'] ;
				   $macdconverValue = abs($result2[$i]['MACDHeight']) - abs($result2[$i-1]['MACDHeight']);
				   if ($macdconverValue < 0) {
					   $MACDConvergence ='Conver';
				   }
				   if ($macdconverValue > 0) {
					   $MACDConvergence ='Diver';
				   }
				   if ($macdconverValue == 0) {
					   $MACDConvergence ='P';
				   }

			   }
			   if ($i >= 2) {
				   $previousColorBack2 = $result2[$i-2]['thisColor'] ;
				   //$previousTurnTypeBack2 = $result2[$i-1]['PreviousTurnType'] ;
			   }
			   if ($i >= 3) {
				   $previousColorBack3 = $result2[$i-3]['thisColor'] ;
				   //$previousTurnTypeBack3 = $result2[$i-1]['PreviousTurnType'] ;
			   }
			   if ($i >= 4) {
				   $previousColorBack4 = $result2[$i-4]['thisColor'] ;
				   //$previousTurnTypeBack4 = $result2[$i-1]['PreviousTurnType'] ;
			   }
               $result2[$i]['pip'] = $pip ;
			   $result2[$i]['previousColor'] = $previousColor;
			   $result2[$i]['previousColorBack2'] = $previousColorBack2;
			   $result2[$i]['previousColorBack3'] = $previousColorBack3;
			   $result2[$i]['previousColorBack4'] = $previousColorBack4;

			   
			   $result2[$i]['macdconverValue'] = $macdconverValue ; 			   
			   $result2[$i]['MACDConvergence'] = $MACDConvergence ; 

			   $result2[$i]['timefrom_unix'] =  date('H:i',$result2[$i]['timestamp']); 

			   if ($result2[$i]['TurnType'] === 'TurnUp' || 
				   $result2[$i]['TurnType'] === 'TurnDown' ) {
				    $result2[$i]['lastTurnID'] = $result2[$i]['candleID'] ;
			   } else {
				   if ($i-1 > 0) {				   
                     $result2[$i]['lastTurnID'] = $result2[$i-1]['lastTurnID'];
				   }
			   }			  
		  }
		  for ($i=2;$i<=count($result2)-1;$i++) {
			  $result2[$i]['PreviousTurnTypeBack2'] = $result2[$i-1]['PreviousTurnType'] ; 
			  $result2[$i]['PreviousTurnTypeBack3'] = $result2[$i-2]['PreviousTurnType'] ; 
			  if ($i > 2) {
                $result2[$i]['PreviousTurnTypeBack4'] = $result2[$i-3]['PreviousTurnType'] ; 
			  }
		  }
		  for ($i=1;$i<=count($result2)-1;$i++) {
			  $distance= ($result2[$i]['candleID'] - $result2[$i]['lastTurnID'])/60 ; 
			  $result2[$i]['distance'] = $distance ; 
		  } 

		  for ($i=2;$i<=count($result2)-1;$i++) {
			  $candleCode = $result2[$i]['emaAbove'].'-' . $result2[$i]['thisColor'].'-'.
              $result2[$i]['emaConflict'].'-' . $result2[$i]['MACDConvergence'].'-' ;
			  $candleCode .= 'dis'.$result2[$i]['distance'].'-' ;
			  $candleCode .= 'cut'.$result2[$i]['CutPointType'].'-' ;
			  //$candleCode .= $result2[$i]['candleWick']['candleType'].'-' ;
			  $result2[$i]['CandleCode'] = $candleCode;
		  } 

		 for ($i=1;$i<=count($result2)-1;$i++) {
			$previousADX = floatval($result2[$i-1]['adx']) ;
			$ADX = floatval($result2[$i]['adx']) ;
			if ($ADX > $previousADX) {
			   $result2[$i]['adxDirection'] = 'Up';
			} else {
			   $result2[$i]['adxDirection'] = 'Down';
			}	
			
			
		  }

		  for ($i=1;$i<=count($result2)-1;$i++) {
			 
               if ($result2[$i]['PreviousTurnType'] ==='' || $i==1) {				  
			   } 
			   $result2[$i]['PreviousSlopeDirection'] = $result2[$i-1]['ema3slopeDirection'];
             
			   if ($result2[$i]['PreviousTurnType'] ==='TurnUp') {
			      $result2[$i]['TurnMode999'] = 'TurnUp';
			   }
			   if ($result2[$i]['PreviousTurnType'] ==='TurnDown') {
			      $result2[$i]['TurnMode999'] = 'TurnDown';
			   }
			   if ($result2[$i]['PreviousTurnType'] ==='N') {
			      $result2[$i]['TurnMode999'] = $result2[$i-1]['TurnMode999']  ;
			   } 
               
			   
			   if ($result2[$i]['TurnMode999'] ==='' || $result2[$i]['TurnMode999'] ==='I') {				  
			      if ($result2[$i]['emaAbove'] ==='3') {				  
				    $result2[$i]['TurnMode999'] = 'TurnUp';
				  } else {
                    $result2[$i]['TurnMode999'] = 'TurnDown';
				  }
			   }
		  } 

		  for ($i=1;$i<=count($result2)-2;$i++) {
			  $cTmp = (array)$result2[$i]['candleWick'] ;
			  //echo $i . '<br>' ;
			  $uWick = floatval($cTmp["upperWickPercent"]);
			  $lWick = floatval($cTmp["lowerWickPercent"]);
			  //echo $i . '--'. $uWick ."--". $lWick . '<br>';
			  if ($uWick === 0.0 || $lWick === 0.0) {                
                $result2[$i]['isBongton'] = 'y';
			  } else {
                $result2[$i]['isBongton'] = 'n';
			  }
		  }

		  for ($i=2;$i<=count($result2)-2;$i++) {			  			  
             $result2[$i]['isPreviousBongton'] = $result2[$i-1]['isBongton'] ;
			 $result2[$i-2]['isPreviousBongtonBack2'] = $result2[$i-1]['isPreviousBongton'] ;
			  
		  }

		  return $result2;


} // end function 

function writeHeadTable() { ?>

<tr>
        <th>Candle ID</th>
        <th>Time</th>
        <th>Color</th>
        <th>Previous Turn</th>
        <th>Turn Mode</th>
        <th>Action</th>
        <th>Action Reason</th>
        <th>Forecast Color</th>
		<th width=100>ResultColor</th>
		<th width=100>Win Status</th>
		<th width=100>Win Con</th>
		<th width=100 >Loss Con</th>
		<th width=100>Lot No</th>

		<th width=100>Money</th>
		<th width=100>Profit</th>
		<th width=100>Balance</th>
    </tr>

<?php
} // end function



?>
<input type="text" id="lastrowSelected" value=1>

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

<script>
function setRowSelected(thisid) {
         
         lastrowSelected = document.getElementById("lastrowSelected").value ;
         $("#"+lastrowSelected).removeClass('rowSelected') ;

		 $("#"+thisid).addClass('rowSelected') ;
		 document.getElementById("lastrowSelected").value =  thisid;

}

function scrollToRow(rowIndex) {
  const container = document.getElementById('tableContainer');
  const table = document.getElementById('myTable');
  const rows = table.getElementsByTagName('tr');
  
  if (rowIndex >= 0 && rowIndex < rows.length) {
    const row = rows[rowIndex];
    const rowTop = row.offsetTop;
    const rowHeight = row.offsetHeight;
    const containerHeight = container.offsetHeight;
    
    container.scrollTo({
      top: rowTop - (containerHeight / 2) + (rowHeight / 2),
      behavior: 'smooth'
    });
    
    // Highlight the row
    Array.from(rows).forEach(r => r.style.backgroundColor = '');
    row.style.backgroundColor = 'yellow';
  }
} 

function smoothScroll(container, targetPosition, duration) {
  const startPosition = container.scrollTop;
  const distance = targetPosition - startPosition;
  let startTime = null;

  function animation(currentTime) {
    if (startTime === null) startTime = currentTime;
    const timeElapsed = currentTime - startTime;
    const scrollY = ease(timeElapsed, startPosition, distance, duration);
    container.scrollTop = scrollY;
    if (timeElapsed < duration) requestAnimationFrame(animation);
  }

  // Easing function - easeInOutQuad
  function ease(t, b, c, d) {
    t /= d / 2;
    if (t < 1) return c / 2 * t * t + b;
    t--;
    return -c / 2 * (t * (t - 2) - 1) + b;
  }

  requestAnimationFrame(animation);
}

function scroll2(timeMaxLossCon) {

	if (timeMaxLossCon=== -1) {
       timeMaxLossCon = document.getElementById("timewant").value ;
	}


    rowFound = 0 ;
    mTable = document.getElementById("myTable"); 
	for (let i=0;i<=mTable.rows.length-1 ;i++ ) {
		if (mTable.rows[i].cells[1].innerHTML=== timeMaxLossCon) {
          mTable.rows[i].classList.add('rowSelected');
          rowFound = i ; break ;
		}	
	}
	if (rowFound===0) {
		alert('Not Found'); return;
	}

	// หรือถ้าใช้ index ของ row (เริ่มจาก 0)
    //let rowIndex = 5; // ต้องการไปยัง row ที่ 6
	let targetRow = document.querySelectorAll("table tbody tr")[rowFound];
	targetRow.scrollIntoView({
	  behavior: "smooth",
	  block: "center"
	});


} // end func

</script>

