class TViewChart {
    constructor(chartID,containerId, textareaId, inputId_Horiz, buttonId) {
        this.chartID = chartID;
        this.containerId = containerId;
        this.textareaId = textareaId;
        this.inputId_Horiz = inputId_Horiz;
        this.buttonId = buttonId;
        this.chart = null;
        this.candleSeries = null;
        this.ema3Series = null;
        this.ema5Series = null;
        this.bbSeries = null;
        this.horizontalLines = null;
        this.data = [];
		// เก็บ references ของทุก price lines
        this.priceLines = [];
        // เก็บ reference ของเส้น horizontal ที่สร้างไว้




        this.init();
    }

    init() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Element with id "${this.containerId}" not found.`);
            return;
        }

        // สร้างกราฟ
        this.chart = LightweightCharts.createChart(container, {
            width: 1200,
            height: 500,
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
            },
        });

        // สร้าง series สำหรับ Candlestick
        this.candleSeries = this.chart.addCandlestickSeries();

        // สร้าง series สำหรับ EMA 3
        this.ema3Series = this.chart.addLineSeries({ color: 'blue', lineWidth: 1, title: 'EMA 3' });

        // สร้าง series สำหรับ EMA 5
        this.ema5Series = this.chart.addLineSeries({ color: 'green', lineWidth: 1, title: 'EMA 5' });

        // สร้าง series สำหรับ Bollinger Bands
        this.bbSeries = this.chart.addLineSeries({ color: 'purple', lineWidth: 1, title: 'Bollinger Bands' });

        // ตั้งค่า event listener สำหรับ textarea
        const textarea = document.getElementById(this.textareaId);
        if (textarea) {
            textarea.addEventListener('change', () => this.updateChartFromTextarea());
        }

        // ตั้งค่า event listener สำหรับปุ่มวาดเส้นแนวนอน
        const drawLineButton = document.getElementById(this.buttonId);
        if (drawLineButton) {

            drawLineButton.addEventListener('click', () => this.addHorizontalPriceLine());
        }

        // ตั้งค่า event listener สำหรับดับเบิ้ลคลิกบนกราฟ
        this.chart.subscribeClick((param) => {
            if (param.point) {
                const price = param.seriesPrices.get(this.candleSeries).close;
                const input = document.getElementById(this.inputId_Horiz);
                if (input) {
                    input.value = price.toFixed(2);
                }
            }
        });
    }

	 
	

	convertData(candleData) {
    let dataTmp = null ;

          console.log(candleData.msg_type)

		  if (candleData.msg_type === 'ohlc') {
			  console.log('Case  OHLC')
			  dataTmp = [{
                    time: candleData.epoch,
                    open: candleData.open,
                    high: candleData.high,
                    low: candleData.low,
                    close: candleData.close
                }];
		  }

		  if (candleData.msg_type === 'candles') {
		      console.log('Case Candles')

			  dataTmp = candleData.candles.map(candle => ({
                    time: candle.epoch,
                    open: candle.open,
                    high: candle.high,
                    low: candle.low,
                    close: candle.close
                }));
		  }

		  return dataTmp ;


	}

    updateChartFromTextarea() {
	    //console.log('Update From TextArea no ', this.textareaId) ;
        const textarea = document.getElementById(this.textareaId);
        if (textarea) {
            try {

                const dataTmp = JSON.parse(textarea.value);
				if (this.chartID === 1) {
					this.data = [];

				}
				//console.log('dataTmp=',dataTmp)
				//console.log('Data After Convert =',dataTmp,' Type is ',typeof dataTmp )

                if (Array.isArray(dataTmp)) {
					//this.data = dataTmp;
					this.data = this.data.concat(dataTmp);
				    //console.log('Length=',this.data.length)

                    this.updateChart();
					//console.log('After Update Chart Length=',this.data.length)
                } else {
                    console.error('Invalid data format in textarea');
                }
            } catch (error) {
                console.error('Error parsing JSON from textarea:', error);
            }
        }
    }


    updateChart() {

        if (!this.data || this.data.length === 0) {
            console.error('Data is null or empty');
            return;
        }

        // อัปเดต Candlestick series
        this.candleSeries.setData(this.data);

        // คำนวณและอัปเดต EMA 3
        const ema3 = this.calculateEMA(this.data, 3);
        this.ema3Series.setData(ema3);

        // คำนวณและอัปเดต EMA 5
        const ema5 = this.calculateEMA(this.data, 5);
        this.ema5Series.setData(ema5);

        // คำนวณและอัปเดต Bollinger Bands
        const bb = this.calculateBollingerBands(this.data, 5, 2);
        this.bbSeries.setData(bb.upper);
    }

    calculateEMA(data, period) {
        const k = 2 / (period + 1);
        let ema = data[0].close;
        const emaData = [];

        data.forEach((candle) => {
            ema = (candle.close * k) + (ema * (1 - k));
            emaData.push({
                time: candle.time,
                value: ema,
            });
        });

        return emaData;
    }

    calculateBollingerBands(data, period, multiplier) {
        const bbData = [];
        for (let i = period - 1; i < data.length; i++) {
            const slice = data.slice(i - period + 1, i + 1);
            const mean = slice.reduce((sum, candle) => sum + candle.close, 0) / period;
            const variance = slice.reduce((sum, candle) => sum + Math.pow(candle.close - mean, 2), 0) / period;
            const stdDev = Math.sqrt(variance);
            bbData.push({
                time: data[i].time,
                value: mean + multiplier * 1 //stdDev, // Upper band
            });
        }
        return {
            upper: bbData,
            lower: bbData.map((band) => ({
                time: band.time,
                value: band.value - 2 * multiplier * 1  // Lower band
            })),
        };
    }

    drawHorizontalLine() {
        const input = document.getElementById(this.inputId_Horiz);


        if (input && input.value) {
            const price = parseFloat(input.value);
            if (!isNaN(price)) {

				if (this.horizontalLines !== null) {
					//alert('Remove');
                     this.candleSeries.removePriceLine(this.horizontalLines);
					 console.log('Remove')


                }
                this.horizontalLines = {
                    price: price,
                    color: '#ff0000',
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Dashed,
                    axisLabelVisible: true,
                    title: 'Horizontal Line',
                };

				const newLine = {
                    price: price,
                    color: '#ff0000',
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Solid,
                    axisLabelVisible: true,
                    title: `Price ${price}`
                };
                this.candleSeries.createPriceLine(this.horizontalLines);
                //this.horizontalLines.push(horizontalLine);
            }
        }
    }

	removeAllLines() {
            // ลบทุกเส้นที่มีอยู่
            if (this.priceLines.length > 0) {
                this.priceLines.forEach(priceLine => {
                    if (priceLine && typeof priceLine === 'object') {
                        this.candleSeries.removePriceLine(priceLine);
                    }
                });
                // ล้าง array
                this.priceLines = [];
            }
    }

	addHorizontalPriceLine() {
            const priceInput = document.getElementById('chart1PriceLine');
            const price = parseFloat(priceInput.value);

			this.removeAllLines();

			let priceLineColor = '#ff0000';
			let thisAction = document.getElementById("showAction2").innerHTML;

			thisAction = thisAction.trim();

            if (thisAction === 'CALL') {
			   //console.log('Case 1 thisAction = ',thisAction) ;
               priceLineColor = '#008080';
            } else {
			  //console.log('Case 2 thisAction = ',thisAction) ;
               priceLineColor = '#ff0000';
			}


            if (!isNaN(price)) {
                // สร้างเส้นใหม่และเก็บ reference
                const newLine = {
                    price: price,
                    color: priceLineColor,
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Solid,
                    axisLabelVisible: true,
                    title: `Price ${price}`
                };

                // เพิ่มเส้นใหม่และเก็บ reference
                const priceLine = this.candleSeries.createPriceLine(newLine);
                this.priceLines.push(priceLine);

                //priceInput.value = '';
            } else {
                //alert('Please enter a valid number');
            }

        }



}



class CandlestickChartSub {
    constructor(containerId, textareaId, inputId_Horiz, buttonId) {
        this.containerId = containerId;
        this.textareaId = textareaId;
        this.inputId_Horiz = inputId_Horiz;
        this.buttonId = buttonId;
        this.chart = null;
        this.candleSeries = null;
        this.ema3Series = null;
        this.ema5Series = null;
        this.bbSeries = null;
        this.horizontalLines = [];
        this.data = [];
		this.priceLines = [];

        this.init();
    }

    init() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Element with id "${this.containerId}" not found.`);
            return;
        }

        // สร้างกราฟ
        this.chart = LightweightCharts.createChart(container, {
            width: 800,
            height: 500,
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
            },
        });

        // สร้าง series สำหรับ Candlestick
        this.candleSeries = this.chart.addCandlestickSeries();

        // สร้าง series สำหรับ EMA 3
        this.ema3Series = this.chart.addLineSeries({ color: 'blue', lineWidth: 1, title: 'EMA 3' });

        // สร้าง series สำหรับ EMA 5
        this.ema5Series = this.chart.addLineSeries({ color: 'green', lineWidth: 1, title: 'EMA 5' });

        // สร้าง series สำหรับ Bollinger Bands
        this.bbSeries = this.chart.addLineSeries({ color: 'purple', lineWidth: 1, title: 'Bollinger Bands' });

        // ตั้งค่า event listener สำหรับ textarea
        const textarea = document.getElementById(this.textareaId);
        if (textarea) {
            textarea.addEventListener('change', () => this.updateChartFromTextarea());
        }

        // ตั้งค่า event listener สำหรับปุ่มวาดเส้นแนวนอน
        const drawLineButton = document.getElementById(this.buttonId);
        if (drawLineButton) {
            drawLineButton.addEventListener('click', () => this.drawHorizontalLine());
        }
/*
        // ตั้งค่า event listener สำหรับดับเบิ้ลคลิกบนกราฟ
        this.chart.subscribeClick((param) => {
            if (param.point) {
                const price = param.seriesPrices.get(this.candleSeries).close;
                const input = document.getElementById(this.inputId_Horiz);
                if (input) {
                    input.value = price.toFixed(2);
                }
            }
        });
*/
    }

    updateChartFromTextarea() {
        const textarea = document.getElementById(this.textareaId);
        if (textarea) {
            try {
                const data = JSON.parse(textarea.value);
                if (Array.isArray(data)) {
                    this.data = data;
                    this.updateChart();
                } else {
                    console.error('Invalid data format in textarea');
                }
            } catch (error) {
                console.error('Error parsing JSON from textarea:', error);
            }
        }
    }

    updateChart() {
        if (!this.data || this.data.length === 0) {
            console.error('Data is null or empty');
            return;
        }

        // อัปเดต Candlestick series
        this.candleSeries.setData(this.data);

        // คำนวณและอัปเดต EMA 3
        const ema3 = this.calculateEMA(this.data, 3);
        this.ema3Series.setData(ema3);

        // คำนวณและอัปเดต EMA 5
        const ema5 = this.calculateEMA(this.data, 5);
        this.ema5Series.setData(ema5);

        // คำนวณและอัปเดต Bollinger Bands
        const bb = this.calculateBollingerBands(this.data, 5, 2);
        this.bbSeries.setData(bb.upper);
    }

    calculateEMA(data, period) {
        const k = 2 / (period + 1);
        let ema = data[0].close;
        const emaData = [];

        data.forEach((candle) => {
            ema = (candle.close * k) + (ema * (1 - k));
            emaData.push({
                time: candle.time,
                value: ema,
            });
        });

        return emaData;
    }

    calculateBollingerBands(data, period, multiplier) {
        const bbData = [];
        for (let i = period - 1; i < data.length; i++) {
            const slice = data.slice(i - period + 1, i + 1);
            const mean = slice.reduce((sum, candle) => sum + candle.close, 0) / period;
            const variance = slice.reduce((sum, candle) => sum + Math.pow(candle.close - mean, 2), 0) / period;
            const stdDev = Math.sqrt(variance);
            bbData.push({
                time: data[i].time,
                value: mean + multiplier * 1 //stdDev, // Upper band
            });
        }
        return {
            upper: bbData,
            lower: bbData.map((band) => ({
                time: band.time,
                value: band.value - 2 * multiplier * 1 //stdDev, // Lower band
            })),
        };
    }

    drawHorizontalLine2() {
        const input = document.getElementById(this.inputId_Horiz);
        if (input && input.value) {
            const price = parseFloat(input.value);
            if (!isNaN(price)) {
                const horizontalLine = {
                    price: price,
                    color: '#ff0000',
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Dashed,
                    axisLabelVisible: true,
                    title: 'Horizontal Line',
                };
                this.candleSeries.createPriceLine(horizontalLine);
                this.horizontalLines.push(horizontalLine);
            }
        }
    }

	removeAllLines() {
            // ลบทุกเส้นที่มีอยู่
            if (this.priceLines.length > 0) {
                this.priceLines.forEach(priceLine => {
                    if (priceLine && typeof priceLine === 'object') {
                        this.candleSeries.removePriceLine(priceLine);
                    }
                });
                // ล้าง array
                this.priceLines = [];
            }
    }

	addHorizontalPriceLine() {
            const priceInput = document.getElementById('chart1PriceLine');
            const price = parseFloat(priceInput.value);

			this.removeAllLines();


            if (!isNaN(price)) {
                // สร้างเส้นใหม่และเก็บ reference
                const newLine = {
                    price: price,
                    color: '#ff0000',
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Solid,
                    axisLabelVisible: true,
                    title: `Price ${price}`
                };

                // เพิ่มเส้นใหม่และเก็บ reference
                const priceLine = this.candleSeries.createPriceLine(newLine);
                this.priceLines.push(priceLine);

                //priceInput.value = '';
            } else {
                //alert('Please enter a valid number (Price Line)');
            }

      }





} // end class sub



export { TViewChart };