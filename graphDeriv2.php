<!-- 
  ดึงข้อมูล จาก deriv.com แล้วหาค่า ema3,ema5 

-->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candlestick Data Fetcher</title>

	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>

	<script src="timeUtil.js"></script>
	
	<link href="css/graphDeriv2.css" rel="stylesheet">

</head>
<body>
    <div class="container">
        <h1>Deriv Candlestick Data Fetcher</h1>
        
        <div class="time-display">
            Server Time: <span id="serverTime">Loading...</span>
        </div>
		<small>Don't have a token? <a href="https://app.deriv.com/account/api-token" target="_blank">Create one here</a> (require Read permission).</small>
		<button type='button' id='' class='mBtn bgBlue' onclick="fetchCandles()">Fetch Candle</button>

		<button type='button' id='btnCall' class='mBtn bgGreen' 
		onclick='placeTrade("CALL");'>
		Place Trade CALL </button>

		<button type='button' id='btnPut' class='mBtn bgRed' 
		onclick='placeTrade("PUT");'>
		Place Trade PUT </button>

		Pocket Money:: <input type="text" id="pocketMoney" value=10>
		 <br><input type="checkbox" id="isBreakFetchCandle" >&nbsp;&nbsp;&nbsp;<span style='color:#0080ff'>Break Fetch Candle </span>  &nbsp;&nbsp;
		
		<input type="checkbox" id="isBreakTrade" onclick='showBreakTrade()'>
         &nbsp;&nbsp;&nbsp;&nbsp;
		 <span style='color:#ff0080'>
		 Break Trade  &nbsp;&nbsp;</span>

		 <input type="checkbox" id="isCheckedStopLoss"  sonclick='showBreakTrade()'>
         &nbsp;&nbsp;&nbsp;&nbsp;
		 <span style='color:#ff0080'>
		 ตั้ง stop loss  &nbsp;&nbsp;</span>
		
		<table>
		<tr>
			<td><input type="radio" id="tradetype">Rise/Fall</td>
			<td><input type="radio" id="tradetype">CALL/PUT</td>
			<td><input type="radio" id="tradetype">Touch</td>
			<td></td>
			<td></td>
		</tr>
		</table>
		

		<?php Tab() ; ?>

        
        <div id="CandleStatus" style='width:300px'></div>
		<div id="" class="bordergray flex">
		     
		
		Signal :: <span id="signalSpan" class="bordergray flex"></span>
		</div>
		     
		
        <div id="status"></div>
        
        <div id="chartContainer" class="chart-container"></div>
		<input type="text" id="TurnCode">
		<input type="text" id="sCode">
		<input type="text" id="tradeNo" value=0>
		<textarea id="AnalyzerCode"></textarea>


<?php $timestamp = time(); ?>        
        
<script src="graphDeriv2.js?ver=<?=$timestamp?>"></script>
<script src="graphDeriv2_Helper.js?ver=<?=$timestamp?>""></script>



<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


<script>

$(document).ready(function () {
  
  setFromLocal();
  $('#tradeTabBtn').trigger('click'); 
  doAjaxSymBols('GetSymBolGroup',symbolType='') 
});




async function doAjaxSymBols(Mode,symbolType) {

     
    let result ;
    let ajaxurl = 'AjaxJson.php';
    let data = { "Mode": Mode ,
    "symbolType" : symbolType
    
    } ;
    data2 = JSON.stringify(data);
	//alert(data2);
	
    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
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
		document.getElementById("groupSymbolContainer").innerHTML = result ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

async function doAjaxNewTrade(candles) {

let endPoint = 'AjaxNewTrade.php' ;
let Mode = 'getsignalWithCutRisk';
let endPoint2 = 'https://thepapers.in/deriv/api/getaction/v3/index.php' ;
Mode = 'getAction';
let useEndPointNo = 2 ;


			let result ;
			let ajaxurl = endPoint2 ;
			let data = { "Mode": Mode ,    
			"candles" : candles
			} ;
			data2 = JSON.stringify(data);	
			//alert(data2);
			try {
				result = await $.ajax({
					url: ajaxurl,
					type: 'POST',
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
				

				lastIndex = candles.length -1 ;
				lastEpoch = candles[0]['epoch'];
				sData = JSON.parse(result);
				console.log('Trade Result',sData);
                document.getElementById("AnalyzerCode").value = JSON.stringify(sData.Analyzer);
				/*
				document.getElementById("signalSpan").innerHTML = sData.Analyzer.overallRisk + '-->'+
					sData.TurnMode999 + '-->' + sData.totalWarning + '->' + sData.CodeWarning;
				document.getElementById("TurnCode").value = sData.TurnMode999;
				*/
                

                isBreakTrade = document.getElementById("isBreakTrade").checked;
				if (useEndPointNo==2 && !isBreakTrade ) {
                   action = sData.thisAction ;  
				   placeTrade(action) ;
				}

				if (useEndPointNo==1 && parseInt(sData.totalWarning) <= 12 && !isBreakTrade) {
                  tradeNo = parseInt(document.getElementById("tradeNo").value) ;
				  document.getElementById("tradeNo").value = tradeNo+1 ;
				  if (sData.TurnMode999 === 'TurnUp') {
					placeTrade('CALL') ;
				  } else {					
				 	placeTrade('PUT') ;
				  }
				} else {
	//				document.getElementById("actionSpan").innerHTML = 'Idle';
				}
				return result;
			} catch (error) {
				console.error(error);
			}
} // end ajax



function savetoLocal() {
localStorage.setItem("curpairSelected",document.getElementById("asset").value );


} // end func

function showBreakTrade() {

	     if (document.getElementById("isBreakTrade").checked ) {
			document.getElementById("isTradeMode").innerHTML = 'Break Trade';			 
	     } else {
            document.getElementById("isTradeMode").innerHTML = 'On Trade';			 
		 }


} // end func

async function doAjaxSaveTradeList() {

    let result ;
	tradeData =  JSON.parse(document.getElementById("tradeResultTxt").value);

    let ajaxurl = 'AjaxNewTrade.php';
    let data = { "Mode": 'saveTradeList' ,   
    "tradeData" : tradeData[0],
    "analyzer": JSON.parse(document.getElementById("AnalyzerCode").value) 
    } ;
    data2 = JSON.stringify(data);
	//alert(data2);
    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
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
		 console.log('Result After Save Trade',result);
		
		tradeNew = [];
		tradeNew.push(tradeData[1]) ;
		document.getElementById("tradeResultTxt").value = JSON.stringify(tradeNew);
		
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

function setShowMartingale() {

         
	     if (document.getElementById("isuseMartinGale").checked === true  ) {			
            document.getElementById("useMartinGaleLabel").innerHTML = 'Use MartinGale Mode';  
	     } else {
            document.getElementById("useMartinGaleLabel").innerHTML = '';     
		 }


} // end func



</script>
<?php
function Tab() {  ?>

<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'London')">Init</button>
  <button class="tablinks" onclick="openCity(event, 'Paris')">Candle</button>
  <button class="tablinks" onclick="openCity(event, 'Tokyo')">LabTrade&Action</button>
  <button class="tablinks" id='tradeTabBtn' onclick="openCity(event, 'Tokyo2')">Trade Result</button>
  <button class="tablinks" onclick="openCity(event, 'Tokyo3')">Tokyo-3</button>
</div>

<div id="London" class="tabcontent">
  <h3>Init</h3>
  <?php InitTab(); ?>  
</div>

<div id="Paris" class="tabcontent">
  <h3>Candle</h3>
  <?php CandleDataTab(); ?>
  
</div>

<div id="Tokyo" class="tabcontent">
  <h3>Chart&Trade</h3>
  <?php ChartWithTrade(); ?>
</div>
<div id="Tokyo2" class="tabcontent">
  <h3>Trade Result :: 
  Work State <span id="workModeDesc" style='color:blue' class="bordergray flex"></span>
  <span id='useMartinGaleLabel' style='color:red'></span></h3>
  
  <?php TradeResultTab(); ?>
</div>
<div id="Tokyo3" class="tabcontent">
  <h3>Tokyo2</h3>
  <p><ol>
   <li>graphDeriv2.js จะทำการ connect deriv อยู่ในนี้เลย รวมทั้ง การขอ request ต่างๆ </li>
   <li>graphDeriv2_Helper.js </li>
   <li>timeUtil.js </li>
   <li> </li>
  </ol></p>
</div>

<textarea id="tradeResultTxt" style='margin:10px;width:100%;height:100px'></textarea>

<!-- 

<div id="CandleTable2" class="tabcontent">
  <h3>CandleTable</h3>
  <p>Paris is the capital of France.</p> 
  <?php CandleDataTab();?>
</div>

<div id="TradeAction" class="tabcontent">
  <h3>TradeAction</h3>
  
  <?php //TradeActionTab();?>
</div>

<div id="TradeResult" class="tabcontent">
  <h3>TradeResult</h3>
  <p>Tokyo is the capital of Japan.</p>
  <?php TradeResultTab();?>
</div>
 -->
<script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>
<?php
} // end function
  

function InitTab() { 
	
	  if (isset($_GET["assetcode"])) {
          $assetTmp = $_GET["assetcode"];
	  } else {
          $assetTmp = '';
	  }
	
	?>

        

		เลือก Group
		<div id="groupSymbolContainer" class="bordergray flex">
		     
		</div>

		<form id="candlestickForm">
		    <div style='display:none'>
		    assetTmp :: <input type="hidden" id="assetTmp" value = '<?=$assetTmp;?>'>
			</div>
            <div class="form-group" style='margin:10px'>			  
                <label for="asset">เลือก Asset:</label>
                <select id="asset" id="asset" required onchange = 'setAssetCode(this.value)'style='width:200px;height:40px;border-radius:8px'>
                    <option value="R_10">Volatility 10 Index</option>
                    <option value="R_25">Volatility 25 Index</option>
                    <option value="R_50">Volatility 50 Index</option>
                    <option value="R_75">Volatility 75 Index</option>
                    <option value="R_100" selected >Volatility 100 Index</option>
                    <option value="BOOM1000">Boom 1000 Index</option>
                    <option value="CRASH1000">Crash 1000 Index</option>
                </select>

				<button type='button' id='' class='mBtn' onclick="saveLocal()">Save To Local</button>
				&nbsp;&nbsp;&nbsp;<input type="checkbox" id="isuseMartinGale" 
				onclick = 'setShowMartingale()'
				checked>&nbsp;&nbsp;Use Martingale
            </div>
			Num Warn :: <input type="text" id="numWarn" value=2>
			Target LotNo :: <input type="text" id="targetLotNo" value=3>
			
            <div class="form-group">
                <label>เลือก Timeframe:</label>
                <div class="radio-group" style='background:red;height:60px;line-height: 60px'>
                    <label><input type="radio" name="timeframe" value="1" checked> 1 นาที</label>
                    <label><input type="radio" name="timeframe" value="5"> 5 นาที</label>
                    <label><input type="radio" name="timeframe" value="10"> 10 นาที</label>
                    <label><input type="radio" name="timeframe" value="15"> 15 นาที</label>
                    <label><input type="radio" name="timeframe" value="30"> 30 นาที</label>
					<button type='button' id='' class='mBtn bgBlue' onclick="fetchCandles()">Fetch Candle</button>
                </div>				
            </div>
        </form>
		


<?php

} // end function

function CandleDataTab() {  ?>
    <div class="candle-data">
            <h2 id='headTableCaption'>Latest Candle Data</h2>
            <table id="candleTable">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Open</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Close</th>
                        <th class="ema3">EMA3</th>
                        <th class="ema5">EMA5</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
    </div>
    

<?php

} // end function



function ChartWithTrade() {  ?>


<?php

} // end function

function TradeResultTab() {  ?>

<div id="TradeResultDisplay" class="bordergray flex">
<?php
   if (isset($_GET["assetCode"])) {
	 $assetCode = $_GET["assetCode"] ;
   } else {
     $assetCode = 'R_100' ;
   }
?>

     

Asset Code  :: <input type="text" id="assetCode" value='<?=$assetCode;?>'>
Money Trade :: <input type="text" id="moneyTrade" value=1>
 <select id="moneyTrade2">
	<option value=1 selected>1
	<option value=2>2
	<option value=4>4
	<option value=8>8
	<option value=16>16


 </select>
 Target :: <input type="text" id="TargetMoney" value=2>
 Balance :: <input type="text" id="balanceTxt" value=0>
&nbsp;&nbsp; <span style="color:blue" id='balanceBath'></span>
 <button type='button' id='' class='mBtn' onclick="CalBalance()">Cal Balance</button>
 <br>
 Current LotNo :: <input type="text" id="currentLotNo" value=1>
 Current ContractId :: <input type="text" id="currentContractId">
 <button type='button' id='' class='mBtn bgPink' onclick="SellContract()">ขายสัญญานี้ </button>
 LossCon::<input type="text" id="lossCon" value=0>
 Balance Time ::<input type="text" id="balanceTimeTxt" value=0 style='width:50px'>
 <span id='shouldSell' style='color:red;font-weight:bold'></span>

 <table id='tableTradeResult'>
 <thead>
 <tr>
	<th>ลำดับ</th>
	<th>LotNo</th>
	<th>Contract ID</th>
	<th>sCode</th>
	<th>Action</th>


    <th>เทรด Time </th>
	<th>เทรดเมื่อ </th>
	<th>หมดเวลา</th>
	<th>เหลือเวลา</th>

	<th>จำนวนเงินเทรด</th>
	<th>Profit</th>
	<th>Win Status</th>
 </tr>
 </thead>
 <tbody id="dataBody">
                <!-- Data will be inserted here -->
 </tbody>
 </table>
     
</div>

<?php
 
} // end function





?>

<ol>
 <li>เพิ่ม textbox ค่า  numWarnCheck </li>
 <li>แก้ calBalance </li>
 <li>เพิ่ม asset name ลงใน table </li>
 <li>เพิ่ม กันเทรดซ้ำ </li>
 <li>เพิ่ม การตรวจสอบ winStatus เวลา loss </li>
 <li>เพิ่ม  ทำ martingale+ losscon </li>


</ol>

</body>
</html>