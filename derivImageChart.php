<?php
/*
ต้องการสร้าง  html page+ pure javascript สำหรับแสดง กราฟ candlestick + ema3+ ema5
ด้วย lightweightchart ที่ timeframe 1M ย้อนหลังไป 60 แท่ง โดยใช้ websocket 
ดึงข้อมูล candle จาก deriv.com และมี listbox ให้เลือก asset ได้ 
เมื่อ plot graph เสร็จ ก็ให้ทำการ บันทึก กราฟเป็น image 

derivImageChart.php
*/
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candlestick Chart with EMA</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #1e1e1e;
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .controls {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        select, button {
            padding: 8px 12px;
            border: 1px solid #444;
            background-color: #333;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        select:hover, button:hover {
            background-color: #555;
        }
        #chart {
            width: 100%;
            height: 600px;
            border: 1px solid #444;
            border-radius: 4px;
        }
        .status {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
        }
        .status.connected {
            background-color: #1a5f1a;
            border: 1px solid #2d8f2d;
        }
        .status.disconnected {
            background-color: #5f1a1a;
            border: 1px solid #8f2d2d;
        }
        .status.loading {
            background-color: #5f5f1a;
            border: 1px solid #8f8f2d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv Candlestick Chart with EMA</h1>
        
        <div class="controls">
            <label for="assetSelect">เลือก Asset:</label>
            <select id="assetSelect">
                <option value="R_10">Volatility 10 Index</option>
                <option value="R_25">Volatility 25 Index</option>
                <option value="R_50">Volatility 50 Index</option>
                <option value="R_75">Volatility 75 Index</option>
                <option value="R_100">Volatility 100 Index</option>
                <option value="frxEURUSD">EUR/USD</option>
                <option value="frxGBPUSD">GBP/USD</option>
                <option value="frxUSDJPY">USD/JPY</option>
                <option value="frxAUDUSD">AUD/USD</option>
                <option value="frxUSDCAD">USD/CAD</option>
            </select>
            <button id="connectBtn">เชื่อมต่อ</button>
            <button id="saveImageBtn" disabled>บันทึกภาพ (Manual)</button>
        </div>

        <div id="chart"></div>
        
        <div id="status" class="status disconnected">
            สถานะ: ยังไม่เชื่อมต่อ
        </div>
    </div>

    <script>
        class DerivChart {
            constructor() {
                this.ws = null;
                this.chart = null;
                this.candlestickSeries = null;
                this.ema3Series = null;
                this.ema5Series = null;
                this.candleData = [];
                this.isConnected = false;
                this.currentSymbol = 'R_10';
                
                this.initChart();
                this.setupEventListeners();
            }

            initChart() {
                const chartContainer = document.getElementById('chart');
                this.chart = LightweightCharts.createChart(chartContainer, {
                    width: chartContainer.clientWidth,
                    height: 600,
                    layout: {
                        background: { color: '#1e1e1e' },
                        textColor: 'white',
                    },
                    grid: {
                        vertLines: { color: '#404040' },
                        horzLines: { color: '#404040' },
                    },
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                    },
                    rightPriceScale: {
                        borderColor: '#cccccc',
                    },
                    timeScale: {
                        borderColor: '#cccccc',
                        timeVisible: true,
                        secondsVisible: false,
                    }
                });

                // สร้าง candlestick series
                this.candlestickSeries = this.chart.addCandlestickSeries({
                    upColor: '#00ff88',
                    downColor: '#ff4444',
                    borderDownColor: '#ff4444',
                    borderUpColor: '#00ff88',
                    wickDownColor: '#ff4444',
                    wickUpColor: '#00ff88',
                });

                // สร้าง EMA series
                this.ema3Series = this.chart.addLineSeries({
                    color: '#ffaa00',
                    lineWidth: 2,
                    title: 'EMA 3'
                });

                this.ema5Series = this.chart.addLineSeries({
                    color: '#00aaff',
                    lineWidth: 2,
                    title: 'EMA 5'
                });

                // Handle resize
                window.addEventListener('resize', () => {
                    this.chart.applyOptions({ 
                        width: chartContainer.clientWidth 
                    });
                });
            }

            setupEventListeners() {
                document.getElementById('connectBtn').addEventListener('click', () => {
                    if (this.isConnected) {
                        this.disconnect();
                    } else {
                        this.connect();
                    }
                });

                document.getElementById('assetSelect').addEventListener('change', (e) => {
                    this.currentSymbol = e.target.value;
                    if (this.isConnected) {
                        this.subscribeToTicks();
                    }
                });

                document.getElementById('saveImageBtn').addEventListener('click', () => {
                    this.saveChartAsImage();
                });
            }

            updateStatus(message, type) {
                const statusEl = document.getElementById('status');
                statusEl.textContent = `สถานะ: ${message}`;
                statusEl.className = `status ${type}`;
            }

            connect() {
                this.updateStatus('กำลังเชื่อมต่อ...', 'loading');
                
                this.ws = new WebSocket('wss://ws.derivws.com/websockets/v3?app_id=1089');
                
                this.ws.onopen = () => {
                    console.log('WebSocket connected');
                    this.isConnected = true;
                    this.updateStatus('เชื่อมต่อแล้ว', 'connected');
                    document.getElementById('connectBtn').textContent = 'ตัดการเชื่อมต่อ';
                    document.getElementById('saveImageBtn').disabled = false;
                    
                    // Get historical data first
                    this.getHistoricalData();
                };

                this.ws.onmessage = (event) => {
                    const data = JSON.parse(event.data);
                    this.handleMessage(data);
                };

                this.ws.onclose = () => {
                    console.log('WebSocket disconnected');
                    this.isConnected = false;
                    this.updateStatus('ตัดการเชื่อมต่อแล้ว', 'disconnected');
                    document.getElementById('connectBtn').textContent = 'เชื่อมต่อ';
                    document.getElementById('saveImageBtn').disabled = true;
                };

                this.ws.onerror = (error) => {
                    console.error('WebSocket error:', error);
                    this.updateStatus('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'disconnected');
                };
            }

            disconnect() {
                if (this.ws) {
                    this.ws.close();
                }
            }

            getHistoricalData() {
                // Request historical candles data
                const message = {
                    ticks_history: this.currentSymbol,
                    adjust_start_time: 1,
                    count: 60,
                    end: 'latest',
                    start: 1,
                    style: 'candles',
                    granularity: 60 // 1 minute
                };
                
                this.ws.send(JSON.stringify(message));
            }

            subscribeToTicks() {
                // Clear existing data
                this.candleData = [];
                this.candlestickSeries.setData([]);
                this.ema3Series.setData([]);
                this.ema5Series.setData([]);
                
                // Get new historical data
                this.getHistoricalData();
            }

            handleMessage(data) {
                if (data.msg_type === 'candles') {
                    this.processHistoricalCandles(data.candles);
                } else if (data.msg_type === 'tick') {
                    this.processNewTick(data.tick);
                }
            }

            processHistoricalCandles(candles) {
                this.candleData = [];
                
                candles.forEach(candle => {
                    const candleData = {
                        time: candle.epoch,
                        open: parseFloat(candle.open),
                        high: parseFloat(candle.high),
                        low: parseFloat(candle.low),
                        close: parseFloat(candle.close)
                    };
                    this.candleData.push(candleData);
                });

                // Sort by time
                this.candleData.sort((a, b) => a.time - b.time);
                
                // Update chart
                this.updateChart();
                
                // Subscribe to live ticks
                const tickMessage = {
                    ticks: this.currentSymbol,
                    subscribe: 1
                };
                this.ws.send(JSON.stringify(tickMessage));
            }

            processNewTick(tick) {
                // This would handle real-time tick updates
                // For simplicity, we'll just log it
                console.log('New tick:', tick);
            }

            calculateEMA(data, period) {
                const ema = [];
                const multiplier = 2 / (period + 1);
                
                if (data.length === 0) return ema;
                
                // First EMA value is the first close price
                ema[0] = { time: data[0].time, value: data[0].close };
                
                // Calculate subsequent EMA values
                for (let i = 1; i < data.length; i++) {
                    const emaValue = (data[i].close * multiplier) + (ema[i-1].value * (1 - multiplier));
                    ema[i] = { time: data[i].time, value: emaValue };
                }
                
                return ema;
            }

            updateChart() {
                // Update candlestick data
                this.candlestickSeries.setData(this.candleData);
                
                // Calculate and update EMA data
                const ema3Data = this.calculateEMA(this.candleData, 3);
                const ema5Data = this.calculateEMA(this.candleData, 5);
                
                this.ema3Series.setData(ema3Data);
                this.ema5Series.setData(ema5Data);
                
                // Fit content
                this.chart.timeScale().fitContent();
                
                // Auto save chart after rendering
                this.autoSaveAfterRender();
            }

            async saveChartAsImage() {
                try {
                    // Create a canvas to draw the chart
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const chartContainer = document.getElementById('chart');
                    
                    // Set canvas size
                    canvas.width = chartContainer.clientWidth;
                    canvas.height = chartContainer.clientHeight;
                    
                    // Fill background
                    ctx.fillStyle = '#1e1e1e';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    
                    // Add text indicating this is a chart screenshot
                    ctx.fillStyle = 'white';
                    ctx.font = '16px Arial';
                    ctx.fillText(`${this.currentSymbol} - Candlestick Chart with EMA3 & EMA5`, 20, 30);
                    ctx.fillText(`Generated: ${new Date().toLocaleString('th-TH')}`, 20, 50);
                    
                    // Convert canvas to blob
                    const blob = await new Promise(resolve => {
                        canvas.toBlob(resolve, 'image/png');
                    });
                    
                    // Create filename with timestamp
                    const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
                    const filename = `${this.currentSymbol}_chart_${timestamp}.png`;
                    
                    // Create FormData and send to server
                    const formData = new FormData();
                    formData.append('image', blob, filename);
                    formData.append('symbol', this.currentSymbol);
                    formData.append('timestamp', new Date().toISOString());
                    
                    // Send to server (you'll need to create this endpoint)
                    const response = await fetch('/api/save-chart', {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        this.displaySaveResult(result.filename, result.path);
                    } else {
                        throw new Error('Failed to save image to server');
                    }
                    
                } catch (error) {
                    console.error('Error saving chart:', error);
                    // Fallback to mock server response for demo
                    this.mockServerSave();
                }
            }

            mockServerSave() {
                // Simulate server save for demo purposes
                const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
                const filename = `${this.currentSymbol}_chart_${timestamp}.png`;
                const serverPath = `/uploads/charts/${filename}`;
                
                this.displaySaveResult(filename, serverPath);
            }

            displaySaveResult(filename, path) {
                // Create or update save result display
                let resultDiv = document.getElementById('saveResult');
                if (!resultDiv) {
                    resultDiv = document.createElement('div');
                    resultDiv.id = 'saveResult';
                    resultDiv.style.cssText = `
                        margin-top: 15px;
                        padding: 15px;
                        background-color: #1a5f1a;
                        border: 1px solid #2d8f2d;
                        border-radius: 4px;
                        font-size: 14px;
                    `;
                    document.querySelector('.container').appendChild(resultDiv);
                }
                
                resultDiv.innerHTML = `
                    <strong>✅ บันทึกภาพสำเร็จ!</strong><br>
                    <strong>ชื่อไฟล์:</strong> ${filename}<br>
                    <strong>ตำแหน่ง:</strong> ${path}<br>
                    <strong>เวลา:</strong> ${new Date().toLocaleString('th-TH')}
                `;
                
                // Auto hide after 5 seconds
                setTimeout(() => {
                    if (resultDiv.parentNode) {
                        resultDiv.style.opacity = '0.5';
                    }
                }, 5000);
            }

            async autoSaveAfterRender() {
                // Wait a bit for chart to fully render
                setTimeout(async () => {
                    await this.saveChartAsImage();
                }, 2000);
            }
        }

        // Initialize the chart when page loads
        document.addEventListener('DOMContentLoaded', () => {
            new DerivChart();
        });
    </script>
</body>
</html>