function connect() {

   if (isConnecting) return;
   //CreateStatusBar();
   isConnecting = true;
   websocket = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');

   websocket.onopen = function () {
      console.log('WebSocket Connected');
      document.getElementById("status-bar").innerHTML = 'WebSocket Connected';

      authenticateUser(); // เพิ่มฟังก์ชันนี้
      //subscribeToTime();

	  //SendRequest();
      //initChart().catch(console.error);
   };


   websocket.onmessage = function (msg) {
      const response = JSON.parse(msg.data);
	  handleResponse(response);
	  return ;

      const data = JSON.parse(msg.data);

      if (data.time) {

      }
      if (data.candles) {

      }
      // ตรวจสอบว่าเป็นข้อมูลของสัญญาที่เราติดตามอยู่หรือไม่
      if (data.proposal_open_contract) {
         // ตรวจสอบสถานะของสัญญา
         if (data.proposal_open_contract.is_sold) {
	 }
      }
      // ตรวจสอบว่าเป็นการตอบกลับของคำสั่งขายหรือไม่
      if (response.sell) {
         if (response.sell.sold) {
            // การขายสำเร็จ
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
		 document.getElementById("status-bar").innerHTML = 'Authentication successful';


         // ตอนนี้ App ของคุณ คุณสามารถเริ่มส่งคำขออื่นๆ ที่ต้องการ authentication
         // เช่น ดึงข้อมูลบัญชี, ทำการซื้อขาย, ฯลฯ
         getAccountBalance();

      }
      if (response.msg_type === 'balance') {
         handleBalanceResponse(response);
      }
      if (response.buy) {
         // จัดการกรณีเทรดสำเร็จ
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

function CreateStatusBar() {

            const statusBar = document.createElement('div');

            statusBar.id='status-bar' ;
            //statusBar.textContent = 'สถานะ: พร้อมใช้งาน';

            // กำหนดสไตล์ให้กับ status bar ผ่าน JavaScript
            statusBar.style.position = 'fixed';
            statusBar.style.bottom = '0';
            statusBar.style.left = '0';
            statusBar.style.width = '100%';
            statusBar.style.backgroundColor = '#f0f0f0';
            statusBar.style.color = '#333';
            statusBar.style.padding = '18px';
            statusBar.style.textAlign = 'center';
            statusBar.style.fontSize = '0.9em';
            statusBar.style.boxShadow = '0px -2px 5px rgba(0, 0, 0, 0.1)';
			statusBar.style.wordWrap = 'break-word';
            statusBar.style.overflowWrap = 'break-word'; // เพิ่ม vendor prefix สำหรับ Safari

            // เพิ่ม status bar เข้าไปใน body ของเอกสาร
            document.body.appendChild(statusBar);

            // ปรับ padding-bottom ของ body เพื่อไม่ให้เนื้อหาถูก status bar ทับ
            document.body.style.paddingBottom = statusBar.offsetHeight + 'px';

} // end func

function SendRequest() {

          let timeframe =  1;
		  const totalCandle = 60 ;
          const requestCandle = {
              "ticks_history": document.getElementById("asset").value ,
              "style": "candles",
              "granularity": timeframe * 60,
              "count": 60,
              "end": "latest"
          };

          console.log(requestCandle);

          timeSubscription999 = setInterval(() => {
				websocket.send(JSON.stringify(requestCandle));

          }, 1000*1); //60 = 60 Second


} // end func

function createTable(jsonObj) {

			let no =1 ;
			captionList ='ลำดับ,เลขสัญญา,contract_type,ราคาเข้าซื้อ,ราคาปัจจุบัน,เหลือเวลา,สิ้นสุด,ผล,กำไร,บาท' ;
			balanceTime = jsonObj.expiry_time - jsonObj.date_start ;
			balanceTime = jsonObj.expiry_time  - jsonObj.current_spot_time ;
			MinuteRemain =  parseInt((balanceTime/60)) ;
			SecondRemain =  parseInt((balanceTime % 60)) ;

			document.getElementById("priceLineValue").value = jsonObj.entry_spot;



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
			}
			st = '<table id="tblTrade" border=1>'; st += '<tr>';
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

					 console.log(st) ;


					 document.getElementById("tradeTable").innerHTML = st;

} // end func

function  newContractToTable(jsonObj) {

			let no =1 ;
			captionList ='ลำดับ,เลขสัญญา,contract_type,ราคาเข้าซื้อ,ราคาปัจจุบัน,เหลือเวลา,สิ้นสุด,ผล,กำไร,บาท' ;
			balanceTime = jsonObj.expiry_time - jsonObj.date_start ;
			balanceTime = jsonObj.expiry_time  - jsonObj.current_spot_time ;
			MinuteRemain =  parseInt((balanceTime/60)) ;
			SecondRemain =  parseInt((balanceTime % 60)) ;

			document.getElementById("priceLineValue").value = jsonObj.entry_spot;



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
			}
			st = '';
 		    st += '<tr>';
            for (i=0;i<=valueList.length-1 ;i++ ) {
				   st += '<td>' + valueList[i]+ '</td>';
			} // end for
			st +='<td><button id="SaleBtn" onclick="SaleContract('+ jsonObj.contract_id +')">Sale</button> </td>';
			st += '</tr>';
			table = document.getElementById("tblTrade").value ;

			table.innerHTML = st + table.innerHTML ;
			// หรือ
			//table.insertAdjacentHTML('afterbegin', st);


			console.log(st) ;


			//document.getElementById("tradeTable").innerHTML = st;

} // end func

//ถ้ามี  html table ซึ่ง id= 'tblTrade' และมี st = '<tr><td>Data1</td></tr>' ต้องการจะเพิ่มแถวใหม่เข้าไปใน tblTrade ด้วย st ทำอย่างไรด้วย pure javascript