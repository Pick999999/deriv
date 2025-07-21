<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candlestick Chart with Draggable Price Line</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            height: 400px;
            position: relative;
            margin-bottom: 20px;
        }
        .controls {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .price-box {
            margin-right: 20px;
            display: flex; 
            align-items: center;
        }
        label {
            margin-right: 10px;
            font-weight: bold;
        }
        input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 120px;
        }
        .instructions {
            background-color: #f0f8ff;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Candlestick Chart with Draggable Price Line</h1>
        
        <div class="instructions">
            <strong>คำแนะนำ:</strong> คลิกและลากเมาส์ขึ้นหรือลงในกราฟเพื่อเลื่อนเส้นราคา ราคาปัจจุบันจะแสดงในช่องด้านล่าง
        </div>
        
        <div class="chart-container" id="chart"></div>
        
        <div class="controls">
            <div class="price-box">
                <label for="priceInput">ราคา:</label>
                <input type="text" id="priceInput" readonly>
            </div>
        </div>
    </div>

    <script>
        // สร้างข้อมูลจำลองสำหรับกราฟ candlestick
        function generateCandlestickData(count = 100) {
            const data = [];
            let time = new Date(Date.UTC(2023, 0, 1, 0, 0, 0, 0));
            let baseValue = 10000;
            let amplitude = 500;
            
            for (let i = 0; i < count; i++) {
                const open = baseValue + Math.round((Math.random() * amplitude - amplitude / 2) * 10) / 10;
                const close = open + Math.round((Math.random() * amplitude - amplitude / 2) * 10) / 10;
                const low = Math.min(open, close) - Math.round(Math.random() * amplitude * 0.3 * 10) / 10;
                const high = Math.max(open, close) + Math.round(Math.random() * amplitude * 0.3 * 10) / 10;
                
                data.push({
                    time: time.getTime() / 1000,
                    open: open,
                    high: high,
                    low: low,
                    close: close
                });
                
                // ปรับค่าพื้นฐานสำหรับแท่งถัดไป
                baseValue = close;
                // เพิ่มเวลาขึ้น 1 วัน
                time.setUTCDate(time.getUTCDate() + 1);
            }
            
            return data;
        }

        // สร้าง chart
        document.addEventListener('DOMContentLoaded', function() {
            const chartElement = document.getElementById('chart');
            const priceInput = document.getElementById('priceInput');
            
            // สร้าง chart ด้วย lightweight-charts
            const chart = LightweightCharts.createChart(chartElement, {
                width: chartElement.clientWidth,
                height: chartElement.clientHeight,
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
                    borderColor: '#d1d4dc',
                    scaleMargins: {
                        top: 0.1,
                        bottom: 0.1,
                    },
                },
                timeScale: {
                    borderColor: '#d1d4dc',
                },
            });
            
            // สร้าง candlestick series และเพิ่มข้อมูล
            const candlestickSeries = chart.addCandlestickSeries({
                upColor: '#26a69a', 
                downColor: '#ef5350',
                borderVisible: false,
                wickUpColor: '#26a69a', 
                wickDownColor: '#ef5350'
            });
            
            const candlestickData = generateCandlestickData();
            candlestickSeries.setData(candlestickData);
            
            // หาค่าราคา min, max และเริ่มต้น
            let minPrice = Infinity;
            let maxPrice = -Infinity;
            let lastPrice = 0;
            
            for (const candle of candlestickData) {
                minPrice = Math.min(minPrice, candle.low);
                maxPrice = Math.max(maxPrice, candle.high);
                lastPrice = candle.close; // ราคาปิดของแท่งสุดท้าย
            }
            
            // คำนวณช่วงราคาและเพิ่มพื้นที่ขอบ
            const priceRange = maxPrice - minPrice;
            minPrice = minPrice - 0.05 * priceRange;
            maxPrice = maxPrice + 0.05 * priceRange;
            
            // ตัวแปรเก็บราคาปัจจุบัน
            let currentPrice = lastPrice;
            
            // สร้างและอัปเดต price line
            let priceLine = null;
            
            function updatePriceLine(price) {
                // จำกัดราคาให้อยู่ในช่วงที่สมเหตุสมผล
                if (price < minPrice) price = minPrice;
                if (price > maxPrice) price = maxPrice;
                
                // ลบเส้นเดิม (ถ้ามี) และสร้างเส้นใหม่
                if (priceLine !== null) {
                    candlestickSeries.removePriceLine(priceLine);
                }
                
                priceLine = candlestickSeries.createPriceLine({
                    price: price,
                    color: '#2962FF',
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Solid,
                    axisLabelVisible: true,
                    title: 'ราคา',
                });
                
                // อัปเดตค่าในช่องข้อความ
                priceInput.value = price.toFixed(2);
                currentPrice = price;
            }
            
            // กำหนดเส้นราคาเริ่มต้น
            updatePriceLine(lastPrice);
            
            // คำนวณราคาจากตำแหน่ง Y บนกราฟ
            function getPriceFromY(y) {
                const chartHeight = chartElement.clientHeight;
                // แปลงค่า Y (0 = ด้านบนของกราฟ, chartHeight = ด้านล่างของกราฟ)
                // เป็นราคา (maxPrice = ด้านบนของกราฟ, minPrice = ด้านล่างของกราฟ)
                return maxPrice - (y / chartHeight) * (maxPrice - minPrice);
            }
            
            // ตัวแปรเก็บสถานะการลาก
            let isDragging = false;
            
            // เพิ่ม event listeners สำหรับการลากเส้น
            chartElement.addEventListener('mousedown', function(e) {
                // คำนวณตำแหน่ง Y ภายในกราฟ
                const rect = chartElement.getBoundingClientRect();
                const y = e.clientY - rect.top;
                
                if (y >= 0 && y <= rect.height) {
                    // คำนวณราคาจากตำแหน่ง Y
                    const price = getPriceFromY(y);
                    updatePriceLine(price);
                    
                    isDragging = true;
                    chartElement.style.cursor = 'ns-resize';
                    e.preventDefault(); // ป้องกันการเลือกข้อความ
                }
            });
            
            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                
                // คำนวณตำแหน่ง Y ภายในกราฟ
                const rect = chartElement.getBoundingClientRect();
                const y = e.clientY - rect.top;
                
                if (y >= 0 && y <= rect.height) {
                    // คำนวณราคาจากตำแหน่ง Y
                    const price = getPriceFromY(y);
                    updatePriceLine(price);
                }
            });
            
            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    chartElement.style.cursor = 'default';
                }
            });
            
            chartElement.addEventListener('mouseleave', function() {
                if (isDragging) {
                    isDragging = false;
                    chartElement.style.cursor = 'default';
                }
            });
            
            // ป้องกันการเลือกข้อความเมื่อลากเมาส์
            chartElement.addEventListener('selectstart', function(e) {
                if (isDragging) {
                    e.preventDefault();
                }
            });
            
            // ทำให้สามารถใช้ปุ่มลูกศรเพื่อเลื่อนราคาได้ด้วย
            document.addEventListener('keydown', function(e) {
                const step = (maxPrice - minPrice) / 100; // 1% ของช่วงราคา
                
                if (e.key === 'ArrowUp') {
                    updatePriceLine(currentPrice + step);
                    e.preventDefault();
                } else if (e.key === 'ArrowDown') {
                    updatePriceLine(currentPrice - step);
                    e.preventDefault();
                }
            });
            
            // ปรับขนาดกราฟเมื่อหน้าต่างเปลี่ยนขนาด
            window.addEventListener('resize', function() {
                chart.applyOptions({
                    width: chartElement.clientWidth,
                    height: chartElement.clientHeight
                });
            });
            
            // กำหนดให้แสดงข้อมูลทั้งหมด
            chart.timeScale().fitContent();
        });
    </script>
</body>
</html>