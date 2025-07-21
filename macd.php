<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Technical Analysis Chart</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@deriv/deriv-api@1.0.12/dist/DerivAPIBasic.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            display: flex;
            flex-direction: column;
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .chart-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 600px;
        }
        .price-chart {
            height: 300px;
            position: relative;
        }
        .indicators {
            display: flex;
            flex-direction: column;
            height: 300px;
        }
        .rsi-chart, .stochastic-chart, .macd-chart {
            height: 100px;
            position: relative;
        }
        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        select, button {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ccc;
            background-color: white;
        }
        button {
            cursor: pointer;
            background-color: #2196F3;
            color: white;
            border: none;
        }
        button:hover {
            background-color: #0b7dda;
        }
        .status {
            padding: 8px;
            margin-top: 10px;
            border-radius: 4px;
            font-size: 14px;
        }
        .error {
            background-color: #ffebee;
            color: #c62828;
        }
        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Deriv Technical Analysis Chart</h1>
            <div class="controls">
                <select id="symbol">
                    <option value="R_100">Volatility 100 Index</option>
                    <option value="R_75">Volatility 75 Index</option>
                    <option value="R_50">Volatility 50 Index</option>
                    <option value="R_25">Volatility 25 Index</option>
                    <option value="R_10">Volatility 10 Index</option>
                    <option value="USDJPY">USD/JPY</option>
                    <option value="EURUSD">EUR/USD</option>
                    <option value="GBPUSD">GBP/USD</option>
                </select>
                <select id="interval">
                    <option value="60">1 minute</option>
                    <option value="300">5 minutes</option>
                    <option value="900">15 minutes</option>
                    <option value="1800">30 minutes</option>
                    <option value="3600">1 hour</option>
                    <option value="86400">1 day</option>
                </select>
                <button id="start">Start Streaming</button>
                <button id="stop" disabled>Stop Streaming</button>
            </div>
        </div>

        <div class="chart-container">
            <div class="price-chart" id="price-chart"></div>
            <div class="indicators">
                <div class="rsi-chart" id="rsi-chart"></div>
                <div class="stochastic-chart" id="stochastic-chart"></div>
                <div class="macd-chart" id="macd-chart"></div>
            </div>
        </div>
        <div class="status" id="status"></div>
    </div>

    <script>
        // Configuration
        const config = {
            rsiPeriod: 14,
            stochasticKPeriod: 14,
            stochasticDPeriod: 3,
            stochasticSlowingPeriod: 3,
            macdFastPeriod: 12,
            macdSlowPeriod: 26,
            macdSignalPeriod: 9
        };

        // App state
        const app = {
            connection: null,
            chartData: [],
            priceChart: null,
            candleSeries: null,
            rsiChart: null,
            rsiSeries: null,
            stochasticChart: null,
            stochasticKSeries: null,
            stochasticDSeries: null,
            macdChart: null,
            macdHistSeries: null,
            macdLineSeries: null,
            macdSignalSeries: null,
            tickSubscriptionId: null,
            tickInterval: null,
            lastTick: null,
            status: document.getElementById('status'),
            startBtn: document.getElementById('start'),
            stopBtn: document.getElementById('stop'),
            symbolSelect: document.getElementById('symbol'),
            intervalSelect: document.getElementById('interval')
        };

        // Initialize charts
        function initializeCharts() {
            // Main price chart
            app.priceChart = LightweightCharts.createChart(document.getElementById('price-chart'), {
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

            app.candleSeries = app.priceChart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });

            // RSI chart
            app.rsiChart = LightweightCharts.createChart(document.getElementById('rsi-chart'), {
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                timeScale: {
                    visible: false,
                },
                rightPriceScale: {
                    scaleMargins: {
                        top: 0.1,
                        bottom: 0.1,
                    },
                },
            });

            app.rsiSeries = app.rsiChart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                title: 'RSI',
                priceLineVisible: false,
            });

            // Add RSI overbought/oversold levels
            const rsiOverSold = app.rsiChart.addLineSeries({
                color: 'rgba(41, 98, 255, 0.3)',
                lineWidth: 1,
                lineStyle: 2,
                priceLineVisible: false,
            });
            
            const rsiOverBought = app.rsiChart.addLineSeries({
                color: 'rgba(41, 98, 255, 0.3)',
                lineWidth: 1,
                lineStyle: 2,
                priceLineVisible: false,
            });

            // Use proper timestamp format for RSI reference lines
            const startTime = Math.floor(Date.now() / 1000) - 30 * 24 * 60 * 60; // 30 days ago
            const endTime = Math.floor(Date.now() / 1000) + 30 * 24 * 60 * 60;  // 30 days in future
            
            rsiOverSold.setData([
                { time: startTime, value: 30 }, 
                { time: endTime, value: 30 }
            ]);
            rsiOverBought.setData([
                { time: startTime, value: 70 }, 
                { time: endTime, value: 70 }
            ]);

            // Stochastic chart
            app.stochasticChart = LightweightCharts.createChart(document.getElementById('stochastic-chart'), {
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                timeScale: {
                    visible: false,
                },
                rightPriceScale: {
                    scaleMargins: {
                        top: 0.1,
                        bottom: 0.1,
                    },
                },
            });

            app.stochasticKSeries = app.stochasticChart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                title: '%K',
                priceLineVisible: false,
            });

            app.stochasticDSeries = app.stochasticChart.addLineSeries({
                color: '#FF6D00',
                lineWidth: 2,
                title: '%D',
                priceLineVisible: false,
            });

            // Add Stochastic overbought/oversold levels
            const stochasticOverSold = app.stochasticChart.addLineSeries({
                color: 'rgba(41, 98, 255, 0.3)',
                lineWidth: 1,
                lineStyle: 2,
                priceLineVisible: false,
            });
            
            const stochasticOverBought = app.stochasticChart.addLineSeries({
                color: 'rgba(41, 98, 255, 0.3)',
                lineWidth: 1,
                lineStyle: 2,
                priceLineVisible: false,
            });

            stochasticOverSold.setData([
                { time: startTime, value: 20 }, 
                { time: endTime, value: 20 }
            ]);
            stochasticOverBought.setData([
                { time: startTime, value: 80 }, 
                { time: endTime, value: 80 }
            ]);

            // MACD chart
            app.macdChart = LightweightCharts.createChart(document.getElementById('macd-chart'), {
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                timeScale: {
                    visible: true,
                    timeVisible: true,
                    secondsVisible: false,
                },
                rightPriceScale: {
                    scaleMargins: {
                        top: 0.1,
                        bottom: 0.1,
                    },
                },
            });

            app.macdHistSeries = app.macdChart.addHistogramSeries({
                color: '#26a69a',
                priceFormat: {
                    type: 'custom',
                    formatter: price => price.toFixed(5),
                },
                priceLineVisible: false,
                title: 'Histogram',
            });

            app.macdLineSeries = app.macdChart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                priceLineVisible: false,
                title: 'MACD',
            });

            app.macdSignalSeries = app.macdChart.addLineSeries({
                color: '#FF6D00',
                lineWidth: 2,
                priceLineVisible: false,
                title: 'Signal',
            });
        }

        // Connect to Deriv API
        function connectToDerivAPI() {
            app.connection = new DerivAPIBasic({ app_id: 66726 });
            app.startBtn.addEventListener('click', startStreaming);
            app.stopBtn.addEventListener('click', stopStreaming);
            updateStatus('Ready to connect. Click "Start Streaming" to begin.', 'success');
        }

        // Update status message
        function updateStatus(message, type = 'success') {
            app.status.textContent = message;
            app.status.className = 'status ' + type;
        }

        // Start streaming data
        async function startStreaming() {
            try {
                app.startBtn.disabled = true;
                app.stopBtn.disabled = false;
                
                const symbol = app.symbolSelect.value;
                const interval = parseInt(app.intervalSelect.value);
                
                updateStatus(`Connecting to ${symbol} with ${interval / 60} minute interval...`, 'success');

                // First get historical data
                await getHistoricalData(symbol, interval);
                
                // Then start streaming real-time data
                startTickStream(symbol, interval);
            } catch (error) {
                console.error('Error starting stream:', error);
                updateStatus('Error starting stream: ' + error.message, 'error');
                app.startBtn.disabled = false;
                app.stopBtn.disabled = true;
            }
        }

        // Stop streaming data
        function stopStreaming() {
            if (app.tickSubscriptionId) {
                app.connection.send({ forget: app.tickSubscriptionId });
                app.tickSubscriptionId = null;
            }
            
            if (app.tickInterval) {
                clearInterval(app.tickInterval);
                app.tickInterval = null;
            }
            
            app.startBtn.disabled = false;
            app.stopBtn.disabled = true;
            updateStatus('Streaming stopped', 'success');
        }

        // Get historical candle data
        async function getHistoricalData(symbol, interval) {
            updateStatus(`Loading historical data for ${symbol}...`, 'success');
            
            try {
                // Request historical data
                const response = await app.connection.send({
                    ticks_history: symbol,
                    adjust_start_time: 1,
                    count: 100,
                    end: "latest",
                    granularity: interval,
                    style: "candles"
                });
                
                if (response.error) {
                    throw new Error(response.error.message);
                }
                
                const candles = response.candles;
                app.chartData = [];
                
                // Process historical data
                for (const candle of candles) {
                    // Convert to UTC timestamp that lightweight-charts can understand
                    // Using time in UNIX timestamp format (seconds) with decimal part for milliseconds
                    const timestamp = Math.floor(candle.epoch);
                    const candleData = {
                        time: timestamp,
                        open: parseFloat(candle.open),
                        high: parseFloat(candle.high),
                        low: parseFloat(candle.low),
                        close: parseFloat(candle.close)
                    };
                    app.chartData.push(candleData);
                }
                
                // Calculate and set indicator data
                calculateIndicators();
                updateCharts();
                
                updateStatus(`Historical data loaded, starting real-time streaming...`, 'success');
            } catch (error) {
                console.error('Error fetching historical data:', error);
                updateStatus('Error fetching historical data: ' + error.message, 'error');
                throw error;
            }
        }

        // Start streaming tick data
        function startTickStream(symbol, interval) {
            let lastCandleTime = 0;
            if (app.chartData.length > 0) {
                lastCandleTime = app.chartData[app.chartData.length - 1].time;
            }
            
            // Subscribe to tick stream
            app.connection.send({
                ticks: symbol,
                subscribe: 1
            }).then(response => {
                if (response.error) {
                    throw new Error(response.error.message);
                }
                
                app.tickSubscriptionId = response.subscription.id;
                let currentCandle = null;
                
                // Process incoming ticks
                app.connection.onMessage().subscribe(message => {
                    try {
                        const data = JSON.parse(message);
                        
                        if (data.tick) {
                            const tick = data.tick;
                            app.lastTick = tick;
                            
                            const tickTime = Math.floor(tick.epoch);
                            const currentCandleTime = Math.floor(tickTime / interval) * interval;
                            
                            // Create or update the current candle
                            if (!currentCandle || currentCandle.time !== currentCandleTime) {
                                // If we have a current candle, push it to the chart data
                                if (currentCandle) {
                                    app.chartData.push(currentCandle);
                                    calculateIndicators();
                                    updateCharts();
                                }
                                
                                // Create a new candle
                                currentCandle = {
                                    time: currentCandleTime,
                                    open: parseFloat(tick.quote),
                                    high: parseFloat(tick.quote),
                                    low: parseFloat(tick.quote),
                                    close: parseFloat(tick.quote)
                                };
                            } else {
                                // Update the current candle
                                currentCandle.high = Math.max(currentCandle.high, parseFloat(tick.quote));
                                currentCandle.low = Math.min(currentCandle.low, parseFloat(tick.quote));
                                currentCandle.close = parseFloat(tick.quote);
                            }
                        }
                    } catch (error) {
                        console.error('Error processing tick:', error);
                    }
                });
                
                // Update charts every 2 seconds
                app.tickInterval = setInterval(() => {
                    if (app.lastTick && currentCandle) {
                        try {
                            // Create a temporary candle for real-time updates
                            const tempChartData = [...app.chartData];
                            tempChartData.push(currentCandle);
                            
                            // Calculate indicators with the temporary data
                            const indicators = calculateIndicatorsForData(tempChartData);
                            
                            // Update chart series
                            app.candleSeries.update(currentCandle);
                            
                            // Update indicators if they're available
                            if (indicators.rsi.length > 0) {
                                const lastRsiIndex = indicators.rsi.length - 1;
                                if (indicators.rsi[lastRsiIndex]) {
                                    app.rsiSeries.update(indicators.rsi[lastRsiIndex]);
                                }
                            }
                            
                            if (indicators.stochasticK.length > 0) {
                                const lastStochIndex = indicators.stochasticK.length - 1;
                                if (indicators.stochasticK[lastStochIndex]) {
                                    app.stochasticKSeries.update(indicators.stochasticK[lastStochIndex]);
                                }
                                if (indicators.stochasticD[lastStochIndex]) {
                                    app.stochasticDSeries.update(indicators.stochasticD[lastStochIndex]);
                                }
                            }
                            
                            if (indicators.macd.length > 0) {
                                const lastMacdIndex = indicators.macd.length - 1;
                                if (indicators.macd[lastMacdIndex]) {
                                    app.macdLineSeries.update(indicators.macd[lastMacdIndex]);
                                }
                                if (indicators.macdSignal.length > 0 && indicators.macdSignal[indicators.macdSignal.length - 1]) {
                                    app.macdSignalSeries.update(indicators.macdSignal[indicators.macdSignal.length - 1]);
                                }
                                if (indicators.macdHist.length > 0 && indicators.macdHist[indicators.macdHist.length - 1]) {
                                    app.macdHistSeries.update(indicators.macdHist[indicators.macdHist.length - 1]);
                                }
                            }
                            
                            updateStatus(`Last update: ${new Date().toLocaleTimeString()} - Price: ${app.lastTick.quote}`, 'success');
                        } catch (error) {
                            console.error('Error updating chart:', error);
                        }
                    }
                }, 2000);
            }).catch(error => {
                console.error('Error in tick subscription:', error);
                updateStatus('Error in tick subscription: ' + error.message, 'error');
                app.startBtn.disabled = false;
                app.stopBtn.disabled = true;
            });
        }

        // Calculate technical indicators
        function calculateIndicators() {
            const results = calculateIndicatorsForData(app.chartData);
            
            app.rsiSeries.setData(results.rsi);
            app.stochasticKSeries.setData(results.stochasticK);
            app.stochasticDSeries.setData(results.stochasticD);
            app.macdHistSeries.setData(results.macdHist);
            app.macdLineSeries.setData(results.macd);
            app.macdSignalSeries.setData(results.macdSignal);
        }

        // Calculate indicators for given data
        function calculateIndicatorsForData(data) {
            const closes = data.map(candle => candle.close);
            const highs = data.map(candle => candle.high);
            const lows = data.map(candle => candle.low);
            const times = data.map(candle => candle.time);
            
            return {
                rsi: calculateRSI(closes, times, config.rsiPeriod),
                stochasticK: calculateStochasticK(closes, highs, lows, times, config.stochasticKPeriod),
                stochasticD: calculateStochasticD(closes, highs, lows, times, config.stochasticKPeriod, config.stochasticDPeriod, config.stochasticSlowingPeriod),
                macd: calculateMACD(closes, times, config.macdFastPeriod, config.macdSlowPeriod),
                macdSignal: calculateMACDSignal(closes, times, config.macdFastPeriod, config.macdSlowPeriod, config.macdSignalPeriod),
                macdHist: calculateMACDHistogram(closes, times, config.macdFastPeriod, config.macdSlowPeriod, config.macdSignalPeriod)
            };
        }

        // Update charts with data
        function updateCharts() {
            app.candleSeries.setData(app.chartData);
            app.priceChart.timeScale().fitContent();
            app.rsiChart.timeScale().fitContent();
            app.stochasticChart.timeScale().fitContent();
            app.macdChart.timeScale().fitContent();
        }

        // Calculate RSI
        function calculateRSI(prices, times, period = 14) {
            const result = [];
            if (prices.length <= period) {
                return result;
            }
            
            let gains = 0;
            let losses = 0;
            
            // First RSI calculation
            for (let i = 1; i <= period; i++) {
                const diff = prices[i] - prices[i - 1];
                if (diff >= 0) {
                    gains += diff;
                } else {
                    losses += Math.abs(diff);
                }
            }
            
            let avgGain = gains / period;
            let avgLoss = losses / period;
            let rs = avgGain / avgLoss;
            let rsi = 100 - (100 / (1 + rs));
            
            result.push({ time: times[period], value: rsi });
            
            // Rest of RSI values
            for (let i = period + 1; i < prices.length; i++) {
                const diff = prices[i] - prices[i - 1];
                avgGain = ((avgGain * (period - 1)) + (diff > 0 ? diff : 0)) / period;
                avgLoss = ((avgLoss * (period - 1)) + (diff < 0 ? Math.abs(diff) : 0)) / period;
                
                rs = avgGain / avgLoss;
                rsi = 100 - (100 / (1 + rs));
                
                result.push({ time: times[i], value: rsi });
            }
            
            return result;
        }

        // Calculate Stochastic %K
        function calculateStochasticK(closes, highs, lows, times, period = 14) {
            const result = [];
            if (closes.length <= period) {
                return result;
            }
            
            for (let i = period - 1; i < closes.length; i++) {
                let highestHigh = -Infinity;
                let lowestLow = Infinity;
                
                for (let j = i - (period - 1); j <= i; j++) {
                    highestHigh = Math.max(highestHigh, highs[j]);
                    lowestLow = Math.min(lowestLow, lows[j]);
                }
                
                const k = ((closes[i] - lowestLow) / (highestHigh - lowestLow)) * 100;
                result.push({ time: times[i], value: k });
            }
            
            return result;
        }

        // Calculate Stochastic %D
        function calculateStochasticD(closes, highs, lows, times, kPeriod = 14, dPeriod = 3, slowing = 3) {
            const kValues = calculateStochasticK(closes, highs, lows, times, kPeriod);
            const result = [];
            
            if (kValues.length <= dPeriod) {
                return result;
            }
            
            for (let i = dPeriod - 1; i < kValues.length; i++) {
                let sum = 0;
                for (let j = i - (dPeriod - 1); j <= i; j++) {
                    sum += kValues[j].value;
                }
                
                const d = sum / dPeriod;
                result.push({ time: kValues[i].time, value: d });
            }
            
            return result;
        }

        // Calculate EMA
        function calculateEMA(prices, period) {
            const result = [];
            const multiplier = 2 / (period + 1);
            
            // First EMA is SMA
            let sma = 0;
            for (let i = 0; i < period; i++) {
                sma += prices[i];
            }
            sma /= period;
            
            let ema = sma;
            result.push(ema);
            
            // Calculate subsequent EMAs
            for (let i = period; i < prices.length; i++) {
                ema = (prices[i] - ema) * multiplier + ema;
                result.push(ema);
            }
            
            return result;
        }

        // Calculate MACD line
        function calculateMACD(prices, times, fastPeriod = 12, slowPeriod = 26) {
            const result = [];
            
            if (prices.length <= slowPeriod) {
                return result;
            }
            
            const fastEMA = calculateEMA(prices, fastPeriod);
            const slowEMA = calculateEMA(prices, slowPeriod);
            
            // Adjust for the offset in the EMAs
            const offset = slowPeriod - fastPeriod;
            
            for (let i = 0; i < slowEMA.length; i++) {
                const macd = fastEMA[i + offset] - slowEMA[i];
                result.push({ time: times[i + slowPeriod], value: macd });
            }
            
            return result;
        }

        // Calculate MACD Signal line
        function calculateMACDSignal(prices, times, fastPeriod = 12, slowPeriod = 26, signalPeriod = 9) {
            const macdLine = calculateMACD(prices, times, fastPeriod, slowPeriod);
            const result = [];
            
            if (macdLine.length <= signalPeriod) {
                return result;
            }
            
            const macdValues = macdLine.map(item => item.value);
            const signalEMA = calculateEMA(macdValues, signalPeriod);
            
            // Safety check to make sure we don't exceed array bounds
            for (let i = 0; i < signalEMA.length && (i + signalPeriod) < macdLine.length; i++) {
                if (macdLine[i + signalPeriod] && macdLine[i + signalPeriod].time) {
                    result.push({ time: macdLine[i + signalPeriod].time, value: signalEMA[i] });
                }
            }
            
            return result;
        }

        // Calculate MACD Histogram
        function calculateMACDHistogram(prices, times, fastPeriod = 12, slowPeriod = 26, signalPeriod = 9) {
            const macdLine = calculateMACD(prices, times, fastPeriod, slowPeriod);
            const signalLine = calculateMACDSignal(prices, times, fastPeriod, slowPeriod, signalPeriod);
            const result = [];
            
            // Safety check to make sure we don't exceed array bounds
            for (let i = 0; i < signalLine.length; i++) {
                if (i + signalPeriod < macdLine.length && macdLine[i + signalPeriod] && signalLine[i]) {
                    const histogram = macdLine[i + signalPeriod].value - signalLine[i].value;
                    result.push({ 
                        time: signalLine[i].time, 
                        value: histogram,
                        color: histogram >= 0 ? '#26a69a' : '#ef5350'
                    });
                }
            }
            
            return result;
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
            initializeCharts();
            connectToDerivAPI();
            
            window.addEventListener('resize', () => {
                if (app.priceChart) app.priceChart.applyOptions({ width: document.getElementById('price-chart').clientWidth });
                if (app.rsiChart) app.rsiChart.applyOptions({ width: document.getElementById('rsi-chart').clientWidth });
                if (app.stochasticChart) app.stochasticChart.applyOptions({ width: document.getElementById('stochastic-chart').clientWidth });
                if (app.macdChart) app.macdChart.applyOptions({ width: document.getElementById('macd-chart').clientWidth });
            });
        });
    </script>
</body>
</html>