<!-- 
ทำการดึงข้อมูล candle จาก deriv.com แล้วนำมาหาค่า  adx พร้อมทั้ง  วิเคราะห์ว่า trend 
เป็น Up,Down,Sideway โดยให้ มี  list ของ asset คือ R_10,R_25
,R_50,R_75,R_100 แล้วแสดงผลในรูป  html table พร้อม ปุ่ม ในแต่ละ  asset ซึ่งเมื่อสนใจ asset อันไหน ก็จะคลิกปุ่มของ asset นั้นๆ ก็จะทำการ
ดึง ข้อมูล candle ของ asset อันนั้น มาวาดกราฟ  candlestick + ema3+ema5  ด้วย 
<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
โดย ให้ ทำการ refresh data และ graph ทุก 2 seconds ทั้งหมด ทำด้วย pure javascript
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Real-time Candle Data with ADX</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 5px 10px;
            cursor: pointer;
        }
        #chartContainer {
            width: 47%;
            height: 320px;
            margin-top: 10px;
			border:1px solid red;
			padding:10px;
        }
		#chartContainerBig {
            width: 47%;
            height: 320px;
            margin-top: 10px;
			border:1px solid blue;
			padding:10px;
        }
        .up-trend {
            color: green;
            font-weight: bold;
        }
        .down-trend {
            color: red;
            font-weight: bold;
        }
        .sideway-trend {
            color: blue;
            font-weight: bold;
        }
        .status {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .connected {
            color: green;
        }
        .disconnected {
            color: red;
        }
		.mBtn { 
		    sdisplay: flex ;
			z-index: 3;
			sposition: relative;
			min-height: 50px;
			background: #fff;
			border: 1px solid #dadce0;
			box-shadow: 0px 3px 10px 0px rgba(31, 31, 31, 0.08);
			border-radius: 26px;
			
			box-sizing: border-box;
			width: 100%;
			text-align:center;
		}
		.mBtn:hover { border:2px solid lightblue;}
		.green { background:#80ff80; }
		.pink { background:#ff0080; }

    </style>
</head>
<body>
    <h1>Deriv Real-time Candle Data with ADX</h1>
    
    <div id="connectionStatus" class="status">
        Connection status: <span id="statusText" class="disconnected">Disconnected</span>
    </div>
	<input type="text" id="priceLineValue" value=0>
    
    <table id="assetTable">
        <thead>
            <tr>
                <th>Asset</th>
                <th>ADX Value</th>
                <th>Trend</th>
                <th>Trend Strength</th>
                <th>Action</th>
				<th>GoTrade</th>
            </tr>
        </thead>
        <tbody id="assetTableBody">
            <!-- Data will be populated here -->
        </tbody>
    </table>
	OnTradeNew(action)
	<table>
	<tr>
	 <td colspan=12>
	   <input type="text" id="numDuration" style='margin-left:10px' value=59>
	   <input type="radio" id="durationUnit" style='margin-left:10px' value="t">Tick
	   <input type="radio" id="durationUnit" style='margin-left:10px' value="s" checked>Second
	   <input type="radio" id="durationUnit" style='margin-left:10px' value="m">Minute
	   <?php SwitchBox();?>
	   &nbsp;&nbsp;&nbsp;Turn Type<span id="labelDesc"></span>

	 
	<tr>
		<td style='width:150px'>Asset Name</td>
		<td style='width:200px'><input type="text" id="assetCode" style='width:90%;padding:8px;border:1px solid gray;border-radius:8px'></td>

		<td style='width:150px'>MoneyTrade</td>
		<td style='width:150px'>
		<input type="text" id="moneyTrade" style='width:90%;padding:8px;border:1px solid gray;border-radius:8px' value=10></td>

        <td style='width:150px'>Balance</td>
		<td style='width:200px'>
		<input type="text" id="Allbalance" style='width:90%;padding:8px;border:1px solid gray;border-radius:8px' value=0></td>


        <td style='width:150px'>Target Profit</td>
		<td style='width:350px'>
		<input type="text" id="profitLimit" style='width:90%;padding:8px;border:1px solid gray;border-radius:8px' value=10></td>

		<td style='width:150px'>Profit Line</td>
		<td style='width:350px'>
		<input type="text" id="profitLineValue" style='width:90%;padding:8px;border:1px solid gray;border-radius:8px' value=''></td>

		

		

		<td><button type='button' id='' class='mBtn green' onclick="OnTradeNew('CALL')">Call</button></td>
		<td style='text-align:center'><button type='button' id='' class='mBtn pink' onclick="OnTradeNew('PUT')">PUT</button></td>

		<td><button type='button' id='' class='mBtn' onclick="drawPriceLine()">Draw Price Line</button></td>
	</tr>
	</table>
	
	<div id="tradeTableContainer" class="bordergray flex">
	     
	</div>
    
	<div id="assetDesc" class="bordergray flex">
	     
	</div>
	<div id="" class="bordergray flex" style='display:flex'>
       <div id="chartContainerBig"></div>     
	   <div id="chartContainer"></div>     
	</div>
	<textarea id="trackList" rows="" cols=""></textarea>
	<script src="claude/indy.js" ></script>
    
    
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <script>
        // List of assets to analyze
        const assets = ['R_10', 'R_25', 'R_50', 'R_75', 'R_100'];
        
        // ADX calculation parameters
        const ADX_PERIOD = 14;

		
        
        // Chart variables
        let chart = null;
        let candleSeries = null;
        let ema3Series = null;
        let ema5Series = null;
        let currentAsset = null;
		let canSold = false;
        
        // WebSocket variables
        let socket = null;
        const API_URL = "wss://ws.binaryws.com/websockets/v3?app_id=66726";
        let subscribedSymbols = new Set();
        let candleData = {};
        let assetSubscriptions = {};
		let closePriceLine = null; // Variable to track the horizontal line
		let trackTradeData = [];
		let clsIndy = new IndicatorCalculator();
		
		
        
        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            initializeTable();
            connectWebSocket();
			createTableNew();
			
			
        });
        
        // WebSocket connection
        function connectWebSocket() {
            updateStatus('Connecting...');
            
            socket = new WebSocket(API_URL);
			apiToken= 'lt5UMO6bNvmZQaR';
            
            socket.onopen = function() {
                updateStatus('Connected', 'connected');
				AuthRequest = {
                    authorize: apiToken
                };
			    socket.send(JSON.stringify(AuthRequest));

                // First, get initial candle history for all assets
                fetchInitialCandleHistory();
            };
            
            socket.onclose = function() {
                updateStatus('Disconnected', 'disconnected');
                // Attempt to reconnect after 3 seconds
                setTimeout(connectWebSocket, 3000);
            };
            
            socket.onerror = function(error) {
                updateStatus('Connection error', 'disconnected');
                console.error('WebSocket error:', error);
            };
            
            socket.onmessage = function(msg) {
                const response = JSON.parse(msg.data);
                handleWebSocketResponse(response);
            };
        }
        
        function updateStatus(text, className) {
            const statusElement = document.getElementById('statusText');
            statusElement.textContent = text;
            statusElement.className = className || 'disconnected';
        }

		function OnTradeNew(action) {        
			
			amount=100 ; duration = 17 ; 
			duration = document.getElementById("numDuration").value ;
			duration_unit = "s" ;
			duration_unit = "m" ;
			amount= parseInt(document.getElementById("moneyTrade").value) ;

			symbol = document.getElementById("assetCode").value ;

			const request1 = {
                    buy: 1,
                    price: parseFloat(amount),
					parameters: {
						 amount: parseFloat(amount),
						 basis: "stake",
						 contract_type: action,
						 currency: "USD",
						 duration: parseInt(duration),
						 duration_unit: duration_unit,
						 symbol: symbol
					}
            };
			socket.send(JSON.stringify(request1));

		
		
		} // end func

		function createTable(jsonObj) {

			let no =1 ;
			captionList ='ลำดับ,เลขสัญญา,contract_type,ราคาเข้าซื้อ,ราคาปัจจุบัน,เหลือเวลา,สิ้นสุด,ผล,กำไร,บาท' ; 
			balanceTime = jsonObj.expiry_time - jsonObj.date_start ;
			balanceTime = jsonObj.expiry_time  - jsonObj.current_spot_time ;
			MinuteRemain =  parseInt((balanceTime/60)) ;
			SecondRemain =  parseInt((balanceTime % 60)) ;

			document.getElementById("priceLineValue").value = jsonObj.entry_spot;

            trackObj = {
               time         : jsonObj.current_spot_time,
               conType      : jsonObj.contract_type, 
               entrySpot    : jsonObj.entry_spot,
			   currrentSpot : jsonObj.current_spot,
               profit       : jsonObj.profit
			}
			trackTradeData.push(trackObj);

			// console.log('Balance Time',balanceTime);
			balanceStr = balanceTime.toString()+'-'+ MinuteRemain.toString()+':'+SecondRemain.toString();
			
			captionAr = captionList.split(',');
			sBath = jsonObj.profit *33 ;
			valueList = [no,jsonObj.contract_id,jsonObj.contract_type,
						jsonObj.entry_spot,jsonObj.current_spot,
				        balanceStr ,jsonObj.is_sold,				        
				        jsonObj.status,
						jsonObj.profit,
				        sBath.toFixed(2)
				
			] ;
			profitLimit = parseFloat(document.getElementById("profitLimit").value) ;
			if (profitLimit != 0 && jsonObj.profit >= profitLimit ) {
               SaleContract(jsonObj.contract_id);
			   playAudio();
			}
			st = '<table border=1>'; st += '<tr>';
			for (i=0;i<=captionAr.length-1 ;i++ ) {
			   st += '<td>' + captionAr[i] + '</td>';
			} 
			
			st += '</tr>';
			if (jsonObj.status=='sold' ) {
				playAudio();
			}

			st += '<tr>';
			for (i=0;i<=valueList.length-1 ;i++ ) {
			   st += '<td>' + valueList[i]+ '</td>';
			} // end for 
					 st +='<td><button id="SaleBtn" onclick="SaleContract('+ jsonObj.contract_id +')">Sale</button> </td>';
					 st += '</tr>';
					 st += '</table>'; 

					 //console.log(st) ;
					 

					 document.getElementById("tradeTable").innerHTML = st;
		 
 } // end func

function setSaleAuto() {
    console.log(document.getElementById("saleAuto").checked)
    document.getElementById("saleAutoLabel").innerHTML = 'เปิดการขาย Auto';
	if (document.getElementById("saleAuto").checked) {
		canSold = true ;
	} else {
        canSold = false ;
	}
    
	
    
   
}
function playAudio() {

      audioFile = 'applause-cheer-236786.mp3';
      const audio = new Audio(audioFile);
      audio.play()
        .then(() => {
          console.log('Audio playback started successfully');
        })
        .catch(error => {
          console.error('Error playing audio:', error);
        });
    }

		function SaleContract(contractID) {
			     
                 socket.send(JSON.stringify({
                   sell: contractID,
                   price: 0 // ขายด้วยราคาตลาดปัจจุบัน
                 }));

		    st = JSON.stringify(trackTradeData);
			localStorage.setItem('trackTradeData',st);
			document.getElementById("trackList").value = st;
		
		} // end func
		
        
        // Initialize the asset table
        function initializeTable() {
            const tableBody = document.getElementById('assetTableBody');
            tableBody.innerHTML = '';
            
            assets.forEach(asset => {
                candleData[asset] = [];
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${asset}</td>
                    <td class="adx-value">-</td>
                    <td class="trend">-</td>
                    <td class="strength">-</td>
                    <td><button onclick="showAssetChart('${asset}')">View Chart</button></td>
					<td><button onclick="goTrade('${asset}')">Go Trade</button></td>

                `;
                tableBody.appendChild(row);
            });
        }
        
        // Fetch initial candle history for all assets
        function fetchInitialCandleHistory() {
            assets.forEach(asset => {
                const request = {
                    ticks_history: asset,
                    style: "candles",
                    count: 100,
                    granularity: 60, // 1-minute candles
					end: "latest",
                    subscribe: 1,
                };
                socket.send(JSON.stringify(request));
            });
			/*
			    ticks_history: selectedSymbol,
                style: "candles",
                granularity: selectedTimeframe * 60, // Convert to seconds
                count: 60,
                end: "latest",
                req_id: requestId++
			*/
        }
        
        // Handle WebSocket responses
        function handleWebSocketResponse(response) {

            if (response.msg_type === 'buy') {
				console.log('Buy',response);
				//AutoSale(response);
				newRowTable(response) ;
				
				
			    startTrackTrade(response.buy.contract_id);	
			}
			if (response.msg_type === 'proposal_open_contract') {
				//console.log(response);
			    //displayTrackTrade(response.proposal_open_contract);	
				//
				UpdateTrackTable(response.proposal_open_contract);
			}
			 
            if (response.msg_type === 'candles') {
                handleInitialCandleData(response);
            } else if (response.msg_type === 'ohlc') {
                handleLiveCandleUpdate(response);
            } else if (response.error) {
                console.error('API error:', response.error);
            }
        } 

		function startTrackTrade(contractId) {
		   const request = {
			  proposal_open_contract: 1,
			  contract_id: contractId,
			  subscribe: 1 // ขอ subscribe ข้อมูลเพื่อติดตามการเปลี่ยนแปลง
		   };

		   socket.send(JSON.stringify(request));
		   console.log(`Started tracking trade ${contractId}`);
		}

		function displayTrackTrade(data) {
			     //console.log(data.current_spot)
                 //createTable(data);
				 newRowTable(data) ;
			    
		} // end func
		
        
        // Handle initial candle data response
        function handleInitialCandleData(response) {
            const symbol = response.echo_req.ticks_history;
            
            if (response.candles) {
                candleData[symbol] = response.candles.map(candle => ({
                    time: candle.epoch,
                    open: candle.open,
                    high: candle.high,
                    low: candle.low,
                    close: candle.close,
                }));
                
                // Update table with initial data
                updateAssetData(symbol);
                
                // If this is the current asset being viewed, update the chart
                if (symbol === currentAsset) {
                    updateChartData();
                }
            }
        }
        
        // Handle live candle updates
        function handleLiveCandleUpdate(response) {
            const symbol = response.ohlc.symbol;
            const candle = {
                time: response.ohlc.epoch,
                open: response.ohlc.open,
                high: response.ohlc.high,
                low: response.ohlc.low,
                close: response.ohlc.close,
            };
            
            // Initialize if not exists
            if (!candleData[symbol]) {
                candleData[symbol] = [];
            }
            
            // Check if we already have this candle (for update)
            const existingIndex = candleData[symbol].findIndex(c => c.time === candle.time);
            if (existingIndex >= 0) {
                candleData[symbol][existingIndex] = candle;
            } else {
                candleData[symbol].push(candle);
                // Keep only the last 100 candles
                if (candleData[symbol].length > 100) {
                    candleData[symbol] = candleData[symbol].slice(-100);
                }
            }
            
            // Update table with new data
            updateAssetData(symbol);
            
            // Update chart if this is the current asset
            if (symbol === currentAsset) {
                updateChartData();
            }
        }
        
        // Update asset data in the table
        function updateAssetData(symbol) {
            const candles = candleData[symbol];
            if (!candles || candles.length < ADX_PERIOD * 2) return;
            
            const adxData = calculateADX(candles, ADX_PERIOD);
            const latestADX = adxData[adxData.length - 1];
            
            const rows = document.querySelectorAll('#assetTableBody tr');
            const row = Array.from(rows).find(r => r.cells[0].textContent === symbol);
            
            if (!row) return;
            
            const trend = determineTrend(latestADX, candles);
            const strength = determineStrength(latestADX);
            
            row.querySelector('.adx-value').textContent = latestADX.toFixed(2);
            
            const trendCell = row.querySelector('.trend');
            trendCell.textContent = trend.label;
            trendCell.className = 'trend ' + trend.class;
            
            const strengthCell = row.querySelector('.strength');
            strengthCell.textContent = strength.label;
            strengthCell.className = 'strength ' + strength.class;
        }
        
        // Determine the trend based on ADX and price movement
        function determineTrend(adxValue, candles) {
            if (adxValue < 20) {
                return { label: 'Sideway', class: 'sideway-trend' };
            }
            
            // Check if recent candles show upward or downward movement
            const lookback = Math.min(5, candles.length);
            const recentCandles = candles.slice(-lookback);
            const priceChanges = recentCandles.map(c => c.close - c.open);
            const sumChanges = priceChanges.reduce((sum, change) => sum + change, 0);
            
            if (sumChanges > 0) {
                return { label: 'Up', class: 'up-trend' };
            } else {
                return { label: 'Down', class: 'down-trend' };
            }
        }
        
        // Determine the strength of the trend based on ADX value
        function determineStrength(adxValue) {
            if (adxValue < 20) {
                return { label: 'Weak or No Trend', class: 'sideway-trend' };
            } else if (adxValue < 40) {
                return { label: 'Strong', class: 'up-trend' };
            } else if (adxValue < 60) {
                return { label: 'Very Strong', class: 'up-trend' };
            } else {
                return { label: 'Extremely Strong', class: 'up-trend' };
            }
        }

		function goTrade(asset) {
			thisPage = 'https://thepapers.in/deriv/trade/v2/index.php?assetcode=' + asset ;
			window.open(thisPage);
		}

		function goTrade2(action) {

			asset = document.getElementById("assetCode").value ;
			thisPage = 'https://thepapers.in/deriv/trade/v2/index.php?assetcode=' + asset +'&action='+ action;
			window.open(thisPage);
		}

		function AutoSale(response) {


		const contractId = response.buy.contract_id;        
        // ตั้งค่า take profit และ stop loss
        let sellRequest = {
            sell_contract_for_multiple_accounts: 1,
            shortcode: response.buy.shortcode,
            contract_id: contractId,
            limit_order: {
                take_profit: {
                    order_amount: response.buy.buy_price * 1.1 // ตัวอย่าง take profit ที่ 150% ของราคาซื้อ
                },
                stop_loss: {
                    order_amount: response.buy.buy_price * 0.9 // ตัวอย่าง stop loss ที่ 90% ของราคาซื้อ
                }
            }
        };
		sellRequest = {
           "set_self_exclusion": 1,
           "contract_id": contractId,
           "take_profit": 110.00000000000001,
           "stop_loss": 80
        }
		sellRequest =   {
          trading_platform_limit_order: 1,
          contract_id: contractId,
          limit_orders: [
            {
             type: "take_profit",
             value : 110.00000000000001
            },
            {
              type: "stop_loss",
              value: 90
            }
    ]
}

		console.log('sellRequest',sellRequest) ;
		
        
        socket.send(JSON.stringify(sellRequest));
		
		
		} // end func
		
        
        // Show chart for a specific asset
        function showAssetChart(asset) {
            currentAsset = asset;
			document.getElementById("assetCode").value = asset;
            document.getElementById("assetDesc").innerHTML = '<h2>'+ asset + '</h2>';
            
            // Initialize chart if not already done
            if (!chart) {
                chart = LightweightCharts.createChart(document.getElementById('chartContainer'), {
                    width: document.getElementById('chartContainer').clientWidth,
                    height: 300,
                    layout: {
                        backgroundColor: '#ffffff',
                        textColor: '#333',
                    },
                    grid: {
                        vertLines: {
                            color: '#eee',
                        },
                        horzLines: {
                            color: '#eee',
                        },
                    },
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                    },
                    rightPriceScale: {
                        borderVisible: false,
                    },
                    timeScale: {
                        borderVisible: false,
                        timeVisible: true,
                        secondsVisible: false
                    },
                });

                chartBig = LightweightCharts.createChart(document.getElementById('chartContainer'), {
                    width: document.getElementById('chartContainerBig').clientWidth,
                    height: 300,
                    layout: {
                        backgroundColor: '#ffffff',
                        textColor: '#333',
                    },
                    grid: {
                        vertLines: {
                            color: '#eee',
                        },
                        horzLines: {
                            color: '#eee',
                        },
                    },
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                    },
                    rightPriceScale: {
                        borderVisible: false,
                    },
                    timeScale: {
                        borderVisible: false,
                        timeVisible: true,
                        secondsVisible: false
                    },
                });

                candleSeriesBig = chart.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderDownColor: '#ef5350',
                    borderUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                    wickUpColor: '#26a69a',
                });
                
                candleSeries = chart.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderDownColor: '#ef5350',
                    borderUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                    wickUpColor: '#26a69a',
                });
                
                ema3Series = chart.addLineSeries({
                    color: 'rgba(255, 165, 0, 1)',
                    lineWidth: 2,
                });
                
                ema5Series = chart.addLineSeries({
                    color: 'rgba(0, 123, 255, 1)',
                    lineWidth: 2,
                });
            }
            
            // Set chart title
            chart.applyOptions({
                title: `${asset} - Real-time Candlestick with EMA3 & EMA5`,
            });
            
            // Initial data load
            updateChartData();
        } 

		function drawPriceLine() {
            // Remove previous line if exists
            if (closePriceLine) {
                candleSeries.removePriceLine(closePriceLine);
            } 
			priceLineValue = parseFloat(document.getElementById("priceLineValue").value);

// r_10 ; entry= 6440.452 profitline = 6440.020 PUT dIFFER = 452-020 = 0.432
// profit = 1.5 ที่ราคา 6438.662 = 1.858
// profit = (current_spot - entry_spot)/spottime

//r_10 entry_spot = 6442.121 ; profitline = 6442.515 differ  = 0.394
            profitLineValue  = parseFloat(document.getElementById("profitLineValue").value);


            // Create new price line
            closePriceLine = candleSeries.createPriceLine({
                price: priceLineValue,
                color: '#ff0080',
                lineWidth: 2,
                lineStyle: LightweightCharts.LineStyle.Solid,
                axisLabelVisible: true,
                title: 'Close',
            });

			drawProfitLine();

        }
        function drawProfitLine() {
            // Remove previous line if exists
            if (closePriceLine) {
                candleSeries.removePriceLine(closePriceLine);
            } 
			priceLineValue = parseFloat(document.getElementById("priceLineValue").value)

            profitLineValue  = parseFloat(document.getElementById("profitLineValue").value)


            // Create new price line
            closePriceLine = candleSeries.createPriceLine({
                price: profitLineValue,
                color: '#2196F3',
                lineWidth: 2,
                lineStyle: LightweightCharts.LineStyle.Solid,
                axisLabelVisible: true,
                title: 'Close',
            });
        }
        
        // Update chart data
        function updateChartData() {
            if (!currentAsset || !candleData[currentAsset]) return;
            
            const candles = candleData[currentAsset];
            if (candles.length === 0) return;
            
            // Prepare data for the chart
            const candleDataForChart = candles.map(c => ({
                time: c.time,
                open: c.open,
                high: c.high,
                low: c.low,
                close: c.close,
            }));
			candleSeries.setData(candleDataForChart);
            
            // Calculate EMAs
			/*
            const ema3 = clsIndy.calculateEMA(candles, 3);
            const ema5 = clsIndy.calculateEMA(candles, 5);
            
            // Update the chart
            
            ema3Series.setData(ema3.map((value, index) => ({
                time: candles[index].time,
                value: value,
            })));
            ema5Series.setData(ema5.map((value, index) => ({
                time: candles[index].time,
                value: value,
            })));
            
            // Adjust time scale to fit data
            chart.timeScale().fitContent();
			*/
        }
        
        // Calculate EMA (Exponential Moving Average)
        function calculateEMAVer1(candles, period) {
            const ema = [];
            const multiplier = 2 / (period + 1);
            
            // Simple Moving Average for the first value
            let sum = 0;
            for (let i = 0; i < period && i < candles.length; i++) {
                sum += candles[i].close;
            }
            ema[period - 1] = sum / period;
            
            // EMA for subsequent values
            for (let i = period; i < candles.length; i++) {
                ema[i] = (candles[i].close - ema[i - 1]) * multiplier + ema[i - 1];
            }
            
            return ema;
        }
        
        // Calculate ADX (Average Directional Index)
        function calculateADX(candles, period) {
            const adx = [];
            const plusDM = [];
            const minusDM = [];
            const TR = [];
            
            // Calculate +DM, -DM, and TR for each period
            for (let i = 1; i < candles.length; i++) {
                const upMove = candles[i].high - candles[i - 1].high;
                const downMove = candles[i - 1].low - candles[i].low;
                
                plusDM[i] = upMove > downMove && upMove > 0 ? upMove : 0;
                minusDM[i] = downMove > upMove && downMove > 0 ? downMove : 0;
                
                TR[i] = Math.max(
                    candles[i].high - candles[i].low,
                    Math.abs(candles[i].high - candles[i - 1].close),
                    Math.abs(candles[i].low - candles[i - 1].close)
                );
            }
            
            // Calculate smoothed +DM, -DM, and TR
            const smoothedPlusDM = [];
            const smoothedMinusDM = [];
            const smoothedTR = [];
            
            // Initial values (simple sum)
            let sumPlusDM = 0;
            let sumMinusDM = 0;
            let sumTR = 0;
            
            for (let i = 1; i <= period; i++) {
                sumPlusDM += plusDM[i] || 0;
                sumMinusDM += minusDM[i] || 0;
                sumTR += TR[i] || 0;
            }
            
            smoothedPlusDM[period] = sumPlusDM;
            smoothedMinusDM[period] = sumMinusDM;
            smoothedTR[period] = sumTR;
            
            // Subsequent values (smoothed)
            for (let i = period + 1; i < candles.length; i++) {
                smoothedPlusDM[i] = smoothedPlusDM[i - 1] - (smoothedPlusDM[i - 1] / period) + (plusDM[i] || 0);
                smoothedMinusDM[i] = smoothedMinusDM[i - 1] - (smoothedMinusDM[i - 1] / period) + (minusDM[i] || 0);
                smoothedTR[i] = smoothedTR[i - 1] - (smoothedTR[i - 1] / period) + (TR[i] || 0);
            }
            
            // Calculate +DI and -DI
            const plusDI = [];
            const minusDI = [];
            
            for (let i = period; i < candles.length; i++) {
                plusDI[i] = (smoothedPlusDM[i] / smoothedTR[i]) * 100;
                minusDI[i] = (smoothedMinusDM[i] / smoothedTR[i]) * 100;
            }
            
            // Calculate DX and ADX
            const DX = [];
            
            for (let i = period; i < candles.length; i++) {
                const diDiff = Math.abs(plusDI[i] - minusDI[i]);
                const diSum = plusDI[i] + minusDI[i];
                DX[i] = (diDiff / diSum) * 100;
            }
            
            // First ADX value is simple average of first 'period' DX values
            let sumDX = 0;
            for (let i = period; i < period * 2 && i < DX.length; i++) {
                if (DX[i]) sumDX += DX[i];
            }
            adx[period * 2 - 1] = sumDX / period;
            
            // Subsequent ADX values are smoothed
            for (let i = period * 2; i < candles.length; i++) {
                adx[i] = ((adx[i - 1] * (period - 1)) + (DX[i] || 0)) / period;
            }
            
            return adx;
        }
        
        // Clean up when page is closed
        window.addEventListener('beforeunload', function() {
            if (socket) {
                socket.close();
            }
        });
    </script>



<script>
 
function createTableNew() {

	let no =1 ;
	captionList ='ลำดับ,เลขสัญญา,contract_type,เวลาเข้าซื้อ,ราคาเข้าซื้อ,ราคาปัจจุบัน,เหลือเวลา,สิ้นสุด,ผล,กำไร,บาท' ;
	captionAr = captionList.split(',');
	st = '<table  id="tradeTable" class="mtable">'; st += '<tr>';
	for (i=0;i<=captionAr.length-1 ;i++ ) {
	   st += '<td>' + captionAr[i] + '</td>';
	}
	st += '</tr>';
	st += '</table>';
        document.getElementById("tradeTableContainer").innerHTML = st;

} // end func

function newRowTable(jsonObj) {
			
     
     no = 1;
	 valueList = [
	 no,jsonObj.buy.contract_id,
	 jsonObj.echo_req.parameters.contract_type,
	 convertUnixTimestampToHHMMSS(jsonObj.buy.purchase_time),
	 0,0,'-',0,'',0,0] ;
	
	 st = '<tr id="tr_' + jsonObj.buy.contract_id + '">';
	 for (i=0;i<=valueList.length-1 ;i++ ) {
              if (i !== 8) {
	        st += '<td>' + valueList[i]+ '</td>';
	      } else {
                st += '<td><span id="profit">' + valueList[i]+ '</span><hr>';
                st +='<button id="SaleBtn" onclick="SaleContract('+ jsonObj.buy.contract_id +')">Sale</button> </td>';
	       }
	} // end for
	st +='<td><button id="SaleBtn" onclick="SaleContract('+ jsonObj.buy.contract_id +')">Sale</button> </td>';
	st += '</tr>';

	document.getElementById("tradeTable").innerHTML = document.getElementById("tradeTable").innerHTML  + st;

	document.getElementById("profitLimit").value = parseFloat(document.getElementById("moneyTrade").value) * 0.1 ;

} // end func

function UpdateTrackTable(jsonObj) {

//captionList ='0-ลำดับ,1-เลขสัญญา,2-contract_type,3-ราคาเข้าซื้อ,
//4-ราคาปัจจุบัน,5-เหลือเวลา,6-สิ้นสุด,7-ผล,8-กำไร,9-บาท' ;
    profitLimit = parseFloat(document.getElementById("profitLimit").value) ;
    if (profitLimit != 0 && jsonObj.profit >= profitLimit && canSold===true) {
          SaleContract(jsonObj.contract_id);
	}
    

    rowId = 'tr_' + jsonObj.contract_id ;
	
	if (document.getElementById("profitLineValue").value === '') {
		document.getElementById("profitLineValue").value = parseFloat(jsonObj.entry_spot) + 0.40 ;
	}

	if (jsonObj.profit >=0 && jsonObj.profit < 0.1) {
       document.getElementById("profitLineValue").value = parseFloat(jsonObj.entry_spot);
	}
	
	
    let thisRow = document.getElementById(rowId);
    thisRow.cells[4].innerHTML = jsonObj.entry_spot;
    thisRow.cells[5].innerHTML = jsonObj.current_spot ;
	balanceTime = jsonObj.expiry_time - jsonObj.current_spot_time;
	balanceTimeStr = Math.floor(balanceTime /60) + ':' + (balanceTime % 60); 
   // thisRow.cells[6].innerHTML = jsonObj.expiry_time - jsonObj.current_spot_time ;		
	thisRow.cells[6].innerHTML = balanceTime + ' = '+ balanceTimeStr ;		
    thisRow.cells[8].innerHTML = jsonObj.status ;
    thisRow.cells[9].innerHTML = jsonObj.profit ;


} // end func

function convertUnixTimestampToHHMMSS(unixTimestamp) {
  // Create a Date object from the Unix timestamp
  const date = new Date(unixTimestamp * 1000);

  // Extract hours, minutes, and seconds
  const hours = date.getHours();
  const minutes = date.getMinutes();
  const seconds = date.getSeconds();

  // Add leading zeros if needed
  const formattedHours = hours.toString().padStart(2, '0');
  const formattedMinutes = minutes.toString().padStart(2, '0');
  const formattedSeconds = seconds.toString().padStart(2, '0');

  // Return the formatted time string
  return `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
}
/*
สร้าง function pure javascript สำหรับวิเคราะห์ข้อมูล จาก  ema3 array ,ema5 array ดังนี้ 
1. ema3 Trend ว่า Up,Down
2. ema5 Trend ว่า Up,Down
3. ema3 อยู่สูงกว่า ema5 หรือ ema5 อยู่สูงกว่า ema3
3. เป็นจุดตัดระหว่าง ema3,ema5 หรือไม่ 
4. ระยะห่างระหว่าง จุดตัดที่ผ่านมา ล่าสุด
5. เป็นจุดกลับตัวหรือไม่ และเป็นจุดกลับตัวแบบไหน 
6. ระยะห่างระหว่าง จุดกลับตัวล่าสุดที่ผ่านมา 

*/

</script>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>



<?php
function SwitchBox() {  ?>

<fieldset>
	<div class="checkboxGroup">
		<label class="checkboxControl">
		<input type="checkbox" id='saleAuto' onclick='setSaleAuto()' />
		<div>
			 ΟΙ
		</div>
		<b></b><span class="indicator"></span></label>
		<span id= 'saleAutoLabel' class="sindicatorLavel">ปิดการขาย Auto</span></label>
	</div>
</fieldset>
<!-- 
<fieldset>
	<label class="checkboxControl2">
	<input type="checkbox"/>
	<div>
	</div>
	<span class="indicator"></span></label>
</fieldset>
 -->
<script>
function setSaleAuto() {
    console.log(document.getElementById("saleAuto").checked)
    document.getElementById("saleAutoLabel").innerHTML = 'เปิดการขาย Auto';
    
   
}
</script>


<style>


fieldset {
  display: inline-block;
  vertical-align: middle;
  border: none;
  top: 0;
  left: 0;
  text-align: center;
}

.legend {
  color: rgba(0,0,0,.7);
  font-size: 12px;
  margin-bottom: 14px;
  height: 15px;
  border-color: #2E6677;
  border-style: solid;
  border-width: 1px 1px 0 1px;
  box-shadow: 1px 1px 0 rgba(255,255,255,0.2) inset;
  text-shadow: 0 1px rgba(255,255,255,.3);
}

.legend span {
  text-transform: uppercase;
  position: relative;
  top: -5px;
  padding: 0 10px;
  background: #509DAD;
  display: inline-block;
}

.checkboxGroup {
  display: inline-block;
  vertical-align: middle;
  width: 125px;
  border: none;
}
/*------- Horizontal power swtich ---------*/
.checkboxControl {
  border: 2px solid #102838;
  border-radius: 7px;
  display: inline-block;
  width: 100px;
  height: 50px;
  padding-top: 1px;
  position: relative;
  vertical-align: middle;
  margin: 0 60px 10px 0;
  color: #297597;
  box-shadow: 0 0 5px rgba(255,255,255,.4), 
				0 2px 1px -1px rgba(255,255,255,.7) inset, 
				8px 0 5px -5px #02425C inset,
				-8px 0 5px -5px #02425C inset;
  user-select: none;
  background: #80DCE9;
}

.checkboxControl input {
  position: absolute;
  visibility: hidden;
}

.checkboxControl > div {
  background: -webkit-linear-gradient(left, #8FD9E4 0%,#A0F2FE 53%,#69DCF1 56%,#33AFCE 99%,#CEF5FF 100%);
  background: linear-gradient(to right, #8FD9E4 0%,#A0F2FE 53%,#69DCF1 56%,#33AFCE 99%,#CEF5FF 100%);
  box-shadow: -2px 0 1px 0 #A6F2FE inset;
  border-radius: 5px;
  line-height: 50px;
  font-weight: bold;
  cursor: pointer;
  position: relative;
  z-index: 1;
  text-shadow: 0 1px rgba(255,255,255,0.5);
  transform-origin: 0 0;
  transform: scaleX(0.93);
  transition: .1s;
}

.checkboxControl div:first-letter {
  letter-spacing: 55px;
}

.checkboxControl :checked ~ div {
  transform-origin: 100% 0;
  -webkit-transform-origin: 100% 0;
  box-shadow: 2px 0 1px 0 #A6F2FE inset;
  background: -webkit-linear-gradient(left, #CEF5FF 0%,#33AFCE 1%,#69DCF1 47%,#A0F2FE 50%,#8FD9E4 100%);
  background: linear-gradient(to right, #CEF5FF 0%,#33AFCE 1%,#69DCF1 47%,#A0F2FE 50%,#8FD9E4 100%);
}
	/* bottom shadow of 'upper' side of the button */
.checkboxControl > b {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 50%;
  height: 100%;
  border-radius: 8px;
  transform: skewY(5deg);
  box-shadow: 0 6px 8px -5px #000;
}

.checkboxControl :checked ~ b {
  right: auto;
  left: 0;
  transform: skewY(-5deg);
}
	/* the light indicator to the right of the button */
.checkboxControl .indicator {
  position: absolute;
  top: 14px;
  right: -20px;
  width: 8px;
  height: 25px;
  box-shadow: 0 0 8px #000 inset;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 15px;
  transition: 0.2s;
}

.checkboxControl .indicator:before {
  content: '';
  display: inline-block;
  margin-top: 8px;
  width: 2px;
  height: 8px;
  border-radius: 10px;
  transition: 0.5s;
  -webkit-transition: 0.5s;
}

.checkboxControl :checked ~ .indicator:before {
  box-shadow: 0 0 7px 6px #BAFC58;
  width: 6px;
  background: #F0F9E3;
  transition: 0.1s;
}

	/*------- Vertical power swtich ---------*/
.checkboxControl2 {
  border: 2px solid #102838;
  border-radius: 7px;
  display: inline-block;
  vertical-align: middle;
  font-weight: bold;
  width: 59px;
  height: 90px;
  position: relative;
  margin: 0 5px;
  color: #12678C;
  box-shadow: 0 0 5px rgba(255, 255, 255, .4);
}

.checkboxControl2 input {
  position: absolute;
  visibility: hidden;
}

.checkboxControl2 > div {
  background: -webkit-linear-gradient(top, #002B44 0%, #0690AC 11%, #038EAA 14%, #A0F2FE 58%, #91DBE7 96%, #B9E8E8 100%);
  background: linear-gradient(to bottom, #002B44 0%, #0690AC 11%, #038EAA 14%, #A0F2FE 58%, #91DBE7 96%, #B9E8E8 100%);
  height: 100%;
  border-radius: 5px;
  line-height: 50px;
  z-index: 1;
  cursor: pointer;
  text-shadow: 0 1px rgba(255,255,255,0.5);
}

.checkboxControl2 > div:after {
  content: 'Ο';
  display: block;
  height: 50%;
  line-height: 4;
  transform-origin: 0 0;
}

.checkboxControl2 > div:before {
  content: 'Ι';
  display: block;
  height: 50%;
  line-height: 2.5;
  border-radius: 80%/5px;
  box-shadow: 0 8px 12px -13px #89DFED inset, 0 -2px 2px -1px rgba(255,255,255,0.8);
  transform-origin: 0 100%;
  transform: scaleY(0.7);
}

.checkboxControl2 :checked ~ div {
  background: -webkit-linear-gradient(bottom, #002B44 0%, #0690AC 11%, #038EAA 14%, #A0F2FE 58%, #91DBE7 96%, #B9E8E8 100%);
  background: linear-gradient(to top, #002B44 0%, #0690AC 11%, #038EAA 14%, #A0F2FE 58%, #91DBE7 96%, #B9E8E8 100%);
}

.checkboxControl2 :checked ~ div:before {
  border-radius: 0;
  box-shadow: none;
  transform: none;
  -webkit-transform: none;
}

.checkboxControl2 :checked ~ div:after {
  border-radius: 80%/5px;
  box-shadow: 0 -8px 12px -5px #89DFED inset, 0 2px 2px 0 #0690AC;
  transform: scaleY(0.7);
}
	/* the light indicator to the top of the button */
.checkboxControl2 .indicator {
  position: absolute;
  top: -20px;
  left: 17px;
  width: 25px;
  height: 8px;
  box-shadow: 0 0 8px #000 inset;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 15px;
  transition: 0.2s;
}

.checkboxControl2 .indicator:before {
  content: '';
  display: block;
  margin: 2px auto;
  width: 8px;
  height: 5px;
  border-radius: 10px;
  transition: 0.5s;
}

.checkboxControl2 :checked ~ .indicator:before {
  box-shadow: 0 0 2px 0px #F95757 inset, 0 0 12px 6px #F95757;
  background: #FFF;
  transition: 0.1s;
}
</style>
<?php } // end function


?>

</body>
</html>