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
}

/**
 * คลาสสำหรับคำนวณตัวชี้วัดทางเทคนิค (Technical Indicators)
 */
class TechnicalIndicators {
    
    /**
     * คำนวณ Exponential Moving Average (EMA)
     * 
     * @param array $prices ข้อมูลราคา
     * @param int $period จำนวนงวด
     * @return array ค่า EMA
     */
    public static function calculateEMA($prices, $period) {
        $ema = [];
        $multiplier = 2 / ($period + 1);
        
        // คำนวณ SMA ที่จุดเริ่มต้น
        $sma = array_sum(array_slice($prices, 0, $period)) / $period;
        $ema[] = $sma;
        
        // คำนวณ EMA สำหรับราคาที่เหลือ
        for ($i = $period; $i < count($prices); $i++) {
            $ema[] = ($prices[$i] - $ema[count($ema) - 1]) * $multiplier + $ema[count($ema) - 1];
        }
        
        // เพิ่ม null สำหรับงวดที่ไม่มีข้อมูล
        $result = array_fill(0, $period - 1, null);
        return array_merge($result, $ema);
    }
    
    /**
     * คำนวณ Relative Strength Index (RSI)
     * 
     * @param array $prices ข้อมูลราคา
     * @param int $period จำนวนงวด
     * @return array ค่า RSI
     */
    public static function calculateRSI($prices, $period = 14) {
        $rsi = [];
        $gains = [];
        $losses = [];
        
        // คำนวณ price changes
        for ($i = 1; $i < count($prices); $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            $gains[] = $change > 0 ? $change : 0;
            $losses[] = $change < 0 ? abs($change) : 0;
        }
        
        // สำหรับงวดแรก คำนวณ average gains/losses
        $avgGain = array_sum(array_slice($gains, 0, $period)) / $period;
        $avgLoss = array_sum(array_slice($losses, 0, $period)) / $period;
        
        // ป้องกันการหารด้วยศูนย์
        if ($avgLoss == 0) {
            $rsi[] = 100;
        } else {
            $rs = $avgGain / $avgLoss;
            $rsi[] = 100 - (100 / (1 + $rs));
        }
        
        // คำนวณ RSI สำหรับงวดที่เหลือ
        for ($i = $period; $i < count($gains); $i++) {
            $avgGain = (($avgGain * ($period - 1)) + $gains[$i]) / $period;
            $avgLoss = (($avgLoss * ($period - 1)) + $losses[$i]) / $period;
            
            if ($avgLoss == 0) {
                $rsi[] = 100;
            } else {
                $rs = $avgGain / $avgLoss;
                $rsi[] = 100 - (100 / (1 + $rs));
            }
        }
        
        // เพิ่ม null สำหรับงวดที่ไม่มีข้อมูล
        $result = array_fill(0, $period, null);
        return array_merge($result, $rsi);
    }
    
    /**
     * คำนวณ Bollinger Bands
     * 
     * @param array $prices ข้อมูลราคา
     * @param int $period จำนวนงวด (ปกติใช้ 20)
     * @param float $multiplier ตัวคูณสำหรับ standard deviation (ปกติใช้ 2)
     * @return array ค่า Bollinger Bands [middle, upper, lower]
     */
    public static function calculateBollingerBands($prices, $period = 20, $multiplier = 2) {
        $bands = ['middle' => [], 'upper' => [], 'lower' => []];
        
        for ($i = 0; $i < count($prices); $i++) {
            if ($i < $period - 1) {
                $bands['middle'][] = null;
                $bands['upper'][] = null;
                $bands['lower'][] = null;
            } else {
                $slice = array_slice($prices, $i - $period + 1, $period);
                $sma = array_sum($slice) / $period;
                
                // คำนวณ standard deviation
                $sumSquaredDiff = 0;
                foreach ($slice as $price) {
                    $sumSquaredDiff += pow($price - $sma, 2);
                }
                $stdDev = sqrt($sumSquaredDiff / $period);
                
                $bands['middle'][] = $sma;
                $bands['upper'][] = $sma + ($multiplier * $stdDev);
                $bands['lower'][] = $sma - ($multiplier * $stdDev);
            }
        }
        
        return $bands;
    }
    
    /**
     * คำนวณ Average Directional Index (ADX)
     * 
     * @param array $high ข้อมูลราคาสูงสุด
     * @param array $low ข้อมูลราคาต่ำสุด
     * @param array $close ข้อมูลราคาปิด
     * @param int $period จำนวนงวด (ปกติใช้ 14)
     * @return array ค่า ADX, +DI, -DI
     */
    public static function calculateADX($high, $low, $close, $period = 14) {
        $result = ['adx' => [], 'plusDI' => [], 'minusDI' => []];
        
        // คำนวณค่า True Range (TR)
        $tr = [];
        $tr[] = $high[0] - $low[0];
        for ($i = 1; $i < count($close); $i++) {
            $tr[] = max(
                $high[$i] - $low[$i],
                abs($high[$i] - $close[$i - 1]),
                abs($low[$i] - $close[$i - 1])
            );
        }
        
        // คำนวณ Directional Movement (+DM, -DM)
        $plusDM = [];
        $minusDM = [];
        $plusDM[] = 0;
        $minusDM[] = 0;
        
        for ($i = 1; $i < count($high); $i++) {
            $upMove = $high[$i] - $high[$i - 1];
            $downMove = $low[$i - 1] - $low[$i];
            
            if ($upMove > $downMove && $upMove > 0) {
                $plusDM[] = $upMove;
            } else {
                $plusDM[] = 0;
            }
            
            if ($downMove > $upMove && $downMove > 0) {
                $minusDM[] = $downMove;
            } else {
                $minusDM[] = 0;
            }
        }
        
        // คำนวณ Smoothed ATR, +DM14, -DM14
        $smoothedTR = [];
        $smoothedPlusDM = [];
        $smoothedMinusDM = [];
        
        // คำนวณค่าเริ่มต้น
        $smoothedTR[0] = array_sum(array_slice($tr, 0, $period));
        $smoothedPlusDM[0] = array_sum(array_slice($plusDM, 0, $period));
        $smoothedMinusDM[0] = array_sum(array_slice($minusDM, 0, $period));
        
        // คำนวณค่าอื่นๆ
        for ($i = 1; $i < count($tr) - $period + 1; $i++) {
            $smoothedTR[$i] = $smoothedTR[$i - 1] - ($smoothedTR[$i - 1] / $period) + $tr[$i + $period - 1];
            $smoothedPlusDM[$i] = $smoothedPlusDM[$i - 1] - ($smoothedPlusDM[$i - 1] / $period) + $plusDM[$i + $period - 1];
            $smoothedMinusDM[$i] = $smoothedMinusDM[$i - 1] - ($smoothedMinusDM[$i - 1] / $period) + $minusDM[$i + $period - 1];
        }
        
        // คำนวณ +DI14, -DI14
        $plusDI = [];
        $minusDI = [];
        
        for ($i = 0; $i < count($smoothedTR); $i++) {
            $plusDI[$i] = 100 * ($smoothedPlusDM[$i] / $smoothedTR[$i]);
            $minusDI[$i] = 100 * ($smoothedMinusDM[$i] / $smoothedTR[$i]);
        }
        
        // คำนวณ DX
        $dx = [];
        
        for ($i = 0; $i < count($plusDI); $i++) {
            $dx[$i] = 100 * (abs($plusDI[$i] - $minusDI[$i]) / ($plusDI[$i] + $minusDI[$i]));
        }
        
        // คำนวณ ADX
        $adx = [];
        $adx[0] = array_sum(array_slice($dx, 0, $period)) / $period;
        
        for ($i = 1; $i < count($dx) - $period + 1; $i++) {
            $adx[$i] = (($adx[$i - 1] * ($period - 1)) + $dx[$i + $period - 1]) / $period;
        }
        
        // เพิ่ม null สำหรับงวดที่ไม่มีข้อมูล
        $nullFill = array_fill(0, $period * 2 - 1, null);
        $result['adx'] = array_merge($nullFill, $adx);
        
        $nullFillDI = array_fill(0, $period - 1, null);
        $result['plusDI'] = array_merge($nullFillDI, $plusDI);
        $result['minusDI'] = array_merge($nullFillDI, $minusDI);
        
        return $result;
    }
}

// ตัวอย่างการใช้งาน
try {
    // สร้าง instance ของ DerivAPI
    $api = new DerivAPI('66726'); // ใช้ App ID ของคุณหรือ App ID สาธารณะ
    
    // ดึงข้อมูล candlestick (ขอข้อมูลเยอะขึ้นเพื่อให้คำนวณตัวชี้วัดได้ถูกต้อง)
    $symbol = "R_100";
    $interval = 60; // 1 นาที
    $count = 100; // จำนวนแท่งเทียน
    
    $candlesticks = $api->getCandlesticks($symbol, $interval, $count);
    
    if (isset($candlesticks['candles'])) {
        // เก็บข้อมูลราคาในรูปแบบ array
        $times = [];
        $opens = [];
        $highs = [];
        $lows = [];
        $closes = [];
        
        // แปลงข้อมูลจาก candlesticks เป็น arrays
        foreach ($candlesticks['candles'] as $candle) {
            $times[] = $candle['epoch'];
            $opens[] = $candle['open'];
            $highs[] = $candle['high'];
            $lows[] = $candle['low'];
            $closes[] = $candle['close'];
        }
        
        // คำนวณตัวชี้วัดทางเทคนิค
        $ema3 = TechnicalIndicators::calculateEMA($closes, 3);
        $ema5 = TechnicalIndicators::calculateEMA($closes, 5);
        $rsi = TechnicalIndicators::calculateRSI($closes);
        $bbands = TechnicalIndicators::calculateBollingerBands($closes);
        $adx = TechnicalIndicators::calculateADX($highs, $lows, $closes);
        
        // แสดงผลลัพธ์
        echo "<h2>ข้อมูล Candlestick และตัวชี้วัดทางเทคนิคสำหรับ {$symbol}</h2>";
        echo "<div style='overflow-x: auto;'>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>เวลา</th>
                <th>เปิด</th>
                <th>สูงสุด</th>
                <th>ต่ำสุด</th>
                <th>ปิด</th>
                <th>EMA(3)</th>
                <th>EMA(5)</th>
                <th>RSI(14)</th>
                <th>BB Middle</th>
                <th>BB Upper</th>
                <th>BB Lower</th>
                <th>ADX</th>
                <th>+DI</th>
                <th>-DI</th>
              </tr>";
        
        $max_display = min(count($times), 20); // แสดงเฉพาะ 20 แถวล่าสุด
        $start_index = count($times) - $max_display;
        
        for ($i = $start_index; $i < count($times); $i++) {
            $time = date('Y-m-d H:i:s', $times[$i]);
            echo "<tr>";
            echo "<td>{$time}</td>";
            echo "<td>" . round($opens[$i], 4) . "</td>";
            echo "<td>" . round($highs[$i], 4) . "</td>";
            echo "<td>" . round($lows[$i], 4) . "</td>";
            echo "<td>" . round($closes[$i], 4) . "</td>";
            echo "<td>" . (is_null($ema3[$i]) ? "-" : round($ema3[$i], 4)) . "</td>";
            echo "<td>" . (is_null($ema5[$i]) ? "-" : round($ema5[$i], 4)) . "</td>";
            echo "<td>" . (is_null($rsi[$i]) ? "-" : round($rsi[$i], 2)) . "</td>";
            echo "<td>" . (is_null($bbands['middle'][$i]) ? "-" : round($bbands['middle'][$i], 4)) . "</td>";
            echo "<td>" . (is_null($bbands['upper'][$i]) ? "-" : round($bbands['upper'][$i], 4)) . "</td>";
            echo "<td>" . (is_null($bbands['lower'][$i]) ? "-" : round($bbands['lower'][$i], 4)) . "</td>";
            echo "<td>" . (is_null($adx['adx'][$i]) ? "-" : round($adx['adx'][$i], 2)) . "</td>";
            echo "<td>" . (is_null($adx['plusDI'][$i]) ? "-" : round($adx['plusDI'][$i], 2)) . "</td>";
            echo "<td>" . (is_null($adx['minusDI'][$i]) ? "-" : round($adx['minusDI'][$i], 2)) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</div>";
        
        // แสดงกราฟเทียน (ใช้ HTML + CSS + JavaScript)
        echo "<h2>กราฟเทียน</h2>";
        echo "<div style='margin-top: 20px; font-style: italic;'>โค้ดนี้ต้องการ JavaScript และ CSS เพิ่มเติมเพื่อแสดงกราฟเทียน</div>";
        
        // สร้างข้อมูลสำหรับกราฟ
        $chartData = [];
        for ($i = $start_index; $i < count($times); $i++) {
            $chartData[] = [
                'time' => $times[$i] * 1000, // เปลี่ยนเป็น JavaScript timestamp
                'open' => $opens[$i],
                'high' => $highs[$i],
                'low' => $lows[$i],
                'close' => $closes[$i],
                'ema3' => $ema3[$i],
                'ema5' => $ema5[$i],
                'rsi' => $rsi[$i],
                'bbMiddle' => $bbands['middle'][$i],
                'bbUpper' => $bbands['upper'][$i],
                'bbLower' => $bbands['lower'][$i],
                'adx' => $adx['adx'][$i],
                'plusDI' => $adx['plusDI'][$i],
                'minusDI' => $adx['minusDI'][$i]
            ];
        }
        
        // แสดงข้อมูลสำหรับนำไปสร้างกราฟ
        echo "<pre style='display: none;' id='chart-data'>" . json_encode($chartData) . "</pre>";
        
    } else {
        echo "ไม่พบข้อมูล candlestick";
        echo "<pre>" . print_r($candlesticks, true) . "</pre>";
    }
    
    // ปิดการเชื่อมต่อ
    $api->disconnect();
    
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
}

// เพิ่มโค้ด HTML และ JavaScript สำหรับแสดงกราฟ (ต้องใช้ไลบรารีเพิ่มเติม)
// โค้ดนี้เป็นตัวอย่างเท่านั้น ในการใช้งานจริงควรใช้ไลบรารีเช่น Chart.js, TradingView Lightweight Charts, Highcharts, etc.
?>

<!-- ส่วนของ HTML สำหรับแสดงข้อมูลเพิ่มเติม -->
<div style="margin-top: 30px;">
    <h3>คำอธิบายตัวชี้วัด</h3>
    <ul>
        <li><strong>EMA (Exponential Moving Average)</strong> - ค่าเฉลี่ยเคลื่อนที่แบบถ่วงน้ำหนัก ที่ให้ความสำคัญกับข้อมูลล่าสุดมากกว่า</li>
        <li><strong>RSI (Relative Strength Index)</strong> - ตัวชี้วัดที่แสดงความแข็งแกร่งของแนวโน้มราคา ค่าอยู่ระหว่าง 0-100</li>
        <li><strong>Bollinger Bands</strong> - แถบราคาที่แสดงความผันผวน ประกอบด้วยเส้นกลาง (SMA) และเส้นบน-ล่างที่ห่างออกไปตามค่าเบี่ยงเบนมาตรฐาน</li>
        <li><strong>ADX (Average Directional Index)</strong> - ตัวชี้วัดที่แสดงความแข็งแกร่งของแนวโน้ม โดยไม่คำนึงถึงทิศทาง</li>
        <li><strong>+DI/-DI (Directional Indicators)</strong> - แสดงทิศทางของแนวโน้มขาขึ้น/ขาลง</li>
    </ul>
</div>