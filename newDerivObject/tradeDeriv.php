<!-- 
  ดึงข้อมูล จาก deriv.com แล้วหาค่า ema3,ema5 
  โดยไปแก้ไข app_id,    
  tradeDeriv.php
-->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candlestick Data Fetcher</title>
	<!-- 
    <script src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>
	 -->
	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .time-display {
            font-size: 24px;
            margin: 20px 0;
        }
        .radio-group {
            display: flex;
            gap: 15px;
        }
        .chart-container {
            height: 400px;
            margin: 20px 0;
            border: 1px solid #ddd;
        }
        .candle-data {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        td:first-child {
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
		td { text-align:center; }
        .ema3 {
            color: #2962FF;
        }
        .ema5 {
            color: #FF6B6B;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv Candlestick Data Fetcher</h1>
		<h2>จุดมุ่งหมายเพื่อดู  หลาย ๆ Time Frame</h2>
        
        <div class="time-display">
            Server Time: <span id="serverTime">Loading...</span>
        </div>
		<button type='button' id='' class='mBtn' 
		onclick='placeTrade(10, 1, "frxEURUSD", "CALL");'>
		Place Trade</button>
		เลือก Group
		<div id="groupSymbolContainer" class="bordergray flex">
		     
		</div>

        <form id="candlestickForm">
            <div class="form-group">
                <label for="asset">เลือก Asset:</label>
                <select id="asset" name="asset" required onchange='saveToLocal()'>
                    <option value="R_10">Volatility 10 Index</option>
                    <option value="R_25">Volatility 25 Index</option>
                    <option value="R_50">Volatility 50 Index</option>
                    <option value="R_75">Volatility 75 Index</option>
                    <option value="R_100" selected >Volatility 100 Index</option>
                    <option value="BOOM1000">Boom 1000 Index</option>
                    <option value="CRASH1000">Crash 1000 Index</option>
                </select>
            </div>
			<button type='button' id='' class='mBtn' onclick="saveToLocal()">Save To Local</button>

            <div class="form-group">
                <label>เลือก Timeframe:</label>
                <div class="radio-group">
                    <label><input type="radio" onclick='saveToLocal()' name="timeframe" id="timeframe1" value="1" > 1 นาที</label>
					<label><input type="radio" onclick='saveToLocal()' name="timeframe" 
					id="timeframe2"
					value="2" > 2 นาที</label>
					<label><input type="radio" onclick='saveToLocal()' name="timeframe" 
					id="timeframe3"
					value="3" > 3 นาที</label>
                    <label><input type="radio" onclick='saveToLocal()'  name="timeframe" 
					id="timeframe5"
					value="5"> 5 นาที</label>
                    <label><input type="radio" onclick='saveToLocal()' name="timeframe" 
					id="timeframe10"
					value="10"> 10 นาที</label>
                    <label><input type="radio" onclick='saveToLocal()' name="timeframe" 
					id="timeframe15"
					value="15"> 15 นาที</label>
                    <label><input type="radio" onclick='saveToLocal()' name="timeframe" 
					id="timeframe30"
					value="30"> 30 นาที</label>
                </div>
            </div>
        </form>
		<button type='button' id='' class='mBtn' onclick="fetchCandles()">Fetch Candle</button>

		
		<button type='button' id='' class='mBtn' onclick="TradePlan1()">TradePlan1</button>

        <div id="status"></div>
        
        <div id="chartContainer" class="chart-container"></div>
        
        <div class="candle-data">
            <h2>Latest Candle Data</h2>
			<span id='balanceResult' style='color:red;font-weight:bold'></span> 
            <table id="candleTable">
			  <!-- 
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Open</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Close</th>
                        <th class="ema3">EMA3</th>
                        <th class="ema5">EMA5</th>
						<th class="ema5">EMAAbove</th>
                    </tr>
                </thead>
				 -->
				<thead>
                    <tr>
					    <th>Time1</th>                        
                        <th>Time</th>                        
						<th class="ema5">Color</th>                        
						<th class="ema5">EMAAbove</th>						
						<th class="ema5">CutPointType</th>
						<th class="ema5">MoneyTrade</th>
						<th class="ema5">Win Status</th>
						<th class="ema5">Loss Con</th>
						
						<th class="ema5">Profit</th>
						<th class="ema5">Balance</th>
						<th class="ema5">EMAConflict</th>
						<th class="ema5">Plan1</th>

                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
	<textarea id="AnalyDataTxt" rows="" cols=""></textarea>

    <script>
        let websocket = null;
        let selectedTimeframe = 1;
        let isProcessing = false;
        let isConnecting = false;
        let reconnectAttempts = 0;
        let timeSubscription = null;
		let AllSymBolList = null ;
        let chart = null;
        let candleSeries = null;
        let ema3Series = null;
        let ema5Series = null;
		let AnalyData = [] ;

        
        async function initChart() {
            const chartContainer = document.getElementById('chartContainer');
            
            if (typeof LightweightCharts === 'undefined') {
                console.error('LightweightCharts library not loaded');
                document.getElementById('status').textContent = 'Error: Chart library not loaded';
                return;
            }
            
            chart = LightweightCharts.createChart(chartContainer, {
                width: chartContainer.clientWidth,
                height: chartContainer.clientHeight,
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                timeScale: {
                    borderColor: '#d1d1d1',
                    timeVisible: true,
					tickMarkFormatter: (time) => {
                      let hours = parseInt(time);
                      hours = timestampToHHMM(time);				
                      return `${hours}`;
                    }   
                },
				rightPriceScale: {
                    borderColor: '#D1D4DC',
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                               
            });



            candleSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350'
            });


            ema3Series = chart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                title: 'EMA 3'
            });

            ema5Series = chart.addLineSeries({
                color: '#FF6B6B',
                lineWidth: 2,
                title: 'EMA 5'
            });

            window.addEventListener('resize', () => {
                chart.applyOptions({
                    width: chartContainer.clientWidth,
                    height: chartContainer.clientHeight
                });
            });
        }

		function timestampToHHMM(timestamp) {
			// สร้างออบเจกต์ Date จาก timestamp (ถ้า timestamp เป็นวินาที ให้คูณด้วย 1000 เพื่อแปลงเป็นมิลลิวินาที)
			const date = new Date(timestamp * 1000);

			// ดึงชั่วโมงและนาที
			const hours = date.getHours();
			const minutes = date.getMinutes();

			// เติมศูนย์ข้างหน้าหากชั่วโมงหรือนาทีน้อยกว่า 10
			const formattedHours = hours < 10 ? `0${hours}` : hours;
			const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;

		    //console.log(`${formattedHours}:${formattedMinutes}`) ;
			

			// รวมเป็นรูปแบบ hh:mm
			return `${formattedHours}:${formattedMinutes}`; 
        }

        function calculateEMA(data, period) {
            const k = 2 / (period + 1);
            let ema = data[0].close;
            const emaData = [];

            data.forEach((candle, index) => {
                if (index === 0) {
                    emaData.push({ time: candle.time, value: ema });
                    return;
                }

                ema = (candle.close * k) + (ema * (1 - k));
                emaData.push({ time: candle.time, value: ema });
            });

            return emaData;
        }

        function connect() {

			 if (isConnecting) return;    
               isConnecting = true;
               websocket = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
            
				websocket.onopen = function() {
					console.log('WebSocket Connected');
					subscribeToTime();
					initChart().catch(console.error);
				};
				

            websocket.onmessage = function(msg) {
                const data = JSON.parse(msg.data);
                
                
                if (data.time) {
                    updateServerTime(data.time);
                }
                
                if (data.candles) {
					console.log('Data',data.candles)
                    processCandles(data.candles);
                }
            };

            websocket.onclose = function() {
                console.log('WebSocket Disconnected:', event.code, event.reason);
                isConnecting = false;

				// ตรวจสอบจำนวนครั้งในการ reconnect
				if (reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
					console.log(`Attempting to reconnect... (${reconnectAttempts + 1}/${MAX_RECONNECT_ATTEMPTS})`);
					reconnectAttempts++;
					setTimeout(connect, RECONNECT_DELAY);
				} else {
					console.error('Max reconnection attempts reached. Please check your connection.');
					// อาจจะเพิ่มการแจ้งเตือนผู้ใช้หรือทำการ reset การเชื่อมต่อ
				}
            };
        }

        function subscribeToTime() {
            if (timeSubscription) {
                clearInterval(timeSubscription);
            }
            websocket.send(JSON.stringify({ "time": 1 }));
            timeSubscription = setInterval(() => {
                if (websocket && websocket.readyState === WebSocket.OPEN) {
                    websocket.send(JSON.stringify({ "time": 1 }));
                }
            }, 1000);
        } 

		// ฟังก์ชั่นสำหรับตรวจสอบสถานะการเชื่อมต่อ
function checkConnection() {
    if (websocket && websocket.readyState === WebSocket.OPEN) {
        return true;
    }
    return false;
}

// ฟังก์ชั่นสำหรับ force reconnect
function forceReconnect() {
    if (websocket) {
        websocket.close();
    }
    reconnectAttempts = 0;
    connect();
}

function updateServerTime(timestamp) {
        const date = new Date(timestamp * 1000);
        const timeStr = date.toLocaleTimeString();
        document.getElementById('serverTime').textContent = timeStr;

        if (date.getSeconds() === 1 && !isProcessing) {
             fetchCandles();
        }
}

function fetchCandles() {
	         
			candleTable = document.getElementById("candleTable");   
			candleTable.rows = 1;

            isProcessing = true;
            const asset = document.getElementById('asset').value;
            const timeframe = parseInt(document.querySelector('input[name="timeframe"]:checked').value);
            
            const request = {
                "ticks_history": asset,
                "style": "candles",
                "granularity": timeframe * 60,
                "count": 60,
                "end": "latest"
            };

            websocket.send(JSON.stringify(request));
            document.getElementById('status').textContent = 'Fetching candles at ' + new Date().toLocaleTimeString();
}

function formatNumber(num) {
         return Number(num).toFixed(5);
}

function processCandles(candles) {
            const candleData = candles.map(candle => ({
                time: candle.epoch,
                open: candle.open,
                high: candle.high,
                low: candle.low,
                close: candle.close
            }));
            console.log('Candle Data')
            
            if (chart && candleSeries) {
                // Update chart
                candleSeries.setData(candleData);

                // Calculate EMAs
                const ema3Data = calculateEMA(candleData, 3);
                const ema5Data = calculateEMA(candleData, 5);
                
                ema3Series.setData(ema3Data);
                ema5Series.setData(ema5Data);

                // Update table
                const tbody = document.querySelector('#candleTable tbody');
                tbody.innerHTML = '';
				let emaAbove = '';
				let classCSS  ='';
				let emaAboveList = [];
				let emaChange = '';
				let emaConflict = '';
				let thisColor = '';
				let CutPointType = '';


                candles.forEach((candle, index) => {
                    const row = document.createElement('tr');
                    const time = new Date(candle.epoch * 1000).toLocaleString();
					if (ema5Data[index].value > ema3Data[index].value) {
						emaAbove = '5'; classCSS  ='ema5';
					} 
					if (ema5Data[index].value < ema3Data[index].value) {
						emaAbove = '3'; classCSS  ='ema3';
					} 

					thisColor = candle.open >= candle.close ? "Red" : "Green";

					emaAboveList.push(emaAbove);

					if (emaAboveList[emaAboveList.length-1] !== emaAboveList[emaAboveList.length-2]) {
						emaChange = 'y' ;
					} else {
						emaChange = '' ;
					}
					emaConflict = '';
					if (thisColor =='Green' && emaAbove === '5' ) {
						emaConflict = 'Y';
					}
					if (thisColor =='Red' && emaAbove === '3' ) {
						emaConflict = 'Y';
					}
					CutPointType = '';

					if (emaChange  === 'y') {					
						if (emaAboveList[emaAboveList.length-2]==='3' && emaAbove === '5' ) {
							CutPointType = '3TO5';
						}
						if (emaAboveList[emaAboveList.length-2]==='5' && emaAbove === '3' ) {
							CutPointType = '5TO3';
						}
						 
					}
					
					
					ema5Data[index].value;
					/*
                    row.innerHTML = `
                        <td width="150px">${time}</td>
                        <td>${formatNumber(candle.open)}</td>
                        <td>${formatNumber(candle.high)}</td>
                        <td>${formatNumber(candle.low)}</td>
                        <td>${formatNumber(candle.close)}</td>
                        <td class="ema3">${formatNumber(ema3Data[index].value)}</td>
                        <td class="ema5">${formatNumber(ema5Data[index].value)}</td>
						<td class="${classCSS}">${emaAbove}</td>
                    `;
					*/
					row.innerHTML = `
						<td>${candle.epoch}</td>   
                        <td>${time}</td>   
						<td>${thisColor}</td>   						
						<td class="${classCSS}">${emaAbove}</td>						
						<td class="${classCSS}">${CutPointType}</td>
						<td id="money_${candle.epoch}" class="${classCSS}"></td>
						<td id="WinStatus_${candle.epoch}" class="${classCSS}"></td>
						<td id="lossCon_${candle.epoch}" class="${classCSS}"></td>
						
						<td id="profit_${candle.epoch}" class="${classCSS}"></td>
						<td id="balance_${candle.epoch}" class="${classCSS}"></td>

						

						<td class="${classCSS}">${emaConflict}</td>
						<td class="${classCSS}"></td>
                        <td class="${classCSS}"></td>
                    `;
					row.id = candle.epoch;
                    tbody.appendChild(row);
					sObj = {
                      timestamp : candle.epoch,
                      time: time,
                      color: thisColor,
                      emaAbove : emaAbove,
					  CutPointType : CutPointType,
				      emaConflict : emaConflict
					}
                    AnalyData.push(sObj);  
                });
            }

			console.log('AnalyData',AnalyData);
			document.getElementById("AnalyDataTxt").value = JSON.stringify(AnalyData);
			

            // Send to endpoint
            //sendToEndpoint(candleData);
 }

		// ฟังก์ชันสำหรับส่งคำสั่งเทรด
function placeTradeV0(amount, duration, symbol, direction) {
    if (!websocket || websocket.readyState !== WebSocket.OPEN) {
        alert('กรุณาเชื่อมต่อก่อนทำการเทรด');
        return;
    }

    const request = {
        buy: 1,
        price: parseFloat(amount),
        parameters: {
            amount: parseFloat(amount),
            basis: "stake",
            contract_type: direction,
            currency: "USD",
            duration: parseInt(duration),
            duration_unit: "m",
            symbol: symbol
        }
    };

    console.log('ส่งคำสั่งเทรด:', request);
    websocket.send(JSON.stringify(request));
    
    // แสดงสถานะการส่งคำสั่ง
    updateTradeStatus('กำลังดำเนินการ...', 'pending');
}

		// ฟังก์ชันสำหรับส่งคำสั่งเทรด
function placeTrade() {

    if (!websocket || websocket.readyState !== WebSocket.OPEN) {
        alert('Not connected to server');
        return;
    }

    //const amount = document.getElementById('amount').value;
    //const duration = document.getElementById('duration').value;
	const amount = 15;
    const duration = 400;

    // ปรับพารามิเตอร์สำหรับการเทรดให้เหมาะสม
    const request = {
        buy: 1,
        price: parseFloat(amount),
        parameters: {
            amount: parseFloat(amount),
            basis: "stake",
            contract_type: 'CALL' ,
            currency: "USD",
            duration: parseInt(duration),
            duration_unit: "m",
            symbol: "frxEURUSD"
        }
    };

    
    websocket.send(JSON.stringify(request));
	console.log('Sending trade request 999:', request);
}
 


// จัดการข้อความที่ได้รับจาก WebSocket
function handleMessage(message) {
    try {
        const response = JSON.parse(message.data);
        console.log('ได้รับการตอบกลับ:', response);
        
        if (response.error) {
            // จัดการกรณีเกิดข้อผิดพลาด
            updateTradeStatus(`เกิดข้อผิดพลาด: ${response.error.message}`, 'error');
            return;
        }

        if (response.buy) {
			alert('Buy')
            // จัดการกรณีเทรดสำเร็จ
            displayTradeResult(response);
        }
    } catch (error) {
        console.error('เกิดข้อผิดพลาดในการประมวลผลข้อความ:', error);
    }
}

// แสดงผลลัพธ์การเทรด
function displayTradeResult(response) {

	console.log('Buy Response',response);
	
    const tradeResult = document.createElement('div');
    tradeResult.className = 'trade-result';
    
    const resultHTML = `
        <div class="result-card">
            <h2>ผลลัพธ์การเทรด</h2>
            <div class="result-details">
                <div class="detail-row">
                    <span class="label">รหัสการเทรด:</span>
                    <span class="value">${response.buy.transaction_id}</span>
                </div>
                <div class="detail-row">
                    <span class="label">ราคาซื้อ:</span>
                    <span class="value">${response.buy.buy_price} USD</span>
                </div>
                <div class="detail-row">
                    <span class="label">สถานะ:</span>
                    <span class="value status-success">สำเร็จ</span>
                </div>
                <div class="detail-row">
                    <span class="label">เวลาเริ่ม:</span>
                    <span class="value">${new Date(response.buy.start_time * 1000).toLocaleString()}</span>
                </div>
            </div>
        </div>
    `;
    
    tradeResult.innerHTML = resultHTML;
    
    // หา container หรือสร้างใหม่
    let container = document.getElementById('trade-result-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'trade-result-container';
        document.body.appendChild(container);
    }
    
    // ล้างผลลัพธ์เก่าและแสดงผลใหม่
    container.innerHTML = '';
    container.appendChild(tradeResult);
    
    // อัพเดทสถานะการเทรด
    updateTradeStatus('เทรดสำเร็จ', 'success');
}

// อัพเดทสถานะการเชื่อมต่อ
function updateConnectionStatus(message, status) {
    const statusDiv = document.getElementById('connection-status') || createStatusElement('connection-status');
    statusDiv.textContent = message;
    statusDiv.className = `status-message ${status}`;
}

// อัพเดทสถานะการเทรด
function updateTradeStatus(message, status) {
    const statusDiv = document.getElementById('trade-status') || createStatusElement('trade-status');
    statusDiv.textContent = message;
    statusDiv.className = `status-message ${status}`;
}

// สร้าง element สำหรับแสดงสถานะ
function createStatusElement(id) {
    const element = document.createElement('div');
    element.id = id;
    element.className = 'status-message';
    document.body.insertBefore(element, document.body.firstChild);
    return element;
}


async function sendToEndpoint(data) {
            try {
                const response = await fetch('https://lovetoshopmall.com/api/candles', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                document.getElementById('status').textContent = 'Data sent successfully at ' + new Date().toLocaleTimeString();
            } catch (error) {
                console.error('Error sending data:', error);
                document.getElementById('status').textContent = 'Error sending data: ' + error.message;
            } finally {
                isProcessing = false;
            }
        }

        // Event listeners
        document.querySelectorAll('input[name="timeframe"]').forEach(radio => {
            radio.addEventListener('change', function(e) {
                selectedTimeframe = parseInt(e.target.value);
                fetchCandles();
            });
        });

        document.getElementById('asset').addEventListener('change', function() {
            fetchCandles();
        });

        // Start the connection when the page loads
        window.addEventListener('load', connect);
		
		 

		
 </script>

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


<script>

$(document).ready(function () {
  
  doAjaxSymBols('GetSymBolGroup',symbolType='') 
});


async function doAjaxSymBols(Mode,symbolType) {
   return;
     
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


function getNextColor(AnalyDataTmp,index) {

          console.log(AnalyDataTmp[index+1]);
            
         return AnalyDataTmp[index+1].color ;

} // end func

function getSuggestColor999(AnalyDataTmp,index) {

	     if (AnalyDataTmp[index].emaAbove ==='3') {
			 return 'Green';
	     } else {
             return 'Red';
		 }


} // end func


function TradePlan1() {

let numWin = 0;
let numLoss = 0;
let lossCon = 0 ;
let WinStatus = '';
let balance = 0 ;
console.clear();
let monyTrade = [1,2,4,6,8];
let AnalyDataTmp = JSON.parse(document.getElementById("AnalyDataTxt").value);
for (let i=0;i<=AnalyDataTmp.length-1 ;i++ ) {
    if (AnalyDataTmp[i].CutPointType != '') {
		 
		TradeColor =  getSuggestColor999(AnalyDataTmp,i) ;
		nextColor = getNextColor(AnalyDataTmp,i);
		console.log(TradeColor,' vs ', nextColor);		
		thisID = "money_"+AnalyDataTmp[i].timestamp ;
		
		if (document.getElementById(thisID)) {	
          if (lossCon == 0) {
            thisMoneyTrade = 1;		    
		  } 
		  if (lossCon == 1) {
            thisMoneyTrade = 2;
		    
		  } 
		  if (lossCon == 2) {
            thisMoneyTrade = 4;
		    
		  } 
		  if (lossCon == 3) {
            thisMoneyTrade = 8;
		    
		  } 
		  if (lossCon == 4) {
            thisMoneyTrade = 16;
		  } 
		  if (lossCon >= 5) {
            thisMoneyTrade = 32;
		  } 
		  document.getElementById(thisID).innerHTML = thisMoneyTrade;			

		}



		if (TradeColor === nextColor) { 
           numWin++ ; WinStatus = 'Win' ; lossCon = 0;
		} else {
           numLoss++ ; WinStatus = 'Loss' ; lossCon++;
		}		
		thisID = "WinStatus_"+AnalyDataTmp[i].timestamp ;
		if (document.getElementById(thisID)) {		
		  document.getElementById(thisID).innerHTML = WinStatus;
		}
		thisID = "lossCon_"+AnalyDataTmp[i].timestamp ;
		if (document.getElementById(thisID)) {		
		  document.getElementById(thisID).innerHTML = lossCon;
		}
		
		thisID = "profit_"+AnalyDataTmp[i].timestamp ;
		if (document.getElementById(thisID)) {	
          if (WinStatus ==='Win') {
		    document.getElementById(thisID).innerHTML = thisMoneyTrade*0.95;
			balance +=  parseFloat(thisMoneyTrade*0.95);
			lossCon = 0;
		  } else {
            document.getElementById(thisID).innerHTML = -1* thisMoneyTrade;
			balance +=  parseFloat(thisMoneyTrade*-1);

		  }
		}
		thisID = "balance_"+AnalyDataTmp[i].timestamp ;
		document.getElementById(thisID).innerHTML = balance.toFixed(2);


		
		
    }// end if
	
} // end for
//alert(numWin+'-'+numLoss+ '-'+ balance.toFixed(2)) ;

document.getElementById("balanceResult").innerHTML = numWin+'-'+numLoss+ ' Balance= '+ balance.toFixed(2);


} // end func
function getSelectedRadioValue(radioGroupName) {
  const radios = document.getElementsByName(radioGroupName);
  for (let i = 0; i < radios.length; i++) {
    if (radios[i].checked) {
      return radios[i].value;
    }
  }
  return null; // or undefined, or an empty string, depending on your needs.
}

function saveToLocal() {

timeframeValue = getSelectedRadioValue("timeframe") ;


sObj = {
 asset:  document.getElementById("asset").value,
 timeframe: timeframeValue
}

localStorage.setItem('tradeDeriv',JSON.stringify(sObj));
} // end func


function getDataLocal() {

data = JSON.parse(localStorage.getItem('tradeDeriv'));

thisid = 'timeframe' + data.timeframe;
document.getElementById("asset").value = data.asset ;
document.getElementById(thisid).checked  = true;



} // end func

$(document).ready(function () {
  getDataLocal() 
});


</script>


</body>
</html>