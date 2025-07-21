<?php
  
  ob_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  $desc = 'ทำการหาจำนวน  NumLoss จาก Timeframe ต่างๆกันเริ่มจาก 5 นาทีขึ้นไป';

?>
<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Document</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> 

  <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tempus Dominus CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    
    <!-- Font Awesome for calendar icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
<style>
table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            cursor: pointer;
        }
        th {
            background-color: #f4f4f4;
        }
        .highlight {
            background-color: yellow;
        }
        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }
 
.sarabun-regular {
  font-family: "Sarabun", sans-serif;
  font-weight: 400;
  font-style: normal;
}

</style>


<script src="newlab.js" ></script>



 </head>
 <body class='sarabun-regular'>
  
  <h1>สรุปการ Loss บน timeframe ใหญ่ </h1>
  <table>
  <tr>
	<td>TimeFrame</td>
	<td><select id="timeframeSelected">
		<option value="5M" selected>5M
		<option value="15M">15M
		<option value="30M">30M
		<option value="1H">1H

	</select></td>
	<td>วันที่ Lab</td>
	<td><?php getDateFromCandle();?></td>
	<td></td>
  </tr>
  </table>
  

<!-- 
<div class="container mt-5">
        <h3>เลือกวันที่ (วว/ดด/ปป)</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group date" id="datetimepicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker" placeholder="เลือกวันที่"/>
                    <div class="input-group-append" data-target="#datetimepicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 -->
	<div id="result" class="bordergray flex">
	     
	</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>   
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

   <!-- Required JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/th.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Set moment locale to Thai
            moment.locale('th');
            
            // Initialize datepicker
            $('#datetimepicker').datetimepicker({
                format: 'L',
                locale: 'th',
                icons: {
                    time: 'fas fa-clock',
                    date: 'fas fa-calendar',
                    up: 'fas fa-arrow-up',
                    down: 'fas fa-arrow-down',
                    previous: 'fas fa-chevron-left',
                    next: 'fas fa-chevron-right',
                    today: 'fas fa-calendar-check',
                    clear: 'fas fa-trash',
                    close: 'fas fa-times'
                },
                buttons: {
                    showToday: true,
                    showClear: true,
                    showClose: true
                },
                tooltips: {
                    today: 'วันนี้',
                    clear: 'ล้าง',
                    close: 'ปิด',
                    selectMonth: 'เลือกเดือน',
                    prevMonth: 'เดือนก่อนหน้า',
                    nextMonth: 'เดือนถัดไป',
                    selectYear: 'เลือกปี',
                    prevYear: 'ปีก่อนหน้า',
                    nextYear: 'ปีถัดไป',
                    selectDecade: 'เลือกทศวรรษ',
                    prevDecade: 'ทศวรรษก่อนหน้า',
                    nextDecade: 'ทศวรรษถัดไป',
                    prevCentury: 'ศตวรรษก่อนหน้า',
                    nextCentury: 'ศตวรรษถัดไป'
                }
            });
        });
    </script>


  <script src="https://lovetoshopmall.com/src/jsUtil.js"></script>
  

<?php
  

function MainLab() { 
	return;

//$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
/*
SELECT distinct(date(timefrom_unix)) , min(id) 
FROM `AnalyEMA_HistoryForLab`
group by  date(timefrom_unix)

*/
require_once("newutil2.php"); 
require_once("clsTrade.php"); 
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

function getDateFromCandle() { 

require_once("newutil2.php"); 
$pdo = getPDONew();
$tablename= 'AnalyEMA_HistoryForLab_5M';
$tablename= 'AnalyEMA_HistoryForLab';
//$tablename= 'AnalyEMA_HistoryForLab';

$sql = 'select distinct(DATE(timefrom_unix)) as dateCandle from '. $tablename . ' WHERE DATE(timefrom_unix) <> "0000-00-00" '; 
$params = array();
//$rs = getData_RS($sql) ;
$rs=pdogetMultiValue2($sql,$params,$pdo) ;
?>
<select id="dateCandle">
	<?php
	while($row = $rs->fetch( PDO::FETCH_ASSOC )) { ?>	
		<option value="<?=$row['dateCandle']?>"><?=$row['dateCandle'];?>
	<?php		    
}
?>
</select>
<button type='button' id='' class='mBtn' onclick="goPrevious()"><--</button>
<button type='button' id='' class='mBtn' onclick="goNext()">--></button>

<button type='button' id='' class='mBtn' onclick="doAjaxFindLoss()">สรุป Loss</button>
<?php

} // end function


// MainLab();
/*
Spread = 0.2 (ค่าธรรมเนียม)
ราคา Bid และ Ask: ค่า Spread คือความแตกต่างระหว่างราคา Bid (ราคาที่ผู้ซื้อเสนอซื้อ) และราคา Ask (ราคาที่ผู้ขายเสนอขาย)
สมมติว่าคุณต้องการซื้อคู่สกุลเงิน EUR/USD และพบว่าราคา Bid อยู่ที่ 1.1000 และราคา Ask อยู่ที่ 1.1005 ในกรณีนี้ ค่า Spread คือ 0.0005 หรือ 0.5 pips

Volume 0.01 -> 1.04784 --> 1.04777-->0.00007 ผลต่าง  0.07 กำไร  0.07 USD
  ราคาลดลง: 1.04784 - 1.04777 = 0.00007 นับเป็น 0.7 PIP
Lot Size=0.01 นั่นคือ Volume = 0.01 
---> pip = (close2-close1 )* 10,000 = 0.7 PIP
---> ผลต่าง = (close2-close1 )* 1,000  = 0.07 USD

Volume    1 -> 1.05123 --> 1.05114 ผลต่าง  9.00 กำไร  9    USD
 ราคาลดลง: 1.05123 - 1.05114 = 0.00009 นับเป็น 0.9 pip

วิธีคิดกำไร 
Profit = ( Close1 - Close2 ) * Volume 


ฉันซื้อ  eurusd ที่ราคาปิด  1.05123 และ ขายออกไปที่  1.05114 คิดเป็นกี่ pip และ กี่ point

1.055605 - 1.055585 = 0.00002->0.2 pip
*/

?>
 </body>
</html>
