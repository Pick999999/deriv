<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Rise/Fall Trading</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f5f7fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #2e8836;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background-color: #236b29;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
        .log-container {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 4px;
            max-height: 250px;
            overflow-y: auto;
        }
        .status-label {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 15px;
            display: inline-block;
        }
        .authorized {
            background-color: #d4edda;
            color: #155724;
        }
        .unauthorized {
            background-color: #f8d7da;
            color: #721c24;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv Rise/Fall Trading</h1>
        
        <div id="auth-status" class="status-label unauthorized">Not Authorized</div>
        
        <div class="form-group">
            <label for="api-token">API Token:</label>
            <input type="text" id="api-token" 
			placeholder="Enter your Deriv API token" required value='lt5UMO6bNvmZQaR'>
        </div>

        <div class="form-group">
            <label for="app-id">App ID:</label>
            <input type="text" id="app-id" placeholder="Enter your Deriv App ID" value="66726">
        </div>
        
        <div class="form-group">
            <label for="symbol">Symbol:</label>
            <select id="symbol">
                <option value="R_10">Volatility 10 (1s) Index</option>
                <option value="R_25">Volatility 25 Index</option>
                <option value="R_50">Volatility 50 Index</option>
                <option value="R_75">Volatility 75 Index</option>
                <option value="R_100">Volatility 100 Index</option>
                <option value="BOOM1000">Boom 1000 Index</option>
                <option value="CRASH1000">Crash 1000 Index</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="contract-type">Contract Type:</label>
            <select id="contract-type">
                <option value="CALL">Rise</option>
                <option value="PUT">Fall</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="duration">Duration (in ticks):</label>
            <input type="number" id="duration" min="1" value="5">
        </div>
        
        <div class="form-group">
            <label for="stake">Stake Amount:</label>
            <input type="number" id="stake" min="1" value="10">
        </div>
        
        <button id="authorize-btn">Authorize</button>
        <button id="buy-btn" disabled>Buy Contract</button>
        <button id="clear-log-btn">Clear Log</button>
        
        <div id="loading" class="loading">
            Processing... Please wait.
        </div>
        
        <div id="result" class="result"></div>
        
        <div class="log-container">
            <h3>API Communication Log</h3>
            <div id="log"></div>
        </div>
    </div>

    <script>
        // DOM Elements
        const apiTokenInput = document.getElementById('api-token');
        const appIdInput = document.getElementById('app-id');
        const symbolSelect = document.getElementById('symbol');
        const contractTypeSelect = document.getElementById('contract-type');
        const durationInput = document.getElementById('duration');
        const stakeInput = document.getElementById('stake');
        const authorizeBtn = document.getElementById('authorize-btn');
        const buyBtn = document.getElementById('buy-btn');
        const clearLogBtn = document.getElementById('clear-log-btn');
        const loadingDiv = document.getElementById('loading');
        const resultDiv = document.getElementById('result');
        const logDiv = document.getElementById('log');
        const authStatusDiv = document.getElementById('auth-status');
        
        // WebSocket connection
        let ws = null;
        let authorized = false;
        let token = "";
        let appId = "66726";
        
        // Log messages function
        function logMessage(direction, message) {
            const timestamp = new Date().toLocaleTimeString();
            const directionIcon = direction === 'sent' ? "→ Sent" : "← Received";
            
            // Format message as JSON string with indentation
            let messageStr;
            if (typeof message === 'object') {
                try {
                    messageStr = JSON.stringify(message, null, 2);
                    // Wrap in pre tag for proper formatting
                    messageStr = `<pre>${messageStr}</pre>`;
                } catch (e) {
                    messageStr = String(message);
                }
            } else {
                messageStr = String(message);
            }
            
            logDiv.innerHTML += `<p><strong>${timestamp} ${directionIcon}:</strong> ${messageStr}</p>`;
            logDiv.scrollTop = logDiv.scrollHeight;
        }
        
        // Clear log
        clearLogBtn.addEventListener('click', function() {
            logDiv.innerHTML = "";
        });
        
        // Establish WebSocket connection
        function connectWebSocket() {
            return new Promise((resolve, reject) => {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    resolve(ws);
                    return;
                }
                
                // Close existing connection if any
                if (ws) {
                    ws.close();
                }
                
                appId = appIdInput.value.trim() || "1089";
                ws = new WebSocket(`wss://ws.binaryws.com/websockets/v3?app_id=${appId}`);
                
                ws.onopen = function() {
                    logMessage('sent', "WebSocket connection established");
                    resolve(ws);
                };
                
                ws.onerror = function(error) {
                    logMessage('received', `WebSocket Error: ${error.message || 'Unknown error'}`);
                    reject(error);
                };
                
                ws.onclose = function() {
                    logMessage('received', "WebSocket connection closed");
                    authorized = false;
                    authStatusDiv.textContent = "Not Authorized";
                    authStatusDiv.classList.remove("authorized");
                    authStatusDiv.classList.add("unauthorized");
                    buyBtn.disabled = true;
                };
                
                ws.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        logMessage('received', data);
                        
                        // Handle WebSocket messages
                        handleWebSocketMessage(data);
                    } catch (error) {
                        logMessage('received', `Error parsing response: ${error.message}`);
                    }
                };
            });
        }
        
        // Send WebSocket message
        async function sendRequest(request) {
            try {
                const socket = await connectWebSocket();
                
                // Add request_id to track response
                request.req_id = Date.now();
                
                logMessage('sent', request);
                socket.send(JSON.stringify(request));
                
                // Return a promise that resolves when we get a response with matching req_id
                return new Promise((resolve) => {
                    const messageHandler = function(event) {
                        const response = JSON.parse(event.data);
                        if (response.req_id === request.req_id) {
                            socket.removeEventListener('message', messageHandler);
                            resolve(response);
                        }
                    };
                    
                    socket.addEventListener('message', messageHandler);
                });
            } catch (error) {
                logMessage('received', `Error sending request: ${error.message}`);
                showResult(`Error: ${error.message}`, "error");
                return null;
            }
        }
        
        // Handle WebSocket messages
        function handleWebSocketMessage(data) {
            // Handle different message types
            if (data.error) {
                showResult(`Error: ${data.error.message || 'Unknown error'}`, "error");
                return;
            }
            
            if (data.msg_type === "authorize") {
                if (data.authorize) {
                    authorized = true;
                    authStatusDiv.textContent = "Authorized";
                    authStatusDiv.classList.remove("unauthorized");
                    authStatusDiv.classList.add("authorized");
                    buyBtn.disabled = false;
                    
                    showResult(`Successfully authorized. Balance: ${data.authorize.balance} ${data.authorize.currency}`, "success");
                } else {
                    authorized = false;
                    authStatusDiv.textContent = "Authorization Failed";
                    authStatusDiv.classList.add("unauthorized");
                    authStatusDiv.classList.remove("authorized");
                    buyBtn.disabled = true;
                    
                    showResult("Authorization failed", "error");
                }
            }
            
            if (data.msg_type === "buy") {
                if (data.buy) {
                    const contractId = data.buy.contract_id;
                    const buyPrice = data.buy.buy_price;
                    
                    showResult(`Contract purchased. Contract ID: ${contractId}, Price: ${buyPrice}`, "success");
                    
                    // Start polling for contract status
                    pollContractStatus(contractId);
                } else {
                    loadingDiv.style.display = "none";
                    showResult("Contract purchase failed", "error");
                }
            }
            
            if (data.msg_type === "proposal_open_contract") {
                const contract = data.proposal_open_contract;
                
                if (contract.status === "open") {
                    loadingDiv.textContent = `Contract in progress... Current profit: ${contract.profit}`;
                } else if (contract.status === "sold") {
                    loadingDiv.style.display = "none";
                    
                    const profit = parseFloat(contract.profit);
                    const isWin = profit >= 0;
                    
                    const resultMessage = `
                        <h3>${isWin ? 'WIN!' : 'LOSS!'}</h3>
                        <p>Contract ID: ${contract.contract_id}</p>
                        <p>Symbol: ${contract.display_name}</p>
                        <p>Entry Spot: ${contract.entry_tick}</p>
                        <p>Exit Spot: ${contract.exit_tick}</p>
                        <p>Buy Price: ${contract.buy_price}</p>
                        <p>Payout: ${contract.payout}</p>
                        <p>Profit: ${profit}</p>
                    `;
                    
                    showResult(resultMessage, isWin ? "success" : "error");
                }
            }
        }
        
        // Authorize with the API token
        authorizeBtn.addEventListener('click', async function() {
            token = apiTokenInput.value.trim();
            
            if (!token) {
                showResult("Please enter your API token", "error");
                return;
            }
            
            // Clear previous results
            resultDiv.style.display = "none";
            loadingDiv.style.display = "block";
            loadingDiv.textContent = "Authorizing...";
            
            try {
                await connectWebSocket();
                
                const authorizeRequest = {
                    authorize: token
                };
                
                await sendRequest(authorizeRequest);
                
            } catch (error) {
                loadingDiv.style.display = "none";
                showResult(`Error: ${error.message}`, "error");
            }
        });
        
        // Buy contract
        buyBtn.addEventListener('click', async function() {
            if (!authorized) {
                showResult("Please authorize with Deriv API first", "error");
                return;
            }
            
            const symbol = symbolSelect.value;
            const contractType = contractTypeSelect.value;
            const duration = parseInt(durationInput.value);
            const stake = parseFloat(stakeInput.value);
            
            if (isNaN(duration) || duration <= 0) {
                showResult("Please enter a valid duration", "error");
                return;
            }
            
            if (isNaN(stake) || stake <= 0) {
                showResult("Please enter a valid stake amount", "error");
                return;
            }
            
            // Clear previous results
            resultDiv.style.display = "none";
            loadingDiv.style.display = "block";
            loadingDiv.textContent = "Getting proposal...";
            
            try {
                // Get a price proposal
                const proposalRequest = {
                    proposal: 1,
                    amount: stake,
                    basis: "stake",
                    contract_type: contractType,
                    currency: "USD",
                    duration: duration,
                    duration_unit: "t",
                    symbol: symbol
                };
                
                const proposalResponse = await sendRequest(proposalRequest);
                
                if (!proposalResponse || !proposalResponse.proposal) {
                    loadingDiv.style.display = "none";
                    if (proposalResponse && proposalResponse.error) {
                        showResult(`Proposal Error: ${proposalResponse.error.message}`, "error");
                    } else {
                        showResult("Failed to get contract proposal", "error");
                    }
                    return;
                }
                
                // Buy the contract with the proposal ID
                loadingDiv.textContent = "Buying contract...";
                
                const buyRequest = {
                    buy: proposalResponse.proposal.id,
                    price: stake
                };
                
                const contractResponse = await sendRequest(buyRequest);
				// ตรวจสอบผลลัพธ์
if (contractResponse && contractResponse.proposal_open_contract) {
    const contract = contractResponse.proposal_open_contract;
    
    // ตรวจสอบว่า contract สิ้นสุดแล้วหรือไม่
    if (contract.is_expired || contract.is_sold) {
		alert(contract.profit);
        // ตรวจสอบว่าชนะหรือแพ้
        if (contract.profit >= 0) {
            showResult(`ชนะ! กำไร: $${contract.profit}`, "success");
        } else {
            showResult(`แพ้! ขาดทุน: $${contract.profit}`, "error");
        }
    } else {
        // contract ยังไม่สิ้นสุด ต้องติดตามต่อไป
        showResult("การเทรดยังไม่สิ้นสุด กำลังติดตามสถานะ...", "info");
    }
} else {
    showResult("ไม่สามารถรับข้อมูล contract ได้", "error");
}
                
            } catch (error) {
                loadingDiv.style.display = "none";
                showResult(`Error: ${error.message}`, "error");
            }
        });
        
        // Poll contract status
        function pollContractStatus(contractId) {
            let checkInterval = setInterval(async function() {
                try {
                    const contractStatusRequest = {
                        proposal_open_contract: 1,
                        contract_id: contractId
                    };
                    
                    const contractResponse = await sendRequest(contractStatusRequest);
                    
                    if (!contractResponse || !contractResponse.proposal_open_contract) {
                        clearInterval(checkInterval);
                        loadingDiv.style.display = "none";
                        if (contractResponse && contractResponse.error) {
                            showResult(`Contract Status Error: ${contractResponse.error.message}`, "error");
                        } else {
                            showResult("Failed to check contract status", "error");
                        }
                        return;
                    }
                    
                    const contract = contractResponse.proposal_open_contract;
                    
                    // If contract is sold/completed, stop polling
                    if (contract.status !== "open") {
                        clearInterval(checkInterval);
                    }
                } catch (error) {
                    clearInterval(checkInterval);
                    loadingDiv.style.display = "none";
                    showResult(`Error polling contract: ${error.message}`, "error");
                }
            }, 2000); // Check every 2 seconds
        }
        
        // Show result function
        function showResult(message, type) {
            loadingDiv.style.display = "none";
            resultDiv.innerHTML = message;
            resultDiv.className = "result " + type;
            resultDiv.style.display = "block";
        }
    </script>
</body>
</html>