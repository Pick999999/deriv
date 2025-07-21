// Global variables
let chart;
let candleSeries;
let bollingerBands;
let selectedAsset = '';
let candles = [];
let ws = null;
let lastTick = null;
let currentCandle = null;

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initChart();
    initStrengthMeter();
    connectWebSocket();
    startServerTimeUpdates();
});

// Initialize WebSocket connection to Deriv API
function connectWebSocket() {
    ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
    
    ws.onopen = function(evt) {
        console.log('WebSocket connection opened');
        // Once connected, get active symbols
        getActiveSymbols();
    };
    
    ws.onmessage = function(msg) {
        const response = JSON.parse(msg.data);
        
        // Handle different types of responses
        if (response.msg_type === 'active_symbols') {
            handleActiveSymbols(response.active_symbols);
        } else if (response.msg_type === 'tick') {
            handleTick(response);
        } else if (response.msg_type === 'ohlc') {
            handleOHLC(response);
        } else if (response.msg_type === 'candles') {
            handleCandles(response);
        } else if (response.msg_type === 'history') {
            handleHistory(response);
        } else if (response.error) {
            console.error('API Error:', response.error.message);
        }
    };
    
    ws.onclose = function() {
        console.log('WebSocket connection closed');
        // Try to reconnect after a delay
        setTimeout(connectWebSocket, 5000);
    };
    
    ws.onerror = function(err) {
        console.error('WebSocket error:', err);
    };
}

// Request active symbols (available assets)
function getActiveSymbols() {
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            active_symbols: 'brief',
            product_type: 'basic'
        }));
    }
}

// Handle active symbols response and populate the asset select dropdown
function handleActiveSymbols(symbols) {
    const assetSelect = document.getElementById('assetSelect');
    assetSelect.innerHTML = '';
    
    // Filter and organize symbols
    const tradableSymbols = symbols.filter(symbol => 
        symbol.exchange_is_open && 
        symbol.submarket !== 'random_index' // Filter out synthetic indices if needed
    );
    
    // Sort by market and display_name
    tradableSymbols.sort((a, b) => {
        if (a.market < b.market) return -1;
        if (a.market > b.market) return 1;
        return a.display_name.localeCompare(b.display_name);
    });
    
    // Group options by market
    const markets = {};
    tradableSymbols.forEach(symbol => {
        if (!markets[symbol.market_display_name]) {
            markets[symbol.market_display_name] = [];
        }
        markets[symbol.market_display_name].push(symbol);
    });
    
    // Create option groups for each market
    for (const marketName in markets) {
        const optgroup = document.createElement('optgroup');
        optgroup.label = marketName;
        
        markets[marketName].forEach(symbol => {
            const option = document.createElement('option');
            option.value = symbol.symbol;
            option.textContent = symbol.display_name;
            optgroup.appendChild(option);
        });
        
        assetSelect.appendChild(optgroup);
    }
    
    // Select the first symbol by default
    if (tradableSymbols.length > 0) {
        selectedAsset = tradableSymbols[0].symbol;
        assetSelect.value = selectedAsset;
        
        // Subscribe to OHLC for the selected symbol
        loadCandles(selectedAsset);
    }
    
    // Add event listener for asset selection change
    assetSelect.addEventListener('change', function() {
        selectedAsset = this.value;
        loadCandles(selectedAsset);
    });
}

// Load candle data for selected asset
function loadCandles(symbol) {
    // Unsubscribe from previous symbol ticks and candles if any
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            forget_all: ['ticks', 'candles']
        }));
        
        // Request historical candles
        ws.send(JSON.stringify({
            ticks_history: symbol,
            count: 30,
            end: 'latest',
            style: 'candles',
            granularity: 60 // 1-minute candles
        }));
        
        // Subscribe to OHLC updates
        ws.send(JSON.stringify({
            ohlc: 1,
            ticks_history: symbol,
            style: 'candles',
            granularity: 60,
            subscribe: 1
        }));
        
        // Subscribe to tick updates as well for real-time price updates
        ws.send(JSON.stringify({
            ticks: symbol,
            subscribe: 1
        }));
    }
}

// Handle historical candles data
function handleHistory(response) {
    if (response.history && response.history.candles) {
        // Convert API candles format to our chart format
        candles = response.history.candles.map(candle => ({
            time: candle.epoch,
            open: parseFloat(candle.open),
            high: parseFloat(candle.high),
            low: parseFloat(candle.low),
            close: parseFloat(candle.close)
        }));
        
        // Update chart with historical data
        updateChart(candles);
    }
}

// Handle candles update from subscription
function handleCandles(response) {
    if (response.candles) {
        // Get the latest candle
        const latestCandle = response.candles[response.candles.length - 1];
        
        // Update or add to our candles array
        const existingIndex = candles.findIndex(c => c.time === latestCandle.epoch);
        
        if (existingIndex >= 0) {
            // Update existing candle
            candles[existingIndex] = {
                time: latestCandle.epoch,
                open: parseFloat(latestCandle.open),
                high: parseFloat(latestCandle.high),
                low: parseFloat(latestCandle.low),
                close: parseFloat(latestCandle.close)
            };
        } else {
            // Add new candle and remove oldest if we have more than 30
            candles.push({
                time: latestCandle.epoch,
                open: parseFloat(latestCandle.open),
                high: parseFloat(latestCandle.high),
                low: parseFloat(latestCandle.low),
                close: parseFloat(latestCandle.close)
            });
            
            if (candles.length > 30) {
                candles.shift();
            }
        }
        
        // Update chart with new data
        updateChart(candles);
    }
}

// Handle OHLC updates
function handleOHLC(response) {
    if (response.ohlc) {
        const ohlc = response.ohlc;
        currentCandle = {
            time: parseInt(ohlc.epoch),
            open: parseFloat(ohlc.open),
            high: parseFloat(ohlc.high),
            low: parseFloat(ohlc.low),
            close: parseFloat(ohlc.close)
        };
        
        // Update the last candle or add new one
        const lastIndex = candles.length - 1;
        if (lastIndex >= 0 && candles[lastIndex].time === currentCandle.time) {
            candles[lastIndex] = currentCandle;
        } else {
            candles.push(currentCandle);
            if (candles.length > 30) {
                candles.shift();
            }
        }
        
        // Update chart
        updateChart(candles);
    }
}

// Handle tick data
function handleTick(response) {
    if (response.tick) {
        lastTick = {
            time: parseInt(response.tick.epoch),
            price: parseFloat(response.tick.quote)
        };
        
        // If we have a current candle, update it with the latest tick
        if (currentCandle && currentCandle.time === Math.floor(lastTick.time / 60) * 60) {
            // Update high/low/close based on tick
            currentCandle.high = Math.max(currentCandle.high, lastTick.price);
            currentCandle.low = Math.min(currentCandle.low, lastTick.price);
            currentCandle.close = lastTick.price;
            
            // Find and update the current candle in our array
            const currentIndex = candles.findIndex(c => c.time === currentCandle.time);
            if (currentIndex >= 0) {
                candles[currentIndex] = currentCandle;
                // Only update chart on every 2nd tick to avoid too frequent updates
                if (lastTick.time % 2 === 0) {
                    updateChart(candles);
                }
            }
        }
    }
}

// Initialize chart with Bollinger Bands
function initChart() {
    chart = LightweightCharts.createChart(document.getElementById('chart'), {
        width: document.querySelector('.chart-container').clientWidth,
        height: 400,
        layout: {
            backgroundColor: '#ffffff',
            textColor: '#333',
        },
        grid: {
            vertLines: {
                color: '#f0f0f0',
            },
            horzLines: {
                color: '#f0f0f0',
            },
        },
        crosshair: {
            mode: LightweightCharts.CrosshairMode.Normal,
        },
        rightPriceScale: {
            borderColor: '#d1d4dc',
        },
        timeScale: {
            borderColor: '#d1d4dc',
            timeVisible: true,
            secondsVisible: false,
        },
    });

    // Create candlestick series
    candleSeries = chart.addCandlestickSeries({
        upColor: '#26a69a',
        downColor: '#ef5350',
        borderVisible: false,
        wickUpColor: '#26a69a',
        wickDownColor: '#ef5350',
    });

    // Add Bollinger Bands
    bollingerBands = chart.addLineSeries({
        color: 'rgba(38, 166, 154, 0.7)',
        lineWidth: 1,
        priceLineVisible: false,
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        chart.applyOptions({
            width: document.querySelector('.chart-container').clientWidth
        });
    });
}

// Initialize the strength meter gauge
function initStrengthMeter() {
    const canvas = document.getElementById('strengthMeter');
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height;
    const radius = Math.min(canvas.width, canvas.height) * 0.8;

    // Initial draw
    drawStrengthMeter(ctx, centerX, centerY, radius, 0);
}

// Draw the strength meter with the given value (0-100)
function drawStrengthMeter(ctx, centerX, centerY, radius, value) {
    // Clear canvas
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
    
    // Calculate angle based on value (0-100)
    const startAngle = Math.PI;
    const endAngle = 0;
    const valueAngle = startAngle - ((value / 100) * (startAngle - endAngle));
    
    // Draw background arc
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, startAngle, endAngle);
    ctx.lineWidth = 20;
    ctx.strokeStyle = '#e0e0e0';
    ctx.stroke();
    
    // Draw value arc
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, startAngle, valueAngle);
    
    // Color based on value
    let color;
    if (value < 30) {
        color = '#ef5350'; // Red for weak
    } else if (value < 70) {
        color = '#ffb74d'; // Orange for medium
    } else {
        color = '#26a69a'; // Green for strong
    }
    
    ctx.strokeStyle = color;
    ctx.stroke();
    
    // Draw needle
    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    const needleLength = radius * 0.9;
    ctx.lineTo(
        centerX + needleLength * Math.cos(valueAngle),
        centerY + needleLength * Math.sin(valueAngle)
    );
    ctx.lineWidth = 3;
    ctx.strokeStyle = '#333';
    ctx.stroke();
    
    // Draw center circle
    ctx.beginPath();
    ctx.arc(centerX, centerY, 10, 0, 2 * Math.PI);
    ctx.fillStyle = '#333';
    ctx.fill();
    
    // Update the value text
    document.getElementById('strengthValue').textContent = Math.round(value);
}

// Update chart with candle data and calculate Bollinger Bands
function updateChart(candles) {
    // Update candlestick series
    candleSeries.setData(candles);
    
    // Calculate and update Bollinger Bands
    const bollingerData = calculateBollingerBands(candles);
    
    const upperBand = bollingerData.map(item => ({
        time: item.time,
        value: item.upper
    }));
    
    const middleBand = bollingerData.map(item => ({
        time: item.time,
        value: item.middle
    }));
    
    const lowerBand = bollingerData.map(item => ({
        time: item.time,
        value: item.lower
    }));
    
    // Update Bollinger Bands series
    bollingerBands.setData(upperBand);
    
    // Add middle and lower bands
    const middleBandSeries = chart.addLineSeries({
        color: 'rgba(125, 125, 125, 0.7)',
        lineWidth: 1,
        priceLineVisible: false,
    });
    
    const lowerBandSeries = chart.addLineSeries({
        color: 'rgba(38, 166, 154, 0.7)',
        lineWidth: 1,
        priceLineVisible: false,
    });
    
    middleBandSeries.setData(middleBand);
    lowerBandSeries.setData(lowerBand);
    
    // Calculate signal strength based on current price relative to Bollinger Bands
    const lastCandle = candles[candles.length - 1];
    const lastBollinger = bollingerData[bollingerData.length - 1];
    
    if (lastCandle && lastBollinger) {
        const price = lastCandle.close;
        const range = lastBollinger.upper - lastBollinger.lower;
        
        let strength;
        if (price > lastBollinger.upper) {
            // Price above upper band - strong buy signal
            strength = 80 + (price - lastBollinger.upper) / range * 20;
        } else if (price < lastBollinger.lower) {
            // Price below lower band - strong sell signal
            strength = 20 - (lastBollinger.lower - price) / range * 20;
        } else {
            // Price within bands - moderate signal
            strength = 50 + ((price - lastBollinger.middle) / (range / 2)) * 30;
        }
        
        // Ensure strength is between 0 and 100
        strength = Math.max(0, Math.min(100, strength));
        
        // Update strength meter
        updateStrengthMeter(strength);
    }
}

// Calculate Bollinger Bands (20-period SMA with 2 standard deviations)
function calculateBollingerBands(candles) {
    const period = 20;
    const multiplier = 2;
    const result = [];
    
    for (let i = 0; i < candles.length; i++) {
        if (i < period - 1) {
            result.push({
                time: candles[i].time,
                upper: null,
                middle: null,
                lower: null
            });
            continue;
        }
        
        // Calculate SMA
        let sum = 0;
        for (let j = i - period + 1; j <= i; j++) {
            sum += candles[j].close;
        }
        const sma = sum / period;
        
        // Calculate standard deviation
        let squareSum = 0;
        for (let j = i - period + 1; j <= i; j++) {
            squareSum += Math.pow(candles[j].close - sma, 2);
        }
        const std = Math.sqrt(squareSum / period);
        
        result.push({
            time: candles[i].time,
            upper: sma + multiplier * std,
            middle: sma,
            lower: sma - multiplier * std
        });
    }
    
    return result;
}

// Update strength meter with new value
function updateStrengthMeter(value) {
    const canvas = document.getElementById('strengthMeter');
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height;
    const radius = Math.min(canvas.width, canvas.height) * 0.8;
    
    drawStrengthMeter(ctx, centerX, centerY, radius, value);
}

// Start server time updates (every second)
function startServerTimeUpdates() {
    updateServerTime();
    setInterval(updateServerTime, 1000);
}

// Update server time display
function updateServerTime() {
    const now = new Date();
    document.getElementById('serverTime').textContent = 'Server Time: ' + 
        now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
    
    // Check if it's time to send data to the endpoint (at the start of each minute)
    if (now.getSeconds() === 0) {
        //sendCandleData();
    }
}

// Send candle data to endpoint
async function sendCandleData() {
    try {
        const response = await fetch('https://thepapers.com/abc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                asset: selectedAsset,
                candles: candles
            })
        });
        
        if (response.ok) {
            const data = await response.json();
            console.log('Received response from endpoint:', data);
            
            // Process the response data if needed
        } else {
            console.error('Error from endpoint:', response.statusText);
        }
    } catch (error) {
        console.error('Error sending data to endpoint:', error);
    }
}