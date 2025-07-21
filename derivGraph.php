<?php
/*
  สร้าง  html และ   pure javascript  ให้ ดึงค่าข้อมูล candle stick จำนวน 60 แท่ง นับจาก เวลาปัจจุบัน 
จาก deriv.com แล้วนำค่ามาใส่ textarea และ  Plot Candle stick และ Indicator
macd 2 เส้น บน  candlestick และ rsi แยกต่างหาก และมี  text box บอก เวลา เริ่มต้น เวลาสิ้นสุด ของ candlestick 
โดย  ค่า macd และ  rsi ให้ ตั้งค่า parameter ได้จาก html และ  Update ค่า candlestick ทุกๆ 1 นาที โดยอัตโนมัติ  แบบ realtime และนำข้อมูล จาก textarea
มา วาดกราฟใหม่ โดยใช้  lightweight-charts.standalone.production.js
*/
?>
<!DOCTYPE html>
<html>
<head>
    <title>Deriv Candlestick Chart</title>
    <script type="text/javascript" src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        .container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 20px;
        }
        .chart-container {
            height: 600px;
            display: grid;
            grid-template-rows: 3fr 1fr 1fr;
            gap: 10px;
        }
        #candlestickChart {
            height: 300px;
            width: 100%;
        }
        #macdChart {
            height: 150px;
            width: 100%;
        }
        #rsiChart {
            height: 150px;
            width: 100%;
        }
        .params {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        .chart-box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="params">
            <div>
                <label>MACD Fast Period:</label>
                <input type="number" id="macdFast" value="12">
            </div>
            <div>
                <label>MACD Slow Period:</label>
                <input type="number" id="macdSlow" value="26">
            </div>
            <div>
                <label>MACD Signal Period:</label>
                <input type="number" id="macdSignal" value="9">
            </div>
            <div>
                <label>RSI Period:</label>
                <input type="number" id="rsiPeriod" value="14">
            </div>
        </div>
        <textarea id="dataArea" rows="10" style="width: 100%"></textarea>
        <div class="chart-container">
            <div id="candlestickChart" class="chart-box"></div>
            <div id="macdChart" class="chart-box"></div>
            <div id="rsiChart" class="chart-box"></div>
        </div>
    </div>

    <script>
        let candlestickSeries, macdSeries, signalSeries, rsiSeries;
        let candlestickChart, macdChart, rsiChart;

        // Initialize charts
        function initializeCharts() {
            if (typeof LightweightCharts === 'undefined') {
                console.error('TradingView library not loaded');
                return;
            }

            const chartProperties = {
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                width: document.getElementById('candlestickChart').clientWidth,
                height: document.getElementById('candlestickChart').clientHeight
            };

            // Candlestick chart
            candlestickChart = LightweightCharts.createChart(
                document.getElementById('candlestickChart'), 
                {
                    ...chartProperties,
                    timeScale: {
                        timeVisible: true,
                        secondsVisible: false,
                    }
                }
            );
            candlestickSeries = candlestickChart.addCandlestickSeries();

            // MACD chart
            macdChart = LightweightCharts.createChart(
                document.getElementById('macdChart'),
                {
                    ...chartProperties,
                    height: document.getElementById('macdChart').clientHeight
                }
            );
            macdSeries = macdChart.addLineSeries({ color: '#2962FF' });
            signalSeries = macdChart.addLineSeries({ color: '#FF2962' });

            // RSI chart
            rsiChart = LightweightCharts.createChart(
                document.getElementById('rsiChart'),
                {
                    ...chartProperties,
                    height: document.getElementById('rsiChart').clientHeight
                }
            );
            rsiSeries = rsiChart.addLineSeries({ color: '#2962FF' });

            // Handle window resize
            window.addEventListener('resize', () => {
                if (candlestickChart && macdChart && rsiChart) {
                    const width = document.getElementById('candlestickChart').clientWidth;
                    
                    candlestickChart.applyOptions({
                        width: width,
                        height: document.getElementById('candlestickChart').clientHeight
                    });

                    macdChart.applyOptions({
                        width: width,
                        height: document.getElementById('macdChart').clientHeight
                    });

                    rsiChart.applyOptions({
                        width: width,
                        height: document.getElementById('rsiChart').clientHeight
                    });
                }
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
                value: macdLine[i] || 0,
                signal: signalLine[i] || 0
            }));
        }

        // Calculate RSI
        function calculateRSI(data, period) {
            const closes = data.map(d => d.close);
            let gains = [0];
            let losses = [0];

            for (let i = 1; i < closes.length; i++) {
                const difference = closes[i] - closes[i - 1];
                gains.push(difference > 0 ? difference : 0);
                losses.push(difference < 0 ? -difference : 0);
            }

            const averageGain = calculateSMA(gains, period);
            const averageLoss = calculateSMA(losses, period);

            const rsi = averageGain.map((gain, i) => {
                const loss = averageLoss[i];
                if (loss === 0) return 100;
                const RS = gain / loss;
                return 100 - (100 / (1 + RS));
            });

            return data.map((candle, i) => ({
                time: candle.time,
                value: rsi[i] || 0
            }));
        }

        // Helper function to calculate EMA
        function calculateEMA(data, period) {
            const multiplier = 2 / (period + 1);
            let ema = [data[0]];

            for (let i = 1; i < data.length; i++) {
                ema.push((data[i] - ema[i - 1]) * multiplier + ema[i - 1]);
            }

            return ema;
        }

        // Helper function to calculate SMA
        function calculateSMA(data, period) {
            const sma = [];
            for (let i = 0; i < data.length; i++) {
                if (i < period - 1) {
                    sma.push(0);
                    continue;
                }
                const sum = data.slice(i - period + 1, i + 1).reduce((a, b) => a + b, 0);
                sma.push(sum / period);
            }
            return sma;
        }

        // Update charts with new data
        function updateCharts(data) {
            if (!candlestickSeries || !macdSeries || !signalSeries || !rsiSeries) {
                console.error('Charts not initialized');
                return;
            }

            // Update parameters
            const macdFast = parseInt(document.getElementById('macdFast').value);
            const macdSlow = parseInt(document.getElementById('macdSlow').value);
            const macdSignal = parseInt(document.getElementById('macdSignal').value);
            const rsiPeriod = parseInt(document.getElementById('rsiPeriod').value);

            // Calculate indicators
            const macdData = calculateMACD(data, macdFast, macdSlow, macdSignal);
            const rsiData = calculateRSI(data, rsiPeriod);

            // Update charts
            candlestickSeries.setData(data);
            macdSeries.setData(macdData.map(d => ({ time: d.time, value: d.value })));
            signalSeries.setData(macdData.map(d => ({ time: d.time, value: d.signal })));
            rsiSeries.setData(rsiData);

            // Sync time scales
            macdChart.timeScale().fitContent();
            rsiChart.timeScale().fitContent();
        }

        // Initialize WebSocket connection
        function initializeWebSocket() {
            const ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=1089');

            ws.onopen = function() {
                console.log('WebSocket Connected');
                // Subscribe to candlestick data
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

            ws.onmessage = function(msg) {
                const data = JSON.parse(msg.data);
                if (data.candles) {
                    const formattedData = data.candles.map(candle => ({
                        time: candle.epoch,
                        open: parseFloat(candle.open),
                        high: parseFloat(candle.high),
                        low: parseFloat(candle.low),
                        close: parseFloat(candle.close)
                    }));

                    document.getElementById('dataArea').value = JSON.stringify(formattedData, null, 2);
                    updateCharts(formattedData);
                }
            };

            ws.onerror = function(err) {
                console.error('WebSocket Error:', err);
            };

            return ws;
        }

        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Give the browser a moment to calculate sizes
            setTimeout(() => {
                initializeCharts();
                const ws = initializeWebSocket();

                // Update charts when parameters change
                document.querySelectorAll('input').forEach(input => {
                    input.addEventListener('change', () => {
                        try {
                            const data = JSON.parse(document.getElementById('dataArea').value);
                            updateCharts(data);
                        } catch (err) {
                            console.error('Error updating charts:', err);
                        }
                    });
                });
            }, 100);
        });
    </script>
</body>
</html>