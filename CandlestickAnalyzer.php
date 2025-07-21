<?php

class CandlestickAnalyzer {
    private $candlestick;
    private $upperWickPercentage;
    private $bodyPercentage;
    private $lowerWickPercentage;

    public function __construct($time, $open, $high, $low, $close, 
                                $upperWickPercentage, 
                                $bodyPercentage, 
                                $lowerWickPercentage) {
        $this->candlestick = [
            'time' => $time,
            'open' => $open,
            'high' => $high,
            'low' => $low,
            'close' => $close
        ];
        
        $this->upperWickPercentage = $upperWickPercentage / 100;
        $this->bodyPercentage = $bodyPercentage / 100;
        $this->lowerWickPercentage = $lowerWickPercentage / 100;
    }

    public function analyzeCandle() {
        $totalRange = $this->candlestick['high'] - $this->candlestick['low'];
        $bodyRange = abs($this->candlestick['close'] - $this->candlestick['open']);
        
        $isGreenCandle = $this->candlestick['close'] >= $this->candlestick['open'];
        
        $upperWickLength = $this->candlestick['high'] - max($this->candlestick['close'], $this->candlestick['open']);
        $lowerWickLength = min($this->candlestick['close'], $this->candlestick['open']) - $this->candlestick['low'];
        
        $buySignal = $this->analyzeBuySignal($isGreenCandle, $upperWickLength, $lowerWickLength, $totalRange);
        $sellSignal = $this->analyzeSellSignal($isGreenCandle, $upperWickLength, $lowerWickLength, $totalRange);
        
        $nextCandleProbability = $this->predictNextCandleProbability($isGreenCandle, $buySignal, $sellSignal);
        $analysis = $this->generateThaiDescription($isGreenCandle, $upperWickLength, $lowerWickLength, $totalRange, $bodyRange);
        
        return [
            'time' => date('Y-m-d H:i:s', $this->candlestick['time']),
            'open' => round($this->candlestick['open'], 2),
            'high' => round($this->candlestick['high'], 2),
            'low' => round($this->candlestick['low'], 2),
            'close' => round($this->candlestick['close'], 2),
            'candleColor' => $isGreenCandle ? 'Green' : 'Red',
            'upperWickPercentage' => round($upperWickLength / $totalRange * 100, 2),
            'bodyPercentage' => round($bodyRange / $totalRange * 100, 2),
            'lowerWickPercentage' => round($lowerWickLength / $totalRange * 100, 2),
            'buySignal' => $buySignal,
            'sellSignal' => $sellSignal,
            'nextGreenCandleProbability' => $nextCandleProbability['green'],
            'nextRedCandleProbability' => $nextCandleProbability['red'],
            'thaiDescription' => $analysis['description'],
            'trend' => $analysis['trend']
        ];
    }

    private function analyzeBuySignal($isGreenCandle, $upperWickLength, $lowerWickLength, $totalRange) {
        $buyStrength = 0;
        
        if ($isGreenCandle) {
            if ($lowerWickLength / $totalRange >= $this->lowerWickPercentage) {
                $buyStrength += 30;
            }
            
            if ($upperWickLength / $totalRange <= $this->upperWickPercentage) {
                $buyStrength += 40;
            }
            
            $bodyRange = abs($this->candlestick['close'] - $this->candlestick['open']);
            if ($bodyRange / $totalRange >= $this->bodyPercentage) {
                $buyStrength += 30;
            }
        }
        
        return $buyStrength;
    }

    private function analyzeSellSignal($isGreenCandle, $upperWickLength, $lowerWickLength, $totalRange) {
        $sellStrength = 0;
        
        if (!$isGreenCandle) {
            if ($upperWickLength / $totalRange >= $this->upperWickPercentage) {
                $sellStrength += 30;
            }
            
            if ($lowerWickLength / $totalRange <= $this->lowerWickPercentage) {
                $sellStrength += 40;
            }
            
            $bodyRange = abs($this->candlestick['close'] - $this->candlestick['open']);
            if ($bodyRange / $totalRange >= $this->bodyPercentage) {
                $sellStrength += 30;
            }
        }
        
        return $sellStrength;
    }

    private function predictNextCandleProbability($isCurrentCandleGreen, $buySignal, $sellSignal) {
        // More complex prediction considering buy and sell signals
        $baseGreenProbability = $isCurrentCandleGreen ? 60 : 40;
        
        // Adjust probability based on signal strengths
        $signalDifference = $buySignal - $sellSignal;
        $probabilityAdjustment = $signalDifference / 10;
        
        $greenProbability = max(10, min(90, $baseGreenProbability + $probabilityAdjustment));
        $redProbability = 100 - $greenProbability;
        
        return [
            'green' => round($greenProbability, 2),
            'red' => round($redProbability, 2)
        ];
    }

    private function generateThaiDescription($isGreenCandle, $upperWickLength, $lowerWickLength, $totalRange, $bodyRange) {
        $upperWickPercent = $upperWickLength / $totalRange * 100;
        $lowerWickPercent = $lowerWickLength / $totalRange * 100;
        $bodyPercent = $bodyRange / $totalRange * 100;

        $description = "";
        $trend = "";

        if ($isGreenCandle) {
            // Green Candle Analysis
            if ($bodyPercent >= 70) {
                $description .= "แท่งเทียนเขียวแข็งแกร่ง - มีการดันราคาขึ้นอย่างชัดเจน ";
                $trend = "Bullish Strong";
            } elseif ($bodyPercent >= 50) {
                $description .= "แท่งเทียนเขียวปานกลาง - แรงซื้อเริ่มมีความมั่นคง ";
                $trend = "Bullish Moderate";
            } else {
                $description .= "แท่งเทียนเขียวอ่อน - มีแรงซื้อเล็กน้อย ";
                $trend = "Bullish Weak";
            }

            if ($upperWickPercent <= 20) {
                $description .= "มีแนวโน้มที่จะดันราคาต่อ ";
            } elseif ($upperWickPercent > 20 && $upperWickPercent <= 50) {
                $description .= "มีแรงขายบ้างเล็กน้อย ";
            } else {
                $description .= "มีแรงขายค่อนข้างมาก อาจมีการย่อตัว ";
            }
        } else {
            // Red Candle Analysis
            if ($bodyPercent >= 70) {
                $description .= "แท่งเทียนแดงแข็งแกร่ง - มีการกดดันราคาลงอย่างชัดเจน ";
                $trend = "Bearish Strong";
            } elseif ($bodyPercent >= 50) {
                $description .= "แท่งเทียนแดงปานกลาง - แรงขายเริ่มมีความมั่นคง ";
                $trend = "Bearish Moderate";
            } else {
                $description .= "แท่งเทียนแดงอ่อน - มีแรงขายเล็กน้อย ";
                $trend = "Bearish Weak";
            }

            if ($lowerWickPercent <= 20) {
                $description .= "มีแนวโน้มที่จะกดราคาต่อ ";
            } elseif ($lowerWickPercent > 20 && $lowerWickPercent <= 50) {
                $description .= "มีแรงซื้อบ้างเล็กน้อย ";
            } else {
                $description .= "มีแรงซื้อค่อนข้างมาก อาจมีการดีดตัว ";
            }
        }

        return [
            'description' => $description,
            'trend' => $trend
        ];
    }
}

function generateCandlestickAnalysis() {
    $results = [];
    
    $percentages = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90];
    
    foreach ($percentages as $upperWick) {
        foreach ($percentages as $body) {
            foreach ($percentages as $lowerWick) {
                $time = time();
                $basePrice = 1720.03;
                
                $open = $basePrice;
                $close = rand(0, 1) ? 
                    $basePrice * (1 + rand(1, 50) / 1000) : 
                    $basePrice * (1 - rand(1, 50) / 1000);
                
                $high = max($open, $close) * (1 + rand(1, 50) / 1000);
                $low = min($open, $close) * (1 - rand(1, 50) / 1000);
                
                $analyzer = new CandlestickAnalyzer(
                    $time, $open, $high, $low, $close, 
                    $upperWick, $body, $lowerWick
                );
                
                $results[] = $analyzer->analyzeCandle();
            }
        }
    }
    
    return $results;
}

// Generate analysis
$analysis = generateCandlestickAnalysis();

// HTML output
echo "<!DOCTYPE html>
<html lang='th'>
<head>
    <meta charset='UTF-8'>
    <title>การวิเคราะห์แท่งเทียน</title>
    <style>
        table { 
            border-collapse: collapse; 
            width: 100%; 
            font-family: Arial, sans-serif; 
            font-size: 12px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: right; 
        }
        th { 
            background-color: #f2f2f2; 
            text-align: center;
        }
        .green { color: green; }
        .red { color: red; }
        .description { text-align: left; }
    </style>
</head>
<body>
    <h1>ผลการวิเคราะห์แท่งเทียน</h1>
    <table>
        <thead>
            <tr>
                <th>เวลา</th>
                <th>Open</th>
                <th>High</th>
                <th>Low</th>
                <th>Close</th>
                <th>สี</th>
                <th>Upper Wick %</th>
                <th>Body %</th>
                <th>Lower Wick %</th>
                <th>Buy Signal</th>
                <th>Sell Signal</th>
                <th>% Green Next</th>
                <th>% Red Next</th>
                <th>แนวโน้ม</th>
                <th>คำอธิบาย</th>
            </tr>
        </thead>
        <tbody>";

// Limit to first 100 results
$limitedAnalysis = array_slice($analysis, 0, 100);

foreach ($limitedAnalysis as $candle) {
    echo "<tr>
        <td>{$candle['time']}</td>
        <td>" . number_format($candle['open'], 2) . "</td>
        <td>" . number_format($candle['high'], 2) . "</td>
        <td>" . number_format($candle['low'], 2) . "</td>
        <td>" . number_format($candle['close'], 2) . "</td>
        <td class='" . strtolower($candle['candleColor']) . "'>{$candle['candleColor']}</td>
        <td>" . number_format($candle['upperWickPercentage'], 2) . "</td>
        <td>" . number_format($candle['bodyPercentage'], 2) . "</td>
        <td>" . number_format($candle['lowerWickPercentage'], 2) . "</td>
        <td>" . number_format($candle['buySignal'], 0) . "</td>
        <td>" . number_format($candle['sellSignal'], 0) . "</td>
        <td>" . number_format($candle['nextGreenCandleProbability'], 2) . "</td>
        <td>" . number_format($candle['nextRedCandleProbability'], 2) . "</td>
        <td>{$candle['trend']}</td>
        <td class='description'>{$candle['thaiDescription']}</td>
    </tr>";
}

echo "</tbody>
    </table>
</body>
</html>";
?>