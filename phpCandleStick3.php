<style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            cursor: pointer;
            background-color: #f2f2f2;
            position: relative; /* เพิ่ม position เพื่อวางลูกศร */
        }
        th.sort-asc::after {
            content: " ▲"; /* ลูกศรขึ้น */
            font-size: 12px;
            color: #0080ff;
        }
        th.sort-desc::after {
            content: " ▼"; /* ลูกศรลง */
            font-size: 12px;
            color: #ff0080;
        }
</style>
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
		$this->Authorized();
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
	public function Authorized() {

        $api_token = 'lt5UMO6bNvmZQaR'; // ต้องสร้าง token จาก account settings บน Deriv.com
		// ทำการล็อกอิน
		$authorizeRequest = json_encode([
			'authorize' => $api_token
		]);
		$this->client->send($authorizeRequest);
		$authorizeResponse = json_decode($this->client->receive(), true);

		// ตรวจสอบการล็อกอิน
		if (isset($authorizeResponse['error'])) {
			die('เกิดข้อผิดพลาดในการล็อกอิน: ' . $authorizeResponse['error']['message']);
		}

		echo "ล็อกอินสำเร็จ! Balance: " . $authorizeResponse['authorize']['balance'] . " " . $authorizeResponse['authorize']['currency'] . "<br>";

	} // end Authorized
    
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
    public function getCandlesticks($symbol, $interval = 60, $count = 20, $start_time = null, $end_time = "latest") {
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
    } // end func getCandlesticks

/*********** end func getCandlesticks ***********************/
    public function buyRiseFallContract($symbol, $duration, $duration_unit, $amount,$contractType) {
		// ขอราคา
		$request = json_encode([
			'proposal' => 1,
			'amount' => $amount,
			'basis' => 'stake',
			'contract_type' => $contractType, // CALL = Rise
			'currency' => 'USD',
			'duration' => $duration,
			'duration_unit' => $duration_unit, // เช่น 'm' สำหรับนาที, 't' สำหรับ ticks
			'symbol' => $symbol // เช่น 'R_100' สำหรับ Volatility 100 Index
		]);
		
		$this->client->send($request);
		$response = json_decode($this->client->receive(), true);
		
		if (isset($response['error'])) {
			echo "เกิดข้อผิดพลาดในการขอราคา: " . $response['error']['message'] . "<br>";
			return false;
		}
		
		// เก็บ ID ของใบเสนอราคา
		$proposal_id = $response['proposal']['id'];
		
		// ส่งคำสั่งซื้อ
		$buyRequest = json_encode([
			'buy' => $proposal_id,
			'price' => $amount
		]);
		
		$this->client->send($buyRequest);
		$buyResponse = json_decode($this->client->receive(), true);
		
		if (isset($buyResponse['error'])) {
			echo "เกิดข้อผิดพลาดในการซื้อ: " . $buyResponse['error']['message'] . "<br>";
			return false;
		}    
		return $buyResponse;
    } // end func buyRiseFallContract

    function trackContractAndSell($contract_id) {
		$request = json_encode([
			'proposal_open_contract' => 1,
			'contract_id' => $contract_id,
			'subscribe' => 1 // ใช้ subscribe เพื่อให้รับข้อมูลแบบ real-time
		]);
		
		$this->client->send($request);
		$loopno = 0 ;
		// วนลูปเพื่อติดตามข้อมูลออเดอร์ตลอดเวลา
		while (true) {
			try {
				$response = json_decode($this->client->receive(), true);
				
				if (isset($response['error'])) {
					echo "เกิดข้อผิดพลาด: " . $response['error']['message'] . "<br>";
					break;
				}
				
				if (isset($response['proposal_open_contract'])) {
					$contract = $response['proposal_open_contract'];
					$loopno++ ;
					
					// แสดงข้อมูลสำคัญของออเดอร์
					echo "รอบที่ : " . $loopno . "<br>";
					echo "Contract ID: " . $contract['contract_id'] . "<br>";
					echo "Symbol: " . $contract['display_name'] . "<br>";
					echo "Buy price: " . $contract['buy_price'] . "<br>";
					echo "Current spot: " . $contract['current_spot'] . "<br>";
					echo "Entry spot: " . $contract['entry_spot'] . "<br>";
					echo "Current value: " . $contract['bid_price'] . "<br>";
					
					// คำนวณกำไร/ขาดทุน
					$profit = $contract['bid_price'] - $contract['buy_price'];
					$profit_percentage = ($profit / $contract['buy_price']) * 100;
					
					echo "Profit/Loss: " . $profit . " (" . number_format($profit_percentage, 2) . "%)<br>";
					
					// ตรวจสอบเงื่อนไขการขาย - ถ้ากำไรมากกว่าหรือเท่ากับ 50%
					if ($profit_percentage >= 50) {
						echo "กำไรถึง 50% ของเงินลงทุนแล้ว! ทำการขาย Contract อัตโนมัติ<br>";
						
						// ส่งคำสั่งขาย Contract
						$sell_request = json_encode([
							'sell' => $contract_id,
							'price' => $contract['bid_price']
						]);
						
						$this->client->send($sell_request);
						
						// รอรับผลการขาย
						$sell_response = json_decode($this->client->receive(), true);
						
						if (isset($sell_response['error'])) {
							echo "เกิดข้อผิดพลาดในการขาย: " . $sell_response['error']['message'] . "<br>";
						} else if (isset($sell_response['sell'])) {
							echo "ขาย Contract สำเร็จ!<br>";
							echo "ขายที่ราคา: " . $sell_response['sell']['sold_for'] . "<br>";
							echo "กำไร: " . $sell_response['sell']['profit'] . "<br>";
							break;
						}
					}
					
					// ถ้าออเดอร์จบแล้ว ให้หยุดการติดตาม
					if ($contract['status'] === 'sold') {
						echo "Contract ended. Result: " . ($contract['profit'] >= 0 ? "WIN" : "LOSS") . "<br>";
						echo "Profit/Loss: " . $contract['profit'] . "<br>";
						break;
					}
					
					// รอ 1 วินาทีก่อนแสดงข้อมูลใหม่
					sleep(1);
					echo "<br>-------------------------<br>";
				}
			} catch (Exception $e) {
				echo "เกิดข้อผิดพลาดในการรับข้อมูล: " . $e->getMessage() . "<br>";
				break;
			}
     } // end while loop
	} // end trackContractAndSell()



} // end class deriv 

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

function main($api,$symbol ) {
	// ตัวอย่างการใช้งาน
	try {    
		$interval = 60; // 1 นาที
		$count = 30; // จำนวนแท่งเทียน    
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
			$adx = TechnicalIndicators::calculateADX($highs, $lows, $closes,7);
			
			// แสดงผลลัพธ์
			echo "<h2>ข้อมูล Candlestick และตัวชี้วัดทางเทคนิคสำหรับ {$symbol}</h2>";
			echo "<div style='overflow-x: auto;'>";
			echo "<table id='myTable' border='1' cellpadding='5'>";
			echo '<thead>
				<tr>
					<th colspan=20>
					<input type="text" id="searchBox" placeholder="Search for data...">
					</th>
					
				</tr>
			</thead>';
			echo "<thead><tr>
					<th onclick='sortTable(0)'>เวลา</th>
					<th>เปิด</th>
					<th>สูงสุด</th>
					<th>ต่ำสุด</th>
					<th>ปิด</th>
					<th>Color</th>
					<th>EMA(3)</th>
					<th>EMA(5)</th>
					<th>MACD</th>
					<th>RSI(14)</th>
					<th>BB Middle</th>
					<th>BB Upper</th>
					<th>BB Lower</th>
					<th>ADX</th>
					<th>+DI</th>
					<th>-DI</th>
				  </tr></thead>";
			
			$max_display = min(count($times), 20); // แสดงเฉพาะ 20 แถวล่าสุด
			$start_index = count($times) - $max_display;

			$colorListAr = [] ;
			
			for ($i = $start_index; $i < count($times); $i++) {
				$time = date('Y-m-d H:i:s', $times[$i]);
				echo "<tr>";
				echo "<td>{$time}</td>";
				echo "<td>" . round($opens[$i], 4) . "</td>";
				echo "<td>" . round($highs[$i], 4) . "</td>";
				echo "<td>" . round($lows[$i], 4) . "</td>";
				echo "<td>" . round($closes[$i], 4) . "</td>";
				if ($closes[$i] > $opens[$i]) {
					$colorListAr[] = 'Green';
					echo "<td style='width:100px;background:green;color:white'>Green</td>";
				} else {
					$colorListAr[] = 'Red';
					echo "<td style='width:100px;background:red;color:white'>Red</td>";
				}
				echo "<td>" . (is_null($ema3[$i]) ? "-" : round($ema3[$i], 4)) . "</td>";
				echo "<td>" . (is_null($ema5[$i]) ? "-" : round($ema5[$i], 4)) . "</td>";
				echo "<td>" . (is_null($ema5[$i]) ? "-" : round($ema3[$i]-$ema5[$i], 4)) . "</td>";
				echo "<td>" . (is_null($rsi[$i]) ? "-" : round($rsi[$i], 2)) . "</td>";
				echo "<td>" . (is_null($bbands['middle'][$i]) ? "-" : round($bbands['middle'][$i], 4)) . "</td>";
				echo "<td>" . (is_null($bbands['upper'][$i]) ? "-" : round($bbands['upper'][$i], 4)) . "</td>";
				echo "<td>" . (is_null($bbands['lower'][$i]) ? "-" : round($bbands['lower'][$i], 4)) . "</td>";
				echo "<td style='baclground:whitesmoke'>" . (is_null($adx['adx'][$i]) ? "-" : round($adx['adx'][$i], 2)) . "</td>";
				echo "<td>" . (is_null($adx['plusDI'][$i]) ? "-" : round($adx['plusDI'][$i], 2)) . "</td>";
				echo "<td>" . (is_null($adx['minusDI'][$i]) ? "-" : round($adx['minusDI'][$i], 2)) . "</td>";
				echo "</tr>";
			}
			
			echo "</table>";
			$score = 0 ; 
			$lengthColor = count($colorListAr) ;
			$startIndex = $lengthColor - 5 ;
			$lastIndex  = $lengthColor - 1 ;
			$numGreen = 0 ; $numRed = 0 ;
			for ($i= $startIndex;$i<=$lastIndex;$i++) {
			  if ($colorListAr[$i] == 'Green') {
				$numGreen++ ;
			  } else {
				$numRed++ ;
			  }           	   
			}
			echo 'Score Green='. $numGreen  . '<br>';
			echo 'Score Red='  . $numRed  . '<br>';
			echo 'Last ADX = '  . $adx['adx'][$lastIndex]  . '<br>'; ?>

  <button type='button' id='' class='mBtn' onclick="GoPageTrade('<?=$symbol?>','CALL')">CALL</button>

 <button type='button' id='' class='mBtn' onclick="GoPageTrade('<?=$symbol?>','PUT')">PUT</button>
  
            
			<?php

			echo "</div>"; 
			return array($numGreen,$numRed);
			
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
					'macd'=> $ema3[$i]- $ema5[$i],
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
    
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
}

} // end main of class


function Descript() { ?>
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

<?php } // end func  
  
function MainPage() {

  $timestamp = time();
  $Derivapi = new DerivAPI('66726'); // ใช้ App ID ของคุณหรือ App ID สาธารณะ
  $symbol = "R_10";
  list($numGreen,$numRed,$adx) = main($Derivapi,$symbol);
  
  $symbol = "R_25";
  list($numGreen,$numRed,$adx) = main($Derivapi,$symbol);
  $symbol = "R_50";
  list($numGreen,$numRed,$adx) = main($Derivapi,$symbol);
  $symbol = "R_75";
  list($numGreen,$numRed,$adx) = main($Derivapi,$symbol);

  $endtimestamp = time();

  echo "<hr>Used Time=" .  ($endtimestamp-$timestamp);
  return;

  // ซื้อ Rise contract
  $symbol = 'R_75'; // Volatility 100 Index
  $duration = 1;
  $duration_unit = 'm'; // minutes
  $amount = 10; // USD
  $contractType = 'CALL';
  $buyResponse = $Derivapi->buyRiseFallContract($symbol, $duration, $duration_unit, $amount,$contractType);
  if ($buyResponse) {
     echo '<hr>';
	 echo "ซื้อสำเร็จ! $symbol <br>";
	 echo "Contract ID: " . $buyResponse['buy']['contract_id'] . "<br>";
	 echo "Price: " . $buyResponse['buy']['buy_price'] . "<br>";
	 echo "กำลังติดตามสถานะออเดอร์...<br>";
	 // ติดตามสถานะออเดอร์
	 $Derivapi->trackContractAndSell($buyResponse['buy']['contract_id']);
 }
  

} // END func MainPage()


MainPage();


?>
<script>

 document.addEventListener("DOMContentLoaded", () => {
    const table = document.getElementById("myTable");
    const headers = table.querySelectorAll("th");

    headers.forEach((header, index) => {
        header.addEventListener("click", () => sortTable(index));
    });
});

function sortTable(columnIndex) {
    const table = document.getElementById("myTable");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));
    const headers = table.querySelectorAll("th");

    // ตรวจสอบลำดับการเรียง (ascending หรือ descending)
    const isAscending = table.getAttribute("data-sort-order") === "asc";
    table.setAttribute("data-sort-order", isAscending ? "desc" : "asc");

    // ลบคลาส sort-asc และ sort-desc จากหัวคอลัมน์ทั้งหมด
    headers.forEach(header => {
        header.classList.remove("sort-asc", "sort-desc");
    });

    // เพิ่มคลาสเพื่อแสดงลูกศรในหัวคอลัมน์ที่ถูก sort
    const currentHeader = headers[columnIndex];
    currentHeader.classList.add(isAscending ? "sort-asc" : "sort-desc");

    rows.sort((a, b) => {
        const aValue = a.querySelectorAll("td")[columnIndex].textContent.trim();
        const bValue = b.querySelectorAll("td")[columnIndex].textContent.trim();

        // ตรวจสอบว่าคอลัมน์นี้เป็นวันที่หรือไม่
        if (isDateColumn(aValue)) {
            const dateA = new Date(aValue);
            const dateB = new Date(bValue);
            return isAscending ? dateA - dateB : dateB - dateA;
        } else if (!isNaN(aValue) && !isNaN(bValue)) {
            return isAscending ? aValue - bValue : bValue - aValue;
        } else {
            return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
        }
    });

    // ลบแถวทั้งหมดออกจาก tbody
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }

    // เพิ่มแถวที่เรียงลำดับแล้วกลับเข้าไป
    rows.forEach(row => tbody.appendChild(row));
}

// ตรวจสอบว่าข้อมูลเป็นวันที่หรือไม่
function isDateColumn(value) {
    const dateRegex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;
    return dateRegex.test(value);
}

function GoPageTrade(symbol,action) {

	    spage = 'https://thepapers.in/deriv/phpTradev2.php?symbol=' + symbol+'&action='+ action ;
		//alert(spage);
		window.open(spage,'_blank');


} // end func

 </script>

