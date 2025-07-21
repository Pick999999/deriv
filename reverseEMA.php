<?php

class EMA3Calculator {
    private $data;
    private $period;
    private $smoothing;

    public function __construct($data, $period = 3) {
        $this->data = $data;
        $this->period = $period;
        $this->smoothing = 2 / ($period + 1);
    }

    private function calculateInitialSMA() {
        // If we have fewer points than the period, use all available points
        $dataSlice = array_slice($this->data, 0, $this->period);
        return array_sum(array_column($dataSlice, 'close')) / count($dataSlice);
    }

    private function calculateEMAValue($sma) {
        $ema = $sma;
        for ($i = count($this->data) - $this->period; $i < count($this->data); $i++) {
            $ema = ($this->data[$i]['close'] - $ema) * $this->smoothing + $ema;
        }
        return $ema;
    }

    public function solveForCloseValue($desiredNextEMA) {
        // If not enough data points, we'll use what we have
        if (count($this->data) < $this->period) {
            $sma = $this->calculateInitialSMA();
        } else {
            // Take the last period of data points to calculate initial SMA
            $periodData = array_slice($this->data, -$this->period);
            $sma = array_sum(array_column($periodData, 'close')) / $this->period;
        }

        // Binary search to find the close value
        $low = min(array_column($this->data, 'low'));
        $high = max(array_column($this->data, 'high'));
        
        while ($high - $low > 0.0001) {
            $midClose = ($low + $high) / 2;
            
            // Create a new dataset with the test close value
            $testData = $this->data;
            $testData[] = ['close' => $midClose];
            
            $testCalculator = new EMA3Calculator($testData, $this->period);
            $testEMA = $testCalculator->calculateEMAValue($sma);
            
            if (abs($testEMA - $desiredNextEMA) < 0.0001) {
                return $midClose;
            }
            
            if ($testEMA > $desiredNextEMA) {
                $low = $midClose;
            } else {
                $high = $midClose;
            }
        }
        
        return ($low + $high) / 2;
    }
}

// Example usage
$data = [
    ['close' => 248.3018],
    ['close' => 247.9945]
];

$calculator = new EMA3Calculator($data);
$requiredCloseValue = $calculator->solveForCloseValue(248.0);

echo "To achieve EMA3 of 248.0, the close value should be: " . $requiredCloseValue;
?>