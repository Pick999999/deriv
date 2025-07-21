<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$start = microtime(true);
file_put_contents("log.txt", "[".date('H:i:s')."] Function B START\n", FILE_APPEND);

sleep(1); // เร็วกว่า A

$end = microtime(true);
file_put_contents("log.txt", "[".date('H:i:s')."] Function B END (".round($end - $start, 2)."s)\n", FILE_APPEND);

echo json_encode([
    'from' => 'B',
    'took' => round($end - $start, 2),
    'received' => $data
]);
