<!-- 
ทำการดึงข้อมูล candle จาก deriv.com แล้วนำมาหาค่า  adx พร้อมทั้ง  วิเคราะห์ว่า trend 
เป็น Up,Down,Sideway โดยให้ มี  list ของ asset คือ R_10,R_25
,R_50,R_75,R_100 แล้วแสดงผลในรูป  html table พร้อม ปุ่ม ในแต่ละ  asset ซึ่งเมื่อสนใจ asset อันไหน ก็จะคลิกปุ่มของ asset นั้นๆ ก็จะทำการ
ดึง ข้อมูล candle ของ asset อันนั้น มาวาดกราฟ  candlestick + ema3+ema5  ด้วย 
<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
โดย ให้ ทำการ refresh data และ graph ทุก 2 seconds ทั้งหมด ทำด้วย pure javascript
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Real-time Candle Data with ADX</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 5px 10px;
            cursor: pointer;
        }
        #chartContainer {
            width: 100%;
            height: 500px;
            margin-top: 20px;
        }
        .up-trend {
            color: green;
            font-weight: bold;
        }
        .down-trend {
            color: red;
            font-weight: bold;
        }
        .sideway-trend {
            color: blue;
            font-weight: bold;
        }
        .status {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .connected {
            color: green;
        }
        .disconnected {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Deriv Real-time Candle Data with ADX</h1>
    
    <div id="connectionStatus" class="status">
        Connection status: <span id="statusText" class="disconnected">Disconnected</span>
    </div>
    
    <table id="assetTable">
        <thead>
            <tr>
                <th>Asset</th>
                <th>ADX Value</th>
                <th>Trend</th>
                <th>Trend Strength</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="assetTableBody">
            <!-- Data will be populated here -->
        </tbody>
    </table>
    
    <div id="chartContainer"></div>
    
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <script>
        // List of assets to analyze
        const assets = ['R_10', 'R_25', 'R_50', 'R_75', 'R_100'];
        
        // ADX calculation parameters
        const ADX_PERIOD = 14;
        
        // Chart variables
        let chart = null;
        let candleSeries = null;
        let ema3Series = null;
        let ema5Series = null;
        let currentAsset = null;
        
        // WebSocket variables
        let socket = null;
        const API_URL = "wss://ws.binaryws.com/websockets/v3?app_id=66726";
        let subscribedSymbols = new Set();
        let candleData = {};
        let assetSubscriptions = {};
        
        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            initializeTable();
            connectWebSocket();
        });
        
        // WebSocket connection
        function connectWebSocket() {
            updateStatus('Connecting...');
            
            socket = new WebSocket(API_URL);
            
            socket.onopen = function() {
                updateStatus('Connected', 'connected');
                // First, get initial candle history for all assets
                fetchInitialCandleHistory();
            };
            
            socket.onclose = function() {
                updateStatus('Disconnected', 'disconnected');
                // Attempt to reconnect after 3 seconds
                setTimeout(connectWebSocket, 3000);
            };
            
            socket.onerror = function(error) {
                updateStatus('Connection error', 'disconnected');
                console.error('WebSocket error:', error);
            };
            
            socket.onmessage = function(msg) {
                const response = JSON.parse(msg.data);
                handleWebSocketResponse(response);
            };
        }
        
        function updateStatus(text, className) {
            const statusElement = document.getElementById('statusText');
            statusElement.textContent = text;
            statusElement.className = className || 'disconnected';
        }
        
        // Initialize the asset table
        function initializeTable() {
            const tableBody = document.getElementById('assetTableBody');
            tableBody.innerHTML = '';
            
            assets.forEach(asset => {
                candleData[asset] = [];
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${asset}</td>
                    <td class="adx-value">-</td>
                    <td class="trend">-</td>
                    <td class="strength">-</td>
                    <td><button onclick="showAssetChart('${asset}')">View Chart</button></td>
                `;
                tableBody.appendChild(row);
            });
        }
        
        // Fetch initial candle history for all assets
        function fetchInitialCandleHistory() {
            assets.forEach(asset => {
                const request = {
                    ticks_history: asset,
                    style: "candles",
                    count: 100,
                    granularity: 60, // 1-minute candles
					end: "latest",
                    subscribe: 1,
                };
                socket.send(JSON.stringify(request));
            });
			/*
			    ticks_history: selectedSymbol,
                style: "candles",
                granularity: selectedTimeframe * 60, // Convert to seconds
                count: 60,
                end: "latest",
                req_id: requestId++
			*/
        }
        
        // Handle WebSocket responses
        function handleWebSocketResponse(response) {
            if (response.msg_type === 'candles') {
                handleInitialCandleData(response);
            } else if (response.msg_type === 'ohlc') {
                handleLiveCandleUpdate(response);
            } else if (response.error) {
                console.error('API error:', response.error);
            }
        }
        
        // Handle initial candle data response
        function handleInitialCandleData(response) {
            const symbol = response.echo_req.ticks_history;
            
            if (response.candles) {
                candleData[symbol] = response.candles.map(candle => ({
                    time: candle.epoch,
                    open: candle.open,
                    high: candle.high,
                    low: candle.low,
                    close: candle.close,
                }));
                
                // Update table with initial data
                updateAssetData(symbol);
                
                // If this is the current asset being viewed, update the chart
                if (symbol === currentAsset) {
                    updateChartData();
                }
            }
        }
        
        // Handle live candle updates
        function handleLiveCandleUpdate(response) {
            const symbol = response.ohlc.symbol;
            const candle = {
                time: response.ohlc.epoch,
                open: response.ohlc.open,
                high: response.ohlc.high,
                low: response.ohlc.low,
                close: response.ohlc.close,
            };
            
            // Initialize if not exists
            if (!candleData[symbol]) {
                candleData[symbol] = [];
            }
            
            // Check if we already have this candle (for update)
            const existingIndex = candleData[symbol].findIndex(c => c.time === candle.time);
            if (existingIndex >= 0) {
                candleData[symbol][existingIndex] = candle;
            } else {
                candleData[symbol].push(candle);
                // Keep only the last 100 candles
                if (candleData[symbol].length > 100) {
                    candleData[symbol] = candleData[symbol].slice(-100);
                }
            }
            
            // Update table with new data
            updateAssetData(symbol);
            
            // Update chart if this is the current asset
            if (symbol === currentAsset) {
                updateChartData();
            }
        }
        
        // Update asset data in the table
        function updateAssetData(symbol) {
            const candles = candleData[symbol];
            if (!candles || candles.length < ADX_PERIOD * 2) return;
            
            const adxData = calculateADX(candles, ADX_PERIOD);
            const latestADX = adxData[adxData.length - 1];
            
            const rows = document.querySelectorAll('#assetTableBody tr');
            const row = Array.from(rows).find(r => r.cells[0].textContent === symbol);
            
            if (!row) return;
            
            const trend = determineTrend(latestADX, candles);
            const strength = determineStrength(latestADX);
            
            row.querySelector('.adx-value').textContent = latestADX.toFixed(2);
            
            const trendCell = row.querySelector('.trend');
            trendCell.textContent = trend.label;
            trendCell.className = 'trend ' + trend.class;
            
            const strengthCell = row.querySelector('.strength');
            strengthCell.textContent = strength.label;
            strengthCell.className = 'strength ' + strength.class;
        }
        
        // Determine the trend based on ADX and price movement
        function determineTrend(adxValue, candles) {
            if (adxValue < 20) {
                return { label: 'Sideway', class: 'sideway-trend' };
            }
            
            // Check if recent candles show upward or downward movement
            const lookback = Math.min(5, candles.length);
            const recentCandles = candles.slice(-lookback);
            const priceChanges = recentCandles.map(c => c.close - c.open);
            const sumChanges = priceChanges.reduce((sum, change) => sum + change, 0);
            
            if (sumChanges > 0) {
                return { label: 'Up', class: 'up-trend' };
            } else {
                return { label: 'Down', class: 'down-trend' };
            }
        }
        
        // Determine the strength of the trend based on ADX value
        function determineStrength(adxValue) {
            if (adxValue < 20) {
                return { label: 'Weak or No Trend', class: 'sideway-trend' };
            } else if (adxValue < 40) {
                return { label: 'Strong', class: 'up-trend' };
            } else if (adxValue < 60) {
                return { label: 'Very Strong', class: 'up-trend' };
            } else {
                return { label: 'Extremely Strong', class: 'up-trend' };
            }
        }
        
        // Show chart for a specific asset
        function showAssetChart(asset) {
            currentAsset = asset;
            
            // Initialize chart if not already done
            if (!chart) {
                chart = LightweightCharts.createChart(document.getElementById('chartContainer'), {
                    width: document.getElementById('chartContainer').clientWidth,
                    height: 500,
                    layout: {
                        backgroundColor: '#ffffff',
                        textColor: '#333',
                    },
                    grid: {
                        vertLines: {
                            color: '#eee',
                        },
                        horzLines: {
                            color: '#eee',
                        },
                    },
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                    },
                    rightPriceScale: {
                        borderVisible: false,
                    },
                    timeScale: {
                        borderVisible: false,
                        timeVisible: true,
                        secondsVisible: false
                    },
                });
                
                candleSeries = chart.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderDownColor: '#ef5350',
                    borderUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                    wickUpColor: '#26a69a',
                });
                
                ema3Series = chart.addLineSeries({
                    color: 'rgba(255, 165, 0, 1)',
                    lineWidth: 2,
                });
                
                ema5Series = chart.addLineSeries({
                    color: 'rgba(0, 123, 255, 1)',
                    lineWidth: 2,
                });
            }
            
            // Set chart title
            chart.applyOptions({
                title: `${asset} - Real-time Candlestick with EMA3 & EMA5`,
            });
            
            // Initial data load
            updateChartData();
        }
        
        // Update chart data
        function updateChartData() {
            if (!currentAsset || !candleData[currentAsset]) return;
            
            const candles = candleData[currentAsset];
            if (candles.length === 0) return;
            
            // Prepare data for the chart
            const candleDataForChart = candles.map(c => ({
                time: c.time,
                open: c.open,
                high: c.high,
                low: c.low,
                close: c.close,
            }));
            
            // Calculate EMAs
            const ema3 = calculateEMA(candles, 3);
            const ema5 = calculateEMA(candles, 5);
            
            // Update the chart
            candleSeries.setData(candleDataForChart);
            ema3Series.setData(ema3.map((value, index) => ({
                time: candles[index].time,
                value: value,
            })));
            ema5Series.setData(ema5.map((value, index) => ({
                time: candles[index].time,
                value: value,
            })));
            
            // Adjust time scale to fit data
            chart.timeScale().fitContent();
        }
        
        // Calculate EMA (Exponential Moving Average)
        function calculateEMA(candles, period) {
            const ema = [];
            const multiplier = 2 / (period + 1);
            
            // Simple Moving Average for the first value
            let sum = 0;
            for (let i = 0; i < period && i < candles.length; i++) {
                sum += candles[i].close;
            }
            ema[period - 1] = sum / period;
            
            // EMA for subsequent values
            for (let i = period; i < candles.length; i++) {
                ema[i] = (candles[i].close - ema[i - 1]) * multiplier + ema[i - 1];
            }
            
            return ema;
        }
        
        // Calculate ADX (Average Directional Index)
        function calculateADX(candles, period) {
            const adx = [];
            const plusDM = [];
            const minusDM = [];
            const TR = [];
            
            // Calculate +DM, -DM, and TR for each period
            for (let i = 1; i < candles.length; i++) {
                const upMove = candles[i].high - candles[i - 1].high;
                const downMove = candles[i - 1].low - candles[i].low;
                
                plusDM[i] = upMove > downMove && upMove > 0 ? upMove : 0;
                minusDM[i] = downMove > upMove && downMove > 0 ? downMove : 0;
                
                TR[i] = Math.max(
                    candles[i].high - candles[i].low,
                    Math.abs(candles[i].high - candles[i - 1].close),
                    Math.abs(candles[i].low - candles[i - 1].close)
                );
            }
            
            // Calculate smoothed +DM, -DM, and TR
            const smoothedPlusDM = [];
            const smoothedMinusDM = [];
            const smoothedTR = [];
            
            // Initial values (simple sum)
            let sumPlusDM = 0;
            let sumMinusDM = 0;
            let sumTR = 0;
            
            for (let i = 1; i <= period; i++) {
                sumPlusDM += plusDM[i] || 0;
                sumMinusDM += minusDM[i] || 0;
                sumTR += TR[i] || 0;
            }
            
            smoothedPlusDM[period] = sumPlusDM;
            smoothedMinusDM[period] = sumMinusDM;
            smoothedTR[period] = sumTR;
            
            // Subsequent values (smoothed)
            for (let i = period + 1; i < candles.length; i++) {
                smoothedPlusDM[i] = smoothedPlusDM[i - 1] - (smoothedPlusDM[i - 1] / period) + (plusDM[i] || 0);
                smoothedMinusDM[i] = smoothedMinusDM[i - 1] - (smoothedMinusDM[i - 1] / period) + (minusDM[i] || 0);
                smoothedTR[i] = smoothedTR[i - 1] - (smoothedTR[i - 1] / period) + (TR[i] || 0);
            }
            
            // Calculate +DI and -DI
            const plusDI = [];
            const minusDI = [];
            
            for (let i = period; i < candles.length; i++) {
                plusDI[i] = (smoothedPlusDM[i] / smoothedTR[i]) * 100;
                minusDI[i] = (smoothedMinusDM[i] / smoothedTR[i]) * 100;
            }
            
            // Calculate DX and ADX
            const DX = [];
            
            for (let i = period; i < candles.length; i++) {
                const diDiff = Math.abs(plusDI[i] - minusDI[i]);
                const diSum = plusDI[i] + minusDI[i];
                DX[i] = (diDiff / diSum) * 100;
            }
            
            // First ADX value is simple average of first 'period' DX values
            let sumDX = 0;
            for (let i = period; i < period * 2 && i < DX.length; i++) {
                if (DX[i]) sumDX += DX[i];
            }
            adx[period * 2 - 1] = sumDX / period;
            
            // Subsequent ADX values are smoothed
            for (let i = period * 2; i < candles.length; i++) {
                adx[i] = ((adx[i - 1] * (period - 1)) + (DX[i] || 0)) / period;
            }
            
            return adx;
        }
        
        // Clean up when page is closed
        window.addEventListener('beforeunload', function() {
            if (socket) {
                socket.close();
            }
        });
    </script>
</body>
</html>