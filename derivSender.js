function authenticateUser() {

   const authRequest = {
      authorize: 'lt5UMO6bNvmZQaR',
      req_id: 1 // Request ID เพื่อติดตามการตอบกลับ
   };

   websocket.send(JSON.stringify(authRequest));

} // end func

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

function DisConnect() {

      websocket.close();
      websocket = null;

} // end func


function fetchCandles() {
/*
 // วิธีที่ 1: ขอข้อมูลประวัติแท่งเทียนย้อนหลัง
    const ticksHistoryRequest = {
        ticks_history: "R_100",  // สัญลักษณ์ของสินทรัพย์
        adjust_start_time: 1,
        count: 10,               // จำนวนแท่งเทียนที่ต้องการ
        end: "latest",           // เวลาสิ้นสุด
        start: 1,                // เวลาเริ่มต้น
        style: "candles",        // ระบุว่าต้องการข้อมูลแบบแท่งเทียน
        granularity: 60          // ช่วงเวลาของแต่ละแท่ง (วินาที) 60 = 1 นาที
    };
*/

   isProcessing = true;
   const asset = document.getElementById('asset').value;
   //const timeframe = parseInt(document.querySelector('input[name="timeframe"]:checked').value);
   //let timeframe = 60 ; // 1 นาที
   /*
   let timeframe = 60 *60 ; // 1 นาที
   if (document.getElementById("unitTimeFrame").innerHTML === 'Minute') {
	   granu = parseInt(document.getElementById("numUnit").value);
   }
   if (document.getElementById("unitTimeFrame").innerHTML === 'Hour') {
	   granu = parseInt(document.getElementById("numUnit").value)*60;
   }
   */
   sname = 'timeframeRadioSel';
   const radios = document.querySelectorAll(`input[name="${sname}"]:checked`);
   if (radios.length > 0) {
     timeframe=  parseInt(radios[0].value);
   }

   numMinute = parseInt(document.getElementById("numMinute").value);
   //alert(timeframe);
   //alert(numMinute);

   granu = numMinute * timeframe  * 60;

  // alert(granu); return;




   const requestCandle = {
      "ticks_history": asset,
      "style": "candles",
      "granularity": granu ,
      "count": 60,
      "end": "latest"
   };
   console.log('Request',JSON.stringify(requestCandle));



   //websocket.send(JSON.stringify(request));

   timeSubscription999 = setInterval(() => {
				websocket.send(JSON.stringify(requestCandle));

   }, 1000*1); //60 = 60 Second
   document.getElementById('status-bar').textContent = 'Fetching candles at ' + new Date().toLocaleTimeString();
   /*
    // ตรวจสอบว่าเป็นข้อมูลประวัติแท่งเทียน
    if (response.history) {
        console.log('ข้อมูลประวัติแท่งเทียน:');
        console.log(response.history);

        // ตัวอย่างการแปลงข้อมูลเป็นรูปแบบที่ใช้งานง่าย
        const candles = response.history.times.map((time, index) => {
            return {
                time: new Date(time * 1000),
                open: response.history.prices[index],
                high: response.history.high[index],
                low: response.history.low[index],
                close: response.history.close[index]
            };
        });

        console.log('แท่งเทียนที่แปลงแล้ว:', candles);
    }

   */
}

function fetchCandles2() {
/*

const request = {
      "ticks_history": asset,
      "style": "candles",
      "granularity": timeframe * 60,
      "count": 60,
      "end": "latest"
   };

 // วิธีที่ 2: สมัครรับข้อมูลแท่งเทียนแบบเรียลไทม์
 */
    const candlesSubscription = {
        ticks_history: "R_100",
		style: "candles",
        adjust_start_time: 1,
        count: 10,
        end: "latest",
        start: 1,
        granularity: 60,
        subscribe: 1              // ระบุว่าต้องการสมัครสมาชิกเพื่อรับข้อมูลแบบเรียลไทม์
    };

    websocket.send(JSON.stringify(candlesSubscription));
	/*
	// ตรวจสอบว่าเป็นข้อมูลแท่งเทียนใหม่จากการสมัครสมาชิก
    if (response.candles) {
        console.log('ข้อมูลแท่งเทียนแบบเรียลไทม์:');
        console.log(response.candles);

        // ดึงแท่งเทียนล่าสุด
        const lastCandle = response.candles[response.candles.length - 1];
        console.log('แท่งเทียนล่าสุด:', {
            time: new Date(lastCandle.epoch * 1000),
            open: lastCandle.open,
            high: lastCandle.high,
            low: lastCandle.low,
            close: lastCandle.close
        });
    }
*/


} // end func


// Test acp


