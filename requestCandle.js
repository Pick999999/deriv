// ฟังก์ชันสำหรับเชื่อมต่อกับ Deriv API
function connectToDerivAPI() {
    websocket = new WebSocket(`${WS_URL}?app_id=${APP_ID}`);

    websocket.onopen = function() {
        console.log('เชื่อมต่อกับ Deriv API สำเร็จ');
        // คุณสามารถเรียก function เพื่อส่ง request หลังจากเชื่อมต่อสำเร็จ
    };

    websocket.onerror = function(error) {
        console.error('เกิดข้อผิดพลาดในการเชื่อมต่อ:', error);
    };

    websocket.onclose = function() {
        console.log('การเชื่อมต่อกับ Deriv API ถูกปิด');
    };

    return websocket;
}

// 1. ฟังก์ชั่นสำหรับขอข้อมูล Candle
function requestCandleData(symbol, granularity, count) {
    if (!websocket || websocket.readyState !== WebSocket.OPEN) {
        websocket = connectToDerivAPI();

        websocket.onopen = function() {
            sendCandleRequest(symbol, granularity, count);
        };
    } else {
        sendCandleRequest(symbol, granularity, count);
    }

    websocket.onmessage = function(msg) {
        const response = JSON.parse(msg.data);
        if (response.msg_type === 'candles') {
            const lightweightData = transformCandleData(response.candles);
            renderLightweightChart(lightweightData);
        }
    };
}

function sendCandleRequest(symbol, granularity, count) {
    const request = {
        ticks_history: symbol,
        adjust_start_time: 1,
        count: count || 1000,
        end: "latest",
        granularity: granularity || 60, // ค่าเริ่มต้น 1 นาที
        start: 1,
        style: "candles"
    };

    websocket.send(JSON.stringify(request));
}

// 2. ฟังก์ชั่นสำหรับขอข้อมูล Tick
function requestTickData(symbol, count) {
    if (!websocket || websocket.readyState !== WebSocket.OPEN) {
        websocket = connectToDerivAPI();

        websocket.onopen = function() {
            sendTickRequest(symbol, count);
        };
    } else {
        sendTickRequest(symbol, count);
    }

    websocket.onmessage = function(msg) {
        const response = JSON.parse(msg.data);

        if (response.msg_type === 'history') {
            const lightweightData = transformTickData(response.history);
            renderLightweightChart(lightweightData, 'line');
        }
    };
}

function sendTickRequest(symbol, count) {
    const request = {
        ticks_history: symbol,
        adjust_start_time: 1,
        count: count || 1000,
        end: "latest",
        start: 1,
        style: "ticks"
    };

    websocket.send(JSON.stringify(request));
}

// 3. ฟังก์ชั่นสำหรับขอข้อมูล History
function requestHistoryData(symbol, granularity, startTime, endTime) {
    if (!websocket || websocket.readyState !== WebSocket.OPEN) {
        websocket = connectToDerivAPI();

        websocket.onopen = function() {
            sendHistoryRequest(symbol, granularity, startTime, endTime);
        };
    } else {
        sendHistoryRequest(symbol, granularity, startTime, endTime);
    }

    websocket.onmessage = function(msg) {
        const response = JSON.parse(msg.data);

        if (response.msg_type === 'candles') {
            const lightweightData = transformCandleData(response.candles);
            renderLightweightChart(lightweightData);
        }
    };
}

function sendHistoryRequest(symbol, granularity, startTime, endTime) {
    const now = Math.floor(Date.now() / 1000);
    const request = {
        ticks_history: symbol,
        adjust_start_time: 1,
        count: 1000,
        end: endTime || "latest",
        start: startTime || (now - 86400), // ค่าเริ่มต้น 24 ชั่วโมงย้อนหลัง
        style: "candles",
        granularity: granularity || 3600 // ค่าเริ่มต้น 1 ชั่วโมง
    };

    websocket.send(JSON.stringify(request));
}

// ฟังก์ชั่นแปลงข้อมูล Candle ให้อยู่ในรูปแบบที่ใช้กับ Lightweight Charts
function transformCandleData(candles) {
    return candles.map(candle => ({
        time: candle.epoch, // หรือ candle.epoch * 1000 หากต้องการ milliseconds
        open: candle.open,
        high: candle.high,
        low: candle.low,
        close: candle.close
    }));
}

// ฟังก์ชั่นแปลงข้อมูล Tick ให้อยู่ในรูปแบบที่ใช้กับ Lightweight Charts (เป็น line chart)
function transformTickData(history) {
    const { prices, times } = history;
    return prices.map((price, index) => ({
        time: times[index], // หรือ times[index] * 1000 หากต้องการ milliseconds
        value: price
    }));
}

// ฟังก์ชั่นสำหรับสร้าง Lightweight Chart
function renderLightweightChart(data, chartType = 'candlestick') {
    // ตรวจสอบว่ามีการโหลด library และสร้าง element สำหรับแสดงกราฟหรือยัง
    if (!document.getElementById('chart-container')) {
        const container = document.createElement('div');
        container.id = 'chart-container';
        container.style.width = '100%';
        container.style.height = '500px';
        document.body.appendChild(container);
    }

    // ตัวอย่างการสร้าง chart โดยใช้ TradingView Lightweight Charts
    const chart = LightweightCharts.createChart(document.getElementById('chart-container'), {
        width: 800,
        height: 400,
        timeScale: {
            timeVisible: true,
            secondsVisible: true,
        }
    });

    // สร้าง series ตามประเภทของกราฟ
    let series;
    if (chartType === 'candlestick') {
        series = chart.addCandlestickSeries({
            upColor: '#26a69a',
            downColor: '#ef5350',
            borderVisible: false,
            wickUpColor: '#26a69a',
            wickDownColor: '#ef5350'
        });
    } else {
        series = chart.addLineSeries({
            color: '#2196F3',
            lineWidth: 2,
        });
    }

    // ใส่ข้อมูลลงใน series
    series.setData(data);

    // ปรับ viewport ให้แสดงข้อมูลทั้งหมด
    chart.timeScale().fitContent();

    return chart;
}

// ตัวอย่างการใช้งาน
// 1. สร้างการเชื่อมต่อเมื่อโหลดหน้าเว็บ
document.addEventListener('DOMContentLoaded', function() {
    // ตรวจสอบว่ามีการโหลด Lightweight Charts แล้วหรือไม่
    if (typeof LightweightCharts === 'undefined') {
        // โหลด script TradingView Lightweight Charts
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js';
        script.onload = function() {
            connectToDerivAPI();
        };
        document.head.appendChild(script);
    } else {
        connectToDerivAPI();
    }
});

// ตัวอย่างการเรียกใช้งาน
function loadCandleData() {
    requestCandleData('R_100', 60, 500); // ขอข้อมูลแท่งเทียน 1 นาที จำนวน 500 แท่ง
}

function loadTickData() {
    requestTickData('R_100', 1000); // ขอข้อมูล tick 1000 จุด
}

function loadHistoryData() {
	alert('Load')
    const oneWeekAgo = Math.floor(Date.now() / 1000) - (7 * 24 * 60 * 60);
    requestHistoryData('R_100', 3600, oneWeekAgo); // ขอข้อมูลย้อนหลัง 1 สัปดาห์ ความละเอียด 1 ชั่วโมง
}

// สามารถเรียกใช้ฟังก์ชั่นเหล่านี้เมื่อต้องการโหลดข้อมูล
// loadCandleData();
// loadTickData();
// loadHistoryData();

/*

 การขอข้อมูล Candle
 ws.onopen = function() {
    ws.send(JSON.stringify({
        ticks_history: "R_100",  // Symbol ที่ต้องการ (ในที่นี้คือ Random Index 100)
        adjust_start_time: 1,
        count: 1000,             // จำนวน candles ที่ต้องการ
        end: "latest",           // เวลาสิ้นสุด
        granularity: 60,         // ความละเอียด 60 วินาที (1 นาที)
        start: 1,                // เวลาเริ่มต้น
        style: "candles"         // ขอข้อมูลเป็นแบบ candles
    }));
};

ws.onmessage = function(msg) {
    const response = JSON.parse(msg.data);
    console.log(response);
    // นำข้อมูลไปใช้งานต่อ
};

{
  "echo_req": {
    "adjust_start_time": 1,
    "count": 1000,
    "end": "latest",
    "granularity": 60,
    "start": 1,
    "style": "candles",
    "ticks_history": "R_100"
  },
  "candles": [
    {"close": 5900.13, "epoch": 1586935200, "high": 5910.57, "low": 5899.95, "open": 5901.35},
    {"close": 5901.44, "epoch": 1586935260, "high": 5911.33, "low": 5898.62, "open": 5900.13},
    // ... more candle data
  ],
  "msg_type": "candles"
}

2. การขอข้อมูล Tick

const ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=YOUR_APP_ID');

ws.onopen = function() {
    ws.send(JSON.stringify({
        ticks_history: "R_100",  // Symbol ที่ต้องการ
        adjust_start_time: 1,
        count: 1000,             // จำนวน ticks ที่ต้องการ
        end: "latest",
        start: 1,
        style: "ticks"           // ขอข้อมูลเป็นแบบ ticks
    }));
};

ws.onmessage = function(msg) {
    const response = JSON.parse(msg.data);
    console.log(response);
    // นำข้อมูลไปใช้งานต่อ
};

{
  "echo_req": {
    "adjust_start_time": 1,
    "count": 1000,
    "end": "latest",
    "start": 1,
    "style": "ticks",
    "ticks_history": "R_100"
  },
  "history": {
    "prices": [5900.13, 5900.15, 5900.21, 5900.25, 5900.18, ...],
    "times": [1586935200, 1586935201, 1586935202, 1586935203, 1586935204, ...]
  },
  "msg_type": "history"
}

3. การขอข้อมูล History (ประวัติราคา)
const ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=YOUR_APP_ID');

ws.onopen = function() {
    ws.send(JSON.stringify({
        ticks_history: "R_100",  // Symbol ที่ต้องการ
        adjust_start_time: 1,
        count: 1000,
        end: "latest",
        start: Math.floor(Date.now() / 1000) - 86400, // 24 ชั่วโมงย้อนหลัง
        style: "candles",        // สามารถขอเป็น candles แต่เน้นช่วงเวลา history
        granularity: 3600        // ความละเอียด 1 ชั่วโมง
    }));
};

ws.onmessage = function(msg) {
    const response = JSON.parse(msg.data);
    console.log(response);
    // นำข้อมูลไปใช้งานต่อ
};

{
  "echo_req": {
    "adjust_start_time": 1,
    "count": 1000,
    "end": "latest",
    "granularity": 3600,
    "start": 1651123200,
    "style": "candles",
    "ticks_history": "R_100"
  },
  "candles": [
    {"close": 5921.44, "epoch": 1651123200, "high": 5954.32, "low": 5899.34, "open": 5902.45},
    {"close": 5934.21, "epoch": 1651126800, "high": 5941.87, "low": 5911.22, "open": 5921.44},
    // ... more candle data per hour
  ],
  "msg_type": "candles"
}

// 4 . แบบ ohlc
const historyRequest = {
                ticks_history: selectedSymbol,
                style: "candles",
                granularity: selectedTimeframe * 60, // Convert to seconds
                count: 60,
                end: "latest",
                req_id: requestId++
            };

*/