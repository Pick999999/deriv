<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Document</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" > 

  <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tempus Dominus CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    
    <!-- Font Awesome for calendar icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


 <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
<style>

 
 
.sarabun-regular {
  font-family: "Sarabun", sans-serif;
  font-weight: 400;
  font-style: normal;
}

</style>



 </head>
 <body class='sarabun-regular'>
    Asset--> <div class="form-group">
                <label for="asset">เลือก Asset:</label>
                <select id="asset" name="asset" required>
                    <option value="R_10">Volatility 10 Index</option>
                    <option value="R_25">Volatility 25 Index</option>
                    <option value="R_50">Volatility 50 Index</option>
                    <option value="R_75">Volatility 75 Index</option>
                    <option value="R_100" selected >Volatility 100 Index</option>
                    <option value="BOOM1000">Boom 1000 Index</option>
                    <option value="CRASH1000">Crash 1000 Index</option>
                </select>
            </div>
			<button type='button' id='' class='mBtn' onclick="getFromLocal()">Get Local Local</button>
			<button type='button' id='' class='mBtn' onclick="savetoLocal()">Save To Local</button>
	<div id="chart-container">
			 
	</div>
<script>




class DerivChart {
    constructor(containerId, curPair, timeframe, candleCount) {
        this.containerId = containerId;
        this.curPair = curPair;
        this.timeframe = timeframe;
        this.candleCount = candleCount;
        this.chart = null;
        this.candleSeries = null;
        this.ema1Series = null;
        this.ema2Series = null;
        this.rsiChart = null;
        this.rsiSeries = null;
        this.timeLabel = null;
        this.ws = null;
        this.data = [];
        this.plotMarker = false;
		this.subplotMarker = false;

        this.init();
    }

    init() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Element with id "${this.containerId}" not found.`);
            return;
        }

        // สร้าง container สำหรับแสดงเวลา
        this.timeLabel = document.createElement('div');
        this.timeLabel.style.padding = '10px';
        container.appendChild(this.timeLabel);

        // สร้าง container สำหรับ candlestick chart
        const chartContainer = document.createElement('div');
        chartContainer.style.height = '400px';
        container.appendChild(chartContainer);

        // สร้าง candlestick chart
        this.chart = LightweightCharts.createChart(chartContainer, {
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

        this.candleSeries = this.chart.addCandlestickSeries();
        this.connectToderiv();
        this.drawPriceLine();
    }

    drawPriceLine() {
        const horizontalLine = {
            price: 2020,
            color: '#ff0000',
            lineWidth: 2,
            lineStyle: LightweightCharts.LineStyle.Dashed,
            axisLabelVisible: true,
            title: 'Horizontal Line'
        };

        this.candleSeries.createPriceLine(horizontalLine);
    }

    async connectToderiv() {
        this.ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');

        this.ws.onopen = () => {
            // Subscribe to candlestick data
            this.ws.send(JSON.stringify({
                ticks_history: this.curPair,
                adjust_start_time: 1,
                count: this.candleCount,
                end: 'latest',
                start: 1,
                style: 'candles',
                granularity: this.getGranularity(),
                subscribe: 1
            }));
        };

        this.ws.onmessage = (msg) => {
            const data = JSON.parse(msg.data);
           // console.log(data);

            if (data.candles) {
                // ข้อมูลย้อนหลัง (historical data)
                this.data = data.candles.map(candle => ({
                    time: candle.epoch,
                    open: candle.open,
                    high: candle.high,
                    low: candle.low,
                    close: candle.close
                }));
                this.updateChart(this.data);
            } else if (data.ohlc) {
                // ข้อมูลอัปเดตแบบ real-time
                if (data.ohlc.epoch % 60 === 0) {
                    this.plotMarker = true;
                    console.log('Plot Marker True');
                } else {
					if (data.ohlc.epoch % 10 === 0) {
					  console.log('Plot Sub Marker True');
                      this.subplotMarker = true;
					}

				}
                const newCandle = {
                    time: data.ohlc.epoch,
                    open: data.ohlc.open,
                    high: data.ohlc.high,
                    low: data.ohlc.low,
                    close: data.ohlc.close
                };
                this.data.push(newCandle);
                this.updateChart(this.data);
            }
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        this.ws.onclose = () => {
            console.log('WebSocket disconnected');
        };
    }

    getGranularity() {
        // Convert timeframe to seconds
        const timeframes = {
            '1m': 60,
            '5m': 300
        };
        return timeframes[this.timeframe];
    }

    updateChart(data) {
        if (data.length === 0) return;

        // Update candlestick series
        this.candleSeries.setData(data);
		console.log(data)
		

        // Create markers for every 10 seconds
        const markers = data.reduce((acc, candle) => {
            if (candle.time % 60 === 0) {
                acc.push({
                    time: candle.time,
                    position: 'aboveBar',
                    color: '#2196F3',
                    shape: 'triangle',
                    text: '▼'
                });
            }

            return acc;
        }, []);

		const submarkers = data.reduce((acc, candle) => {
            if (candle.time % 10 === 0) {
                acc.push({
                    time: candle.time,
                    position: 'aboveBar',
                    color: '#ff0080',
                    shape: 'triangle',
                    text: '▼'
                });
            }

            return acc;
        }, []);

        // Add markers to the series
        if (this.plotMarker) {
            this.candleSeries.setMarkers(markers);
            this.plotMarker = false; // Reset plotMarker
        } else {
			if (this.subplotMarker) {
              this.candleSeries.setMarkers(submarkers);
              this.subplotMarker = false; // Reset plotMarker
			}

		}

        // Optional: Add time formatter to ensure proper time display
        this.chart.applyOptions({
            timeScale: {
                timeVisible: true,
                secondsVisible: false,
            },
        });

        // Update time label
        const lastCandle = data[data.length - 1];
        this.timeLabel.textContent = `Last Update: ${new Date(lastCandle.time * 1000).toLocaleString()}`;
    }
} // end class


function savetoLocal() {
localStorage.setItem("curpairSelected",document.getElementById("asset").value );


} // end func

function getFromLocal() {


document.getElementById("asset").value = localStorage.getItem("curpairSelected")

} // end func

document.addEventListener('DOMContentLoaded', () => {

	const curpair = localStorage.getItem('curpairSelected');
	document.getElementById("asset").value = localStorage.getItem("curpairSelected")
    const chart = new DerivChart('chart-container', curpair, '1m', 100);
});

</script>




 </body>
</html>
