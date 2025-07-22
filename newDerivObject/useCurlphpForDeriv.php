<?php

// useCurlphpForDeriv.php
// ไฟล์สำหรับดึงข้อมูล candlestick จาก Deriv.com โดยใช้ API แบบ REST ผ่าน cURL


// ไฟล์สำหรับดึงข้อมูล candlestick จาก Deriv.com โดยใช้ API แบบ REST ผ่าน cURL พร้อมการดีบัก




// กำหนดค่าตัวแปรสำหรับการเชื่อมต่อ API
$api_url = 'wss://ws.binaryws.com/websockets/v3?app_id=66726';

// สร้างฟังก์ชันสำหรับส่งคำขอไปยัง API
function get_candlestick_data($symbol, $interval, $count, $app_id) {
    // สร้าง URL สำหรับการเชื่อมต่อ
    $url = "https://api.deriv.com/v3?app_id=66726";
    
    // สร้างข้อมูลคำขอในรูปแบบ JSON
    $request_data = json_encode([
        "ticks_history" => $symbol,
        "adjust_start_time" => 1,
        "count" => $count,
        "end" => "latest",
        "granularity" => 60,
        "style" => "candles"
    ]);
    
    // เริ่มต้นการใช้งาน cURL
    $ch = curl_init();
    
    // กำหนดค่าต่างๆ สำหรับ cURL
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $request_data,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($request_data)
        ]
    ]);
    
    // ส่งคำขอและรับข้อมูลการตอบกลับ
    $response = curl_exec($ch);
    
    // ตรวจสอบความผิดพลาด
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
        return false;
    }
    
    // ปิดการเชื่อมต่อ cURL
    curl_close($ch);
    
    // แปลงข้อมูล JSON เป็น array
    $result = json_decode($response, true);
    
    return $result;
}

// ตัวอย่างการใช้งาน
$app_id = '66726'; // แทนที่ด้วย App ID ของคุณจาก Deriv.com
$symbol = 'R_100'; // ตัวอย่างสัญลักษณ์ (Volatility 100 Index)
$interval = 60; // ระยะเวลาของแท่งเทียนเป็นวินาที (1 นาที)
$count = 10; // จำนวนแท่งเทียนที่ต้องการ

// เรียกใช้ฟังก์ชันเพื่อดึงข้อมูล
$candlestick_data = get_candlestick_data($symbol, $interval, $count, $app_id);

// แสดงผลลัพธ์
if ($candlestick_data) {
    // ตรวจสอบว่ามีข้อมูล candles หรือไม่
    if (isset($candlestick_data['candles']) && !empty($candlestick_data['candles'])) {
        echo "<h2>ข้อมูล Candlestick สำหรับ {$symbol}</h2>";
        echo "<table border='1'>";
        echo "<tr><th>เวลา</th><th>เปิด</th><th>สูงสุด</th><th>ต่ำสุด</th><th>ปิด</th><th>ปริมาณ</th></tr>";
        
        foreach ($candlestick_data['candles'] as $candle) {
            $time = date('Y-m-d H:i:s', $candle['epoch']);
            echo "<tr>";
            echo "<td>{$time}</td>";
            echo "<td>{$candle['open']}</td>";
            echo "<td>{$candle['high']}</td>";
            echo "<td>{$candle['low']}</td>";
            echo "<td>{$candle['close']}</td>";
            echo "<td>" . (isset($candle['volume']) ? $candle['volume'] : 'N/A') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "ไม่พบข้อมูล Candlestick";
        echo "<pre>" . print_r($candlestick_data, true) . "</pre>";
    }
} else {
    echo "เกิดข้อผิดพลาดในการดึงข้อมูล";
}
?>
