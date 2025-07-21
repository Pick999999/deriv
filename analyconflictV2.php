<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candlestick Chart with EMA Analysis</title>
    <script src="https://unpkg.com/lightweight-charts@3.8.0/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        .control-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        label {
            font-weight: bold;
            color: #333;
        }
        select, input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #45a049;
        }
        .time-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        #chartContainer {
            height: 600px;
            width: 100%;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .summary-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #4CAF50;
        }
        .summary-card h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .summary-card p {
            margin: 5px 0;
            font-size: 16px;
        }
        .conflicts-table, .backtest-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .conflicts-table th, .conflicts-table td,
        .backtest-table th, .backtest-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .conflicts-table th, .backtest-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .loading {
            text-align: center;
            padding: 20px;
            font-size: 16px;
            color: #666;
        }
        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .backtest-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .backtest-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv Candlestick Chart with EMA Analysis</h1>
        
        <div class="controls">
            <div class="control-group">
                <label for="startDate">Start Date:</label>
                <input type="datetime-local" id="startDate">
            </div>
            <div class="control-group">
                <label for="endDate">End Date:</label>
                <input type="datetime-local" id="endDate">
            </div>
            <div class="control-group">
                <label for="asset">Asset:</label>
                <select id="asset">
                    <option value="R_50">Volatility 50 Index</option>
                    <option value="R_75">Volatility 75 Index</option>
                    <option value="R_100">Volatility 100 Index</option>
                    <option value="RDBEAR">Bear Market Index</option>
                    <option value="RDBULL">Bull Market Index</option>
                    <option value="1HZ10V">Volatility 10 (1s) Index</option>
                    <option value="1HZ25V">Volatility 25 (1s) Index</option>
                    <option value="1HZ50V">Volatility 50 (1s) Index</option>
                    <option value="1HZ75V">Volatility 75 (1s) Index</option>
                    <option value="1HZ100V">Volatility 100 (1s) Index</option>
                    <option value="BOOM300N">Boom 300 Index</option>
                    <option value="BOOM500N">Boom 500 Index</option>
                    <option value="BOOM1000N">Boom 1000 Index</option>
                    <option value="CRASH300N">Crash 300 Index</option>
                    <option value="CRASH500N">Crash 500 Index</option>
                    <option value="CRASH1000N">Crash 1000 Index</option>
                </select>
            </div>
            <div class="control-group">
                <label for="timeframe">Timeframe:</label>
                <select id="timeframe">
                    <option value="60">1 Minute</option>
                    <option value="120">2 Minutes</option>
                    <option value="180">3 Minutes</option>
                    <option value="300">5 Minutes</option>
                    <option value="600">10 Minutes</option>
                    <option value="900">15 Minutes</option>
                    <option value="1800">30 Minutes</option>
                    <option value="3600">1 Hour</option>
                    <option value="14400">4 Hours</option>
                    <option value="86400">1 Day</option>
                </select>
            </div>
            <button onclick="loadData()">Load Data</button>
        </div>

        <div class="time-controls">
            <button onclick="adjustTime(-1)">-1 Hour</button>
            <button onclick="adjustTime(1)">+1 Hour</button>
        </div>

        <div id="loading" class="loading" style="display: none;">Loading data...</div>
        <div id="error" class="error" style="display: none;"></div>

        <div id="chartContainer"></div>

        <div class="summary">
            <div class="summary-card">
                <h3>Total Candles</h3>
                <p id="totalCandles">0</p>
            </div>
            <div class="summary-card">
                <h3>Conflicts</h3>
                <p id="totalConflicts">0</p>
            </div>
            <div class="summary-card">
                <h3>Conflict Rate</h3>
                <p id="conflictRate">0%</p>
            </div>
        </div>

        <div id="conflictsSection" style="display: none;">
            <h3>Conflict Points</h3>
            <table class="conflicts-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Open</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Close</th>
                        <th>EMA3</th>
                        <th>EMA5</th>
                        <th>Expected Color</th>
                        <th>Actual Color</th>
                    </tr>
                </thead>
                <tbody id="conflictsTableBody">
                </tbody>
            </table>
        </div>

        <div class="backtest-section">
            <h3>Backtest Trading Strategy</h3>
            <div class="backtest-controls">
                <div class="control-group">
                    <label for="tradeAmount">Trade Amount:</label>
                    <input type="number" id="tradeAmount" value="10" min="1">
                </div>
                <div class="control-group">
                    <label for="stopLoss">Stop Loss (pips):</label>
                    <input type="number" id="stopLoss" value="20" min="1">
                </div>
                <div class="control-group">
                    <label for="takeProfit">Take Profit (pips):</label>
                    <input type="number" id="takeProfit" value="30" min="1">
                </div>
                <button onclick="runBacktest()">Run Backtest</button>
            </div>

            <div id="backtestSummary" style="display: none;">
                <div class="summary">
                    <div class="summary-card">
                        <h3>Total Trades</h3>
                        <p id="totalTrades">0</p>
                    </div>
                    <div class="summary-card">
                        <h3>Wins</h3>
                        <p id="totalWins">0</p>
                    </div>
                    <div class="summary-card">
                        <h3>Losses</h3>
                        <p id="totalLosses">0</p>
                    </div>
                    <div class="summary-card">
                        <h3>Win Rate</h3>
                        <p id="winRate">0%</p>
                    </div>
                    <div class="summary-card">
                        <h3>Total P&L</h3>
                        <p id="totalPL">0</p>
                    </div>
                </div>
            </div>

            <div id="backtestResults" style="display: none;">
                <h4>Backtest Results</h4>
                <table class="backtest-table">
                    <thead>
                        <tr>
                            <th>Trade #</th>
                            <th>Entry Time</th>
                            <th>Entry Price</th>
                            <th>Exit Time</th>
                            <th>Exit Price</th>
                            <th>Direction</th>
                            <th>Result</th>
                            <th>P&L</th>
                        </tr>
                    </thead>
                    <tbody id="backtestTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let chart;
        let candlestickSeries;
        let ema3Series;
        let ema5Series;
        let chartData = [];
        let conflictPoints = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeChart();
            loadSettings();
            setDefaultDates();
        });

        function initializeChart() {
            const chartContainer = document.getElementById('chartContainer');
            chart = LightweightCharts.createChart(chartContainer, {
                width: chartContainer.clientWidth,
                height: 600,
                layout: {
                    backgroundColor: '#ffffff',
                    textColor: 'rgba(33, 56, 77, 1)',
                },
                grid: {
                    vertLines: {
                        color: 'rgba(197, 203, 206, 0.5)',
                    },
                    horzLines: {
                        color: 'rgba(197, 203, 206, 0.5)',
                    },
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                rightPriceScale: {
                    borderColor: 'rgba(197, 203, 206, 1)',
                },
                timeScale: {
                    borderColor: 'rgba(197, 203, 206, 1)',
                },
            });

            candlestickSeries = chart.addCandlestickSeries({
                upColor: '#4CAF50',
                downColor: '#F44336',
                borderVisible: false,
                wickUpColor: '#4CAF50',
                wickDownColor: '#F44336',
            });

            ema3Series = chart.addLineSeries({
                color: '#2196F3',
                lineWidth: 2,
                title: 'EMA3',
            });

            ema5Series = chart.addLineSeries({
                color: '#FF9800',
                lineWidth: 2,
                title: 'EMA5',
            });

            // Handle resize
            window.addEventListener('resize', () => {
                chart.applyOptions({ width: chartContainer.clientWidth });
            });
        }

        function setDefaultDates() {
            const now = new Date();
            const start = new Date(now.getTime() - 24 * 60 * 60 * 1000); // 24 hours ago
            
            document.getElementById('startDate').value = formatDateForInput(start);
            document.getElementById('endDate').value = formatDateForInput(now);
        }

        function formatDateForInput(date) {
            return date.toISOString().slice(0, 16);
        }

        function saveSettings() {
            const settings = {
                startDate: document.getElementById('startDate').value,
                endDate: document.getElementById('endDate').value,
                asset: document.getElementById('asset').value,
                timeframe: document.getElementById('timeframe').value,
            };
            localStorage.setItem('derivChartSettings', JSON.stringify(settings));
        }

        function loadSettings() {
            const savedSettings = localStorage.getItem('derivChartSettings');
            if (savedSettings) {
                const settings = JSON.parse(savedSettings);
                document.getElementById('startDate').value = settings.startDate || '';
                document.getElementById('endDate').value = settings.endDate || '';
                document.getElementById('asset').value = settings.asset || 'R_50';
                document.getElementById('timeframe').value = settings.timeframe || '300';
            }
        }

        function adjustTime(hours) {
            const startDate = new Date(document.getElementById('startDate').value);
            const endDate = new Date(document.getElementById('endDate').value);
            
            startDate.setHours(startDate.getHours() + hours);
            endDate.setHours(endDate.getHours() + hours);
            
            document.getElementById('startDate').value = formatDateForInput(startDate);
            document.getElementById('endDate').value = formatDateForInput(endDate);
        }

        async function loadData() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const asset = document.getElementById('asset').value;
            const timeframe = document.getElementById('timeframe').value;

            if (!startDate || !endDate) {
                showError('Please select both start and end dates');
                return;
            }

            showLoading(true);
            hideError();
            saveSettings();

            try {
                const data = await fetchDerivData(asset, timeframe, startDate, endDate);
                processData(data);
                updateChart();
                updateSummary();
                displayConflicts();
            } catch (error) {
                showError('Error loading data: ' + error.message);
            } finally {
                showLoading(false);
            }
        }

        async function fetchDerivData(asset, timeframe, startDate, endDate) {
            const start = Math.floor(new Date(startDate).getTime() / 1000);
            const end = Math.floor(new Date(endDate).getTime() / 1000);
            
            return new Promise((resolve, reject) => {
                const ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=1089');
                
                ws.onopen = function() {
                    const request = {
                        ticks_history: asset,
                        adjust_start_time: 1,
                        count: 5000,
                        end: 'latest',
                        granularity: parseInt(timeframe),
                        start: start,
                        style: 'candles'
                    };
                    
                    ws.send(JSON.stringify(request));
                };
                
                ws.onmessage = function(event) {
                    const response = JSON.parse(event.data);
                    
                    if (response.error) {
                        reject(new Error(response.error.message));
                        ws.close();
                        return;
                    }
                    
                    if (response.candles) {
                        const data = response.candles.map(candle => ({
                            time: candle.epoch,
                            open: parseFloat(candle.open),
                            high: parseFloat(candle.high),
                            low: parseFloat(candle.low),
                            close: parseFloat(candle.close)
                        }));
                        
                        resolve(data);
                        ws.close();
                    }
                };
                
                ws.onerror = function(error) {
                    reject(new Error('WebSocket connection failed: ' + error.message));
                    ws.close();
                };
                
                // Timeout after 30 seconds
                setTimeout(() => {
                    if (ws.readyState === WebSocket.OPEN) {
                        ws.close();
                        reject(new Error('Request timeout'));
                    }
                }, 30000);
            });
        }

        function calculateEMA(data, period) {
            const multiplier = 2 / (period + 1);
            const emaData = [];
            
            if (data.length === 0) return emaData;
            
            // First EMA value is the first closing price
            emaData.push({
                time: data[0].time,
                value: data[0].close
            });
            
            // Calculate subsequent EMA values
            for (let i = 1; i < data.length; i++) {
                const ema = (data[i].close - emaData[i-1].value) * multiplier + emaData[i-1].value;
                emaData.push({
                    time: data[i].time,
                    value: parseFloat(ema.toFixed(5))
                });
            }
            
            return emaData;
        }

        function processData(data) {
            chartData = data;
            conflictPoints = [];
            
            const ema3Data = calculateEMA(data, 3);
            const ema5Data = calculateEMA(data, 5);
            
            // Find conflicts
            for (let i = 0; i < data.length; i++) {
                const candle = data[i];
                const ema3 = ema3Data[i];
                const ema5 = ema5Data[i];
                
                if (ema3 && ema5) {
                    const ema3Above = ema3.value > ema5.value;
                    const candleGreen = candle.close > candle.open;
                    
                    // Check for conflicts
                    if ((ema3Above && !candleGreen) || (!ema3Above && candleGreen)) {
                        conflictPoints.push({
                            ...candle,
                            ema3: ema3.value,
                            ema5: ema5.value,
                            expectedColor: ema3Above ? 'Green' : 'Red',
                            actualColor: candleGreen ? 'Green' : 'Red'
                        });
                    }
                }
            }
            
            // Update chart series data
            candlestickSeries.setData(data);
            ema3Series.setData(ema3Data);
            ema5Series.setData(ema5Data);
            
            // Add conflict markers
            addConflictMarkers();
        }

        function addConflictMarkers() {
            const markers = conflictPoints.map(point => ({
                time: point.time,
                position: 'aboveBar',
                color: '#FF5722',
                shape: 'circle',
                text: 'C',
                size: 1
            }));
            
            candlestickSeries.setMarkers(markers);
        }

        function updateChart() {
            chart.timeScale().fitContent();
        }

        function updateSummary() {
            const totalCandles = chartData.length;
            const totalConflicts = conflictPoints.length;
            const conflictRate = totalCandles > 0 ? ((totalConflicts / totalCandles) * 100).toFixed(2) : 0;
            
            document.getElementById('totalCandles').textContent = totalCandles;
            document.getElementById('totalConflicts').textContent = totalConflicts;
            document.getElementById('conflictRate').textContent = conflictRate + '%';
        }

        function displayConflicts() {
            const conflictsSection = document.getElementById('conflictsSection');
            const tableBody = document.getElementById('conflictsTableBody');
            
            if (conflictPoints.length === 0) {
                conflictsSection.style.display = 'none';
                return;
            }
            
            conflictsSection.style.display = 'block';
            tableBody.innerHTML = '';
            
            conflictPoints.forEach(point => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${new Date(point.time * 1000).toLocaleString()}</td>
                    <td>${point.open}</td>
                    <td>${point.high}</td>
                    <td>${point.low}</td>
                    <td>${point.close}</td>
                    <td>${point.ema3}</td>
                    <td>${point.ema5}</td>
                    <td>${point.expectedColor}</td>
                    <td>${point.actualColor}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        function runBacktest() {
            if (chartData.length === 0) {
                showError('Please load data first');
                return;
            }

            const tradeAmount = parseFloat(document.getElementById('tradeAmount').value);
            const stopLoss = parseFloat(document.getElementById('stopLoss').value);
            const takeProfit = parseFloat(document.getElementById('takeProfit').value);

            const ema3Data = calculateEMA(chartData, 3);
            const ema5Data = calculateEMA(chartData, 5);
            
            const trades = [];
            let currentTrade = null;
            
            for (let i = 1; i < chartData.length; i++) {
                const prevCandle = chartData[i - 1];
                const currentCandle = chartData[i];
                const prevEma3 = ema3Data[i - 1];
                const prevEma5 = ema5Data[i - 1];
                const currentEma3 = ema3Data[i];
                const currentEma5 = ema5Data[i];
                
                if (!prevEma3 || !prevEma5 || !currentEma3 || !currentEma5) continue;
                
                // Check if this is a conflict point
                const isConflict = conflictPoints.some(point => point.time === currentCandle.time);
                
                if (!isConflict && !currentTrade) {
                    // Enter trade on non-conflict points
                    const ema3Above = currentEma3.value > currentEma5.value;
                    const direction = ema3Above ? 'BUY' : 'SELL';
                    
                    currentTrade = {
                        entryTime: currentCandle.time,
                        entryPrice: currentCandle.close,
                        direction: direction,
                        stopLoss: direction === 'BUY' ? currentCandle.close - (stopLoss / 10000) : currentCandle.close + (stopLoss / 10000),
                        takeProfit: direction === 'BUY' ? currentCandle.close + (takeProfit / 10000) : currentCandle.close - (takeProfit / 10000)
                    };
                }
                
                if (currentTrade) {
                    // Check exit conditions
                    let exitCondition = null;
                    let exitPrice = null;
                    
                    if (currentTrade.direction === 'BUY') {
                        if (currentCandle.low <= currentTrade.stopLoss) {
                            exitCondition = 'STOP_LOSS';
                            exitPrice = currentTrade.stopLoss;
                        } else if (currentCandle.high >= currentTrade.takeProfit) {
                            exitCondition = 'TAKE_PROFIT';
                            exitPrice = currentTrade.takeProfit;
                        }
                    } else {
                        if (currentCandle.high >= currentTrade.stopLoss) {
                            exitCondition = 'STOP_LOSS';
                            exitPrice = currentTrade.stopLoss;
                        } else if (currentCandle.low <= currentTrade.takeProfit) {
                            exitCondition = 'TAKE_PROFIT';
                            exitPrice = currentTrade.takeProfit;
                        }
                    }
                    
                    if (exitCondition) {
                        const pnl = currentTrade.direction === 'BUY' ? 
                            (exitPrice - currentTrade.entryPrice) * tradeAmount :
                            (currentTrade.entryPrice - exitPrice) * tradeAmount;
                        
                        trades.push({
                            ...currentTrade,
                            exitTime: currentCandle.time,
                            exitPrice: exitPrice,
                            result: exitCondition === 'TAKE_PROFIT' ? 'WIN' : 'LOSS',
                            pnl: parseFloat(pnl.toFixed(2))
                        });
                        
                        currentTrade = null;
                    }
                }
            }
            
            displayBacktestResults(trades);
        }

        function displayBacktestResults(trades) {
            const wins = trades.filter(t => t.result === 'WIN').length;
            const losses = trades.filter(t => t.result === 'LOSS').length;
            const totalPL = trades.reduce((sum, t) => sum + t.pnl, 0);
            const winRate = trades.length > 0 ? ((wins / trades.length) * 100).toFixed(2) : 0;
            
            // Update summary
            document.getElementById('totalTrades').textContent = trades.length;
            document.getElementById('totalWins').textContent = wins;
            document.getElementById('totalLosses').textContent = losses;
            document.getElementById('winRate').textContent = winRate + '%';
            document.getElementById('totalPL').textContent = totalPL.toFixed(2);
            
            // Show results table
            const tableBody = document.getElementById('backtestTableBody');
            tableBody.innerHTML = '';
            
            trades.forEach((trade, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${new Date(trade.entryTime * 1000).toLocaleString()}</td>
                    <td>${trade.entryPrice}</td>
                    <td>${new Date(trade.exitTime * 1000).toLocaleString()}</td>
                    <td>${trade.exitPrice}</td>
                    <td>${trade.direction}</td>
                    <td style="color: ${trade.result === 'WIN' ? 'green' : 'red'}">${trade.result}</td>
                    <td style="color: ${trade.pnl >= 0 ? 'green' : 'red'}">${trade.pnl.toFixed(2)}</td>
                `;
                tableBody.appendChild(row);
            });
            
            document.getElementById('backtestSummary').style.display = 'block';
            document.getElementById('backtestResults').style.display = 'block';
        }

        function showLoading(show) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
        }

        function hideError() {
            document.getElementById('error').style.display = 'none';
        }

        function showError(message) {
            document.getElementById('error').textContent = message;
            document.getElementById('error').style.display = 'block';
        }

function runBacktest2() {
            if (chartData.length === 0) {
                showError('Please load data first');
                return;
            }

            const tradeAmount = parseFloat(document.getElementById('tradeAmount').value);
            const stopLoss = parseFloat(document.getElementById('stopLoss').value);
            const takeProfit = parseFloat(document.getElementById('takeProfit').value);

            const ema3Data = calculateEMA(chartData, 3);
            const ema5Data = calculateEMA(chartData, 5);
            
            const trades = [];
            let currentTrade = null;
            
            for (let i = 1; i < chartData.length; i++) {
                const prevCandle = chartData[i - 1];
                const currentCandle = chartData[i];
                const prevEma3 = ema3Data[i - 1];
                const prevEma5 = ema5Data[i - 1];
                const currentEma3 = ema3Data[i];
                const currentEma5 = ema5Data[i];
                
                if (!prevEma3 || !prevEma5 || !currentEma3 || !currentEma5) continue;
                
                // Check if this is a conflict point
                const isConflict = conflictPoints.some(point => point.time === currentCandle.time);
                
                if (!isConflict && !currentTrade) {
                    // Enter trade on non-conflict points
                    const ema3Above = currentEma3.value > currentEma5.value;
                    const direction = ema3Above ? 'BUY' : 'SELL';
                    
                    currentTrade = {
                        entryTime: currentCandle.time,
                        entryPrice: currentCandle.close,
                        direction: direction,
                        stopLoss: direction === 'BUY' ? currentCandle.close - (stopLoss / 10000) : currentCandle.close + (stopLoss / 10000),
                        takeProfit: direction === 'BUY' ? currentCandle.close + (takeProfit / 10000) : currentCandle.close - (takeProfit / 10000)
                    };
                }
                
                if (currentTrade) {
                    // Check exit conditions
                    let exitCondition = null;
                    let exitPrice = null;
                    
                    if (currentTrade.direction === 'BUY') {
                        if (currentCandle.low <= currentTrade.stopLoss) {
                            exitCondition = 'STOP_LOSS';
                            exitPrice = currentTrade.stopLoss;
                        } else if (currentCandle.high >= currentTrade.takeProfit) {
                            exitCondition = 'TAKE_PROFIT';
                            exitPrice = currentTrade.takeProfit;
                        }
                    } else {
                        if (currentCandle.high >= currentTrade.stopLoss) {
                            exitCondition = 'STOP_LOSS';
                            exitPrice = currentTrade.stopLoss;
                        } else if (currentCandle.low <= currentTrade.takeProfit) {
                            exitCondition = 'TAKE_PROFIT';
                            exitPrice = currentTrade.takeProfit;
                        }
                    }
                    
                    if (exitCondition) {
                        const pnl = currentTrade.direction === 'BUY' ? 
                            (exitPrice - currentTrade.entryPrice) * tradeAmount :
                            (currentTrade.entryPrice - exitPrice) * tradeAmount;
                        
                        trades.push({
                            ...currentTrade,
                            exitTime: currentCandle.time,
                            exitPrice: exitPrice,
                            result: exitCondition === 'TAKE_PROFIT' ? 'WIN' : 'LOSS',
                            pnl: parseFloat(pnl.toFixed(2))
                        });
                        
                        currentTrade = null;
                    }
                }
            }
            
            displayBacktestResults(trades);
}


    </script>
</body>
</html>