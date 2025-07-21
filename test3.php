<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candle Aggregation (2s to 1m)</title>
	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
 

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .chart-container {
            height: 400px;
            width: 100%;
            margin-bottom: 30px;
        }
        .status-panel {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .status-panel h3 {
            margin-top: 0;
            color: #555;
        }
        .log-container {
            height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .log-entry {
            margin-bottom: 5px;
            font-family: monospace;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .log-timestamp {
            color: #777;
            font-size: 0.9em;
        }
        .current-candle-info {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        .candle-value {
            flex: 1;
            min-width: 120px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
        }
        .candle-value h4 {
            margin: 0 0 5px 0;
            color: #555;
        }
        .candle-value .value {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
        }
        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        .button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .button.stop {
            background-color: #f44336;
        }
        .symbol-selector {
            margin-bottom: 20px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv Candle Aggregation (2s to 1m)</h1>
        
        <div class="symbol-selector">
            <label for="symbol">เลือกสัญลักษณ์: </label>
            <select id="symbol">
                <option value="R_10">Volatility 10 Index</option>
                <option value="R_25">Volatility 25 Index</option>
                <option value="R_50">Volatility 50 Index</option>
                <option value="R_75">Volatility 75 Index</option>
                <option value="R_100">Volatility 100 Index</option>
            </select>
            <button id="connectBtn" class="button">เชื่อมต่อ WebSocket</button>
            <button id="disconnectBtn" class="button stop" disabled>ยกเลิกการเชื่อมต่อ</button>
        </div>
        
        <div class="status-panel">
            <h3>สถานะการเชื่อมต่อ: <span id="connectionStatus">ไม่ได้เชื่อมต่อ</span></h3>
        </div>
        
        <div class="chart-container" id="chart"></div>
        
        <h3>แท่งเทียนปัจจุบัน (1 นาที)</h3>
        <div class="current-candle-info">
            <div class="candle-value">
                <h4>เวลา</h4>
                <div id="currentTime" class="value">-</div>
            </div>
            <div class="candle-value">
                <h4>เปิด (Open)</h4>
                <div id="currentOpen" class="value">-</div>
            </div>
            <div class="candle-value">
                <h4>สูงสุด (High)</h4>
                <div id="currentHigh" class="value">-</div>
            </div>
            <div class="candle-value">
                <h4>ต่ำสุด (Low)</h4>
                <div id="currentLow" class="value">-</div>
            </div>
            <div class="candle-value">
                <h4>ปิด (Close)</h4>
                <div id="currentClose" class="value">-</div>
            </div>
        </div>
        
        <h3>บันทึกการทำงาน</h3>
        <div class="log-container" id="logContainer"></div>
    </div>

    <script>
        // ตัวแปรสำหรับเก็บอ้างอิงถึง WebSocket
        let ws = null;
        
        // ตัวแปรสำหรับเก็บข้อมูลแท่งเทียน
        let currentCandle = {
            open: null,
            high: null,
            low: null,
            close: null,
            timestamp: null
        };
        
        // เก็บข้อมูลแท่งเทียนที่สมบูรณ์แล้ว
        const completedCandles = [];
        
        // ตัวแปรสำหรับเก็บอ้างอิงถึงกราฟ
        let chart = null;
        let candleSeries = null;
        
        // สร้างกราฟแท่งเทียน
        function createChart() {
            const chartContainer = document.getElementById('chart');
            
            chart = LightweightCharts.createChart(chartContainer, {
                width: chartContainer.clientWidth,
                height: 400,
                layout: {
                    backgroundColor: '#ffffff',
                    textColor: '#333',
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
                timeScale: {
                    borderColor: '#8c8c8c',
                    timeVisible: true,
                    secondsVisible: false,
                },
            });
            
            candleSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderDownColor: '#ef5350',
                borderUpColor: '#26a69a',
                wickDownColor: '#ef5350',
                wickUpColor: '#26a69a',
            });
            
            // ปรับขนาดกราฟเมื่อขนาดหน้าจอเปลี่ยน
            window.addEventListener('resize', () => {
                if (chart) {
                    chart.applyOptions({
                        width: chartContainer.clientWidth
                    });
                }
            });
        }
        
        // เพิ่มบันทึกลงในพื้นที่บันทึกการทำงาน
        function addLog(message) {
            const logContainer = document.getElementById('logContainer');
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            
            const logEntry = document.createElement('div');
            logEntry.className = 'log-entry';
            logEntry.innerHTML = `<span class="log-timestamp">[${timeString}]</span> ${message}`;
            
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }
        
        // อัปเดตข้อมูลแท่งเทียนปัจจุบันในหน้าเว็บ
        function updateCurrentCandleDisplay() {
            if (currentCandle.timestamp === null) {
                document.getElementById('currentTime').textContent = '-';
                document.getElementById('currentOpen').textContent = '-';
                document.getElementById('currentHigh').textContent = '-';
                document.getElementById('currentLow').textContent = '-';
                document.getElementById('currentClose').textContent = '-';
                return;
            }
            
            const time = new Date(currentCandle.timestamp);
            document.getElementById('currentTime').textContent = time.toLocaleTimeString();
            document.getElementById('currentOpen').textContent = currentCandle.open !== null ? currentCandle.open.toFixed(5) : '-';
            document.getElementById('currentHigh').textContent = currentCandle.high !== null ? currentCandle.high.toFixed(5) : '-';
            document.getElementById('currentLow').textContent = currentCandle.low !== null ? currentCandle.low.toFixed(5) : '-';
            document.getElementById('currentClose').textContent = currentCandle.close !== null ? currentCandle.close.toFixed(5) : '-';
        }
        
        // ฟังก์ชันสำหรับปรับปรุงกราฟแท่งเทียน
        function updateChart() {
            if (!candleSeries) return;
            
            // อัปเดตแท่งเทียนที่สมบูรณ์
            const chartData = completedCandles.map(candle => ({
                time: candle.timestamp / 1000,
                open: candle.open,
                high: candle.high,
                low: candle.low,
                close: candle.close
            }));
            
            candleSeries.setData(chartData);
            
            // เพิ่มแท่งเทียนปัจจุบันถ้ามีข้อมูล
            if (currentCandle.open !== null) {
                candleSeries.update({
                    time: currentCandle.timestamp / 1000,
                    open: currentCandle.open,
                    high: currentCandle.high,
                    low: currentCandle.low,
                    close: currentCandle.close
                });
            }
        }
        
        // ฟังก์ชันสำหรับส่งคำขอไปยัง API
        function sendRequest(request) {
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify(request));
                addLog(`ส่งคำขอ: ${JSON.stringify(request)}`);
            } else {
                addLog('ไม่สามารถส่งคำขอได้: WebSocket ไม่ได้เชื่อมต่อ');
            }
        }
        
        // ฟังก์ชันสำหรับเริ่มต้นแท่งเทียนใหม่
        function startNewCandle(timestamp, price) {
            // บันทึกแท่งเทียนก่อนหน้า (ถ้ามี)
            if (currentCandle.open !== null) {
                completedCandles.push({...currentCandle});
                addLog(`แท่งเทียนสมบูรณ์: O:${currentCandle.open.toFixed(5)} H:${currentCandle.high.toFixed(5)} L:${currentCandle.low.toFixed(5)} C:${currentCandle.close.toFixed(5)} เวลา:${new Date(currentCandle.timestamp).toLocaleTimeString()}`);
            }
            
            // เริ่มต้นแท่งเทียนใหม่
            currentCandle = {
                open: price,
                high: price,
                low: price,
                close: price,
                timestamp: timestamp
            };
            
            addLog(`เริ่มแท่งเทียนใหม่ที่ราคา ${price.toFixed(5)} เวลา ${new Date(timestamp).toLocaleTimeString()}`);
            updateCurrentCandleDisplay();
            updateChart();
        }
        
        // ฟังก์ชันสำหรับปรับปรุงแท่งเทียนปัจจุบัน
        function updateCurrentCandle(price) {
            if (currentCandle.open === null) return;
            
            // ปรับค่า high และ low ถ้าจำเป็น
            const oldHigh = currentCandle.high;
            const oldLow = currentCandle.low;
            
            currentCandle.high = Math.max(currentCandle.high, price);
            currentCandle.low = Math.min(currentCandle.low, price);
            currentCandle.close = price;
            
            // บันทึกการเปลี่ยนแปลงที่สำคัญ
            if (currentCandle.high !== oldHigh) {
                addLog(`อัปเดตค่าสูงสุดเป็น: ${currentCandle.high.toFixed(5)}`);
            }
            if (currentCandle.low !== oldLow) {
                addLog(`อัปเดตค่าต่ำสุดเป็น: ${currentCandle.low.toFixed(5)}`);
            }
            
            updateCurrentCandleDisplay();
            updateChart();
        }
        
        // ฟังก์ชันสำหรับตรวจสอบว่าต้องสร้างแท่งเทียนใหม่หรือไม่
        function checkForNewCandle(timestamp, price) {
            // ถ้ายังไม่มีแท่งเทียนปัจจุบัน หรือได้เข้าสู่นาทีใหม่
            const currentMinute = Math.floor(timestamp / 60000) * 60000;
            const previousMinute = currentCandle.timestamp ? Math.floor(currentCandle.timestamp / 60000) * 60000 : null;
            
            if (currentCandle.open === null || currentMinute !== previousMinute) {
                startNewCandle(currentMinute, price);
            } else {
                updateCurrentCandle(price);
            }
        }
        
        // ฟังก์ชันสำหรับเชื่อมต่อ WebSocket
        function connectWebSocket() {
            if (ws) {
                ws.close();
            }
            
            const symbolSelect = document.getElementById('symbol');
            const selectedSymbol = symbolSelect.value;
            
            // ล้างข้อมูลเก่า
            currentCandle = {
                open: null,
                high: null,
                low: null,
                close: null,
                timestamp: null
            };
            completedCandles.length = 0;
            updateCurrentCandleDisplay();
            
            // เปลี่ยนสถานะปุ่ม
            document.getElementById('connectBtn').disabled = true;
            document.getElementById('disconnectBtn').disabled = false;
            document.getElementById('connectionStatus').textContent = 'กำลังเชื่อมต่อ...';
            
            // เชื่อมต่อ WebSocket
            ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
            
            // เมื่อเชื่อมต่อสำเร็จ
            ws.onopen = function() {
                addLog('เชื่อมต่อ WebSocket สำเร็จ');
                document.getElementById('connectionStatus').textContent = 'เชื่อมต่อแล้ว';
                
                // ส่งคำขอเพื่อเริ่มต้นการสมัครรับข้อมูล tick
                sendRequest({
                    ticks_history: selectedSymbol,
                    style: "candles",
                    granularity: 2*60, // 2 วินาที
                    subscribe: 1,
                    end: "latest"
                });
            };
            
            // เมื่อได้รับข้อมูล
            ws.onmessage = function(msg) {
                const data = JSON.parse(msg.data);
                
                // เมื่อได้รับข้อมูล candles history
                if (data.candles) {
                    addLog(`ได้รับข้อมูลประวัติแท่งเทียน ${data.candles.length} แท่ง`);
                    
                    // ประมวลผลแท่งเทียนล่าสุด (ถ้ามี)
                    if (data.candles.length > 0) {
                        const latestCandle = data.candles[data.candles.length - 1];
                        const timestamp = latestCandle.epoch * 1000; // แปลงเป็นมิลลิวินาที
                        const price = parseFloat(latestCandle.close);
                        checkForNewCandle(timestamp, price);
                    }
                }
                
                // เมื่อได้รับข้อมูล candle ใหม่ (จากการ subscribe)
                if (data.ohlc) {
                    const timestamp = data.ohlc.epoch * 1000; // แปลงเป็นมิลลิวินาที
                    const price = parseFloat(data.ohlc.close);
                    
                    addLog(`ได้รับข้อมูล OHLC ราคา ${price.toFixed(5)} เวลา ${new Date(timestamp).toLocaleTimeString()}`);
                    checkForNewCandle(timestamp, price);
                }
                
                // เมื่อได้รับข้อความแสดงข้อผิดพลาด
                if (data.error) {
                    addLog(`ข้อผิดพลาด: ${data.error.message}`);
                    document.getElementById('connectionStatus').textContent = `เกิดข้อผิดพลาด: ${data.error.message}`;
                }
            };
            
            // จัดการข้อผิดพลาด
            ws.onerror = function(error) {
                addLog(`ข้อผิดพลาด WebSocket: ${error.message || 'ไม่ทราบสาเหตุ'}`);
                document.getElementById('connectionStatus').textContent = 'เกิดข้อผิดพลาด';
            };
            
            // เมื่อการเชื่อมต่อถูกปิด
            ws.onclose = function() {
                addLog('การเชื่อมต่อ WebSocket ถูกปิด');
                document.getElementById('connectionStatus').textContent = 'ไม่ได้เชื่อมต่อ';
                document.getElementById('connectBtn').disabled = false;
                document.getElementById('disconnectBtn').disabled = true;
            };
        }
        
        // ฟังก์ชันสำหรับปิดการเชื่อมต่อ WebSocket
        function disconnectWebSocket() {
            if (ws) {
                ws.close();
                ws = null;
            }
        }
        
        // เมื่อหน้าเว็บโหลดเสร็จ
        document.addEventListener('DOMContentLoaded', function() {
            // สร้างกราฟ
            createChart();
            
            // ตั้งค่าปุ่มเชื่อมต่อ
            document.getElementById('connectBtn').addEventListener('click', connectWebSocket);
            
            // ตั้งค่าปุ่มยกเลิกการเชื่อมต่อ
            document.getElementById('disconnectBtn').addEventListener('click', disconnectWebSocket);
            
            addLog('ระบบพร้อมใช้งาน กรุณาเลือกสัญลักษณ์และกดเชื่อมต่อ WebSocket');
        });
    </script>
</body>
</html>