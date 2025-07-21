<!-- page1.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Page 1 - Sender</title>
</head>
<body>
    <button onclick="processAndSend()">Process and Send Data</button>

    <script>
        const ws = new WebSocket('ws://localhost:8080');
        
        function processAndSend() {
            // จำลองการประมวลผล
            const result = {
                timestamp: new Date().toISOString(),
                data: "ผลการประมวลผลจาก Page 1"
            };
            
            ws.send(JSON.stringify(result));
        }
    </script>
</body>
</html>