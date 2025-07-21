document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const fetchBtn = document.getElementById('fetch');
            const symbolSelect = document.getElementById('symbol');
            const granularitySelect = document.getElementById('granularity');
            const countSelect = document.getElementById('count');
            const dataBody = document.getElementById('dataBody');
            const errorMessage = document.getElementById('errorMessage');
            const statusMessage = document.getElementById('statusMessage');
            const loader = document.getElementById('loader');
            const autoRefreshToggle = document.getElementById('autoRefresh');
            const nextUpdateTimeSpan = document.getElementById('nextUpdateTime');
            
            // Auth elements
            const apiTokenInput = document.getElementById('apiToken');
            const connectTokenBtn = document.getElementById('connectToken');
            const oauthLoginBtn = document.getElementById('oauthLogin');
            const logoutBtn = document.getElementById('logout');
            const tokenError = document.getElementById('tokenError');
            const tokenSuccess = document.getElementById('tokenSuccess');
            const oauthError = document.getElementById('oauthError');
            const oauthSuccess = document.getElementById('oauthSuccess');
            const authStatusIndicator = document.getElementById('authStatusIndicator');
            const authStatusText = document.getElementById('authStatusText');
            const authDetails = document.getElementById('authDetails');
            const accountIdSpan = document.getElementById('accountId');
            const accountBalanceSpan = document.getElementById('accountBalance');
            const accountEmailSpan = document.getElementById('accountEmail');
            const tokenAuthDiv = document.getElementById('tokenAuth');
            const oauthAuthDiv = document.getElementById('oauthAuth');
            
            // Tab elements
            const tabs = document.querySelectorAll('.tab');
            
            // App state
            let ws = null;
            let refreshTimer = null;
            let nextUpdateTime = null;
            //let authToken = null;
            let accountInfo = null;
            
            // Check for stored token
            const storedToken = localStorage.getItem('derivApiToken');
            if (storedToken) {
                apiTokenInput.value = storedToken;
                // Auto connect if token exists
                setTimeout(() => {
                    connectWithToken(storedToken);
                }, 500);
            }
            
            // Initialize WebSocket connection
            function connectWebSocket() {
                return new Promise((resolve, reject) => {
                    if (ws && ws.readyState === WebSocket.OPEN) {
                        resolve(ws);
                        return;
                    }
                    
                    ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
                    
                    ws.onopen = function() {
                        console.log('WebSocket connection established');
                        // If we have an auth token, authorize immediately
                        if (authToken) {
                            authorizeWebSocket(ws, authToken)
                                .then(() => resolve(ws))
                                .catch(error => {
                                    console.error('Authorization failed:', error);
                                    resolve(ws); // Still resolve with the socket, just unauthorized
                                });
                        } else {
                            resolve(ws);
                        }
                    };
                    
                    ws.onerror = function(error) {
                        console.error('WebSocket error:', error);
                        errorMessage.textContent = 'Connection error. Please try again.';
                        reject(error);
                    };
                    
                    ws.onclose = function() {
                        console.log('WebSocket connection closed');
                    };
                });
            }
            
            // Authorize WebSocket connection with token
            function authorizeWebSocket(socket, token) {
                return new Promise((resolve, reject) => {
                    if (!socket || socket.readyState !== WebSocket.OPEN) {
                        reject(new Error('WebSocket not connected'));
                        return;
                    }
                    
                    const timeoutId = setTimeout(() => {
                        reject(new Error('Authorization timed out'));
                    }, 10000);
                    
                    const authHandler = function(msg) {
                        const data = JSON.parse(msg.data);
                        
                        if (data.msg_type === 'authorize') {
                            clearTimeout(timeoutId);
                            socket.removeEventListener('message', authHandler);
                            
                            if (data.error) {
                                reject(new Error(data.error.message || 'Authorization failed'));
                            } else {
                                // Store account info
                                accountInfo = data.authorize;
                                updateAuthStatus(true);
                                resolve(data.authorize);
                            }
                        }
                    };
                    
                    socket.addEventListener('message', authHandler);
                    
                    socket.send(JSON.stringify({
                        authorize: token
                    }));
                });
            }
            
            // Connect with API token
            function connectWithToken(token) {
                tokenError.textContent = '';
                tokenSuccess.textContent = '';
                
                if (!token) {
                    tokenError.textContent = 'Please enter an API token';
                    return;
                }
                
                // Show loader
                connectTokenBtn.disabled = true;
                connectTokenBtn.textContent = 'Connecting...';
                
                // First establish a WebSocket connection
                connectWebSocket()
                    .then(socket => {
                        // Then authorize with the token
                        return authorizeWebSocket(socket, token);
                    })
                    .then(authorizeData => {
                        console.log('Authorization successful:', authorizeData);
                        
                        // Store token
                        authToken = token;						
                        localStorage.setItem('derivApiToken', token);
                        
                        // Update UI
                        tokenSuccess.textContent = 'Connected successfully!';
                        authDetails.classList.add('expanded');
                        updateAccountInfo(authorizeData);
                    })
                    .catch(error => {
                        console.error('Connection error:', error);
                        tokenError.textContent = `Error: ${error.message}`;
                        updateAuthStatus(false);
                    })
                    .finally(() => {
                        connectTokenBtn.disabled = false;
                        connectTokenBtn.textContent = 'Connect';
                    });
            }
            
            // Update authentication status UI
            function updateAuthStatus(isAuthenticated) {
                if (isAuthenticated) {
                    authStatusIndicator.classList.add('connected');
                    authStatusText.textContent = 'Connected';
                    authDetails.classList.add('expanded');
                } else {
                    authStatusIndicator.classList.remove('connected');
                    authStatusText.textContent = 'Not Connected';
                    authDetails.classList.remove('expanded');
                    accountInfo = null;
                    authToken = null;
                    localStorage.removeItem('derivApiToken');
                }
            }
            
            // Update account information UI
            function updateAccountInfo(data) {
                if (!data) return;
                
                accountIdSpan.textContent = data.loginid || 'N/A';
                accountBalanceSpan.textContent = data.balance ? `${data.currency} ${parseFloat(data.balance).toFixed(2)}` : 'N/A';
                accountEmailSpan.textContent = data.email || 'N/A';
            }
            
            // Handle OAuth login
            function initiateOAuth() {
                const clientId = '29421'; // Example client ID, you would need to register your app with Deriv
                const redirectUri = encodeURIComponent(window.location.href);
                const scope = encodeURIComponent('read trade admin');
                
                const oauthUrl = `https://oauth.deriv.com/oauth2/authorize?app_id=${clientId}&l=en&redirect_uri=${redirectUri}&scope=${scope}&response_type=token`;
                
                window.location.href = oauthUrl;
            }
            
            // Check for OAuth callback
            function checkOAuthCallback() {
                const hash = window.location.hash;
                if (hash && hash.includes('access_token=')) {
                    const params = new URLSearchParams(hash.substring(1));
                    const token = params.get('access_token');
                    
                    if (token) {
                        // Clear the URL hash to avoid token exposure
                        history.pushState('', document.title, window.location.pathname + window.location.search);
                        
                        // Use the token
                        connectWithToken(token);
                        
                        oauthSuccess.textContent = 'Logged in successfully via OAuth!';
                        return true;
                    }
                }
                return false;
            }
            
            // Logout function
            function logout() {
                updateAuthStatus(false);
                apiTokenInput.value = '';
                tokenSuccess.textContent = '';
                oauthSuccess.textContent = '';
                tokenError.textContent = '';
                oauthError.textContent = '';
                
                // Close existing WebSocket connection to ensure we start fresh
                if (ws) {
                    ws.close();
                    ws = null;
                }
            }
            
            // Send request to get candle data
            async function fetchCandleData() {
                try {
                    // Clear previous errors
                    errorMessage.textContent = '';
                    
                    // Show loader
                    loader.style.display = 'inline-block';
                    
                    // Get selected values
                    const symbol = symbolSelect.value;
                    const granularity = parseInt(granularitySelect.value);
                    const count = parseInt(countSelect.value);
                    
                    // Connect to WebSocket
                    const socket = await connectWebSocket();
                    
                    // Create a promise to handle the response
                    const candleDataPromise = new Promise((resolve, reject) => {
                        const timeoutId = setTimeout(() => {
                            reject(new Error('Request timed out'));
                        }, 10000); // 10 seconds timeout
                        
                        // Handle WebSocket messages
                        socket.onmessage = function(msg) {
                            const data = JSON.parse(msg.data);
                            
                            // Check if this is a candle response
                            if (data.msg_type === 'candles') {
                                clearTimeout(timeoutId);
                                resolve(data);
                            } else if (data.error) {
                                clearTimeout(timeoutId);
                                reject(new Error(data.error.message || 'Unknown error'));
                            }
                        };
                        
                        // Send the candles request
                        socket.send(JSON.stringify({
                            ticks_history: symbol,
                            granularity: granularity,
                            style: 'candles',
                            count: count,
                            end: 'latest'
                        }));
                    });
                    
                    // Wait for the response
                    const response = await candleDataPromise;
                    
                    // Process and display the data
                    displayCandleData(response.candles || []);
                    
                    // Update last fetch time
                    const now = new Date();
                    statusMessage.textContent = `Last updated: ${formatDate(now)}`;
                    
                } catch (error) {
                    console.error('Error fetching candle data:', error);
                    errorMessage.textContent = `Error: ${error.message}`;
                } finally {
                    // Hide loader
                    loader.style.display = 'none';
                }
            }
            
            // Display candle data in the table
            function displayCandleData(candles) {
                if (!candles || candles.length === 0) {
                    errorMessage.textContent = 'No data available for the selected criteria.';
                    return;
                }
				doAjaxNewTrade(candles);
                
                // Clear existing data
                dataBody.innerHTML = '';
                
                // Sort candles by epoch (newest first)
                candles.sort((a, b) => b.epoch - a.epoch);
                
                // Create table rows
                candles.forEach(candle => {
                    const row = document.createElement('tr');
                    
                    // Format timestamp
                    const date = new Date(candle.epoch * 1000);
                    const formattedDate = formatDate(date);
                    
                    // Create cells
                    row.innerHTML = `
                        <td>${candle.epoch}</td>
                        <td>${formattedDate}</td>
                        <td>${parseFloat(candle.open).toFixed(5)}</td>
                        <td>${parseFloat(candle.high).toFixed(5)}</td>
                        <td>${parseFloat(candle.low).toFixed(5)}</td>
                        <td>${parseFloat(candle.close).toFixed(5)}</td>
                    `;
                    
                    dataBody.appendChild(row);
                });
            }
            
            // Format date to a readable string
            function formatDate(date) {
                return date.toLocaleString();
            }
            
            // Update the next update time display
            function updateNextTimeDisplay() {
                if (!nextUpdateTime) return;
                
                const now = new Date();
                const timeDiff = nextUpdateTime - now;
                
                if (timeDiff <= 0) {
                    nextUpdateTimeSpan.textContent = '(updating...)';
                    return;
                }
                
                const seconds = Math.floor(timeDiff / 1000);
                nextUpdateTimeSpan.textContent = `(next update in ${seconds} seconds)`;
            }
            
            // Start the auto-refresh timer
            function startAutoRefresh() {
                if (refreshTimer) {
                    clearInterval(refreshTimer);
                }
                
                // Set the next update time to 1 minute from now
                nextUpdateTime = new Date();
                nextUpdateTime.setMinutes(nextUpdateTime.getMinutes() + 1);
                nextUpdateTime.setSeconds(0); // Align to the start of the next minute
                
                // Calculate initial delay (time until the next minute starts)
                const now = new Date();
                const initialDelay = nextUpdateTime - now;
                
                // Setup the display timer to update every second
                const displayTimer = setInterval(updateNextTimeDisplay, 1000);
                updateNextTimeDisplay();
                
                // Setup the first fetch after initialDelay
                const timerHandle = setTimeout(() => {
                    // Fetch data
                    fetchCandleData();
                    
                    // Setup recurring timer every minute
                    refreshTimer = setInterval(() => {
                        fetchCandleData();
                        
                        // Update next update time
                        nextUpdateTime = new Date();
                        nextUpdateTime.setMinutes(nextUpdateTime.getMinutes() + 1);
                        nextUpdateTime.setSeconds(0);
                    }, 60000); // 1 minute
                    
                    clearInterval(displayTimer);
                    
                    // Setup the display timer to update every second
                    setInterval(updateNextTimeDisplay, 1000);
                }, initialDelay);
                
                return timerHandle;
            }
            
            // Stop the auto-refresh timer
            function stopAutoRefresh() {
                if (refreshTimer) {
                    clearInterval(refreshTimer);
                    refreshTimer = null;
                }
                nextUpdateTime = null;
                nextUpdateTimeSpan.textContent = '';
            }
            
            // Tab switching functionality
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all auth forms
                    tokenAuthDiv.classList.remove('expanded');
                    oauthAuthDiv.classList.remove('expanded');
                    
                    // Show selected auth form
                    const tabName = this.getAttribute('data-tab');
                    if (tabName === 'token') {
                        tokenAuthDiv.classList.add('expanded');
                        oauthAuthDiv.classList.remove('expanded');
                    } else if (tabName === 'oauth') {
                        tokenAuthDiv.classList.remove('expanded');
                        oauthAuthDiv.classList.add('expanded');
                    }
                });
            });
            
            // Event Listeners
            connectTokenBtn.addEventListener('click', function() {
                connectWithToken(apiTokenInput.value.trim());
            });
            
            oauthLoginBtn.addEventListener('click', initiateOAuth);
            
            logoutBtn.addEventListener('click', logout);
            
            fetchBtn.addEventListener('click', fetchCandleData);
            
            autoRefreshToggle.addEventListener('change', function() {
                if (this.checked) {
                    startAutoRefresh();
                    fetchCandleData(); // Fetch immediately when enabling
                } else {
                    stopAutoRefresh();
                }
            });
            
            // Handle select changes - stop and restart auto refresh if needed
            [symbolSelect, granularitySelect, countSelect].forEach(select => {
                select.addEventListener('change', function() {
                    if (autoRefreshToggle.checked) {
                        stopAutoRefresh();
                        startAutoRefresh();
                        fetchCandleData(); // Fetch immediately when changing parameters
                    }
                });
            });
            
            // Check for OAuth callback on page load
            checkOAuthCallback();
            
            // Fix tab display on load
            document.querySelector('.tab[data-tab="token"]').click();
            
            // Cleanup WebSocket connection when the page is closed
            window.addEventListener('beforeunload', function() {
                stopAutoRefresh();
                if (ws) {
                    ws.close();
                }
            });
        });