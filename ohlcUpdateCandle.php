<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>OHLC Trade</title>

  <style>
   body {
    margin: 0;
    padding-bottom: 40px; /* เว้นที่ว่างด้านล่างสำหรับ status bar */
    font-family: sans-serif;
}
.flex { display:flex;padding:10px ; align-items: center; /* This centers items vertically */ }
.bordergray { border:1px solid gray }

.content {
    padding: 20px;
}
#maincontainer {
    width: 100%;
	max-width:100%;
	background:blue;
}

.CandlechartContainer {
     height:auto;
     min-height: 200px;
     width: 100%;
	 max-width:100%;
	 
	 padding:10px;
	 border:2px solid red;
}

.indicator-chart {
     height: 200px;
     width: 45%;
	 max-width:100%;
	 
	 padding:10px;
	 border:2px solid red;
}

.status-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: #f0f0f0;
    color: #333;
    padding: 10px;
    text-align: center;
    font-size: 0.9em;
    box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1); /* เพิ่มเงาเล็กน้อย */
} 
.tBox { 
  padding:8px;
  border:1px solid lightgray; 
  border-radius:8px;
  margin:8px;
  text-align:center; 
  width:70px;
}
.mBtn { 
		    sdisplay: flex ;
			z-index: 3;
			sposition: relative;
			min-height: 50px;
			background: #fff;
			border: 1px solid #dadce0;
			box-shadow: 0px 3px 10px 0px rgba(31, 31, 31, 0.08);
			border-radius: 26px;
			
			box-sizing: border-box;
			min-width: 150px;
			text-align:center;
			cursor:pointer;
		}
		.mBtn:hover { border:2px solid lightblue;}
		.green { background:#80ff80; }
		.pink { background:#ff0080; }
  </style>
  


  
  

  <?php 
    $timestamp = time(); 
	$domain = 'https://thepapers.in/deriv/';
  ?>        
<script>
let websocket = null;
let selectedTimeframe = 1;
let isProcessing = false;
let isConnecting = false;
let reconnectAttempts = 0;
let timeSubscription = null;
let AllSymBolList = null;
let chart = null;
let candleSeries = null;
let ema3Series = null;
let ema5Series = null;
let tradeHistory = null;
let assetCode = null ;
let warnNumcheck = null ;
let workingCode = null;
let workingDesc = ['Idle','RequestCandle','AjaxGetAction','PlaceTrade','Wait Result'];
let CurrentLotNo = null ;
let totalRowTrade = 0 ;
let LossCon = 0 ;
let MoneyTradeList = [1,2,4,9,20,35,54,54,54,54,54,54,54] ;
let thisMoneyTrade = 1 ;
let balanceTime = 0 ;
</script>

 <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
 <script src="https://thepapers.in/deriv/timeUtil.js"></script>       
 <script src="<?=$domain?>graphTreeview.js?ver=<?=$timestamp?>"></script>
 <script src="<?=$domain?>deriv.js?ver=<?=$timestamp?>"></script>
 <script src="<?=$domain?>derivSender.js?ver=<?=$timestamp?>"></script>
<script src="<?=$domain?>derivReciver.js?ver=<?=$timestamp?>"></script>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


<script>
// graphTreeview.js ,deriv.js,derivSender.js,derivReciver.js
$(document).ready(function () {
  
  CreateStatusBar();
  initChart(); //  By graphTreeView.js
});

function setTimeFrame(nUnit) {

if (nUnit==='m') { 
	//selectedTimeframe = 1 ;
	document.getElementById("unitTimeFrame").innerHTML = 'Minute';
}
if (nUnit==='h') { 
	//selectedTimeframe = 1  ;
	document.getElementById("unitTimeFrame").innerHTML = 'Hour';
	
}

document.getElementById("numUnit").value = selectedTimeframe ;



} // end func 

function drawProfitLine() {
            // Remove previous line if exists
             

            profitLineValue  = parseFloat(document.getElementById("profitLine").value)


            // Create new price line
            closePriceLine = candleSeries.createPriceLine({
                price: profitLineValue,
                color: '#2196F3',
                lineWidth: 2,
                lineStyle: LightweightCharts.LineStyle.Solid,
                axisLabelVisible: true,
                title: 'Close',
            });
        }

</script>


 

  <title>Document</title>
 </head>
 <body>
  Asset:: <input type="text" class= 'tBox' id="asset" value='R_100'>
  <!-- Connect() จาก deriv.js  -->
  <button type='button' id='' class='mBtn' onclick="connect()">Connect</button>

  <button type='button' id='' class='mBtn' onclick="DisConnect()">DisConnect</button>
  
  <button type='button' id='' class='mBtn' onclick="initChart()">Init Chart</button>

  <button type='button' id='' class='mBtn' onclick="fetchCandles()">getCandle History</button>
  <button type='button' id='' class='mBtn' onclick="fetchCandles2()">getCandleOHLC </button>
  <button type='button' id='' class='mBtn' onclick="drawProfitLine()">Draw Profit Line</button>

  <span id="serverTime" class="bordergray flex">00:00:00 </span>
  <div id="" class="bordergray flex">
    <div id="numUnit" class="bordergray flex" style='height:40px;max-height:40px'>
       ป้อนหน่วย <input type="text" class='tBox' id="numMinute" value=1 >
	   <span id='unitTimeFrame'>Num Unit </span>   
    </div>
    <div id="" class="bordergray flex" style='height:40px;max-height:40px'>
     <input type="radio" name="timeframeRadioSel" id="minute" value=1 onclick='setTimeFrame("m")' checked>Minute
	</div> 
	<div id="" class="bordergray flex" style='height:40px;max-height:40px'>
     <input type="radio" name="timeframeRadioSel" id="Hour" value=60 onclick='setTimeFrame("h")' >Hour
	</div> 

	<div id="" class="bordergray flex" style='height:40px;max-height:40px'>
     <input type="text"  id="profitLine" >Profit Line
	</div> 

	

	 
  </div>
  <div id="maincontainer" class="bordergray flex" style='padding:20px'>
	  <div id="chartContainer" class="CandlechartContainer" style='background:white'>
	   <h4>Candle</h4>    
	  </div>
	  <div id="rsi-container" class="indicator-chart" >
	   <h4>RSI</h4>
		
	  </div>
      <div class="content"></div>
         
        
    
  </div>

     

 </body>
</html>
<?php
/*
1. Connect-->
   SubscribeToTime()-->data.time
   Authen-->response.msg_type === 'authorize'
   getCandle-->data.candles,data.ohlc
   PlaceTrade-->response.buy
   TrackTrade-->data.proposal_open_contract
   Sell-->response.sell
   */