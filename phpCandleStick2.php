<?php
// เริ่มต้น output buffering และการตั้งค่าการแสดงข้อผิดพลาด
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตรวจสอบว่ามีการติดตั้ง Composer และไลบรารี WebSocket
if (!file_exists('vendor/autoload.php')) {
    die("กรุณาติดตั้ง WebSocket Client ด้วย Composer ก่อน: composer require textalk/websocket");
}

require 'vendor/autoload.php';
use WebSocket\Client;

/**
 * คลาสสำหรับการดึงข้อมูลจาก Deriv API ผ่าน WebSocket
 */
class DerivAPI {
    private $client;
    private $app_id;
    
    /**
     * สร้าง instance ของ DerivAPI
     * 
     * @param string $app_id App ID จาก Deriv.com
     */
    public function __construct($app_id = '66726') {
        $this->app_id = $app_id;
        $this->connect();
    }
    
    /**
     * เชื่อมต่อกับ WebSocket API
     */
    public function connect() {
        try {
            $this->client = new Client("wss://ws.binaryws.com/websockets/v3?app_id={$this->app_id}");
        } catch (Exception $e) {
            throw new Exception("ไม่สามารถเชื่อมต่อกับ Deriv API ได้: " . $e->getMessage());
        }
    }
    
    /**
     * ปิดการเชื่อมต่อ
     */
    public function disconnect() {
        if ($this->client) {
            $this->client->close();
        }
    }
    
    /**
     * ดึงข้อมูล candlestick
     * 
     * @param string $symbol สัญลักษณ์ (เช่น R_100, R_50)
     * @param int $interval ระยะเวลาเป็นวินาที (60, 300, 900, 1800, 3600, etc.)
     * @param int $count จำนวนแท่งเทียนที่ต้องการ
     * @param string|int $start_time เวลาเริ่มต้น (unix timestamp หรือ yyyy-mm-dd hh:mm:ss)
     * @param string|int $end_time เวลาสิ้นสุด (unix timestamp หรือ "latest")
     * @return array ข้อมูล candlestick
     */
    public function getCandlesticks($symbol, $interval = 60, $count = 100, $start_time = null, $end_time = "latest") {
        $request = [
            "ticks_history" => $symbol,
            "style" => "candles",
            "granularity" => $interval,
            "count" => $count
        ];
        
        // เพิ่มเวลาเริ่มต้นและสิ้นสุดถ้ามีการระบุ
        if ($start_time !== null) {
            if (is_string($start_time) && !is_numeric($start_time)) {
                $start_time = strtotime($start_time);
            }
            $request["start"] = $start_time;
        }
        
        if ($end_time !== null) {
            if ($end_time !== "latest" && is_string($end_time) && !is_numeric($end_time)) {
                $end_time = strtotime($end_time);
            }
            $request["end"] = $end_time;
        }
        
        try {
            $this->client->send(json_encode($request));
            $response = $this->client->receive();
            return json_decode($response, true);
        } catch (Exception $e) {
            throw new Exception("เกิดข้อผิดพลาดในการดึงข้อมูล candlestick: " . $e->getMessage());
        }
    }
    
    /**
     * ดึงรายการสัญลักษณ์ที่มีอยู่
     * 
     * @return array รายการสัญลักษณ์
     */
    public function getActiveSymbols() {
        $request = [
            "active_symbols" => "brief"
        ];
        
        try {
            $this->client->send(json_encode($request));
            $response = $this->client->receive();
            return json_decode($response, true);
        } catch (Exception $e) {
            throw new Exception("เกิดข้อผิดพลาดในการดึงรายการสัญลักษณ์: " . $e->getMessage());
        }
    }
    
    /**
     * สมัครรับข้อมูล tick แบบเรียลไทม์
     * 
     * @param string $symbol สัญลักษณ์
     * @param callable $callback ฟังก์ชันที่จะเรียกเมื่อได้รับข้อมูลใหม่
     * @param int $duration ระยะเวลาที่จะรับข้อมูล (วินาที)
     */
    public function subscribeTicks($symbol, $callback, $duration = 10) {
        $request = [
            "ticks" => $symbol,
            "subscribe" => 1
        ];
        
        try {
            $this->client->send(json_encode($request));
            
            $startTime = time();
            while (time() - $startTime < $duration) {
                $response = $this->client->receive();
                $data = json_decode($response, true);
                
                if (isset($data['tick'])) {
                    call_user_func($callback, $data['tick']);
                }
            }
            
            // ยกเลิกการสมัครรับข้อมูล
            $request["subscribe"] = 0;
            $this->client->send(json_encode($request));
            $this->client->receive(); // รับข้อความยืนยันการยกเลิก
            
        } catch (Exception $e) {
            throw new Exception("เกิดข้อผิดพลาดในการสมัครรับข้อมูล tick: " . $e->getMessage());
        }
    }
}

// ตัวอย่างการใช้งาน
try {
    // สร้าง instance ของ DerivAPI
    $api = new DerivAPI('66726'); // ใช้ App ID ของคุณ
    
    // ดึงข้อมูล candlestick
    echo "<h2>ข้อมูล Candlestick สำหรับ R_100</h2>";
    $candlesticks = $api->getCandlesticks("R_100", 60, 10);
    
    if (isset($candlesticks['candles'])) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>เวลา</th><th>เปิด</th><th>สูงสุด</th><th>ต่ำสุด</th><th>ปิด</th></tr>";
        
        foreach ($candlesticks['candles'] as $candle) {
            $time = date('Y-m-d H:i:s', $candle['epoch']);
            echo "<tr>";
            echo "<td>{$time}</td>";
            echo "<td>{$candle['open']}</td>";
            echo "<td>{$candle['high']}</td>";
            echo "<td>{$candle['low']}</td>";
            echo "<td>{$candle['close']}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "ไม่พบข้อมูล candlestick";
        echo "<pre>" . print_r($candlesticks, true) . "</pre>";
    }
    
    // ดึงรายการสัญลักษณ์ที่มีอยู่ (แสดงตัวอย่างเพียง 5 รายการ)
    echo "<h2>รายการสัญลักษณ์ที่มีอยู่</h2>";
    $symbols = $api->getActiveSymbols();
    
    if (isset($symbols['active_symbols'])) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Symbol</th><th>ชื่อ</th><th>Market</th></tr>";
        
        $count = 0;
        foreach ($symbols['active_symbols'] as $symbol) {
            if ($count >= 5) break; // แสดงเพียง 5 รายการเป็นตัวอย่าง
            
            echo "<tr>";
            echo "<td>{$symbol['symbol']}</td>";
            echo "<td>{$symbol['display_name']}</td>";
            echo "<td>{$symbol['market_display_name']}</td>";
            echo "</tr>";
            
            $count++;
        }
        
        echo "</table>";
        echo "<p>แสดงเพียง 5 รายการจากทั้งหมด " . count($symbols['active_symbols']) . " รายการ</p>";
    }
    
    /*
    // ตัวอย่างการสมัครรับข้อมูล tick แบบเรียลไทม์ (ปกติอยู่ในหน้าเว็บจริง)
    echo "<h2>ข้อมูล Tick แบบเรียลไทม์ (10 วินาที)</h2>";
    
    $api->subscribeTicks("R_100", function($tick) {
        echo "เวลา: " . date('Y-m-d H:i:s', $tick['epoch']) . " - ราคา: " . $tick['quote'] . "<br>";
        ob_flush();
        flush();
    }, 10);
    */
    
    // ปิดการเชื่อมต่อ
    $api->disconnect();
    
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
}
?>