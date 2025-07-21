<?php
include 'candlestick_analyzer.php';

// ข้อมูลจาก Deriv.com
$candlestick_data = [
    1640995200 => ['open' => 1.1345, 'high' => 1.1367, 'low' => 1.1340, 'close' => 1.1355],
    // ... เพิ่มข้อมูลจริงของคุณ
];

$analyzer = new CandlestickAnalyzer($candlestick_data);
$results = $analyzer->analyzeTrends();
?>

<!DOCTYPE html>
<!-- วาง HTML code จาก artifact -->

<script>
// ส่งข้อมูลจาก PHP ไป JavaScript
const phpData = <?php echo json_encode($candlestick_data); ?>;
const phpResults = <?php echo json_encode($results); ?>;

// Load ข้อมูลจริงแทน sample data
document.addEventListener('DOMContentLoaded', function() {
    loadDataFromPHP(phpData);
});
</script>