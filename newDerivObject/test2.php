<!DOCTYPE html>
<html>
<head>
    <title>Chart with Horizontal Line</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
</head>
<body>
    <div>
        <input type="text" id="priceInput" placeholder="Enter price">
        <button onclick="addHorizontalLine()">Add Line</button>
        <button onclick="removeAllLines()">Remove All Lines</button>
    </div>
    <div id="chart"></div>

    <script>
        const chart = LightweightCharts.createChart(document.getElementById('chart'), {
            width: 800,
            height: 400,
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
                borderColor: '#cccccc',
            },
            timeScale: {
                borderColor: '#cccccc',
            },
        });

        const candlestickSeries = chart.addCandlestickSeries({
            upColor: '#26a69a',
            downColor: '#ef5350',
            borderVisible: false,
            wickUpColor: '#26a69a',
            wickDownColor: '#ef5350'
        });

        const data = [
            { time: '2024-01-01', open: 100, high: 105, low: 95, close: 102 },
            { time: '2024-01-02', open: 102, high: 107, low: 98, close: 105 },
            { time: '2024-01-03', open: 105, high: 110, low: 101, close: 108 },
            { time: '2024-01-04', open: 108, high: 115, low: 105, close: 112 },
            { time: '2024-01-05', open: 112, high: 118, low: 110, close: 115 },
        ];

        candlestickSeries.setData(data);

        // เก็บ references ของทุก price lines
        let priceLines = [];

        function addHorizontalLine() {
            const priceInput = document.getElementById('priceInput');
            const price = parseFloat(priceInput.value);

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
                const priceLine = candlestickSeries.createPriceLine(newLine);
                priceLines.push(priceLine);

                priceInput.value = '';
            } else {
                alert('Please enter a valid number');
            }
        }

        function removeAllLines() {
            // ลบทุกเส้นที่มีอยู่
            if (priceLines.length > 0) {
                priceLines.forEach(priceLine => {
                    if (priceLine && typeof priceLine === 'object') {
                        candlestickSeries.removePriceLine(priceLine);
                    }
                });
                // ล้าง array
                priceLines = [];
            }
        }

        document.getElementById('priceInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                addHorizontalLine();
            }
        });
    </script>
</body>
</html>