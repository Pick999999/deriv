<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60">
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
        // JavaScript แบบเรียบง่ายสำหรับ iPad 2
        var countdownElement = document.getElementById('countdown');
        var timeElement = document.getElementById('currentTime');
        var seconds = 60;
        
        // แสดงเวลาปัจจุบัน
        function updateTime() {
            var now = new Date();
            timeElement.textContent = now.toLocaleString('th-TH');
        }
        
        // Countdown timer
        function countdown() {
            countdownElement.textContent = seconds + ' วินาที';
            seconds--;
            
            if (seconds < 0) {
                countdownElement.textContent = 'กำลัง refresh...';
                return;
            }
            
            setTimeout(countdown, 1000);
        }
        
        // เริ่มต้น
        updateTime();
        countdown();
    </script>
</body>
</html>