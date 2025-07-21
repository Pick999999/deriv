
$(document).ready(function() {

            
            getCurpair();
            
            // Initialize datetimepicker with timezone support
            $('.datetimepicker').datetimepicker({
                format: 'Y-m-d H:i',
                step: 1,
                onShow: function(ct, $input) {
                    const tz = 'Asia/Bangkok';
                    $input.val(moment.tz(ct, tz).format('YYYY-MM-DD HH:mm:ss'));
                },
                onChangeDateTime: function(ct, $input) {
                    const tz = 'Asia/Bangkok';
                    $input.val(moment.tz(ct, tz).format('YYYY-MM-DD HH:mm:ss'));
                }
            });

            document.getElementById('start_date3').value = localStorage.getItem('startHistorydate')
            document.getElementById('end_date3').value =  localStorage.getItem('endHistorydate')
            // localStorage.setItem('curpairHistory',document.getElementById('curpairText3').value )
            // $("#btnRealTrade").click()
            // $("#btngetCurpairOpen").trigger("click");
            

            // $("#setTimeserver").trigger("click" );

            // Load dates from localStorage
            loadDateTimePickers();

            // Save dates to localStorage
              
            $('#btnAnalysis').on('click', function(event) {
                event.preventDefault();
                localStorage.setItem('startHistorydate',document.getElementById('start_date2').value )
                localStorage.setItem('endHistorydate',document.getElementById('end_date2').value )
                localStorage.setItem('curpairHistory',document.getElementById('curpairText2').value )

                sdata = document.getElementById("candlestick-form")
                $.ajax({
                    url: '/plotgraphFromDecision',
                    type: 'POST',
                    data: $("#candlestick-form2").serialize(),
                    success: function(data) {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        console.log('Dat Module Graph Decision ', data);
                        
                        chart = null
                        document.getElementById("chartdecision").innerHTML = ""
                        document.getElementById("ema3Table").innerHTML = ""
                        sCaption = data.candlestick_data[0]['minuteno'] + '['+data.candlestick_data[0]['color'] + '] ถึง ' + data.candlestick_data[data.candlestick_data.length-1]['minuteno'] + '['+data.candlestick_data[data.candlestick_data.length-1]['color'] + ']'                     
                        document.getElementById("labelGraph").innerHTML = sCaption                        
                        
                        
                        lastIndex = data.candleListAnalyzed.length-1
                        lastIndex0 = data.candleListAnalyzed.length-2
                        if (data.candleListAnalyzed[lastIndex0]['ADX']['value'] < data.candleListAnalyzed[lastIndex]['ADX']['value']) {
                            sGrow = ' เพิ่ม '
                        } else {
                            sGrow = ' ลดลง ' 
                        }
                        
                        sCaption2 = 'ADX= '+  data.candleListAnalyzed[lastIndex]['ADX']['value'] + ' ' + sGrow
                        sCaption2 += ' rsi= '+  data.candleListAnalyzed[lastIndex]['RSI']['value']

                        document.getElementById("labelGraph").innerHTML += '<br>' + sCaption2

                        localStorage.setItem('candleListAnalyzed',JSON.stringify(data.candleListAnalyzed))                        
                        let curpair = data.curpair
                        let candlestickData = data.candlestick_data.map(item => {
                            return {
                                x: new Date(item.from * 1000),
                                y: [item.open, item.max, item.min, item.close]
                            };
                        });

                        let ema3Data = data.ema3_data.map(item => {
                            return {
                                x: new Date(item.from * 1000),
                                y: item.ema3
                            };
                        });

                        let ema5Data = data.ema5_data.map(item => {
                            return {
                                x: new Date(item.from * 1000),
                                y: item.ema5
                            };
                        });

                        let upperBandData = data.upper_band.map(item => {
                            return {
                                x: new Date(item.from * 1000),
                                y: item.value
                            };
                        });

                        let middleBandData = data.middle_band.map(item => {
                            return {
                                x: new Date(item.from * 1000),
                                y: item.value
                            };
                        });

                        let lowerBandData = data.lower_band.map(item => {
                            return {
                                x: new Date(item.from * 1000),
                                y: item.value
                            };
                        });

                        AnalyEMAAr = [] 
                        AnalyEMA = data.candleListAnalyzed ;
                        for ( i = 0 ; i<= data.candleListAnalyzed.length-1;i++) {
                            AnalyEMAAr.push(data.candleListAnalyzed[i].AnalyEMA)
                        }

                        console.log('ANALY EMA ' , AnalyEMAAr)

                        

                        var options = {
                            chart: {
                                type: 'candlestick',
                                height: 350,
                                background: '#000' 
                            },
                            series: [{
                                name: 'Candlestick',
                                data: candlestickData
                            }, {
                                name: 'EMA3',
                                type: 'line',
                                data: ema3Data,
                                color: '#FFF' // Set EMA3 line color to white
                            }, {
                                name: 'EMA5',
                                type: 'line',
                                data: ema5Data,
                                color: '#FF0000' // Optional: set EMA5 line color to red
                            }, 
                             
                            ],
                            xaxis: {
                                type: 'datetime',
                                
                                labels: {
                                    style: {
                                        colors: '#FFFF00' // Set y-axis label color to yellow
                                    },
                                    datetimeFormatter: {
                                        year: 'yyyy',
                                        month: "MMM 'yy",
                                        day: 'dd MMM',
                                        hour: 'HH:mm',
                                    },
                                    formatter: function(val, timestamp) {
                                        return moment(timestamp).tz('Asia/Bangkok').format('HH:mm');
                                    }
                                },
                                tickAmount: 'dataPoints' // This will display all ticks
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        colors: '#FFFF00' // Set y-axis label color to yellow
                                    }
                                }
                            },
                            yaxis: {
                                tooltip: {
                                    enabled: true
                                }
                            },
                            title: {
                                text: 'Candlestick Chart with EMA3 and EMA5 OF ' + curpair,
                                align: 'left',
                                style: {
                                    color: '#FFF' // Set title color to white
                                }
                            },
                            
                            plotOptions: {
                                candlestick: {
                                    colors: {
                                        upward: '#00B746',
                                        downward: '#EF403C'
                                    },
                                    wick: {
                                        useFillColor: true,
                                    },
                                    borderColor: '#FFF' // Set candlestick border color to white
                                }
                            }
                        };

                        sTable =  TableEMA(AnalyEMAAr)
                        document.getElementById("ema3Table").innerHTML = sTable
                        
                        console.log(sTable)

                        
                        
                        var chart = new ApexCharts(document.querySelector("#chartdecision"), options);
                        chart.render();
                        chart.updateSeries(options);
                        return;
                        
                        
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            });
 
        });

        
        
         
        

function setDateTimePickerNow() {


const now = moment().tz('Asia/Bangkok');
const start = now.clone().subtract(30, 'minutes'); 



// แปลงเวลาปัจจุบันให้เป็น string ในรูปแบบที่ต้องการ
dateString1 = now.format('YYYY-MM-DD HH:mm:ss');
dateString2 = start.format('YYYY-MM-DD HH:mm:ss');

dateString1Ar = dateString1.split(' ')
dateString1 = dateString1Ar[0] + ' 07:00:00'

dateString2Ar = dateString2.split(' ')
dateString2 = dateString2Ar[0] + ' 23:30:00'
console.log(dateString2);

// dateString1 = "2024-03-08 07:00:00"; // รูปแบบ YYYY-MM-DD
// dateString2 = "2024-03-08 23:33:00"; // รูปแบบ YYYY-MM-DD

// แปลง string เป็น moment object
let momentDate = moment(dateString1);
let momentDate2 = moment(dateString2);

$('#start_date3').val(momentDate.format('YYYY-MM-DD HH:mm:ss'));
$('#end_date3').val(momentDate2.format('YYYY-MM-DD HH:mm:ss'));     

    // $('#start_date3').val(start.format('YYYY-MM-DD HH:mm:ss'));
    // $('#end_date3').val(now.format('YYYY-MM-DD HH:mm:ss')); 

    
    saveDateTimePickers(start, now);
}   

function getDayOfWeek(dateString) {
    // สร้าง Moment object จากสตริงวันที่
    const date = moment(dateString);
    
    // ใช้ฟังก์ชัน format เพื่อรับชื่อวันเป็นภาษาไทย
    return date.locale('th').format('dddd');
  }

function AddDay(numDay) {

startDate = document.getElementById('start_date3').value   ;
endDate = document.getElementById('start_date3').value   ;
console.log(' startDate ',startDate)

aa = new Date(startDate)
console.log('aa date',aa.getDay())

const start = moment.tz($('#start_date3').val(), 'Asia/Bangkok');
const end = moment.tz($('#end_date3').val(), 'Asia/Bangkok');
if (numDay > 0 ) { minutes = 1440 * numDay }
if (numDay < 0 ) { minutes = 1440 * numDay }



            start.add(minutes, 'minutes');
            end.add(minutes, 'minutes');

            $('#start_date3').val(start.format('YYYY-MM-DD HH:mm:ss'));
            $('#end_date3').val(end.format('YYYY-MM-DD HH:mm:ss'));

            dateString= document.getElementById('start_date3').value ; 
            console.log('dateString',dateString)
            
            dayWeek = getDayOfWeek(dateString)            
            console.log('dayWeek',dayWeek)
            document.getElementById('dayOfWeek').value  = dayWeek ;    
            totalCandle = calculateDifference()
            document.getElementById('totalCandle').value  = totalCandle;
            
            /*
const dayOfWeek = parseInt(aa.getDay());  
console.log('dayOfWeek =',dayOfWeek)
if (dayOfWeek == 1) { 
    dayWeek =  "วันจันทร์"; 
}    
if (dayOfWeek == 2) { 
    dayWeek =  "วันอังคาร"; 
}
if (dayOfWeek == 3) { 
    dayWeek =  "วันพุธ";
}
if (dayOfWeek == 4) { 
    dayWeek =  "วันพฤหัส";
 }
if (dayOfWeek == 5) { dayWeek =  "วันศุกร์"; }
if (dayOfWeek == 6) { dayWeek =  "วันเสาร์"; }
if (dayOfWeek == 7) { dayWeek =  "วันอาทิตย์"; }
*/


    

   
}


function saveDateTimePickers(start, end) {
            localStorage.setItem('start_date3', start.format('YYYY-MM-DD HH:mm:ss'));
            localStorage.setItem('end_date3', end.format('YYYY-MM-DD HH:mm:ss'));
}

function loadDateTimePickers() {

            const start = localStorage.getItem('startDecisiondate');
            const end = localStorage.getItem('endDecisiondate');
            if (start && end) {
                $('#start_date2').val(start);
                $('#end_date2').val(end);
            }
            curpairDecision = localStorage.getItem("curpairDecision")
            document.getElementById('curpairText2').value  = curpairDecision ;
}

function addTime(minutes) {
            const start = moment.tz($('#start_date2').val(), 'Asia/Bangkok');
            const end = moment.tz($('#end_date2').val(), 'Asia/Bangkok');

            start.add(minutes, 'minutes');
            end.add(minutes, 'minutes');

            $('#start_date2').val(start.format('YYYY-MM-DD HH:mm:ss'));
            $('#end_date2').val(end.format('YYYY-MM-DD HH:mm:ss'));

            saveDateTimePickers(start, end);
}

         
      
         
         
         

        
function getCurpair() {

            var listBox = document.getElementById('curpairList');
            //alert(listBox.length)
            if ( listBox.length >= 2 ) { return }

            $.ajax({ 
                url: '/getAllCurpair',
                type: 'POST',
                data: $('#candlestick-form').serialize(),
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    console.log(data)
                    
                    data.forEach(function(item) {
                        var option = document.createElement('option');
                        option.value = item.curpairName;
                        option.text = item.curpairName;
                        listBox.appendChild(option);
                    });
                    
                },
                error: function(error) {
                    console.error('Error fetching data:', error);
                }
            });
}

         

function convertToThaiMinutes(dateString) {
            // สร้าง Date object จากวันที่และเวลาในรูปแบบสตริง
            const date = new Date(dateString);
            
            // ตรวจสอบว่า Date object ถูกสร้างขึ้นถูกต้องหรือไม่
            if (isNaN(date)) {
                console.log('Invalid date string');
                return null;
            }
            
            // ดึงชั่วโมงและนาทีจาก Date object
            const hours = date.getHours();
            const minutes = date.getMinutes();
            
            // แปลงเวลาเป็นนาที
            const totalMinutes = (hours * 60) + minutes;
             // จัดรูปแบบเวลาเป็น "HH:MM"
            const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            
            return formattedTime;
}

function TableEMA(AnalyEMAAr) {

            
            sTable = '<table>';
            rows = '' ;

            
            sRow1 = '<tr><td>MinuteNo</td>' ; sRow2 = '<tr><td>SlopeDirection</td>' ;
            sRow3 = '<tr><td>Slope Value</td>' ; sRow4 = '<tr><td>MACD</td>' ;
            sRow5 = '<tr><td>emaTurnType</td>' ; sRow6 = '<tr><td>Color</td>' ;
            sRow6 = '<tr><td>Color</td>' ;
            sRow7 = '<tr><td>Lab</td>' ; 
            console.log("--->In Plot Table ",AnalyEMAAr)

            for(i = 1 ; i < AnalyEMAAr.length- 1; i++ ) {               

                AnalyEMAAr[i]['macd'] = AnalyEMAAr[i]['macd'] *1000*1000
                AnalyEMAAr[i]['macd'] = AnalyEMAAr[i]['macd'].toFixed(2)
                //console.log('Minute No ', AnalyEMAAr[i]['minuteno'])
                if (AnalyEMAAr[i]['ema3SlopeDirection'] == 'Slope-Up') {
                    AnalyEMAAr[i]['ema3SlopeDirection'] = '<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRWox3FmMKKU2sojqDJT_4vXDc-3PeHFM2qKQ&s" width=30>'
                } else {
                    AnalyEMAAr[i]['ema3SlopeDirection'] = '<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS194DdCUJL6zAu9i-XnR26kU9FcCG1bhmo_g&s" width=30>' 
                }
                if (Math.abs(AnalyEMAAr[i]['ema3SlopeValue']) <= 12) {
                    if (AnalyEMAAr[i]['ema3SlopeValue'] <= 0) {
                      AnalyEMAAr[i]['ema3SlopeDirection'] = '<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTT4UoPs5V0Anr12XhztM_Wg6Ih8RvMkMPXyw&s" width=30>' 
                    } else {
                      AnalyEMAAr[i]['ema3SlopeDirection'] = '<img src="https://png.pngtree.com/png-vector/20210407/ourmid/pngtree-arrow-green-icon-clipart-png-image_3141336.jpg" width=30>'   
                    }  
                }

                sRow1 += `<td>${AnalyEMAAr[i].minuteno}</td>`
                sRow2 += `<td>${AnalyEMAAr[i]['ema3SlopeDirection']}</td>`
                sRow3 += `<td>${AnalyEMAAr[i]['ema3SlopeValue'].toFixed(2)}</td>`
                sRow4 += `<td>${AnalyEMAAr[i]['macd']}</td>`
                sRow5 += `<td>${AnalyEMAAr[i]['emaTurnType']}</td>`
                if (AnalyEMAAr[i]['color'] == "Green" ) {
                   sDiv = '<div style="width:80px;height:20px;background:green;color:white">Green</div>'
                }
                if (AnalyEMAAr[i]['color'] == "Red" ) {
                   sDiv = '<div style="width:80px;height:20px;background:red;color:white">Red</div>'                   
                }
                if (AnalyEMAAr[i]['color'] == "Equal" ) {
                    sDiv = '<div style="width:80px;height:20px;background:gray;color:white">Equal</div>'                   
                 }

                sRow6 += `<td style="text-align:center">`+ sDiv + `</td>`
                // sRow6 += `<td>${AnalyEMAAr[i]['color']}</td>`
                sRow7 += `<td><button type="button" id="btn_`+ (i-1) +`" onclick=CreateLabAnaly(` + (i-1) + `,'` + AnalyEMAAr[i].minuteno + `')>`+ AnalyEMAAr[i].minuteno+ `</button>`
                

                // if (Math.abs(sLope[i]) <=10) {
                //  sRow2 += `<td class="fontRed">${sLope[i]}</td>`
                // } else {
                //   sRow2 += `<td>${sLope[i]}</td>`
                // }
             }

            // for(i = 1 ; i< jsonData.length- 1; i++ ) {               
            //    sRow1 += `<td>${MinuteAr[i]}</td>`
            //    if (Math.abs(sLope[i]) <=10) {
            //     sRow2 += `<td class="fontRed">${sLope[i]}</td>`
            //    } else {
            //      sRow2 += `<td>${sLope[i]}</td>`
            //    }
            // }
            sRow1 =  sRow1 + '</tr>' 
            sRow2 =  sRow2 + '</tr>' 
            sRow3 =  sRow3 + '</tr>' 
            sRow4 =  sRow4 + '</tr>' 
            sRow5 =  sRow5 + '</tr>' 
            sRow6 =  sRow6 + '</tr>' 
            sRow7 =  sRow7 + '</tr>' 

            sTable = '<table id="tblAnalyzed" border=1>' + sRow1 +  sRow3 + sRow4 + sRow5 + sRow6 + sRow2 + sRow7 +  '</table>' ;
            return sTable ;

} 
 

function chkQThread() {

         
         console.log('qOfThread',qOfThread)
         if (qOfThread.wstatus == 'idle' && document.getElementById("curpairSelected").value != '' && document.getElementById("lockTrade").checked== false) {         
             $('#btnRealTrade2').trigger("click")
         }
         
        //  alert('Check')
}         

function setQThread() {

         console.log('qOfThread',qOfThread)
}         

function setCurPairToText() {

    document.getElementById('curpairText2').value = document.getElementById('curpairList').value
}

// แก้ไข Ajax Click ข้างล่าง
// รูปแบบ การ Request ฝั่ง html,Python 
// HTML =<input type='text' id='start_date' name='start_date' class='form-control datetimepicker' required>
// start_date = request.form['start_date']
// end_date = request.form['end_date']
function CreateLabAnaly(idno,minuteno) {

    Ema3SlopePararell = document.getElementById('Ema3SlopePararell').checked ? 'y' : 'n';
    checkMACDHeight = document.getElementById('macdHeight').checked ? 'y' : 'n';
    checkEMAConflict = document.getElementById('emaConflict').checked ? 'y' : 'n';
    
    for (i=0;i<=30 ;i++ ) {
        thisID = '#btn_' + i ;
        if  ($(thisID).hasClass("selectedBtn") ) {
            $(thisID).removeClass("selectedBtn") 
        }
    } 
    thisID = '#btn_' + idno ;
    $(thisID).addClass("selectedBtn")
    // document.getElementById(thisID).addClass
    // selectedBtn
    dataToSend = {
        "minuteno" : minuteno,
        "ema3Pararell" : Ema3SlopePararell,
        "checkMACDHeight" : checkMACDHeight,
        "checkEMAConflict" : checkEMAConflict,
    }
    console.log('datato Send',dataToSend)
    fetch('/CreateLabAnaly', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dataToSend)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
        })
        .catch((error) => {
            console.error('Error:', error);
        });

    // alert(minuteno)
    // $.ajax({ 
    //     url: '/CreateLabAnaly',
    //     type: 'POST',
    //     data: $('#candlestick-form').serialize(),
    //     success: function(data) {
    //         if (data.error) {
    //             alert(data.error);
    //             return;
    //         }
    //         console.log(data)
    //         localStorage.setItem('curpairOpen',JSON.stringify(data))           
    //     },
    //     error: function(error) {
    //         console.error('Error fetching data:', error);
    //     }
    // });
}

function ViewTradeFromMinute() {
    // แก้ไข Ajax Click ข้างล่าง
    // รูปแบบ การ Request ฝั่ง html,Python 
    // HTML =<input type='text' id='start_date' name='start_date' class='form-control datetimepicker' required>
    // start_date = request.form['start_date']
    // end_date = request.form['end_date']
       
       
    Ema3SlopePararell = document.getElementById('Ema3SlopePararell').checked ? 'y' : 'n';
    checkMACDHeight = document.getElementById('macdHeight').checked ? 'y' : 'n';
    checkEMAConflict = document.getElementById('emaConflict').checked ? 'y' : 'n';

       dataToSend = {
        "ema3Pararell" : Ema3SlopePararell,
        "checkMACDHeight" : checkMACDHeight,
        "checkEMAConflict" : checkEMAConflict,
       }
       console.log('optionTrade',dataToSend)
       fetch('/ViewTradeFromMinute', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dataToSend)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            generateTable(data.AnswerObject)
        })
        .catch((error) => {
            console.error('Error:', error);
        });

        // $.ajax({ 
        //     url: '/ViewTradeFromMinute',
        //     type: 'POST',
        //     data: $('#candlestick-form').serialize(),
        //     data2: optionTrade,
        //     success: function(data) {
        //         if (data.error) {
        //             alert(data.error);
        //             return;
        //         }
        //         console.log(data)
        //         generateTable(data.AnswerObject)
        //     },
        //     error: function(error) {
        //         console.error('Error fetching data:', error);
        //     }
        // });
 
}

function generateTable(data) {
    if (!Array.isArray(data) || data.length === 0) {
        console.error("Invalid data provided. It must be a non-empty array.");
        return;
    }

    // Create table element
    const table = document.createElement('table');

    // Create table header row
    const headerRow = document.createElement('tr');
    
    Object.keys(data[0]).forEach(key => {
        const th = document.createElement('th');
        th.textContent = key.charAt(0).toUpperCase() + key.slice(1);
        headerRow.appendChild(th);
    });
    table.appendChild(headerRow);

    // Create table rows
    data.forEach(item => {
        const row = document.createElement('tr');
        colno= 0 
        Object.values(item).forEach(val => {
            
            if (colno == 3 ) { 
               lastValue = val
               console.log('lastValue=',lastValue)
            }
            
            const td = document.createElement('td');
            td.textContent = val;
            row.appendChild(td);
            colno++ 
        });
        if (lastValue == 'y' ) {
            row.classList.add('brakeTrade')
        }
        table.appendChild(row);
    });

    // Append table to container
    const tableContainer = document.getElementById('table-container');
    tableContainer.innerHTML = '';
    tableContainer.appendChild(table);
}


async function doAjaxCheckCurOpenNow() {
   let result ;
   let ajaxurl = '/listCurpairOpen';
   let data = { 
    'Mode': 'listCurpair' 
    
   } ;
   data2 = JSON.stringify(data);
   //alert(data2);
   try {
     result = await $.ajax({
     url: ajaxurl,
     type: 'POST',
       data: data2,
       success: function(data, textStatus, jqXHR){
       console.log(textStatus + ' :' + jqXHR.status);
       // do something with data
     },
     error: function(jqXHR, textStatus, errorThrown){
              alert(textStatus + ': ' + jqXHR.status + ' ' + errorThrown);
              console.log(textStatus + ': '  + jqXHR.status + ' '  + errorThrown);
       }
      });
      alert(result);
      
      return result;
     } catch (error) {
      console.error(error);
  }
}
 
function calculateDifference() {
    const datetimepicker1 = document.getElementById('start_date3').value;
    const datetimepicker2 = document.getElementById('end_date3').value;
    
    if (!datetimepicker1 || !datetimepicker2) {
      document.getElementById('result').textContent = 'กรุณาเลือกวันเวลาทั้งสองช่อง';
      return;
    }
    
    const date1 = new Date(datetimepicker1);
    const date2 = new Date(datetimepicker2);
    
    const diffInMs = Math.abs(date2 - date1);
    const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
    
    // alert()
    //document.getElementById('result').textContent = `ความต่างคือ ${diffInMinutes} นาที`;
    return diffInMinutes

  }

  function convertToTimestamp() {
    const datetimepicker1 = document.getElementById('start_date3').value;
    const datetimepicker2 = document.getElementById('end_date3').value;
    
    if (!datetimepicker1 || !datetimepicker2) {
      document.getElementById('result').textContent = 'กรุณาเลือกวันเวลาทั้งสองช่อง';
      return;
    }
    
    const timestamp1 = new Date(datetimepicker1).getTime() / 1000;
    const timestamp2 = new Date(datetimepicker2).getTime() / 1000;
    
    /*document.getElementById('result').innerHTML = `
      Timestamp ของวันเวลาที่ 1: ${timestamp1}<br>
      Timestamp ของวันเวลาที่ 2: ${timestamp2}
    `;
    */
    return [timestamp1,timestamp2]
  }

  async function doAjaxCreateCandleAnalysis() {
     let result ;

     timestampArray = convertToTimestamp()
     localStorage.setItem('startHistorydate',document.getElementById('start_date3').value )
     localStorage.setItem('endHistorydate',document.getElementById('end_date3').value )
     localStorage.setItem('curpairHistory',document.getElementById('curpairText3').value )
    //  btnCalNumCandle
    //  document.getElementById('btnCalNumCandle').addClass('working')
    $("#btnCalNumCandle").addClass('working')

     dataToSend = {
        "curpair" : document.getElementById('curpairText3').value ,
        "timeframeText" : document.getElementById('timeFrameText').value ,
        "startTimestamp" : timestampArray[0],
        "endTimestamp" : timestampArray[1],
       }
       
       //   'endTimestamp' : timestampArray[1],
     fetch('/CreateAnalysisData999', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dataToSend)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data.message);
            $("#btnCalNumCandle").removeClass('working')
            // alert(data.message)
            //generateTable(data.AnswerObject)
        })
        .catch((error) => {
            console.error('Error:', error);
        });

  }

  async function doAjaxget1CandleAtDay() {
    let result ;

    
    sDate= document.getElementById('start_date_lab').value
    endtimestamp = new Date(sDate).getTime() / 1000;

    dataToSend = {
       "curpair" : document.getElementById('curpairText3').value ,       
       "endTimestamp" : endtimestamp,
    }
      
      //   'endTimestamp' : timestampArray[1],
    fetch('/getCandleDataAtDatetime', {
       method: 'POST',
       headers: {
           'Content-Type': 'application/json'
       },
       body: JSON.stringify(dataToSend)
       })
       .then(response => response.json())
       .then(data => {
           console.log('Success:', data.timefrom_unix);        
           document.getElementById('thisid').innerHTML = data.id
           document.getElementById('PreviousAllCodeString').innerHTML = data.previousAllCodeString
           document.getElementById('AllCodeString').innerHTML = data.AllCodeString
           document.getElementById('PreviousAllCode').innerHTML = data.previousAllCode
           document.getElementById('AllCode').innerHTML = data.AllCode 
           document.getElementById('NumColor').innerHTML = data.sText
           document.getElementById('resultColor').innerHTML = data.resultColor
           document.getElementById('thisSlopeDirection').innerHTML = data.thisSlopeDirection 
           
       })
       .catch((error) => {
           console.error('Error:', error);
       });

    //    console.log('Response',response.timefrom_unix)
    


 }

  async function doAjaxCreateMasterCode() {
    let result ;
    
    //alert('doAjaxCreateMasterCode')
     

    dataToSend = {
       "Mode"  : "CreateMasterCode",
       "curpair" : document.getElementById('curpairText3').value ,       
      }
    
    $("#btnCreateMaster").addClass("working")  
      
      //   'endTimestamp' : timestampArray[1],
    fetch('/CreateMasterCode', {
       method: 'POST',
       headers: {
           'Content-Type': 'application/json'
       },
       body: JSON.stringify(dataToSend)
       })
       .then(response => response.json())
       .then(data => {
           console.log('Success:', data.message);
           alert(data.message)
           //generateTable(data.AnswerObject)
       })
       .catch((error) => {
           console.error('Error:', error);
       });
       $("#btnCreateMaster").removeClass("working")  

 }

  async function doAjaxgetDecisionMakingTable() {
    let result ;

    // timestampArray = convertToTimestamp()
    dataToSend = {
       "curpair" : document.getElementById('curpairText3').value ,       
      }
      
      //   'endTimestamp' : timestampArray[1],
    fetch('/getDecisionTable', {
       method: 'POST',
       headers: {
           'Content-Type': 'application/json'
       },
       body: JSON.stringify(dataToSend)
       })
       .then(response => response.json())
       .then(data => {
           console.log('Success:', data.AnswerObject);
        //    alert(data.AnswerObject)
           document.getElementById('resultShowDecision').innerHTML  = data.AnswerObject ; 
           //generateTable(data.AnswerObject)
       })
       .catch((error) => {
           console.error('Error:', error);
       });
}



 