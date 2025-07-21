<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Trading Dashboard</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>

	https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .connection-status {
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .connected {
            background: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
            border: 1px solid #4CAF50;
        }

        .disconnected {
            background: rgba(244, 67, 54, 0.2);
            color: #f44336;
            border: 1px solid #f44336;
        }

        .connecting {
            background: rgba(255, 152, 0, 0.2);
            color: #FF9800;
            border: 1px solid #FF9800;
        }

        .controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }

        select, input {
            padding: 12px;
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .chart-section:hover {
            transform: translateY(-5px);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f3f4;
        }

        .chart-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #2c3e50;
        }

        .timeframe-selector {
            display: flex;
            gap: 5px;
        }

        .timeframe-btn {
            padding: 8px 12px;
            border: 2px solid #e0e6ed;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .timeframe-btn:hover {
            background: #f8f9fa;
        }

        .timeframe-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .chart-container {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
        }

        .analysis-section {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .analyze-btn {
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .analyze-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .analyze-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .analysis-result {
            margin-top: 20px;
            padding: 20px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            border-left: 4px solid #667eea;
            text-align: left;
            display: none;
        }

        .price-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding: 10px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
            
            .controls {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📈 Deriv Trading Dashboard</h1>
        
        <div id="connectionStatus" class="connection-status connecting">
            🔄 Connecting to Deriv API...
        </div>
        
        <div class="controls">
            <div class="control-group">
                <label for="assetSelect">Asset Selection</label>
                <select id="assetSelect">
                    <option value="R_10">Volatility 10 Index</option>
                    <option value="R_25">Volatility 25 Index</option>
                    <option value="R_50">Volatility 50 Index</option>
                    <option value="R_75">Volatility 75 Index</option>
                    <option value="R_100">Volatility 100 Index</option>
                    <option value="BOOM1000">Boom 1000 Index</option>
                    <option value="CRASH1000">Crash 1000 Index</option>
                    <option value="JD10">Jump 10 Index</option>
                    <option value="JD25">Jump 25 Index</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="emaShort">EMA Short Period</label>
                <input type="number" id="emaShort" value="10" min="1" max="100">
            </div>
            
            <div class="control-group">
                <label for="emaLong">EMA Long Period</label>
                <input type="number" id="emaLong" value="20" min="1" max="200">
            </div>
        </div>

        <div class="charts-container">
            <div class="chart-section">
                <div class="chart-header">
                    <div class="chart-title">Higher Timeframe Chart</div>
                    <div class="timeframe-selector">
                        <button class="timeframe-btn" data-chart="1" data-timeframe="30">30s</button>
                        <button class="timeframe-btn active" data-chart="1" data-timeframe="15">15s</button>
                        <button class="timeframe-btn" data-chart="1" data-timeframe="10">10s</button>
                        <button class="timeframe-btn" data-chart="1" data-timeframe="5">5s</button>
                        <button class="timeframe-btn" data-chart="1" data-timeframe="1">1s</button>
                    </div>
                </div>
                <div id="chart1" class="chart-container"></div>
                <div id="priceInfo1" class="price-info">
                    <span>Last Price: --</span>
                    <span>Change: --</span>
                </div>
            </div>

            <div class="chart-section">
                <div class="chart-header">
                    <div class="chart-title">Lower Timeframe Chart</div>
                    <div class="timeframe-selector">
                        <button class="timeframe-btn" data-chart="2" data-timeframe="30">30s</button>
                        <button class="timeframe-btn" data-chart="2" data-timeframe="15">15s</button>
                        <button class="timeframe-btn" data-chart="2" data-timeframe="10">10s</button>
                        <button class="timeframe-btn active" data-chart="2" data-timeframe="5">5s</button>
                        <button class="timeframe-btn" data-chart="2" data-timeframe="1">1s</button>
                    </div>
                </div>
                <div id="chart2" class="chart-container"></div>
                <div id="priceInfo2" class="price-info">
                    <span>Last Price: --</span>
                    <span>Change: --</span>
                </div>
            </div>
        </div>

        <div class="analysis-section">
            <button class="analyze-btn" id="analyzeBtn" onclick="analyzeRelationship()">
                🔍 Analyze Relationship & Predict
            </button>
            <div id="analysisResult" class="analysis-result"></div>
        </div>
    </div>

    <script>
        // Global variables
        let ws;
        let chart1, chart2;
        let candlestickSeries1, candlestickSeries2;
        let emaShortSeries1, emaLongSeries1;
        let emaShortSeries2, emaLongSeries2;
        let chart1Data = [], chart2Data = [];
        let currentAsset = 'R_10';
        let timeframe1 = 15, timeframe2 = 5;
        let tickSubscription1 = null, tickSubscription2 = null;
        let candleBuffer1 = {}, candleBuffer2 = {};
        let lastPrices = {};

        // Deriv WebSocket connection
        function connectToDerivAPI() {
            const wsUrl = 'wss://ws.binaryws.com/websockets/v3?app_id=66726';
            ws = new WebSocket(wsUrl);
            
            ws.onopen = function() {
                updateConnectionStatus('connected', '✅ Connected to Deriv API');
                console.log('Connected to Deriv API');
                
                // Initialize with current asset
                subscribeToTicks();
            };
            
            ws.onmessage = function(event) {
                const data = JSON.parse(event.data);
                handleWebSocketMessage(data);
            };
            
            ws.onclose = function() {
                updateConnectionStatus('disconnected', '❌ Disconnected from Deriv API');
                console.log('Disconnected from Deriv API');
                
                // Attempt reconnection after 3 seconds
                setTimeout(connectToDerivAPI, 3000);
            };
            
            ws.onerror = function(error) {
                console.error('WebSocket error:', error);
                updateConnectionStatus('disconnected', '❌ Connection Error');
            };
        }

        // Update connection status
        function updateConnectionStatus(status, message) {
            const statusElement = document.getElementById('connectionStatus');
            statusElement.className = `connection-status ${status}`;
            statusElement.textContent = message;
            
            // Enable/disable analyze button based on connection
            const analyzeBtn = document.getElementById('analyzeBtn');
            analyzeBtn.disabled = status !== 'connected';
        }

        // Handle WebSocket messages
        function handleWebSocketMessage(data) {
            if (data.msg_type === 'tick' && data.tick) {
                processTick(data.tick);
            } else if (data.error) {
                console.error('API Error:', data.error);
            }
        }

        // Process incoming tick data
        function processTick(tick) {
            const symbol = tick.symbol;
            const price = parseFloat(tick.quote);
            const timestamp = parseInt(tick.epoch);
            
            // Store last price for price info display
            lastPrices[symbol] = {
                price: price,
                timestamp: timestamp
            };
            
            // Update price info displays
            updatePriceInfo();
            
            // Process tick for both charts
            if (symbol === currentAsset) {
                processTickForChart(1, price, timestamp);
                processTickForChart(2, price, timestamp);
            }
        }

        // Process tick for specific chart
        function processTickForChart(chartNum, price, timestamp) {
            const timeframe = chartNum === 1 ? timeframe1 : timeframe2;
            const buffer = chartNum === 1 ? candleBuffer1 : candleBuffer2;
            const chartData = chartNum === 1 ? chart1Data : chart2Data;
            
            // Calculate candle start time
            const candleStartTime = Math.floor(timestamp / timeframe) * timeframe;
            
            if (!buffer[candleStartTime]) {
                // New candle
                buffer[candleStartTime] = {
                    time: candleStartTime,
                    open: price,
                    high: price,
                    low: price,
                    close: price,
                    isComplete: false
                };
            } else {
                // Update existing candle
                buffer[candleStartTime].high = Math.max(buffer[candleStartTime].high, price);
                buffer[candleStartTime].low = Math.min(buffer[candleStartTime].low, price);
                buffer[candleStartTime].close = price;
            }
            
            // Check if candle is complete (next candle started)
            const currentCandleTime = Math.floor(Date.now() / 1000 / timeframe) * timeframe;
            
            // Update chart data
            const sortedCandles = Object.values(buffer)
                .sort((a, b) => a.time - b.time)
                .slice(-100); // Keep last 100 candles
            
            if (chartNum === 1) {
                chart1Data = sortedCandles;
                updateChart1(sortedCandles);
            } else {
                chart2Data = sortedCandles;
                updateChart2(sortedCandles);
            }
        }

        // Subscribe to tick data
        function subscribeToTicks() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                // Unsubscribe from previous subscriptions
                if (tickSubscription1) {
                    ws.send(JSON.stringify({
                        forget: tickSubscription1
                    }));
                }
                if (tickSubscription2) {
                    ws.send(JSON.stringify({
                        forget: tickSubscription2
                    }));
                }
                
                // Subscribe to new asset
                const subscribeMessage = {
                    ticks: currentAsset,
                    subscribe: 1
                };
                
                ws.send(JSON.stringify(subscribeMessage));
                
                // Clear existing buffers
                candleBuffer1 = {};
                candleBuffer2 = {};
                chart1Data = [];
                chart2Data = [];
                
                console.log('Subscribed to ticks for:', currentAsset);
            }
        }

        // Initialize charts
        function initCharts() {
            // Chart 1 (Higher timeframe)
            chart1 = LightweightCharts.createChart(document.getElementById('chart1'), {
                width: document.getElementById('chart1').clientWidth,
                height: 400,
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f3fa' },
                    horzLines: { color: '#f0f3fa' },
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                rightPriceScale: {
                    borderColor: '#cccccc',
                },
                timeScale: {
                    borderColor: '#cccccc',
                    timeVisible: true,
                },
            });

            candlestickSeries1 = chart1.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });

            emaShortSeries1 = chart1.addLineSeries({
                color: '#2196F3',
                lineWidth: 2,
                title: 'EMA Short',
            });

            emaLongSeries1 = chart1.addLineSeries({
                color: '#FF9800',
                lineWidth: 2,
                title: 'EMA Long',
            });

            // Chart 2 (Lower timeframe)
            chart2 = LightweightCharts.createChart(document.getElementById('chart2'), {
                width: document.getElementById('chart2').clientWidth,
                height: 400,
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f3fa' },
                    horzLines: { color: '#f0f3fa' },
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                rightPriceScale: {
                    borderColor: '#cccccc',
                },
                timeScale: {
                    borderColor: '#cccccc',
                    timeVisible: true,
                },
            });

            candlestickSeries2 = chart2.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });

            emaShortSeries2 = chart2.addLineSeries({
                color: '#2196F3',
                lineWidth: 2,
                title: 'EMA Short',
            });

            emaLongSeries2 = chart2.addLineSeries({
                color: '#FF9800',
                lineWidth: 2,
                title: 'EMA Long',
            });
        }

        // Update Chart 1
        function updateChart1(candleData) {
            if (candleData.length === 0) return;
            
            const emaShortPeriod = parseInt(document.getElementById('emaShort').value);
            const emaLongPeriod = parseInt(document.getElementById('emaLong').value);
            
            candlestickSeries1.setData(candleData);
            
            const emaShort = calculateEMA(candleData, emaShortPeriod);
            const emaLong = calculateEMA(candleData, emaLongPeriod);
            
            emaShortSeries1.setData(emaShort);
            emaLongSeries1.setData(emaLong);
        }

        // Update Chart 2
        function updateChart2(candleData) {
            if (candleData.length === 0) return;
            
            const emaShortPeriod = parseInt(document.getElementById('emaShort').value);
            const emaLongPeriod = parseInt(document.getElementById('emaLong').value);
            
            candlestickSeries2.setData(candleData);
            
            const emaShort = calculateEMA(candleData, emaShortPeriod);
            const emaLong = calculateEMA(candleData, emaLongPeriod);
            
            emaShortSeries2.setData(emaShort);
            emaLongSeries2.setData(emaLong);
        }

        // Calculate EMA
        function calculateEMA(data, period) {
            if (data.length === 0) return [];
            
            const ema = [];
            const multiplier = 2 / (period + 1);
            
            for (let i = 0; i < data.length; i++) {
                if (i === 0) {
                    ema.push({
                        time: data[i].time,
                        value: data[i].close
                    });
                } else {
                    const value = (data[i].close * multiplier) + (ema[i-1].value * (1 - multiplier));
                    ema.push({
                        time: data[i].time,
                        value: parseFloat(value.toFixed(5))
                    });
                }
            }
            
            return ema;
        }

        // Update price info displays
        function updatePriceInfo() {
            const priceData = lastPrices[currentAsset];
            if (!priceData) return;
            
            const price = priceData.price.toFixed(5);
            const priceInfo1 = document.getElementById('priceInfo1');
            const priceInfo2 = document.getElementById('priceInfo2');
            
            // Calculate simple change (this is simplified)
            const change = '0.00%'; // You can enhance this with proper calculation
            
            const priceHtml = `
                <span>Last Price: ${price}</span>
                <span>Change: ${change}</span>
            `;
            
            priceInfo1.innerHTML = priceHtml;
            priceInfo2.innerHTML = priceHtml;
        }

        // Handle timeframe button clicks
        function handleTimeframeClick(event) {
            const btn = event.target;
            const chartNum = btn.dataset.chart;
            const timeframe = parseInt(btn.dataset.timeframe);
            
            // Update active button
            document.querySelectorAll(`[data-chart="${chartNum}"]`).forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Update timeframe
            if (chartNum === '1') {
                timeframe1 = timeframe;
                candleBuffer1 = {};
                chart1Data = [];
            } else {
                timeframe2 = timeframe;
                candleBuffer2 = {};
                chart2Data = [];
            }
        }

        // Analyze relationship between charts
        function analyzeRelationship() {
            if (chart1Data.length < 10 || chart2Data.length < 10) {
                alert('Not enough data for analysis. Please wait for more data to accumulate.');
                return;
            }
            
            const analysisResult = document.getElementById('analysisResult');
            
            // Simple correlation analysis
            const recentData1 = chart1Data.slice(-10);
            const recentData2 = chart2Data.slice(-20);
            
            const trend1 = recentData1[recentData1.length - 1].close > recentData1[0].close ? 'Upward' : 'Downward';
            const trend2 = recentData2[recentData2.length - 1].close > recentData2[0].close ? 'Upward' : 'Downward';
            
            const volatility1 = calculateVolatility(recentData1);
            const volatility2 = calculateVolatility(recentData2);
            
            const correlation = trend1 === trend2 ? 'Positive' : 'Negative';
            
            // Generate prediction
            const prediction = generatePrediction(recentData1, recentData2);
            
            const currentPrice = lastPrices[currentAsset] ? lastPrices[currentAsset].price.toFixed(5) : 'N/A';
            
            analysisResult.innerHTML = `
                <h3>📊 Real-Time Analysis Results</h3>
                <p><strong>Asset:</strong> ${currentAsset}</p>
                <p><strong>Current Price:</strong> ${currentPrice}</p>
                <p><strong>Higher Timeframe (${timeframe1}s):</strong> ${trend1} trend, Volatility: ${volatility1.toFixed(4)}</p>
                <p><strong>Lower Timeframe (${timeframe2}s):</strong> ${trend2} trend, Volatility: ${volatility2.toFixed(4)}</p>
                <p><strong>Correlation:</strong> ${correlation} correlation detected</p>
                <h4>🔮 Prediction for Lower Timeframe:</h4>
                <p>${prediction}</p>
                <p><em>Analysis based on real Deriv.com market data. Use for educational purposes only.</em></p>
            `;
            
            analysisResult.style.display = 'block';
        }

        // Calculate volatility
        function calculateVolatility(data) {
            if (data.length < 2) return 0;
            
            const returns = [];
            for (let i = 1; i < data.length; i++) {
                returns.push((data[i].close - data[i-1].close) / data[i-1].close);
            }
            
            const mean = returns.reduce((sum, r) => sum + r, 0) / returns.length;
            const variance = returns.reduce((sum, r) => sum + Math.pow(r - mean, 2), 0) / returns.length;
            
            return Math.sqrt(variance);
        }

        // Generate prediction
        function generatePrediction(data1, data2) {
            if (data1.length === 0 || data2.length === 0) {
                return "Insufficient data for prediction.";
            }
            
            const lastPrice1 = data1[data1.length - 1].close;
            const lastPrice2 = data2[data2.length - 1].close;
            
            const trend1 = data1.length >= 5 ? data1[data1.length - 1].close > data1[data1.length - 5].close : false;
            const trend2 = data2.length >= 10 ? data2[data2.length - 1].close > data2[data2.length - 10].close : false;
            
            if (trend1 && trend2) {
                return `Based on the higher timeframe upward momentum, the lower timeframe is likely to continue its upward movement. Expected price range: ${(lastPrice2 * 1.001).toFixed(5)} - ${(lastPrice2 * 1.005).toFixed(5)}`;
            } else if (!trend1 && !trend2) {
                return `Both timeframes show downward pressure. Lower timeframe may continue declining. Expected price range: ${(lastPrice2 * 0.995).toFixed(5)} - ${(lastPrice2 * 0.999).toFixed(5)}`;
            } else {
                return `Mixed signals detected. Lower timeframe may experience consolidation or reversal. Monitor closely for breakout direction.`;
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            connectToDerivAPI();
            
            // Asset selection
            document.getElementById('assetSelect').addEventListener('change', function() {
                currentAsset = this.value;
                subscribeToTicks();
            });
            
            // EMA inputs
            document.getElementById('emaShort').addEventListener('input', function() {
                if (chart1Data.length > 0) updateChart1(chart1Data);
                if (chart2Data.length > 0) updateChart2(chart2Data);
            });
            
            document.getElementById('emaLong').addEventListener('input', function() {
                if (chart1Data.length > 0) updateChart1(chart1Data);
                if (chart2Data.length > 0) updateChart2(chart2Data);
            });
            
            // Timeframe buttons
            document.querySelectorAll('.timeframe-btn').forEach(btn => {
                btn.addEventListener('click', handleTimeframeClick);
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (chart1) chart1.applyOptions({ width: document.getElementById('chart1').clientWidth });
            if (chart2) chart2.applyOptions({ width: document.getElementById('chart2').clientWidth });
        });

        // Handle page unload
        window.addEventListener('beforeunload', function() {
            if (ws) {
                ws.close();
            }
        });
    </script>
</body>
</html>

สร้าง  html page + pure javascript โดยให้แสดง กราฟ candlestick ดังนี้ 
1.สร้างกราฟด้วย lightweightchart จาก https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js
2.มี listbox แสดงข้อมูล asset ของ deriv.com ให้เลือกสำหรับเลือก asset ทำงาน 
3.ให้ใช้ข้อมูล candle จริง ๆ จาก deriv.com โดยดึงข้อมูล candles ไม่เอา ohlc และดึงด้วย socket
4.มี กราฟ 2 อัน แต่ละอันให้เลือก  timeframe แยกกัน มี timeframe 1,5,10,15,30 Minute โดย แสดงค่า
candleStick,emaShort,emaLong โดย emaShort,emaLong มี  input ให้กรอกค่าได้
5. โดยปกติ chart แรกจะเป็น timeframe ใหญ่ และ chart ที่สอง จะเป็น timeframe ย่อย
6.มีปุ่มกดสำหรับ วิเคราะห์ ความสัมพันธ์ หรือเพื่อ ทำนาย graph จาก timeframe เล็กโดย อ้างจากกราฟ timeframe ใหญ่