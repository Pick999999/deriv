<?php
// deriv/newDerivObject/useSocketphpForDeriv.php
// ไฟล์สำหรับดึงข้อมูล candlestick จาก Deriv.com โดยใช้ WebSocket API

// ตั้งค่า error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1); 



// ฟังก์ชันสำหรับการเชื่อมต่อ WebSocket และดึงข้อมูล candlestick
function getDerivsCandles($symbol, $interval, $count = 100) {
    // URL ของ WebSocket API ของ Deriv
    $websocket_url = 'wss://ws.binaryws.com/websockets/v3?app_id=66726';
    
    // ตรวจสอบว่า extension WebSocket ถูกติดตั้งหรือไม่
    if (!extension_loaded('sockets')) {
        die("PHP Sockets extension is required");
    }

    // สร้าง socket สำหรับการเชื่อมต่อ WebSocket
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (!$socket) {
        die('Unable to create socket: ' . socket_strerror(socket_last_error()));
    }

    // แปลง WebSocket URL เป็น host และ port
    $parsed_url = parse_url(str_replace('wss://', 'https://', $websocket_url));
    $host = $parsed_url['host'];
    $port = isset($parsed_url['port']) ? $parsed_url['port'] : 443;

    // เชื่อมต่อไปยัง host
    $result = socket_connect($socket, $host, $port);
    if (!$result) {
        die('Unable to connect to server: ' . socket_strerror(socket_last_error()));
    }

    // สร้าง HTTP headers สำหรับ WebSocket handshake
    $headers = "GET " . ($parsed_url['path'] ?? '/') . 
               (isset($parsed_url['query']) ? "?" . $parsed_url['query'] : "") . " HTTP/1.1\r\n" .
               "Host: " . $host . "\r\n" .
               "Upgrade: websocket\r\n" .
               "Connection: Upgrade\r\n" .
               "Sec-WebSocket-Key: " . base64_encode(openssl_random_pseudo_bytes(16)) . "\r\n" .
               "Sec-WebSocket-Version: 13\r\n\r\n";

    // ส่ง headers
    socket_write($socket, $headers, strlen($headers));

    // อ่าน response จาก handshake
    $response = '';
    while (true) {
        $buffer = socket_read($socket, 2048);
        $response .= $buffer;
        if (strpos($response, "\r\n\r\n") !== false) {
            break;
        }
    }

    // ตรวจสอบว่า handshake สำเร็จหรือไม่
    if (strpos($response, "HTTP/1.1 101") === false) {
        die("WebSocket handshake failed: " . $response);
    }

    // สร้าง payload สำหรับการดึงข้อมูล candlestick
    // กำหนดค่าพารามิเตอร์ตาม API ของ Deriv
    $granularity = getGranularity($interval); // แปลงช่วงเวลาเป็นค่า granularity ที่ Deriv API ใช้
    $style = "candles";
    
    $payload = json_encode([
        "ticks_history" => $symbol,
        "count" => $count,
        "end" => "latest",
        "style" => $style,
        "granularity" => $granularity,
        "req_id" => 1
    ]);

    // เข้ารหัส WebSocket frame ตามมาตรฐาน RFC 6455
    $frame = encodeWebSocketFrame($payload);
    
    // ส่ง request
    socket_write($socket, $frame, strlen($frame));
    
    // อ่าน response
    $response = '';
    while (true) {
        $buffer = socket_read($socket, 8192);
        if ($buffer === false) {
            break;
        }
        $response .= $buffer;
        
        // ตรวจสอบว่าได้รับข้อมูลครบหรือไม่
        if (strlen($buffer) < 8192) {
            break;
        }
    }
    
    // ปิด socket
    socket_close($socket);
    
    // ถอดรหัส WebSocket frame และแปลงเป็น JSON
    $decoded = decodeWebSocketFrame($response);
    $data = json_decode($decoded, true);
    
    // แปลงข้อมูลให้อยู่ในรูปแบบ candlestick ที่ใช้งานได้
    return formatCandlestickData($data);
}

// ฟังก์ชันสำหรับแปลงช่วงเวลาเป็น granularity ที่ Deriv API ใช้
function getGranularity($interval) {
    $map = [
        '1m' => 60,
        '5m' => 300,
        '15m' => 900,
        '30m' => 1800,
        '1h' => 3600,
        '2h' => 7200,
        '4h' => 14400,
        '8h' => 28800,
        '1d' => 86400,
    ];
    
    return isset($map[$interval]) ? $map[$interval] : 60; // ค่าเริ่มต้นคือ 1 นาที
}

// ฟังก์ชันสำหรับเข้ารหัส WebSocket frame
function encodeWebSocketFrame($payload) {
    $length = strlen($payload);
    $frame = "";
    
    // ตั้งค่า header byte (FIN = 1, Opcode = 1 สำหรับ text frame)
    $frame .= chr(129); // 10000001 in binary
    
    // ตั้งค่าความยาวของ payload
    if ($length <= 125) {
        $frame .= chr($length);
    } elseif ($length <= 65535) {
        $frame .= chr(126) . chr(($length >> 8) & 255) . chr($length & 255);
    } else {
        $frame .= chr(127);
        for ($i = 7; $i >= 0; $i--) {
            $frame .= chr(($length >> ($i * 8)) & 255);
        }
    }
    
    // เพิ่ม payload
    $frame .= $payload;
    
    return $frame;
}

// ฟังก์ชันสำหรับถอดรหัส WebSocket frame
function decodeWebSocketFrame($data) {
    $len = ord($data[1]) & 127;
    $maskKey = "";
    $dataOffset = 2;
    
    // คำนวณ data offset ตามความยาวของ payload
    if ($len == 126) {
        $maskKey = substr($data, 4, 4);
        $dataOffset = 8;
    } elseif ($len == 127) {
        $maskKey = substr($data, 10, 4);
        $dataOffset = 14;
    } else {
        $maskKey = substr($data, 2, 4);
        $dataOffset = 6;
    }
    
    // ถอดรหัส payload
    $text = "";
    $payloadData = substr($data, $dataOffset);
    $dataLength = strlen($payloadData);
    
    // ใช้ XOR กับ mask key ตามมาตรฐาน WebSocket
    for ($i = 0; $i < $dataLength; $i++) {
        $text .= $payloadData[$i] ^ $maskKey[$i % 4];
    }
    
    return $text;
}

// ฟังก์ชันสำหรับแปลงข้อมูลให้อยู่ในรูปแบบ candlestick ที่ใช้งานได้
function formatCandlestickData($data) {
    $result = [];
    
    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if (isset($data['candles']) && is_array($data['candles'])) {
        foreach ($data['candles'] as $candle) {
            $result[] = [
                'time' => $candle['epoch'],
                'open' => $candle['open'],
                'high' => $candle['high'],
                'low' => $candle['low'],
                'close' => $candle['close']
            ];
        }
    }
    
    return $result;
}

// ตัวอย่างการใช้งาน
try {
    // ตัวอย่างการดึงข้อมูล candlestick ของ R_50 (Volatility 50 Index) ในช่วงเวลา 1 นาที จำนวน 100 แท่ง
    $candlesticks = getDerivsCandles("R_50", "1m", 100);
    
    // แสดงผลเป็น JSON
    header('Content-Type: application/json');
    echo json_encode($candlesticks, JSON_PRETTY_PRINT);
    
    // หรือบันทึกลงในฐานข้อมูล
    // saveCandlesticksToDB($candlesticks);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

/**
 * ฟังก์ชันสำหรับบันทึกข้อมูลลงในฐานข้อมูล (ตัวอย่าง)
 */
function saveCandlesticksToDB($candlesticks) {
    // เชื่อมต่อกับฐานข้อมูล
    $conn = new mysqli("localhost", "username", "password", "database");
    
    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // เตรียม statement สำหรับการ insert
    $stmt = $conn->prepare("INSERT INTO candlesticks (symbol, time, open, high, low, close) VALUES (?, ?, ?, ?, ?, ?)");
    
    // วนลูปเพื่อบันทึกข้อมูลแต่ละแท่ง
    foreach ($candlesticks as $candle) {
        $stmt->bind_param("ssdddd", $symbol, $candle['time'], $candle['open'], $candle['high'], $candle['low'], $candle['close']);
        $symbol = "R_50"; // แทนที่ด้วยสัญลักษณ์ที่ถูกต้อง
        $stmt->execute();
    }
    
    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();
}
?>