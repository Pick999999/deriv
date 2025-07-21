<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            overflow: hidden;
        }

        .dashboard {
            display: grid;
            grid-template-areas: 
                "header header header"
                "watchlist chart-main indicators"
                "positions chart-main orderbook";
            grid-template-columns: 250px 1fr 250px;
            grid-template-rows: 60px 1fr 300px;
            height: 100vh;
            gap: 2px;
        }

        .header {
            grid-area: header;
            background: #1a1a1a;
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid #333;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #00ff88;
            margin-right: 30px;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
        }

        .nav-btn {
            background: #333;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .nav-btn:hover {
            background: #444;
        }

        .market-status {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #00ff88;
        }

        .watchlist {
            grid-area: watchlist;
            background: #1a1a1a;
            border-right: 1px solid #333;
        }

        .panel-header {
            background: #2a2a2a;
            padding: 10px 15px;
            font-weight: bold;
            border-bottom: 1px solid #333;
            font-size: 14px;
        }

        .symbol-list {
            height: calc(100% - 40px);
            overflow-y: auto;
        }

        .symbol-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 15px;
            border-bottom: 1px solid #2a2a2a;
            cursor: pointer;
            transition: background 0.3s;
        }

        .symbol-item:hover {
            background: #2a2a2a;
        }

        .symbol-name {
            font-weight: bold;
        }

        .symbol-price {
            text-align: right;
        }

        .price-up {
            color: #00ff88;
        }

        .price-down {
            color: #ff4444;
        }

        .chart-main {
            grid-area: chart-main;
            background: #1a1a1a;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .chart-toolbar {
            background: #2a2a2a;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #333;
        }

        .timeframe-buttons {
            display: flex;
            gap: 5px;
        }

        .timeframe-btn {
            background: #333;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .timeframe-btn.active {
            background: #00ff88;
            color: black;
        }

        .chart-container {
            flex: 1;
            position: relative;
            background: #111;
        }

        .candlestick-chart {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .indicators {
            grid-area: indicators;
            background: #1a1a1a;
            border-left: 1px solid #333;
        }

        .indicator-item {
            padding: 10px 15px;
            border-bottom: 1px solid #2a2a2a;
            font-size: 12px;
        }

        .indicator-label {
            color: #888;
            margin-bottom: 5px;
        }

        .indicator-value {
            font-weight: bold;
            font-size: 14px;
        }

        .positions {
            grid-area: positions;
            background: #1a1a1a;
            border-top: 1px solid #333;
            border-right: 1px solid #333;
        }

        .position-item {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            padding: 8px 15px;
            border-bottom: 1px solid #2a2a2a;
            font-size: 12px;
        }

        .orderbook {
            grid-area: orderbook;
            background: #1a1a1a;
            border-top: 1px solid #333;
            border-left: 1px solid #333;
        }

        .orderbook-content {
            height: calc(100% - 40px);
            display: flex;
            flex-direction: column;
        }

        .orders-sell, .orders-buy {
            flex: 1;
            overflow-y: auto;
        }

        .order-item {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            padding: 3px 10px;
            font-size: 11px;
            font-family: monospace;
        }

        .orders-sell .order-item {
            color: #ff4444;
        }

        .orders-buy .order-item {
            color: #00ff88;
        }

        .scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar::-webkit-scrollbar-track {
            background: #2a2a2a;
        }

        .scrollbar::-webkit-scrollbar-thumb {
            background: #555;
            border-radius: 3px;
        }

        .trading-pair {
            font-size: 18px;
            font-weight: bold;
            color: #00ff88;
        }

        .price-display {
            margin-left: auto;
            text-align: right;
        }

        .current-price {
            font-size: 20px;
            font-weight: bold;
            color: #00ff88;
        }

        .price-change {
            font-size: 14px;
            color: #00ff88;
        }

        /* Responsive Chart Simulation */
        .chart-line {
            position: absolute;
            height: 2px;
            background: #00ff88;
            transform-origin: left;
        }

        .volume-bar {
            position: absolute;
            bottom: 20px;
            background: rgba(0, 255, 136, 0.3);
            width: 3px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Header -->
        <div class="header">
            <div class="logo">TradePro</div>
            <div class="nav-buttons">
                <button class="nav-btn">Trade</button>
                <button class="nav-btn">Portfolio</button>
                <button class="nav-btn">History</button>
                <button class="nav-btn">Analysis</button>
            </div>
            <div class="market-status">
                <div class="status-dot"></div>
                <span>Market Open</span>
                <span>17:42:35</span>
            </div>
        </div>

        <!-- Watchlist -->
        <div class="watchlist">
            <div class="panel-header">Watchlist</div>
            <div class="symbol-list scrollbar">
                <div class="symbol-item">
                    <div>
                        <div class="symbol-name">BTC/USDT</div>
                        <div style="font-size: 11px; color: #888;">Bitcoin</div>
                    </div>
                    <div class="symbol-price price-up">
                        <div>68,245.30</div>
                        <div style="font-size: 11px;">+2.45%</div>
                    </div>
                </div>
                <div class="symbol-item">
                    <div>
                        <div class="symbol-name">ETH/USDT</div>
                        <div style="font-size: 11px; color: #888;">Ethereum</div>
                    </div>
                    <div class="symbol-price price-up">
                        <div>3,425.80</div>
                        <div style="font-size: 11px;">+1.23%</div>
                    </div>
                </div>
                <div class="symbol-item">
                    <div>
                        <div class="symbol-name">EUR/USD</div>
                        <div style="font-size: 11px; color: #888;">Euro Dollar</div>
                    </div>
                    <div class="symbol-price price-down">
                        <div>1.0856</div>
                        <div style="font-size: 11px;">-0.15%</div>
                    </div>
                </div>
                <div class="symbol-item">
                    <div>
                        <div class="symbol-name">GBP/USD</div>
                        <div style="font-size: 11px; color: #888;">Pound Dollar</div>
                    </div>
                    <div class="symbol-price price-up">
                        <div>1.2645</div>
                        <div style="font-size: 11px;">+0.32%</div>
                    </div>
                </div>
                <div class="symbol-item">
                    <div>
                        <div class="symbol-name">USD/JPY</div>
                        <div style="font-size: 11px; color: #888;">Dollar Yen</div>
                    </div>
                    <div class="symbol-price price-down">
                        <div>148.25</div>
                        <div style="font-size: 11px;">-0.08%</div>
                    </div>
                </div>
                <div class="symbol-item">
                    <div>
                        <div class="symbol-name">GOLD</div>
                        <div style="font-size: 11px; color: #888;">Gold Spot</div>
                    </div>
                    <div class="symbol-price price-up">
                        <div>2,048.50</div>
                        <div style="font-size: 11px;">+0.75%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chart -->
        <div class="chart-main">
            <div class="chart-toolbar">
                <div class="trading-pair">BTC/USDT</div>
                <div class="timeframe-buttons">
                    <button class="timeframe-btn">1m</button>
                    <button class="timeframe-btn">5m</button>
                    <button class="timeframe-btn active">15m</button>
                    <button class="timeframe-btn">1h</button>
                    <button class="timeframe-btn">4h</button>
                    <button class="timeframe-btn">1D</button>
                </div>
                <div class="price-display">
                    <div class="current-price">68,245.30</div>
                    <div class="price-change">+1,648.75 (+2.45%)</div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="tradingChart" class="candlestick-chart"></canvas>
            </div>
        </div>

        <!-- Technical Indicators -->
        <div class="indicators">
            <div class="panel-header">Technical Indicators</div>
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

        <!-- Positions -->
        <div class="positions">
            <div class="panel-header">Open Positions</div>
            <div class="position-item" style="font-weight: bold; color: #888;">
                <div>Symbol</div>
                <div>Size</div>
                <div>P&L</div>
            </div>
            <div class="position-item">
                <div>BTC/USDT</div>
                <div>0.25 BTC</div>
                <div class="price-up">+$425.50</div>
            </div>
            <div class="position-item">
                <div>ETH/USDT</div>
                <div>2.5 ETH</div>
                <div class="price-down">-$85.25</div>
            </div>
            <div class="position-item">
                <div>EUR/USD</div>
                <div>10,000</div>
                <div class="price-up">+$125.30</div>
            </div>
        </div>

        <!-- Order Book -->
        <div class="orderbook">
            <div class="panel-header">Order Book</div>
            <div class="orderbook-content">
                <div class="orders-sell scrollbar">
                    <div class="order-item" style="font-weight: bold; color: #888;">
                        <div>Price</div>
                        <div>Size</div>
                        <div>Total</div>
                    </div>
                    <div class="order-item">
                        <div>68,280.50</div>
                        <div>0.125</div>
                        <div>8,535.06</div>
                    </div>
                    <div class="order-item">
                        <div>68,275.25</div>
                        <div>0.250</div>
                        <div>17,068.81</div>
                    </div>
                    <div class="order-item">
                        <div>68,270.00</div>
                        <div>0.180</div>
                        <div>12,288.60</div>
                    </div>
                    <div class="order-item">
                        <div>68,265.75</div>
                        <div>0.095</div>
                        <div>6,485.25</div>
                    </div>
                    <div class="order-item">
                        <div>68,260.50</div>
                        <div>0.320</div>
                        <div>21,843.36</div>
                    </div>
                </div>
                <div style="padding: 5px 10px; background: #2a2a2a; text-align: center; font-weight: bold; color: #00ff88;">
                    68,245.30
                </div>
                <div class="orders-buy scrollbar">
                    <div class="order-item">
                        <div>68,240.15</div>
                        <div>0.145</div>
                        <div>9,894.82</div>
                    </div>
                    <div class="order-item">
                        <div>68,235.00</div>
                        <div>0.285</div>
                        <div>19,446.98</div>
                    </div>
                    <div class="order-item">
                        <div>68,230.85</div>
                        <div>0.165</div>
                        <div>11,258.09</div>
                    </div>
                    <div class="order-item">
                        <div>68,225.50</div>
                        <div>0.220</div>
                        <div>15,009.61</div>
                    </div>
                    <div class="order-item">
                        <div>68,220.25</div>
                        <div>0.175</div>
                        <div>11,938.54</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple chart simulation
        const canvas = document.getElementById('tradingChart');
        const ctx = canvas.getContext('2d');

        function resizeCanvas() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            drawChart();
        }

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

        // Initialize
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // Update prices simulation
        setInterval(() => {
            const symbols = document.querySelectorAll('.symbol-item');
            symbols.forEach(symbol => {
                const priceElement = symbol.querySelector('.symbol-price div:first-child');
                if (priceElement) {
                    const currentPrice = parseFloat(priceElement.textContent.replace(',', ''));
                    const change = (Math.random() - 0.5) * currentPrice * 0.001;
                    const newPrice = currentPrice + change;
                    priceElement.textContent = newPrice.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    // Update price color
                    const priceContainer = symbol.querySelector('.symbol-price');
                    priceContainer.className = change > 0 ? 'symbol-price price-up' : 'symbol-price price-down';
                }
            });
            
            // Update main price
            const mainPrice = document.querySelector('.current-price');
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
        setInterval(() => {
            const now = new Date();
            const timeElement = document.querySelector('.market-status span:last-child');
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', {hour12: false});
            }
        }, 1000);
    </script>
</body>
</html>