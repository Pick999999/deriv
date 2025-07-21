<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two Columns with Equal Height Divs</title>
    <style>
        .row {
            display: flex;
            width: 100%; /* กำหนดความกว้างของแถว (ปรับได้ตามต้องการ) */
            height: 350px; /* กำหนดความสูงของแถว (ปรับได้ตามต้องการ) */
            border: 1px solid #ccc; /* เส้นขอบเพื่อแสดงโครงสร้าง (ลบได้) */
        }

        .column-a {
            display: flex;
            flex-direction: column;
            flex: 1; /* ให้ column A ใช้พื้นที่เท่ากันกับ column B ในแถว */
            border-right: 1px solid #eee; /* เส้นขอบระหว่างคอลัมน์ (ลบได้) */
        }

        .column-a div {
            flex: 1; /* ทำให้ div ภายใน column A มีความสูงเท่ากัน */
            background-color: #f0f0f0; /* สีพื้นหลังเพื่อแสดงความแตกต่าง (ลบได้) */
            border-bottom: 1px solid #eee; /* เส้นขอบระหว่าง div ใน column A (ลบได้) */
            display: flex; /* จัดข้อความให้อยู่ตรงกลาง (ทางเลือก) */
            justify-content: center; /* จัดข้อความให้อยู่ตรงกลาง (ทางเลือก) */
            align-items: center; /* จัดข้อความให้อยู่ตรงกลาง (ทางเลือก) */
        }

        .column-a div:last-child {
            border-bottom: none; /* ลบเส้นขอบล่างของ div สุดท้ายใน column A */
        }

        .column-b {
            flex: 1; /* ให้ column B ใช้พื้นที่เท่ากันกับ column A ในแถว */
            height: 100%; /* ให้ div ใน column B สูงเต็มความสูงของแถว */
            background-color: #e0e0e0; /* สีพื้นหลังเพื่อแสดงความแตกต่าง (ลบได้) */
            display: flex; /* จัดข้อความให้อยู่ตรงกลาง (ทางเลือก) */
            justify-content: center; /* จัดข้อความให้อยู่ตรงกลาง (ทางเลือก) */
            align-items: center; /* จัดข้อความให้อยู่ตรงกลาง (ทางเลือก) */
        }
		body {
            font-family: Arial, sans-serif;
            background:blue;
        }
        .container {

			swidth:100%;
			background:red
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .controls {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        button {
            padding: 8px 16px;
            cursor: pointer;
        }
        .status {
            margin-top: 10px;
            padding: 8px;
            border-radius: 4px;
        }
        .connected {
            background-color: #d4edda;
            color: #155724;
        }
        .disconnected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .server-time {
            margin-left: auto;
            padding: 8px;
            background-color: #e7f5fe;
            border-radius: 4px;
            font-weight: bold;
        }
        select {
            padding: 8px;
        }
		.scontainer {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        #chart-container {
            width: 100%;
            height: 100px;
            border: 1px solid #ddd;
			margin: 20px;
        }
		#chart-container2 {
            width: 300px;
            height: 100px;
            border: 1px solid #ddd;
			margin: 20px;
        }
        textarea {
            width: 100%;
            height: 200px;
            font-family: monospace;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .controls {
            display: flex;
            gap: 10px;
        }
		.flex { display:flex; flex-direction:row }
    </style>
</head>
<body>
    <div id="" class="bordergray flex">
     <div class="controls">
		<div id="server-time" class="server-time">Server Time: --:--:--</div>
            <button id="connect-btn">Connect</button>
            <button id="disconnect-btn">Disconnect</button>
            <select id="symbol-select">
                <option value="R_100">Volatility 100 Index</option>
                <option value="R_50">Volatility 50 Index</option>
                <option value="R_25">Volatility 25 Index</option>
                <option value="1HZ100V">1HZ100V</option>
            </select>
            <select id="timeframe-select">
                <option value="60">1 Minute</option>
                <option value="300">5 Minutes</option>
                <option value="900">15 Minutes</option>
                <option value="3600">1 Hour</option>
                <option value="86400">1 Day</option>
            </select>            
        </div>
		<div id="status" class="status disconnected">Disconnected</div>
		<div id="resType" class="status disconnected">-</div>
    </div>
    <div class="row">
        <div class="column-a">
            <div>A - Div 1</div>
            <div>A - Div 2</div>
        </div>
        <div  class="column-b">
            <div id='chart-container'>B</div>
        </div>
    </div>
<textarea id="candle-data" placeholder="Paste your candle data here in JSON format..."></textarea>


<!-- Step 2 --> 
    <script   src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
	<script src="chartObj.js"></script>
	<script src="derivFetch.js"></script>


    <script>                
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            const fetcher = new DerivCandlestickFetcher();
			const chart = new CandleStickChartWithEMA('chart-container');			
        });
		

    </script>

</body>
</html>