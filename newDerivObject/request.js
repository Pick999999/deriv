//request.js
import { timesubscript_json,Candles_Hist_json } from './derivJson.js' ;

function attemptReconnect(deriv) {

        if (deriv.reconnectAttempts >= deriv.maxReconnectAttempts) {
            console.error('Max reconnection attempts reached');
            return;
        }

        deriv.reconnectAttempts++;
        const delay = deriv.reconnectDelay * Math.pow(2, deriv.reconnectAttempts - 1); // exponential backoff

        console.log(`Attempting to reconnect in ${delay/1000} seconds... (Attempt ${deriv.reconnectAttempts}/${deriv.maxReconnectAttempts})`);

        setTimeout(() => {
            console.log('Attempting to reconnect...');
			if (deriv.connect()) {

			} else {
			}
			/*
            deriv.connect()
                .catch(error => {
                    console.error('Reconnection failed:', error);
                });
           */
        }, delay);

} // end func

function authenticate(Deriv) {

        return new Promise((resolve, reject) => {
            if (!Deriv.token) {
                reject(new Error('API token is required'));
                return;
            }

            const authRequest = {
                authorize: Deriv.token
            };

            Deriv.ws.send(JSON.stringify(authRequest));
            // เพิ่ม handler สำหรับการตรวจสอบ response ของ authentication
            const authHandler = (response) => {
                if (response.error) {
                    reject(new Error(response.error.message));
                } else if (response.authorize) {
                    console.log('Successfully authenticated');
                    resolve(response.authorize);
                }
            };

            // เพิ่ม one-time listener สำหรับ authentication response
            const messageHandler = (msg) => {
                const response = JSON.parse(msg.data);
                if (response.msg_type === 'authorize') {
                    authHandler(response);
                    Deriv.ws.removeEventListener('message', messageHandler);
                }
            };

            Deriv.ws.addEventListener('message', messageHandler);
        });
} // end Authenticate

function subscribeToTime(Deriv) {
        /*
		โดยมีหน้าที่หลักคือ:
        ส่งคำขอข้อมูลเวลา (time request) ไปยังเซิร์ฟเวอร์ของ Deriv ทุกๆ 1 วินาที โดย:
		ส่ง JSON object {"time": 1} ผ่าน WebSocket
		ตั้ง interval timer ให้ส่งซ้ำทุก 1000 มิลลิวินาที (1 วินาที)
         มีการตรวจสอบว่า:
         ถ้ามี timeSubscription อยู่แล้ว จะยกเลิกการส่งข้อมูลเดิมก่อน (clearInterval)
         ตรวจสอบว่า WebSocket connection ยังเปิดอยู่หรือไม่ก่อนส่งข้อมูล
         มีส่วนที่ถูก comment ไว้สำหรับตรวจสอบ checkbox "startReadCandle" (ปัจจุบันไม่ได้ใช้งาน)

	         ประโยชน์หลักของฟังก์ชั่นนี้คือ:
		ใช้ sync เวลาระหว่างเครื่องผู้ใช้กับเซิร์ฟเวอร์ของ Deriv
		อาจใช้ในการอัพเดทข้อมูล real-time เช่น ราคา candle stick หรือข้อมูลการเทรดอื่นๆ
		ช่วยรักษาการเชื่อมต่อ WebSocket ให้คงอยู่ (keep-alive mechanism)
      */

        /*
			if (!document.getElementById("startReadCandle").checked) {
				alert('Disabled');
				return;
			}
			*/
        if (Deriv.timeSubscription) {
            clearInterval(Deriv.timeSubscription);
        }

/*
        Deriv.ws.send(JSON.stringify({
            "time": 1
        }));
*/
        Deriv.ws.send(timesubscript_json(1));

        Deriv.timeSubscription = setInterval(() => {
            if (Deriv.ws && Deriv.ws.readyState === WebSocket.OPEN) {
				let asset = document.getElementById("realSelectedAsset").value;
 		        let timeframe =  1;
				const totalCandle = 60 ;
                const requestCandle = {
                  "ticks_history": asset,
                  "style": "candles",
                  "granularity": timeframe * 60,
                  "count": 60,
                  "end": "latest"
                };
				const requestTime = {
                   "time": 1
                };
			    const requestPortfolio = {
                  portfolio: 1
                }

//				let requestJson= Candles_Hist_json(asset,timeframe,totalCandle);
                //Deriv.ws.send(JSON.stringify(request));
				//Deriv.ws.send(JSON.stringify(request2));
				Deriv.ws.send(JSON.stringify(requestTime));
				Deriv.ws.send(JSON.stringify(requestCandle));
				//Deriv.ws.send(JSON.stringify(requestPortfolio));
            }
        }, 1000*1); //60 = 60 Second
/*
		Deriv.timeSubscription = setInterval(() => {
            if (Deriv.ws && Deriv.ws.readyState === WebSocket.OPEN) {
				let asset = document.getElementById("realSelectedAsset").value;
 		        let timeframe =  1;
				const totalCandle = 60 ;
                const request = {
                  "ticks_history": asset,
                  "style": "candles",
                  "granularity": timeframe * 60,
                  "count": 60,
                  "end": "latest"
                };


//				let requestJson= Candles_Hist_json(asset,timeframe,totalCandle);
                //Deriv.ws.send(JSON.stringify(request));
				Deriv.ws.send(JSON.stringify(request));
            }
        }, 1000*5); //60 = 60 Second
*/
	    console.log('SubScribe To Time Finished');


} // end subscribeToTime


function getCandles1M(Deriv) {

//           alert(document.getElementById("realSelectedAsset").value) ;
           let asset = document.getElementById("realSelectedAsset").value;
		   let timeframe =  1;
           const request = {
                "ticks_history": asset,
                "style": "candles",
                "granularity": timeframe * 60,
                "count": 60,
                "end": "latest",
            };

            Deriv.ws.send(JSON.stringify(request));

} // end func

function getCandleInterval(Deriv) {
let asset = document.getElementById("realSelectedAsset").value;
let timeframe =  1;

         // Request candles data
            const request = {
                ticks_history: asset,
                adjust_start_time: 1,
                count: 2000,
                end: endDate,
                start: startDate,
                style: "candles",
                granularity: timeframe * 60

            };
			Deriv.ws.send(request);

} // end func

function getCandle2Sec(Deriv) {

     	  Deriv.ws.send(JSON.stringify({
                ticks_history: document.getElementById("realSelectedAsset").value,
                adjust_start_time: 1,
                count: 20,
                end: 'latest',
                start: 1,
                style: 'candles',
                granularity: 60,
                subscribe: 1
            }));


} // end func

function OnTrade(Deriv,contract_type) {

        /* Trade แบบ Rise/Fall  */
        if (contract_type === 'Null' || contract_type == '') {
            return;
        }
		//alert(document.getElementById("contractType").value) ;

        console.clear();
        //const timeframe = parseInt(document.querySelector('input[name="timeframe"]:checked').value);
        //const contractType = document.getElementById("contractType").value;
  		let  amountTrade  = parseFloat(document.getElementById("realmoneyTrade").value);
		if (document.getElementById("useMartingale").checked === false) {
          amountTrade = parseFloat(document.getElementById("realmoneyTrade").value);
		} else {
          amountTrade = parseFloat(document.getElementById("thisMoneyTrade").value);
		}
		//alert(amountTrade);
        //const timeDuration = parseInt(document.getElementById("timeDuration").value);

        let direction = contract_type;
        //let duration = document.getElementById("timeDuration").value;
        let symBol = document.getElementById("realSelectedAsset").value; // "R_10"
		document.getElementById("chart1PriceLine").value = '';

        const request = {
            buy: 1,
            price: parseFloat(amountTrade),
            parameters: {
                amount: amountTrade,
                basis: "stake",
                contract_type: direction,
                currency: "USD",
                duration: parseInt(document.getElementById("realTimeduration").value),
                duration_unit: 'm',
                symbol: symBol
            }
        };

        //console.log('Sending trade request:', request);
        console.log('"Waiting trade response.buy:', request);
        Deriv.ws.send(JSON.stringify(request));

 } // end func OnTrade

function unsubscribeContract(contractId) {
// ยกเลิกการ Tracking Contract
        //if (activeSubscriptions.has(contractId)) {
        this.socket.send(JSON.stringify({
            forget_all: ['proposal_open_contract'],
            contract_id: contractId
        }));

        //  activeSubscriptions.delete(contractId);
        // }
}

function CloseContract(contractId,deriv) {

          deriv.ws.send(JSON.stringify({
                sell: contractId,
                price: 0
          }));




          //alert('Close ' + contractId);
/*
// ตัวอย่าง response เมื่อขายสำเร็จ
{
    "msg_type": "sell",
    "sell": {
        "balance_after": 1000.00,       // ยอดเงินคงเหลือหลังขาย
        "contract_id": "123456",        // ID ของ contract ที่ขาย
        "reference_id": "abc123",       // ID อ้างอิงของการขาย
        "sold_for": 500.50,            // จำนวนเงินที่ได้จากการขาย
        "transaction_id": "xyz789"      // ID ของธุรกรรม
    },
    "echo_req": {
        "sell": "123456",
        "price": 0
    }
}

{
    "balance_after": 9033.43,
    "contract_id": 273083754448,
    "reference_id": 544395703768,
    "sold_for": 1.16,
    "transaction_id": 544395732208
}

*/


} // end func


async function doAjaxGetSignal() {

    let result ;
	let textArea = document.getElementById("chart1Data");
    let ajaxurl = 'https://thepapers.in/deriv/newDerivObject/AjaxGetSignal.php';
    let data = { "Mode": 'getSignal' ,
    "candleData" : textArea.value
    } ;

    let data2 = JSON.stringify(data);

	//alert(data2);
    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
	        dataType: "json",
            data: data2,
	    success: function(data, textStatus, jqXHR){
              console.log(textStatus + ": " + jqXHR.status);
              // do something with data
            },
            error: function(jqXHR, textStatus, errorThrown){
			  //alert(textStatus + ": " + jqXHR.status + " " + errorThrown);
              console.log(textStatus + ": " + jqXHR.status + " " + errorThrown);
            }
        });
        //alert(result);
	    console.log('Result =>',result);

        //let resultAr = result.split('---');
		//let thisAction = resultAr[1].trim();
		let thisAction = result.thisAction ;
		document.getElementById("actionSpan").innerHTML = thisAction ;
		document.getElementById("showAction2").innerHTML = thisAction ;
		document.getElementById("rsi").value = result.rsi;

		if (thisAction ==='CALL') {
			document.getElementById("showAction2").innerHTML = 'CALL';
			$("#btnCallTrade").trigger("click");
		}
		if (thisAction ==='PUT') {
			document.getElementById("showAction2").innerHTML = 'PUT';
			$("#btnPutTrade").trigger("click");
		}
		let chart1Data = JSON.parse(textArea.value);
		let lastIndex = chart1Data.length - 1;
		let lastClosePrice = chart1Data[lastIndex].close ;
		document.getElementById("lastClosePrice").value = lastClosePrice;




		//document.getElementById("mainBoxAsset").innerHTML = result ;

        return result;
    } catch (error) {
        console.error(error);
    }
}

function FindGrandTotal() {

         let tradestable = document.getElementById("trades-table");
		 var profit2 = 0 ;
		 var balance2= 0 ;
		 let closed = '' ;

		 for (i=1;i<=tradestable.rows.length-1 ;i++ ) {
            closed = tradestable.rows[i].cells[13].innerHTML;
			if (closed.trim() === 'Y') {
		      profit2 = parseFloat(tradestable.rows[i].cells[8].innerHTML);
		      balance2 = balance2 + profit2 ;
			}
	     }
		 document.getElementById("closedbalance").value = balance2.toFixed(2);
		 let TradeList = [] ;
		 let lossCon = 0 ;
		 let maxLossCon = 0 ;
		 let profit = 0 ;
		 //alert(balance2)
		 for (i=1;i<=tradestable.rows.length-1 ;i++ ) {
             var rows = tradestable.rows[i];
			 tradestable.rows[i].cells[0].innerHTML = i ;
			 let expire = tradestable.rows[i].cells[10].innerHTML;
             profit = parseFloat(tradestable.rows[i].cells[8].innerHTML) ;
			 if (profit < 0) {
				 lossCon++ ;
			 } else {
                 lossCon = 0 ;
			 }
			 if (lossCon > maxLossCon) {
                 maxLossCon = lossCon ;
			 }

			 if (expire.trim() ==='') {
				 tradestable.deleteRow(i);
			 } else {
				 let tradeObj = {
				   subtradeno : tradestable.rows[i].cells[0].innerHTML ,
                   contractId: tradestable.rows[i].cells[1].innerHTML ,
                   action: tradestable.rows[i].cells[2].innerHTML ,
                   assetCode: tradestable.rows[i].cells[3].innerHTML ,
                   MoneyTrade: parseFloat(tradestable.rows[i].cells[4].innerHTML) ,
                   InitBuyPrice: parseFloat(tradestable.rows[i].cells[5].innerHTML) ,
                   CloseBuyPrice: parseFloat(tradestable.rows[i].cells[6].innerHTML) ,
                   profit :  parseFloat(tradestable.rows[i].cells[8].innerHTML) ,
                   lossCon : lossCon,
                   startTime: tradestable.rows[i].cells[9].innerHTML ,
                   closeTime: tradestable.rows[i].cells[10].innerHTML ,
                   closeStatus : tradestable.rows[i].cells[13].innerHTML
				 }
                 TradeList.push(tradeObj) ;
			 }
	     }

		 let starttimeTrade = engDate(TradeList[0].startTime) ;
		 console.log('Start Time',starttimeTrade) ;

         let stoptimeTrade  =  engDate(TradeList[TradeList.length-1].startTime) ;

		 let thisTrade = {
           tradeNo   : document.getElementById("maintradeno").value ,
           assetCode : document.getElementById("realSelectedAsset").value ,
           timeframe : document.getElementById("realTimeduration").value ,
		   starttime : starttimeTrade ,
		   endtime : stoptimeTrade ,
           totalTrade: TradeList.length ,
           maxLossCon : maxLossCon,
           totalProfit : document.getElementById("closedbalance").value ,
           tradeList : TradeList
		 }

         localStorage.setItem('tradeTransaction',JSON.stringify(thisTrade));
		 AjaxSaveTradeList(thisTrade);

		 //alert('balance2')


} // end func

function engDate(thaiDate) {

// แยกส่วนของวันที่และเวลา
const [datePart, timePart] = thaiDate.split(' ');
// แยกวัน, เดือน, ปี
const [day, month, year] = datePart.split('/');

// แปลงปีจาก พ.ศ. เป็น ค.ศ.
const buddhistYear = parseInt(year);
const christianYear = buddhistYear - 543;

// สร้างวันที่ในรูปแบบ ISO (YYYY-MM-DD)
const isoDate = `${christianYear}-${month.padStart(2, '0')}-${day.padStart(2, '0')}T${timePart}`;
console.log('iso Date',isoDate) ;

// สร้างวัตถุ Date
const date = new Date(isoDate);

// แปลงเป็นรูปแบบภาษาอังกฤษ (English format)
const englishDate = date.toLocaleString('en-US', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false
});

return isoDate ;
//return englishDate ;


} // end func


export { attemptReconnect,subscribeToTime,getCandles1M,getCandle2Sec,authenticate,OnTrade,CloseContract,doAjaxGetSignal,FindGrandTotal };