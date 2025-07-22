<!-- CandleFetchV2.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Candlestick Chart</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js">
	</script>


	<script src="ChartUtil.js"></script>	
	<script src="IndyLib.js"></script>

	<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
	
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        #chart-container {
            width: 100%;
            height: 500px;
            margin-top: 20px;
        }
		#rsi-container {
            width: 100%;
            height: 300px;
            margin-top: 20px;
        }
        .form-container {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select, input, button {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            width: 100%;
            max-width: 300px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .loading {
            margin-top: 10px;
            color: #666;
        }
		.flex { display:flex; }
		.mBtn { display: flex;			
			min-height: 44px;
			border: 1px solid transparent;
			background: #fff;
			box-shadow: 0px 2px 8px 0px rgba(60, 64, 67, 0.25);
			border-radius: 24px;
			margin-right:10px;
			box-sizing: border-box;
			width: auto;
			min-width:80px;
			color:black;
			text-align:center;
			align-items: center;
            justify-content: center;
	 	} 
		.mBtn:hover { color:white; }
    </style>
</head>
<body>
    <h1>Deriv Candlestick Chart</h1>
    
    <div class="form-container">
        <form id="candle-form">
		 Scan Mode<input type="checkbox" id="scanMode" checked style='width:30px'>
		  <div id="" class="bordergray flex" >
		     
		   <button type='button' id='' class='mBtn' onclick="MainScanCandleData()">Start Scan</button>
		   <button type='button' id='' class='mBtn' onclick="AnalysisIndy()">Analysis Indy</button>
			
			<button type='button' id='' class='mBtn' onclick="MaingetCandleData('R_10')">Vol-10</button>
			<button type='button' id='' class='mBtn' onclick="MaingetCandleData('R_25')">Vol-25</button>
		    <button type='button' id='' class='mBtn' onclick="MaingetCandleData('R_50')">Vol-50</button>
			<button type='button' id='' class='mBtn' onclick="MaingetCandleData('R_75')">Vol-75</button>
            <button type='button' id='' class='mBtn' onclick="MaingetCandleData('R_100')">Vol-100</button>


		  </div>
		  <div id="" class="bordergray flex" style='margin-top:10px;padding:10px;align-items:center'>
		       
		  
		   Asset Selected::<input type="text" id="assetSelected" style='width:100px'><br>

		   <label for="birthdaytime">Start Date:</label>: 
		   <input type="datetime-local" id="startTimeSelected" style='width:200px;font-size:18px'>

		   <label for="birthdaytime">Stop Date:</label>: 
		   <input type="datetime-local" id="stopTimeSelected" style='width:200px;font-size:18px'>
		   


		   </div>
		   <div id="analyResult" class="bordergray flex" style='border:1px solid gray;padding:15px;margin:15px;border-radius:8px'>
		       
		  </div>
            <div class="form-group">
                <label for="symbol">สินค้า:</label>
                <select id="symbol" required>
                    <option value="R_100">Volatility 100 Index</option>
                    <option value="R_75">Volatility 75 Index</option>
                    <option value="R_50">Volatility 50 Index</option>
                    <option value="R_25">Volatility 25 Index</option>
                    <option value="R_10">Volatility 10 Index</option>
                    <option value="WLDAUD">AUD Basket</option>
                    <option value="WLDEUR">EUR Basket</option>
                    <option value="WLDGBP">GBP Basket</option>
                    <option value="WLDUSD">USD Basket</option>
                    <option value="frxAUDUSD">AUD/USD</option>
                    <option value="frxEURUSD">EUR/USD</option>
                    <option value="frxGBPUSD">GBP/USD</option>
                    <option value="frxUSDJPY">USD/JPY</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="interval">ช่วงเวลา:</label>
                <select id="interval" required>
                    <option value="60">1 นาที</option>
                    <option value="300">5 นาที</option>
                    <option value="900">15 นาที</option>
                    <option value="1800">30 นาที</option>
                    <option value="3600">1 ชั่วโมง</option>
                    <option value="86400">1 วัน</option>
                </select>
            </div>
            
            <div class="form-group">
                <button id= 'connectBtn' type="submit">Connect WS</button>
				<button type="button" id='requestBtn' onclick='MaingetCandleData()'>Request Data</button>

				<button type='button' id='captureChartButton' class='' >Capture Screen</button>

				<button type='button' id='btnClearMaker' onclick='clearMarkers()' >Clear Marker</button>

            </div>
			<div id="connectStatus" class="loading" style="sdisplay: none;">Status</div>
        </form>
        
        <div id="loading" class="loading" style="display: none;">กำลังโหลดข้อมูล...</div>
        <div id="error" class="error" style="display: none;"></div>
    </div>
    
    <div id="chart-container"></div>
	<div id="rsi-container"></div>
	<textarea id="txtData" style='position:absolute;width:100%;height:200px;margin:20px;left:-200px;display:none' rows="" cols=""></textarea>

	<textarea id="AllIndyText" style='position:absolute;width:100%;height:200px;margin:20px;left:-200px;display:none' rows="" cols=""></textarea>

	<textarea id="AllIndyText2" style='position:absolute;width:100%;height:200px;margin:20px;left:-200px;display:none' rows="" cols=""></textarea>


	
    
    <script>
        // เริ่มต้น WebSocket connection
        let ws = null;
        let chart = null;
		let rsiChart = null;
        let candleSeries = null;
		let ema3Series = null;
		let ema5Series = null;
		let upperBandSeries = null;
		let lowerBandSeries = null;


        
        function initChart() {
            if (chart) {
                document.getElementById('chart-container').innerHTML = '';
            }
			if (rsiChart) {
                document.getElementById('rsi-container').innerHTML = '';
            }
            
            chart = LightweightCharts.createChart(document.getElementById('chart-container'), {
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                timeScale: {
                    timeVisible: true,
                    secondsVisible: false,
                    borderColor: '#D1D4DC',
                },
                rightPriceScale: {
                    borderColor: '#D1D4DC',
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
            });

           const rsiChartContainer = document.getElementById('rsi-container');
		   rsiChart = LightweightCharts.createChart(rsiChartContainer, {
                height: rsiChartContainer.offsetHeight,
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                timeScale: {
                    borderColor: '#d1d1d1',
                    timeVisible: true,
                },
                rightPriceScale: {
                    borderColor: '#d1d1d1',
                    scaleMargins: {
                        top: 0.1,
                        bottom: 0.1,
                    },
                },
           });
			rsiSeries = rsiChart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                priceLineVisible: false,
            });


            
            // สร้าง candlestick series
            candleSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });

            ema3Series = chart.addLineSeries({ color: 'blue', lineWidth: 1, title: 'EMA 3' });

           // สร้าง series สำหรับ EMA 5
            ema5Series = chart.addLineSeries({ color: 'green', lineWidth: 1, title: 'EMA 5' });

			// Add Bollinger Bands
            upperBandSeries = chart.addLineSeries({
            color: 'rgba(255,255,0, 0.5)',              
            lineWidth: 3,
        });

            lowerBandSeries = chart.addLineSeries({
              color: 'rgba(128, 0, 128, 0.5)',
              lineWidth: 3,
            });

           // สร้าง series สำหรับ Bollinger Bands
            //bbSeries = chart.addLineSeries({ color: 'purple', lineWidth: 1, title: 'Bollinger Bands' });
            
            // ปรับขนาดกราฟตามหน้าจอ
            window.addEventListener('resize', () => {
                if (chart) {
                    chart.applyOptions({
                        width: document.getElementById('chart-container').clientWidth
                    });
                }
            });
        }  

		function requestCandle() {
		   // ขอข้อมูล candlestick
           const request = {
                  ticks_history: document.getElementById("symbol").value,
                  count: 60,  // จำนวนแท่งเทียน
                  end: 'latest',
                  style: 'candles',
                  granularity: document.getElementById("interval").value   // ช่วงเวลาเป็นวินาที
            };
                    
             ws.send(JSON.stringify(request));
		
		} // end func

		function requestCandleWithAssetCode(assetCode) {
		   // ขอข้อมูล candlestick
		   //ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
           const request = {
                  ticks_history: assetCode,
                  count: 60,  // จำนวนแท่งเทียน
                  end: 'latest',
                  style: 'candles',
                  granularity: document.getElementById("interval").value   // ช่วงเวลาเป็นวินาที
            };                    
            ws.send(JSON.stringify(request));
		
		} // end func
		
        
        // ฟังก์ชันเชื่อมต่อกับ Deriv API
        function connectWebSocket() {
            if (ws !== null) {
                ws.close();
            }
            
            ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
            
            ws.onopen = function() {
                console.log('WebSocket connection established');
				document.getElementById("connectStatus").innerHTML = 'Connected';
				
            };
            
            ws.onclose = function() {
                console.log('WebSocket connection closed');
				document.getElementById("connectStatus").innerHTML = 'Closed';
				$('#connectBtn').trigger('click');
				//connectWebSocket();
            };
            
            ws.onerror = function(error) {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('error').textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
                document.getElementById('error').style.display = 'block';
                console.error('WebSocket error:', error);
            };
        }
        
        // ฟังก์ชันสำหรับจัดการข้อมูล candlestick
        function handleResponse(response) {
            const errorElement = document.getElementById('error');
            const loadingElement = document.getElementById('loading');
            
            if (response.error) {
                errorElement.textContent = `เกิดข้อผิดพลาด: ${response.error.message}`;
                errorElement.style.display = 'block';
                loadingElement.style.display = 'none';
                return;
            }
            
            if (response.msg_type === 'candles') {
                loadingElement.style.display = 'none';
				console.log(response)
                if (!response.candles || response.candles.length === 0) {
                    errorElement.textContent = 'ไม่พบข้อมูล candlestick';
                    errorElement.style.display = 'block';
                    return;
                } 

				document.getElementById("txtData").value = JSON.stringify(response.candles);
                
                // แปลงข้อมูลให้อยู่ในรูปแบบที่ถูกต้องสำหรับ lightweight-charts
                const candlesData = response.candles.map(candle => ({
                    time: candle.epoch,  // timestamp in seconds
                    open: parseFloat(candle.open),
                    high: parseFloat(candle.high),
                    low: parseFloat(candle.low),
                    close: parseFloat(candle.close)
                }));

                const AllIndy = MainCallAllIndy(candlesData) ;
				document.getElementById("AllIndyText").value = JSON.stringify(AllIndy);
				console.log('allIndy',AllIndy);
				ema3Series.setData(AllIndy.ema3);
                ema5Series.setData(AllIndy.ema5);

				const bollingerBands = AllIndy.bb;
                upperBandSeries.setData(bollingerBands.map(band => ({
                    time: band.time,
                    value: band.upper
                })));
                lowerBandSeries.setData(bollingerBands.map(band => ({
                    time: band.time,
                    value: band.lower
                })));

				//upperBandSeries.setData(AllIndy.ema5);
                //lowerBandSeries.setData(AllIndy.ema5);

				rsiSeries.setData(AllIndy.rsi);
//				rsiSeries.setData(AllIndy.adx.adx);

                let st = '';
				let lastIndex = AllIndy.adx.length-1 ;
				st = 'ADX = ' + AllIndy.adx[lastIndex].adx.toFixed(2) ;
                st += ' ; DI+ : ' + AllIndy.adx[lastIndex].plusDI.toFixed(2) ;; 
				st += ' ; DI- : ' + AllIndy.adx[lastIndex].minusDI.toFixed(2) ;; 

                document.getElementById("analyResult").innerHTML = '';
				document.getElementById("analyResult").innerHTML = st;

                let scanMode = document.getElementById("scanMode").checked;
				if (scanMode) {				
					let assetCode = response.echo_req.ticks_history ;
					lastIndex = candlesData.length-1 ; 
					let lastTime = candlesData[lastIndex].time ;

					let sObj = {
						assetCode : assetCode ,
						rawCandle : candlesData,
						allIndy  : 	AllIndy,
						lastTimeCandle : lastTime
					}
					
					let AllAnalyData = [];
					if (document.getElementById("AllIndyText2").value != '') {				
					 AllIndyText2 = JSON.parse(document.getElementById("AllIndyText2").value) ;
					 for (let j=0;j<=AllIndyText2.length-1 ;j++ ) {
						AllAnalyData.push(AllIndyText2[j]) ;
					 }
					 
					}

					AllAnalyData.push(sObj) ; 
					//alert(AllAnalyData.length) ;
					document.getElementById("AllIndyText2").value = JSON.stringify(AllAnalyData) ;

					console.log('All Data',AllAnalyData) ;

                }

                




                //bbSeries.setData(AllIndy.bb.upper);
                
                // แสดงข้อมูลบนกราฟ
                if (candleSeries) {
                    candleSeries.setData(candlesData);                    
                    // ปรับช่วงเวลาให้แสดงทั้งหมด
                    chart.timeScale().fitContent();                    
                    errorElement.style.display = 'none';
                } 
				// เรียกใช้ฟังก์ชันเพื่อเริ่มตรวจจับการคลิก
                subscribeToChartClicks();
            }
        }
        
        // จัดการ Form submit
        document.getElementById('candle-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const symbol = document.getElementById('symbol').value;
            const interval = parseInt(document.getElementById('interval').value, 10);
            
            // สร้างกราฟใหม่
            initChart();
            
            // แสดงสถานะกำลังโหลด
            document.getElementById('loading').style.display = 'block';
            document.getElementById('error').style.display = 'none';
            
            // สร้างการเชื่อมต่อใหม่
            connectWebSocket();
            
            // จัดการการตอบกลับจาก WebSocket
            ws.onmessage = function(msg) {
                const response = JSON.parse(msg.data);
                handleResponse(response);
            };
            
            // หลังจากเชื่อมต่อแล้ว รอสักครู่แล้วส่งคำขอข้อมูล

            setTimeout(() => {
                if (ws.readyState === WebSocket.OPEN) {
                    // ขอข้อมูล candlestick
                    const request = {
                        ticks_history: symbol,
                        count: 60,  // จำนวนแท่งเทียน
                        end: 'latest',
                        style: 'candles',
                        granularity: interval  // ช่วงเวลาเป็นวินาที
                    };
                    
                    ws.send(JSON.stringify(request));
                } else {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('error').textContent = 'ไม่สามารถเชื่อมต่อกับ Deriv API ได้';
                    document.getElementById('error').style.display = 'block';
                }
            }, 1000);
        });
        
        // เริ่มต้นการทำงาน
        document.addEventListener('DOMContentLoaded', () => {
			//$('#candle-form').trigger('submit');
            initChart();
        });
    </script>

<script>
// JavaScript code to capture TradingView Lightweight Charts and save to server
document.addEventListener('DOMContentLoaded', function() {
  // Assuming you have a button to trigger the capture
  const captureButton = document.getElementById('captureChartButton');
  
  captureButton.addEventListener('click', function() {
    // Find the TradingView chart container element
    const chartContainer = document.querySelector('.tv-lightweight-charts');
    
    if (!chartContainer) {
      console.error('TradingView chart container not found');
      return;
    }
    
    // Use html2canvas to capture the chart as an image
    // Make sure to include the html2canvas library in your project:
    /* 
	*/
    html2canvas(chartContainer).then(canvas => {
      // Convert canvas to base64 image data
      const imageData = canvas.toDataURL('image/png');
      
      // Send the image data to your PHP server
      saveChartImage(imageData);
    }).catch(error => {
      console.error('Error capturing chart:', error);
    });
  });
  
  function saveChartImage(imageData) {
    // Create form data to send to the server
    const formData = new FormData();
    formData.append('image', imageData);
    
    // Optional: Add any additional metadata
    formData.append('timestamp', new Date().toISOString());
    formData.append('chartType', 'tradingview-lightweight');
    
    // Send the data to your PHP endpoint
    fetch('https://thepapers.in/deriv/save-chart.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      console.log('Chart saved successfully:', data);
      // Optional: Display success message to user
      alert('Chart saved successfully!');
    })
    .catch(error => {
      console.error('Error saving chart:', error);
      // Optional: Display error message to user
      alert('Failed to save chart. Please try again.');
    });
  }
});

function MaingetCandleData(assetCode) {


document.getElementById("symbol").value = assetCode ;
requestCandle();

} // end func

function MainScanCandleData() {

document.getElementById("AllIndyText2").value = '';

let assetCode = 'R_10';
requestCandleWithAssetCode(assetCode) ;

assetCode = 'R_25';
requestCandleWithAssetCode(assetCode) ;

assetCode = 'R_50';
requestCandleWithAssetCode(assetCode) ;

assetCode = 'R_75';
requestCandleWithAssetCode(assetCode) ;

assetCode = 'R_100';
requestCandleWithAssetCode(assetCode) ;

//AnalysisIndy()

} // end func



function AnalysisIndy() {
let thisAdx = null;
let adxLength = 0 ;
let jObj = null;

let AllAnalysisData = JSON.parse(document.getElementById("AllIndyText2").value) ;		 
let analyList = []
let stTable = '<table border=1><tr><td>AssetCode</td><td>ADX</td></tr>' ;

for (let i=0;i<=AllAnalysisData.length-1 ;i++ ) {
   adxLength = AllAnalysisData[i].allIndy.adx.length-1;
   thisAdx = AllAnalysisData[i].allIndy.adx[adxLength]['adx'] ;

   sObj = {
     "assetCode"  : AllAnalysisData[i].assetCode ,
     "ADX"        : thisAdx.toFixed(2) ,
   }
   stTable += `<tr><td>${AllAnalysisData[i].assetCode}</td><td>${thisAdx.toFixed(2)}</tr>` ;
   //console.log('adx', thisAdx) ;
   analyList.push(sObj)
}
stTable += '</table>';
document.getElementById("analyResult").innerHTML = stTable ; //JSON.stringify(analyList);


} // end func


$(document).ready(function () {
   $('#connectBtn').trigger('click');
});

</script>

</body>
</html>

