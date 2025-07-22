<?php
/*  

//labCandleDeriv.php
สร้าง html form ด้วย  bootstrap5 cdn มี  dtpicker 2 อัน คือ startDate,endDate สำหรับเลือก วัน และ ช่วงเวลา  และ 
input text box 1 อัน สำหรับป้อนค่า asset  และ  input text box 1 อัน สำหรับป้อนค่า  timeframe ในหน่วย minute 
button 1 อัน  และเมื่อ คลิก button ให้ทำการดึงข้อมูล candle stick จาก deriv.com และนำข้อมูลที่ได้ มาใส่ใน textarea มาวาด กราฟ candle stick และ ema3,ema5
bollinger band  ด้วย  https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js
โดยทำด้วย pure javascript

labCandleDerivV2.php
|
|-- Chart.js
|-- me.doAjaxGetSignal()
|----- deriv/newDerivObject/AjaxGetSignal.php
|--------- deriv/newDerivObject/TradingConditionAnalyzer.php
|--------- deriv/newDerivObject/testMarkRisk.php
|-- deriv/newDerivObject/testMarkRisk.php 


*/

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Chart Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


	<script src="IndyLib.js?ver=<?=time();?>"></script>
	<script src="https://thepapers.in/deriv/devlab/jsAnalyCandle.js?ver=<?=time();?>"></script>

	<script src="https://thepapers.in/deriv/newDerivObject/ChartUtil.js?ver=<?=time();?>"></script>
	
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped&family=Playfair+Display:ital@1&family=Sarabun:wght@200&display=swap" rel="stylesheet">
	
	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped&family=Noto+Sans+Thai:wght@200&family=Playfair+Display:ital@1&family=Sarabun:wght@200&display=swap" rel="stylesheet">
	
	<style>
	 font-family: 'Noto Sans Thai', sans-serif;
	 font-family: 'Noto Sans Thai Looped', sans-serif;
	 font-family: 'Playfair Display', serif;
	 .sarabun { font-family: 'Sarabun', sans-serif; }
	
	th { background:#0080ff ; color:white;padding:8px;height:30px; }
	
	body,* {
	  
	  font-family: 'Sarabun', sans-serif;
	
	}
	#messageBox { padding:8px; border:1px solid gray; color:red;height:150px;overflow:scroll }
	.btnSelected { background:#66ff66 }
	</style>
	 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

	
</head>
<style>
  td { border:1px solid gray; padding:5px } 
  .green { background:#00ff00 } 
  .gray { background:#cecece } 
  .mBtns {
   display: flex ;
    z-index: 3;
    align-items:center; justify-content:center;
    min-height: 44px;
    border: 1px solid transparent;
    background: #fff;
    box-shadow: 0px 2px 8px 0px rgba(60, 64, 67, 0.25);
    border-radius: 24px;
    margin: 0 auto;
    box-sizing: border-box;
	
    min-width: 50px;
	text-align:center;
  }
  .mBtn:hover { background-color:#aaffaa }
  .mt10 { margin-top:10px }
  .wAuto { width:auto;min-width:100px }
  .sflex { display:flex; }

</style>

<body>
    <button type='button' id='' class='mBtn' onclick="TestAjax()">TestAjax</button>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Trading Chart Analysis - Live Deriv.com Data</h4>
						Case Study-->R_50 2025-03-07:07:00--2025-03-07:18:00--
						Loss = 5 ที่เวลา 17:27 timeframe = 1
                    </div>
                    <div class="card-body">
                        <form id="chartForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" class="form-control" id="startDate"
									value='' onchange='SaveLocal()'
									>
									<div id="" class="bordergray sflex">
									     
									
									<button type='button' id='' class='mBtns wAuto mt10' onclick="SubDay()"><== -1 Day</button>
									<button type='button' id='' class='mBtns wAuto mt10' onclick="AddDay()">+1 Day ==></button>
									</div>
									
									<table>
									<tr>
									<td>
									    <button type='button' id='btn57' class='mBtn' onclick="setTimePicker(5,7)">05-07</button></td>
										<td><button type='button' id='btn79' class='mBtn' onclick="setTimePicker(7,9)">07-09</button></td>
										<td><button type='button' id='btn911' class='mBtn' onclick="setTimePicker(9,11)">09-11</button></td>
										<td>
										<button type='button' id='btn1113' class='mBtn' onclick="setTimePicker(11,13)">11-13</button></td>
										<td>
										<button type='button' id='btn1315' class='mBtn' onclick="setTimePicker(13,15)">13-15</button></td>
										<td><button type='button' id='btn1517' class='mBtn' onclick="setTimePicker(15,17)">15-17</button></td>
                                        <td><button type='button' id='btn1719' class='mBtn' onclick="setTimePicker(17,19)">17-19</button></td>
										<td><button type='button' id='btn1921' class='mBtn' onclick="setTimePicker(19,21)">19-21</button></td>
										<td><button type='button' id='btn2123' class='mBtn' onclick="setTimePicker(21,23)">21-23</button></td>

										<td><button type='button' id='btn0423' class='mBtn' onclick="setTimePicker(04,23)">04-23</button></td>

									</tr>
									</table>									
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">End Date</label>
                                    <input type="text" class="form-control" id="endDate"
									value='' onchange='SaveLocal()'
									>
									<button type='button' id='' class='mBtn' onclick="SetDateNow()">Set Date Now</button>
                                </div>
								<div class="col-md-3">
								<label class="form-label">Minute Num</label>
								 <div id="" class="bordergray flex" style='display:flex'>
								      
								 
								 <input type="text" class="form-control" id="minuteADD" value=1 style='width:100px'>
								 <button type='button' id='' class='mBtn' 
								 onclick="SubMinute()">-</button>
								 <button type='button' id='' class='mBtn' 
								 onclick="AddMinute()">+</button>
								 </div>
                                </div>


                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Asset</label>
									<!-- 
                                    <input type="text" class="form-control" id="asset" placeholder="e.g. R_50" value='R_25' onchange='SaveLocal()'>
									 -->
									<select id="asset" onchange='SaveLocal()'>
										<option value="R_25" selected>R-25
										<option value="R_50">R-50
                                        <option value="R_75">R-75
										<option value="R_100">R-100
									</select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Timeframe (minutes)</label>
									<!-- 
                                    <input type="number" class="form-control" id="timeframe" placeholder="Enter timeframe" value=1 onchange='SaveLocal()'> 
									<select id="timeframe" onchange='SaveLocal()' style='width:150px'>
										<option value=1 selected>1
										<option value=3>3
										<option value=5>5
										<option value=10>10
										<option value=15>15
										<option value=30>30
									</select>-->
									<?php
									  $tfList = [1,3,5,10,15,30,60];
									  for ($i=0;$i<=count($tfList)-1;$i++) { ?>
                                         &nbsp;&nbsp;
                                       <input type="radio" name="timeframe"
									   onclick= 'SaveLocal()'
									   id='tfList<?=$tfList[$i]?>' 
									   value= <?=$tfList[$i]?>>&nbsp;
									   <?=$tfList[$i]?>M									    
									 <?php }
									?>
                                </div>
                            </div>
							<button type='button' id='btnConnectStatus' class='gray'
							onclick='reconnect()'
							style='height:30px;width:30px;border-radius:15px'></button>
                            <button type="submit" class="btn btn-primary">Get Data</button>
							<button type="botton" class="btn btn-primary" onclick='AjaxgetSuggestSignal("AjaxgetSuggestSignal2")'>Get Signal</button>

							<button type="botton" class="btn btn-primary" onclick='AjaxScan()'>Scan</button>
							<input type="checkbox" id="lockClearMarkers" schecked>No Clear Markers

							<button type='button' id='' class='mBtn' onclick="clearMarkers()">Clear Markers</button> 

							
							<button type='button' id='' class='mBtn' onclick="MarkCutPoint()">Mark CutPoint</button> 

							<button type='button' id='' class='mBtn' onclick="MarkTurnPoint()">Mark Turn Point</button>

							<button type='button' id='' class='mBtn' onclick="ShowDataFromSelectPoint()">แสดงข้อมูลจากจุดที่คลิกเลือก</button> 
							<select id="markerName">
								<option value="1" selected>Series1
								<option value="2">Series2
								<option value="3">Series3
								<option value="4">ADX Con
							</select>
							<button type='button' id='' class='mBtn' onclick="showmarkerFromList()">Show Marker From List</button>

							<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
    Open modal
  </button>
  <button type='button' id='' class='mBtn' onclick="MarkNoRisk()">Mark No Risk</button>

							<span id='suggestSignal99'></span>
							<div id='suggestSignal2'></div>
							<input type="text" id="timestampSelect">
							<button type='button' id='' class='mBtn' onclick="setStartPoint()">เป็นจุด Start</button>
							<button type='button' id='' class='mBtn' onclick="setStopPoint()">เป็นจุด Stop</button>
							<button type='button' id='' class='mBtn' onclick='AjaxNewCalProfit("2Point")'>คำนวณกำไร 2 จุด</button>
							<input type="text" id="startPoint">ถึง
							<input type="text" id="stopPoint">
							<hr>
							ClsTrade Ver :: <select name="">
								<option value="" selected>1
								<option value="">2
								<option value="">3

							</select>
							<button type='button' id='' class='mBtn' onclick='AjaxNewCalProfit()'>คำนวณกำไร php</button>
							<button type='button' id='' class='mBtn' onclick='MarkTimeLoss()'>Mark Time Loss</button>

                           

							 <input type="text" id="barIndex" placeholder="ระบุ timestamp หรือ index">
                             <button id="scrollButton">เลื่อนไปยังตำแหน่ง</button>



                        </form>
						<input type="text" id="turnList">
						<div id="" class="bordergray flex">
						   <span style='color:red;font-weight:bold'>Code Candle:::</span>emaAbove-emaConflict-macdconv-ema3SlopeDirection-distancefromLastTurn-isTurnPoint-isCutPoint<hr>
						   <div id="candleCode" class="bordergray flex">
						        
						   </div>
						</div>

						<div id="messageBox" class="bordergray flex">
						     
						</div>
						<div id="labResult" class="bordergray flex" style='border:1px solid gray;margin-top:10px;color:#0080ff;padding:8px'>
						     
						</div>
						<div id="actionResult"></div>
                        <div id="chart" class="mt-4" style="height: 400px;"></div>
						<div id="chartDataDetail" class="bordergray flex">
						     
						</div>
						
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div id="actionSpan" class="bordergray flex">
	     
	</div>

	
	<?php Tab();   ?>
	<div class="mt-4" style='sdisplay:none'>
      <textarea id="dataOutput" class="form-control" rows="5" readonly></textarea>
    </div>
	AnalyData::
	<textarea id="AnalyData" class='form-control'></textarea>
	<textarea id="RiskPeriodData" class='form-control'></textarea>
	<textarea id="SignalChecker" class='form-control'></textarea>
	
	<textarea id="tradeTimeLine" class='form-control'></textarea>

	<?php ModalForm1();?>

<?php
 function Tab() {  ?>

  <div class="container mt-5">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Table Analysis</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#tradeloss" type="button" role="tab" aria-controls="profile" aria-selected="false">Trade Loss</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#losslist" type="button" role="tab" aria-controls="contact" aria-selected="false">Loss List </button>
            </li>
			<li class="nav-item" role="presentation">
                <button class="nav-link" id="static-tab" data-bs-toggle="tab" data-bs-target="#static" type="button" role="tab" aria-controls="static" aria-selected="false">ดูสถิติ</button>
            </li>

			<li class="nav-item" role="presentation">
                <button class="nav-link" id="lab-tab" data-bs-toggle="tab" data-bs-target="#labTrade" type="button" role="tab" aria-controls="static" aria-selected="false">Lab Trade</button>
            </li>
			<li class="nav-item" role="presentation">
                <button class="nav-link" id="lab-knowLedge" data-bs-toggle="tab" data-bs-target="#labknowLedge" type="button" role="tab" aria-controls="static" aria-selected="false">Lab Knowledge</button>
            </li>

		</ul>

        <!-- Tab content -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <p id= 'numCheckResult' class="mt-3">This is the home tab content.</p>
				<div id="tableContainer" style='margin:30px;text-align:center'></div>
            </div>
            <div class="tab-pane fade" id="tradeloss" role="tabpanel" aria-labelledby="profile-tab">
                <p id='tradeLossResult' class="mt-3">
					
                </p>
            </div>
            <div class="tab-pane fade" id="losslist" role="tabpanel" aria-labelledby="contact-tab">
                <p id='losslistResult' class="mt-3">This is the contact tab content.</p>
            </div>

			<div class="tab-pane fade" id="static" role="tabpanel" aria-labelledby="static-tab">
			  StartPoint : <input type="text" id="startPointSelected">
			  EndPoint : <input type="text" id="endPointSelected">
			  Total-Point : <input type="text" id="totalPointSelected">
			  <hr>
			  <ol>
			   
			   <li>หาตั้งแต่จุดเริ่ม จนถึง Turnpoint </li>
			   <li>หาตั้งแต่จุดเริ่ม จนถึง cutpoint </li>
			   <li>Mark จุด Turn ต่างๆ <button type='button' id='' class='mBtn' onclick="MarkTurnPoint()">Mark TurnPoint</button>  </li>
			   <li>Mark จุด CutPoint ต่างๆ <button type='button' id='' class='mBtn' onclick="MarkCutPoint()">Mark CutPoint</button>  </li>
			   <li> </li>
			   <li> </li>
			  </ol>
			  <div id="labResult999" class="bordergray flex">

			       
			  </div>


			 
				<input type="text" id= 'allIndyJson'>
            </div>

			<div class="tab-pane fade" id="labTrade" role="tabpanel" aria-labelledby="lab-tab">
			    <div id="labtrade_container" class="bordergray flex">
			         
			    </div>
			    <button type='button' id='' class='mBtn' onclick="AjaxLabTrade()">คำนวณ</button>
                <p id='labTradeResult' class="mt-3">This is the Lab tab content.</p>
            </div>

			<div class="tab-pane fade" id="labknowLedge" role="tabpanel" aria-labelledby="lab-tab">
			    <h2>Lab Knowledge</h2>
			    <div id="labknowLedge_container" class="bordergray flex">
			        <?php  showKnowLedge(); ?>
			    </div>
			    
                
            </div>
        </div>
    </div>
  
  
<?php
 } // end function
  
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- 
	<script src="https://cdn.jsdelivr.net/npm/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
 -->
	<script src="https://thepapers.in/deriv/lightweightChart401.js"></script>
	lightweightChart401.js

	

	<script src="IndyLib.js"></script> 


	<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


    <script>
        // Initialize WebSocket connection
        let ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');

        // Initialize date pickers
        flatpickr("#startDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        flatpickr("#endDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        // Initialize chart
        const chart = LightweightCharts.createChart(document.getElementById('chart'), {
            width: document.getElementById('chart').clientWidth,
            height: 400,
            layout: {
                background: { color: '#ffffff' },
                textColor: '#333',
            },
            grid: {
                vertLines: { color: '#f0f0f0' },
                horzLines: { color: '#f0f0f0' },
            },
            timeScale: {
                timeVisible: true,
                secondsVisible: false,
		 	    tickMarkFormatter: (time) => {
                 let hours = parseInt(time);
                 hours = timestampToHHMM(time);				
                 return `${hours}`;
                }                
             }, 
             crosshair: {
                mode: LightweightCharts.CrosshairMode.Normal,
             },
             // กำหนดค่า tooltip
             localization: {
               locale: 'th-TH',
               priceFormatter: price => price.toFixed(2), // กำหนดทศนิยม 2 ตำแหน่ง
               timeFormatter: time => {
                 return new Date(time * 1000).toLocaleString('th-TH');
               },
             },
			 barSpacing: 20, // ระยะห่างระหว่างแท่ง (pixels)
             rightOffset: 10, // ระยะห่างด้านขวาของกราฟ
             minBarSpacing: 10, // ระยะห่างขั้นต่ำระหว่างแท่ง 
        });



// ดักจับเหตุการณ์คลิกบนกราฟ
/*
chart.subscribeClick((param) => {

	 console.log(param.time)
     //let ss = timestampToHHMM(param.time);
	 //console.log(ss)
	 
     if (param.time) {
        document.getElementById("suggestSignal2").innerHTML = '';
        
        data = JSON.parse(localStorage.getItem('AnalysisData')) ;
		const clickedCandles = data.filter(candle => parseInt(candle.timestamp) >= param.time).slice(0, 5);
        if (clickedCandles) {
			//console.log('Click Candle',clickedCandles);			
			document.getElementById("timestampSelect").value = param.time;
			document.getElementById("lastPointIndy").value = param.time;
			FindIndy();
			let targetElementId = 'tableContainer';
			//AjaxgetSuggestSignal();
			AjaxgetSuggestSignal('AjaxgetSuggestSignal2');
			createTableFromJSON(clickedCandles,targetElementId );
			winTradeList = JSON.parse(localStorage.getItem('winTradeList'));
			for (i=0;i<=winTradeList.length-1 ;i++ ) {
				if (winTradeList[i].id === param.time) {
					numCheck = winTradeList[i].numcheck;
					numCheck += ' ; ' + winTradeList[i].actionList;
					numCheck += '<br>->' + winTradeList[i].actionCodeList;

					break ;
				}
			
			} 
			document.getElementById("numCheckResult").innerHTML = 'Num Trade Until Win ='+ numCheck ;
			

			
			//alert(clickedCandle.timestamp)
            //createTableFromJSON(jsonData, targetElementId)
            
        } else {
			alert('Not Found');
		}
     }
});

*/
        // Add candlestick series
        const candleSeries = chart.addCandlestickSeries();
        
        // Add EMA series
        const ema3Series = chart.addLineSeries({
            color: 'rgba(0,128,128, 1)',            
            lineWidth: 2,
        });

        const ema5Series = chart.addLineSeries({
            color: 'rgba(255, 0, 0, 1)',
            lineWidth: 2,
        });

        // Add Bollinger Bands
        const upperBandSeries = chart.addLineSeries({
            color: 'rgba(255,255,0, 0.5)',              
            lineWidth: 3,
        });

        const lowerBandSeries = chart.addLineSeries({
            color: 'rgba(128, 0, 128, 0.5)',
            lineWidth: 3,
        });


        
         
        // WebSocket message handler
        ws.onmessage = function(msg) {
            const data = JSON.parse(msg.data);
            console.log(data)
            
            if (data.msg_type === "candles") {
                const candleData = data.candles.map(candle => ({
                    time: candle.epoch,					
                    open: parseFloat(candle.open),
                    high: parseFloat(candle.high),
                    low: parseFloat(candle.low),
                    close: parseFloat(candle.close)
                }));
                time1 = candleData[0].time;
				
                //alert(candleData.length);
				


                // Update textarea
                document.getElementById('dataOutput').value = JSON.stringify(candleData, null, 2);
				
				
				//doAjaxGetSignal();				
				doAjaxGetAnalysisData(candleData);
				//AjaxgetCalProfit();
				CallAllIndy();

                //subscribeToChartClicks();
                // Update chart
                candleSeries.setData(candleData);
				// เรียกใช้ฟังก์ชันเพื่อเริ่มตรวจจับการคลิก
                

                // Calculate and set EMA
                const ema3Data = calculateEMA(candleData, 3);
                const ema5Data = calculateEMA(candleData, 5);

				let AllIndy = JSON.parse(document.getElementById("allIndyJson").value) ;
                adxData = AllIndy.adx ;

                slopeData = AllIndy.slopes ;
				//console.log('SLOPE DATA',slopeData)
				
				TurnList = AllIndy.TurnList;				
                ema3Series.setData(ema3Data);
                ema5Series.setData(ema5Data);

				chart.subscribeCrosshairMove(param => {
					
					if (param.time && param.point) {
						const price = param.seriesData.get(candleSeries);
						// console.log('Price',price)						
						const ema3Value = ema3Lookup[param.time];
						const ema5Value = ema5Lookup[param.time];

						const adxValue = adxLookup[param.time];
						const slope= slopeLookup[param.time];

						if (price) {
							// สร้าง tooltip แบบกำหนดเอง
							const tooltipEl = document.createElement('div');
							tooltipEl.style.position = 'absolute';
							tooltipEl.style.left = `${param.point.x + 15}px`;
							tooltipEl.style.top = `${param.point.y + 15}px`;
							tooltipEl.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
							tooltipEl.style.padding = '8px';
							tooltipEl.style.borderRadius = '4px';
							tooltipEl.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.2)';
							tooltipEl.style.fontSize = '12px';
							tooltipEl.style.zIndex = '1000';
							tooltipEl.style.pointerEvents = 'none';
							if (ema3Value.toFixed(2) > ema5Value.toFixed(2)) {
								emaAbove='3';
							} 
							if (ema3Value.toFixed(2) < ema5Value.toFixed(2)) {
								emaAbove='5';
							} 
							MACD = ema3Value.toFixed(4) - ema5Value.toFixed(4);
							
							tooltipEl.innerHTML = `
								<div style="font-weight: bold; margin-bottom: 4px;">Date: ${param.time}</div>
								<div>UWick: ${price.high.toFixed(2)-price.close.toFixed(2)}</div>
								<div>LWick: ${price.close.toFixed(2)-price.low.toFixed(2)}</div>

								<div>PIP: ${price.open.toFixed(2)-price.close.toFixed(2)}</div>
                                
								<div>MACD HEIGHT: ${MACD}</div>
								<div>Slope: ${slope}</div>
								<div>adxValue: ${adxValue}</div>
								<div style="margin-top: 4px; color: #2962FF; font-weight: bold;">EMA Above: ${emaAbove}</div>
                               

							`; 
 							 tooltipEl.innerHTML = getInner(param.time);

							 
							
							// ลบ tooltip เก่า (ถ้ามี)
							const oldTooltip = document.querySelector('.custom-tooltip');
							if (oldTooltip) {
								oldTooltip.remove();
							} 
							document.getElementById("chartDataDetail").innerHTML = tooltipEl.innerHTML ;
							
							
							
							tooltipEl.className = 'custom-tooltip';
							document.body.appendChild(tooltipEl);
						}
					} else {
						// ลบ tooltip เมื่อเมาส์ออกจากพื้นที่กราฟ
						const oldTooltip = document.querySelector('.custom-tooltip');
						if (oldTooltip) {
							oldTooltip.remove();
						}
					}
				});

				const ema3Lookup = {};
                ema3Data.forEach(item => {
                   ema3Lookup[item.time] = item.value;
                });

				const ema5Lookup = {};
                ema5Data.forEach(item => {
                   ema5Lookup[item.time] = item.value;
                });

				const adxLookup = {};
                adxData.forEach(item => {
                   adxLookup[item.time] = item.adx;
                });

				const slopeLookup = {};
                slopeData.forEach(item => {
                   slopeLookup[item.time] = item.value*100;
                });

				//AllAnalyData = JSON.parse(document.getElementById("AnalyData").value) ;
				//console.log('Turn List999', TurnList);
				
                
				 
				


				


                // Calculate and set Bollinger Bands
                const bollingerBands = calculateBollingerBands(candleData);
                upperBandSeries.setData(bollingerBands.map(band => ({
                    time: band.time,
                    value: band.upper
                })));
                lowerBandSeries.setData(bollingerBands.map(band => ({
                    time: band.time,
                    value: band.lower
                })));
            }
        };

        // Form submission handler
        document.getElementById('chartForm').addEventListener('submit', async (e) => {
            e.preventDefault();
			//ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');

			/*console.log('sDate-',document.getElementById('startDate').value);
			console.log('eDate-',document.getElementById('endDate').value);
			*/

            const startDate = new Date(document.getElementById('startDate').value).getTime() / 1000;
            const endDate = new Date(document.getElementById('endDate').value).getTime() / 1000;
            const asset = document.getElementById('asset').value;
            //const timeframe = parseInt(document.getElementById('timeframe').value);
			const timeframe = parseInt(getRadioValue());

			console.log('startDate',startDate)
            console.log('endDate',endDate)

            console.log('total',(endDate-startDate)/60)
            document.getElementById("messageBox").innerHTML = '' ;
//			alert(timeframe)

			

            // Request candles data
            const request = {
                ticks_history: asset,
                adjust_start_time: 1,                
                count:1200,
                end: parseInt(endDate),
                start: parseInt(startDate),
                style: "candles",
                granularity: 60*timeframe
            };
             console.log('ส่ง Candle Request ',JSON.stringify(request))
             
            ws.send(JSON.stringify(request));
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            chart.applyOptions({
                width: document.getElementById('chart').clientWidth
            });
        });

        // WebSocket connection handlers
        ws.onopen = function() {
            console.log('Connected to Deriv WebSocket API');
			subscribeToTime();
			$("#btnConnectStatus").removeClass('gray').addClass('green');
        };

        ws.onclose = function() {
            //console.log('Disconnected from Deriv WebSocket API');
			console.log(`WebSocket ถูกปิด: รหัส ${event.code}, เหตุผล: ${event.reason}`);
			playBeep(0.5, 500, 0.5);
			$("#btnConnectStatus").removeClass('green').addClass('gray');
			if (ws && ws.readyState !== WebSocket.CLOSED) {
               ws.close();
            }
			// สร้างการเชื่อมต่อใหม่
			ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726'); 
		    // ตั้งค่า event handlers ใหม่
            ws.onopen = function() {
               console.log("เชื่อมต่อใหม่สำเร็จ");
               isConnected = true;
			   //subscribeToTime();
			   setInterval(() => subscribeToTime(), 3000);

             };
            

        };


        ws.onerror = function(error) {
            console.error('WebSocket Error:', error);
        };

function subscribeToTime() {
      

     
     ws.send(JSON.stringify({
       time: 1     
     }));


} // end func


function playBeep(duration, frequency, volume) {
    // Create an audio context
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();

    // Create an oscillator node
    const oscillator = audioContext.createOscillator();

    // Create a gain node to control the volume
    const gainNode = audioContext.createGain();

    // Connect the oscillator to the gain node and the gain node to the destination (speakers)
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);

    // Set the oscillator frequency (in Hz)
    oscillator.frequency.value = frequency;

    // Set the gain (volume) value
    gainNode.gain.value = volume;

    // Start the oscillator
    oscillator.start();

    // Stop the oscillator after the specified duration
    oscillator.stop(audioContext.currentTime + duration);
}


function reconnect() {

  console.log("กำลังพยายามเชื่อมต่อใหม่...");  
  // ปิดการเชื่อมต่อเดิมถ้ายังไม่ถูกปิด
  if (ws && ws.readyState !== WebSocket.CLOSED) {
    ws.close();
  }  
  // สร้างการเชื่อมต่อใหม่
  ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
  
  // ตั้งค่า event handlers ใหม่
  ws.onopen = function() {
    console.log("เชื่อมต่อใหม่สำเร็จ");
    isConnected = true;    
  };
		
		
} // end func

/*
document.getElementById('searchButton').addEventListener('click', function() {
            const timestamp = document.getElementById('timeInput').value.trim();
            scrollToTime(timestamp);
});
*/		

    </script>

<script>

// Function to create a table from JSON data
function createTableFromJSON(jsonData, targetElementId) {

	//console.log('Json Data',jsonData);
    // Parse JSON if it's a string
    const data = typeof jsonData === 'string' ? JSON.parse(jsonData) : jsonData;
    
      
    // Get target element
    const container = document.getElementById(targetElementId);
    if (!container) {
        throw new Error(`Element with id '${targetElementId}' not found`);
    }
    
    // Create table element
    const table = document.createElement('table');
    table.style.borderCollapse = 'collapse';
    //table.style.width = '100%';
    
    // Create table header
    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
	let sName = ['timestamp','timefrom_unix','pip','thisColor','ema3Position','ema5Position','MACDHeight','MACDConvergence','CutPointType','emaAbove','PreviousTurnType','PreviousTurnTypeBack2',
	'PreviousTurnTypeBack3','PreviousTurnTypeBack4'];
	//sName = 'all';
    
    // Get all unique columns from the data
    const columns = new Set();
    data.forEach(item => {
        Object.keys(item).forEach(key => columns.add(key));
    });
    
    // Add header cells
    sName.forEach(column => {
		//console.log(column)
		if (sName === 'all') {				 
		 foundElement = true;
        } else {
		 foundElement = sName.includes(column);
		}
		if (foundElement)		{		
			const th = document.createElement('th');
			th.textContent = column;
			th.style.border = '1px solid #ddd';
			th.style.padding = '8px';
			th.style.backgroundColor = '#f2f2f2';
			headerRow.appendChild(th);
		}
    });
    
    thead.appendChild(headerRow);
    table.appendChild(thead);
    
    // Create table body
    const tbody = document.createElement('tbody');
    
    // Add data rows
    data.forEach((item, index) => {
        const row = document.createElement('tr');
        row.style.backgroundColor = index % 2 === 0 ? '#ffffff' : '#f9f9f9';
        sName.forEach(column => {
			let foundElement = sName.includes(column);
			if (sName === 'all') {				 
		      foundElement = true;
            } else {
		      foundElement = sName.includes(column);
		    }
			if (foundElement) {
					
				const cell = document.createElement('td');
				cell.textContent = item[column] ?? '';
				cell.style.border = '1px solid #ddd';
				cell.style.padding = '8px';
				cell.style.width=  '100px';
				row.appendChild(cell);
			}
        });
        
        tbody.appendChild(row);
    });
    
    table.appendChild(tbody);
    
    // Clear previous content and add the table
    container.innerHTML = '';
    container.appendChild(table);
    
    return table;
} // end func

async function doAjaxGetSignal() {

    let textArea = document.getElementById("dataOutput");
    let aa = JSON.parse(textArea.value) ;
	aa.length =  aa.length - 1;
	let thisData = JSON.stringify(aa);
    

    let result ;
	
    let ajaxurl = 'https://thepapers.in/deriv/newDerivObject/AjaxGetSignal.php';
    let data = { "Mode": 'getAnalysisData' ,    
    "candleData" : thisData
    } ;
    let data2 = JSON.stringify(data);
	
    let lastIndex = aa.length-1;	
	let time2 =  timestampToTimeMS(aa[lastIndex].time);
	
    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
	        dataType: "json",
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
        
		console.log('Result Aaa',result)
		
		AnalysisData = JSON.stringify(result.AnalysisData) ;
		localStorage.setItem('AnalysisData',AnalysisData);		
		AnalyData = result.AnalysisData ;
		RiskData =  result.highRiskPeriods ;
/*
	    exTra1 =  result.exTra1 ;
		document.getElementById("messageBox").innerHTML = JSON.stringify(exTra1);;
*/
		document.getElementById("AnalyData").value = JSON.stringify(AnalyData);
		document.getElementById("RiskPeriodData").value = JSON.stringify(RiskData);

		AjaxpostToServer(JSON.stringify(AnalyData));
		createTableFromJSON(AnalyData, 'tableContainer');

         
		allIndy =	JSON.parse(document.getElementById("allIndyJson").value) ;		
		document.getElementById("allIndyJson").value =JSON.stringify(allIndy);
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

function SetDateNow() {

        let endDatePicker = flatpickr("#endDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        const newEndDate = new Date(); 
		endDatePicker.setDate(newEndDate);


} // end func


function AddDay() {

	    let startDatePicker = flatpickr("#startDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        let endDatePicker = flatpickr("#endDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

           
			//console.log(endDatePicker.selectedDates);
	        const currentStartDate = startDatePicker.selectedDates[0];
            const currentEndDate = endDatePicker.selectedDates[0];
			const minuteAdd = 1440 ; //parseInt(document.getElementById("minuteADD").value) ;
            if (currentEndDate) {
			   const newStartDate = new Date(currentStartDate.getTime() + (60000*minuteAdd)); 
               const newEndDate = new Date(currentEndDate.getTime() + (60000*minuteAdd)); // Add 1 minute (60000 milliseconds)
			    startDatePicker.setDate(newStartDate);
                endDatePicker.setDate(newEndDate);
				document.getElementById("dataOutput").value = '';
				
                // Automatically trigger form submission
                document.getElementById('chartForm').dispatchEvent(new Event('submit'));
            }
			SaveLocal();
} // end func
function SubDay() {

	    let startDatePicker = flatpickr("#startDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        let endDatePicker = flatpickr("#endDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

           
			//console.log(endDatePicker.selectedDates);
	        const currentStartDate = startDatePicker.selectedDates[0];
            const currentEndDate = endDatePicker.selectedDates[0];
			const minuteAdd = 1440 ; //parseInt(document.getElementById("minuteADD").value) ;
            if (currentEndDate) {
			   const newStartDate = new Date(currentStartDate.getTime() - (60000*minuteAdd)); 
               const newEndDate = new Date(currentEndDate.getTime() - (60000*minuteAdd)); // Add 1 minute (60000 milliseconds)
			    startDatePicker.setDate(newStartDate);
                endDatePicker.setDate(newEndDate);
				document.getElementById("dataOutput").value = '';
				
                // Automatically trigger form submission
                document.getElementById('chartForm').dispatchEvent(new Event('submit'));
            }
			SaveLocal();
} // end func

function AddMinute() {

        let endDatePicker = flatpickr("#endDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        flatpickr("#startDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

           
			console.log(endDatePicker.selectedDates);
	     
            const currentEndDate = endDatePicker.selectedDates[0];
			const minuteAdd = parseInt(document.getElementById("minuteADD").value) ;
            if (currentEndDate) {
               const newEndDate = new Date(currentEndDate.getTime() + (60000*minuteAdd)); // Add 1 minute (60000 milliseconds)
                endDatePicker.setDate(newEndDate);
                // Automatically trigger form submission
                document.getElementById('chartForm').dispatchEvent(new Event('submit'));
            }
} // end func

function SubMinute() {

        let endDatePicker = flatpickr("#endDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        flatpickr("#startDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

           
			console.log(endDatePicker.selectedDates);
	     
            const currentEndDate = endDatePicker.selectedDates[0];
			const minuteAdd = parseInt(document.getElementById("minuteADD").value) ;
            if (currentEndDate) {
               const newEndDate = new Date(currentEndDate.getTime() - (60000*minuteAdd)); // Add 1 minute (60000 milliseconds)
                endDatePicker.setDate(newEndDate);
                // Automatically trigger form submission
                document.getElementById('chartForm').dispatchEvent(new Event('submit'));
            }
} // end func

// Function to convert timestamp to HH:MM:SS format
function timestampToTime(timestamp) {
    // Create a new Date object from timestamp
    const date = new Date(timestamp * 1000); // Multiply by 1000 if timestamp is in seconds
    
    // Get hours, minutes, seconds
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');
    
    // Return formatted string
    return `${hours}:${minutes}:${seconds}`;
}

// Alternative function that handles milliseconds
function timestampToTimeMS(timestamp) {
    // Check if timestamp is in milliseconds (13 digits) or seconds (10 digits)
    const date = new Date(timestamp.toString().length === 10 ? timestamp * 1000 : timestamp);
    
    // Get hours, minutes, seconds
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');
    
    return `${hours}:${minutes}:${seconds}`;
}

function SaveLocal(thisBtnID) {

timeframe = getRadioValue();

localObj = {

	startDate : document.getElementById("startDate").value ,
    endDate : document.getElementById("endDate").value ,
    asset  : document.getElementById("asset").value ,
    timeframe : timeframe, //document.getElementById("timeframe").value 
	BtnID :thisBtnID

} 

localStorage.setItem('labCandleDeiv',JSON.stringify(localObj));
document.getElementById("minuteADD").value = timeframe ;

} // end func

$(document).ready(function () {

   let labCandleDeriv1 = JSON.parse(localStorage.getItem('labCandleDeiv'));

   thisTimeFrame = labCandleDeriv1.timeframe ;   
   setRadioValue(thisTimeFrame);

   thisBtnID = labCandleDeriv1.BtnID ;
   
   
   //if (labCandleDeriv) {
   document.getElementById("startDate").value = labCandleDeriv1.startDate;
   document.getElementById("endDate").value = labCandleDeriv1.endDate;
   document.getElementById("asset").value = labCandleDeriv1.asset;
   $(thisBtnID).addClass('btnSelected') ;

   subscribeToChartClicks();

});
function timestampToHHMM(timestamp) {
    // สร้างออบเจกต์ Date จาก timestamp (ถ้า timestamp เป็นวินาที ให้คูณด้วย 1000 เพื่อแปลงเป็นมิลลิวินาที)
    const date = new Date(timestamp * 1000);

    // ดึงชั่วโมงและนาที
    const hours = date.getHours();
    const minutes = date.getMinutes();

    // เติมศูนย์ข้างหน้าหากชั่วโมงหรือนาทีน้อยกว่า 10
    const formattedHours = hours < 10 ? `0${hours}` : hours;
    const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;

    // รวมเป็นรูปแบบ hh:mm
    return `${formattedHours}:${formattedMinutes}`;
}

window.addEventListener('load', () => {
    chart.timeScale().applyOptions({
        barSpacing: 30,
        rightOffset: 12,
        minBarSpacing: 10
    });
});


async function AjaxgetSuggestSignal(Mode) {

let thistimestamp = parseInt(document.getElementById("timestampSelect").value) ;

//let dataTmp = JSON.parse(localStorage.getItem('AnalysisData'));
let dataTmp =  JSON.parse(document.getElementById('dataOutput').value) ;
 
//'AjaxgetSuggestSignal2'

const Candles = dataTmp.filter(candle => parseInt(candle.time) <= thistimestamp);
    
	
    let result ;
	let ajaxurl = 'https://thepapers.in/deriv/newDerivObject/AjaxGetSignal.php';
	let AnalysisData = localStorage.getItem('AnalysisData');   
    let data = { 
	   "Mode": Mode  ,
       "timestampSelected" : document.getElementById("timestampSelect").value,
       "candleData" : JSON.stringify(Candles),
       "startPoint" : document.getElementById("startPoint").value,
       "stopPoint" : document.getElementById("stopPoint").value
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
        //alert(result.lastTime);
		 result = JSON.parse(result);
		 console.log('rrrrr-',result.lastTime)
		//document.getElementById("suggestSignal").innerHTML = '';
        document.getElementById("suggestSignal2").innerHTML = '' ;
		
		document.getElementById("suggestSignal2").innerHTML = result.lastTime +'->'+ result.thisAction + '->'+ result.actionReason;
		//document.getElementById("mainBoxAsset").innerHTML = result ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

function CalProfit() {
let AnalyData2 = null ;
let thisIndex = null;
let numWin = 0 ;
let numLoss = 0 ;


 function getSuggestColor() {

	      
		  

          thisemaAbove = AnalyData2[thisIndex].emaAbove ; 
		  if (thisemaAbove) {
		  }
		  //console.log(thisemaAbove);
		  if (AnalyData2[thisIndex].emaAbove==='5') {		  
		    return 'Red';
		  } else {
            return 'Green';
		  } 


		  
 } // end func
 function getResultColor() {
 
            
          resultColor = AnalyData2[thisIndex+1].thisColor ; 
		  return resultColor;
 
 } // end func
 

	
	startPoint =  parseInt(document.getElementById("startPoint").value)  ;
	stopPoint =   parseInt(document.getElementById("stopPoint").value)  ;
	if (stopPoint > startPoint) {
		tmp = parseInt(document.getElementById("startPoint").value) ; 
		startPoint = parseInt(document.getElementById("stopPoint").value) ; 
		stopPoint = tmp ; 
	}
    
    let totalCandle = 0 ; profit = 0 ; balance= 0;
	AnalyData = JSON.parse(document.getElementById("AnalyData").value);
	for (let i=0;i<=AnalyData.length-1 ;i++ ) {
		if (parseInt(AnalyData[i].timestamp) >= startPoint && parseInt(AnalyData[i].timestamp) <= stopPoint) {

			totalCandle++ ;
		}
	
	}


    AnalyData2 = AnalyData.filter(
		object => parseInt(object.timestamp) >= startPoint && 
		parseInt(object.timestamp) <= stopPoint);

	 console.log('AnalyData2',AnalyData2)
     //alert(AnalyData2.length);

	

	//alert(AnalyData2.length);
	for (let i=0;i<=AnalyData2.length-2 ;i++ ) {
		 thisIndex= i ;
		 suggestColor = getSuggestColor();
		 resultColor = getResultColor()  ;
		 if (suggestColor== resultColor) {
            numWin++ 
		 } else {
            numLoss++ ;
		 }	

	}

    totalCandle = AnalyData2.length ;
	numRed = 0 ; numGreen = 0 ; numConflict = 0 ;
	numTurnUp = 0 ; numTurnDown = 0 ;

	for (let i=0;i<=AnalyData2.length-2 ;i++ ) {
		 if ( AnalyData2[i].thisColor == "Green") { numGreen++ ; }
		 if ( AnalyData2[i].thisColor == "Red") { numRed++ ; }

		 if ( AnalyData2[i].emaConflict !== "N") { numConflict++ ; }
		 if ( AnalyData2[i].PreviousTurnType !== "TurnUp") { numTurnUp++ ; }
		 if ( AnalyData2[i].PreviousTurnType !== "TurnDown") { numTurnDown++ ; }



	}
	st = 'จำนวนแท่งเทียน =' + totalCandle ;
	st += ' จำนวนแท่งเทียน Green =' + numGreen ;
	st += ' จำนวนแท่งเทียน Red =' + numRed ;
	st += ' จำนวนแท่งเทียน Conflict =' + numConflict ;
	st += ' จำนวน Turn Up =' + numTurnUp ;
	st += ' จำนวน  Turn Down =' + numTurnDown ;

	document.getElementById("messageBox").innerHTML = st;
	





    totalPoint = AnalyData2.length  ;
	
	//AjaxgetCalProfit(totalPoint,numWin,numLoss) ;

	

} // end func


async function AjaxgetCalProfit(totalPoint,numWin,numLoss) {

let thistimestamp = parseInt(document.getElementById("timestampSelect").value) ;

//let dataTmp = JSON.parse(localStorage.getItem('AnalysisData'));
let dataTmp =  JSON.parse(document.getElementById('dataOutput').value) ;
 
//'AjaxgetSuggestSignal2'

//const Candles = dataTmp.filter(candle => parseInt(candle.time) <= thistimestamp);
    
	
    let result ;
	let ajaxurl = 'https://thepapers.in/deriv/newDerivObject/AjaxGetSignal.php';
	//let AnalysisData = localStorage.getItem('AnalysisData');   
	AnalysisData = document.getElementById("AnalyData").value ;

	if (document.getElementById("startPoint").value  != '') {
		startPoint = document.getElementById("startPoint").value ;
	} else {
		startPoint = dataTmp[2];
    }
	if (document.getElementById("stopPoint").value  != '') {
		stopPoint = document.getElementById("stopPoint").value ;
	} else {
		stopPoint = dataTmp[dataTmp.length-1];
    }

    let data = { 
	   "Mode": 'AjaxSaveCalProfit'  ,
       "asset" :  document.getElementById("asset").value ,
       "timestampSelected" : document.getElementById("timestampSelect").value,
       "AnalyData" : AnalysisData,
       "startPoint" : document.getElementById("startPoint").value,
       "stopPoint" : document.getElementById("stopPoint").value,
       "totalPoint" : totalPoint,
       "numWin" : numWin ,
	   "numLoss" : numLoss 

    } ;
    data2 = JSON.stringify(data);	
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
   
		result = JSON.parse(result);
		//console.log('rrrrr-',result.winTradeList);

		//document.getElementById("suggestSignal").innerHTML = '';
        document.getElementById("tradeLossResult").innerHTML = '' ;
		
		
        numcheckArray = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		
		

		for (i=0;i<= result.winTradeList.length-1 ;i++ ) {			            
            numcheckArray[result.winTradeList[i].numcheck]++ ;					
		} 
        stTable = '<table border=1><tr>';  
		for (i=0;i<= numcheckArray.length-1 ;i++ ) {			
			stTable += '<td>' + i + '</td>';
		}
		stTable += '<td>Total Trade</td></tr><tr>';
		let total = 0 ;
		for (i=0;i<= numcheckArray.length-1 ;i++ ) {			
			stTable += '<td>' + numcheckArray[i] + '</td>';
			total += numcheckArray[i]  ;
		}

		stTable += '<td>'+ total + '</td>' +'</tr><tr>';   
		let percent = 0 ;
		for (i=0;i<= numcheckArray.length-1 ;i++ ) {			
			percent = (numcheckArray[i]/total)*100 ;
			stTable += '<td>' + percent.toFixed(2) + '</td>';			
		}
		stTable += '<td>'+ total + '</td>' +'</tr></table>';   




		document.getElementById("tradeLossResult").innerHTML += stTable;

		document.getElementById("tradeLossResult").innerHTML += '<hr>' +JSON.stringify(result.winTradeList);

        let stLossList = '<table style="width:100%;margin-left:100px"><tr><td>ลำดับที่ </td><td>นาที่</td><td>NumTo Win</td></tr>';
		
		for (i=0;i<= result.winTradeList.length-1 ;i++ ) {			            
            if (parseInt(result.winTradeList[i].numcheck) >= 4) {
               stLossList  += '<tr><td>'+ (i+1) + '</td><td>'+result.winTradeList[i].minute + '</td><td>'+ result.winTradeList[i].numcheck  +'</td><td>'+
               result.winTradeList[i].actionCodeList +  '</td></tr>';
				   
            }
		} 
        stLossList += '</tr></table>';
		
		document.getElementById("losslistResult").innerHTML = stLossList;
		localStorage.setItem('winTradeList',	JSON.stringify(result.winTradeList));
        
		//document.getElementById("mainBoxAsset").innerHTML = result ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

function setStartPoint() {

	document.getElementById("startPoint").value = document.getElementById("timestampSelect").value ;


} // end func
function setStopPoint() {

	document.getElementById("stopPoint").value = document.getElementById("timestampSelect").value ;


} // end func

function MaincalculateADX() {
	     
		 let data = JSON.parse(document.getElementById("dataOutput").value) ;
		 let adxPeriod  = parseInt(document.getElementById("adxPeriod").value);
		 adx = calculateADX999(data,adxPeriod);
		 document.getElementById("allIndyJson").value = JSON.stringify(adx);
	     console.log('adx=',adx) ;
		 let lastIndex = adx.length-1 ;
		 document.getElementById("IndyResult").innerHTML = "ADX = "+ adx[lastIndex].adx.toFixed(4);
		 

} // end func

function CallAllIndy() { 

	     //MaincalculateADX() ;
		 let data = JSON.parse(document.getElementById("dataOutput").value) ;
		 
		 
		 allIndy = MainCallAllIndy(data);	
		 //console.log('All Indy',allIndy) ;
		 
		 document.getElementById("allIndyJson").value = JSON.stringify(allIndy);
		 //console.log('Step Cal ',JSON.stringify(allIndy.TurnList))
		 



} // end func

function FindIndy() {

  	     let adxData = JSON.parse(document.getElementById("allIndyJson").value);
		 lastPointSelected = parseInt(document.getElementById("lastPointIndy").value) ;
		 let thisADX = 0 ; 
		 let adxPosition = '';
		 let differ = 0 ;
		 for (let i=0;i<=adxData.length-1 ;i++ ) {
			 if (adxData[i].time === lastPointSelected) {
                 previousADX = parseFloat(adxData[i-1].adx) ;
				 thisADX = parseFloat(adxData[i].adx); 
				 differ =  thisADX - previousADX ;
				 if (previousADX < thisADX) {
                    adxPosition = 'D' ;
				 } else {
                    adxPosition = 'U'  ;
				 }
				 break;
				 
			 }		 
		 }
         differ = differ.toFixed(4);
		 if (differ > 0) {		 
		   document.getElementById("tdadxResult").innerHTML = thisADX.toFixed(4) + '-U'+   '<hr>'+ differ ;
		 } else {
           document.getElementById("tdadxResult").innerHTML = thisADX.toFixed(4) + '-D '+  '<hr><span style="color:red">'+ differ + "</span>";
		 }


} // end func


async function AjaxpostToServer(AnalyDataString) {

    return;
    let result ;
    let ajaxurl = 'https://thepapers.in/deriv/newDerivObject/labCandleDerivV2.php/AjaxSaveData.php';
    let data = { "Mode": 'SaveAnalyData' ,
    "sData" : AnalyDataString

    } ;
    data2 = JSON.stringify(data);
	
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
        
        return result;
    } catch (error) {
        console.error(error);
    }
} // end func 


async function AjaxNewCalProfit(submode='') {

    let result ;
	let ajaxurl = 'https://thepapers.in/deriv/newDerivObject/newClsTradeV3.php';
	//let AnalysisData = localStorage.getItem('AnalysisData');   
	AnalysisData = document.getElementById("AnalyData").value ;

	AnalysisData2 = JSON.parse(AnalysisData);
 

	

//	alert(AnalysisData3.length)
	

	if (document.getElementById("startPoint").value  != '') {
		startPoint = document.getElementById("startPoint").value ;
	} else {		
		startPoint = AnalysisData2[0]['timestamp'];
    }
	if (document.getElementById("stopPoint").value  != '') {
		stopPoint = document.getElementById("stopPoint").value ;
	} else {		
		stopPoint = AnalysisData2[AnalysisData2.length-1]['timestamp'];
    }
	totalPoint = (stopPoint - startPoint)/60 ;
	
	

    let data = { 
	   "Mode": 'AjaxNewCalProfit'  ,
       "SubMode" : submode, 
       "asset" :  document.getElementById("asset").value ,      
       "startPoint" : startPoint,
       "stopPoint" : stopPoint,
       "totalPoint" : totalPoint,
       //"AnalyData" : AnalysisData2
    } ;

    data2 = JSON.stringify(data);	
	//alert(data2);
	
    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
            //dataType: 'json',
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

		//document.getElementById("suggestSignal").innerHTML = '';
        document.getElementById("messageBox").innerHTML = result ;
		resultObj = JSON.parse(result);
		timeLoss = resultObj.TimeLoss;
		loss4Str = resultObj.loss4Str;
		tradeTimeline = resultObj.tradeTimeline ;

		
		document.getElementById("tradeTimeLine").value =JSON.stringify(resultObj.tradeTimeline );
	 //   console.log('After TradeTime Line',AnalyData);
		


        lossPointList = timeLoss.split(';');
		loss4List = loss4Str.split('##');
		//alert(lossPointList.length);
		//alert(tradeTimeline.length)
		MarkLossPoint(tradeTimeline);
		MarkTimeLoss();
		$("#lab-tab").trigger("click");

	} catch (error) {
        console.error(error);
    }
} // end func 



async function doAjaxGetAnalysisData(candleData) {

    let textArea = document.getElementById("dataOutput");
    let aa = JSON.parse(textArea.value) ;
	aa.length =  aa.length - 1;
	let thisData = JSON.stringify(aa);
    

    let result ;
	
    let ajaxurl = 'https://thepapers.in/deriv/newDerivObject/AjaxGetSignal.php';
    let data = { "Mode": 'getAnalysisData' ,    
    "candleData" : candleData
    } ;
    let data2 = JSON.stringify(data);

	
    let lastIndex = aa.length-1;	
	let time2 =  timestampToTimeMS(aa[lastIndex].time);
	
    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
	        dataType: "json",
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
        
		//console.log('Result Aaa',result)
		
		AnalysisData = JSON.stringify(result.AnalysisData) ;
		localStorage.setItem('AnalysisData',AnalysisData);		
		AnalyData = result.AnalysisData ;
		document.getElementById("AnalyData").value = JSON.stringify(AnalyData);

		RiskPeriodData = result.highRiskPeriods ;
		document.getElementById("RiskPeriodData").value = JSON.stringify(RiskPeriodData);

		SignalChecker = result.signalChecker;
		document.getElementById("SignalChecker").value = JSON.stringify(SignalChecker);
			

/*
		extraData = JSON.stringify(result.exTra1) ;
		document.getElementById("messageBox").innerHTML = extraData;
		extraData2 = JSON.stringify(result.exTra2) ;
		document.getElementById("messageBox").innerHTML += '<hr>' + extraData2;
		extraData3 = JSON.stringify(result.exTra3) ;
		document.getElementById("messageBox").innerHTML += '<hr>' + extraData3;

		extraData4 = JSON.stringify(result.exTra4) ;
		document.getElementById("messageBox").innerHTML += '<hr>' + extraData4;
		extraData5 = JSON.stringify(result.exTra5) ;
		document.getElementById("messageBox").innerHTML += '<hr>emaConflict==>' + extraData5;
		
*/
       

		AjaxpostToServer(JSON.stringify(AnalyData));
		createTableFromJSON(AnalyData, 'tableContainer');

         
		allIndy =	JSON.parse(document.getElementById("allIndyJson").value) ;		
		document.getElementById("allIndyJson").value =JSON.stringify(allIndy);
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

function setTimePicker(startTime,stopTime) {

	     list = '57,79,911,1113,1315,1517,1719,1921,2123';
		 list2 = list.split(',');
		 for (i=0;i<=list2.length-1 ;i++ ) {
            thisID = '#btn' + list2[i];		
		    $(thisID).removeClass('btnSelected');		 
		 }
		 thisBtnID = '#btn' + startTime+stopTime ;
		 console.log(thisID);			
		 $(thisBtnID).addClass('btnSelected');

	     startTmp = document.getElementById("startDate").value ;
		 startDate = startTmp.split(' ');
		 startDate = startDate[0].split('-');
		 if (parseInt(startDate[1]) < 10)	 {
           //startDate[1] = '0'+ startDate[1];
		 } 
		 //alert(parseInt(startDate[2]));
		 if (parseInt(startDate[2]) < 10)	 {
          startDate[2] = startDate[2];
		 }
		 if (parseInt(startTime) < 10) {
           startTime ='0' + startTime ;
		 }

		 newDate =  startDate[0]+'-'+startDate[1]+ '-' +startDate[2] + ' ' +startTime + ':00';

		 newDate2 =  startDate[0]+'-'+startDate[1]+ '-' +startDate[2] + ' ' +stopTime + ':00';



		 
		 document.getElementById("startDate").value = newDate;
		 document.getElementById("endDate").value = newDate2;
		 SaveLocal(thisBtnID) ;
		 

} // end func

function getRadioValue() {

	 const selectedRadio = document.querySelector('input[name="timeframe"]:checked');
     console.log('Selected Radio ', selectedRadio);
	 
	 //alert('getRadioValue-->'+selectedRadio.value);
	 return selectedRadio.value;
		 


} // end func

function setRadioValue(desiredValue) {

	const radios = document.querySelectorAll('input[name="timeframe"]');
    //const desiredValue = "female"; // Change this to the desired value

    radios.forEach(radio => {
     if (radio.value === desiredValue) {
       radio.checked = true;
     }
    });


} // end func

async function AjaxScan() {

    let result ;
    let ajaxurl = 'https://thepapers.in/deriv/newDerivObject/AjaxGetSignal.php';
    let data = { "Mode": 'ScanSignal' ,    
    "AnalyData" : document.getElementById("AnalyData").value
    } ;
    data2 = JSON.stringify(data);
	//alert(data2); return ;
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
        alert(result);
		//document.getElementById("mainBoxAsset").innerHTML = result ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

async function AjaxAll(Mode,dataObj) {

    let result ;
    let ajaxurl = 'AjaxGetSignal.php';
    let data = { 
		"Mode" : Mode,
		"data" : dataObj	
    } ;
    data2 = JSON.stringify(data) ;	
	//alert(data2); return;
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
		document.getElementById("actionResult").innerHTML = result;
		
        return result;
    } catch (error) {
        console.error(error);
    }
} 

function getInner(sTime) {
	     
// return 'sssss '+ sTime ;
 AnalyObj = JSON.parse(document.getElementById("AnalyData").value) ;
 RiskPeriod = JSON.parse(document.getElementById("RiskPeriodData").value) ;


 for (let i=0;i<=AnalyObj.length-1 ;i++ ) {
	 if (parseInt(AnalyObj[i]['timestamp']) === sTime) {
		 st  = 'Close Price ::'  + AnalyObj[i]['timefrom_unix'] + ' = '+ AnalyObj[i]['close'] ;
		 st  += ' EMA ABOVE::'  +  AnalyObj[i]['emaAbove'] ;
  	     st  = st + '  MACD::'  +  AnalyObj[i]['MACDHeight'] ;
		 st =  st + ' SlopeValue::'  +  AnalyObj[i]['ema3SlopeValue'] ;
		 st =  st + ' PIP ::'  +  AnalyObj[i]['pip'] ;
		 st =  st + ' Distance ::'  +  AnalyObj[i]['distance'] + '<hr>';

	     st =  st + ' CutPointType ::'  +  AnalyObj[i]['CutPointType'] + ' ';
		 st =  st + ' TurnType ::'  +  AnalyObj[i]['TurnType'] + ' ';
		 st =  st + ' TurnMode999 ::'  +  AnalyObj[i]['TurnMode999'] + '<hr>';
 
         thisTimeStamp = sTime ;
		 riskDesc  = searchRiskPeriod(RiskPeriod,thisTimeStamp);
		 st =  st + ' RiskDesc ::'  +  riskDesc + '<hr>';
        
		 
		
	 }
 
 }
 return st ;




} // end func

function searchRiskPeriod(RiskPeriod,thisTimeStamp) {

        riskDesc = 'No Risk';
	    for (let i=0;i<=RiskPeriod.length-1 ;i++ ) {
			if (RiskPeriod[i].timestamp === thisTimeStamp) {
				riskDesc = RiskPeriod[i].risk + "<br>";
				riskDesc = ' Warn ='+  RiskPeriod[i].warnings +"<br>";
				riskDesc += JSON.stringify(RiskPeriod[i].details) ;
				stRisk = ' Risk Level =<span style="color:red">'+  RiskPeriod[i].risk +"</span>";
				stRisk += ' AllWarn Code ='+  RiskPeriod[i].AllWcode +"<br>";
				stRisk += ' Warn ='+  RiskPeriod[i].warnings +"<br>";
				
				for (let j=0;j<=RiskPeriod[i].details.candlePatterns.length-1 ;j++ ) {
					  stRisk += RiskPeriod[i].details.candlePatterns[j].description +' ; ';	
				}
				for (let j=0;j<=RiskPeriod[i].details.emaConditions.length-1 ;j++ ) {
					  stRisk += RiskPeriod[i].details.emaConditions[j].description +' ; ';	
				}
				for (let j=0;j<=RiskPeriod[i].details.emaDifference.length-1 ;j++ ) {
					  stRisk += RiskPeriod[i].details.emaDifference[j].description +' ; ';	
				}
				for (let j=0;j<=RiskPeriod[i].details.marketConditions.length-1 ;j++ ) {
					  stRisk += RiskPeriod[i].details.marketConditions[j].description +' ; ';	
				}


				//console.log('St2',st) ;
				



				//console.log(RiskPeriod[i].details.candlePatterns.length)

				break;
			}	    
	    }
		return stRisk ;
		return riskDesc ;


} // end func


// เพิ่มฟังก์ชันเลื่อนไปยังตำแหน่งที่ระบุ
        document.getElementById('scrollButton').addEventListener('click', function() {
			event.preventDefault() ;
            const input = document.getElementById('barIndex').value.trim();
            
            // ทำงานเมื่อมีการระบุค่า
            if (input) {
                if (isNaN(input)) {
					 console.clear();
					
                    startDate = document.getElementById("startDate").value ;
					console.log(startDate);
					startDate = startDate.split(' ');
					a = startDate[0] ;
					b = a.split('-') ;

					 
                    // ถ้าเป็น timestamp ในรูปแบบ yyyy-mm-dd
					candleData = JSON.parse(document.getElementById("dataOutput").value) ;
					 
					year = parseInt(b[0]) ; month = parseInt(b[1]) ;
					day = parseInt(b[2]);
					console.log(year,'-',month,'-',day) ;
					if (month < 10) {
						month = '0' + month.toString();
					}
					if (day < 10) {
						day = '0' + day.toString();
					}
					



                    const targetTime = input;
					const [hours, minutes] = targetTime.split(':').map(Number);
                    const date = new Date();
					date.setFullYear(year, month, day); 
                    date.setHours(hours, minutes, 0, 0);
                    TimeStampCheck=  date.getTime()/1000;

					//console.log(TimeStampCheck)

					//const dateString = '2025-03-12 08:00:00';
                    const dateString = year + '-'+ month +'-' + day + ' '+targetTime +':00';
					console.log('Date Str',dateString);
					
                    const timestampInSeconds = Math.floor(new Date(dateString.replace(' ', 'T')).getTime() / 1000);
					 console.log('--->',timestampInSeconds) ;
					
					

                    
                    // ค้นหาว่า timestamp ตรงกับ index ไหน
                    /*let targetIndex = candleData.findIndex(candle => parseInt(candle.time) === parseInt(timestampInSeconds));
					*/
				    
                    let targetIndex = 0 ;
                    for (let i=0;i<=candleData.length-1 ;i++ ) {
					  if (parseInt(candleData[i].time) === parseInt(timestampInSeconds)) {
                        targetIndex = i  ; break ;  
					  } else {
                        targetIndex = i  ;
					  }                    
                    }
					
					
					
                    //const  targetIndex  = 10
                    if (targetIndex !== -1) {
					    let posCal = (candleData.length - targetIndex)*-1   ;
						posCal = posCal + 30;
						console.log(targetIndex,'->',posCal) ;
                        // ใช้ coordinate เพื่อเลื่อนไปยังตำแหน่งที่ต้องการ
                        chart.timeScale().scrollToPosition(posCal , false);
                    } else {
                        alert('ไม่พบข้อมูล timestamp ที่ระบุ');
                    }
                } else {
                    // ถ้าเป็นตัวเลข (index)
                    const targetIndex = parseInt(input);
					chart.timeScale().scrollToPosition(targetIndex - 5, false);
					return;
                    
                    if (targetIndex >= 0 ) {
                        // เลื่อนไปยัง index ที่ต้องการ
                        chart.timeScale().scrollToPosition(targetIndex - 5, false);
                    } else {
                        alert('ตำแหน่ง index ไม่ถูกต้อง');
                    }
                }
            }
        });

</script>

<?php
  
function ModalForm1() {  ?>

<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Modal Heading</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <h2>บทวิเคราะห์  EMA </h2>
		<ol>
		 <li>ema ต้องใช้เวลา 1-3 แท่งกว่าจะวิ่งทันกัน </li>
		 <li>ema อาจจะสลับ candle ในแนว sideway หรือ staircase ก็ได้  </li>
		 <li>แท่งเทียน ที่อยู่ใน Sideway </li>
		 <li> </li>
		</ol>
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

function showKnowLedge(){ ?>
<ol>
<li>
ในการวิเคราะห์ทางเทคนิคเกี่ยวกับการเทรด คำว่า "ราคาอยู่ใต้ทั้ง EMA3 และ EMA5 แต่ต้องการเปิด Long" หมายถึง:
"ราคาอยู่ใต้ทั้ง EMA3 และ EMA5" - ราคาปัจจุบันของสินทรัพย์ (เช่น หุ้น, คริปโต, หรือสกุลเงิน) กำลังซื้อขายต่ำกว่าค่าเฉลี่ยเคลื่อนที่แบบเอ็กซ์โพเนนเชียล (EMA) ทั้งของระยะเวลา 3 วัน/คาบและ 5 วัน/คาบ
"ต้องการเปิด Long" - นักลงทุนต้องการเข้าซื้อสินทรัพย์ (เปิดสถานะซื้อ) โดยคาดหวังว่าราคาจะปรับตัวสูงขึ้นในอนาคต
โดยทั่วไป สถานการณ์นี้อาจถือเป็นการเทรดที่ "ขัดกับเทรนด์" (counter-trend) เนื่องจาก:

เมื่อราคาอยู่ต่ำกว่า EMA มักบ่งชี้แนวโน้มขาลงในระยะสั้น
แต่นักเทรดกลับต้องการซื้อ (Long) ซึ่งเป็นการคาดการณ์ว่าราคาจะกลับตัวขึ้น

นักเทรดอาจมีเหตุผลในการเปิด Long ในสถานการณ์นี้ เช่น:

เชื่อว่าราคาอาจกำลังจะกลับตัวขึ้น (reversal)
มีสัญญาณอื่นที่บ่งชี้การกลับตัว (เช่น รูปแบบแท่งเทียน, ดัชนีอื่นๆ)
ราคาอยู่ในแนวรับสำคัญ (support level)

อย่างไรก็ตาม กลยุทธ์นี้มีความเสี่ยงสูงขึ้นเพราะขัดกับแนวโน้มทางเทคนิคในระยะสั้น
</li>
<li>
ตลาดที่อยู่ในช่วง Consolidation โดยที่ EMA ไม่มีทิศทางชัดเจน มีลักษณะและความหมายดังนี้:
ช่วง Consolidation คืออะไร?
Consolidation หมายถึงช่วงเวลาที่ราคาเคลื่อนไหวในกรอบแคบ ไม่มีทิศทางขาขึ้นหรือขาลงที่ชัดเจน เป็นช่วงที่ราคาอยู่ในภาวะพักตัว หรือรวมตัว ราคามักจะแกว่งตัวไปมาในระยะแคบๆ
EMA ไม่มีทิศทางชัดเจน หมายความว่าอย่างไร?
เมื่อ EMA (Exponential Moving Average) ไม่มีทิศทางชัดเจน จะมีลักษณะดังนี้:

เส้น EMA มีแนวโน้มแนวราบ ไม่ได้ชี้ขึ้นหรือชี้ลงอย่างชัดเจน
เส้น EMA หลายระยะเวลาอาจตัดกันไปมาบ่อยครั้ง
ระยะห่างระหว่างเส้น EMA แคบลง (ไม่แยกห่างกันชัดเจน)

ลักษณะสำคัญของตลาดในช่วงนี้:

ราคาอาจแกว่งตัวรอบๆ เส้น EMA ไปมา ขึ้นๆ ลงๆ แต่ไม่เคลื่อนที่ไปในทิศทางใดทิศทางหนึ่งอย่างต่อเนื่อง
ปริมาณการซื้อขาย (Volume) มักจะลดลง แสดงถึงการรอคอยของผู้เล่นในตลาด
เกิดแรงซื้อและแรงขายที่สมดุลกัน แรงซื้อและแรงขายมีกำลังพอๆ กัน

ความหมายต่อการวิเคราะห์และการเทรด:

ตลาดกำลังอยู่ในช่วงรอจุดเปลี่ยน อาจกำลังรอปัจจัยใหม่มากระตุ้น
เป็นช่วงที่ผู้เล่นในตลาดกำลังประเมินสถานการณ์ ไม่แน่ใจในทิศทางต่อไป
มักเกิดขึ้นหลังจากมีการเคลื่อนไหวอย่างรุนแรงในทิศทางใดทิศทางหนึ่ง
อาจเป็นช่วงสะสมกำลัง (Accumulation) หรือแจกจ่าย (Distribution) ก่อนที่ราคาจะเคลื่อนไหวในทิศทางใหม่อย่างชัดเจน

การแปลความหมายทางการเทรด:
นักเทรดบางคนอาจหลีกเลี่ยงการเข้าเทรดในช่วงนี้เพราะทิศทางไม่ชัดเจน ขณะที่บางคนอาจมองว่าเป็นโอกาสในการเทรดแบบ Range Trading (ซื้อที่แนวรับ ขายที่แนวต้าน) รอให้ราคาเบรกออกจากกรอบ Consolidation พร้อมปริมาณการซื้อขายที่เพิ่มขึ้นก่อนตัดสินใจเข้าเทรดตามทิศทางใหม่
</li>
<li>
การที่ EMA3 ตัดลง EMA5 ขัดแย้งกับการเปิด Long เพราะ:
เมื่อ EMA3 (ค่าเฉลี่ยเคลื่อนที่แบบเอ็กซ์โพเนนเชียล 3 คาบ) ตัดลงมาต่ำกว่า EMA5 (ค่าเฉลี่ยเคลื่อนที่แบบเอ็กซ์โพเนนเชียล 5 คาบ) นี่เป็นสัญญาณทางเทคนิคที่บ่งชี้ถึงการเปลี่ยนแปลงแนวโน้มในระยะสั้นไปทางขาลง เพราะ:

การตัดของเส้นค่าเฉลี่ย: เมื่อค่าเฉลี่ยระยะสั้นกว่า (EMA3) ตัดลงมาต่ำกว่าค่าเฉลี่ยระยะยาวกว่า (EMA5) แสดงว่าแรงขายเริ่มมีมากกว่าแรงซื้อ
แนวโน้มขาลงระยะสั้น: สัญญาณนี้บ่งชี้ว่าโมเมนตัมราคากำลังเปลี่ยนเป็นขาลง ราคามีแนวโน้มที่จะลดลงต่อไปในระยะสั้น
Death Cross ในระยะสั้นมาก: ถึงแม้จะเป็นการตัดกันของเส้น EMA ระยะสั้นมาก แต่ก็ยังจัดเป็น Death Cross ขนาดเล็ก ซึ่งเป็นสัญญาณขาลง

เมื่อเทียบกับการเปิด Long (การซื้อโดยคาดหวังว่าราคาจะขึ้น):

การเปิด Long ต้องการแนวโน้มขาขึ้น หรืออย่างน้อยก็มีสัญญาณว่าราคากำลังจะกลับตัวขึ้น
แต่สัญญาณ EMA3 ตัดลง EMA5 กลับบ่งชี้ทิศทางตรงข้าม คือราคามีแนวโน้มจะลงต่อ

นักเทรดที่ใช้ระบบ EMA มักจะรอให้ EMA3 ตัดขึ้นเหนือ EMA5 (Golden Cross ระยะสั้น) ก่อนที่จะพิจารณาเปิดสถานะ Long เพื่อให้สอดคล้องกับแนวโน้มทางเทคนิค การเปิด Long ในช่วงที่ EMA3 เพิ่งตัดลง EMA5 จึงเป็นการเทรดที่ "ขัดกับสัญญาณทางเทคนิค" และมีความเสี่ยงสูงขึ้น
</li>
</ol>



<?php
} // end function

  


/*
1,2,4,6,8,12
0.90,1.8,3.6,
Loss= 1 : Balance = -1 
Loss= 2 : MoneyTrade = 2 
           Win -> MoneyTrade =2 Profit = 1.8 Balance =   1.8+(-1) = 0.8 ->Stop
           Loss-> Balance = -1 + -2 = -3 
Loss= 3 : MoneyTrade = 4 
           Win--> Profit = 3.6 Balance =   3.6+(-3) = 0.6
		   Loss-> Balance = -1 + -2 + -4 = -7
Loss= 4 : MoneyTrade = 8 
           Win--> Profit = 7.2 Balance =   7.2+(-7) = 0.2
		   Loss-> Balance = -1 + -2 + -4 +-8 = -15

Loss= 5 : MoneyTrade = 20 
           Win--> Profit = 18 Balance =   18+(-15) = 3
		   Loss-> Balance = -1 + -2 + -4 + -8 + -20 = -35


*/		   
     


?>

</body>
</html>