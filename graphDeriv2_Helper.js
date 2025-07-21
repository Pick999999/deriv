
function setFromLocal() {

      sData = localStorage.getItem('graphDeriv2') ;
	  sObj = JSON.parse(sData);
	  if (document.getElementById("assetTmp").value ==='') {
	    document.getElementById("assetCode").value = sObj.assetCode ;
        document.getElementById("asset").value = sObj.assetCode ;
	  } else {
        document.getElementById("assetCode").value = document.getElementById("assetTmp").value ;
        document.getElementById("asset").value = document.getElementById("assetTmp").value ;

	  }
	  document.getElementById("moneyTrade").value = sObj.moneyTrade ;
      document.getElementById("numWarn").value = sObj.numWarn ;
	 // document.getElementById("isCheckedStopLoss").checked = false;

	  setCheckBox('isBreakFetchCandle',sObj.BreakFetchCandle);
	  setCheckBox('isBreakTrade',sObj.useBreakTrade);
	  setCheckBox('isuseMartinGale',sObj.useMartingale);
	  setCheckBox('isCheckedStopLoss',sObj.useStopLoss);

	  const radios = document.getElementsByName('timeframe');
      for (let i = 0; i < radios.length; i++) {
        if (radios[i].value === sObj.timeframe) {
           radios[i].checked = true;
        }
      }

} // end func

function setAssetCode(sValue) {

document.getElementById("assetCode").value = sValue ;

} // end func


function saveLocal() {

	thisTimeFrame = getRadioValue('timeframe') ;
	useStopLoss = getCheckBox('isCheckedStopLoss') ;
	useMartingale  = getCheckBox('isuseMartinGale') ;
	useBreakTrade  = getCheckBox('isBreakTrade') ;
	BreakFetchCandle = getCheckBox('isBreakFetchCandle') ;
	sObj = {
	 assetCode  : document.getElementById("assetCode").value ,
	 moneyTrade : document.getElementById("moneyTrade").value,
	 timeframe  : thisTimeFrame,
	 numWarn    : document.getElementById("numWarn").value,
	 useMartingale : useMartingale,
	 useStopLoss : useStopLoss,
	 useBreakTrade : useBreakTrade,
	 BreakFetchCandle : BreakFetchCandle
	}

	thisPageData = JSON.stringify(sObj);
	localStorage.setItem('graphDeriv2',thisPageData) ;


} // end func



function setCurrentState(currentStateno) {

         workingCode = currentStateno ;
		 document.getElementById("workModeDesc").innerHTML = workingDesc[currentStateno];

} // end func

function calculateEMA(data, period) {
   const k = 2 / (period + 1);
   let ema = data[0].close;
   const emaData = [];

   data.forEach((candle, index) => {
      if (index === 0) {
         emaData.push({
            time: candle.time,
            value: ema
         });
         return;
      }
      ema = (candle.close * k) + (ema * (1 - k));
      emaData.push({
         time: candle.time,
         value: ema
      });
   });

   return emaData;
}

function CalBalance() {

   tableTradeResult = document.getElementById("tableTradeResult");
   let balance = 0;
   let profitColumn = 10;
   let statusColumn = 11;
   for (let i = 1; i <= tableTradeResult.rows.length - 1; i++) {
      status = tableTradeResult.rows[i].cells[statusColumn].innerHTML;
      if (status === 'Win' || status === 'Loss' || status === 'Sell' || status==='!!' ) {
         thisProfit = parseFloat(tableTradeResult.rows[i].cells[profitColumn].innerHTML);
		 if (thisProfit < 0 && status === '!!' ) {
           tableTradeResult.rows[i].cells[statusColumn].innerHTML = 'Loss';
		 }
         balance = balance + thisProfit;
      }
   }
   console.log('On Cal Balance',balance) ;

   //alert(balance);
   balance = balance.toFixed(2);
   let balanceBath = (balance * 33).toFixed(2);
   document.getElementById("balanceTxt").value = balance;
   if (balance < 0) {
      document.getElementById("balanceBath").innerHTML = '<span style="color:red">' + balanceBath + '  บาท<span>';
   } else {
      document.getElementById("balanceBath").innerHTML = balanceBath + '  บาท';
   }


} // end func

function getCheckBox(id) {
var chk = document.getElementById(id) ;

       console.log('id',id)

      if (chk.checked) {
		  return 'y';
	  } else {
		  return 'n';
	  }
   return null; // Return null if no radio button is selected
}

function setCheckBox(id,checked) {

          console.log(id,checked)

	     if (checked === 'y') {
            document.getElementById(id).checked = true;
	     }
		 if (checked === 'n') {
            document.getElementById(id).checked = false;
	     }


} // end func



function getRadioValue(name) {
   const radios = document.getElementsByName(name);
   for (let i = 0; i < radios.length; i++) {
      if (radios[i].checked) {
         return radios[i].value;
      }
   }
   return null; // Return null if no radio button is selected
}

function formatTimestamp(timestampInSeconds) {
   // สร้าง Date object (JavaScript ใช้มิลลิวินาที ดังนั้นต้องคูณด้วย 1000)
   const date = new Date(timestampInSeconds * 1000);
   const now = new Date();

   // สำหรับ GMT
   const daysEng = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
   const monthsEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

   // สำหรับภาษาไทย
   const daysThai = ["อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์"];
   const monthsThai = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];

   // ฟอร์แมต GMT
   const gmtDayOfWeek = daysEng[date.getUTCDay()];
   const gmtDay = date.getUTCDate();
   const gmtMonth = monthsEng[date.getUTCMonth()];
   const gmtYear = date.getUTCFullYear();
   const gmtHours = String(date.getUTCHours()).padStart(2, '0');
   const gmtMinutes = String(date.getUTCMinutes()).padStart(2, '0');
   const gmtSeconds = String(date.getUTCSeconds()).padStart(2, '0');

   // ฟอร์แมตเวลาท้องถิ่น
   const localDayOfWeek = daysThai[date.getDay()];
   const localDay = date.getDate();
   const localMonth = monthsThai[date.getMonth()];
   const localYear = date.getFullYear();
   const localHours = String(date.getHours()).padStart(2, '0');
   const localMinutes = String(date.getMinutes()).padStart(2, '0');
   const localSeconds = String(date.getSeconds()).padStart(2, '0');

   // คำนวณ timezone offset
   const tzOffset = date.getTimezoneOffset();
   const tzHours = Math.abs(Math.floor(tzOffset / 60));
   const tzMinutes = Math.abs(tzOffset % 60);
   const tzSign = tzOffset <= 0 ? '+' : '-';

   // คำนวณเวลาเชิงเปรียบเทียบ (Relative time)
   const diffSeconds = Math.floor((now - date) / 1000);

   let relativeTime;
   if (diffSeconds < 60) {
      relativeTime = `${diffSeconds} seconds ago`;
   } else if (diffSeconds < 3600) {
      const minutes = Math.floor(diffSeconds / 60);
      relativeTime = `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
   } else if (diffSeconds < 86400) {
      const hours = Math.floor(diffSeconds / 3600);
      relativeTime = `${hours} hour${hours > 1 ? 's' : ''} ago`;
   } else {
      const days = Math.floor(diffSeconds / 86400);
      relativeTime = `${days} day${days > 1 ? 's' : ''} ago`;
   }

   // สร้างผลลัพธ์
   return `Assuming that this timestamp is in **seconds**:
**GMT**: วัน${gmtDayOfWeek}ที่ ${gmtDay} ${gmtMonth} ${gmtYear} เวลา ${gmtHours}:${gmtMinutes}:${gmtSeconds}
**Your time zone**: วัน${localDayOfWeek}ที่ ${localDay} ${localMonth} ${localYear} เวลา ${localHours}:${localMinutes}:${localSeconds} GMT${tzSign}${String(tzHours).padStart(2, '0')}:${String(tzMinutes).padStart(2, '0')}
**Relative**: ${relativeTime}`;

   // ตัวอย่างการใช้งาน (timestamp ปัจจุบัน)
   //console.log(formatTimestamp(Math.floor(Date.now() / 1000)));
}


function convertTimestampToHHMM(timestamp) {


   // Create a Date object from the timestamp
   const date = new Date(timestamp);
   const options = {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false // ใช้รูปแบบ 24 ชั่วโมง (ถ้าต้องการ 12 ชั่วโมงให้เปลี่ยนเป็น true)
   };
   return date.toLocaleTimeString('th-TH', options);

   // Get hours and minutes
   let hours = date.getHours();
   let minutes = date.getMinutes();
   console.log('Hour', hours)


   // Pad with leading zeros if needed
   hours = hours.toString().padStart(2, '0');
   minutes = minutes.toString().padStart(2, '0');

   // Return the formatted time
   return `${hours}:${minutes}`;
}

// แสดงผลลัพธ์การเทรด
function displayTradeResult(response) {


   let tradeResultAr = [];
   if (document.getElementById("tradeResultTxt").value != '') {
      tradeResultAr = JSON.parse(document.getElementById("tradeResultTxt").value);
   }
   thisNo = tradeResultAr.length + 1;
   thisNo = parseInt(document.getElementById("tradeNo").value);
   LotNo = parseInt(document.getElementById("currentLotNo").value);

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
   tradeResultAr.push(sObj);
   document.getElementById("tradeResultTxt").value = JSON.stringify(tradeResultAr);
   document.getElementById("currentContractId").value = response.buy.contract_id;
   console.log('Tick Time', response.buy.purchase_time)

   ticktimeStr = convertTimestampToHHMM(parseInt(response.buy.purchase_time));
   ticktimeObj = formatTimestampLong(response.buy.purchase_time);
   ticktimeStr = ticktimeObj.stHour;

   TurnCode = document.getElementById("TurnCode").value;
   totalRowTrade++;

   let newRowTrade = `
	   <td>${thisNo}</td>
	    <td id=lotno_${totalRowTrade}>${LotNo}</td>
		<td>${response.buy.contract_id}</td>
		<td>${TurnCode}</td>
		<td>${response.echo_req.parameters.contract_type}</td>

		<td id=ticktimeStr_${response.buy.contract_id}>${ticktimeStr} </td>
		<td id=ticktime_${response.buy.contract_id}>${response.buy.purchase_time} </td>
		<td id=expiretime_${response.buy.contract_id}>${response.buy.expiry_time}</td>
		<td id=balancetime_${response.buy.contract_id}>${response.buy.expiry_time}-${response.buy.purchase_time}</td>

		<td>${response.buy.buy_price}</td>
		<td id=profit_${response.buy.contract_id}>Profit</td>
		<td id=winstatus_${response.buy.contract_id}>!!</td>

	`;
   //    console.log('New Row',newRowTrade);

   document.getElementById("shouldSell").innerHTML = '-';

   const row = document.createElement('tr');
   row.innerHTML = newRowTrade;
   //tbl = document.getElementById("tableTradeResult");
   const dataBody = document.getElementById('dataBody');
   const firstRow = dataBody.firstChild; // หาแถวแรกที่มีอยู่
   //dataBody.appendChild(row);
   // ถ้ามีแถวอื่นอยู่แล้ว ให้แทรกก่อนแถวแรก
   if (firstRow) {
      dataBody.insertBefore(row, firstRow);
   } else {
      // ถ้ายังไม่มีแถวใดๆ เลย ให้เพิ่มเป็นแถวแรก
      dataBody.appendChild(row);
   }


   //alert(totalRowTrade) ;


   console.log('Buy Response', response);

   const tradeResult = document.createElement('div');
   tradeResult.className = 'trade-result';

   const resultHTML = `
        <div class="result-card">
            <h2>ผลลัพธ์การเทรด</h2>
            <div class="result-details">
                <div class="detail-row">
                    <span class="label">รหัสการเทรด:</span>
                    <span class="value">${response.buy.transaction_id}</span>
                </div>
                <div class="detail-row">
                    <span class="label">ราคาซื้อ:</span>
                    <span class="value">${response.buy.buy_price} USD</span>
                </div>
                <div class="detail-row">
                    <span class="label">สถานะ:</span>
                    <span class="value status-success">สำเร็จ</span>
                </div>
                <div class="detail-row">
                    <span class="label">เวลาเริ่ม:</span>
                    <span class="value">${new Date(response.buy.start_time * 1000).toLocaleString()}</span>
                </div>
            </div>
        </div>
    `;

   //tradeResult.innerHTML = resultHTML;
   //document.getElementById("TradeResultDisplay").innerHTML = resultHTML;
   return;


   // หา container หรือสร้างใหม่
   let container = document.getElementById('trade-result-container');
   if (!container) {
      container = document.createElement('div');
      container.id = 'trade-result-container';
      document.body.appendChild(container);
   }

   // ล้างผลลัพธ์เก่าและแสดงผลใหม่
   container.innerHTML = '';
   container.appendChild(tradeResult);

   // อัพเดทสถานะการเทรด
   updateTradeStatus('เทรดสำเร็จ', 'success');
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