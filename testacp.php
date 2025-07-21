<?php
  ob_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  if (isset($_GET["assetCode"])) {
	 $currentAsset =  $_GET["assetCode"] ;
  } else {
     $currentAsset =  'R_100' ;
  }
  $currentAsset =  '' ;
  $candleTimeframe = 1;


?>
<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Trade V3</title>

  <style>
   

</style>
   
  <link href="testacp.css" rel="stylesheet">
  
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped&family=Playfair+Display:ital@1&family=Sarabun:wght@200&display=swap" rel="stylesheet">
  
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped&family=Noto+Sans+Thai:wght@200&family=Playfair+Display:ital@1&family=Sarabun:wght@200&display=swap" rel="stylesheet">
  
  <style>
   .Noto { font-family: 'Noto Sans Thai', sans-serif; }
   /*  font-family: 'Noto Sans Thai Looped', sans-serif;*/
   /*font-family: 'Playfair Display', serif;*/
   .sarabun { font-family: 'Sarabun', sans-serif; }
  /*
   body,* {
    font-family: 'Kanit', sans-serif;*/
    font-family: 'Sarabun', sans-serif;
    font-family: 'Noto Sans Thai', sans-serif;
   }
   */
  </style>
  
  
  
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  <script src="claude/indy.js?ver=<?=rand(0,10000)?>" ></script>
  <script src="claude/analyzeIndy.js?ver=<?=rand(0,10000)?>" ></script>
  
  <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
  <script src="testacpjs.js?ver=<?=rand(0,10000)?>" ></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  

  <script>
   $(document).ready(function () {
     
	 connect(); 
	 beginOfMinute = true ; // เพื่อเติมค่า emaAbove ลงใน textbox
     showAssetChart();
	 
	 showAssetChartOHLC('')     
	 createTable();
	 //getTradeSectionNo();
	 getAjaxTradeSectionNo();
	 //getLocalData();
	 
	 
   });
    
   
  </script>
  
 </head>
 <body class='sarabun'>

  

<div id="" class="bordergray flex">
     
	<div id="" class="bordergray flex">     
	  <button type='button' id='' class='mBtn' onclick="openDerivPage()">Open DerivPage</button> 
	  <button type='button' id='btnConnect' class='mBtn' onclick="connect()">Connect DERIV</button>
	  <button type='button' id='' class='mBtn' onclick="DisConnect()">DisConnect</button>
	  <button type='button' id='btnInitChart' class='mBtn' onclick="showAssetChart('asset')">Init Chart</button>  
	  <button type='button' id='btnFetchCandle' class='mBtn' onclick="fetchCandles('c')">Request Candle Data</button>
	  <button type='button' id='' class='mBtn' onclick="fetchCandles('o')">Request OHLC</button>
	  
<button type="button" class="mBtn" data-bs-toggle="modal" data-bs-target="#myModal">
  ตั้งค่าต่างๆ 
</button>


	  <button type='button' id='' class='mBtn' onclick="BuyContract('CALL',true)">Buy&CALL</button>
	  <button type='button' id='' class='mBtn' onclick="BuyContract('PUT',true)">Buy&PUT</button>
	  <button type='button' id='' class='mBtn' onclick="drawPriceLine()">Draw Price Line</button>
	  <button type='button' id='' class='mBtn' onclick="doAjaxSaveTrade()">Save Trade</button>
	</div>

	<div id="serverTime" class="bordergray flex">
	</div>
       
  


</div>  


<hr>
ASSET <input type="text" id="asset" value='<?=$currentAsset?>' style='width:70px'
onchange = 'AllFetchCandle(this.value)'
>
Candle TF
<!-- 
<input type="number" id="candleTF" value=<?=$candleTimeframe;?> style='width:70px'>
 -->
<select id="candleTF" class='mBtn' style='text-align:center' onchange='CalDuration(this.value)'> 
	<option value="1" selected>1
	<option value="2">2
	<option value="3">3
	<option value="5">5
	<option value="10">10
	<option value="15">15
	<option value="30">30

</select>

Money Trade <input type="number" id="moneyTrade" value=10 style='width:70px'>
จำนวนวินาทีของ Buy <input type="text" id="numDuration" value=60 style='width:50px;text-align:center'>

จำนวนนาทีของ Buy 
<!-- 
<input type="text" id="numDurationB"  style='width:50px;text-align:center'
value=1 onchange='CalDuration(this.value)'> -->
<select id="numDurationB" class='mBtn' style='text-align:center' onchange='CalDuration(this.value)'> 
	<option value="1" selected>1
	<option value="2">2
	<option value="3">3
	<option value="5">5
	<option value="10">10
	<option value="15">15
	<option value="30">30

</select>
<label class="switch-modern">
     <input type="checkbox" id="autoConnect">
      <span class="slider-modern"></span>
</label>Auto Connect
<button type='button' id='' class='mBtn' onclick="saveLocal()">SaveLocal</button>

<br>
<!-- **************  Row 2 *************** -->
<input type="checkbox" id="autoSale" style='margin-right:5px;margin-left:15px;width:20px' onclick='setAutosale()'>Auto Sale&nbsp;&nbsp; 

<label class="switch-modern">
     <input type="checkbox" id="autoSale" onclick='setAutosale()'>
      <span class="slider-modern"></span>
</label>Auto Sale

Pocket Money  <input type="number" id="pocketMoney" value=10 style='width:120px'>
Total Profit  <input type="number" id="totalProfit" value=0 style='width:120px'>
<input type="checkbox" id="checkTarget" checked onclick='setCheckTarget(this.checked)'>Check Target
Profit Target <input type="number" id="targetProfit" onchange='SetProfitTarget(this.value)' value=0.5 style='width:70px'>
<button type='button' id='' class='mBtn' onclick="CalPocketMoney()">Cal Total Profit</button>
<br>
Trade Transaction No<input type="text" id="tradeTransactionNo">
Current Contract<input type="text" id="currentContractID">
Price Line Value<input type="text" id="priceLineValue">

Profit Line Value<input type="text" id="profitLineValue">
Differ Value<input type="text" id="differValue">
Sale Condition &nbsp;<select id="SaleTypeCondition">
	<option value="1" selected>ขายเมื่อกำไรถึงเป้า
	<option value="2">ขายเมื่อ EMA Above เปลี่ยน
	<option value="3">ขายเมื่อ Turn Type 3 เปลี่ยน
	<option value="4">ขายเมื่อ Turn Type 5 เปลี่ยน

</select>
<br/>
EMA3 TurnType<input type="text" id="ema3turnType" style='width:40px' onchange='alert(this.value);ManageSaleContract()'>

EMA5 TurnType<input type="text" id="ema5turnType" style='width:40px' onchange='alert(this.value);ManageSaleContract()'>

EMA Above<input type="text" id="emaAbove" onchange='alert(this.value);ManageSaleContract()'>

isReversal<input type="text" id="isReversal" onchange='alert(this.value);ManageSaleContract()' style='width:40px'> 

<!-- 
&nbsp;&nbsp;<input type="checkbox" id="playAudio" onclick='setplayAudio()'>&nbsp;&nbsp;Play Audio 

<input type="checkbox" id="isOpenTrade" sonclick='ToggleTrade()' schecked>&nbsp;&nbsp;OpenTrade 
<input type="checkbox" id="isuseMartingale" onclick='setMartingale(this.checked)'>&nbsp;&nbsp;Use Martingale
 
<button type='button' id='' class='mBtn' onclick="SetCheckAll()">Set Check All</button>
-->
<br>
<label class="switch-modern">
     <input type="checkbox" id="playAudio" onclick='setplayAudio()'>
      <span class="slider-modern"></span>
</label>Play Audio

<label class="switch-modern">
     <input type="checkbox" id="isOpenTrade" onclick='setTradeMode(this.checked)'>
      <span class="slider-modern"></span>
</label>Open Trade Mode

<label class="switch-modern">
     <input type="checkbox" id="isuseMartingale" onclick='setMartingale(this.checked)'>
      <span class="slider-modern"></span>
</label>Use MartinGale
<button type='button' id='' class='mBtn' onclick="SetCheckAll()">Set Check All</button>

Can Be Trade <input type="text" id="canBeTradeTxt">
<button type='button' id='' class='mBtn' onclick="doAjaxsetPageStatus()">Get Trade Status</button>


<br>
Response FROM Server <input type="text" class= 'txtSmall' id="reponseFromServer">
Action Code <input type="text" id="actionReason">
Case No <input type="text" id="actionCaseNo" class= 'txtLong'>


<div id="tfDesc" class="bordergray flex">
  <div id="" class="bordergray flex">
    <h4 style='color:#0080ff'>ใช้  TimeFrame ::<span id="candleTimeframe"><?=$candleTimeframe?> </span>นาที    </h4>   
  </div>
  <div id='timeserver4' style='text-align:center;font-size:24px;color:#0080ff;font-weight:bold'></div>

    
	
</div>  
<div id="" class="bordergray flex">     
  
  <div id="chartContainer" class="bordergray flex2">
     
  </div>
  <div id="chartContainer2" class="bordergray flex2">
     ohlc
  </div>

 

</div>
 <div id="" class="bordergray" style='width:100%;min-height:100px;padding:10px;border:1px solid blue'>
     <div id='timeserver3' style='text-align:center;font-size:24px;color:#0080ff;font-weight:bold'></div>
     Trade History
	 Balance= <span id='tradeBalance'></span>
	      
	 
  </div>

Time Server :: <span id= 'timeserver2'></span>
&nbsp;&nbsp;&nbsp;&nbsp;LastStatus:: <span id= 'winstatus' style="margin-right:25px">Wait!!!</span>
LossCon :: <input type="text" id="lossCon" value=0 style='width:60px'>
This MoneyTrade :: <input type="text" id="thisMoneyTrade" value=0 style='width:60px'>

Trade Section No:<input type="text" id="tradesectionNo" value=0>
<div id="tradeTableContainer" class="bordergray flex" style='margin-bottom:50px'>
     
</div>
<?php ModalFormInitTrade() ;?>

<?php
  function ModalFormInitTrade() {  ?>
   
  <!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">ตั้งค่าต่างๆ </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form action="/action_page.php">
  <div class="form-group">
    <label for="email">จำนวนเงินเทรด:</label>
    <input type="number" class="form-control" id="MoneyTrade">
  </div><br>
  <div class="form-group">
    <label for="pwd">Sale เมื่อ:</label>
    <select id="saleCondition" class="form-control">
		<option value="1" selected>พบว่า ema3 turn เปลี่ยน
		<option value="">พบว่า ema5 turn เปลี่ยน
		<option value="">พบว่า ema3 Above เปลี่ยน
    </select>
  </div>
  <!-- 
  <div class="checkbox">
    <label><input type="checkbox"> Remember me</label>
  </div>
   -->
  <button type="submit" class="btn btn-default">Submit</button>
</form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

  <?php
  } // end function

  /*
  ไม่ให้  Sale 
  JD75,stpRNG2
  
  */
  
?>
 </body>


</html>
