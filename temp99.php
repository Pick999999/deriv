<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Deriv Trading App</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> 

  <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tempus Dominus CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    
    <!-- Font Awesome for calendar icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
<style>
.sarabun-regular {
  font-family: "Sarabun", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.profit-green {
  color: green;
  font-weight: bold;
}

.profit-red {
  color: red;
  font-weight: bold;
}

.contract-table {
  width: 100%;
}

.contract-table th, .contract-table td {
  padding: 8px;
  text-align: center;
}

.contract-table th {
  background-color: #f2f2f2;
}

.trade-controls {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 15px;
}

.action-buttons {
  display: flex;
  gap: 10px;
}

.stake-input {
  max-width: 150px;
}

.take-profit-select {
  max-width: 150px;
}
</style>

 </head>
 <body class='sarabun-regular'>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="asset">เลือก Asset:</label>
                    <select id="asset" name="asset" class="form-select" required>
                        <option value="R_10">Volatility 10 Index</option>
                        <option value="R_25">Volatility 25 Index</option>
                        <option value="R_50">Volatility 50 Index</option>
                        <option value="R_75">Volatility 75 Index</option>
                        <option value="R_100" selected >Volatility 100 Index</option>
                        <option value="BOOM1000">Boom 1000 Index</option>
                        <option value="CRASH1000">Crash 1000 Index</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="btn-group mt-4">
                    <button type='button' id='getLocalBtn' class='btn btn-secondary' onclick="getFromLocal()">Get From Local</button>
                    <button type='button' id='saveLocalBtn' class='btn btn-primary' onclick="savetoLocal()">Save To Local</button>
                    <button type='button' id='refreshChartBtn' class='btn btn-success' onclick="refreshChart()">Refresh Chart</button>
                </div>
            </div>
			

        </div>
		<div id="" class="bordergray flex" style='color:#0080ff;fonnt-weight:bold'>
		   <input type="radio" name="viewType" id="viewType1" checked>&nbsp;Update Candle
			&nbsp;&nbsp;<input type="radio" name="viewType" id="viewType2">&nbsp;Append Candle  
		</div>
        
        <!-- Trading Controls Section -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="trade-controls">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="stake">Stake Amount:</label>
                                <input type="number" id="stake" class="form-control stake-input" value="10" min="1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="duration">Duration (ticks):</label>
                                <input type="number" id="duration" class="form-control stake-input" value="5" min="1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="takeProfit">Take Profit %:</label>
                                <select id="takeProfit" class="form-select take-profit-select">
                                    <option value="0">Manual</option>
                                    <option value="5">5%</option>
                                    <option value="10">10%</option>
                                    <option value="15">15%</option>
                                    <option value="20">20%</option>
                                    <option value="25">25%</option>
                                    <option value="30">30%</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="action-buttons mt-4">
                                <button id="riseButton" class="btn btn-success">Rise</button>
                                <button id="fallButton" class="btn btn-danger">Fall</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contract Tracking Table -->
        <div class="row mt-3">
            <div class="col-12">
                <h4>Active Contracts</h4>
                <div class="table-responsive">
                    <table id="contractsTable" class="table table-striped contract-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Asset</th>
                                <th>Entry Price</th>
                                <th>Current Price</th>
                                <th>Stake</th>
                                <th>Profit/Loss</th>
                                <th>P/L %</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contract rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id="chart-container" class="mt-3">
        <!-- Chart will be rendered here -->
    </div>
<script>

// Deriv API Token - You'll need to replace this with your actual API token
const DERIV_API_TOKEN = 'YOUR_API_TOKEN'; 

// Class for managing Deriv trading
class DerivTrader {
    constructor() {
        this.ws = null;
        this.contracts = new Map(); // Store active contracts
        this.isConnected = false;
        this.takeProfitPercentage = 0;
        this.currentPrice = 0;
        this.ticksSubscription = null;
        this.contractsSubscription = new Map(); // Store subscription IDs for contracts
        
        this.connect();
    }
    
    connect() {
        this.ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
        
        this.ws.onopen = () => {
            console.log('Connected to Deriv API');
            this.isConnected = true;
            
            // Start getting ticks for price updates
            const asset = document.getElementById('asset').value;
            this.subscribeTicks(asset);
            
            // Initialize with price request
            this.getPriceProposal();
        };
        
        this.ws.onmessage = (msg) => {
            const data = JSON.parse(msg.data);
            //console.log('Trader received:', data);
            
            if (data.tick) {
                // Store subscription ID for management
                if (data.subscription && data.subscription.id) {
                    this.ticksSubscription = data.subscription.id;
                }
                
                // Update current price
                this.currentPrice = data.tick.quote;
                
                // Update price display
                const priceDisplay = document.createElement('div');
                priceDisplay.id = 'current-price-display';
                priceDisplay.style.fontSize = '1.2rem';
                priceDisplay.style.fontWeight = 'bold';
                priceDisplay.style.textAlign = 'center';
                priceDisplay.style.margin = '10px 0';
                priceDisplay.textContent = `${data.tick.symbol} Current Price: ${data.tick.quote.toFixed(4)}`;
                
                // Check if element exists
                const existingDisplay = document.getElementById('current-price-display');
                if (existingDisplay) {
                    existingDisplay.textContent = priceDisplay.textContent;
                } else {
                    const chartContainer = document.getElementById('chart-container');
                    if (chartContainer) {
                        chartContainer.insertBefore(priceDisplay, chartContainer.firstChild);
                    }
                }
                
                // Check all contracts for take profit
                this.checkContractsForTakeProfit();
            } else if (data.proposal) {
                // Handle price proposal
                console.log('Price proposal received:', data.proposal);
            } else if (data.buy) {
                // Handle successful contract purchase
                console.log('Contract bought:', data.buy);
                
                // Store contract and start tracking
                this.trackContract(data.buy);
                
                // Get contract updates
                this.subscribeToContract(data.buy.contract_id);
                
                // Show success message
                alert(`Contract purchased successfully! Contract ID: ${data.buy.contract_id}`);
            } else if (data.proposal_open_contract) {
                // Store subscription ID for management
                if (data.subscription && data.subscription.id && data.proposal_open_contract.contract_id) {
                    this.contractsSubscription.set(
                        data.proposal_open_contract.contract_id,
                        data.subscription.id
                    );
                }
                
                // Update contract status
                const contract = data.proposal_open_contract;
                
                if (this.contracts.has(contract.contract_id)) {
                    this.updateContract(contract);
                }
            } else if (data.sell) {
                // Handle successful contract sale
                console.log('Contract sold:', data.sell);
                
                if (this.contracts.has(data.sell.contract_id)) {
                    // Update the contract with final data
                    const contractInfo = this.contracts.get(data.sell.contract_id);
                    contractInfo.isFinished = true;
                    contractInfo.finalProfit = data.sell.sold_for - contractInfo.buy_price;
                    
                    // Update UI
                    this.updateContractUI(contractInfo);
                    
                    // Unsubscribe from contract updates
                    this.unsubscribeFromContract(data.sell.contract_id);
                    
                    // Remove from active tracking after some time
                    setTimeout(() => {
                        this.removeContract(data.sell.contract_id);
                    }, 5000);
                    
                    // Show success message
                    const profitLoss = data.sell.sold_for - contractInfo.buy_price;
                    const profitLossText = profitLoss >= 0 ? `Profit: ${profitLoss.toFixed(2)}` : `Loss: ${Math.abs(profitLoss).toFixed(2)}`;
                    alert(`Contract sold successfully! ${profitLossText}`);
                }
            } else if (data.forget) {
                // Successful unsubscription
                console.log('Unsubscribed:', data.forget);
            } else if (data.error) {
                console.error('Deriv API error:', data.error);
                alert(`Error: ${data.error.message}`);
            }
        };
        
        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
            this.isConnected = false;
        };
        
        this.ws.onclose = () => {
            console.log('WebSocket disconnected');
            this.isConnected = false;
        };
    }
    
    subscribeTicks(symbol) {
        if (!this.isConnected) return;
        
        // Unsubscribe from any existing ticks first
        this.unsubscribeTicks();
        
        // Subscribe to new ticks
        this.ws.send(JSON.stringify({
            ticks: symbol,
            subscribe: 1
        }));
    }
    
    unsubscribeTicks() {
        if (!this.isConnected) return;
        
        // Cancel all tick subscriptions
        this.ws.send(JSON.stringify({
            forget_all: 'ticks'
        }));
        
        this.ticksSubscription = null;
    }
    
    getPriceProposal() {
        if (!this.isConnected) return;
        
        const symbol = document.getElementById('asset').value;
        const amount = parseFloat(document.getElementById('stake').value) || 10;
        const duration = parseInt(document.getElementById('duration').value) || 5;
        
        // Get price proposal for CALL (rise)
        this.ws.send(JSON.stringify({
            proposal: 1,
            amount: amount,
            basis: 'stake',
            contract_type: 'CALL',
            currency: 'USD',
            duration: duration,
            duration_unit: 't',
            symbol: symbol
        }));
        
        // Get price proposal for PUT (fall)
        this.ws.send(JSON.stringify({
            proposal: 1,
            amount: amount,
            basis: 'stake',
            contract_type: 'PUT',
            currency: 'USD',
            duration: duration,
            duration_unit: 't',
            symbol: symbol
        }));
    }
    
    buyContract(type, amount, duration) {
        if (!this.isConnected) {
            alert('Not connected to Deriv API');
            return;
        }
        
        const symbol = document.getElementById('asset').value;
        
        // Disable the trading buttons during purchase
        document.getElementById('riseButton').disabled = true;
        document.getElementById('fallButton').disabled = true;
        
        const request = {
            buy: 1,
            price: amount,
            parameters: {
                contract_type: type === 'rise' ? 'CALL' : 'PUT',
                symbol: symbol,
                duration: duration,
                duration_unit: 't', // ticks
                currency: 'USD',
                barrier: type === 'rise' ? undefined : undefined // Remove barrier as it's not needed for Rise/Fall
            }
        };
        
        console.log('Buying contract:', request);
        this.ws.send(JSON.stringify(request));
        
        // Re-enable the buttons after a short delay
        setTimeout(() => {
            document.getElementById('riseButton').disabled = false;
            document.getElementById('fallButton').disabled = false;
        }, 1000);
    }
    
    trackContract(contractData) {
        // Store contract info
        const contractInfo = {
            contract_id: contractData.contract_id,
            buy_price: contractData.buy_price,
            type: contractData.longcode.includes('rise') ? 'Rise' : 'Fall',
            symbol: document.getElementById('asset').value,
            start_price: this.currentPrice,
            current_price: this.currentPrice,
            stake: contractData.buy_price,
            profit: 0,
            profit_percentage: 0,
            isFinished: false
        };
        
        this.contracts.set(contractData.contract_id, contractInfo);
        
        // Add to UI
        this.addContractToUI(contractInfo);
    }
    
    subscribeToContract(contractId) {
        if (!this.isConnected) return;
        
        this.ws.send(JSON.stringify({
            proposal_open_contract: 1,
            contract_id: contractId,
            subscribe: 1
        }));
    }
    
    unsubscribeFromContract(contractId) {
        if (!this.isConnected) return;
        
        // If we have the subscription ID, use it for unsubscribing
        if (this.contractsSubscription.has(contractId)) {
            const subscriptionId = this.contractsSubscription.get(contractId);
            this.ws.send(JSON.stringify({
                forget: subscriptionId
            }));
            this.contractsSubscription.delete(contractId);
        }
    }
    
    updateContract(contractData) {
        if (!this.contracts.has(contractData.contract_id)) return;
        
        const contractInfo = this.contracts.get(contractData.contract_id);
        
        // Update contract info
        contractInfo.current_price = contractData.current_spot_time ? 
            contractData.current_spot : contractInfo.current_price;
        contractInfo.profit = contractData.profit;
        contractInfo.profit_percentage = (contractData.profit / contractInfo.buy_price) * 100;
        
        // Update UI
        this.updateContractUI(contractInfo);
        
        // Check for auto take profit
        this.checkTakeProfit(contractInfo);
    }
    
    checkTakeProfit(contractInfo) {
        const takeProfitPercentage = parseFloat(document.getElementById('takeProfit').value);
        
        if (takeProfitPercentage > 0 && contractInfo.profit_percentage >= takeProfitPercentage) {
            console.log(`Take profit reached (${takeProfitPercentage}%) for contract ${contractInfo.contract_id}`);
            this.sellContract(contractInfo.contract_id);
        }
    }
    
    checkContractsForTakeProfit() {
        this.contracts.forEach(contract => {
            if (!contract.isFinished) {
                this.checkTakeProfit(contract);
            }
        });
    }
    
    sellContract(contractId) {
        if (!this.isConnected) return;
        
        this.ws.send(JSON.stringify({
            sell: contractId,
            price: 0 // Market price
        }));
    }
    
    removeContract(contractId) {
        // Remove contract from tracking
        this.contracts.delete(contractId);
        
        // Remove from UI
        const row = document.getElementById(`contract-${contractId}`);
        if (row) {
            row.remove();
        }
    }
    
    addContractToUI(contractInfo) {
        const table = document.getElementById('contractsTable').getElementsByTagName('tbody')[0];
        const row = table.insertRow();
        row.id = `contract-${contractInfo.contract_id}`;
        
        row.innerHTML = `
            <td>${contractInfo.contract_id.substring(0, 8)}...</td>
            <td>${contractInfo.type}</td>
            <td>${contractInfo.symbol}</td>
            <td>${contractInfo.start_price.toFixed(4)}</td>
            <td class="current-price">${contractInfo.current_price.toFixed(4)}</td>
            <td>${contractInfo.stake.toFixed(2)} USD</td>
            <td class="profit">${contractInfo.profit.toFixed(2)} USD</td>
            <td class="profit-percentage">${contractInfo.profit_percentage.toFixed(2)}%</td>
            <td>
                <button class="btn btn-sm btn-warning sell-btn" data-contract-id="${contractInfo.contract_id}">
                    Sell
                </button>
            </td>
        `;
        
        // Add event listener to sell button
        row.querySelector('.sell-btn').addEventListener('click', (e) => {
            const contractId = e.target.getAttribute('data-contract-id');
            this.sellContract(contractId);
        });
    }
    
    updateContractUI(contractInfo) {
        const row = document.getElementById(`contract-${contractInfo.contract_id}`);
        if (!row) return;
        
        // Update fields
        row.querySelector('.current-price').textContent = contractInfo.current_price.toFixed(4);
        
        const profitCell = row.querySelector('.profit');
        profitCell.textContent = `${contractInfo.profit.toFixed(2)} USD`;
        profitCell.className = contractInfo.profit >= 0 ? 'profit profit-green' : 'profit profit-red';
        
        const percentCell = row.querySelector('.profit-percentage');
        percentCell.textContent = `${contractInfo.profit_percentage.toFixed(2)}%`;
        percentCell.className = contractInfo.profit >= 0 ? 'profit-percentage profit-green' : 'profit-percentage profit-red';
        
        // Update sell button if contract is finished
        if (contractInfo.isFinished) {
            row.querySelector('.sell-btn').disabled = true;
            row.querySelector('.sell-btn').textContent = 'Sold';
        }
    }
    
    updateUI() {
        // Update any UI elements based on current trader state
        
        // Update price proposals
        this.getPriceProposal();
        
        // Update contracts table
        this.contracts.forEach(contract => {
            this.updateContractUI(contract);
        });
    }
    
    disconnect() {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            // Unsubscribe from all
            this.ws.send(JSON.stringify({
                forget_all: 'ticks'
            }));
            
            this.ws.send(JSON.stringify({
                forget_all: 'proposal_open_contract'
            }));
            
            this.ws.send(JSON.stringify({
                forget_all: 'proposal'
            }));
            
            this.ws.close();
        }
        
        // Clear subscriptions
        this.ticksSubscription = null;
        this.contractsSubscription.clear();
        
        this.isConnected = false;
    }
}

// นำเข้า class DerivChart ที่ปรับปรุงแล้ว
class DerivChart {
    constructor(containerId, curPair, timeframe, candleCount) {
        this.containerId = containerId;
        this.curPair = curPair;
        this.timeframe = timeframe;
        this.candleCount = candleCount;
        this.chart = null;
        this.candleSeries = null;
        this.ema1Series = null;
        this.ema2Series = null;
        this.rsiChart = null;
        this.rsiSeries = null;
        this.timeLabel = null;
        this.ws = null;
        this.historyCandles = [];  // เปลี่ยนจาก data เป็น historyCandles เพื่อให้ชัดเจนขึ้น
        this.plotMarker = false;
        this.subplotMarker = false;

        this.init();
    }

    init() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Element with id "${this.containerId}" not found.`);
            return;
        }

        // สร้าง container สำหรับแสดงเวลา
        this.timeLabel = document.createElement('div');
        this.timeLabel.style.padding = '10px';
        container.appendChild(this.timeLabel);

        // สร้าง container สำหรับ candlestick chart
        const chartContainer = document.createElement('div');
        chartContainer.style.height = '400px';
        container.appendChild(chartContainer);

        // สร้าง candlestick chart
        this.chart = LightweightCharts.createChart(chartContainer, {
            layout: {
                background: { color: '#ffffff' },
                textColor: '#333',
            },
            grid: {
                vertLines: { color: '#f0f0f0' },
                horzLines: { color: '#f0f0f0' },
            },
            timeScale: {
                timeVisible: true,
                secondsVisible: false,
            },
        });

        this.candleSeries = this.chart.addCandlestickSeries();
        this.connectToderiv();
        this.drawPriceLine();
    }

    drawPriceLine() {
        const horizontalLine = {
            price: 2020,
            color: '#ff0000',
            lineWidth: 2,
            lineStyle: LightweightCharts.LineStyle.Dashed,
            axisLabelVisible: true,
            title: 'Horizontal Line'
        };

        this.candleSeries.createPriceLine(horizontalLine);
    }

    async connectToderiv() {
        this.ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');

        this.ws.onopen = () => {
            // Subscribe to candlestick data
            this.ws.send(JSON.stringify({
                ticks_history: this.curPair,
                adjust_start_time: 1,
                count: this.candleCount,
                end: 'latest',
                start: 1,
                style: 'candles',
                granularity: this.getGranularity(),
                subscribe: 1
            }));
        };

        this.ws.onmessage = (msg) => {
            const data = JSON.parse(msg.data);
            //console.log(data);

            if (data.candles) {
                // ข้อมูลย้อนหลัง (historical data)
                this.historyCandles = data.candles.map(candle => ({
                    time: candle.epoch,
                    open: candle.open,
                    high: candle.high,
                    low: candle.low,
                    close: candle.close,
                    epoch: candle.epoch  // เพิ่ม epoch เพื่อใช้ในการเปรียบเทียบ
                }));
                
                // เรียงข้อมูลตามเวลา
                this.historyCandles.sort((a, b) => a.time - b.time);
                
                console.log(`Stored ${this.historyCandles.length} historical candles`);
                this.updateChart(this.historyCandles);
            } else if (data.ohlc) {
                // ข้อมูลอัปเดตแบบ real-time
                if (data.ohlc.epoch % 60 === 0) {
                    this.plotMarker = true;
                    console.log('Plot Marker True');
                } else {
                    if (data.ohlc.epoch % 10 === 0) {
                        console.log('Plot Sub Marker True');
                        this.subplotMarker = true;
                    }
                }
                
                const newCandle = {
                    time: data.ohlc.epoch,
                    epoch: data.ohlc.epoch,
                    open: parseFloat(data.ohlc.open),
                    high: parseFloat(data.ohlc.high),
                    low: parseFloat(data.ohlc.low),
                    close: parseFloat(data.ohlc.close)
                };
                
                // แปลง epoch เป็นนาที (ตัดวินาทีทิ้ง) เพื่อใช้ในการเปรียบเทียบ
                const candleMinute = Math.floor(newCandle.epoch / this.getGranularity()) * this.getGranularity();
                
                // ตรวจสอบว่ามีแท่งเทียนของนาทีนี้อยู่แล้วหรือไม่
                let existingCandleIndex = this.historyCandles.findIndex(candle => 
                    Math.floor(candle.epoch / this.getGranularity()) * this.getGranularity() === candleMinute
                );
				if (document.getElementById("viewType1").checked) {
					//existingCandleIndex = -2 ;
				} else {
					existingCandleIndex = -1 ;
				}
				console.log('existingCandleIndex',existingCandleIndex);
				
                
                if (existingCandleIndex !== -1) {
                    // อัพเดทข้อมูลแท่งเทียนที่มีอยู่แล้ว
                    console.log(`Updating candle for ${new Date(newCandle.time * 1000).toLocaleTimeString()}`);
                    
                    // อัพเดตค่า high และ low ถ้าจำเป็น
                    if (newCandle.high > this.historyCandles[existingCandleIndex].high) {
                        this.historyCandles[existingCandleIndex].high = newCandle.high;
                    }
                    
                    if (newCandle.low < this.historyCandles[existingCandleIndex].low) {
                        this.historyCandles[existingCandleIndex].low = newCandle.low;
                    }
                    
                    // อัพเดตค่า close และ epoch
                    this.historyCandles[existingCandleIndex].close = newCandle.close;
                    this.historyCandles[existingCandleIndex].epoch = newCandle.epoch;
                    this.historyCandles[existingCandleIndex].time = newCandle.time;
                } else {
                    // เพิ่มแท่งเทียนใหม่
                    console.log(`Adding new candle for ${new Date(newCandle.time * 1000).toLocaleTimeString()}`);
                    this.historyCandles.push(newCandle);
                    
                    // เรียงข้อมูลตามเวลาอีกครั้งหลังจากเพิ่มข้อมูลใหม่
                    this.historyCandles.sort((a, b) => a.time - b.time);
                }
                
                // แสดงข้อมูลล่าสุด
                console.log("Latest candle:", newCandle);
                
                this.updateChart(this.historyCandles);
            }
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        this.ws.onclose = () => {
            console.log('WebSocket disconnected');
        };
    }

    getGranularity() {
        // Convert timeframe to seconds
        const timeframes = {
            '1m': 60,
            '5m': 300
        };
        return timeframes[this.timeframe];
    }

    updateChart(data) {
        if (data.length === 0) return;

        // Update candlestick series
        this.candleSeries.setData(data);
        console.log("Chart updated with", data.length, "candles");

        // Create markers for every 60 seconds (1 minute)
        const markers = data.reduce((acc, candle) => {
            if (candle.time % 60 === 0) {
                acc.push({
                    time: candle.time,
                    position: 'aboveBar',
                    color: '#2196F3',
                    shape: 'triangle',
                    text: '▼'
                });
            }
            return acc;
        }, []);

        const submarkers = data.reduce((acc, candle) => {
            if (candle.time % 10 === 0) {
                acc.push({
                    time: candle.time,
                    position: 'aboveBar',
                    color: '#ff0080',
                    shape: 'triangle',
                    text: '▼'
                });
            }
            return acc;
        }, []);

        // Add markers to the series
        if (this.plotMarker) {
            this.candleSeries.setMarkers(markers);
            this.plotMarker = false; // Reset plotMarker
        } else {
            if (this.subplotMarker) {
              this.candleSeries.setMarkers(submarkers);
              this.subplotMarker = false; // Reset subplotMarker
            }
        }

        // Optional: Add time formatter to ensure proper time display
        this.chart.applyOptions({
            timeScale: {
                timeVisible: true,
                secondsVisible: false,
            },
        });

        // Update time label
        const lastCandle = data[data.length - 1];
        this.timeLabel.textContent = `Last Update: ${new Date(lastCandle.time * 1000).toLocaleString()}`;
    }
    
    // เพิ่มฟังก์ชั่นสำหรับดึงข้อมูล historyCandles ไปใช้งาน
    getHistoryCandles() {
        return [...this.historyCandles]; // ส่งค่ากลับเป็น copy เพื่อป้องกันการเปลี่ยนแปลงโดยตรง
    }
    
    // ฟังก์ชั่นสำหรับปิดการเชื่อมต่อ
    disconnect() {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.close();
        }
    }
}

// ตัวแปรที่เก็บ instance ของ chart และ trader
let chartInstance = null;
let traderInstance = null;

function savetoLocal() {
    localStorage.setItem("curpairSelected", document.getElementById("asset").value);
    alert("Asset saved to local storage!");
}

function getFromLocal() {
    const savedAsset = localStorage.getItem("curpairSelected");
    if (savedAsset) {
        document.getElementById("asset").value = savedAsset;
        alert("Asset loaded from local storage: " + savedAsset);
    } else {
        alert("No asset found in local storage!");
    }
}

// ฟังก์ชั่นสำหรับรีเฟรชกราฟเมื่อเปลี่ยนค่า asset
function refreshChart() {
    const chartContainer = document.getElementById('chart-container');
    const curpair = document.getElementById("asset").value;
    
    // ลบ DOM elements เดิมทั้งหมดใน chart container
    while (chartContainer.firstChild) {
        chartContainer.removeChild(chartContainer.firstChild);
    }
    
    // ปิดการเชื่อมต่อ WebSocket เดิม (ถ้ามี)
    if (chartInstance) {
        chartInstance.disconnect();
    }
    
    // ปิดการเชื่อมต่อ trader (ถ้ามี)
    if (traderInstance) {
        traderInstance.disconnect();
    }
    
    // สร้างกราฟใหม่
    chartInstance = new DerivChart('chart-container', curpair, '1m', 100);
    
    // สร้าง trader instance ใหม่
    traderInstance = new DerivTrader();
    
    console.log("Chart and trader refreshed with asset:", curpair);
}
</script>