<?php
//clsAggregate.php

class CandleConverter {
    
    /**
     * แปลง candle data จาก 1 นาที เป็น timeframe ที่ต้องการ
     * 
     * @param array $candleData - array ของ candle data 1 นาที
     * @param int $targetTimeframe - timeframe ที่ต้องการ (เป็นนาที เช่น 3, 5, 15, 30)
     * @return array - array ของ candle data ใหม่
     */
    public function convertTimeframe($candleData, $targetTimeframe) {
        if (empty($candleData) || $targetTimeframe < 1) {
            return [];
        }
        
        // เรียงข้อมูลตาม timestamp
        usort($candleData, function($a, $b) {
            return $a['epoch'] <=> $b['epoch'];
        });
        
        $convertedCandles = [];
        $currentGroup = [];
        $groupStartTime = null;
        
        foreach ($candleData as $candle) {
            $candleTime = $candle['epoch'];
            
            // คำนวณเวลาเริ่มต้นของกลุ่ม (ปัดลงให้ตรงกับ timeframe)
            $groupTime = floor($candleTime / ($targetTimeframe * 60)) * ($targetTimeframe * 60);
            
            // ถ้าเป็นกลุ่มใหม่
            if ($groupStartTime === null || $groupTime !== $groupStartTime) {
                // ประมวลผลกลุ่มเก่า (ถ้ามี)
                if (!empty($currentGroup)) {
                    $convertedCandles[] = $this->processGroup($currentGroup, $groupStartTime);
                }
                
                // เริ่มกลุ่มใหม่
                $currentGroup = [$candle];
                $groupStartTime = $groupTime;
            } else {
                // เพิ่มเข้ากลุ่มปัจจุบัน
                $currentGroup[] = $candle;
            }
        }
        
        // ประมวลผลกลุ่มสุดท้าย
        if (!empty($currentGroup)) {
            $convertedCandles[] = $this->processGroup($currentGroup, $groupStartTime);
        }
        
        return $convertedCandles;
    }
    
    /**
     * ประมวลผลกลุ่มของ candle data เพื่อสร้าง candle ใหม่
     * 
     * @param array $group - กลุ่มของ candle data
     * @param int $groupStartTime - เวลาเริ่มต้นของกลุ่ม
     * @return array - candle data ใหม่
     */
    private function processGroup($group, $groupStartTime) {
        // หา open จาก candle แรก
        $open = $group[0]['open'];
        
        // หา close จาก candle สุดท้าย
        $close = end($group)['close'];
        
        // หา high และ low จากทั้งกลุ่ม
        $high = max(array_column($group, 'high'));
        $low = min(array_column($group, 'low'));
        
        // รวม volume (ถ้ามี)
        $volume = 0;
        foreach ($group as $candle) {
            if (isset($candle['volume'])) {
                $volume += $candle['volume'];
            }
        }
        
        return [
            'epoch' => $groupStartTime,
            'datetime' => date('Y-m-d H:i:s', $groupStartTime),
            'open' => $open,
            'high' => $high,
            'low' => $low,
            'close' => $close,
            'volume' => $volume > 0 ? $volume : null
        ];
    }
    
    /**
     * แปลง JSON string เป็น array และแปลง timeframe
     * 
     * @param string $jsonData - JSON string ของ candle data
     * @param int $targetTimeframe - timeframe ที่ต้องการ
     * @return string - JSON string ของ candle data ใหม่
     */
    public function convertFromJson($jsonData, $targetTimeframe) {
        $candleArray = json_decode($jsonData, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data: ' . json_last_error_msg());
        }
        
        $converted = $this->convertTimeframe($candleArray, $targetTimeframe);
        return json_encode($converted, JSON_PRETTY_PRINT);
    }
} // end class


function Main() { 




// ตัวอย่างการใช้งาน
try {
    // ตัวอย่างข้อมูล candle 1 นาที (รูปแบบที่ Deriv.com ใช้)
    $sampleData = [
        ['epoch' => 1704067260, 'open' => 100.50, 'high' => 100.80, 'low' => 100.40, 'close' => 100.70],
        ['epoch' => 1704067320, 'open' => 100.70, 'high' => 100.90, 'low' => 100.60, 'close' => 100.85],
        ['epoch' => 1704067380, 'open' => 100.85, 'high' => 101.00, 'low' => 100.75, 'close' => 100.95],
        ['epoch' => 1704067440, 'open' => 100.95, 'high' => 101.10, 'low' => 100.80, 'close' => 101.05],
        ['epoch' => 1704067500, 'open' => 101.05, 'high' => 101.20, 'low' => 100.90, 'close' => 101.15],
        ['epoch' => 1704067560, 'open' => 101.15, 'high' => 101.30, 'low' => 101.00, 'close' => 101.25]
    ];
    
    $converter = new CandleConverter();
    
    // แปลงเป็น 3 นาที
    echo "=== แปลงเป็น Timeframe 3 นาท ===\n";
    $result3min = $converter->convertTimeframe($sampleData, 3);
    print_r($result3min);
    
    echo "\n=== แปลงเป็น Timeframe 5 นาท ===\n";
    $result5min = $converter->convertTimeframe($sampleData, 5);
    print_r($result5min);
    
    // ตัวอย่างการใช้กับ JSON
    if (isset($argv[1]) && isset($argv[2])) {
        $jsonFile = $argv[1];
        $timeframe = intval($argv[2]);
        
        if (file_exists($jsonFile)) {
            $jsonData = file_get_contents($jsonFile);
            $convertedJson = $converter->convertFromJson($jsonData, $timeframe);
            
            // บันทึกไฟล์ใหม่
            $outputFile = pathinfo($jsonFile, PATHINFO_FILENAME) . "_{$timeframe}min.json";
            file_put_contents($outputFile, $convertedJson);
            echo "แปลงสำเร็จ! บันทึกไฟล์: $outputFile\n";
        } else {
            echo "ไม่พบไฟล์: $jsonFile\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

} // end function Main()

// ฟังก์ชันช่วยสำหรับการใช้งานง่ายๆ
function convertCandleData($inputData, $targetTimeframe) {
    $converter = new CandleConverter();
    
    if (is_string($inputData)) {
        // ถ้าเป็น JSON string
        return $converter->convertFromJson($inputData, $targetTimeframe);
    } elseif (is_array($inputData)) {
        // ถ้าเป็น array
        return $converter->convertTimeframe($inputData, $targetTimeframe);
    } else {
        throw new Exception('Input data must be JSON string or array');
    }
}



?>

<!-- 

คุณสมบัติหลัก:

แปลง timeframe จาก 1 นาที เป็น timeframe ใดก็ได้ (3, 5, 15, 30 นาที หรือมากกว่า)
รองรับรูปแบบข้อมูล ทั้ง JSON string และ PHP array
จัดการข้อมูล ที่ไม่เรียงลำดับโดยอัตโนมัติ
คำนวณ OHLC อย่างถูกต้อง (Open จาก candle แรก, Close จาก candle สุดท้าย, High/Low จากทั้งกลุ่ม)
วิธีการใช้งาน:

1. ใช้ใน code:
   $converter = new CandleConverter();
   $result = $converter->convertTimeframe($candleData, 3); // แปลงเป็น 3 นาที

2. ใช้ command line:
   php candle_converter.php input.json 5
   (จะสร้างไฟล์ input_5min.json)

3. ใช้ฟังก์ชันง่ายๆ:
   $result = convertCandleData($jsonString, 3);

รูปแบบข้อมูลที่รองรับ:
- epoch: timestamp ในรูปแบบ Unix timestamp
- open, high, low, close: ราคา
- volume: volume (optional)

ข้อดี:
- รองรับ timeframe ใดก็ได้ (3, 5, 15, 30, 60 นาที)
- จัดการข้อมูลที่ไม่เรียงลำดับ
- รองรับทั้ง JSON และ Array
- มี error handling
-->