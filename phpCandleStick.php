<?php
//phpCandleStick.php
// เชื่อมต่อ WebSocket
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';
$websocket = new WebSocket\Client("wss://ws.binaryws.com/websockets/v3?app_id=66726");

// ฟังก์ชันสำหรับขอข้อมูล candlestick
function requestCandlestick($symbol, $interval, $count = 100) {
    $request = [
        "ticks_history" => $symbol,
        "style"         => "candles",
        "granularity"   => $interval, // ช่วงเวลาเป็นวินาที (60, 300, 900, 1800, 3600, etc.)
        "count"         => $count,
        "end"          => "latest"
    ];
    
    return json_encode($request);
}

try {
    // ส่งคำขอข้อมูล candlestick สำหรับ Symbol "R_100"
    $request = requestCandlestick("R_100", 60); // ขอข้อมูลทุก 1 นาที
    $websocket->send($request);
    
    // รับข้อมูลและประมวลผล
    $response = $websocket->receive();
    $data = json_decode($response, true);
    
    if (isset($data['candles'])) {
        foreach ($data['candles'] as $candle) {
            echo "เวลา: " . date('Y-m-d H:i:s', $candle['epoch']) . "\n";
            echo "เปิด: " . $candle['open'] . "\n";
            echo "สูงสุด: " . $candle['high'] . "\n";
            echo "ต่ำสุด: " . $candle['low'] . "\n";
            echo "ปิด: " . $candle['close'] . "\n";
            echo "-------------------\n";
        }
    }
    
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
} finally {
    $websocket->close();
}