class CandleStickChartWithEMA {
            constructor(containerId) {
                this.container = document.getElementById(containerId);
                this.chart = LightweightCharts.createChart(this.container, {
                    //width:  this.container.clientWidth,
					width:  1000,
                    height: this.container.clientHeight,
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
                        secondsVisible: true,
						tickMarkFormatter: (time) => {
							const date = new Date(time * 1000);
							const hours = date.getHours().toString().padStart(2, '0');
							const minutes = date.getMinutes().toString().padStart(2, '0');
							const seconds = date.getSeconds().toString().padStart(2, '0');
							return `${hours}:${minutes}:${seconds}`;
						}
                    },
                });

                this.candleSeries = this.chart.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderDownColor: '#ef5350',
                    borderUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                    wickUpColor: '#26a69a',
                });

                this.ema3Series = this.chart.addLineSeries({
                    color: '#2962FF',
                    lineWidth: 2,
                });

                this.ema5Series = this.chart.addLineSeries({
                    color: '#FF6D00',
                    lineWidth: 2,
                });
                this.marker = [] ;

                window.addEventListener('resize', () => {
                    this.chart.applyOptions({
                        width: this.container.clientWidth,
                        height: this.container.clientHeight,
                    });
                });
            }

            calculateEMA(data, period, priceKey = 'close') {
                if (data.length === 0) return [];

                const k = 2 / (period + 1);
                const emaData = [];

                // Calculate SMA for first point
                let sum = 0;
                for (let i = 0; i < Math.min(period, data.length); i++) {
                    sum += data[i][priceKey];
                }
                let ema = sum / Math.min(period, data.length);
                emaData.push({ time: data[period - 1].time, value: ema });

                // Calculate EMA for subsequent points
                for (let i = period; i < data.length; i++) {
                    ema = (data[i][priceKey] - ema) * k + ema;
                    emaData.push({ time: data[i].time, value: ema });
                }

                return emaData;
            }

            updateChart(candleData) {
                if (!Array.isArray(candleData) || candleData.length === 0) {
                    console.error('Invalid candle data');
                    return;
                }

                // Sort data by time (just in case it's not sorted)
                candleData.sort((a, b) => a.time - b.time);

                // Calculate EMAs
                //const ema3Data = this.calculateEMA(candleData, 3);
                //const ema5Data = this.calculateEMA(candleData, 5);


				//console.log('last Second',candleData[candleData.length-1].time );
				let lastTimestamp = candleData[candleData.length-1].time ;
				const date = new Date(lastTimestamp * 1000); // คูณด้วย 1000 เพราะ JavaScript ใช้มิลลิวินาที
                const seconds = date.getSeconds();
				if (seconds ===0  ) {
				  this.AddMarkers(lastTimestamp,'0') ;
				  document.getElementById("priceInput").value = candleData[candleData.length-1].close;
				  this.addPriceLine();

				}
                if (seconds ===30  ) {
				  this.AddMarkers(lastTimestamp,'30') ;
				  document.getElementById("priceInput").value = candleData[candleData.length-1].close;
				}





                // Update series
                this.candleSeries.setData(candleData);
               // this.ema3Series.setData(ema3Data);
               // this.ema5Series.setData(ema5Data);

                // Adjust time scale to fit all data
               // this.chart.timeScale().fitContent();
            }

            parseCandleData(textData) {
                try {
                    const data = JSON.parse(textData);
                    if (!Array.isArray(data)) {
                        throw new Error('Data should be an array');
                    }

                    // Convert time to timestamp if it's a string
                    return data.map(item => {
                        const time = typeof item.time === 'string'
                            ? new Date(item.time).getTime() / 1000
                            : item.time;

                        return {
                            time,
                            open: parseFloat(item.open),
                            high: parseFloat(item.high),
                            low: parseFloat(item.low),
                            close: parseFloat(item.close),
                        };
                    });
                } catch (error) {
                    console.error('Error parsing candle data:', error);
                    return null;
                }
            }

			AddMarkers(sTime,caption) {
              console.log(sTime)

			 // Define the marker to add
			 const markerTmp = {
               time: sTime, // The time point where the marker should appear
               position: 'aboveBar', // 'aboveBar' or 'belowBar'
               color: 'blue',
               shape: 'circle', // 'circle', 'square', 'arrowUp', 'arrowDown'
               size: 2, // Optional: size of the marker
               text: caption, // Optional: text to display next to the marker
             };
			 console.log(markerTmp) ;
             this.marker.push(markerTmp) ;

             // Add the marker(s) to the candlestick series
             //this.candleSeries.setMarkers(marker);
			 //this.candleSeries.setMarkers([marker]);
			 this.candleSeries.setMarkers(this.marker);
			}

            addPriceLine() {
			  try {
				// ดึงค่าราคาจาก textbox
				const priceValue = parseFloat(document.getElementById('priceInput').value);

				// ตรวจสอบว่าค่าที่ได้เป็นตัวเลขที่ถูกต้อง
				if (isNaN(priceValue)) {
				  alert('กรุณาใส่ตัวเลขที่ถูกต้อง');
				  return;
				}

				// สมมติว่า chart เป็นตัวแปรที่อ้างถึงอินสแตนซ์ของ CandleStickChartWithEMA
				if (typeof chart !== 'undefined') {
				  // ลบ price line เดิม ถ้ามี
				  if (chart.currentPriceLine) {
					//chart.currentPriceLine.remove();
					 chart.candleSeries.removePriceLine(chart.currentPriceLine);
				  }

				  // ตรวจสอบว่าอินสแตนซ์ของ chart มี series ที่สามารถเรียกใช้ createPriceLine ได้
				  // ต้องดูว่า candlestickSeries อยู่ในตำแหน่งไหนใน object chart
				  // อาจเป็น chart.series หรือ chart.candleSeries หรืออื่นๆ
				  let series = null;

				  if (chart.series) {
					series = chart.series;
				  } else if (chart.candleSeries) {
					series = chart.candleSeries;
				  } else if (chart.mainSeries) {
					series = chart.mainSeries;
				  } else if (chart.chart && chart.chart.candlestickSeries) {
					// บางทีอาจมีการซ้อนกันเป็น chart.chart.candlestickSeries
					series = chart.chart.candlestickSeries;
				  }

				  if (series && typeof series.createPriceLine === 'function') {
					// สร้าง price line ใหม่
					chart.currentPriceLine = series.createPriceLine({
					  price: priceValue,
					  color: '#FF0000',
					  lineWidth: 2,
					  lineStyle: 0, // LineStyle.Solid ใน LightweightCharts
					  axisLabelVisible: true,
					  title: 'Price Line: ' + priceValue
					});

					console.log('เพิ่ม price line ที่ราคา:', priceValue);
				  } else {
					console.error('ไม่พบ series ที่เรียกใช้ createPriceLine ได้');
					alert('ไม่สามารถเพิ่ม price line ได้ เนื่องจากไม่พบ series ที่เหมาะสม');
				  }
				} else {
				  console.error('ไม่พบตัวแปร chart');
				  alert('ไม่สามารถเพิ่ม price line ได้ เนื่องจากไม่พบตัวแปร chart');
				}
			  } catch (error) {
				console.error('เกิดข้อผิดพลาดในการเพิ่ม price line:', error);
				alert('เกิดข้อผิดพลาดในการเพิ่ม price line');
			  }
			}


}// end class