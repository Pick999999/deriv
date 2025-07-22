<!-- deriv/newDerivObject/mainpage.php/mainpage.php -->
<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Deriv-Trade</title>
 
  <!-- Bootstrap 5 CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  

	 
    
    <!-- Tempus Dominus CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    

    <!-- Font Awesome for calendar icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

 <link href="https://thepapers.in/deriv/newDerivObject/main.css" rel="stylesheet">
 <link href="https://thepapers.in/deriv/newDerivObject/radio.css" rel="stylesheet">

 <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>  
 <style>
 input[type="checkbox"] {
    width: 30px;
    height: 30px;
    accent-color: blue; /* Changes the color of the checkbox */
}
 </style>
 <link href="" rel="stylesheet">


 </head>
 <body class='sarabun-regular'>
  
  <h1>Deriv Trade </h1>

<label class="switch">
  <input type="checkbox">
  <span class="slider round"></span>
</label>


<div class="myContainer smt-5 bordergray">
          
		 <div id="" class="bordergray flex" style='display:flex;flex-direction:row;justify-content: space-around;'>
		    <div class='boxShadow' style='background:#bfffbf;width:45%; '>
			
             <table id="sortableTable" style='width:98%'>
             
               <tr>
				<td style='width:150px'>Time Duration :: 
				<input type="number" onfocus='this.select();' class='form-control' id="realTimeduration" onblur='SaveInitTrade()'></td>
				<td>Time Unit :: <select name="realDurationUnit" class='form-control'>
					<option value="M" selected>Minute
					<option value="H">Hour
				</select> </td>
				<td>
				<input type="checkbox" id="chkAutotrade" onclick='SaveInitTrade()'> 
				
				 &nbsp;&nbsp;&nbsp;Auto Trade
				</td>
				 
               </tr>
              </table>
            </div>
			
			<div class='boxShadow' style='background:#00d084;width:50%'>
			  <table id="sortableTable" style='width:98%;color:white'>
			  
				<tr>
					<td>Money Trade:: <input type="number" class='form-control' id="realmoneyTrade" style='width:100px' 
					onblur='SaveInitTrade()'
					onfocus='this.select();'></td>
					<td>Stop Loss:: 
					<input type="number" class='form-control' id="realmoneyStopLoss" style='width:100px' onfocus='this.select();'
					onblur='SaveInitTrade()'
					
					></td>
					<td>Profit::: 
					<input type="number" class='form-control' id="realmoneyProfit" style='width:100px' onfocus='this.select();' 
					onblur='SaveInitTrade()'
					></td>
					<td>Check StopLoss<input type="checkbox" id="isCheckStopLoss" style='width:100px' 
					onfocus='this.select();'
					onblur='SaveInitTrade()'
					></td>

					
					 
				</tr>
			  </table>
			</div>


			
		 </div>
		 <div id="" class="bordergray flex" style='justify-content: space-between'>
		   <!-- 
		   <div>
			 <button type='button' id='' class='mBtn' onclick="setClassGraph('Chart1-100')">Chart1-100</button>
			 <button type='button' id='' class='mBtn' onclick="setClassGraph('Chart2-100')">Chart2-100</button>
			 <button type='button' id='' class='mBtn' onclick="setClassGraph('Chart1-50')">Chart1-50</button>
		   </div>
		    -->
		   
		   <div>
		        <button type='button' id='btnStart' class='mBtn green'>Start</button>
 		        
				
				
           </div>
		   
			
			
		 </div>
         
         <div id="MainbtnAssetContainer" class="bordergray flex" style='justify-content: space-between'>
		    
		    <div id="btnAssetContainer" class="bordergray flex">
			</div>
		         
			<div id="" class="bordergray flex" style='display:none'>
			   <button type='button' id='' class='mBtn' onclick="fff()">View Candle</button>
			   <button type='button' id='btn1' class='mBtn'>Graph-1</button>
               <button type='button' id='btn2' class='mBtn' >Graph-2</button>
			   
			 <button type='button' id='' class='mBtn' onclick="setClassGraph('Chart1-100')">Chart1-100</button>
			 <button type='button' id='' class='mBtn' onclick="setClassGraph('Chart2-100')">Chart2-100</button>
			 <button type='button' id='' class='mBtn' onclick="setClassGraph('Chart1-50')">Chart1-50</button>
			   
		    </div>
         </div>
		   Lost Close Price  : <input type="text" id="lastClosePrice">&nbsp;&nbsp;&nbsp;
		   Price Line : <input type="text" id="chart1PriceLine">&nbsp;&nbsp;&nbsp;

		   <button type='button' id='drawLineBtn' class='mBtn'>Draw Line</button>
		   <button type='button' id='FindGrandTotalBtn' class='mBtn'>Find-Grand-total</button>
		   Grand Total :: <input type="text" id="closedbalance" style='width:70px' value=0>
           Balance :: <input type="text" id="balance" style='width:70px' value=0>
		   Timeserver :: <input type="text" id="timeserver" style='width:70px'>
		   <span id='showTime' style='color:red;font-weight:bold'></span>
		   <span id='showSecond' style='color:red;font-weight:bold'></span>
		   <span id='actionSpan' style='color:red;font-weight:bold'></span>
		   
		   <span id='showTradeList' style='border:1px solid gray;color:red;font-weight:bold;width:auto;min-width:100px'>
		   </span>



		 <div id="" class="bordergray flex row" style='sbackground:magenta'>
		   <div id="" class="row" style='width:100%;padding-bottom:15px'>
		      <div class='col-md-2'>   		   
		       Main TradeNo :: <input type="number" class='form-control' id="maintradeno" >
			  </div>
		      <div class='col-md-2'>   		   
		       Loss Con:: <input type="number" class='form-control' id="lossCon" value=0>
			  </div>
			  <div class='col-md-2'>   		   
		       Win Con:: <input type="number" class='form-control' id="winCon" value=0>
			  </div>
			  <div class='col-md-2'>   		   
			   This Money :: <input type="number" class='form-control' id="thisMoneyTrade">
              </div>
			  <div class='col-md-2'>   		   
			   Use Martingale :: <br><input type="checkbox" id="useMartingale" >
              </div>
			  <div class='col-md-2'>   		   
			    Max Loss Con::<input type="text" id="maxlossCon" class='form-control' value=0>
              </div> 
			  <input type="hidden" id="contractClosedList" style='width:300px'>
			  <input type="hidden" id="gailMoney" style='width:300px'
			  value='1,2,6,18,54,162'
			  >

			  

           </div>			


		    <table id="trades-table" class='sarabun' style='width:100%'>
				<thead>
					<tr class='sarabun'>
					    <th>เทรดครั้งที่</th>
						<th>Contract ID</th>
						<th class='sarabun'>ประเภท</th>
						<th>สินทรัพย์</th>
						<th>เงินลงเทรด</th>
						<th>ราคาซื้อ</th>
						<th>ราคาปัจจุบัน</th>
                        <th>Price Diff</th>
						<th>กำไร/ขาดทุน ($)</th>
						<th>เวลาเริ่ม</th>
						<th>หมดเวลา</th>
						<th>เหลือเวลา</th>
						<th>การดำเนินการ</th>
						<th>Closed ??</th>
					</tr>
				</thead>
				<tbody id="trades-body"></tbody>
 </table>
 <div style='display:flex;'>
   RSI::<input type="text" id="rsi" value=0 style='width:120px;'>
   <button type='button' id='btnCallTrade' class='mBtn green'>CALL</button>
   <button type='button' id='btnPutTrade' class='mBtn pink' >PUT</button>
</div>
		    <div id="chart1Container" class="bordergray flex width50" style='background:yellow'>
		         Graph TF 1M


		    </div>
			<div id="chart2Container" class="bordergray flex width50" style='background:magenta;justify-content: space-between;'>
			     <div>Graph TF 2 Sec</div>

				 <div style='color:white;width:280px;display:flex'>
				    <span>Action ::</span>
				    <div id="showAction2" ></div>
				 </div>

				 <div style='color:white;display:flex'>
				   <span>Count Down Sec ::</span>
				   <div id='showTime2'></div>				 
			     </div>
		      
		 </div>

</div><!-- end container -->
<input type="text" id="assetSelectedList" style='width:100%'>

<div id="" class="bordergray sideBar flexColumn" >
     
	 <button type="button" class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#exampleModal" >Disp</button>

	 <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal2">Curpair</button>
	 <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal3">Plan</button>

	 <button type="button" class="btn btn-primary" data-bs-toggle="Gl" data-bs-target="" style='text-align:center;background:green' onclick="setClassGraph('Chart1-100')">G1</button>
	 <button type="button" class="btn btn-primary" data-bs-toggle="G2" style='text-align:center;background:red' onclick="setClassGraph('Chart2-100')" data-bs-target="">G2</button>
	 <button type="button" class="btn btn-primary" data-bs-toggle="Gl2" onclick="setClassGraph('Chart1-50')" data-bs-target="">G1+G2</button>


</div>
<div id="result" class="bordergray flex">
     Result
</div>
<?php 
  ModalForm1();
  ModalForm2();
  ModalForm3()
?>

<?php
  //RetrieveAssetGroup();
?>

 <div id="tmpBox" class="bordergray flex">
 Asset ID <input type="text" id="realSelectedAssetID"> 
         Asset Select <input type="text" id="realSelectedAsset">
		 Asset Name <input type="text" id="realAssetName">
    <button type='button' id='btnSubscript' class='mBtn green'>Subscript</button>
    <button type='button' id='btnAuthen' class='mBtn green'>AUTHEN</button>
	<button type='button' id='' class='mBtn' >Close Order</button>
				<button type='button' id='' class='mBtn' onclick="SaveInitTrade()">SaveLocal</button>
      
 </div>
<?php   DeclareJS();   ?>
  

<input type="text" id="chart1Data">
<input type="text" id="chart2Data">


<?php
  
function RetrieveAssetGroup() { 

	$newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/';
	require_once($newUtilPath ."iqlab/newutil2.php"); 
	$pdo = getPDONew();
	$newUtilPath = '/home/thepaper/domains/thepapers.in/private_html/';
	require_once($newUtilPath ."iqlab/newutil2.php"); 
	
	 
	$sql = 'select * from symbol_group'; 
	$params = array();
	$rs= pdogetMultiValue2($sql,$params,$pdo) ;
	 
	$results = []; 
	$GroupList  = array() ;
	while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
        $GroupList[]  =   $row['symbol_type'] ;
	    $dataObj = new stdClass();
	    foreach ($row as $key => $value) {
	        $dataObj->$key = $value;
	    }
	    $results[] = $dataObj;			    
	}
    
	// แสดงผลข้อมูลในรูปแบบ JSON
	$symBolGroup=  json_encode($results, JSON_UNESCAPED_UNICODE);
	?>
	 <input type="text" id="symbolGroup" value='<?=$symBolGroup?>'>
	
	<?php
	$sql = "SELECT a.id as symbol_id,b.id as groupid,symbol,display_name,a.symbol_type   FROM `trading_symbols` a INNER join symbol_group b 
     on a.symbol_type=b.symbol_type"; 

    $rs= pdogetMultiValue2($sql,$params,$pdo) ;
	 
	$results = [];
	
	while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		
	    $dataObj = new stdClass();
	    foreach ($row as $key => $value) {
	        $dataObj->$key = $value;
	    }
	    $results[] = $dataObj;
			    
	}
	// แสดงผลข้อมูลในรูปแบบ JSON
	$symBolAsset =  json_encode($results, JSON_UNESCAPED_UNICODE);
	?>

  	<input type="text" id="symbolAsset" value='<?=$symBolAsset?>'>

	<?php
	

	return $GroupList;


} // end function

function ModalForm1() {  ?>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">ตั้งค่าการแสดงผล</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email address</label>
            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
          </div>
          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" class="form-control" id="exampleInputPassword1">
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="exampleCheck1">
            <label class="form-check-label" for="exampleCheck1">Check me out</label>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


<?php
} // end function

function ModalForm2() { 
	
	$stGroupList = RetrieveAssetGroup() ;
	?>

<!-- Modal -->
<div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">ตั้งค่า ตัวแปร การ Trade</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Asset Group</label>
            <?php $groupListAr = $stGroupList; ?>
			<select id="groupList" class='form-control' onchange=createAssetButton(this.value)>
			<?php
			  for ($i=0;$i<=count($groupListAr)-1;$i++) { ?>
			    <option value="<?=$groupListAr[$i]?>"> <?=$groupListAr[$i]?>
			 <?php  }

			?>
			
				
			</select>
			<button type='button' id='btnGetAsset' class='mBtn' 
			onclick="createAssetButton(document.getElementById('groupList').value)">
			get asset</button>

			<button type='button' id='btnClear' class='mBtn' 
			onclick="ClearAllSelectAsset()">
			Clear All Select</button>

            
          </div>
		   

          <?php Accordian(); ?>


          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label" style='color:red;font-weight:bold'>แจ้ง Message</label>
			<div id="messageResult" class="bordergray flex">
			     
			</div>
            
          </div>
           
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick='SaveInitTrade()'>Save changes</button>
      </div>
    </div>
  </div>
</div>


<?php
} // end function

function ModalForm3() { 
	
	
	?>

<!-- Modal -->
<div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">เลือก แผน การ Trade</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Plan Trade</label>
			

			<div class="hidden-toggles">
				
				<input name="coloration-level" type="radio" id="coloration-plan1" class="hidden-toggles__input" value=1 onclick='SetPlanTrade(1)'>
				<label for="coloration-plan1" class="hidden-toggles__label">Plan-A</label>
				
				<input name="coloration-level" type="radio" id="coloration-plan2" class="hidden-toggles__input" checked value=2 onclick='SetPlanTrade(2)'>
				<label for="coloration-plan2" class="hidden-toggles__label">Plan-B</label>	
				
				<input name="coloration-level" type="radio" id="coloration-plan3" class="hidden-toggles__input" value=3 onclick='SetPlanTrade(3)'>
				<label for="coloration-plan3" class="hidden-toggles__label">Plan-C</label>
				
				<input name="coloration-level" type="radio" id="coloration-striking" class="hidden-toggles__input" onclick='SetPlanTrade(4)'>
				<label for="coloration-striking" class="hidden-toggles__label">Plan-D</label>
				
			</div>
            
			
			

            <input type="hidden" id="planno" value=0>
          </div>
		   

         


          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label" style='color:red;font-weight:bold'>แจ้ง Message</label>

			<div id="messageResultPlan" class="bordergray flex">
			     
			</div>
            
          </div>
           
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick='SaveInitTrade()'>Save changes</button>
      </div>
    </div>
  </div>
</div>
<script>
function SetPlanTrade(planno) {
let msg = '';
	     
		 document.getElementById("planno").value = planno;
		 if (planno==1) {
			 msg  ='เทรดแบบ Manual'
		 }
		 if (planno==2) {
			 msg  ='เทรดแบบ Auto โดยตั้ง timeframe candle ที่ 1m,timeframe trade ที่ 3-5 Minute ตั้ง check Profit,StopLoss';
		 }
		 if (planno==3) {
			 msg  ='เทรดแบบ Auto โดยตั้ง timeframe candle ที่ 1m,timeframe trade ที่ 3-5 Minute <span style="color:red">ไม่ตั้ง check Profit,StopLoss </span>';
		 }
		 document.getElementById("messageResultPlan").innerHTML = msg;
		 




} // end func
</script>


<?php
} // end function



function Accordian() {  ?>


<div class="accordion" id="accordionExample">
    <!-- Accordion Item 1 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingOne">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          เลือก รายการ Asset
        </button>
      </h2>
      <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
        <div id= 'collapseOneInner' class="accordion-body" style=''>
           <span style='margin-left:35px;color:red;font-weight:bold'>คลิกเลือก Asset Group ข้างบน</span>
        </div>
      </div>
    </div>

    <!-- Accordion Item 2 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingTwo">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          กำหนด  TimeDuration & Contract Trade
        </button>
      </h2>
      <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
        <div class="accordion-body row">
          
		  <div class="col-md-4 mb-3">
            <label for="timeduration" class="form-label">Time Duration</label>
            <input type="number" class="form-control" id="timeduration">
          </div>
		  <div class="col-md-4 mb-3">
            <label for="timeduration" class="form-label">หน่วย</label>
            <select class='form-select' id="timedurationunit">
				<option value="minute" selected>Minute
				<option value="hour">Hour
            </select>
          </div>
		  <div class="col-md-4 mb-3">
            <label for="timeduration" class="form-label">Contract Type</label>
            <select class='form-select' id="contracttype">
				<option value="risefall" selected>Call-Put
				<option value="multiply">Multiply
            </select>
          </div>

		  <div class="col-md-4 mb-3">
             <label for="timeduration" class="form-label">ใช้  Auto Trade ???
			 <input type="checkbox" id="autotrade">
			 </label>
             
          </div>

        </div>
      </div>
    </div>

    <!-- Accordion Item 3 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingThree">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          กำหนด Money
        </button>
      </h2>
      <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
        <div class="accordion-body row">
		 <div class="col-md-4 mb-3">
            <label for="timeduration" class="form-label">จำนวนเงิน(USD)</label>
            <input type="number" class="form-control" id="moneytrade">
          </div>
		  <div class="col-md-4 mb-3">
            <label for="timeduration" class="form-label">StopLoss(%)</label>
            <input type="number" class="form-control" id="moneystoplossPercent">
          </div>
		  <div class="col-md-4 mb-3">
            <label for="timeduration" class="form-label">Take Profit(%)</label>
            <input type="number" class="form-control" id="moneyprofitPercent">
          </div>
          
        </div>
      </div>
    </div>
  </div>


<?php 
} // end function

function DeclareJS() {  ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>   
  

   <!-- Required JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/th.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

	
	<!-- แบบถูกต้อง -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

	<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

	<script type='module' src="https://thepapers.in/deriv/newDerivObject/derivJson.js"></script>

    <script type='module' src="https://thepapers.in/deriv/newDerivObject/main.js"></script>


    <script src="https://thepapers.in/deriv/newDerivObject/main2.js"></script>
    
		
    

<?php
} // end function


?>
  
<!-- 
main.js
  mainDeriv.js,Chart.js,request.js,Deriv_Response.js
main2.js


-->

 </body>
</html>
