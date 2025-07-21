<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Trading Demo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .button-container, .trade-form {
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            margin-right: 10px;
            cursor: pointer;
        }
        #result {
            border: 1px solid #ccc;
            padding: 15px;
            min-height: 100px;
            white-space: pre-wrap;
            margin-bottom: 20px;
        }
        .trade-form input, .trade-form select {
            margin: 5px;
            padding: 5px;
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
            background-color: #f4f4f4;
        }
        .profit { color: green; }
        .loss { color: red; }
    </style>
</head>
<body>
    <div class="button-container">
        <button onclick="getAssets()">A: Get Assets</button>
        <button onclick="getBalance()">B: Get Balance</button>
    </div>

    <div class="trade-form">
        <h3>Place Trade</h3>
        <select id="symbol">
            <option value="frxEURUSD">EUR/USD</option>
            <option value="frxGBPUSD">GBP/USD</option>
            <option value="frxUSDJPY">USD/JPY</option>
        </select>
        <select id="contractType">
            <option value="CALL">Buy (Call)</option>
            <option value="PUT">Sell (Put)</option>
        </select>
        <input type="number" id="duration" placeholder="Duration (minutes)" value="1">
        <input type="number" id="amount" placeholder="Amount" value="10">
        <button onclick="placeTrade()">Place Trade</button>
    </div>

    <div id="result"></div>
	<!-- เพิ่ม div สำหรับแสดง error ในไฟล์ index.html -->
<div class="trade-form">
    <!-- ... (ส่วนอื่นๆ คงเดิม) ... -->
    <button onclick="placeTrade()">Place Trade</button>
    <div id="error-message" style="color: red; margin-top: 10px;"></div>
</div>


    <table id="tradesTable">
        <thead>
            <tr>
                <th>Contract ID</th>
                <th>Symbol</th>
                <th>Buy Price</th>
                <th>Current Price</th>
                <th>Profit/Loss</th>
                <th>Status</th>
                <th>Last Update</th>
            </tr>
        </thead>
        <tbody id="tradesTableBody">
        </tbody>
    </table>

    <script type="module" src="main.js"></script>
</body>
</html>