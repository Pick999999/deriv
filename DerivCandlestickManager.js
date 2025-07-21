/**
 * DerivCandlestickManager - A class for fetching and displaying candlestick data from Deriv.com via WebSocket
 */
 //DerivCandlestickManager.js
class DerivCandlestickManager {
  /**
   * Constructor for the DerivCandlestickManager
   * @param {Object} options - Configuration options
   * @param {string} options.appId - Your Deriv API App ID
   * @param {string} options.endpoint - WebSocket endpoint (default: 'wss://ws.binaryws.com/websockets/v3')
   * @param {string} options.tableId - ID of the HTML table element to render data (optional)
   */
  constructor(options = {}) {
    this.appId = options.appId;
    this.endpoint = options.endpoint || 'wss://ws.binaryws.com/websockets/v3?app_id=66726';
    this.tableId = options.tableId || null;
    this.socket = null;
    this.isConnected = false;
    this.candlestickData = [];
    this.activeSubscriptions = {};
    this.requestCallbacks = {};
    this.forgetRequests = {};
    this.connectionPromise = null;
    this.reconnectAttempts = 0;
    this.maxReconnectAttempts = 5;
    this.reconnectInterval = 3000;
  }

  /**
   * Connect to the Deriv WebSocket server
   * @returns {Promise<boolean>} - Connection success status
   */
  connect() {
    // Return existing connection promise if already connecting
    if (this.connectionPromise && this.isConnected === false) {
      return this.connectionPromise;
    }

    // Return true if already connected
    if (this.isConnected && this.socket?.readyState === WebSocket.OPEN) {
      return Promise.resolve(true);
    }

    // Create new connection promise
    this.connectionPromise = new Promise((resolve, reject) => {
      try {
        this.socket = new WebSocket(this.endpoint);

        // Connection opened
        this.socket.addEventListener('open', (event) => {
          console.log('Connected to Deriv WebSocket server');
          this.isConnected = true;
          this.reconnectAttempts = 0;

          // Authenticate with app_id
          if (this.appId) {
            this._sendRequest({
              authorize: this.appId
            });
          }

          // Resolve the connection promise
          resolve(true);
          this.connectionPromise = null;
        });

        // Listen for messages
        this.socket.addEventListener('message', (event) => {
          try {
            const data = JSON.parse(event.data);
            this._handleMessage(data);
          } catch (err) {
            console.error('Error parsing WebSocket message:', err);
          }
        });

        // Connection closed
        this.socket.addEventListener('close', (event) => {
          console.log('Disconnected from Deriv WebSocket server');
          this.isConnected = false;

          // Attempt to reconnect
          this._attemptReconnect();
        });

        // Connection error
        this.socket.addEventListener('error', (error) => {
          console.error('WebSocket error:', error);
          this.isConnected = false;
          reject(error);
          this.connectionPromise = null;
        });

      } catch (error) {
        console.error('Failed to create WebSocket connection:', error);
        this.isConnected = false;
        reject(error);
        this.connectionPromise = null;
      }
    });

    return this.connectionPromise;
  }

  /**
   * Attempt to reconnect to the WebSocket server
   * @private
   */
  _attemptReconnect() {
    if (this.reconnectAttempts >= this.maxReconnectAttempts) {
      console.error('Max reconnection attempts reached');
      return;
    }

    this.reconnectAttempts++;
    console.log(`Attempting to reconnect (${this.reconnectAttempts}/${this.maxReconnectAttempts})...`);

    setTimeout(() => {
      this.connect().then(() => {
        // Resubscribe to active subscriptions
        for (const subscriptionId in this.activeSubscriptions) {
          const subscription = this.activeSubscriptions[subscriptionId];

          if (subscription.type === 'candles') {
            this.subscribeCandlestick({
              ticks_history: subscription.symbol,
              granularity: subscription.granularity,
              style: subscription.style,
              count: subscription.count,
              end: 'latest'
            });
          }
        }
      }).catch(error => {
        console.error('Reconnection failed:', error);
      });
    }, this.reconnectInterval);
  }

  /**
   * Disconnect from the WebSocket server
   */
  disconnect() {
    // Forget all active subscriptions
    for (const subscriptionId in this.activeSubscriptions) {
      this._sendForgetRequest(subscriptionId);
    }

    // Clear active subscriptions
    this.activeSubscriptions = {};

    // Close the socket
    if (this.socket) {
      this.socket.close();
      this.isConnected = false;
      this.socket = null;
    }
  }

  /**
   * Send a request to the Deriv WebSocket server
   * @param {Object} request - Request object
   * @param {Function} callback - Callback function for the response
   * @returns {Promise<Object>} - Response data
   * @private
   */
  _sendRequest(request) {
    if (!this.isConnected || !this.socket) {
      return Promise.reject(new Error('Not connected to WebSocket server'));
    }

    return new Promise((resolve, reject) => {
      // Add unique request id
      const reqId = Date.now();
      request.req_id = reqId;

      // Store callback for this request ID
      this.requestCallbacks[reqId] = {
        resolve,
        reject,
        timer: setTimeout(() => {
          if (this.requestCallbacks[reqId]) {
            delete this.requestCallbacks[reqId];
            reject(new Error('Request timeout'));
          }
        }, 30000) // 30 second timeout
      };

      // Send the request
      const messageStr = JSON.stringify(request);
      this.socket.send(messageStr);
    });
  }

  /**
   * Send a forget request to unsubscribe
   * @param {string} subscriptionId - Subscription ID to forget
   * @private
   */
  _sendForgetRequest(subscriptionId) {
    if (!this.isConnected || !this.socket) {
      return;
    }

    const request = {
      forget: subscriptionId
    };

    // Send the forget request
    const messageStr = JSON.stringify(request);
    this.socket.send(messageStr);

    // Store the forget request so we know to handle the response
    this.forgetRequests[subscriptionId] = true;
  }

  /**
   * Handle incoming WebSocket messages
   * @param {Object} data - Message data
   * @private
   */
  _handleMessage(data) {
    // Handle request callbacks
    if (data.req_id && this.requestCallbacks[data.req_id]) {
      const callback = this.requestCallbacks[data.req_id];

      // Clear timeout
      clearTimeout(callback.timer);

      // Check for errors
      if (data.error) {
        callback.reject(new Error(data.error.message || 'Unknown error'));
      } else {
        callback.resolve(data);
      }

      // Remove the callback
      delete this.requestCallbacks[data.req_id];
      return;
    }

    // Handle candles subscription data
    if (data.msg_type === 'candles' && data.subscription) {
      this._handleCandleUpdate(data);
      return;
    }

    // Handle history data
    if (data.msg_type === 'history' && data.candles) {
      this._handleHistoryData(data);
      return;
    }

    // Handle forget responses
    if (data.msg_type === 'forget' && this.forgetRequests[data.forget]) {
      delete this.forgetRequests[data.forget];

      // Remove from active subscriptions if this was a subscription
      if (this.activeSubscriptions[data.forget]) {
        delete this.activeSubscriptions[data.forget];
      }

      return;
    }
  }

  /**
   * Handle candle update message
   * @param {Object} data - Candle update data
   * @private
   */
  _handleCandleUpdate(data) {
    // Store subscription ID
    if (data.subscription && data.subscription.id) {
      const subId = data.subscription.id;

      // If this is a new subscription, store it
      if (!this.activeSubscriptions[subId]) {
        this.activeSubscriptions[subId] = {
          type: 'candles',
          symbol: data.echo_req.ticks_history,
          granularity: data.echo_req.granularity,
          style: data.echo_req.style,
          count: data.echo_req.count
        };
      }
    }

    // Process candle data
    if (data.candles && data.candles.length > 0) {
      const newCandle = data.candles[0];

      const candleData = {
        symbol: data.echo_req.ticks_history,
        timestamp: newCandle.epoch * 1000, // Convert to milliseconds
        dateTime: new Date(newCandle.epoch * 1000),
        open: parseFloat(newCandle.open),
        high: parseFloat(newCandle.high),
        low: parseFloat(newCandle.low),
        close: parseFloat(newCandle.close),
        volume: 0 // Deriv doesn't provide volume data
      };

      // Update the first candle or add a new one
      if (this.candlestickData.length > 0 &&
          this.candlestickData[0].timestamp === candleData.timestamp) {
        this.candlestickData[0] = candleData;
      } else {
        this.candlestickData.unshift(candleData);
      }

      // If we have a table ID, automatically update the table
      if (this.tableId) {
        this.renderToTable();
      }

      // Dispatch custom event
      const event = new CustomEvent('candleUpdate', { detail: candleData });
      document.dispatchEvent(event);
    }
  }

  /**
   * Handle historical data message
   * @param {Object} data - Historical candle data
   * @private
   */
  _handleHistoryData(data) {
    // Store subscription ID if available
    if (data.subscription && data.subscription.id) {
      const subId = data.subscription.id;

      // If this is a new subscription, store it
      if (!this.activeSubscriptions[subId]) {
        this.activeSubscriptions[subId] = {
          type: 'candles',
          symbol: data.echo_req.ticks_history,
          granularity: data.echo_req.granularity,
          style: data.echo_req.style,
          count: data.echo_req.count
        };
      }
    }

    // Clear existing data
    this.candlestickData = [];

    // Process history data
    if (data.candles && data.candles.length > 0) {
      // Process from newest to oldest
      const candles = [...data.candles].reverse();

      candles.forEach(candle => {
        this.candlestickData.push({
          symbol: data.echo_req.ticks_history,
          timestamp: candle.epoch * 1000, // Convert to milliseconds
          dateTime: new Date(candle.epoch * 1000),
          open: parseFloat(candle.open),
          high: parseFloat(candle.high),
          low: parseFloat(candle.low),
          close: parseFloat(candle.close),
          volume: 0 // Deriv doesn't provide volume data
        });
      });

      // If we have a table ID, automatically update the table
      if (this.tableId) {
        this.renderToTable();
      }

      // Dispatch custom event
      const event = new CustomEvent('historyUpdate', { detail: this.candlestickData });
      document.dispatchEvent(event);
    }
  }

  /**
   * Subscribe to candlestick data for a symbol
   * @param {Object} params - Subscription parameters
   * @param {string} params.symbol - The trading symbol (e.g., "R_100", "frxEURUSD", etc.)
   * @param {number} params.granularity - Candlestick interval in seconds (e.g., 60, 300, 900, 3600, etc.)
   * @param {number} params.count - Number of candles to fetch (default: 1000)
   * @param {boolean} params.subscribe - Whether to subscribe to updates (default: true)
   * @returns {Promise<Object>} - Subscription response
   */
  async subscribeCandlestick(params) {
    // Connect if not already connected
    if (!this.isConnected) {
      await this.connect();
    }

    // Validate parameters
    if (!params.symbol) {
      throw new Error('Symbol is required');
    }

    if (!params.granularity) {
      throw new Error('Granularity is required');
    }

    // Create request object
    const request = {
      ticks_history: params.symbol,
      adjust_start_time: 1,
      granularity: params.granularity,
      style: 'candles',
      count: params.count || 1000,
      end: 'latest'
    };

    // Add subscribe flag if not explicitly set to false
    if (params.subscribe !== false) {
      request.subscribe = 1;
    }

    // Send request
    try {
      const response = await this._sendRequest(request);
      return response;
    } catch (error) {
      console.error('Error subscribing to candlestick data:', error);
      throw error;
    }
  }

  /**
   * Unsubscribe from all active subscriptions
   */
  unsubscribeAll() {
    for (const subscriptionId in this.activeSubscriptions) {
      this._sendForgetRequest(subscriptionId);
    }
  }

  /**
   * Get available symbols from Deriv API
   * @returns {Promise<Array>} - Array of available symbols
   */
  async getAvailableSymbols() {
    // Connect if not already connected
    if (!this.isConnected) {
      await this.connect();
    }

    try {
      const response = await this._sendRequest({
        active_symbols: 'brief',
        product_type: 'basic'
      });

      return response.active_symbols || [];
    } catch (error) {
      console.error('Error fetching available symbols:', error);
      throw error;
    }
  }

  /**
   * Render candlestick data to an HTML table
   * @param {string} tableId - ID of the HTML table element (optional, overrides constructor setting)
   * @param {Array} data - Candlestick data to render (optional, uses stored data if not provided)
   * @returns {boolean} - Success status
   */
  renderToTable(tableId = null, data = null) {
    const targetTableId = tableId || this.tableId;
    const targetData = data || this.candlestickData;

    if (!targetTableId) {
      console.error('No table ID provided for rendering');
      return false;
    }

    if (!targetData || targetData.length === 0) {
      console.error('No data available for rendering');
      return false;
    }

    try {
      const table = document.getElementById(targetTableId);
      if (!table) {
        console.error(`Table with ID "${targetTableId}" not found`);
        return false;
      }

      // Clear existing table content
      table.innerHTML = '';

      // Create table header
      const thead = document.createElement('thead');
      const headerRow = document.createElement('tr');
      const headers = ['Date/Time', 'Open', 'High', 'Low', 'Close'];

      headers.forEach(headerText => {
        const th = document.createElement('th');
        th.textContent = headerText;
        headerRow.appendChild(th);
      });

      thead.appendChild(headerRow);
      table.appendChild(thead);

      // Create table body
      const tbody = document.createElement('tbody');

      targetData.forEach(candle => {
        const row = document.createElement('tr');

        // Format date and time
        const dateTimeCell = document.createElement('td');
        dateTimeCell.textContent = candle.dateTime.toLocaleString();
        row.appendChild(dateTimeCell);

        // Add OHLC data
        [candle.open, candle.high, candle.low, candle.close].forEach(value => {
          const cell = document.createElement('td');
          cell.textContent = value.toLocaleString(undefined, {
            minimumFractionDigits: 5,
            maximumFractionDigits: 5
          });
          row.appendChild(cell);
        });

        tbody.appendChild(row);
      });

      table.appendChild(tbody);
      return true;
    } catch (error) {
      console.error('Error rendering table:', error);
      return false;
    }
  }

  /**
   * Create and return an HTML table element with candlestick data
   * @param {Array} data - Candlestick data to render (optional, uses stored data if not provided)
   * @returns {HTMLTableElement} - The created table element
   */
  createTable(data = null) {
    const targetData = data || this.candlestickData;

    if (!targetData || targetData.length === 0) {
      console.error('No data available for table creation');
      return null;
    }

    try {
      const table = document.createElement('table');
      table.className = 'candlestick-table';

      // Create table header
      const thead = document.createElement('thead');
      const headerRow = document.createElement('tr');
      const headers = ['Date/Time', 'Open', 'High', 'Low', 'Close'];

      headers.forEach(headerText => {
        const th = document.createElement('th');
        th.textContent = headerText;
        headerRow.appendChild(th);
      });

      thead.appendChild(headerRow);
      table.appendChild(thead);

      // Create table body
      const tbody = document.createElement('tbody');

      targetData.forEach(candle => {
        const row = document.createElement('tr');

        // Add date/time
        const dateTimeCell = document.createElement('td');
        dateTimeCell.textContent = candle.dateTime.toLocaleString();
        row.appendChild(dateTimeCell);

        // Add OHLC data
        [candle.open, candle.high, candle.low, candle.close].forEach(value => {
          const cell = document.createElement('td');
          cell.textContent = value.toLocaleString(undefined, {
            minimumFractionDigits: 5,
            maximumFractionDigits: 5
          });
          row.appendChild(cell);
        });

        tbody.appendChild(row);
      });

      table.appendChild(tbody);
      return table;
    } catch (error) {
      console.error('Error creating table:', error);
      return null;
    }
  }

  /**
   * Export data to CSV format
   * @returns {string} - CSV formatted data
   */
  exportToCSV() {
    if (!this.candlestickData || this.candlestickData.length === 0) {
      return '';
    }

    const headers = ['Timestamp', 'Date/Time', 'Open', 'High', 'Low', 'Close'];
    const csvRows = [headers.join(',')];

    for (const candle of this.candlestickData) {
      const row = [
        Math.floor(candle.timestamp / 1000), // Convert back to seconds for compatibility
        candle.dateTime.toISOString(),
        candle.open,
        candle.high,
        candle.low,
        candle.close
      ];
      csvRows.push(row.join(','));
    }

    return csvRows.join('\n');
  }
}

// Export the class for use in other files
if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
  module.exports = DerivCandlestickManager;
} else {
  // For browser environments
  window.DerivCandlestickManager = DerivCandlestickManager;
}

