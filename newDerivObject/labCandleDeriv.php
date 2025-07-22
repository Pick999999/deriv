<?php
/*  

//labCandleDeriv.php
สร้าง html form ด้วย  bootstrap5 cdn มี  dtpicker 2 อัน คือ startDate,endDate สำหรับเลือก วัน และ ช่วงเวลา  และ 
input text box 1 อัน สำหรับป้อนค่า asset  และ  input text box 1 อัน สำหรับป้อนค่า  timeframe ในหน่วย minute 
button 1 อัน  และเมื่อ คลิก button ให้ทำการดึงข้อมูล candle stick จาก deriv.com และนำข้อมูลที่ได้ มาใส่ใน textarea มาวาด กราฟ candle stick และ ema3,ema5
bollinger band  ด้วย  https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js
โดยทำด้วย pure javascript
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
	<script src="IndyLib.js"></script>
	<script src="https://thepapers.in/deriv/devlab/jsAnalyCandle.js"></script>
</head>
<style>
  td { border:1px solid gray; padding:5px } 
  .green { background:#00ff00 } 
  .gray { background:#cecece } 

</style>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Trading Chart Analysis - Live Deriv.com Data</h4>
                    </div>
                    <div class="card-body">
                        <form id="chartForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" class="form-control" id="startDate"
									value='2025-02-21 12:00:00' onchange='SaveLocal()'
									>
									<button type='button' id='' class='mBtn' onclick="AddDay()">+</button>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">End Date</label>
                                    <input type="text" class="form-control" id="endDate"
									value='2025-02-21 13:00:00' onchange='SaveLocal()'
									>
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
                                    <input type="text" class="form-control" id="asset" placeholder="e.g. R_50" value='R_25' onchange='SaveLocal()'>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Timeframe (minutes)</label>
                                    <input type="number" class="form-control" id="timeframe" placeholder="Enter timeframe" value=1 onchange='SaveLocal()'>
                                </div>
                            </div>
							<button type='button' id='btnConnectStatus' class='gray'
							onclick='reconnect()'
							style='height:30px;width:30px;border-radius:15px'></button>
                            <button type="submit" class="btn btn-primary">Get Data</button>
							<button type="botton" class="btn btn-primary" onclick='AjaxgetSuggestSignal("AjaxgetSuggestSignal2")'>Get Signal</button>
							<span id='suggestSignal99'></span>
							<div id='suggestSignal2'></div>
							<input type="text" id="timestampSelect">
							<button type='button' id='' class='mBtn' onclick="setStartPoint()">เป็นจุด Start</button>
							<button type='button' id='' class='mBtn' onclick="setStopPoint()">เป็นจุด Stop</button>
							<button type='button' id='' class='mBtn' onclick='AjaxgetCalProfit()'>คำนวณกำไร 2 จุด</button>
							<input type="text" id="startPoint">ถึง
							<input type="text" id="stopPoint">



                        </form>
                        <div id="chart" class="mt-4" style="height: 400px;"></div>
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
			  Last Point :: <input type="text" id="lastPointIndy"value=0>
			  <button type='button' id='' class='mBtn' onclick="CallAllIndy()">หา Indy ต่างๆ</button>
			  <table id="sortableTable" style='width:100%'>
			  
			  <tr>
 			    <td style='width:120px;background:#0080c0;color:white'>Indy Name</td>
				<td>ADX</td>
				<td>Stocha</td>
				<td></td>
				<td></td>
			  </tr>
			  <tr>
			    <td style='width:120px;background:#0080c0;color:white'>Indy Param</td>
				<td><input type="number" id="adxPeriod" value=7 style='width:50px'></td>
				<td></td>
				<td></td>
				<td></td>
			  </tr>
			  <tr>
			    <td style='width:120px;background:#0080c0;color:white'>Indy Result</td>
				<td id='tdadxResult'></td>
				<td></td>
				<td></td>
				<td></td>
			  </tr>
			  </table>
			  <button type='button' id='' class='mBtn' onclick="MaincalculateADX()">ADX</button>
			  <button type='button' id='' class='mBtn' onclick="MaincalculateADX()">Stochastic</button>
			  <button type='button' id='' class='mBtn' onclick="findADX()">Ichi Clould</button>
                Indy+ สถิติต่างๆ
                <p id='IndyResult' class="mt-3">
					
                </p>
				<input type="text" id= 'allIndyJson'>
            </div>

			<div class="tab-pane fade" id="labTrade" role="tabpanel" aria-labelledby="lab-tab">
			    <button type='button' id='' class='mBtn' onclick="AjaxLabTrade()">คำนวณ</button>
                <p id='labTradeResult' class="mt-3">This is the Lab tab content.</p>
            </div>
        </div>
    </div>
  
  
<?php
 } // end function
  
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script> 

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

        // Add candlestick series
        const candlestickSeries = chart.addCandlestickSeries();
        
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

        // Calculate EMA
        function calculateEMA(data, period) {
            const k = 2 / (period + 1);
            let ema = data[0].close;
            const emaData = [];

            data.forEach((candle, index) => {
                ema = (candle.close * k) + (ema * (1 - k));
                emaData.push({
                    time: candle.time,
                    value: ema
                });
            });

            return emaData;
        }

        // Calculate Bollinger Bands
        function calculateBollingerBands(data, period = 20) {
            const bands = [];
            for (let i = period - 1; i < data.length; i++) {
                const slice = data.slice(i - period + 1, i + 1);
                const sum = slice.reduce((acc, val) => acc + val.close, 0);
                const sma = sum / period;
                
                const squaredDiffs = slice.map(candle => Math.pow(candle.close - sma, 2));
                const variance = squaredDiffs.reduce((acc, val) => acc + val, 0) / period;
                const stdDev = Math.sqrt(variance);
                
                bands.push({
                    time: data[i].time,
                    upper: sma + (2 * stdDev),
                    lower: sma - (2 * stdDev)
                });
            }
            return bands;
        }

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

                // Update textarea
                document.getElementById('dataOutput').value = JSON.stringify(candleData, null, 2);
				doAjaxGetSignal();
				AjaxgetCalProfit();
				CallAllIndy();


                // Update chart
                candlestickSeries.setData(candleData);

                // Calculate and set EMA
                const ema3Data = calculateEMA(candleData, 3);
                const ema5Data = calculateEMA(candleData, 5);
                ema3Series.setData(ema3Data);
                ema5Series.setData(ema5Data);

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

            const startDate = new Date(document.getElementById('startDate').value).getTime() / 1000;
            const endDate = new Date(document.getElementById('endDate').value).getTime() / 1000;
            const asset = document.getElementById('asset').value;
            const timeframe = parseInt(document.getElementById('timeframe').value);

            // Request candles data
            const request = {
                ticks_history: asset,
                adjust_start_time: 1,
                count: 2000,
                end: endDate,
                start: startDate,
                style: "candles",
                granularity: timeframe * 60
			  
            };
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
      
     console.log('Sub Scribe to time')
     
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
		

    </script>

<script>

// Function to create a table from JSON data
function createTableFromJSON(jsonData, targetElementId) {

	console.log('Json Data',jsonData);
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
    let data = { "Mode": 'getSignal' ,    
    "candleData" : thisData
    } ;
    let data2 = JSON.stringify(data);
	
    let lastIndex = aa.length-1;
	//alert(aa[lastIndex].open,'-',aa[lastIndex].close);
	let time2 =  timestampToTimeMS(aa[lastIndex].time);
	//alert(time2+ ' Open= '+aa[lastIndex].open+ ' Close= '+aa[lastIndex].close);

	//alert(data2);
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
        //alert(result);
		console.log('Result =>',result);
	    console.log('Result =>',result.thisAction);		
		AnalysisData = JSON.stringify(result.AnalysisData) ;
		localStorage.setItem('AnalysisData',AnalysisData);
		//document.getElementById("suggestSignal").innerHTML = result.thisAction + '->'+ result.actionReason;

		AnalyData = result.AnalysisData ;
		createTableFromJSON(AnalyData, 'tableContainer');




		 

		

		
		//document.getElementById("mainBoxAsset").innerHTML = result ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

function AddDay() {

	    let startDatePicker = flatpickr("#startDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        let endDatePicker = flatpickr("#endDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
/*
        flatpickr("#startDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
*/
           
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

function SaveLocal() {

localObj = {

	startDate : document.getElementById("startDate").value ,
    endDate : document.getElementById("endDate").value ,
    asset  : document.getElementById("asset").value ,
    timeframe : document.getElementById("timeframe").value 

} 

localStorage.setItem('labCandleDeiv',JSON.stringify(localObj));
document.getElementById("minuteADD").value = document.getElementById("timeframe").value ;

} // end func

$(document).ready(function () {

   let labCandleDeriv1 = JSON.parse(localStorage.getItem('labCandleDeiv'));
   
   
   //if (labCandleDeriv) {
	   document.getElementById("startDate").value = labCandleDeriv1.startDate;
	   document.getElementById("endDate").value = labCandleDeriv1.endDate;
       document.getElementById("asset").value = labCandleDeriv1.asset;
	   document.getElementById("timeframe").value = labCandleDeriv1.timeframe;

   //}

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

async function AjaxgetCalProfit() {

let thistimestamp = parseInt(document.getElementById("timestampSelect").value) ;

//let dataTmp = JSON.parse(localStorage.getItem('AnalysisData'));
let dataTmp =  JSON.parse(document.getElementById('dataOutput').value) ;
 
//'AjaxgetSuggestSignal2'

//const Candles = dataTmp.filter(candle => parseInt(candle.time) <= thistimestamp);
    
	
    let result ;
	let ajaxurl = 'https://thepapers.in/deriv/newDerivObject/AjaxGetSignal.php';
	let AnalysisData = localStorage.getItem('AnalysisData');   

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
	   "Mode": 'AjaxgetCalProfit'  ,
       "timestampSelected" : document.getElementById("timestampSelect").value,
       "candleData" : JSON.stringify(dataTmp),
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
		console.log('rrrrr-',result.winTradeList);

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
		 document.getElementById("allIndyJson").value = JSON.stringify(allIndy);



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




/*
1,2,4-> 7  
9*0.90 = 8.1  กำไร 1.1  เงินทุน 16 -->1,2,4,9 = 16usd
19*0.9 =17.1  กำไร 1.1  เงินทุน 35 -->1,2,4,9,19 = 35usd
40*0.9 = 36   กำไร 1.0  เงินทุน 75 -->1,2,4,9,19,40 = 75usd






*/


</script>


</body>
</html>