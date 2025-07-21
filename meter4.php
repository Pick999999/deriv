<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv.com Asset Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .panel {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .strength-meter {
            margin-top: 20px;
            text-align: center;
        }
        .meter {
            position: relative;
            width: 200px;
            height: 100px;
            margin: 0 auto;
        }
        .meter-bg {
            position: absolute;
            width: 200px;
            height: 100px;
            border-radius: 100px 100px 0 0;
            overflow: hidden;
            background: #e0e0e0;
        }
        .meter-fill {
            position: absolute;
            width: 100%;
            height: 0;
            bottom: 0;
            background: linear-gradient(to right, #ff0000, #ffff00, #00ff00);
            transition: height 0.5s;
        }
        .meter-pointer {
            position: absolute;
            width: 2px;
            height: 20px;
            background: #333;
            bottom: 0;
            left: 50%;
            transform-origin: bottom center;
            transform: rotate(-90deg);
            transition: transform 0.5s;
        }
        .meter-labels {
            display: flex;
            justify-content: space-between;
            width: 200px;
            margin: 5px auto 0;
        }
        .meter-value {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
        }
        #chart-container {
            height: 400px;
            width: 100%;
        }
        .server-time {
            text-align: center;
            font-size: 18px;
            margin-bottom: 10px;
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="panel">
            <h2>Asset Selection</h2>
            <select id="asset-list" size="10"></select>
            
            <div class="strength-meter">
                <h2>Signal Strength</h2>
                <div class="meter">
                    <div class="meter-bg">
                        <div class="meter-fill" id="meter-fill"></div>
                    </div>
                    <div class="meter-pointer" id="meter-pointer"></div>
                </div>
                <div class="meter-labels">
                    <span>0</span>
                    <span>50</span>
                    <span>100</span>
                </div>
                <div class="meter-value" id="meter-value">0%</div>
            </div>
        </div>
        
        <div class="panel">
            <div class="server-time" id="server-time">Loading time...</div>
            <div id="chart-container"></div>
        </div>
    </div>

    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <script>
        // Global variables
        let currentAsset = '';
        let chart = null;
        let candleSeries = null;
        let bollingerBandsSeries = null;
        let lastMinute = -1;
        let candleData = [];
        
        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize chart
            const chartContainer = document.getElementById('chart-container');
            chart = LightweightCharts.createChart(chartContainer, {
                width: chartContainer.clientWidth,
                height: 400,
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
                },
            });
            
            candleSeries = chart.addCandlestickSeries();
            bollingerBandsSeries = chart.addLineSeries({
                color: 'rgba(75, 192, 192, 1)',
                lineWidth: 2,
            });
            
            // Load assets
            loadAssets();
            
            // Set up timers
            setInterval(updateServerTime, 1000);
            setInterval(updateSignalStrength, 2000);
            setInterval(updateChartData, 2000);
        });
        
        // Load assets from Deriv.com
        async function loadAssets() {
            try {
                // In a real app, you would fetch this from Deriv API
                // For demo purposes, we'll use a mock list
                const assets = [
                    'Volatility 10 Index',
                    'Volatility 25 Index',
                    'Volatility 50 Index',
                    'Volatility 75 Index',
                    'Volatility 100 Index',
                    'Crash 300 Index',
                    'Crash 500 Index',
                    'Crash 1000 Index',
                    'Jump 10 Index',
                    'Jump 25 Index',
                    'Jump 50 Index',
                    'Jump 75 Index',
                    'Jump 100 Index'
                ];
                
                const assetList = document.getElementById('asset-list');
                assets.forEach(asset => {
                    const option = document.createElement('option');
                    option.value = asset;
                    option.textContent = asset;
                    assetList.appendChild(option);
                });
                
                assetList.addEventListener('change', function() {
                    currentAsset = this.value;
                    // Reset chart when asset changes
                    candleData = [];
                    updateChartData();
                });
                
                // Select first asset by default
                if (assets.length > 0) {
                    assetList.selectedIndex = 0;
                    currentAsset = assets[0];
                }
                
            } catch (error) {
                console.error('Error loading assets:', error);
            }
        }
        
        // Update server time display
        function updateServerTime() {
            const now = new Date();
            const timeString = now.toISOString().replace('T', ' ').substring(0, 19);
            document.getElementById('server-time').textContent = timeString;
            
            // Check if minute has changed
            const currentMinute = now.getMinutes();
            if (currentMinute !== lastMinute) {
                lastMinute = currentMinute;
                
                // Every full minute (when seconds are 0), send data to endpoint
                if (now.getSeconds() === 0) {
                    sendCandleData();
                }
            }
        }
        
        // Update signal strength meter
        function updateSignalStrength() {
            if (!currentAsset) return;
            
            // Simulate random signal strength between 0-100
            const strength = Math.floor(Math.random() * 101);
            
            // Update meter display
            document.getElementById('meter-fill').style.height = `${strength}%`;
            document.getElementById('meter-pointer').style.transform = `rotate(${-90 + (strength * 1.8)}deg)`;
            document.getElementById('meter-value').textContent = `${strength}%`;
        }
        
        // Update chart data
        async function updateChartData() {
            if (!currentAsset) return;
            
            try {
                // In a real app, you would fetch this from Deriv API
                // For demo, we'll generate mock data
                const now = new Date();
                const timestamp = Math.floor(now.getTime() / 1000);
                
                // Generate a new candle every 2 seconds
                const open = 100 + Math.random() * 10;
                const high = open + Math.random() * 2;
                const low = open - Math.random() * 2;
                const close = low + Math.random() * (high - low);
                
                const newCandle = {
                    time: timestamp,
                    open: open,
                    high: high,
                    low: low,
                    close: close
                };
                
                // Add to our data array
                candleData.push(newCandle);
                
                // Keep only the last 100 candles
                if (candleData.length > 100) {
                    candleData.shift();
                }
                
                // Update chart
                candleSeries.setData(candleData);
                
                // Calculate Bollinger Bands
                if (candleData.length >= 20) { // Typical BB period is 20
                    const bbData = calculateBollingerBands(candleData);
                    bollingerBandsSeries.setData(bbData);
                }
                
            } catch (error) {
                console.error('Error updating chart data:', error);
            }
        }
        
        // Calculate Bollinger Bands
        function calculateBollingerBands(data, period = 20, multiplier = 2) {
            const bbData = [];
            
            for (let i = period - 1; i < data.length; i++) {
                const slice = data.slice(i - period + 1, i + 1);
                const closes = slice.map(c => c.close);
                const mean = closes.reduce((sum, val) => sum + val, 0) / period;
                
                const squaredDiffs = closes.map(c => Math.pow(c - mean, 2));
                const variance = squaredDiffs.reduce((sum, val) => sum + val, 0) / period;
                const stdDev = Math.sqrt(variance);
                
                bbData.push({
                    time: data[i].time,
                    value: mean + (multiplier * stdDev)
                });
            }
            
            return bbData;
        }
        
        // Send candle data to endpoint every minute
        async function sendCandleData() {
            if (!currentAsset || candleData.length < 30) return;
            
            try {
                // Get last 30 candles
                const dataToSend = candleData.slice(-30);
                
                // In a real app, you would send this to the endpoint
                console.log(`Sending data for ${currentAsset} to endpoint:`, dataToSend);
                
                // Mock fetch to the endpoint
                const response = await mockFetch('https://thepapers.com/abc', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        asset: currentAsset,
                        candles: dataToSend
                    })
                });
                
                console.log('Response from endpoint:', response);
                
            } catch (error) {
                console.error('Error sending candle data:', error);
            }
        }
        
        // Mock fetch function for demonstration
        async function mockFetch(url, options) {
            console.log(`Mock fetch to ${url} with options:`, options);
            return new Promise(resolve => {
                setTimeout(() => {
                    resolve({
                        ok: true,
                        status: 200,
                        json: async () => ({ success: true, message: 'Data received' })
                    });
                }, 500);
            });
        }
    </script>
</body>
</html>