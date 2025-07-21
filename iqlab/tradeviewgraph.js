
        let chart;
        let candlestickSeries;
        let ema3Series;
        let ema5Series;
        let markersSeries;
		let formattedData;

        function handleResize() {
            if (chart) {
                const chartContainer = document.getElementById('chart-container');
                const width = chartContainer.clientWidth;
                const height = chartContainer.clientHeight;
                chart.applyOptions({
                    width: width,
                    height: height
                });
            }
        }

		 // ฟังก์ชันคำนวณ pip
        function calculateDetailedPips(high, low) {
          const pipValue = 0.0001;
          const pipMove = (high - low) / pipValue;
              return parseFloat(pipMove.toFixed(4));
        } 

		// ฟังก์ชันเพิ่ม marker
        function addMarker2(type) {
			alert(formattedData);
            const lastData = formattedData[formattedData.length - 1];
            const marker = {
                time: lastData.time,
                position: type === 'buy' ? 'belowBar' : 'aboveBar',
                color: type === 'buy' ? '#2196F3' : '#e91e63',
                shape: type === 'buy' ? 'arrowUp' : 'arrowDown',
                text: type === 'buy' ? 'Buy' : 'Sell'
            };
            
            const currentMarkers = candlestickSeries.markers() || [];
            candlestickSeries.setMarkers([...currentMarkers, marker]);
        }
		 



        function initChart() {
            const chartContainer = document.getElementById('chart-container');
            const width = chartContainer.clientWidth;
            const height = chartContainer.clientHeight;

            chart = LightweightCharts.createChart(chartContainer, {
                width: width,
                height: height,
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                rightPriceScale: {
                    borderColor: '#dfdfdf',
                },
                localization: {
                  priceFormatter: price => price.toFixed(6),
                },
                timeScale: {
                timeVisible: true,
                secondsVisible: false,
                tickMarkFormatter: (time, tickMarkType, locale) => {
                    const date = new Date(time * 1000); // แปลง timestamp เป็น Date object
                    const hours = date.getHours().toString().padStart(2, '0');
                    const minutes = date.getMinutes().toString().padStart(2, '0');
                    return `${hours}:${minutes}`;
                }
            },
            });

            candlestickSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350'
            });

			// กำหนด tooltip
        chart.subscribeCrosshairMove(param => {
            if (param.time) {
                const data = param.seriesData.get(candlestickSeries);
                if (data) {
                    const pips = calculateDetailedPips(data.high, data.low);
                    const tooltipText = `
                        O: ${data.open.toFixed(6)}
                        H: ${data.high.toFixed(6)}
                        L: ${data.low.toFixed(6)}
                        C: ${data.close.toFixed(6)}
                        Pips: ${pips}
                    `;
                    
                    // แสดง tooltip ใน div
                    const tooltip = document.getElementById('tooltip');
                    if (tooltip) {
                        tooltip.style.display = 'block';
                        tooltip.style.left = `${param.point.x + 15}px`;
                        tooltip.style.top = `${param.point.y}px`;
                        tooltip.innerHTML = tooltipText.replace(/\n/g, '<br>');
                    }
                }
            }
        });

        // สร้าง tooltip div
        const tooltipDiv = document.createElement('div');
        tooltipDiv.id = 'tooltip';
        tooltipDiv.style.position = 'absolute';
        tooltipDiv.style.display = 'none';
        tooltipDiv.style.padding = '8px';
        tooltipDiv.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
        tooltipDiv.style.border = '1px solid #ccc';
        tooltipDiv.style.borderRadius = '4px';
        tooltipDiv.style.fontSize = '12px';
        tooltipDiv.style.whiteSpace = 'pre-line';
        tooltipDiv.style.zIndex = '1000';
        document.body.appendChild(tooltipDiv);

            ema3Series = chart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                title: 'EMA 3'
            });

            ema5Series = chart.addLineSeries({
                color: '#FF6B00',
                lineWidth: 2,
                title: 'EMA 5'
            });

            markersSeries = chart.addLineSeries({
                lastValueVisible: false,
                priceLineVisible: false,
            });
        }

        function calculateEMA(data, period) {
            const k = 2 / (period + 1);
            let ema = data[0].close;
            const emaData = [];
			stEMA  = '';

            data.forEach((item, i) => {
                ema = (item.close * k) + (ema * (1 - k));
                stEMA  += formatDateTime999(item.time) + ' ==> ' + ema.toFixed(7) + ' @@@ ';
                emaData.push({
                    time: item.time,
                    value: ema
                });
            });
			console.log('ema',emaData)
            if (period==3) {
				document.getElementById("ema3").value = stEMA ;
            }
			if (period==5) {
				document.getElementById("ema5").value = stEMA ;
            }
			

            return emaData;
        }

        function findReversalPoints(emaData) {
            const reversals = [];
            for (let i = 1; i < emaData.length - 1; i++) {
                if (emaData[i-1].value < emaData[i].value && emaData[i].value > emaData[i+1].value) {
				    
                    reversals.push({
                        time: emaData[i].time,
                        position: 'high',
                        value: emaData[i].value
                    });
                }
                else if (emaData[i-1].value > emaData[i].value && emaData[i].value < emaData[i+1].value) {
                    reversals.push({
                        time: emaData[i].time,
                        position: 'low',
                        value: emaData[i].value
                    });
                }
            }
            return reversals;
        }

        function findCrossovers(ema3Data, ema5Data) {
            const crossovers = [];
            for (let i = 1; i < ema3Data.length; i++) {
                if (ema3Data[i-1].value <= ema5Data[i-1].value && ema3Data[i].value > ema5Data[i].value) {
                    crossovers.push({
                        time: ema3Data[i].time,
                        type: 'golden',
                        value: ema3Data[i].value
                    });
                }
                else if (ema3Data[i-1].value >= ema5Data[i-1].value && ema3Data[i].value < ema5Data[i].value) {
                    crossovers.push({
                        time: ema3Data[i].time,
                        type: 'death',
                        value: ema3Data[i].value
                    });
                }
            }
            return crossovers;
        }

        function formatTimestamp(timestamp) {
/*
			// สร้างวัตถุ Date จาก timestamp (ถ้า timestamp เป็นมิลลิวินาที ต้องหารด้วย 1000)
            const date = new Date(timestamp);
			const options = { hour: 'numeric', minute: 'numeric', hour12: false };
			const formattedTime = date.toLocaleTimeString('th-TH', options); 
			// 'th-TH' สำหรับภาษาไทย
            //return formattedTime;
           return formattedDateTime ;
*/

            return new Date(timestamp * 1000).toLocaleDateString();
        } 
		function formatDateTime999(timestamp) {
			const date = new Date(timestamp * 1000);
			const year = date.getFullYear();
			const month = String(date.getMonth() + 1).padStart(2, '0'); // เดือนเริ่มจาก 0
			const day = String(date.getDate()).padStart(2, '0');
			const hours = String(date.getHours()).padStart(2, '0');
			const minutes = String(date.getMinutes()).padStart(2, '0');
			const seconds = String(date.getSeconds()).padStart(2, '0');

			return `${hours}:${minutes}`;

            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

        function displaySignals(ema3Reversals, ema5Reversals, crossovers) {
            const signalList = document.getElementById('signal-list');
            let html = '<h4>EMA3 Reversals การกลับตัว : ' +  ema3Reversals.length + ' ครั้ง </h4><ul>';
            
            ema3Reversals.forEach(rev => {
			    //console.log('Rev.Time= ', rev.time)
				
                html += `<li>${formatDateTime999(rev.time)} - ${rev.position === 'high' ? 'Peak(TurnDown)' : 'Valley(TurnUp)'} at ${rev.value.toFixed(6)}</li>`;
            });
            
            html += '</ul><h4>EMA5 Reversals: ' +  ema5Reversals.length + ' ครั้ง </h4><ul>';
            
            ema5Reversals.forEach(rev => {
                html += `<li>${formatDateTime999(rev.time)} - ${rev.position === 'high' ? 'Peak(TurnDown)' : 'Valley(TurnUp)'} at ${rev.value.toFixed(6)}</li>`;
            });
            
            html += '</ul><h4>EMA Crossovers: จำนวน ' + crossovers.length + ' จุด</h4><ul>';
            
            crossovers.forEach(cross => {
                html += `<li>${formatDateTime999(cross.time)} - ${cross.type === 'golden' ? 'Golden Cross' : 'Death Cross'} at ${cross.value.toFixed(6)}</li>`;
            });
            
            html += '</ul>';
            signalList.innerHTML = html;
        }

		function formatDateTime(date) {
            return new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
                .toISOString()
                .slice(0, 16);
        }
		// Function to save dates to localStorage
        function saveDates() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            localStorage.setItem('savedStartDate', startDate);
            localStorage.setItem('savedEndDate', endDate);
            
            console.log('Dates saved:', {
                startDate: startDate,
                endDate: endDate
            });
        }

        // Function to load dates from localStorage
        function loadSavedDates() {
            const savedStartDate = localStorage.getItem('savedStartDate');
            const savedEndDate = localStorage.getItem('savedEndDate');
            
            if (savedStartDate) {
                document.getElementById('startDate').value = savedStartDate;
            }
            if (savedEndDate) {
                document.getElementById('endDate').value = savedEndDate;
            }
        }

        async function loadData() {
			SaveDateToLocal();
            if (!chart) {
                initChart();
            }

            const symbol = document.getElementById('symbol').value;
            const timeframe = document.getElementById('timeframe').value;
			/*
            const startDate = Math.floor(new Date(document.getElementById('startDate').value).getTime() / 1000);
            const endDate = Math.floor(new Date(document.getElementById('endDate').value || new Date()).getTime() / 1000);
			*/

			startDate =document.getElementById("dateInput").value + '%20';
			startDate += document.getElementById("hourSelect").value + '%3A';
			startDate += document.getElementById("minuteSelect").value + '%3A'+"00" ;

			endDate =  document.getElementById("dateInput2").value + '%20' ;
			endDate += document.getElementById("hourSelect2").value + '%3A';
			endDate += document.getElementById("minuteSelect2").value + '%3A'+"00" ;

				;
			//alert(startDate);

            try {

				urlIQoption = 'https://lovetoshopmall.com/api/getcandle/?timefromUnix='+startDate +'&endtimefromUnix='+endDate + '&timeframe=' + timeframe ;
				console.log(urlIQoption)				
				const response = await fetch(urlIQoption);
				console.log('url=',urlIQoption);

                const rawData = await response.json();
				//alert(rawData.length)
			    if (rawData.length==0)    {
					alert('ไม่มีข้อมูล ');
					return;
			    }
				 // แปลงข้อมูลให้อยู่ในรูปแบบที่ Lightweight Charts ต้องการ
                formattedData = rawData.map(item => ({
					time: item.timestamp, // หรือใช้ item.from ก็ได้
					open: parseFloat(item.open),
					high: parseFloat(item.high),
					low: parseFloat(item.low),
					close: parseFloat(item.close)
               }));


/*
                if (!data.chart || !data.chart.result || !data.chart.result[0]) {
                    alert('No data available for this symbol');
                    return;
                }

                const quotes = data.chart.result[0];								
                const timestamps = quotes.timestamp;
                const ohlc = quotes.indicators.quote[0];

				const ohlc = data;
			    console.log(ohlc)

                i = 0 ;
                const chartData = data.map((timestamp, i) => ({
                    time: timestamp,					
                    open: ohlc.open[i],
                    high: ohlc.high[i],
                    low: ohlc.low[i],
                    close: ohlc.close[i]
                })).filter(d => d.open && d.high && d.low && d.close);
				*/
						// ตัวอย่างข้อมูล markers
				const markers2 = [
					{
						time: formattedData[0].time,
						position: 'belowBar',
						color: '#2196F3',
						shape: 'arrowUp',
						text: 'Buy'
					},
					{
						time: formattedData[0].time,
						position: 'aboveBar',
						color: '#e91e63',
						shape: 'arrowDown',
						text: 'Sell'
					}
				];

                
                
                chartData = formattedData;
                candlestickSeries.setData(chartData);
				chart.timeScale().fitContent();
	

  


                const ema3Data = calculateEMA(chartData, 3);
                const ema5Data = calculateEMA(chartData, 5);

                ema3Series.setData(ema3Data);
                ema5Series.setData(ema5Data);

                const ema3Reversals = findReversalPoints(ema3Data);
                const ema5Reversals = findReversalPoints(ema5Data);
                const crossovers = findCrossovers(ema3Data, ema5Data);

                const markers = [
                    ...ema3Reversals.map(rev => ({
                        time: rev.time,
                        position: rev.position === 'high' ? 'aboveBar' : 'belowBar',
                        color: '#2962FF',
                        shape: 'circle',
                        text: 'EMA3 ' + (rev.position === 'high' ? '▼' : '▲')
                    })),
                    ...ema5Reversals.map(rev => ({
                        time: rev.time,
                        position: rev.position === 'high' ? 'aboveBar' : 'belowBar',
                        color: '#FF6B00',
                        shape: 'circle',
                        text: 'EMA5 ' + (rev.position === 'high' ? '▼' : '▲')
                    })),
                    ...crossovers.map(cross => ({
                        time: cross.time,
                        position: cross.type === 'golden' ? 'belowBar' : 'aboveBar',
                        color: cross.type === 'golden' ? '#26a69a' : '#ef5350',
                        shape: 'square',
                        text: cross.type === 'golden' ? '✚' : '✖'
                    }))
                ];

                markersSeries.setMarkers(markers);
                displaySignals(ema3Reversals, ema5Reversals, crossovers);

		 
				
                

                chart.timeScale().fitContent();

            } catch (error) {
                console.error('Error loading data:', error);
                alert('Error loading data. Please check the symbol and try again.');
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('resize', handleResize);
            //document.getElementById('endDate').valueAsDate = new Date();
			 const now = new Date();
             document.getElementById('startDate').value = formatDateTime(now);
             document.getElementById('endDate').value = formatDateTime(now);
			 // Add event listeners to save on change
             document.getElementById('startDate').addEventListener('change', saveDates);
             document.getElementById('endDate').addEventListener('change', saveDates);

			 document.getElementById('buyButton').addEventListener('click', () => addMarker2('buy'));
             document.getElementById('sellButton').addEventListener('click', () => addMarker2('sell'));


        // Load saved dates when page loads
        loadSavedDates();
             document.getElementById('loadButton').addEventListener('click', loadData);
        });



