<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class TradingSignalChecker {
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function analyzeSignals() {
    $results = [];
    $dataCount = count($this->data);
    
    for ($i = 0; $i < $dataCount; $i++) {
        $dayData = $this->data[$i];
        $previousDayData = $i > 0 ? $this->data[$i-1] : null;
        
        // ตรวจสอบข้อมูลที่จำเป็นมีครบถ้วน
        if (!$this->validateData($dayData)) {
            $results[] = [
                'date' => $dayData['date'] ?? 'Day ' . ($i + 1),
                'error' => 'Invalid data format',
                'should_avoid' => true
            ];
            continue;
        }
        
        $ema3 = $dayData['ema3'];
        $ema5 = $dayData['ema5'];
        $candle = $dayData['candle'];
        $emaDiff = $ema3 - $ema5;
        
        $signals = [];
        
        // 1. ตรวจสอบตลาด Sideway (ต้องมีข้อมูลวันก่อนหน้า)
        if ($previousDayData !== null && $this->isWhipsawMarket($ema3, $ema5, $emaDiff, $previousDayData)) {
            $signals[] = "การตัดกันของเส้น EMA เกิดขึ้นบ่อย (ตลาดไซด์เวย์)";
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
    
    private function validateData($dayData) {
        return isset($dayData['ema3'], $dayData['ema5'], $dayData['candle']['open'], 
               $dayData['candle']['high'], $dayData['candle']['low'], $dayData['candle']['close']) &&
               is_numeric($dayData['ema3']) && is_numeric($dayData['ema5']) &&
               is_numeric($dayData['candle']['open']) && is_numeric($dayData['candle']['high']) &&
               is_numeric($dayData['candle']['low']) && is_numeric($dayData['candle']['close']);
    } 

	private function isSidewayTrend($dataHistory, $lookbackPeriod = 5) {
    if (count($dataHistory) < $lookbackPeriod) return false;
    
    $totalCrossings = 0;
    $totalSmallDiffs = 0;
    
    for ($i = 1; $i < $lookbackPeriod; $i++) {
        $current = $dataHistory[$i];
        $previous = $dataHistory[$i-1];
        
        $currentDiff = $current['ema3'] - $current['ema5'];
        $previousDiff = $previous['ema3'] - $previous['ema5'];
        
        if (($currentDiff * $previousDiff) < 0) $totalCrossings++;
        if (abs($currentDiff) < ($current['ema5'] * 0.003)) $totalSmallDiffs++;
    }
    
    // ถ้ามีการตัดกันเกิน 50% ของช่วงเวลาที่ตรวจสอบ และความต่างเล็กน้อย
    return ($totalCrossings >= ($lookbackPeriod / 2)) && 
           ($totalSmallDiffs >= ($lookbackPeriod / 2));
}
    
     // ปรับปรุงเมธอด isWhipsawMarket
private function isWhipsawMarket($ema3, $ema5, $emaDiff, $previousDayData) {
    if ($previousDayData === null) {
        return false;
    }
    
    $prevEma3 = $previousDayData['ema3'];
    $prevEma5 = $previousDayData['ema5'];
    $prevEmaDiff = $prevEma3 - $prevEma5;
    
    // ตรวจสอบการเปลี่ยนทิศทาง
    $directionChanged = ($emaDiff * $prevEmaDiff) < 0;
    
    // ตรวจสอบความต่างไม่เกิน 0.3%
    $smallDifference = abs($emaDiff) < ($ema5 * 0.003);
    
    return $directionChanged && $smallDifference;
}
    
    private function isDojiOrSpinningTop($candle) {
        $totalRange = $candle['high'] - $candle['low'];
        
        // ตรวจสอบ Division by Zero
        if ($totalRange == 0) {
            return false; // ไม่สามารถเป็น Doji ได้ถ้า high == low
        }
        
        $bodySize = abs($candle['close'] - $candle['open']);
        $bodyRatio = $bodySize / $totalRange;
        
        $isDoji = $bodyRatio < 0.05;
        
        $upperWick = $candle['high'] - max($candle['open'], $candle['close']);
        $lowerWick = min($candle['open'], $candle['close']) - $candle['low'];
        
        $isSpinningTop = ($bodyRatio < 0.3) && 
                        (($upperWick / $totalRange) > 0.3) && 
                        (($lowerWick / $totalRange) > 0.3);
        
        return $isDoji || $isSpinningTop;
    }
    
    private function isOverextendedTrend($ema3, $ema5, $emaDiff) {
        if ($ema5 == 0) return false; // 防止除以零
        $emaDiffPercentage = abs($emaDiff) / $ema5;
        return $emaDiffPercentage > 0.02;
    }
    
    private function isWeakMomentum($candle, $ema3, $ema5) {
        if ($candle['close'] == 0) return false; // 防止除以零
        
        $distanceToEMA3 = abs($candle['close'] - $ema3);
        $distanceToEMA5 = abs($candle['close'] - $ema5);
        
        return ($distanceToEMA3 / $candle['close']) < 0.002 || 
               ($distanceToEMA5 / $candle['close']) < 0.002;
    }
    
    private function isSidewayMarket($ema3, $ema5, $emaDiff) {
        return abs($emaDiff) < ($ema5 * 0.005);
    }
    
    private function hasFalseRejection($candle) {
        $totalRange = $candle['high'] - $candle['low'];
        
        // ตรวจสอบ Division by Zero
        if ($totalRange == 0) {
            return false;
        }
        
        $upperWick = $candle['high'] - max($candle['open'], $candle['close']);
        $lowerWick = min($candle['open'], $candle['close']) - $candle['low'];
        
        $hasLongUpperWick = ($upperWick / $totalRange) > 0.5;
        $hasLongLowerWick = ($lowerWick / $totalRange) > 0.5;
        
        $bodySize = abs($candle['close'] - $candle['open']);
        $weakBody = ($bodySize / $totalRange) < 0.3;
        
        return ($hasLongUpperWick || $hasLongLowerWick) && $weakBody;
    } // end func 

	private function getRecentMovement($data, $period = 3) {
			$sumMovement = 0;
			for ($i = max(0, count($data)-$period); $i < count($data); $i++) {
				$sumMovement += $data[$i]['close'] - $data[$i]['open'];
			}
			return $sumMovement / $period;
    }


	private function calculateATR($data, $period = 14) {
		$trueRanges = [];
		for ($i = 1; $i < count($data); $i++) {
			$tr = max(
				$data[$i]['high'] - $data[$i]['low'],
				abs($data[$i]['high'] - $data[$i-1]['close']),
				abs($data[$i]['low'] - $data[$i-1]['close'])
			);
			$trueRanges[] = $tr;
		}
        return array_sum(array_slice($trueRanges, -$period)) / $period;
    } // end func




} //******************************* end class








// สร้าง HTML Table
function renderAnalysisTable($data) {
    $html = '<!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ผลวิเคราะห์เทรด</title>
        <style>
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }
            h1 {
                color: #2c3e50;
                text-align: center;
                margin-bottom: 30px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
                box-shadow: 0 2px 3px rgba(0,0,0,0.1);
            }
            th {
                background-color: #3498db;
                color: white;
                padding: 12px;
                text-align: left;
            }
            td {
                padding: 10px 12px;
                border-bottom: 1px solid #ddd;
                vertical-align: top;
            }
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
            .avoid-yes {
                color: #e74c3c;
                font-weight: bold;
            }
            .avoid-no {
                color: #2ecc71;
                font-weight: bold;
            }
            .signal-list {
                margin: 0;
                padding-left: 18px;
            }
            .signal-item {
                margin-bottom: 5px;
            }
            .json-container {
                background-color: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                border-left: 4px solid #3498db;
                margin-bottom: 30px;
                overflow-x: auto;
            }
            .section-title {
                color: #2c3e50;
                border-bottom: 2px solid #3498db;
                padding-bottom: 5px;
                margin-top: 40px;
            }
        </style>
    </head>
    <body>
        <h1>ผลวิเคราะห์การเทรด</h1>
        
        <h2 class="section-title">ข้อมูลในรูปแบบ JSON</h2>
        <div class="json-container">
            <pre>'.htmlspecialchars($jsonData).'</pre>
        </div>
        
        <h2 class="section-title">ผลวิเคราะห์</h2>
        <table>
            <thead>
                <tr>
                    <th>วันที่</th>
                    <th>EMA3</th>
                    <th>EMA5</th>
                    <th>ผลต่าง EMA</th>
                    <th>สัญญาณเตือน</th>
                    <th>ควรหลีกเลี่ยง?</th>
                    <th>ข้อมูล Candlestick</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($data as $item) {
        $signalsHtml = '<ul class="signal-list">';
        foreach ($item['signals'] as $signal) {
            $signalsHtml .= '<li class="signal-item">' . htmlspecialchars($signal) . '</li>';
        }
        $signalsHtml .= '</ul>';

        $shouldAvoid = $item['should_avoid'] ? 
                      '<span class="avoid-yes">ใช่ (ควรหลีกเลี่ยง)</span>' : 
                      '<span class="avoid-no">ไม่ (สามารถเทรดได้)</span>';

        $candleInfo = 'Open: ' . $item['candle']['open'] . '<br>' .
                      'High: ' . $item['candle']['high'] . '<br>' .
                      'Low: ' . $item['candle']['low'] . '<br>' .
                      'Close: ' . $item['candle']['close'];

        $html .= '<tr>
                    <td>' . htmlspecialchars($item['date']) . '</td>
                    <td>' . $item['ema3'] . '</td>
                    <td>' . $item['ema5'] . '</td>
                    <td>' . number_format($item['ema_diff'], 5) . '</td>
                    <td>' . $signalsHtml . '</td>
                    <td>' . $shouldAvoid . '</td>
                    <td>' . $candleInfo . '</td>
                  </tr>';
    }

    $html .= '</tbody>
        </table>
    </body>
    </html>';

    return $html;
} // end class
return ;


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
 
/*
$signalChecker = new TradingSignalChecker($historicalData);
$analysisData = $signalChecker->analyzeSignals();
echo '<h2>Count=' . count($analysisData) . '</h2>';


// แปลงเป็น JSON
//$jsonData = json_encode($analysisData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
// แสดงผล HTML
echo renderAnalysisTable($analysisData);


*/
?>