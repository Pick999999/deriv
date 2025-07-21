<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signal Strength Meter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
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
    </style>
</head>
<body>
    <h1>R_100 Signal Strength Meter</h1>
    
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
                    count: 10,
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
                // Get the most recent candle
                const latestCandle = data.candles[data.candles.length - 1];
                updateLatestCandle(latestCandle);
            }
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
        }

        // Calculate signal strength based on candle data
        function calculateSignalStrength() {
            if (!lastCandleData) return;
            
            // Simple algorithm for signal strength
            // Range from -100 (strong sell) to +100 (strong buy)
            
            const open = parseFloat(lastCandleData.open);
            const close = parseFloat(lastCandleData.close);
            const high = parseFloat(lastCandleData.high);
            const low = parseFloat(lastCandleData.low);
            
            // Calculate price movement as percentage
            const candleBodySize = Math.abs(close - open);
            const candleTotalSize = high - low;
            
            // Direction of movement
            const direction = close > open ? 1 : -1;
            
            // Relative strength based on body size compared to total size
            const relativeStrength = candleTotalSize > 0 ? (candleBodySize / candleTotalSize) : 0;
            
            // Calculate signal (range -100 to 100)
            let signal = direction * relativeStrength * 100;
            
            // Add some technical indicators influence (simplified for demo)
            // Momentum factor based on previous signal
            const momentum = 0.3 * signalStrength;
            
            // Combine factors
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

        // Connect to WebSocket when page loads
        connectWebSocket();
        
        // Periodically recalculate signal strength for animation
        setInterval(() => {
            if (lastCandleData) {
                // Add small random variation for visual interest
                const randomFactor = Math.random() * 10 - 5;
                const newSignal = Math.max(-100, Math.min(100, signalStrength + randomFactor));
                updateSignalStrength(newSignal);
            }
        }, 2000);
    </script>
</body>
</html>