class DerivCandlestickFetcher {
  constructor(appId, options = {}) {
    // Configuration
    this.appId = appId;
    this.endpoint = options.endpoint || 'wss://ws.binaryws.com/websockets/v3?app_id=66726';
//    'wss://ws.binaryws.com/websockets/v3?app_id=66726' ;
    this.reconnectDelay = options.reconnectDelay || 3000;
    this.maxReconnectAttempts = options.maxReconnectAttempts || 5;
    
    // State variables
    this.socket = null;
    this.reconnectAttempts = 0;
    this.isConnected = false;
    this.subscriptions = new Map();
    this.callbacks = {
      onConnect: options.onConnect || (() => {}),
      onDisconnect: options.onDisconnect || (() => {}),
      onError: options.onError || (() => {}),
      onReconnecting: options.onReconnecting || (() => {})
    };

    // Initialize connection
    this.connect();
  }
  
  connect() {
    try {
      this.socket = new WebSocket(this.endpoint);
      
      this.socket.onopen = () => {
        console.log('Connection established');
        this.isConnected = true;
        this.reconnectAttempts = 0;
        this.authorize();
        this.callbacks.onConnect();
        
        // Resubscribe to existing subscriptions after reconnecting
        if (this.subscriptions.size > 0) {
          this.subscriptions.forEach((params, symbol) => {
            this.subscribeToCandlesticks(symbol, params.granularity, params.callback, false);
          });
        }
      };
      
      this.socket.onclose = (event) => {
        this.isConnected = false;
        console.log(`Connection closed: ${event.code} ${event.reason}`);
        this.callbacks.onDisconnect(event);
        this.attemptReconnect();
      };
      
      this.socket.onerror = (error) => {
        console.error('WebSocket error:', error);
        this.callbacks.onError(error);
      };
      
      this.socket.onmessage = (msg) => {
        this.handleMessage(msg);
      };
    } catch (error) {
      console.error('Failed to establish connection:', error);
      this.callbacks.onError(error);
      this.attemptReconnect();
    }
  }
  
  authorize() {
    if (!this.isConnected) return;
    
    const authorizeRequest = {
      authorize: this.appId
    };
    
    this.send(authorizeRequest);
  }
  
  attemptReconnect() {
    if (this.reconnectAttempts < this.maxReconnectAttempts) {
      this.reconnectAttempts++;
      console.log(`Attempting to reconnect... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
      this.callbacks.onReconnecting(this.reconnectAttempts, this.maxReconnectAttempts);
      
      setTimeout(() => {
        this.connect();
      }, this.reconnectDelay);
    } else {
      console.error('Max reconnection attempts reached');
    }
  }
  
  handleMessage(msg) {
    try {
      const response = JSON.parse(msg.data);
      
      // Handle candlestick responses
      if (response.msg_type === 'candles' && this.subscriptions.has(response.echo_req.ticks_history)) {
        const symbol = response.echo_req.ticks_history;
        const subscription = this.subscriptions.get(symbol);
        
        if (subscription && typeof subscription.callback === 'function') {
          subscription.callback(response);
        }
      }
      
      // Handle ohlc stream updates
      if (response.msg_type === 'ohlc' && this.subscriptions.has(response.ohlc.symbol)) {
        const symbol = response.ohlc.symbol;
        const subscription = this.subscriptions.get(symbol);
        
        if (subscription && typeof subscription.callback === 'function') {
          subscription.callback(response);
        }
      }
      
      // Handle errors
      if (response.error) {
        console.error('API error:', response.error);
        this.callbacks.onError(response.error);
      }
    } catch (error) {
      console.error('Error handling message:', error);
    }
  }
  
  send(request) {
    if (!this.isConnected) {
      console.error('Cannot send message: WebSocket is not connected');
      return;
    }
    
    try {
      this.socket.send(JSON.stringify(request));
    } catch (error) {
      console.error('Error sending message:', error);
    }
  }
  
  subscribeToCandlesticks(symbol, granularity, callback, isInitial = true) {
    if (!this.isConnected) {
      console.error('Cannot subscribe: WebSocket is not connected');
      return;
    }
    
    // Store subscription for reconnection purposes
    if (isInitial) {
      this.subscriptions.set(symbol, {
        granularity,
        callback
      });
    }
    
    // Initial history request
    const historyRequest = {
      ticks_history: symbol,
      adjust_start_time: 1,
      count: 50,
      end: 'latest',
      start: 1,
      style: 'candles',
      granularity: granularity
    };
    
    this.send(historyRequest);

    // Subscribe to updates
    const subscribeRequest = {
      ticks_history: symbol,
      adjust_start_time: 1,
      count: 1,
      end: 'latest',
      start: 1,
      style: 'candles',
      granularity: granularity,
      subscribe: 1
    };
    
    //this.send(subscribeRequest);
  }
  
  unsubscribeFromCandlesticks(symbol) {
    if (!this.isConnected || !this.subscriptions.has(symbol)) {
      return;
    }
    
    const unsubscribeRequest = {
      forget_all: ['candles', 'ohlc'],
      ticks_history: symbol
    };
    
    this.send(unsubscribeRequest);
    this.subscriptions.delete(symbol);
  }
  
  unsubscribeAll() {
    if (!this.isConnected) {
      return;
    }
    
    const unsubscribeRequest = {
      forget_all: ['candles', 'ohlc']
    };
    
    this.send(unsubscribeRequest);
    this.subscriptions.clear();
  }
  
  disconnect() {
    this.unsubscribeAll();
    
    if (this.socket) {
      this.socket.close();
      this.socket = null;
    }
    
    this.isConnected = false;
  }
}