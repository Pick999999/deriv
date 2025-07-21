<?php
/*
สร้างกราฟ  4 รูป โดยแสดง ใน  1 html page โดยแต่ละรูป เป็นกราฟแสดง  candlestick + ema3+ ema5  พร้อมทั้ง rsi 
โดยแต่ละรูป แสดงถึง asset ของ  deriv.com ได้แก่ ค่า r_10,r_25,r_50,r_100 และดึงข้อมูล candlestick จาก  deriv.com
 โดยให้ ใช้ https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js
ทั้งหมด ทำโดย pure javascript และ ไม่ต้องการ mock data แต่ต้องการข้อมูล  real time จาก  deriv.com


*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Volatility Indices Charts</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .chart-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 15px;
        }
        .price-chart {
            height: 300px;
            margin-bottom: 10px;
        }
        .rsi-chart {
            height: 150px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .chart-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 350px;
            font-size: 16px;
            color: #666;
        }
        .status-bar {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #e9f7ef;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>Deriv Volatility Indices Real-Time Charts</h1>
    <div class="status-bar" id="connectionStatus">Connecting to Deriv API...</div>
    <div class="container">
        <div class="chart-container">
            <div class="chart-title">Volatility 10 Index (1m)</div>
            <div id="price_chart_R_10" class="price-chart"></div>
            <div class="chart-title">RSI (14)</div>
            <div id="rsi_chart_R_10" class="rsi-chart"></div>
            <div id="loading_R_10" class="loading">Loading data...</div>
        </div>
        <div class="chart-container">
            <div class="chart-title">Volatility 25 Index (1m)</div>
            <div id="price_chart_R_25" class="price-chart"></div>
            <div class="chart-title">RSI (14)</div>
            <div id="rsi_chart_R_25" class="rsi-chart"></div>
            <div id="loading_R_25" class="loading">Loading data...</div>
        </div>
        <div class="chart-container">
            <div class="chart-title">Volatility 50 Index (1m)</div>
            <div id="price_chart_R_50" class="price-chart"></div>
            <div class="chart-title">RSI (14)</div>
            <div id="rsi_chart_R_50" class="rsi-chart"></div>
            <div id="loading_R_50" class="loading">Loading data...</div>
        </div>
        <div class="chart-container">
            <div class="chart-title">Volatility 100 Index (1m)</div>
            <div id="price_chart_R_100" class="price-chart"></div>
            <div class="chart-title">RSI (14)</div>
            <div id="rsi_chart_R_100" class="rsi-chart"></div>
            <div id="loading_R_100" class="loading">Loading data...</div>
        </div>
    </div>

    <script>
        // Configuration
        const symbols = ['R_10', 'R_25', 'R_50', 'R_100'];
        const candleInterval = 60; // 1 minute candles
        const wsUrl = 'wss://ws.binaryws.com/websockets/v3?app_id=66726';
        
        // Data storage
        const chartData = {
            R_10: { candles: [], ema3: [], ema5: [], rsi: [] },
            R_25: { candles: [], ema3: [], ema5: [], rsi: [] },
            R_50: { candles: [], ema3: [], ema5: [], rsi: [] },
            R_100: { candles: [], ema3: [], ema5: [], rsi: [] }
        };
        
        // Charts and series references
        const priceCharts = {};
        const rsiCharts = {};
        const candleSeries = {};
        const ema3Series = {};
        const ema5Series = {};
        const rsiSeries = {};
        
        // WebSocket connection
        let socket;
        let connectionAttempts = 0;
        const maxConnectionAttempts = 3;
        
        // Wait for DOM to be fully loaded before initializing
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            connectWebSocket();
        });
        
        function initializeCharts() {
            symbols.forEach(symbol => {
                try {
                    // Check if elements exist
                    const priceChartElement = document.getElementById(`price_chart_${symbol}`);
                    const rsiChartElement = document.getElementById(`rsi_chart_${symbol}`);
                    
                    if (!priceChartElement || !rsiChartElement) {
                        console.error(`Chart elements not found for ${symbol}`);
                        return;
                    }
                    
                    // Initialize price chart
                    priceCharts[symbol] = LightweightCharts.createChart(priceChartElement, {
                        width: priceChartElement.clientWidth,
                        height: priceChartElement.clientHeight,
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
                            secondsVisible: true,
                        },
                    });
                    
                    // Create candlestick series
                    candleSeries[symbol] = priceCharts[symbol].addCandlestickSeries({
                        upColor: '#26a69a',
                        downColor: '#ef5350',
                        borderVisible: false,
                        wickUpColor: '#26a69a',
                        wickDownColor: '#ef5350',
                    });
                    
                    // Create EMA series
                    ema3Series[symbol] = priceCharts[symbol].addLineSeries({
                        color: '#2196F3',
                        lineWidth: 2,
                        title: 'EMA 3',
                    });
                    
                    ema5Series[symbol] = priceCharts[symbol].addLineSeries({
                        color: '#FF9800',
                        lineWidth: 2,
                        title: 'EMA 5',
                    });
                    
                    // Initialize RSI chart
                    rsiCharts[symbol] = LightweightCharts.createChart(rsiChartElement, {
                        width: rsiChartElement.clientWidth,
                        height: rsiChartElement.clientHeight,
                        layout: {
                            background: { color: '#ffffff' },
                            textColor: '#333',
                        },
                        grid: {
                            vertLines: { color: '#f0f0f0' },
                            horzLines: { color: '#f0f0f0' },
                        },
                        timeScale: { visible: false }, // Hide timeScale on RSI chart
                        rightPriceScale: {
                            autoScale: true,
                            scaleMargins: {
                                top: 0.1,
                                bottom: 0.1,
                            },
                        },
                    });
                    
                    // Add RSI series
                    rsiSeries[symbol] = rsiCharts[symbol].addLineSeries({
                        color: '#9C27B0',
                        lineWidth: 2,
                        title: 'RSI 14',
                    });
                    
                    // Add RSI overbought/oversold levels
                    const overboughtSeries = rsiCharts[symbol].addLineSeries({
                        color: '#ccc',
                        lineWidth: 1,
                        lineStyle: 2, // Dashed
                    });
                    
                    const oversoldSeries = rsiCharts[symbol].addLineSeries({
                        color: '#ccc',
                        lineWidth: 1,
                        lineStyle: 2, // Dashed
                    });
                    
                    const currentTime = Math.floor(Date.now() / 1000);
                    
                    overboughtSeries.setData([
                        { time: currentTime - 3600, value: 70 },
                        { time: currentTime, value: 70 },
                    ]);
                    
                    oversoldSeries.setData([
                        { time: currentTime - 3600, value: 30 },
                        { time: currentTime, value: 30 },
                    ]);
                    
                    // Sync charts' timeScales
                    priceCharts[symbol].timeScale().subscribeVisibleTimeRangeChange((timeRange) => {
                        if (timeRange && rsiCharts[symbol]) {
                            rsiCharts[symbol].timeScale().setVisibleRange({
                                from: timeRange.from,
                                to: timeRange.to
                            });
                        }
                    });
                    
                } catch (error) {
                    console.error(`Error initializing chart for ${symbol}:`, error);
                }
            });
            
            // Make charts responsive
            window.addEventListener('resize', debounce(() => {
                symbols.forEach(symbol => {
                    try {
                        const priceChartElement = document.getElementById(`price_chart_${symbol}`);
                        const rsiChartElement = document.getElementById(`rsi_chart_${symbol}`);
                        
                        if (priceChartElement && priceCharts[symbol]) {
                            priceCharts[symbol].applyOptions({
                                width: priceChartElement.clientWidth,
                            });
                        }
                        
                        if (rsiChartElement && rsiCharts[symbol]) {
                            rsiCharts[symbol].applyOptions({
                                width: rsiChartElement.clientWidth,
                            });
                        }
                    } catch (error) {
                        console.error(`Error resizing chart for ${symbol}:`, error);
                    }
                });
            }, 250));
        }
        
        // Debounce function to prevent excessive resizing calls
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }
        
        function connectWebSocket() {
            try {
                socket = new WebSocket(wsUrl);
                
                socket.onopen = function() {
                    document.getElementById('connectionStatus').innerText = 'Connected to Deriv API';
                    document.getElementById('connectionStatus').style.backgroundColor = '#d4edda';
                    connectionAttempts = 0;
                    
                    // Subscribe to ticks for initial connection validation
                    symbols.forEach(symbol => {
                        socket.send(JSON.stringify({
                            ticks: symbol,
                            subscribe: 1
                        }));
                    });
                    
                    // Request historical data and then subscribe to new candles
                    requestHistoricalData();
                };
                
                socket.onmessage = function(msg) {
                    const data = JSON.parse(msg.data);
                    
                    // Handle tick data (just to verify connection)
                    if (data.tick) {
                        const symbol = data.tick.symbol;
                        console.log(`Received tick for ${symbol}: ${data.tick.quote}`);
                    }
                    
                    // Handle historical candles
                    if (data.history && data.history.prices) {
                        const symbol = data.echo_req.ticks_history;
                        processHistoricalData(symbol, data);
                    }
                    
                    // Handle new candle updates
                    if (data.ohlc) {
                        const symbol = data.ohlc.symbol;
                        processNewCandle(symbol, data.ohlc);
                    }
                };
                
                socket.onclose = function() {
                    document.getElementById('connectionStatus').innerText = 'Connection closed. Reconnecting...';
                    document.getElementById('connectionStatus').style.backgroundColor = '#fff3cd';
                    
                    connectionAttempts++;
                    if (connectionAttempts < maxConnectionAttempts) {
                        setTimeout(connectWebSocket, 3000);
                    } else {
                        document.getElementById('connectionStatus').innerText = 'Connection failed after multiple attempts. Please refresh the page.';
                        document.getElementById('connectionStatus').style.backgroundColor = '#f8d7da';
                    }
                };
                
                socket.onerror = function(error) {
                    console.error('WebSocket Error:', error);
                    document.getElementById('connectionStatus').innerText = 'Connection error. Reconnecting...';
                    document.getElementById('connectionStatus').style.backgroundColor = '#f8d7da';
                };
                
            } catch (error) {
                console.error('Failed to connect:', error);
                document.getElementById('connectionStatus').innerText = 'Failed to connect. Reconnecting...';
                document.getElementById('connectionStatus').style.backgroundColor = '#f8d7da';
                
                connectionAttempts++;
                if (connectionAttempts < maxConnectionAttempts) {
                    setTimeout(connectWebSocket, 3000);
                } else {
                    document.getElementById('connectionStatus').innerText = 'Connection failed after multiple attempts. Please refresh the page.';
                }
            }
        }
        
        function requestHistoricalData() {
            symbols.forEach(symbol => {
                // Get candles for the past 60 minutes
                const endTime = Math.floor(Date.now() / 1000);
                const startTime = endTime - (60 * 60); // 1 hour in seconds
                
                socket.send(JSON.stringify({
                    ticks_history: symbol,
                    end: 'latest',
                    start: startTime,
                    style: 'candles',
                    granularity: candleInterval,
                    count: 60
                }));
                
                // Subscribe to ongoing candles
                socket.send(JSON.stringify({
                    ticks_history: symbol,
                    end: 'latest',
                    style: 'candles',
                    granularity: candleInterval,
                    subscribe: 1
                }));
            });
        }
        
        function processHistoricalData(symbol, data) {
            try {
                const candles = data.history.prices.map((price, index) => {
                    const time = data.history.times[index];
                    return {
                        time: time,
                        open: parseFloat(price[0]),
                        high: parseFloat(price[1]),
                        low: parseFloat(price[2]),
                        close: parseFloat(price[3])
                    };
                });
                
                if (candles.length === 0) {
                    const loadingElement = document.getElementById(`loading_${symbol}`);
                    if (loadingElement) {
                        loadingElement.innerText = 'No data available';
                    }
                    return;
                }
                
                // Store the candles
                chartData[symbol].candles = candles;
                
                // Calculate indicators
                calculateIndicators(symbol);
                
                // Update the chart
                updateChart(symbol);
                
                // Hide loading message
                const loadingElement = document.getElementById(`loading_${symbol}`);
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }
            } catch (error) {
                console.error(`Error processing historical data for ${symbol}:`, error);
            }
        }
        
        function processNewCandle(symbol, ohlc) {
            try {
                const candle = {
                    time: parseInt(ohlc.open_time),
                    open: parseFloat(ohlc.open),
                    high: parseFloat(ohlc.high),
                    low: parseFloat(ohlc.low),
                    close: parseFloat(ohlc.close)
                };
                
                // Check if this is a new candle or an update to the latest candle
                if (chartData[symbol].candles.length > 0 && 
                    chartData[symbol].candles[chartData[symbol].candles.length - 1].time === candle.time) {
                    // Update the latest candle
                    chartData[symbol].candles[chartData[symbol].candles.length - 1] = candle;
                } else {
                    // Add a new candle
                    chartData[symbol].candles.push(candle);
                    
                    // Remove the oldest candle if we have more than 100
                    if (chartData[symbol].candles.length > 100) {
                        chartData[symbol].candles.shift();
                    }
                }
                
                // Recalculate indicators and update the chart
                calculateIndicators(symbol);
                updateChart(symbol);
            } catch (error) {
                console.error(`Error processing new candle for ${symbol}:`, error);
            }
        }
        
        function calculateIndicators(symbol) {
            try {
                const candles = chartData[symbol].candles;
                if (candles.length === 0) return;
                
                // Calculate EMA3
                chartData[symbol].ema3 = calculateEMA(candles, 3);
                
                // Calculate EMA5
                chartData[symbol].ema5 = calculateEMA(candles, 5);
                
                // Calculate RSI
                chartData[symbol].rsi = calculateRSI(candles, 14);
            } catch (error) {
                console.error(`Error calculating indicators for ${symbol}:`, error);
            }
        }
        
        function calculateEMA(candles, period) {
            if (candles.length < period) return [];
            
            const prices = candles.map(c => c.close);
            const ema = [];
            
            // Calculate SMA for the first EMA value
            let sum = 0;
            for (let i = 0; i < period; i++) {
                sum += prices[i];
            }
            
            // First EMA value is SMA
            ema.push({
                time: candles[period - 1].time,
                value: sum / period
            });
            
            // Calculate EMA
            const multiplier = 2 / (period + 1);
            for (let i = period; i < prices.length; i++) {
                const emaValue = prices[i] * multiplier + ema[i - period].value * (1 - multiplier);
                ema.push({
                    time: candles[i].time,
                    value: emaValue
                });
            }
            
            return ema;
        }
        
        function calculateRSI(candles, period) {
            if (candles.length < period + 1) return [];
            
            const prices = candles.map(c => c.close);
            const rsi = [];
            let gains = 0;
            let losses = 0;
            
            // Calculate first average gain and loss
            for (let i = 1; i <= period; i++) {
                const change = prices[i] - prices[i - 1];
                if (change >= 0) {
                    gains += change;
                } else {
                    losses -= change;
                }
            }
            
            let avgGain = gains / period;
            let avgLoss = losses / period;
            
            // Calculate RSI using Wilder's smoothing method
            for (let i = period + 1; i < prices.length; i++) {
                const change = prices[i] - prices[i - 1];
                let currentGain = 0;
                let currentLoss = 0;
                
                if (change >= 0) {
                    currentGain = change;
                } else {
                    currentLoss = -change;
                }
                
                // Use the smoothing formula
                avgGain = (avgGain * (period - 1) + currentGain) / period;
                avgLoss = (avgLoss * (period - 1) + currentLoss) / period;
                
                const rs = avgGain / (avgLoss === 0 ? 0.001 : avgLoss); // Avoid division by zero
                const rsiValue = 100 - (100 / (1 + rs));
                
                rsi.push({
                    time: candles[i].time,
                    value: rsiValue
                });
            }
            
            return rsi;
        }
        
        function updateChart(symbol) {
            try {
                if (!candleSeries[symbol]) {
                    console.error(`Candle series not found for ${symbol}`);
                    return;
                }
                
                // Update candlestick series
                candleSeries[symbol].setData(chartData[symbol].candles);
                
                // Update EMA series
                if (chartData[symbol].ema3.length > 0 && ema3Series[symbol]) {
                    ema3Series[symbol].setData(chartData[symbol].ema3);
                }
                
                if (chartData[symbol].ema5.length > 0 && ema5Series[symbol]) {
                    ema5Series[symbol].setData(chartData[symbol].ema5);
                }
                
                // Update RSI series
                if (chartData[symbol].rsi.length > 0 && rsiSeries[symbol]) {
                    rsiSeries[symbol].setData(chartData[symbol].rsi);
                }
                
                // Fit the content
                if (priceCharts[symbol]) {
                    priceCharts[symbol].timeScale().fitContent();
                }
                if (rsiCharts[symbol]) {
                    rsiCharts[symbol].timeScale().fitContent();
                }
            } catch (error) {
                console.error(`Error updating chart for ${symbol}:`, error);
            }
        }
    </script>
</body>
</html>