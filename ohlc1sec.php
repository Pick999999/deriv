
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv OHLC Historical Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            color: #2a3f5f;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .control-panel {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .time-cell {
            text-align: left;
        }
        button {
            background-color: #2a3f5f;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #1f3050;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .status-bar {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #2a3f5f;
            margin: 15px 0;
        }
        .loading {
            display: none;
            margin-left: 10px;
        }
        input, select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: inline-block;
            width: 130px;
        }
		#chart-container {
            width: 100%;
            height: 300px;
            border: 1px solid #ddd;
			margin: 20px;
			background-color: #f5f5f5;
        }
        #data-container {
            max-height: 500px;
            overflow-y: auto;
        }
        .granularity-info {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
    </style>
	
</head>
<body>
    <div class="container">
        <h1>Deriv OHLC Historical Data</h1>
        
        <div class="control-panel" style='display:flex'>
            <div class="form-group">
                <label for="symbol">Symbol:</label>
                <input id="symbol" type="text" value="R_100" placeholder="e.g. R_100">
            </div>
            
            <div class="form-group">
                <label for="count">Count (max 5000):</label>
                <input id="count" type="number" value="100" min="1" max="5000">
            </div>
            
            <div class="form-group">
                <label for="granularity">Granularity:</label>
                <select id="granularity">
                    <option value="60">1 minute</option>
                    <option value="120">2 minutes</option>
                    <option value="300">5 minutes</option>
                    <option value="600">10 minutes</option>
                    <option value="900">15 minutes</option>
                    <option value="1800">30 minutes</option>
                    <option value="3600">1 hour</option>
                    <option value="7200">2 hours</option>
                    <option value="14400">4 hours</option>
                    <option value="28800">8 hours</option>
                    <option value="86400">1 day</option>
                </select>
                <div class="granularity-info">Note: Granularity values less than 60 seconds may not be supported for historical data</div>
            </div>
            
            <div class="form-group">
                <label for="start-date">Start Date:</label>
                <input id="start-date" type="date">
            </div>
            
            <div class="form-group">
                <label for="end-date">End Date:</label>
                <input id="end-date" type="date">
            </div>
            
            <button id="fetch-btn">Fetch Data</button>
            <button id="stop-btn" disabled>Stop Stream</button>
            <span id="loading" class="loading">Loading...</span>
        </div>
        
        <div id="status-bar" class="status-bar">
            Status: Ready to connect
        </div>
		<div id="" class="bordergray flex" style='margin-bottom:10px'>
		   <input type="text" id="priceInput" placeholder="ใส่ราคา">
            <button onclick="chart.addPriceLine()">เพิ่ม Price Line</button>  
		</div>

		
		
		<div id="data-container">
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Open</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Close</th>
                    </tr>
                </thead>
                <tbody id="data-body"></tbody>
            </table>
        </div>
		<div id="chart-container" style='margin-bottom:100px'>
			
		</div>
		<div id="" class="bordergray flex" style='height:100px;background:red'>
		     
		</div>

        
        
    </div>

	<textarea id="candle-data" style='width:100%;height:300px' placeholder="Paste your candle data here in JSON format..."></textarea>
     
    
    
    <script>
	    //let chart = null;
	
        document.addEventListener('DOMContentLoaded', () => {
            // DOM Elements
            const symbolInput = document.getElementById('symbol');
            const countInput = document.getElementById('count');
            const granularitySelect = document.getElementById('granularity');
            const startDateInput = document.getElementById('start-date');
            const endDateInput = document.getElementById('end-date');
            const fetchButton = document.getElementById('fetch-btn');
            const stopButton = document.getElementById('stop-btn');
            const statusBar = document.getElementById('status-bar');
            const dataBody = document.getElementById('data-body');
            const loadingIndicator = document.getElementById('loading');
			const textarea = document.getElementById('candle-data');
			chart = new CandleStickChartWithEMA('chart-container');			            
			let allData = [];
			
            
            // Set default dates (yesterday noon to today)
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            yesterday.setHours(12, 0, 0, 0);
            startDateInput.valueAsDate = yesterday;
            
            const today = new Date();
            endDateInput.valueAsDate = today;
            
            // API Connection variables
            const apiUrl = 'wss://ws.binaryws.com/websockets/v3?app_id=66726';
            let connection = null;
            let subscriptionId = null;
            
            // Format timestamp to readable date/time
            function formatTime(timestamp) {
                const date = new Date(timestamp * 1000);
                return date.toLocaleString();
            }
            
            // Update status message
            function updateStatus(message) {
                statusBar.textContent = `Status: ${message}`;
            } 

			
            
            // Handle candles data display
            function handleCandlesData(candles) {
                dataBody.innerHTML = '';
                candles.forEach(candle => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="time-cell">${formatTime(candle.epoch)}</td>
                        <td>${candle.open}</td>
                        <td>${candle.high}</td>
                        <td>${candle.low}</td>
                        <td>${candle.close}</td>
                    `;
                    dataBody.appendChild(row);
                });
                chart.updateChart(candles);
                loadingIndicator.style.display = 'none';
                updateStatus(`Received ${candles.length} historical candles`);
            }
            
            // Handle OHLC updates
            function handleOHLCUpdate(ohlc) {
                // Check if this candle already exists in the table
                const existingRows = dataBody.querySelectorAll('tr');
                let updated = false;

				ss = {
                          time : ohlc.epoch,
                          open : ohlc.open,
                          high : ohlc.high,
						  low :  ohlc.low,
						  close : ohlc.close,
				}
				if (allData.length > 100) {
				  //allData = [...allData.slice(0,9)] ;
				}	
				allData.push(ss);
				chart.updateChart(allData);
                
                for (let i = 0; i < existingRows.length; i++) {
                    const firstCell = existingRows[i].querySelector('td');
                    if (firstCell && firstCell.textContent === formatTime(ohlc.epoch)) {
                        // Update existing row
                        existingRows[i].innerHTML = `
                            <td class="time-cell">${formatTime(ohlc.epoch)}</td>
                            <td>${ohlc.open}</td>
                            <td>${ohlc.high}</td>
                            <td>${ohlc.low}</td>
                            <td>${ohlc.close}</td>
                        `;
                        updated = true;
                        break;
                    }
                }
                
                // Add new row if candle doesn't exist
                if (!updated) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="time-cell">${formatTime(ohlc.epoch)}</td>
                        <td>${ohlc.open}</td>
                        <td>${ohlc.high}</td>
                        <td>${ohlc.low}</td>
                        <td>${ohlc.close}</td>
                    `;
                    dataBody.prepend(row);
                }
            }
            
            // Initialize WebSocket connection
            function initConnection() {
                if (connection) {
                    connection.close();
                }
                
                connection = new WebSocket(apiUrl);
                
                connection.onopen = () => {
                    updateStatus('Connected to Deriv API');
                    fetchButton.disabled = false;
                };
                
                connection.onclose = () => {
                    updateStatus('Disconnected from Deriv API');
                    fetchButton.disabled = false;
                    stopButton.disabled = true;
                    loadingIndicator.style.display = 'none';
                };
                
                connection.onerror = (error) => {
                    updateStatus(`Error: ${error.message || 'Connection failed'}`);
                    fetchButton.disabled = false;
                    loadingIndicator.style.display = 'none';
                };
                
                connection.onmessage = (msg) => {
                    const data = JSON.parse(msg.data);
                    console.log('Received data:', data); // Debug log
                    
                    // Handle ticks history response
                    if (data.msg_type === 'candles') {
                        handleCandlesData(data.candles);
                    }
                    
                    // Handle subscription stream
                    if (data.msg_type === 'ohlc') {
                        handleOHLCUpdate(data.ohlc);
						exampleData = JSON.stringify(data.ohlc) ;
						
						
                    }
                    
                    // Store subscription ID for cancellation
                    if (data.subscription && data.subscription.id) {
                        subscriptionId = data.subscription.id;
                    }
                    
                    // Handle errors
                    if (data.error) {
                        updateStatus(`API Error: ${data.error.message}`);
                        console.error('API Error:', data.error);
                        loadingIndicator.style.display = 'none';
                        fetchButton.disabled = false;
                    }
                };
            }
            
            // Initialize connection on page load
            initConnection();
            
            // Event: Fetch button clicked
            fetchButton.addEventListener('click', () => {
                const symbol = symbolInput.value;
                const count = parseInt(countInput.value);
                const granularity = parseInt(granularitySelect.value);
                
                if (!symbol || !count) {
                    updateStatus('Please enter a valid symbol and count');
                    return;
                }
                
                if (count > 5000) {
                    updateStatus('Count cannot exceed 5000');
                    return;
                }
                
                const startDate = startDateInput.valueAsDate;
                const endDate = endDateInput.valueAsDate;
                
                if (!startDate || !endDate) {
                    updateStatus('Please select both start and end dates');
                    return;
                }
                
                // Convert dates to Unix timestamps
                const startTime = Math.floor(startDate.getTime() / 1000);
                const endTime = Math.floor(endDate.getTime() / 1000);
                
                dataBody.innerHTML = '';
                fetchButton.disabled = true;
                loadingIndicator.style.display = 'inline-block';
                updateStatus(`Requesting OHLC data for ${symbol}...`);
                
                // Check if connection is closed
                if (connection.readyState !== WebSocket.OPEN) {
                    initConnection();
                    setTimeout(() => fetchData(symbol, count, granularity, startTime, endTime), 1000);
                } else {
                    fetchData(symbol, count, granularity, startTime, endTime);
                }
            });
            
            function fetchData(symbol, count, granularity, startTime, endTime) {
                // Request OHLC data
				startTime = startTime+ (60*10);
				endTime = startTime+ (60*60);

                const request = {
                    ticks_history: symbol,
                    adjust_start_time: 1,
                    count: count,
                    granularity: granularity,
                    start: startTime,
                    end: endTime,
                    style: 'candles',
                    subscribe: 1  // Subscribe to updates
                };
                
                console.log('Sending request:', request); // Debug log
                connection.send(JSON.stringify(request));
                stopButton.disabled = false;
            }
            
            // Event: Stop button clicked
            stopButton.addEventListener('click', () => {
                if (subscriptionId) {
                    const request = {
                        forget: subscriptionId
                    };
                    connection.send(JSON.stringify(request));
                    updateStatus('Subscription stopped');
                    subscriptionId = null;
                }
                stopButton.disabled = true;
            });
            
            // Handle page unload
            window.addEventListener('beforeunload', () => {
                if (connection) {
                    connection.close();
                }
            });
        });
	


			
	 
  </script>

  
  
  <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
 <script src="chartObj.js"></script>

 <script>
        

        // Initialize chart when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            const chart = new CandleStickChartWithEMA('chart-container');			
            const textarea = document.getElementById('candle-data');
            
             
            
            // Example data button
            document.getElementById('example-data').addEventListener('click', () => {
                exampleData = JSON.parse(localStorage.getItem('exampleData')) ;
                textarea.value = JSON.stringify(exampleData, null, 2);
                chart.updateChart(exampleData);
				//chart2.updateChart(exampleData);
            });
        });
    </script>
  

</body>
</html>