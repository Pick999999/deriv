<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Latest Candle Data</title>

   <link href="newTest.css" rel="stylesheet">    


<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous">

	
</script>

<script src="newTest.js"></script>
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
        
        <div id="authDetails" class="auth-details collapsible">
            <div>Account ID: <span id="accountId"></span></div>
            <div>Balance: <span id="accountBalance"></span></div>
            <div>Email: <span id="accountEmail"></span></div>
            <div>
                <button id="logout">Logout</button>
            </div>
        </div>
    </div>
    
    <div class="controls">
        <div>
            <label for="symbol">Symbol:</label>
            <select id="symbol">
                <option value="R_100">Volatility 100 Index</option>
                <option value="R_50">Volatility 50 Index</option>
                <option value="R_25">Volatility 25 Index</option>
                <option value="R_10">Volatility 10 Index</option>
                <option value="EURUSD">EUR/USD</option>
                <option value="GBPUSD">GBP/USD</option>
                <option value="USDJPY">USD/JPY</option>
                <option value="AUDUSD">AUD/USD</option>
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
        <div class="loader" id="loader"></div>
        
        <div class="toggle-container">
            <label class="toggle-switch">
                <input type="checkbox" id="autoRefresh">
                <span class="toggle-slider"></span>
            </label>
            <span class="toggle-label">Auto-refresh (1 min)</span>
            <span id="nextUpdateTime"></span>
        </div>
		<!-- Add this inside the controls div, after the auto-refresh toggle container -->
<div class="toggle-container">
    <label class="toggle-switch">
        <input type="checkbox" id="autoTrade">
        <span class="toggle-slider"></span>
    </label>
    <span class="toggle-label">Auto-Trade</span>
    <span id="nextTradeTime"></span>
</div>

		<div class="toggle-container">
            <label class="toggle-switch">
                <input type="checkbox" id="autoTrade">
                <span class="toggle-slider"></span>
            </label>
            <span class="toggle-label">Auto-Trade</span>
            <span id="nextTradeTime"></span>
        </div>
    </div>
    
    <div class="container">
        <div id="errorMessage" class="error"></div>
        <div id="statusMessage" class="status"></div>
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
// Add this to your existing script at an appropriate location (before the end of the DOMContentLoaded event)

// Elements for trading
const autoTradeToggle = document.getElementById('autoTrade');
const nextTradeTimeSpan = document.getElementById('nextTradeTime');

// Trading state
let tradeTimer = null;
let nextTradeTime = null;
let tradeHistory = [];
let authToken = null ;
// Add trading panel to the page
function addTradingPanel() {
    // Create trading panel container
    const tradingPanel = document.createElement('div');
    tradingPanel.className = 'trading-panel';
    tradingPanel.innerHTML = `
        <h2>Trading Controls</h2>
        <div class="trade-controls">
            <div class="control-group">
                <label for="tradeType">Trade Type:</label>
                <select id="tradeType">
                    <option value="CALL">Rise</option>
                    <option value="PUT">Fall</option>
                </select>
            </div>
            <div class="control-group">
                <label for="duration">Duration:</label>
                <input type="number" id="duration" value="1" min="1" max="60">
                <select id="durationUnit">
                    <option value="m">Minute(s)</option>
                    <option value="t">Tick(s)</option>
                    <option value="h">Hour(s)</option>
                </select>
            </div>
            <div class="control-group">
                <label for="stake">Stake Amount:</label>
                <input type="number" id="stake" value="10" min="1" step="1">
            </div>
            <div class="control-group buttons">
                <button id="tradeRise" class="trade-button rise">Trade Rise</button>
                <button id="tradeFall" class="trade-button fall">Trade Fall</button>
            </div>
        </div>
        <div class="trade-status">
            <div id="tradeMessage" class="status"></div>
            <div id="tradeError" class="error"></div>
        </div>
        <h2>Trade History</h2>
        <table id="tradeHistory">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Type</th>
                    <th>Symbol</th>
                    <th>Duration</th>
                    <th>Stake</th>
                    <th>Entry Price</th>
                    <th>Exit Price</th>
                    <th>Profit/Loss</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="tradeHistoryBody">
                <!-- Trade history will be inserted here -->
            </tbody>
        </table>
    `;
    
    // Add trading panel to the page before the candle data container
    const container = document.querySelector('.container');
    document.body.insertBefore(tradingPanel, container);
    
    // Add styles for trading panel
    const style = document.createElement('style');
    style.textContent = `
        .trading-panel {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .trading-panel h2 {
            color: #2a3052;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .trade-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .control-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .control-group label {
            font-weight: bold;
            color: #444;
        }
        .control-group.buttons {
            margin-left: auto;
        }
        .trade-button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .trade-button.rise {
            background-color: #4CAF50;
            color: white;
        }
        .trade-button.rise:hover {
            background-color: #3e8e41;
        }
        .trade-button.fall {
            background-color: #f44336;
            color: white;
        }
        .trade-button.fall:hover {
            background-color: #d32f2f;
        }
        #tradeHistory {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        #tradeHistory th, #tradeHistory td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        #tradeHistory th {
            padding-top: 12px;
            padding-bottom: 12px;
            background-color: #2a3052;
            color: white;
        }
        #tradeHistory tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .profit {
            color: #4CAF50;
            font-weight: bold;
        }
        .loss {
            color: #f44336;
            font-weight: bold;
        }
        .pending {
            color: #ff9800;
        }
    `;
    document.head.appendChild(style);
    
    // Add event listeners for trade buttons
    document.getElementById('tradeRise').addEventListener('click', () => {
        placeTrade('CALL');
    });
    
    document.getElementById('tradeFall').addEventListener('click', () => {
        placeTrade('PUT');
    });
}

// Place a trade
async function placeTrade(contractType) {

	
    try {
        if (!authToken) {
            showTradeError('Please connect to your Deriv account first');
            return;
        }
        symbolSelect = document.getElementById("symbol").value ;
        const symbol = symbolSelect.value;
        const durationValue = document.getElementById('duration').value;
        const durationUnit = document.getElementById('durationUnit').value;
        const stake = document.getElementById('stake').value;
        
        // Validate inputs
        if (!durationValue || durationValue < 1) {
            showTradeError('Please enter a valid duration');
            return;
        }
        
        if (!stake || stake < 1) {
            showTradeError('Please enter a valid stake amount');
            return;
        }
        
        // Reset status messages
        document.getElementById('tradeError').textContent = '';
        document.getElementById('tradeMessage').textContent = 'Placing trade...';
        
        // Connect to WebSocket
        const socket = await connectWebSocket();
        
        // Create a promise to handle the response
        const tradePromise = new Promise((resolve, reject) => {
            const timeoutId = setTimeout(() => {
                reject(new Error('Trade request timed out'));
            }, 10000); // 10 seconds timeout
            
            const proposalHandler = function(msg) {
                const data = JSON.parse(msg.data);
                
                if (data.msg_type === 'proposal') {
                    if (data.error) {
                        clearTimeout(timeoutId);
                        socket.removeEventListener('message', proposalHandler);
                        reject(new Error(data.error.message || 'Proposal error'));
                        return;
                    }
                    
                    // Got proposal, now buy the contract
                    const proposal = data.proposal;
                    
                    socket.send(JSON.stringify({
                        buy: proposal.id,
                        price: stake
                    }));
                } else if (data.msg_type === 'buy') {
                    clearTimeout(timeoutId);
                    socket.removeEventListener('message', proposalHandler);
                    
                    if (data.error) {
                        reject(new Error(data.error.message || 'Buy error'));
                    } else {
                        resolve(data.buy);
                    }
                }
            };
            
            socket.addEventListener('message', proposalHandler);
            
            // Create proposal request
            const proposal = {
                proposal: 1,
                amount: parseFloat(stake),
                basis: 'stake',
                contract_type: contractType,
                currency: accountInfo?.currency || 'USD',
                symbol: symbol,
                duration_unit: durationUnit
            };
            
            // Add duration based on unit
            proposal.duration = parseInt(durationValue);
            
            socket.send(JSON.stringify(proposal));
        });
        
        // Wait for the response
        const tradeResult = await tradePromise;
        
        // Add to trade history
        const newTrade = {
            id: tradeResult.contract_id,
            time: new Date(),
            type: contractType === 'CALL' ? 'Rise' : 'Fall',
            symbol: symbol,
            duration: `${durationValue} ${durationUnit}`,
            stake: `${tradeResult.currency} ${parseFloat(tradeResult.buy_price).toFixed(2)}`,
            entryPrice: null,
            exitPrice: null,
            profitLoss: null,
            status: 'Pending'
        };
        
        tradeHistory.unshift(newTrade);
        updateTradeHistory();
        
        // Subscribe to contract updates
        subscribeToContract(tradeResult.contract_id);
        
        document.getElementById('tradeMessage').textContent = `Trade placed successfully! Contract ID: ${tradeResult.contract_id}`;
        
    } catch (error) {
        console.error('Trade error:', error);
        showTradeError(`Error: ${error.message}`);
    }
}

// Subscribe to contract updates
function subscribeToContract(contractId) {
    if (!ws || ws.readyState !== WebSocket.OPEN) {
        connectWebSocket().then(socket => {
            sendSubscribeRequest(socket, contractId);
        });
    } else {
        sendSubscribeRequest(ws, contractId);
    }
}

// Send contract subscription request
function sendSubscribeRequest(socket, contractId) {
    socket.send(JSON.stringify({
        proposal_open_contract: 1,
        contract_id: contractId,
        subscribe: 1
    }));
    
    // Add handler for contract updates
    const contractHandler = function(msg) {
        const data = JSON.parse(msg.data);
        
        if (data.msg_type === 'proposal_open_contract') {
            if (data.error) {
                console.error('Contract subscription error:', data.error);
                return;
            }
            
            const contract = data.proposal_open_contract;
            
            // Find the trade in history
            const tradeIndex = tradeHistory.findIndex(t => t.id === contract.contract_id);
            if (tradeIndex !== -1) {
                // Update trade information
                tradeHistory[tradeIndex].entryPrice = parseFloat(contract.entry_spot).toFixed(5);
                
                if (contract.status === 'sold' || contract.status === 'won' || contract.status === 'lost') {
                    // Contract is finished
                    socket.removeEventListener('message', contractHandler);
                    
                    tradeHistory[tradeIndex].exitPrice = parseFloat(contract.exit_tick).toFixed(5);
                    tradeHistory[tradeIndex].profitLoss = `${contract.currency} ${parseFloat(contract.profit).toFixed(2)}`;
                    tradeHistory[tradeIndex].status = contract.status.charAt(0).toUpperCase() + contract.status.slice(1);
                }
                
                updateTradeHistory();
            }
        }
    };
    
    socket.addEventListener('message', contractHandler);
}

// Show trade error
function showTradeError(message) {
    document.getElementById('tradeError').textContent = message;
    document.getElementById('tradeMessage').textContent = '';
}

// Update trade history table
function updateTradeHistory() {
    const tradeHistoryBody = document.getElementById('tradeHistoryBody');
    if (!tradeHistoryBody) return;
    
    tradeHistoryBody.innerHTML = '';
    
    if (tradeHistory.length === 0) {
        const emptyRow = document.createElement('tr');
        emptyRow.innerHTML = '<td colspan="9">No trade history yet</td>';
        tradeHistoryBody.appendChild(emptyRow);
        return;
    }
    
    tradeHistory.forEach(trade => {
        const row = document.createElement('tr');
        
        // Format date
        const date = trade.time;
        const formattedDate = formatDate(date);
        
        // Determine profit/loss class
        let profitLossClass = '';
        if (trade.status !== 'Pending') {
            const profitValue = trade.profitLoss ? parseFloat(trade.profitLoss.split(' ')[1]) : 0;
            profitLossClass = profitValue > 0 ? 'profit' : (profitValue < 0 ? 'loss' : '');
        }
        
        row.innerHTML = `
            <td>${formattedDate}</td>
            <td>${trade.type}</td>
            <td>${trade.symbol}</td>
            <td>${trade.duration}</td>
            <td>${trade.stake}</td>
            <td>${trade.entryPrice || 'Pending'}</td>
            <td>${trade.exitPrice || 'Pending'}</td>
            <td class="${profitLossClass}">${trade.profitLoss || 'Pending'}</td>
            <td class="${trade.status.toLowerCase()}">${trade.status}</td>
        `;
        
        tradeHistoryBody.appendChild(row);
    });
}

// Start auto-trade timer
function startAutoTrade() {
    if (tradeTimer) {
        clearInterval(tradeTimer);
    }
    
    if (!authToken) {
        showTradeError('Please connect to your Deriv account first');
        autoTradeToggle.checked = false;
        return;
    }
    
    // Set the next trade time to 1 minute from now
    nextTradeTime = new Date();
    nextTradeTime.setMinutes(nextTradeTime.getMinutes() + 1);
    nextTradeTime.setSeconds(0); // Align to the start of the next minute
    
    // Calculate initial delay
    const now = new Date();
    const initialDelay = nextTradeTime - now;
    
    // Setup the display timer to update every second
    const displayTimer = setInterval(updateNextTradeTimeDisplay, 1000);
    updateNextTradeTimeDisplay();
    
    // Setup the first trade after initialDelay
    const timerHandle = setTimeout(() => {
        // Execute auto trade
        executeAutoTrade();
        
        // Setup recurring timer every minute
        tradeTimer = setInterval(() => {
            executeAutoTrade();
            
            // Update next trade time
            nextTradeTime = new Date();
            nextTradeTime.setMinutes(nextTradeTime.getMinutes() + 1);
            nextTradeTime.setSeconds(0);
        }, 60000); // 1 minute
        
        clearInterval(displayTimer);
        
        // Setup the display timer to update every second
        setInterval(updateNextTradeTimeDisplay, 1000);
    }, initialDelay);
    
    return timerHandle;
}

// Stop auto-trade timer
function stopAutoTrade() {
    if (tradeTimer) {
        clearInterval(tradeTimer);
        tradeTimer = null;
    }
    nextTradeTime = null;
    nextTradeTimeSpan.textContent = '';
}

// Execute auto trade logic
function executeAutoTrade() {
    // Get selected values
    const contractType = document.getElementById('tradeType').value;
    
    // Place the trade
    placeTrade(contractType);
}

// Update the next trade time display
function updateNextTradeTimeDisplay() {
    if (!nextTradeTime) return;
    
    const now = new Date();
    const timeDiff = nextTradeTime - now;
    
    if (timeDiff <= 0) {
        nextTradeTimeSpan.textContent = '(trading...)';
        return;
    }
    
    const seconds = Math.floor(timeDiff / 1000);
    nextTradeTimeSpan.textContent = `(next trade in ${seconds} seconds)`;
}

// Function to analyze candle data for trading signals (example)
function doAjaxNewTrade(candles) {
    if (!candles || candles.length < 5) return;
    
    // Basic algorithm: If the last 3 candles are going up, predict UP, else DOWN
    const lastCandles = candles.slice(0, 3);
    
    // Simple trend detection
    let upCount = 0;
    let downCount = 0;
    
    for (let i = 0; i < lastCandles.length; i++) {
        if (parseFloat(lastCandles[i].close) > parseFloat(lastCandles[i].open)) {
            upCount++;
        } else if (parseFloat(lastCandles[i].close) < parseFloat(lastCandles[i].open)) {
            downCount++;
        }
    }
    
    // Update the trade type based on prediction
    const tradeTypeSelect = document.getElementById('tradeType');
    if (tradeTypeSelect) {
        if (upCount > downCount) {
            tradeTypeSelect.value = 'CALL'; // Predict UP
        } else {
            tradeTypeSelect.value = 'PUT'; // Predict DOWN
        }
    }
}

// Add event listener for auto-trade toggle
if (autoTradeToggle) {
    autoTradeToggle.addEventListener('change', function() {
        if (this.checked) {
            startAutoTrade();
        } else {
            stopAutoTrade();
        }
    });
}

// Add trading panel to the page
addTradingPanel();




        
    </script>





</body>
</html>