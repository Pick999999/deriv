<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candle Data Fetcher</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightweight-charts/4.1.1/lightweight-charts.standalone.production.js"></script>

	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>  
	<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

	
	<link href="css/newlabV2.css" rel="stylesheet">


<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped&family=Playfair+Display:ital@1&family=Sarabun:wght@200&display=swap" rel="stylesheet">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped&family=Noto+Sans+Thai:wght@200&family=Playfair+Display:ital@1&family=Sarabun:wght@200&display=swap" rel="stylesheet">

<style>

.sarabun : { font-family: 'Sarabun', sans-serif; }


* { .sarabun }
</style>

</head>
<body class='sarabun'>
    <div class="container">
        <h1>🕯️ Deriv Candle Data Fetcher</h1>
        
        <div class="controls">
            <div class="control-group">
                <label for="assetSelect">📊 เลือก Asset:</label>
                <select id="assetSelect">
                    <option value="">กำลังโหลด...</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="datePickerA">📅 เลือกวันที่:</label>
                <input type="date" id="datePickerA">
            </div>
			<div class="control-group">
                <label for="timePickerB">🕐 Hour:</label>
                <select id="setHour" onchange='setHour(this.value)'>
				 <?php
				   for ($i=0;$i<=23;$i++) { ?>
				     <option value="<?=$i?>" selected><?=$i;?>  
				   <?php }
				 ?>					
                </select>
				<div id="hourText" class="bordergray flex" style='color:#ff0080'>
				     
				</div>
            </div>
            
            <div class="control-group">
                <label for="timePickerB">🕐 เวลาเริ่มต้น:</label>
                <input type="time" id="timePickerB" value="00:00">
            </div>
            
            <div class="control-group">
                <label for="timePickerC">🕐 เวลาสิ้นสุด:</label>
                <input type="time" id="timePickerC" value="23:59">
            </div>
            
            <div class="control-group">
                <label for="timeframe">⏱️ Timeframe (นาที):</label>
                <select id="timeframe">
                    <option value="60" selected>1 นาที</option>
                    <option value="180">3 นาที</option>
                    <option value="300">5 นาที</option>
                    <option value="600">10 นาที</option>
                    <option value="900">15 นาที</option>
                    <option value="1800">30 นาที</option>
                    <option value="3600" >60 นาที</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="candleCount">🕯️ จำนวนแท่ง:</label>
                <input type="number" id="candleCount" min="1" max="1000" value="60">
            </div>
        </div>

        <div class="button-group">
            <button id="fetchHistorical">📈 ดึงข้อมูลตามวันที่</button>
            <button id="fetchCurrent">🔄 ดึงข้อมูลปัจจุบัน</button>
            <button id="connectSocket">🔌 เชื่อมต่อ Socket</button>
            <button id="disconnectSocket">❌ ตัดการเชื่อมต่อ</button>

			<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
              ดูสถิติ การ Trade
            </button>
        </div>

        <div id="status" class="status"></div>
        <div id="loading" class="loading">
            <div class="spinner"></div>
            <p>กำลังดึงข้อมูล...</p>
        </div>

        <div class="response-section">
            <h3>📄 Raw Data Response:</h3>
            <textarea id="responseData" placeholder="ข้อมูล response จะแสดงที่นี่..."></textarea>
        </div>

        <div class="response-section">
            <h3>🌐 Response จาก hdlightc.com:</h3>
            <div id="hdlightcResponse">รอข้อมูล...</div>
        </div>

        <div class="chart-container">
            <h3 id="chartTitle">📊 Candlestick Chart with EMA</h3>
            <div class="ema-controls">
                <label for="emaShort">EMA Short:</label>
                <input type="number" id="emaShort" min="1" max="200" value="3">
                
                <label for="emaLong">EMA Long:</label>
                <input type="number" id="emaLong" min="1" max="200" value="5">
                
                <button id="updateEMA">🔄 อัพเดท EMA</button>
				 Candle Time Selected&nbsp;&nbsp;
				 <input type="text" id="timeInputSelected" style='width:150px' 
				 onchange= 'aaaaa()'>
				 <span id='timeDisplay'></span>
				 <button type='button' id='btnCalTime' class='mBtn' onclick="calDisplayTime222()">calDisplayTime2</button>
				 <button type='button' id='' class='mBtn' onclick="doAjaxGetAction()">Get Action</button>
				 <button type='button' id='' class='mBtn' onclick="doAjaxCalWinById()">Cal Win ByID</button>

				 <button type='button' id='' class='mBtn' onclick="doAjaxCalAll()">Cal ALL</button>
				 
            </div>
			<div id="actionResult" class="bordergray flex" style='padding:8px;height:auto'>
				--
			</div>
            <div id="chart"></div>
			<div id="rsiChart" style="height:300px;margin-top: 60px;"></div>
        </div>
    </div>
	<?php
	  ModalForm1();
	?>

    <script>
        class DerivCandleFetcher {
            constructor() {
                this.ws = null;
                this.chart = null;
                this.candleSeries = null;
                this.emaShortSeries = null;
                this.emaLongSeries = null;
                this.candlesData = [];
                this.isConnected = false;
				this.marker = [] ;
                
                this.initChart();
                this.bindEvents();
                this.loadAssets();
                this.setDefaultDate();
            }

			addMarkers(sTime,caption) {
			   const markerTmp = {
                 time: sTime, // The time point where the marker should appear
                 position: 'aboveBar', // 'aboveBar' or 'belowBar'
                 color: 'blue',
                 shape: 'circle', // 'circle', 'square', 'arrowUp', 'arrowDown'
                 size: 2, // Optional: size of the marker
                 text: caption, // Optional: text to display next to the marker
               };
		       //console.log(markerTmp) ;
			   this.marker = [];
               this.marker.push(markerTmp) ;
               // Add the marker(s) to the candlestick series
               //this.candleSeries.setMarkers(marker);
	           //this.candleSeries.setMarkers([marker]);
			   this.candleSeries.setMarkers(this.marker);
               
			}
            
            initChart() {
                const chartContainer = document.getElementById('chart');
                this.chart = LightweightCharts.createChart(chartContainer, {
                    width: chartContainer.clientWidth,
                    height: 500,
                    layout: {
                        background: { color: '#ffffff' },
                        textColor: '#333',
                    },
                    grid: {
                        vertLines: { color: '#f0f0f0' },
                        horzLines: { color: '#f0f0f0' },
                    },
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                    },
                    timeScale: {
                        borderColor: '#cccccc',
                        timeVisible: true,
                        secondsVisible: false,
                    },
                });
                
                this.candleSeries = this.chart.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderVisible: false,
                    wickUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                });
                
                this.emaShortSeries = this.chart.addLineSeries({
                    color: '#2196F3',
                    lineWidth: 2,
                    title: 'EMA Short',
                });
                
                this.emaLongSeries = this.chart.addLineSeries({
                    color: '#ff0000',
                    lineWidth: 2,
                    title: 'EMA Long',
                }); 

				this.rsiChart = LightweightCharts.createChart(document.getElementById('rsiChart'), {
					width: chartContainer.clientWidth,
					height: 250,
					layout: { background: { color: '#ffffff' }, textColor: '#333' },
					grid: { vertLines: { color: '#f0f0f0' }, horzLines: { color: '#f0f0f0' } },
					timeScale: { borderColor: '#cccccc', timeVisible: true, secondsVisible: false },
				});

				this.rsiSeries = this.rsiChart.addLineSeries({
					color: '#9C27B0',
					lineWidth: 2,
					title: 'RSI',
				});

				// Add RSI reference lines
				this.rsiChart.addLineSeries({
					color: '#FF5722',
					lineWidth: 1,
					lineStyle: LightweightCharts.LineStyle.Dashed,
					priceLineVisible: false,
				}).setData([{ time: 0, value: 70 }, { time: 9999999999, value: 70 }]);

				this.rsiChart.addLineSeries({
					color: '#4CAF50',
					lineWidth: 1,
					lineStyle: LightweightCharts.LineStyle.Dashed,
					priceLineVisible: false,
				}).setData([{ time: 0, value: 30 }, { time: 9999999999, value: 30 }]);			


                
                // Handle resize
                new ResizeObserver(entries => {
                    if (entries.length === 0 || entries[0].target !== chartContainer) return;
                    const { width, height } = entries[0].contentRect;
                    this.chart.applyOptions({ width, height });
                }).observe(chartContainer);
            } 

			
            
            bindEvents() {
                document.getElementById('connectSocket').onclick = () => this.connectSocket();
                document.getElementById('disconnectSocket').onclick = () => this.disconnectSocket();
                document.getElementById('fetchHistorical').onclick = () => this.fetchHistoricalData();
                document.getElementById('fetchCurrent').onclick = () => this.fetchCurrentData();
                document.getElementById('updateEMA').onclick = () => this.updateEMA();
                
                // Auto-update EMA when values change
                document.getElementById('emaShort').oninput = () => this.updateEMA();
                document.getElementById('emaLong').oninput = () => this.updateEMA();
                
                // Auto-fetch data when asset or timeframe changes
                document.getElementById('assetSelect').onchange = () => this.autoFetchData();
                document.getElementById('timeframe').onchange = () => this.autoFetchData();

				/*document.getElementById('timeInputSelected').onchange = () => this.calDisplayTime2();
				*/

				this.chart.subscribeClick((param) => {
					document.getElementById("actionResult").innerHTML = '';
					if (!param || !param.time) {
					  return; // ถ้าไม่มี time ใน param ให้ออก
					} 
					const timeInput = document.getElementById('timeInputSelected');

					// ดึง time จาก candlestick ที่คลิก
					const clickedTime = parseInt(param.time);
					timeInput.value = clickedTime; // ใส่ time ใน text box
					
					aaaaa();
					let date = new Date(clickedTime*1000);
				    let hours = String(date.getHours()).padStart(2, '0');
				    let minutes = String(date.getMinutes()).padStart(2, '0');
				
				    hours = parseInt(hours)-7 ;
					let stime = hours + ':' + minutes;
					this.addMarkers(clickedTime,stime);
					/*
					document.getElementById("timeDisplay").innerHTML = '-';
					document.getElementById("timeDisplay").innerHTML = this.formatTime(parseInt(param.time));
					*/
					 
                });				
            } 

			// ฟังก์ชันแปลง timestamp เป็น hh:mm
			formatTime(timestamp) {

				console.log(timestamp);
				
				let date = new Date(timestamp);
				let hours = String(date.getHours()).padStart(2, '0');
				let minutes = String(date.getMinutes()).padStart(2, '0');
				return `${hours}:${minutes}`;
             }

			 calDisplayTime2() {
                
				timestampValue=parseInt(document.getElementById('timeInputSelected').value) ;
				console.log('Select Time=',timestampValue);
				/*
				document.getElementById("timeDisplay").innerHTML = '-';
				document.getElementById("timeDisplay").innerHTML = formatTime(parseInt(timestampValue));
				*/
		
		
		      } // end func
		
            
            setDefaultDate() {
                const today = new Date();
                document.getElementById('datePickerA').value = today.toISOString().split('T')[0];
            }
            
            async loadAssets() {
                const assets = [
                    { symbol: 'R_10', display: 'Volatility 10 Index' },
                    { symbol: 'R_25', display: 'Volatility 25 Index' },
                    { symbol: 'R_50', display: 'Volatility 50 Index' },
                    { symbol: 'R_75', display: 'Volatility 75 Index' },
                    { symbol: 'R_100', display: 'Volatility 100 Index' },
                    { symbol: 'RDBEAR', display: 'Bear Market Index' },
                    { symbol: 'RDBULL', display: 'Bull Market Index' },
                    { symbol: 'frxEURUSD', display: 'EUR/USD' },
                    { symbol: 'frxGBPUSD', display: 'GBP/USD' },
                    { symbol: 'frxUSDJPY', display: 'USD/JPY' },
                    { symbol: 'frxAUDUSD', display: 'AUD/USD' },
                    { symbol: 'frxUSDCHF', display: 'USD/CHF' },
                    { symbol: 'frxUSDCAD', display: 'USD/CAD' },
                    { symbol: 'frxEURJPY', display: 'EUR/JPY' },
                    { symbol: 'frxEURGBP', display: 'EUR/GBP' }
                ];
                
                const select = document.getElementById('assetSelect');
                select.innerHTML = '<option value="">เลือก Asset</option>';
                
                assets.forEach(asset => {
                    const option = document.createElement('option');
                    option.value = asset.symbol;
                    option.textContent = asset.display;
                    select.appendChild(option);
                });
            }
            
            connectSocket() {
                if (this.isConnected) {
                    this.showStatus('เชื่อมต่ออยู่แล้ว', 'info');
                    return;
                }
                
                try {
                    this.ws = new WebSocket('wss://ws.derivws.com/websockets/v3?app_id=66726');
                    
                    this.ws.onopen = () => {
                        this.isConnected = true;
                        this.showStatus('เชื่อมต่อ WebSocket สำเร็จ', 'success');
                        document.getElementById('connectSocket').textContent = '✅ เชื่อมต่อแล้ว';
                    };
                    
                    this.ws.onmessage = (event) => {
                        const data = JSON.parse(event.data);
                        this.handleSocketMessage(data);
                    };
                    
                    this.ws.onclose = () => {
                        this.isConnected = false;
                        this.showStatus('การเชื่อมต่อ WebSocket ถูกปิด', 'error');
                        document.getElementById('connectSocket').textContent = '🔌 เชื่อมต่อ Socket';
                    };
                    
                    this.ws.onerror = (error) => {
                        this.showStatus('เกิดข้อผิดพลาดในการเชื่อมต่อ WebSocket', 'error');
                        console.error('WebSocket error:', error);
                    };
                    
                } catch (error) {
                    this.showStatus('ไม่สามารถเชื่อมต่อ WebSocket ได้', 'error');
                    console.error('Connection error:', error);
                }
            }
            
            disconnectSocket() {
                if (this.ws && this.isConnected) {
                    this.ws.close();
                    this.isConnected = false;
                    document.getElementById('connectSocket').textContent = '🔌 เชื่อมต่อ Socket';
                    this.showStatus('ตัดการเชื่อมต่อ WebSocket แล้ว', 'info');
                }
            }
            
            handleSocketMessage(data) {
                if (data.msg_type === 'candles') {
                    this.processCandlesData(data);
                } else if (data.error) {
                    this.showStatus(`Error: ${data.error.message}`, 'error');
                }
            }
            
            fetchHistoricalData() {
                if (!this.isConnected) {
                    this.showStatus('กรุณาเชื่อมต่อ WebSocket ก่อน', 'error');
                    return;
                }
                
                const asset = document.getElementById('assetSelect').value;
                const date = document.getElementById('datePickerA').value;
                const startTime = document.getElementById('timePickerB').value;
                const endTime = document.getElementById('timePickerC').value;
				console.log('start time',startTime)
				
                const granularity = document.getElementById('timeframe').value;
                
                if (!asset || !date) {
                    this.showStatus('กรุณาเลือก Asset และวันที่', 'error');
                    return;
                }
                
                // Convert date and time to timestamps
                const startDateTime = new Date(`${date}T${startTime}:00`);
                const endDateTime = new Date(`${date}T${endTime}:00`);
                
                const request = {
                    ticks_history: asset,
                    adjust_start_time: 1,
                    count: 5000,
                    end: Math.floor(endDateTime.getTime() / 1000),
                    start: Math.floor(startDateTime.getTime() / 1000),
                    style: 'candles',
                    granularity: parseInt(granularity)
                };
				console.log(request);
					
                
                this.showLoading(true);
                this.ws.send(JSON.stringify(request));
            }
            
            fetchCurrentData() {
                if (!this.isConnected) {
                    this.showStatus('กรุณาเชื่อมต่อ WebSocket ก่อน', 'error');
                    return;
                }
                
                const asset = document.getElementById('assetSelect').value;
                const count = parseInt(document.getElementById('candleCount').value);
                const granularity = document.getElementById('timeframe').value;
                
                if (!asset) {
                    this.showStatus('กรุณาเลือก Asset', 'error');
                    return;
                }
                
                const request = {
                    ticks_history: asset,
                    adjust_start_time: 1,
                    count: count,
                    end: 'latest',
                    style: 'candles',
                    granularity: parseInt(granularity)
                };
                
                this.showLoading(true);
                this.ws.send(JSON.stringify(request));
            }
            
            processCandlesData(data) {
                this.showLoading(false);
                
                // Display raw data
                document.getElementById('responseData').value = JSON.stringify(data, null, 2);
                 
                if (data.candles && data.candles.length > 0) {
                    this.candlesData = data.candles.map(candle => ({
                        time:  candle.epoch + (7 * 3600), // เพิ่ม 7 ชั่วโมงสำหรับเวลาไทย,
                        open: parseFloat(candle.open),
                        high: parseFloat(candle.high),
                        low: parseFloat(candle.low),
                        close: parseFloat(candle.close)
                    }));
                    
                    // Update date/time pickers based on candle data
                    this.updateDateTimePickers(data.candles);
                    
                    this.updateChart();
                    this.updateChartTitle();
                    //this.sendToHdlightc(data);
                    this.showStatus(`ได้รับข้อมูล ${this.candlesData.length} แท่งเทียน`, 'success');
                } else {
                    this.showStatus('ไม่พบข้อมูลเทียน', 'error');
                }
            }
            
            updateChart() {
                if (this.candlesData.length === 0) return;
                
                // Update candles
                this.candleSeries.setData(this.candlesData);
                
                // Calculate and update EMAs
                this.updateEMA(); 

// Calculate and update RSI
const rsiData = this.calculateRSI(this.candlesData, 14);
this.rsiSeries.setData(rsiData);
this.rsiChart.timeScale().fitContent();
                
                // Fit content
                this.chart.timeScale().fitContent();
            }
            
            updateEMA() {
                if (this.candlesData.length === 0) return;
                
                const shortPeriod = parseInt(document.getElementById('emaShort').value);
                const longPeriod = parseInt(document.getElementById('emaLong').value);
                
                const emaShortData = this.calculateEMA(this.candlesData, shortPeriod);
                const emaLongData = this.calculateEMA(this.candlesData, longPeriod);
                
                this.emaShortSeries.setData(emaShortData);
                this.emaLongSeries.setData(emaLongData);
                
                // Update chart title when EMA values change
                this.updateChartTitle();
            }
            
            calculateEMA(data, period) {
                if (data.length < period) return [];
                
                const emaData = [];
                const multiplier = 2 / (period + 1);
                
                // First EMA value is SMA
                let ema = data.slice(0, period).reduce((sum, candle) => sum + candle.close, 0) / period;
                emaData.push({ time: data[period - 1].time, value: ema });
                
                // Calculate subsequent EMA values
                for (let i = period; i < data.length; i++) {
                    ema = (data[i].close * multiplier) + (ema * (1 - multiplier));
                    emaData.push({ time: data[i].time, value: ema });
                }
                
                return emaData;
            }
calculateRSI(data, period = 14) {
    if (data.length < period + 1) return [];
    
    const rsiData = [];
    const gains = [];
    const losses = [];
    
    // Calculate price changes
    for (let i = 1; i < data.length; i++) {
        const change = data[i].close - data[i-1].close;
        gains.push(change > 0 ? change : 0);
        losses.push(change < 0 ? Math.abs(change) : 0);
    }
    
    // Calculate RSI
    for (let i = period - 1; i < gains.length; i++) {
        const avgGain = gains.slice(i - period + 1, i + 1).reduce((a, b) => a + b) / period;
        const avgLoss = losses.slice(i - period + 1, i + 1).reduce((a, b) => a + b) / period;
        
        const rs = avgGain / (avgLoss || 0.001); // Avoid division by zero
        const rsi = 100 - (100 / (1 + rs));
        
        rsiData.push({ time: data[i + 1].time, value: rsi });
    }
    
    return rsiData;
}
            
            async sendToHdlightc(data) {
                try {
                    const response = await fetch('https://hdlightc.com/api/candles', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        const result = await response.text();
                        document.getElementById('hdlightcResponse').innerHTML = `
                            <div style="padding: 15px; background: #d4edda; border-radius: 8px; color: #155724;">
                                <strong>สำเร็จ:</strong> ${result}
                            </div>
                        `;
                    } else {
                        throw new Error(`HTTP ${response.status}`);
                    }
                } catch (error) {
                    document.getElementById('hdlightcResponse').innerHTML = `
                        <div style="padding: 15px; background: #f8d7da; border-radius: 8px; color: #721c24;">
                            <strong>ข้อผิดพลาด:</strong> ${error.message}
                        </div>
                    `;
                }
            }
            
            showStatus(message, type) {
                const status = document.getElementById('status');
                status.textContent = message;
                status.className = `status ${type}`;
                status.style.display = 'block';
                
                setTimeout(() => {
                    status.style.display = 'none';
                }, 5000);
            }
            
            showLoading(show) {
                document.getElementById('loading').style.display = show ? 'block' : 'none';
            }
            
            autoFetchData() {
                if (this.isConnected) {
                    const asset = document.getElementById('assetSelect').value;
                    if (asset) {
                        this.fetchCurrentData();
                    }
                }
            }
            
            updateDateTimePickers(candles) {
                if (candles && candles.length > 0) {
                    const firstCandle = new Date(candles[0].epoch * 1000);
                    const lastCandle = new Date(candles[candles.length - 1].epoch * 1000);
                    
                    // Update date picker
                    document.getElementById('datePickerA').value = firstCandle.toISOString().split('T')[0];
                    
                    // Update time pickers
                    const startTime = firstCandle.toTimeString().slice(0, 5);
                    const endTime = lastCandle.toTimeString().slice(0, 5);
                    
                    document.getElementById('timePickerB').value = startTime;
                    document.getElementById('timePickerC').value = endTime;
                }
            }
            
            updateChartTitle() {
                const asset = document.getElementById('assetSelect').value;
                const assetText = document.getElementById('assetSelect').selectedOptions[0]?.text || 'Unknown Asset';
                const timeframe = document.getElementById('timeframe').value;
                const timeframeText = document.getElementById('timeframe').selectedOptions[0]?.text || 'Unknown Timeframe';
                const emaShort = document.getElementById('emaShort').value;
                const emaLong = document.getElementById('emaLong').value;
                /*
                document.getElementById('chartTitle').innerHTML = `
                    📊 Candlestick Chart with EMA - ${assetText} (${timeframeText}) | EMA Short: ${emaShort} | EMA Long: ${emaLong}
                `;
				*/
				let dateRange = '';
				if (this.candlesData.length > 0) {
					//let firstDate = new Date((this.candlesData[0].time ) * 1000).toLocaleString('th-TH');
					let firstDate = new Date((this.candlesData[0].time - (7 * 3600)) * 1000).toLocaleString('th-TH');
					let lastDate = new Date((this.candlesData[this.candlesData.length - 1].time - (7 * 3600)) * 1000).toLocaleString('th-TH');

				//firstDate = new Date(this.candlesData[0].time);
				//lastDate = new Date((this.candlesData[this.candlesData.length - 1].time );
					dateRange = ` | ${firstDate} - ${lastDate}`;
				}
				
				document.getElementById('chartTitle').innerHTML = `
					📊 Candlestick Chart with EMA - ${assetText} (${timeframeText}) : <br>${dateRange}
				`;


            }
        } 

		// ฟังก์ชันแปลง timestamp เป็น hh:mm
		function formatTime(timestamp) {

				
				
				
				let date = new Date(timestamp*1000);
				let hours = String(date.getHours()).padStart(2, '0');
				let minutes = String(date.getMinutes()).padStart(2, '0');
				//console.log(timestamp,'-',minutes);
				hours = parseInt(hours)-7 ;

				return `${hours}:${minutes}`;
        }

		
        
        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
            new DerivCandleFetcher();
        });
    </script>

<?php
function ModalForm1() {  ?>

<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">ดึงประวัตการเทรด</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        
          <select id="daySelect">
		  <?php
			 for ($i=1;$i<=31;$i++) { ?>
			    <option value="<?=$i?>" selected><?=$i;?>
			 <?php }  		    
		  ?>
          </select>
		  <select id="monthSelect">
		  <?php
			 for ($i=1;$i<=12;$i++) { ?>
			    <option value="<?=$i?>" selected><?=$i;?>
			 <?php }  		    
		  ?>
          </select>
		  <select id="yearSelect">
		  <?php
			 for ($i=1;$i<=2;$i++) { ?>
			    <option value="<?=$i+2023?>" selected><?=$i+2023;?>
			 <?php }  		    
		  ?>
          </select>
		  <button type='button' id='' class='mBtn' onclick="doAjaxRetrieveTradeHistory() ">ดึง Trade Hostory</button>
		  <div id="resultModal1" class="bordergray flex">
		       
		  </div>

        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>


  <?php
  
  } // end function
  
?>
<script>

function AddMarkers() {

         const markerTmp = {
               time: sTime, // The time point where the marker should appear
               position: 'aboveBar', // 'aboveBar' or 'belowBar'
               color: 'blue',
               shape: 'circle', // 'circle', 'square', 'arrowUp', 'arrowDown'
               size: 2, // Optional: size of the marker
               text: caption, // Optional: text to display next to the marker
         };
		 console.log(markerTmp) ;
         this.marker.push(markerTmp) ;
         // Add the marker(s) to the candlestick series
         //this.candleSeries.setMarkers(marker);
	     //this.candleSeries.setMarkers([marker]);
			 this.candleSeries.setMarkers(this.marker);

} // end func


async function doAjaxRetrieveTradeHistory() {

    let sDate = document.getElementById("yearSelect").value + '-';

    sDate += document.getElementById("monthSelect").value + '-';
	sDate += document.getElementById("daySelect").value ;

    sObj = {
     sDate  : document.getElementById("daySelect").value ,
     sMonth : document.getElementById("monthSelect").value ,
     sYear  : document.getElementById("yearSelect").value
	}
    
	localStorage.setItem('newLabV2',JSON.stringify(sObj)) ;

    let result ;
    let ajaxurl = 'AjaxGetAction.php';
    let data = { "Mode": 'retriveTradeHistory' ,
      "dayTrade" : sDate    
    } ;
    data2 = JSON.stringify(data);
	//alert(data2);
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
        //alert(result);
		document.getElementById("resultModal1").innerHTML = result ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
} 

async function doAjaxGetAction() {
 
    candleData =  document.getElementById("responseData").value;     
	candleData = JSON.parse(candleData) ;
	
	document.getElementById("actionResult").innerHTML =  '';
	timeSelected = parseInt(document.getElementById("timeInputSelected").value);	
	
 
 let date = new Date(timeSelected*1000);
 let hours = String(date.getHours()).padStart(2, '0');
 let minutes = String(date.getMinutes()).padStart(2, '0');
				
 hours = parseInt(hours)-7 ;
 let stime = hours + ':' + minutes;
 //alert(stime);
  
let newCandleData = [] ;
for (i=0;i<=candleData.candles.length-1 ;i++ ) {

	 timeCandle = candleData.candles[i].epoch ;
	 let date = new Date(timeCandle*1000);
     let hours = String(date.getHours()).padStart(2, '0');
     let minutes = String(date.getMinutes()).padStart(2, '0');
	 hours = parseInt(hours) ;
	 let stime2 = hours + ':' + minutes;
	 
	 //console.log(timeCandle,' = ' ,stime2) ;
	 let sObj = {
		  time: candleData.candles[i].epoch,
		  open: candleData.candles[i].open,
		  close: candleData.candles[i].close,
          high: candleData.candles[i].high,
          low: candleData.candles[i].low
			  
		}
	 newCandleData.push(sObj)
	 
	 if (stime=== stime2) {
		//alert('Found At Index= '+ i); 
		break;
	 } else {
        
        //newCandleData.push(candleData.candles[i])
	 }
} // end for 
    let result ;
    let ajaxurl = 'AjaxGetAction.php';
    let data = { 
	   "Mode": 'getActionFromNewLabV2' ,
       "emaShort" : document.getElementById("emaShort").value ,
       "emaLong" : document.getElementById("emaLong").value ,		   
       "candleData" : newCandleData 
    } ;
    data2 = JSON.stringify(data);
	


	//alert(data2); return;
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
        //alert(result);
		result = JSON.parse(result);
		document.getElementById("actionResult").innerHTML = 
			result.timefrom+ '-->thisColor='+result.thisColor + ' -->Suggest= ' + result.suggestColor + ' :: '+result.actionReason + ' :: ' + result.CaseNo ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
} 

async function doAjaxCalWinById() {
 
    candleData =  document.getElementById("responseData").value;     
	candleData = JSON.parse(candleData) ;
	
	document.getElementById("actionResult").innerHTML =  '';
	timeSelected = parseInt(document.getElementById("timeInputSelected").value);	
	
 
 let date = new Date(timeSelected*1000);
 let hours = String(date.getHours()).padStart(2, '0');
 let minutes = String(date.getMinutes()).padStart(2, '0');
				
 hours = parseInt(hours)-7 ;
 let stime = hours + ':' + minutes;
 //alert(stime);
  
 
    let result ;
    let ajaxurl = 'AjaxGetAction.php';
    let data = { 
	   "Mode": 'CalWinById' ,
       "emaShort" : document.getElementById("emaShort").value ,
       "emaLong" : document.getElementById("emaLong").value ,		   
       "candleData" : candleData.candles ,
       "timeSelected" : timeSelected
    } ;
    data2 = JSON.stringify(data);
	


	//alert(data2); return;
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
        //alert(result);
		console.log('result',result)
		
		//result = JSON.parse(result);
		/*document.getElementById("actionResult").innerHTML = 
			result.timefrom+ '-->thisColor='+result.thisColor + ' -->Suggest= ' + result.suggestColor + ' :: '+result.actionReason + ' :: ' + result.CaseNo ;
			*/
        document.getElementById("actionResult").innerHTML = result;
        
		
        return result;
    } catch (error) {
        console.error(error);
    }
} 

function calDisplayTime222() {
                
				timestampValue=parseInt(document.getElementById('timeInputSelected').value) ;
				console.log('Select Time=',timestampValue);
				
	//document.getElementById("timeDisplay").innerHTML = '-';
	document.getElementById("timeDisplay").innerHTML = formatTime(parseInt(timestampValue));
				
		
		
} // end func
		
function aaaaa() {

         
	     $("#btnCalTime").trigger('click');

		 
		 


} // end func


function setHour(hourValue) {

//let nextHour = parseInt(hourValue)+1 ;
let nextHour = parseInt(hourValue) ;

        if (hourValue >= 10) {
		  st =  hourValue + ':00'; 
		  st2 = (parseInt(hourValue)) + ':59'; 
		  st3 = hourValue + ':00'; 
		  st4 = (parseInt(hourValue)) + ':59'; 
        }  else {
		  st = "0" + hourValue + ':00'; 
		  st3 = "0" + hourValue + ':00'; 
          if (nextHour < 10 ) {
           st2 = "0" + nextHour  + ':59'; 
		   st4 = "0" + nextHour  + ':59'; 
          } else {
           st2 = nextHour  + ':59'; 
		   st4 = "0" + nextHour  + ':59'; 
		  }
		   
		  
          
		}
/*
		if (hourValue === 9) {
		  
          st =  "0" + hourValue + ':00:00'; 
		  st2 = (parseInt(hourValue)+1) + ':00:00'; 
		  console.log('---->',st2)
		  
		}
		*/
		
		
	    
		document.getElementById("timePickerB").value = st;
		
		document.getElementById("timePickerC").value = st2;
		document.getElementById("hourText").innerHTML = st3 + ' ถึง ' +st4;
		
		
		



} // end func

function getConvertCandleData() {

    candleData =  document.getElementById("responseData").value;     
	candleData = JSON.parse(candleData) ;
	
	
  
	let newCandleData = [] ;
	for (i=0;i<=candleData.candles.length-1 ;i++ ) {

	 timeCandle = candleData.candles[i].epoch ;
	 let date = new Date(timeCandle*1000);
     let hours = String(date.getHours()).padStart(2, '0');
     let minutes = String(date.getMinutes()).padStart(2, '0');
	 hours = parseInt(hours) ;
	 let stime2 = hours + ':' + minutes;
	 
	 //console.log(timeCandle,' = ' ,stime2) ;
	 let sObj = {
		  time: candleData.candles[i].epoch,
		  open: candleData.candles[i].open,
		  close: candleData.candles[i].close,
          high: candleData.candles[i].high,
          low: candleData.candles[i].low
			  
	 }
	 newCandleData.push(sObj)
    } // end for 

    return newCandleData ;  

} // end func


async function doAjaxCalAll() {

    candleData = getConvertCandleData()  ;
    let result ;
    let ajaxurl = 'predictAll.php';
    let data = { "Mode": 'predictAll' ,   
    "data" : candleData 
    } ;
    data2 = JSON.stringify(data);
	//alert(data2);
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
        //alert(result);
		console.log(result) ;
		
		//document.getElementById("mainBoxAsset").innerHTML = result ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
}




$(document).ready(function () {
  //console.log("Hello World!");
  sObj = localStorage.getItem("newLabV2") ;
  sObj = JSON.parse(sObj) ; 
  document.getElementById("daySelect").value = sObj.sDate  ;
  document.getElementById("monthSelect").value = sObj.sMonth ;
  document.getElementById("yearSelect").value =  sObj.sYear  ; 
  document.getElementById("assetSelect").value = 'R_10';
  $("#connectSocket").trigger('click');
  $("fetchCurrent").trigger('click');
  

});


</script>

</body>
</html>