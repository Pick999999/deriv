<?php
function analyzeCandlesticks($candleData) {
    // Helper function to calculate EMA
    function calculateEMA($data, $period) {
        $k = 2 / ($period + 1);
        $ema = $data[0]['close'];
        return array_map(function($candle) use (&$ema, $k) {
            $ema = ($candle['close'] * $k) + ($ema * (1 - $k));
            return $ema;
        }, $data);
    }

    // Helper function to calculate RSI
    function calculateRSI($data, $period = 14) {
        $gains = [];
        $losses = [];
        
        // Calculate price changes
        for($i = 1; $i < count($data); $i++) {
            $change = $data[$i]['close'] - $data[$i-1]['close'];
            $gains[] = $change > 0 ? $change : 0;
            $losses[] = $change < 0 ? -$change : 0;
        }
        
        // Calculate initial average gain and loss
        $avgGain = array_sum(array_slice($gains, 0, $period)) / $period;
        $avgLoss = array_sum(array_slice($losses, 0, $period)) / $period;
        
        $rsi = [];
        $currentGain = $avgGain;
        $currentLoss = $avgLoss;
        
        // First RSI value
        $rsi[] = 100 - (100 / (1 + $currentGain / ($currentLoss ?: 1)));
        
        // Calculate subsequent RSI values
        for($i = $period + 1; $i < count($data); $i++) {
            $currentGain = (($currentGain * ($period - 1)) + ($gains[$i-1] ?? 0)) / $period;
            $currentLoss = (($currentLoss * ($period - 1)) + ($losses[$i-1] ?? 0)) / $period;
            $rsi[] = 100 - (100 / (1 + $currentGain / ($currentLoss ?: 1)));
        }
        
        // Pad initial values with empty strings
        return array_merge(array_fill(0, $period, ""), $rsi);
    }

    // Helper function to get candle color
    function getCandleColor($candle) {
        if ($candle['close'] > $candle['open']) return "Green";
        if ($candle['close'] < $candle['open']) return "Red";
        return "Equal";
    }

    // Helper function to detect turn points
    function detectTurnPoints($emaValues) {
        return array_map(function($value, $i) use ($emaValues) {
            if ($i < 2 || $i >= count($emaValues) - 1) return "";
            if ($emaValues[$i-1] > $value && $emaValues[$i+1] > $value) return "TurnDown";
            if ($emaValues[$i-1] < $value && $emaValues[$i+1] < $value) return "TurnUp";
            return "";
        }, $emaValues, array_keys($emaValues));
    }

    // Helper function to calculate slope
    function calculateSlope($values, $index) {
        if ($index < 1) return 0;
        return $values[$index] - $values[$index - 1];
    }

    // Helper function to get slope direction
    function getSlopeDirection($slope) {
        if (abs($slope) < 0.0001) return "Pararell";
        return $slope > 0 ? "Up" : "Down";
    }

    // Helper function to count candle colors between turn points
    function countCandleColorsBetweenTurnPoints($candleData, $turnPoints, $currentIndex) {
        $counts = ['Green' => 0, 'Red' => 0, 'Equal' => 0];
        $lastTurnPointIndex = -1;

        // Find the last turn point before current index
        for($i = $currentIndex - 1; $i >= 0; $i--) {
            if($turnPoints[$i]) {
                $lastTurnPointIndex = $i;
                break;
            }
        }

        // If no previous turn point found, return zeros
        if($lastTurnPointIndex === -1) return $counts;

        // Count colors between last turn point and current index
        for($i = $lastTurnPointIndex; $i <= $currentIndex; $i++) {
            $color = getCandleColor($candleData[$i]);
            $counts[$color]++;
        }

        return $counts;
    }

    // Calculate EMAs
    $ema3 = calculateEMA($candleData, 3);
    $ema5 = calculateEMA($candleData, 5);
    $ema7 = calculateEMA($candleData, 7);

    // Calculate RSI
    $rsiValues = calculateRSI($candleData);

    // Detect turn points
    $ema3TurnPoints = detectTurnPoints($ema3);
    $ema5TurnPoints = detectTurnPoints($ema5);

    $result = [];
    foreach($candleData as $i => $candle) {
        // Convert epoch to time
        $date = new DateTime("@{$candle['epoch']}");
        $minuteNo = $date->format('H:i');

        // Calculate slopes
        $ema3Slope = calculateSlope($ema3, $i);
        $ema5Slope = calculateSlope($ema5, $i);
        $ema7Slope = calculateSlope($ema7, $i);

        // Get previous candle colors
        $currentColor = getCandleColor($candle);
        $prevColor1 = $i > 0 ? getCandleColor($candleData[$i-1]) : "";
        $prevColor2 = $i > 1 ? getCandleColor($candleData[$i-2]) : "";
        $prevColor3 = $i > 2 ? getCandleColor($candleData[$i-3]) : "";

        // Calculate EMA crossovers
        $ema3ema5Cross = ($ema3[$i] > $ema5[$i] && $ema3[$i-1] <= $ema5[$i-1]) ? "Golden Cross" :
                        ($ema3[$i] < $ema5[$i] && $ema3[$i-1] >= $ema5[$i-1]) ? "Death Cross" : "";
        $ema5ema7Cross = ($ema5[$i] > $ema7[$i] && $ema5[$i-1] <= $ema7[$i-1]) ? "Golden Cross" :
                        ($ema5[$i] < $ema7[$i] && $ema5[$i-1] >= $ema7[$i-1]) ? "Death Cross" : "";

        // Calculate distance from last turn points
        $ema3TurnDistance = 0;
        $ema5TurnDistance = 0;
        for($j = $i; $j >= 0; $j--) {
            if($ema3TurnPoints[$j]) {
                $ema3TurnDistance = $i - $j;
                break;
            }
        }
        for($j = $i; $j >= 0; $j--) {
            if($ema5TurnPoints[$j]) {
                $ema5TurnDistance = $i - $j;
                break;
            }
        }

        // Count candle colors between turn points for EMA3
        $colorCounts = countCandleColorsBetweenTurnPoints($candleData, $ema3TurnPoints, $i);

        $result[] = [
            'CandleID' => (string)$candle['CandleID'],
            'MinuteNo' => $minuteNo,
            'ema3' => number_format($ema3[$i], 2),
            'ema5' => number_format($ema5[$i], 2),
            'ema7' => number_format($ema7[$i], 2),
            'rsi' => is_numeric($rsiValues[$i]) ? number_format($rsiValues[$i], 2) : "",
            'สีของแท่งเทียน' => $currentColor,
            'สีของแท่งเทียน ย้อนหลังไป 1 แท่ง' => $prevColor1,
            'สีของแท่งเทียน ย้อนหลังไป 2 แท่ง' => $prevColor2,
            'สีของแท่งเทียน ย้อนหลังไป 3 แท่ง' => $prevColor3,
            'ema3-ema5' => number_format($ema3[$i] - $ema5[$i], 2),
            'ema5-ema7' => number_format($ema5[$i] - $ema7[$i], 2),
            'ประเภทจุดกลับตัวของ ema3' => $ema3TurnPoints[$i],
            'ประเภทจุดกลับตัวของ ema5' => $ema5TurnPoints[$i],
            'ประเภทจุดกลับตัวของ ema7' => detectTurnPoints($ema7)[$i],
            'ประเภทจุดกลับตัวของ ema3 ย้อนหลังไป 1 แท่ง' => $i > 0 ? $ema3TurnPoints[$i-1] : "",
            'ประเภทจุดกลับตัวของ ema5 ย้อนหลังไป 1 แท่ง' => $i > 0 ? $ema5TurnPoints[$i-1] : "",
            'Slope Value ของ ema3' => number_format($ema3Slope, 2),
            'Slope Value ของ ema5' => number_format($ema5Slope, 2),
            'Slope Value ของ ema7' => number_format($ema7Slope, 2),
            'Slope Direction ของ ema3' => getSlopeDirection($ema3Slope),
            'Slope Direction ของ ema5' => getSlopeDirection($ema5Slope),
            'Slope Direction ของ ema7' => getSlopeDirection($ema7Slope),
            'เป็นจุดตัดกันของ ema3,ema5แบบไหน' => $ema3ema5Cross,
            'เป็นจุดตัดกันของ ema5,ema7แบบไหน' => $ema5ema7Cross,
            'ระยะห่างจากจุดกลับตัวของ ema3 จุดสุดท้าย' => $ema3TurnDistance,
            'ระยะห่างจากจุดกลับตัวของ ema5 จุดสุดท้าย' => $ema5TurnDistance,
            'จำนวนแท่งเทียนสีเขียว' => $colorCounts['Green'],
            'จำนวนแท่งเทียนสีแดง' => $colorCounts['Red'],
            'จำนวนแท่งเทียน Equal' => $colorCounts['Equal']
        ];
    }

    return $result;
}

// Example usage:
$candleData = [
    [
        "CandleID" => 1,
        "close" => 95210.65,
        "epoch" => 1736376240,
        "high" => 95228.25,
        "low" => 95205.65,
        "open" => 95205.95
    ]
    // ... more candle data ...
];

$analysis = analyzeCandlesticks($candleData);
print_r($analysis);
?>