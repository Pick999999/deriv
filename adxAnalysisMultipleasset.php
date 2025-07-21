<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Trend Analysis</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin: 0;
        }
        
        .last-update {
            font-size: 14px;
            color: #666;
        }
        
        .controls {
            margin-bottom: 20px;
        }
        
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-right: 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        
        button:hover {
            background-color: #45a049;
        }
        
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .asset-card {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        
        .asset-card:hover {
            transform: translateY(-5px);
        }
        
        .asset-card h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 18px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        
        .asset-card p {
            margin: 8px 0;
            font-size: 14px;
        }
        
        .indicator {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .indicator-label {
            font-weight: bold;
        }
        
        .up {
            color: #4caf50;
        }
        
        .down {
            color: #f44336;
        }
        
        .sideway {
            color: #888;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #666;
        }
        
        .chart-container {
            height: 100px;
            margin-top: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }

        .adx-trend {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .weak {
            background-color: #fff9c4;
        }

        .medium {
            background-color: #ffecb3;
        }

        .strong {
            background-color: #ffccbc;
        }

        .very-strong {
            background-color: #ffcdd2;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Deriv Trend Analysis</h1>
        <div class="last-update" id="last-update">Last update: -</div>
    </div>
    
    <div class="controls">
        <button id="refresh-btn">Refresh Data</button>
        <button id="clear-btn">Clear Saved Data</button>
    </div>
    
    <div id="results-container" class="grid-container">
        <div class="loading">กำลังโหลดและวิเคราะห์ข้อมูล...</div>
    </div>

    <script src="sequence_AnalyADX.js"></script>
</body>
</html>