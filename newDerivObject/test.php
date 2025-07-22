<?php

 ob_start();
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 $st = "";   
 

 $sFileName = '../newDerivObject/dataTest.json';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file); 

 //echo $st ; return;


 $sdata = JSON_DECODE($st,true) ;
 $sdata2 = $sdata['AnalysisData'] ;
 AnalyADX( $sdata2); return;


 

function AnalyADX($data) {

$result = [];

// ตัวแปรสำหรับเก็บทิศทางปัจจุบันและจำนวนการเกิดต่อเนื่อง
$currentDirection = null;
$consecutiveCount = 0;

// วนลูปผ่านข้อมูลแต่ละรายการ
foreach ($data as $index => $item) {
    // สร้างรายการใหม่เริ่มจากข้อมูลเดิม
    $newItem = $item;
    
    // ตรวจสอบทิศทาง ADX โดยเปรียบเทียบกับข้อมูลก่อนหน้า
    if ($index > 0) {
        $previousAdx = $data[$index - 1]['adx'];
        $currentAdx = $item['adx'];
        
        // กำหนดทิศทาง Up หรือ Down
        if ($currentAdx > $previousAdx) {
            $direction = "Up";
        } else if ($currentAdx < $previousAdx) {
            $direction = "Down";
        } else {
            $direction = "Flat"; // ถ้าค่าเท่ากัน
        }
        
        // ตรวจสอบว่าทิศทางเหมือนเดิมหรือไม่
        if ($direction === $currentDirection) {
            $consecutiveCount++;
        } else {
            $currentDirection = $direction;
            $consecutiveCount = 1;
        }
    } else {
        // สำหรับแท่งแรก ไม่สามารถกำหนดทิศทางได้
        $direction = "Initial";
        $currentDirection = $direction;
        $consecutiveCount = 1;
    }
    
    // เพิ่มข้อมูลวิเคราะห์ลงในรายการ
    $newItem['adxDirection'] = $direction;
    $newItem['DirectionCon'] = $consecutiveCount;
    
    // เพิ่มลงในอาร์เรย์ผลลัพธ์
    $result[] = $newItem;
}

// แสดงผลลัพธ์เป็น JSON
echo json_encode($result, JSON_PRETTY_PRINT);

} // end function 
?>


