<?php
//clsCandlestickIndy.php
ob_start();
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
1.calculateEMA($prices, $period)
2.detectTrendReversals($emaValues, $sensitivity = 0.1)
3.detectParallelSlope($emaValues, $tolerance = 0.01)
4.detectEMACrossover($prices, $candles)
5.aggregateCandles($oneMinuteCandles, $targetTimeframe)
*/

class CandlestickIndy {


function __construct($curpair,$timeframe=1 ) { 

		$this->curpair = $curpair;
		$this->timeframe = $timeframe;
		$this->tolerance = 0.01;
		$this->PIPMultiply = 1000;
		$this->SlopeMultiply = 10000 ; //1000*1000;
		
} // end __construct


    /**
     * รวมข้อมูลแท่งเทียนจาก 1 นาที เป็น Timeframe ที่ต้องการ
     * 
     * @param array $oneMinuteCandles อาร์เรย์ของแท่งเทียน 1 นาที
     * @param int $targetTimeframe timeframe เป้าหมาย (5 หรือ 15)
     * @return array แท่งเทียนที่รวมแล้ว
     */


function analyzeCandlestick($candle) {

    $open = $candle['open'] ;
	$close = $candle['close'] ;
	$high  = $candle['high'] ;
	$low  = $candle['low'] ;

    $body = abs($close - $open); // ขนาดของตัวเทียน
    $upperWick = $high - max($open, $close); // ไส้บน
    $lowerWick = min($open, $close) - $low; // ไส้ล่าง
    $totalHeight = $high - $low; // ความสูงทั้งหมดของแท่งเทียน

    // คำนวณเปอร์เซ็นต์
    $bodyPercent = ($totalHeight > 0) ? ($body / $totalHeight) * 100 : 0;
    $upperWickPercent = ($totalHeight > 0) ? ($upperWick / $totalHeight) * 100 : 0;
    $lowerWickPercent = ($totalHeight > 0) ? ($lowerWick / $totalHeight) * 100 : 0;

    // ตรวจสอบประเภทของแท่งเทียน
    $type = "Unknown";
    if ($bodyPercent < 10) {
        if ($upperWickPercent > 40 && $lowerWickPercent > 40) {
            $type = "Long-Legged Doji";
        } elseif ($upperWickPercent > 60) {
            $type = "Gravestone Doji";
        } elseif ($lowerWickPercent > 60) {
            $type = "Dragonfly Doji";
        } else {
            $type = "Doji";
        }
    } elseif ($bodyPercent > 80) {
        $type = ($close > $open) ? "Bullish Marubozu" : "Bearish Marubozu";
    } elseif ($lowerWickPercent > 50 && $bodyPercent < 30) {
        $type = ($close > $open) ? "Hammer" : "Hanging Man";
    } elseif ($upperWickPercent > 50 && $bodyPercent < 30) {
        $type = ($close > $open) ? "Inverted Hammer" : "Shooting Star";
    }
	$bongton = 'n' ;
	if ($upperWickPercent ===0 && $lowerWickPercent===0) {
		$bongton = 'y' ;
	} 


    return [
        'type' => $type,
        'body_percent' => round($bodyPercent, 2),
        'upper_wick_percent' => round($upperWickPercent, 2),
        'lower_wick_percent' => round($lowerWickPercent, 2),
		'bongton' => $bongton 
    ];
}



function aggregateCandles($oneMinuteCandles, $targetTimeframe) {
    // ตรวจสอบว่า timeframe เป็นตัวเลขบวก
    if (!is_numeric($targetTimeframe) || $targetTimeframe <= 0) {
        throw new InvalidArgumentException("Timeframe ต้องเป็นตัวเลขบวก");
    }

    $aggregatedCandles = [];
    $currentGroup = null;

    foreach ($oneMinuteCandles as $candle) {
        // คำนวณ timestamp กลุ่ม
        $groupTimestamp = floor($candle['timestamp'] / ($targetTimeframe * 60)) * ($targetTimeframe * 60);

        // ถ้ายังไม่มีกลุ่มปัจจุบันหรือเป็นกลุ่มใหม่
        if ($currentGroup === null || $groupTimestamp != $currentGroup['timestamp']) {
            // บันทึกกลุ่มก่อนหน้า
            if ($currentGroup !== null) {
                $aggregatedCandles[] = $currentGroup;
            }

            // เริ่มกลุ่มใหม่
            $currentGroup = [
				'id' => $groupTimestamp,
                'timestamp' => $groupTimestamp,
				'from' => $groupTimestamp,
				'fromTime' =>  date('Y-m-d H:i:s',$groupTimestamp),
                'open' => $candle['open'],
                'high' => $candle['high'],
                'low' => $candle['low'],
                'close' => $candle['close'],
                'volume' => $candle['volume']
            ];
        } else {
            // อัปเดตกลุ่มปัจจุบัน
            $currentGroup['high'] = max($currentGroup['high'], $candle['high']);
            $currentGroup['low'] = min($currentGroup['low'], $candle['low']);
            $currentGroup['close'] = $candle['close'];
            $currentGroup['volume'] += $candle['volume'];
        }
    }

    // เพิ่มกลุ่มสุดท้าย
    if ($currentGroup !== null) {
        $aggregatedCandles[] = $currentGroup;
    }

    return $aggregatedCandles;
}
    /**
     * สร้างข้อมูลตัวอย่าง
     * 
     * @return array ข้อมูลแท่งเทียน 1 นาที
     */
function generateSampleData() {
        $sampleCandles = [];
        $baseTimestamp = strtotime('2024-01-01 00:00:00');

        for ($i = 0; $i < 30; $i++) {
            $sampleCandles[] = [
                'timestamp' => $baseTimestamp + $i * 60,
                'open' => rand(100, 110),
                'high' => rand(110, 120),
                'low' => rand(90, 100),
                'close' => rand(100, 110),
                'volume' => rand(1000, 5000)
            ];
        }

        return $sampleCandles;
    }

function calculateEMA($prices, $period) {
    // ตรวจสอบว่ามีข้อมูลเพียงพอหรือไม่
    if (count($prices) < $period) {
        return []; // ส่งคืนอาร์เรย์ว่างถ้าข้อมูลไม่เพียงพอ
    }

    // คำนวณค่า k
    $k = 2 / ($period + 1);

    // คำนวณ SMA เริ่มต้นสำหรับค่าแรก
    $initialSlice = array_slice($prices, 0, $period);
    $initialEMA = array_sum($initialSlice) / $period;
    $emaValues = [$initialEMA];

    // คำนวณ EMA สำหรับข้อมูลที่เหลือ
    for ($i = $period; $i < count($prices); $i++) {
        $currentPrice = $prices[$i];
		
        $prevEMA = end($emaValues);
		 

        // ป้องกันค่า NaN ด้วยการตรวจสอบ
        if (!is_nan($currentPrice) && !is_nan($prevEMA)) {
            $newEMA = ($currentPrice - $prevEMA) * $k + $prevEMA;
            $emaValues[] = $newEMA;
        }
    }
    // ตัวล่าสุด คือ index= 0 ไม่ใช่   count($emaValues)-1 ???
    return $emaValues;
} 

function calculateEMAStylePython($prices, $period) {
    // สร้าง array ผลลัพธ์เท่ากับ input
    $ema = array_fill(0, count($prices), null);
    
    // ตรวจสอบว่ามีข้อมูลครบตามช่วง
    if (count($prices) >= $period) {
        $smoothing = 2 / ($period + 1);
        
        // คำนวณ SMA เริ่มแรก
        $initialSlice = array_slice($prices, 0, $period);
        $sma = array_sum($initialSlice) / $period;
        
        // กำหนดค่า EMA เริ่มแรก
        $ema[$period - 1] = $sma;
        
        // คำนวณ EMA ต่อไป
        for ($i = $period; $i < count($prices); $i++) {
            $ema[$i] = ($prices[$i] - $ema[$i-1]) * $smoothing + $ema[$i-1];
        }

		for ($i=0;$i<=count($ema)-1;$i++) {
			$ema[$i] = round($ema[$i],7) ;
		   
		}
    }
    
    return $ema;
}


function detectTrendReversals($emaValues, $sensitivity = 0.1) {
    $reversals = [
        'TurnUp' => [],
        'TurnDown' => []
    ];

    for ($i = 2; $i < count($emaValues); $i++) {
        $prev2 = $emaValues[$i - 2];
        $prev1 = $emaValues[$i - 1];
        $current = $emaValues[$i];

        // ตรวจจับ Turn Up
        if ($prev2 > $prev1 && $prev1 < $current) {
            $reversals['TurnUp'][] = [
                'index' => $i,
                'ema' => $current
            ];
        }

        // ตรวจจับ Turn Down
        if ($prev2 < $prev1 && $prev1 > $current) {
            $reversals['TurnDown'][] = [
                'index' => $i,
                'ema' => $current
            ];
        }
    }

    return $reversals;
}

function detectParallelSlope($emaValues, $tolerance = 0.01) {
    $parallelSegments = [];

    for ($i = 2; $i < count($emaValues) - 1; $i++) {
        for ($j = $i + 1; $j < count($emaValues); $j++) {
            // คำนวณความชัน (slope) ระหว่างจุด i และ j
            $slope1 = ($emaValues[$i] - $emaValues[$i-1]);
            $slope2 = ($emaValues[$j] - $emaValues[$j-1]);

            // ตรวจสอบว่าความชันคล้ายกันภายในค่าความคลาดเคลื่อนที่กำหนด
            if (abs($slope1 - $slope2) <= $tolerance) {
                $parallelSegments[] = [
                    'start1' => $i - 1,
                    'end1' => $i,
                    'start2' => $j - 1,
                    'end2' => $j,
                    'slope1' => $slope1,
                    'slope2' => $slope2
                ];
            }
        }
    }

    return $parallelSegments;
}

//function detectEMACrossover($prices, $candles) {
function detectEMACrossover($ema3Values,$ema5Values,$candles) {
    // คำนวณ EMA3 และ EMA5
  //  $ema3Values = calculateEMA($prices, 3);
  //  $ema5Values = calculateEMA($prices, 5);

    $crossovers = [];

    // ตรวจจับจุดตัดกัน
    for ($i = 1; $i < count($ema3Values)-1; $i++) {
        $prevEMA3 = $ema3Values[$i-1];
        $currentEMA3 = $ema3Values[$i];
        $prevEMA5 = $ema5Values[$i-1];
        $currentEMA5 = $ema5Values[$i];

        // ตรวจสอบการตัดกัน
        if (
            ($prevEMA3 < $prevEMA5 && $currentEMA3 > $currentEMA5) || 
            ($prevEMA3 > $prevEMA5 && $currentEMA3 < $currentEMA5)
        ) {
            $crossoverType = ($prevEMA3 < $prevEMA5) ? 'Bullish (EMA3 crosses above EMA5)' : 'Bearish (EMA5 crosses above EMA3)';
            
            // วิเคราะห์ตำแหน่งการตัดบนแท่งเทียน
            $currentCandle = $candles[$i];
			$crossoverPosition ='';
            $crossoverPosition = $this->analyzeCrossoverPosition($currentCandle);
/*
            $crossovers[] = [
                'index' => $i,
                'type' => $crossoverType,
                'ema3_prev' => $prevEMA3,
                'ema3_current' => $currentEMA3,
                'ema5_prev' => $prevEMA5,
                'ema5_current' => $currentEMA5,
                'candle' => $currentCandle,
                //'crossover_position' => $crossoverPosition
            ];
*/
			$crossovers[] = [
                'index' => $i,
				'from' => $candles[$i]['from'],
				'minuteno' => date('Y-m-d H:i:s',$candles[$i]['from']),
                'type' => $crossoverType,
				'crossoverPosition'=>$crossoverPosition
                
            ];
        }
    }

    return $crossovers;
}

function analyzeCrossoverPosition($candle) {



    $candle2 = JSON_ENCODE($candle);
	$candle = JSON_DECODE($candle2,true);
    // สมมุติโครงสร้าง $candle ประกอบด้วย open, high, low, close
    $candleBody = abs($candle['close'] - $candle['open']);
    $candleHigh = $candle['high'];
    $candleLow = $candle['low'];
    $candleClose = $candle['close'];
    $candleOpen = $candle['open'];

    // คำนวณตำแหน่งการตัด
    $bodyBottom = min($candleOpen, $candleClose);
    $bodyTop = max($candleOpen, $candleClose);
    $bodyHeight = $candleBody;
    $totalHeight = $candleHigh - $candleLow;

	if ($totalHeight != 0) {	
    // วิเคราะห์ตำแหน่งการตัด
      if (abs($candleClose - $candleOpen) / $totalHeight < 0.3) {
         return 'Near Candle Edges (Potential Reversal Signal)';
      }
	}

    $bottomThird = $candleLow + ($totalHeight / 3);
    $topThird = $candleHigh - ($totalHeight / 3);

    if ($bodyBottom >= $bottomThird && $bodyTop <= $topThird) {
        return 'Middle of Candle Body';
    } elseif ($bodyBottom < $bottomThird) {
        return 'Lower Third of Candle';
    } elseif ($bodyTop > $topThird) {
        return 'Upper Third of Candle';
    }

    return 'Undefined Position';
}// end func 


function ExtractDataFromMQL5($data) { 

 $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';	
 

 // บันทึก raw data เพื่อตรวจสอบ
 file_put_contents('debug_raw_log.txt',  $data . "\n", FILE_APPEND);

 $sFileNameToRead = $newUtilPath .'eaforex/debug_raw_log.txt';

 $file = fopen($sFileNameToRead,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file);

$postData = $st;
$rawData = $st;
$pairs = [];


parse_str($rawData, $resultData);
$candles = $resultData['candles'] ;
//echo "<hr>";
//print_r($candles);
echo $candles[0]['time'] . ' = ' . $candles[0]['close'] ;




} // end function

function findControlCharacters($string) {
// หา อักขระ ทืี่ทำให้  การแปลง json  error 
// $cleanJsonString = preg_replace('/[\x00-\x1F\x80-\x9F]/', '', $jsonString);

    $problematicChars = [];
    for ($i = 0; $i < strlen($string); $i++) {
        $char = $string[$i];
        $ord = ord($char);
        if ($ord < 32 || ($ord >= 127 && $ord <= 159)) {
            $problematicChars[] = [
                'position' => $i,
                'character' => $char,
                'ascii' => $ord
            ];
        }
    }
    return $problematicChars;
} 

public function saveAsHTML($filename = 'eurusd_data.html') {
        $html_content = $this->displayAsHTMLTable();
        file_put_contents($filename, $html_content);
        return $filename;
}

// สร้าง HTML Table จากข้อมูล
public function displayAsHTMLTable($caption,$data) {
        try {
            //$data = $this->fetchData();

            // เริ่มสร้าง HTML
            $html = '<!DOCTYPE html>
            <html lang="th">
            <head>
                <meta charset="UTF-8">
                <title>'. $caption.'</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-top: 20px; 
                    }
                    th, td { 
                        border: 1px solid #ddd; 
                        padding: 8px; 
                        text-align: right; 
                    }
                    th { 
                        background-color: #f2f2f2; 
                        font-weight: bold; 
                    }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    tr:hover { background-color: #f5f5f5; }
                </style>
            </head>
            <body>
                <h2>'.  $caption. '</h2>
                <table>
                    <thead>
                        <tr>
                            <th>วันที่และเวลา</th>
                            <th>Open</th>
                            <th>High</th>
                            <th>Low</th>
                            <th>Close</th>
                            <th>Volume</th>
                        </tr>
                    </thead>
                    <tbody>';

            // เพิ่มแถวข้อมูล
            foreach ($data as $row) {
                $html .= "<tr>
                    <td>" . htmlspecialchars($row['timestamp']) . '->'.
					 htmlspecialchars(date('Y-m-d H:i:s',$row['timestamp'])) . "</td>
                    <td>" . number_format($row['open'], 6) . "</td>
                    <td>" . number_format($row['high'], 6) . "</td>
                    <td>" . number_format($row['low'], 6) . "</td>
                    <td>" . number_format($row['close'], 6) . "</td>
                    <td>" . number_format($row['volume'], 0) . "</td>
                </tr>";
            }

            // ปิด HTML
            $html .= '</tbody>
                </table>
            </body>
            </html>';

            return $html;
        } catch (Exception $e) {
            return "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
} // end func


public function dynamicDisplayAsHTMLTable($caption, $data) {

    try {
        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (empty($data)) {
            return "ไม่มีข้อมูล";
        }

        // ดึง keys จากแถวแรกเพื่อใช้เป็นหัวคอลัมน์
        $headers = array_keys($data[0]);

        // เริ่มสร้าง HTML
        $html = '<!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <title>'. htmlspecialchars($caption).'</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 20px; 
                }
                th, td { 
                    border: 1px solid #ddd; 
                    padding: 8px; 
                    text-align: right; 
                }
                th { 
                    background-color: #f2f2f2; 
                    font-weight: bold; 
                }
                tr:nth-child(even) { background-color: #f9f9f9; }
                tr:hover { background-color: #f5f5f5; }
            </style>
        </head>
        <body>
            <h2>'.  htmlspecialchars($caption). '</h2>
            <table>
                <thead>
                    <tr>';
        
        // สร้างหัวตาราง dynamic
        foreach ($headers as $header) {
            $html .= "<th>" . htmlspecialchars(ucfirst($header)) . "</th>";
        } 


        
        $html .= '</tr>
                </thead>
                <tbody>';
        
        // เพิ่มแถวข้อมูล dynamic
        foreach ($data as $row) {
            $html .= "<tr>";
            foreach ($headers as $header) {
                // ตรวจสอบประเภทข้อมูลเพื่อจัดรูปแบบ
				/*
                if ($header == 'timestamp') {
                    $value = htmlspecialchars($row[$header]);
                } elseif (is_numeric($row[$header])) {
                    // ตรวจสอบว่าเป็นจำนวนทศนิยมหรือจำนวนเต็ม
                    $value = is_float($row[$header]) 
                        ? number_format($row[$header], 6) 
                        : number_format($row[$header], 0);
                } else {
                    $value = htmlspecialchars($row[$header]);
                }
				*/
				$value = htmlspecialchars($row[$header]);
                $html .= "<td>" . $value . "</td>";
            }
            $html .= "</tr>";
        }
        
        // ปิด HTML
        $html .= '</tbody>
            </table>
        </body>
        </html>';
        
        return $html;
    } catch (Exception $e) {
        return "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
} // end func

function get_close_prices($candleDataList) { 

	     
         $prices = array_column($candleDataList, 'close');
         return $prices ; 



} // end function

function checkSlope($ema,$thisPointIndex) { 

	$slopeValue = $ema[$thisPointIndex] - $ema[$thisPointIndex-1];
	$slopeValue = $slopeValue * $this->SlopeMultiply;
	if ($slopeValue > 0 ) {
		$slopeType = 'Up';
	}
	if ($slopeValue < 0 ) {
		$slopeType = 'Down';
	}
	if (abs($slopeValue) <= $this->tolerance) {
       $slopeType = 'P';      
	}

	return array($slopeValue,$slopeType) ;



} // end function



function newEMAObject() { 

$st='
{
  "curpairID": "",
  "timeframe" : "",
  "id": "",
  "timestamp": "",
  "timefrom": "",
  "timefrom_unix": "",
  "emaPatternCode": "",
  "slopePatternCode": "",
  "ColorPatternCode": "",
  "TurnTypePatternCode": "",
  "previousMasterCode": "",
  "thismasterCode": "",
  "MixMasterCode": "",
  "previousMasterCode2": "",
  "thismasterCode2": "",
  "MixMasterCode2": "",
  "AllCode": "",
  "AllCode2": "",
  "AllCode15": "",
  "AllCode13": "",
  "minuteno": "",
  "previousPIP": "",
  "pip": "",
  "pip2": "",
  "pipGrowth": "",
  "code": "",
  "previousEMA3": "",
  "ema3": "",
  "ema5": "",
  "differEMA": "",
  "ema3SlopeValue": "",
  "ema5SlopeValue": "",
  "PreviousSlopeDirection": "",
  "ema3slopeDirection": "",
  "ema5slopeDirection": "",
  "MACDHeight": "",
  "MACDHeightCode": "",
  "MACDConvergence": "",
  "emaAbove": "",
  "emaConflict": "N",
  "previousColorBack4": "N",
  "previousColorBack3": "N",
  "previousColorBack2": "N",
  "previousColor": "N",
  "thisColor": "",
  "nextColor": "",
  "CutPointType": "N",
  "crossoverPosition" : "N",
  "TurnType": "",  
  "bodyShape": "",
  "resultColor": "",
  "ADX": "",
  "ADXShort": "",
  "cci": "",
  "Score": "",

   
  "PreviousTurnType": "N",
  "PreviousTurnTypeBack2": "N",
  "PreviousTurnTypeBack3": "N",
  "PreviousTurnTypeBack4": "N"

}';


 

$obj = json_decode($st,true);
return $obj ;
	

} // end function


function CreateAnalyisData($candleDataList) { 

if (is_object($candleDataList) || is_array($candleDataList)) {
  //echo 'Type Object' ;
} else {
  //echo 'Type String' ;
  $candleDataList = JSON_DECODE($candleDataList,true);
}
for ($i=0;$i<=count($candleDataList)-1;$i++) {
   $candleDataList[$i]['id'] = $i+1 ;
   $candleDataList[$i]['from'] = $candleDataList[$i]['epoch'] ;   
}

$closePrices = $this->get_close_prices($candleDataList) ;

$ema3= $this->calculateEMAStylePython($closePrices, $period=3);
$ema5= $this->calculateEMAStylePython($closePrices, $period=5);

$totalData = count($candleDataList) ;
//$AnalyObjList = $this->newEMAObject($totalData)  ;
$CutOverList = $this->detectEMACrossover($ema3,$ema5,$candleDataList);
//echo JSON_ENCODE($CutOverList); return ;

for ($i=0;$i<=$totalData-1;$i++) {
	$AnalyObjTmp = $this->newEMAObject()  ;
	
	
	$CandleTypeBody = $this->analyzeCandlestick($candleDataList[$i])  ;
    $AnalyObjTmp['body_percent'] = $CandleTypeBody['body_percent'];
	$AnalyObjTmp['upper_wick_percent'] = $CandleTypeBody['upper_wick_percent'];
	$AnalyObjTmp['lower_wick_percent'] = $CandleTypeBody['lower_wick_percent'];
	$AnalyObjTmp['candleType'] = $CandleTypeBody['type'];
	$AnalyObjTmp['bongton'] = $CandleTypeBody['bongton'];



	//$AnalyObjList[$i]['CandleType'] = $CandleTypeBody['type'];
    $AnalyObjTmp['id'] = $candleDataList[$i]['id'] ;
	$AnalyObjTmp['timeframe'] = $this->timeframe ;
	$AnalyObjTmp['timefrom'] = $candleDataList[$i]['from'] ;
	
	$AnalyObjTmp['timefrom_unix'] = date('Y-m-d H:i:s',$candleDataList[$i]['from']) ;
	$AnalyObjTmp['minuteno'] = date('H:i:s',$candleDataList[$i]['from']) ;

	/* EMA Section */
	$AnalyObjTmp['ema3'] = $ema3[$i] ;
	$AnalyObjTmp['ema5'] = $ema5[$i] ;
	if ($ema3[$i] != $ema5[$i] ) {
	  $emaAbove = ($ema3[$i] > $ema5[$i]) ? '3' : '5';
	} else {
      $emaAbove = '3=5' ;
	}	
	$AnalyObjTmp['emaAbove'] = $emaAbove ;
	
	// slopevalue,slopeType
	if ($i>=2) {
	  list($slope3Value,$slope3Direction) = $this->checkSlope($ema3,$i);
	} else {
		$slope3Value = null ; $slope3Direction = null;
	}
	if ($i>=5) {
	  list($slope5Value,$slope5Direction) = $this->checkSlope($ema5,$i);
	} else {
		$slope5Value = null ; $slope5Direction = null;
	}
	$AnalyObjTmp['ema3SlopeValue'] =  $slope3Value ;
	$AnalyObjTmp['ema5SlopeValue'] =  $slope5Value ;

	$AnalyObjTmp['ema3slopeDirection'] =  $slope3Direction ;
	$AnalyObjTmp['ema5slopeDirection'] =  $slope5Direction ;    
	/* Turn Type */
	$ema3TurnType =  "N" ;
	if ($i-1 >= 0) {	
		if (($ema3[$i-1] > $ema3[$i]) &&  ($ema3[$i-1] > $ema3[$i-2])) {
			$ema3TurnType ='TurnDown' ;
		}
		if (($ema3[$i-1] < $ema3[$i]) &&  ($ema3[$i-1] < $ema3[$i-2])) {
			$ema3TurnType ='TurnUp' ;
		}
	}
	// จุดที่แล้ว เป็น จุด Turn ประเภท ?? 
	//$AnalyObjTmp['ema3TurnType'] =  $ema3TurnType;

	$ema5TurnType =  "N" ;
	if ($i-1 >= 0) {	
		if (($ema5[$i-1] > $ema5[$i]) &&  ($ema5[$i-1] > $ema5[$i-2])) {
			$ema5TurnType ='TurnDown' ;
		}
		if (($ema5[$i-1] < $ema5[$i]) &&  ($ema5[$i-1] < $ema5[$i-2])) {
			$ema5TurnType ='TurnUp' ;
		}
	}
	
	$AnalyObjTmp['MACDHeight'] = $ema3[$i] - $ema5[$i] ;


	/* This Color  Section */
	if ($candleDataList[$i]['close'] != $candleDataList[$i]['open']) {
		$thisColor = ($candleDataList[$i]['close'] > $candleDataList[$i]['open']) ? 'Green' : 'Red';
	} else {
		$thisColor =  'Equal' ;	
	}	
	$AnalyObjTmp['thisColor'] = $thisColor ;

    $emaConflict = 'N'; 
	if ($emaAbove == 3 && $thisColor=='Red') {
       $emaConflict = '35R';
	}
	if ($emaAbove == 5 && $thisColor=='Green') {
       $emaConflict = '53G';
	}
	$AnalyObjTmp['emaConflict'] = $emaConflict ;



	/* PIP SECTION*/
    $AnalyObjTmp['pip'] = ($ema3[$i] - $ema5[$i])*$this->PIPMultiply ;

	$AnalyObjList[] = $AnalyObjTmp;
	$lastIndex = count($AnalyObjList) -2 ;
	if ($lastIndex > 0) {	
	  $AnalyObjList[$lastIndex]['ema3TurnType'] = $ema3TurnType ;
	  $AnalyObjList[$lastIndex]['ema5TurnType'] = $ema5TurnType ;
	}

	if (count($AnalyObjList) == 2 ) {	
      $AnalyObjList[1]['previousColor'] = $AnalyObjList[0]['thisColor'] ;
	}
	if (count($AnalyObjList) == 3 ) {	
      $AnalyObjList[2]['previousColor'] = $AnalyObjList[1]['thisColor'] ;
	  $AnalyObjList[2]['previousColorBack2'] = $AnalyObjList[1]['previousColor'] ;
	}
	if (count($AnalyObjList) == 4 ) {	
      $AnalyObjList[3]['previousColor'] = $AnalyObjList[2]['thisColor'] ;
	  $AnalyObjList[3]['previousColorBack2'] = $AnalyObjList[2]['previousColor'] ;
	}
 
} // end for
// จบแล้ว Loop ต่อ

$lastIndex = count($AnalyObjList) -1 ;
for ($i=1;$i<=count($AnalyObjList)-1;$i++) {


    

	$AnalyObjList[$i]['previousColor'] = $AnalyObjList[$i-1]['thisColor'] ;
	if (isset($AnalyObjList[$i-1]['previousColor'])) {
	   $AnalyObjList[$i]['previousColorBack2'] = $AnalyObjList[$i-1]['previousColor'] ;
	} else {
	   $AnalyObjList[$i]['previousColorBack2'] = 'N' ;
	}

	if (isset($AnalyObjList[$i-1]['previousColorBack2'])) {
	  $AnalyObjList[$i]['previousColorBack3'] = $AnalyObjList[$i-1]['previousColorBack2'] ;
	} else {
	  $AnalyObjList[$i]['previousColorBack3'] = 'N';
	}
	if (isset($AnalyObjList[$i-1]['previousColorBack3'] )) {
	  $AnalyObjList[$i]['previousColorBack4'] = $AnalyObjList[$i-1]['previousColorBack3'] ;
	} else {
	  $AnalyObjList[$i]['previousColorBack4'] = 'N';
	}
}

for ($i=3;$i<=count($AnalyObjList)-1;$i++) {
 
/*
   "TurnType": "",
  "PreviousTurnType": "N",
  "PreviousTurnTypeBack2": "N",
  "PreviousTurnTypeBack3": "N",
  "PreviousTurnTypeBack4": "N",
*/
    
	if (isset($AnalyObjList[$i-1]['MACDHeight'])) {
       $macdDiff =  $AnalyObjList[$i]['MACDHeight']- $AnalyObjList[$i-1]['MACDHeight'];
	   if ($macdDiff > 0) {
		 $macdConver = 'Diver';
	   }
	   if ($macdDiff < 0) {
         $macdConver = 'Conver';
	   }
	   if ($macdDiff == 0) {
         $macdConver = 'Zero';
	   }
	   $AnalyObjList[$i]['MACDConvergence'] = $macdConver ;	 
	}

	if ($AnalyObjList[$i-1]['emaAbove'] == "3" and $AnalyObjList[$i]['emaAbove'] == "5") {
       $AnalyObjList[$i]['CutPointType']  = '3=>5(Bear)'    ;
    }
	if ($AnalyObjList[$i-1]['emaAbove'] == "5" and $AnalyObjList[$i]['emaAbove'] == "3") {
       $AnalyObjList[$i]['CutPointType']  = '5=>3(Bull)'    ;
    }

        
	$AnalyObjList[$i]['PreviousTurnType'] = $AnalyObjList[$i-1]['ema3TurnType'] ;
	if (isset($AnalyObjList[$i-1]['PreviousTurnType'])) {
	   $AnalyObjList[$i]['PreviousTurnTypeBack2'] = $AnalyObjList[$i-1]['PreviousTurnType'] ;
	} else {
	  $AnalyObjList[$i]['PreviousTurnTypeBack2'] = 'N';
	}
	if (isset($AnalyObjList[$i-1]['PreviousTurnTypeBack2'] )) {
	   $AnalyObjList[$i]['PreviousTurnTypeBack3'] = $AnalyObjList[$i-1]['PreviousTurnTypeBack2'] ;
	} else {
	   $AnalyObjList[$i]['PreviousTurnTypeBack3'] = 'N';
	}

	if (isset($AnalyObjList[$i-1]['PreviousTurnTypeBack3'] )) {
	   $AnalyObjList[$i]['PreviousTurnTypeBack4'] = $AnalyObjList[$i-1]['PreviousTurnTypeBack3'] ;
	} else {
	   $AnalyObjList[$i]['PreviousTurnTypeBack4'] = 'N';
	}
} // end for 

for ($i=4;$i<=count($AnalyObjList)-1;$i++) {
 
/*
   "TurnType": "",
  "PreviousTurnType": "N",
  "PreviousTurnTypeBack2": "N",
  "PreviousTurnTypeBack3": "N",
  "PreviousTurnTypeBack4": "N",
*/
	$AnalyObjList[$i]['Previous5TurnType'] = $AnalyObjList[$i-1]['ema5TurnType'] ;
	if (isset($AnalyObjList[$i-1]['Previous5TurnType'])) {
	   $AnalyObjList[$i]['Previous5TurnTypeBack2'] = $AnalyObjList[$i-1]['Previous5TurnType'] ;
	} else {
	  $AnalyObjList[$i]['Previous5TurnTypeBack2'] = 'N';
	}
	if (isset($AnalyObjList[$i-1]['Previous5TurnTypeBack2'] )) {
	   $AnalyObjList[$i]['Previous5TurnTypeBack3'] = $AnalyObjList[$i-1]['Previous5TurnTypeBack2'] ;
	} else {
	   $AnalyObjList[$i]['Previous5TurnTypeBack3'] = 'N';
	}

	if (isset($AnalyObjList[$i-1]['Previous5TurnTypeBack3'] )) {
	   $AnalyObjList[$i]['Previous5TurnTypeBack4'] = $AnalyObjList[$i-1]['Previous5TurnTypeBack3'] ;
	} else {
	   $AnalyObjList[$i]['Previous5TurnTypeBack4'] = 'N';
	}
} // end for 

for ($i=0;$i<=count($CutOverList)-1;$i++) {
	 $thisFromTime = $CutOverList[$i]['from'] ;
	 for ($j=0;$j<=count($AnalyObjList)-1;$j++) {
	    if ($thisFromTime == $AnalyObjList[$j]['timefrom']) {
          //$AnalyObjList[$j]['CutPointType'] = $CutOverList[$i]['type'] ;
		  $AnalyObjList[$j]['crossoverPosition'] = $CutOverList[$i]['crossoverPosition'] ;
	    }
	 }   
}





/*
$caption = 'สรุป';
$html3 = $this->dynamicDisplayAsHTMLTable($caption, $AnalyObjList) ;
//echo $html3;
*/

return $AnalyObjList;




} // end function


function InsertData_AnalyEMATmp($dataTxt) { 

require_once("newutil2.php");          
$pdo=getPDONew();


$sql='TRUNCATE AnalyEMATmp' ;
$params = array();
pdoExecuteQueryV2($pdo,$sql,$params) ;


$sqlInsert = 'INSERT INTO AnalyEMATmp(
curpairID, timeframe, id, timestamp, timefrom,
timefrom_unix,minuteno, previousPIP, pip, pip2, 
pipGrowth, code, previousEMA3, ema3, ema5, 
differEMA,ema3SlopeValue, ema5SlopeValue,PreviousSlopeDirection,ema3slopeDirection, ema5slopeDirection, MACDHeight, MACDHeightCode, MACDConvergence, emaAbove,
emaConflict,previousColorBack4,previousColorBack3,previousColorBack2,previousColor,
thisColor,nextColor,CutPointType,TurnType,PreviousTurnType,
PreviousTurnTypeBack2, PreviousTurnTypeBack3,PreviousTurnTypeBack4,bodyShape,resultColor, ADX,ADXShort, cci, Score) values(
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?,?,
?,?,?,?
)';

$jsonObj = JSON_DECODE($dataTxt,true);


for ($i=0;$i<=count($jsonObj)-1;$i++) {
   
    $jsonObj[$i]["curpairID"] = $i;
	$jsonObj[$i]["ema3SlopeValue"] = $jsonObj[$i]["ema3SlopeValue"]=== null ? 0 : $jsonObj[$i]["ema3SlopeValue"];
    $jsonObj[$i]["ema3slopeDirection"] = $jsonObj[$i]["ema3slopeDirection"]=== null ? 'N' : $jsonObj[$i]["ema3slopeDirection"];

	$params = array(
	
	$jsonObj[$i]["curpairID"],
	$jsonObj[$i]["timeframe"],
	$jsonObj[$i]["id"],
	$jsonObj[$i]["timestamp"],
	$jsonObj[$i]["timefrom"],

	$jsonObj[$i]["timefrom_unix"],	
	$jsonObj[$i]["minuteno"],
	$jsonObj[$i]["previousPIP"],
	$jsonObj[$i]["pip"],
	$jsonObj[$i]["pip2"],

	$jsonObj[$i]["pipGrowth"],
	$jsonObj[$i]["code"],
	$jsonObj[$i]["previousEMA3"],
	$jsonObj[$i]["ema3"],
	$jsonObj[$i]["ema5"],

	$jsonObj[$i]["differEMA"],
	$jsonObj[$i]["ema3SlopeValue"] ,
	$jsonObj[$i]["ema5SlopeValue"],
	$jsonObj[$i]["PreviousSlopeDirection"],
	$jsonObj[$i]["ema3slopeDirection"],

	$jsonObj[$i]["ema5slopeDirection"],
	$jsonObj[$i]["MACDHeight"],
	$jsonObj[$i]["MACDHeightCode"],
	$jsonObj[$i]["MACDConvergence"],
	$jsonObj[$i]["emaAbove"],


	$jsonObj[$i]["emaConflict"],
	$jsonObj[$i]["previousColorBack4"],
	$jsonObj[$i]["previousColorBack3"],
	$jsonObj[$i]["previousColorBack2"],
	$jsonObj[$i]["previousColor"],

	$jsonObj[$i]["thisColor"],
	$jsonObj[$i]["nextColor"],
	$jsonObj[$i]["CutPointType"],
	$jsonObj[$i]["TurnType"],
	$jsonObj[$i]["PreviousTurnType"],

	$jsonObj[$i]["PreviousTurnTypeBack2"],
	$jsonObj[$i]["PreviousTurnTypeBack3"],
	$jsonObj[$i]["PreviousTurnTypeBack4"],
	$jsonObj[$i]["bodyShape"],
	$jsonObj[$i]["resultColor"],

	$jsonObj[$i]["ADX"],
	$jsonObj[$i]["ADXShort"],
	$jsonObj[$i]["cci"],
	$jsonObj[$i]["Score"]) ;
	//echo count($params) ; return ;

	$ErrMsg  = '';                     
       try {                     
          $rs = $pdo->prepare($sqlInsert);
          $rs->execute($params);          

		  
       } catch (PDOException $ex) {
          echo  $ex->getMessage();
		  return false ;
       
       } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
		  return false ;
       }

	
	unset($params);
} // end for 

} // end function


} // end class
/*
// ตัวอย่างการใช้งาน
$aggregator = new CandlestickIndy();

// สร้างข้อมูลตัวอย่าง
$oneMinuteCandles = $aggregator->generateSampleData();

// รวมเป็น 5 นาที
$fiveMinuteCandles = $aggregator->aggregateCandles($oneMinuteCandles, 5);

// รวมเป็น 15 นาที
$fifteenMinuteCandles = $aggregator->aggregateCandles($oneMinuteCandles, 15);

// พิมพ์ผลลัพธ์
echo "แท่งเทียน 5 นาที:\n";
print_r($fiveMinuteCandles);

echo "\nแท่งเทียน 15 นาที:\n";
print_r($fifteenMinuteCandles);

2024.12.13 13:01:42.301	web_request2 (EURUSD,H1)	<b>Fatal error</b>:  Uncaught TypeError: is_nan(): Argument #1 ($num) must be of type float, array given in /home/ddhousin/domains/lovetoshopmall.com/private_html/eaforex/clsCandlestickIndy.php:113

/*
จงระบุ ชนิดแท่งเทียน ประเภทต่างๆ  เช่น doji,hammer หรือ ประเภทอื่นๆ จาก ข้อมูล  candlestick  และ คำนวณ % ของ body,upperstick,lowerstick   ด้วย php 
*/

