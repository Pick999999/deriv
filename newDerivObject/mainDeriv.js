import { attemptReconnect, subscribeToTime} from './request.js';
import { TViewChart } from './Chart.js'; 
import { mainResponse} from './Deriv_Response.js';

class Deriv {

    constructor() {
       // this.textareaId = textareaId;
        this.symbol = null;
		this.isSubscribe = null;
		this.token = 'lt5UMO6bNvmZQaR';
        this.ws = null;
        this.data = [];
		this.contractList = [];
		this.TradeList = [];

		this.connected = false;
		this.reconnectAttempts = 0 ;
        this.reconnectDelay = 0 ;
		this.maxReconnectAttempts = 1000 ;
        this.timeSubscription = null;
        this.connect();
    } 



	connect() {

        this.ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
        this.ws.onopen = () => {
            console.log('WebSocket connected');	
			//subscribeToTime(this);
			/*
			if (this.isSubscribe === false)			{
			  requestCandle(this.ws,this.symbol);
			}
			if (this.isSubscribe === true)			{
			  requestCandleWithSub(this.ws,this.symbol);
			}
			*/
        };
 
        this.ws.onmessage = (event) => {
            const response = JSON.parse(event.data);
            //console.log('Received data:', response);
			//console.log('Received Type:', response.msg_type);

			if (response.msg_type ==='candles') {
				this.data = [];
				//alert('yes-->', this.data.length);
				
			}
			mainResponse(response,this) ;
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        this.ws.onclose = () => {
			console.log('Disconnected from Deriv WebSocket');
            this.connected = false;
            attemptReconnect(this);
        };
    } // end connect

	




} // end class


export { Deriv };