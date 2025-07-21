<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//newAnalysisData.php

 


class AnalysisData {
    public $timeCandle;
    public $turnType;
    public $cutPointType;
    public $distanceFromTurnType;
    public $distanceFromCutPointType;
    public $isEmaConflict;

    public function __construct(
        $timeCandle, 
        $turnType, 
        $cutPointType, 
        $distanceFromTurnType = 1, 
        $distanceFromCutPointType = 1, 
        $isEmaConflict = 'y'
    ) {
        $this->timeCandle = $timeCandle;
        $this->turnType = $turnType;
        $this->cutPointType = $cutPointType;
        $this->distanceFromTurnType = $distanceFromTurnType;
        $this->distanceFromCutPointType = $distanceFromCutPointType;
        $this->isEmaConflict = $isEmaConflict;
    }
}

class EMAAnalyzer {
    private $candleData;
    private $ema3Values = [];
    private $ema5Values = [];

    public function __construct($candleData) {
        $this->candleData = $candleData;
    }

    public function calculateEMA() {
        // Calculate EMA3 and EMA5
        $this->calculateEMAValues(3, $this->ema3Values);
        $this->calculateEMAValues(5, $this->ema5Values);
    }

    private function calculateEMAValues($period, &$emaValues) {
        $smoothing = 2 / ($period + 1);

        foreach ($this->candleData as $index => $candle) {
            $closePrice = $candle['close'];

            if ($index < $period - 1) {
                // Initial period - use simple average
                $emaValues[] = $this->calculateSimpleAverage($index, $period);
            } else {
                if (empty($emaValues)) {
                    // First EMA is the simple average of first $period candles
                    $emaValues[] = $this->calculateSimpleAverage($period - 1, $period);
                }

                // Calculate EMA
                $lastEMA = end($emaValues);
                $currentEMA = ($closePrice - $lastEMA) * $smoothing + $lastEMA;
                $emaValues[] = $currentEMA;
            }
        }
    }

    private function calculateSimpleAverage($endIndex, $period) {
        $sum = 0;
        for ($i = $endIndex - $period + 1; $i <= $endIndex; $i++) {
            $sum += $this->candleData[$i]['close'];
        }
        return $sum / $period;
    }

    public function analyzeEMAInteractions() {
        $this->calculateEMA();
        $analysisResults = [];
        $turnPoints = [];
        $cutPoints = [];

        for ($i = 1; $i < count($this->ema3Values); $i++) {
            $currentEma3 = $this->ema3Values[$i];
            $prevEma3 = $this->ema3Values[$i - 1];
            $currentEma5 = $this->ema5Values[$i];
            $prevEma5 = $this->ema5Values[$i - 1];
            $currentCandle = $this->candleData[$i];

            // Determine Turn Type for EMA3
            $turnType = $this->determineTurnType($prevEma3, $currentEma3);

            // Determine Cut Point Type
            $cutPointType = $this->determineCutPointType($prevEma3, $currentEma3, $prevEma5, $currentEma5);

            // Track turn points
            if ($turnType !== 'NoTurn') {
                $turnPoints[] = [
                    'index' => $i,
                    'type' => $turnType,
                    'value' => $currentEma3
                ];
            }

            // Track cut points
            if ($cutPointType !== 'NoCrossing') {
                $cutPoints[] = [
                    'index' => $i,
                    'type' => $cutPointType
                ];
            }

            // Calculate distances
            $distanceFromTurn = $this->calculateDistanceFromTurn($turnPoints, $i);
            $distanceFromCutPoint = $this->calculateDistanceFromCutPoint($cutPoints, $i);

            // Check EMA Conflicts
            $isEma3Conflict = $this->checkEMAConflict($currentEma3, $currentCandle, $turnType);
            $isEma5Conflict = $this->checkEMAConflict($currentEma5, $currentCandle, $turnType);

            // Create AnalysisData object
            $analysisResult = new AnalysisData(
                $currentCandle['time'],
                $turnType,
                $cutPointType,
                $distanceFromTurn,
                $distanceFromCutPoint,
                $isEma3Conflict
            );

            $analysisResults[] = $analysisResult;
        }

        return $analysisResults;
    }

    private function determineTurnType($prevValue, $currentValue) {

        if ($currentValue > $prevValue) {
            return 'TurnUp';
        } elseif ($currentValue < $prevValue) {
            return 'TurnDown';
        }
        return 'NoTurn';
    }

    private function determineCutPointType($prevEma3, $currentEma3, $prevEma5, $currentEma5) {
        if ($prevEma3 < $prevEma5 && $currentEma3 > $currentEma5) {
            return 'Ema3AboveEma5';
        } elseif ($prevEma3 > $prevEma5 && $currentEma3 < $currentEma5) {
            return 'Ema5AboveEma3';
        }
        return 'NoCrossing';
    }

    private function calculateDistanceFromTurn($turnPoints, $currentIndex) {
        if (empty($turnPoints)) {
            return 0;
        }
        $lastTurnPoint = end($turnPoints);
        return $currentIndex - $lastTurnPoint['index'];
    }

    private function calculateDistanceFromCutPoint($cutPoints, $currentIndex) {
        if (empty($cutPoints)) {
            return 0;
        }
        $lastCutPoint = end($cutPoints);
        return $currentIndex - $lastCutPoint['index'];
    }

    private function checkEMAConflict($currentEma, $currentCandle, $turnType) {
        $closePrice = $currentCandle['close'];
        
        if ($turnType === 'TurnUp' && $currentEma < $closePrice) {
            return 'n'; // Conflict - EMA not moving up with the candle
        } elseif ($turnType === 'TurnDown' && $currentEma > $closePrice) {
            return 'n'; // Conflict - EMA not moving down with the candle
        }
        return 'y'; // No conflict
    }

    // Getter methods for EMA values (optional, for debugging)
    public function getEMA3Values() {
        return $this->ema3Values;
    }

    public function getEMA5Values() {
        return $this->ema5Values;
    }
}

// Example usage
$jsonData = [
    [
        "time" => 1739167260,
        "open" => 240.5076,
        "high" => 240.5906,
        "low" => 240.4675,
        "close" => 240.5677
    ],
    [
        "time" => 1739167320,
        "open" => 240.5905,
        "high" => 240.6301,
        "low" => 240.4905,
        "close" => 240.5631
    ]
    // Add more candle data as needed
];

 $st = "";   
 $sFileName = 'newDerivObject/dataRaw.json';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file);

$jsonData = JSON_DECODE($st,true) ;
//echo $jsonData;
$analyzer = new EMAAnalyzer($jsonData);
$analysisResults = $analyzer->analyzeEMAInteractions();

// Print EMA3 and EMA5 values (for verification)
echo "EMA3 Values:<br>";
print_r($analyzer->getEMA3Values());
echo "<br>EMA5 Values:<br>";
print_r($analyzer->getEMA5Values());

// Print analysis results
echo "<br>Analysis Results:<br>";
foreach ($analysisResults as $result) {
    echo "Time: " . date('Y-m-d H:i:s', $result->timeCandle) . "<br>";
    echo "Turn Type: " . $result->turnType . "<br>";
    echo "Cut Point Type: " . $result->cutPointType . "<br>";
    echo "Distance from Turn: " . $result->distanceFromTurnType . "<br>";
    echo "Distance from Cut Point: " . $result->distanceFromCutPointType . "<br>";
    echo "EMA Conflict: " . $result->isEmaConflict . "<br><hr>";
}
?>
