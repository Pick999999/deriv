function timesubscript_json(second99) {

	     return   JSON.stringify({
                    "time": second99
                  });

} // end func

function Candles_Hist_json(asset,timeframe,totalCandle) {

         const request = {
                  "ticks_history": asset,
                  "style": "candles",
                  "granularity": timeframe * 60,
                  "count": totalCandle,
                  "end": "latest"
         };

		 return JSON.stringify(request) ;

} // end func



export { timesubscript_json,Candles_Hist_json } 
