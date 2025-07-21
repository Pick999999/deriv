<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">

  <style>
   body {
    margin: 0;
    padding-bottom: 40px; /* เว้นที่ว่างด้านล่างสำหรับ status bar */
    font-family: sans-serif;
}

.content {
    padding: 20px;
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
$(document).ready(function () {
  console.log("Hello World!");
  CreateStatusBar();
  initChart();
});

</script>


 

  <title>Document</title>
 </head>
 <body>
  Asset:: <input type="text" id="asset" value='R_100'>
  <button type='button' id='' class='mBtn' onclick="connect()">Connect</button>
  <button type='button' id='' class='mBtn' onclick="DisConnect()">DisConnect</button>
  
  <button type='button' id='' class='mBtn' onclick="initChart()">Init Chart</button>

  <button type='button' id='' class='mBtn' onclick="fetchCandles()">getCandle History</button>
  <button type='button' id='' class='mBtn' onclick="fetchCandles2()">getCandleOHLC </button>

  <span id="serverTime" class="bordergray flex">00:00:00 </span>
     

  <div class="content">
        <h1>เนื้อหาของเว็บไซต์</h1>
        <p>นี่คือส่วนเนื้อหาของเว็บไซต์ของคุณ คุณสามารถใส่ข้อความ รูปภาพ หรือองค์ประกอบอื่นๆ ได้ที่นี่</p>
        <p>ลองเลื่อนหน้าจอลงมา คุณจะเห็น status bar ที่ด้านล่างเสมอ</p>
        
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