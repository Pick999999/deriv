class DerivCandlestickFetcher {
            constructor() {
                this.socket = null;
				this.chart = new CandleStickChartWithEMA('chart-container');
                this.intervalId = null;
                this.timeUpdateInterval = null;
                this.candles = [];
                this.symbol = 'R_100';
                this.timeframe = '60';
                this.isConnected = false;
                this.serverTimeOffset = 0; // Difference between server time and local time in seconds
                
                // DOM elements
                this.connectBtn = document.getElementById('connect-btn');
                this.disconnectBtn = document.getElementById('disconnect-btn');
                this.symbolSelect = document.getElementById('symbol-select');
                this.timeframeSelect = document.getElementById('timeframe-select');
                this.statusDiv = document.getElementById('status');
                this.tableBody = document.getElementById('table-body');
                this.serverTimeDiv = document.getElementById('server-time');
                
                // Event listeners
                this.connectBtn.addEventListener('click', () => this.connect());
                this.disconnectBtn.addEventListener('click', () => this.disconnect());
                this.symbolSelect.addEventListener('change', (e) => {
                    this.symbol = e.target.value;
                    if (this.isConnected) {
                        this.disconnect();
                        this.connect();
                    }
                });
                this.timeframeSelect.addEventListener('change', (e) => {
                    this.timeframe = e.target.value;
                    if (this.isConnected) {
                        this.disconnect();
                        this.connect();
                    }
                });
            }
            
            connect() {
                if (this.isConnected) return;
                
                this.socket = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
                
                this.socket.onopen = () => {
                    this.isConnected = true;
                    this.updateStatus('Connected', true);
                    this.subscribeToCandles();
                    this.requestServerTime();
                    
                    // Start auto-update every 2 seconds
                    this.intervalId = setInterval(() => {
                        this.updateTable();
                    }, 2000);
                    
                    // Start server time updates every second
                    this.timeUpdateInterval = setInterval(() => {
                        this.updateServerTimeDisplay();
                    }, 1000);
                };
                
                this.socket.onmessage = (msg) => {
                    const data = JSON.parse(msg.data);
					document.getElementById("resType").innerHTML = data.msg_type;
                    if (data.msg_type === 'candles') {
                        this.processCandles(data.candles);
                    } else if (data.msg_type === 'ohlc') {
                        this.processOHLC(data.ohlc);
                    } else if (data.msg_type === 'time') {
                        this.processServerTime(data.time);
                    }
                };
                
                this.socket.onclose = () => {
                    this.isConnected = false;
                    this.updateStatus('Disconnected', false);
                    clearInterval(this.intervalId);
                    clearInterval(this.timeUpdateInterval);
                };
                
                this.socket.onerror = (error) => {
                    console.error('WebSocket error:', error);
                    this.updateStatus('Connection error', false);
                    clearInterval(this.timeUpdateInterval);
                };
            }
            
            disconnect() {
                if (this.socket) {
                    this.socket.close();
                }
                clearInterval(this.intervalId);
                clearInterval(this.timeUpdateInterval);
                this.isConnected = false;
                this.updateStatus('Disconnected', false);
                this.serverTimeDiv.textContent = 'Server Time: --:--:--';
            }
            
            subscribeToCandles() {
                const request = {
                    "ticks_history": this.symbol,
                    "end": "latest",
                    "start": 1,
                    "style": "candles",
                    "granularity": parseInt(this.timeframe),
                    "subscribe": 1
                };
                
                this.socket.send(JSON.stringify(request));
            }
            
            requestServerTime() {
                const request = {
                    "time": 1
                };
                this.socket.send(JSON.stringify(request));
            }
            
            processServerTime(serverTimestamp) {
                // Calculate the difference between server time and local time
                const localTime = Math.floor(Date.now() / 1000);
                this.serverTimeOffset = serverTimestamp - localTime;
                
                // Update the display immediately
                this.updateServerTimeDisplay();
            }
            
            updateServerTimeDisplay() {
                if (this.serverTimeOffset === 0) return;
                
                const serverTime = new Date((Math.floor(Date.now() / 1000) + this.serverTimeOffset) * 1000);
                const hours = serverTime.getHours().toString().padStart(2, '0');
                const minutes = serverTime.getMinutes().toString().padStart(2, '0');
                const seconds = serverTime.getSeconds().toString().padStart(2, '0');
                
                this.serverTimeDiv.textContent = `Server Time: ${hours}:${minutes}:${seconds}`;
            }
            
            processCandles(candlesData) {
                if (!candlesData || !Array.isArray(candlesData)) return;
                
                this.candles = candlesData.map(candle => ({
                    time: this.formatTime(candle.epoch),
                    open: parseFloat(candle.open),
                    high: parseFloat(candle.high),
                    low: parseFloat(candle.low),
                    close: parseFloat(candle.close),
                    volume: candle.volume || 0
                }));
                
                this.updateTable();
            }
            
             
			 processOHLC(ohlcData) {
               if (!ohlcData) return;
				// Convert ohlcData epoch to minutes (floor to minute precision)
				const ohlcMinute = Math.floor(ohlcData.epoch / 60);				
				if (this.candles.length === 0) {
					// If no candles exist, create the first one
					this.candles.push({
						time: this.formatTime(ohlcData.epoch),
						open: parseFloat(ohlcData.open),
						high: parseFloat(ohlcData.high),
						low: parseFloat(ohlcData.low),
						close: parseFloat(ohlcData.close),
						volume: 0
					});
					return;
				}

				const latestCandle = this.candles[this.candles.length - 1];
				const latestCandleMinute = Math.floor(new Date(latestCandle.time).getTime() / 1000 / 60);				
				if (ohlcMinute > latestCandleMinute) {
					// New minute - create a new candle
					this.candles.push({
						time: this.formatTime(ohlcData.epoch),
						open: parseFloat(ohlcData.open),
						high: parseFloat(ohlcData.high),
						low: parseFloat(ohlcData.low),
						close: parseFloat(ohlcData.close),
						volume: 0
					});
					
					// Keep only the most recent 1000 candles (adjust as needed)
					if (this.candles.length > 1000) {
						this.candles.shift();
					}
				} else {
					// Same minute - update the current candle
					latestCandle.high = Math.max(latestCandle.high, parseFloat(ohlcData.high));
					latestCandle.low = Math.min(latestCandle.low, parseFloat(ohlcData.low));
					latestCandle.close = parseFloat(ohlcData.close);
				}
				
				// Update the table immediately when we get new data
				this.updateTable();
            } // end processOHLC
            
            formatTime(timestamp) {
                const date = new Date(timestamp * 1000);
                return date.toLocaleString();
            }
            
            updateTable() {
                if (this.candles.length === 0) return;
                
                // Sort candles by time (newest first)
                const sortedCandles = [...this.candles].sort((a, b) => {
                    return new Date(b.time) - new Date(a.time);
                });
                
                // Limit to 100 candles for display
                const displayCandles = sortedCandles.slice(0, 100);
				this.chart.updateChart(displayCandles);
                
                // Clear table
                this.tableBody.innerHTML = '';
                
                // Add rows
                displayCandles.forEach(candle => {
                    const row = document.createElement('tr');
                    
                    row.innerHTML = `
                        <td>${candle.time}</td>
                        <td>${candle.open.toFixed(4)}</td>
                        <td>${candle.high.toFixed(4)}</td>
                        <td>${candle.low.toFixed(4)}</td>
                        <td>${candle.close.toFixed(4)}</td>
                        <td>${candle.volume}</td>
                    `;
                    
                    this.tableBody.appendChild(row);
                });
            }
            
            updateStatus(message, isConnected) {
                this.statusDiv.textContent = message;
                this.statusDiv.className = `status ${isConnected ? 'connected' : 'disconnected'}`;
            }

 }  // end class