<?php
class cls_AllIndicator {
public $candleData ;
public $ema3 ;
public $ema5 ;
public $BB ;
public $rsi ;
public $candlePattern ;
public $firstTimestamp;
public $LastTimestamp;
public $currentTimestamp;
/*
1.calculateMACD
2.calculateStochastic
3.calculateRSI
4.calculateVolatility
5.calculateEMAValues
6.calculateBollingerBands
7.calculateADX
8.calculateIchimokuCloud

candle {
 candleTimestamp :  timestamp,
 timefrom_unix : timefromunix,
 rawData : {}
 IndyValue : {
   ema3 : ema3Value,
   ema5 : ema5Value,
   macd : macdObj ,
   rsi  : rsiObj,
   vola : VolaObj,
   stoChas : stoObj,
   BB : BBObj,
   ADX : adxObj,
   Ichi : IchiObj
 }
 prediction : {
   claude : {},
   chatgpt : {},
   deepseek : {},
   PK : {}


 }
}

*/

public function calculateMACD($fastPeriod = 12, $slowPeriod = 26, $signalPeriod = 9) {
        $fastEMA = $this->calculateEMAValues($fastPeriod);
        $slowEMA = $this->calculateEMAValues($slowPeriod);
        
        $macdLine = [];
        for ($i = 0; $i < count($this->candles); $i++) {
            $macdLine[$i] = $fastEMA[$i] - $slowEMA[$i];
        }
        
        // Calculate signal line (EMA of MACD line)
        $signalLine = [];
        $multiplier = 2 / ($signalPeriod + 1);
        
        // Use SMA for the first value
        $sum = 0;
        for ($i = 0; $i < $signalPeriod && $i < count($macdLine); $i++) {
            $sum += $macdLine[$i];
        }
        
        $sma = $sum / $signalPeriod;
        $signalLine[0] = $sma;
        
        // Calculate signal line EMA for remaining values
        for ($i = 1; $i < count($macdLine); $i++) {
            $signalLine[$i] = ($macdLine[$i] - $signalLine[$i-1]) * $multiplier + $signalLine[$i-1];
        }
        
        // Calculate histogram
        $histogram = [];
        for ($i = 0; $i < count($macdLine); $i++) {
            $histogram[$i] = $macdLine[$i] - $signalLine[$i];
        }
        
        return [
            'macd' => $macdLine,
            'signal' => $signalLine,
            'histogram' => $histogram
        ];
    }
    
    /**
     * Calculate Stochastic Oscillator
     * 
     * @param int $kPeriod %K period (default 14)
     * @param int $dPeriod %D period (default 3)
     * @return array Stochastic Oscillator values
     */
 public function calculateStochastic($kPeriod = 14, $dPeriod = 3) {
        $stochK = [];
        $stochD = [];
        
        for ($i = $kPeriod - 1; $i < count($this->candles); $i++) {
            // Find highest high and lowest low in the period
            $highestHigh = -INF;
            $lowestLow = INF;
            
            for ($j = $i - $kPeriod + 1; $j <= $i; $j++) {
                $highestHigh = max($highestHigh, $this->candles[$j]['high']);
                $lowestLow = min($lowestLow, $this->candles[$j]['low']);
            }
            
            // Calculate %K
            $range = $highestHigh - $lowestLow;
            if ($range == 0) {
                $stochK[$i] = 50; // Default to middle value if no range
            } else {
                $stochK[$i] = (($this->candles[$i]['close'] - $lowestLow) / $range) * 100;
            }
        }
        
        // Calculate %D (SMA of %K)
        for ($i = $kPeriod + $dPeriod - 2; $i < count($this->candles); $i++) {
            $sum = 0;
            for ($j = $i - $dPeriod + 1; $j <= $i; $j++) {
                $sum += $stochK[$j];
            }
            $stochD[$i] = $sum / $dPeriod;
        }
        
        return [
            'k' => $stochK,
            'd' => $stochD
        ];
  }


public function calculateRSI($period = 14) {
        $rsi = [];
        $gains = [];
        $losses = [];
        
        // Calculate initial gains and losses
        for ($i = 1; $i < count($this->candles); $i++) {
            $change = $this->candles[$i]['close'] - $this->candles[$i-1]['close'];
            $gains[$i] = max(0, $change);
            $losses[$i] = max(0, -$change);
        }
        
        // Calculate average gains and losses
        for ($i = $period; $i < count($this->candles); $i++) {
            if ($i == $period) {
                $avgGain = array_sum(array_slice($gains, 1, $period)) / $period;
                $avgLoss = array_sum(array_slice($losses, 1, $period)) / $period;
            } else {
                $avgGain = (($avgGain * ($period - 1)) + $gains[$i]) / $period;
                $avgLoss = (($avgLoss * ($period - 1)) + $losses[$i]) / $period;
            }
            
            if ($avgLoss == 0) {
                $rsi[$i] = 100;
            } else {
                $rs = $avgGain / $avgLoss;
                $rsi[$i] = 100 - (100 / (1 + $rs));
            }
        }
        
        return $rsi;
    }


private function calculateVolatility($period = 20) {
        $lastIndex = count($this->candles) - 1;
        $startIdx = max(0, $lastIndex - $period + 1);
        
        $changes = [];
        for ($i = $startIdx + 1; $i <= $lastIndex; $i++) {
            $changes[] = abs(($this->candles[$i]['close'] - $this->candles[$i-1]['close']) / $this->candles[$i-1]['close']);
        }
        
        return array_sum($changes) / count($changes);
}

private function calculateEMAValues($period) {
        $ema = [];
        $multiplier = 2 / ($period + 1);
        
        // Use SMA for the first value
        $sum = 0;
        for ($i = 0; $i < $period && $i < count($this->candles); $i++) {
            $sum += $this->candles[$i]['close'];
        }
        
        $sma = $sum / $period;
        $ema[0] = $sma;
        
        // Calculate EMA for remaining values
        for ($i = 1; $i < count($this->candles); $i++) {
            $close = $this->candles[$i]['close'];
            $ema[$i] = ($close - $ema[$i-1]) * $multiplier + $ema[$i-1];
        }
        
        return $ema;
}

private function calculateBollingerBands() {
        $period = 20;
        $standardDeviations = 2;
        $this->bollingerBands = [];
        
        for ($i = 0; $i < count($this->candles); $i++) {
            if ($i < $period - 1) {
                $this->bollingerBands[$i] = [
                    'middle' => null,
                    'upper' => null,
                    'lower' => null
                ];
                continue;
            }
            
            // Calculate SMA for middle band
            $sum = 0;
            for ($j = $i - $period + 1; $j <= $i; $j++) {
                $sum += $this->candles[$j]['close'];
            }
            $sma = $sum / $period;
            
            // Calculate standard deviation
            $sumSquaredDiff = 0;
            for ($j = $i - $period + 1; $j <= $i; $j++) {
                $diff = $this->candles[$j]['close'] - $sma;
                $sumSquaredDiff += $diff * $diff;
            }
            $standardDeviation = sqrt($sumSquaredDiff / $period);
            
            // Calculate bands
            $this->bollingerBands[$i] = [
                'middle' => $sma,
                'upper' => $sma + ($standardDeviation * $standardDeviations),
                'lower' => $sma - ($standardDeviation * $standardDeviations)
            ];
        }
}

private function calculateADX(array $highs, array $lows, array $closes, $period = 14) {
        $count = count($highs);
        if ($count < $period * 2) return;
        
        $plusDM = [];
        $minusDM = [];
        $trueRanges = [];
        
        // คำนวณ +DM, -DM และ True Range
        for ($i = 1; $i < $count; $i++) {
            $upMove = $highs[$i] - $highs[$i - 1];
            $downMove = $lows[$i - 1] - $lows[$i];
            
            $plusDM[] = ($upMove > $downMove && $upMove > 0) ? $upMove : 0;
            $minusDM[] = ($downMove > $upMove && $downMove > 0) ? $downMove : 0;
            
            $trueRanges[] = max(
                $highs[$i] - $lows[$i],
                abs($highs[$i] - $closes[$i - 1]),
                abs($lows[$i] - $closes[$i - 1])
            );
        }
        
        // ค่าแรกเป็น SMA
        $sumPlusDM = array_sum(array_slice($plusDM, 0, $period));
        $sumMinusDM = array_sum(array_slice($minusDM, 0, $period));
        $sumTR = array_sum(array_slice($trueRanges, 0, $period));
        
        $plusDI = ($sumTR == 0) ? 0 : (100 * $sumPlusDM / $sumTR);
        $minusDI = ($sumTR == 0) ? 0 : (100 * $sumMinusDM / $sumTR);
        $dx = (($plusDI + $minusDI) == 0) ? 0 : (100 * abs($plusDI - $minusDI) / ($plusDI + $minusDI));
        
        $adx = array_sum(array_slice(array_fill(0, $period, $dx), 0, $period)) / $period;
        $this->adx = array_fill(0, $period + 13, null); // ใส่ค่า null สำหรับช่วงแรก
        
        // คำนวณ ADX ต่อๆ ไป
        for ($i = $period; $i < count($plusDM); $i++) {
            $sumPlusDM = (($sumPlusDM * ($period - 1)) + $plusDM[$i]) / $period;
            $sumMinusDM = (($sumMinusDM * ($period - 1)) + $minusDM[$i]) / $period;
            $sumTR = (($sumTR * ($period - 1)) + $trueRanges[$i]) / $period;
            
            $plusDI = ($sumTR == 0) ? 0 : (100 * $sumPlusDM / $sumTR);
            $minusDI = ($sumTR == 0) ? 0 : (100 * $sumMinusDM / $sumTR);
            $dx = (($plusDI + $minusDI) == 0) ? 0 : (100 * abs($plusDI - $minusDI) / ($plusDI + $minusDI));
            
            $adx = (($adx * ($period - 1)) + $dx) / $period;
            $this->adx[] = $adx;
        }
    }
    
    /**
     * คำนวณ Ichimoku Cloud
     */
 private function calculateIchimokuCloud(array $highs, array $lows) {
        $count = count($highs);
        $this->ichimoku = array_fill(0, $count, [
            'tenkan' => null,
            'kijun' => null,
            'senkou_a' => null,
            'senkou_b' => null,
            'chikou' => null
        ]);
        
        // คำนวณ Tenkan-sen (Conversion Line)
        for ($i = 8; $i < $count; $i++) {
            $highSlice = array_slice($highs, $i - 8, 9);
            $lowSlice = array_slice($lows, $i - 8, 9);
            $this->ichimoku[$i]['tenkan'] = (max($highSlice) + min($lowSlice)) / 2;
        }
        
        // คำนวณ Kijun-sen (Base Line)
        for ($i = 25; $i < $count; $i++) {
            $highSlice = array_slice($highs, $i - 25, 26);
            $lowSlice = array_slice($lows, $i - 25, 26);
            $this->ichimoku[$i]['kijun'] = (max($highSlice) + min($lowSlice)) / 2;
        }
        
        // คำนวณ Senkou Span A (Leading Span A)
        for ($i = 25; $i < $count; $i++) {
            if ($this->ichimoku[$i]['tenkan'] !== null && $this->ichimoku[$i]['kijun'] !== null) {
                $this->ichimoku[$i]['senkou_a'] = ($this->ichimoku[$i]['tenkan'] + $this->ichimoku[$i]['kijun']) / 2;
            }
        }
        
        // คำนวณ Senkou Span B (Leading Span B)
        for ($i = 51; $i < $count; $i++) {
            $highSlice = array_slice($highs, $i - 51, 52);
            $lowSlice = array_slice($lows, $i - 51, 52);
            $this->ichimoku[$i]['senkou_b'] = (max($highSlice) + min($lowSlice)) / 2;
        }
        
        // คำนวณ Chikou Span (Lagging Span)
        for ($i = 0; $i < $count - 25; $i++) {
            $this->ichimoku[$i]['chikou'] = $this->candles[$i + 25]['close'];
        }
  }




function __construct($candleData) { 

         $this->candleData  = $candleData;
	 
} // end __construct

function TestCase() {


}
  // Methods
function init_CSS_JS($foldername) { ?>


<?php
} 
function init_Data() { 

        require_once($_SERVER['DOCUMENT_ROOT'] ."/dataservice/clsDataService.php"); 
        $clsDataService = new clsDataService($this->shopName,$this->memberid) ;
	$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
        require_once($newUtilPath ."src/dataservice/index.php"); 

}

function Rendor() { 

        require_once($_SERVER['DOCUMENT_ROOT'] ."/dataservice/clsDataService.php"); 
        $clsDataService = new clsDataService($this->shopName,$this->memberid) ;

}

/*
require_once($_SERVER['DOCUMENT_ROOT'] ."/shopA/cls***.php"); 
$cls_aa = new $cls_aa() ;
*/


} // end class

  


?>