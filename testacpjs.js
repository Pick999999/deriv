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
*/


/********************** global Var Declare ******************/
let websocket = null;
let selectedTimeframe = 1;
let isProcessing = false;
let isConnecting = false;
let reconnectAttempts = 0;
let timeSubscription = null;
let AllSymBolList = null;
let chart = null;
let chartOHLC = null;
let candleSeries = null;
let candleData = null;
let candleDataOHLC = [];
let ema3Series = null;
let ema5Series = null;
let tradeHistory = null;
let assetCode = null ;
let warnNumcheck = null ;
let workingCode = null;
let workingDesc = ['Idle','RequestCandle','AjaxGetAction','PlaceTrade','Wait Result'];
let CurrentLotNo = 1 ;
let totalRowTrade = 0 ;
let LossCon = 0 ;

let useMartinGale = false;
//let MoneyTradeList = [1,2,4,9,22,40,54,54,54,54,54,54,54] ;
let MoneyTradeList = [1,2,6,18,54,162,384,54,54,54,54,54,54] ;

let startMoneyTrade = 1;
let thisMoneyTrade = 1 ;
let maxMoneyTrade = 1 ;
let balanceTime = 0 ;
let listTrade = [];
let clsIndy = new IndicatorCalculator();
//let clsanalyzeIndy = new analyzeIndy();
let beginOfMinute = false ;
let previousTurnType = null;
let currentTurnType = null;
let closePriceLine = null;
let closePriceLineOHLC = null;

let lastClosePrice = 0 ;

let targetPriceLine = null;
let targetPriceLineOHLC = null;

let currentContractType = '';
let currentContractID = '';
let currentProfit = 0 ;
let autoSale = false;
let isplayAudio = false;
let emaAbove = null;
let ohlcMarker = [] ;
let curOHLC = null;
let ohlcList = [] ;
let tradeTable = document.getElementById("tradeTable");

let isCheckTarget = null;
let targetProfit = 0;
let canBeTrade = false ;
let isOnTrade = false ;

/********************** Const Declare ******************/
const authRequest = JSON.stringify({
      authorize: 'lt5UMO6bNvmZQaR',
      req_id: 1 // Request ID เพื่อติดตามการตอบกลับ
   });

const timeSubscriptString = JSON.stringify({ "time": 1 });
let CurrentSignalObject = null ;


function connect() {
   if (isConnecting) return;
   //isConnecting = true;
   websocket = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
   websocket.onopen = function() {
    console.log('เชื่อมต่อกับ Deriv API สำเร็จ');
	$("#btnConnect").addClass('glow-basic');
	isConnecting = true;
	authenticateUser();
	//getLocal();


	subscribeToTime();
	doAjaxsetPageStatus();
	InitVar();
	//fetchCandles('c');
	//fetchCandles('o');

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

function InitVar() {

	     isCheckTarget = document.getElementById("checkTarget").checked;
	     targetProfit = parseFloat(document.getElementById("targetProfit").value);



} // end func


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
   document.getElementById('serverTime').textContent = timeStr;
   document.getElementById('timeserver2').textContent = timeStr;
   document.getElementById('timeserver3').textContent = timeStr;
   document.getElementById('timeserver4').textContent = timeStr;


   if (date.getSeconds() === 0) {
     // fetchCandles();
	 //alert('New Minute');
	 beginOfMinute = true ;
	 console.log('At updateTimeServer',beginOfMinute);
	 doAjaxgetAction();

   }

   if (date.getSeconds() === 30) {
      doAjaxSaveTrade();
   }


   if (date.getSeconds() === 45  ) {
	   console.log('canBeTrade',canBeTrade) ;

	   if (canBeTrade === false) {
          doAjaxsetPageStatus();
	   }
   }
}

function handleResponse(response) {


     //console.log(response.msg_type);
	 if (response.msg_type === 'authorize') {
	     console.log('authen Success',response);
     }
     if (response.msg_type === 'time') { updateServerTime(response.time); }

	 if (response.msg_type === 'ohlc') {
            //console.log('ข้อมูลแท่งเทียนแบบเรียลไทม์ ohlc:');
            //console.log(response.ohlc);
			const candle = {
                time: parseFloat(response.ohlc.epoch + (7 * 3600)  ),
                open: parseFloat(response.ohlc.open),
                high: parseFloat(response.ohlc.high),
                low: parseFloat(response.ohlc.low),
                close: parseFloat(response.ohlc.close)
            };
            // console.log('ohlc',candle)

            //candleData= [];
            candleDataOHLC.push(candle) ;
			//console.log(candleData)
			updateChartDataOHLC();
            //sAll = JSON.parse(document.getElementById("txtchartData").value);
	 }
	 if (response.msg_type === 'candles') {
	     //data2 = candleDataProcess(response.candles);
	     // console.log('data2.candleData',response);
		  candleData = response.candles ;
		  //console.log('CandleData',candleData);

		  updateChartData();
             //document.getElementById("txtchartData").value =  JSON.stringify(data2) ;
         }

         // Handle history data
     if (response.msg_type === 'history' ) {
	     //console.log('history',response)
          //this._handleHistoryData(data);
           return;
     }
	 if (response.msg_type === 'buy') {
         //console.log('Buy',response);

	     requestTrackTrade(response.buy.contract_id);
		 newRowTable(response);
		 isOnTrade = true;
     }

	 // ตรวจสอบว่าเป็นข้อมูลของสัญญาที่เราติดตามอยู่หรือไม่
     if (response.proposal_open_contract) {
		 //console.log('ข้อมูลสัญญา:', response);
         //thisContractId = response.proposal_open_contract.contract_id ;
		 UpdateTrackTable(response.proposal_open_contract);
	 }

	 if (response.msg_type === 'sell') {
         //console.log('sell',response);
		 thisid= 'tr_' +  response.sell.contract_id ;
		 tr  = document.getElementById(thisid);
		 cost = parseFloat(tr.cells[4].innerHTML);
		 if (cost < response.sell.sold_for ) {
			 profit = response.sell.sold_for  ;
		 } else {
			 profit = response.sell.sold_for - cost ;
		 }
		 isOnTrade = false;
		 CalPocketMoney();

     }



} // end func


function DisConnect() {

      websocket.close();
      websocket = null;
	  $("#btnFetchCandle").removeClass('glow-basic') ;
	  $("#btnConnect").removeClass('glow-basic') ;
	  isConnecting = false;




} // end func

function saveLocal() {

         isPlayAudio=document.getElementById("playAudio").checked;
	     sObj = {
           MoneyTrade: document.getElementById("moneyTrade").value ,
           TargetProfit : document.getElementById("targetProfit").value ,
		   Asset : document.getElementById("asset").value ,
		   playAudio : isPlayAudio
		 }
         localStorage.setItem('testacp',JSON.stringify(sObj));

} // end func

function getLocalData() {

         testacp = JSON.parse(localStorage.getItem('testacp')) ;
		 document.getElementById("moneyTrade").value=testacp.MoneyTrade;
		 document.getElementById("targetProfit").value = testacp.TargetProfit ;
         document.getElementById("asset").value = testacp.Asset;
		 document.getElementById("playAudio").checked = testacp.playAudio;

		 isPlayAudio = testacp.playAudio;


} // end func


function setplayAudio() {

         isPlayAudio=document.getElementById("playAudio").checked;
		 //saveLocal();

} // end func


function fetchCandles(candletype) {


   isProcessing = true;
   const assetText = document.getElementById('asset').value;
   //const timeframe = parseInt(document.querySelector('input[name="timeframe"]:checked').value);
   //const timeframe = 1;
   const timeframe = document.getElementById("candleTF").value ;
   $("#btnFetchCandle").addClass('glow-basic');
   if (candletype==='c') {
	   const requestCandleHist = {
		  "ticks_history": assetText,
		  "style": "candles",
		  "granularity": timeframe * 60,
		  "count": 60,
		  "end": "latest"
	   };
	   timeSubscription = setInterval(() => {
		  if (websocket && websocket.readyState === WebSocket.OPEN) {
			 websocket.send(JSON.stringify(requestCandleHist));
		  }
	   }, 1000);
   }

   if (candletype==='o') {
	   const requestCandleHist = {
		  "ticks_history": assetText,
		  "style": "candles",
		  "granularity": timeframe * 60,
		  "count": 60,
		  "end": "latest",
          "subscribe" :1
	   };
	   timeSubscription = setInterval(() => {
		  if (websocket && websocket.readyState === WebSocket.OPEN) {
			 websocket.send(JSON.stringify(requestCandleHist));
		  }
	   }, 1000);
   }
   console.log('Fetch Candle Start',assetText);


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

//BuyContract()
function BuyContract(action,callFromButton=false) {

/*
	 if (document.getElementById("isOpenTrade").checked === false && callFromButton==false) {
		 return ;
	 }
*/

	 if (canBeTrade === false) {
		 return ;
	 }
	 if (beginOfMinute === false) {
		 console.log('beginOfMinute',beginOfMinute)
		 return ;
	 }


	 //action = action.replace(/\n/g, '');

	 amount=10 ;
	 document.getElementById("autoSale").checked = false;

	 duration = parseInt(document.getElementById("numDuration").value)-5 ;
	 duration = 55 ;  // จำนวน tick, นาที ,ชั่วโมงและ 1 tick = 2 วินาที
	 duration_unit = "s" ;
	 //duration_unit = "m" ;
     symbol = document.getElementById("asset").value ;
	 currentContractType = action ;
	 //duration = 30 ;  // จำนวน tick, นาที ,ชั่วโมงและ 1 tick = 2 วินาที
	 amount= parseFloat(document.getElementById("moneyTrade").value) ;
	 amount = thisMoneyTrade;

	 if ( thisMoneyTrade > maxMoneyTrade ) {
         maxMoneyTrade = thisMoneyTrade ;
		 console.log(maxMoneyTrade);
	 }


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
	// console.log(JSON.stringify(request1));



	 document.getElementById("priceLineValue").value = '';
	 document.getElementById("profitLineValue").value = '';



} // end func

function requestTrackTrade(contractId) {
   const request = {
      proposal_open_contract: 1,
      contract_id: contractId,
      subscribe: 1 // ขอ subscribe ข้อมูลเพื่อติดตามการเปลี่ยนแปลง
   };

   websocket.send(JSON.stringify(request));
//   console.log(`Started tracking trade ${contractId}`);
}

function checkisNewContract(thisContractId) {


   for (i=0;i<=listTrade.length-1 ;i++ ) {
	   if (listTrade[i] ===  thisContractId ) {
		   return false;
	   }
   } // end for

   return true;

} // end func

function createTable() {

			let no =1 ;
			captionList ='ลำดับ,LotNo,เลขสัญญา,contract_type,จำนวนเงิน,เวลาเข้าซื้อ,ราคาปิด<br>แท่งล่าสุด,ราคาเข้าซื้อ,ราคาปัจจุบัน,เหลือเวลา,สิ้นสุด,ผล,กำไร,บาท' ;
			captionAr = captionList.split(',');

			st = '<table  id="tradeTable" class="mtable">'; st += '<tr>';
			for (i=0;i<=captionAr.length-1 ;i++ ) {
			   st += '<th>' + captionAr[i] + '</th>';
			}
			st += '</tr>';
 			st += '</table>';
            document.getElementById("tradeTableContainer").innerHTML = st;

} // end func

function newRowTable(jsonObj) {

            //console.log('jsonObj',jsonObj) ;


			//let no =1 ;
			let no = document.getElementById("tradeTable").rows.length ;
			captionList ='ลำดับ,LotNo,เลขสัญญา,contract_type,จำนวนเงิน,เวลาเข้าซื้อ,ราคาเข้าซื้อ,ราคาปัจจุบัน,เหลือเวลา,สิ้นสุด,ผล,กำไร,บาท' ;
			contractType = jsonObj.echo_req.parameters.contract_type ;
			contractType =contractType+'::'+document.getElementById("actionReason").value + '::'+document.getElementById("actionCaseNo").value ;
			balanceTime  = jsonObj.expiry_time - jsonObj.date_start ;
			MoneyTrade   = jsonObj.buy_price ;
			balanceTime  = jsonObj.expiry_time  - jsonObj.current_spot_time ;
			MinuteRemain =  parseInt((balanceTime/60)) ;
			SecondRemain =  parseInt((balanceTime % 60)) ;

/*
 sObj = {
      tradeno: thisNo,
      lotNo  : LotNo,
      contract_id: response.buy.contract_id,
      transaction_id: response.buy.transaction_id,
      asset: response.echo_req.parameters.symbol,
      contract_type: response.echo_req.parameters.contract_type,
      purchaseTime: response.buy.purchase_time,
      startTime: response.buy.start_time,
      endTime: response.buy.start_time + (60 * parseInt(response.echo_req.parameters.duration)),
      duration: response.echo_req.parameters.duration + response.echo_req.parameters.duration_unit,
      amount: response.echo_req.parameters.amount,
      payout: response.buy.payout,
      winStatus: '',
      profit: 0,
      sCode: document.getElementById("sCode").value,
      detailTradeList: []
   }
*/
			document.getElementById("priceLineValue").value = jsonObj.entry_spot;
			// console.log('Balance Time',balanceTime);
			balanceStr = balanceTime.toString()+'-'+ MinuteRemain.toString()+':'+SecondRemain.toString();
			currentContractID =  jsonObj.buy.contract_id;

			captionAr = captionList.split(',');
			sBath = jsonObj.profit *33 ;
			valueList = [no,CurrentLotNo,jsonObj.buy.contract_id,
				        contractType,jsonObj.buy.buy_price,
				        convertUnixTimestampToHHMMSS(jsonObj.buy.purchase_time),
						lastClosePrice,0,
				        '-',0,
				        '',
						0,
				        0

			] ;
			/*
			//profitLimit = parseFloat(document.getElementById("profitLimit").value) ;
			if (profitLimit != 0 && jsonObj.profit >= profitLimit ) {
              // SaleContract(jsonObj.contract_id);
			}
			*/
			//alert(contractType);






			st = '<tr id="tr_' + jsonObj.buy.contract_id + '">';
			st = '';
			for (i=0;i<=valueList.length-1 ;i++ ) {
               if (i !== 9) {
			     st += '<td>' + valueList[i]+ '</td>';
			   } else {
                 st += '<td><span id="profit">' + valueList[i]+ '</span><hr>';
                 st +='<button id="SaleBtn" onclick="SaleContract('+ jsonObj.buy.contract_id +')">Sale</button> </td>';
			   }
			} // end for
		    st +='<td><button id="SaleBtn_'+ jsonObj.buy.contract_id + '" class="green" onclick="SaleContract('+ jsonObj.buy.contract_id +')">Sale</button> </td>';
			st += '</tr>';
			//console.log(st);

			//document.getElementById("tradeTable").innerHTML = st+ document.getElementById("tradeTable").innerHTML  ;

			tradeTable = document.getElementById("tradeTable");
			const newRow = tradeTable.insertRow(1); // แทรกที่ตำแหน่ง 1
			newRow.id = 'tr_' + jsonObj.buy.contract_id ; // กำหนด ID
			newRow.innerHTML = st ;

			CurrentSignalObject.contractID = jsonObj.buy.contract_id ;
			CurrentSignalObject.contractType = contractType ;
			CurrentSignalObject.startTimeTrade = jsonObj.buy.start_time ;
			CurrentSignalObject.moneyTrade= jsonObj.buy.buy_price ;
            CurrentSignalObject.lastCandleClosePrice  = jsonObj.buy.buy_price ;
			CurrentSignalObject.targetProfit = targetProfit ;
			CurrentSignalObject.timeframeSecond = document.getElementById("numDuration").value ;
			//console.log('CurrentSignalObject',CurrentSignalObject);



/*
			CurrentSignalObject = {
          assetCode : null,
          thisTimeRequest : null,
          signalResponse : null,
		  tradeAction : null ,
          moneyTrade : null,
          timeframeSecond : null,
          contractID : null,
		  contractType : null,
		  buyTime : null,
		  lastClosePrice : null,
		  entrySpotPrice : null,
          winStatus : null,
          profit : null,
          ohlcData : null

	 }
*/

} // end func

function convertTimestampToHHMMSS(timestamp) {
  // Ensure the timestamp is a non-negative number
  const totalSeconds = Math.max(0, Math.floor(timestamp));

  // Calculate hours, minutes, and seconds
  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;

  // Add leading zeros if needed
  const formattedHours = hours.toString().padStart(2, '0');
  const formattedMinutes = minutes.toString().padStart(2, '0');
  const formattedSeconds = seconds.toString().padStart(2, '0');

  // Return the formatted time string
  return `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
}

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


function UpdateTrackTable(jsonObj) {


//captionList ='0-ลำดับ,1-เลขสัญญา,2-contract_type,3-ราคาเข้าซื้อ,
//4-ราคาปัจจุบัน,5-เหลือเวลา,6-สิ้นสุด,7-ผล,8-กำไร,9-บาท' ;

             if (document.getElementById("priceLineValue").value === ''
			 || document.getElementById("priceLineValue").value === 'undefined' )
			 {
				document.getElementById("priceLineValue").value = jsonObj.entry_spot;
				drawPriceLine();
             }
			 document.getElementById("currentContractID").value = jsonObj.contract_id;
			 rowId = 'tr_' + jsonObj.contract_id;

			 balanceTime = jsonObj.expiry_time - jsonObj.current_spot_time;
	         balanceTimeStr = Math.floor(balanceTime /60) + ':' + (balanceTime % 60);
			 //console.log(rowId);
			 startCol  = 5;
			 let thisRow = document.getElementById(rowId);
			 //thisRow.cells[startCol].innerHTML = jsonObj.buy_price
			 thisRow.cells[startCol+2].innerHTML = jsonObj.entry_spot;
             thisRow.cells[startCol+3].innerHTML = jsonObj.current_spot ;
			 thisRow.cells[startCol+4].innerHTML = jsonObj.expiry_time - jsonObj.current_spot_time +'= '+ balanceTimeStr;
			 //jsonObj.purchase_time  ;
			 thisRow.cells[startCol+6].innerHTML = jsonObj.status ;
			 thisRow.cells[startCol+7].innerHTML = jsonObj.profit ;
			 currentProfit = jsonObj.profit ;

			 if (jsonObj.profit >=-0.1 &&  jsonObj.profit <=0.1) {
				 document.getElementById("profitLineValue").value = jsonObj.current_spot;
				 priceValue=parseFloat(document.getElementById("priceLineValue").value) ;
				 differ = jsonObj.current_spot - priceValue ;
			     document.getElementById("differValue").value = differ.toFixed(4)+'::' + jsonObj.profit ;
				 drawPriceLine();

			 }
			 tblTrade = document.getElementById("tradeTable");
			 balance = 0 ;

			 for (let i = 1; i < tblTrade.rows.length; i++) {
                 const row = tblTrade.rows[i];
                 // console.log(row.cells[10]) ;
                  balance  += parseFloat(row.cells[12].innerHTML) ;

             }
			 document.getElementById("tradeBalance").innerHTML = ' Balance = '+ balance.toFixed(2);


			 if (jsonObj.status === 'lost') {
				 lossCon = parseInt(document.getElementById("lossCon").value)+1;
				 document.getElementById("lossCon").value = parseInt(document.getElementById("lossCon").value)+1;
				 document.getElementById("winstatus").innerHTML = 'lost';

				 document.getElementById("thisMoneyTrade").value = thisMoneyTrade;
				 if (useMartinGale) {
					 thisMoneyTrade = MoneyTradeList[lossCon];
				 }


				 //document.getElementById("pocketMoney").value = parseFloat(document.getElementById("pocketMoney").value)+  jsonObj.profit;
				 let newPocket = parseFloat(document.getElementById("pocketMoney").value)+  jsonObj.profit;
				 document.getElementById("pocketMoney").value = newPocket.toFixed(2);
			 }
			 if (jsonObj.status === 'won') {
				 document.getElementById("lossCon").value = 0;
				 lossCon = 0 ;
				 document.getElementById("winstatus").innerHTML = 'Win';
				 thisMoneyTrade = MoneyTradeList[lossCon];
				 document.getElementById("thisMoneyTrade").value = thisMoneyTrade;
				 CurrentLotNo++ ;
				 let newPocket = parseFloat(document.getElementById("pocketMoney").value)+  jsonObj.profit;
				 document.getElementById("pocketMoney").value = newPocket.toFixed(2);


			 }

			 if ( (jsonObj.status === 'sold' || jsonObj.status === 'lost' || jsonObj.status === 'won' ) && isCheckTarget === true) {
				 profitTarget = parseFloat(document.getElementById("targetProfit").value) ;
				 totalProfit = CalPocketMoney();
				 if (totalProfit >= profitTarget ) {
					 document.getElementById("isOpenTrade").checked  = false;
					 canBeTrade = false ;
					 document.getElementById("canBeTradeTxt").value = false;
					 message = 'profit=' + totalProfit.toFixed(2) + ' $' ;
					 thaiBath = 33*totalProfit ;
					 message +=' Bath=' + thaiBath.toFixed(2) ;
					 totalTrade = tradeTable.rows.length-1 ;
					 message +=' จำนวนการเทรด =' + totalTrade ;
					 message +=' Max Money Trade =' + maxMoneyTrade ;





					 doAjaxSendToBot(message) ;
					 doAjaxSendCloseTrade();
					 playAudio();
					 doAjaxSaveTrade();

					 //updateFinishedTrade2CurrentObject(jsonObj);
					 //alert('Trade Terminate');
				 }
				 updateFinishedTrade2CurrentObject(jsonObj)
			 }


			 balanceTime = parseInt(jsonObj.expiry_time - jsonObj.current_spot_time);
             //console.log('Balance Time',balanceTime)


			 if (balanceTime <= 15) {
				  //console.log('Change BG Color')
				 thisSaleBtn = '#SaleBtn_'+jsonObj.contract_id;
				 $(thisSaleBtn).removeClass('pink');
				 $(thisSaleBtn).addClass('hide');
			 } else {
			   if (balanceTime > 15 && balanceTime < 25) {
				  //console.log('Change BG Color')
				 thisSaleBtn = '#SaleBtn_'+jsonObj.contract_id;
				 $(thisSaleBtn).removeClass('green');
				 $(thisSaleBtn).addClass('pink');
			   }

			 }
			 //CalPocketMoney();

} // end func

function SaleContract(contractID) {

         websocket.send(JSON.stringify({
            sell: contractID,
            price: 0 // ขายด้วยราคาตลาดปัจจุบัน
         }));
		 //playAudio();
		 thisSaleBtn = 'SaleBtn_'+ contractID;
		 $("#"+thisSaleBtn).addClass('sold');
		 document.getElementById(thisSaleBtn).innerHTML = 'Sold';

		 return ;
         if (document.getElementById("emaAbove").value = 'ema5Above') {
		   BuyContract('PUT');
         } else {
		   BuyContract('CALL') ;
		 }

} // end func

function playAudio() {


      isplayAudio = document.getElementById("playAudio").checked ;
	  if (isplayAudio == false) {
		  return ;
	  }
	 // console.log('playAudio',isplayAudio)
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


// แสดงผลลัพธ์การเทรด




// Show chart for a specific asset
function showAssetChart() {

            currentAsset = document.getElementById("asset").value  ;
            //document.getElementById("asset").value = asset;
            //document.getElementById("assetDesc").innerHTML = '<h2>'+ asset + '</h2>';
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
                    color: '#ff0000',
                    lineWidth: 2,
                });
            }

            // Set chart title
            chart.applyOptions({
                title: `${asset} - Real-time Candlestick with EMA3 & EMA5`,
            });
            $("#btnInitChart").addClass('glow-basic');

            // Initial data load
            //updateChartData();
} // end show assetchart

function showAssetChartOHLC(asset) {

            //document.getElementById("asset").value = asset;
            //document.getElementById("assetDesc").innerHTML = '<h2>'+ asset + '</h2>';
            // Initialize chart if not already done
            if (!chartOHLC) {
                chartOHLC = LightweightCharts.createChart(document.getElementById('chartContainer2'), {
                    width: document.getElementById('chartContainer2').clientWidth,
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
                candleSeriesOHLC = chartOHLC.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderDownColor: '#ef5350',
                    borderUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                    wickUpColor: '#26a69a',
                });


            }

            // Set chart title
            chartOHLC.applyOptions({
                title: `${asset} - Real-time Candlestick with EMA3 & EMA5`,
            });
            //$("#btnInitChart").addClass('glow-basic');

            // Initial data load
            //updateChartData();
} // end show assetchart2




	function drawPriceLine() {
            // Remove previous line if exists
            if (closePriceLine) {
                candleSeries.removePriceLine(closePriceLine);
            }
			if (targetPriceLine) {
                candleSeries.removePriceLine(targetPriceLine);
            }

			if (closePriceLineOHLC) {
                candleSeriesOHLC.removePriceLine(closePriceLineOHLC);
            }

			if (document.getElementById("profitLineValue").value != '') {
			   priceLineValue = parseFloat(document.getElementById("profitLineValue").value);
			   title = 'profit';
			} else {
	           priceLineValue = parseFloat(document.getElementById("priceLineValue").value);
			   title = 'entry';
			}
			if (currentContractType=='CALL') {
              LineColor =  '#00ff00';
			  targetLineValue = parseFloat(document.getElementById("priceLineValue").value)+2.4;
			} else {
              LineColor =  '#ff0000';
			  targetLineValue = parseFloat(document.getElementById("priceLineValue").value)-2.4;
			}



            // Create new price line
            closePriceLine = candleSeries.createPriceLine({
                price: priceLineValue,
                color: LineColor,
                lineWidth: 2,
                lineStyle: LightweightCharts.LineStyle.Solid,
                axisLabelVisible: true,
                title: title,
            });
			targetPriceLine = candleSeries.createPriceLine({
                price: targetLineValue,
                color: LineColor,
                lineWidth: 2,
                lineStyle: LightweightCharts.LineStyle.dashed,
                axisLabelVisible: true,
                title: 'Target',
            });

           closePriceLineOHLC = candleSeriesOHLC.createPriceLine({
                price: priceLineValue,
                color: LineColor,
                lineWidth: 2,
                lineStyle: LightweightCharts.LineStyle.Solid,
                axisLabelVisible: true,
                title: title,
            });
}

function updateChartDataOHLC() {

         //console.log(candleDataOHLC);
		 /*
	 markers.push({
       time: candle.time,
       position: 'aboveBar',
       color: markerColor,
       shape: 'arrowDown',
       text: markerText,
       size: 1.2
     });
     candleSeries.setMarkers(markers);
	 */
         //console.log(candleDataOHLC);
		// console.log('candle',candleSeriesOHLC);
		 let timestamp = candleDataOHLC[candleDataOHLC.length-1].time ;

        // let timestamp = candleDataOHLC[0].time ;
		// console.log(timestamp);


		 const sDate = new Date(timestamp * 1000);

         //console.log(timestamp,'-',sDate.getSeconds());

		 if (sDate.getSeconds() === 0) {
			 ohlcMarker.push({
				 time: timestamp,
				 position: 'aboveBar',
				 color: '#ff0080',
				 shape: 'arrowDown',
				 text: sDate.getMinutes(),
				 size: 1.2
            });
            candleSeriesOHLC.setMarkers(ohlcMarker);
		 }


         candleSeriesOHLC.setData(candleDataOHLC);


		 ohlcList.push(candleDataOHLC[candleDataOHLC.length-1]);

}

function updateChartData() {

           // if (!currentAsset || !candleData[currentAsset]) return;

            //const candles = candleData[currentAsset];
			const candles = candleData;

            if (candles.length === 0) return;


            // Prepare data for the chart
            const candleDataForChart = candles.map(c => ({
                time: c.epoch + (7 * 3600),
                open: c.open,
                high: c.high,
                low: c.low,
                close: c.close,
            }));

            //console.log(candles[candles.length-1].epoch);



//            console.log('On Update Data=',candleDataForChart);

            // Calculate EMAs
            const ema3 = clsIndy.calculateEMA(candles, 3);
            const ema5 = clsIndy.calculateEMA(candles, 5);


			if (beginOfMinute == true ) {
				//alert('New Minute');

				analy= analyzeEMA(ema3, ema5) ;
				//console.log('Analy Data ',analy);


				previousTurnType = document.getElementById("ema3turnType").value;

                previousEMAAbove = document.getElementById("emaAbove").value;
				document.getElementById("ema3turnType").value = analy.trends.ema3;
				document.getElementById("ema5turnType").value = analy.trends.ema5;
				document.getElementById("emaAbove").value = analy.position;
				document.getElementById("isReversal").value = analy.reversal.isReversal;


				if (previousEMAAbove !== '' && previousEMAAbove !== analy.position &&
                    currentProfit > 0
				) {
					//alert('buy');
					SaleContract(currentContractID);
				}


				currentTurnType = analy.trends.ema3;
				if (previousTurnType !== currentTurnType) {
					//alert(currentTurnType);
					ManageSaleContract(currentTurnType)
				}
                beginOfMinute = false ;
				//console.log(analy)

			}




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
chart.timeScale().fitContent();

/*
            ema5Series.setData(ema5.map((value, index) => ({
                time: candleDataForChart[index].time,
                value: value,
            })));
*/
            // Adjust time scale to fit data
            //chart.timeScale().fitContent();
        }

function openDerivPage() {

	 sPage = 'https://app.deriv.com/dtrader?chart_type=area&interval=1t&symbol=1HZ100V&trade_type=accumulator&lang=TH&account=demo';
	 window.open(sPage,'_blank');



} // end func



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

function ManageSaleContract(currentTurnType) {

 //console.log('On Change Event currentTurnType',currentTurnType) ;
 return;
 if (currentTurnType ==='Up') {
   BuyContract('CALL');
 }	else {
   BuyContract('PUT');
 }


} // end func

function setAutosale() {

	if (document.getElementById("autoSale").checked ) {
		autoSale = true;
	} else {
        autoSale = false;
	}

} // end func

function fillAnalysisEMA() {

	           analy= analyzeEMA(ema3, ema5) ;
				//console.log('Analy Data ',analy);

				beginOfMinute = false ;
				previousTurnType = document.getElementById("ema3turnType").value;

                previousEMAAbove = document.getElementById("emaAbove").value;
				document.getElementById("ema3turnType").value = analy.trends.ema3;
				document.getElementById("ema5turnType").value = analy.trends.ema5;
				document.getElementById("emaAbove").value = analy.position;
				document.getElementById("isReversal").value = analy.reversal.isReversal;


} // end func


function CalDuration(numMinute) {


		 document.getElementById("numDuration").value = (numMinute*60)-5;

		 document.getElementById("candleTimeframe").innerHTML = numMinute;
		 candleTimeframe = numMinute;



} // end func


async function doAjaxgetAction() {

	const candleData2 = candleData.map(c => ({
        time: c.epoch ,
        open: c.open,
        high: c.high,
        low: c.low,
        close: c.close,
    }));

    let result ;
    let ajaxurl = 'AjaxGetAction.php';
    let data = { "Mode": 'getAction' ,
       candleData : candleData2
    } ;
    data2 = JSON.stringify(data);
	//alert(data2);
	//console.log(data2)

    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data2,
	    success: function(data, textStatus, jqXHR){
              //console.log(textStatus + ": " + jqXHR.status);
              // do something with data
            },
            error: function(jqXHR, textStatus, errorThrown){
			  alert(textStatus + ": " + jqXHR.status + " " + errorThrown);
              console.log(textStatus + ": " + jqXHR.status + " " + errorThrown);
            }
        });
        //alert(result);
		console.log(result)
        result = JSON.parse(result);

		document.getElementById("reponseFromServer").value = result.action ;

		document.getElementById("actionCaseNo").value = result.CaseNo;
		document.getElementById("actionReason").value = result.actionReason;
		//console.log('lastClosePrice',result.lastClosePrice);
		lastClosePrice = result.lastClosePrice ;

        if (canBeTrade === true && beginOfMinute === true ) {
			BuyContract(result.action);
			CurrentSignalObject = getEmptySignalObject() ;
			CurrentSignalObject.assetCode = document.getElementById("asset").value ;
			CurrentSignalObject.signalResponse = result.action ;
			CurrentSignalObject.actionCaseNo = result.CaseNo ;
			CurrentSignalObject.actionReason = result.actionReason ;
			beginOfMinute = false ;
		}




        return result;
    } catch (error) {
        console.error(error);
    }

	/*
	CurrentSignalObject = {
          assetCode : null,
          thisTime : null,
          signalResponse : null,
		  tradeAction : null ,
          moneyTrade : null,
          timeframeSecond : null,
          contractID : null,
		  contractType : null,
		  buyTime : null,
		  lastClosePrice : null,
		  entrySpotPrice : null,
          winStatus : null,
          profit : null,
          ohlcData : []

	 }

	*/
}

function InitInput() {

	     document.getElementById("ema3turnType").value = '';
         document.getElementById("ema5turnType").value = '';
		 document.getElementById("emaAbove").value = '';
		 document.getElementById("isReversal").value = '';
		 document.getElementById("reponseFromServer").value = '';
		 document.getElementById("actionReason").value = '';
		 document.getElementById("actionCaseNo").value = '';

} // end func

function setMartingale(ischecked) {

         useMartinGale = ischecked ;
		 //canBeTrade = ischecked ;
		 //document.getElementById("canBeTradeTxt").value = ischecked;



} // end func

function setTradeMode(ischecked) {


		 canBeTrade = ischecked ;
		 document.getElementById("canBeTradeTxt").value = ischecked;



} // end func

function getDetailTrade() {

	let tradeTableA = document.getElementById("tradeTable") ;

	tradelist = [];

	for (i=1;i<=tradeTableA.rows.length-1 ;i++ ) {
		sObj = {
		 tradeno : tradeTableA.rows[i].cells[0].innerHTML,
		 Lotno : tradeTableA.rows[i].cells[1].innerHTML,
         contractID : tradeTableA.rows[i].cells[2].innerHTML,
		 actionCode :  tradeTableA.rows[i].cells[3].innerHTML,
         caseNo     :  tradeTableA.rows[i].cells[3].innerHTML,
         contractType : tradeTableA.rows[i].cells[3].innerHTML,
         moneyTrade :  tradeTableA.rows[i].cells[4].innerHTML,
         timetrade :  tradeTableA.rows[i].cells[5].innerHTML,
         lastClosePrice: tradeTableA.rows[i].cells[6].innerHTML,
         entryPrice : tradeTableA.rows[i].cells[7].innerHTML,
         closeTradePrice : tradeTableA.rows[i].cells[8].innerHTML,
         winStatus :  tradeTableA.rows[i].cells[10].innerHTML,
         profit    :  tradeTableA.rows[i].cells[12].innerHTML
	   }
       tradelist.push(sObj)  ;



	} // end for

	return tradelist;



} // end func




async function getAjaxTradeSectionNo() {



    let result ;
    let ajaxurl = 'AjaxGetAction.php';
    let data = {
		"Mode": 'getTradeSectionNo'
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
		document.getElementById("tradesectionNo").value = result ;

        return result;
    } catch (error) {
        console.error(error);
    }
}

function CalPocketMoney() {

         tradeTable = document.getElementById("tradeTable");
		 let totalProfit = 0 ;
		 for (i=1;i<=tradeTable.rows.length-1 ;i++ ) {
			 status = tradeTable.rows[i].cells[11].innerHTML ;
			 if (status !== 'open') {
		       totalProfit += parseFloat(tradeTable.rows[i].cells[12].innerHTML) ;
			   //console.log(i,'=',parseFloat(tradeTable.rows[i].cells[12].innerHTML)) ;
			 }
		 } // end for
//         console.log('Cal Pocket Money Profit =',totalProfit);

		 document.getElementById("totalProfit").value = totalProfit.toFixed(2);
		 return totalProfit ;

} // end func

function setCheckTarget(sChecked) {

         isCheckTarget = sChecked


} // end func

function SetProfitTarget(target) {

	     targetProfit = target;

} // end func

function SetCheckAll() {

   document.getElementById("playAudio").checked = !document.getElementById("playAudio").checked ;
   document.getElementById("isOpenTrade").checked = !document.getElementById("isOpenTrade").checked ;
   document.getElementById("isuseMartingale").checked = !document.getElementById("isuseMartingale").checked ;

   useMartinGale  = document.getElementById("playAudio").checked ;
   isplayAudio    = document.getElementById("playAudio").checked;






} // end func

function getEmptySignalObject() {

	 sObj = {
          assetCode : null,
          thisTime : null,
          signalResponse : null,
		  tradeAction : null ,
          moneyTrade : null,
          timeframeSecond : null,

          contractID : null,
		  contractType : null,
          startTimeTrade : null,
		  buyTime : null,
		  buyTime_Display : null,
		  lastCandleClosePrice : null,
		  entrySpotPrice : null,
          winStatus : null,
          profit : null,
          ohlcData : null,
          targetProfit : null

	 }

	 return sObj ;


} // end func

function updateFinishedTrade2CurrentObject(jsonObj) {

	     tradeTable = document.getElementById("tradeTable");
		 thisRows = tradeTable.rows[1] ;




         CurrentSignalObject.contractID = jsonObj.contract_id ;
		 CurrentSignalObject.contractType = jsonObj.contract_type ;
		 CurrentSignalObject.targetProfit = targetProfit;
         CurrentSignalObject.timeframeSecond = document.getElementById("numDuration").value ;


		 CurrentSignalObject.startTimeTrade = jsonObj.entry_tick_time ;
		 CurrentSignalObject.tradeAction = jsonObj.contract_type ;

         CurrentSignalObject.buyTime  = jsonObj.entry_tick_time ;
         CurrentSignalObject.buyTime_Display = thisRows.cells[5].innerHTML;
		 CurrentSignalObject.lastClosePrice = thisRows.cells[6].innerHTML;

		 CurrentSignalObject.entrySpotPrice = thisRows.cells[7].innerHTML ;
         CurrentSignalObject.ClosedSpotPrice = jsonObj.exit_tick;
		 CurrentSignalObject.winStatus = thisRows.cells[11].innerHTML ;
		 CurrentSignalObject.profit = jsonObj.profit ;

         lastIndex = ohlcList.length-1 ;
		 const newArray = ohlcList.slice(lastIndex-30, lastIndex);

		 CurrentSignalObject.ohlcData = newArray;



//         console.log('Finished Trade A',jsonObj) ;
//		 console.log('Finished Trade B',CurrentSignalObject) ;
		 listTrade.push(CurrentSignalObject);







} // end func


async function doAjaxSaveTrade() {

	//let tradeTableA = document.getElementById("tradeTable") ;



    detailTrade= getDetailTrade();
	//console.log('detailTrade',detailTrade);

    //return;
    let result ;
    let ajaxurl = 'AjaxGetAction.php';
    let data = {
	 "Mode": 'SaveTrade' ,
     "tradeSectionNoOfDay" : document.getElementById("tradesectionNo").value ,
     "curpairCode" :  document.getElementById("asset").value,
     "detailTrade" : detailTrade,
     "ohlcList" : listTrade
    } ;
    data2 = JSON.stringify(data);
	//alert(data2);
	//console.log('Data Save Trade',data2);

	ohlcList = [];

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
		//document.getElementById("mainBoxAsset").innerHTML = result ;
        return result;
    } catch (error) {
        console.error(error);
    }
}

function saveLocal() {

         //alert(document.getElementById("autoConnect").checked);
	     sObj = {
           asset : document.getElementById("asset").value ,
           CandleTimeframe: document.getElementById("candleTF").value ,
           numDuration : document.getElementById("numDuration").value,
           autoConnect : document.getElementById("autoConnect").checked
		 }

         localStorage.setItem('testacp',JSON.stringify(sObj));
} // end func

function getLocal() {

	     testacp = JSON.parse(localStorage.getItem('testacp'));
		 document.getElementById("asset").value = testacp.asset;
         document.getElementById("candleTF").value = testacp.CandleTimeframe ;
         document.getElementById("numDuration").value  = testacp.numDuration;
		 document.getElementById("candleTimeframe").innerHTML = testacp.CandleTimeframe;
		 document.getElementById("autoConnect").checked = testacp.autoConnect;


} // end func


/*function doAjaxSendToBot() {


} // end func
*/
async function doAjaxSendToBot(message) {

    url='https://telegram-bot-railway-production-ed74.up.railway.app/?message='+message;
    let result ;
    let ajaxurl = url;
    let data = { "Mode": 'sendmessage'
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

        return result;
    } catch (error) {
        console.error(error);
    }
}

async function doAjaxsetPageStatus() {


    let result ;

	let ajaxurl = 'AjaxGetAction.php';
    let data = { "Mode": 'getPageStatus'

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

		console.log('getPageStatus',result) ;

        result2 = JSON.parse(result);

		document.getElementById("asset").value = result2.assetCode ;
		document.getElementById("moneyTrade").value = result2.moneyTrade ;
		document.getElementById("targetProfit").value = result2.targetTrade ;

		fetchCandles('c');
	    fetchCandles('o');


		if (result2.isopenTrade ==='y' || result2.isopenTrade ==='Y') {
			//SetCheckAll();
			sPage = 'https://thepapers.in/deriv/testacp.php'
			//window.open(sPage,'_self');
            canBeTrade = true ;
			let table = document.getElementById("tradeTable");
			// ตรวจสอบว่าตารางมีอยู่จริงและมีมากกว่า 1 แถว
            if (table && table.rows.length > 1) {
              for (let i = table.rows.length - 1; i > 0; i--) {
                table.deleteRow(i); // ลบแถวที่ตำแหน่ง i
              }
            }

			//$("#isOpenTrade").trigger("click");
		} else {
            canBeTrade = false ;
		}

		if (result2.isMartingale ==='y' || result2.isMartingale ==='Y') {
          document.getElementById("isuseMartingale").checked = true;
		  useMartinGale = true ;
		} else {
          document.getElementById("isuseMartingale").checked = false;
		  useMartinGale = false ;
		}

		document.getElementById("canBeTradeTxt").value = canBeTrade;
		document.getElementById("isOpenTrade").checked = canBeTrade;




        return result;
    } catch (error) {
        console.error(error);
    }
}

function AllFetchCandle(assetValue) {

	    if (assetValue !== '') {
           fetchCandles('c');
	       fetchCandles('o');
	    }


} // end func


async function doAjaxSendCloseTrade() {

    let result ;
    let ajaxurl = 'AjaxGetAction.php';
    let data = { "Mode": 'setCloseTrade'

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

        return result;
    } catch (error) {
        console.error(error);
    }
}


window.addEventListener('storage', (event) => {
  // `event` object จะมีข้อมูลเกี่ยวกับการเปลี่ยนแปลง
  // เช่น event.key, event.oldValue, event.newValue, event.url, event.storageArea

  if (event.key === 'myKey') { // ตรวจสอบว่าเป็นการเปลี่ยนแปลงของ key ที่ต้องการหรือไม่
    alert(`ค่าของ 'myKey' ใน localStorage เปลี่ยนแปลงจาก "${event.oldValue}" เป็น "${event.newValue}"`);
    console.log('localStorage change detected for myKey!');
    console.log('Old Value:', event.oldValue);
    console.log('New Value:', event.newValue);
    console.log('Key:', event.key);
    console.log('URL:', event.url);
  } else {
    // ถ้าคุณต้องการ alert ทุกครั้งที่ localStorage มีการเปลี่ยนแปลง ไม่ว่าจะ key ไหน
    alert(`มีการเปลี่ยนแปลงใน localStorage! Key: ${event.key}, Old: ${event.oldValue}, New: ${event.newValue}`);
  }
});

// ตัวอย่างการเปลี่ยนแปลงค่าใน localStorage (คุณสามารถรันโค้ดนี้ในแท็บอื่นเพื่อดูผลลัพธ์)
// localStorage.setItem('myKey', 'new value ' + Math.random());
// หรือ
// localStorage.removeItem('myKey');