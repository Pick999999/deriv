<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Asset Analyzer</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #2a3f5f;
            text-align: center;
        }
        .category-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .category-btn {
            padding: 10px 15px;
            background-color: #2a3f5f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .category-btn:hover {
            background-color: #1e2e45;
        }
        .category-btn.active {
            background-color: #1e2e45;
            box-shadow: 0 0 5px rgba(0,0,0,0.3);
        }
        .assets-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }
        .asset-card {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .asset-card:hover {
            transform: translateY(-3px);
        }
        .asset-name {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .asset-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .status-open {
            background-color: #c6f6d5;
            color: #22543d;
        }
        .status-closed {
            background-color: #fed7d7;
            color: #822727;
        }
        .analyze-btn {
            padding: 6px 12px;
            background-color: #4299e1;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .analyze-btn:disabled {
            background-color: #cbd5e0;
            cursor: not-allowed;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .close-btn {
            font-size: 24px;
            cursor: pointer;
            background: none;
            border: none;
        }
        .chart-container {
            height: 400px;
            width: 100%;
        }
        .analysis-result {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f0f9ff;
            border-left: 4px solid #3182ce;
        }
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error-message {
            color: #e53e3e;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv Asset Analyzer</h1>
        
        <div id="categoryButtons" class="category-buttons">
            <!-- Category buttons will be inserted here -->
            <div class="loader"></div>
        </div>
        
        <div id="assetsContainer" class="assets-container">
            <!-- Asset cards will be inserted here -->
            <p>Select a category to view assets</p>
        </div>
    </div>
    
    <div id="analysisModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Asset Analysis</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div id="chartContainer" class="chart-container"></div>
            <div id="analysisResult" class="analysis-result">
                <!-- Analysis results will go here -->
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let activeCategory = null;
        let assetCategories = {};
        let chart = null;
        const apiUrl = 'https://api.deriv.com/api';
		//apiToken= 'lt5UMO6bNvmZQaR';
        const token = 'lt5UMO6bNvmZQaR'; // If you have an API token

        // Helper function to create WebSocket connection
        function createWebSocket() {
            return new Promise((resolve, reject) => {
                const ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
                
                ws.onopen = () => {
                    console.log('WebSocket connection established');
                    resolve(ws);
                };
                
                ws.onerror = (error) => {
                    console.error('WebSocket error:', error);
                    reject(error);
                };
            });
        }

        // Send WebSocket request
        function sendRequest(ws, request) {
            return new Promise((resolve, reject) => {
                const requestId = Math.random().toString(36).substring(2, 15);
                request.req_id = requestId;
                
                const messageHandler = (event) => {
                    const response = JSON.parse(event.data);
                    if (response.req_id === requestId) {
                        ws.removeEventListener('message', messageHandler);
                        if (response.error) {
                            reject(response.error);
                        } else {
                            resolve(response);
                        }
                    }
                };
                
                ws.addEventListener('message', messageHandler);
                ws.send(JSON.stringify(request));
            });
        }

        // Initialize the app
        async function init() {
            try {
                const ws = await createWebSocket();
                
                // Get active symbols
                const activeSymbolsResponse = await sendRequest(ws, {
                    active_symbols: 'brief',
                    product_type: 'basic'
                });
                
                if (activeSymbolsResponse.active_symbols) {
                    organizeSymbolsByCategory(activeSymbolsResponse.active_symbols);
                    createCategoryButtons();
                }
                
                // Close the WebSocket connection after initialization
                ws.close();
            } catch (error) {
                console.error('Initialization error:', error);
                document.getElementById('categoryButtons').innerHTML = `
                    <div class="error-message">
                        Failed to load asset data. Please try again later.
                    </div>
                `;
            }
        }

        // Organize symbols by market category
        function organizeSymbolsByCategory(symbols) {
            assetCategories = {};
            
            symbols.forEach(symbol => {
                const category = symbol.market_display_name;
                
                if (!assetCategories[category]) {
                    assetCategories[category] = [];
                }
                
                assetCategories[category].push({
                    symbol: symbol.symbol,
                    name: symbol.display_name,
                    market: symbol.market,
                    submarket: symbol.submarket,
                    isOpen: symbol.exchange_is_open === 1,
                    pip: symbol.pip,
                    displayOrder: symbol.display_order
                });
            });
            
            // Sort assets in each category
            Object.keys(assetCategories).forEach(category => {
                assetCategories[category].sort((a, b) => a.displayOrder - b.displayOrder);
            });
        }

        // Create category buttons
        function createCategoryButtons() {
            const buttonContainer = document.getElementById('categoryButtons');
            buttonContainer.innerHTML = '';
            
            Object.keys(assetCategories).sort().forEach(category => {
                const button = document.createElement('button');
                button.className = 'category-btn';
                button.textContent = category;
                button.onclick = () => selectCategory(category);
                buttonContainer.appendChild(button);
            });
        }

        // Select a category and display its assets
        function selectCategory(category) {
            // Update active button
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.textContent === category) {
                    btn.classList.add('active');
                }
            });
            
            activeCategory = category;
            displayAssets(category);
        }

        // Display assets for the selected category
        function displayAssets(category) {
            const assetsContainer = document.getElementById('assetsContainer');
            assetsContainer.innerHTML = '';
            
            if (!assetCategories[category] || assetCategories[category].length === 0) {
                assetsContainer.innerHTML = '<p>No assets found in this category</p>';
                return;
            }
            
            assetCategories[category].forEach(asset => {
                const assetCard = document.createElement('div');
                assetCard.className = 'asset-card';
                
                const statusClass = asset.isOpen ? 'status-open' : 'status-closed';
                const statusText = asset.isOpen ? 'Open' : 'Closed';
                
                assetCard.innerHTML = `
                    <div class="asset-name">${asset.name}</div>
                    <div class="asset-status ${statusClass}">${statusText}</div>
                    <button class="analyze-btn" ${!asset.isOpen ? 'disabled' : ''} 
                        onclick="analyzeAsset('${asset.symbol}', '${asset.name}')">
                        Analyze Trend
                    </button>
                `;
                
                assetsContainer.appendChild(assetCard);
            });
        }

        // Analyze asset trend
        async function analyzeAsset(symbol, name) {
            openModal();
            document.getElementById('modalTitle').textContent = `Analyzing ${name}`;
            document.getElementById('chartContainer').innerHTML = '<div class="loading"><div class="loader"></div></div>';
            document.getElementById('analysisResult').innerHTML = 'Analyzing...';
            
            try {
                const ws = await createWebSocket();
                
                // Get candles data - last 50 candles with 1-hour interval
                const candlesResponse = await sendRequest(ws, {
                    ticks_history: symbol,
                    style: 'candles',
                    count: 50,
                    granularity: 60 // 1 hour
                });
                
                if (candlesResponse.candles) {
                    const candles = candlesResponse.candles;
                    renderChart(candles, name);
                    const trend = analyzeADXTrend(candles);
                    displayAnalysisResult(trend);
                } else {
                    document.getElementById('analysisResult').innerHTML = 'Failed to retrieve candle data';
                }
                
                ws.close();
            } catch (error) {
                console.error('Analysis error:', error);
                document.getElementById('analysisResult').innerHTML = 'An error occurred during analysis';
                document.getElementById('chartContainer').innerHTML = '<div class="error-message">Failed to load chart data</div>';
            }
        }

        // Calculate ADX indicator
        function calculateADX(candles, period = 14) {
            const highs = candles.map(candle => parseFloat(candle.high));
            const lows = candles.map(candle => parseFloat(candle.low));
            const closes = candles.map(candle => parseFloat(candle.close));
            
            // Calculate True Range
            const trueRanges = [];
            for (let i = 1; i < candles.length; i++) {
                const high = highs[i];
                const low = lows[i];
                const prevClose = closes[i - 1];
                
                const tr1 = high - low;
                const tr2 = Math.abs(high - prevClose);
                const tr3 = Math.abs(low - prevClose);
                
                trueRanges.push(Math.max(tr1, tr2, tr3));
            }
            
            // Calculate Average True Range (ATR)
            let atr = trueRanges.slice(0, period).reduce((sum, tr) => sum + tr, 0) / period;
            const atrs = [atr];
            
            for (let i = period; i < trueRanges.length; i++) {
                atr = (atr * (period - 1) + trueRanges[i]) / period;
                atrs.push(atr);
            }
            
            // Calculate Directional Movement
            const plusDMs = [];
            const minusDMs = [];
            
            for (let i = 1; i < candles.length; i++) {
                const highDiff = highs[i] - highs[i - 1];
                const lowDiff = lows[i - 1] - lows[i];
                
                let plusDM = 0;
                let minusDM = 0;
                
                if (highDiff > lowDiff && highDiff > 0) {
                    plusDM = highDiff;
                }
                
                if (lowDiff > highDiff && lowDiff > 0) {
                    minusDM = lowDiff;
                }
                
                plusDMs.push(plusDM);
                minusDMs.push(minusDM);
            }
            
            // Calculate Directional Indicators
            const plusDIs = [];
            const minusDIs = [];
            
            let plusDI = 100 * plusDMs.slice(0, period).reduce((sum, dm) => sum + dm, 0) / 
                          (period * atrs[0]);
            let minusDI = 100 * minusDMs.slice(0, period).reduce((sum, dm) => sum + dm, 0) / 
                           (period * atrs[0]);
            
            plusDIs.push(plusDI);
            minusDIs.push(minusDI);
            
            for (let i = 1; i < atrs.length; i++) {
                plusDI = (plusDI * (period - 1) + 100 * plusDMs[i + period - 1] / atrs[i]) / period;
                minusDI = (minusDI * (period - 1) + 100 * minusDMs[i + period - 1] / atrs[i]) / period;
                
                plusDIs.push(plusDI);
                minusDIs.push(minusDI);
            }
            
            // Calculate Directional Movement Index (DX)
            const dxs = [];
            for (let i = 0; i < plusDIs.length; i++) {
                const dx = 100 * Math.abs(plusDIs[i] - minusDIs[i]) / (plusDIs[i] + minusDIs[i]);
                dxs.push(dx);
            }
            
            // Calculate Average Directional Index (ADX)
            let adx = dxs.slice(0, period).reduce((sum, dx) => sum + dx, 0) / period;
            const adxs = [adx];
            
            for (let i = period; i < dxs.length; i++) {
                adx = (adx * (period - 1) + dxs[i]) / period;
                adxs.push(adx);
            }
            
            return {
                adx: adxs[adxs.length - 1],
                plusDI: plusDIs[plusDIs.length - 1],
                minusDI: minusDIs[minusDIs.length - 1]
            };
        }

        // Analyze trend using ADX
        function analyzeADXTrend(candles) {
            const adxResult = calculateADX(candles);
            const adx = adxResult.adx;
            const plusDI = adxResult.plusDI;
            const minusDI = adxResult.minusDI;
            
            let strength;
            if (adx < 15) {
                strength = 'Sideways (No Trend)';
            } else if (adx < 25) {
                strength = 'Weak Trend';
            } else if (adx < 50) {
                strength = 'Strong Trend';
            } else if (adx < 75) {
                strength = 'Very Strong Trend';
            } else {
                strength = 'Extremely Strong Trend';
            }
            
            let direction;
            if (plusDI > minusDI) {
                direction = 'Bullish (Upward)';
            } else {
                direction = 'Bearish (Downward)';
            }
            
            return {
                strength,
                direction,
                adx: adx.toFixed(2),
                plusDI: plusDI.toFixed(2),
                minusDI: minusDI.toFixed(2)
            };
        }

        // Calculate EMA
        function calculateEMA(data, period) {
            const k = 2 / (period + 1);
            let ema = data[0];
            const emaData = [ema];
            
            for (let i = 1; i < data.length; i++) {
                ema = data[i] * k + ema * (1 - k);
                emaData.push(ema);
            }
            
            return emaData;
        }

        // Render the chart
        function renderChart(candles, name) {
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.innerHTML = '';
            
            // Prepare data for lightweight-charts
            const candleData = candles.map(candle => ({
                time: candle.time,
                open: parseFloat(candle.open),
                high: parseFloat(candle.high),
                low: parseFloat(candle.low),
                close: parseFloat(candle.close)
            }));
            
            // Calculate EMA3 and EMA5
            const closes = candles.map(candle => parseFloat(candle.close));
            const ema3 = calculateEMA(closes, 3);
            const ema5 = calculateEMA(closes, 5);
            
            const ema3Data = candles.map((candle, index) => ({
                time: candle.time,
                value: ema3[index]
            }));
            
            const ema5Data = candles.map((candle, index) => ({
                time: candle.time,
                value: ema5[index]
            }));
            
            // Create the chart
            chart = LightweightCharts.createChart(chartContainer, {
                width: chartContainer.clientWidth,
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
                },
            });
            
            // Add candle series
            const candleSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });
            candleSeries.setData(candleData);
            
            // Add EMA3 series
            const ema3Series = chart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                title: 'EMA3',
            });
            ema3Series.setData(ema3Data);
            
            // Add EMA5 series
            const ema5Series = chart.addLineSeries({
                color: '#FF6D00',
                lineWidth: 2,
                title: 'EMA5',
            });
            ema5Series.setData(ema5Data);
            
            // Fit content
            chart.timeScale().fitContent();
            
            // Handle window resize
            window.addEventListener('resize', () => {
                if (chart) {
                    chart.applyOptions({
                        width: chartContainer.clientWidth
                    });
                }
            });
        }

        // Display analysis results
        function displayAnalysisResult(trend) {
            const resultElement = document.getElementById('analysisResult');
            
            let trendColor;
            if (trend.strength.includes('Extremely Strong')) {
                trendColor = '#805ad5'; // Purple
            } else if (trend.strength.includes('Very Strong')) {
                trendColor = '#3182ce'; // Blue
            } else if (trend.strength.includes('Strong')) {
                trendColor = '#38a169'; // Green
            } else if (trend.strength.includes('Weak')) {
                trendColor = '#d69e2e'; // Yellow
            } else {
                trendColor = '#718096'; // Gray for sideways
            }
            
            let directionColor = trend.direction.includes('Bullish') ? '#38a169' : '#e53e3e';
            
            resultElement.innerHTML = `
                <h3>Trend Analysis</h3>
                <p><strong>Trend Strength:</strong> <span style="color: ${trendColor}; font-weight: bold;">${trend.strength}</span></p>
                <p><strong>Trend Direction:</strong> <span style="color: ${directionColor}; font-weight: bold;">${trend.direction}</span></p>
                <p><strong>ADX Value:</strong> ${trend.adx}</p>
                <p><strong>+DI Value:</strong> ${trend.plusDI}</p>
                <p><strong>-DI Value:</strong> ${trend.minusDI}</p>
                <p><strong>Interpretation:</strong> ${getTrendInterpretation(trend)}</p>
            `;
        }

        // Get trend interpretation
        function getTrendInterpretation(trend) {
            const strength = trend.strength;
            const direction = trend.direction;
            
            if (strength.includes('Sideways')) {
                return "The market is showing no clear trend. This could be a consolidation phase. Consider waiting for a clear trend to emerge before making trading decisions.";
            }
            
            if (direction.includes('Bullish')) {
                if (strength.includes('Extremely Strong')) {
                    return "The market is showing an extremely strong upward momentum. This suggests a robust bullish trend, but be cautious of potential overextension.";
                } else if (strength.includes('Very Strong')) {
                    return "The market is in a very strong uptrend. The bulls are firmly in control, suggesting continued upward movement.";
                } else if (strength.includes('Strong')) {
                    return "The market is showing a strong upward trend. The bulls are in control, and the trend may continue.";
                } else {
                    return "The market shows a weak upward bias. While the direction is bullish, the momentum is not strong.";
                }
            } else {
                if (strength.includes('Extremely Strong')) {
                    return "The market is showing extremely strong downward momentum. This suggests a powerful bearish trend, but be cautious of potential overextension.";
                } else if (strength.includes('Very Strong')) {
                    return "The market is in a very strong downtrend. The bears are firmly in control, suggesting continued downward movement.";
                } else if (strength.includes('Strong')) {
                    return "The market is showing a strong downward trend. The bears are in control, and the trend may continue.";
                } else {
                    return "The market shows a weak downward bias. While the direction is bearish, the momentum is not strong.";
                }
            }
        }

        // Open the modal
        function openModal() {
            document.getElementById('analysisModal').style.display = 'flex';
        }

        // Close the modal
        function closeModal() {
            document.getElementById('analysisModal').style.display = 'none';
            if (chart) {
                chart.remove();
                chart = null;
            }
        }

        // Initialize the app on page load
        window.onload = init;
    </script>
</body>
</html>