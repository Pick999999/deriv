<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv OHLC with Candlestick Chart</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
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
        #data-container {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
        }
        .granularity-info {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        .chart-container {
            height: 400px;
            margin-top: 20px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .layout {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .tab-container {
            display: flex;
            border-bottom: 1px solid #ddd;
        }
        .tab {
            padding: 10px 15px;
            cursor: pointer;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
            margin-right: 5px;
        }
        .tab.active {
            background-color: #fff;
            border-bottom: 1px solid #fff;
            margin-bottom: -1px;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv OHLC with Candlestick Chart</h1>
        
        <div class="control-panel">
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
		<div id="" class="bordergray flex">
		   <input type="text" id="priceInput" placeholder="ใส่ราคา">
            <button onclick="addPriceLine()">เพิ่ม Price Line</button>  
		</div>
        
        <div class="layout">
            <div class="chart-container" id="chart-container"></div>
            
            <div class="tab-container">
                <div class="tab active" data-tab="data">Data Table</div>
                <div class="tab" data-tab="code">Code Example</div>
            </div>
			
            
            <div class="tab-content active" data-content="data">
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
            </div>
            
            <div class="tab-content" data-content="code">
                <pre id="code-example" style="overflow: auto; max-height: 400px; background-color: #f5f5f5; padding: 10px; border-radius: 5px;"></pre>
            </div>
        </div>
    </div>
    
    <script>
        // CandleStickChart class to handle the charting functionality
        class CandleStickChart {
            constructor(containerId) {
                this.containerId = containerId;
                this.chartContainer = document.getElementById(containerId);
                this.chart = null;
                this.candleSeries = null;
                this.data = [];
                
                this.initChart();
            }
            
            initChart() {
                // Create chart instance
                this.chart = LightweightCharts.createChart(this.chartContainer, {
                    width: this.chartContainer.clientWidth,
                    height: this.chartContainer.clientHeight,
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
                        borderColor: '#ddd',
                        visible: true,
                    },
                    timeScale: {
                        borderColor: '#ddd',
                        timeVisible: true,
                        secondsVisible: true,
                    },
                });
                
                // Create candlestick series
                this.candleSeries = this.chart.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderVisible: false,
                    wickUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                });
                
                // Handle resize
                window.addEventListener('resize', () => {
                    if (this.chart) {
                        this.chart.applyOptions({
                            width: this.chartContainer.clientWidth,
                            height: this.chartContainer.clientHeight,
                        });
                    }
                });
            }
            
            // Update chart with new data
            updateData(candles) {
                if (!this.candleSeries) return;
                
                // Format data for lightweight-charts
                this.data = candles.map(candle => ({
                    time: candle.epoch,
                    open: parseFloat(candle.open),
                    high: parseFloat(candle.high),
                    low: parseFloat(candle.low),
                    close: parseFloat(candle.close),
                }));
                
                // Set data to chart
                this.candleSeries.setData(this.data);
                
                // Fit content to view
                this.chart.timeScale().fitContent();
            }
            
            // Add a single candle update
            addCandleUpdate(ohlc) {
                if (!this.candleSeries) return;
                
                const candleData = {
                    time: ohlc.epoch,
                    open: parseFloat(ohlc.open),
                    high: parseFloat(ohlc.high),
                    low: parseFloat(ohlc.low),
                    close: parseFloat(ohlc.close),
                };
                
                // Update existing candle or add new one
                this.candleSeries.update(candleData);
            }
            
            // Clear all data
            clearData() {
                if (this.candleSeries) {
                    this.data = [];
                    this.candleSeries.setData(this.data);
                }
            }
        }
        
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
            const codeExample = document.getElementById('code-example');
            
            // Initialize chart
            const candleChart = new CandleStickChart('chart-container');
            
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    
                    // Hide all tab content
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Show corresponding content
                    const tabName = tab.getAttribute('data-tab');
                    document.querySelector(`.tab-content[data-content="${tabName}"]`).classList.add('active');
                });
            });
            
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
            
            // Example code for the Code tab
            function updateCodeExample(symbol, granularity, startTime, endTime) {
                const codeText = `
// Example of how to use Deriv API to get OHLC data with JavaScript
const ws = new WebSocket('wss://ws.binaryws.com/websockets/v3');

ws.onopen = function() {
    const request = {
        ticks_history: '${symbol}',
        adjust_start_time: 1,
        count: 1000,
        granularity: ${granularity},
        start: ${startTime},
        end: ${endTime},
        style: 'candles'
    };
    
    ws.send(JSON.stringify(request));
};

ws.onmessage = function(msg) {
    const data = JSON.parse(msg.data);
    
    if (data.msg_type === 'candles') {
        console.log('Received candles:', data.candles);
        // Process candles data here
    }
};`;
                
                codeExample.textContent = codeText;
            }
            
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
                
                // Sort candles by epoch in descending order (newest first)
                candles.sort((a, b) => b.epoch - a.epoch);
                
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
                
                // Update chart with candles data
                candleChart.updateData(candles);
                
                loadingIndicator.style.display = 'none';
                updateStatus(`Received ${candles.length} historical candles`);
            }
            
            // Handle OHLC updates
            function handleOHLCUpdate(ohlc) {
                // Check if this candle already exists in the table
                const existingRows = dataBody.querySelectorAll('tr');
                let updated = false;
                
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
                
                // Update chart with new candle data
                candleChart.addCandleUpdate(ohlc);
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
                
                // Clear existing data
                dataBody.innerHTML = '';
                candleChart.clearData();
                
                // Update code example
                updateCodeExample(symbol, granularity, startTime, endTime);
                
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
</body>
</html>