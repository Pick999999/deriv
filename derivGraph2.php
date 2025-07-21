ฉันต้องการ สร้าง  Object เป็น pure javascript ไว้ให้ สำหรับ  html page เรียกใช้ หลาย ๆ Object
ใน 1 page โดย  object นี้จะ มีคุณสมบัติดังนี้ 
  1.รับค่า candlestick จาก deriv.com โดยมี parameter คือ curpair,timeframe 1m,5m ,เวลาแท่งเทียน,จำนวนแท่งเทียนที่ต้องการ 
  2.วาดกราฟ candle stick + ema 2 เส้น + rsi ด้วย lightweight-charts.standalone.production.js
  โดย ema 2 เส้น จะ วาดบนกราฟ  candlestick 
  3.มี label บอกเวลา  ของแท่งเทียนที่ดึงมา 
<!DOCTYPE html>
<html>
<head>
    <title>Deriv Trading Chart</title>
    <style>
        .container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            padding: 20px;
        }
        .chart-container {
            border: 1px solid #ccc;
            padding: 10px;
        }
        .params-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        #candleChart, #rsiChart {
            height: 400px;
        }
        textarea {
            width: 100%;
            height: 200px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="chart-container">
            <div class="params-container">
                <div>
                    <label>MACD Fast Period:</label>
                    <input type="number" id="macdFast" value="12">
                    
                    <label>MACD Slow Period:</label>
                    <input type="number" id="macdSlow" value="26">
                    
                    <label>MACD Signal Period:</label>
                    <input type="number" id="macdSignal" value="9">
                </div>
                <div>
                    <label>RSI Period:</label>
                    <input type="number" id="rsiPeriod" value="14">
                    
                    <label>Start Time:</label>
                    <input type="text" id="startTime" readonly>
                    
                    <label>End Time:</label>
                    <input type="text" id="endTime" readonly>
                </div>
            </div>
            <div id="candleChart"></div>
            <div id="rsiChart"></div>
        </div>
        <div>
            <textarea id="dataOutput"></textarea>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightweight-charts/4.1.1/lightweight-charts.standalone.production.js"></script>
    <script>
        // รอให้ DOM และไฟล์ JavaScript โหลดเสร็จก่อน
        window.addEventListener('load', function() {
            const ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
            let candleSeries, macdSeries, signalSeries, rsiSeries;
            let candleChart, rsiChart;
            
            // Initialize charts
            function initCharts() {
                if (typeof LightweightCharts === 'undefined') {
                    console.error('LightweightCharts library not loaded');
                    return;
                }

                // Candle chart
                candleChart = LightweightCharts.createChart(document.getElementById('candleChart'), {
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
                    }
                });

                candleSeries = candleChart.addCandlestickSeries();
                macdSeries = candleChart.addLineSeries({
                    color: 'blue',
                    lineWidth: 2,
                });
                signalSeries = candleChart.addLineSeries({
                    color: 'red',
                    lineWidth: 2,
                });

                // RSI chart
                rsiChart = LightweightCharts.createChart(document.getElementById('rsiChart'), {
                    height: 200,
                    layout: {
                        background: { color: '#ffffff' },
                        textColor: '#333',
                    },
                    grid: {
                        vertLines: { color: '#f0f0f0' },
                        horzLines: { color: '#f0f0f0' },
                    },
                });

                rsiSeries = rsiChart.addLineSeries({
                    color: 'purple',
                    lineWidth: 2,
                });
            }

            // Calculate MACD
            function calculateMACD(data, fastPeriod, slowPeriod, signalPeriod) {
                const closes = data.map(d => d.close);
                const fastEMA = calculateEMA(closes, fastPeriod);
                const slowEMA = calculateEMA(closes, slowPeriod);
                const macdLine = fastEMA.map((fast, i) => fast - slowEMA[i]);
                const signalLine = calculateEMA(macdLine, signalPeriod);

                return data.map((candle, i) => ({
                    time: candle.time,
                    value: macdLine[i],
                    signal: signalLine[i]
                }));
            }

            // Calculate RSI
            function calculateRSI(data, period) {
                const closes = data.map(d => d.close);
                let gains = [], losses = [];
                
                for(let i = 1; i < closes.length; i++) {
                    const difference = closes[i] - closes[i-1];
                    gains.push(difference > 0 ? difference : 0);
                    losses.push(difference < 0 ? -difference : 0);
                }

                const avgGain = calculateSMA(gains, period);
                const avgLoss = calculateSMA(losses, period);
                
                return data.map((candle, i) => ({
                    time: candle.time,
                    value: i < period ? null : 100 - (100 / (1 + avgGain[i-period] / avgLoss[i-period]))
                }));
            }

            // Helper functions for technical indicators
            function calculateEMA(data, period) {
                const multiplier = 2 / (period + 1);
                let ema = [data[0]];
                
                for(let i = 1; i < data.length; i++) {
                    ema.push((data[i] - ema[i-1]) * multiplier + ema[i-1]);
                }
                
                return ema;
            }

            function calculateSMA(data, period) {
                let sma = [];
                for(let i = 0; i < data.length; i++) {
                    if(i < period) {
                        sma.push(null);
                        continue;
                    }
                    const sum = data.slice(i-period, i).reduce((a, b) => a + b, 0);
                    sma.push(sum / period);
                }
                return sma;
            }

            // Update charts with new data
            function updateCharts(candleData) {
                const macdFast = parseInt(document.getElementById('macdFast').value);
                const macdSlow = parseInt(document.getElementById('macdSlow').value);
                const macdSignal = parseInt(document.getElementById('macdSignal').value);
                const rsiPeriod = parseInt(document.getElementById('rsiPeriod').value);

                candleSeries.setData(candleData);

                const macdData = calculateMACD(candleData, macdFast, macdSlow, macdSignal);
                macdSeries.setData(macdData.map(d => ({ time: d.time, value: d.value })));
                signalSeries.setData(macdData.map(d => ({ time: d.time, value: d.signal })));

                const rsiData = calculateRSI(candleData, rsiPeriod);
                rsiSeries.setData(rsiData);

                // Update time display
                document.getElementById('startTime').value = new Date(candleData[0].time * 1000).toLocaleString();
                document.getElementById('endTime').value = new Date(candleData[candleData.length-1].time * 1000).toLocaleString();
                
                // Update textarea
                document.getElementById('dataOutput').value = JSON.stringify(candleData, null, 2);
            }

            // WebSocket message handler
            ws.onmessage = (msg) => {
                const data = JSON.parse(msg.data);
                
                if(data.msg_type === "candles") {
                    const candleData = data.candles.map(candle => ({
                        time: candle.epoch,
                        open: parseFloat(candle.open),
                        high: parseFloat(candle.high),
                        low: parseFloat(candle.low),
                        close: parseFloat(candle.close)
                    }));
                    updateCharts(candleData);
                }
            };

            // Initialize WebSocket connection
            ws.onopen = () => {
                initCharts();
                
                // Subscribe to candles
                ws.send(JSON.stringify({
                    ticks_history: "R_100",
                    adjust_start_time: 1,
                    count: 60,
                    end: "latest",
                    start: 1,
                    style: "candles",
                    subscribe: 1
                }));
            };

            // Auto-update every minute
            setInterval(() => {
                ws.send(JSON.stringify({
                    ticks_history: "R_100",
                    adjust_start_time: 1,
                    count: 60,
                    end: "latest",
                    start: 1,
                    style: "candles"
                }));
            }, 60000);

            // Event listeners for parameter changes
            document.getElementById('macdFast').addEventListener('change', () => {
                const data = JSON.parse(document.getElementById('dataOutput').value);
                updateCharts(data);
            });

            document.getElementById('macdSlow').addEventListener('change', () => {
                const data = JSON.parse(document.getElementById('dataOutput').value);
                updateCharts(data);
            });

            document.getElementById('macdSignal').addEventListener('change', () => {
                const data = JSON.parse(document.getElementById('dataOutput').value);
                updateCharts(data);
            });

            document.getElementById('rsiPeriod').addEventListener('change', () => {
                const data = JSON.parse(document.getElementById('dataOutput').value);
                updateCharts(data);
            });
        });
    </script>
</body>
</html>