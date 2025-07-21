<?php
/*
สร้าง html page สำหรับดึง ข้อมูล candle จาก Deriv.com โดยมี 
1. list ของ asset จาก deriv.com
2. dtpicker A ให้เลือกเฉพาะ วัน 
3. dtpicker B และ C  ให้แสดงช่วงเวลาของวัน 
4. มี List Box ให้เลือก timeframe ตามนี้  1,3,5,10,15,30,60 นาที
5. มีปุ่ม ดึงข้อมูล candle stick จาก Deriv.com ด้วย  pure javascript + socket โดย แบ่งเป็น 
     5.1 ปุ่ม  A ดึง Candles จาก  dtpicker A ตามช่วงเวลา B,C
     5.2 ปุ่ม  B ดึง Candles จาก  เวลาปัจจุบัน ย้อนหลังไป พร้อมทั้ง มี input ให้กรอกจำนวนแท่ง ที่ต้องการมี default = 60
6. เมื่อ ได้ข้อมูล response มาเป็น candles ให้
       6.1 ใส่ text area 
       6.2 ส่ง ajax ไป ยัง  hdlightc.com และรอ response กลับมา แสดงผลใน div
       6.3 นำข้อมูล มา plot graph candle+ema Short+ema long โดย ema Short และ long ให้ปรับค่าได้ 
       และเมื่อ ปรับค่าแล้วก็ให้ update ค่าในกราฟทันที  
       6.4 กราฟถูกสร้างจาก lightweight chart
*/
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candle Data Fetcher</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightweight-charts/4.1.1/lightweight-charts.standalone.production.js"></script>

	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>  
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 2.5em;
            font-weight: 700;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .control-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 0.9em;
        }

        select, input, button {
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            margin: 5px;
            transition: all 0.3s ease;
            transform: translateY(0);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }

        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            font-weight: 500;
            display: none;
        }

        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        textarea {
            width: 100%;
            min-height: 200px;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 10px 0;
            resize: vertical;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .ema-controls {
            display: flex;
            gap: 20px;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 8px;
        }

        .ema-controls label {
            margin: 0 5px 0 0;
        }

        .ema-controls input {
            width: 80px;
            margin: 0 10px 0 0;
        }

        #chart {
            height: 500px;
            margin-top: 15px;
        }

        .response-section {
            margin: 20px 0;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .controls {
                grid-template-columns: 1fr;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .ema-controls {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🕯️ Deriv Candle Data Fetcher</h1>
        
        <div class="controls">
            <div class="control-group">
                <label for="assetSelect">📊 เลือก Asset:</label>
                <select id="assetSelect">
                    <option value="">กำลังโหลด...</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="datePickerA">📅 เลือกวันที่:</label>
                <input type="date" id="datePickerA">
            </div>
            
            <div class="control-group">
                <label for="timePickerB">🕐 เวลาเริ่มต้น:</label>
                <input type="time" id="timePickerB" value="00:00">
            </div>
            
            <div class="control-group">
                <label for="timePickerC">🕐 เวลาสิ้นสุด:</label>
                <input type="time" id="timePickerC" value="23:59">
            </div>
            
            <div class="control-group">
                <label for="timeframe">⏱️ Timeframe (นาที):</label>
                <select id="timeframe">
                    <option value="60">1 นาที</option>
                    <option value="180">3 นาที</option>
                    <option value="300">5 นาที</option>
                    <option value="600">10 นาที</option>
                    <option value="900">15 นาที</option>
                    <option value="1800">30 นาที</option>
                    <option value="3600" selected>60 นาที</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="candleCount">🕯️ จำนวนแท่ง:</label>
                <input type="number" id="candleCount" min="1" max="1000" value="60">
            </div>
        </div>

        <div class="button-group">
            <button id="fetchHistorical">📈 ดึงข้อมูลตามวันที่</button>
            <button id="fetchCurrent">🔄 ดึงข้อมูลปัจจุบัน</button>
            <button id="connectSocket">🔌 เชื่อมต่อ Socket</button>
            <button id="disconnectSocket">❌ ตัดการเชื่อมต่อ</button>
        </div>

        <div id="status" class="status"></div>
        <div id="loading" class="loading">
            <div class="spinner"></div>
            <p>กำลังดึงข้อมูล...</p>
        </div>

        <div class="response-section">
            <h3>📄 Raw Data Response:</h3>
            <textarea id="responseData" placeholder="ข้อมูล response จะแสดงที่นี่..."></textarea>
        </div>

        <div class="response-section">
            <h3>🌐 Response จาก hdlightc.com:</h3>
            <div id="hdlightcResponse">รอข้อมูล...</div>
        </div>

        <div class="chart-container">
            <h3>📊 Candlestick Chart with EMA</h3>
            <div class="ema-controls">
                <label for="emaShort">EMA Short:</label>
                <input type="number" id="emaShort" min="1" max="200" value="20">
                
                <label for="emaLong">EMA Long:</label>
                <input type="number" id="emaLong" min="1" max="200" value="50">
                
                <button id="updateEMA">🔄 อัพเดท EMA</button>
            </div>
            <div id="chart"></div>
        </div>
    </div>

    <script>
        class DerivCandleFetcher {
            constructor() {
                this.ws = null;
                this.chart = null;
                this.candleSeries = null;
                this.emaShortSeries = null;
                this.emaLongSeries = null;
                this.candlesData = [];
                this.isConnected = false;
                
                this.initChart();
                this.bindEvents();
                this.loadAssets();
                this.setDefaultDate();
            }
            
            initChart() {
                const chartContainer = document.getElementById('chart');
                this.chart = LightweightCharts.createChart(chartContainer, {
                    width: chartContainer.clientWidth,
                    height: 500,
                    layout: {
                        background: { color: '#ffffff' },
                        textColor: '#333',
                    },
                    grid: {
                        vertLines: { color: '#f0f0f0' },
                        horzLines: { color: '#f0f0f0' },
                    },
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                    },
                    timeScale: {
                        borderColor: '#cccccc',
                        timeVisible: true,
                        secondsVisible: false,
                    },
                });
                
                this.candleSeries = this.chart.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderVisible: false,
                    wickUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                });
                
                this.emaShortSeries = this.chart.addLineSeries({
                    color: '#2196F3',
                    lineWidth: 2,
                    title: 'EMA Short',
                });
                
                this.emaLongSeries = this.chart.addLineSeries({
                    color: '#FF9800',
                    lineWidth: 2,
                    title: 'EMA Long',
                });
                
                // Handle resize
                new ResizeObserver(entries => {
                    if (entries.length === 0 || entries[0].target !== chartContainer) return;
                    const { width, height } = entries[0].contentRect;
                    this.chart.applyOptions({ width, height });
                }).observe(chartContainer);
            }
            
            bindEvents() {
                document.getElementById('connectSocket').onclick = () => this.connectSocket();
                document.getElementById('disconnectSocket').onclick = () => this.disconnectSocket();
                document.getElementById('fetchHistorical').onclick = () => this.fetchHistoricalData();
                document.getElementById('fetchCurrent').onclick = () => this.fetchCurrentData();
                document.getElementById('updateEMA').onclick = () => this.updateEMA();
                
                // Auto-update EMA when values change
                document.getElementById('emaShort').oninput = () => this.updateEMA();
                document.getElementById('emaLong').oninput = () => this.updateEMA();
            }
            
            setDefaultDate() {
                const today = new Date();
                document.getElementById('datePickerA').value = today.toISOString().split('T')[0];
            }
            
            async loadAssets() {
                const assets = [
                    { symbol: 'R_10', display: 'Volatility 10 Index' },
                    { symbol: 'R_25', display: 'Volatility 25 Index' },
                    { symbol: 'R_50', display: 'Volatility 50 Index' },
                    { symbol: 'R_75', display: 'Volatility 75 Index' },
                    { symbol: 'R_100', display: 'Volatility 100 Index' },
                    { symbol: 'RDBEAR', display: 'Bear Market Index' },
                    { symbol: 'RDBULL', display: 'Bull Market Index' },
                    { symbol: 'frxEURUSD', display: 'EUR/USD' },
                    { symbol: 'frxGBPUSD', display: 'GBP/USD' },
                    { symbol: 'frxUSDJPY', display: 'USD/JPY' },
                    { symbol: 'frxAUDUSD', display: 'AUD/USD' },
                    { symbol: 'frxUSDCHF', display: 'USD/CHF' },
                    { symbol: 'frxUSDCAD', display: 'USD/CAD' },
                    { symbol: 'frxEURJPY', display: 'EUR/JPY' },
                    { symbol: 'frxEURGBP', display: 'EUR/GBP' }
                ];
                
                const select = document.getElementById('assetSelect');
                select.innerHTML = '<option value="">เลือก Asset</option>';
                
                assets.forEach(asset => {
                    const option = document.createElement('option');
                    option.value = asset.symbol;
                    option.textContent = asset.display;
                    select.appendChild(option);
                });
            }
            
            connectSocket() {
                if (this.isConnected) {
                    this.showStatus('เชื่อมต่ออยู่แล้ว', 'info');
                    return;
                }
                
                try {
                    this.ws = new WebSocket('wss://ws.derivws.com/websockets/v3?app_id=66726');
                    
                    this.ws.onopen = () => {
                        this.isConnected = true;
                        this.showStatus('เชื่อมต่อ WebSocket สำเร็จ', 'success');
                        document.getElementById('connectSocket').textContent = '✅ เชื่อมต่อแล้ว';
                    };
                    
                    this.ws.onmessage = (event) => {
                        const data = JSON.parse(event.data);
                        this.handleSocketMessage(data);
                    };
                    
                    this.ws.onclose = () => {
                        this.isConnected = false;
                        this.showStatus('การเชื่อมต่อ WebSocket ถูกปิด', 'error');
                        document.getElementById('connectSocket').textContent = '🔌 เชื่อมต่อ Socket';
                    };
                    
                    this.ws.onerror = (error) => {
                        this.showStatus('เกิดข้อผิดพลาดในการเชื่อมต่อ WebSocket', 'error');
                        console.error('WebSocket error:', error);
                    };
                    
                } catch (error) {
                    this.showStatus('ไม่สามารถเชื่อมต่อ WebSocket ได้', 'error');
                    console.error('Connection error:', error);
                }
            }
            
            disconnectSocket() {
                if (this.ws && this.isConnected) {
                    this.ws.close();
                    this.isConnected = false;
                    document.getElementById('connectSocket').textContent = '🔌 เชื่อมต่อ Socket';
                    this.showStatus('ตัดการเชื่อมต่อ WebSocket แล้ว', 'info');
                }
            }
            
            handleSocketMessage(data) {
                if (data.msg_type === 'candles') {
                    this.processCandlesData(data);
                } else if (data.error) {
                    this.showStatus(`Error: ${data.error.message}`, 'error');
                }
            }
            
            fetchHistoricalData() {
                if (!this.isConnected) {
                    this.showStatus('กรุณาเชื่อมต่อ WebSocket ก่อน', 'error');
                    return;
                }
                
                const asset = document.getElementById('assetSelect').value;
                const date = document.getElementById('datePickerA').value;
                const startTime = document.getElementById('timePickerB').value;
                const endTime = document.getElementById('timePickerC').value;
                const granularity = document.getElementById('timeframe').value;
                
                if (!asset || !date) {
                    this.showStatus('กรุณาเลือก Asset และวันที่', 'error');
                    return;
                }
                
                // Convert date and time to timestamps
                const startDateTime = new Date(`${date}T${startTime}:00`);
                const endDateTime = new Date(`${date}T${endTime}:00`);
                
                const request = {
                    ticks_history: asset,
                    adjust_start_time: 1,
                    count: 5000,
                    end: Math.floor(endDateTime.getTime() / 1000),
                    start: Math.floor(startDateTime.getTime() / 1000),
                    style: 'candles',
                    granularity: parseInt(granularity)
                };
                
                this.showLoading(true);
                this.ws.send(JSON.stringify(request));
            }
            
            fetchCurrentData() {
                if (!this.isConnected) {
                    this.showStatus('กรุณาเชื่อมต่อ WebSocket ก่อน', 'error');
                    return;
                }
                
                const asset = document.getElementById('assetSelect').value;
                const count = parseInt(document.getElementById('candleCount').value);
                const granularity = document.getElementById('timeframe').value;
                
                if (!asset) {
                    this.showStatus('กรุณาเลือก Asset', 'error');
                    return;
                }
                
                const request = {
                    ticks_history: asset,
                    adjust_start_time: 1,
                    count: count,
                    end: 'latest',
                    style: 'candles',
                    granularity: parseInt(granularity)
                };
                
                this.showLoading(true);
                this.ws.send(JSON.stringify(request));
            }
            
            processCandlesData(data) {
                this.showLoading(false);
                
                // Display raw data
                document.getElementById('responseData').value = JSON.stringify(data, null, 2);
                
                if (data.candles && data.candles.length > 0) {
                    this.candlesData = data.candles.map(candle => ({
                        time: candle.epoch,
                        open: parseFloat(candle.open),
                        high: parseFloat(candle.high),
                        low: parseFloat(candle.low),
                        close: parseFloat(candle.close)
                    }));
                    
                    this.updateChart();
                    this.sendToHdlightc(data);
                    this.showStatus(`ได้รับข้อมูล ${this.candlesData.length} แท่งเทียน`, 'success');
                } else {
                    this.showStatus('ไม่พบข้อมูลเทียน', 'error');
                }
            }
            
            updateChart() {
                if (this.candlesData.length === 0) return;
                
                // Update candles
                this.candleSeries.setData(this.candlesData);
                
                // Calculate and update EMAs
                this.updateEMA();
                
                // Fit content
                this.chart.timeScale().fitContent();
            }
            
            updateEMA() {
                if (this.candlesData.length === 0) return;
                
                const shortPeriod = parseInt(document.getElementById('emaShort').value);
                const longPeriod = parseInt(document.getElementById('emaLong').value);
                
                const emaShortData = this.calculateEMA(this.candlesData, shortPeriod);
                const emaLongData = this.calculateEMA(this.candlesData, longPeriod);
                
                this.emaShortSeries.setData(emaShortData);
                this.emaLongSeries.setData(emaLongData);
            }
            
            calculateEMA(data, period) {
                if (data.length < period) return [];
                
                const emaData = [];
                const multiplier = 2 / (period + 1);
                
                // First EMA value is SMA
                let ema = data.slice(0, period).reduce((sum, candle) => sum + candle.close, 0) / period;
                emaData.push({ time: data[period - 1].time, value: ema });
                
                // Calculate subsequent EMA values
                for (let i = period; i < data.length; i++) {
                    ema = (data[i].close * multiplier) + (ema * (1 - multiplier));
                    emaData.push({ time: data[i].time, value: ema });
                }
                
                return emaData;
            }
            
            async sendToHdlightc(data) {
                try {
                    const response = await fetch('https://hdlightc.com/api/candles', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        const result = await response.text();
                        document.getElementById('hdlightcResponse').innerHTML = `
                            <div style="padding: 15px; background: #d4edda; border-radius: 8px; color: #155724;">
                                <strong>สำเร็จ:</strong> ${result}
                            </div>
                        `;
                    } else {
                        throw new Error(`HTTP ${response.status}`);
                    }
                } catch (error) {
                    document.getElementById('hdlightcResponse').innerHTML = `
                        <div style="padding: 15px; background: #f8d7da; border-radius: 8px; color: #721c24;">
                            <strong>ข้อผิดพลาด:</strong> ${error.message}
                        </div>
                    `;
                }
            }
            
            showStatus(message, type) {
                const status = document.getElementById('status');
                status.textContent = message;
                status.className = `status ${type}`;
                status.style.display = 'block';
                
                setTimeout(() => {
                    status.style.display = 'none';
                }, 5000);
            }
            
            showLoading(show) {
                document.getElementById('loading').style.display = show ? 'block' : 'none';
            }
        }
        
        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
            new DerivCandleFetcher();
        });
    </script>
</body>
</html>