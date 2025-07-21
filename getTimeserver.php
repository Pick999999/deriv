<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv.com Time Server with Countdown</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
            width: 80%;
            max-width: 500px;
        }
        .time-display {
            font-size: 2em;
            margin: 20px 0;
            color: #333;
        }
        .countdown {
            font-size: 3em;
            margin: 20px 0;
            color: #e31c4b;
            font-weight: bold;
        }
        .status {
            color: #666;
            margin: 10px 0;
        }
        button {
            background-color: #2a3052;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin: 10px 5px;
        }
        button:hover {
            background-color: #1d2233;
        }
        .input-group {
            margin: 20px 0;
        }
        input {
            padding: 8px;
            width: 80px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        label {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv.com Time</h1>
        <div class="status" id="connection-status">Connecting to Deriv.com server...</div>
        <div class="time-display" id="server-time">--:--:--</div>
        <div class="input-group">
            <label for="countdown-input">Countdown (seconds):</label>
            <input type="number" id="countdown-input" min="1" max="3600" value="60">
            <button id="start-countdown">Start Countdown</button>
        </div>
        <div class="countdown" id="countdown-display">--</div>
        <button id="reconnect-btn">Reconnect</button>
    </div>

    <script>
        // สถานะและตัวแปรสำหรับการจัดการเวลา
        let serverTimeOffset = 0; // ความต่างระหว่างเวลาเซิร์ฟเวอร์กับเวลาท้องถิ่น
        let ws; // WebSocket connection
        let countdownInterval; // interval สำหรับการนับถอยหลัง
        let countdownEndTime; // เวลาสิ้นสุดการนับถอยหลัง
        let isCountingDown = false; // สถานะการนับถอยหลัง

        // DOM Elements
        const serverTimeElement = document.getElementById('server-time');
        const connectionStatusElement = document.getElementById('connection-status');
        const countdownDisplay = document.getElementById('countdown-display');
        const countdownInput = document.getElementById('countdown-input');
        const startCountdownBtn = document.getElementById('start-countdown');
        const reconnectBtn = document.getElementById('reconnect-btn');

        // เริ่มต้นการเชื่อมต่อกับ WebSocket
        function connectWebSocket() {
            connectionStatusElement.textContent = "Connecting to Deriv.com server...";
            
            // เชื่อมต่อกับ WebSocket API ของ Deriv.com
            ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=1089');
            
            ws.onopen = function() {
                connectionStatusElement.textContent = "Connected, requesting time...";
                requestServerTime();
            };
            
            ws.onclose = function() {
                connectionStatusElement.textContent = "Disconnected from server";
            };
            
            ws.onerror = function(error) {
                connectionStatusElement.textContent = "Connection error";
                console.error('WebSocket error:', error);
            };
            
            ws.onmessage = function(msg) {
                const data = JSON.parse(msg.data);
                
                // ตรวจสอบการตอบกลับของ time request
                if (data.msg_type === 'time') {
                    if (data.error) {
                        connectionStatusElement.textContent = "Error: " + data.error.message;
                    } else {
                        handleServerTime(data.time);
                    }
                }
            };
        }

        // ส่งคำขอเวลาปัจจุบันจากเซิร์ฟเวอร์
        function requestServerTime() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify({ time: 1 }));
            }
        }

        // จัดการกับเวลาที่ได้รับจากเซิร์ฟเวอร์
        function handleServerTime(serverTime) {
            // คำนวณความต่างระหว่างเวลาเซิร์ฟเวอร์กับเวลาท้องถิ่น (เป็นวินาที)
            const localTime = Math.floor(Date.now() / 1000);
            serverTimeOffset = serverTime - localTime;
            
            connectionStatusElement.textContent = "Time synchronized with Deriv.com server";
            
            // เริ่มแสดงเวลาและอัพเดททุกวินาที
            updateTimeDisplay();
            setInterval(updateTimeDisplay, 1000);
        }

        // อัพเดทการแสดงเวลา
        function updateTimeDisplay() {
            // คำนวณเวลาปัจจุบันของเซิร์ฟเวอร์
            const currentServerTime = Math.floor(Date.now() / 1000) + serverTimeOffset;
            const serverDate = new Date(currentServerTime * 1000);
            
            // แสดงเวลาในรูปแบบ HH:MM:SS
            const hours = serverDate.getHours().toString().padStart(2, '0');
            const minutes = serverDate.getMinutes().toString().padStart(2, '0');
            const seconds = serverDate.getSeconds().toString().padStart(2, '0');
            
            serverTimeElement.textContent = `${hours}:${minutes}:${seconds}`;
            
            // ถ้ากำลังนับถอยหลัง ให้อัพเดทการแสดงผล
            if (isCountingDown) {
                updateCountdown();
            }
        }

        // เริ่มการนับถอยหลัง
        function startCountdown() {
            // ถ้ามีการนับถอยหลังอยู่แล้ว ให้ยกเลิกก่อน
            if (isCountingDown) {
                clearInterval(countdownInterval);
            }
            
            // อ่านค่าจำนวนวินาทีจาก input
            const countdownSeconds = parseInt(countdownInput.value, 10);
            if (isNaN(countdownSeconds) || countdownSeconds <= 0) {
                alert("Please enter a valid countdown time (seconds)");
                return;
            }
            
            // คำนวณเวลาสิ้นสุดการนับถอยหลัง
            const currentServerTime = Math.floor(Date.now() / 1000) + serverTimeOffset;
            countdownEndTime = currentServerTime + countdownSeconds;
            
            isCountingDown = true;
            updateCountdown();
        }

        // อัพเดทการแสดงผลเวลานับถอยหลัง
        function updateCountdown() {
            const currentServerTime = Math.floor(Date.now() / 1000) + serverTimeOffset;
            const remainingSeconds = countdownEndTime - currentServerTime;
            
            if (remainingSeconds <= 0) {
                countdownDisplay.textContent = "0";
                isCountingDown = false;
                // เพิ่มการแจ้งเตือนเมื่อนับถอยหลังเสร็จสิ้น
                alert("Countdown finished!");
                return;
            }
            
            countdownDisplay.textContent = remainingSeconds;
        }

        // Event Listeners
        startCountdownBtn.addEventListener('click', startCountdown);
        reconnectBtn.addEventListener('click', connectWebSocket);

        // เริ่มต้นเชื่อมต่อเมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', connectWebSocket);
    </script>
</body>
</html>