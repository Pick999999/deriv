class Indy {

    constructor(curPair,timeframe) {
		this.curPair = curPair;
		this.timeframe = timeframe;

		alert(this.curPair);		
    }
	calculateEMA(data, period) {
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

} 