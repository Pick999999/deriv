<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv.com Candlestick Data</title>
    <style>

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
    <div sclass="container">
        
        
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
		<button type='button' id='example-data' class='mBtn' >ButtonA</button>
		<button type='button' id='update-chart' class='mBtn' >ButtonB</button>

		<div id="chart-container"></div>
        
        <div id="status" class="status disconnected">Disconnected</div>
		<div id="resType" class="status disconnected">-</div>
        
        <div id="table-container">
            <table id="candlestick-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Open</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Close</th>
                        <th>Volume</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <!-- Data will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>

	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
	<script src="chartObj.js"></script>
	<script src="derivFetch.js"></script>


    <script>                
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            const fetcher = new DerivCandlestickFetcher();
        });

    </script>



<textarea id="candle-data" placeholder="Paste your candle data here in JSON format..."></textarea>


<script>
        

        // Initialize chart when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            const chart = new CandleStickChartWithEMA('chart-container');			
            const textarea = document.getElementById('candle-data');
            
            // Update chart button
            document.getElementById('update-chart').addEventListener('click', () => {
                const candleData = chart.parseCandleData(textarea.value);
                if (candleData) {
                    chart.updateChart(candleData);
                } else {
                    alert('Invalid candle data format. Please check your input.');
                }
            });
            
            // Example data button
            document.getElementById('example-data').addEventListener('click', () => {
                const exampleData = [
                    {"time": 1633046400, "open": 45000, "high": 45500, "low": 44800, "close": 45300},
                    {"time": 1633132800, "open": 45300, "high": 45800, "low": 45100, "close": 45650},
                    {"time": 1633219200, "open": 45650, "high": 46000, "low": 45500, "close": 45800},
                    {"time": 1633305600, "open": 45800, "high": 46200, "low": 45600, "close": 46000},
                    {"time": 1633392000, "open": 46000, "high": 46500, "low": 45800, "close": 46300},
                    {"time": 1633478400, "open": 46300, "high": 46800, "low": 46100, "close": 46650},
                    {"time": 1633564800, "open": 46650, "high": 47000, "low": 46400, "close": 46800},
                    {"time": 1633651200, "open": 46800, "high": 47200, "low": 46600, "close": 47000},
                    {"time": 1633737600, "open": 47000, "high": 47500, "low": 46800, "close": 47300},
                    {"time": 1633824000, "open": 47300, "high": 47800, "low": 47100, "close": 47650},
                    {"time": 1633910400, "open": 47650, "high": 48000, "low": 47400, "close": 47800},
                    {"time": 1633996800, "open": 47800, "high": 48200, "low": 47600, "close": 48000},
                    {"time": 1634083200, "open": 48000, "high": 48500, "low": 47800, "close": 48300},
                    {"time": 1634169600, "open": 48300, "high": 48800, "low": 48100, "close": 48650},
                    {"time": 1634256000, "open": 48650, "high": 49000, "low": 48400, "close": 48800},
                    {"time": 1634342400, "open": 48800, "high": 49200, "low": 48600, "close": 49000},
                    {"time": 1634428800, "open": 49000, "high": 49500, "low": 48800, "close": 49300},
                    {"time": 1634515200, "open": 49300, "high": 49800, "low": 49100, "close": 49650},
                    {"time": 1634601600, "open": 49650, "high": 50000, "low": 49400, "close": 49800},
                    {"time": 1634688000, "open": 49800, "high": 50200, "low": 49600, "close": 50000}
                ];
                
                textarea.value = JSON.stringify(exampleData, null, 2);
                chart.updateChart(exampleData);
				
            });
        });
    </script>
</body>
</html>