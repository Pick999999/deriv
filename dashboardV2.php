<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
	<link href="css/dashBoardV2.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>

	
	<script src="dashBoardV2.js" ></script>
	
    
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-grid">
            <!-- Header -->
            <div class="header-panel">
                <div class="d-flex align-items-center h-100 px-3">
                    <div class="logo me-4">TradePro</div>
                    <div class="d-flex gap-2">
					    <button class="btn btn-dark-custom btn-sm" id='btnConnect' onclick="connect()">Connect</button>
                        <button class="btn btn-dark-custom btn-sm">Trade</button>
                        <button class="btn btn-dark-custom btn-sm">Portfolio</button>
                        <button class="btn btn-dark-custom btn-sm">History</button>
                        <button class="btn btn-dark-custom btn-sm">Analysis</button>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2 market-status">
                        <span class="status-dot"></span>
                        <span id="myBalance" style='font-size:24px;color:#ffff00'></span>
						&nbsp;$
                        <span>Market Open</span>
                        <span id="current-time" style="color:#ffff80;font-size:22px">-</span>
                    </div>
                </div>
            </div>

            <!-- Watchlist -->
            <div class="watchlist-panel">
                <div class="panel-header">
                    <i class="bi bi-list-ul me-2"></i>Watchlist
                </div>
                <div class="scrollable h-100">
                    <div class="symbol-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">BTC/USDT</div>
                                <small class="text-muted">Bitcoin</small>
                            </div>
                            <div class="text-end price-up">
                                <div>68,245.30</div>
                                <small>+2.45%</small>
                            </div>
                        </div>
                    </div>
                    <div class="symbol-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">ETH/USDT</div>
                                <small class="text-muted">Ethereum</small>
                            </div>
                            <div class="text-end price-up">
                                <div>3,425.80</div>
                                <small>+1.23%</small>
                            </div>
                        </div>
                    </div>
                    <div class="symbol-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">EUR/USD</div>
                                <small class="text-muted">Euro Dollar</small>
                            </div>
                            <div class="text-end price-down">
                                <div>1.0856</div>
                                <small>-0.15%</small>
                            </div>
                        </div>
                    </div>
                    <div class="symbol-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">GBP/USD</div>
                                <small class="text-muted">Pound Dollar</small>
                            </div>
                            <div class="text-end price-up">
                                <div>1.2645</div>
                                <small>+0.32%</small>
                            </div>
                        </div>
                    </div>
                    <div class="symbol-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">USD/JPY</div>
                                <small class="text-muted">Dollar Yen</small>
                            </div>
                            <div class="text-end price-down">
                                <div>148.25</div>
                                <small>-0.08%</small>
                            </div>
                        </div>
                    </div>
                    <div class="symbol-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">GOLD</div>
                                <small class="text-muted">Gold Spot</small>
                            </div>
                            <div class="text-end price-up">
                                <div>2,048.50</div>
                                <small>+0.75%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Chart -->
            <div class="chart-panel">
                <div class="panel-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <span id='watchAsset' class="fw-bold text-success fs-5">BTC/USDT</span>
							<input type="text" id="timeFrameSel">
                            <div class="btn-group" role="group">
                                <button type="button" id='btnTimeFrame1' class="btn btn-dark-custom btn-sm" onclick='fetchCandles("1","m",this.id)'>1m</button>
                                <button type="button" id='btnTimeFrame5' class="btn btn-dark-custom btn-sm" onclick='fetchCandles("5","m",this.id)'>5m</button>
                                <button type="button" id='btnTimeFrame15' class="btn btn-dark-custom btn-sm " onclick='fetchCandles("15","m",this.id)'>15m</button>
								<button type="button" id='btnTimeFrame30' class="btn btn-dark-custom btn-sm " onclick='fetchCandles("30","m",this.id)'>30m</button>
                                <button type="button" id='btnTimeFrame1h' class="btn btn-dark-custom btn-sm" onclick='fetchCandles("1","h",this.id)'>1h</button>
                                <button type="button" id='btnTimeFrame4h' class="btn btn-dark-custom btn-sm" onclick='fetchCandles("4","h")' >4h</button>
                                <button type="button" id='btnTimeFrame1d' class="btn btn-dark-custom btn-sm" onclick='fetchCandles("1","D",this.id)'>1D</button>
                            </div>
                        </div>
                        <div class="flex text-end">
						    <!-- 
						     <div id="priceAtFirstSecond" class="bordergray flex">
						          9999
						     </div>
							  -->
                            <div id="current-price-display" class="current-price-display">68,245.30</div>&nbsp;&nbsp;
                            <span id='priceDiff'><small  class="price-up">+1,648.75 (+2.45%)</small></span>
                        </div>
                    </div>
                </div>
                <div id="chartContainer" sclass="chart-container">
				   <!-- 
                    <canvas id="tradingChart" class="candlestick-chart"></canvas>
                   -->
                </div>
				
				
				<div id="" class="bordergray sflex">
				  <div id="" class="flex" style='justify-content: space-between;'>
				    <div id="" >
				       <h4 style='margin-left:15px'>Trade Section </h4>  
				    </div>    
					<div id="" class="flex" >
					  <span id='current-time2' style="color:#ffff80;font-size:22px"></span> 
					</div>
					Second Time::<input type="text" id ="secondTime">
				  </div>
				   
				   

				   <span id='tradeInfo'></span>
				   <div id="" class="bordergray flex" style='height:60px'>
				     <table style='margin-right:15px'>
					   <tr>
				        <td><input type="radio" name="timeframeTrade" checked>1 M</td>
					    <td><input type="radio" name="timeframeTrade">3 M</td>
					    <td><input type="radio" name="timeframeTrade">5 M</td>
					    <td><input type="radio" name="timeframeTrade">1 Hour</td>
                      </tr> 
                     </table>
				   
					   <button type='button' id='' class='mBtn green' onclick="BuyContract('CALL')">CALL</button>  
					   <button type='button' id='' class='mBtn red' onclick="BuyContract('PUT')">PUT</button>  
					   <button type='button' id='' class='mBtn lightblue' onclick="fff()">AUTO</button>  
                   </div> 


				</div>
				<div id="tradeTableContainer" class="bordergray">
     
                </div>
            </div>

            <!-- Technical Indicators -->
            <div class="indicators-panel">
                <div class="panel-header">
                    <i class="bi bi-graph-up me-2"></i>Technical Indicators
                </div>
                <div class="scrollable h-100">
                    <div class="indicator-item">
                        <div class="indicator-label">RSI (14)</div>
                        <div class="indicator-value price-up">62.45</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">MACD</div>
                        <div class="indicator-value price-up">+145.23</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">MA (20)</div>
                        <div class="indicator-value">67,890.15</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">MA (50)</div>
                        <div class="indicator-value">66,245.80</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">Bollinger Upper</div>
                        <div class="indicator-value">69,450.20</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">Bollinger Lower</div>
                        <div class="indicator-value">65,230.10</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">Volume</div>
                        <div class="indicator-value">2.45M</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">24h High</div>
                        <div class="indicator-value price-up">68,890.25</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">24h Low</div>
                        <div class="indicator-value price-down">65,120.50</div>
                    </div>
                </div>
            </div>

            <!-- Positions -->
            <div class="positions-panel">
                <div class="panel-header">
                    <i class="bi bi-wallet2 me-2"></i>Open Positions
                </div>
                <div class="scrollable h-100">
                    <div class="position-item">
                        <div class="row">
                            <div class="col-4 fw-bold text-muted">Symbol</div>
                            <div class="col-4 fw-bold text-muted">Size</div>
                            <div class="col-4 fw-bold text-muted">P&L</div>
                        </div>
                    </div>
                    <div class="position-item">
                        <div class="row">
                            <div class="col-4">BTC/USDT</div>
                            <div class="col-4">0.25 BTC</div>
                            <div class="col-4 price-up">+$425.50</div>
                        </div>
                    </div>
                    <div class="position-item">
                        <div class="row">
                            <div class="col-4">ETH/USDT</div>
                            <div class="col-4">2.5 ETH</div>
                            <div class="col-4 price-down">-$85.25</div>
                        </div>
                    </div>
                    <div class="position-item">
                        <div class="row">
                            <div class="col-4">EUR/USD</div>
                            <div class="col-4">10,000</div>
                            <div class="col-4 price-up">+$125.30</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Book -->
            <div class="orderbook-panel">
                <div class="panel-header">
                    <i class="bi bi-journals me-2"></i>Order Book
                </div>
                <div class="d-flex flex-column h-100" style="height: calc(100% - 40px);">
                    <div class="flex-fill scrollable">
                        <div class="order-item">
                            <div class="row fw-bold text-muted">
                                <div class="col-4">Price</div>
                                <div class="col-4">Size</div>
                                <div class="col-4">Total</div>
                            </div>
                        </div>
                        <div class="orders-sell">
                            <div class="order-item">
                                <div class="row">
                                    <div class="col-4">68,280.50</div>
                                    <div class="col-4">0.125</div>
                                    <div class="col-4">8,535.06</div>
                                </div>
                            </div>
                            <div class="order-item">
                                <div class="row">
                                    <div class="col-4">68,275.25</div>
                                    <div class="col-4">0.250</div>
                                    <div class="col-4">17,068.81</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center py-2 bg-dark border-top border-bottom">
                        <strong class="price-up">68,245.30</strong>
                    </div>
                    <div class="flex-fill scrollable">
                        <div class="orders-buy">
                            <div class="order-item">
                                <div class="row">
                                    <div class="col-4">68,240.15</div>
                                    <div class="col-4">0.145</div>
                                    <div class="col-4">9,894.82</div>
                                </div>
                            </div>
                            <div class="order-item">
                                <div class="row">
                                    <div class="col-4">68,235.00</div>
                                    <div class="col-4">0.285</div>
                                    <div class="col-4">19,446.98</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Tabs Container -->
            <div class="bottom-tabs-panel">
                <div class="h-100">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="bottomTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                                <i class="bi bi-clipboard-check me-1"></i>Orders
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                <i class="bi bi-clock-history me-1"></i>History
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="alerts-tab" data-bs-toggle="tab" data-bs-target="#alerts" type="button" role="tab">
                                <i class="bi bi-bell me-1"></i>Alerts
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="news-tab" data-bs-toggle="tab" data-bs-target="#news" type="button" role="tab">
                                <i class="bi bi-newspaper me-1"></i>News
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analysis-tab" data-bs-toggle="tab" data-bs-target="#analysis" type="button" role="tab">
                                <i class="bi bi-bar-chart me-1"></i>Analysis
                            </button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="orders" role="tabpanel">
                            <table class="table table-dark-custom table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Symbol</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>15:32:45</td>
                                        <td>BTC/USDT</td>
                                        <td><span class="text-success">BUY</span></td>
                                        <td>0.125</td>
                                        <td>68,200.00</td>
                                        <td><span class="badge bg-success">Filled</span></td>
                                    </tr>
                                    <tr>
                                        <td>14:28:12</td>
                                        <td>ETH/USDT</td>
                                        <td><span class="text-danger">SELL</span></td>
                                        <td>1.5</td>
                                        <td>3,420.50</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <table class="table table-dark-custom table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Symbol</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Entry Price</th>
                                        <th>Exit Price</th>
                                        <th>P&L</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>2025-05-21</td>
                                        <td>BTC/USDT</td>
                                        <td><span class="text-success">BUY</span></td>
                                        <td>0.5</td>
                                        <td>67,500.00</td>
                                        <td>68,100.00</td>
                                        <td class="text-success">+$300.00</td>
                                    </tr>
                                    <tr>
                                        <td>2025-05-20</td>
                                        <td>ETH/USDT</td>
                                        <td><span class="text-danger">SELL</span></td>
                                        <td>2.0</td>
                                        <td>3,500.00</td>
                                        <td>3,450.00</td>
                                        <td class="text-danger">-$100.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="alerts" role="tabpanel">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item bg-dark text-light border-secondary">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">BTC Price Alert</h6>
                                        <small>2 mins ago</small>
                                    </div>
                                    <p class="mb-1">Bitcoin reached $68,000 resistance level</p>
                                    <small class="text-success">Active</small>
                                </div>
                                <div class="list-group-item bg-dark text-light border-secondary">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">RSI Overbought</h6>
                                        <small>15 mins ago</small>
                                    </div>
                                    <p class="mb-1">ETH RSI above 70 - Consider taking profits</p>
                                    <small class="text-warning">Warning</small>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="news" role="tabpanel">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item bg-dark text-light border-secondary">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Bitcoin ETF Approval News</h6>
                                        <small>1 hour ago</small>
                                    </div>
                                    <p class="mb-1">SEC approves new Bitcoin ETF applications driving market sentiment...</p>
                                    <small class="text-info">Market Impact: High</small>
                                </div>
                                <div class="list-group-item bg-dark text-light border-secondary">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Fed Interest Rate Decision</h6>
                                        <small>3 hours ago</small>
                                    </div>
                                    <p class="mb-1">Federal Reserve maintains current interest rates, positive for crypto...</p>
                                    <small class="text-success">Market Impact: Medium</small>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="analysis" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-dark border-secondary">
                                        <div class="card-header">Market Summary</div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="text-success">↑ 65%</div>
                                                    <small>Bulls</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-danger">↓ 35%</div>
                                                    <small>Bears</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-warning">75</div>
                                                    <small>Fear & Greed</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-dark border-secondary">
                                        <div class="card-header">Top Movers</div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <span>DOGE/USDT</span>
                                                <span class="text-success">+12.5%</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>ADA/USDT</span>
                                                <span class="text-success">+8.2%</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>SOL/USDT</span>
                                                <span class="text-danger">-3.1%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
         // Chart drawing function
		/*
        const canvas = document.getElementById('tradingChart');
        const ctx = canvas.getContext('2d');

        function resizeCanvas() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            drawChart();
        }
		*/

        function drawChart() {
            if (!canvas.width || !canvas.height) return;
            
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Draw grid
            ctx.strokeStyle = '#333';
            ctx.lineWidth = 1;
            
            for (let i = 0; i < 10; i++) {
                const y = (canvas.height / 10) * i;
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
                
                const x = (canvas.width / 20) * i;
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }
            
            // Draw candlesticks
            const candleWidth = 8;
            const spacing = 12;
            const basePrice = 67000;
            const priceRange = 2000;
            
            for (let i = 0; i < Math.floor(canvas.width / spacing); i++) {
                const x = i * spacing + 50;
                const open = basePrice + Math.random() * priceRange;
                const close = open + (Math.random() - 0.5) * 500;
                const high = Math.max(open, close) + Math.random() * 200;
                const low = Math.min(open, close) - Math.random() * 200;
                
                const openY = canvas.height - ((open - basePrice) / priceRange) * canvas.height * 0.8 - 50;
                const closeY = canvas.height - ((close - basePrice) / priceRange) * canvas.height * 0.8 - 50;
                const highY = canvas.height - ((high - basePrice) / priceRange) * canvas.height * 0.8 - 50;
                const lowY = canvas.height - ((low - basePrice) / priceRange) * canvas.height * 0.8 - 50;
                
                // Draw wick
                ctx.strokeStyle = close > open ? '#00ff88' : '#ff4444';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(x, highY);
                ctx.lineTo(x, lowY);
                ctx.stroke();
                
                // Draw body
                ctx.fillStyle = close > open ? '#00ff88' : '#ff4444';
                const bodyHeight = Math.abs(closeY - openY);
                const bodyTop = Math.min(openY, closeY);
                ctx.fillRect(x - candleWidth/2, bodyTop, candleWidth, bodyHeight);
            }
            
            // Draw moving average
            ctx.strokeStyle = '#ffaa00';
            ctx.lineWidth = 2;
            ctx.beginPath();
            for (let i = 0; i < Math.floor(canvas.width / spacing); i++) {
                const x = i * spacing + 50;
                const price = basePrice + priceRange/2 + Math.sin(i * 0.1) * 200;
                const y = canvas.height - ((price - basePrice) / priceRange) * canvas.height * 0.8 - 50;
                
                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            }
            ctx.stroke();
        }

        // Initialize chart
        //window.addEventListener('resize', resizeCanvas);
        //resizeCanvas();

        // Update prices simulation
        setInterval(() => {
            const symbols = document.querySelectorAll('.symbol-item');
            symbols.forEach(symbol => {
                const priceElement = symbol.querySelector('.text-end div:first-child');
                if (priceElement) {
                    const currentPrice = parseFloat(priceElement.textContent.replace(',', ''));
                    const change = (Math.random() - 0.5) * currentPrice * 0.001;
                    const newPrice = currentPrice + change;
                    priceElement.textContent = newPrice.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    // Update price color
                    const priceContainer = symbol.querySelector('.text-end');
                    priceContainer.className = change > 0 ? 'text-end price-up' : 'text-end price-down';
                }
            });
            
            // Update main price
            const mainPrice = document.querySelector('.current-price-display');
            if (mainPrice) {
                const currentPrice = parseFloat(mainPrice.textContent.replace(',', ''));
                const change = (Math.random() - 0.5) * currentPrice * 0.0005;
                const newPrice = currentPrice + change;
                mainPrice.textContent = newPrice.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        }, 2000);

        // Update time
		/*
        setInterval(() => {
            const now = new Date();
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', {hour12: false});
            }
        }, 1000);
		*/

        // Timeframe button functionality
        document.querySelectorAll('.btn-group button').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-group button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Here you would typically reload chart data for the selected timeframe
                console.log('Selected timeframe:', this.textContent);
            });
        });

        // Symbol selection functionality
        document.querySelectorAll('.symbol-item').forEach(item => {
            item.addEventListener('click', function() {
                const symbolName = this.querySelector('.fw-bold').textContent;
                document.querySelector('.chart-panel .fw-bold').textContent = symbolName;
                console.log('Selected symbol:', symbolName);
                // Here you would typically load new chart data for the selected symbol
            });
        });

        // Tab content auto-refresh simulation
        setInterval(() => {
            // Simulate real-time updates for active tab content
            const activeTab = document.querySelector('.tab-pane.active');
            if (activeTab && activeTab.id === 'orders') {
                // Update order statuses, times, etc.
                console.log('Updating orders tab...');
            }
        }, 5000);
    </script>
</body>
</html>