//derivapi.js
/*
-connect = response.
-reconnect
-getAssetOpened(all curpair)
-getCandle History ที่ timeframe ต่างๆ
-เทรดในแบบต่างๆ
-Proposal
-authenticate ใช้ token
-subscribeToTime()

*/
class derivAPI {

	  constructor(appID,timeframe) {
		this.appID = appID ;
		this.timeframe = timeframe;
		this.AssetList = null;
		this.connectToderiv();
		this.curPair ='RT_100';


     }

	 sAlert() {
		 alert('ssdfsdfsdf');
	 }

	 authenticate() {
        return new Promise((resolve, reject) => {
            if (!this.token) {
                reject(new Error('API token is required'));
                return;
            }

            const authRequest = {
                authorize: this.token
            };

            this.socket.send(JSON.stringify(authRequest));

            // เพิ่ม handler สำหรับการตรวจสอบ response ของ authentication
            const authHandler = (response) => {
                if (response.error) {
                    reject(new Error(response.error.message));
                } else if (response.authorize) {
                    console.log('Successfully authenticated');
                    resolve(response.authorize);
                }
            };

            // เพิ่ม one-time listener สำหรับ authentication response
            const messageHandler = (msg) => {
                const response = JSON.parse(msg.data);
                if (response.msg_type === 'authorize') {
                    authHandler(response);
                    this.socket.removeEventListener('message', messageHandler);
                }
            };

            this.socket.addEventListener('message', messageHandler);
        });
     } // end authenticate


	 async connectToderiv() {
        this.ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id='+this.appID);
        this.ws.onopen = () => {
            // Subscribe to candlestick data
			//alert('Connected Success');
			this.getAssetsOpened();

        };



        this.ws.onmessage = (msg) => {

            const response = JSON.parse(msg.data);

            // Assets Open
            console.log('Received data:', response);
			if (response.msg_type === 'active_symbols') {
              console.log('Active symbols:', response.active_symbols);
			  this.AssetList = response.active_symbols;
			 // alert(this.AssetList.length);
			  for (i=0;i<=(this.AssetList.length)-1 ;i++ ) {

			  }


			  return;
            }

            const data = JSON.parse(msg.data);



			//console.log(data)

            if (data.candles) {
				// ข้อมูลย้อนหลัง (historical data)
				this.data = data.candles.map(candle => ({
					time: candle.epoch,
					open: candle.open,
					high: candle.high,
					low: candle.low,
					close: candle.close
				}));
				this.updateChart(this.data);
			} else if (data.ohlc) {
				// ข้อมูลอัปเดตแบบ real-time
				const newCandle = {
					time: data.ohlc.epoch,
					open: data.ohlc.open,
					high: data.ohlc.high,
					low: data.ohlc.low,
					close: data.ohlc.close
				};
				this.data.push(newCandle);
				this.updateChart(this.data);
			}
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        this.ws.onclose = () => {
            console.log('WebSocket disconnected');
        };
    } // end connectToDeriv

	getAssetsOpened() {
		// ส่งคำขอ active_symbols
		/*
       this.ws.send(JSON.stringify({
         active_symbols: 'brief',
         product_type: 'basic',
         msg_type: 'active_symbols'
       }));
	   */
	   const request = {
            active_symbols: 'brief',
            product_type: 'basic'
       };
	   this.ws.send(JSON.stringify(request));

	}

	getGranularity() {
        // Convert timeframe to seconds
			const timeframes = {
				'1m': 60,
				'5m': 300
			};
            return timeframes[this.timeframe];
    } // end getGranularity

	fetchCandles() {
        this.isProcessing = true;
        let asset = document.getElementById('symbolSelect').value;
        if (!asset) {
            console.log('No Assset');
            asset = 'R_10';
        } else {
            console.log('Candle Asset=', asset);
            //alert(asset) ;

        }
        const timeframe = parseInt(document.querySelector('input[name="timeframe"]:checked').value);

        const request = {
            "ticks_history": asset,
            "style": "candles",
            "granularity": timeframe * 60,
            "count": 15,
            "end": "latest"
        };

        this.socket.send(JSON.stringify(request));
        document.getElementById('status').textContent += 'Fetching candles at ' + new Date().toLocaleTimeString();
    }


}


