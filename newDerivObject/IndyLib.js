/**
 * Calculate ADX (Average Directional Index) indicator
 * @param {Array} data - Array of price data objects with time, open, high, low, close
 * @param {number} period - Period for calculations (default: 14)
 * @returns {Array} - Array of ADX values
 */

function calculateEMA(data, period) {
        const k = 2 / (period + 1);
        let ema = data[0].close;
        const emaData = [];

        data.forEach((candle, index) => {
            ema = (candle.close * k) + (ema * (1 - k));
            emaData.push({
                time: candle.time,
                value: ema
            });
        });

        return emaData;
}

 // Calculate Bollinger Bands
function calculateBollingerBands(data, period = 20) {
            const bands = [];
            for (let i = period - 1; i < data.length; i++) {
                const slice = data.slice(i - period + 1, i + 1);
                const sum = slice.reduce((acc, val) => acc + val.close, 0);
                const sma = sum / period;

                const squaredDiffs = slice.map(candle => Math.pow(candle.close - sma, 2));
                const variance = squaredDiffs.reduce((acc, val) => acc + val, 0) / period;
                const stdDev = Math.sqrt(variance);

                bands.push({
                    time: data[i].time,
                    upper: sma + (2 * stdDev),
                    lower: sma - (2 * stdDev)
                });
            }
            return bands;
}


function calculateRSI(data, period = 14) {
        const rsiData = [];
        let gains = [];
        let losses = [];

        // Calculate price changes
        for (let i = 1; i < data.length; i++) {
            const change = data[i].close - data[i - 1].close;
            gains.push(change > 0 ? change : 0);
            losses.push(change < 0 ? -change : 0);
        }

        // Calculate initial RSI
        const avgGain = gains.slice(0, period).reduce((a, b) => a + b) / period;
        const avgLoss = losses.slice(0, period).reduce((a, b) => a + b) / period;

        let prevAvgGain = avgGain;
        let prevAvgLoss = avgLoss;

        for (let i = period; i < data.length; i++) {
            const currentGain = gains[i - 1];
            const currentLoss = losses[i - 1];

            const smoothedAvgGain = (prevAvgGain * (period - 1) + currentGain) / period;
            const smoothedAvgLoss = (prevAvgLoss * (period - 1) + currentLoss) / period;

            const rs = smoothedAvgGain / smoothedAvgLoss;
            const rsi = 100 - (100 / (1 + rs));

            rsiData.push({
                time: data[i].time,
                value: rsi
            });

            prevAvgGain = smoothedAvgGain;
            prevAvgLoss = smoothedAvgLoss;
        }

        return rsiData;
 }


function calculateADX999(data, period = 5) {
  if (!data || data.length < period + 1) {
    return [];
  }

  //alert(data.length);

  const smoothingPeriod = period;
  const trueRanges = [];
  const plusDMs = [];
  const minusDMs = [];


  // Calculate initial TR and DM values
  for (let i = 1; i < data.length; i++) {
    const current = data[i];
    const previous = data[i - 1];

    // True Range calculation
    const tr1 = current.high - current.low;
    const tr2 = Math.abs(current.high - previous.close);
    const tr3 = Math.abs(current.low - previous.close);
    const tr = Math.max(tr1, tr2, tr3);
    trueRanges.push(tr);

    // Directional Movement calculation
    const upMove = current.high - previous.high;
    const downMove = previous.low - current.low;

    let plusDM = 0;
    let minusDM = 0;

    if (upMove > downMove && upMove > 0) {
      plusDM = upMove;
    }

    if (downMove > upMove && downMove > 0) {
      minusDM = downMove;
    }

    plusDMs.push(plusDM);
    minusDMs.push(minusDM);
  }

  // Calculate smoothed TR, +DM, and -DM for the first period
  let smoothedTR = trueRanges.slice(0, period).reduce((sum, value) => sum + value, 0);
  let smoothedPlusDM = plusDMs.slice(0, period).reduce((sum, value) => sum + value, 0);
  let smoothedMinusDM = minusDMs.slice(0, period).reduce((sum, value) => sum + value, 0);

  const trs = [smoothedTR];
  const plusDMValues = [smoothedPlusDM];
  const minusDMValues = [smoothedMinusDM];

  // Calculate subsequent smoothed values using Wilder's smoothing method
  for (let i = period; i < trueRanges.length; i++) {
    smoothedTR = smoothedTR - (smoothedTR / smoothingPeriod) + trueRanges[i];
    smoothedPlusDM = smoothedPlusDM - (smoothedPlusDM / smoothingPeriod) + plusDMs[i];
    smoothedMinusDM = smoothedMinusDM - (smoothedMinusDM / smoothingPeriod) + minusDMs[i];

    trs.push(smoothedTR);
    plusDMValues.push(smoothedPlusDM);
    minusDMValues.push(smoothedMinusDM);
  }

  // Calculate +DI and -DI
  const plusDIs = [];
  const minusDIs = [];

  for (let i = 0; i < trs.length; i++) {
    const plusDI = (plusDMValues[i] / trs[i]) * 100;
    const minusDI = (minusDMValues[i] / trs[i]) * 100;

    plusDIs.push(plusDI);
    minusDIs.push(minusDI);
  }

  // Calculate DX
  const dxValues = [];

  for (let i = 0; i < plusDIs.length; i++) {
    const dx = (Math.abs(plusDIs[i] - minusDIs[i]) / (plusDIs[i] + minusDIs[i])) * 100;
    dxValues.push(dx);
  }

  // Calculate ADX using Wilder's smoothing on DX
  const adxValues = [];
  let adx = dxValues.slice(0, period).reduce((sum, value) => sum + value, 0) / period;
  adxValues.push(adx);

  for (let i = 1; i < dxValues.length - period + 1; i++) {
    adx = ((adxValues[i - 1] * (period - 1)) + dxValues[i + period - 1]) / period;
    adxValues.push(adx);
  }

  // Prepare the result array with corresponding time values
  const result = [];

  // ADX values start after 2*period-1 bars from the beginning of data
  const startIndex = 2 * period - 1;


  for (let i = 0; i < adxValues.length; i++) {
    result.push({
      time: data[i + startIndex].time,
      adx: adxValues[i],
      plusDI: plusDIs[i + period - 1],
      minusDI: minusDIs[i + period - 1]
    });
  }

  return result;
}



// You need at least 2*period-1 data points to get the first ADX value
//const adxResult = calculateADX(priceData, 14);
//console.log(adxResult);

//////////////////////

/**
 * Technical Indicators in Pure JavaScript
 * - Stochastic Oscillator
 * - Ichimoku Cloud
 */

// ============ STOCHASTIC OSCILLATOR ============
/**
 * Calculate Stochastic Oscillator
 * @param {Array} data - Array of price objects with high, low, close properties
 * @param {number} periodK - %K period (typically 14)
 * @param {number} periodD - %D period (typically 3)
 * @returns {Array} - Array of objects containing %K and %D values
 */
function calculateStochasticOscillator(data, periodK = 14, periodD = 3) {
  const result = [];

  // Need at least periodK data points to start
  if (data.length < periodK) {
    return result;
  }

  // Calculate %K values first
  for (let i = periodK - 1; i < data.length; i++) {
    // Get lowest low and highest high in the periodK lookback period
    let lowestLow = Number.MAX_VALUE;
    let highestHigh = Number.MIN_VALUE;

    for (let j = i - (periodK - 1); j <= i; j++) {
      lowestLow = Math.min(lowestLow, data[j].low);
      highestHigh = Math.max(highestHigh, data[j].high);
    }

    // Calculate %K
    const currentClose = data[i].close;
    const k = (highestHigh - lowestLow === 0) ? 0 :
              ((currentClose - lowestLow) / (highestHigh - lowestLow)) * 100;

    result.push({
      time: data[i].time,
      k: k
    });
  }

  // Now calculate %D (SMA of %K values)
  for (let i = periodD - 1; i < result.length; i++) {
    let sumK = 0;
    for (let j = i - (periodD - 1); j <= i; j++) {
      sumK += result[j].k;
    }

    const d = sumK / periodD;
    result[i].d = d;
  }

  // Remove entries without both K and D values
  return result.filter(item => item.k !== undefined && item.d !== undefined);
}

// ============ ICHIMOKU CLOUD ============
/**
 * Calculate Ichimoku Cloud
 * @param {Array} data - Array of price objects with time, open, high, low, close
 * @param {number} tenkanPeriod - Tenkan-sen period (typically 9)
 * @param {number} kijunPeriod - Kijun-sen period (typically 26)
 * @param {number} senkouSpanBPeriod - Senkou Span B period (typically 52)
 * @param {number} displacement - Displacement value (typically 26)
 * @returns {Array} - Array of Ichimoku values
 */
function calculateIchimokuCloud(data, tenkanPeriod = 9, kijunPeriod = 26, senkouSpanBPeriod = 52, displacement = 26) {
  const result = [];

  // Need enough data for calculation
  if (data.length < Math.max(tenkanPeriod, kijunPeriod, senkouSpanBPeriod)) {
    return result;
  }

  for (let i = Math.max(tenkanPeriod, kijunPeriod, senkouSpanBPeriod) - 1; i < data.length; i++) {
    // Tenkan-sen (Conversion Line): (highest high + lowest low) / 2 for tenkanPeriod
    let tenkanHighest = Number.MIN_VALUE;
    let tenkanLowest = Number.MAX_VALUE;
    for (let j = i - (tenkanPeriod - 1); j <= i; j++) {
      tenkanHighest = Math.max(tenkanHighest, data[j].high);
      tenkanLowest = Math.min(tenkanLowest, data[j].low);
    }
    const tenkanSen = (tenkanHighest + tenkanLowest) / 2;

    // Kijun-sen (Base Line): (highest high + lowest low) / 2 for kijunPeriod
    let kijunHighest = Number.MIN_VALUE;
    let kijunLowest = Number.MAX_VALUE;
    for (let j = i - (kijunPeriod - 1); j <= i; j++) {
      kijunHighest = Math.max(kijunHighest, data[j].high);
      kijunLowest = Math.min(kijunLowest, data[j].low);
    }
    const kijunSen = (kijunHighest + kijunLowest) / 2;

    // Senkou Span A (Leading Span A): (Tenkan-sen + Kijun-sen) / 2, plotted displacement periods ahead
    const senkouSpanA = (tenkanSen + kijunSen) / 2;

    // Senkou Span B (Leading Span B): (highest high + lowest low) / 2 for senkouSpanBPeriod, plotted displacement periods ahead
    let senkouBHighest = Number.MIN_VALUE;
    let senkouBLowest = Number.MAX_VALUE;
    for (let j = i - (senkouSpanBPeriod - 1); j <= i; j++) {
      senkouBHighest = Math.max(senkouBHighest, data[j].high);
      senkouBLowest = Math.min(senkouBLowest, data[j].low);
    }
    const senkouSpanB = (senkouBHighest + senkouBLowest) / 2;

    // Chikou Span (Lagging Span): Current closing price, plotted displacement periods behind
    const chikouSpan = data[i].close;

    result.push({
      time: data[i].time,
      tenkanSen: tenkanSen,
      kijunSen: kijunSen,
      senkouSpanA: senkouSpanA,
      senkouSpanB: senkouSpanB,
      chikouSpan: chikouSpan,
      // Add the future plotting times for senkou spans
      senkouSpanATime: i + displacement < data.length ? data[i + displacement].time : null,
      senkouSpanBTime: i + displacement < data.length ? data[i + displacement].time : null,
      // Add the past reference time for chikou span
      chikouSpanTime: i - displacement >= 0 ? data[i - displacement].time : null
    });
  }

  return result;
}

// ============ USAGE EXAMPLE ============
/*
// Example data array
const sampleData = [
  {
    "time": 1741055400,
    "open": 205.97,
    "high": 206.1344,
    "low": 205.97,
    "close": 206.0263
  },
  // Add more data points here...
];
*/
// Example usage

// ฟังก์ชันสำหรับหาจุดกลับตัวของ EMA
function findTurningPoints(emaData) {
  // ต้องมีอย่างน้อย 3 จุดเพื่อหาจุดกลับตัว
  if (emaData.length < 3) {
    return [];
  }

  const result = [];

  // จุดแรกไม่สามารถตรวจสอบได้ จึงใส่ N
  result.push({
    time: emaData[0].time,
    value: emaData[0].value,
    signal: "N"
  });

  // ตรวจสอบจุดที่ 2 ถึงจุดก่อนสุดท้าย
  for (let i = 1; i < emaData.length - 1; i++) {
    const prev = emaData[i - 1].value;
    const current = emaData[i].value;
    const next = emaData[i + 1].value;

    let signal = "N"; // ค่าเริ่มต้นคือไม่มีการกลับตัว

    // ตรวจสอบ TurnDown: ค่าปัจจุบันสูงกว่าค่าก่อนหน้าและหลังจาก
    if (current > prev && current > next) {
      signal = "TurnDown";
    }
    // ตรวจสอบ TurnUp: ค่าปัจจุบันต่ำกว่าค่าก่อนหน้าและหลังจาก
    else if (current < prev && current < next) {
      signal = "TurnUp";
    }

    result.push({
      time: emaData[i].time,
      value: emaData[i].value,
      signal: signal
    });
  }

  // จุดสุดท้ายไม่สามารถตรวจสอบได้ จึงใส่ N
  result.push({
    time: emaData[emaData.length - 1].time,
    value: emaData[emaData.length - 1].value,
    signal: "N"
  });

  return result;
}

/**
 * คำนวณ slope ของแต่ละแท่งเทียน
 * @param {Array} candleData - Array ของข้อมูลแท่งเทียน
 * @return {Array} - Array ของค่า slope ของแต่ละแท่ง
 */
function calculateCandleSlopes(candleData) {
  // ตรวจสอบว่ามีข้อมูลหรือไม่
  if (!candleData || candleData.length === 0) {
    return [];
  }

  // สร้าง array เปล่าสำหรับเก็บค่า slope
  const slopes = [];

  // คำนวณ slope สำหรับแต่ละแท่งเทียน
  for (const candle of candleData) {
    // วิธีที่ 1: คำนวณ slope จาก open ไป close (เทียบกับเวลา)
    // สมมติว่าแต่ละแท่งมีช่วงเวลาคงที่ (เช่น 1 หน่วย)
    const timeUnit = 1;
    const slope = (candle.close - candle.open) / timeUnit;

    slopes.push({
		time: candle.time,
		slope: slope.toFixed(4)
	 }
	);

    // วิธีที่ 2 (ถ้าต้องการ): คำนวณ slope โดยพิจารณาจาก high และ low ด้วย
    // const verticalRange = candle.high - candle.low;
    // const slopeAlternative = verticalRange / timeUnit;
    // slopes.push(slopeAlternative);
  }

  return slopes;
}

function calculateEMASlope(emaData) {
    // ต้องมีอย่างน้อย 2 จุดเพื่อคำนวณ slope
    if (emaData.length < 2) {
        return [];
    }

    const slopeData = [];

    for (let i = 1; i < emaData.length; i++) {
        const currentEMA = emaData[i].value;
        const previousEMA = emaData[i-1].value;
        const currentTime = emaData[i].time;
        const previousTime = emaData[i-1].time;

        // คำนวณ slope: การเปลี่ยนแปลงของ EMA หารด้วยการเปลี่ยนแปลงของเวลา
        const slope = (currentEMA - previousEMA) / (currentTime - previousTime);

        slopeData.push({
            time: currentTime,
            value: slope
        });
    }

    return slopeData;
}

function MainCallAllIndy(data) {


 const ema3 = calculateEMA(data,3) ;
 const ema5 = calculateEMA(data,5) ;
 const bb = calculateBollingerBands(data, period = 20) ;

 const rsi  = calculateRSI(data, period = 14) ;
 const ADX = calculateADX999(data, period = 3);

 const Candleslopes = calculateCandleSlopes(data) ;
 const emaSlopes = calculateEMASlope(ema5);
 //console.log('emaSlopes',emaSlopes)


  // Calculate Stochastic Oscillator with default periods (14, 3)
  const stochastic = calculateStochasticOscillator(data);
  // Calculate Ichimoku Cloud with default periods (9, 26, 52, 26)
  const ichimoku = calculateIchimokuCloud(data);

  const turningPoints = findTurningPoints(ema3);
  //alert('Finished');
  return {
    ema3 : ema3,
    ema5 : ema5,
    bb   : bb,
    rsi : rsi,
	adx: ADX ,
    stochastic: stochastic,
    ichimoku: ichimoku,
    slopes : emaSlopes,
    TurnList : turningPoints
  };
}

/*
// Export functions for use
if (typeof module !== 'undefined') {
  module.exports = {
    calculateStochasticOscillator,
    calculateIchimokuCloud,
    processData
  };
}
*/