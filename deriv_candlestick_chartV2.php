<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv.com Asset Viewer</title>
	 <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .loading {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .category-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .category-btn {
            padding: 10px 15px;
            background-color: #ff444f;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .category-btn:hover {
            background-color: #e63946;
        }
        .category-btn.active {
            background-color: #0a0e1a;
        }
        .asset-table {
            width: 100%;
            border-collapse: collapse;
        }
        .asset-table th, .asset-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .asset-table th {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        .asset-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        .status-open {
            background-color: #c8e6c9;
            color: #2e7d32;
        }
        .status-closed {
            background-color: #ffcdd2;
            color: #c62828;
        }
		.flex { display:flex; width:100%}
		.flexA { width:50%;border:1px solid blue;}
		.flexChart { width:50%;border:1px solid red;}

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
			min-width: 150px;
			text-align:center;
		}
		.mBtn:hover { border:2px solid lightblue;}
		.green { background:#80ff80; }
		.pink { background:#ff0080; }

        .error-message {
            color: #c62828;
            padding: 20px;
            border: 1px solid #ffcdd2;
            background-color: #ffebee;
            border-radius: 4px;
            margin: 20px 0;
        }
        .trend {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 10px;
        }
        .trend-sideway {
            background-color: #e0e0e0;
            color: #616161;
        }
        .trend-weak {
            background-color: #bbdefb;
            color: #1976d2;
        }
        .trend-strong {
            background-color: #c8e6c9;
            color: #388e3c;
        }
        .trend-very-strong {
            background-color: #ffecb3;
            color: #ff8f00;
        }
        .trend-extremely-strong {
            background-color: #ffcdd2;
            color: #d32f2f;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .loading-spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
		
    </style>
</head>
<body>
    <h1>Deriv.com Asset Viewer</h1>
	<button type='button' id='' class='mBtn' onclick="LabFetchCandle_Derived()">
	LabFetchCandle-Derived</button>

	<button type='button' id='' class='mBtn' onclick="LabFetchCandle_Forex()">
	LabFetchCandle-Forex</button>

    <div id="loading" class="loading">กำลังโหลดข้อมูล Asset...</div>
    <div id="errorContainer"></div>
    <div id="categoryButtons" class="category-buttons"></div>
    <div id="assetContainer">
        <table id="assetTable" class="asset-table">
            <thead>
                <tr>
                    <th>ชื่อ Asset</th>
                    <th>รหัส</th>
                    <th>หมวดหมู่</th>
                    <th>ประเภท</th>
                    <th>สถานะ</th>
                    <th>Trend (ADX)</th>
                </tr>
            </thead>
            <tbody id="assetTableBody"></tbody>
        </table>
    </div>

    <!-- Modal สำหรับแสดงข้อมูลเพิ่มเติม -->
    <div id="trendModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">กำลังวิเคราะห์ Trend...</h2>
            <div id="modalContent">
                <div class="sloading-spinner"></div>
				   <div id="tradeContainer" class="bordergray flex">
				        
				   </div>
				<div class="flex">
                   <div class="flexLeft"> 
                    <h3>ผลการวิเคราะห์:</h3>
					<button type='button' id='' class='mBtn' onclick="goTrade()">GO Trade</button>
                    <p><strong>ADX:</strong><span id='spanadxValue'> ${adxValue}</span></p>
                    <p><strong>+DI:</strong><span id='spanplusDI'> ${Math.round(adxResult.plusDI)}</span></p>
                    <p><strong>-DI:</strong><span id='spanminusDI'> ${Math.round(adxResult.minusDI)}</span></p>
                    <p><strong>Trend Status:</strong> <span class="trend ${trendInfo.class}"><span id='spantrendInfo2'>${trendInfo.text}</span></span></p>
                    <p><strong>ทิศทาง:</strong> ${adxResult.plusDI > adxResult.minusDI ? 'ขาขึ้น (Bullish)' : 'ขาลง (Bearish)'}</p>
                    <div>
                        <h3>คำอธิบาย:</h3>
                        <ul>
                            <li><strong>ADX ต่ำกว่า 20:</strong> ตลาดไซด์เวย์ ไม่มีเทรนด์ที่ชัดเจน</li>
                            <li><strong>ADX 20-25:</strong> เทรนด์เริ่มก่อตัว แต่ยังไม่แข็งแรง</li>
                            <li><strong>ADX 25-35:</strong> เทรนด์แข็งแรง มีแนวโน้มที่ชัดเจน</li>
                            <li><strong>ADX 35-45:</strong> เทรนด์แข็งแรงมาก</li>
                            <li><strong>ADX มากกว่า 45:</strong> เทรนด์แข็งแรงที่สุด (อาจจะใกล้จุดสิ้นสุดเทรนด์)</li>
                        </ul>
                    </div>
                   </div>
				   
				   <div id='chartContainer' class='flexChart'>FlexChart</div>
                  </div>
            </div>
        </div>
    </div>

    <script>
        // หมวดหมู่ที่เราจะแสดง
        let categories = {};
        let activeCategory = null;
        let allAssets = [];
        let ws = null;
		let categorizedAssetsA = null;
        const modal = document.getElementById("trendModal");

        // ฟังก์ชั่นดึงข้อมูล Asset จาก Deriv API
        async function fetchDerivAssets() {
            try {
                // สร้าง WebSocket connection ไปยัง Deriv API
                ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
                
                return new Promise((resolve, reject) => {
                    ws.onopen = function() {
                        // เมื่อเชื่อมต่อสำเร็จ ส่งคำขอดึงข้อมูล active symbols
                        ws.send(JSON.stringify({
                            active_symbols: 'brief',
                            product_type: 'basic'
                        }));
                    };
                    
                    ws.onmessage = function(msg) {
                        const response = JSON.parse(msg.data);
                        
                        // ตรวจสอบว่าได้รับข้อมูล active_symbols หรือไม่
                        if (response.active_symbols) {
                            resolve(response.active_symbols);
                        } else if (response.error) {
                            reject(new Error(response.error.message));
                        }
                    };
                    
                    ws.onerror = function(error) {
                        reject(new Error('WebSocket error: ' + JSON.stringify(error)));
                    };
                    
                    // ตั้งเวลา timeout กรณีเชื่อมต่อนานเกินไป
                    setTimeout(() => {
                        if (ws.readyState === WebSocket.OPEN) {
                            reject(new Error('Connection timeout'));
                        }
                    }, 10000); // 10 วินาที
                });
            } catch (error) {
                throw new Error('Failed to fetch assets: ' + error.message);
            }
        }

        // จัดกลุ่ม assets ตามหมวดหมู่
        function organizeAssetsByCategory(assets) {
            const categorizedAssets = {};
            //console.log('assets',assets);

            
            assets.forEach(asset => {
                const category = asset.market_display_name;                
                if (!categorizedAssets[category]) {
                    categorizedAssets[category] = [];
                }
                
                categorizedAssets[category].push(asset);
            });
            //console.log('categorizedAssets',categorizedAssets.Forex);
			categorizedAssetsA = categorizedAssets ;
            
            return categorizedAssets;
        }

        // สร้างปุ่มสำหรับแต่ละหมวดหมู่
        function createCategoryButtons(categories) {
            const buttonContainer = document.getElementById('categoryButtons');
            buttonContainer.innerHTML = '';
            
            Object.keys(categories).sort().forEach(category => {
                const button = document.createElement('button');
                button.textContent = `${category} (${categories[category].length})`;
                button.className = 'category-btn';
                button.dataset.category = category;
                
                button.addEventListener('click', () => {
                    // ลบคลาส active จากทุกปุ่ม
                    document.querySelectorAll('.category-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // เพิ่มคลาส active ให้ปุ่มที่ถูกคลิก
                    button.classList.add('active');
                    
                    // แสดง assets ในหมวดหมู่ที่เลือก
                    displayAssetsByCategory(category);
                });
                
                buttonContainer.appendChild(button);
            });
        }

        // แสดง assets ตามหมวดหมู่ที่เลือก
        function displayAssetsByCategory(category) {
            activeCategory = category;
            const tableBody = document.getElementById('assetTableBody');
            tableBody.innerHTML = '';
            
            categories[category].forEach(asset => {
                const row = document.createElement('tr');
                symbol = asset.symbol ;
                // ชื่อ Asset
                const nameCell = document.createElement('td');
                nameCell.textContent = asset.display_name;
                row.appendChild(nameCell);
                
                // รหัส
                const symbolCell = document.createElement('td');
                symbolCell.textContent = asset.symbol;
                row.appendChild(symbolCell);
                
                // หมวดหมู่
                const categoryCell = document.createElement('td');
                categoryCell.textContent = asset.market_display_name;
                row.appendChild(categoryCell);
                
                // ประเภท
                const typeCell = document.createElement('td');
                typeCell.textContent = asset.submarket_display_name;
                row.appendChild(typeCell);

				const pageCell = document.createElement('td');
				sPage = 'https://thepapers.in/deriv/testacp.php?assetCode='+ asset.symbol;
				st = '<a href="'+ sPage +'" target=_blank>Go Trade->' + asset.symbol + '</a>' ;
                pageCell.innerHTML = st ;
                row.appendChild(pageCell);
                
                // สถานะ
                const statusCell = document.createElement('td');
                const statusSpan = document.createElement('span');
                statusSpan.className = `asset-status ${asset.exchange_is_open ? 'status-open' : 'status-closed'}`;
                statusSpan.textContent = asset.exchange_is_open ? 'เปิด' : 'ปิด';
                statusSpan.dataset.symbol = asset.symbol;
                
                // เพิ่ม event listener สำหรับคลิกที่สถานะเปิด
                if (asset.exchange_is_open) {
                    statusSpan.addEventListener('click', () => {
                        analyzeAssetTrend(asset);
                    });
                }
                
                statusCell.appendChild(statusSpan);
                row.appendChild(statusCell);
                
                // เพิ่มคอลัมน์ Trend
                const trendCell = document.createElement('td');
                trendCell.id = `trend-${asset.symbol}`;
                trendCell.textContent = 'คลิกที่ "เปิด" เพื่อวิเคราะห์';
                row.appendChild(trendCell);

				
				groupname = asset.market_display_name ;
                assetname = asset.display_name  ;
				console.log(groupname,'-',assetname);
				
                
				if (groupname === 'Derived' ) {
				   st = asset.display_name.substr(0,3);
				   if (st === 'Vol') {
					   tableBody.appendChild(row);
				   }					
				} else {                
                  tableBody.appendChild(row);
				}
            });
        }

        // ฟังก์ชั่นดึงข้อมูล candles จาก Deriv API
        async function fetchCandles(symbol, timeframe = '1m', count = 20) {
            return new Promise((resolve, reject) => {
                // ตรวจสอบสถานะการเชื่อมต่อ WebSocket
                if (!ws || ws.readyState !== WebSocket.OPEN) {
                    ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
                    
                    ws.onopen = function() {
                        sendCandleRequest();
                    };
                } else {
                    sendCandleRequest();
                }
                
                function sendCandleRequest() {
                    // ส่งคำขอข้อมูล candles
					/*
                    ws.send(JSON.stringify({
                        ticks_history: symbol,
                        style: 'candles',
                        granularity: parseInt(timeframe === '1d' ? 86400 : 3600), // วันหรือชั่วโมง
                        count: count,
                        end: 'latest'
                    }));
					ticks_history: asset,
                    style: "candles",
                    count: 100,
                    granularity: 60, // 1-minute candles
					end: "latest",
                    subscribe: 1,
					*/
					
					
					ws.send(JSON.stringify({
                        ticks_history: symbol,
                        style: 'candles',
                        granularity: 60, // 
                        count: 60,
                        end: 'latest'
                    }));
                    
                    ws.onmessage = function(msg) {
                        const response = JSON.parse(msg.data);
						
                        
                        if (response.candles) {
							let name = 'Assrt999';
							renderChart(response.candles, name);
                            resolve(response.candles);
                        } else if (response.error) {
                            reject(new Error(response.error.message));
                        }
                    };
                }
            });
        }

        // คำนวณ ADX indicator
        function calculateADX(candles, period = 14) {
            if (candles.length < period + 1) {
                //throw new Error('ต้องการข้อมูล candle อย่างน้อย ' + (period + 1) + ' แท่ง');
				return {
                plusDI: 0,
                minusDI: 0,
                adx: 0
            };
            }
            
            const high = candles.map(candle => candle.high);
            const low = candles.map(candle => candle.low);
            const close = candles.map(candle => candle.close);
            
            // คำนวณ +DM และ -DM
            const plusDM = [];
            const minusDM = [];
            
            for (let i = 1; i < candles.length; i++) {
                const highDiff = high[i] - high[i - 1];
                const lowDiff = low[i - 1] - low[i];
                
                if (highDiff > lowDiff && highDiff > 0) {
                    plusDM.push(highDiff);
                } else {
                    plusDM.push(0);
                }
                
                if (lowDiff > highDiff && lowDiff > 0) {
                    minusDM.push(lowDiff);
                } else {
                    minusDM.push(0);
                }
            }
            
            // คำนวณ True Range (TR)
            const tr = [];
            for (let i = 1; i < candles.length; i++) {
                const range1 = high[i] - low[i];
                const range2 = Math.abs(high[i] - close[i - 1]);
                const range3 = Math.abs(low[i] - close[i - 1]);
                
                tr.push(Math.max(range1, range2, range3));
            }
            
            // คำนวณ ATR (Average True Range) โดยใช้ Simple Moving Average
            const atr = [];
            let sum = 0;
            
            for (let i = 0; i < period; i++) {
                sum += tr[i];
            }
            
            atr.push(sum / period);
            
            for (let i = period; i < tr.length; i++) {
                atr.push((atr[atr.length - 1] * (period - 1) + tr[i]) / period);
            }
            
            // คำนวณ +DI และ -DI
            const plusDI = [];
            const minusDI = [];
            let plusDMSum = 0;
            let minusDMSum = 0;
            
            for (let i = 0; i < period; i++) {
                plusDMSum += plusDM[i];
                minusDMSum += minusDM[i];
            }
            
            plusDI.push(100 * (plusDMSum / period) / atr[0]);
            minusDI.push(100 * (minusDMSum / period) / atr[0]);
            
            for (let i = 1; i < atr.length; i++) {
                const newPlusDM = (plusDMSum - (plusDMSum / period)) + plusDM[i + period - 1];
                const newMinusDM = (minusDMSum - (minusDMSum / period)) + minusDM[i + period - 1];
                
                plusDMSum = newPlusDM;
                minusDMSum = newMinusDM;
                
                plusDI.push(100 * (plusDMSum / period) / atr[i]);
                minusDI.push(100 * (minusDMSum / period) / atr[i]);
            }
            
            // คำนวณ DX (Directional Index)
            const dx = [];
            for (let i = 0; i < plusDI.length; i++) {
                dx.push(100 * Math.abs(plusDI[i] - minusDI[i]) / (plusDI[i] + minusDI[i]));
            }
            
            // คำนวณ ADX (Average Directional Index)
            const adx = [];
            let dxSum = 0;
            
            for (let i = 0; i < period; i++) {
                dxSum += dx[i];
            }
            
            adx.push(dxSum / period);
            
            for (let i = 1; i < dx.length - period + 1; i++) {
                adx.push((adx[adx.length - 1] * (period - 1) + dx[i + period - 1]) / period);
            }
            
            return {
                plusDI: plusDI.slice(-1)[0],
                minusDI: minusDI.slice(-1)[0],
                adx: adx.slice(-1)[0]
            };
        }

        // วิเคราะห์ Trend ของ Asset จากค่า ADX
        function interpretADXTrend(adxValue) {
            if (adxValue < 20) {
                return { status: 'sideway', text: 'Sideway (ไซด์เวย์)', class: 'trend-sideway' };
            } else if (adxValue >= 20 && adxValue < 25) {
                return { status: 'weak', text: 'Weak Trend (เทรนด์อ่อน)', class: 'trend-weak' };
            } else if (adxValue >= 25 && adxValue < 35) {
                return { status: 'strong', text: 'Strong Trend (เทรนด์แข็งแรง)', class: 'trend-strong' };
            } else if (adxValue >= 35 && adxValue < 45) {
                return { status: 'very-strong', text: 'Very Strong Trend (เทรนด์แข็งแรงมาก)', class: 'trend-very-strong' };
            } else {
                return { status: 'extremely-strong', text: 'Extremely Strong Trend (เทรนด์แข็งแรงที่สุด)', class: 'trend-extremely-strong' };
            }
        }

        // วิเคราะห์ Trend ของ Asset
        async function analyzeAssetTrend(asset) {
            try {
                // แสดง Modal
                const modal = document.getElementById('trendModal');
                const modalTitle = document.getElementById('modalTitle');
                const modalContent = document.getElementById('modalContent');
                
                modalTitle.textContent = `กำลังวิเคราะห์ Trend ของ ${asset.display_name} (${asset.symbol})`;
				/*
                modalContent.innerHTML 
				= '<div  class="loading-spinner"></div><div id="chartContainer"></div>';
				modalContent.innerHTML = `
				 <div class="flex">
                   <div class="flexLeft"> 
                    <h3>ผลการวิเคราะห์:</h3>
                    
                   </div>
				   <div id='chartContainer' class='flexChart'>FlexChart</div>
                  </div>
                `;
				*/
				
                modal.style.display = 'block';
                
                // ดึงข้อมูล candles
                const candles = await fetchCandles(asset.symbol, '1d', 30);
                // คำนวณ ADX
                const adxResult = calculateADX(candles);
                const adxValue = Math.round(adxResult.adx);
                const trendInfo = interpretADXTrend(adxValue);
				
                
                // อัพเดตค่า Trend ในตาราง
                const trendCell = document.getElementById(`trend-${asset.symbol}`);
                trendCell.innerHTML = `<span class="trend ${trendInfo.class}">${trendInfo.text} (ADX: ${adxValue})</span>`;
				if (adxResult.plusDI > adxResult.minusDI) {
					sTrend = 'ขาขึ้น (Bullish)';
				} else {
                    sTrend = 'ขาลง (Bearish)';
				}
                
                 
				
				//alert(adxValue);
                 stBtnCall = "<button type='button' id='' class='mBtn green' onclick=goTrade('" ; 
				 stBtnCall += asset.symbol + "')>Go Trade</button>";

				 stBtnCall = "<button type='button' id='' class='mBtn green' onclick=goTrade('" ; 
				 stBtnCall += asset.symbol + "','CALL')>Go Trade Call</button>";

				 stBtnPut = "<button type='button' id='' class='mBtn pink' onclick=goTrade('" ; 
				 stBtnPut += asset.symbol + "','PUT')>Go Trade PUT</button>";

				 modalTitle.innerHTML = modalTitle.textContent +  stBtnCall + stBtnPut ;

				 

				//document.getElementById("tradeContainer").innerHTML = stBtn;
					
				 
				
				document.getElementById("spanadxValue").innerHTML = adxValue;
				document.getElementById("spanplusDI").innerHTML = adxResult.plusDI ;
				document.getElementById("spanminusDI").innerHTML = adxResult.minusDI ;
				document.getElementById("spantrendInfo2").innerHTML = sTrend ;


				
				
				
                
            } catch (error) {
                console.error('Error analyzing trend:', error);
                showError(`ไม่สามารถวิเคราะห์เทรนด์ได้: ${error.message}`);
                
                // อัพเดต Modal กรณีเกิดข้อผิดพลาด
                const modalContent = document.getElementById('modalContent');
                modalContent.innerHTML = `<div class="error-message">เกิดข้อผิดพลาดในการวิเคราะห์: ${error.message}</div>`;
            }
        }

        // แสดงข้อความ error
        function showError(message) {
            const errorContainer = document.getElementById('errorContainer');
            errorContainer.innerHTML = `<div class="error-message">${message}</div>`;
        } 

		// Calculate EMA
        function calculateEMA(data, period) {
            const k = 2 / (period + 1);
            let ema = data[0];
            const emaData = [ema];
            
            for (let i = 1; i < data.length; i++) {
                ema = data[i] * k + ema * (1 - k);
                emaData.push(ema);
            }
            
            return emaData;
        }

		function renderChart(candles, name) {

			
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.innerHTML = '';
            
            // Prepare data for lightweight-charts
            const candleData = candles.map(candle => ({
                time: candle.epoch,
                open: parseFloat(candle.open),
                high: parseFloat(candle.high),
                low: parseFloat(candle.low),
                close: parseFloat(candle.close)
            }));
            
			
            // Calculate EMA3 and EMA5
            const closes = candles.map(candle => parseFloat(candle.close));
            const ema3 = calculateEMA(closes, 3);
            const ema5 = calculateEMA(closes, 5);
            
            const ema3Data = candles.map((candle, index) => ({
                time: candle.epoch,
                value: ema3[index]
            }));
            
            const ema5Data = candles.map((candle, index) => ({
                time: candle.epoch,
                value: ema5[index]
            }));
            
			
            // Create the chart
            chart = LightweightCharts.createChart(chartContainer, {
                width: chartContainer.clientWidth,
                height: 400,
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
			
            
            // Add candle series
            const candleSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });
			
            candleSeries.setData(candleData);
			
            
            // Add EMA3 series
            const ema3Series = chart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                title: 'EMA3',
            });
            //console.log(ema3Data);
            
            ema3Series.setData(ema3Data);
			
            
            // Add EMA5 series
            const ema5Series = chart.addLineSeries({
                color: '#FF6D00',
                lineWidth: 2,
                title: 'EMA5',
            });
            ema5Series.setData(ema5Data);
      
            // Fit content
            chart.timeScale().fitContent();
            
            // Handle window resize
            window.addEventListener('resize', () => {
                if (chart) {
                    chart.applyOptions({
                        width: chartContainer.clientWidth
                    });
                }
            });
        }

        // ฟังก์ชั่นหลักที่จะทำงานเมื่อโหลดหน้าเว็บ
        async function initializeAssetViewer() {
            try {
                document.getElementById('loading').style.display = 'flex';
                
                // ดึงข้อมูล assets จาก Deriv API
                allAssets = await fetchDerivAssets();
                
                // จัดกลุ่ม assets ตามหมวดหมู่
                categories = organizeAssetsByCategory(allAssets);
                
                // สร้างปุ่มสำหรับแต่ละหมวดหมู่
                createCategoryButtons(categories);
                
                // เลือกหมวดหมู่แรกโดยอัตโนมัติ (ถ้ามี)
                const firstCategory = Object.keys(categories).sort()[0];
                if (firstCategory) {
                    const firstButton = document.querySelector(`.category-btn[data-category="${firstCategory}"]`);
                    if (firstButton) {
                        firstButton.click();
                    }
                }
                
                document.getElementById('loading').style.display = 'none';
            } catch (error) {
                document.getElementById('loading').style.display = 'none';
                showError(`เกิดข้อผิดพลาด: ${error.message}`);
                console.error('Error initializing asset viewer:', error);
            }
			// console.log('categorizedAssets-3 ',categorizedAssets);
			
        }

        // การจัดการ Modal
        // Get the modal
        const closeModal = document.getElementsByClassName("close")[0];
        
        // When the user clicks on <span> (x), close the modal
        closeModal.onclick = function() {
            modal.style.display = "none";
        }
        
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        } 

		function goTradePage(symbol) {

			     //alert(symbol);
		
		
		} // end func
		

		async function LabFetchCandle_Forex() {

		        // console.log('categorizedAssets',categorizedAssetsA.Forex);
				 for (i=0;i<=categorizedAssetsA.Forex.length-1 ;i++ ) {
				    //console.log('Assets',categorizedAssetsA.Forex[i].symbol);
					//ss = fetchCandles(categorizedAssetsA.Forex[i].symbol, timeframe = '1m', count = 60) ;
					let candles = await fetchCandles(categorizedAssetsA.Forex[i].symbol, '1m', 30);
                    // คำนวณ ADX
                    adxResult = calculateADX(candles);
                    adxValue = Math.round(adxResult.adx);
                    trendInfo = interpretADXTrend(adxValue);				
					// อัพเดตค่า Trend ในตาราง
					cellName = 'trend-' + categorizedAssetsA.Forex[i].symbol ;
					//console.log('cellName',cellName);

					if (adxResult.plusDI > adxResult.minusDI) {
						sTrend = 'ขาขึ้น (Bullish)';
					} else {
						sTrend = 'ขาลง (Bearish)';
					}
					
					const trendCell = document.getElementById(cellName);
					//console.log(cellName,'=',trendCell.innerHTML);
					
					//console.log(cellName,'-',trendInfo.text,'-',adxValue,'-',sTrend)

					trendCell.innerHTML = 'ssss';
					trendCell.innerHTML = `<span class="trend ${trendInfo.class}">${trendInfo.text} (ADX: ${adxValue}:: ${sTrend} )</span>`;
					
					
				 } // end for 
		
		} // end func

		async function LabFetchCandle_Derived() {

		         //console.log('categorizedAssets',categorizedAssetsA);
				 //return;
				 for (i=0;i<=categorizedAssetsA.Derived.length-1 ;i++ ) {
				    //console.log('Assets',categorizedAssetsA.Derived[i].symbol);
					//ss = fetchCandles(categorizedAssetsA.Forex[i].symbol, timeframe = '1m', count = 60) ;
					let candles = await fetchCandles(categorizedAssetsA.Derived[i].symbol, '1m', 30);
                    // คำนวณ ADX
                    adxResult = calculateADX(candles);
                    adxValue = Math.round(adxResult.adx);
                    trendInfo = interpretADXTrend(adxValue);				
					// อัพเดตค่า Trend ในตาราง
					cellName = 'trend-' + categorizedAssetsA.Derived[i].symbol ;
					console.log('cellName',cellName);

					if (adxResult.plusDI > adxResult.minusDI) {
						sTrend = 'ขาขึ้น (Bullish)';
					} else {
						sTrend = 'ขาลง (Bearish)';
					}
					
					
					const trendCell = document.getElementById(cellName);
					//console.log(cellName,'=',trendCell.innerHTML);
					
					//console.log(cellName,'-',trendInfo.text,'-',adxValue,'-',sTrend)
					if (!trendCell) {										
						console.log('Not Cell') ;
						
                    } else {
						trendCell.innerHTML = `<span class="trend ${trendInfo.class}">${trendInfo.text} (ADX: ${adxValue}:: ${sTrend} )</span>`;
					}
					
					
				 } // end for 
		
		} // end func

		function goTrade(asset,action) {

			sPage = 'https://thepapers.in/deriv/testacp.php?assetCode='+ asset;
			sPage += '&action='+ action ;
			window.open(sPage,'_blank');
		
		
		} // end func
		
		

        // เริ่มต้นทำงานเมื่อโหลดหน้าเว็บ
        window.addEventListener('DOMContentLoaded', initializeAssetViewer);

    </script>
	
</body>
</html>