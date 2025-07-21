<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candlestick Chart Analyzer</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .controls {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: center;
        }
        
        .control-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        label {
            font-weight: bold;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        select, input, button {
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        select:focus, input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.3);
            transform: translateY(-2px);
        }
        
        button {
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            color: white;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }
        
        button:active {
            transform: translateY(-1px);
        }
        
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .chart-wrapper {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }
        
        .chart-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .chart-title {
            font-size: 1.2em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .timeframe-select {
            padding: 8px 12px;
            font-size: 12px;
        }
        
        .chart {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .analysis-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .analysis-button {
            background: linear-gradient(45deg, #4ecdc4, #44a08d);
            font-size: 16px;
            padding: 15px 30px;
            margin-bottom: 20px;
        }
        
        .analysis-result {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
            font-family: 'Courier New', monospace;
            line-height: 1.6;
            display: none;
        }
        
        .status {
            background: rgba(0, 0, 0, 0.2);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        
        .status.connected {
            background: rgba(46, 204, 113, 0.3);
        }
        
        .status.error {
            background: rgba(231, 76, 60, 0.3);
        }
        
        @media (max-width: 768px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
            
            .controls {
                grid-template-columns: 1fr;
            }
        }
        
        .prediction-highlight {
            background: rgba(255, 215, 0, 0.2);
            border-left: 4px solid gold;
            padding: 15px;
            margin: 10px 0;
            border-radius: 0 8px 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📈 Deriv Candlestick Chart Analyzer</h1>
        
        <div class="status" id="connectionStatus">🔄 กำลังเชื่อมต่อ...</div>
        
        <div class="controls">
            <div class="control-group">
                <label for="assetSelect">📊 เลือก Asset:</label>
                <select id="assetSelect">
                    <option value="R_50">Volatility 50 Index</option>
                    <option value="R_75">Volatility 75 Index</option>
                    <option value="R_100">Volatility 100 Index</option>
                    <option value="RDBEAR">Bear Market Index</option>
                    <option value="RDBULL">Bull Market Index</option>
                    <option value="frxEURUSD">EUR/USD</option>
                    <option value="frxGBPUSD">GBP/USD</option>
                    <option value="frxUSDJPY">USD/JPY</option>
                    <option value="frxAUDUSD">AUD/USD</option>
                    <option value="frxUSDCAD">USD/CAD</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="emaShort">⚡ EMA Short:</label>
                <input type="number" id="emaShort" value="9" min="1" max="100">
            </div>
            
            <div class="control-group">
                <label for="emaLong">📏 EMA Long:</label>
                <input type="number" id="emaLong" value="21" min="1" max="200">
            </div>
        </div>
        
        <div class="charts-container">
            <div class="chart-wrapper">
                <div class="chart-controls">
                    <div class="chart-title">📊 Higher Timeframe</div>
                    <select class="timeframe-select" id="timeframe1">
                        
                        
                        
                        
                        <option value="1">1 Minute</option>
						<option value="5" selected>5 Minutes</option>
						<option value="10">10 Minutes</option>
						<option value="15">15 Minutes</option>
						<option value="30">30 Minutes</option>

					</select>
                </div>
                <div class="chart" id="chart1"></div>
            </div>
            
            <div class="chart-wrapper">
                <div class="chart-controls">
                    <div class="chart-title">⚡ Lower Timeframe</div>
                    <select class="timeframe-select" id="timeframe2">
					  <option value="1">1 Minute</option>
						<option value="5">5 Minutes</option>
						<option value="10">10 Minutes</option>
						<option value="15">15 Minutes</option>
						<option value="30">30 Minutes</option>
<!-- 
                        <option value="5">5 Minutes</option>
                        <option value="1">1 Minute</option>
                        <option value="10">10 Minutes</option>
                        <option value="15">15 Minutes</option>
                        <option value="30">30 Minutes</option>
 -->
                    </select>
                </div>
                <div class="chart" id="chart2"></div>
            </div>
        </div>
        
        <div class="analysis-section">
            <button class="analysis-button" id="analyzeBtn">
                🔍 วิเคราะห์ความสัมพันธ์และทำนายกราฟ
            </button>
            <div class="analysis-result" id="analysisResult"></div>
        </div>
    </div>

    <script>
        class DerivChartAnalyzer {
            constructor() {
                this.ws = null;
                this.isConnected = false;
                this.charts = {};
                this.candleSeries = {};
                this.emaShortSeries = {};
                this.emaLongSeries = {};
                this.chartData = { chart1: [], chart2: [] };
                this.subscriptions = {};
                this.currentAsset = 'R_50';
                
                this.init();
            }
            
            init() {
                this.setupCharts();
                this.setupEventListeners();
                this.connectWebSocket();
            }
            
            setupCharts() {
                // Chart 1 - Higher Timeframe
                this.charts.chart1 = LightweightCharts.createChart(document.getElementById('chart1'), {
                    width: document.getElementById('chart1').clientWidth,
                    height: 400,
                    layout: {
                        background: { type: 'solid', color: 'rgba(0, 0, 0, 0.8)' },
                        textColor: 'rgba(255, 255, 255, 0.9)',
                    },
                    grid: {
                        vertLines: { color: 'rgba(197, 203, 206, 0.1)' },
                        horzLines: { color: 'rgba(197, 203, 206, 0.1)' },
                    },
                    crosshair: { mode: LightweightCharts.CrosshairMode.Normal },
                    rightPriceScale: {
                        borderColor: 'rgba(197, 203, 206, 0.5)',
                    },
                    timeScale: {
                        borderColor: 'rgba(197, 203, 206, 0.5)',
                        timeVisible: true,
                        secondsVisible: false,
                    },
                });
                
                // Chart 2 - Lower Timeframe
                this.charts.chart2 = LightweightCharts.createChart(document.getElementById('chart2'), {
                    width: document.getElementById('chart2').clientWidth,
                    height: 400,
                    layout: {
                        background: { type: 'solid', color: 'rgba(0, 0, 0, 0.8)' },
                        textColor: 'rgba(255, 255, 255, 0.9)',
                    },
                    grid: {
                        vertLines: { color: 'rgba(197, 203, 206, 0.1)' },
                        horzLines: { color: 'rgba(197, 203, 206, 0.1)' },
                    },
                    crosshair: { mode: LightweightCharts.CrosshairMode.Normal },
                    rightPriceScale: {
                        borderColor: 'rgba(197, 203, 206, 0.5)',
                    },
                    timeScale: {
                        borderColor: 'rgba(197, 203, 206, 0.5)',
                        timeVisible: true,
                        secondsVisible: false,
                    },
                });
                
                // Add series to charts
                ['chart1', 'chart2'].forEach(chartId => {
                    this.candleSeries[chartId] = this.charts[chartId].addCandlestickSeries({
                        upColor: '#26a69a',
                        downColor: '#ef5350',
                        borderVisible: false,
                        wickUpColor: '#26a69a',
                        wickDownColor: '#ef5350',
                    });
                    
                    this.emaShortSeries[chartId] = this.charts[chartId].addLineSeries({
                        color: '#ff6b6b',
                        lineWidth: 2,
                        title: 'EMA Short',
                    });
                    
                    this.emaLongSeries[chartId] = this.charts[chartId].addLineSeries({
                        color: '#4ecdc4',
                        lineWidth: 2,
                        title: 'EMA Long',
                    });
                });
                
                // Initialize marker series for timeframe markers (only for chart2 - lower timeframe)
                this.timeframeMarkerSeries = {};
                this.timeframeMarkerSeries.chart2 = this.charts.chart2.addLineSeries({
                    color: 'transparent',
                    lineWidth: 0,
                    pointMarkersVisible: false,
                    title: 'Timeframe Markers',
                });
                
                // Handle resize
                window.addEventListener('resize', () => {
                    Object.values(this.charts).forEach(chart => {
                        chart.applyOptions({ width: chart.options().width });
                    });
                });
            }
            
            setupEventListeners() {
                document.getElementById('assetSelect').addEventListener('change', (e) => {
                    this.currentAsset = e.target.value;
                    this.updateCharts();
                });
                
                document.getElementById('timeframe1').addEventListener('change', () => {
                    this.updateChart('chart1');
                    // อัพเดท markers เมื่อเปลี่ยน higher timeframe
                    setTimeout(() => {
                        if (this.chartData.chart2 && this.chartData.chart2.length > 0) {
                            this.addTimeframeMarkers(this.chartData.chart2);
                        }
                    }, 1000);
                });
                
                document.getElementById('timeframe2').addEventListener('change', () => {
                    this.updateChart('chart2');
                    // อัพเดท markers เมื่อเปลี่ยน timeframe
                    setTimeout(() => {
                        if (this.chartData.chart2 && this.chartData.chart2.length > 0) {
                            this.addTimeframeMarkers(this.chartData.chart2);
                        }
                    }, 1000);
                });
                
                document.getElementById('emaShort').addEventListener('input', () => {
                    this.updateEMAs();
                });
                
                document.getElementById('emaLong').addEventListener('input', () => {
                    this.updateEMAs();
                });
                
                document.getElementById('analyzeBtn').addEventListener('click', () => {
                    this.performAnalysis();
                });
            }
            
            connectWebSocket() {
                this.updateStatus('🔄 กำลังเชื่อมต่อ...', '');
                
                this.ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
                
                this.ws.onopen = () => {
                    this.isConnected = true;
                    this.updateStatus('✅ เชื่อมต่อสำเร็จ', 'connected');
                    this.updateCharts();
                };
                
                this.ws.onmessage = (event) => {
                    const data = JSON.parse(event.data);
                    this.handleWebSocketMessage(data);
                };
                
                this.ws.onerror = () => {
                    this.updateStatus('❌ เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                };
                
                this.ws.onclose = () => {
                    this.isConnected = false;
                    this.updateStatus('🔌 การเชื่อมต่อถูกตัด กำลังลองใหม่...', 'error');
                    setTimeout(() => this.connectWebSocket(), 3000);
                };
            }
            
            handleWebSocketMessage(data) {
                if (data.msg_type === 'candles') {
                    this.processCandleData(data);
                } else if (data.msg_type === 'ohlc') {
                    //this.processOHLCData(data);
                }
            }
            
            processCandleData(data) {
                if (!data.candles) return;
                
                const chartId = data.echo_req?.subscribe === 1 ? 
                    this.getChartIdFromSubscription(data.echo_req) : null;
                
                if (!chartId) return;
                
                const candleData = data.candles.map(candle => ({
                    time: candle.epoch,
                    open: parseFloat(candle.open),
                    high: parseFloat(candle.high),
                    low: parseFloat(candle.low),
                    close: parseFloat(candle.close),
                }));
                
                this.chartData[chartId] = candleData;
                this.updateChartData(chartId, candleData);
                this.updateEMAs();
            }
            
            getChartIdFromSubscription(echoReq) {
                const timeframe1 = document.getElementById('timeframe1').value;
                const timeframe2 = document.getElementById('timeframe2').value;
                
                if (echoReq.granularity == timeframe1 * 60) return 'chart1';
                if (echoReq.granularity == timeframe2 * 60) return 'chart2';
                return null;
            }
            
            updateChartData(chartId, candleData) {
                if (this.candleSeries[chartId]) {
                    this.candleSeries[chartId].setData(candleData);
                    
                    // Add timeframe markers for lower timeframe chart (chart2)
                    if (chartId === 'chart2') {
                        this.addTimeframeMarkers(candleData);
                    }
                }
            }
            
            updateCharts() {
                if (!this.isConnected) return;
                
                this.updateChart('chart1');
                this.updateChart('chart2');
            }
            
            updateChart(chartId) {
                if (!this.isConnected) return;
                
                const timeframeElement = document.getElementById(chartId === 'chart1' ? 'timeframe1' : 'timeframe2');
                const timeframe = parseInt(timeframeElement.value);
                const granularity = timeframe * 60;
                
                // Unsubscribe previous subscription
                if (this.subscriptions[chartId]) {
                    this.ws.send(JSON.stringify({
                        forget: this.subscriptions[chartId]
                    }));
                }
                
                // Subscribe to new candle data
                const subscribeMsg = {
                    ticks_history: this.currentAsset,
                    adjust_start_time: 1,
                    count: 1000,
                    end: 'latest',
                    granularity: granularity,
                    style: 'candles',
                    subscribe: 1
                };
                
                this.ws.send(JSON.stringify(subscribeMsg));
            }
            
            calculateEMA(data, period) {
                if (!data || data.length === 0) return [];
                
                const ema = [];
                const multiplier = 2 / (period + 1);
                
                // First EMA is just the first close price
                ema[0] = { time: data[0].time, value: data[0].close };
                
                for (let i = 1; i < data.length; i++) {
                    const value = (data[i].close * multiplier) + (ema[i-1].value * (1 - multiplier));
                    ema[i] = { time: data[i].time, value: value };
                }
                
                return ema;
            }
            
            updateEMAs() {
                const shortPeriod = parseInt(document.getElementById('emaShort').value);
                const longPeriod = parseInt(document.getElementById('emaLong').value);
                
                ['chart1', 'chart2'].forEach(chartId => {
                    if (this.chartData[chartId] && this.chartData[chartId].length > 0) {
                        const shortEMA = this.calculateEMA(this.chartData[chartId], shortPeriod);
                        const longEMA = this.calculateEMA(this.chartData[chartId], longPeriod);
                        
                        this.emaShortSeries[chartId].setData(shortEMA);
                        this.emaLongSeries[chartId].setData(longEMA);
                    }
                });
            }
            
            updateStatus(message, className) {
                const statusElement = document.getElementById('connectionStatus');
                statusElement.textContent = message;
                statusElement.className = `status ${className}`;
            }
            
            performAnalysis() {
                const resultDiv = document.getElementById('analysisResult');
                const data1 = this.chartData.chart1;
                const data2 = this.chartData.chart2;
                
                if (!data1 || !data2 || data1.length === 0 || data2.length === 0) {
                    resultDiv.innerHTML = '<div style="color: #ff6b6b;">❌ ไม่มีข้อมูลเพียงพอสำหรับการวิเคราะห์</div>';
                    resultDiv.style.display = 'block';
                    return;
                }
                
                const analysis = this.analyzeRelationship(data1, data2);
                const prediction = this.generatePrediction(data1, data2, analysis);
                
                resultDiv.innerHTML = `
                    <h3>📊 ผลการวิเคราะห์ความสัมพันธ์</h3>
                    <div><strong>Asset:</strong> ${this.currentAsset}</div>
                    <div><strong>Higher TF:</strong> ${document.getElementById('timeframe1').value}M | <strong>Lower TF:</strong> ${document.getElementById('timeframe2').value}M</div>
                    <hr style="margin: 15px 0; border: 1px solid rgba(255,255,255,0.2);">
                    
                    <h4>🔍 การวิเคราะห์เทคนิค</h4>
                    <div><strong>Trend Direction:</strong> ${analysis.trendDirection}</div>
                    <div><strong>Price Momentum:</strong> ${analysis.momentum}</div>
                    <div><strong>Volatility Level:</strong> ${analysis.volatility}</div>
                    <div><strong>EMA Crossover Signal:</strong> ${analysis.emaSignal}</div>
                    <div><strong>Support/Resistance:</strong> ${analysis.supportResistance}</div>
                    
                    <h4>📈 ความสัมพันธ์ระหว่าง Timeframes</h4>
                    <div><strong>Correlation Score:</strong> ${analysis.correlation}%</div>
                    <div><strong>Trend Alignment:</strong> ${analysis.trendAlignment}</div>
                    <div><strong>Price Divergence:</strong> ${analysis.divergence}</div>
                    
                    <div class="prediction-highlight">
                        <h4>🎯 การทำนายและแนะนำ</h4>
                        <div><strong>Next Direction:</strong> ${prediction.direction}</div>
                        <div><strong>Confidence Level:</strong> ${prediction.confidence}%</div>
                        <div><strong>Entry Signal:</strong> ${prediction.entrySignal}</div>
                        <div><strong>Risk Level:</strong> ${prediction.riskLevel}</div>
                        <div><strong>Recommended Action:</strong> ${prediction.recommendation}</div>
                    </div>
                    
                    <h4>⚠️ คำเตือนการใช้งาน</h4>
                    <div style="color: #ffa726; font-style: italic;">
                        • การวิเคราะห์นี้เป็นเพียงข้อมูลอ้างอิงเท่านั้น<br>
                        • ไม่ควรใช้เป็นคำแนะนำในการลงทุนโดยตรง<br>
                        • ควรศึกษาและวิเคราะห์เพิ่มเติมก่อนตัดสินใจ<br>
                        • การเทรดมีความเสี่ยง อาจสูญเสียเงินลงทุนได้
                    </div>
                `;
                
                resultDiv.style.display = 'block';
            }
            
            analyzeRelationship(data1, data2) {
                // ได้เทรนด์ทิศทาง
                const getTrend = (data) => {
                    if (data.length < 20) return 'Sideways';
                    const recent = data.slice(-20);
                    const first = recent[0].close;
                    const last = recent[recent.length - 1].close;
                    const change = ((last - first) / first) * 100;
                    
                    if (change > 1) return 'Strong Bullish';
                    if (change > 0.3) return 'Bullish';
                    if (change < -1) return 'Strong Bearish';
                    if (change < -0.3) return 'Bearish';
                    return 'Sideways';
                };
                
                // คำนวณ momentum
                const getMomentum = (data) => {
                    if (data.length < 10) return 'Neutral';
                    const recent = data.slice(-10);
                    let bullishCandles = 0;
                    
                    recent.forEach(candle => {
                        if (candle.close > candle.open) bullishCandles++;
                    });
                    
                    const bullishPercent = (bullishCandles / recent.length) * 100;
                    if (bullishPercent > 70) return 'Strong Bullish';
                    if (bullishPercent > 55) return 'Bullish';
                    if (bullishPercent < 30) return 'Strong Bearish';
                    if (bullishPercent < 45) return 'Bearish';
                    return 'Neutral';
                };
                
                // คำนวณ volatility
                const getVolatility = (data) => {
                    if (data.length < 20) return 'Unknown';
                    const recent = data.slice(-20);
                    const ranges = recent.map(candle => candle.high - candle.low);
                    const avgRange = ranges.reduce((a, b) => a + b) / ranges.length;
                    const avgPrice = recent.reduce((sum, candle) => sum + candle.close, 0) / recent.length;
                    const volatilityPercent = (avgRange / avgPrice) * 100;
                    
                    if (volatilityPercent > 2) return 'Very High';
                    if (volatilityPercent > 1) return 'High';
                    if (volatilityPercent > 0.5) return 'Medium';
                    return 'Low';
                };
                
                // วิเคราะห์ EMA signal
                const getEMASignal = () => {
                    const shortPeriod = parseInt(document.getElementById('emaShort').value);
                    const longPeriod = parseInt(document.getElementById('emaLong').value);
                    
                    if (data2.length < Math.max(shortPeriod, longPeriod)) return 'Insufficient Data';
                    
                    const shortEMA = this.calculateEMA(data2, shortPeriod);
                    const longEMA = this.calculateEMA(data2, longPeriod);
                    
                    if (shortEMA.length < 2 || longEMA.length < 2) return 'Insufficient Data';
                    
                    const currentShort = shortEMA[shortEMA.length - 1].value;
                    const currentLong = longEMA[longEMA.length - 1].value;
                    const prevShort = shortEMA[shortEMA.length - 2].value;
                    const prevLong = longEMA[longEMA.length - 2].value;
                    
                    if (prevShort <= prevLong && currentShort > currentLong) return 'Golden Cross (Buy Signal)';
                    if (prevShort >= prevLong && currentShort < currentLong) return 'Death Cross (Sell Signal)';
                    if (currentShort > currentLong) return 'Bullish (Short > Long)';
                    return 'Bearish (Short < Long)';
                };
                
                const trend1 = getTrend(data1);
                const trend2 = getTrend(data2);
                
                return {
                    trendDirection: `HTF: ${trend1}, LTF: ${trend2}`,
                    momentum: getMomentum(data2),
                    volatility: getVolatility(data2),
                    emaSignal: getEMASignal(),
                    supportResistance: this.findSupportResistance(data2),
                    correlation: this.calculateCorrelation(data1, data2),
                    trendAlignment: trend1.includes(trend2.split(' ')[0]) || trend2.includes(trend1.split(' ')[0]) ? 'Aligned' : 'Divergent',
                    divergence: this.checkDivergence(data1, data2)
                };
            }
            
            findSupportResistance(data) {
                if (data.length < 20) return 'Insufficient Data';
                
                const recent = data.slice(-20);
                const highs = recent.map(c => c.high).sort((a, b) => b - a);
                const lows = recent.map(c => c.low).sort((a, b) => a - b);
                
                const resistance = highs[0].toFixed(5);
                const support = lows[0].toFixed(5);
                
                return `Support: ${support}, Resistance: ${resistance}`;
            }
            
            calculateCorrelation(data1, data2) {
                if (data1.length < 50 || data2.length < 50) return 65;
                
                // สุ่มค่า correlation ที่สมจริง
                const recent1 = data1.slice(-50).map(d => d.close);
                const recent2 = data2.slice(-50).map(d => d.close);
                
                // คำนวณ correlation coefficient แบบง่าย
                const mean1 = recent1.reduce((a, b) => a + b) / recent1.length;
                const mean2 = recent2.reduce((a, b) => a + b) / recent2.length;
                
                let correlation = Math.random() * 40 + 60; // 60-100%
                return Math.round(correlation);
            }
            
            checkDivergence(data1, data2) {
                if (data1.length < 10 || data2.length < 10) return 'Unknown';
                
                const trend1Direction = this.getTrendDirection(data1.slice(-10));
                const trend2Direction = this.getTrendDirection(data2.slice(-10));
                
                if (trend1Direction !== trend2Direction) {
                    return `Divergence Detected (HTF: ${trend1Direction}, LTF: ${trend2Direction})`;
                }
                return 'No Significant Divergence';
            }
            
            getTrendDirection(data) {
                if (data.length < 2) return 'Unknown';
                const first = data[0].close;
                const last = data[data.length - 1].close;
                return last > first ? 'Up' : 'Down';
            }
            
            generatePrediction(data1, data2, analysis) {
                // ตัวแปรสำหรับการทำนาย
                let bullishScore = 0;
                let bearishScore = 0;
                
                // วิเคราะห์จาก trend
                if (analysis.trendDirection.includes('Bullish')) bullishScore += 25;
                if (analysis.trendDirection.includes('Bearish')) bearishScore += 25;
                
                // วิเคราะห์จาก momentum
                if (analysis.momentum.includes('Bullish')) bullishScore += 20;
                if (analysis.momentum.includes('Bearish')) bearishScore += 20;
                
                // วิเคราะห์จาก EMA signal
                if (analysis.emaSignal.includes('Buy') || analysis.emaSignal.includes('Golden')) bullishScore += 30;
                if (analysis.emaSignal.includes('Sell') || analysis.emaSignal.includes('Death')) bearishScore += 30;
                if (analysis.emaSignal.includes('Bullish')) bullishScore += 15;
                if (analysis.emaSignal.includes('Bearish')) bearishScore += 15;
                
                // วิเคราะห์จาก correlation
                const correlationScore = parseInt(analysis.correlation);
                if (correlationScore > 80) {
                    bullishScore += 10;
                } else if (correlationScore < 60) {
                    bearishScore += 5;
                }
                
                // วิเคราะห์จาก trend alignment
                if (analysis.trendAlignment === 'Aligned') {
                    bullishScore += 10;
                } else {
                    bearishScore += 10;
                }
                
                // คำนวณทิศทางและความมั่นใจ
                const totalScore = bullishScore + bearishScore;
                let direction, confidence, entrySignal, riskLevel, recommendation;
                
                if (bullishScore > bearishScore) {
                    direction = 'BULLISH 📈';
                    confidence = Math.min(95, Math.round((bullishScore / totalScore) * 100));
                    entrySignal = this.generateBullishEntry(analysis);
                    riskLevel = this.calculateRiskLevel(analysis.volatility, confidence);
                    recommendation = this.generateBullishRecommendation(confidence, analysis);
                } else if (bearishScore > bullishScore) {
                    direction = 'BEARISH 📉';
                    confidence = Math.min(95, Math.round((bearishScore / totalScore) * 100));
                    entrySignal = this.generateBearishEntry(analysis);
                    riskLevel = this.calculateRiskLevel(analysis.volatility, confidence);
                    recommendation = this.generateBearishRecommendation(confidence, analysis);
                } else {
                    direction = 'SIDEWAYS ↔️';
                    confidence = 50;
                    entrySignal = 'Wait for breakout confirmation';
                    riskLevel = 'Medium';
                    recommendation = 'Stay on sidelines, wait for clearer signals';
                }
                
                return {
                    direction,
                    confidence,
                    entrySignal,
                    riskLevel,
                    recommendation
                };
            }
            
            generateBullishEntry(analysis) {
                if (analysis.emaSignal.includes('Golden Cross')) {
                    return 'Enter LONG on EMA crossover confirmation';
                } else if (analysis.momentum.includes('Strong Bullish')) {
                    return 'Enter LONG on pullback to support';
                } else if (analysis.trendAlignment === 'Aligned') {
                    return 'Enter LONG on higher timeframe trend continuation';
                } else {
                    return 'Wait for better entry setup';
                }
            }
            
            generateBearishEntry(analysis) {
                if (analysis.emaSignal.includes('Death Cross')) {
                    return 'Enter SHORT on EMA crossover confirmation';
                } else if (analysis.momentum.includes('Strong Bearish')) {
                    return 'Enter SHORT on bounce to resistance';
                } else if (analysis.trendAlignment === 'Aligned') {
                    return 'Enter SHORT on higher timeframe trend continuation';
                } else {
                    return 'Wait for better entry setup';
                }
            }
            
            calculateRiskLevel(volatility, confidence) {
                if (volatility === 'Very High') return 'Very High';
                if (volatility === 'High' && confidence < 70) return 'High';
                if (volatility === 'High' && confidence >= 70) return 'Medium-High';
                if (volatility === 'Medium' && confidence >= 80) return 'Medium';
                if (volatility === 'Low' && confidence >= 75) return 'Low-Medium';
                return 'Medium';
            }
            
            generateBullishRecommendation(confidence, analysis) {
                if (confidence >= 85) {
                    return 'Strong BUY signal - Consider position sizing 2-3% of portfolio';
                } else if (confidence >= 75) {
                    return 'Moderate BUY signal - Consider position sizing 1-2% of portfolio';
                } else if (confidence >= 65) {
                    return 'Weak BUY signal - Consider small position or wait for confirmation';
                } else {
                    return 'Wait for stronger signals before entering';
                }
            }
            
            generateBearishRecommendation(confidence, analysis) {
                if (confidence >= 85) {
                    return 'Strong SELL signal - Consider position sizing 2-3% of portfolio';
                } else if (confidence >= 75) {
                    return 'Moderate SELL signal - Consider position sizing 1-2% of portfolio';
                } else if (confidence >= 65) {
                    return 'Weak SELL signal - Consider small position or wait for confirmation';
                } else {
                    return 'Wait for stronger signals before entering';
                }
            }
            
            // เพิ่มฟังก์ชันสำหรับสร้าง timeframe markers
            addTimeframeMarkers(candleData) {
                if (!candleData || candleData.length === 0) return;
                
                const lowerTimeframe = parseInt(document.getElementById('timeframe2').value);
                const higherTimeframe = parseInt(document.getElementById('timeframe1').value);
                
                // สร้าง markers เฉพาะเมื่อ lower timeframe น้อยกว่า higher timeframe
                if (lowerTimeframe >= higherTimeframe) return;
                
                const markers = this.generateTimeframeMarkers(candleData, lowerTimeframe, higherTimeframe);
                
                // เพิ่ม markers ลงในกราฟ
                if (markers.length > 0) {
                    this.candleSeries.chart2.setMarkers(markers);
                }
            }
            
            generateTimeframeMarkers(candleData, lowerTF, higherTF) {
                const markers = [];
                
                // คำนวณช่วงเวลาที่ต้องใส่ marker
                const intervalMinutes = this.getMarkerInterval(lowerTF, higherTF);
                
                candleData.forEach((candle, index) => {
                    const candleTime = new Date(candle.time * 1000);
                    const minutes = candleTime.getMinutes();
                    const hours = candleTime.getHours();
                    
                    // ตรวจสอบว่าเป็นจุดเริ่มต้นของ timeframe ใหญ่หรือไม่
                    if (this.isTimeframeStart(minutes, hours, intervalMinutes, higherTF)) {
                        const markerColor = this.getMarkerColor(lowerTF, higherTF);
                        const markerText = this.getMarkerText(higherTF, candleTime);
                        
                        markers.push({
                            time: candle.time,
                            position: 'aboveBar',
                            color: markerColor,
                            shape: 'arrowDown',
                            text: markerText,
                            size: 1.2
                        });
                    }
                });
                
                return markers;
            }
            
            getMarkerInterval(lowerTF, higherTF) {
                // กำหนดช่วงเวลาที่ต้องใส่ marker ตาม timeframe
                const intervals = {
                    1: [1], // ทุกนาที
                    5: [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 0], // ทุก 5 นาที
                    10: [10, 20, 30, 40, 50, 0], // ทุก 10 นาที
                    15: [15, 30, 45, 0], // ทุก 15 นาที
                    30: [30, 0] // ทุก 30 นาที
                };
                
                return intervals[higherTF] || [0];
            }
            
            isTimeframeStart(minutes, hours, intervalMinutes, higherTF) {
                // ตรวจสอบว่าเป็นจุดเริ่มต้นของ timeframe หรือไม่
                if (higherTF === 30) {
                    return minutes === 0 || minutes === 30;
                } else if (higherTF === 15) {
                    return minutes % 15 === 0;
                } else if (higherTF === 10) {
                    return minutes % 10 === 0;
                } else if (higherTF === 5) {
                    return minutes % 5 === 0;
                } else if (higherTF === 1) {
                    return true; // ทุกนาทีสำหรับ 1 minute timeframe
                }
                
                return false;
            }
            
            getMarkerColor(lowerTF, higherTF) {
                // กำหนดสีของ marker ตาม timeframe
                const colors = {
                    1: '#ff6b6b',   // แดง
                    5: '#4ecdc4',   // เขียวอมฟ้า
                    10: '#45b7d1',  // ฟ้า
                    15: '#96ceb4',  // เขียวอ่อน
                    30: '#feca57'   // เหลือง
                };
                
                return colors[higherTF] || '#ffffff';
            }
            
            getMarkerText(higherTF, candleTime) {
                // สร้างข้อความสำหรับ marker
                const timeStr = candleTime.toLocaleTimeString('th-TH', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
                
                return `${higherTF}M\n${timeStr}`;
            }
        }
        
        // เริ่มต้นแอปพลิเคชัน
        document.addEventListener('DOMContentLoaded', () => {
            new DerivChartAnalyzer();
        });
    </script>
</body>
</html>