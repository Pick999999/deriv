<?php
/*
analyconflict.php
สร้าง htmlpage สำหรับแสดง กราฟ candlestick + ema3 + ema5 โดยดึงข้อมูลจาก deriv.com ตามช่วงเวลา
และใช้  https://unpkg.com/lightweight-charts@3.8.0/dist/lightweight-charts.standalone.production.js 
ในหน้า เพจ ต้องมี 
 1.dtpicker 2 อันให้เลือกช่วงเวลา  ที่จะดึง candle จาก deriv.com กูไม่เอา mockdata
 2.list box ให้เลือก asset
 3.list box ให้เลือก timeframe
 4.lightweight chart
 5.มีปุ่ม +1Hour,-1Hour และเมื่อ load data ให้ทำการบันทึก ข้อมูล ลง localStrage และเมื่อเปิดเพจมาให้นำค่ามาใส่ 
 6.ในจุดที่ ema3 อยู่ เหนือ ema5 สี แท่งเทียนควรจะเป็น สีเขียว และ ถ้า ema5 อยู่เหนือ ema3 สี แท่งเทียนควรจะเป็น สีแดง
 ถ้าไม่ตรงตามนี้ จะเรียกว่า conflict ให้ แสดงจุด conflict ใน table และสร้าง marker ให้แสดงจุด conflict บน graph ด้วย
 7.สรุป แท่งเทียนทั้งหมดว่า มีกีแท่งและ มีจุด conflict กี่อัน
 8.เพิ่ม function การสร้าง  Backtest สำหรับการเทรดโดย ให้เข้าเทรดใน จุดที่ไม่เกิด conflict แล้ว ดูผลว่า win หรือ loss และสร้าง รายงานออกมาเป็น Table และ ผลสรุปการ win/loss

 */
 ?>
 <!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candlestick Chart with EMA Analysis</title>
    <script src="https://unpkg.com/lightweight-charts@3.8.0/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .controls {
            display: flex;
            gap: 20px;
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
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .chart-container {
            margin: 20px 0;
            height: 500px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .summary {
            display: flex;
            gap: 30px;
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .summary-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .conflict-table {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .backtest-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .backtest-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
        }
        .backtest-results {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .backtest-summary {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .backtest-table {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            max-height: 400px;
            overflow-y: auto;
        }
        .win-trade {
            background-color: #d4edda;
            color: #155724;
        }
        .loss-trade {
            background-color: #f8d7da;
            color: #721c24;
        }
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .stat-label {
            font-weight: bold;
        }
        .stat-value {
            color: #333;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .error {
            color: #dc3545;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Candlestick Chart with EMA Analysis</h1>
        
        <div class="controls">
            <div class="control-group">
                <label for="startDate">วันที่เริ่มต้น:</label>
                <input type="datetime-local" id="startDate">
            </div>
            
            <div class="control-group">
                <label for="endDate">วันที่สิ้นสุด:</label>
                <input type="datetime-local" id="endDate">
            </div>
            
            <div class="control-group">
                <label>&nbsp;</label>
                <div style="display: flex; gap: 5px;">
                    <button type="button" onclick="adjustTime(-1)">-1 Hour</button>
                    <button type="button" onclick="adjustTime(1)">+1 Hour</button>
                </div>
            </div>
            
            <div class="control-group">
                <label for="asset">Asset:</label>
                <select id="asset">
                    <option value="R_50">Volatility 50 Index</option>
                    <option value="R_75">Volatility 75 Index</option>
                    <option value="R_100">Volatility 100 Index</option>
                    <option value="frxEURUSD">EUR/USD</option>
                    <option value="frxGBPUSD">GBP/USD</option>
                    <option value="frxUSDJPY">USD/JPY</option>
                    <option value="frxAUDUSD">AUD/USD</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="timeframe">Timeframe:</label>
                <select id="timeframe">
                    <option value="60">1 นาที</option>
                    <option value="120">2 นาที</option>
                    <option value="180">3 นาที</option>
                    <option value="300">5 นาที</option>
                    <option value="600">10 นาที</option>
                    <option value="900">15 นาที</option>
                    <option value="1800">30 นาที</option>
                    <option value="3600">1 ชั่วโมง</option>
                </select>
            </div>
            
            <button onclick="loadChart()">โหลดข้อมูล</button>
        </div>
        
        <div class="summary">
            <div class="summary-item">
                <div class="summary-label">จำนวนแท่งเทียนทั้งหมด</div>
                <div class="summary-value" id="totalCandles">0</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">จุด Conflict</div>
                <div class="summary-value" id="conflictCount" style="color: #dc3545;">0</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">เปอร์เซ็นต์ Conflict</div>
                <div class="summary-value" id="conflictPercent" style="color: #dc3545;">0%</div>
            </div>
        </div>
        
        <div id="chartContainer" class="chart-container"></div>
        
        .conflict-row {
            background-color: #fff3cd;
        }
        
        <div class="backtest-section">
            <h3>Backtest Trading System</h3>
            <div class="backtest-controls">
                <div class="control-group">
                    <label for="tradeDirection">ทิศทางการเทรด:</label>
                    <select id="tradeDirection">
                        <option value="call">Call (เมื่อ EMA3 > EMA5)</option>
                        <option value="put">Put (เมื่อ EMA5 > EMA3)</option>
                        <option value="both">Both Directions</option>
                    </select>
                </div>
                <div class="control-group">
                    <label for="tradeDuration">ระยะเวลาการเทรด (นาที):</label>
                    <select id="tradeDuration">
                        <option value="1">1 นาที</option>
                        <option value="2">2 นาที</option>
                        <option value="3">3 นาที</option>
                        <option value="5">5 นาที</option>
                        <option value="10">10 นาที</option>
                    </select>
                </div>
                <div class="control-group">
                    <label for="investAmount">จำนวนเงินลงทุน:</label>
                    <input type="number" id="investAmount" value="100" min="1">
                </div>
                <div class="control-group">
                    <label for="payout">Payout (%):</label>
                    <input type="number" id="payout" value="80" min="1" max="100">
                </div>
                <button onclick="runBacktest2()">เริ่ม Backtest</button>
				<button onclick="runBacktest3()">เริ่ม Backtest Case 2</button>
				
            </div>
			<div id="tradeResult" class="bordergray flex">
			  <div id="" class="bordergray flex" style='height:20px;padding:20px;background:#008080;color:white'>
			       ผลการเทรด
			  </div>
			  <div id="tradeResult" class="bordergray flex">
			  </div>

				     
			</div>
            
            <div class="backtest-results" id="backtestResults" style="display: none;">
                <div class="backtest-summary">
                    <h4>สรุปผลการ Backtest</h4>
                    <div class="stat-item">
                        <span class="stat-label">จำนวนสัญญาณทั้งหมด:</span>
                        <span class="stat-value" id="totalSignals">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">จำนวนการเทรด:</span>
                        <span class="stat-value" id="totalTrades">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Win:</span>
                        <span class="stat-value" id="winTrades" style="color: #28a745;">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Loss:</span>
                        <span class="stat-value" id="lossTrades" style="color: #dc3545;">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Win Rate:</span>
                        <span class="stat-value" id="winRate">0%</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">กำไร/ขาดทุน:</span>
                        <span class="stat-value" id="totalProfit">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">ROI:</span>
                        <span class="stat-value" id="roi">0%</span>
                    </div>
                </div>
                
                <div class="backtest-table">
                    <h4>รายการเทรด</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>เวลาเข้า</th>
                                <th>ทิศทาง</th>
                                <th>ราคาเข้า</th>
                                <th>ราคาออก</th>
                                <th>ผลลัพธ์</th>
                                <th>กำไร/ขาดทุน</th>
                            </tr>
                        </thead>
                        <tbody id="backtestTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="conflict-table">
            <h3>ตาราง Conflict Points</h3>
            <table id="conflictTable">
                <thead>
                    <tr>
                        <th>เวลา</th>
                        <th>ราคาปิด</th>
                        <th>EMA3</th>
                        <th>EMA5</th>
						<th>MACD</th>
                        <th>สีแท่งเทียน</th>
                        <th>สีที่ควรจะเป็น</th>
                        <th>ประเภท Conflict</th>
                    </tr>
                </thead>
                <tbody id="conflictTableBody">
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let chart;
        let candlestickSeries;
        let ema3Series;
        let ema5Series;
		let ema3Data;
        let ema5Data;

        let conflictMarkers = [];
		let candleData = null;
		let ema3DataForBacktest  = null;
		let ema5DataForBacktest = null;
        
        // Initialize default dates
        function initializeDates() {
            // Load from localStorage or use defaults
            const savedData = localStorage.getItem('chartSettings');
            if (savedData) {
                const settings = JSON.parse(savedData);
                document.getElementById('startDate').value = settings.startDate || '';
                document.getElementById('endDate').value = settings.endDate || '';
                document.getElementById('asset').value = settings.asset || 'R_50';
                document.getElementById('timeframe').value = settings.timeframe || '60';
            } else {
                const now = new Date();
                const oneWeekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                
                document.getElementById('startDate').value = oneWeekAgo.toISOString().slice(0, 16);
                document.getElementById('endDate').value = now.toISOString().slice(0, 16);
            }
        }
        
        // Adjust time for both date pickers
        function adjustTime(hours) {
            const startInput = document.getElementById('startDate');
            const endInput = document.getElementById('endDate');
            
            if (startInput.value) {
                const startDate = new Date(startInput.value);
                startDate.setHours(startDate.getHours() + hours);
                startInput.value = startDate.getFullYear() + '-' + 
                    String(startDate.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(startDate.getDate()).padStart(2, '0') + 'T' + 
                    String(startDate.getHours()).padStart(2, '0') + ':' + 
                    String(startDate.getMinutes()).padStart(2, '0');
            }
            
            if (endInput.value) {
                const endDate = new Date(endInput.value);
                endDate.setHours(endDate.getHours() + hours);
                endInput.value = endDate.getFullYear() + '-' + 
                    String(endDate.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(endDate.getDate()).padStart(2, '0') + 'T' + 
                    String(endDate.getHours()).padStart(2, '0') + ':' + 
                    String(endDate.getMinutes()).padStart(2, '0');
            }
        }
        
        // Save settings to localStorage
        function saveSettings() {
            const settings = {
                startDate: document.getElementById('startDate').value,
                endDate: document.getElementById('endDate').value,
                asset: document.getElementById('asset').value,
                timeframe: document.getElementById('timeframe').value
            };
            localStorage.setItem('chartSettings', JSON.stringify(settings));
        }
        
        // Calculate EMA
        function calculateEMA(data, period) {
            const ema = [];
            const multiplier = 2 / (period + 1);
            
            if (data.length === 0) return ema;
            
            // First EMA value is the first close price
            ema[0] = {
                time: data[0].time,
                value: data[0].close
            };
            
            // Calculate subsequent EMA values
            for (let i = 1; i < data.length; i++) {
                const emaValue = (data[i].close * multiplier) + (ema[i - 1].value * (1 - multiplier));
                ema.push({
                    time: data[i].time,
                    value: emaValue
                });
            }
            
            return ema;
        }
        
        // Deriv WebSocket connection
        let ws;
        
        function connectToDerivAPI() {
            return new Promise((resolve, reject) => {
                ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=1089');
                
                ws.onopen = function() {
                    console.log('Connected to Deriv API');
                    resolve();
                };
                
                ws.onerror = function(error) {
                    console.error('WebSocket error:', error);
                    reject(error);
                };
                
                ws.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    if (data.msg_type === 'candles') {
                        handleCandleData(data);
                    }
                };
            });
        }
        
        let candleDataPromise;
        
        function requestCandleData() {
            return new Promise((resolve, reject) => {
                const startTime = Math.floor(new Date(document.getElementById('startDate').value).getTime() / 1000);
                const endTime = Math.floor(new Date(document.getElementById('endDate').value).getTime() / 1000);
                const symbol = document.getElementById('asset').value;
                const granularity = parseInt(document.getElementById('timeframe').value);
                
                candleDataPromise = { resolve, reject };
                
                const request = {
                    ticks_history: symbol,
                    start: startTime,
                    end: endTime,
                    granularity: granularity,
                    style: 'candles'
                };
                
                ws.send(JSON.stringify(request));
            });
        }
        
        function handleCandleData(data) {
            if (data.error) {
                console.error('API Error:', data.error);
                candleDataPromise.reject(new Error(data.error.message));
                return;
            }
            
            const candles = data.candles || [];
            const formattedData = candles.map(candle => ({
                time: candle.epoch,
                open: parseFloat(candle.open),
                high: parseFloat(candle.high),
                low: parseFloat(candle.low),
                close: parseFloat(candle.close)
            })); 
            candleDataForBacktest  =   formattedData;
			candleData = formattedData;
            
            candleDataPromise.resolve(formattedData);
        }
        
        // Detect conflicts
        function detectConflicts(candleData, ema3Data, ema5Data) {
            const conflicts = [];
            
            for (let i = 0; i < candleData.length; i++) {
                const candle = candleData[i];
                const ema3 = ema3Data[i];
                const ema5 = ema5Data[i];
                
                if (!ema3 || !ema5) continue;
                
                const isGreen = candle.close > candle.open;
                const isRed = candle.close < candle.open;
                const ema3AboveEma5 = ema3.value > ema5.value;
                const ema5AboveEma3 = ema5.value > ema3.value;
                
                let isConflict = false;
                let conflictType = '';
                let expectedColor = '';
                let actualColor = '';
                candleData[i].emaConflict = 'n';

				if (ema3AboveEma5 ) {
                   candleData[i].emaAbove = 3 ;
				} else {
                   candleData[i].emaAbove = 5 ;
				}
                if (ema3AboveEma5 && isRed) {
                    isConflict = true;
                    conflictType = 'EMA3 > EMA5 แต่แท่งเทียนสีแดง';
                    expectedColor = 'เขียว';
                    actualColor = 'แดง';
					candleData[i].emaConflict = 'y';
					
					
                } else if (ema5AboveEma3 && isGreen) {
                    isConflict = true;
                    conflictType = 'EMA5 > EMA3 แต่แท่งเทียนสีเขียว';
                    expectedColor = 'แดง';
                    actualColor = 'เขียว';
					candleData[i].emaConflict = 'y';
					
                }

                
                if (isConflict) {
                    conflicts.push({
                        time: candle.time,
                        price: candle.close,
                        ema3: ema3.value,
                        ema5: ema5.value,
                        macd : ema3.value-ema5.value,
                        actualColor: actualColor,
                        expectedColor: expectedColor,
                        conflictType: conflictType
                    });
                }
            }
            
            return conflicts;
        }
        
        // Create chart
        function createChart() {
            const container = document.getElementById('chartContainer');
            container.innerHTML = '';
            
            chart = LightweightCharts.createChart(container, {
                width: container.offsetWidth,
                height: 500,
                rightPriceScale: {
                    visible: true,
                },
                leftPriceScale: {
                    visible: false,
                },
                layout: {
                    backgroundColor: '#ffffff',
                    textColor: '#333',
                },
                grid: {
                    vertLines: {
                        color: '#e1e1e1',
                    },
                    horzLines: {
                        color: '#e1e1e1',
                    },
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                timeScale: {
                    timeVisible: true,
					rightOffset: 20,  // เพิ่มบรรทัดนี้
					barSpacing: 20,
                    minBarSpacing: 3.5,
                    secondsVisible: false,
                },
            });
            
            // Create series
            candlestickSeries = chart.addCandlestickSeries({
                upColor: '#4CAF50',
                downColor: '#F44336',
                borderDownColor: '#F44336',
                borderUpColor: '#4CAF50',
                wickDownColor: '#F44336',
                wickUpColor: '#4CAF50',
            });
            
            ema3Series = chart.addLineSeries({
                color: '#2196F3',
                lineWidth: 2,
                title: 'EMA3',
            });
            
            ema5Series = chart.addLineSeries({
                color: '#ff0000',
                lineWidth: 2,
                title: 'EMA5',
            });
        }
        
        // Load chart data
        async function loadChart() {
            // Save current settings before loading
            saveSettings();
            
            const container = document.getElementById('chartContainer');
            container.innerHTML = '<div class="loading">กำลังเชื่อมต่อกับ Deriv API...</div>';
            
            try {
                // Connect to Deriv WebSocket API
                if (!ws || ws.readyState !== WebSocket.OPEN) {
                    await connectToDerivAPI();
                }
                
                container.innerHTML = '<div class="loading">กำลังโหลดข้อมูลจาก Deriv...</div>';
                
                // Request candle data from Deriv
                const rawData = await requestCandleData();
                
                if (rawData.length === 0) {
                    container.innerHTML = '<div class="error">ไม่พบข้อมูลในช่วงเวลาที่เลือก</div>';
                    return;
                }
                
                // Calculate EMAs
                ema3Data = calculateEMA(rawData, 3);
                ema5Data = calculateEMA(rawData, 5);

				/*ema3DataForBacktest  = ema3Data ;
				ema5DataForBacktest  = ema5Data ;
				*/
				 
				
                
                // Detect conflicts
                const conflicts = detectConflicts(rawData, ema3Data, ema5Data);
				console.log('Conflicts',conflicts);
				
                
                // Create chart
                createChart();
                
                // Add data to series
                candlestickSeries.setData(rawData);
                ema3Series.setData(ema3Data);
                ema5Series.setData(ema5Data);
                
                // Add conflict markers
                const markers = conflicts.map(conflict => ({
                    time: conflict.time,
                    position: 'aboveBar',
                    color: '#FF6B6B',
                    shape: 'circle',
                    text: '⚠️',
                    size: 1
                }));
                
                candlestickSeries.setMarkers(markers);
                
                // Update summary
                document.getElementById('totalCandles').textContent = rawData.length;
                document.getElementById('conflictCount').textContent = conflicts.length;
                document.getElementById('conflictPercent').textContent = 
                    ((conflicts.length / rawData.length) * 100).toFixed(1) + '%';
                
                let candleDataForBacktest = [];
        let ema3DataForBacktest = [];
        let ema5DataForBacktest = [];
        
        // Generate trading signals (non-conflict points)
        function generateTradingSignals(candleData, ema3Data, ema5Data, conflicts) {
            const signals = [];
            const conflictTimes = new Set(conflicts.map(c => c.time));
            
            for (let i = 0; i < candleData.length; i++) {
                const candle = candleData[i];
                const ema3 = ema3Data[i];
                const ema5 = ema5Data[i];
                
                if (!ema3 || !ema5 || conflictTimes.has(candle.time)) continue;
                
                const isGreen = candle.close > candle.open;
                const isRed = candle.close < candle.open;
                const ema3AboveEma5 = ema3.value > ema5.value;
                const ema5AboveEma3 = ema5.value > ema3.value;
                
                // Valid signal conditions (no conflict)
                if (ema3AboveEma5 && isGreen) {
                    signals.push({
                        time: candle.time,
                        direction: 'call',
                        price: candle.close,
                        ema3: ema3.value,
                        ema5: ema5.value
                    });
                } else if (ema5AboveEma3 && isRed) {
                    signals.push({
                        time: candle.time,
                        direction: 'put',
                        price: candle.close,
                        ema3: ema3.value,
                        ema5: ema5.value
                    });
                }
            }
            
            return signals;
        }
        
        // Run backtest
        function BrunBacktest() {

            alert('a')
            if (candleDataForBacktest.length === 0) {
                alert('กรุณาโหลดข้อมูลก่อนทำ Backtest');
                return;
            }
            
            const tradeDirection = document.getElementById('tradeDirection').value;
            const tradeDuration = parseInt(document.getElementById('tradeDuration').value);
            const investAmount = parseFloat(document.getElementById('investAmount').value);
            const payout = parseFloat(document.getElementById('payout').value);
            
            // Detect conflicts first
            const conflicts = detectConflicts(candleDataForBacktest, ema3DataForBacktest, ema5DataForBacktest);
            
            // Generate trading signals
            const allSignals = generateTradingSignals(candleDataForBacktest, ema3DataForBacktest, ema5DataForBacktest, conflicts);
            
            // Filter signals based on trade direction
            let signals = allSignals;
            if (tradeDirection === 'call') {
                signals = allSignals.filter(s => s.direction === 'call');
            } else if (tradeDirection === 'put') {
                signals = allSignals.filter(s => s.direction === 'put');
            }
            
            // Execute trades
            const trades = [];
            const timeframe = parseInt(document.getElementById('timeframe').value);
            
            for (const signal of signals) {
                const entryTime = signal.time;
                const exitTime = entryTime + (tradeDuration * 60);
                
                // Find exit candle
                const exitCandle = candleDataForBacktest.find(c => c.time >= exitTime);
                if (!exitCandle) continue;
                
                const entryPrice = signal.price;
                const exitPrice = exitCandle.close;
                
                let isWin = false;
                if (signal.direction === 'call') {
                    isWin = exitPrice > entryPrice;
                } else if (signal.direction === 'put') {
                    isWin = exitPrice < entryPrice;
                }
                
                const profit = isWin ? (investAmount * payout / 100) : -investAmount;
                
                trades.push({
                    entryTime: entryTime,
                    direction: signal.direction,
                    entryPrice: entryPrice,
                    exitPrice: exitPrice,
                    isWin: isWin,
                    profit: profit
                });
            }
            
            // Display results
            displayBacktestResults(allSignals, trades, investAmount);
        }
        
        // Display backtest results
        function displayBacktestResults(signals, trades, investAmount) {
            const resultsDiv = document.getElementById('backtestResults');
            resultsDiv.style.display = 'grid';
            
            const winTrades = trades.filter(t => t.isWin);
            const lossTrades = trades.filter(t => !t.isWin);
            const totalProfit = trades.reduce((sum, t) => sum + t.profit, 0);
            const totalInvested = trades.length * investAmount;
            const winRate = trades.length > 0 ? (winTrades.length / trades.length * 100).toFixed(1) : 0;
            const roi = totalInvested > 0 ? (totalProfit / totalInvested * 100).toFixed(1) : 0;
            
            // Update summary
            document.getElementById('totalSignals').textContent = signals.length;
            document.getElementById('totalTrades').textContent = trades.length;
            document.getElementById('winTrades').textContent = winTrades.length;
            document.getElementById('lossTrades').textContent = lossTrades.length;
            document.getElementById('winRate').textContent = winRate + '%';
            document.getElementById('totalProfit').textContent = totalProfit.toFixed(2);
            document.getElementById('roi').textContent = roi + '%';
            
            // Update profit color
            const profitElement = document.getElementById('totalProfit');
            profitElement.style.color = totalProfit >= 0 ? '#28a745' : '#dc3545';
            
            // Update table
            const tableBody = document.getElementById('backtestTableBody');
            tableBody.innerHTML = '';
            
            trades.forEach(trade => {
                const row = document.createElement('tr');
                row.className = trade.isWin ? 'win-trade' : 'loss-trade';
                
                const entryTimeStr = new Date(trade.entryTime * 1000).toLocaleString('th-TH');
                const directionText = trade.direction === 'call' ? 'Call ↗' : 'Put ↘';
                const resultText = trade.isWin ? 'Win' : 'Loss';
                
                row.innerHTML = `
                    <td>${entryTimeStr}</td>
                    <td>${directionText}</td>
                    <td>${trade.entryPrice.toFixed(4)}</td>
                    <td>${trade.exitPrice.toFixed(4)}</td>
                    <td>${resultText}</td>
                    <td>${trade.profit.toFixed(2)}</td>
                `;
                
                tableBody.appendChild(row);
            });
            
            if (trades.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="6" style="text-align: center; color: #666;">ไม่มีสัญญาณการเทรด</td>';
                tableBody.appendChild(row);
            }
        }
                updateConflictTable(conflicts);
                
            } catch (error) {
                console.error('Error loading chart:', error);
                container.innerHTML = `<div class="error">เกิดข้อผิดพลาดในการโหลดข้อมูล: ${error.message}</div>`;
            }
        } 

		// Run backtest
        function runBacktest() {
			


            if (candleDataForBacktest.length === 0) {
                alert('กรุณาโหลดข้อมูลก่อนทำ Backtest');
                return;
            }
            
            const tradeDirection = document.getElementById('tradeDirection').value;
            const tradeDuration = parseInt(document.getElementById('tradeDuration').value);
            const investAmount = parseFloat(document.getElementById('investAmount').value);
            const payout = parseFloat(document.getElementById('payout').value);
            
            // Detect conflicts first
            const conflicts = detectConflicts(candleDataForBacktest, ema3DataForBacktest, ema5DataForBacktest);
            
            // Generate trading signals
            const allSignals = generateTradingSignals(candleDataForBacktest, ema3DataForBacktest, ema5DataForBacktest, conflicts);
            
            // Filter signals based on trade direction
            let signals = allSignals;
            if (tradeDirection === 'call') {
                signals = allSignals.filter(s => s.direction === 'call');
            } else if (tradeDirection === 'put') {
                signals = allSignals.filter(s => s.direction === 'put');
            }
            
            // Execute trades
            const trades = [];
            const timeframe = parseInt(document.getElementById('timeframe').value);
            
            for (const signal of signals) {
                const entryTime = signal.time;
                const exitTime = entryTime + (tradeDuration * 60);
                
                // Find exit candle
                const exitCandle = candleDataForBacktest.find(c => c.time >= exitTime);
                if (!exitCandle) continue;
                
                const entryPrice = signal.price;
                const exitPrice = exitCandle.close;
                
                let isWin = false;
                if (signal.direction === 'call') {
                    isWin = exitPrice > entryPrice;
                } else if (signal.direction === 'put') {
                    isWin = exitPrice < entryPrice;
                }
                
                const profit = isWin ? (investAmount * payout / 100) : -investAmount;
                
                trades.push({
                    entryTime: entryTime,
                    direction: signal.direction,
                    entryPrice: entryPrice,
                    exitPrice: exitPrice,
                    isWin: isWin,
                    profit: profit
                });
            }
            
            // Display results
            displayBacktestResults(allSignals, trades, investAmount);
        }
        
        // Display backtest results
        function displayBacktestResults(signals, trades, investAmount) {
            const resultsDiv = document.getElementById('backtestResults');
            resultsDiv.style.display = 'grid';
            
            const winTrades = trades.filter(t => t.isWin);
            const lossTrades = trades.filter(t => !t.isWin);
            const totalProfit = trades.reduce((sum, t) => sum + t.profit, 0);
            const totalInvested = trades.length * investAmount;
            const winRate = trades.length > 0 ? (winTrades.length / trades.length * 100).toFixed(1) : 0;
            const roi = totalInvested > 0 ? (totalProfit / totalInvested * 100).toFixed(1) : 0;
            
            // Update summary
            document.getElementById('totalSignals').textContent = signals.length;
            document.getElementById('totalTrades').textContent = trades.length;
            document.getElementById('winTrades').textContent = winTrades.length;
            document.getElementById('lossTrades').textContent = lossTrades.length;
            document.getElementById('winRate').textContent = winRate + '%';
            document.getElementById('totalProfit').textContent = totalProfit.toFixed(2);
            document.getElementById('roi').textContent = roi + '%';
            
            // Update profit color
            const profitElement = document.getElementById('totalProfit');
            profitElement.style.color = totalProfit >= 0 ? '#28a745' : '#dc3545';
            
            // Update table
            const tableBody = document.getElementById('backtestTableBody');
            tableBody.innerHTML = '';
            
            trades.forEach(trade => {
                const row = document.createElement('tr');
                row.className = trade.isWin ? 'win-trade' : 'loss-trade';
                
                const entryTimeStr = new Date(trade.entryTime * 1000).toLocaleString('th-TH');
                const directionText = trade.direction === 'call' ? 'Call ↗' : 'Put ↘';
                const resultText = trade.isWin ? 'Win' : 'Loss';
                
                row.innerHTML = `
                    <td>${entryTimeStr}</td>
                    <td>${directionText}</td>
                    <td>${trade.entryPrice.toFixed(4)}</td>
                    <td>${trade.exitPrice.toFixed(4)}</td>
                    <td>${resultText}</td>
                    <td>${trade.profit.toFixed(2)}</td>
                `;
                
                tableBody.appendChild(row);
            });
            
            if (trades.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="6" style="text-align: center; color: #666;">ไม่มีสัญญาณการเทรด</td>';
                tableBody.appendChild(row);
            }
        } 

function runBacktest2 () {
                 
				 //formattedData;
				 /*
				 console.log(candleData);
                 console.log(ema3Data);
                 console.log(ema5Data);
				 */
                 console.log(candleData);
				 
				 for (i=0;i<=candleData.length-1 ;i++ ) {
					 if (candleData[i].close > candleData[i].open) {
						 candleData[i].color = 'Green' ;						 
					 }
					 if (candleData[i].close < candleData[i].open) {
						 candleData[i].color = 'Red' ;
					 }
					 if (candleData[i].close === candleData[i].open) {
						 candleData[i].color = 'Gray' ;
					 }				
					  

				 } // end for 
				 for (i=0;i<=candleData.length-2 ;i++ ) {
					 if (candleData[i].emaConflict==='y') {
                         candleData[i].action = 'Idle'; 
					 } else {
					   if (candleData[i].emaAbove ===3) {
                          candleData[i].action = 'CALL'; 
					   } else {
                          candleData[i].action = 'PUT'; 
					   }
                     }
					 candleData[i].nextColor =candleData[i+1].color ;
				 
				 } // end for 
				 numTrade = 0 ; numConflict= 0 ; numWin = 0 ;numLoss = 0 ;
				 for (i=0;i<=candleData.length-2 ;i++ ) {
					 candleData[i].winStatus = '';
					 if (candleData[i].emaConflict==='y') {
                        numConflict++ ;
					 }
					 if (candleData[i].action !=='Idle') {
                        numTrade++ ;
						if (candleData[i].action ==='CALL' && candleData[i].nextColor ==='Green' ) {
                           numWin++ ;
						   candleData[i].winStatus = 'Win';
					    }
						if (candleData[i].action ==='PUT' && candleData[i].nextColor ==='Red' ) {
                           numWin++ ;
						   candleData[i].winStatus = 'Win';
					    }
						if (candleData[i].action ==='CALL' && candleData[i].nextColor ==='Red' ) {
                           numLoss++ ;
						   candleData[i].winStatus = 'Loss';
					    }
						if (candleData[i].action ==='PUT' && candleData[i].nextColor ==='Green' ) {
                           numLoss++ ;
						   candleData[i].winStatus = 'Loss';
					    }
					 }


				 }
				 console.log('NumTrade',numTrade);
				 console.log('NumWin',numWin);
				 console.log('NumConflict',numConflict);

                 const markers = []; 
				 for (i=0;i<=candleData.length-1 ;i++ ) {
					 if (candleData[i].winStatus==='Win') {
						 thisMarker = {
                           time: candleData[i].time,
                           position: 'aboveBar',
                           color: '#0000ff',
                           shape: 'circle',
                           text: 'W',
                           size: 1
                         }
						 markers.push(thisMarker) ;						
					 }
					 if (candleData[i].winStatus==='Loss') {
						 thisMarker = {
                           time: candleData[i].time,
                           position: 'belowBar',
                           color: '#FF6B6B',
                           shape: 'circle',
                           text: 'L',
                           size: 1
                         }
						 markers.push(thisMarker) ;						
					 }
				 
				 } // end for 
				 /*
				 // Add conflict markers
                const markers = conflicts.map(conflict => ({
                    time: conflict.time,
                    position: 'aboveBar',
                    color: '#FF6B6B',
                    shape: 'circle',
                    text: '⚠️',
                    size: 1
                }));
				*/
                candlestickSeries.setMarkers([]);
                candlestickSeries.setMarkers(markers);
				st  = 'จำนวนแท่งเทียน =' + candleData.length ;
				st  += ':: จำนวน เข้าเทรด =' + numTrade ;
				st  += ' :: จำนวน Win ='  + numWin ;
				st  += ':: จำนวน Loss =' + numLoss ;



				document.getElementById("tradeResult").innerHTML = st;
				

				 



				 console.log(candleData);
				 
				 
				 


        } // end func
        
function runBacktest3 () {
                 
				 
                 //console.log(candleData);
				 
				 for (i=0;i<=candleData.length-1 ;i++ ) {
					 if (candleData[i].close > candleData[i].open) {
						 candleData[i].color = 'Green' ;						 
					 }
					 if (candleData[i].close < candleData[i].open) {
						 candleData[i].color = 'Red' ;
					 }
					 if (candleData[i].close === candleData[i].open) {
						 candleData[i].color = 'Gray' ;
					 }				
					  

				 } // end for 
				 for (i=0;i<=candleData.length-2 ;i++ ) {
					 if (candleData[i].emaConflict==='y') {
                        // candleData[i].action = 'Idle'; 
						if (candleData[i].emaAbove ===3) {
                          candleData[i].action = 'CALL'; 
					     } else {
                           candleData[i].action = 'PUT'; 
					     }
                        
					 } else {
					   if (candleData[i].emaAbove ===3) {
                          candleData[i].action = 'CALL'; 
					   } else {
                          candleData[i].action = 'PUT'; 
					   }
                     }
					 candleData[i].nextColor =candleData[i+1].color ;
				 
				 } // end for 
				 numTrade = 0 ; numConflict= 0 ; numWin = 0 ;numLoss = 0 ;
				 for (i=0;i<=candleData.length-2 ;i++ ) {
					 candleData[i].winStatus = '';
					 if (candleData[i].emaConflict==='y') {
                        numConflict++ ;
					 }
					 if (candleData[i].action !=='Idle') {
                        numTrade++ ;
						if (candleData[i].action ==='CALL' && candleData[i].nextColor ==='Green' ) {
                           numWin++ ;
						   candleData[i].winStatus = 'Win';
					    }
						if (candleData[i].action ==='PUT' && candleData[i].nextColor ==='Red' ) {
                           numWin++ ;
						   candleData[i].winStatus = 'Win';
					    }
						if (candleData[i].action ==='CALL' && candleData[i].nextColor ==='Red' ) {
                           numLoss++ ;
						   candleData[i].winStatus = 'Loss';
					    }
						if (candleData[i].action ==='PUT' && candleData[i].nextColor ==='Green' ) {
                           numLoss++ ;
						   candleData[i].winStatus = 'Loss';
					    }
					 }


				 }
				 console.log('NumTrade',numTrade);
				 console.log('NumWin',numWin);
				 console.log('NumConflict',numConflict);

                 const markers = []; 
				 for (i=0;i<=candleData.length-1 ;i++ ) {
					 if (candleData[i].winStatus==='Win') {
						 thisMarker = {
                           time: candleData[i].time,
                           position: 'aboveBar',
                           color: '#0000ff',
                           shape: 'circle',
                           text: 'W',
                           size: 1
                         }
						 markers.push(thisMarker) ;						
					 }
					 if (candleData[i].winStatus==='Loss') {
						 thisMarker = {
                           time: candleData[i].time,
                           position: 'belowBar',
                           color: '#FF6B6B',
                           shape: 'circle',
                           text: 'L',
                           size: 1
                         }
						 markers.push(thisMarker) ;						
					 }
				 
				 } // end for 
				 /*
				 // Add conflict markers
                const markers = conflicts.map(conflict => ({
                    time: conflict.time,
                    position: 'aboveBar',
                    color: '#FF6B6B',
                    shape: 'circle',
                    text: '⚠️',
                    size: 1
                }));
				*/
                candlestickSeries.setMarkers([]);
                candlestickSeries.setMarkers(markers);
				st  = 'จำนวนแท่งเทียน =' + candleData.length ;
				st  += ':: จำนวน เข้าเทรด =' + numTrade ;
				st  += ' :: จำนวน Win ='  + numWin ;
				st  += ':: จำนวน Loss =' + numLoss ;
				document.getElementById("tradeResult").innerHTML = st;
				
				console.log('Candle Data BackTest3=',candleData);
				const processedData = addContinueFields(candleData);
                console.log(processedData);

				// คำนวณ ADX และ RSI
                adxPeriod = 7 ; rsiPeriod = 7 ;
                const adxValues = calculateADX(candleData, adxPeriod);
                const rsiValues = calculateRSI(candleData, rsiPeriod);
                
                // เพิ่มค่า ADX และ RSI เข้าไปในข้อมูล
                const enhancedData = candleData.map((candle, index) => ({
                    ...candle,
                    adx: adxValues[index],
                    rsi: rsiValues[index]
                }));
                
				console.log('enhancedData',enhancedData)
				

				
// หาค่าสูงสุด
//const maxValues = findMaxContinueValues(processedData);
//console.log("Max values:", maxValues);

const testMaxValues = findMaxContinueValues(processedData);
console.log("Test max values:");
console.log(`Max Win Continue: ${testMaxValues.maxWinContinue} (${testMaxValues.maxWinPeriod.start} - ${testMaxValues.maxWinPeriod.end})`);
console.log(`Max Loss Continue: ${testMaxValues.maxLossContinue} (${testMaxValues.maxLossPeriod.start} - ${testMaxValues.maxLossPeriod.end})`);

st = "<br>Test max values:<hr>";
st +=`Max Win Continue: ${testMaxValues.maxWinContinue} (${testMaxValues.maxWinPeriod.start} - ${testMaxValues.maxWinPeriod.end})`;
st += `Max Loss Continue: ${testMaxValues.maxLossContinue} (${testMaxValues.maxLossPeriod.start} - ${testMaxValues.maxLossPeriod.end})`;
document.getElementById("tradeResult").innerHTML += st;
				 
				 
				 


} // end func

// Function สำหรับแปลง timestamp เป็น hh:mm
function formatTime(timestamp) {
    const date = new Date(timestamp * 1000);
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
}


// Function สำหรับหาค่าสูงสุดของ winContinue และ lossContinue พร้อมช่วงเวลา
function findMaxContinueValues(processedData) {
    if (!Array.isArray(processedData) || processedData.length === 0) {
        return { maxWinContinue: 0, maxLossContinue: 0 };
    }

    let maxWinContinue = 0;
    let maxLossContinue = 0;
    let maxWinPeriod = { start: null, end: null };
    let maxLossPeriod = { start: null, end: null };
    
    let currentWinStart = null;
    let currentLossStart = null;

    processedData.forEach((item, index) => {
        // ติดตาม Win streak
        if (item.winStatus === "Win") {
            if (currentWinStart === null) {
                currentWinStart = index;
            }
            
            if (item.winContinue > maxWinContinue) {
                maxWinContinue = item.winContinue;
                maxWinPeriod = {
                    start: formatTime(processedData[currentWinStart].time),
                    end: formatTime(item.time)
                };
            }
        } else {
            currentWinStart = null;
        }

        // ติดตาม Loss streak
        if (item.winStatus === "Loss") {
            if (currentLossStart === null) {
                currentLossStart = index;
            }
            
            if (item.lossContinue > maxLossContinue) {
                maxLossContinue = item.lossContinue;
                maxLossPeriod = {
                    start: formatTime(processedData[currentLossStart].time),
                    end: formatTime(item.time)
                };
            }
        } else {
            currentLossStart = null;
        }
    });

    return {
        maxWinContinue,
        maxWinPeriod,
        maxLossContinue,
        maxLossPeriod
    };
}



function addContinueFields(candleData) {
    if (!Array.isArray(candleData) || candleData.length === 0) {
        return candleData;
    }

    let winContinue = 0;
    let lossContinue = 0;

    return candleData.map((candle, index) => {
        // คำนวณค่า winContinue และ lossContinue
        if (candle.winStatus === "Win") {
            winContinue++;
            lossContinue = 0; // รีเซ็ต lossContinue เมื่อ Win
        } else if (candle.winStatus === "Loss") {
            lossContinue++;
            winContinue = 0; // รีเซ็ต winContinue เมื่อ Loss
        }

        // สร้าง object ใหม่พร้อมกับ field เพิ่ม
        return {
            ...candle,
            winContinue: winContinue,
            lossContinue: lossContinue
        };
    });
}		

// คำนวณ True Range
        function calculateTrueRange(high, low, prevClose) {
            const tr1 = high - low;
            const tr2 = Math.abs(high - prevClose);
            const tr3 = Math.abs(low - prevClose);
            return Math.max(tr1, tr2, tr3);
        }

        // คำนวณ Directional Movement
        function calculateDirectionalMovement(high, low, prevHigh, prevLow) {
            const upMove = high - prevHigh;
            const downMove = prevLow - low;
            
            let plusDM = 0;
            let minusDM = 0;
            
            if (upMove > downMove && upMove > 0) {
                plusDM = upMove;
            }
            if (downMove > upMove && downMove > 0) {
                minusDM = downMove;
            }
            
            return { plusDM, minusDM };
}

// คำนวณ ADX
        function calculateADX(data, period = 14) {
            const adxValues = [];
            const trueRanges = [];
            const plusDMs = [];
            const minusDMs = [];
            
            // คำนวณ TR และ DM
            for (let i = 1; i < data.length; i++) {
                const tr = calculateTrueRange(data[i].high, data[i].low, data[i-1].close);
                const dm = calculateDirectionalMovement(data[i].high, data[i].low, data[i-1].high, data[i-1].low);
                
                trueRanges.push(tr);
                plusDMs.push(dm.plusDM);
                minusDMs.push(dm.minusDM);
            }
            
            // คำนวณ ATR, +DI, -DI
            const atr = calculateEMA(trueRanges, period);
            const plusDI = calculateEMA(plusDMs, period);
            const minusDI = calculateEMA(minusDMs, period);
            
            // คำนวณ DX และ ADX
            const dxValues = [];
            for (let i = 0; i < atr.length; i++) {
                if (atr[i] && atr[i] > 0) {
                    const plusDIValue = (plusDI[i] / atr[i]) * 100;
                    const minusDIValue = (minusDI[i] / atr[i]) * 100;
                    const dx = Math.abs(plusDIValue - minusDIValue) / (plusDIValue + minusDIValue) * 100;
                    dxValues.push(isNaN(dx) ? 0 : dx);
                } else {
                    dxValues.push(0);
                }
            }
            
            const adx = calculateEMA(dxValues, period);
            
            // เติม null สำหรับค่าที่ไม่สามารถคำนวณได้
            for (let i = 0; i < data.length; i++) {
                if (i === 0) {
                    adxValues.push(null);
                } else {
                    const adxIndex = i - 1;
                    if (adxIndex < adx.length && adx[adxIndex] !== undefined) {
                        adxValues.push(Math.round(adx[adxIndex] * 100) / 100);
                    } else {
                        adxValues.push(null);
                    }
                }
            }
            
            return adxValues;
        }

        // คำนวณ RSI
        function calculateRSI(data, period = 14) {
            const rsiValues = [];
            const gains = [];
            const losses = [];
            
            // คำนวณ gains และ losses
            for (let i = 1; i < data.length; i++) {
                const change = data[i].close - data[i-1].close;
                gains.push(change > 0 ? change : 0);
                losses.push(change < 0 ? Math.abs(change) : 0);
            }
            
            // คำนวณ average gain และ average loss
            let avgGain = 0;
            let avgLoss = 0;
            
            // คำนวณค่าเฉลี่ยเริ่มต้น
            for (let i = 0; i < period && i < gains.length; i++) {
                avgGain += gains[i];
                avgLoss += losses[i];
            }
            avgGain /= period;
            avgLoss /= period;
            
            // เติม null สำหรับค่าที่ไม่สามารถคำนวณได้
            for (let i = 0; i <= period; i++) {
                rsiValues.push(null);
            }
            
            // คำนวณ RSI
            for (let i = period; i < gains.length; i++) {
                if (avgLoss === 0) {
                    rsiValues.push(100);
                } else {
                    const rs = avgGain / avgLoss;
                    const rsi = 100 - (100 / (1 + rs));
                    rsiValues.push(Math.round(rsi * 100) / 100);
                }
                
                // อัปเดต average gain และ loss
                avgGain = ((avgGain * (period - 1)) + gains[i]) / period;
                avgLoss = ((avgLoss * (period - 1)) + losses[i]) / period;
            }
            
            return rsiValues;
}
		


        // Update conflict table
        function updateConflictTable(conflicts) {
            const tableBody = document.getElementById('conflictTableBody');
            tableBody.innerHTML = '';
            
            conflicts.forEach(conflict => {
                const row = document.createElement('tr');
                row.className = 'conflict-row';
                
                const timeStr = new Date(conflict.time * 1000).toLocaleString('th-TH');
                
                row.innerHTML = `
                    <td>${timeStr}</td>
                    <td>${conflict.price.toFixed(4)}</td>
                    <td>${conflict.ema3.toFixed(4)}</td>
                    <td>${conflict.ema5.toFixed(4)}</td>
					<td>${conflict.macd.toFixed(4)}</td>
                    <td style="color: ${conflict.actualColor === 'เขียว' ? '#4CAF50' : '#F44336'}">${conflict.actualColor}</td>
                    <td style="color: ${conflict.expectedColor === 'เขียว' ? '#4CAF50' : '#F44336'}">${conflict.expectedColor}</td>
                    <td>${conflict.conflictType}</td>
                `;
                
                tableBody.appendChild(row);
            });
            
            if (conflicts.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="7" style="text-align: center; color: #666;">ไม่พบจุด Conflict</td>';
                tableBody.appendChild(row);
            }
        }
        
        // Handle window resize
        window.addEventListener('resize', () => {
            if (chart) {
                chart.resize(document.getElementById('chartContainer').offsetWidth, 500);
            }
        });
        
        // Initialize
        initializeDates();
        
        // Load initial chart
        loadChart();




    </script>
</body>
</html>
มี json array ชื่อ CandleData [
{
    "time": 1751504400,
    "open": 98669.7782,
    "high": 98767.185,
    "low": 98640.6136,
    "close": 98691.7797,
    "emaConflict": "n",
    "emaAbove": 5,
    "color": "Green",
    "action": "PUT",
    "nextColor": "Green",
    "winStatus": "Loss"
}]
จง สร้าง field เพิ่ม ชื่อว่า winContinue, LossContinue โดยใช้ค่าจาก winStatus
และ เมื่อ winStatus =Win ค่า LossCintinue ต้องเป็น 0 โดยสร้างเป็น function pure javascript