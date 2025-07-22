let markerList = [] ;
// ฟังก์ชันสำหรับตรวจสอบการคลิกบนแท่งเทียน
function subscribeToChartClicks() {
  // สร้าง cross-hair เพื่อช่วยในการตรวจจับตำแหน่ง

  chart.subscribeCrosshairMove(param => {
    // ถ้าเมาส์อยู่นอก chart หรือไม่มีข้อมูลที่ตำแหน่งนั้น
    if (!param.point || !param.time || param.point.x < 0 || param.point.y < 0) {
      return;
    }

    // ข้อมูลของ candlestick ที่ crosshair อยู่
    const candleData = param.seriesData.get(candleSeries);

    if (candleData) {
     // console.log('Crosshair on candle:', candleData);
    }
  });

  // สมัครสมาชิกเพื่อตรวจจับการคลิกที่ chart
  chart.subscribeClick(param => { 
    
	if (document.getElementById("lockClearMarkers").checked === true) {
		return ;
	}
    // ตรวจสอบว่ามีการคลิกลงใน chart หรือไม่
    if (!param.point || !param.time || param.point.x < 0 || param.point.y < 0) {
      return;
    }

    // ดึงข้อมูล candlestick ที่ตำแหน่งที่คลิก
    const candleData = param.seriesData.get(candleSeries);

    if (candleData) {
      console.log('Clicked on candle:', candleData);
	  if (document.getElementById("startPoint").value ==='') {
	    document.getElementById("startPoint").value = candleData.time ;
	  } else {
		document.getElementById("stopPoint").value = candleData.time ;
	  }
      // ทำงานเพิ่มเติมเมื่อมีการคลิกบนแท่งเทียน
      handleCandleClick(candleData);
    }
  });
}

// ฟังก์ชันค้นหาและเลื่อนไปยังแท่งเทียนที่ระบุเวลา
  function scrollToTime(timestamp) {
            // แปลง timestamp เป็นตัวเลข
            const timeToFind = parseInt(timestamp);
            const messageBox = document.getElementById('messageBox');

            // ตรวจสอบว่า timestamp ถูกต้องหรือไม่
            if (isNaN(timeToFind)) {
                messageBox.textContent = 'กรุณาระบุ timestamp ที่ถูกต้อง';
                return;
            }
			const candleData = JSON.parse(document.getElementById('dataOutput').value);

            // ค้นหาแท่งเทียนที่มีเวลาตรงกับที่ระบุ
            const foundCandle = candleData.find(candle => parseInt(candle.time) === timeToFind);
            console.log('foundCandle',foundCandle)

            if (foundCandle) {
                // หากพบ เลื่อนไปที่แท่งเทียนนั้น
                const timeScale = chart.timeScale();

                // กำหนดช่วงเวลาที่จะแสดงโดยให้แท่งเทียนที่ต้องการอยู่ตรงกลาง
                timeScale.scrollToPosition(
                    candleData.findIndex(candle => parseInt(candle.time) === timeToFind),
                    0.5 // ตำแหน่งที่จะให้แท่งเทียนอยู่บนหน้าจอ (0.5 = กลางจอ)
                );

                messageBox.textContent = `พบแท่งเทียนที่เวลา ${timeToFind} และเลื่อนไปยังตำแหน่งแล้ว`;
                messageBox.style.color = '#4CAF50';
            } else {
                messageBox.textContent = `ไม่พบแท่งเทียนที่เวลา ${timeToFind}`;
                messageBox.style.color = '#d32f2f';
            }

 } // end func find scroll

// ฟังก์ชันจัดการเมื่อมีการคลิกบนแท่งเทียน
function handleCandleClick(candleData) {
  // คุณสามารถทำอะไรก็ได้กับข้อมูล candlestick ที่ถูกคลิก
  /*
  alert(`คลิกบนแท่งเทียน: ${candleData.time}
  เปิด: ${candleData.open}
  สูงสุด: ${candleData.high}
  ต่ำสุด: ${candleData.low}
  ปิด: ${candleData.close}`);
  */
  // หรือแสดงข้อมูลใน DOM
  const infoElement = document.getElementById('candle-info');
  if (infoElement) {
    infoElement.innerHTML = `
      <h3>ข้อมูลแท่งเทียนที่เลือก</h3>
      <p>เวลา: ${candleData.time}</p>
      <p>เปิด: ${candleData.open}</p>
      <p>สูงสุด: ${candleData.high}</p>
      <p>ต่ำสุด: ${candleData.low}</p>
      <p>ปิด: ${candleData.close}</p>
    `;
  }

  let markersObj = {
      time: candleData.time,
      position: 'aboveBar',
      color: '#f68410',
      shape: 'circle',
      text: 'คลิกที่นี่'
  }

  let foundAt = -1 ;
  let foundTime = 0 ;
  for (let i=0;i<=markerList.length-1 ;i++ ) {
	   //console.log(markerList[i].time);
	   if (markerList[i].time === candleData.time) {
		   foundTime = candleData.time;
		   foundAt = i ;break;

	   }
  }


  let AnalysisData = JSON.parse(localStorage.getItem('AnalysisData'));
  const result = AnalysisData.filter(object => parseInt(object.timestamp) === candleData.time);
//  console.log('Result A',result) ;
  
  document.getElementById("candleCode").innerHTML = result[0].CandleCode;
	  
  //console.log('Result',result[0].MACDHeight,'-',result[0].emaAbove) ;




  if (foundAt >= 0 ) {
	  markerList.splice(foundAt, 1);
	  clearMarkers();
  } else {
      markerList.push(markersObj)
  }

  let startPointSelected = document.getElementById("startPointSelected");
//  console.log('998989--',startPointSelected.value);

  if (startPointSelected) {
	  document.getElementById("startPointSelected").value =  markerList[0].time;
	  document.getElementById("endPointSelected").value =  markerList[markerList.length-1].time;
      document.getElementById("totalPointSelected").value =  markerList.length;
	  dataOutput = JSON.parse(document.getElementById("dataOutput").value) ;

	  const filteredData = dataOutput.filter(item => item.time >= markerList[0].time && item.time <= markerList[markerList.length-1].time);
	  //alert(filteredData.length)

  }

  // หรือเปลี่ยนสีของแท่งเทียนที่เลือก (ต้องใช้ markers)
  //startPointSelected = 54565;

  dataAnaly = document.getElementById("AnalyData").value ;

//  console.log('Data Analy',dataAnaly)
  
 // dataAnaly2 = JSON.PARSE(dataAnaly) ;  
  //alert(dataAnaly2.length);
  /*
  for (let i=0;i<=dataAnaly2.length-1 ;i++ ) {
     console.log(dataAnaly2[i].timestamp)
	  
	  if (parseInt(dataAnaly2[i].timestamp) === candleData.time ) {
		  alert(dataAnaly2[i].CandleCode) ;
		  break ;
	  }  
  }
  */
 
  //candleCode
  
  candleSeries.setMarkers(markerList);
  const dataObj = {
	 startPoint : startPointSelected.value,
     dataAnaly  : dataAnaly
  }


  // newDerivObject/labCandleDerivV2.php
  //AjaxAll('FindSignal',dataObj);


}

function MarkTimeLoss() {

let tradeData= JSON.parse(document.getElementById("messageBox").innerHTML) ;
console.log('Trade Data',tradeData) ;

//let timeLossA = tradeData.TimeLoss ;
//timeLoss = timeLossA.split(';')
//timeLine = timeLossA.split(';')

markerList = [] ;
/*
for (i=0;i<= tradeData.tradeTimeline.length-1 ;i++ ) {
  if (
	  (parseInt(tradeData.tradeTimeline[i].lossCon) > 0 ) &&
	  tradeData.tradeTimeline[i].action !== 'Idle'
  ) {
     thisText = 'L(' + tradeData.tradeTimeline[i].lossCon  +')'  ;
     let markersObj = {
       time: parseInt(tradeData.tradeTimeline[i].timestamp),
       position: 'aboveBar',
       color: '#f68410',
       shape: 'circle',
       text:  thisText
     }
     markerList.push(markersObj);
  }
}
*/
thisColor =  '#f68410'
for (i=0;i<= tradeData.tradeTimeline.length-1 ;i++ ) {
  if (
	  (parseInt(tradeData.tradeTimeline[i].lossCon) > 0 )
  ) {
     if (
	  tradeData.tradeTimeline[i].action === 'Call' ||
      tradeData.tradeTimeline[i].action === 'Put'
	 ) {
		 /*
       if (tradeData.tradeTimeline[i].winStatus =='Y') {
        thisText =  tradeData.tradeTimeline[i].action ;
        thisColor =  '#80ff00' ;
       } else {
        thisText = 'L(' + tradeData.tradeTimeline[i].lossCon  +')'  ;
        thisColor =  '#f68410' ;
	   }
	   */
	   thisText = tradeData.tradeTimeline[i].action ;
	   thisText += '-' + tradeData.tradeTimeline[i].winStatus ;
       if (tradeData.tradeTimeline[i].action === 'Call') {
	      thisColor =  '#80ff00' ;
	   } else {
          thisColor =  '#ff0000' ;
	   }

	 } else {
       // Idle Case
       thisText = 'Idle['+  tradeData.tradeTimeline[i].lossCon +']' ;
	   thisColor =  '#808080' ;
	 }
     let markersObj = {
       time: parseInt(tradeData.tradeTimeline[i].timestamp),
       position: 'aboveBar',
       color: thisColor ,
       size : 1,
       shape: 'arrowDown',
       text:  thisText
     }
     markerList.push(markersObj);
  }
}
//alert(markerList.length);

candleSeries.setMarkers(markerList);

} // end func


function setMarkerFromEvent(markerListTmp) {

  candleSeries.setMarkers(markerListTmp);

} // end func

function MarkTurnPoint() {
/*
circle' - รูปวงกลม
'square' - รูปสี่เหลี่ยมจัตุรัส
'arrowUp' - ลูกศรชี้ขึ้น
'arrowDown' - ลูกศรชี้ลง
'triangle' - รูปสามเหลี่ยม
'flag' - รูปธง
'diamond' - รูปเพชร (สี่เหลี่ยมขนมเปียกปูน)
'star' - รูปดาว
size:1-5,
*/

		 markerList = [] ;
		 let AnalyData = JSON.parse(document.getElementById("AnalyData").value) ;
		 for (let i=1;i<=AnalyData.length-1 ;i++ ) {
			 if (AnalyData[i].PreviousTurnType !=='N') {
				 if (AnalyData[i].PreviousTurnType ==='TurnUp') {
					 thisShape = 'arrowUp';
					 thisColor = ' #0080ff';
					 thisText = 'U';
				 } else {
                     thisShape = 'arrowDown';
					 thisColor = '#ff0080';
					 thisText = 'D';
				 }

				 let markersObj = {
				   time: parseInt(AnalyData[i-1].timestamp),
				   position: 'aboveBar',
				   color: thisColor,
                   size:1,
				   shape: thisShape,
				   text: thisText
			     }
				 markerList.push(markersObj) ;
			 }
		 }
		 //setMarkerFromEvent(markerList);
		 candleSeries.setMarkers(markerList);




} // end func

function MarkNoRisk() {

         markerList = [] ;

		 let RiskPeriodData = JSON.parse(document.getElementById("RiskPeriodData").value) ;
//		 alert(RiskPeriodData.length);
		 for (let i=1;i<=RiskPeriodData.length-1 ;i++ ) {
			 if (RiskPeriodData[i].warnings >=  1) {				  
                 thisShape = 'arrowDown';
				 thisColor = '#ff0080';
				 thisText = 'N'+ RiskPeriodData[i].warnings;			
				 let markersObj = {
				   time: parseInt(RiskPeriodData[i-1].timestamp),
				   position: 'aboveBar',
				   color: thisColor,
                   size:1,
				   shape: thisShape,
				   text: thisText
			     }
				 markerList.push(markersObj) ;
			 }
		 }
		 alert(markerList.length)
		 //setMarkerFromEvent(markerList);
		 candleSeries.setMarkers(markerList);


} // end func


function showmarkerFromList() {

	     listid = document.getElementById("markerName").value ;
		 markerList = [] ;
		 let AnalyData = JSON.parse(document.getElementById("AnalyData").value) ;

		  
		 result = AnalyData.filter(object => object.adxDirectionCon > 0);
		 result = AnalyData ;
		 /*
		 
		 for (let i=1;i<=result.length-1 ;i++ ) {
			 if (result[i].adxDirectionCon > 0) {
				  thisShape = 'arrowUp';
				  thisColor = '#ff0080';
				  thisText = result[i].adxDirectionCon; 
				  if ( thisText === 1) {
					  thisShape = 'arrowDown';
					  thisColor = '#0080ff';
				  }
				  thisText = thisText.toString();
                 
				 let markersObj = {
				   time: parseInt(result[i-1].timestamp),
				   position: 'aboveBar',
				   color: thisColor,
                   size:1,
				   shape: thisShape,
				   text: thisText
			     }
			    markerList.push(markersObj) ;
			 }
		 }
		 */
		 thisText = 'I'; thisShape = 'circle';
		 thisColor = '#ff0080';
		 for (let i=1;i<=result.length-1 ;i++ ) {
			  
			  //thisText = result[i].TurnMode999; 
			  if ( result[i].PreviousTurnType === 'TurnUp') {
				  thisShape = 'arrowUp';
				  thisColor = '#0080ff';
				  thisText =  'U';
			  }
			  if ( result[i].PreviousTurnType === 'TurnDown') {
				  thisShape = 'arrowDown';
				  thisColor = '#ff0080';
				  thisText = 'D';
			  }
			  //thisText = result[i].TurnType; 
			  let markersObj = {
				   time: parseInt(result[i].timestamp),
				   position: 'aboveBar',
				   color: thisColor,
                   size:1,
				   shape: thisShape,
				   text: thisText
			  }
			  markerList.push(markersObj) ;              
		 }

		 candleSeries.setMarkers(markerList);




} // end func



function MarkCutPoint() {
/*
circle' - รูปวงกลม
'square' - รูปสี่เหลี่ยมจัตุรัส
'arrowUp' - ลูกศรชี้ขึ้น
'arrowDown' - ลูกศรชี้ลง
'triangle' - รูปสามเหลี่ยม
'flag' - รูปธง
'diamond' - รูปเพชร (สี่เหลี่ยมขนมเปียกปูน)
'star' - รูปดาว
size:1-5,
*/

		 markerList = [] ;
		 let AnalyData = JSON.parse(document.getElementById("AnalyData").value) ;
		 MaxTrade = 0 ; TimeMax = [];
		 TotalTrade = 0 ;
		 for (let i=1;i<=AnalyData.length-1 ;i++ ) {
			 previousCutPointType = AnalyData[i-1].CutPointType ;
			 if (AnalyData[i].CutPointType !=='N' && previousCutPointType ==='N') {
				 TotalTrade++ ;
				 if (AnalyData[i].CutPointType ==='3->5') {
					 thisShape = 'arrowDown';
					 thisColor = '#ff0080';
					 thisText = 'ตัดลง(35)' ;
				 } else {
                     thisShape = 'arrowUp';
					 thisColor = ' #0080ff';
					 thisText = 'ตัดขึ้น(53)' ;
				 }

				 TotalTradeToWin = numTradeToWin(AnalyData,i,AnalyData[i].CutPointType);
				 if (TotalTradeToWin > MaxTrade ) {
					 MaxTrade = TotalTradeToWin;
					 TimeMax.push(AnalyData[i].timefrom_unix);
				 }
				 thisText +=  '=' + TotalTradeToWin;

				 let markersObj = {
				   time: parseInt(AnalyData[i].timestamp),
				   position: 'aboveBar',
				   color: thisColor,
                   size:1,
				   shape: thisShape,
				   text: thisText
			     }
				 markerList.push(markersObj) ;
			 }
		 } // end for
         stTimeMax = '';
		 for (let i=0;i<=TimeMax.length-1 ;i++ ) {
		   stTimeMax += TimeMax[i]+',' ;
		 }
		 alert('TotalTrade=' + TotalTrade +' Max Trade= '+MaxTrade+ ' จำนวน = '+ TimeMax.length+ ' at ' + stTimeMax);
		 document.getElementById("labResult").innerHTML = 'TotalTrade=' + TotalTrade +' Max Trade= '+MaxTrade+ ' จำนวน = '+ TimeMax.length+ ' at ' + stTimeMax+ '<hr>';


		 let totalTrade = 0 ;
		 let totalWin = 0 ;
		 let totalLoss = 0 ;
		 let st ='<table>' ;




		 let winStatus = '';
		 for (let i=1;i<=AnalyData.length-2 ;i++ ) {
		  if (AnalyData[i].CutPointType !=='N') {
			 st+='<tr>';
			 winStatus = '';
             if (AnalyData[i].CutPointType ==='3->5') {
                 st+= '<td>' + AnalyData[i-1].timefrom_unix + '</td>';
                 st+= '<td>' + AnalyData[i].CutPointType + '</td>';
				 totalTrade++;
				 nextColor = AnalyData[i].timefrom_unix + ' : '+ AnalyData[i].thisColor;
				 if (AnalyData[i].thisColor ==='Red') {
                    totalWin++ ;
					winStatus = 'Win';
			     } else {
                    totalLoss++;
					winStatus = 'Loss';
				 }
				 st+= '<td>' + 'Request Red' + '</td>';
				 st+= '<td>' + nextColor  + '</td>';
				 st+= '<td>' + AnalyData[i+1].pip  + '</td>';
				 st+= '<td>' + winStatus  + '</td>';


             }
			 if (AnalyData[i].CutPointType ==='5->3') {
				 st+= '<td>' + AnalyData[i-1].timefrom_unix + '</td>';
                 st+= '<td>' + AnalyData[i].CutPointType + '</td>';
				 totalTrade++;
				 requestColor = 'Green';
				 nextColor = AnalyData[i].timefrom_unix + ' : '+ AnalyData[i].thisColor;
				 if (AnalyData[i].thisColor === requestColor) {
                    totalWin++ ; winStatus = 'Win';
			     } else {
                    totalLoss++; winStatus = 'Loss';
				 }
				 st+= '<td>' + 'Request Green'  + '</td>';
				 st+= '<td>' + nextColor  + '</td>';
				 st+= '<td>' + AnalyData[i+1].pip  + '</td>';
				 st+= '<td>' + winStatus  + '</td>';
			 }
			 st+='</tr>';
			 let markersObj = {
				   time: parseInt(AnalyData[i-1].timestamp),
				   position: 'belowBar',
				   color: thisColor,
                   size:1,
				   shape: thisShape,
				   text: winStatus
			 }
             //markerList.push(markersObj) ;

		  }
		 }

         st+='</table>';
		 ///alert(totalWin +' vs ' + totalLoss);
		 /*alert(totalTrade+'/'+AnalyData.length);

		 */
		 document.getElementById("labResult999").innerHTML = st;
		 candleSeries.setMarkers(markerList);

         let st2  ='TimeFrame =' + document.getElementById("timeframe").value + 'M' +  ' จำนวน  การเทรด =' + (totalWin+totalLoss) ;
         st2 += ' จำนวน Win =' + totalWin ;
		 st2 += ' จำนวน  Loss =' + totalLoss ;


		 document.getElementById("labResult").innerHTML +=  st2+"<br>" ;



		 //setMarkerFromEvent(markerList);
		 




} // end func



function numTradeToWin(AnalyData,thisPoint,CutPointType) {


 	     if (CutPointType ==='3->5') {
			 SuggestColor = 'Red'
	     }
		 if (CutPointType ==='5->3') {
			 SuggestColor = 'Green'
	     }
		 numTrade = 0 ;
		 sObj = {
          AnalyData : AnalyData[thisPoint].candleID,
		  timeFrom : AnalyData[thisPoint].timefrom_unix,

		 }

		 tradeList = []
         for (let i=thisPoint+1;i<=AnalyData.length-1 ;i++ ) {
			emaAbove = AnalyData[i-1].emaAbove ;
			emaConflict = AnalyData[i-1].emaConflict ;
            if (emaConflict == 'N') {
				 numTrade++ ;
				 if (emaAbove === '3') {
					 SuggestColor = 'Green'
				 } else {
					 SuggestColor = 'Red'
				 }
				 if (AnalyData[i].thisColor === SuggestColor ) {
					 //return numTrade ;
					 break;
				 } else {
					 //numTrade++ ;
					 a= 9999
				 }

		    } else {
				action = 'Idle';
			}

         } // end for

		 return numTrade ;



} // end func

function MarkLossPoint(tradeTimeline) {


// ตัวอย่างการใช้งาน:
const jsonData = JSON.stringify(tradeTimeline) ;
//'[{"timestamp":"1738505580","action":"Call","SuggestColor":"Green","resultColor":"Green","lossCon":0,"winStatus":"Y"},{"timestamp":"1738505640","action":"Call","SuggestColor":"Green","resultColor":"Green","lossCon":0,"winStatus":"Y"},{"timestamp":"1738505700","action":"Call","SuggestColor":"Green","resultColor":"Green","lossCon":0,"winStatus":"Y"}]';

// เรียกฟังก์ชันเพื่อสร้างตาราง
const tableElement = createHTMLTable(jsonData);

// นำตารางไปแสดงผลในส่วนใดส่วนหนึ่งของ HTML (เช่น div ที่มี id="labtrade_container")

  const tableContainer = document.getElementById('labtrade_container');
  tableContainer.innerHTML = '';
  if (tableContainer) {
    tableContainer.appendChild(tableElement);
  } else {
    document.body.appendChild(tableElement); // หากไม่มี container ให้เพิ่มลงใน body โดยตรง
  }



} // end func

function createHTMLTable(jsonData) {
  try {
    const dataArray = JSON.parse(jsonData);
    if (!Array.isArray(dataArray) || dataArray.length === 0) {
      return "<p>No data to display.</p>";
    }

    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const tbody = document.createElement('tbody');

    // สร้างส่วนหัวของตาราง
    const headerRow = document.createElement('tr');
    const firstObject = dataArray[0];
    for (const key in firstObject) {
      if (firstObject.hasOwnProperty(key)) {
        const th = document.createElement('th');
        th.textContent = key;
        headerRow.appendChild(th);
      }
    }
    thead.appendChild(headerRow);
    table.appendChild(thead);

    // สร้างส่วนเนื้อหาของตาราง
    dataArray.forEach(item => {
      const row = document.createElement('tr');
      for (const key in item) {
        if (item.hasOwnProperty(key)) {
          const td = document.createElement('td');
          td.textContent = item[key];
          row.appendChild(td);
        }
      }
      tbody.appendChild(row);
    });
    table.appendChild(tbody);

    return table;
  } catch (error) {
    console.error("Error parsing JSON:", error);
    return "<p>Error: Could not parse JSON data.</p>";
  }
}



function clearMarkers() {
 
  if (document.getElementById("lockClearMarkers").checked) {
	  return ;
  }
  document.getElementById("startPoint").value = '';
  document.getElementById("stopPoint").value = '';

  markerList = [] ;
  candleSeries.setMarkers(markerList);
} // end func

function ShowDataFromSelectPoint() {

console.log(markerList)

startMarkerTime = markerList[0].time ;
stopMarkerTime = markerList[markerList.length-1].time ;
if (startMarkerTime >  stopMarkerTime) {
	let tmp = startMarkerTime;
    startMarkerTime =  stopMarkerTime
    stopMarkerTime  =  tmp

}

 console.log(startMarkerTime,'-',stopMarkerTime)

let AnalysisData = JSON.parse(localStorage.getItem('AnalysisData'));
//console.log('Length All =',AnalysisData.length);
console.log('Data =',AnalysisData[0]);



const result = AnalysisData.filter(object => parseInt(object.timestamp) >= startMarkerTime
&&  parseInt(object.timestamp) <= stopMarkerTime );

let stTable = '<table>';
stTable += '<tr><td>Time</td><td>ema3</td><td>ema5</td><td>MACD</td><td>emaAbove</td></tr>'
for (let i=0;i<=result.length-1 ;i++ ) {

	stTable += '<tr><td>'+ result[i].timefrom_unix + '</td><td>'+ result[i].ema3.toFixed(2)+'</td>';
	stTable += '<td>'+result[i].ema5.toFixed(2)+'</td>';
	stTable += '<td>'+result[i].MACDHeight +'</td>';
	stTable += '<td>'+result[i].emaAbove +'</td>';
	stTable += '<td>'+result[i].CutPointType +'</td>';


	stTable += '</tr>';

}

stTable += '</table>';
console.log('Find Mark Result ',result);
document.getElementById("labTradeResult").innerHTML = stTable;


} // end func

async function TestAjax() {

sDat = JSON.parse(document.getElementById("AnalyData").value) ;

    let result ;
    let ajaxurl = 'AjaxGetSignal.php';
    let data = { "Mode": 'TestAjax',


    } ;
    data2 = JSON.stringify(data);
	
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
			  alert(textStatus + ": " + jqXHR.status + " " + errorThrown);	 
              console.log(textStatus + ": " + jqXHR.status + " " + errorThrown);
            }
        });
        alert(result);
		
        return result;
    } catch (error) {
        console.error(error);
    }
}

 
 