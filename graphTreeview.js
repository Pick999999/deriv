/*
งานวาดกราฟ ไม่มีอะไรมาก 1.InitChart 2.กำหนดข้อมูลให้แต่ละ Series 3.Updatechart
*/

async function initChart() {

   //const chartContainer = document.getElementById('chartContainer');
   getChartContainer() ;

   //const chartContainer = getChartContainer() ;
   const chartContainer = document.getElementById('chartContainer');
   if (typeof LightweightCharts === 'undefined') {
      console.error('LightweightCharts library not loaded');
      document.getElementById('status').textContent = 'Error: Chart library not loaded';
      return;
   }

   chart = LightweightCharts.createChart(chartContainer, {
      width: 1000,
      height: 400,
      layout: {
         background: {
            color: '#ffffff'
         },
         textColor: '#333',
      },
      grid: {
         vertLines: {
            color: '#f0f0f0'
         },
         horzLines: {
            color: '#f0f0f0'
         },
      },
      timeScale: {
         timeVisible: true,
         secondsVisible: false,
         tickMarkFormatter: (time) => {
            let hours = parseInt(time);
            hours = timestampToHHMM(time);
            return `${hours}`;
         }
      },
      crosshair: {
         mode: LightweightCharts.CrosshairMode.Normal,
      },
      localization: {
         locale: 'th-TH',
         priceFormatter: price => price.toFixed(2), // กำหนดทศนิยม 2 ตำแหน่ง
         timeFormatter: time => {
            return new Date(time * 1000).toLocaleString('th-TH');
         },
      },

   });

   candleSeries = chart.addCandlestickSeries({
      upColor: '#26a69a',
      downColor: '#ef5350',
      borderVisible: false,
      wickUpColor: '#26a69a',
      wickDownColor: '#ef5350'
   });

   ema3Series = chart.addLineSeries({
      color: '#2962FF',
      lineWidth: 2,
      title: 'EMA 3'
   });

   ema5Series = chart.addLineSeries({
      color: '#FF6B6B',
      lineWidth: 2,
      title: 'EMA 5'
   });
   // Create Bollinger Bands series
   bbUpperSeries = chart.addLineSeries({
                color: 'rgba(114, 73, 203, 0.8)',
                lineWidth: 3,
                title: 'BB Upper',
                lineStyle: LightweightCharts.LineStyle.Dashed,
   });
   bbLowerSeries = chart.addLineSeries({
       color: 'rgba(114, 73, 203, 0.8)',
       lineWidth: 3,
                title: 'BB Lower',
                lineStyle: LightweightCharts.LineStyle.Dashed,
   });

   window.addEventListener('resize', () => {
      chart.applyOptions({
         width: chartContainer.clientWidth,
         height: chartContainer.clientHeight
      });
   });
}

function AddMarkers() {


} // end func
function ClearMarkers() {


} // end func

function AddPriceLine() {


} // end func
function ClearPriceLine() {


} // end func




function updateChart99(Data) {

	     candleSeries.setData(Data.candleData) ;
		 ema3Series.setData(Data.ema3) ;
		 ema5Series.setData(Data.ema5) ;

		 console.log('BB',Data.Bollinger.upperBand);
//Bollinger

		 bbUpperSeries.setData(Data.Bollinger.upperBand);
         bbLowerSeries.setData(Data.Bollinger.lowerBand);

		 console.log("Cabdle Data After Update Chart",Data);



/*
            try {
                if (!candleSeries[symbol]) {
                    console.error(`Candle series not found for ${symbol}`);
                    return;
                }

                // Update candlestick series
                candleSeries[symbol].setData(chartData[symbol].candles);

                // Update EMA series
                if (chartData[symbol].ema3.length > 0 && ema3Series[symbol]) {
                    ema3Series[symbol].setData(chartData[symbol].ema3);
                }

                if (chartData[symbol].ema5.length > 0 && ema5Series[symbol]) {
                    ema5Series[symbol].setData(chartData[symbol].ema5);
                }

                // Update RSI series
                if (chartData[symbol].rsi.length > 0 && rsiSeries[symbol]) {
                    rsiSeries[symbol].setData(chartData[symbol].rsi);
                }

                // Fit the content
                if (priceCharts[symbol]) {
                    priceCharts[symbol].timeScale().fitContent();
                }
                if (rsiCharts[symbol]) {
                    rsiCharts[symbol].timeScale().fitContent();
                }
            } catch (error) {
                console.error(`Error updating chart for ${symbol}:`, error);
            }
*/
}

function getChartContainer() {

	const chartContainer = document.getElementById('chartContainer');
	if (!chartContainer) {
	  const newChartContainer = document.createElement('div');
	  newChartContainer.id = 'chartContainer';
	  newChartContainer.style.width = '100%';
	  newChartContainer.style.height = '300px';
	  newChartContainer.style.border = '2px solid blue';

	  // เพิ่ม div ที่สร้างใหม่เข้าไปใน body หรือส่วนอื่น ๆ ของ DOM ที่ต้องการ
	  document.body.appendChild(newChartContainer);

	  console.log("สร้าง div 'chartContainer' เรียบร้อยแล้ว");
	} else {
	  console.log("พบ div 'chartContainer' แล้ว");
	}

	const chartData = document.getElementById('txtchartData');
	if (!chartData) {
	  const newChartData = document.createElement('textarea');
	  newChartData.id = 'txtchartData';
	  newChartData.style.width = '100%';
	  newChartData.style.height = '300px';
	  newChartData.style.border = '2px solid red';

	  // เพิ่ม div ที่สร้างใหม่เข้าไปใน body หรือส่วนอื่น ๆ ของ DOM ที่ต้องการ
	  document.body.appendChild(newChartData);

	  console.log("สร้าง div 'char Data' เรียบร้อยแล้ว");
	} else {
	  console.log("พบ div 'chartData' แล้ว");
	}



return chartContainer;



} // end func
