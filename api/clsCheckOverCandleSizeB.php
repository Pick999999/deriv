<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//clsCheckOverCandleSizeB.php
class cls_CheckOverCandleSizeB {
    
    /**
     * คำนวณ Average True Range (ATR)
     * @param array $candles - Array ของข้อมูลแท่งเทียน
     * @param int $periods - จำนวนคาบ (default: 14)
     * @return float - ค่า ATR
     */
    public static function calculateATR($candles, $periods = 14) {
        $candleCount = count($candles);
        if ($candleCount < $periods + 1) {
            return 0;
        }
        
        $trueRanges = [];
        
        for ($i = 1; $i < $candleCount; $i++) {
            $current = $candles[$i];
            $previous = $candles[$i - 1];
            
            $tr1 = $current['high'] - $current['low'];
            $tr2 = abs($current['high'] - $previous['close']);
            $tr3 = abs($current['low'] - $previous['close']);
            
            $trueRanges[] = max($tr1, $tr2, $tr3);
        }
        
        // คำนวณ ATR จาก periods ล่าสุด
        $recentTR = array_slice($trueRanges, -$periods);
        return array_sum($recentTR) / count($recentTR);
    }
    
    /**
     * ตรวจสอบ ATR Filter
     * @param array $candles - Array ของข้อมูลแท่งเทียน
     * @param float $multiplier - ตัวคูณของ ATR (default: 2.0)
     * @param int $atrPeriods - จำนวนคาบ ATR (default: 14)
     * @return array - ผลการตรวจสอบ
     */
    public static function atrFilter($candles, $multiplier = 2.0, $atrPeriods = 14) {
        $candleCount = count($candles);
        if ($candleCount < 2) {
            return ['error' => 'ข้อมูลไม่เพียงพอ'];
        }
        
        $atr = self::calculateATR($candles, $atrPeriods);
        $currentCandle = $candles[$candleCount - 1];
        $bodySize = abs($currentCandle['close'] - $currentCandle['open']);
        
        $threshold = $atr * $multiplier;
        $isAbnormal = $bodySize > $threshold;
        
        return [
            'is_abnormal' => $isAbnormal,
            'body_size' => $bodySize,
            'atr' => $atr,
            'threshold' => $threshold,
            'ratio' => $atr > 0 ? $bodySize / $atr : 0,
            'signal' => $isAbnormal ? 'ATR_ABNORMAL' : 'ATR_NORMAL'
        ];
    }
    
    /**
     * ตรวจสอบ Volume Spike
     * @param array $candles - Array ของข้อมูลแท่งเทียน
     * @param float $multiplier - ตัวคูณของ Average Volume (default: 3.0)
     * @param int $periods - จำนวนคาบในการคำนวณ Average Volume (default: 20)
     * @return array - ผลการตรวจสอบ
     */
    public static function volumeSpikeFilter($candles, $multiplier = 3.0, $periods = 20) {
        $candleCount = count($candles);
        if ($candleCount < $periods + 1) {
            return ['error' => 'ข้อมูลไม่เพียงพอ'];
        }
        
        // คำนวณ Average Volume จาก n periods ที่ผ่านมา (ไม่รวมแท่งปัจจุบัน)
        $totalVolume = 0;
        for ($i = $candleCount - $periods - 1; $i < $candleCount - 1; $i++) {
            $totalVolume += $candles[$i]['volume'];
        }
        $averageVolume = $totalVolume / $periods;
        
        $currentVolume = $candles[$candleCount - 1]['volume'];
        $threshold = $averageVolume * $multiplier;
        $isAbnormal = $currentVolume > $threshold;
        
        return [
            'is_abnormal' => $isAbnormal,
            'current_volume' => $currentVolume,
            'average_volume' => $averageVolume,
            'threshold' => $threshold,
            'ratio' => $averageVolume > 0 ? $currentVolume / $averageVolume : 0,
            'signal' => $isAbnormal ? 'VOLUME_SPIKE' : 'VOLUME_NORMAL'
        ];
    }
    
    /**
     * ตรวจสอบ Price Gap
     * @param array $candles - Array ของข้อมูลแท่งเทียน
     * @param float $multiplier - ตัวคูณของ Average Gap (default: 2.0)
     * @param int $periods - จำนวนคาบในการคำนวณ Average Gap (default: 20)
     * @return array - ผลการตรวจสอบ
     */
    public static function priceGapFilter($candles, $multiplier = 2.0, $periods = 20) {
        $candleCount = count($candles);
        if ($candleCount < $periods + 2) {
            return ['error' => 'ข้อมูลไม่เพียงพอ'];
        }
        
        // คำนวณ gaps จาก periods ที่ผ่านมา
        $gaps = [];
        for ($i = $candleCount - $periods - 1; $i < $candleCount - 1; $i++) {
            $currentOpen = $candles[$i]['open'];
            $previousClose = $candles[$i - 1]['close'];
            $gaps[] = abs($currentOpen - $previousClose);
        }
        
        $averageGap = array_sum($gaps) / count($gaps);
        
        // คำนวณ gap ปัจจุบัน
        $currentOpen = $candles[$candleCount - 1]['open'];
        $previousClose = $candles[$candleCount - 2]['close'];
        $currentGap = abs($currentOpen - $previousClose);
        
        $threshold = $averageGap * $multiplier;
        $isAbnormal = $currentGap > $threshold;
        
        return [
            'is_abnormal' => $isAbnormal,
            'current_gap' => $currentGap,
            'average_gap' => $averageGap,
            'threshold' => $threshold,
            'ratio' => $averageGap > 0 ? $currentGap / $averageGap : 0,
            'signal' => $isAbnormal ? 'GAP_ABNORMAL' : 'GAP_NORMAL'
        ];
    }
    
    /**
     * ตรวจสอบ Wick Size (Upper และ Lower Shadow)
     * @param array $candles - Array ของข้อมูลแท่งเทียน
     * @param float $multiplier - ตัวคูณของ Average Wick (default: 2.5)
     * @param int $periods - จำนวนคาบ (default: 20)
     * @return array - ผลการตรวจสอบ
     */
    public static function wickSizeFilter($candles, $multiplier = 2.5, $periods = 20) {
        $candleCount = count($candles);
        if ($candleCount < $periods + 1) {
            return ['error' => 'ข้อมูลไม่เพียงพอ'];
        }
        
        $currentCandle = $candles[$candleCount - 1];
        $currentUpper = $currentCandle['high'] - max($currentCandle['open'], $currentCandle['close']);
        $currentLower = min($currentCandle['open'], $currentCandle['close']) - $currentCandle['low'];
        
        // คำนวณ average wick sizes
        $totalUpper = 0;
        $totalLower = 0;
        
        for ($i = $candleCount - $periods - 1; $i < $candleCount - 1; $i++) {
            $candle = $candles[$i];
            $upperWick = $candle['high'] - max($candle['open'], $candle['close']);
            $lowerWick = min($candle['open'], $candle['close']) - $candle['low'];
            
            $totalUpper += $upperWick;
            $totalLower += $lowerWick;
        }
        
        $avgUpper = $totalUpper / $periods;
        $avgLower = $totalLower / $periods;
        
        $upperThreshold = $avgUpper * $multiplier;
        $lowerThreshold = $avgLower * $multiplier;
        
        $upperAbnormal = $currentUpper > $upperThreshold;
        $lowerAbnormal = $currentLower > $lowerThreshold;
        $isAbnormal = $upperAbnormal || $lowerAbnormal;
        
        return [
            'is_abnormal' => $isAbnormal,
            'upper_wick' => $currentUpper,
            'lower_wick' => $currentLower,
            'avg_upper_wick' => $avgUpper,
            'avg_lower_wick' => $avgLower,
            'upper_abnormal' => $upperAbnormal,
            'lower_abnormal' => $lowerAbnormal,
            'signal' => $isAbnormal ? 'WICK_ABNORMAL' : 'WICK_NORMAL'
        ];
    }
    
    /**
     * Multi-Condition Filter - รวมทุกเงื่อนไข
     * @param array $candles - Array ของข้อมูลแท่งเทียน
     * @param array $config - การตั้งค่าต่างๆ
     * @return array - ผลการวิเคราะห์รวม
     */
    public static function CheckOverCandleSizeB($candles, $config = []) {
        // ค่า default configurations
        $defaultConfig = [
            'atr_multiplier' => 2.0,
            'atr_periods' => 14,
            'volume_multiplier' => 3.0,
            'volume_periods' => 20,
            'gap_multiplier' => 2.0,
            'gap_periods' => 20,
            'wick_multiplier' => 2.5,
            'wick_periods' => 20,
            'min_abnormal_conditions' => 1, // จำนวนเงื่อนไขขั้นต่ำที่ต้องเป็น true
        ];
        
        $config = array_merge($defaultConfig, $config);
        
        // ทำการตรวจสอบแต่ละเงื่อนไข
        $atrResult = self::atrFilter($candles, $config['atr_multiplier'], $config['atr_periods']);
        $volumeResult = self::volumeSpikeFilter($candles, $config['volume_multiplier'], $config['volume_periods']);
        $gapResult = self::priceGapFilter($candles, $config['gap_multiplier'], $config['gap_periods']);
        $wickResult = self::wickSizeFilter($candles, $config['wick_multiplier'], $config['wick_periods']);
        
        // ตรวจสอบ errors
        $errors = [];
        if (isset($atrResult['error'])) $errors[] = 'ATR: ' . $atrResult['error'];
        if (isset($volumeResult['error'])) $errors[] = 'Volume: ' . $volumeResult['error'];
        if (isset($gapResult['error'])) $errors[] = 'Gap: ' . $gapResult['error'];
        if (isset($wickResult['error'])) $errors[] = 'Wick: ' . $wickResult['error'];
        
        if (!empty($errors)) {
            return ['error' => implode(', ', $errors)];
        }
        
        // นับจำนวนเงื่อนไขที่ผิดปกติ
        $abnormalConditions = [];
        $abnormalCount = 0;
        
        if ($atrResult['is_abnormal']) {
            $abnormalConditions[] = 'ATR_FILTER';
            $abnormalCount++;
        }
        if ($volumeResult['is_abnormal']) {
            $abnormalConditions[] = 'VOLUME_SPIKE';
            $abnormalCount++;
        }
        if ($gapResult['is_abnormal']) {
            $abnormalConditions[] = 'PRICE_GAP';
            $abnormalCount++;
        }
        if ($wickResult['is_abnormal']) {
            $abnormalConditions[] = 'WICK_SIZE';
            $abnormalCount++;
        }
        
        $isAbnormal = $abnormalCount >= $config['min_abnormal_conditions'];
        
        // กำหนดระดับความรุนแรง
        $severity = 'LOW';
        if ($abnormalCount >= 3) {
            $severity = 'HIGH';
        } elseif ($abnormalCount >= 2) {
            $severity = 'MEDIUM';
        } elseif ($abnormalCount >= 1) {
            $severity = 'LOW';
        }
        
        // คำแนะนำการเทรด
        $recommendation = 'NORMAL_TRADING';
        $waitPeriods = 0;
        
        if ($isAbnormal) {
            switch ($severity) {
                case 'HIGH':
                    $recommendation = 'AVOID_TRADING';
                    $waitPeriods = 5; // รอ 5 แท่ง
                    break;
                case 'MEDIUM':
                    $recommendation = 'WAIT_FOR_CONFIRMATION';
                    $waitPeriods = 3; // รอ 3 แท่ง
                    break;
                case 'LOW':
                    $recommendation = 'CAUTIOUS_TRADING';
                    $waitPeriods = 2; // รอ 2 แท่ง
                    break;
            }
        }
        
        return [
            'is_abnormal' => $isAbnormal,
            'abnormal_count' => $abnormalCount,
            'abnormal_conditions' => $abnormalConditions,
            'severity' => $severity,
            'recommendation' => $recommendation,
            'wait_periods' => $waitPeriods,
            'filters' => [
                'atr' => $atrResult,
                'volume' => $volumeResult,
                'gap' => $gapResult,
                'wick' => $wickResult
            ],
            'summary' => [
                'total_conditions' => 4,
                'abnormal_conditions' => $abnormalCount,
                'abnormal_percentage' => round(($abnormalCount / 4) * 100, 2)
            ]
        ];
    }
    
    /**
     * แสดงผลลัพธ์ในรูปแบบที่อ่านง่าย
     * @param array $result - ผลจาก CheckOverCandleSizeB
     * @return string - ข้อความแสดงผล
     */
    public static function formatResult($result) {
        if (isset($result['error'])) {
            return "❌ Error: " . $result['error'];
        }
        
        $output = "=== Multi-Condition Filter Analysis ===\n";
        $output .= "🎯 Overall Status: " . ($result['is_abnormal'] ? "⚠️ ABNORMAL" : "✅ NORMAL") . "\n";
        $output .= "📊 Abnormal Conditions: {$result['abnormal_count']}/4 ({$result['summary']['abnormal_percentage']}%)\n";
        $output .= "🚨 Severity Level: {$result['severity']}\n";
        $output .= "💡 Recommendation: {$result['recommendation']}\n";
        
        if ($result['wait_periods'] > 0) {
            $output .= "⏳ Wait for: {$result['wait_periods']} candles\n";
        }
        
        $output .= "\n--- Detailed Analysis ---\n";
        
        // ATR Filter
        $atr = $result['filters']['atr'];
        $output .= "📈 ATR Filter: " . ($atr['is_abnormal'] ? "⚠️ ABNORMAL" : "✅ NORMAL") . "\n";
        $output .= "   Body: " . number_format($atr['body_size'], 4) . " | ATR: " . number_format($atr['atr'], 4) . " | Ratio: " . number_format($atr['ratio'], 2) . "\n";
        
        // Volume Filter
        $vol = $result['filters']['volume'];
        $output .= "📊 Volume Filter: " . ($vol['is_abnormal'] ? "⚠️ SPIKE" : "✅ NORMAL") . "\n";
        $output .= "   Current: " . number_format($vol['current_volume']) . " | Avg: " . number_format($vol['average_volume']) . " | Ratio: " . number_format($vol['ratio'], 2) . "\n";
        
        // Gap Filter
        $gap = $result['filters']['gap'];
        $output .= "📉 Gap Filter: " . ($gap['is_abnormal'] ? "⚠️ ABNORMAL" : "✅ NORMAL") . "\n";
        $output .= "   Current: " . number_format($gap['current_gap'], 4) . " | Avg: " . number_format($gap['average_gap'], 4) . " | Ratio: " . number_format($gap['ratio'], 2) . "\n";
        
        // Wick Filter
        $wick = $result['filters']['wick'];
        $output .= "🕯️ Wick Filter: " . ($wick['is_abnormal'] ? "⚠️ ABNORMAL" : "✅ NORMAL") . "\n";
        $output .= "   Upper: " . number_format($wick['upper_wick'], 4) . " | Lower: " . number_format($wick['lower_wick'], 4) . "\n";
        
        if ($result['is_abnormal']) {
            $output .= "\n🚨 Abnormal Conditions Detected: " . implode(', ', $result['abnormal_conditions']) . "\n";
        }
        
        return $output;
    }
} // end class

function HowtoUse() { 





// ===== ตัวอย่างการใช้งาน =====

// ข้อมูลตัวอย่าง
$sampleCandles = [
    ['open' => 100, 'high' => 102, 'low' => 99, 'close' => 101, 'volume' => 1000],
    ['open' => 101, 'high' => 103, 'low' => 100, 'close' => 102, 'volume' => 1200],
    ['open' => 102, 'high' => 104, 'low' => 101, 'close' => 103, 'volume' => 1100],
    ['open' => 103, 'high' => 105, 'low' => 102, 'close' => 104, 'volume' => 1300],
    ['open' => 104, 'high' => 106, 'low' => 103, 'close' => 105, 'volume' => 1250],
    ['open' => 105, 'high' => 107, 'low' => 104, 'close' => 106, 'volume' => 1400],
    ['open' => 106, 'high' => 108, 'low' => 105, 'close' => 107, 'volume' => 1350],
    ['open' => 107, 'high' => 109, 'low' => 106, 'close' => 108, 'volume' => 1500],
    ['open' => 108, 'high' => 110, 'low' => 107, 'close' => 109, 'volume' => 1450],
    ['open' => 109, 'high' => 111, 'low' => 108, 'close' => 110, 'volume' => 1600],
    ['open' => 110, 'high' => 112, 'low' => 109, 'close' => 111, 'volume' => 1550],
    ['open' => 111, 'high' => 113, 'low' => 110, 'close' => 112, 'volume' => 1700],
    ['open' => 112, 'high' => 114, 'low' => 111, 'close' => 113, 'volume' => 1650],
    ['open' => 113, 'high' => 115, 'low' => 112, 'close' => 114, 'volume' => 1800],
    ['open' => 114, 'high' => 116, 'low' => 113, 'close' => 115, 'volume' => 1750],
    ['open' => 115, 'high' => 117, 'low' => 114, 'close' => 116, 'volume' => 1900],
    ['open' => 116, 'high' => 118, 'low' => 115, 'close' => 117, 'volume' => 1850],
    ['open' => 117, 'high' => 119, 'low' => 116, 'close' => 118, 'volume' => 2000],
    ['open' => 118, 'high' => 120, 'low' => 117, 'close' => 119, 'volume' => 1950],
    ['open' => 119, 'high' => 121, 'low' => 118, 'close' => 120, 'volume' => 2100],
    ['open' => 120, 'high' => 122, 'low' => 119, 'close' => 121, 'volume' => 2050],
    // แท่งที่มีหลายความผิดปกติ
    ['open' => 121, 'high' => 135, 'low' => 118, 'close' => 130, 'volume' => 8000] // body ใหญ่, volume สูง, wick ยาว
];

$n = 3;
$a = array_slice($sampleCandles, 0, $n) ;

//echo $sampleCandles[$n]['open'] ; return;

echo "=== ตัวอย่างการใช้งาน Multi-Condition Filter ===\n\n";

// การตั้งค่าแบบกำหนดเอง
$customConfig = [
    'atr_multiplier' => 2.0,
    'volume_multiplier' => 3.0,
    'gap_multiplier' => 2.0,
    'wick_multiplier' => 2.5,
    'min_abnormal_conditions' => 1
];

$result = cls_CheckOverCandleSizeB::CheckOverCandleSizeB($sampleCandles, $customConfig);

// แสดงผลลัพธ์
echo cls_CheckOverCandleSizeB::formatResult($result);
echo "\n\n";

// แสดงผลลัพธ์แบบ raw data
echo "=== Raw Result Data ===\n";
print_r($result); 

echo "<hr>" ;
$ss = JSON_ENCODE($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
echo '<pre>' . $ss . '</pre>';

//******************
// วิเคราะห์
$result = cls_CheckOverCandleSizeB::CheckOverCandleSizeB($sampleCandles, [
    'atr_multiplier' => 2.0,
    'volume_multiplier' => 3.0,
    'min_abnormal_conditions' => 1
]);

// ตัดสินใจเทรด
switch($result['recommendation']) {
    case 'AVOID_TRADING':
        echo "🚫 หยุดเทรด - ความเสี่ยงสูงมาก";
        break;
    case 'WAIT_FOR_CONFIRMATION':
        echo "⏳ รอสัญญาณยืนยัน {$result['wait_periods']} แท่ง";
        break;
    case 'CAUTIOUS_TRADING':
        echo "⚠️ เทรดด้วยความระมัดระวัง";
        break;
    default:
        echo "✅ เทรดตามปกติได้";
}

/*
🔍 4 เงื่อนไขหลัก:
1. ATR Filter

ตรวจ body size เทียบกับ Average True Range
body_size > ATR × 2 = ผิดปกติ

2. Volume Spike Filter

ตรวจ volume ที่เพิ่มขึ้นผิดปกติ
current_volume > avg_volume × 3 = volume spike

3. Price Gap Filter

ตรวจ gap ระหว่างแท่งเทียน
current_gap > avg_gap × 2 = gap ผิดปกติ

4. Wick Size Filter

ตรวจ upper/lower shadow ที่ยาวผิดปกติ
wick_size > avg_wick × 2.5 = wick ผิดปกติ

📊 ระดับความรุนแรง:

HIGH: 3-4 เงื่อนไข → AVOID_TRADING (รอ 5 แท่ง)
MEDIUM: 2 เงื่อนไข → WAIT_FOR_CONFIRMATION (รอ 3 แท่ง)
LOW: 1 เงื่อนไข → CAUTIOUS_TRADING (รอ 2 แท่ง)

*/

} // end function

HowtoUse();

?>