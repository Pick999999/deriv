<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candlestick Data Fetcher</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        .status { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .connected { background-color: #d4edda; color: #155724; }
        .disconnected { background-color: #f8d7da; color: #721c24; }
        .reconnecting { background-color: #fff3cd; color: #856404; }
        .control-panel { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        select, button, input { padding: 8px 12px; border-radius: 4px; border: 1px solid #ced4da; }
        button { background-color: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0069d9; }
        button:disabled { background-color: #6c757d; cursor: not-allowed; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
        .data-container { height: 500px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; margin-top: 20px; }
        .bullish { color: #28a745; }
        .bearish { color: #dc3545; }
    </style>
    <!-- Import DerivCandlestickFetcher from external file -->
    <script src="DerivCandlestickFetcher.js"></script>
	<script src="IndyLib.js"></script>
	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
</head>
<body>
    <div class="container">
        <h1>Deriv Candlestick Data Fetcher</h1>
        
        <div id="connectionStatus" class="status disconnected">
            Status: Disconnected
        </div>
        
        <div class="control-panel">
            <input type="text" id="appId" placeholder="App ID" value="66726">
            <select id="symbol">
                <option value="R_100">Volatility 100 Index</option>
                <option value="R_50">Volatility 50 Index</option>
                <option value="R_25">Volatility 25 Index</option>
                <option value="R_75">Volatility 75 Index</option>
                <option value="BTCUSD">Bitcoin</option>
                <option value="ETHUSD">Ethereum</option>
                <option value="frxEURUSD">EUR/USD</option>
                <option value="frxGBPUSD">GBP/USD</option>
                <option value="frxUSDJPY">USD/JPY</option>
            </select>
            
            <select id="granularity">
                <option value="60">1 minute</option>
                <option value="300">5 minutes</option>
                <option value="900">15 minutes</option>
                <option value="1800">30 minutes</option>
                <option value="3600">1 hour</option>
                <option value="14400">4 hours</option>
                <option value="86400">1 day</option>
            </select>
            
            <button id="connectButton">Connect</button>
            <button id="subscribeButton" disabled>Subscribe</button>
            <button id="unsubscribeButton" disabled>Unsubscribe</button>
			<button id='historyButton' class='mBtn'>historyButton</button>
            <button id="clearButton">Clear Data</button>
        </div>

		<div id="price-chart" style='height: 300px; margin-bottom: 120px;'>
		</div>

        <div class="data-container">
            <table id="candlestickTable">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Open</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Close</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody id="candlestickData">
                    <!-- Data will be inserted here -->
                </tbody>
            </table>
        </div>
		<textarea id="candleData" rows="" cols="" style='width:100%;hight:150px;margin-top:20px'></textarea>
    </div>

    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statusElement = document.getElementById('connectionStatus');
            const connectButton = document.getElementById('connectButton');
            const subscribeButton = document.getElementById('subscribeButton');
            const unsubscribeButton = document.getElementById('unsubscribeButton');
			const historyButton = document.getElementById('historyButton');
            const clearButton = document.getElementById('clearButton');
            const appIdInput = document.getElementById('appId');
            const symbolSelect = document.getElementById('symbol');
            const granularitySelect = document.getElementById('granularity');
            const dataTableBody = document.getElementById('candlestickData');
            
            let derivFetcher = null;
            let currentSymbol = null;
            
            // Update UI status
            function updateConnectionStatus(status, message) {
                statusElement.textContent = `Status: ${message}`;
                statusElement.className = `status ${status}`;
                
                if (status === 'connected') {
                    connectButton.textContent = 'Disconnect';
                    subscribeButton.disabled = false;
                    unsubscribeButton.disabled = true;
                } else {
                    connectButton.textContent = 'Connect';
                    subscribeButton.disabled = true;
                    unsubscribeButton.disabled = true;
                }
            }
            
            // Format timestamp
            function formatTime(timestamp) {
                const date = new Date(timestamp * 1000);
                return date.toLocaleString();
            }
            
            // Add row to table
            function addCandlestickRow(time, open, high, low, close, isUpdate = false) {
                const row = document.createElement('tr');
                
                // Determine if bullish or bearish
                const cssClass = parseFloat(close) >= parseFloat(open) ? 'bullish' : 'bearish';
                const type = parseFloat(close) >= parseFloat(open) ? 'Bullish' : 'Bearish';
                
                // Format the row content
                row.innerHTML = `
                    <td>${formatTime(time)}</td>
                    <td>${parseFloat(open).toFixed(5)}</td>
                    <td>${parseFloat(high).toFixed(5)}</td>
                    <td>${parseFloat(low).toFixed(5)}</td>
                    <td class="${cssClass}">${parseFloat(close).toFixed(5)}</td>
                    <td class="${cssClass}">${type}</td>
                `;
                
                if (isUpdate) {
                    dataTableBody.insertBefore(row, dataTableBody.firstChild);
                } else {
                    dataTableBody.appendChild(row);
                }
            }
            
            // Process candlestick data
            function processCandlestickData(data) {
                if (data.msg_type === 'candles') {
					document.getElementById("candleData").value = JSON.stringify(data);
                    // Clear existing data for new history
                    dataTableBody.innerHTML = '';
					

                    
                    // Process historical data
                    const candles = data.candles || [];
                    candles.forEach(candle => {
                        addCandlestickRow(candle.epoch, candle.open, candle.high, candle.low, candle.close);
                    });

					let candle2 = [];

					let tmp = null ;
					for (let i=0;i<=data.candles.length-1 ;i++ ) {
					   tmp = { 
						   time : data.candles[i].epoch,  // ใช้ epoch โดยตรง
                           open : data.candles[i].open,
                           high : data.candles[i].high,
                           low  : data.candles[i].low,
                           close: data.candles[i].close
					   }
                       candle2.push(tmp);
					} 

//					mainCreateIndy(candle2);

					AllIndy = MainCallAllIndy(candle2) ;
					console.log('allIndy',AllIndy);
					


					const priceChartContainer = document.getElementById('price-chart');
                    const priceChart = createPriceChart(priceChartContainer, candle2);


                } else if (data.msg_type === 'ohlc') {
                    // Process real-time updates
                    const ohlc = data.ohlc;
                    addCandlestickRow(ohlc.epoch, ohlc.open, ohlc.high, ohlc.low, ohlc.close, true);
                }
            }
            
            // Connect button handler
            connectButton.addEventListener('click', () => {
                if (derivFetcher && derivFetcher.isConnected) {
                    // Disconnect
                    derivFetcher.disconnect();
                    derivFetcher = null;
                    updateConnectionStatus('disconnected', 'Disconnected');
                } else {
                    // Connect
                    const appId = appIdInput.value.trim();
                    
                    if (!appId) {
                        alert('Please enter a valid App ID');
                        return;
                    }
                    
                    derivFetcher = new DerivCandlestickFetcher(appId, {
                        onConnect: () => {
                            updateConnectionStatus('connected', 'Connected');
                        },
                        onDisconnect: () => {
                            updateConnectionStatus('disconnected', 'Disconnected');
                        },
                        onError: (error) => {
                            console.error('Error:', error);
                        },
                        onReconnecting: (attempt, max) => {
                            updateConnectionStatus('reconnecting', `Reconnecting... (${attempt}/${max})`);
                        }
                    });
                }
            });
            
            // Subscribe button handler
            subscribeButton.addEventListener('click', () => {
                if (!derivFetcher || !derivFetcher.isConnected) {
                    return;
                }
                
                const symbol = symbolSelect.value;
                const granularity = parseInt(granularitySelect.value);
                
                if (currentSymbol) {
                    derivFetcher.unsubscribeFromCandlesticks(currentSymbol);
                }
                
                derivFetcher.subscribeToCandlesticks(symbol, granularity, processCandlestickData);
                currentSymbol = symbol;
                
                unsubscribeButton.disabled = false;
                subscribeButton.disabled = true;
            });
            
            // Unsubscribe button handler
            unsubscribeButton.addEventListener('click', () => {
                if (!derivFetcher || !currentSymbol) {
                    return;
                }
                
                derivFetcher.unsubscribeFromCandlesticks(currentSymbol);
                currentSymbol = null;
                
                unsubscribeButton.disabled = true;
                subscribeButton.disabled = false;
            });

           //historyButton
           historyButton.addEventListener('click', () => {
			    
                if (!derivFetcher ) {
					alert('Return');
                    return;
                } 
				
				const symbol = symbolSelect.value;
                const granularity = parseInt(granularitySelect.value);
                
                if (currentSymbol) {
                    derivFetcher.unsubscribeFromCandlesticks(currentSymbol);
                }
                
                derivFetcher.subscribeToCandlesticks(symbol, granularity, processCandlestickData);
                currentSymbol = symbol;
                
                unsubscribeButton.disabled = false;
                subscribeButton.disabled = true;
				
                
                
            });

            
            // Clear button handler
            clearButton.addEventListener('click', () => {
                dataTableBody.innerHTML = '';
            });
        });
    </script>

<script>

function createPriceChart(container, data) {

         const chart = LightweightCharts.createChart(container, {
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
				secondsVisible: true,  // เพิ่มตัวเลือกนี้
				borderColor: '#D1D4DC',
				tickMarkFormatter: (time) => {
                  // แปลง timestamp เป็นเวลาที่อ่านได้
                  const date = new Date(time * 1000);
                  return date.toLocaleTimeString();
                }
	            },
                rightPriceScale: {
                    borderColor: '#D1D4DC',
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
            });

            const candleSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });


            candleSeries.setData(data);
            chart.timeScale().fitContent();

			

            return { chart, series: candleSeries };

} // end func createPriceChart

function convertDataForTradingView(data) {
            return data.map(candle => ({
                time: typeof candle.time === 'number' ? formatTimeFromEpoch(candle.time) : candle.time,
                open: candle.open,
                high: candle.high,
                low: candle.low,
                close: candle.close
            }));
}

function formatTimeFromEpoch(epoch) {
            const date = new Date(epoch * 1000);
            return date.toISOString().split('T')[0]; // Format: 'YYYY-MM-DD'
 }

function convertDataForTradingView2(data) {

            return data.map(candle => (
				{
                time: candle.epoch,
                open: candle.open,
                high: candle.high,
                low: candle.low,
                close: candle.close
            }));
}

window.onload = function() { 
    

	// Convert data for TradingView format
	 

   

 
  
            


}


 


</script>

</body>
</html>