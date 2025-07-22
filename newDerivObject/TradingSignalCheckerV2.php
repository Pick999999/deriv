<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class TradingSignalChecker {
    // ข้อมูล EMA และ Candlestick เป็น array
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    /**
     * ตรวจสอบว่าตลาดอยู่ในภาวะที่ควรหลีกเลี่ยงการเทรดหรือไม่
     * @return array รายการสัญญาณเสี่ยง
     */
    public function analyzeSignals() {
        $results = [];
        
        foreach ($this->data as $i => $dayData) {
            $ema3 = $dayData['ema3'];
            $ema5 = $dayData['ema5'];
            $candle = $dayData['candle'];
            $emaDiff = $ema3 - $ema5;
            
            $signals = [];
            
            // 1. ตลาดตัดกันบ่อย (Whipsaw)
            if ($this->isWhipsawMarket($ema3, $ema5, $emaDiff)) {
                $signals[] = "EMA3 และ EMA5 ตัดกันบ่อย (ตลาด Sideway)";
            }
            
            // 2. แท่งเทียน Doji หรือ Spinning Top
            if ($this->isDojiOrSpinningTop($candle)) {
                $signals[] = "พบแท่งเทียน Doji/Spinning Top (ตลาดลังเล)";
            }
            
            // 3. แนวโน้มเกินขอบเขต (Overextended)
            if ($this->isOverextendedTrend($ema3, $ema5, $emaDiff)) {
                $signals[] = "แนวโน้มเกินขอบเขต (อาจเกิด Pullback)";
            }
            
            // 4. แรงซื้อ/ขายอ่อน (Weak Momentum)
            if ($this->isWeakMomentum($candle, $ema3, $ema5)) {
                $signals[] = "แรงซื้อ/ขายอ่อน (ปิดใกล้เส้น EMA)";
            }
            
            // 5. ตลาด Sideway (EMA แนบกัน)
            if ($this->isSidewayMarket($ema3, $ema5, $emaDiff)) {
                $signals[] = "ตลาด Sideway (EMA แนบกัน)";
            }
            
            // 6. มีการปฏิเสธ (Rejection) แต่ไม่มีการตามมา
            if ($this->hasFalseRejection($candle)) {
                $signals[] = "มีสัญญาณ Rejection แต่ไม่มีการ Confirm";
            }
            
            $results[] = [
                'date' => $dayData['date'] ?? 'Day ' . ($i + 1),
                'signals' => $signals,
                'should_avoid' => !empty($signals),
                'ema3' => $ema3,
                'ema5' => $ema5,
                'ema_diff' => $emaDiff,
                'candle' => $candle
            ];
        }
        
        return $results;
    }
    
    private function isWhipsawMarket($ema3, $ema5, $emaDiff) {
        return abs($emaDiff) < ($ema5 * 0.001);
    }
    
    private function isDojiOrSpinningTop($candle) {
		return;
        $bodySize = abs($candle['close'] - $candle['open']);
        $totalRange = $candle['high'] - $candle['low'];
		if ($totalRange === 0 ) {
			return '';
		} else {        
			$isDoji = ($bodySize / $totalRange) < 0.05;
			
			$upperWick = $candle['high'] - max($candle['open'], $candle['close']);
			$lowerWick = min($candle['open'], $candle['close']) - $candle['low'];
			$isSpinningTop = ($bodySize / $totalRange) < 0.3 && 
							($upperWick / $totalRange) > 0.3 && 
							($lowerWick / $totalRange) > 0.3;
		}         
        return $isDoji || $isSpinningTop;
    }
    
    private function isOverextendedTrend($ema3, $ema5, $emaDiff) {
        $emaDiffPercentage = abs($emaDiff) / $ema5;
        return $emaDiffPercentage > 0.02;
    }
    
    private function isWeakMomentum($candle, $ema3, $ema5) {
        $distanceToEMA3 = abs($candle['close'] - $ema3);
        $distanceToEMA5 = abs($candle['close'] - $ema5);
        
        return ($distanceToEMA3 / $candle['close']) < 0.002 || 
               ($distanceToEMA5 / $candle['close']) < 0.002;
    }
    
    private function isSidewayMarket($ema3, $ema5, $emaDiff) {
        return abs($emaDiff) < ($ema5 * 0.005);
    }
    
    private function hasFalseRejection($candle) {
        $upperWick = $candle['high'] - max($candle['open'], $candle['close']);
        $lowerWick = min($candle['open'], $candle['close']) - $candle['low'];
        $totalRange = $candle['high'] - $candle['low'];
        
        $hasLongUpperWick = ($upperWick / $totalRange) > 0.5;
        $hasLongLowerWick = ($lowerWick / $totalRange) > 0.5;
        
        $bodySize = abs($candle['close'] - $candle['open']);
        $weakBody = ($bodySize / $totalRange) < 0.3;
        
        return ($hasLongUpperWick || $hasLongLowerWick) && $weakBody;
    }
}

// ตัวอย่างข้อมูล input (array ของแต่ละวัน)
$historicalData = [
    [
        'date' => '2023-05-01',
        'ema3' => 100.50,
        'ema5' => 100.45,
        'candle' => [
            'open' => 100.50,
            'high' => 101.20,
            'low' => 100.10,
            'close' => 100.80
        ]
    ],
    [
        'date' => '2023-05-02',
        'ema3' => 101.20,
        'ema5' => 100.90,
        'candle' => [
            'open' => 100.80,
            'high' => 101.50,
            'low' => 100.70,
            'close' => 101.30
        ]
    ],
    [
        'date' => '2023-05-03',
        'ema3' => 101.25,
        'ema5' => 101.10,
        'candle' => [
            'open' => 101.30,
            'high' => 101.35,
            'low' => 101.10,
            'close' => 101.15
        ]
    ],
    [
        'date' => '2023-05-04',
        'ema3' => 101.18,
        'ema5' => 101.15,
        'candle' => [
            'open' => 101.15,
            'high' => 101.20,
            'low' => 101.00,
            'close' => 101.05
        ]
    ],
    [
        'date' => '2023-05-05',
        'ema3' => 101.10,
        'ema5' => 101.12,
        'candle' => [
            'open' => 101.05,
            'high' => 101.10,
            'low' => 100.80,
            'close' => 100.85
        ]
    ]
];


 $st = "";   
 $sFileName = 'dataTest.json';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file); 

 $sdata = JSON_DECODE($st,true) ;
 $historicalDataA = $sdata['AnalysisData'] ;
 $historicalData = array();
 for ($i=0;$i<=count($historicalDataA)-1;$i++) {
	 //$sObj = new stdClass();
	 $sObj = array();

	 $sObj['date'] = intval($historicalDataA[$i]['timestamp']);
	 $sObj['ema3'] =  $historicalDataA[$i]['ema3'];
	 $sObj['ema5'] =  $historicalDataA[$i]['ema5'];
	 //$sCandle = new stdClass();
	 $sCandle = array();
	 $sCandle['open'] = $historicalDataA[$i]['open'];
	 $sCandle['high'] = $historicalDataA[$i]['high'];
	 $sCandle['low'] = $historicalDataA[$i]['low'];
	 $sCandle['close'] = $historicalDataA[$i]['close'];
	 $sObj['candle'] = $sCandle ;
	 $historicalData[] = $sObj ;
 }
 //print_r($historicalData);
 //return ;
 
// สร้างอ็อบเจ็กต์และวิเคราะห์ข้อมูล
$signalChecker = new TradingSignalChecker($historicalData);
$analysisResults = $signalChecker->analyzeSignals();

// แสดงผลลัพธ์
echo "<h2>ผลการวิเคราะห์จุดที่ไม่ควรเข้าเทรด</h2>";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
echo "<tr>
        <th>วันที่</th>
        <th>EMA3</th>
        <th>EMA5</th>
        <th>ผลต่าง</th>
        <th>สัญญาณเสี่ยง</th>
        <th>ควรหลีกเลี่ยง?</th>
      </tr>";

foreach ($analysisResults as $result) {
    $signalsText = empty($result['signals']) ? 'ไม่มีสัญญาณเสี่ยง' : implode('<br>', $result['signals']);
    $shouldAvoid = $result['should_avoid'] ? 'ใช่' : 'ไม่';
    
    echo "<tr>
            <td>{$result['date']}</td>
            <td>{$result['ema3']}</td>
            <td>{$result['ema5']}</td>
            <td>" . number_format($result['ema_diff'], 4) . "</td>
            <td>{$signalsText}</td>
            <td>{$shouldAvoid}</td>
          </tr>";
}

echo "</table>";

// แสดงตัวอย่างข้อมูล Candlestick
echo "<h3>ตัวอย่างข้อมูล Candlestick ล่าสุด:</h3>";
$lastDay = end($historicalData);
echo "<pre>" . print_r($lastDay['candle'], true) . "</pre>";
?>


