<?php
/*
มี  array ของ ราคา close จำนวน n รายการ หาค่า ema3 ได้  = A 
ต่อมา กำหนด ค่า ema3 = B โดยที่ไม่ทราบว่า close 
ต้องการ แก้สมการ หา ว่า  close ตัวสุดท้ายต้องมีค่า เท่าไร ถึงจะทำให้  ema3 = B 
ทำด้วย php
*/

class EMACalculator {
    // คำนวณ EMA 3 period
    public function calculateEMA3($closeArray) {
        $smoothingFactor = 2 / (3 + 1);
        $ema = $closeArray[0];
        
        for ($i = 1; $i < count($closeArray); $i++) {
            $ema = ($closeArray[$i] * $smoothingFactor) + ($ema * (1 - $smoothingFactor));
        }
        
        return $ema;
    }

    // หา Close ตัวสุดท้ายที่ทำให้ EMA3 เท่ากับค่าเป้าหมาย
    public function findLastCloseForTargetEMA($existingCloseArray, $targetEMA) {
        // ใช้วิธี Binary Search เพื่อหาค่า Close ที่เหมาะสม
        $left = 0;
        $right = $targetEMA * 2; // กำหนดขอบเขตการค้นหา
        
        while ($left <= $right) {
            $midClose = ($left + $right) / 2;
            
            // สร้าง array ใหม่โดยเพิ่ม Close ตัวสุดท้าย
            $testCloseArray = $existingCloseArray;
            $testCloseArray[] = $midClose;
            
            // คำนวณ EMA3 ด้วย Close ใหม่
            $currentEMA = $this->calculateEMA3($testCloseArray);
            
            // เช็คความแม่นยำ
            if (abs($currentEMA - $targetEMA) < 0.0001) {
                return $midClose;
            }
            
            // ปรับขอบเขตการค้นหา
            if ($currentEMA < $targetEMA) {
                $left = $midClose + 0.0001;
            } else {
                $right = $midClose - 0.0001;
            }
        }
        
        return null; // ถ้าไม่พบค่า
    }
}

// ตัวอย่างการใช้งาน
$calculator = new EMACalculator();

// ตัวอย่าง array ของราคา Close
$closeArray = [10, 11, 12];

// คำนวณ EMA3 ปัจจุบัน
$currentEMA = $calculator->calculateEMA3($closeArray);
echo "EMA3 ปัจจุบัน: " . $currentEMA . "<br>";

// หา Close ตัวสุดท้ายที่ทำให้ EMA3 เป็น 15
$targetClose = $calculator->findLastCloseForTargetEMA($closeArray, 15);
echo "Close ตัวสุดท้ายที่ทำให้ EMA3 = 15: คือ " . $targetClose . "<hr>";

$closeArray = [10, 11, 12,18.750025];
$currentEMA = $calculator->calculateEMA3($closeArray);
print_r($currentEMA);




?>
