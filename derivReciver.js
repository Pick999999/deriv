//data.time,data.candles,data.proposal_open_contract
//response.msg_type === 'balance'
//response.buy
/*
1.authorize(response)
2.updateServerTime(dataTimestamp)
3.candleDataProcess()

*/

function handleResponse(response) {


        // console.log(response.msg_type);

         if (response.msg_type === 'authorize') { authorize(response); }
         if (response.msg_type === 'time') { updateServerTime(response.time); }

		 if (response.msg_type === 'ohlc') {
            console.log('ข้อมูลแท่งเทียนแบบเรียลไทม์:');
            console.log(response.ohlc);
			/*
			{
    "close": "1514.44",
    "epoch": 1744976236,
    "granularity": 60,
    "high": "1515.60",
    "id": "5636bf1f-e847-4bf0-4774-b48bdb197aaf",
    "low": "1514.44",
    "open": "1515.60",
    "open_time": 1744976220,
    "pip_size": 2,
    "symbol": "R_100"
}
*/
			sAll = JSON.parse(document.getElementById("txtchartData").value);
			s = sAll['candleData'];
			console.log('s0=',s[0],'len s=',s.length);


			sObj = {
			  time:  response.ohlc.epoch ,
              open:  parseFloat(response.ohlc.open) ,
			  high:  parseFloat(response.ohlc.high) ,
              low:  parseFloat(response.ohlc.low) ,
              close:  parseFloat(response.ohlc.close)
			}
             console.log('sObj',sObj);

			s.push(sObj) ;
			console.log('after push len=',s.length) ;
			sAll['candleData'] = s ;

			document.getElementById("txtchartData").value =  JSON.stringify(sAll) ;
			updateChart99(s);

		 }
		 if (response.msg_type === 'candles') {
			 data2 = candleDataProcess(response.candles);
		     console.log('data2.candleData',data2.candleData)
             document.getElementById("txtchartData").value =  JSON.stringify(data2) ;
			 //updateChart99(data2.candleData);
			 updateChart99(data2);

		 }
		 if (response.msg_type === 'candles' ) {
			 //console.log(response);
			 //&& response.subscription
             //this._handleCandleUpdate(data);
            return;
         }
         // Handle history data
         if (response.msg_type === 'history' ) {
			 console.log('history',response)

           //this._handleHistoryData(data);
           return;
         }


} // end func


function authorize(response) {

         if (response.error) {
            console.error('Authentication error:', response.error.message);
            return;
         }
		 document.getElementById("status-bar").innerHTML = 'Authentication successful';


         console.log('Authentication successful');
         console.log('Account info:', response.authorize);
		 subscribeToTime();

         // ตอนนี้ App ของคุณ คุณสามารถเริ่มส่งคำขออื่นๆ ที่ต้องการ authentication
         // เช่น ดึงข้อมูลบัญชี, ทำการซื้อขาย, ฯลฯ
         //getAccountBalance();

} // end func


function updateServerTime(dataTimestamp) {

   const date = new Date(dataTimestamp * 1000);
   const timeStr = date.toLocaleTimeString();
   document.getElementById('serverTime').textContent = timeStr;

   if (date.getSeconds() === 0) {
     // fetchCandles();
   }

} // end func

function candleDataProcess(candles) {

          const candleData = candles.map(candle => ({
                    time: candle.epoch,
                    open: parseFloat(candle.open),
                    high: parseFloat(candle.high),
                    low: parseFloat(candle.low),
                    close: parseFloat(candle.close)
          }));
          ema3 =  calculateEMANew(candleData, 3)  ;
		  ema5 =  calculateEMANew(candleData, 5)  ;
		  period = 20 ; multiplier = 2 ;
		  BB = calculateBollingerBands(candleData, period, multiplier);

		  data2 = {
           timecandle : candleData[candles.length-1].time ,
           candleData : candleData ,
           ema3 : ema3 ,
           ema5 : ema5 ,
           Bollinger : BB
		  }
		  return data2 ;



} // end func

// Calculate Bollinger Bands
function calculateBollingerBands(data, period = 20, multiplier = 2) {
            if (!data || data.length < period) return { upperBand: [], lowerBand: [] };

            let upperBand = [];
            let lowerBand = [];

            for (let i = period - 1; i < data.length; i++) {
                let sum = 0;
                let validPoints = 0;

                for (let j = i - period + 1; j <= i; j++) {
                    if (data[j] && typeof data[j].close === 'number') {
                        sum += data[j].close;
                        validPoints++;
                    }
                }

                if (validPoints === 0) continue;

                const sma = sum / validPoints;

                let sumSquares = 0;
                validPoints = 0;

                for (let j = i - period + 1; j <= i; j++) {
                    if (data[j] && typeof data[j].close === 'number') {
                        sumSquares += Math.pow(data[j].close - sma, 2);
                        validPoints++;
                    }
                }

                if (validPoints === 0) continue;

                const stdDev = Math.sqrt(sumSquares / validPoints);

                upperBand.push({
                    time: data[i].time,
                    value: sma + multiplier * stdDev
                });

                lowerBand.push({
                    time: data[i].time,
                    value: sma - multiplier * stdDev
                });
            }

            return { upperBand, lowerBand };
} // end BB

function calculateEMANew(candles, period) {


            if (candles.length < period) return [];

            const prices = candles.map(c => c.close);
            const ema = [];

            // Calculate SMA for the first EMA value
            let sum = 0;
            for (let i = 0; i < period; i++) {
                sum += prices[i];
            }

            // First EMA value is SMA
            ema.push({
                time: candles[period- 1].time,
                value: sum / period
            });
			//console.log('ema Step1',ema)


            // Calculate EMA
            const multiplier = 2 / (period + 1);
            for (let i = period; i < prices.length; i++) {
                const emaValue = prices[i] * multiplier + ema[i - period].value * (1 - multiplier);
                ema.push({
                    time: candles[i].time,
                    value: emaValue
                });
            }

            return ema;
 }




/*
**** Time ****
{
    "echo_req": {
        "time": 1
    },
    "msg_type": "time",
    "time": 1744939955
}

TOLA907613
HTTPS://fwdth.co/TOLA1
เลขสมาชิก  9611735104

เลขบิลทรู --305 776 428




*/