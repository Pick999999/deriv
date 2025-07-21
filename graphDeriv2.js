let websocket = null;
let selectedTimeframe = 1;
let isProcessing = false;
let isConnecting = false;
let reconnectAttempts = 0;
let timeSubscription = null;
let AllSymBolList = null;
let chart = null;
let candleSeries = null;
let ema3Series = null;
let ema5Series = null;
let tradeHistory = null;
let assetCode = null ;
let warnNumcheck = null ;
let workingCode = null;
let workingDesc = ['Idle','RequestCandle','AjaxGetAction','PlaceTrade','Wait Result'];
let CurrentLotNo = null ;
let totalRowTrade = 0 ;
let LossCon = 0 ;
let MoneyTradeList = [1,2,4,9,20,35,54,54,54,54,54,54,54] ;
let thisMoneyTrade = 1 ;
let balanceTime = 0 ;

function InitVar() {




} // end func





async function initChart() {
   const chartContainer = document.getElementById('chartContainer');

   if (typeof LightweightCharts === 'undefined') {
      console.error('LightweightCharts library not loaded');
      document.getElementById('status').textContent = 'Error: Chart library not loaded';
      return;
   }

   chart = LightweightCharts.createChart(chartContainer, {
      width: chartContainer.clientWidth,
      height: chartContainer.clientHeight,
      layout: {
         background: {
            color: '#ffffff'
         },
         textColor: '#333',
      },
      grid: {
         vertLines: {
            color: '#f0f0f0'
         },
         horzLines: {
            color: '#f0f0f0'
         },
      },
      timeScale: {
         timeVisible: true,
         secondsVisible: false,
         tickMarkFormatter: (time) => {
            let hours = parseInt(time);
            hours = timestampToHHMM(time);
            return `${hours}`;
         }
      },
      crosshair: {
         mode: LightweightCharts.CrosshairMode.Normal,
      },
      localization: {
         locale: 'th-TH',
         priceFormatter: price => price.toFixed(2), // กำหนดทศนิยม 2 ตำแหน่ง
         timeFormatter: time => {
            return new Date(time * 1000).toLocaleString('th-TH');
         },
      },

   });

   candleSeries = chart.addCandlestickSeries({
      upColor: '#26a69a',
      downColor: '#ef5350',
      borderVisible: false,
      wickUpColor: '#26a69a',
      wickDownColor: '#ef5350'
   });

   ema3Series = chart.addLineSeries({
      color: '#2962FF',
      lineWidth: 2,
      title: 'EMA 3'
   });

   ema5Series = chart.addLineSeries({
      color: '#FF6B6B',
      lineWidth: 2,
      title: 'EMA 5'
   });

   window.addEventListener('resize', () => {
      chart.applyOptions({
         width: chartContainer.clientWidth,
         height: chartContainer.clientHeight
      });
   });
}



function connect() {

   if (isConnecting) return;
   isConnecting = true;
   websocket = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');

   websocket.onopen = function () {
      console.log('WebSocket Connected');
      authenticateUser(); // เพิ่มฟังก์ชันนี้
      subscribeToTime();
      initChart().catch(console.error);
   };


   websocket.onmessage = function (msg) {
      const response = JSON.parse(msg.data);
      const data = JSON.parse(msg.data);
      if (data.time) {
         updateServerTime(data.time);
      }
      if (data.candles) {
         console.log('Data Candle', data)
         processCandles(data);
      }
      // ตรวจสอบว่าเป็นข้อมูลของสัญญาที่เราติดตามอยู่หรือไม่
      if (data.proposal_open_contract) {
         console.log('ข้อมูลสัญญา:', data.proposal_open_contract);
		 lotno = document.getElementById("currentLotNo").value ;

		 lotId =  "lotno_" + totalRowTrade;
         contractId = data.proposal_open_contract.contract_id
         profitId = "profit_" + contractId;
         ticktimeId = "ticktime_" + contractId;
         expiretimeId = "expiretime_" + contractId;
         balancetimeId = "balancetime_" + contractId;

         //console.log('Profit id', profitId) ;
         document.getElementById(lotId).innerHTML = document.getElementById("currentLotNo").value ;
         document.getElementById(profitId).innerHTML = data.proposal_open_contract.profit;
         document.getElementById(ticktimeId).innerHTML = data.proposal_open_contract.entry_tick_time;
         document.getElementById(expiretimeId).innerHTML = data.proposal_open_contract.expiry_time;

         let balanceTimeA = data.proposal_open_contract.expiry_time - data.proposal_open_contract.current_spot_time;
         balanceTime = parseInt(balanceTimeA);

         document.getElementById(balancetimeId).innerHTML = balanceTime;
		 document.getElementById("balanceTimeTxt").value = balanceTime;;
         let profit = data.proposal_open_contract.profit;

         if (document.getElementById("isCheckedStopLoss").checked  && balanceTime < 20) {
            if (balanceTime < 20) {
               if (profit < -0.65) {
				   document.getElementById("shouldSell").innerHTML = 'Should Sell';
                  //SellContract() ;
                  //return;
               }
            }
         }
         tradeList = JSON.parse(document.getElementById("tradeResultTxt").value);
         lastIndex = tradeList.length - 1;
         //console.log(tradeList[lastIndex].detailTradeList.length) ;
         let listNo = tradeList[lastIndex].detailTradeList.length + 1
         let sdetail = {
            contract_id: data.proposal_open_contract.contract_id,
            listNo: listNo,
            contract_type: data.proposal_open_contract.contract_type,
            current_spot_time: data.proposal_open_contract.current_spot_time,
            current_spot_price: data.proposal_open_contract.current_spot,
            is_sold: data.proposal_open_contract.is_sold,
            profit: data.proposal_open_contract.profit
         }
         tradeList[lastIndex].profit = data.proposal_open_contract.profit;
         tradeList[lastIndex].detailTradeList.push(sdetail);
         document.getElementById("tradeResultTxt").value = JSON.stringify(tradeList);


         // ตรวจสอบสถานะของสัญญา
         if (data.proposal_open_contract.is_sold) {
            console.log('สัญญาสิ้นสุดแล้ว ', 'กำไร/ขาดทุน:', data.proposal_open_contract.profit);
            tradeList = JSON.parse(document.getElementById("tradeResultTxt").value);
            lastIndex = tradeList.length - 1;

            winstatusId = 'winstatus_' + contractId;
            if (data.proposal_open_contract.profit > 0) {
               winStatus = 'Win';
			   LossCon = 0 ;
               document.getElementById("lossCon").value = LossCon;
			   document.getElementById("currentLotNo").value = parseInt(document.getElementById("currentLotNo").value) +1;
               tradeList[lastIndex].winStstatus = 'Win';
            }
            if (data.proposal_open_contract.profit < 0) {
               winStatus = 'Loss';
			   LossCon++;
               document.getElementById("lossCon").value = LossCon;
               tradeList[lastIndex].winStstatus = 'Loss';
            }
            tradeList[lastIndex].profit = data.proposal_open_contract.profit;
            let balance = parseFloat(document.getElementById("balanceTxt").value);

            balance = balance + data.proposal_open_contract.profit;
            document.getElementById("balanceTxt").value = balance.toFixed(2);
            document.getElementById(winstatusId).innerHTML = winStatus;
            CalBalance();
            doAjaxSaveTradeList();

			let currentLotNo = parseInt(document.getElementById("currentLotNo").value);
			let targetLotNo = parseInt(document.getElementById("targetLotNo").value);
			let targetMoney= parseFloat(document.getElementById("TargetMoney").value) ;
			//if (currentLotNo > targetLotNo ) {
            if (balance  >=  targetMoney) {
				//alert('Finished Lot No');
				document.getElementById("isBreakFetchCandle").checked = true;
				playAudio();

			}



         }
      }
      // ตรวจสอบว่าเป็นการตอบกลับของคำสั่งขายหรือไม่
      if (response.sell) {
         if (response.sell.sold) {
            // การขายสำเร็จ
            console.log("ขายสัญญาสำเร็จ!");


            console.log("ราคาขาย:", response.sell.sold_for);
            console.log("กำไร/ขาดทุน:", response.sell.profit);
            alert("ขายสัญญาสำเร็จ!-- " + response.sell.profit);

            // ข้อมูลเพิ่มเติม
            console.log("เงินลงทุน:", response.sell.buy_price);
            console.log("ส่วนต่าง:", response.sell.profit_percentage, "%");

            // อาจดำเนินการเพิ่มเติมหลังจากขายสำเร็จ เช่น อัพเดต UI
         } else {
            // การขายไม่สำเร็จ
            console.error("ไม่สามารถขายสัญญาได้");
            if (response.error) {
               console.error("เหตุผล:", response.error.message);
            }
         }
      }

      // จัดการกับการตอบกลับการ authorize
      if (response.msg_type === 'authorize') {
         if (response.error) {
            console.error('Authentication error:', response.error.message);
            return;
         }

         console.log('Authentication successful');
         console.log('Account info:', response.authorize);

         // ตอนนี้คุณสามารถเริ่มส่งคำขออื่นๆ ที่ต้องการ authentication
         // เช่น ดึงข้อมูลบัญชี, ทำการซื้อขาย, ฯลฯ
         getAccountBalance();

      }
      if (response.msg_type === 'balance') {
         handleBalanceResponse(response);
      }
      if (response.buy) {
         //alert('Buy')

         // จัดการกรณีเทรดสำเร็จ
		 setCurrentState(4);
         displayTradeResult(response);
         contractId = response.buy.contract_id;
         startTrackTrade(websocket, contractId);
      }
   }; // end onme

   websocket.onclose = function () {
      console.log('WebSocket Disconnected:', event.code, event.reason);
      isConnecting = false;
      MAX_RECONNECT_ATTEMPTS = 150;

      // ตรวจสอบจำนวนครั้งในการ reconnect
      if (reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
         console.log(`Attempting to reconnect... (${reconnectAttempts + 1}/${MAX_RECONNECT_ATTEMPTS})`);
         reconnectAttempts++;
         setTimeout(connect, RECONNECT_DELAY);
      } else {
         console.error('Max reconnection attempts reached. Please check your connection.');
         // อาจจะเพิ่มการแจ้งเตือนผู้ใช้หรือทำการ reset การเชื่อมต่อ
      }
   };
}

function subscribeToTime() {
   if (timeSubscription) {
      clearInterval(timeSubscription);
   }

   websocket.send(JSON.stringify({
      "time": 1
   }));
   timeSubscription = setInterval(() => {
      if (websocket && websocket.readyState === WebSocket.OPEN) {
         websocket.send(JSON.stringify({
            "time": 1
         }));
      }
   }, 1000);
}




function SellContract() {

   contractId = document.getElementById("currentContractId").value;
   websocket.send(JSON.stringify({
      sell: contractId,
      price: 0 // ขายด้วยราคาตลาดปัจจุบัน
   }));

   thisProfit = parseFloat(document.getElementById("profit_" + contractId).innerHTML);

   thisBalance = parseFloat(document.getElementById("balanceTxt").value);
   newBalance = thisBalance + thisProfit;
   document.getElementById("balanceTxt").value = newBalance.toFixed(2);
   document.getElementById("winstatus_" + contractId).innerHTML = 'Sell';
   document.getElementById("lossCon").value = parseInt(document.getElementById("lossCon").value) + 1;

   // ตรวจสอบ response ด้วย response.sell
}

function authenticateUser() {
   // Replace 'YOUR_API_TOKEN' with your actual API token from deriv.com
   const authRequest = {
      authorize: 'lt5UMO6bNvmZQaR',
      req_id: 1 // Request ID เพื่อติดตามการตอบกลับ
   };

   websocket.send(JSON.stringify(authRequest));
}

function getAccountBalance() {
   const balanceRequest = {
      balance: 1,
      req_id: 2
   };
   websocket.send(JSON.stringify(balanceRequest));
}

function handleAuthorizeResponse(response) {
   if (response.error) {
      console.error('Authentication error:', response.error.message);
      return;
   }

   console.log('Authentication successful');
   console.log('Account info:', response.authorize);

   // ดำเนินการต่อหลังจาก authentication สำเร็จ
   getAccountBalance();
}

function handleBalanceResponse(response) {
   if (response.error) {
      console.error('Balance request error:', response.error.message);
      return;
   }

   console.log('Balance info:', response.balance);
}


// ฟังก์ชั่นสำหรับตรวจสอบสถานะการเชื่อมต่อ
function checkConnection() {
   if (websocket && websocket.readyState === WebSocket.OPEN) {
      return true;
   }
   return false;
}

// ฟังก์ชั่นสำหรับ force reconnect
function forceReconnect() {
   if (websocket) {
      websocket.close();
   }
   reconnectAttempts = 0;
   connect();
}

function updateServerTime(timestamp) {

   const date = new Date(timestamp * 1000);
   const timeStr = date.toLocaleTimeString();
   document.getElementById('serverTime').textContent = timeStr;

   if (date.getSeconds() === 0) {
      fetchCandles();
   }
}

function fetchCandles() {

   if (document.getElementById("isBreakFetchCandle").checked === true) {
      return;
   }
   setCurrentState(2)  ;

   isProcessing = true;
   const asset = document.getElementById('asset').value;
   const timeframe = parseInt(document.querySelector('input[name="timeframe"]:checked').value);

   const request = {
      "ticks_history": asset,
      "style": "candles",
      "granularity": timeframe * 60,
      "count": 60,
      "end": "latest"
   };

   websocket.send(JSON.stringify(request));
   document.getElementById('status').textContent = 'Fetching candles at ' + new Date().toLocaleTimeString();
}

function formatNumber(num) {
   return Number(num).toFixed(5);
}

function formatDate(date) {
   return date.toLocaleString();
}

function processCandles(data) {

   candles = data.candles;
   asset = data.echo_req.ticks_history;
   endTime = data.echo_req.end;
   //alert(asset);
   const candleData = candles.map(candle => ({
      time: candle.epoch,
      open: candle.open,
      high: candle.high,
      low: candle.low,
      close: candle.close
   }));

   sendToEndpoint(candleData);
   lastTime = new Date(candleData[candleData.length - 1].time * 1000).toLocaleString();
   document.getElementById("CandleStatus").innerHTML = asset + ' :: ' + lastTime;


   if (chart && candleSeries) {
      // Update chart
      candleSeries.setData(candleData);

      // Calculate EMAs
      const ema3Data = calculateEMA(candleData, 3);
      const ema5Data = calculateEMA(candleData, 5);

      ema3Series.setData(ema3Data);
      ema5Series.setData(ema5Data);

      // Update table
      const tbody = document.querySelector('#candleTable tbody');

      document.getElementById("headTableCaption").innerHTML = asset + ' :: ' + endTime;


      tbody.innerHTML = '';
      candles.sort((a, b) => b.epoch - a.epoch);

      candles.forEach((candle, index) => {
         const row = document.createElement('tr');
         const time = new Date(candle.epoch * 1000).toLocaleString();
         // Format timestamp
         const date = new Date(candle.epoch * 1000);
         const formattedDate = formatDate(date);
         row.innerHTML = `
                        <td>${formattedDate}</td>
                        <td>${formatNumber(candle.open)}</td>
                        <td>${formatNumber(candle.high)}</td>
                        <td>${formatNumber(candle.low)}</td>
                        <td>${formatNumber(candle.close)}</td>
                        <td class="ema3">${formatNumber(ema3Data[index].value)}</td>
                        <td class="ema5">${formatNumber(ema5Data[index].value)}</td>
                    `;
         tbody.appendChild(row);
      });
   }

}



function getMartingale() {

   lossCon = parseInt(document.getElementById("lossCon").value);
   if (lossCon == 0) {
      amount = 1;
   }
   if (lossCon == 1) {
      amount = 2;
   }
   if (lossCon == 2) {
      amount = 4;
   }
   if (lossCon == 3) {
      amount = 6;
   }
   if (lossCon == 4) {
      amount = 16;
   }
   if (lossCon == 5) {
      amount = 32;
   }

   return amount;


} // end func


// ฟังก์ชันสำหรับส่งคำสั่งเทรด
function placeTrade(contractType) {

   if (!websocket || websocket.readyState !== WebSocket.OPEN) {
      alert('Not connected to server');
      return;
   }
   if (contractType === 'Idle') {
      return;
   }
   setCurrentState(3)  ;

   //const amount = document.getElementById('amount').value;
   //const duration = document.getElementById('duration').value;
   /*const amount = 1;
	if (document.getElementById("isuseMartinGale").checked === true) {
       const amount = getMartingale() ;
	}
	*/
   const amount = parseFloat(document.getElementById("moneyTrade").value);


   symbol = document.getElementById("asset").value;
   const duration = getRadioValue('timeframe');


   // ปรับพารามิเตอร์สำหรับการเทรดให้เหมาะสม
   const request = {
      buy: 1,
      price: parseFloat(amount),
      parameters: {
         amount: parseFloat(amount),
         basis: "stake",
         contract_type: contractType,
         currency: "USD",
         duration: parseInt(duration),
         duration_unit: "m",
         symbol: symbol
      }
   };


   websocket.send(JSON.stringify(request));
   console.log('Sending trade request 999:', request);
}


// จัดการข้อความที่ได้รับจาก WebSocket
function handleMessage(message) {
   try {
      const response = JSON.parse(message.data);
      console.log('ได้รับการตอบกลับ:', response);

      if (response.error) {
         // จัดการกรณีเกิดข้อผิดพลาด
         updateTradeStatus(`เกิดข้อผิดพลาด: ${response.error.message}`, 'error');
         return;
      }

      if (response.buy) {
         //alert('Buy')
         // จัดการกรณีเทรดสำเร็จ
         console.log('Buy Response', response)

         displayTradeResult(response);
      }
   } catch (error) {
      console.error('เกิดข้อผิดพลาดในการประมวลผลข้อความ:', error);
   }
}

// ฟังก์ชันสำหรับติดตามการเทรด
function startTrackTrade(ws, contractId) {
   const request = {
      proposal_open_contract: 1,
      contract_id: contractId,
      subscribe: 1 // ขอ subscribe ข้อมูลเพื่อติดตามการเปลี่ยนแปลง
   };

   ws.send(JSON.stringify(request));
   console.log(`Started tracking trade ${contractId}`);
}





// อัพเดทสถานะการเชื่อมต่อ
function updateConnectionStatus(message, status) {
   const statusDiv = document.getElementById('connection-status') || createStatusElement('connection-status');
   statusDiv.textContent = message;
   statusDiv.className = `status-message ${status}`;
}

// อัพเดทสถานะการเทรด
function updateTradeStatus(message, status) {
   const statusDiv = document.getElementById('trade-status') || createStatusElement('trade-status');
   statusDiv.textContent = message;
   statusDiv.className = `status-message ${status}`;
}

// สร้าง element สำหรับแสดงสถานะ
function createStatusElement(id) {
   const element = document.createElement('div');
   element.id = id;
   element.className = 'status-message';
   document.body.insertBefore(element, document.body.firstChild);
   return element;
}


async function sendToEndpoint(candlesData) {

   doAjaxNewTrade(candlesData);
   return;


} // end function

// Event listeners
document.querySelectorAll('input[name="timeframe"]').forEach(radio => {
   radio.addEventListener('change', function (e) {
      selectedTimeframe = parseInt(e.target.value);
      fetchCandles();
   });
});

document.getElementById('asset').addEventListener('change', function () {
   fetchCandles();
});

// Start the connection when the page loads
window.addEventListener('load', connect);




/*
Step
  1.Connect-->Authen-->getCandle-->Placerade-->response.buy-->TrackTrade-->Sell-->response.sell


  const request = {
      buy: 1,
      price: 1,
      parameters: {
         amount:1,
         basis: "stake",
         contract_type: 'CALL',
         currency: "USD",
         duration: 1,
         duration_unit: "m",
         symbol: 'R_100'
      }
   }; ได้ response
   {
  "buy": {
    "balance_after": 9900.62,
    "contract_id": 12345678,
    "buy_price": 1,
    "purchase_time": 1600000001,
    "start_time": 1600000001,
    "transaction_id": 87654321,
    "longcode": "Win payout if R_100 is strictly higher than entry spot at 1 minute after contract start time.",
    "payout": 1.95,
    "shortcode": "CALL_R_100_1.00_1600000001_1m_0",
    "currency": "USD"
  },
  "echo_req": {
    "buy": 1,
    "price": 1,
    "parameters": {
      "amount": 1,
      "basis": "stake",
      "contract_type": "CALL",
      "currency": "USD",
      "duration": 1,
      "duration_unit": "m",
      "symbol": "R_100"
    }
  },
  "msg_type": "buy"
}

*/