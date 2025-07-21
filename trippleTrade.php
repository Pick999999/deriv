<!-- trippleTrade.php -->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Deriv Trading Interface</title>
	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2a3052;
        }
		.chartContainer {
		   width:100% ; height:300px; border:1px solid lightgray;
		}
        .tab-container {
            margin-top: 20px;
        }
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
            margin-right: 5px;
        }
        .tab.active {
            background-color: #2a3052;
            color: white;
        }
        .tab-content {
            display: none;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .tab-content.active {
            display: block;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #2a3052;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #1d2233;
        }
        #logContainer {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            height: 200px;
            overflow-y: auto;
            background-color: #f9f9f9;
        }
        .log-entry {
            margin-bottom: 5px;
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .success {
            color: #4CAF50;
        }
        .error {
            color: #F44336;
        }
        .info {
            color: #2196F3;
        }
        .trade-history {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .trade-history table {
            width: 100%;
            border-collapse: collapse;
        }
        .trade-history th, 
        .trade-history td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .trade-history th {
            background-color: #2a3052;
            color: white;
        }
        .trade-history tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .profit {
            color: #4CAF50;
            font-weight: bold;
        }
        .loss {
            color: #F44336;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Deriv Trading Interface</h1>

        <div class="form-group">
            <label for="apiToken">API Token:</label>
            <input type="text" id="apiToken" placeholder="กรอก API Token ของคุณ"
			value='lt5UMO6bNvmZQaR' style='width:200px'
			>       
            <button id="connectBtn">เชื่อมต่อ API</button>
            <button id="disconnectBtn" disabled>ตัดการเชื่อมต่อ</button>
		</div>
		<div id="chartContainer" class="chartContainer">
		     
		</div>
		<div class="controls">
            <div class="price-box">
                <label for="priceInput">Min :</label>
                <input type="text" id="minpriceInput" sreadonly>
				<label for="priceInput">Max :</label>
                <input type="text" id="maxpriceInput" sreadonly>
				<label for="priceInput">ราคา:</label>
                <input type="text" id="priceInput" sreadonly>
            </div>
        </div>

        <div class="tab-container">
            <div class="tabs">
                <div class="tab active" data-tab="risefall">Rise/Fall</div>
                <div class="tab" data-tab="higherlower">Higher/Lower</div>
                <div class="tab" data-tab="touchnotouch">Touch/No-Touch</div>
				<div class="tab" data-tab="RemarkTab">RemarkTab</div>
            </div>

            <!-- Rise/Fall Tab -->
            <div id="risefall" class="tab-content active">
                <h2>Rise/Fall Trading</h2>
                <div class="form-group">
                    <label for="rf-symbol">สินทรัพย์:</label>
                    <select id="rf-symbol">
                        <option value="R_10">Volatility 10 Index</option>
                        <option value="R_25">Volatility 25 Index</option>
                        <option value="R_50">Volatility 50 Index</option>
                        <option value="R_75">Volatility 75 Index</option>
                        <option value="R_100">Volatility 100 Index</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rf-contractType">ประเภทสัญญา:</label>
                    <select id="rf-contractType">
                        <option value="CALL">Rise</option>
                        <option value="PUT">Fall</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rf-amount">จำนวนเงินเดิมพัน (USD):</label>
                    <input type="number" id="rf-amount" value="10" min="1">
                </div>
                <div class="form-group">
                    <label for="rf-duration">ระยะเวลา:</label>
                    <input type="number" id="rf-duration" value="5" min="1">
                    <select id="rf-durationUnit">
                        <option value="t">Ticks</option>
                        <option value="s">วินาที</option>
                        <option value="m">นาที</option>
                        <option value="h">ชั่วโมง</option>
                        <option value="d">วัน</option>
                    </select>
                </div>
                <button id="rf-buyBtn" disabled>ซื้อ</button>
            </div>

            <!-- Higher/Lower Tab -->
            <div id="higherlower" class="tab-content">
                <h2>Higher/Lower Trading</h2>
                <div class="form-group">
                    <label for="hl-symbol">สินทรัพย์:</label>
                    <select id="hl-symbol">
                        <option value="R_10">Volatility 10 Index</option>
                        <option value="R_25">Volatility 25 Index</option>
                        <option value="R_50">Volatility 50 Index</option>
                        <option value="R_75">Volatility 75 Index</option>
                        <option value="R_100">Volatility 100 Index</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hl-contractType">ประเภทสัญญา:</label>
                    <select id="hl-contractType">
                        <option value="CALL">Higher</option>
                        <option value="PUT">Lower</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hl-barrier">Barrier (ค่าเปลี่ยนแปลงเทียบกับราคาปัจจุบัน):</label>
                    <input type="text" id="hl-barrier" value="+0.01" placeholder="เช่น +0.01, -0.01">
                </div>
                <div class="form-group">
                    <label for="hl-amount">จำนวนเงินเดิมพัน (USD):</label>
                    <input type="number" id="hl-amount" value="10" min="1">
                </div>
                <div class="form-group">
                    <label for="hl-duration">ระยะเวลา:</label>
                    <input type="number" id="hl-duration" value="5" min="1">
                    <select id="hl-durationUnit">
                        <option value="m">นาที</option>
                        <option value="h">ชั่วโมง</option>
                        <option value="d">วัน</option>
                    </select>
                </div>
                <button id="hl-buyBtn" disabled>ซื้อ</button>
            </div>

            <!-- Touch/No-Touch Tab -->
            <div id="touchnotouch" class="tab-content">
                <h2>Touch/No-Touch Trading</h2>
                <div class="form-group">
                    <label for="tnt-symbol">สินทรัพย์:</label>
                    <select id="tnt-symbol">
                        <option value="R_10">Volatility 10 Index</option>
                        <option value="R_25">Volatility 25 Index</option>
                        <option value="R_50">Volatility 50 Index</option>
                        <option value="R_75">Volatility 75 Index</option>
                        <option value="R_100">Volatility 100 Index</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tnt-contractType">ประเภทสัญญา:</label>
                    <select id="tnt-contractType">
                        <option value="ONETOUCH">Touch</option>
                        <option value="NOTOUCH">No-Touch</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tnt-barrier">Barrier (ค่าเปลี่ยนแปลงเทียบกับราคาปัจจุบัน):</label>
                    <input type="text" id="tnt-barrier" value="+0.01" placeholder="เช่น +0.01, -0.01">
                </div>
                <div class="form-group">
                    <label for="tnt-amount">จำนวนเงินเดิมพัน (USD):</label>
                    <input type="number" id="tnt-amount" value="10" min="1">
                </div>
                <div class="form-group">
                    <label for="tnt-duration">ระยะเวลา:</label>
                    <input type="number" id="tnt-duration" value="5" min="1">
                    <select id="tnt-durationUnit">
                        <option value="m">นาที</option>
                        <option value="h">ชั่วโมง</option>
                        <option value="d">วัน</option>
                    </select>
                </div>
                <button id="tnt-buyBtn" disabled>ซื้อ</button>
            </div>

			<div id="RemarkTab" class="tab-content">
                <h2>Remark Tab</h2>
                 <div id="remarkdiv" class="bordergray flex">
                      
                 </div>
				 <div id="" class="bordergray flex">
				    1. Rise/Fall

หลักการ: เดิมพันว่าราคาจะขึ้น (Rise) หรือลง (Fall) เมื่อเทียบกับราคาเริ่มต้น ณ จุดที่เปิดออปชัน
การชนะ: ถ้าทำนายทิศทางถูกต้อง (ขึ้นหรือลง) ณ เวลาหมดอายุสัญญา คุณจะได้รับผลตอบแทน
ความเสี่ยง: คุณแค่ต้องทำนายทิศทางให้ถูกต้อง แต่ไม่จำเป็นต้องทำนายขนาดการเคลื่อนไหว

2. Higher/Lower

หลักการ: เดิมพันว่าราคาจะสูงกว่า (Higher) หรือต่ำกว่า (Lower) ระดับราคาเป้าหมายที่กำหนดไว้
การชนะ: ถ้าตลาดเคลื่อนไหวไปในทิศทางที่คุณทำนายและผ่านระดับเป้าหมาย คุณจะได้รับผลตอบแทน
ความแตกต่างจาก Rise/Fall: มีการกำหนดเป้าหมายราคาเฉพาะ (barrier) ซึ่งต้องถูกทะลุ ต่างจาก Rise/Fall ที่เปรียบเทียบกับราคาเริ่มต้นเท่านั้น

3. Touch/No-Touch

หลักการ: เดิมพันว่าราคาจะแตะ (Touch) หรือไม่แตะ (No-Touch) ระดับราคาเป้าหมายที่กำหนดไว้ภายในระยะเวลาสัญญา
การชนะ:

Touch: ชนะเมื่อราคาแตะถึงระดับเป้าหมายก่อนหมดอายุสัญญา
No-Touch: ชนะเมื่อราคาไม่แตะระดับเป้าหมายตลอดอายุสัญญา


ความแตกต่าง: ไม่จำเป็นต้องทำนายทิศทาง แต่ทำนายว่าราคาจะถึงจุดเฉพาะหรือไม่  
				 </div>
                 
                 
                 
                
            </div>
        </div>

        <h2>บันทึกการทำงาน</h2>
        <div id="logContainer"></div>

        <h2>ประวัติการเทรด</h2>
        <div class="trade-history">
            <table>
                <thead>
                    <tr>
                        <th>เวลา</th>
                        <th>ประเภท</th>
                        <th>สินทรัพย์</th>
                        <th>ทิศทาง</th>
                        <th>จำนวนเงิน</th>
                        <th>สถานะ</th>
                        <th>กำไร/ขาดทุน</th>
                    </tr>
                </thead>
                <tbody id="tradeHistoryBody">
                    <!-- ข้อมูลประวัติการเทรดจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // ตัวแปรสำหรับ WebSocket connection
        let ws = null;
        let isConnected = false;
        let activeContracts = {}; // เก็บข้อมูลสัญญาที่กำลังดำเนินการอยู่
		let candleSeries = [] ;
		let candlestickSeries = null;
		let chart = null;
		let requestId = 0;
		let chartElement = null;
	    let minPrice = Infinity;
        let maxPrice = -Infinity;
        let lastPrice = 0;	
        let currentPrice = lastPrice;        
        let priceLine = null;
		let priceInput = 0;
		let isDragging = false;

/* From Chart


*/
    

        // ฟังก์ชันสำหรับเพิ่มข้อความลงใน log
        function addLog(message, type = 'info') {
            const logContainer = document.getElementById('logContainer');
            const logEntry = document.createElement('div');
            logEntry.className = `log-entry ${type}`;
            logEntry.textContent = `${new Date().toLocaleTimeString()}: ${message}`;
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
        } 

		function updatePriceLine2(price,minPrice,maxPrice) {

			    console.log(price,'-',minPrice,'-',maxPrice);
			     
                // จำกัดราคาให้อยู่ในช่วงที่สมเหตุสมผล
                if (price < minPrice) price = minPrice;
                if (price > maxPrice) price = maxPrice;
                
                // ลบเส้นเดิม (ถ้ามี) และสร้างเส้นใหม่
                if (priceLine !== null) {
                    candlestickSeries.removePriceLine(priceLine);
                }
                
                priceLine = candlestickSeries.createPriceLine({
                    price: price,
                    color: '#2962FF',
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Solid,
                    axisLabelVisible: true,
                    title: 'ราคา',
                });
                priceInput = document.getElementById("priceInput").value ;
                
                // อัปเดตค่าในช่องข้อความ
                priceInput.value = price.toFixed(2);
                currentPrice = price;
         }

        // ฟังก์ชันสำหรับการเชื่อมต่อกับ API
        function connectToAPI() {
            const apiToken = document.getElementById('apiToken').value.trim();
            
            if (!apiToken) {
                addLog('กรุณากรอก API Token', 'error');
                return;
            }

            // ปิดการเชื่อมต่อเดิมถ้ามี
            if (ws) {
                ws.close();
            }

            addLog('กำลังเชื่อมต่อกับ Deriv API...');
            
            // สร้างการเชื่อมต่อ WebSocket ใหม่
            ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
            
            // เมื่อเชื่อมต่อสำเร็จ
            ws.onopen = function() {
                addLog('เชื่อมต่อกับ Deriv API สำเร็จ', 'success');                
                // ส่งคำขอ authorize
                sendRequest({
                    authorize: apiToken
                });

				timeSubscription = setInterval(() => {
					  if (ws && ws.readyState === WebSocket.OPEN) {
						 ws.send(JSON.stringify({ "time": 1 }));
					  }
					  selectedSymbol = document.getElementById("rf-symbol").value ;
					  selectedTimeframe = 1;
					  // สำหรับ แท่งใหญ่
					  const subscribeRequest = {
							ticks_history: selectedSymbol,
							style: "candles",
							granularity: selectedTimeframe * 60, // Convert to seconds
							count: 30,
							end: "latest",
							//subscribe: 1,
							req_id: requestId++
					  };
					  ws.send(JSON.stringify(subscribeRequest));


				   }, 1000);
				}

            
			
            
            // เมื่อได้รับข้อความจาก API
            ws.onmessage = function(msg) {
                const data = JSON.parse(msg.data);
				console.log(msg.data)
				if (data.candles) {
				  processCandles(data);
				  return;
				}
                
                // ตรวจสอบการ authorize
                if (data.authorize) {
                    isConnected = true;
                    document.getElementById('connectBtn').disabled = true;
                    document.getElementById('disconnectBtn').disabled = false;
                    document.getElementById('rf-buyBtn').disabled = false;
                    document.getElementById('hl-buyBtn').disabled = false;
                    document.getElementById('tnt-buyBtn').disabled = false;
                    
                    addLog(`การยืนยันตัวตนสำเร็จ! ยอดเงินคงเหลือ: ${data.authorize.balance} ${data.authorize.currency}`, 'success');
                }
                // ตรวจสอบการซื้อ
                else if (data.buy) {
                    addLog(`การซื้อสำเร็จ! รหัสสัญญา: ${data.buy.contract_id}`, 'success');
                    addLog(`รายละเอียด: ${data.buy.longcode}`, 'info');
                    
                    // บันทึกข้อมูลสัญญาและติดตามสถานะ
                    const contractId = data.buy.contract_id;
                    activeContracts[contractId] = {
                        contractId: contractId,
                        buyTime: new Date(),
                        type: getContractTypeFromLongcode(data.buy.longcode),
                        symbol: getSymbolFromLongcode(data.buy.longcode),
                        direction: getDirectionFromLongcode(data.buy.longcode),
                        amount: data.buy.buy_price,
                        status: 'กำลังดำเนินการ',
                        profit: null,
                        longcode: data.buy.longcode
                    };
                    
                    // เพิ่มรายการในตารางประวัติ
                    addTradeHistory(activeContracts[contractId]);
                    
                    // ติดตามสถานะของสัญญา
                    subscribeToContract(contractId);
                }
                // ตรวจสอบข้อมูลสัญญา
                else if (data.proposal_open_contract) {
                    const contract = data.proposal_open_contract;
                    
                    // อัพเดทข้อมูลสัญญา
                    if (contract.contract_id && activeContracts[contract.contract_id]) {
                        const contractId = contract.contract_id;
                        
                        // อัพเดทสถานะสัญญา
                        if (contract.is_sold === 1) {
                            // สัญญาสิ้นสุดแล้ว
                            const profit = contract.profit;
                            const status = profit >= 0 ? 'ชนะ' : 'แพ้';
                            
                            activeContracts[contractId].status = status;
                            activeContracts[contractId].profit = profit;
                            
                            // อัพเดทตารางประวัติ
                            updateTradeHistory(contractId, status, profit);
                            
                            // ยกเลิกการติดตาม
                            setTimeout(() => {
                                unsubscribeFromContract(contractId);
                            }, 1000);
                            
                            addLog(`สัญญา ${contractId} สิ้นสุดแล้ว: ${status} (${profit >= 0 ? '+' : ''}${profit})`, profit >= 0 ? 'success' : 'error');
                        }
                    }
                }
                // ตรวจสอบการสมัครสมาชิก
                else if (data.subscription) {
                    // ได้รับการยืนยันการสมัครสมาชิกติดตามสัญญา
                    if (data.subscription.id) {
                        // เก็บ subscription id สำหรับยกเลิกในภายหลัง
                        const contractId = getContractIdFromSubscription(data);
                        if (contractId && activeContracts[contractId]) {
                            activeContracts[contractId].subscriptionId = data.subscription.id;
                        }
                    }
                }
                // ตรวจสอบข้อผิดพลาด
                else if (data.error) {
                    addLog(`เกิดข้อผิดพลาด: ${data.error.message}`, 'error');
                }
                
                //console.log('ได้รับข้อมูล:', data);
            };
            
            // จัดการกับข้อผิดพลาดในการเชื่อมต่อ
            ws.onerror = function(error) {
                addLog('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                console.error('WebSocket Error:', error);
            };
            
            // เมื่อการเชื่อมต่อถูกปิด
            ws.onclose = function() {
                isConnected = false;
                document.getElementById('connectBtn').disabled = false;
                document.getElementById('disconnectBtn').disabled = true;
                document.getElementById('rf-buyBtn').disabled = true;
                document.getElementById('hl-buyBtn').disabled = true;
                document.getElementById('tnt-buyBtn').disabled = true;
                
                addLog('การเชื่อมต่อถูกปิด');
            };
        }

        // ฟังก์ชันสำหรับส่งคำขอไปยัง API
        function sendRequest(request) {
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify(request));
				document.getElementById("remarkdiv").innerHTML = JSON.stringify(request);
				
                console.log('ส่งคำขอ:', request);
            } else {
                addLog('ไม่มีการเชื่อมต่อ API', 'error');
            }
        } 

		function processCandles(data) {

			   candles = data.candles;
			   asset = data.echo_req.ticks_history;
			   endTime = data.echo_req.end;
			   //alert(asset);
			   candleData = candles.map(candle => ({
				  time: candle.epoch,
				  open: candle.open,
				  high: candle.high,
				  low: candle.low,
				  close: candle.close
			   }));
			  //console.log('Process Candle')
			   if (chart && candlestickSeries) {
                 // Update chart
                 candlestickSeries.setData(candleData);
				 for (const candle of candleData) {
                    minPrice = Math.min(minPrice, candle.low);
                    maxPrice = Math.max(maxPrice, candle.high);
                    lastPrice = candle.close; // ราคาปิดของแท่งสุดท้าย
                 }  
				 max=0 ; min=0 ;
				 for (i=0;i<=candleData.length-1 ;i++ ) {
				     if (candleData[i].high > max) {
						 max = candleData[i].high;
				     }
					 if (candleData[i].low > min) {
						 min = candleData[i].low;
				     }

				 } // end for 
				// minPrice = min ;
				// maxPrice = max ;
				 
				 
				 document.getElementById("maxpriceInput").value = maxPrice;
                 document.getElementById("minpriceInput").value = minPrice;
				 // คำนวณช่วงราคาและเพิ่มพื้นที่ขอบ
				 const priceRange = maxPrice - minPrice;
				 minPrice = minPrice - 0.05 * priceRange;
				 maxPrice = maxPrice + 0.05 * priceRange;           
                 // ตัวแปรเก็บราคาปัจจุบัน
                 currentPrice = lastPrice;
				 if (requestId < 2) {
					updatePriceLine2(currentPrice,minPrice,maxPrice);
				 }
				 


			   }
			   
		} // end func


        // ฟังก์ชันสำหรับซื้อ Rise/Fall
        function buyRiseFall() {
            if (!isConnected) {
                addLog('กรุณาเชื่อมต่อ API ก่อน', 'error');
                return;
            }
            
            const symbol = document.getElementById('rf-symbol').value;
            const contractType = document.getElementById('rf-contractType').value;
            const amount = parseFloat(document.getElementById('rf-amount').value);
            const duration = parseInt(document.getElementById('rf-duration').value);
            const durationUnit = document.getElementById('rf-durationUnit').value;
            
            if (isNaN(amount) || amount <= 0) {
                addLog('กรุณากรอกจำนวนเงินเดิมพันให้ถูกต้อง', 'error');
                return;
            }
            
            if (isNaN(duration) || duration <= 0) {
                addLog('กรุณากรอกระยะเวลาให้ถูกต้อง', 'error');
                return;
            }
            
            sendRequest({
                buy: 1,
                price: parseFloat(amount),
                parameters: {
                    amount: amount,
                    basis: "stake",
                    contract_type: contractType,
                    currency: "USD",
                    duration: duration,
                    duration_unit: durationUnit,
                    symbol: symbol
                }
            });
            
            addLog(`กำลังซื้อ ${contractType === 'CALL' ? 'Rise' : 'Fall'} สำหรับ ${symbol} เป็นเวลา ${duration} ${getDurationText(durationUnit)}`);
        }

        // ฟังก์ชันสำหรับซื้อ Higher/Lower
        function buyHigherLower() {
            if (!isConnected) {
                addLog('กรุณาเชื่อมต่อ API ก่อน', 'error');
                return;
            }
            
            const symbol = document.getElementById('hl-symbol').value;
            const contractType = document.getElementById('hl-contractType').value;
            const barrier = document.getElementById('hl-barrier').value;
            const amount = parseFloat(document.getElementById('hl-amount').value);
            const duration = parseInt(document.getElementById('hl-duration').value);
            const durationUnit = document.getElementById('hl-durationUnit').value;
            
            if (isNaN(amount) || amount <= 0) {
                addLog('กรุณากรอกจำนวนเงินเดิมพันให้ถูกต้อง', 'error');
                return;
            }
            
            if (isNaN(duration) || duration <= 0) {
                addLog('กรุณากรอกระยะเวลาให้ถูกต้อง', 'error');
                return;
            }
            
            if (!barrier) {
                addLog('กรุณากรอก Barrier', 'error');
                return;
            }
            
            sendRequest({
                buy: 1,
                price: parseFloat(amount),
                parameters: {
                    amount: amount,
                    basis: "stake",
                    contract_type: contractType,
                    currency: "USD",
                    duration: duration,
                    duration_unit: durationUnit,
                    barrier: barrier,
                    symbol: symbol
                }
            });
            
            addLog(`กำลังซื้อ ${contractType === 'CALL' ? 'Higher' : 'Lower'} สำหรับ ${symbol} ที่ barrier ${barrier} เป็นเวลา ${duration} ${getDurationText(durationUnit)}`);
        }

        // ฟังก์ชันสำหรับซื้อ Touch/No-Touch
        function buyTouchNoTouch() {
            if (!isConnected) {
                addLog('กรุณาเชื่อมต่อ API ก่อน', 'error');
                return;
            }
            
            const symbol = document.getElementById('tnt-symbol').value;
            const contractType = document.getElementById('tnt-contractType').value;
            const barrier = document.getElementById('tnt-barrier').value;
            const amount = parseFloat(document.getElementById('tnt-amount').value);
            const duration = parseInt(document.getElementById('tnt-duration').value);
            const durationUnit = document.getElementById('tnt-durationUnit').value;
            
            if (isNaN(amount) || amount <= 0) {
                addLog('กรุณากรอกจำนวนเงินเดิมพันให้ถูกต้อง', 'error');
                return;
            }
            
            if (isNaN(duration) || duration <= 0) {
                addLog('กรุณากรอกระยะเวลาให้ถูกต้อง', 'error');
                return;
            }
            
            if (!barrier) {
                addLog('กรุณากรอก Barrier', 'error');
                return;
            }
            
            sendRequest({
                buy: 1,
                price: parseFloat(amount),
                parameters: {
                    amount: amount,
                    basis: "stake",
                    contract_type: contractType,
                    currency: "USD",
                    duration: duration,
                    duration_unit: durationUnit,
                    barrier: barrier,
                    symbol: symbol
                }
            });
            
            addLog(`กำลังซื้อ ${contractType === 'ONETOUCH' ? 'Touch' : 'No-Touch'} สำหรับ ${symbol} ที่ barrier ${barrier} เป็นเวลา ${duration} ${getDurationText(durationUnit)}`);
        }

        // ฟังก์ชันสำหรับเปลี่ยนข้อความหน่วยเวลา
        function getDurationText(unit) {
            switch(unit) {
                case 't': return 'ticks';
                case 's': return 'วินาที';
                case 'm': return 'นาที';
                case 'h': return 'ชั่วโมง';
                case 'd': return 'วัน';
                default: return unit;
            }
        }

        // การจัดการ Tab
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // ลบ active class จากทุก tab และ tab content
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // เพิ่ม active class ให้กับ tab ที่คลิกและ content ที่เกี่ยวข้อง
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // ปุ่มเชื่อมต่อ
            document.getElementById('connectBtn').addEventListener('click', connectToAPI);
            
            // ปุ่มตัดการเชื่อมต่อ
            document.getElementById('disconnectBtn').addEventListener('click', function() {
                if (ws) {
                    ws.close();
                    addLog('ตัดการเชื่อมต่อสำเร็จ');
                }
            });
            
            // ปุ่มซื้อ Rise/Fall
            document.getElementById('rf-buyBtn').addEventListener('click', buyRiseFall);
            
            // ปุ่มซื้อ Higher/Lower
            document.getElementById('hl-buyBtn').addEventListener('click', buyHigherLower);
            
            // ปุ่มซื้อ Touch/No-Touch
            document.getElementById('tnt-buyBtn').addEventListener('click', buyTouchNoTouch);
        });

        // ฟังก์ชันสำหรับติดตามสถานะสัญญา
        function subscribeToContract(contractId) {
            sendRequest({
                proposal_open_contract: 1,
                contract_id: contractId,
                subscribe: 1
            });
        }

        // ฟังก์ชันสำหรับยกเลิกการติดตามสถานะสัญญา
        function unsubscribeFromContract(contractId) {
            if (activeContracts[contractId] && activeContracts[contractId].subscriptionId) {
                sendRequest({
                    forget: activeContracts[contractId].subscriptionId
                });
            }
        }

        // ฟังก์ชันสำหรับเพิ่มข้อมูลในตารางประวัติการเทรด
        function addTradeHistory(trade) {
            const tableBody = document.getElementById('tradeHistoryBody');
            const row = document.createElement('tr');
            row.id = `trade-${trade.contractId}`;
            
            row.innerHTML = `
                <td>${trade.buyTime.toLocaleTimeString()}</td>
                <td>${trade.type}</td>
                <td>${trade.symbol}</td>
                <td>${trade.direction}</td>
                <td>${trade.amount} USD</td>
                <td>${trade.status}</td>
                <td>รอผล</td>
            `;
            
            tableBody.appendChild(row);
        }

        // ฟังก์ชันสำหรับอัพเดทข้อมูลในตารางประวัติการเทรด
        function updateTradeHistory(contractId, status, profit) {
            const row = document.getElementById(`trade-${contractId}`);
            if (row) {
                const cells = row.getElementsByTagName('td');
                cells[5].textContent = status;
                
                const profitCell = cells[6];
                profitCell.textContent = `${profit >= 0 ? '+' : ''}${profit.toFixed(2)} USD`;
                profitCell.className = profit >= 0 ? 'profit' : 'loss';
            }
        }

        // ฟังก์ชันสำหรับดึงประเภทสัญญาจาก longcode
        function getContractTypeFromLongcode(longcode) {
            if (longcode.includes('Rise') || longcode.includes('Fall')) {
                return 'Rise/Fall';
            } else if (longcode.includes('Higher') || longcode.includes('Lower')) {
                return 'Higher/Lower';
            } else if (longcode.includes('Touch') || longcode.includes('No touch')) {
                return 'Touch/No-Touch';
            }
            return 'อื่นๆ';
        }

        // ฟังก์ชันสำหรับดึงชื่อสินทรัพย์จาก longcode
        function getSymbolFromLongcode(longcode) {
            // ดึงชื่อสินทรัพย์จาก longcode - ตัวอย่าง: "Volatility 10 Index"
            const match = longcode.match(/(Volatility \d+ Index|R_\d+)/);
            return match ? match[0] : 'ไม่ทราบ';
        }

        // ฟังก์ชันสำหรับดึงทิศทางการเทรดจาก longcode
        function getDirectionFromLongcode(longcode) {
            if (longcode.includes('Rise')) {
                return 'Rise';
            } else if (longcode.includes('Fall')) {
                return 'Fall';
            } else if (longcode.includes('Higher')) {
                return 'Higher';
            } else if (longcode.includes('Lower')) {
                return 'Lower';
            } else if (longcode.includes('Touch')) {
                return 'Touch';
            } else if (longcode.includes('No touch')) {
                return 'No-Touch';
            }
            return 'ไม่ทราบ';
        }

        // ฟังก์ชันสำหรับดึง contract ID จาก subscription data
        function getContractIdFromSubscription(data) {
            if (data.proposal_open_contract && data.proposal_open_contract.contract_id) {
                return data.proposal_open_contract.contract_id;
            }
            return null;
        }
    </script>


<script>
        // สร้างข้อมูลจำลองสำหรับกราฟ candlestick
        function generateCandlestickData(count = 100) {
            const data = [];
            let time = new Date(Date.UTC(2023, 0, 1, 0, 0, 0, 0));
            let baseValue = 10000;
            let amplitude = 500;
            
            for (let i = 0; i < count; i++) {
                const open = baseValue + Math.round((Math.random() * amplitude - amplitude / 2) * 10) / 10;
                const close = open + Math.round((Math.random() * amplitude - amplitude / 2) * 10) / 10;
                const low = Math.min(open, close) - Math.round(Math.random() * amplitude * 0.3 * 10) / 10;
                const high = Math.max(open, close) + Math.round(Math.random() * amplitude * 0.3 * 10) / 10;
                
                data.push({
                    time: time.getTime() / 1000,
                    open: open,
                    high: high,
                    low: low,
                    close: close
                });
                
                // ปรับค่าพื้นฐานสำหรับแท่งถัดไป
                baseValue = close;
                // เพิ่มเวลาขึ้น 1 วัน
                time.setUTCDate(time.getUTCDate() + 1);
            }
            
            return data;
        }

        // สร้าง chart
        document.addEventListener('DOMContentLoaded', function() {
            chartElement = document.getElementById('chartContainer');
            const priceInput = document.getElementById('priceInput');
            
            // สร้าง chart ด้วย lightweight-charts
            chart = LightweightCharts.createChart(chartElement, {
                width: chartElement.clientWidth,
                height: chartElement.clientHeight,
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
                rightPriceScale: {
                    borderColor: '#d1d4dc',
                    scaleMargins: {
                        top: 0.1,
                        bottom: 0.1,
                    },
                },
                timeScale: {
                    borderColor: '#d1d4dc',
                },
            });
            
            // สร้าง candlestick series และเพิ่มข้อมูล
            candlestickSeries = chart.addCandlestickSeries({
                upColor: '#26a69a', 
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a', 
                wickDownColor: '#ef5350'
            });
            
            const candlestickData = generateCandlestickData();
            candlestickSeries.setData(candlestickData);
            
            // หาค่าราคา min, max และเริ่มต้น
            minPrice = Infinity;
            maxPrice = -Infinity;
            lastPrice = 0;
            
            for (const candle of candlestickData) {
                minPrice = Math.min(minPrice, candle.low);
                maxPrice = Math.max(maxPrice, candle.high);
                lastPrice = candle.close; // ราคาปิดของแท่งสุดท้าย
            }
            
            // คำนวณช่วงราคาและเพิ่มพื้นที่ขอบ
            const priceRange = maxPrice - minPrice;
            minPrice = minPrice - 0.05 * priceRange;
            maxPrice = maxPrice + 0.05 * priceRange;
            
            // ตัวแปรเก็บราคาปัจจุบัน
            let currentPrice = lastPrice;
            
            // สร้างและอัปเดต price line
            let priceLine = null;
            
            function updatePriceLine(price) {
                // จำกัดราคาให้อยู่ในช่วงที่สมเหตุสมผล
                if (price < minPrice) price = minPrice;
                if (price > maxPrice) price = maxPrice;
                
                // ลบเส้นเดิม (ถ้ามี) และสร้างเส้นใหม่
                if (priceLine !== null) {
                    candlestickSeries.removePriceLine(priceLine);
                }
                
                priceLine = candlestickSeries.createPriceLine({
                    price: price,
                    color: '#2962FF',
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Solid,
                    axisLabelVisible: true,
                    title: 'ราคา',
                });
                
                // อัปเดตค่าในช่องข้อความ
                priceInput.value = price.toFixed(2);
                currentPrice = price;
            }
            
            // กำหนดเส้นราคาเริ่มต้น
            updatePriceLine(lastPrice);
            
            // คำนวณราคาจากตำแหน่ง Y บนกราฟ
            function getPriceFromY(y) {
                const chartHeight = chartElement.clientHeight;
                // แปลงค่า Y (0 = ด้านบนของกราฟ, chartHeight = ด้านล่างของกราฟ)
                // เป็นราคา (maxPrice = ด้านบนของกราฟ, minPrice = ด้านล่างของกราฟ)
                return maxPrice - (y / chartHeight) * (maxPrice - minPrice);
            }
            
            // ตัวแปรเก็บสถานะการลาก
            isDragging = false;
            
            // เพิ่ม event listeners สำหรับการลากเส้น
            chartElement.addEventListener('mousedown', function(e) {
                // คำนวณตำแหน่ง Y ภายในกราฟ
                const rect = chartElement.getBoundingClientRect();
                const y = e.clientY - rect.top;
				//console.log('Y=',y);
				
				
                
                if (y >= 0 && y <= rect.height) {
                    // คำนวณราคาจากตำแหน่ง Y
                    const price = getPriceFromY(y);
                    updatePriceLine(price);
                    
                    isDragging = true;
                    chartElement.style.cursor = 'ns-resize';
                    e.preventDefault(); // ป้องกันการเลือกข้อความ
                }
            });
            
            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                
                // คำนวณตำแหน่ง Y ภายในกราฟ
                const rect = chartElement.getBoundingClientRect();
                const y = e.clientY - rect.top;
				//console.log('Y2=',y);
                
                if (y >= 0 && y <= rect.height) {
                    // คำนวณราคาจากตำแหน่ง Y
                    const price = getPriceFromY(y);
                    updatePriceLine(price);
                }
            });
            
            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    chartElement.style.cursor = 'default';
                }
            });
            
            chartElement.addEventListener('mouseleave', function() {
                if (isDragging) {
                    isDragging = false;
                    chartElement.style.cursor = 'default';
                }
            });
            
            // ป้องกันการเลือกข้อความเมื่อลากเมาส์
            chartElement.addEventListener('selectstart', function(e) {
                if (isDragging) {
                    e.preventDefault();
                }
            });
            
            // ทำให้สามารถใช้ปุ่มลูกศรเพื่อเลื่อนราคาได้ด้วย
            document.addEventListener('keydown', function(e) {
                const step = (maxPrice - minPrice) / 100; // 1% ของช่วงราคา
                
                if (e.key === 'ArrowUp') {
                    updatePriceLine(currentPrice + step);
                    e.preventDefault();
                } else if (e.key === 'ArrowDown') {
                    updatePriceLine(currentPrice - step);
                    e.preventDefault();
                }
            });
            
            // ปรับขนาดกราฟเมื่อหน้าต่างเปลี่ยนขนาด
            window.addEventListener('resize', function() {
                chart.applyOptions({
                    width: chartElement.clientWidth,
                    height: chartElement.clientHeight
                });
            });
            
            // กำหนดให้แสดงข้อมูลทั้งหมด
            chart.timeScale().fitContent();
        });
    </script>

</body>
</html>