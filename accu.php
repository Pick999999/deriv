<!DOCTYPE html>
<html>
<!-- ... (ส่วน head และ style คงเดิม) ... -->
<head>
    <title>Deriv Accumulators Trading</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 200px; padding: 5px; }
        button { padding: 10px 20px; background: #2196F3; color: white; border: none; cursor: pointer; }
        button:disabled { background: #ccc; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        #status { margin: 10px 0; padding: 10px; }
        .success { color: green; }
        .error { color: red; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <!-- ... (ส่วน HTML คงเดิม) ... -->
    <h2>Deriv Accumulators Trading</h2>

    <div id="loginSection">
        <div class="form-group">
            <label for="apiToken">API Token:</label>
            <input type="password" id="apiToken" value='lt5UMO6bNvmZQaR' required>
            <p style="font-size: 0.8em; color: #666;">
                (สร้าง Token ได้ที่ <a href="https://app.deriv.com/account/api-token" target="_blank">Deriv API Token</a>)
            </p>
        </div>
        <button id="loginButton">Login</button>
    </div>

    <div id="tradingSection" class="hidden">
        <div class="form-group">
            <label for="stake">ทุนทรัพย์ (USD):</label>
            <input type="number" id="stake" min="1" value=1000 required>
        </div>
        
        <div class="form-group">
            <label for="growth">อัตราการเติบโต:</label>
            <select id="growth">
                <option value="0.01">1%</option>
                <option value="0.02">2%</option>
                <option value="0.03">3%</option>
                <option value="0.04">4%</option>
                <option value="0.05">5%</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="takeProfit">ตัวปิดเทรดเอากำไร (USD):</label>
            <input type="number" id="takeProfit" min="0.01" step="0.01" value=10 required>
        </div>
        
        <div class="form-group">
            <label for="rounds">จำนวนรอบการเทรด:</label>
            <input type="number" id="rounds" min="1" required value=3>
        </div>
        
        <button id="startTrading">Start Trading</button>
        <button id="logout">Logout</button>
    </div>
    
    <div id="status"></div>
    
    <div id="reports" class="hidden">
        <h3>รายงานการเทรด</h3>
        <div id="reportTable"></div>
        
        <h3>JSON Report</h3>
        <pre id="jsonReport"></pre>
    </div>

<script>
let ws;
let isTrading = false;
let currentRound = 0;
let tradeReport = [];
let currentContract = null;

// ... (ส่วน initializeWebSocket, login, logout คงเดิม) ...
function initializeWebSocket(token) {
    ws = new WebSocket(`wss://ws.binaryws.com/websockets/v3?app_id=66726&l=${token}`);
    
    ws.onopen = () => {
        showStatus('เชื่อมต่อกับ Deriv สำเร็จ', 'success');
        ws.send(JSON.stringify({ authorize: token }));
    };
    
    ws.onmessage = (msg) => {
        const response = JSON.parse(msg.data);
        console.log('Received:', response);
        
        if (response.msg_type === 'authorize') {
            if (response.error) {
                showStatus('การล็อกอินไม่สำเร็จ: ' + response.error.message, 'error');
                document.getElementById('loginSection').classList.remove('hidden');
                document.getElementById('tradingSection').classList.add('hidden');
                document.getElementById('reports').classList.add('hidden');
            } else {
                showStatus('ล็อกอินสำเร็จ - ยินดีต้อนรับ ' + response.authorize.email, 'success');
                document.getElementById('loginSection').classList.add('hidden');
                document.getElementById('tradingSection').classList.remove('hidden');
                document.getElementById('reports').classList.remove('hidden');
            }
        }
    };
    
    ws.onerror = () => showStatus('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
    ws.onclose = () => {
        showStatus('การเชื่อมต่อถูกปิด', 'error');
        document.getElementById('loginSection').classList.remove('hidden');
        document.getElementById('tradingSection').classList.add('hidden');
        document.getElementById('reports').classList.add('hidden');
    };
}

function login() {
    const token = document.getElementById('apiToken').value;
    if (!token) {
        showStatus('กรุณากรอก API Token', 'error');
        return;
    }
    
    initializeWebSocket(token);
}

function logout() {
    if (ws) {
        ws.close();
    }
    document.getElementById('apiToken').value = '';
    document.getElementById('loginSection').classList.remove('hidden');
    document.getElementById('tradingSection').classList.add('hidden');
    document.getElementById('reports').classList.add('hidden');
    showStatus('ออกจากระบบสำเร็จ');
}

function checkActiveContracts() {
    return new Promise((resolve, reject) => {
        const request = {
            proposal_open_contract: 1,
            contract_id: currentContract,
            subscribe: 0
        };
        
        ws.send(JSON.stringify(request));
        
        function handleMessage(msg) {
            const response = JSON.parse(msg.data);
            console.log('Check contract status:', response);
            
            if (response.error) {
                resolve(false); // ถ้ามี error แสดงว่าไม่มี contract ที่เปิดอยู่
                return;
            }
            
            if (response.msg_type === 'proposal_open_contract') {
                const contract = response.proposal_open_contract;
                const isActive = !(contract.status === 'sold' || contract.is_expired || contract.is_settleable);
                resolve(!isActive);
            }
        }
        
        ws.addEventListener('message', handleMessage, { once: true });
        
        // Set timeout for the check
        setTimeout(() => resolve(true), 5000);
    });
}
async function startTrading() {
    if (isTrading) return;
    
    const stake = parseFloat(document.getElementById('stake').value);
    const growth = parseFloat(document.getElementById('growth').value);
    const takeProfit = parseFloat(document.getElementById('takeProfit').value);
    const rounds = parseInt(document.getElementById('rounds').value);
    
    if (!stake || !takeProfit || !rounds) {
        showStatus('กรุณากรอกข้อมูลให้ครบ', 'error');
        return;
    }
    
    isTrading = true;
    currentRound = 0;
    tradeReport = [];
    document.getElementById('startTrading').disabled = true;
    
    for (let i = 0; i < rounds; i++) {
        currentRound = i + 1;
        currentContractFinished = false;
        showStatus(`กำลังเทรดรอบที่ ${currentRound}/${rounds}`);
        
        try {
            await trade(stake, growth, takeProfit);
            // รอให้ contract จบก่อน
            await waitForContractCompletion();
            // รออีก 2 วินาทีก่อนเริ่มรอบถัดไป
            await new Promise(resolve => setTimeout(resolve, 5000));
        } catch (error) {
            showStatus(`เกิดข้อผิดพลาด: ${error.message}`, 'error');
            break;
        }
    }
    
    isTrading = false;
    document.getElementById('startTrading').disabled = false;
    updateReports();
} 



// เพิ่มฟังก์ชันรอให้ contract จบ
function waitForContractCompletion() {
    return new Promise((resolve) => {
        const checkInterval = setInterval(() => {
            if (currentContractFinished) {
                clearInterval(checkInterval);
                resolve();
            }
        }, 1000);
    });
}

function trade(stake, growth, takeProfit) {
    return new Promise((resolve, reject) => {
        const request = {
            buy: 1,
            price: stake,
            parameters: {
                contract_type: "ACCU",
                symbol: "R_100",
                currency: "USD",
                amount: stake,
                basis: "stake",
                growth_rate: growth
            }
        };
        
        console.log('Sending request:', request);
        ws.send(JSON.stringify(request));
        
        const timeoutId = setTimeout(() => {
            reject(new Error('Timeout'));
        }, 30000);
        
        function handleMessage(msg) {
            const response = JSON.parse(msg.data);
            console.log('Trade response:', response);
            
            if (response.error) {
                clearTimeout(timeoutId);
                reject(new Error(response.error.message));
                return;
            }
            
            if (response.msg_type === 'buy') {
                const trade = {
                    round: currentRound,
                    stake: stake,
                    growth: growth,
                    targetProfit: takeProfit,
                    contractId: response.buy.contract_id,
                    buyPrice: response.buy.buy_price,
                    timestamp: new Date().toISOString()
                };
                
                tradeReport.push(trade);
                clearTimeout(timeoutId);
                resolve(trade);
                subscribeToContract(response.buy.contract_id, takeProfit);
            }
        }
        
        ws.addEventListener('message', handleMessage, { once: true });
    });
}

function subscribeToContract(contractId, targetProfit) {
    const request = {
        proposal_open_contract: 1,
        contract_id: contractId,
        subscribe: 1
    };
    
    ws.send(JSON.stringify(request));
    
    function handleUpdate(msg) {
        const response = JSON.parse(msg.data);
        console.log('Contract update:', response);
        
        if (response.msg_type === 'proposal_open_contract') {
            const contract = response.proposal_open_contract;
            
            if (contract.profit >= targetProfit || contract.is_expired || contract.status === 'sold') {
                currentContractFinished = true;  // เพิ่มบรรทัดนี้
                
                const forgetRequest = {
                    forget_all: ['proposal_open_contract']
                };
                ws.send(JSON.stringify(forgetRequest));
                
                const tradeIndex = tradeReport.findIndex(t => t.contractId === contractId);
                if (tradeIndex !== -1) {
                    tradeReport[tradeIndex].finalProfit = contract.profit;
                    updateReports();
                }
                
                ws.removeEventListener('message', handleUpdate);
            }
        }
    }
    
    ws.addEventListener('message', handleUpdate);
}

// ... (ส่วน showStatus และ updateReports คงเดิม) ...
function showStatus(message, type = '') {
    const statusDiv = document.getElementById('status');
    statusDiv.textContent = message;
    statusDiv.className = type;
}

function updateReports() {
    let tableHTML = `
        <table>
            <thead>
                <tr>
                    <th>รอบ</th>
                    <th>ทุน</th>
                    <th>อัตราเติบโต</th>
                    <th>Take Profit</th>
                    <th>Contract ID</th>
                    <th>ราคาซื้อ</th>
                    <th>เวลา</th>
                    <th>กำไร</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    tradeReport.forEach(trade => {
        tableHTML += `
            <tr>
                <td>${trade.round}</td>
                <td>${trade.stake}</td>
                <td>${trade.growth}%</td>
                <td>${trade.targetProfit}</td>
                <td>${trade.contractId}</td>
                <td>${trade.buyPrice}</td>
                <td>${new Date(trade.timestamp).toLocaleString()}</td>
                <td>${trade.finalProfit !== undefined ? trade.finalProfit : 'รอผล'}</td>
            </tr>
        `;
    });
    
    tableHTML += '</tbody></table>';
    document.getElementById('reportTable').innerHTML = tableHTML;
    document.getElementById('jsonReport').textContent = JSON.stringify(tradeReport, null, 2);
}

// Event Listeners
document.getElementById('loginButton').addEventListener('click', login);
document.getElementById('logout').addEventListener('click', logout);
document.getElementById('startTrading').addEventListener('click', startTrading);
</script>
</body>
</html>