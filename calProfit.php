<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ทดสอบกับข้อมูลจริงจากไฟล์ JSON

$jsonData = file_get_contents('paste.txt');
$trades = json_decode($jsonData, true);

// ค่า stake amount และ payout ratio ที่ทราบ
$knownStakeAmount = 100;
$knownPayoutRatio = 95;

// ทดสอบการคำนวณกับแต่ละรายการเทรด
foreach ($trades as $trade) {
    $calculatedProfit = calculateDerivProfitFinal(
        $trade['conType'], 
        $trade['entrySpot'], 
        $trade['currrentSpot'], 
        $knownStakeAmount, 
        $knownPayoutRatio
    );
    
    echo "จริง: {$trade['profit']}, คำนวณ: {$calculatedProfit}<br>";
}


function calculateDerivProfitFinal($conType, $entrySpot, $currentSpot, $stakeAmount, $payoutRatio) {
    // แปลง payout ratio จากเปอร์เซ็นต์เป็นทศนิยมถ้าจำเป็น
    if ($payoutRatio > 1) {
        $payoutRatio = $payoutRatio / 100;
    }
    
    // คำนวณค่าการเปลี่ยนแปลงของราคา (price difference)
    $priceDiff = $currentSpot - $entrySpot;
    
    // คำนวณ multiplier จาก stake amount และ payout ratio
    $multiplier = 5.85; // ค่าจากการวิเคราะห์ข้อมูล
    
    // ปรับ multiplier ตาม stake amount และ payout ratio
    $adjustedMultiplier = $multiplier * ($stakeAmount / 100) * ($payoutRatio / 0.95);
    
    // คำนวณ profit ตามทิศทางของการเทรด
    if ($conType === 'CALL') {
        // สำหรับ CALL (Rise)
        return $priceDiff * $adjustedMultiplier;
    } else {
        // สำหรับ PUT (Fall)
        return -$priceDiff * $adjustedMultiplier;
    }
}
?>