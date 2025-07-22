<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSI Chart with TradingView Lightweight Charts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .chart-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        #price-chart {
            height: 300px;
            margin-bottom: 20px;
        }
        #rsi-chart {
            height: 150px;
        }
    </style>
    <!-- Import TradingView Lightweight Charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightweight-charts/4.1.1/lightweight-charts.standalone.production.min.js"></script>

	<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
</head>
<body>
    <div class="chart-container">
        <h2>Price Chart</h2>
        <div id="price-chart"></div>
        <h2>RSI Indicator (14)</h2>
        <div id="rsi-chart"></div>
    </div>

    <script>
        // Sample data
        let candleData = [
            { time: '2021-01-01', open: 100, high: 105, low: 98, close: 103 },
            { time: '2021-01-02', open: 103, high: 110, low: 102, close: 108 },
            { time: '2021-01-03', open: 108, high: 112, low: 105, close: 106 },
            { time: '2021-01-04', open: 106, high: 109, low: 103, close: 107 },
            { time: '2021-01-05', open: 107, high: 111, low: 105, close: 110 },
            { time: '2021-01-06', open: 110, high: 115, low: 108, close: 112 },
            { time: '2021-01-07', open: 112, high: 118, low: 110, close: 116 },
            { time: '2021-01-08', open: 116, high: 120, low: 114, close: 119 },
            { time: '2021-01-09', open: 119, high: 125, low: 118, close: 123 },
            { time: '2021-01-10', open: 123, high: 128, low: 121, close: 125 },
            { time: '2021-01-11', open: 125, high: 127, low: 122, close: 124 },
            { time: '2021-01-12', open: 124, high: 126, low: 120, close: 121 },
            { time: '2021-01-13', open: 121, high: 123, low: 118, close: 119 },
            { time: '2021-01-14', open: 119, high: 120, low: 115, close: 116 },
            { time: '2021-01-15', open: 116, high: 118, low: 113, close: 115 },
            { time: '2021-01-16', open: 115, high: 120, low: 114, close: 118 },
            { time: '2021-01-17', open: 118, high: 122, low: 117, close: 121 },
            { time: '2021-01-18', open: 121, high: 125, low: 120, close: 124 },
            { time: '2021-01-19', open: 124, high: 128, low: 123, close: 127 },
            { time: '2021-01-20', open: 127, high: 132, low: 126, close: 130 }
        ];

        // Function to convert epoch timestamp to TradingView time format
        function formatTimeFromEpoch(epoch) {
            const date = new Date(epoch * 1000);
            return date.toISOString().split('T')[0]; // Format: 'YYYY-MM-DD'
        }

        // Function to convert data from deriv.com format to TradingView format
        function convertDataForTradingView(data) {
            return data.map(candle => ({
                time: typeof candle.time === 'number' ? formatTimeFromEpoch(candle.time) : candle.time,
                open: candle.open,
                high: candle.high,
                low: candle.low,
                close: candle.close
            }));
        }

        // Function to calculate RSI
        function calculateRSI(data, period = 7) {
            if (data.length < period + 1) {
                return Array(data.length).fill({ value: 50 });
            }

            let rsiValues = [];
            let gains = [];
            let losses = [];
            
            // Calculate initial gains and losses
            for (let i = 1; i < data.length; i++) {
                const change = data[i].close - data[i-1].close;
                gains.push(change > 0 ? change : 0);
                losses.push(change < 0 ? Math.abs(change) : 0);
            }

            // Calculate RSI for each point
            for (let i = period; i <= data.length-1; i++) {
                const gainsSlice = gains.slice(i - period, i);
                const lossesSlice = losses.slice(i - period, i);
                
                // Calculate average gain and average loss
                const avgGain = gainsSlice.reduce((sum, val) => sum + val, 0) / period;
                const avgLoss = lossesSlice.reduce((sum, val) => sum + val, 0) / period;
                
                // Calculate RS and RSI
                if (avgLoss === 0) {
				    console.log('A-',data[i].time,'-',i);
					
                    rsiValues.push({ time: data[i].time, value: 100 });
                } else {
					console.log('B-',data[i].time,'-',i);
                    const rs = avgGain / avgLoss;
                    const rsi = 100 - (100 / (1 + rs));
                    rsiValues.push({ time: data[i].time, value: rsi });
                }
            }
			 console.log(rsiValues)
			

            // Pad the beginning with null values
            const padding = Array(period).fill(null);
            return padding.concat(rsiValues);
        }

        // Create price chart
        function createPriceChart(container, data) {
            const chart = LightweightCharts.createChart(container, {
                height: container.offsetHeight,
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
            });

            const candleSeries = chart.addCandlestickSeries({
                upColor: '#26a69a',
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a',
                wickDownColor: '#ef5350',
            });

            candleSeries.setData(data);
            chart.timeScale().fitContent();

            return { chart, series: candleSeries };
        }

        // Create RSI chart
        function createRSIChart(container, data) {
            const chart = LightweightCharts.createChart(container, {
                height: container.offsetHeight,
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

            // Add RSI line series
            const rsiSeries = chart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                priceLineVisible: false,
            });

            // Add overbought level line (70)
            const overboughtLine = chart.addLineSeries({
                color: '#FF0000',
                lineWidth: 1,
                lineStyle: LightweightCharts.LineStyle.Dashed,
                priceLineVisible: false,
            });
            overboughtLine.setData([
                { time: data[0].time, value: 70 },
                { time: data[data.length - 1].time, value: 70 }
            ]);

            // Add middle level line (50)
            const middleLine = chart.addLineSeries({
                color: '#787B86',
                lineWidth: 1,
                lineStyle: LightweightCharts.LineStyle.Dashed,
                priceLineVisible: false,
            });
            middleLine.setData([
                { time: data[0].time, value: 50 },
                { time: data[data.length - 1].time, value: 50 }
            ]);

            // Add oversold level line (30)
            const oversoldLine = chart.addLineSeries({
                color: '#00FF00',
                lineWidth: 1,
                lineStyle: LightweightCharts.LineStyle.Dashed,
                priceLineVisible: false,
            });
            oversoldLine.setData([
                { time: data[0].time, value: 30 },
                { time: data[data.length - 1].time, value: 30 }
            ]);

            // Calculate and set RSI data
            const rsiData = calculateRSI(data).filter(item => item !== null);
            rsiSeries.setData(rsiData);

            chart.timeScale().fitContent();

            return { chart, series: rsiSeries };
        }

        // Convert data for TradingView format
        const tradingViewData = convertDataForTradingView(candleData);

        // Create charts when page loads
        window.onload = function() {
            // Create price chart
            const priceChartContainer = document.getElementById('price-chart');
            const priceChart = createPriceChart(priceChartContainer, tradingViewData);
            
            // Create RSI chart
            const rsiChartContainer = document.getElementById('rsi-chart');
            const rsiChart = createRSIChart(rsiChartContainer, tradingViewData);
            
            // Sync charts' time scales
            priceChart.chart.timeScale().subscribeVisibleLogicalRangeChange(range => {
                rsiChart.chart.timeScale().setVisibleLogicalRange(range);
            });
            
            rsiChart.chart.timeScale().subscribeVisibleLogicalRangeChange(range => {
                priceChart.chart.timeScale().setVisibleLogicalRange(range);
            });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                priceChart.chart.applyOptions({
                    height: priceChartContainer.offsetHeight
                });
                rsiChart.chart.applyOptions({
                    height: rsiChartContainer.offsetHeight
                });
            });
        };

        // Function to update charts with new data
        function updateCharts(newData) {
            // Convert data to TradingView format
            const tvData = convertDataForTradingView(newData);
            
            // Recreate charts with new data
            const priceChartContainer = document.getElementById('price-chart');
            const rsiChartContainer = document.getElementById('rsi-chart');
            
            // Clear containers
            priceChartContainer.innerHTML = '';
            rsiChartContainer.innerHTML = '';
            
            // Create new charts
            const priceChart = createPriceChart(priceChartContainer, tvData);
            const rsiChart = createRSIChart(rsiChartContainer, tvData);
            
            // Sync time scales
            priceChart.chart.timeScale().subscribeVisibleLogicalRangeChange(range => {
                rsiChart.chart.timeScale().setVisibleLogicalRange(range);
            });
            
            rsiChart.chart.timeScale().subscribeVisibleLogicalRangeChange(range => {
                priceChart.chart.timeScale().setVisibleLogicalRange(range);
            });
        }

        // Example function to update with data from deriv.com
        function updateWithDerivData(derivData) {
            // Update candleData with new data from deriv.com
            candleData = derivData.map(candle => ({
                time: candle.epoch,
                open: candle.open,
                high: candle.high,
                low: candle.low,
                close: candle.close
            }));
            
            // Update charts
            updateCharts(candleData);
        }
    </script>
</body>
</html>