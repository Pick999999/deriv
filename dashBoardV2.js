//chart-container
/*
   globalvar
   globalconst
   connect
   auth
   timeserver
   requestCandles
   analysis
   buy
   track
   sale
   report
   chart
   // ขาดเหลือ เติมจาก  testacp.php + testacp.js
*/

/********************** global Var Declare ******************/
let websocket = null;
let selectedTimeframe = 1;
let isProcessing = false;
let isConnecting = false;
let reconnectAttempts = 0;
let timeSubscription = null;
let AllSymBolList = null;
let priceAtFirstSecond = 0 ;
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
let listTrade = [];
let candleData = null;
let currentAsset = null;

// Chart Declare
let chart = null;
let candleSeries = null;
let ema3Series = null;
let ema5Series = null;

/********************** Const Declare ******************/
const authRequest = JSON.stringify({
      authorize: 'lt5UMO6bNvmZQaR',
      req_id: 1 // Request ID เพื่อติดตามการตอบกลับ
   });

const timeSubscriptString = JSON.stringify({ "time": 1 });





$(document).ready(function () {
  console.log("Hello World!");
  InitChart();
  createTable();
 // connect() ;
 // showAssetChart(asset='R_100');
});

function connect() {
   if (isConnecting) { DisConnect();  return ;}
   //isConnecting = true;
   websocket = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
   websocket.onopen = function() {
        console.log('เชื่อมต่อกับ Deriv API สำเร็จ');
		document.getElementById("btnConnect").innerHTML = 'DisConnect';
	    isConnecting = true;
	    authenticateUser();
	    subscribeToTime();
	    showAssetChart(asset='R_100');
	    fetchCandles();
        // คุณสามารถเรียก function เพื่อส่ง request หลังจากเชื่อมต่อสำเร็จ
    };
    websocket.onmessage = function (msg) {
      const response = JSON.parse(msg.data);
	  handleResponse(response);
	  return ;
    }

    websocket.onerror = function(error) {
        console.error('เกิดข้อผิดพลาดในการเชื่อมต่อ:', error);
    };

    websocket.onclose = function() {
        console.log('การเชื่อมต่อกับ Deriv API ถูกปิด');
    };

} // end function connect

function authenticateUser() {
         websocket.send(authRequest);
} // end func

function subscribeToTime() {
   if (timeSubscription) {
      clearInterval(timeSubscription);
   }

   timeSubscription = setInterval(() => {
      if (websocket && websocket.readyState === WebSocket.OPEN) {
         websocket.send(timeSubscriptString);
      }
   }, 1000);
}
function updateServerTime(timestamp) {

   const date = new Date(timestamp * 1000);
   const timeStr = date.toLocaleTimeString();
   //console.log(timeStr);

   document.getElementById('current-time').textContent = timeStr;
   document.getElementById('current-time2').textContent = timeStr;


   if (date.getSeconds() === 0) {
     // fetchCandles();
	   //console.log('At Fir')

   }
   document.getElementById("secondTime").value = 60- date.getSeconds();
}

function handleResponse(response) {


         // console.log(response.msg_type);
	 if (response.msg_type === 'authorize') {
	     console.log('authen Success',response);
		 document.getElementById("myBalance").innerHTML = response.authorize.balance ;
      }
      if (response.msg_type === 'time') { updateServerTime(response.time); }

	 if (response.msg_type === 'ohlc') {
            console.log('ข้อมูลแท่งเทียนแบบเรียลไทม์:');
            console.log(response.ohlc);
            sAll = JSON.parse(document.getElementById("txtchartData").value);
	 }
	 if (response.msg_type === 'candles') {
	     //data2 = candleDataProcess(response.candles);
	     //console.log('data2.candleData',response.candles);
		 candleData = response.candles;
		 //console.log('Candle Data',candleData);

		 updateChartData();
             //document.getElementById("txtchartData").value =  JSON.stringify(data2) ;
         }

         // Handle history data
         if (response.msg_type === 'history' ) {
	     console.log('history',response)
          //this._handleHistoryData(data);
           return;
         }
         if (response.msg_type === 'buy') {
           console.log('Buy',response);
           requestTrackTrade(response.buy.contract_id);
           newRowTable(response);
         }

        // ตรวจสอบว่าเป็นข้อมูลของสัญญาที่เราติดตามอยู่หรือไม่
        if (response.proposal_open_contract) {
	      //console.log('ข้อมูลสัญญา:', response);
          //thisContractId = response.proposal_open_contract.contract_id ;
	      UpdateTrackTable(response.proposal_open_contract);
        }

} // end func



function DisConnect() {

      document.getElementById("btnConnect").innerHTML = 'Connect Again';

      websocket.close();
      websocket = null;
	  console.log('DisConnect')


} // end func

function cancelCurrentSubscription() {
    if (timeSubscription) {
        clearInterval(timeSubscription);
        timeSubscription = null;
        console.log('Subscription cancelled');
    }
}

function fetchCandles(numDuration=1,durationUnit='m',btnID) {
	const timeframes = {
    "1M": 60,        // 1 minute
    "5M": 300,       // 5 minutes
    "15M": 900,      // 15 minutes
    "30M": 1800,     // 30 minutes
    "1H": 3600,      // 1 hour
    "2H": 7200,      // 2 hours
    "4H": 14400,     // 4 hours
    "8H": 28800,     // 8 hours
    "1D": 86400      // 1 day
  };
  // granularity = จำนวนวินาที
  count = 60 ;
  if (numDuration+durationUnit ==='1m') { granurality=60 ;  }
  if (numDuration+durationUnit ==='5m') { granurality=60 *5 ; }
  if (numDuration+durationUnit ==='15m') { granurality=60 *15; }
  if (numDuration+durationUnit ==='30m') { granurality=60 *30 ; }
  if (numDuration+durationUnit ==='1h') { granurality=60 *60 ; count=24 }
  if (numDuration+durationUnit ==='2h') { granurality=2*60 *60 ; count=12 }
  if (numDuration+durationUnit ==='4h') { granurality=4*60 *60 ; count=6 }
  if (numDuration+durationUnit ==='8h') { granurality=8*60 *60 ; count=6 }
  if (numDuration+durationUnit ==='1d') { granurality=24*60 *60 ; count=30 }

  $("#"+btnID).addClass('active') ;
  document.getElementById("timeFrameSel").value = numDuration+':'+durationUnit;



   isProcessing = true;
   //const asset = document.getElementById('asset').value;
   asset = 'R_100';
   document.getElementById("watchAsset").innerHTML = asset;

   //const timeframe = parseInt(document.querySelector('input[name="timeframe"]:checked').value);
   const timeframe = 1;
   const requestCandleHist = {
      "ticks_history": asset,
      "style": "candles",
      "granularity": granurality,
      "count": count,
      "end": "latest"
   };
  // cancelCurrentSubscription();

   timeSubscription = setInterval(() => {
      if (websocket && websocket.readyState === WebSocket.OPEN) {
         websocket.send(JSON.stringify(requestCandleHist));
      }
   }, 1000);

   //websocket.send(JSON.stringify(requestCandleHist));
   //document.getElementById('status').textContent = 'Fetching candles at ' + new Date().toLocaleTimeString();
}

function formatCandleData(candle) {
         return {
            time: parseInt(candle.epoch || candle.time),
            open: parseFloat(candle.open),
            high: parseFloat(candle.high),
            low: parseFloat(candle.low),
            close: parseFloat(candle.close)
         };
}

function InitChart() {

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
                candleSeries = chart.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderDownColor: '#ef5350',
                    borderUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                    wickUpColor: '#26a69a',
                });

                ema3Series = chart.addLineSeries({
                    color: '#0080ff',
                    lineWidth: 2,
                });

                ema5Series = chart.addLineSeries({
                    //color: 'rgba(0, 123, 255, 1)',
					color: '#ff0000',
                    lineWidth: 2,
                });
            }

            // Set chart title
            chart.applyOptions({
                title: `${assetCode} - Real-time Candlestick with EMA3 & EMA5`,
            });

} // end InitChart

// Show chart for a specific asset
        function showAssetChart(asset) {
            currentAsset = asset;
            //document.getElementById("assetCode").value = asset;
            //document.getElementById("assetDesc").innerHTML = '<h2>'+ asset + '</h2>';
            // Initialize chart if not already done
            if (!chart) {
				InitChart();
            }

            // Set chart title
            chart.applyOptions({
                title: `${asset} - Real-time Candlestick with EMA3 & EMA5`,
            });

            // Initial data load

        }

	function drawPriceLine() {
            // Remove previous line if exists
            if (closePriceLine) {
                candleSeries.removePriceLine(closePriceLine);
            }
	        priceLineValue = parseFloat(document.getElementById("priceLineValue").value)
            // Create new price line
            closePriceLine = candleSeries.createPriceLine({
                price: priceLineValue,
                color: '#2196F3',
                lineWidth: 2,
                lineStyle: LightweightCharts.LineStyle.Solid,
                axisLabelVisible: true,
                title: 'Close',
            });
        }

	function updateChartData() {

            //if (!currentAsset || !candleData[currentAsset]) return;

            //const candles = candleData[currentAsset];
            const candles = candleData;
            if (candles.length === 0) return;

            // Prepare data for the chart
            const candleDataForChart = candles.map(c => ({
                time: c.epoch,
                open: c.open,
                high: c.high,
                low: c.low,
                close: c.close,
            }));

			//current-price-display
			lastPrice = candles[candles.length-1].close;
            //document.getElementById("current-price-display").innerHTML = lastPrice;
			document.getElementById("current-price-display").textContent = lastPrice.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
             });
/*
             lastIndex =  candles.length-1 ;
             sTime =  candles[lastIndex].epoch;
			//priceAtFirstSecond
			 console.log(candles[lastIndex])

			 const date = new Date(sTime * 1000);
			 console.log(date.getSeconds())

             if (date.getSeconds() === 0) {
			  priceAtFirstSecond = candles[candles.length-1].close ;
			  document.getElementById("priceAtFirstSecond").innerHTML = priceAtFirstSecond;
             }
			 priceDiff = lastPrice - priceAtFirstSecond ;
			 console.log(lastPrice,' - ' ,priceAtFirstSecond)

             document.getElementById("priceDiff").innerHTML = priceDiff;

*/

            // Calculate EMAs
           const ema3 = calculateEMA(candles, 3);
           const ema5 = calculateEMA(candles, 5);

            // Update the chart
           candleSeries.setData(candleDataForChart);
		   ema3Series.setData(ema3.map((value, index) => ({
			time: candleDataForChart[index].time,
			value: value !== null ? value : undefined
			// หรือถ้าต้องการข้ามค่า null ให้ใช้: value: value !== null ? value : undefined
			})));

			ema5Series.setData(ema5.map((value, index) => ({
				time: candleDataForChart[index].time,
				value: value !== null ? value : undefined
				// หรือถ้าต้องการข้ามค่า null ให้ใช้: value: value !== null ? value : undefined
			})));



            // Adjust time scale to fit data
            //chart.timeScale().fitContent();
        }

// Calculate EMA (Exponential Moving Average)
function calculateEMA(candles, period) {
    const numElements = candles.length;
    // สร้าง array ที่มีขนาดเท่ากับ candles และเริ่มต้นด้วยค่า null ทั้งหมด
    const ema = new Array(numElements).fill(null);

    const multiplier = 2 / (period + 1);

    // คำนวณ Simple Moving Average สำหรับค่าแรก
    let sum = 0;
    for (let i = 0; i < period && i < candles.length; i++) {
        sum += candles[i].close;
    }

    // กำหนดค่า SMA ที่ตำแหน่ง period - 1
    ema[period - 1] = sum / period;

    // คำนวณ EMA สำหรับค่าถัดไป
    for (let i = period; i < candles.length; i++) {
        ema[i] = (candles[i].close - ema[i - 1]) * multiplier + ema[i - 1];
    }

    return ema;
}

// Buy Section
//BuyContract()
function BuyContract(action) {

	 amount=10 ;

	 //duration = document.getElementById("numDuration").value ;
	 duration = 57 ;  // จำนวน tick, นาที ,ชั่วโมงและ 1 tick = 2 วินาที
	 duration = parseInt(document.getElementById("secondTime").value)-4-3-1;
	 duration_unit = "s" ;
	 //duration_unit = "m" ;
    // symbol = document.getElementById("asset").value ;
	 symbol = 'R_100' ;

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
	 websocket.send(JSON.stringify(request1));



} // end func

function requestTrackTrade(contractId) {
   const request = {
      proposal_open_contract: 1,
      contract_id: contractId,
      subscribe: 1 // ขอ subscribe ข้อมูลเพื่อติดตามการเปลี่ยนแปลง
   };

   websocket.send(JSON.stringify(request));
   console.log(`Started tracking trade ${contractId}`);
}

function createTable() {

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

     no=1;
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

} // end func

function UpdateTrackTable(jsonObj) {

//captionList ='0-ลำดับ,1-เลขสัญญา,2-contract_type,3-ราคาเข้าซื้อ,
//4-ราคาปัจจุบัน,5-เหลือเวลา,6-สิ้นสุด,7-ผล,8-กำไร,9-บาท' ;


    rowId = 'tr_' + jsonObj.contract_id;
    let thisRow = document.getElementById(rowId);
    thisRow.cells[4].innerHTML = jsonObj.entry_spot;
    thisRow.cells[5].innerHTML = jsonObj.current_spot ;
    thisRow.cells[6].innerHTML = jsonObj.expiry_time - jsonObj.current_spot_time ;
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

function SaleContract(contractID) {

         websocket.send(JSON.stringify({
            sell: contractID,
            price: 0 // ขายด้วยราคาตลาดปัจจุบัน
         }));


} // end func






