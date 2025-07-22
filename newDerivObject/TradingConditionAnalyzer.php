<?php

class TradingConditionAnalyzer {
    // Store candle data and EMA values
    private $candles = [];
    private $ema3 = [];
    private $ema5 = [];
    private $ema3_minus_ema5 = [];
    
    /**
     * Constructor to initialize with historical data
     * 
     * @param array $candleData Array of candle data (time, open, high, low, close)
     */
    public function __construct(array $candleData) {
        $this->candles = $candleData;
        $this->calculateEMAs();
        $this->calculateEMADifference();
		
    }
    
    /**
     * Load candle data from text file
     * 
     * @param string $filePath Path to the text file containing JSON data
     * @return TradingConditionAnalyzer Instance with loaded data
     */
    public static function loadFromFile($filePath) {
        $jsonContent = file_get_contents($filePath);
        $candleData = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error parsing JSON data: " . json_last_error_msg());
        }
        
        return new self($candleData);
    }
    
    /**
     * Calculate EMA3 and EMA5 from candle data
     */
    private function calculateEMAs() {
        // Get close prices from candles
        $closes = array_column($this->candles, 'close');
        
        // Calculate EMA3
        $this->ema3 = $this->calculateEMA($closes, 3);
        
        // Calculate EMA5
        $this->ema5 = $this->calculateEMA($closes, 5);
    }
    
    /**
     * Calculate EMA for given period
     * 
     * @param array $prices Array of closing prices
     * @param int $period EMA period
     * @return array Array of EMA values
     */
    private function calculateEMA($prices, $period) {
        $ema = [];
        $multiplier = 2 / ($period + 1);
        
        // Initialize EMA with SMA for first period elements
        $sma = array_sum(array_slice($prices, 0, $period)) / $period;
        $ema[0] = $sma;
        
        // Calculate remaining EMA values
        for ($i = 1; $i < count($prices); $i++) {
            $ema[$i] = ($prices[$i] - $ema[$i-1]) * $multiplier + $ema[$i-1];
        }
        
        return $ema;
    }
    
    /**
     * Calculate difference between EMA3 and EMA5
     */
    private function calculateEMADifference() {
        $count = min(count($this->ema3), count($this->ema5));
        
        for ($i = 0; $i < $count; $i++) {
            $this->ema3_minus_ema5[$i] = $this->ema3[$i] - $this->ema5[$i];
        }
    }
    
    /**
     * Check for unfavorable candlestick patterns
     * 
     * @param int $index Index of the current candle to check
     * @return array Detected patterns and their risk level
     */
    public function checkCandlePatterns($index) {
        $candle = $this->candles[$index];
        $previousCandle = isset($this->candles[$index-1]) ? $this->candles[$index-1] : null;
        $prevPreviousCandle = isset($this->candles[$index-2]) ? $this->candles[$index-2] : null;
        
        $warnings = [];
        
        // Calculate body and wick sizes
        $bodySize = abs($candle['close'] - $candle['open']);
        $upperWick = $candle['high'] - max($candle['open'], $candle['close']);
        $lowerWick = min($candle['open'], $candle['close']) - $candle['low'];
        $totalSize = $candle['high'] - $candle['low'];
        
        // Check for Doji (body less than 10% of total candle size)
        if ($bodySize < 0.1 * $totalSize && $totalSize > 0) {
            $warnings[] = [
				'wCode' => 'CP1', 
                'pattern' => 'Doji', 
                'risk' => 'high', 
                'description' => 'เปิด-ปิดใกล้เคียงกัน บ่งบอกความลังเลของตลาด'
            ];
        }
        
        // Check for long wicks (wick more than 70% of total candle)
        if (($upperWick > 0.7 * $totalSize || $lowerWick > 0.7 * $totalSize) && $totalSize > 0) {
            $warnings[] = [
				'wCode' => 'CP2', 
                'pattern' => 'Long Wick', 
                'risk' => 'high', 
                'description' => 'ไส้เทียนยาวผิดปกติ แสดงถึงความผันผวนสูง'
            ];
        }
        
        // Check for abnormally large candle (body 3x larger than average of previous 5 candles)
        if ($index >= 5) {
            $avgBodySize = 0;
            for ($i = 1; $i <= 5; $i++) {
                $prevCandle = $this->candles[$index - $i];
                $avgBodySize += abs($prevCandle['close'] - $prevCandle['open']);
            }
            $avgBodySize /= 5;
            
            if ($bodySize > 3 * $avgBodySize && $avgBodySize > 0) {
                $warnings[] = [
					'wCode' => 'CP3', 
                    'pattern' => 'Abnormally Large Candle', 
                    'risk' => 'medium', 
                    'description' => 'แท่งเทียนขนาดใหญ่ผิดปกติ อาจเกิด reversal'
                ];
            }
        }
        
        // Check for Shooting Star / Hanging Man pattern
        $isBullish = $candle['close'] > $candle['open'];
        
        if ($isBullish && $upperWick > 2 * $bodySize && $lowerWick < 0.2 * $bodySize && $bodySize > 0) {
            $warnings[] = [
				'wCode' => 'CP4', 
                'pattern' => 'Shooting Star', 
                'risk' => 'high', 
                'description' => 'มีไส้บนยาว บ่งบอกถึงแรงขายที่กำลังเข้ามา'
            ];
        }
        
        if (!$isBullish && $lowerWick > 2 * $bodySize && $upperWick < 0.2 * $bodySize && $bodySize > 0) {
            $warnings[] = [
				'wCode' => 'CP5', 
                'pattern' => 'Hanging Man', 
                'risk' => 'high', 
                'description' => 'มีไส้ล่างยาว บ่งบอกถึงแรงซื้อที่กำลังอ่อนลง'
            ];
        }
        
        // Check for Evening Star pattern
        if ($index >= 2 && $previousCandle && $prevPreviousCandle) {
            $c1 = $prevPreviousCandle;
            $c2 = $previousCandle;
            $c3 = $candle;
            
            $c1IsBullish = $c1['close'] > $c1['open'];
            $c2IsBullish = $c2['close'] > $c2['open'];
            $c3IsBullish = $c3['close'] > $c3['open'];
            
            $c1BodySize = abs($c1['close'] - $c1['open']);
            $c2BodySize = abs($c2['close'] - $c2['open']);
            $c3BodySize = abs($c3['close'] - $c3['open']);
            
            // Evening Star (bearish reversal)
            if ($c1IsBullish && $c1BodySize > $c2BodySize && !$c3IsBullish && 
                $c2['low'] > $c1['close'] && $c3['close'] < $c1['open']) {
                $warnings[] = [
					'wCode' => 'CP6', 
                    'pattern' => 'Evening Star', 
                    'risk' => 'very high', 
                    'description' => 'รูปแบบ Evening Star บ่งบอกการกลับตัวลง'
                ];
            }
            
            // Morning Star (bullish reversal)
            if (!$c1IsBullish && $c1BodySize > $c2BodySize && $c3IsBullish && 
                $c2['high'] < $c1['close'] && $c3['close'] > $c1['open']) {
                $warnings[] = [
					'wCode' => 'CP7', 
                    'pattern' => 'Morning Star', 
                    'risk' => 'very high', 
                    'description' => 'รูปแบบ Morning Star บ่งบอกการกลับตัวขึ้น'
                ];
            }
        }
        
        return $warnings;
    }
    
    /**
     * Check for unfavorable EMA conditions
     * 
     * @param int $index Index of the current point to check
     * @param string $tradeDirection Intended trade direction ('long' or 'short')
     * @return array Detected EMA warnings
     */
    public function checkEMAConditions($index, $tradeDirection) {
        $warnings = [];
        
        // Get current values
        $currentEMA3 = $this->ema3[$index];
        $currentEMA5 = $this->ema5[$index];
        $currentPrice = $this->candles[$index]['close'];
        
        // Get previous values if available
        $prevEMA3 = isset($this->ema3[$index-1]) ? $this->ema3[$index-1] : null;
        $prevEMA5 = isset($this->ema5[$index-1]) ? $this->ema5[$index-1] : null;
        
        // Check EMA crossover in the opposite direction of intended trade
        if ($prevEMA3 !== null && $prevEMA5 !== null) {
            $wasBullishCross = $prevEMA3 < $prevEMA5 && $currentEMA3 > $currentEMA5;
            $wasBearishCross = $prevEMA3 > $prevEMA5 && $currentEMA3 < $currentEMA5;
            
            if ($tradeDirection === 'long' && $wasBearishCross) {
                $warnings[] = [
					'wCode' => 'E1', 
                    'condition' => 'EMA Bearish Cross', 
                    'risk' => 'high', 
                    'description' => 'EMA3 ตัดลง EMA5 ซึ่งขัดแย้งกับการเปิด Long'
                ];
            }
            
            if ($tradeDirection === 'short' && $wasBullishCross) {
                $warnings[] = [
					'wCode' => 'E2', 
                    'condition' => 'EMA Bullish Cross', 
                    'risk' => 'high', 
                    'description' => 'EMA3 ตัดขึ้น EMA5 ซึ่งขัดแย้งกับการเปิด Short'
                ];
            }
        }
        
        // Check if EMAs are too far apart (indicating potential pullback)
        $emaDistance = abs($currentEMA3 - $currentEMA5);
        $avgPrice = ($this->candles[$index]['high'] + $this->candles[$index]['low']) / 2;
        $percentDistance = $emaDistance / $avgPrice * 100;
        
        if ($percentDistance > 1.5) {  // More than 1.5% apart
            $warnings[] = [
				'wCode' => 'E3', 
                'condition' => 'EMA Overextended', 
                'risk' => 'medium', 
                'description' => 'EMA3 และ EMA5 ห่างกันเกินไป อาจเกิด pullback'
            ];
        }
        
        // Check if price is too far from EMAs
        $priceEma3Distance = abs($currentPrice - $currentEMA3);
        $priceEma3Percent = $priceEma3Distance / $currentPrice * 100;
        
        if ($priceEma3Percent > 2) {  // Price more than 2% away from EMA3
            $warnings[] = [
				'wCode' => 'E4', 
                'condition' => 'Price Far from EMA', 
                'risk' => 'medium', 
                'description' => 'ราคาอยู่ห่างจาก EMA3 มากเกินไป อาจเกิดการดีดตัวกลับ'
            ];
        }
        
        // Check for wrong position of price relative to EMAs for intended trade
        $isPriceAboveEMAs = $currentPrice > $currentEMA3 && $currentPrice > $currentEMA5;
        $isPriceBelowEMAs = $currentPrice < $currentEMA3 && $currentPrice < $currentEMA5;
        
        if ($tradeDirection === 'long' && $isPriceBelowEMAs) {
            $warnings[] = [
				'wCode' => 'E5', 
                'condition' => 'Price Below EMAs', 
                'risk' => 'high', 
                'description' => 'ราคาอยู่ใต้ทั้ง EMA3 และ EMA5 แต่ต้องการเปิด Long'
            ];
        }
        
        if ($tradeDirection === 'short' && $isPriceAboveEMAs) {
            $warnings[] = [
				'wCode' => 'E6', 
                'condition' => 'Price Above EMAs', 
                'risk' => 'high', 
                'description' => 'ราคาอยู่เหนือทั้ง EMA3 และ EMA5 แต่ต้องการเปิด Short'
            ];
        }
        
        return $warnings;
    }
    
    /**
     * Check EMA3-EMA5 difference signals
     * 
     * @param int $index Index of the current point to check
     * @return array Detected EMA difference warnings
     */
    public function checkEMADifference($index) {
        $warnings = [];
        
        // Need at least 3 previous points to check for trend changes
        if ($index < 3) {
            return $warnings;
        }
        
        $current = $this->ema3_minus_ema5[$index];
        $prev1 = $this->ema3_minus_ema5[$index-1];
        $prev2 = $this->ema3_minus_ema5[$index-2];
        $prev3 = $this->ema3_minus_ema5[$index-3];
        
        // Check for sudden direction change in EMA3-EMA5
        if (($prev2 > $prev3 && $prev1 > $prev2 && $current < $prev1) || 
            ($prev2 < $prev3 && $prev1 < $prev2 && $current > $prev1)) {
            $warnings[] = [
				'wCode' => 'ED1', 
                'condition' => 'EMA Difference Direction Change', 
                'risk' => 'high', 
                'description' => 'EMA3-EMA5 เปลี่ยนทิศทางอย่างกะทันหัน บ่งบอกถึงการเปลี่ยนโมเมนตัม'
            ];
        }
        
        // Check for oscillation without clear direction
        if (($prev3 > $prev2 && $prev2 < $prev1 && $prev1 > $current) || 
            ($prev3 < $prev2 && $prev2 > $prev1 && $prev1 < $current)) {
            $warnings[] = [
				'wCode' => 'ED2', 
                'condition' => 'EMA Difference Oscillation', 
                'risk' => 'medium', 
                'description' => 'EMA3-EMA5 แกว่งตัวไปมาโดยไม่มีทิศทางชัดเจน'
            ];
        }
        
        // Check for weakening momentum
        if ($current > 0 && $prev1 > 0 && $prev2 > 0) {  // Bullish trend
            if ($current < $prev1 && $prev1 < $prev2) {
                $warnings[] = [
					'wCode' => 'ED3', 
                    'condition' => 'Weakening Bullish Momentum', 
                    'risk' => 'medium', 
                    'description' => 'EMA3-EMA5 กำลังอ่อนแรงลงในทิศทางขาขึ้น'
                ];
            }
        }
        
        if ($current < 0 && $prev1 < 0 && $prev2 < 0) {  // Bearish trend
            if ($current > $prev1 && $prev1 > $prev2) {
                $warnings[] = [
					'wCode' => 'ED4', 
                    'condition' => 'Weakening Bearish Momentum', 
                    'risk' => 'medium', 
                    'description' => 'EMA3-EMA5 กำลังอ่อนแรงลงในทิศทางขาลง'
                ];
            }
        }
        
        return $warnings;
    }
    
    /**
     * Check for market consolidation
     * 
     * @param int $index Index of the current point to check
     * @return array Detected market condition warnings
     */
    public function checkMarketConditions($index) {
        $warnings = [];
        $candle = $this->candles[$index];
        
        // Check for consolidation (flat EMAs)
        if ($index >= 5) {
            $ema3Variation = $this->calculateVariation($this->ema3, $index, 5);
            $ema5Variation = $this->calculateVariation($this->ema5, $index, 5);
            
            $avgPrice = ($candle['high'] + $candle['low']) / 2;
            $ema3PercentVar = $ema3Variation / $avgPrice * 100;
            $ema5PercentVar = $ema5Variation / $avgPrice * 100;
            
            if ($ema3PercentVar < 0.3 && $ema5PercentVar < 0.3) {  // Less than 0.3% variation
                $warnings[] = [
					'wCode' => 'M1', 
                    'condition' => 'Market Consolidation', 
                    'risk' => 'medium', 
                    'description' => 'ตลาดอยู่ในช่วง Consolidation EMA ไม่มีทิศทางชัดเจน'
                ];
            }
        }
        
        return $warnings;
    }
    
    /**
     * Calculate variation (max - min) over a period
     * 
     * @param array $data Data array
     * @param int $index Current index
     * @param int $period Period to check
     * @return float Variation value
     */
    private function calculateVariation($data, $index, $period) {
        $slice = array_slice($data, max(0, $index - $period + 1), min($period, $index + 1));
        return max($slice) - min($slice);
    }
    
    /**
     * Analyze current trading conditions and return all warnings
     * 
     * @param int $index Index to analyze (default: most recent)
     * @param string $tradeDirection Intended trade direction ('long' or 'short')
     * @return array All detected warnings
     */
    public function analyzeTradingConditions($index = null, $tradeDirection = 'long') {
        if ($index === null) {
            $index = count($this->candles) - 1;
        }
        
        $result = [
            'timestamp' => $this->candles[$index]['time'],
            'price' => $this->candles[$index]['close'],
            'candlePatterns' => $this->checkCandlePatterns($index),
            'emaConditions' => $this->checkEMAConditions($index, $tradeDirection),
            'emaDifference' => $this->checkEMADifference($index),
            'marketConditions' => $this->checkMarketConditions($index),
            'totalWarnings' => 0,
            'overallRisk' => 'low',
            'shouldTrade' => true,
			'AllWCode' => ''
        ];
        
        // Count warnings and determine overall risk
        $highRiskCount = 0;
        $mediumRiskCount = 0;
        $wCode = '';
        foreach (['candlePatterns', 'emaConditions', 'emaDifference', 'marketConditions'] as $category) {
            foreach ($result[$category] as $warning) {
                $result['totalWarnings']++;
                $wCode .= $warning['wCode'] .'-';
                if ($warning['risk'] === 'high' || $warning['risk'] === 'very high') {
                    $highRiskCount++;
                } else if ($warning['risk'] === 'medium') {
                    $mediumRiskCount++;
                }
            }
        } 
		$wCode = substr($wCode,0,strlen($wCode)-1);
		$result['AllWCode'] = $wCode ;
        
        // Determine overall risk and trading recommendation
        if ($highRiskCount >= 2 || $result['totalWarnings'] >= 4) {
            $result['overallRisk'] = 'very high';
            $result['shouldTrade'] = false;
        } else if ($highRiskCount >= 1 || $mediumRiskCount >= 2) {
            $result['overallRisk'] = 'high';
            $result['shouldTrade'] = false;
        } else if ($mediumRiskCount >= 1) {
            $result['overallRisk'] = 'medium';
            $result['shouldTrade'] = true; // Could trade with caution
        }
        
        return $result;
    }
    
    /**
     * Get the count of candles
     * 
     * @return int Number of candles
     */
    public function getCandleCount() {
        return count($this->candles);
    }
    
    /**
     * Get EMA values
     * 
     * @return array Array with EMA3 and EMA5 values
     */
    public function getEMAValues() {
        return [
            'ema3' => $this->ema3,
            'ema5' => $this->ema5,
            'ema3_minus_ema5' => $this->ema3_minus_ema5
        ];
    }
}

// Example usage for file-based data
function analyzeFromFile($filePath, $tradeDirection = 'long') {
    try {
        // Load data from file
        $analyzer = TradingConditionAnalyzer::loadFromFile($filePath);
        
        // Analyze most recent candle
        $result = $analyzer->analyzeTradingConditions(null, $tradeDirection);
        
        // Display results
        echo "Trading Condition Analysis<br>";
        echo "=========================<br>";
        echo "Timestamp: " . date('Y-m-d H:i:s', $result['timestamp']) . "<br>";
        echo "Current Price: " . $result['price'] . "<br><br>";
        
        echo "Candle Pattern Warnings: " . count($result['candlePatterns']) . "<br>";
        foreach ($result['candlePatterns'] as $warning) {
            echo "- {$warning['pattern']} (Risk: {$warning['risk']}): {$warning['description']}<br>";
        }
        
        echo "<br>EMA Condition Warnings: " . count($result['emaConditions']) . "<br>";
        foreach ($result['emaConditions'] as $warning) {
            echo "- {$warning['condition']} (Risk: {$warning['risk']}): {$warning['description']}<br>";
        }
        
        echo "<br>EMA Difference Warnings: " . count($result['emaDifference']) . "<br>";
        foreach ($result['emaDifference'] as $warning) {
            echo "- {$warning['condition']} (Risk: {$warning['risk']}): {$warning['description']}<br>";
        }
        
        echo "<br>Market Condition Warnings: " . count($result['marketConditions']) . "<br>";
        foreach ($result['marketConditions'] as $warning) {
            echo "- {$warning['condition']} (Risk: {$warning['risk']}): {$warning['description']}<br>";
        }
        
        echo "<br>Summary:<br>";
        echo "Total Warnings: {$result['totalWarnings']}<br>";
        echo "Overall Risk: {$result['overallRisk']}<br>";
        echo "Recommendation: " . ($result['shouldTrade'] ? "Safe to trade" : "Avoid trading") . "<br>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
}

// Function to scan multiple data points and find high risk periods
function scanForHighRiskPeriods($filePath, $tradeDirection = 'long') {
    try {
        // Load data from file
        $analyzer = TradingConditionAnalyzer::loadFromFile($filePath);
        $highRiskPeriods = [];
        
        // Get total number of candles
        $candleCount = $analyzer->getCandleCount();
        
        // Start from EMA needed periods
        $startIndex = 0; // We need at least 5 candles for proper analysis
        
        
        
        // Analyze each candle
        for ($i = $startIndex; $i < $candleCount-1; $i++) {
            $result = $analyzer->analyzeTradingConditions($i, $tradeDirection);

            // Store high and very high risk periods
            if ($result['overallRisk'] === 'high' || $result['overallRisk'] === 'very high') {
                $highRiskPeriods[] = [
                    'index' => $i,
                    'timestamp' => $result['timestamp'],
					'timefrom_unix' => date('H:i',$result['timestamp']),
                    'price' => $result['price'],
                    'risk' => $result['overallRisk'],
                    'warnings' => $result['totalWarnings'],
					'AllWcode' => '',
                    'details' => [
                        'candlePatterns' => $result['candlePatterns'],
                        'emaConditions' => $result['emaConditions'],
                        'emaDifference' => $result['emaDifference'],
                        'marketConditions' => $result['marketConditions']
                    ]
                ];
            }
        }
		for ($i=0;$i<=count($highRiskPeriods)-1;$i++) {
 		 $sCode = '';
		 for ($i2=0;$i2<=count($highRiskPeriods[$i]['details']['candlePatterns'])-1;$i2++) {
		   $sCode .= $highRiskPeriods[$i]['details']['candlePatterns'][$i2]['wCode'].';';
		 }
		 for ($i2=0;$i2<=count($highRiskPeriods[$i]['details']['emaConditions'])-1;$i2++) {
		   $sCode .= $highRiskPeriods[$i]['details']['emaConditions'][$i2]['wCode'].';';
		 }
		 for ($i2=0;$i2<=count($highRiskPeriods[$i]['details']['emaDifference'])-1;$i2++) {
		   $sCode .= $highRiskPeriods[$i]['details']['emaDifference'][$i2]['wCode'].';';
		 }
		 for ($i2=0;$i2<=count($highRiskPeriods[$i]['details']['marketConditions'])-1;$i2++) {
		   $sCode .= $highRiskPeriods[$i]['details']['marketConditions'][$i2]['wCode'].';';
		 }
		 $highRiskPeriods[$i]['AllWcode'] = $sCode ;

		   
		} // end for i


        return array($candleCount,$highRiskPeriods);
        // Display results
        echo "Found " . count($highRiskPeriods) . " high risk periods out of " . ($candleCount) . " candles analyzed.<br><br>";
         
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
} // end function 




// Function to scan multiple data points and find high risk periods
function DisplayForHighRiskPeriods($candleCount,$highRiskPeriods) {

    
	echo "Scanning for high risk trading periods Count=...$candleCount<br>";
	$startIndex = 0 ;
    try {
         
        // Display results
        echo "Found " . count($highRiskPeriods) . " high risk periods out of " . ($candleCount - $startIndex) . " candles analyzed.<br><br>";
        
        foreach ($highRiskPeriods as $period) {
            echo "<strong>" . date('Y-m-d H:i:s', $period['timestamp']) . 
                 " | Price: " . $period['price'] . 
                 " | Risk: " . $period['risk'] . 
                 " | Warnings: " . $period['warnings'] . "</strong><br>";
            
            // Display detailed warnings
            echo "<details>";
            echo "<summary>Show Warning Details</summary>";
            
            // Show candle pattern warnings
            if (!empty($period['details']['candlePatterns'])) {
                echo "<br><u>Candle Pattern Warnings:</u><br>";
                foreach ($period['details']['candlePatterns'] as $warning) {
                    echo "- {$warning['pattern']} (Risk: {$warning['risk']}): {$warning['description']}<br>";
                }
            }
            
            // Show EMA condition warnings
            if (!empty($period['details']['emaConditions'])) {
                echo "<br><u>EMA Condition Warnings:</u><br>";
                foreach ($period['details']['emaConditions'] as $warning) {
                    echo "- {$warning['condition']} (Risk: {$warning['risk']}): {$warning['description']}<br>";
                }
            }
            
            // Show EMA difference warnings
            if (!empty($period['details']['emaDifference'])) {
                echo "<br><u>EMA Difference Warnings:</u><br>";
                foreach ($period['details']['emaDifference'] as $warning) {
                    echo "- {$warning['condition']} (Risk: {$warning['risk']}): {$warning['description']}<br>";
                }
            }
            
            // Show market condition warnings
            if (!empty($period['details']['marketConditions'])) {
                echo "<br><u>Market Condition Warnings:</u><br>";
                foreach ($period['details']['marketConditions'] as $warning) {
                    echo "- {$warning['condition']} (Risk: {$warning['risk']}): {$warning['description']}<br>";
                }
            }
            
            echo "</details><br>";
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
} // end function


function Main() { 

list($candleCount,$highRiskPeriods) = scanForHighRiskPeriods('rawData.json', 'long');
DisplayForHighRiskPeriods($candleCount,$highRiskPeriods);


} // end function

Main();
