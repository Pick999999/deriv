<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Assets Charts Dashboard</title>
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
            max-width: 1400px;
            margin: 0 auto;
        }
        .controls {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .control-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .control-group label {
            font-weight: bold;
        }
        .control-group input, .control-group select {
            padding: 8px 12px;
            border: 1px solid #444;
            border-radius: 4px;
            background: #333;
            color: white;
        }
        .status {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status.running {
            background: #2d5a2d;
            color: #90ee90;
        }
        .status.stopped {
            background: #5a2d2d;
            color: #ff9090;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
            gap: 20px;
        }
        .chart-container {
            background: #2a2a2a;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .chart-title {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }
        .chart-status {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
            background: #444;
        }
        .chart-status.connected {
            background: #2d5a2d;
            color: #90ee90;
        }
        .chart-status.error {
            background: #5a2d2d;
            color: #ff9090;
        }
        .chart {
            height: 400px;
            margin-bottom: 10px;
        }
        .chart-info {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #ccc;
        }
        button {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover {
            background: #357abd;
        }
        button:disabled {
            background: #666;
            cursor: not-allowed;
        }
        .log {
            background: #1a1a1a;
            border: 1px solid #444;
            padding: 10px;
            height: 150px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 12px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .progress {
            background: #333;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-bar {
            background: #4a90e2;
            height: 100%;
            width: 0;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv Assets Charts Dashboard</h1>
        
        <div class="controls">
            <div class="control-group">
                <label>Update Interval:</label>
                <select id="intervalSelect">
                    <option value="5">5 seconds</option>
                    <option value="10" selected>10 seconds</option>
                    <option value="15">15 seconds</option>
                    <option value="30">30 seconds</option>
                    <option value="60">1 minute</option>
                </select>
            </div>
            <div class="control-group">
                <label>Candles Count:</label>
                <input type="number" id="candlesCount" value="100" min="50" max="500">
            </div>
            <button id="startBtn">Start Monitoring</button>
            <button id="stopBtn" disabled>Stop Monitoring</button>
            <button id="clearLogBtn">Clear Log</button>
            <div class="status stopped" id="systemStatus">Stopped</div>
        </div>
		<div class="time-display" style="background: #2a2a2a; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
    <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">Deriv Server Time</div>
    <div id="serverTime" style="font-size: 24px; color: #4a90e2; font-weight: bold;">--:--:--</div>
    <div id="timeStatus" style="font-size: 12px; color: #ccc; margin-top: 5px;">Connecting...</div>
</div>

        <div class="progress">
            <div class="progress-bar" id="progressBar"></div>
        </div>

        <div class="charts-grid" id="chartsGrid"></div>
        
        <div class="log" id="logArea"></div>
    </div>

    <script>
        class DerivChartsApp {
            constructor() {
                this.assets = ['R_10', 'R_25', 'R_50', 'R_75', 'R_100'];
                this.charts = {};
                this.candlestickSeries = {};
                this.ema3Series = {};
                this.ema5Series = {};
                this.isRunning = false;
                this.updateInterval = 10;
                this.candlesCount = 100;
                this.intervalId = null;
                this.updateProgress = 0; 

				this.serverTimeOffset = 0;
				this.timeUpdateInterval = null;
				this.timeWS = null;
                
                this.initializeUI();
                this.createCharts();
            }

            initializeUI() {
                const startBtn = document.getElementById('startBtn');
                const stopBtn = document.getElementById('stopBtn');
                const clearLogBtn = document.getElementById('clearLogBtn');
                const intervalSelect = document.getElementById('intervalSelect');
                const candlesCountInput = document.getElementById('candlesCount');

                startBtn.addEventListener('click', () => this.start());
                stopBtn.addEventListener('click', () => this.stop());
                clearLogBtn.addEventListener('click', () => this.clearLog());
                
                intervalSelect.addEventListener('change', (e) => {
                    this.updateInterval = parseInt(e.target.value);
                    if (this.isRunning) {
                        this.restart();
                    }
                });
                
                candlesCountInput.addEventListener('change', (e) => {
                    this.candlesCount = parseInt(e.target.value);
                });
            }

            createCharts() {
                const chartsGrid = document.getElementById('chartsGrid');
                chartsGrid.innerHTML = '';

                this.assets.forEach(asset => {
                    const container = document.createElement('div');
                    container.className = 'chart-container';
                    container.innerHTML = `
                        <div class="chart-header">
                            <div class="chart-title">${asset}</div>
						    <div class="chart-time">${asset}</div>
                            <div class="chart-status" id="status-${asset}">Disconnected</div>
                        </div>
                        <div class="chart" id="chart-${asset}"></div>
                        <div class="chart-info">
                            <span id="price-${asset}">Price: --</span>
                            <span id="lastupdate-${asset}">Last Update: --</span>
                        </div>
                    `;
                    chartsGrid.appendChild(container);

                    // Create chart with better options
                    const chart = LightweightCharts.createChart(document.getElementById(`chart-${asset}`), {
                        width: 0,
                        height: 400,
                        layout: {
                            background: { color: '#1e1e1e' },
                            textColor: '#d1d4dc',
                        },
                        grid: {
                            vertLines: { color: '#2B2B43' },
                            horzLines: { color: '#2B2B43' },
                        },
                        crosshair: {
                            mode: LightweightCharts.CrosshairMode.Normal,
                        },
                        rightPriceScale: {
                            borderColor: '#485c7b',
                            scaleMargins: {
                                top: 0.1,
                                bottom: 0.1,
                            },
                        },
                        timeScale: {
                            borderColor: '#485c7b',
                            timeVisible: true,
                            secondsVisible: false,
                        },
                        handleScroll: {
                            mouseWheel: true,
                            pressedMouseMove: true,
                        },
                        handleScale: {
                            axisPressedMouseMove: true,
                            mouseWheel: true,
                            pinch: true,
                        },
                    });

                    // Add series
                    const candlestickSeries = chart.addCandlestickSeries({
                        upColor: '#26a69a',
                        downColor: '#ef5350',
                        borderVisible: false,
                        wickUpColor: '#26a69a',
                        wickDownColor: '#ef5350',
                    });

                    const ema3Series = chart.addLineSeries({
                        color: '#2196F3',
                        lineWidth: 2,
                        title: 'EMA 3',
                    });

                    const ema5Series = chart.addLineSeries({
                        color: '#FF9800',
                        lineWidth: 2,
                        title: 'EMA 5',
                    });

                    this.charts[asset] = chart;
                    this.candlestickSeries[asset] = candlestickSeries;
                    this.ema3Series[asset] = ema3Series;
                    this.ema5Series[asset] = ema5Series;

                    // Auto-resize chart
                    const resizeObserver = new ResizeObserver(entries => {
                        for (let entry of entries) {
                            const { width } = entry.contentRect;
                            chart.applyOptions({ width: Math.max(width - 30, 300) });
                        }
                    });
                    resizeObserver.observe(container);
                });
            }

            calculateEMA(data, period) {
                if (data.length < period) return [];
                
                const multiplier = 2 / (period + 1);
                const emaData = [];
                
                // First EMA = Simple Moving Average
                let sum = 0;
                for (let i = 0; i < period; i++) {
                    sum += data[i].close;
                }
                let ema = sum / period;
                emaData.push({ time: data[period - 1].time, value: ema });
                
                // Calculate remaining EMAs
                for (let i = period; i < data.length; i++) {
                    ema = (data[i].close - ema) * multiplier + ema;
                    emaData.push({ time: data[i].time, value: ema });
                }
                
                return emaData;
            } 

			initializeTimeServer() {
            this.timeWS = new WebSocket('wss://ws.derivws.com/websockets/v3?app_id=66726');
    
    this.timeWS.onopen = () => {
        document.getElementById('timeStatus').textContent = 'Connected';
        this.requestServerTime();
    };
    
    this.timeWS.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.msg_type === 'time') {
            const serverTime = data.time * 1000;
            const localTime = Date.now();
            this.serverTimeOffset = serverTime - localTime;
            this.startTimeUpdater();
        }
    };
    
    this.timeWS.onclose = () => {
        document.getElementById('timeStatus').textContent = 'Disconnected';
        this.stopTimeUpdater();
    };
}

requestServerTime() {
    if (this.timeWS && this.timeWS.readyState === WebSocket.OPEN) {
        this.timeWS.send(JSON.stringify({"time": 1}));
    }
}

startTimeUpdater() {
    if (this.timeUpdateInterval) clearInterval(this.timeUpdateInterval);
    
    this.timeUpdateInterval = setInterval(() => {
        const currentServerTime = new Date(Date.now() + this.serverTimeOffset);
        const timeString = currentServerTime.toLocaleTimeString('en-US', {
            hour12: false,
            timeZone: 'Asia/Bangkok'
        });
        document.getElementById('serverTime').textContent = timeString;
    }, 1000);
    
    // Update immediately
    const currentServerTime = new Date(Date.now() + this.serverTimeOffset);
    const timeString = currentServerTime.toLocaleTimeString('en-US', {
        hour12: false,
        timeZone: 'Asia/Bangkok'
    });
    document.getElementById('serverTime').textContent = timeString;
}

stopTimeUpdater() {
    if (this.timeUpdateInterval) {
        clearInterval(this.timeUpdateInterval);
        this.timeUpdateInterval = null;
    }
}

            async fetchHistoricalData(asset) {
                return new Promise((resolve, reject) => {
                    const ws = new WebSocket('wss://ws.derivws.com/websockets/v3?app_id=66726');
                    let timeoutId;
                    
                    const cleanup = () => {
                        if (timeoutId) clearTimeout(timeoutId);
                        if (ws.readyState === WebSocket.OPEN || ws.readyState === WebSocket.CONNECTING) {
                            ws.close();
                        }
                    };
                    
                    timeoutId = setTimeout(() => {
                        cleanup();
                        reject(new Error(`Timeout fetching data for ${asset}`));
                    }, 15000);
                    
                    ws.onopen = () => {
                        const request = {
                            ticks_history: asset,
                            adjust_start_time: 1,
                            count: this.candlesCount,
                            end: 'latest',
                            start: 1,
                            style: 'candles',
                            granularity: 60,
                            req_id: Date.now()
                        };
                        
                        ws.send(JSON.stringify(request));
                    };
                    
                    ws.onmessage = (event) => {
                        try {
                            const data = JSON.parse(event.data);
                            
                            if (data.error) {
                                cleanup();
                                reject(new Error(`API Error: ${data.error.message}`));
                                return;
                            }
                            //time:  candle.epoch + (7 * 3600), // เพิ่ม 7 ชั่วโมงสำหรับเวลาไทย,
                            if (data.candles && Array.isArray(data.candles)) {
                                const candleData = data.candles.map(candle => ({
                                    time: parseInt(candle.epoch)+(7*3600),
                                    open: parseFloat(candle.open),
                                    high: parseFloat(candle.high),
                                    low: parseFloat(candle.low),
                                    close: parseFloat(candle.close)
                                })).filter(candle => 
                                    !isNaN(candle.open) && !isNaN(candle.high) && 
                                    !isNaN(candle.low) && !isNaN(candle.close)
                                );
                                
                                cleanup();
                                resolve(candleData);
                            }
                        } catch (error) {
                            cleanup();
                            reject(new Error(`Parse error: ${error.message}`));
                        }
                    };
                    
                    ws.onerror = (error) => {
                        cleanup();
                        reject(new Error(`WebSocket error for ${asset}`));
                    };
                });
            }

            updateChart(asset, data) {
                if (!data || data.length === 0) {
                    this.log(`⚠️ No data available for ${asset}`);
                    return;
                }

                try {
                    // Sort data by time
                    data.sort((a, b) => a.time - b.time);
                    
                    // Update candlestick data
                    this.candlestickSeries[asset].setData(data);
					 

                    // Calculate and update EMAs
                    if (data.length >= 5) {
                        const ema3Data = this.calculateEMA(data, 3);
                        const ema5Data = this.calculateEMA(data, 5);

                        this.ema3Series[asset].setData(ema3Data);
                        this.ema5Series[asset].setData(ema5Data);
                    }

                    // Update UI
                    const latestCandle = data[data.length - 1];
                    document.getElementById(`price-${asset}`).textContent = 
                        `Price: ${latestCandle.close.toFixed(5)}`;
                    document.getElementById(`lastupdate-${asset}`).textContent = 
                        `Last Update: ${new Date().toLocaleTimeString('th-TH', { timeZone: 'Asia/Bangkok' })}`;
                    
                    const statusEl = document.getElementById(`status-${asset}`);
                    statusEl.textContent = 'Connected';
                    statusEl.className = 'chart-status connected';

                    // Fit chart content
                    this.charts[asset].timeScale().fitContent();

                    // Capture and save chart image
                    setTimeout(() => this.captureAndSaveChart(asset), 1000);

                } catch (error) {
                    this.log(`❌ Error updating chart for ${asset}: ${error.message}`);
                    const statusEl = document.getElementById(`status-${asset}`);
                    statusEl.textContent = 'Error';
                    statusEl.className = 'chart-status error';
                }
            }

            async captureAndSaveChart(asset) {
                try {
                    const chartElement = document.getElementById(`chart-${asset}`);
                    const canvas = chartElement.querySelector('canvas');
                    
                    if (!canvas) {
                        this.log(`⚠️ Canvas not found for ${asset}`);
                        return;
                    }
                    
                    // Get image data
                    const imageData = canvas.toDataURL('image/png', 0.8);
                    
                    // Get current price
                    const priceText = document.getElementById(`price-${asset}`).textContent;
                    
                    const payload = {
                        asset: asset,
                        imageData: imageData,
                        timestamp: new Date().toISOString(),
                        price: priceText,
                        lastUpdate: new Date().toLocaleTimeString()
                    };
                    
                    const response = await fetch('https://thepapers.in/deriv/saveImageV2.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload)
                    });

                    if (response.ok) {
                        this.log(`✅ Chart image saved for ${asset}`);
                    } else {
                        const errorText = await response.text();
                        this.log(`❌ Failed to save chart image for ${asset}: ${errorText}`);
                    }
                } catch (error) {
                    this.log(`❌ Error saving chart image for ${asset}: ${error.message}`);
                }
            }

            updateProgressBar(current, total) {
                const percentage = (current / total) * 100;
                document.getElementById('progressBar').style.width = `${percentage}%`;
            }

            async updateAllCharts() {
                this.log(`🔄 Starting charts update cycle...`);
                
                const promises = this.assets.map(async (asset, index) => {
                    try {
                        this.updateProgressBar(index, this.assets.length);
                        
                        const statusEl = document.getElementById(`status-${asset}`);
                        statusEl.textContent = 'Loading...';
                        statusEl.className = 'chart-status';
                        
                        const data = await this.fetchHistoricalData(asset);
                        
                        if (data && data.length > 0) {
                            this.updateChart(asset, data);
                            return { asset, success: true, count: data.length };
                        } else {
                            this.log(`⚠️ No data received for ${asset}`);
                            statusEl.textContent = 'No Data';
                            statusEl.className = 'chart-status error';
                            return { asset, success: false, error: 'No data' };
                        }
                    } catch (error) {
                        this.log(`❌ Failed to update ${asset}: ${error.message}`);
                        const statusEl = document.getElementById(`status-${asset}`);
                        statusEl.textContent = 'Error';
                        statusEl.className = 'chart-status error';
                        return { asset, success: false, error: error.message };
                    }
                });

                const results = await Promise.allSettled(promises);
                this.updateProgressBar(this.assets.length, this.assets.length);
                
                const successful = results.filter(r => r.status === 'fulfilled' && r.value.success).length;
                const failed = results.length - successful;
                
                this.log(`✨ Update cycle completed: ${successful} successful, ${failed} failed`);
                
                // Reset progress bar after 2 seconds
                setTimeout(() => {
                    document.getElementById('progressBar').style.width = '0%';
                }, 2000);
            }

            start() {
                if (this.isRunning) return;

                this.isRunning = true;
                document.getElementById('startBtn').disabled = true;
                document.getElementById('stopBtn').disabled = false;
                document.getElementById('systemStatus').textContent = `Running (${this.updateInterval}s interval)`;
                document.getElementById('systemStatus').className = 'status running';

                this.log(`🚀 Started monitoring ${this.assets.length} assets with ${this.updateInterval}s interval`);

				this.initializeTimeServer();
                
                // Initial update
                this.updateAllCharts();

                // Set up interval
                this.intervalId = setInterval(() => {
                    if (this.isRunning) {
                        this.updateAllCharts();
                    }
                }, this.updateInterval * 1000);

				setInterval(() => {
    if (this.timeWS && this.timeWS.readyState === WebSocket.OPEN) {
        this.requestServerTime();
    }
}, 30000);
            }

			

            stop() {
                if (!this.isRunning) return;

                this.isRunning = false;
                document.getElementById('startBtn').disabled = false;
                document.getElementById('stopBtn').disabled = true;
                document.getElementById('systemStatus').textContent = 'Stopped';
                document.getElementById('systemStatus').className = 'status stopped';

                if (this.intervalId) {
                    clearInterval(this.intervalId);
					this.stopTimeUpdater();
    if (this.timeWS) {
        this.timeWS.close();
        this.timeWS = null;
    }
                    this.intervalId = null;

                }

                // Update all status indicators
                this.assets.forEach(asset => {
                    const statusEl = document.getElementById(`status-${asset}`);
                    statusEl.textContent = 'Disconnected';
                    statusEl.className = 'chart-status';
                });

                // Reset progress bar
                document.getElementById('progressBar').style.width = '0%';

                this.log('⏹️ Stopped monitoring charts');
            }

            restart() {
                this.log('🔄 Restarting with new settings...');
                this.stop();
                setTimeout(() => this.start(), 1000);
            }

            clearLog() {
                document.getElementById('logArea').innerHTML = '';
            }

            log(message) {
                const logArea = document.getElementById('logArea');
                const timestamp = new Date().toLocaleTimeString();
                const logLine = `[${timestamp}] ${message}`;
                
                logArea.innerHTML += logLine + '\n';
                logArea.scrollTop = logArea.scrollHeight;
                
                // Keep only last 100 lines
                const lines = logArea.innerHTML.split('\n');
                if (lines.length > 100) {
                    logArea.innerHTML = lines.slice(-100).join('\n');
                }
            }
        }

function adjustRightMargin() {
    const chartWidth = document.getElementById('chart').clientWidth;
    const pixelsPer100px = 100; // ระยะห่างที่ต้องการ (100px)
    
    // คำนวณ logical units ที่ต้องเพิ่ม
    const logicalUnitsToAdd = pixelsPer100px / 7;
    
    const timeScale = chart.timeScale();
    const visibleRange = timeScale.getVisibleLogicalRange();
    
    if (visibleRange) {
        const newRange = {
            from: visibleRange.from,
            to: visibleRange.to + logicalUnitsToAdd
        };
        timeScale.setVisibleLogicalRange(newRange);
    } 
	alert('ss')
}

        // Initialize the application
        let app;
        
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', () => {
            app = new DerivChartsApp();
            
            // Handle page visibility changes
            document.addEventListener('visibilitychange', () => {
                if (app) {
                    if (document.hidden && app.isRunning) {
                        app.log('👁️ Page hidden - monitoring continues in background');
                    } else if (!document.hidden && app.isRunning) {
                        app.log('👁️ Page visible - resuming active monitoring');
                    }
                }
            });
            
            // Handle page unload
            window.addEventListener('beforeunload', () => {
                if (app && app.isRunning) {
                    app.stop();
                }
            });
        });
    </script>
</body>
</html>