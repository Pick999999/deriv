<?php

class CheckOverCandleSizeA {
    
    /**
     * คำนวณ Body Size Ratio Filter
     * @param array $candles - Array ของข้อมูลแท่งเทียน [open, high, low, close, volume]
     * @param int $periods - จำนวนคาบในการคำนวณค่าเฉลี่ย (default: 20)
     * @param float $threshold - ค่าเกณฑ์การตรวจจับ (default: 2.5)
     * @return array - [is_abnormal, body_size, average_body, ratio]
     */
    public static function bodySizeRatioFilter($candles, $periods = 20, $threshold = 2.5) {
        $candleCount = count($candles);
        
        if ($candleCount < $periods + 1) {
            return ['error' => 'ข้อมูลไม่เพียงพอ ต้องการอย่างน้อย ' . ($periods + 1) . ' แท่ง'];
        }
        
        // คำนวณ body size ของแท่งปัจจุบัน
        $currentCandle = $candles[$candleCount - 1];
        $currentBodySize = abs($currentCandle['close'] - $currentCandle['open']);
        
        // คำนวณ average body size จาก n periods ที่ผ่านมา (ไม่รวมแท่งปัจจุบัน)
        $totalBodySize = 0;
        for ($i = $candleCount - $periods - 1; $i < $candleCount - 1; $i++) {
            $bodySize = abs($candles[$i]['close'] - $candles[$i]['open']);
            $totalBodySize += $bodySize;
        }
        
        $averageBodySize = $totalBodySize / $periods;
        
        // คำนวณ ratio และตรวจสอบความผิดปกติ
        $ratio = $averageBodySize > 0 ? $currentBodySize / $averageBodySize : 0;
        $isAbnormal = $ratio > $threshold;
        
        return [
            'is_abnormal' => $isAbnormal,
            'current_body_size' => $currentBodySize,
            'average_body_size' => $averageBodySize,
            'ratio' => $ratio,
            'threshold' => $threshold,
            'signal' => $isAbnormal ? 'ABNORMAL_BODY' : 'NORMAL'
        ];
    }
    
    /**
     * คำนวณ Candle Momentum Filter
     * @param array $candles - Array ของข้อมูลแท่งเทียน
     * @param int $periods - จำนวนคาบในการคำนวณค่าเฉลี่ย (default: 14)
     * @param float $threshold - ค่าเกณฑ์การตรวจจับ (default: 2.0)
     * @return array - [is_abnormal, price_change, average_change, ratio]
     */
    public static function candleMomentumFilter($candles, $periods = 14, $threshold = 2.0) {
        $candleCount = count($candles);
        
        if ($candleCount < $periods + 1) {
            return ['error' => 'ข้อมูลไม่เพียงพอ ต้องการอย่างน้อย ' . ($periods + 1) . ' แท่ง'];
        }
        
        // คำนวณการเปลี่ยนแปลงของราคาปัจจุบัน
        $currentCandle = $candles[$candleCount - 1];
        $previousCandle = $candles[$candleCount - 2];
        $currentPriceChange = abs($currentCandle['close'] - $previousCandle['close']);
        
        // คำนวณ average price change จาก n periods ที่ผ่านมา
        $totalPriceChange = 0;
        for ($i = $candleCount - $periods; $i < $candleCount - 1; $i++) {
            $priceChange = abs($candles[$i]['close'] - $candles[$i - 1]['close']);
            $totalPriceChange += $priceChange;
        }
        
        $averagePriceChange = $totalPriceChange / $periods;
        
        // คำนวณ ratio และตรวจสอบความผิดปกติ
        $ratio = $averagePriceChange > 0 ? $currentPriceChange / $averagePriceChange : 0;
        $isAbnormal = $ratio > $threshold;
        
        return [
            'is_abnormal' => $isAbnormal,
            'current_price_change' => $currentPriceChange,
            'average_price_change' => $averagePriceChange,
            'ratio' => $ratio,
            'threshold' => $threshold,
            'signal' => $isAbnormal ? 'ABNORMAL_MOMENTUM' : 'NORMAL'
        ];
    }
    
    /**
     * รวม Filter ทั้งสองแบบ
     * @param array $candles - Array ของข้อมูลแท่งเทียน
     * @param array $config - การตั้งค่า [body_periods, body_threshold, momentum_periods, momentum_threshold]
     * @return array - ผลการวิเคราะห์รวม
     */
    public static function combinedFilter($candles, $config = []) {
        // ค่า default
        $bodyPeriods = $config['body_periods'] ?? 20;
        $bodyThreshold = $config['body_threshold'] ?? 2.5;
        $momentumPeriods = $config['momentum_periods'] ?? 14;
        $momentumThreshold = $config['momentum_threshold'] ?? 2.0;
        
        $bodyFilter = self::bodySizeRatioFilter($candles, $bodyPeriods, $bodyThreshold);
        $momentumFilter = self::candleMomentumFilter($candles, $momentumPeriods, $momentumThreshold);
        
        // ตรวจสอบว่ามี error หรือไม่
        if (isset($bodyFilter['error']) || isset($momentumFilter['error'])) {
            return ['error' => 'ข้อมูลไม่เพียงพอสำหรับการวิเคราะห์'];
        }
        
        $isAbnormal = $bodyFilter['is_abnormal'] || $momentumFilter['is_abnormal'];
        
        return [
            'is_abnormal' => $isAbnormal,
            'body_filter' => $bodyFilter,
            'momentum_filter' => $momentumFilter,
            'recommendation' => $isAbnormal ? 'WAIT_FOR_CONFIRMATION' : 'NORMAL_TRADING',
            'alerts' => [
                'abnormal_body' => $bodyFilter['is_abnormal'],
                'abnormal_momentum' => $momentumFilter['is_abnormal']
            ]
        ];
    }
}

// ===== ตัวอย่างการใช้งาน =====

// ข้อมูลตัวอย่าง (ในการใช้งานจริงให้ดึงจาก API หรือ Database)
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
    // แท่งที่มี body ใหญ่ผิดปกติ
    ['open' => 121, 'high' => 130, 'low' => 120, 'close' => 128, 'volume' => 5000]	
];

echo "=== ตัวอย่างการใช้งาน Trading Filters ===<br><br>";

// ทดสอบ Body Size Ratio Filter
echo "1. Body Size Ratio Filter:<br>";
$bodyResult = CheckOverCandleSizeA::bodySizeRatioFilter($sampleCandles, 20, 2.5);
print_r($bodyResult);
echo "<br>";

// ทดสอบ Candle Momentum Filter
echo "2. Candle Momentum Filter:<br>";
$momentumResult = CheckOverCandleSizeA::candleMomentumFilter($sampleCandles, 14, 2.0);
print_r($momentumResult);
echo "<br>";

// ทดสอบ Combined Filter
echo "3. Combined Filter:<br>";
$combinedResult = CheckOverCandleSizeA::combinedFilter($sampleCandles, [
    'body_periods' => 20,
    'body_threshold' => 2.5,
    'momentum_periods' => 14,
    'momentum_threshold' => 2.0
]);

print_r($combinedResult);
/*
echo "<hr>" ;
$ss = JSON_ENCODE($combinedResult, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
echo '<pre>' . $ss . '</pre>';
**/
// วิเคราะห์
$result = CheckOverCandleSizeA::combinedFilter($sampleCandles);

if ($result['is_abnormal']) {
    echo "⚠️ พบสัญญาณผิดปกติ - รอการยืนยันก่อนเข้าเทรด";
    if ($result['alerts']['abnormal_body']) {
        echo "- Body size ใหญ่ผิดปกติ\n";
    }
    if ($result['alerts']['abnormal_momentum']) {
        echo "- Price momentum สูงผิดปกติ\n"; 
    }
} else {
    echo "✅ สัญญาณปกติ - สามารถเทรดตามปกติ";
}





?>

