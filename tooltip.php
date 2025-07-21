<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candlestick Chart with EMA3 Tooltip</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        #chart-container {
            width: 100%;
            height: 400px;
        }
    </style>
</head>
<body>
    <div id="chart-container"></div>

    <script>
        // สร้างข้อมูลตัวอย่าง (คุณควรแทนที่ด้วยข้อมูลจริงจาก deriv.com)
        const candlestickData = [
            { time: '2023-01-01', open: 10, high: 15, low: 9, close: 12 },
            { time: '2023-01-02', open: 12, high: 14, low: 11, close: 13 },
            { time: '2023-01-03', open: 13, high: 16, low: 12, close: 15 },
            { time: '2023-01-04', open: 15, high: 18, low: 13, close: 14 },
            { time: '2023-01-05', open: 14, high: 16, low: 13, close: 16 },
            { time: '2023-01-06', open: 16, high: 19, low: 15, close: 18 },
            { time: '2023-01-07', open: 18, high: 20, low: 16, close: 17 },
            { time: '2023-01-08', open: 17, high: 19, low: 15, close: 19 },
            { time: '2023-01-09', open: 19, high: 22, low: 18, close: 20 },
            { time: '2023-01-10', open: 20, high: 23, low: 19, close: 21 },
        ];

        // คำนวณ EMA3
        function calculateEMA(data, period) {
            const emaData = [];
            const k = 2 / (period + 1);
            
            // คำนวณ SMA สำหรับค่าเริ่มต้น
            let sum = 0;
            for (let i = 0; i < period; i++) {
                sum += data[i].close;
            }
            let ema = sum / period;
            
            // สร้างข้อมูล EMA
            for (let i = 0; i < data.length; i++) {
                if (i < period - 1) {
                    // ยังไม่พอข้อมูลสำหรับคำนวณ EMA
                    emaData.push({ 
                        time: data[i].time, 
                        value: null 
                    });
                } else if (i === period - 1) {
                    // ใช้ SMA เป็นค่าเริ่มต้น
                    emaData.push({ 
                        time: data[i].time, 
                        value: ema 
                    });
                } else {
                    // คำนวณ EMA
                    ema = (data[i].close - ema) * k + ema;
                    emaData.push({ 
                        time: data[i].time, 
                        value: ema 
                    });
                }
            }
            
            return emaData;
        }

        // คำนวณ EMA3
        const emaData = calculateEMA(candlestickData, 3);

        // สร้าง lookup object เพื่อให้ง่ายต่อการค้นหาค่า EMA
        const emaLookup = {};
        emaData.forEach(item => {
            emaLookup[item.time] = item.value;
        });

        // สร้าง chart
        const chartContainer = document.getElementById('chart-container');
        const chart = LightweightCharts.createChart(chartContainer, {
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
                borderColor: '#d1d1d1',
            },
            timeScale: {
                borderColor: '#d1d1d1',
            },
            localization: {
                priceFormatter: price => price.toFixed(2),
            },
        });

        // สร้าง candlestick series
        const candlestickSeries = chart.addCandlestickSeries({
            upColor: '#4CAF50',
            downColor: '#FF5252',
            borderUpColor: '#4CAF50',
            borderDownColor: '#FF5252',
            wickUpColor: '#4CAF50',
            wickDownColor: '#FF5252',
        });
        candlestickSeries.setData(candlestickData);

        // สร้าง EMA series
        const emaSeries = chart.addLineSeries({
            color: '#2962FF',
            lineWidth: 2,
            title: 'EMA3',
        });
        emaSeries.setData(emaData);

        // กำหนด tooltip
        chart.subscribeCrosshairMove(param => {
            if (param.time && param.point) {
                const price = param.seriesData.get(candlestickSeries);
                const emaValue = emaLookup[param.time];
                
                if (price) {
                    // สร้าง tooltip แบบกำหนดเอง
                    const tooltipEl = document.createElement('div');
                    tooltipEl.style.position = 'absolute';
                    tooltipEl.style.left = `${param.point.x + 15}px`;
                    tooltipEl.style.top = `${param.point.y + 15}px`;
                    tooltipEl.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
                    tooltipEl.style.padding = '8px';
                    tooltipEl.style.borderRadius = '4px';
                    tooltipEl.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.2)';
                    tooltipEl.style.fontSize = '12px';
                    tooltipEl.style.zIndex = '1000';
                    tooltipEl.style.pointerEvents = 'none';
					console.log('tooltipEl.innerHTML');
                    
                    tooltipEl.innerHTML = `
                        <div style="font-weight: bold; margin-bottom: 4px;">Date: ${param.time}</div>
                        <div>Open: ${price.open.toFixed(2)}</div>
                        <div>High: ${price.high.toFixed(2)}</div>
                        <div>Low: ${price.low.toFixed(2)}</div>
                        <div>Close: ${price.close.toFixed(2)}</div>
                        <div style="margin-top: 4px; color: #2962FF; font-weight: bold;">EMA3: ${emaValue ? emaValue.toFixed(2) : 'N/A'}</div>
                    `;
					 
					
                    
                    // ลบ tooltip เก่า (ถ้ามี)
                    const oldTooltip = document.querySelector('.custom-tooltip');
                    if (oldTooltip) {
                        oldTooltip.remove();
                    }
                    
                    tooltipEl.className = 'custom-tooltip';
                    document.body.appendChild(tooltipEl);
                }
            } else {
                // ลบ tooltip เมื่อเมาส์ออกจากพื้นที่กราฟ
                const oldTooltip = document.querySelector('.custom-tooltip');
                if (oldTooltip) {
                    oldTooltip.remove();
                }
            }
        });

        // ปรับขนาดกราฟเมื่อขนาดหน้าจอเปลี่ยน
        window.addEventListener('resize', () => {
            chart.applyOptions({
                width: chartContainer.clientWidth,
                height: chartContainer.clientHeight
            });
        });

        // เรียกใช้งานครั้งแรก
        chart.applyOptions({
            width: chartContainer.clientWidth,
            height: chartContainer.clientHeight
        });
    </script>
</body>
</html>