<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Latest Candle Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2a3052;
        }
        .container {
            margin-top: 20px;
        }
        #candleData {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        #candleData th, #candleData td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        #candleData th {
            padding-top: 12px;
            padding-bottom: 12px;
            background-color: #2a3052;
            color: white;
        }
        #candleData tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .controls {
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        select, button, input {
            padding: 8px 12px;
            font-size: 14px;
        }
        button {
            background-color: #2a3052;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #1e2237;
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2a3052;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: none;
            margin-left: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .success {
            color: green;
            margin-top: 10px;
        }
        .status {
            color: #2a3052;
            margin-top: 10px;
            font-weight: bold;
        }
        .toggle-container {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: #2a3052;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        .toggle-label {
            margin-left: 10px;
        }
        #nextUpdateTime {
            margin-left: 10px;
            color: #666;
        }
        .auth-container {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .auth-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .auth-title {
            font-weight: bold;
            color: #2a3052;
        }
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .auth-form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .auth-form input {
            flex: 1;
            min-width: 200px;
        }
        .auth-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #ccc;
        }
        .status-indicator.connected {
            background-color: #4CAF50;
        }
        .auth-details {
            margin-top: 10px;
            font-size: 14px;
        }
        .auth-details span {
            font-weight: bold;
        }
        .badge {
            background-color: #2a3052;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            margin-left: 5px;
        }
        .collapsible {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.2s ease-out;
        }
        .expanded {
            max-height: 500px;
        }
        .tab-container {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
        }
        .tab {
            padding: 8px 16px;
            cursor: pointer;
            border: 1px solid transparent;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
            margin-right: 5px;
        }
        .tab.active {
            background-color: #f5f5f5;
            border-color: #ddd;
            font-weight: bold;
        }
    </style>
</script>

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous">
	
</script>
</head>
<body>
    <h1>Deriv Latest Candle Data</h1>
    
    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-title">Deriv Authentication</div>
            <div class="auth-status">
                <div id="authStatusIndicator" class="status-indicator"></div>
                <span id="authStatusText">Not Connected</span>
            </div>
        </div>
        
        <div class="tab-container">
            <div class="tab active" data-tab="token">API Token</div>
            <div class="tab" data-tab="oauth">OAuth</div>
        </div>
        
        <div id="tokenAuth" class="auth-form">
            <div class="auth-form-row">
                <input type="text" id="apiToken" value='lt5UMO6bNvmZQaR' placeholder="Enter your Deriv API Token">
                <button id="connectToken">Connect</button>
            </div>
            <div class="error" id="tokenError"></div>
            <div class="success" id="tokenSuccess"></div>
            <small>Don't have a token? <a href="https://app.deriv.com/account/api-token" target="_blank">Create one here</a> (require Read permission).</small>
        </div>
        
        <div id="oauthAuth" class="auth-form collapsible">
            <button id="oauthLogin">Login with Deriv</button>
            <div class="error" id="oauthError"></div>
            <div class="success" id="oauthSuccess"></div>
        </div>
        
        <div id="authDetails" class="auth-details collapsible" style='display:none'>
            <div>Account ID: <span id="accountId"></span></div>
            <div>Balance: <span id="accountBalance"></span></div>
            <div>Email: <span id="accountEmail"></span></div>
            <div>
                <button id="logout">Logout</button>
            </div>
        </div>
    </div>

	<div class="status" id="connection-status">Connecting to Deriv.com server...</div>
        <div class="time-display" id="server-time">--:--:--</div>
        <div class="input-group">
            <label for="countdown-input">Countdown (seconds):</label>
            <input type="number" id="countdown-input" min="1" max="3600" value="60">
			<!-- 
            <button id="start-countdown">Start Countdown</button>
			 -->
        </div>
        <div class="countdown" id="countdown-display">--</div>
        
    
    <div class="controls">
        <div>
            <label for="symbol">Symbol:</label>
            <select id="symbol">
                <option value="R_100">Volatility 100 Index</option>
                <option value="R_50">Volatility 50 Index</option>
                <option value="R_25">Volatility 25 Index</option>
                <option value="R_10">Volatility 10 Index</option>
                <option value="frxEURUSD">EUR/USD</option>
                <option value="frxGBPUSD">GBP/USD</option>
                <option value="frxUSDJPY">USD/JPY</option>
                <option value="frxAUDUSD">AUD/USD</option>
            </select>
        </div>
        
        <div>
            <label for="granularity">Timeframe:</label>
            <select id="granularity">
                <option value="60">1 minute</option>
                <option value="300">5 minutes</option>
                <option value="900">15 minutes</option>
                <option value="1800">30 minutes</option>
                <option value="3600">1 hour</option>
                <option value="14400">4 hours</option>
                <option value="86400">1 day</option>
            </select>
        </div>
        
        <div>
            <label for="count">Count:</label>
            <select id="count">
                <option value="5">5</option>
                <option value="10" >10</option>
                <option value="20">20</option>
				<option value="30" >30</option>
                <option value="60" selected>60</option>
                <option value="100">100</option>
            </select>
        </div>
        
        <button id="fetch">Fetch Data</button>
		<button id="btnTradeCall" >Trade Call</button>
		<button id="btnTradePut" >Trade Put</button>

        <div class="loader" id="loader"></div>
        
        <div class="toggle-container">
            <label class="toggle-switch">
                <input type="checkbox" id="autoRefresh">
                <span class="toggle-slider"></span>
            </label>
            <span class="toggle-label">Auto-refresh (1 min)</span>
            <span id="nextUpdateTime"></span>
        </div>

		<div class="toggle-container">
            <label class="toggle-switch">
                <input type="checkbox" id="autoTrade">
                <span class="toggle-slider"></span>
            </label>
            <span class="toggle-label">Auto-Trade</span>
            <span id="nextTradeTime"></span>
        </div>
		<div id="" class="bordergray flex">
		     Time Remain :: <input type="text" id="timeRemain">
		</div>
    </div>
	<div id="" class="bordergray flex">
	   Signal : <span id='signalSpan' style='color:red;font-weight:bold'></span>  
	</div>
	<div id="" class="bordergray flex">
	   Action  : <span id='actionSpan' style='color:red;font-weight:bold'></span>  
	</div>

	<div id="" class="bordergray flex">
	   Profit : <span id='profitSpan' style='color:red;font-weight:bold'></span>  
	</div>
	<div id="" class="bordergray flex">
	   Win Status : <span id='winStatusSpan' style='color:red;font-weight:bold'></span>  
	</div>
	<div id="" class="bordergray flex">
	   Balance : <input type="number" id="balance" >  
	</div>
    
    <div class="container">
        <div id="errorMessage" class="error"></div>
        <div id="statusMessage" class="status"></div>
		<textarea id="jsonHistory" style='width:100%;height:100px'></textarea>
        <table id="candleData">
            <thead>
                <tr>
                    <th>Epoch</th>
                    <th>Time</th>
                    <th>Open</th>
                    <th>High</th>
                    <th>Low</th>
                    <th>Close</th>
                </tr>
            </thead>
            <tbody id="dataBody">
                <!-- Data will be inserted here -->
            </tbody>
        </table>
    </div>

	

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const fetchBtn = document.getElementById('fetch');
            const symbolSelect = document.getElementById('symbol');
            const granularitySelect = document.getElementById('granularity');
            const countSelect = document.getElementById('count');
            const dataBody = document.getElementById('dataBody');
            const errorMessage = document.getElementById('errorMessage');
            const statusMessage = document.getElementById('statusMessage');
            const loader = document.getElementById('loader');
            const autoRefreshToggle = document.getElementById('autoRefresh');
            const nextUpdateTimeSpan = document.getElementById('nextUpdateTime');
            
            // Auth elements
            const apiTokenInput = document.getElementById('apiToken');
            const connectTokenBtn = document.getElementById('connectToken');
            const oauthLoginBtn = document.getElementById('oauthLogin');
            const logoutBtn = document.getElementById('logout');
            const tokenError = document.getElementById('tokenError');
            const tokenSuccess = document.getElementById('tokenSuccess');
            const oauthError = document.getElementById('oauthError');
            const oauthSuccess = document.getElementById('oauthSuccess');
            const authStatusIndicator = document.getElementById('authStatusIndicator');
            const authStatusText = document.getElementById('authStatusText');
            const authDetails = document.getElementById('authDetails');
            const accountIdSpan = document.getElementById('accountId');
            const accountBalanceSpan = document.getElementById('accountBalance');
            const accountEmailSpan = document.getElementById('accountEmail');
            const tokenAuthDiv = document.getElementById('tokenAuth');
            const oauthAuthDiv = document.getElementById('oauthAuth');

			// DOM Elements

			// สถานะและตัวแปรสำหรับการจัดการเวลา
        let serverTimeOffset = 0; // ความต่างระหว่างเวลาเซิร์ฟเวอร์กับเวลาท้องถิ่น
        
        let countdownInterval; // interval สำหรับการนับถอยหลัง
        let countdownEndTime; // เวลาสิ้นสุดการนับถอยหลัง
        let isCountingDown = true; // สถานะการนับถอยหลัง
		let currentTradeObj = {}
		let listOfTradeObj =  [] 

			const serverTimeElement = document.getElementById('server-time');
			const connectionStatusElement = document.getElementById('connection-status');
			const countdownDisplay = document.getElementById('countdown-display');
			const countdownInput = document.getElementById('countdown-input');
			const startCountdownBtn = document.getElementById('start-countdown');
			const reconnectBtn = document.getElementById('reconnect-btn');

			let   activeTradeIds = [];
            
            // Tab elements
            const tabs = document.querySelectorAll('.tab');
            
            // App state
            let ws = null;
            let refreshTimer = null;
            let nextUpdateTime = null;
            let authToken = null;
            let accountInfo = null;
            
            // Check for stored token
            const storedToken = localStorage.getItem('derivApiToken');
            if (storedToken) {
                apiTokenInput.value = storedToken;
                // Auto connect if token exists
                setTimeout(() => {
                    connectWithToken(storedToken);
                }, 500);
            } 


            

            
            // Initialize WebSocket connection
            function connectWebSocket() {
                return new Promise((resolve, reject) => {
                    if (ws && ws.readyState === WebSocket.OPEN) {
                        resolve(ws);
                        return;
                    }
                    
                    ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
                    ws.onopen = function() {
                        console.log('WebSocket connection established');
						requestServerTime();
                        // If we have an auth token, authorize immediately
                        if (authToken) {
                            authorizeWebSocket(ws, authToken)
                                .then(() => resolve(ws))
                                .catch(error => {
                                    console.error('Authorization failed:', error);
                                    resolve(ws); // Still resolve with the socket, just unauthorized
                                });
                        } else {
                            resolve(ws);
                        }
                    };
                    
                    ws.onerror = function(error) {
                        console.error('WebSocket error:', error);
                        errorMessage.textContent = 'Connection error. Please try again.';
                        reject(error);
                    };
                    
                    ws.onclose = function() {
                        console.log('WebSocket connection closed');
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

                });
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
			document.getElementById("countdown-input").value = 60- seconds ;
			//console.log(seconds)
			autoRefresh = document.getElementById("autoRefresh").checked;
			if (seconds === '00' && autoRefresh == true) {
				startAutoRefresh();
                fetchCandleData(); // Fetch immediately when enabling
			   //fetchCandleData();
			}
            
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

            
            // Authorize WebSocket connection with token
            function authorizeWebSocket(socket, token) {
                return new Promise((resolve, reject) => {
                    if (!socket || socket.readyState !== WebSocket.OPEN) {
                        reject(new Error('WebSocket not connected'));
                        return;
                    }
                    
                    const timeoutId = setTimeout(() => {
                        reject(new Error('Authorization timed out'));
                    }, 10000);
                    
                    const authHandler = function(msg) {
                        const data = JSON.parse(msg.data);
                        
                        if (data.msg_type === 'authorize') {
                            clearTimeout(timeoutId);
                            socket.removeEventListener('message', authHandler);
                            
                            if (data.error) {
                                reject(new Error(data.error.message || 'Authorization failed'));
                            } else {
                                // Store account info
                                accountInfo = data.authorize;
                                updateAuthStatus(true);
                                resolve(data.authorize);
                            }
                        }
                    };
                    
                    socket.addEventListener('message', authHandler);                    
                    socket.send(JSON.stringify({
                        authorize: token
                    }));
                });
            } 
            
            // Connect with API token
            function connectWithToken(token) {
                tokenError.textContent = '';
                tokenSuccess.textContent = '';
                
                if (!token) {
                    tokenError.textContent = 'Please enter an API token';
                    return;
                }
                
                // Show loader
                connectTokenBtn.disabled = true;
                connectTokenBtn.textContent = 'Connecting...';
                
                // First establish a WebSocket connection
                connectWebSocket()
                    .then(socket => {
                        // Then authorize with the token
                        return authorizeWebSocket(socket, token);
                    })
                    .then(authorizeData => {
                        console.log('Authorization successful:', authorizeData);
                        
                        // Store token
                        authToken = token;
                        localStorage.setItem('derivApiToken', token);
                        
                        // Update UI
                        tokenSuccess.textContent = 'Connected successfully!';
                        authDetails.classList.add('expanded');
                        updateAccountInfo(authorizeData);
                    })
                    .catch(error => {
                        console.error('Connection error:', error);
                        tokenError.textContent = `Error: ${error.message}`;
                        updateAuthStatus(false);
                    })
                    .finally(() => {
                        connectTokenBtn.disabled = false;
                        connectTokenBtn.textContent = 'Connect';
                    });
            }
            
            // Update authentication status UI
            function updateAuthStatus(isAuthenticated) {
                if (isAuthenticated) {
                    authStatusIndicator.classList.add('connected');
                    authStatusText.textContent = 'Connected';
                    authDetails.classList.add('expanded');
                } else {
                    authStatusIndicator.classList.remove('connected');
                    authStatusText.textContent = 'Not Connected';
                    authDetails.classList.remove('expanded');
                    accountInfo = null;
                    authToken = null;
                    localStorage.removeItem('derivApiToken');
                }
            } 

			

			// ฟังก์ชันสำหรับส่งคำสั่ง trade rise
			function buyCall(lastEpoch) {

                 
				 symbol = document.getElementById("symbol").value ;
				 
				 duration  = 1;
				 durationUnit  = 'm';
                 amountTrade = 1 ; 
				 direction='CALL';

			     document.getElementById("actionSpan").innerHTML = direction;

				const request = {
					buy: 1,
					price: parseFloat(amountTrade),
					parameters: {
						amount: parseFloat(amountTrade),
						basis: "stake",
						contract_type: direction,
						currency: "USD",
						duration: 1,
						duration_unit: 'm',
						symbol: symbol
					}
				};
    
                ws.send(JSON.stringify(request));
                console.log('Sent buy FALL order:', request);
				signalSpan = document.getElementById("signalSpan").innerHTML ;
				currentTradeObj.curpair = document.getElementById("symbol").value ;
				currentTradeObj.Epoch = lastEpoch ;
				currentTradeObj.signalSpan = signalSpan ;
                currentTradeObj.action = 'CALL' ;
                currentTradeObj.duration = '1m';
				
				
				

			} // end func buy 

			function buyPut(lastEpoch) {

                 
				 symbol = document.getElementById("symbol").value ;
				 
				 duration  = 1;
				 durationUnit  = 'm';
                 amountTrade = 1 ; 
				 direction='PUT';
				symbol = 'R_100'  ; 
				document.getElementById("actionSpan").innerHTML = direction;

				const request = {
					buy: 1,
					price: parseFloat(amountTrade),
					parameters: {
						amount: parseFloat(amountTrade),
						basis: "stake",
						contract_type: direction,
						currency: "USD",
						duration: 1,
						duration_unit: 'm',
						symbol: symbol
					}
				};
    
                ws.send(JSON.stringify(request));
                console.log('Sent buy FALL order:', request);
				signalSpan = document.getElementById("signalSpan").innerHTML ;
				
				currentTradeObj.curpair = document.getElementById("symbol").value ;
				currentTradeObj.Epoch = lastEpoch ;
				currentTradeObj.signalSpan = signalSpan ;
                currentTradeObj.action = 'PUT' ;
                currentTradeObj.duration = '1m';
			} // end func buy 

			async function doAjaxNewTrade(candles) {
			let result ;
			let ajaxurl = 'AjaxNewTrade.php';
			let data = { "Mode": 'newTradewithCutRisk' ,    
			"candles" : candles
			} ;
			data2 = JSON.stringify(data);
			console.log('Data Candles=',candles);
			//alert(document.getElementById("autoTrade").checked) ;
	
			try {
				result = await $.ajax({
					url: ajaxurl,
					type: 'POST',
					data: data2,
				success: function(data, textStatus, jqXHR){
					  console.log(textStatus + ": " + jqXHR.status);
					  // do something with data
					},
					error: function(jqXHR, textStatus, errorThrown){
					  alert(textStatus + ": " + jqXHR.status + " " + errorThrown);	 
					  console.log(textStatus + ": " + jqXHR.status + " " + errorThrown);
					}
				});

				lastIndex = candles.length -1 ;
				lastEpoch = candles[0]['epoch'];
				
				resultAr= result.split('-->');
				document.getElementById("signalSpan").innerHTML = result;
				console.log(resultAr[1]);

				if (parseInt(resultAr[1]) <= 2) {
				 if (resultAr[0] === 'TurnUp') {
					buyCall(lastEpoch);
				 } else {
					buyPut(lastEpoch);
				 }
				} else {
					document.getElementById("actionSpan").innerHTML = 'Idle';
				}
				return result;
			} catch (error) {
				console.error(error);
			}
} // end ajax


			// ฟังก์ชันสำหรับติดตามการเทรด
			function trackTrade(contractId) {
				const request = {
					proposal_open_contract: 1,
					contract_id: contractId,
					subscribe: 1  // ขอ subscribe ข้อมูลเพื่อติดตามการเปลี่ยนแปลง
				};
				
				ws.send(JSON.stringify(request));
				console.log(`Started tracking trade ${contractId}`);
			}
            
            // Update account information UI
            function updateAccountInfo(data) {
                if (!data) return;
                
                accountIdSpan.textContent = data.loginid || 'N/A';
                accountBalanceSpan.textContent = data.balance ? `${data.currency} ${parseFloat(data.balance).toFixed(2)}` : 'N/A';
                accountEmailSpan.textContent = data.email || 'N/A';
				
            }
            
            // Handle OAuth login
            function initiateOAuth() {
                const clientId = '29421'; // Example client ID, you would need to register your app with Deriv
                const redirectUri = encodeURIComponent(window.location.href);
                const scope = encodeURIComponent('read trade admin');
                
                const oauthUrl = `https://oauth.deriv.com/oauth2/authorize?app_id=${clientId}&l=en&redirect_uri=${redirectUri}&scope=${scope}&response_type=token`;
                
                window.location.href = oauthUrl;
            }
            
            // Check for OAuth callback
            function checkOAuthCallback() {
                const hash = window.location.hash;
                if (hash && hash.includes('access_token=')) {
                    const params = new URLSearchParams(hash.substring(1));
                    const token = params.get('access_token');
                    
                    if (token) {
                        // Clear the URL hash to avoid token exposure
                        history.pushState('', document.title, window.location.pathname + window.location.search);                        
                        // Use the token
                        connectWithToken(token);                        
                        oauthSuccess.textContent = 'Logged in successfully via OAuth!';
						
                        return true;
                    }
                }
                return false;
            }
            
            // Logout function
            function logout() {
                updateAuthStatus(false);
                apiTokenInput.value = '';
                tokenSuccess.textContent = '';
                oauthSuccess.textContent = '';
                tokenError.textContent = '';
                oauthError.textContent = '';
                
                // Close existing WebSocket connection to ensure we start fresh
                if (ws) {
                    ws.close();
                    ws = null;
                }
            }

            
            // Send request to get candle data
            async function fetchCandleData() {
                try {
                    // Clear previous errors
                    errorMessage.textContent = '';
                    
                    // Show loader
                    loader.style.display = 'inline-block';
                    
                    // Get selected values
                    const symbol = symbolSelect.value;
                    const granularity = parseInt(granularitySelect.value);
                    const count = parseInt(countSelect.value);
                    
                    // Connect to WebSocket
                    const socket = await connectWebSocket();
                    
                    // Create a promise to handle the response
                    const candleDataPromise = new Promise((resolve, reject) => {
                        const timeoutId = setTimeout(() => {
                            reject(new Error('Request timed out'));
                        }, 10000); // 10 seconds timeout
                        
                        // Handle WebSocket messages
                        socket.onmessage = function(msg) {
                            const data = JSON.parse(msg.data);
							//alert(data.msg_type);
             

// ข้อมูลการอัพเดทสถานะการเทรด
            if (data.msg_type === 'proposal_open_contract') {
                const contract = data.proposal_open_contract;                
				document.getElementById("profitSpan").innerHTML = contract.profit;
                // ถ้าการเทรดจบแล้ว
                if (contract.is_sold === 1) {
					console.log('currentTradeObj',currentTradeObj);
                    console.log(`Trade ${contract.contract_id} finished!`);
                    console.log(`Result: ${contract.profit >= 0 ? 'WIN' : 'LOSS'}`);
                    console.log(`Profit: ${contract.profit}`); 

balance =parseFloat(document.getElementById("balance").value)+parseFloat(contract.profit) ;
                    console.log('Balance=',balance)  ;
                    document.getElementById("balance").value = balance;
                    if (contract.profit > 0) {                    
					  document.getElementById("winStatusSpan").innerHTML += 'WIN-';
					  currentTradeObj.winStatus = 'Win';
                    } else {
                      document.getElementById("winStatusSpan").innerHTML += 'LOSS-';
					  currentTradeObj.winStatus = 'Loss';
					} 
					listOfTradeObj.push(currentTradeObj) ;
					document.getElementById("jsonHistory").value = JSON.stringify(listOfTradeObj);
					currentTradeObj = {}

					
					
                    
                    // ลบ ID ของการเทรดที่จบแล้วออกจากรายการ
                    activeTradeIds = activeTradeIds.filter(id => id !== contract.contract_id);
                }
            } 
			


							if (data.msg_type === 'buy') {
								if (data.error) {
									console.error('Trade error:', data.error);
								} else {
									console.log('Trade successful:', data);
									// เก็บ ID ของการเทรดที่สำเร็จ
									activeTradeIds.push(data.buy.contract_id);
									// ติดตามการเทรดนี้
									trackTrade(data.buy.contract_id);
								}
							}
                            
                            // Check if this is a candle response
                            if (data.msg_type === 'candles') {
                                clearTimeout(timeoutId);
                                resolve(data);
                            } else if (data.error) {
                                clearTimeout(timeoutId);
                                reject(new Error(data.error.message || 'Unknown error'));
                            }
                        }; 


                        
                        // Send the candles request
						symbol2 = document.getElementById("symbol").value ;
                        socket.send(JSON.stringify({
                            ticks_history: symbol2,
                            granularity: granularity,
                            style: 'candles',
                            count: count,
                            end: 'latest'
                        }));
                    });
                    
                    // Wait for the response
                    const response = await candleDataPromise;
                    
                    // Process and display the data
                    displayCandleData(response.candles || []);
                    
                    // Update last fetch time
                    const now = new Date();
                    statusMessage.textContent = `Last updated: ${formatDate(now)}`;
                    
                } catch (error) {
                    console.error('Error fetching candle data:', error);
                    errorMessage.textContent = `Error: ${error.message}`;
                } finally {
                    // Hide loader
                    loader.style.display = 'none';
                }
            }
            
            // Display candle data in the table
            function displayCandleData(candles) {
                if (!candles || candles.length === 0) {
                    errorMessage.textContent = 'No data available for the selected criteria.';
                    return;
                } 
				autoTrade = document.getElementById("autoTrade").checked ;
				if (autoTrade=== true) {
				   doAjaxNewTrade(candles);
				}
                
                // Clear existing data
                dataBody.innerHTML = '';
                
                // Sort candles by epoch (newest first)
                candles.sort((a, b) => b.epoch - a.epoch);
                
                // Create table rows
                candles.forEach(candle => {
                    const row = document.createElement('tr');
                    
                    // Format timestamp
                    const date = new Date(candle.epoch * 1000);
                    const formattedDate = formatDate(date);
                    
                    // Create cells
                    row.innerHTML = `
                        <td>${candle.epoch}</td>
                        <td>${formattedDate}</td>
                        <td>${parseFloat(candle.open).toFixed(5)}</td>
                        <td>${parseFloat(candle.high).toFixed(5)}</td>
                        <td>${parseFloat(candle.low).toFixed(5)}</td>
                        <td>${parseFloat(candle.close).toFixed(5)}</td>
                    `;
                    
                    dataBody.appendChild(row);
                });
            }
            
            // Format date to a readable string
            function formatDate(date) {
                return date.toLocaleString();
            }
            
            // Update the next update time display
            function updateNextTimeDisplay() {
                if (!nextUpdateTime) return;
                
                const now = new Date();
                const timeDiff = nextUpdateTime - now;
                
                if (timeDiff <= 0) {
                    nextUpdateTimeSpan.textContent = '(updating...)';
                    return;
                }
                
                const seconds = Math.floor(timeDiff / 1000);
                nextUpdateTimeSpan.textContent = `(next update in ${seconds} seconds)`;
				
				document.getElementById("timeRemain").value =  seconds;
//				console.log('Time Remain=',seconds)
				
				if (parseInt(seconds) === 60 ) {
                   console.log('Start Fetch ')
				   fetchCandleData();
				}
            }
            
            // Start the auto-refresh timer
            function startAutoRefresh() {
                if (refreshTimer) {
                    clearInterval(refreshTimer);
                }
                
                // Set the next update time to 1 minute from now
                nextUpdateTime = new Date();
                nextUpdateTime.setMinutes(nextUpdateTime.getMinutes() + 1);
                nextUpdateTime.setSeconds(0); // Align to the start of the next minute
                
                // Calculate initial delay (time until the next minute starts)
                const now = new Date();
                const initialDelay = nextUpdateTime - now;
                
                // Setup the display timer to update every second
                const displayTimer = setInterval(updateNextTimeDisplay, 1000);
                updateNextTimeDisplay();
                
                // Setup the first fetch after initialDelay
                const timerHandle = setTimeout(() => {
                    // Fetch data
                    fetchCandleData();
                    
                    // Setup recurring timer every minute
                    refreshTimer = setInterval(() => {
                        fetchCandleData();
                        
                        // Update next update time
                        nextUpdateTime = new Date();
                        nextUpdateTime.setMinutes(nextUpdateTime.getMinutes() + 1);
                        nextUpdateTime.setSeconds(0);
                    }, 60000); // 1 minute
                    
                    clearInterval(displayTimer);
                    
                    // Setup the display timer to update every second
                    setInterval(updateNextTimeDisplay, 1000);
                }, initialDelay);
                
                return timerHandle;
            } 

			// ส่งคำขอเวลาปัจจุบันจากเซิร์ฟเวอร์
           function requestServerTime() {
             if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify({ time: 1 }));
				console.log('Send get Time')			
             } else {
				console.log('Send get Time Error')
			 }
            }
            
            // Stop the auto-refresh timer
            function stopAutoRefresh() {
                if (refreshTimer) {
                    clearInterval(refreshTimer);
                    refreshTimer = null;
                }
                nextUpdateTime = null;
                nextUpdateTimeSpan.textContent = '';
            }
            
            // Tab switching functionality
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all auth forms
                    tokenAuthDiv.classList.remove('expanded');
                    oauthAuthDiv.classList.remove('expanded');
                    
                    // Show selected auth form
                    const tabName = this.getAttribute('data-tab');
                    if (tabName === 'token') {
                        tokenAuthDiv.classList.add('expanded');
                        oauthAuthDiv.classList.remove('expanded');
                    } else if (tabName === 'oauth') {
                        tokenAuthDiv.classList.remove('expanded');
                        oauthAuthDiv.classList.add('expanded');
                    }
                });
            });
            
            // Event Listeners
            connectTokenBtn.addEventListener('click', function() {
                connectWithToken(apiTokenInput.value.trim());
            });
            
            oauthLoginBtn.addEventListener('click', initiateOAuth);
            
			btnTradePut = document.getElementById("btnTradePut");
			btnTradePut.addEventListener('click', buyPut) ;

			btnTradeCall = document.getElementById("btnTradeCall");
			btnTradeCall.addEventListener('click', buyCall);

            
            logoutBtn.addEventListener('click', logout);
            
            fetchBtn.addEventListener('click', fetchCandleData);
            
            autoRefreshToggle.addEventListener('change', function() {
                if (this.checked) {
                    //startAutoRefresh();
                    //fetchCandleData(); // Fetch immediately when enabling
                } else {
                    stopAutoRefresh();
                }
            });
            
            // Handle select changes - stop and restart auto refresh if needed
            [symbolSelect, granularitySelect, countSelect].forEach(select => {
                select.addEventListener('change', function() {
                    if (autoRefreshToggle.checked) {
                        stopAutoRefresh();
                        startAutoRefresh();
                        fetchCandleData(); // Fetch immediately when changing parameters
                    }
                });
            });
            
            // Check for OAuth callback on page load
            checkOAuthCallback();
            
            // Fix tab display on load
            document.querySelector('.tab[data-tab="token"]').click();
            
            // Cleanup WebSocket connection when the page is closed
            window.addEventListener('beforeunload', function() {
                stopAutoRefresh();
                if (ws) {
                    ws.close();
                }
            });
        });
    </script>

 

</body>
</html>