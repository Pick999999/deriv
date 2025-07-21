<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Signal Strength Meter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 500px;
            max-width: 95%;
            margin-top: 20px;
        }
        
        .meter-container {
            position: relative;
            width: 100%;
            height: 250px;
        }
        
        .meter {
            position: absolute;
            width: 100%;
            height: 200px;
            top: 0;
        }
        
        .needle-container {
            position: absolute;
            width: 100%;
            height: 200px;
            top: 0;
            display: flex;
            justify-content: center;
        }
        
        .needle {
            position: absolute;
            bottom: 0;
            width: 4px;
            height: 180px;
            background-color: #ff3b3b;
            transform-origin: bottom center;
            transform: rotate(0deg);
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 10;
            border-radius: 2px;
        }
        
        .needle:after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: -8px;
            width: 20px;
            height: 20px;
            background-color: #ff3b3b;
            border-radius: 50%;
        }
        
        .status-panel {
            background-color: #1e1e1e;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        
        .data-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
        }
        
        .data-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .data-label {
            font-weight: bold;
            color: #9e9e9e;
        }
        
        .data-value {
            font-weight: bold;
        }
        
        .connection-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .connected {
            background-color: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
        }
        
        .disconnected {
            background-color: rgba(244, 67, 54, 0.2);
            color: #F44336;
        }
        
        .connecting {
            background-color: rgba(255, 152, 0, 0.2);
            color: #FF9800;
        }
        
        h1 {
            margin-bottom: 5px;
        }
        
        .subtitle {
            color: #9e9e9e;
            margin-top: 0;
            margin-bottom: 20px;
        }
        
        .strength-text {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
        
        .buy-signal {
            color: #4CAF50;
        }
        
        .sell-signal {
            color: #F44336;
        }
        
        .neutral-signal {
            color: #FFC107;
        }

        .values-container {
            position: absolute;
            width: 100%;
            height: 200px;
            top: 0;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .value-indicator {
            width: 40px;
            text-align: center;
            position: relative;
            height: 200px;
        }

        .value-indicator span {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #9e9e9e;
        }

        .table-container {
            width: 100%;
            margin-top: 30px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1e1e1e;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        th {
            background-color: #2a2a2a;
            color: #9e9e9e;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .bullish {
            color: #4CAF50;
        }

        .bearish {
            color: #F44336;
        }

        .doji {
            color: #FFC107;
        }
    </style>
</head>
<body>
    <h1>R_100 Signal Strength Meter</h1>
    <p class="subtitle">Live updates every 2 seconds</p>
    
    <div class="connection-status connecting" id="connectionStatus">Connecting...</div>
    
    <div class="container">
        <div class="meter-container">
            <svg class="meter" viewBox="0 0 200 100">
                <!-- Outer arc and tick marks -->
                <path d="M10,100 A90,90 0 0,1 190,100" stroke="#333" stroke-width="4" fill="none"/>
                
                <!-- Colored segments -->
                <path d="M10,100 A90,90 0 0,1 40,55" stroke="#F44336" stroke-width="8" fill="none"/>
                <path d="M40,55 A90,90 0 0,1 75,30" stroke="#FF9800" stroke-width="8" fill="none"/>
                <path d="M75,30 A90,90 0 0,1 125,30" stroke="#FFC107" stroke-width="8" fill="none"/>
                <path d="M125,30 A90,90 0 0,1 160,55" stroke="#8BC34A" stroke-width="8" fill="none"/>
                <path d="M160,55 A90,90 0 0,1 190,100" stroke="#4CAF50" stroke-width="8" fill="none"/>
                
                <!-- Tick marks -->
                <line x1="10" y1="100" x2="15" y2="93" stroke="#fff" stroke-width="2"/>
                <line x1="40" y1="55" x2="43" y2="47" stroke="#fff" stroke-width="2"/>
                <line x1="75" y1="30" x2="75" y2="22" stroke="#fff" stroke-width="2"/>
                <line x1="125" y1="30" x2="125" y2="22" stroke="#fff" stroke-width="2"/>
                <line x1="160" y1="55" x2="157" y2="47" stroke="#fff" stroke-width="2"/>
                <line x1="190" y1="100" x2="185" y2="93" stroke="#fff" stroke-width="2"/>
                
                <!-- Labels -->
                <text x="10" y="115" text-anchor="middle" fill="#9e9e9e" font-size="8">-100</text>
                <text x="40" y="70" text-anchor="middle" fill="#9e9e9e" font-size="8">-60</text>
                <text x="75" y="40" text-anchor="middle" fill="#9e9e9e" font-size="8">-20</text>
                <text x="100" y="25" text-anchor="middle" fill="#9e9e9e" font-size="8">0</text>
                <text x="125" y="40" text-anchor="middle" fill="#9e9e9e" font-size="8">20</text>
                <text x="160" y="70" text-anchor="middle" fill="#9e9e9e" font-size="8">60</text>
                <text x="190" y="115" text-anchor="middle" fill="#9e9e9e" font-size="8">100</text>
                
                <!-- Title -->
                <text x="100" y="85" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">SIGNAL STRENGTH</text>
            </svg>
            
            <div class="needle-container">
                <div class="needle" id="needle"></div>
            </div>
        </div>
        
        <div class="strength-text" id="strengthText">
            Waiting for data...
        </div>
        
        <div class="status-panel">
            <div class="data-row">
                <span class="data-label">Current Price:</span>
                <span class="data-value" id="currentPrice">-</span>
            </div>
            <div class="data-row">
                <span class="data-label">Signal Strength:</span>
                <span class="data-value" id="signalStrength">-</span>
            </div>
            <div class="data-row">
                <span class="data-label">Updated At:</span>
                <span class="data-value" id="updatedAt">-</span>
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Open</th>
                        <th>Close</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody id="candleTable">
                    <tr>
                        <td colspan="6" style="text-align: center;">Waiting for candle data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // WebSocket connection
            let ws;
            let candleData = [];
            let isConnected = false;
            
            // Elements
            const needle = document.getElementById('needle');
            const strengthText = document.getElementById('strengthText');
            const currentPrice = document.getElementById('currentPrice');
            const signalStrength = document.getElementById('signalStrength');
            const updatedAt = document.getElementById('updatedAt');
            const connectionStatus = document.getElementById('connectionStatus');
            const candleTable = document.getElementById('candleTable');
            
            // Initialize WebSocket connection
            function connectWebSocket() {
                // Use your app_id from Deriv API - for demo purposes, using a sample one
                ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
                
                ws.onopen = function(evt) {
                    isConnected = true;
                    connectionStatus.className = 'connection-status connected';
                    connectionStatus.textContent = 'Connected';
                    
                    // Subscribe to R_100 ticks
                    subscribeToCandles();
                    
                    // Set interval to check connection and refresh data
                    setInterval(function() {
                        if (isConnected) {
                            // Request new candle data every 2 seconds
                            subscribeToCandles();
                        } else {
                            reconnect();
                        }
                    }, 2000);
                };
                
                ws.onmessage = function(msg) {
                    const data = JSON.parse(msg.data);
                    
                    if (data.error) {
                        console.error('Error:', data.error.message);
                        return;
                    }
                    
                    // Handle candle data
                    if (data.msg_type === 'candles') {
                        processCandles(data.candles);
                    } else if (data.msg_type === 'ohlc') {
                        updateLatestCandle(data.ohlc);
                    }
                };
                
                ws.onclose = function() {
                    isConnected = false;
                    connectionStatus.className = 'connection-status disconnected';
                    connectionStatus.textContent = 'Disconnected';
                    setTimeout(reconnect, 1000);
                };
                
                ws.onerror = function(err) {
                    console.error('WebSocket Error:', err);
                    isConnected = false;
                    connectionStatus.className = 'connection-status disconnected';
                    connectionStatus.textContent = 'Connection Error';
                };
            }
            
            function reconnect() {
                if (!isConnected) {
                    connectionStatus.className = 'connection-status connecting';
                    connectionStatus.textContent = 'Reconnecting...';
                    connectWebSocket();
                }
            }
            
            function subscribeToCandles() {
                if (!isConnected) return;
                
                // Get historical candles for R_100
                ws.send(JSON.stringify({
                    ticks_history: 'R_100',
                    style: 'candles',
                    granularity: 60, // 1 minute candles
                    count: 10 // Last 10 candles
                }));
                
                // Subscribe to updates
                ws.send(JSON.stringify({
                    ticks_history: 'R_100',
                    style: 'candles',
                    granularity: 60,
                    subscribe: 1
                }));
            }
            
            function processCandles(candles) {
                if (!candles || candles.length === 0) return;
                
                // Store the candle data
                candleData = candles.slice(-10);  // Keep only the last 10 candles
                
                // Update UI with latest data
                updateUI();
                
                // Update the table
                updateCandleTable();
            }
            
            function updateLatestCandle(ohlc) {
                // Update the latest candle or add a new one
                if (candleData.length > 0 && candleData[candleData.length - 1].epoch === parseInt(ohlc.epoch)) {
                    candleData[candleData.length - 1] = {
                        epoch: parseInt(ohlc.epoch),
                        open: parseFloat(ohlc.open),
                        high: parseFloat(ohlc.high),
                        low: parseFloat(ohlc.low),
                        close: parseFloat(ohlc.close)
                    };
                } else {
                    // Add new candle and keep only the last 10
                    candleData.push({
                        epoch: parseInt(ohlc.epoch),
                        open: parseFloat(ohlc.open),
                        high: parseFloat(ohlc.high),
                        low: parseFloat(ohlc.low),
                        close: parseFloat(ohlc.close)
                    });
                    
                    if (candleData.length > 10) {
                        candleData.shift();
                    }
                }
                
                // Update UI with latest data
                updateUI();
                
                // Update the table
                updateCandleTable();
            }
            
            function calculateSignalStrength() {
                if (candleData.length < 5) return 0;
                
                // Extract the last 5 candles for calculations
                const lastCandles = candleData.slice(-5);
                
                // Calculate various technical indicators and signals
                
                // 1. Calculate trend direction (positive = bullish, negative = bearish)
                let trendScore = 0;
                for (let i = 1; i < lastCandles.length; i++) {
                    if (lastCandles[i].close > lastCandles[i-1].close) {
                        trendScore += 20;
                    } else if (lastCandles[i].close < lastCandles[i-1].close) {
                        trendScore -= 20;
                    }
                }
                
                // 2. Calculate candle sizes (larger = stronger signal)
                let candleSizeScore = 0;
                for (let i = 0; i < lastCandles.length; i++) {
                    const candleSize = Math.abs(lastCandles[i].close - lastCandles[i].open);
                    const priceRange = lastCandles[i].high - lastCandles[i].low;
                    
                    // Calculate normalized candle body size (0-10 score)
                    if (priceRange > 0) {
                        const bodySizeRatio = candleSize / priceRange;
                        candleSizeScore += (bodySizeRatio * 10) * (lastCandles[i].close > lastCandles[i].open ? 1 : -1);
                    }
                }
                candleSizeScore = candleSizeScore / lastCandles.length;
                
                // 3. Momentum calculation (accelerating price movements)
                let momentumScore = 0;
                if (lastCandles.length >= 3) {
                    const lastDiff = lastCandles[lastCandles.length-1].close - lastCandles[lastCandles.length-2].close;
                    const prevDiff = lastCandles[lastCandles.length-2].close - lastCandles[lastCandles.length-3].close;
                    
                    // If momentum is accelerating in either direction
                    if (Math.abs(lastDiff) > Math.abs(prevDiff)) {
                        momentumScore = 30 * (lastDiff > 0 ? 1 : -1);
                    }
                }
                
                // Combine all scores with weights
                let finalScore = (trendScore * 0.5) + (candleSizeScore * 3) + (momentumScore);
                
                // Cap the score between -100 and 100
                return Math.max(-100, Math.min(100, finalScore));
            }
            
            function updateUI() {
                if (candleData.length === 0) return;
                
                const latestCandle = candleData[candleData.length - 1];
                const signalStrengthValue = calculateSignalStrength();
                
                // Update the needle position (rotate from -90 to 90 degrees)
                const needleRotation = -90 + ((signalStrengthValue + 100) / 200 * 180);
                needle.style.transform = `rotate(${needleRotation}deg)`;
                
                // Update text labels
                currentPrice.textContent = latestCandle.close.toFixed(2);
                signalStrength.textContent = signalStrengthValue.toFixed(2);
                updatedAt.textContent = new Date(latestCandle.epoch * 1000).toLocaleTimeString();
                
                // Update strength text
                if (signalStrengthValue > 60) {
                    strengthText.textContent = "STRONG BUY";
                    strengthText.className = "strength-text buy-signal";
                } else if (signalStrengthValue > 20) {
                    strengthText.textContent = "BUY";
                    strengthText.className = "strength-text buy-signal";
                } else if (signalStrengthValue > -20) {
                    strengthText.textContent = "NEUTRAL";
                    strengthText.className = "strength-text neutral-signal";
                } else if (signalStrengthValue > -60) {
                    strengthText.textContent = "SELL";
                    strengthText.className = "strength-text sell-signal";
                } else {
                    strengthText.textContent = "STRONG SELL";
                    strengthText.className = "strength-text sell-signal";
                }
            }
            
            function updateCandleTable() {
                if (candleData.length === 0) return;
                
                // Clear the table
                candleTable.innerHTML = '';
                
                // Fill with candle data (newest first)
                const reversedData = [...candleData].reverse();
                
                reversedData.forEach(candle => {
                    const row = document.createElement('tr');
                    
                    // Determine candle type
                    let candleType = 'neutral';
                    let candleTypeText = 'Doji';
                    
                    if (candle.close > candle.open) {
                        candleType = 'bullish';
                        candleTypeText = 'Bullish';
                    } else if (candle.close < candle.open) {
                        candleType = 'bearish';
                        candleTypeText = 'Bearish';
                    }
                    
                    // Format time
                    const time = new Date(candle.epoch * 1000).toLocaleTimeString();
                    
                    row.innerHTML = `
                        <td>${time}</td>
                        <td>${candle.open.toFixed(2)}</td>
                        <td>${candle.close.toFixed(2)}</td>
                        <td>${candle.high.toFixed(2)}</td>
                        <td>${candle.low.toFixed(2)}</td>
                        <td class="${candleType}">${candleTypeText}</td>
                    `;
                    
                    candleTable.appendChild(row);
                });
            }
            
            // Start the WebSocket connection
            connectWebSocket();
        });
    </script>
</body>
</html>