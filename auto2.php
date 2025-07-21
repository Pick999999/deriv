<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60" id="refreshMeta">
    <title>Auto Refresh - iPad 2</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f0f0f0;
            font-size: 18px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .refresh-info {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4CAF50;
        }
        .time-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .control-panel {
            background: #fff3e0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .pause-btn {
            background: #ff9800;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }
        .pause-btn:hover {
            background: #f57c00;
        }
        .pause-btn.paused {
            background: #4CAF50;
        }
        .status {
            font-weight: bold;
            margin: 10px 0;
        }
        .timer {
            font-size: 24px;
            font-weight: bold;
            color: #2196F3;
            text-align: center;
            margin: 20px 0;
        }
        .content {
            line-height: 1.6;
        }
        .last-update {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="refresh-info">
            ✅ หน้านี้จะ refresh อัตโนมัติทุก 1 นาที
        </div>
        
        <div class="time-info">
            <div><strong>⏰ เวลาเริ่มต้น:</strong> <span id="startTime"></span></div>
            <div><strong>🔄 Refresh ครั้งต่อไป:</strong> <span id="nextRefresh"></span></div>
        </div>
        
        <div class="control-panel">
            <div class="status" id="status">🟢 กำลังทำงาน</div>
            <button class="pause-btn" id="pauseBtn" onclick="togglePause()">⏸️ หยุดพัก</button>
        </div>
        
        <div class="timer" id="countdown">60</div>
        
        <div class="content">
            <h2>🔄 Auto Refresh Page สำหรับ iPad 2</h2>
            
            <p>หน้านี้ออกแบบมาสำหรับ iPad 2 โดยเฉพาะ:</p>
            
            <ul>
                <li>ใช้ meta refresh tag (ไม่ต้องพึ่ง JavaScript)</li>
                <li>Font ขนาดใหญ่เพื่อให้อ่านง่าย</li>
                <li>Layout เหมาะกับหน้าจอ iPad</li>
                <li>มี countdown timer แสดงเวลา</li>
            </ul>
            
            <p><strong>วิธีใช้งาน:</strong></p>
            <ol>
                <li>เปิดหน้านี้ใน Safari บน iPad</li>
                <li>หน้าจะ refresh ทุก 60 วินาที</li>
                <li>สามารถปิดหน้าต่างได้เมื่อไม่ใช้งาน</li>
            </ol>
            
            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <strong>💡 เคล็ดลับ:</strong> ถ้าต้องการให้ refresh เร็วขึ้น เปลี่ยน content="60" เป็นจำนวนวินาทีที่ต้องการ
            </div>
        </div>
        
        <div class="last-update">
            Last updated: <span id="currentTime"></span>
        </div>
    </div>

    <script>
        // ตัวแปรสำหรับควบคุม
        var countdownElement = document.getElementById('countdown');
        var timeElement = document.getElementById('currentTime');
        var startTimeElement = document.getElementById('startTime');
        var nextRefreshElement = document.getElementById('nextRefresh');
        var statusElement = document.getElementById('status');
        var pauseBtn = document.getElementById('pauseBtn');
        var refreshMeta = document.getElementById('refreshMeta');
        
        var seconds = 60;
        var isPaused = false;
        var startTime = new Date();
        var nextRefreshTime = new Date(startTime.getTime() + 60000);
        var countdownInterval;
        
        // แสดงเวลาปัจจุบัน
        function updateTime() {
            var now = new Date();
            timeElement.textContent = now.toLocaleString('th-TH');
        }
        
        // แสดงเวลาเริ่มต้นและเวลา refresh ครั้งต่อไป
        function updateTimeInfo() {
            startTimeElement.textContent = startTime.toLocaleString('th-TH');
            nextRefreshElement.textContent = nextRefreshTime.toLocaleString('th-TH');
        }
        
        // Countdown timer
        function countdown() {
            if (isPaused) {
                countdownElement.textContent = '⏸️ หยุดพัก';
                return;
            }
            
            countdownElement.textContent = seconds + ' วินาที';
            seconds--;
            
            if (seconds < 0) {
                countdownElement.textContent = 'กำลัง refresh...';
                return;
            }
        }
        
        // เริ่ม countdown
        function startCountdown() {
            countdownInterval = setInterval(countdown, 1000);
        }
        
        // หยุด countdown
        function stopCountdown() {
            clearInterval(countdownInterval);
        }
        
        // สลับสถานะ pause/resume
        function togglePause() {
            isPaused = !isPaused;
            
            if (isPaused) {
                // หยุดพัก
                stopCountdown();
                refreshMeta.setAttribute('content', '999999'); // หยุด auto refresh
                statusElement.textContent = '🔴 หยุดพัก';
                pauseBtn.textContent = '▶️ เริ่มต้นใหม่';
                pauseBtn.className = 'pause-btn paused';
            } else {
                // เริ่มใหม่
                seconds = 60;
                nextRefreshTime = new Date(Date.now() + 60000);
                updateTimeInfo();
                startCountdown();
                refreshMeta.setAttribute('content', '60'); // เริ่ม auto refresh
                statusElement.textContent = '🟢 กำลังทำงาน';
                pauseBtn.textContent = '⏸️ หยุดพัก';
                pauseBtn.className = 'pause-btn';
            }
        }
        
        // เริ่มต้นทุกอย่าง
        function init() {
            updateTime();
            updateTimeInfo();
            startCountdown();
            
            // อัพเดทเวลาทุกวินาที
            setInterval(updateTime, 1000);
        }
        
        // เริ่มต้นเมื่อโหลดหน้าเสร็จ
        init();
    </script>
</body>
</html>