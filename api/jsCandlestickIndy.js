class Dog {
  constructor(name) {
    this.name = name;
  }

  // Shorthand method definition (นิยมใช้)
  bark() {
    console.log("Woof!");
  }

  
}


class Indy {
  constructor(curpair) {
    this.curpair = curpair ;
	this.candlesticks = null;
  }

  showCurPair() {
    console.log(this.curpair);	  
  }

  setRawdata(candleData) {
    this.candlesticks = JSON.parse(candleData) ;
    //console.log('set raw data success',this.candlesticks);
	console.log('set raw data success',this.candlesticks[0]);
	
  }

  

  calculateIndicators(candlesticks999) {
    // Helper function to calculate EMA
    function calculateEMA(prices, period) {
        const multiplier = 2 / (period + 1);
        let ema = prices[0];
        
        return prices.map((price, index) => {
            if (index === 0) return ema;
            ema = (price - ema) * multiplier + ema;
            return ema;
        });
    }

	function calculateColor(closeprices,openprices) {
    let color = [];
        for (let i = 0; i < closeprices.length; i++) {
			if (closeprices[i] > openprices[i]) {
				color.push('Green');
			}
			if (closeprices[i] < openprices[i]) {
				color.push('Red');
			}
			if (closeprices[i] === openprices[i]) {
				color.push('Gray');
			}
 	    }
		return color ;
	}

    // Helper function to calculate RSI
    function calculateRSI(prices, period = 14) {
        let gains = [];
        let losses = [];
        
        // Calculate price changes
        for (let i = 1; i < prices.length; i++) {
            const change = prices[i] - prices[i - 1];
            gains.push(change > 0 ? change : 0);
            losses.push(change < 0 ? -change : 0);
        }
        
        // Calculate average gain and loss
        let avgGain = gains.slice(0, period).reduce((a, b) => a + b, 0) / period;
        let avgLoss = losses.slice(0, period).reduce((a, b) => a + b, 0) / period;
        
        const rsi = [];
        rsi[period - 1] = 100 - (100 / (1 + avgGain / avgLoss));
        
        // Calculate RSI for remaining periods
        for (let i = period; i < prices.length; i++) {
            avgGain = (avgGain * (period - 1) + gains[i - 1]) / period;
            avgLoss = (avgLoss * (period - 1) + losses[i - 1]) / period;
            rsi[i] = 100 - (100 / (1 + avgGain / avgLoss));
        }
        
        return rsi;
    }

    // Helper function to calculate Bollinger Bands
    function calculateBB(prices, period = 20, stdDev = 2) {
        const sma = prices.map((_, index, array) => {
            if (index < period - 1) return null;
            return array.slice(index - period + 1, index + 1)
                       .reduce((sum, price) => sum + price, 0) / period;
        });
        
        const bands = prices.map((_, index, array) => {
            if (index < period - 1) return null;
            
            const slice = array.slice(index - period + 1, index + 1);
            const avg = sma[index];
            const variance = slice.reduce((sum, price) => sum + Math.pow(price - avg, 2), 0) / period;
            const std = Math.sqrt(variance);
            
            return {
                upper: avg + (stdDev * std),
                middle: avg,
                lower: avg - (stdDev * std)
            };
        });
        
        return bands;
    }

    // Helper function to calculate ATR
    function calculateATR(candles, period = 14) {
        const tr = candles.map((candle, index) => {
            if (index === 0) return candle.high - candle.low;
            
            const previousClose = candles[index - 1].close;
            return Math.max(
                candle.high - candle.low,
                Math.abs(candle.high - previousClose),
                Math.abs(candle.low - previousClose)
            );
        });
        
        let atr = [tr[0]];
        for (let i = 1; i < tr.length; i++) {
            atr[i] = ((atr[i - 1] * (period - 1)) + tr[i]) / period;
        }
        
        return atr;
    }

    // Extract close prices
    const closePrices = candlesticks999.map(candle => parseFloat(candle.close));
	const openPrices = candlesticks999.map(candle => parseFloat(candle.open));

	const diffPrices = candlesticks999.map(candle => parseFloat(candle.open)-parseFloat(candle.close));
   // console.log('Diff Price=', diffPrices);
	let colorList = [];
	for (let i=0;i<=diffPrices.length-1 ;i++ ) {
		if (diffPrices[i] > 0) {
          colorList.push('Green');
		}
		if (diffPrices[i] < 0) {
          colorList.push('Red');
		}
		if (diffPrices[i] === 0) {
          colorList.push('Gray');
		}	
	}
	//console.log('colorList =', colorList);
    let previuscolorList = [null];
	let previuscolorListBack2=[null,null];
	let previuscolorListBack3=[null,null,null];
	let previuscolorListBack4=[null,null,null,null];
	for (let i=1;i<=colorList.length-1 ;i++ ) {
        previuscolorList.push(colorList[i-1]);
		if (i >=2) {
          previuscolorListBack2.push(colorList[i-1]);
		}
		if (i >=3) {
          previuscolorListBack3.push(colorList[i-1]);
		}
		if (i >=4) {
          previuscolorListBack4.push(colorList[i-1]);
		}
	}
	console.log('previuscolorList =', previuscolorList);
	console.log('previuscolorList Back2 =', previuscolorListBack2);
	console.log('previuscolorList Back3 =', previuscolorListBack3);
	console.log('previuscolorList Back4 =', previuscolorListBack4);

	 
	
	 
	/*
	let colorList = diffPrices.map(price => {
     if (price > 0) {
       return 'Green';
      } else if (price < 0) {
       return 'Red';
      } else {
       return 'Gray';
     }
    });
	
	*/
	
	

    
    // Calculate indicators
	const color = calculateColor(closePrices,openPrices);
    const ema3 = calculateEMA(closePrices, 3);
    const ema5 = calculateEMA(closePrices, 5);
    const rsi = calculateRSI(closePrices);
    const bb = calculateBB(closePrices);
    const atr = calculateATR(candlesticks999);



    // Transform data into desired format
    return candlesticks999.map((candle, index) => {
        return {
            candleID: candle.epoch,
            timeframe: "1m",
            id: (index + 1).toString(),
            timestamp: candle.epoch.toString() ,
            timefrom_unix: new Date(candle.epoch * 1000).toISOString(),
            pip: ((candle.high - candle.low) * 100).toFixed(2),
            ema3: ema3[index]?.toFixed(2) || 0,
            ema5: ema5[index]?.toFixed(2) || 0,
            BB: bb[index] ? {
                upper: bb[index].upper?.toFixed(2) || 0,
                middle: bb[index].middle?.toFixed(2) || 0,
                lower: bb[index].lower?.toFixed(2) || 0
            } : 0,
            rsi: rsi[index]?.toFixed(2) || 0,
            atr: atr[index]?.toFixed(2) || 0,
            thisColor:colorList[index],
            previousColor:previuscolorList[index],
            previousColorBack2:previuscolorListBack2[index],
			previousColorBack3:previuscolorListBack3[index],
            previousColorBack4:previuscolorListBack4[index],
        };
    });
	
  } // end main Cal Indy function

  mainCalIndy() {

     let result = this.calculateIndicators(this.candlesticks);
	 return result;
	 //console.log('Cal Result',result);
	 
  }

} // end class clsIndy



class AdvancedIndicators {
    calculateAdvancedIndicators(data) {
        return data.map((current, index) => {
            const previous = index > 0 ? data[index - 1] : null;
            
            // Calculate all indicators
            const macd = this.calculateMACD(current);
            const ema3Slope = this.calculateEMA3Slope(current, previous);
            const ema5Slope = this.calculateEMA5Slope(current, previous);
            const emaAbove = this.determineEMAPosition(current);
            const emaCross = this.detectEMACross(current, previous);
            const TurnType = this.detectTurnPoint(data, index);
            const color = this.determineColor(current, previous);
            
            // New calculations
            const ema3SlopeDirection = this.determineEMASlopeDirection(ema3Slope);
            const ema5SlopeDirection = this.determineEMASlopeDirection(ema5Slope);
            const emaConflict = this.detectEMAConflict(current, color);
            const ema3Position = this.detectEMA3CandlePosition(current);

			//let previousColor = data[index].color;
			
			//if (index > 0) {			
             //let  previousColor = this.determinePreviousColor[current, previous] ;			
			let  previousColor = '999';//current.color ;			

			//}
			
			//	this.determinePreviousColor(current, previous);

            
            // Return enriched data object
            return {
                ...current,
                
                macd: macd.toFixed(2),
                ema3Slope: ema3Slope.toFixed(2),
                ema5Slope: ema5Slope.toFixed(2),
                ema3SlopeDirection,
                ema5SlopeDirection,
                emaAbove,
                emaCross,
                emaConflict,
                ema3Position,
                TurnType,
                previousColor:previousColor
            };
        });
    }
    
    // Existing methods remain the same
    calculateMACD(candle) {
        return parseFloat(candle.ema3) - parseFloat(candle.ema5);
    }
    
    calculateEMA3Slope(current, previous) {
        if (!previous) return 0;
        return parseFloat(current.ema3) - parseFloat(previous.ema3);
    }
    
    calculateEMA5Slope(current, previous) {
        if (!previous) return 0;
        return parseFloat(current.ema5) - parseFloat(previous.ema5);
    }
    
    determineEMAPosition(candle) {
        return parseFloat(candle.ema3) > parseFloat(candle.ema5) ? 'ema3Above' : 'ema5Above';
    }
    
    detectEMACross(current, previous) {
        if (!previous) return 'none';
        
        const currentEma3 = parseFloat(current.ema3);
        const currentEma5 = parseFloat(current.ema5);
        const previousEma3 = parseFloat(previous.ema3);
        const previousEma5 = parseFloat(previous.ema5);
        
        if (previousEma3 <= previousEma5 && currentEma3 > currentEma5) {
            return 'goldencross';
        }
        if (previousEma3 >= previousEma5 && currentEma3 < currentEma5) {
            return 'deathcross';
        }
        
        return 'none';
    }
    
    detectTurnPoint(data, currentIndex) {
        if (currentIndex < 2 || currentIndex >= data.length - 1) return 'none';
        
        const prev2 = parseFloat(data[currentIndex - 2].ema3);
        const prev1 = parseFloat(data[currentIndex - 1].ema3);
        const current = parseFloat(data[currentIndex].ema3);
        const next1 = parseFloat(data[currentIndex + 1].ema3);
        
        if (prev2 > prev1 && prev1 > current && current < next1) {
            return 'reverseUp';
        }
        if (prev2 < prev1 && prev1 < current && current > next1) {
            return 'reverseDown';
        }
        
        return 'none';
    }
    
    determineColor(current, previous) {
        if (!previous) return 'gray';
        
        const currentPrice = parseFloat(current.close);
        const previousPrice = parseFloat(previous.close);
        
        if (currentPrice > previousPrice) {
            return 'green';
        } else if (currentPrice < previousPrice) {
            return 'red';
        }
        return 'gray';
    }

	determinePreviousColor(current, previous) {

        console.log(current.color)
        
        if (!previous) return '-';       
        const previousColor = previous.color;

		return previousColor;
        
         
    }
    
    // New methods
    determineEMASlopeDirection(slope) {
        const threshold = 0.001; // Small threshold to determine parallel movement
        if (Math.abs(slope) < threshold) {
            return 'parallel';
        }
        return slope > 0 ? 'up' : 'down';
    }
    
    detectEMAConflict(candle, color) {
        const ema3 = parseFloat(candle.ema3);
        const ema5 = parseFloat(candle.ema5);
        
        // Conflict conditions
        if (ema3 > ema5 && color === 'red') {
            return true;
        }
        if (ema3 < ema5 && color === 'green') {
            return true;
        }
        return false;
    }
    
    detectEMA3CandlePosition(candle) {
        const ema3 = parseFloat(candle.ema3);
        const high = parseFloat(candle.high);
        const low = parseFloat(candle.low);
        const open = parseFloat(candle.open);
        const close = parseFloat(candle.close);
        
        if (ema3 > high) {
            return 'aboveHigh';
        } else if (ema3 <= high && ema3 > Math.max(open, close)) {
            return 'betweenHighOpen';
        } else if (ema3 <= Math.max(open, close) && ema3 >= Math.min(open, close)) {
            return 'betweenOpenClose';
        } else if (ema3 < Math.min(open, close) && ema3 > low) {
            return 'betweenCloseLow';
        } else {
            return 'belowLow';
        }
    }
} // end class


class AdvancedIndicatorsStep2 {

      FinalStep(data) {

        let PreviousTurnType = [null] ;
		let PreviousTurnTypeBack2 = [null,null] ;
        let PreviousTurnTypeBack3 = [null,null,null] ;
		let PreviousTurnTypeBack4 = [null,null,null,null] ;

	    for (let i=0;i<=data.length-1 ;i++ ) {
			let sss = data[i];
			if (i >= 1) {			
			  sss.PreviousTurnTypeBack2 = data[i-1].TurnType;
			  sss.macdconverValue = data[i].macd - data[i-1].macd ;
			  if (sss.macdconverValue > 0) {
                 sss.MACDConvergence = 'Diver';
			  }
			  if (sss.macdconverValue < 0) {
                 sss.MACDConvergence = 'Conver';
			  }
			  if (sss.macdconverValue === 0) {
                 sss.MACDConvergence = 'Pararell';
			  }			  
			}
			if (i >= 2) {			
			  sss.PreviousTurnTypeBack2 = data[i-2].TurnType;
			}
			if (i >= 3) {			
			  sss.PreviousTurnTypeBack3 = data[i-3].TurnType;
			}
			if (i >= 4) {			
			  sss.PreviousTurnTypeBack4 = data[i-4].TurnType;
			}
			data[i] = sss;


			console.log(sss)			
	    }

        return data;
   

	  }

}
/*
   // แบบที่ 2: ใช้ Optional Chaining และ Nullish Coalescing
  
    const getPreviousColors2 = (data, currentId) => {
    const idx = data.findIndex(item => item.id === currentId);
    return {
      previousColor: data[idx - 1]?.color ?? null,
      previousColorBack2: data[idx - 2]?.color ?? null,
      previousColorBack3: data[idx - 3]?.color ?? null,
      previousColorBack4: data[idx - 4]?.color ?? null
    };
  };
*/





