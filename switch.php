<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Switch Checkbox Examples</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 40px;
            font-size: 2em;
        }

        .switch-group {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 4px solid #667eea;
        }

        .switch-group h3 {
            margin-top: 0;
            color: #555;
            font-size: 1.2em;
        }

        /* Basic Switch */
        .switch-basic {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            margin: 10px;
        }

        .switch-basic input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #4CAF50;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        /* Modern Switch */
        .switch-modern {
            position: relative;
            display: inline-block;
            width: 70px;
            height: 40px;
            margin: 10px;
        }

        .switch-modern input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider-modern {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            transition: all 0.3s ease;
            border-radius: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .slider-modern:before {
            position: absolute;
            content: "";
            height: 32px;
            width: 32px;
            left: 4px;
            bottom: 4px;
            background: white;
            transition: all 0.3s ease;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        input:checked + .slider-modern {
            background: linear-gradient(135deg, #4ecdc4, #44a08d);
        }

        input:checked + .slider-modern:before {
            transform: translateX(30px);
        }

        /* iOS Style Switch */
        .switch-ios {
            position: relative;
            display: inline-block;
            width: 80px;
            height: 45px;
            margin: 10px;
        }

        .switch-ios input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider-ios {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e0e0e0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 45px;
            border: 2px solid #f0f0f0;
        }

        .slider-ios:before {
            position: absolute;
            content: "";
            height: 35px;
            width: 35px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 50%;
            box-shadow: 0 3px 12px rgba(0,0,0,0.15);
        }

        input:checked + .slider-ios {
            background-color: #007AFF;
            border-color: #007AFF;
        }

        input:checked + .slider-ios:before {
            transform: translateX(35px);
        }

        /* Neon Switch */
        .switch-neon {
            position: relative;
            display: inline-block;
            width: 90px;
            height: 50px;
            margin: 10px;
        }

        .switch-neon input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider-neon {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #2c3e50;
            transition: all 0.4s ease;
            border-radius: 50px;
            border: 3px solid #34495e;
        }

        .slider-neon:before {
            position: absolute;
            content: "";
            height: 38px;
            width: 38px;
            left: 4px;
            bottom: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transition: all 0.4s ease;
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
        }

        input:checked + .slider-neon {
            background-color: #1a252f;
            border-color: #00ff88;
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.3);
        }

        input:checked + .slider-neon:before {
            transform: translateX(40px);
            background: linear-gradient(135deg, #00ff88, #00cc6a);
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.6);
        }

        /* Label Styles */
        .switch-label {
            display: flex;
            align-items: center;
            margin: 15px 0;
            font-size: 16px;
            color: #555;
        }

        .switch-label span {
            margin-left: 15px;
            font-weight: 500;
        }

        /* Status Display */
        .status {
            margin-top: 30px;
            padding: 20px;
            background: #e8f5e8;
            border-radius: 10px;
            border-left: 4px solid #4CAF50;
        }

        .status h4 {
            margin: 0 0 10px 0;
            color: #2e7d32;
        }

        .status-item {
            margin: 5px 0;
            color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔘 Switch Checkbox Examples</h1>
        
        <div class="switch-group">
            <h3>Basic Switch</h3>
            <label class="switch-basic">
                <input type="checkbox" id="basic1">
                <span class="slider"></span>
            </label>
            <label class="switch-basic">
                <input type="checkbox" id="basic2" checked>
                <span class="slider"></span>
            </label>
        </div>

        <div class="switch-group">
            <h3>Modern Gradient Switch</h3>
            <label class="switch-modern">
                <input type="checkbox" id="modern1">
                <span class="slider-modern"></span>
            </label>
            <label class="switch-modern">
                <input type="checkbox" id="modern2" checked>
                <span class="slider-modern"></span>
            </label>
        </div>

        <div class="switch-group">
            <h3>iOS Style Switch</h3>
            <label class="switch-ios">
                <input type="checkbox" id="ios1">
                <span class="slider-ios"></span>
            </label>
            <label class="switch-ios">
                <input type="checkbox" id="ios2" checked>
                <span class="slider-ios"></span>
            </label>
        </div>

        <div class="switch-group">
            <h3>Neon Glow Switch</h3>
            <label class="switch-neon">
                <input type="checkbox" id="neon1">
                <span class="slider-neon"></span>
            </label>
            <label class="switch-neon">
                <input type="checkbox" id="neon2" checked>
                <span class="slider-neon"></span>
            </label>
        </div>

        <div class="switch-group">
            <h3>Switch with Labels</h3>
            <label class="switch-label">
                <label class="switch-ios">
                    <input type="checkbox" id="notifications" onchange="updateStatus()">
                    <span class="slider-ios"></span>
                </label>
                <span>การแจ้งเตือน</span>
            </label>

            <label class="switch-label">
                <label class="switch-modern">
                    <input type="checkbox" id="darkMode" onchange="updateStatus()">
                    <span class="slider-modern"></span>
                </label>
                <span>โหมดมืด</span>
            </label>

            <label class="switch-label">
                <label class="switch-neon">
                    <input type="checkbox" id="autoSave" checked onchange="updateStatus()">
                    <span class="slider-neon"></span>
                </label>
                <span>บันทึกอัตโนมัติ</span>
            </label>
        </div>

        <div class="status">
            <h4>🔍 สถานะปัจจุบัน</h4>
            <div class="status-item" id="notificationStatus">การแจ้งเตือน: ปิด</div>
            <div class="status-item" id="darkModeStatus">โหมดมืด: ปิด</div>
            <div class="status-item" id="autoSaveStatus">บันทึกอัตโนมัติ: เปิด</div>
        </div>
    </div>

    <script>
        function updateStatus() {
            const notifications = document.getElementById('notifications').checked;
            const darkMode = document.getElementById('darkMode').checked;
            const autoSave = document.getElementById('autoSave').checked;

            document.getElementById('notificationStatus').textContent = 
                `การแจ้งเตือน: ${notifications ? 'เปิด' : 'ปิด'}`;
            document.getElementById('darkModeStatus').textContent = 
                `โหมดมืด: ${darkMode ? 'เปิด' : 'ปิด'}`;
            document.getElementById('autoSaveStatus').textContent = 
                `บันทึกอัตโนมัติ: ${autoSave ? 'เปิด' : 'ปิด'}`;
        }

        // Initialize status on page load
        updateStatus();
    </script>
</body>
</html>