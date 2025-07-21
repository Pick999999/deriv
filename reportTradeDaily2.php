<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Deriv WebSocket Trades Fetcher</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    #output { margin-top: 20px; }
    .trade { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
    .error { color: red; }
  </style>
</head>
<body>
  <h2>Deriv WebSocket API - Today's Trades</h2>
  <p>Enter your API token and click "Fetch Trades" to retrieve all trades executed today (June 24, 2025).</p>
  <input type="text" id="apiToken" placeholder="Enter your API token" size="40">
  <button onclick="fetchTrades()">Fetch Trades</button>
  <div id="output"></div>

  <script>
    let ws = null;
    const outputDiv = document.getElementById('output');

    function logMessage(message, isError = false) {
      console.log(message);
      const p = document.createElement('p');
      p.textContent = message;
      if (isError) p.className = 'error';
      outputDiv.appendChild(p);
    }

    function connectWebSocket() {
      ws = new WebSocket('wss://ws.derivws.com/websockets/v3?app_id=1089');
      
      ws.onopen = () => {
        logMessage('WebSocket connection established.');
      };

      ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        handleWebSocketMessage(data);
      };

      ws.onerror = (error) => {
        logMessage('WebSocket error: ' + JSON.stringify(error), true);
      };

      ws.onclose = () => {
        logMessage('WebSocket connection closed.');
      };
    }

    function handleWebSocketMessage(data) {
      if (data.error) {
        logMessage('Error: ' + data.error.message + ' (Code: ' + data.error.code + ')', true);
        return;
      }

      switch (data.msg_type) {
        case 'authorize':
          logMessage('Authorization successful.');
          requestRecentTrades();
          break;
        case 'statement':
          filterAndDisplayTodayTrades(data.statement.transactions);
          break;
        default:
          console.log('Received message:', data);
      }
    }

    function requestRecentTrades() {
      const statementRequest = {
        statement: 1,
        description: 1,
        req_id: 2
      };

      ws.send(JSON.stringify(statementRequest));
      logMessage('Requested recent trades.');
    }

    function filterAndDisplayTodayTrades(transactions) {
      outputDiv.innerHTML = '<h3>Trades Executed Today (June 24, 2025)</h3>';
      
      // Define today's boundaries in Unix seconds (Asia/Bangkok, UTC+7)
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Start of day
      const startOfDay = Math.floor(today.getTime() / 1000); // Convert to Unix seconds
      const endOfDay = startOfDay + 86400 - 1; // End of day (23:59:59)

      // Filter for today's trades (buy/sell actions)
      const todayTrades = transactions.filter(trade => 
        trade.transaction_time >= startOfDay && 
        trade.transaction_time <= endOfDay && 
        (trade.action_type === 'buy' || trade.action_type === 'sell')
      );

      if (todayTrades.length === 0) {
        logMessage('No trades found for today.');
        return;
      }

      todayTrades.forEach(trade => {
        const tradeDiv = document.createElement('div');
        tradeDiv.className = 'trade';
        tradeDiv.innerHTML = `
          <strong>Transaction ID:</strong> ${trade.transaction_id}<br>
          <strong>Action:</strong> ${trade.action_type}<br>
          <strong>Contract ID:</strong> ${trade.contract_id || 'N/A'}<br>
          <strong>Amount:</strong> ${trade.amount}<br>
          <strong>Currency:</strong> ${trade.currency}<br>
          <strong>Description:</strong> ${trade.description || 'N/A'}<br>
          <strong>Time:</strong> ${new Date(trade.transaction_time * 1000).toLocaleString('th-TH', { timeZone: 'Asia/Bangkok' })}
        `;
        outputDiv.appendChild(tradeDiv);
        console.log('Trade:', trade);
      });
      logMessage(`Displayed ${todayTrades.length} trades for today.`);
    }

    function fetchTrades() {
      const apiToken = document.getElementById('apiToken').value.trim();
      if (!apiToken) {
        logMessage('Please enter a valid API token.', true);
        return;
      }

      if (ws && ws.readyState === WebSocket.OPEN) {
        ws.close();
      }

      connectWebSocket();

      ws.onopen = () => {
        logMessage('WebSocket connection established.');
        const authRequest = {
          authorize: apiToken,
          req_id: 1
        };
        ws.send(JSON.stringify(authRequest));
        logMessage('Sent authorization request.');
      };
    }

    // Keep connection alive with periodic pings
    setInterval(() => {
      if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({ ping: 1 }));
      }
    }, 30000);
  </script>
</body>
</html>

https://thepapers.in/deriv/formasset.php
https://thepapers.in/deriv/viewanalysis.php


