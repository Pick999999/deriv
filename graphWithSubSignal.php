<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candle Chart</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
		#chartA-container {
            height: 300px;
            width: 100%;
			border:1px solid red;
			display:none;
        }
        #chart-container {
            height: 300px;
            width: 100%;
			border:1px solid red;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .time-display {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .status {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .diff-info {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }
		td { padding:5px; border:1px solid gray; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Deriv Candle Chart</h1>
            <div>
                <div class="time-display">Server Time: <span id="server-time">Loading...</span></div>
                <div class="diff-info">Price Difference: <span id="price-diff">-</span></div>
                <div style="margin-top: 10px;">
                    <label for="symbol-select">Symbol:</label>
                    <select id="symbol-select">
                        <option value="R_100">Volatility 100 Index</option>
                        <option value="R_50">Volatility 50 Index</option>
                        <option value="R_25">Volatility 25 Index</option>
                        <option value="R_75">Volatility 75 Index</option>
                    </select>
                    
                    <label for="timeframe-select">Timeframe:</label>
                    <select id="timeframe-select">
					    
                        <option value="1">1 Minute</option>
                        <option value="5">5 Minutes</option>
                        <option value="15">15 Minutes</option>
                        <option value="60">1 Hour</option>
                    </select>
                </div>
            </div>
			<div id="responsemsg" class="bordergray flex">
			     
			</div>
			<button type='button' id='' class='mBtn' onclick="initialize()">Start</button>
        </div>
        
		<div id="chartA-container"></div>
        <div id="chart-container"></div>
		<div id="tradeTable" class="bordergray flex">
		     
		</div>
        <div class="status" id="status">Connecting to Deriv API...</div>
		<button type='button' id='btnCall' onclick="placeTrade('CALL')">CALL</button>
		<button type='button' id='btnPut'  onclick="placeTrade('PUT')">PUT</button>

        <div class="status" id="debug"></div>
    </div>

    <script>
        // WebSocket connection and variables
        let ws;
		let chartA;
        let chart;
        let candleSeries;
        let markersSeries;
        let serverTimeOffset = 0;
        let selectedSymbol = 'R_100';
        let selectedTimeframe = 1; // Default 1 minute
        let ohlcData = [];
        let zeroSecondCandles = []; // Store 00 second candle info
        let isConnected = false;
        let requestId = 1;
        let activeSubscriptionId = null;
        let closePriceLine = null; // Variable to track the horizontal line
        let lastZeroSecondClose = null; // Store the last 00 second close price
		let contractRegister = [] 

        // Debug function
        function debug(message) {
            const debugElem = document.getElementById('debug');
            debugElem.textContent = message;
            console.log(message);
        }

        // Initialize chart
        function initializeChart() {

			chartA = LightweightCharts.createChart(document.getElementById('chartA-container'), {
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                rightPriceScale: {
                    borderColor: '#dcdee0',
                },
                timeScale: {
                    borderColor: '#dcdee0',
                    timeVisible: true,
                },
            });
            chart = LightweightCharts.createChart(document.getElementById('chart-container'), {
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                rightPriceScale: {
                    borderColor: '#dcdee0',
                },
                timeScale: {
                    borderColor: '#dcdee0',
                    timeVisible: true,
                },
            });

            candleSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });

            // Handle resize
            window.addEventListener('resize', () => {
                chart.applyOptions({
                    width: document.getElementById('chart-container').clientWidth,
                });
            });
        }

        // Connect to Deriv WebSocket API
        function connectWebSocket() {
            ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');

            ws.onopen = function() {
                isConnected = true;
                document.getElementById('status').textContent = 'Connected to Deriv API';
				const authRequest = {
                  authorize: 'lt5UMO6bNvmZQaR',
                  req_id: 1 // Request ID เพื่อติดตามการตอบกลับ
                };
                 ws.send(JSON.stringify(authRequest));
                
                // Get server time for synchronization
                const timeRequest = {
                    time: 1,
                    req_id: requestId++
                };
                
                ws.send(JSON.stringify(timeRequest));
                
                // Request candle data after connection
                requestCandleData();
            };

            ws.onclose = function() {
                isConnected = false;
                document.getElementById('status').textContent = 'Disconnected. Attempting to reconnect...';
                setTimeout(connectWebSocket, 5000); // Try to reconnect after 5 seconds
            };

            ws.onerror = function(error) {
                console.error('WebSocket Error:', error);
                document.getElementById('status').textContent = 'Error connecting to Deriv API';
            };

            ws.onmessage = function(event) {
                const data = JSON.parse(event.data);
                console.log('Data Msg Type ',data.msg_type);
				document.getElementById("responsemsg").innerHTML = data.msg_type;
				
                
                // Handle server time response
                if (data.msg_type === 'time') {
                    const serverTime = new Date(data.time * 1000);
                    serverTimeOffset = serverTime - new Date();
                    updateServerTime();
                }
                
                // Handle history data
                if (data.msg_type === 'history' || data.msg_type === 'candles') {
                    handleCandleData(data);
                }
                
                // Handle real-time updates
                if (data.msg_type === 'ohlc') {
                    handleOHLCUpdate(data.ohlc);
                }
                
                // Store subscription ID for future unsubscribe
                if (data.subscription && data.subscription.id) {
                    activeSubscriptionId = data.subscription.id;
                }
				if (data.msg_type === 'authorize') {
					/*
                    if (response.error) {
                       console.error('Authentication error:', response.error.message);
                       return;
                    }
					*/
					//console.log('Authentication successful');
					//console.log('Account info:', data.authorize);
					// ตอนนี้ App ของคุณ คุณสามารถเริ่มส่งคำขออื่นๆ ที่ต้องการ authentication
					// เช่น ดึงข้อมูลบัญชี, ทำการซื้อขาย, ฯลฯ					
                }
				if (data.msg_type === 'buy') {
					//response.buy
					 console.log('buy response',data);
					 console.log('ContractID=',data.buy.contract_id)
					 contractRegister.push(data.buy.contract_id);
				     startTrackTrade(data.buy.contract_id);
					 
				}
				if (data.msg_type === 'proposal_open_contract') {
					
					 console.log('proposal_open_contract response',data);
					 console.log('Proposal ContractID=',data.proposal_open_contract.contract_id);
					 found = false;
					 for (i=0;i<=contractRegister.length-1 ;i++ ) {
					  if (contractRegister[i]===data.proposal_open_contract.contract_id) {
					 	 found= true; break;
					  }					 
					 } // end for 
					 found = false;
					 
					 
					 if (!found) {					 					 
					   console.log(' Not Found') ;
					   createTable(data.proposal_open_contract);
					 } else {
					   console.log('Found') ;
                       UpdateTable(data.proposal_open_contract);
					 }
					 //createTableFromJSON(data.proposal_open_contract)
					 
				}
				
				
                
                // Log errors
                if (data.error) {
                    debug(`Error: ${data.error.code} - ${data.error.message}`);
                }
            };
        }

        // Format candle data for chart
        function formatCandleData(candle) {
            return {
                time: parseInt(candle.epoch || candle.time),
                open: parseFloat(candle.open),
                high: parseFloat(candle.high),
                low: parseFloat(candle.low),
                close: parseFloat(candle.close)
            };
        }
        
        // Handle candle data from history request
        function handleCandleData(data) {
            if (!data || (!data.candles && !data.history)) return;
            
            let candles = data.candles || data.history.prices;
            if (!candles || !candles.length) {
                debug("No candle data received");
                return;
            }
            
            debug(`Received ${candles.length} candles for ${selectedSymbol}`);
            
            // Reset zero second candles array when loading new data
            zeroSecondCandles = [];
            
            const formattedData = candles.map(candle => {
                // Handle different response formats
                if (typeof candle === 'object') {
                    return formatCandleData(candle);
                }
            }).filter(Boolean);
            
            if (formattedData.length > 0) {
                ohlcData = formattedData;
                candleSeries.setData(formattedData);
                
                // Find and mark candles at 00 seconds
                const markers = [];
                
                formattedData.forEach((candle, index) => {
                    const candleDate = new Date(candle.time * 1000);
                    
                    if (candleDate.getSeconds() === 0) {
                        // Store this candle as a zero second candle
                        zeroSecondCandles.push({
                            time: candle.time,
                            close: candle.close
                        });
                        
                        // Set this as the last zero second close
                        lastZeroSecondClose = candle.close;
                        
                        // If there's a previous 00-second candle, calculate price difference
                        let priceDiffText = '';
                        if (zeroSecondCandles.length > 1) {
                            const prevZeroCandle = zeroSecondCandles[zeroSecondCandles.length - 2];
                            const priceDiff = candle.close - prevZeroCandle.close;
                            const priceDiffPoints = priceDiff.toFixed(2);
                            const direction = priceDiff >= 0 ? '▲' : '▼';
                            const color = priceDiff >= 0 ? 'green' : 'red';
                            priceDiffText = `\n${direction} ${Math.abs(priceDiffPoints)} pts`;
                        }
                        
                        // Add marker for this 00-second candle
                        markers.push({
                            time: candle.time,
                            position: 'aboveBar',
                            color: '#2196F3',
                            shape: 'circle',
                            text: `${new Date(candle.time * 1000).toLocaleTimeString()}${priceDiffText}`
                        });
                    }
                });
                
                // Add markers to chart
                if (markers.length > 0) {
                    candleSeries.setMarkers(markers);
                }
                
                // Draw horizontal line for the last 00-second candle
                if (lastZeroSecondClose !== null) {
                    drawClosePriceLine(lastZeroSecondClose);
                    updatePriceDifferenceDisplay(0); // Reset difference display
                }
                
                // Subscribe to real-time updates after getting historical data
                subscribeToCandleUpdates();
            }
        }
        
        // Handle OHLC update
        function handleOHLCUpdate(ohlc) {
            if (!ohlc) return;
            //เปลี่ยน format จาก epoch-->time
            const newCandle = formatCandleData(ohlc);
            
            // Update the chart with new data
			// เพิ่มแท่งเทียนใหม่เข้าไป 
            candleSeries.update(newCandle);
            
            // Add to our data array if it's a new candle or update existing one
            const lastCandle = ohlcData.length > 0 ? ohlcData[ohlcData.length - 1] : null;
            
            if (!lastCandle || lastCandle.time !== newCandle.time) {
                // This is a new candle
                ohlcData.push(newCandle);
                
                // Check if this is a new candle at 00 seconds
                const candleDate = new Date(newCandle.time * 1000);
                if (candleDate.getSeconds() === 0) {
                    // Calculate price difference from last 00-second candle
                    let priceDiff = 0;
                    let priceDiffText = '';
                    
                    if (lastZeroSecondClose !== null) {
                        priceDiff = newCandle.close - lastZeroSecondClose;
                        const priceDiffPoints = priceDiff.toFixed(2);
                        const direction = priceDiff >= 0 ? '▲' : '▼';
                        const color = priceDiff >= 0 ? 'green' : 'red';
                        priceDiffText = `\n${direction} ${Math.abs(priceDiffPoints)} pts`;
                    }
                    
                    // Update the last zero second close price
                    lastZeroSecondClose = newCandle.close;
                    
                    // Store this zero second candle
                    zeroSecondCandles.push({
                        time: newCandle.time,
                        close: newCandle.close
                    });
                    
                    // Create marker for the new 00-second candle
                    const markers = candleSeries.markers() || [];
                    markers.push({
                        time: newCandle.time,
                        position: 'aboveBar',
                        color: '#2196F3',
                        shape: 'circle',
                        text: `${new Date(newCandle.time * 1000).toLocaleTimeString()}${priceDiffText}`
                    });
                    
                    // Update markers on chart
                    candleSeries.setMarkers(markers);
                    
                    // Draw horizontal line at the close price
                    drawClosePriceLine(newCandle.close);
                    
                    // Reset the price difference display
                    updatePriceDifferenceDisplay(0);
                    
                    debug(`New candle at 00 seconds, drawing line at close price: ${newCandle.close}`);
                } else if (lastZeroSecondClose !== null) {
                    // Not a 00-second candle but we can calculate current difference
                    const priceDiff = newCandle.close - lastZeroSecondClose;
                    updatePriceDifferenceDisplay(priceDiff);
                }
            } else {
                // Update the existing candle
                ohlcData[ohlcData.length - 1] = newCandle;
                
                // If we have a last zero second close, calculate live difference
                if (lastZeroSecondClose !== null) {
                    const priceDiff = newCandle.close - lastZeroSecondClose;
                    updatePriceDifferenceDisplay(priceDiff);
                }
            }
            /*
            debug(`Updated candle: ${new Date(newCandle.time * 1000).toISOString()} - O:${newCandle.open} H:${newCandle.high} L:${newCandle.low} C:${newCandle.close}`);
			*/
        }

        // Draw horizontal line at close price
        function drawClosePriceLine(closePrice) {
            // Remove previous line if exists
            if (closePriceLine) {
                candleSeries.removePriceLine(closePriceLine);
            }
            
            // Create new price line
            closePriceLine = candleSeries.createPriceLine({
                price: closePrice,
                color: '#2196F3',
                lineWidth: 2,
                lineStyle: LightweightCharts.LineStyle.Solid,
                axisLabelVisible: true,
                title: 'Close',
            });
        }

        // Update price difference display
        function updatePriceDifferenceDisplay(priceDiff) {
            const priceDiffElement = document.getElementById('price-diff');
            const priceDiffPoints = priceDiff.toFixed(2);
            const direction = priceDiff >= 0 ? '▲' : '▼';
            const color = priceDiff >= 0 ? 'green' : 'red';
            
            priceDiffElement.innerHTML = `<span style="color: ${color}">${direction} ${Math.abs(priceDiffPoints)} points</span>`;
        }

        // Request candle data
        function requestCandleData() {
            if (!isConnected) return;
            
            // Unsubscribe from previous subscription if exists
            if (activeSubscriptionId) {
                const forgetRequest = {
                    forget: activeSubscriptionId
                };
                ws.send(JSON.stringify(forgetRequest));
                activeSubscriptionId = null;
            }
            
            // Reset tracking variables
            lastZeroSecondClose = null;
            zeroSecondCandles = [];
            
            // Request historical data
            const historyRequest = {
                ticks_history: selectedSymbol,
                style: "candles",
                granularity: selectedTimeframe * 60, // Convert to seconds
                count: 60,
                end: "latest",
                req_id: requestId++
            };
            console.log('Send Request',historyRequest);
            
            
            debug(`Requesting candle data for ${selectedSymbol}, timeframe: ${selectedTimeframe}m`);
            ws.send(JSON.stringify(historyRequest));
            document.getElementById('status').textContent = `Fetching ${selectedSymbol} candle data...`;
        }
        
        // Subscribe to real-time candle updates
        function subscribeToCandleUpdates() {
            if (!isConnected) return;            
            const subscribeRequest = {
                ticks_history: selectedSymbol,
                style: "candles",
                granularity: selectedTimeframe * 60, // Convert to seconds
                count: 1,
                end: "latest",
                subscribe: 1,
                req_id: requestId++
            };
            
            ws.send(JSON.stringify(subscribeRequest));
            document.getElementById('status').textContent = `Streaming ${selectedSymbol} candle data (${selectedTimeframe}m)`;
        } 

		// ฟังก์ชันสำหรับติดตามการเทรด
		function startTrackTrade(contractId) {
		   const request = {
			  proposal_open_contract: 1,
			  contract_id: contractId,
			  subscribe: 1 // ขอ subscribe ข้อมูลเพื่อติดตามการเปลี่ยนแปลง
		   };

		   ws.send(JSON.stringify(request));
		   console.log(`Started tracking trade ${contractId}`);
		}


        // Update server time display
        function updateServerTime() {
            const now = new Date(new Date().getTime() + serverTimeOffset);
            document.getElementById('server-time').textContent = now.toISOString().replace('T', ' ').substr(0, 19);
            
            // Check if we're at 00 seconds to ensure we catch new candles
            if (now.getSeconds() === 0) {
                checkForNewCandleAtZeroSeconds();
            }
        }

        // Check if we need to create a new horizontal line
        function checkForNewCandleAtZeroSeconds() {
            if (ohlcData.length === 0) return;
            
            const lastCandle = ohlcData[ohlcData.length - 1];
            const lastCandleTime = new Date(lastCandle.time * 1000);
            const currentTime = new Date(new Date().getTime() + serverTimeOffset);
            
            // If the last candle is from the current minute (at 00 seconds)
            if (lastCandleTime.getMinutes() === currentTime.getMinutes() && 
                lastCandleTime.getHours() === currentTime.getHours() && 
                lastCandleTime.getDate() === currentTime.getDate() && 
                lastCandleTime.getMonth() === currentTime.getMonth() && 
                lastCandleTime.getFullYear() === currentTime.getFullYear()) {
                
                // Draw the close price line
                drawClosePriceLine(lastCandle.close);
                debug(`Drawing close price line at ${lastCandle.close} for time: ${lastCandleTime.toISOString()}`);
            }
        }

        // Change the symbol or timeframe
        function changeParameters() {
            if (isConnected) {
                // Remove current price line when changing parameters
                if (closePriceLine) {
                    candleSeries.removePriceLine(closePriceLine);
                    closePriceLine = null;
                }
                
                // Reset markers
                candleSeries.setMarkers([]);
                
                // Reset price difference display
                document.getElementById('price-diff').textContent = '-';
                
                requestCandleData();
            }
        }

        // Refresh data periodically (every 2 seconds)
        function setupDataRefresh() {
            setInterval(() => {
                if (isConnected && !activeSubscriptionId) {
                    // Only request data if not subscribed
                    requestCandleData();
                }
            }, 2000);
        }

        // Initialize application
        function initialize() {
            initializeChart();
            connectWebSocket();
            
            // Update time display every second
            setInterval(updateServerTime, 1000);
            
            // Setup refresh
            setupDataRefresh();
            
            // Setup symbol selector
            document.getElementById('symbol-select').addEventListener('change', function() {
                selectedSymbol = this.value;
                changeParameters();
            });
            
            // Setup timeframe selector
            document.getElementById('timeframe-select').addEventListener('change', function() {
                selectedTimeframe = parseInt(this.value);
                changeParameters();
            });
        } 


		function placeTrade(contractType) {

		   if (!ws || ws.readyState !== WebSocket.OPEN) {
			  alert('Not connected to server');
			  return;
		   }
		   if (contractType === 'Idle') {
			  return;
		   }
  
		   const amount = 1 ; //parseFloat(document.getElementById("moneyTrade").value);
		   symbol = document.getElementById("symbol-select").value ;
		   const duration = 2 ; //getRadioValue('timeframe');

		   // ปรับพารามิเตอร์สำหรับการเทรดให้เหมาะสม
		   const request = {
			  buy: 1,
			  price: parseFloat(amount),
			  parameters: {
				 amount: parseFloat(amount),
				 basis: "stake",
				 contract_type: contractType,
				 currency: "USD",
				 duration: parseInt(duration),
				 duration_unit: "t",
				 symbol: symbol
			  }
		   };

           ws.send(JSON.stringify(request));
           console.log('Sending trade request :', request);
        }

function createTable(jsonObj) {
let no =1 ;

captionList ='ลำดับ,เลขสัญญา,contract_type,ราคาเข้าซื้อ,ราคาปัจจุบัน,สิ้นสุด,ผล,กำไร' ; 
captionAr = captionList.split(',');
valueList = [no,jsonObj.contract_id,jsonObj.contract_type,
            jsonObj.entry_spot,jsonObj.current_spot,jsonObj.is_sold,jsonObj.status,
	        jsonObj.profit 
	
] ;
st = '<table border=1>'; st += '<tr>';
for (i=0;i<=captionAr.length-1 ;i++ ) {
   st += '<td>' + captionAr[i] + '</td>';
} 
st += '</tr>';



		 st += '<tr>';
		 for (i=0;i<=valueList.length-1 ;i++ ) {
		   st += '<td>' + valueList[i]+ '</td>';
		 } // end for 
		 st += '</tr>';
		 st += '</table>'; 

		 console.log(st) ;
		 

		 document.getElementById("tradeTable").innerHTML = st;
		 
} // end func

 
function UpdateTable(jsonObj) {


} // end func
 


// Start the application
 //   document.addEventListener('DOMContentLoaded', initialize);
    </script>
</body>
</html>