<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv.com WebSocket API Tester</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        #connectBtn {
            background-color: #2196F3;
        }
        #connectBtn:hover {
            background-color: #0b7dda;
        }
        #disconnectBtn {
            background-color: #f44336;
        }
        #disconnectBtn:hover {
            background-color: #da190b;
        }
        .status {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .connected {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .disconnected {
            background-color: #f2dede;
            color: #a94442;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h1>Deriv.com WebSocket API Tester</h1>
    
    <div class="container">
        <div id="status" class="status disconnected">
            Status: Disconnected
        </div>
        
        <div class="button-group">
            <button id="connectBtn">Connect</button>
            <button id="disconnectBtn" disabled>Disconnect</button>
            <button id="sendBtn" disabled>Send Request</button>
            <button id="clearBtn">Clear</button>
        </div>
        
        <div>
            <label for="requestText">Request (JSON):</label>
            <textarea id="requestText" placeholder='Enter your JSON request, e.g. {"ping":1}'></textarea>
        </div>
        
        <div>
            <label for="responseText">Response:</label>
            <textarea id="responseText" placeholder="Response will appear here..." readonly></textarea>
        </div>
    </div>

    <script>
        // WebSocket variables
        let socket = null;
        const websocketUrl = "wss://ws.binaryws.com/websockets/v3?app_id=66726";
        
        // DOM elements
        const connectBtn = document.getElementById('connectBtn');
        const disconnectBtn = document.getElementById('disconnectBtn');
        const sendBtn = document.getElementById('sendBtn');
        const clearBtn = document.getElementById('clearBtn');
        const requestText = document.getElementById('requestText');
        const responseText = document.getElementById('responseText');
        const statusDiv = document.getElementById('status');
        
        // Connect to WebSocket
        connectBtn.addEventListener('click', () => {
            if (socket && socket.readyState === WebSocket.OPEN) {
                updateStatus("Already connected");
                return;
            }
            
            socket = new WebSocket(websocketUrl);
            
            socket.onopen = function(e) {
                updateStatus("Connected to Deriv WebSocket", true);
                connectBtn.disabled = true;
                disconnectBtn.disabled = false;
                sendBtn.disabled = false;
                
                // Send authorization if needed
                // const authReq = {
                //     "authorize": "YOUR_API_TOKEN_HERE"
                // };
                // socket.send(JSON.stringify(authReq));
            };
            
            socket.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    const prettyResponse = JSON.stringify(data, null, 2);
                    responseText.value += prettyResponse + "\n\n";
                } catch (e) {
                    responseText.value += event.data + "\n\n";
                }
                
                // Auto-scroll to bottom
                responseText.scrollTop = responseText.scrollHeight;
            };
            
            socket.onclose = function(event) {
                if (event.wasClean) {
                    updateStatus(`Connection closed cleanly, code=${event.code} reason=${event.reason}`);
                } else {
                    updateStatus('Connection died');
                }
                
                connectBtn.disabled = false;
                disconnectBtn.disabled = true;
                sendBtn.disabled = true;
            };
            
            socket.onerror = function(error) {
                updateStatus(`WebSocket Error: ${error.message}`);
            };
        });
        
        // Disconnect from WebSocket
        disconnectBtn.addEventListener('click', () => {
            if (socket) {
                socket.close();
                socket = null;
            }
        });
        
        // Send request
        sendBtn.addEventListener('click', () => {
            if (!socket || socket.readyState !== WebSocket.OPEN) {
                updateStatus("Not connected to WebSocket");
                return;
            }
            
            try {
                const request = requestText.value.trim();
                if (!request) {
                    alert("Please enter a valid JSON request");
                    return;
                }
                
                // Validate JSON
                JSON.parse(request);
                
                socket.send(request);
                responseText.value += "Sent: " + request + "\n\n";
            } catch (e) {
                alert("Invalid JSON: " + e.message);
            }
        });
        
        // Clear response
        clearBtn.addEventListener('click', () => {
            responseText.value = '';
        });
        
        // Update connection status
        function updateStatus(message, isConnected = false) {
            statusDiv.textContent = "Status: " + message;
            statusDiv.className = "status " + (isConnected ? "connected" : "disconnected");
        }
        
        // Format JSON when textarea loses focus
        requestText.addEventListener('blur', function() {
            try {
                const json = JSON.parse(this.value);
                this.value = JSON.stringify(json, null, 2);
            } catch (e) {
                // Not valid JSON, leave as is
            }
        });
    </script>
</body>
</html>