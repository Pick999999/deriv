function analyzeCandlesticks(candleData) {
    // Helper function to calculate EMA
    function calculateEMA(data, period) {
        const k = 2 / (period + 1);
        let ema = data[0].close;
        return data.map((candle, i) => {
            ema = (candle.close * k) + (ema * (1 - k));
            return ema;
        });
    }

    // Helper function to calculate RSI
    function calculateRSI(data, period = 14) {
        let gains = [];
        let losses = [];

        // Calculate price changes
        for(let i = 1; i < data.length; i++) {
            const change = data[i].close - data[i-1].close;
            gains.push(change > 0 ? change : 0);
            losses.push(change < 0 ? -change : 0);
        }

        // Calculate initial average gain and loss
        const avgGain = gains.slice(0, period).reduce((a, b) => a + b, 0) / period;
        const avgLoss = losses.slice(0, period).reduce((a, b) => a + b, 0) / period;

        let rsi = [];
        let currentGain = avgGain;
        let currentLoss = avgLoss;

        // First RSI value
        rsi.push(100 - (100 / (1 + currentGain / (currentLoss || 1))));

        // Calculate subsequent RSI values
        for(let i = period + 1; i < data.length; i++) {
            currentGain = ((currentGain * (period - 1)) + (gains[i-1] || 0)) / period;
            currentLoss = ((currentLoss * (period - 1)) + (losses[i-1] || 0)) / period;
            rsi.push(100 - (100 / (1 + currentGain / (currentLoss || 1))));
        }

        // Pad initial values with empty strings
        return Array(period).fill("").concat(rsi);
    }

    // Helper function to get candle color
    function getCandleColor(candle) {
        if (candle.close > candle.open) return "Green";
        if (candle.close < candle.open) return "Red";
        return "Equal";
    }

    // Helper function to detect turn points
    function detectTurnPoints(emaValues) {
        return emaValues.map((value, i, arr) => {
            if (i < 2 || i >= arr.length - 1) return "";
            if (arr[i-1] > value && arr[i+1] > value) return "TurnDown";
            if (arr[i-1] < value && arr[i+1] < value) return "TurnUp";
            return "";
        });
    }

    // Helper function to calculate slope
    function calculateSlope(values, index) {
        if (index < 1) return 0;
        return values[index] - values[index - 1];
    }

    // Helper function to get slope direction
    function getSlopeDirection(slope) {
        if (Math.abs(slope) < 0.0001) return "Pararell";
        return slope > 0 ? "Up" : "Down";
    }

    // Helper function to count candle colors between turn points
    function countCandleColorsBetweenTurnPoints(candleData, turnPoints, currentIndex) {
        let counts = { Green: 0, Red: 0, Equal: 0 };
        let lastTurnPointIndex = -1;

        // Find the last turn point before current index
        for(let i = currentIndex - 1; i >= 0; i--) {
            if(turnPoints[i]) {
                lastTurnPointIndex = i;
                break;
            }
        }

        // If no previous turn point found, return zeros
        if(lastTurnPointIndex === -1) return counts;

        // Count colors between last turn point and current index
        for(let i = lastTurnPointIndex; i <= currentIndex; i++) {
            const color = getCandleColor(candleData[i]);
            counts[color]++;
        }

        return counts;
    }

    // Calculate EMAs
    const ema3 = calculateEMA(candleData, 3);
    const ema5 = calculateEMA(candleData, 5);
    const ema7 = calculateEMA(candleData, 7);



    // Calculate RSI
    const rsiValues = calculateRSI(candleData);

    // Detect turn points
    const ema3TurnPoints = detectTurnPoints(ema3);
    const ema5TurnPoints = detectTurnPoints(ema5);

    return candleData.map((candle, i) => {
        // Convert epoch to time
        const date = new Date(candle.epoch * 1000);
        const minuteNo = `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;

        // Calculate slopes
        const ema3Slope = calculateSlope(ema3, i);
        const ema5Slope = calculateSlope(ema5, i);
        const ema7Slope = calculateSlope(ema7, i);

        // Get previous candle colors
        const currentColor = getCandleColor(candle);
        const prevColor1 = i > 0 ? getCandleColor(candleData[i-1]) : "";
        const prevColor2 = i > 1 ? getCandleColor(candleData[i-2]) : "";
        const prevColor3 = i > 2 ? getCandleColor(candleData[i-3]) : "";

        // Calculate EMA crossovers
        const ema3ema5Cross = ema3[i] > ema5[i] && ema3[i-1] <= ema5[i-1] ? "Golden Cross" :
                             ema3[i] < ema5[i] && ema3[i-1] >= ema5[i-1] ? "Death Cross" : "";
        const ema5ema7Cross = ema5[i] > ema7[i] && ema5[i-1] <= ema7[i-1] ? "Golden Cross" :
                             ema5[i] < ema7[i] && ema5[i-1] >= ema7[i-1] ? "Death Cross" : "";

        // Calculate distance from last turn points
        let ema3TurnDistance = 0;
        let ema5TurnDistance = 0;
        for(let j = i; j >= 0; j--) {
            if(ema3TurnPoints[j]) {
                ema3TurnDistance = i - j;
                break;
            }
        }
        for(let j = i; j >= 0; j--) {
            if(ema5TurnPoints[j]) {
                ema5TurnDistance = i - j;
                break;
            }
        }

		if (ema3[i].toFixed(5) > ema5[5].toFixed(5) ){
			emaAbove = '3';
		}
		if (ema3[i].toFixed(5) < ema5[5].toFixed(5) ){
			emaAbove = '5';
		}
		if (ema3[i].toFixed(4) == ema5[5].toFixed(4) ){
			emaAbove = 'EQ';
		}



        // Count candle colors between turn points for EMA3
        const colorCounts = countCandleColorsBetweenTurnPoints(candleData, ema3TurnPoints, i);


        return {
            CandleID: candle.CandleID,
            MinuteNo: minuteNo,
            ema3: parseFloat(ema3[i].toFixed(6)),
            ema5: parseFloat(ema5[i].toFixed(6)),
            ema7: parseFloat(ema7[i].toFixed(6)),
            rsi: typeof rsiValues[i] === 'number' ? rsiValues[i].toFixed(2) : "",
            "thiscandleColor": currentColor,
            "prevCandleColor1": prevColor1,
            "prevCandleColor2": prevColor2,
            "prevCandleColor3": prevColor3,
            "ema3_ema5_diff": parseFloat((ema3[i] - ema5[i]).toFixed(6)),
            "ema5_ema7_diff": parseFloat((ema5[i] - ema7[i]).toFixed(6)),
            "emaAbove" : emaAbove,
            "ema3TurnPoint": ema3TurnPoints[i],
            "ema5TurnPoint": ema5TurnPoints[i],
            "ema7TurnPoint": detectTurnPoints(ema7)[i],
            "prevEma3TurnPoint": i > 0 ? ema3TurnPoints[i-1] : "",
            "prevEma5TurnPoint": i > 0 ? ema5TurnPoints[i-1] : "",
            "ema3SlopeValue": parseFloat(ema3Slope.toFixed(4)),
            "ema5SlopeValue": parseFloat(ema5Slope.toFixed(4)),
            "ema7SlopeValue": parseFloat(ema7Slope.toFixed(4)),
            "ema3SlopeDirection": getSlopeDirection(ema3Slope),
            "ema5SlopeDirection": getSlopeDirection(ema5Slope),
            "ema7SlopeDirection": getSlopeDirection(ema7Slope),
            "ema3_ema5_crossType": ema3ema5Cross,
            "ema5_ema7_crossType": ema5ema7Cross,
            "distanceFromLastEma3Turn": ema3TurnDistance,
            "distanceFromLastEma5Turn": ema5TurnDistance,
            "greenCandleCount": colorCounts.Green,
            "redCandleCount": colorCounts.Red,
            "equalCandleCount": colorCounts.Equal
        };
    });
}

function MainAnaly() {
/*
{
    "CandleID": "9",  // Unique identifier for each candle
    "MinuteNo": "06:33",  // Time stamp in HH:mm format
    "ema3": "2662.38",  // 3-period Exponential Moving Average
    "ema5": "2662.40",  // 5-period Exponential Moving Average
    "ema7": "2662.40",  // 7-period Exponential Moving Average
    "rsi": "",  // Relative Strength Index
    "candleColor": "Red",  // Current candle color (was: สีของแท่งเทียน)
    "prevCandleColor1": "Red",  // Previous candle color (was: สีของแท่งเทียน ย้อนหลังไป 1 แท่ง)
    "prevCandleColor2": "Green",  // Color of candle 2 periods ago (was: สีของแท่งเทียน ย้อนหลังไป 2 แท่ง)
    "prevCandleColor3": "Green",  // Color of candle 3 periods ago (was: สีของแท่งเทียน ย้อนหลังไป 3 แท่ง)
    "ema3_ema5_diff": "-0.02",  // Difference between EMA3 and EMA5 (was: ema3-ema5)
    "ema5_ema7_diff": "0.00",  // Difference between EMA5 and EMA7 (was: ema5-ema7)
    "ema3TurnPoint": "TurnDown",  // EMA3 turning point type (was: ประเภทจุดกลับตัวของ ema3)
    "ema5TurnPoint": "TurnDown",  // EMA5 turning point type (was: ประเภทจุดกลับตัวของ ema5)
    "ema7TurnPoint": "TurnDown",  // EMA7 turning point type (was: ประเภทจุดกลับตัวของ ema7)
    "prevEma3TurnPoint": "TurnUp",  // Previous EMA3 turning point (was: ประเภทจุดกลับตัวของ ema3 ย้อนหลังไป 1 แท่ง)
    "prevEma5TurnPoint": "TurnUp",  // Previous EMA5 turning point (was: ประเภทจุดกลับตัวของ ema5 ย้อนหลังไป 1 แท่ง)
    "ema3SlopeValue": "-0.12",  // Slope value of EMA3 (was: Slope Value ของ ema3)
    "ema5SlopeValue": "-0.07",  // Slope value of EMA5 (was: Slope Value ของ ema5)
    "ema7SlopeValue": "-0.05",  // Slope value of EMA7 (was: Slope Value ของ ema7)
    "ema3SlopeDirection": "Down",  // Direction of EMA3 slope (was: Slope Direction ของ ema3)
    "ema5SlopeDirection": "Down",  // Direction of EMA5 slope (was: Slope Direction ของ ema5)
    "ema7SlopeDirection": "Down",  // Direction of EMA7 slope (was: Slope Direction ของ ema7)
    "ema3_ema5_crossType": "Death Cross",  // Type of crossover between EMA3 and EMA5 (was: เป็นจุดตัดกันของ ema3,ema5แบบไหน)
    "ema5_ema7_crossType": "",  // Type of crossover between EMA5 and EMA7 (was: เป็นจุดตัดกันของ ema5,ema7แบบไหน)
    "distanceFromLastEma3Turn": 0,  // Number of candles since last EMA3 turn point (was: ระยะห่างจากจุดกลับตัวของ ema3 จุดสุดท้าย)
    "distanceFromLastEma5Turn": 0,  // Number of candles since last EMA5 turn point (was: ระยะห่างจากจุดกลับตัวของ ema5 จุดสุดท้าย)
    "greenCandleCount": 0,  // Count of green candles (was: จำนวนแท่งเทียนสีเขียว)
    "redCandleCount": 2,  // Count of red candles (was: จำนวนแท่งเทียนสีแดง)
    "equalCandleCount": 0  // Count of neutral candles (was: จำนวนแท่งเทียน Equal)
}

*/

	// Example usage:
	//const candleData =  [{"close":2662.31,"epoch":1736378640,"high":2662.49,"low":2662.29,"open":2662.34},{"close":2662.39,"epoch":1736378700,"high":2662.41,"low":2662.29,"open":2662.31},{"close":2662.29,"epoch":1736378760,"high":2662.39,"low":2662.27,"open":2662.39},{"close":2662.37,"epoch":1736378820,"high":2662.37,"low":2662.29,"open":2662.29},{"close":2662.39,"epoch":1736378880,"high":2662.41,"low":2662.31,"open":2662.39},{"close":2662.38,"epoch":1736378940,"high":2662.4,"low":2662.34,"open":2662.39},{"close":2662.41,"epoch":1736379000,"high":2662.41,"low":2662.32,"open":2662.38},{"close":2662.58,"epoch":1736379060,"high":2662.69,"low":2662.38,"open":2662.41},{"close":2662.5,"epoch":1736379120,"high":2662.59,"low":2662.45,"open":2662.58},{"close":2662.26,"epoch":1736379180,"high":2662.5,"low":2662.2,"open":2662.5},{"close":2662.45,"epoch":1736379240,"high":2662.58,"low":2662.26,"open":2662.26},{"close":2662.86,"epoch":1736379300,"high":2662.87,"low":2662.48,"open":2662.48},{"close":2662.9,"epoch":1736379360,"high":2662.93,"low":2662.79,"open":2662.86},{"close":2662.89,"epoch":1736379420,"high":2663.09,"low":2662.88,"open":2662.9},{"close":2662.89,"epoch":1736379480,"high":2662.89,"low":2662.89,"open":2662.89}]
	candleDataStr = document.getElementById("resultRawData").innerHTML ;
	candleData = JSON.parse(candleDataStr);



    for (i=0;i<=candleData.length-1 ;i++ ) {
		candleData[i]['CandleID'] = i ;

    }
	const analysis = analyzeCandlesticks(candleData);
	console.log(analysis);
	document.getElementById("resultAnalysis").innerHTML = JSON.stringify(analysis);
	suggestAction = getActionForTrade999();
	document.getElementById("resultSuggestTrade").innerHTML = suggestAction;
	if (document.getElementById("autoTrade").checked ) {
		placeOrder(suggestAction);
	}



} // end func