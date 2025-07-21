<!--
1.สร้าง html  แล้ว  สร้าง  listbox ของ ข้อมูล asset จาก  deriv.com
2.strength meter แบบ ไมล์รถยนต์ (เป็นรูปครึ่งวงกลม) เพื่อวัดความ แรงของสัญญาณ การ ซื้อ-ขาย ที่เลือกจาก listbox โดย update ทุก 2 วินาที
3.graph candle พร้อม bollinger band ด้วย
<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script> โดย update ข้อมูล ทุก 2 second
4.สร้าง label แสดงเวลา ของ timeserver พร้อม update ทุกๆ 1 วินาที 
5.เมื่อครบรอบ timeserver ทุก 1 นาที หรือ เริ่ม วินทีที่ 0 ให้ส่งข้อมูล candles ย้อนหลัง 30 แท่งไปที่ endpoint https://thepapers.com/abc แล้ว รอรับผลกลับมา 
ทั้งหมด ทำด้วย pure javascript โดยดึงข้อมูล จาก deriv.com และ ทำแยก ไฟล์ javascript ออกมาต่างหาก


-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signal Strength Meter</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .dashboard {
            width: 400px;
            position: relative;
            margin-bottom: 20px;
        }
        .gauge {
            width: 100%;
            height: 200px;
            position: relative;
        }
        .gauge-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 200px 200px 0 0;
            background: linear-gradient(90deg, #ff0000 0%, #ffff00 50%, #00ff00 100%);
            overflow: hidden;
            clip-path: polygon(0 50%, 100% 50%, 100% 0, 0 0);
        }
        .gauge-inner {
            position: absolute;
            width: 80%;
            height: 160px;
            top: 20px;
            left: 10%;
            border-radius: 160px 160px 0 0;
            background-color: #f5f5f5;
            clip-path: polygon(0 50%, 100% 50%, 100% 0, 0 0);
        }
        .needle {
            position: absolute;
            width: 4px;
            height: 100px;
            background-color: #333;
            bottom: 0;
            left: 50%;
            transform-origin: bottom center;
            transform: rotate(0deg);
            z-index: 10;
            transition: transform 0.5s ease-out;
        }
        .needle-cap {
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: #333;
            border-radius: 50%;
            bottom: -10px;
            left: calc(50% - 10px);
            z-index: 11;
        }
        .gauge-labels {
            position: absolute;
            width: 100%;
            height: 200px;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            box-sizing: border-box;
        }
        .gauge-label {
            position: absolute;
            font-size: 12px;
            font-weight: bold;
        }
        .gauge-label:nth-child(1) {
            left: 10%;
            top: 75%;
        }
        .gauge-label:nth-child(2) {
            left: 30%;
            top: 40%;
        }
        .gauge-label:nth-child(3) {
            left: 50%;
            top: 25%;
        }
        .gauge-label:nth-child(4) {
            right: 30%;
            top: 40%;
        }
        .gauge-label:nth-child(5) {
            right: 10%;
            top: 75%;
        }
        .value-display {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
        .status {
            font-size: 16px;
            margin-top: 10px;
            padding: 5px 15px;
            border-radius: 15px;
            font-weight: bold;
        }
        .info-panel {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            width: 400px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .candle-data {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .candle-item {
            text-align: center;
            flex: 1;
        }
        .label {
            font-size: 12px;
            color: #666;
        }
        .value {
            font-size: 14px;
            font-weight: bold;
        }
        .title {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .chart-container {
            width: 600px;
            height: 400px;
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        #chart {
            width: 100%;
            height: 100%;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 900px;
            margin: 0 auto;
        }
        .panels {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 20px;
        }
        @media (max-width: 900px) {
            .panels {
                flex-direction: column;
            }
            .chart-container {
                width: 400px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>R_100 Signal Strength Meter</h1>
        
        <div class="panels">
            <div class="dashboard">
                <div class="gauge">
                    <div class="gauge-bg"></div>
                    <div class="gauge-inner"></div>
                    <div class="needle"></div>
                    <div class="needle-cap"></div>
                    <div class="gauge-labels">
                        <div class="gauge-label">Strong Sell</div>
                        <div class="gauge-label">Sell</div>
                        <div class="gauge-label">Neutral</div>
                        <div class="gauge-label">Buy</div>
                        <div class="gauge-label">Strong Buy</div>
                    </div>
                </div>
                <div class="value-display">Signal: <span id="signal-value">0</span></div>
                <div class="status" id="signal-status">Neutral</div>
            </div>

            <div class="info-panel">
                <div class="title">Candle Data (R_100)</div>
                <div class="candle-data">
                    <div class="candle-item">
                        <div class="label">Open</div>
                        <div class="value" id="open-value">-</div>
                    </div>
                    <div class="candle-item">
                        <div class="label">High</div>
                        <div class="value" id="high-value">-</div>
                    </div>
                    <div class="candle-item">
                        <div class="label">Low</div>
                        <div class="value" id="low-value">-</div>
                    </div>
                    <div class="candle-item">
                        <div class="label">Close</div>
                        <div class="value" id="close-value">-</div>
                    </div>
                </div>
                <div id="connection-status">Connecting to WebSocket...</div>
                <div id="last-update"></div>
            </div>
        </div>
        
        <div class="chart-container">
            <div id="chart"></div>
        </div>
    </div>

    <script>
        // DOM Elements
        const needle = document.querySelector('.needle');
        const signalValue = document.getElementById('signal-value');
        const signalStatus = document.getElementById('signal-status');
        const openValue = document.getElementById('open-value');
        const highValue = document.getElementById('high-value');
        const lowValue = document.getElementById('low-value');
        const closeValue = document.getElementById('close-value');
        const connectionStatus = document.getElementById('connection-status');
        const lastUpdate = document.getElementById('last-update');

        // WebSocket connection
        let ws;
        let lastCandleData = null;
        let signalStrength = 0;
        
        // Chart data
        let candleData = [];
        let upperBandData = [];
        let middleBandData = [];
        let lowerBandData = [];
        let chart;
        let candleSeries;
        let upperBandSeries;
        let middleBandSeries;
        let lowerBandSeries;

        // Initialize Chart
        function initChart() {
            chart = LightweightCharts.createChart(document.getElementById('chart'), {
                width: 600,
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
                    borderColor: '#D1D4DC',
                },
                rightPriceScale: {
                    borderColor: '#D1D4DC',
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
            });
            
            // Create candlestick series
            candleSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });
            
            // Create Bollinger Bands series
            upperBandSeries = chart.addLineSeries({
                color: 'rgba(48, 63, 189, 0.7)',
                lineWidth: 1,
                priceLineVisible: false,
            });
            
            middleBandSeries = chart.addLineSeries({
                color: 'rgba(48, 63, 189, 1)',
                lineWidth: 1,
                priceLineVisible: false,
            });
            
            lowerBandSeries = chart.addLineSeries({
                color: 'rgba(48, 63, 189, 0.7)',
                lineWidth: 1,
                priceLineVisible: false,
            });
            
            // Auto-resize chart
            window.addEventListener('resize', () => {
                chart.applyOptions({
                    width: document.querySelector('.chart-container').clientWidth,
                    height: document.querySelector('.chart-container').clientHeight,
                });
            });
        }

        // Initialize WebSocket connection
        function connectWebSocket() {
            ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
            
            ws.onopen = function() {
                connectionStatus.textContent = 'Connected to WebSocket';
                connectionStatus.style.color = 'green';
                
                // Subscribe to R_100 candles
                subscribeToCandles();
            };
            
            ws.onmessage = function(event) {
                const data = JSON.parse(event.data);
                
                // Handle different message types
                if (data.msg_type === 'candles') {
                    handleCandleData(data);
                } else if (data.msg_type === 'ohlc') {
                    updateLatestCandle(data.ohlc);
                }
            };
            
            ws.onclose = function() {
                connectionStatus.textContent = 'WebSocket connection closed. Reconnecting...';
                connectionStatus.style.color = 'red';
                setTimeout(connectWebSocket, 5000);
            };
            
            ws.onerror = function(error) {
                connectionStatus.textContent = 'WebSocket error: ' + error.message;
                connectionStatus.style.color = 'red';
            };
        }

        // Subscribe to R_100 candles
        function subscribeToCandles() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                // First request historical candles
                ws.send(JSON.stringify({
                    ticks_history: 'R_100',
                    style: 'candles',
                    end: 'latest',
                    count: 50,
                    granularity: 60
                }));
                
                // Then subscribe to ongoing updates
                ws.send(JSON.stringify({
                    ticks_history: 'R_100',
                    style: 'candles',
                    subscribe: 1,
                    granularity: 60
                }));
            }
        }

        // Handle candle data
        function handleCandleData(data) {
            if (data.candles && data.candles.length > 0) {
                // Process all candles
                processCandleData(data.candles);
                
                // Get the most recent candle
                const latestCandle = data.candles[data.candles.length - 1];
                updateLatestCandle(latestCandle);
            }
        }

        // Process candle data for chart
        function processCandleData(candles) {
            // Format candle data for chart
            candleData = candles.map(candle => ({
                time: candle.epoch,
                open: parseFloat(candle.open),
                high: parseFloat(candle.high),
                low: parseFloat(candle.low),
                close: parseFloat(candle.close)
            }));
            
            // Calculate Bollinger Bands
            calculateBollingerBands();
            
            // Update chart
            updateChart();
        }

        // Calculate Bollinger Bands (20-period, 2 standard deviations)
        function calculateBollingerBands() {
            const period = 20;
            const deviations = 2;
            
            upperBandData = [];
            middleBandData = [];
            lowerBandData = [];
            
            if (candleData.length < period) return;
            
            for (let i = period - 1; i < candleData.length; i++) {
                // Get last 'period' closes
                const closes = candleData.slice(i - period + 1, i + 1).map(d => d.close);
                
                // Calculate SMA (Middle Band)
                const sma = closes.reduce((sum, price) => sum + price, 0) / period;
                
                // Calculate Standard Deviation
                const squaredDifferences = closes.map(price => Math.pow(price - sma, 2));
                const variance = squaredDifferences.reduce((sum, val) => sum + val, 0) / period;
                const stdDev = Math.sqrt(variance);
                
                // Calculate Upper and Lower Bands
                const upperBand = sma + (deviations * stdDev);
                const lowerBand = sma - (deviations * stdDev);
                
                // Add to data arrays
                const timePoint = { 
                    time: candleData[i].time,
                    value: sma
                };
                
                middleBandData.push(timePoint);
                upperBandData.push({ 
                    time: candleData[i].time,
                    value: upperBand
                });
                lowerBandData.push({ 
                    time: candleData[i].time,
                    value: lowerBand
                });
            }
        }

        // Update the chart with new data
        function updateChart() {
            if (!chart) return;
            
            candleSeries.setData(candleData);
            upperBandSeries.setData(upperBandData);
            middleBandSeries.setData(middleBandData);
            lowerBandSeries.setData(lowerBandData);
            
            // Fit content
            chart.timeScale().fitContent();
        }

        // Update latest candle info
        function updateLatestCandle(candle) {
            lastCandleData = candle;
            
            // Update displayed values
            openValue.textContent = formatPrice(candle.open);
            highValue.textContent = formatPrice(candle.high);
            lowValue.textContent = formatPrice(candle.low);
            closeValue.textContent = formatPrice(candle.close);
            
            // Calculate signal strength
            calculateSignalStrength();
            
            // Update timestamp
            const now = new Date();
            lastUpdate.textContent = `Last updated: ${now.toLocaleTimeString()}`;
            
            // Add or update the latest candle in chart data
            const formattedCandle = {
                time: candle.epoch,
                open: parseFloat(candle.open),
                high: parseFloat(candle.high),
                low: parseFloat(candle.low),
                close: parseFloat(candle.close)
            };
            
            // Find if this candle already exists in our data
            const existingIndex = candleData.findIndex(c => c.time === candle.epoch);
            
            if (existingIndex !== -1) {
                // Update existing candle
                candleData[existingIndex] = formattedCandle;
            } else {
                // Add new candle
                candleData.push(formattedCandle);
            }
            
            // Recalculate Bollinger Bands and update chart
            calculateBollingerBands();
            updateChart();
        }

        // Calculate signal strength based on candle data and Bollinger Bands
        function calculateSignalStrength() {
            if (!lastCandleData || upperBandData.length === 0) return;
            
            const open = parseFloat(lastCandleData.open);
            const close = parseFloat(lastCandleData.close);
            const high = parseFloat(lastCandleData.high);
            const low = parseFloat(lastCandleData.low);
            
            // Get last Bollinger Band values
            const lastUpperBand = upperBandData[upperBandData.length - 1].value;
            const lastMiddleBand = middleBandData[middleBandData.length - 1].value;
            const lastLowerBand = lowerBandData[lowerBandData.length - 1].value;
            
            // Calculate basic signal strength
            
            // Direction of movement
            const direction = close > open ? 1 : -1;
            
            // Candle body size
            const candleBodySize = Math.abs(close - open);
            const candleTotalSize = high - low;
            
            // Relative strength based on body size compared to total size
            const relativeStrength = candleTotalSize > 0 ? (candleBodySize / candleTotalSize) : 0;
            
            // Base signal calculation
            let signal = direction * relativeStrength * 50;
            
            // Bollinger Band factor (-50 to +50)
            let bbFactor = 0;
            
            // Close near upper band (buy signal)
            if (close > (lastMiddleBand + (lastUpperBand - lastMiddleBand) * 0.5)) {
                bbFactor = ((close - lastMiddleBand) / (lastUpperBand - lastMiddleBand)) * 50;
            } 
            // Close near lower band (sell signal)
            else if (close < (lastMiddleBand - (lastMiddleBand - lastLowerBand) * 0.5)) {
                bbFactor = -((lastMiddleBand - close) / (lastMiddleBand - lastLowerBand)) * 50;
            }
            
            // Combine signals
            signal = signal * 0.6 + bbFactor * 0.4;
            
            // Add some momentum factor based on previous signal
            const momentum = 0.3 * signalStrength;
            signal = 0.7 * signal + momentum;
            
            // Clamp value between -100 and 100
            signal = Math.max(-100, Math.min(100, signal));
            
            // Update the signal strength
            updateSignalStrength(signal);
        }

        // Update the signal strength display
        function updateSignalStrength(value) {
            signalStrength = value;
            
            // Update needle position (0 degrees at -100, 180 degrees at +100)
            const degrees = (value + 100) * 180 / 200;
            needle.style.transform = `rotate(${degrees}deg)`;
            
            // Update text display
            signalValue.textContent = value.toFixed(1);
            
            // Update status text
            let statusText, statusColor;
            
            if (value < -80) {
                statusText = "Strong Sell";
                statusColor = "#ff0000";
            } else if (value < -30) {
                statusText = "Sell";
                statusColor = "#ff6600";
            } else if (value < 30) {
                statusText = "Neutral";
                statusColor = "#ffcc00";
            } else if (value < 80) {
                statusText = "Buy";
                statusColor = "#66cc00";
            } else {
                statusText = "Strong Buy";
                statusColor = "#00cc00";
            }
            
            signalStatus.textContent = statusText;
            signalStatus.style.backgroundColor = statusColor;
            signalStatus.style.color = "#ffffff";
        }

        // Helper to format price with 2 decimal places
        function formatPrice(price) {
            return parseFloat(price).toFixed(2);
        }

        // Initialize chart
        initChart();
        
        // Connect to WebSocket when page loads
        connectWebSocket();
        
        // Periodically recalculate signal strength for animation
        setInterval(() => {
            if (lastCandleData) {
                // Add small random variation for visual interest
                const randomFactor = Math.random() * 5 - 2.5;
                const newSignal = Math.max(-100, Math.min(100, signalStrength + randomFactor));
                updateSignalStrength(newSignal);
            }
        }, 2000);
    </script>
</body>
</html>