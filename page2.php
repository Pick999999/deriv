<!-- page2.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Page 2 - Receiver</title>
</head>
<body>
    <div id="result"></div>

    <script>
        const ws = new WebSocket('ws://localhost:8080');
        
        ws.onmessage = function(event) {
            const result = JSON.parse(event.data);
            document.getElementById('result').innerHTML = `
                <p>เวลา: ${result.timestamp}</p>
                <p>ข้อมูล: ${result.data}</p>
            `;
        };
    </script>
</body>
</html>