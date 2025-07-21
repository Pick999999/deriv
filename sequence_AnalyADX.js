/*
ฉันมี  array เก็บค่าของ asset code จาก deriv.com ต้องการให้  loop ดึงค่า candle  จาก deriv.com
โดยดึงข้อมูล  latest ที่ timeframe 1 นาที จำนวน 60 แท่ง หลังจากนั้น นำมา วิเคราะห์ความแข็งแกร่งของ trend และทิศทางของ trend
ด้วย adx จากนั้น นำมาค่าแต่ละ aaset มาเก็บเป็น  array json ตามนี้

{
  assetCode : assetCode ,
  lastTimeCandle : 'hh:mm',
  rawData : เป็น array ของข้อมูลดิบ ,
  ema3 : ema3,
  ema5 : ema5,
  emaAbove: ema3 อยู่เหนือ ema5 หรือ ema5 อยู่เหนือ ema3,
  isema5TurnPoint: turnup,turndown
  adx : ค่า adx ,
  adxList : ตรงนี้จะเอาค่า adx 5 ตัวสุดท้ายมาคำนวณว่า adx มีค่าขึ้นหรือลดลง
  trend: up,down,sideway
  TrendAnalysis : วิเคราะห์ความแข็งแกร่งของ Trend
 }
จากนั้น เก็บข้อมูลลง localStorage
*/

// ฟังก์ชันหลักสำหรับดึงข้อมูลและวิเคราะห์ trend
async function fetchAndAnalyzeAssets() {
  // สมมติว่ามี asset codes จาก Deriv เก็บไว้ใน array
  const assetCodes = ["R_10", "R_25", "R_50", "R_75", "R_100"];

  const results = [];

  for (const assetCode of assetCodes) {
    try {
      console.log(`กำลังวิเคราะห์ ${assetCode}...`);

      // ดึงข้อมูล candle จาก Deriv API
      const candleData = await fetchCandleData(assetCode);

      if (!candleData || !candleData.length) {
        console.error(`ไม่สามารถดึงข้อมูล candle สำหรับ ${assetCode} ได้`);
        continue;
      }

      // คำนวณ indicators
      const ema3 = calculateEMA(candleData, 3);
      const ema5 = calculateEMA(candleData, 5);
      const adxValues = calculateADX(candleData);

      // ตรวจสอบว่า EMA ไหนอยู่เหนือกว่า
      const emaAbove = ema3[ema3.length - 1] > ema5[ema5.length - 1] ? "ema3" : "ema5";

      // ตรวจสอบ turning point ของ EMA5
      const isEma5TurnPoint = checkEma5TurnPoint(ema5);

      // ตรวจสอบทิศทางของ trend
      const trend = determineTrend(candleData, ema3, ema5, adxValues);

      // วิเคราะห์ความแข็งแกร่งของ trend
      const trendAnalysis = analyzeTrendStrength(adxValues, trend);

      // สร้าง object ผลลัพธ์
      const result = {
        assetCode: assetCode,
        lastTimeCandle: formatTimeFromTimestamp(candleData[candleData.length - 1].time),
        rawData: candleData,
        ema3: ema3[ema3.length - 1],
        ema5: ema5[ema5.length - 1],
        emaAbove: emaAbove,
        isEma5TurnPoint: isEma5TurnPoint,
        adx: adxValues.adx[adxValues.adx.length - 1],
        adxList: adxValues.adx.slice(-5),
        trend: trend,
        trendAnalysis: trendAnalysis
      };

      results.push(result);

    } catch (error) {
      console.error(`เกิดข้อผิดพลาดในการวิเคราะห์ ${assetCode}:`, error);
    }
  }

  // เก็บผลลัพธ์ลง localStorage
  localStorage.setItem('derivTrendAnalysis', JSON.stringify(results));
  console.log('บันทึกข้อมูลการวิเคราะห์ลง localStorage เรียบร้อยแล้ว');

  return results;
}

// ฟังก์ชันจำลองสำหรับดึงข้อมูล candle จาก Deriv API (ในสถานการณ์จริงควรใช้ WebSocket API ของ Deriv)
async function fetchCandleData(assetCode, timeframe = 60) {
  // ในสถานการณ์จริง คุณจะต้องเชื่อมต่อกับ Deriv API ด้วย WebSocket
  // และส่งคำขอข้อมูล candle ด้วย ticks_history

  // เนื่องจากนี่เป็นตัวอย่าง เราจะสร้างข้อมูลจำลอง
  return new Promise((resolve) => {
    setTimeout(() => {
      const candles = [];
      const endTime = Date.now();
      const interval = 60 * 1000; // 1 นาที

      for (let i = timeframe - 1; i >= 0; i--) {
        const time = endTime - (i * interval);
        const open = 100 + Math.random() * 5;
        const close = open + (Math.random() * 2 - 1);
        const high = Math.max(open, close) + Math.random();
        const low = Math.min(open, close) - Math.random();
        const volume = Math.floor(Math.random() * 1000);

        candles.push({ time, open, high, low, close, volume });
      }

      resolve(candles);
    }, 300); // จำลองการดีเลย์ของเครือข่าย
  });
}

// ฟังก์ชันสำหรับคำนวณ EMA (Exponential Moving Average)
function calculateEMA(candles, period) {
  const closes = candles.map(candle => candle.close);
  const ema = [];

  // คำนวณ SMA สำหรับค่าเริ่มต้น
  const sma = closes.slice(0, period).reduce((sum, price) => sum + price, 0) / period;
  ema.push(sma);

  // คำนวณ EMA
  const multiplier = 2 / (period + 1);

  for (let i = period; i < closes.length; i++) {
    const value = (closes[i] - ema[ema.length - 1]) * multiplier + ema[ema.length - 1];
    ema.push(value);
  }

  return ema;
}

// ฟังก์ชันสำหรับคำนวณ ADX (Average Directional Index)
function calculateADX(candles, period = 14) {
  const highs = candles.map(candle => candle.high);
  const lows = candles.map(candle => candle.low);
  const closes = candles.map(candle => candle.close);

  // คำนวณ True Range
  const tr = [];
  for (let i = 1; i < candles.length; i++) {
    const hl = highs[i] - lows[i];
    const hc = Math.abs(highs[i] - closes[i - 1]);
    const lc = Math.abs(lows[i] - closes[i - 1]);
    tr.push(Math.max(hl, hc, lc));
  }

  // คำนวณ +DM และ -DM
  const plusDM = [];
  const minusDM = [];

  for (let i = 1; i < candles.length; i++) {
    const highDiff = highs[i] - highs[i - 1];
    const lowDiff = lows[i - 1] - lows[i];

    if (highDiff > lowDiff && highDiff > 0) {
      plusDM.push(highDiff);
    } else {
      plusDM.push(0);
    }

    if (lowDiff > highDiff && lowDiff > 0) {
      minusDM.push(lowDiff);
    } else {
      minusDM.push(0);
    }
  }

  // คำนวณ Smoothed TR, +DM, -DM
  const smoothedTR = calculateSmoothed(tr, period);
  const smoothedPlusDM = calculateSmoothed(plusDM, period);
  const smoothedMinusDM = calculateSmoothed(minusDM, period);

  // คำนวณ +DI และ -DI
  const plusDI = [];
  const minusDI = [];

  for (let i = 0; i < smoothedTR.length; i++) {
    plusDI.push((smoothedPlusDM[i] / smoothedTR[i]) * 100);
    minusDI.push((smoothedMinusDM[i] / smoothedTR[i]) * 100);
  }

  // คำนวณ DX
  const dx = [];
  for (let i = 0; i < plusDI.length; i++) {
    dx.push((Math.abs(plusDI[i] - minusDI[i]) / (plusDI[i] + minusDI[i])) * 100);
  }

  // คำนวณ ADX
  const adx = calculateSmoothed(dx, period);

  return {
    adx,
    plusDI,
    minusDI
  };
}

// Calculate ADX (Average Directional Index)
        function calculateADXOld(candles, period) {
            const adx = [];
            const plusDM = [];
            const minusDM = [];
            const TR = [];

            // Calculate +DM, -DM, and TR for each period
            for (let i = 1; i < candles.length; i++) {
                const upMove = candles[i].high - candles[i - 1].high;
                const downMove = candles[i - 1].low - candles[i].low;

                plusDM[i] = upMove > downMove && upMove > 0 ? upMove : 0;
                minusDM[i] = downMove > upMove && downMove > 0 ? downMove : 0;

                TR[i] = Math.max(
                    candles[i].high - candles[i].low,
                    Math.abs(candles[i].high - candles[i - 1].close),
                    Math.abs(candles[i].low - candles[i - 1].close)
                );
            }

            // Calculate smoothed +DM, -DM, and TR
            const smoothedPlusDM = [];
            const smoothedMinusDM = [];
            const smoothedTR = [];

            // Initial values (simple sum)
            let sumPlusDM = 0;
            let sumMinusDM = 0;
            let sumTR = 0;

            for (let i = 1; i <= period; i++) {
                sumPlusDM += plusDM[i] || 0;
                sumMinusDM += minusDM[i] || 0;
                sumTR += TR[i] || 0;
            }

            smoothedPlusDM[period] = sumPlusDM;
            smoothedMinusDM[period] = sumMinusDM;
            smoothedTR[period] = sumTR;

            // Subsequent values (smoothed)
            for (let i = period + 1; i < candles.length; i++) {
                smoothedPlusDM[i] = smoothedPlusDM[i - 1] - (smoothedPlusDM[i - 1] / period) + (plusDM[i] || 0);
                smoothedMinusDM[i] = smoothedMinusDM[i - 1] - (smoothedMinusDM[i - 1] / period) + (minusDM[i] || 0);
                smoothedTR[i] = smoothedTR[i - 1] - (smoothedTR[i - 1] / period) + (TR[i] || 0);
            }

            // Calculate +DI and -DI
            const plusDI = [];
            const minusDI = [];

            for (let i = period; i < candles.length; i++) {
                plusDI[i] = (smoothedPlusDM[i] / smoothedTR[i]) * 100;
                minusDI[i] = (smoothedMinusDM[i] / smoothedTR[i]) * 100;
            }

            // Calculate DX and ADX
            const DX = [];

            for (let i = period; i < candles.length; i++) {
                const diDiff = Math.abs(plusDI[i] - minusDI[i]);
                const diSum = plusDI[i] + minusDI[i];
                DX[i] = (diDiff / diSum) * 100;
            }

            // First ADX value is simple average of first 'period' DX values
            let sumDX = 0;
            for (let i = period; i < period * 2 && i < DX.length; i++) {
                if (DX[i]) sumDX += DX[i];
            }
            adx[period * 2 - 1] = sumDX / period;

            // Subsequent ADX values are smoothed
            for (let i = period * 2; i < candles.length; i++) {
                adx[i] = ((adx[i - 1] * (period - 1)) + (DX[i] || 0)) / period;
            }

            return adx;
}

// ฟังก์ชันช่วยสำหรับคำนวณค่า Smoothed
function calculateSmoothed(data, period) {
  const smoothed = [];

  // คำนวณค่าเริ่มต้น
  let sum = 0;
  for (let i = 0; i < period; i++) {
    sum += data[i];
  }
  smoothed.push(sum / period);

  // คำนวณค่าที่เหลือ
  for (let i = period; i < data.length; i++) {
    smoothed.push(
      (smoothed[smoothed.length - 1] * (period - 1) + data[i]) / period
    );
  }

  return smoothed;
}

// ฟังก์ชันตรวจสอบ turning point ของ EMA5
function checkEma5TurnPoint(ema5) {
  if (ema5.length < 3) return "none";

  const last = ema5[ema5.length - 1];
  const prev = ema5[ema5.length - 2];
  const prevPrev = ema5[ema5.length - 3];

  if (prev < last && prev <= prevPrev) {
    return "turnup";
  } else if (prev > last && prev >= prevPrev) {
    return "turndown";
  } else {
    return "none";
  }
}

// ฟังก์ชันตรวจสอบทิศทางของ trend
function determineTrend(candles, ema3, ema5, adxValues) {
  const lastEma3 = ema3[ema3.length - 1];
  const lastEma5 = ema5[ema5.length - 1];
  const lastADX = adxValues.adx[adxValues.adx.length - 1];
  const lastPlusDI = adxValues.plusDI[adxValues.plusDI.length - 1];
  const lastMinusDI = adxValues.minusDI[adxValues.minusDI.length - 1];

  // ถ้า ADX น้อยกว่า 20 แสดงว่าตลาดไม่มีทิศทางชัดเจน (sideway)
  if (lastADX < 20) {
    return "sideway";
  }

  // ถ้า +DI มากกว่า -DI แสดงว่าเป็น uptrend
  if (lastPlusDI > lastMinusDI) {
    return "up";
  }

  // ถ้า -DI มากกว่า +DI แสดงว่าเป็น downtrend
  return "down";
}

// ฟังก์ชันวิเคราะห์ความแข็งแกร่งของ trend
function analyzeTrendStrength(adxValues, trend) {
  const lastADX = adxValues.adx[adxValues.adx.length - 1];
  const adxDirection = isADXIncreasing(adxValues.adx.slice(-5));

  let strength = "";
  let direction = "";

  // วิเคราะห์ความแข็งแกร่งของ trend ตามค่า ADX
  if (lastADX < 20) {
    strength = "ไม่มีทิศทางชัดเจน";
  } else if (lastADX >= 20 && lastADX < 30) {
    strength = "แนวโน้มอ่อน";
  } else if (lastADX >= 30 && lastADX < 50) {
    strength = "แนวโน้มปานกลาง";
  } else if (lastADX >= 50 && lastADX < 75) {
    strength = "แนวโน้มแข็งแกร่ง";
  } else {
    strength = "แนวโน้มแข็งแกร่งมาก";
  }

  // วิเคราะห์ทิศทางของ trend
  if (trend === "up") {
    direction = "ขาขึ้น";
  } else if (trend === "down") {
    direction = "ขาลง";
  } else {
    direction = "sideway";
  }

  // วิเคราะห์การเพิ่มขึ้นหรือลดลงของ ADX
  const momentum = adxDirection === "increasing" ? "กำลังเร่งตัว" : "กำลังชะลอตัว";

  return `${strength} ${direction} (ADX = ${lastADX.toFixed(2)}) ${momentum}`;
}

// ฟังก์ชันตรวจสอบว่า ADX กำลังเพิ่มขึ้นหรือลดลง
function isADXIncreasing(adxList) {
  if (adxList.length < 2) return "stable";

  const lastADX = adxList[adxList.length - 1];
  const prevADX = adxList[adxList.length - 2];

  return lastADX > prevADX ? "increasing" : "decreasing";
}

// ฟังก์ชันจัดรูปแบบเวลาจาก timestamp
function formatTimeFromTimestamp(timestamp) {
  const date = new Date(timestamp);
  const hours = date.getHours().toString().padStart(2, '0');
  const minutes = date.getMinutes().toString().padStart(2, '0');

  return `${hours}:${minutes}`;
}

// เมื่อหน้าเว็บโหลดเสร็จแล้ว ทำการเรียกใช้ฟังก์ชันหลัก
document.addEventListener('DOMContentLoaded', () => {
  console.log('เริ่มวิเคราะห์ข้อมูลจาก Deriv...');
  fetchAndAnalyzeAssets().then(results => {
    console.log('ผลลัพธ์การวิเคราะห์:', results);
    displayResults(results);
  });

  // ตั้งเวลาให้ทำการวิเคราะห์ทุก 5 นาที
  setInterval(() => {
    console.log('อัพเดทการวิเคราะห์...');
    fetchAndAnalyzeAssets();
  }, 5 * 60 * 1000);
});

// ฟังก์ชันแสดงผลลัพธ์บนหน้าเว็บ
function displayResults(results) {
  const container = document.getElementById('results-container');
  if (!container) return;

  container.innerHTML = '';

  results.forEach(result => {
    const card = document.createElement('div');
    card.className = 'asset-card';

    // กำหนดสีตาม trend
    let trendColor = '#888'; // สีเทาสำหรับ sideway
    if (result.trend === 'up') {
      trendColor = '#4caf50'; // สีเขียวสำหรับ uptrend
    } else if (result.trend === 'down') {
      trendColor = '#f44336'; // สีแดงสำหรับ downtrend
    }

    card.style.borderLeft = `5px solid ${trendColor}`;

    card.innerHTML = `
      <h3>${result.assetCode}</h3>
      <p>เวลาล่าสุด: ${result.lastTimeCandle}</p>
      <p>EMA3: ${result.ema3.toFixed(4)} | EMA5: ${result.ema5.toFixed(4)}</p>
      <p>EMA Above: ${result.emaAbove}</p>
      <p>EMA5 Turn Point: ${result.isEma5TurnPoint}</p>
      <p>ADX: ${result.adx.toFixed(2)}</p>
      <p>Trend: <strong style="color: ${trendColor}">${result.trend}</strong></p>
      <p>การวิเคราะห์: ${result.trendAnalysis}</p>
    `;

    container.appendChild(card);
  });
}

// สำหรับการเชื่อมต่อกับ WebSocket API ของ Deriv ในสถานการณ์จริง
// จำเป็นต้องใช้โค้ดประมาณนี้:


function connectToDerivWebSocket() {
  const ws = new WebSocket('wss://ws.binaryws.com/websockets/v3');

  ws.onopen = function() {
    console.log('เชื่อมต่อกับ Deriv WebSocket สำเร็จ');

    // ขอข้อมูล authorize หากมี token (ถ้าต้องการดึงข้อมูลที่ต้องการการยืนยันตัวตน)
    // ws.send(JSON.stringify({ authorize: 'YOUR_API_TOKEN' }));

    // ขอข้อมูล ticks history
    requestTicksHistory(ws, 'R_100', 60);
  };

  ws.onmessage = function(msg) {
    const data = JSON.parse(msg.data);

    if (data.error) {
      console.error('Deriv API Error:', data.error);
      return;
    }

    if (data.history) {
      // แปลงข้อมูลจาก API เป็นรูปแบบที่ต้องการ
      const candles = formatCandleData(data.history);
      processCandles(candles);
    }
  };

  ws.onclose = function() {
    console.log('ปิดการเชื่อมต่อ Deriv WebSocket');
    // พยายามเชื่อมต่อใหม่
    setTimeout(connectToDerivWebSocket, 5000);
  };

  return ws;
}

function requestTicksHistory(ws, symbol, count) {
  ws.send(JSON.stringify({
    ticks_history: symbol,
    adjust_start_time: 1,
    count: count,
    end: 'latest',
    granularity: 60, // 1 minute
    style: 'candles'
  }));
}

function formatCandleData(history) {
  const { times, open, high, low, close } = history;

  return times.map((time, index) => ({
    time: time * 1000, // Convert to milliseconds
    open: open[index],
    high: high[index],
    low: low[index],
    close: close[index]
  }));
}