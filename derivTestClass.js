

const symbolInput = document.getElementById('symbol');
const countInput = document.getElementById('count');
const granularitySelect = document.getElementById('granularity');
const startDateInput = document.getElementById('start-date');
const endDateInput = document.getElementById('end-date');
const fetchButton = document.getElementById('fetch-btn');
const stopButton = document.getElementById('stop-btn');
const statusBar = document.getElementById('status-bar');
const dataBody = document.getElementById('data-body');
const loadingIndicator = document.getElementById('loading');
const textarea = document.getElementById('candle-data');
//chart = new CandleStickChartWithEMA('chart-container');


// Set default dates (yesterday noon to today)
const yesterday = new Date();
yesterday.setDate(yesterday.getDate() - 1);
yesterday.setHours(12, 0, 0, 0);
startDateInput.valueAsDate = yesterday;

const today = new Date();
endDateInput.valueAsDate = today;

// API Connection variables
const apiUrl = 'wss://ws.binaryws.com/websockets/v3?app_id=66726';
let connection = null;
let subscriptionId = null;

// Format timestamp to readable date/time
function formatTime(timestamp) {
   const date = new Date(timestamp * 1000);
   return date.toLocaleString();
}

// Update status message
function updateStatus(message) {
   statusBar.textContent = `Status: ${message}`;
}


// Handle candles data display
function handleCandlesData(candles) {
   dataBody.innerHTML = '';
   candles.forEach(candle => {
      const row = document.createElement('tr');
      row.innerHTML = `
                        <td class="time-cell">${formatTime(candle.epoch)}</td>
                        <td>${candle.open}</td>
                        <td>${candle.high}</td>
                        <td>${candle.low}</td>
                        <td>${candle.close}</td>
                    `;
      dataBody.appendChild(row);
   });

   loadingIndicator.style.display = 'none';
   updateStatus(`Received ${candles.length} historical candles`);
}

// Handle OHLC updates
function handleOHLCUpdate(ohlc) {
   // Check if this candle already exists in the table
   const existingRows = dataBody.querySelectorAll('tr');
   let updated = false;

   for (let i = 0; i < existingRows.length; i++) {
      const firstCell = existingRows[i].querySelector('td');
      if (firstCell && firstCell.textContent === formatTime(ohlc.epoch)) {
         // Update existing row
         existingRows[i].innerHTML = `
                            <td class="time-cell">${formatTime(ohlc.epoch)}</td>
                            <td>${ohlc.open}</td>
                            <td>${ohlc.high}</td>
                            <td>${ohlc.low}</td>
                            <td>${ohlc.close}</td>
                        `;
         updated = true;
         break;
      }
   }

   // Add new row if candle doesn't exist
   if (!updated) {
      const row = document.createElement('tr');
      row.innerHTML = `
                        <td class="time-cell">${formatTime(ohlc.epoch)}</td>
                        <td>${ohlc.open}</td>
                        <td>${ohlc.high}</td>
                        <td>${ohlc.low}</td>
                        <td>${ohlc.close}</td>
                    `;
      dataBody.prepend(row);
   }
}

// Initialize WebSocket connection
function initConnection() {
   if (connection) {
      connection.close();
   }

   connection = new WebSocket(apiUrl);

   connection.onopen = () => {
      updateStatus('Connected to Deriv API');
      fetchButton.disabled = false;
   };

   connection.onclose = () => {
      updateStatus('Disconnected from Deriv API');
      fetchButton.disabled = false;
      stopButton.disabled = true;
      loadingIndicator.style.display = 'none';
   };

   connection.onerror = (error) => {
      updateStatus(`Error: ${error.message || 'Connection failed'}`);
      fetchButton.disabled = false;
      loadingIndicator.style.display = 'none';
   };

   connection.onmessage = (msg) => {
      const data = JSON.parse(msg.data);
      console.log('Received data:', data); // Debug log

      // Handle ticks history response
      if (data.msg_type === 'candles') {
         handleCandlesData(data.candles);
      }

      // Handle subscription stream
      if (data.msg_type === 'ohlc') {
         handleOHLCUpdate(data.ohlc);
         exampleData = JSON.stringify(data.ohlc);
         console.log('OHLC', exampleData);

         chart.updateChart(exampleData);
      }

      // Store subscription ID for cancellation
      if (data.subscription && data.subscription.id) {
         subscriptionId = data.subscription.id;
      }

      // Handle errors
      if (data.error) {
         updateStatus(`API Error: ${data.error.message}`);
         console.error('API Error:', data.error);
         loadingIndicator.style.display = 'none';
         fetchButton.disabled = false;
      }
   };
}

// Initialize connection on page load
initConnection();

// Event: Fetch button clicked
fetchButton.addEventListener('click', () => {
   const symbol = symbolInput.value;
   const count = parseInt(countInput.value);
   const granularity = parseInt(granularitySelect.value);

   if (!symbol || !count) {
      updateStatus('Please enter a valid symbol and count');
      return;
   }

   if (count > 5000) {
      updateStatus('Count cannot exceed 5000');
      return;
   }

   const startDate = startDateInput.valueAsDate;
   const endDate = endDateInput.valueAsDate;

   if (!startDate || !endDate) {
      updateStatus('Please select both start and end dates');
      return;
   }

   // Convert dates to Unix timestamps
   const startTime = Math.floor(startDate.getTime() / 1000);
   const endTime = Math.floor(endDate.getTime() / 1000);

   dataBody.innerHTML = '';
   fetchButton.disabled = true;
   loadingIndicator.style.display = 'inline-block';
   updateStatus(`Requesting OHLC data for ${symbol}...`);

   // Check if connection is closed
   if (connection.readyState !== WebSocket.OPEN) {
      initConnection();
      setTimeout(() => fetchData(symbol, count, granularity, startTime, endTime), 1000);
   } else {
      fetchData(symbol, count, granularity, startTime, endTime);
   }
});

function fetchData(symbol, count, granularity, startTime, endTime) {
   // Request OHLC data
   const request = {
      ticks_history: symbol,
      adjust_start_time: 1,
      count: count,
      granularity: granularity,
      start: startTime,
      end: endTime,
      style: 'candles',
      subscribe: 1 // Subscribe to updates
   };

   console.log('Sending request:', request); // Debug log
   connection.send(JSON.stringify(request));
   stopButton.disabled = false;
}

// Event: Stop button clicked
stopButton.addEventListener('click', () => {
   if (subscriptionId) {
      const request = {
         forget: subscriptionId
      };
      connection.send(JSON.stringify(request));
      updateStatus('Subscription stopped');
      subscriptionId = null;
   }
   stopButton.disabled = true;
});

// Handle page unload
window.addEventListener('beforeunload', () => {
   if (connection) {
      connection.close();
   }
});