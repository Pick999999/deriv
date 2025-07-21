<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$start = microtime(true);
file_put_contents("log.txt", "[".date('H:i:s')."] Function A START\n", FILE_APPEND);

sleep(3); // จำลองงานหนัก 3 วินาที

$end = microtime(true);
file_put_contents("log.txt", "[".date('H:i:s')."] Function A END (".round($end - $start, 2)."s)\n", FILE_APPEND);

echo json_encode([
    'from' => 'A',
    'took' => round($end - $start, 2),
    'received' => $data
]);
