<?php
// ต้องติดตั้ง library ด้วย: composer require textalk/websocket
require __DIR__ . '/vendor/autoload.php';
use WebSocket\Client;

// กำหนดค่าการเชื่อมต่อ
$app_id = '66726'; // ต้องสมัคร API ที่ https://developers.deriv.com/
$api_token = 'lt5UMO6bNvmZQaR'; // ต้องสร้าง token จาก account settings บน Deriv.com

// เชื่อมต่อกับ Deriv API
$client = new Client('wss://ws.binaryws.com/websockets/v3?app_id=' . $app_id);

// ทำการล็อกอิน
$authorizeRequest = json_encode([
    'authorize' => $api_token
]);
$client->send($authorizeRequest);
$authorizeResponse = json_decode($client->receive(), true);

// ตรวจสอบการล็อกอิน
if (isset($authorizeResponse['error'])) {
    die('เกิดข้อผิดพลาดในการล็อกอิน: ' . $authorizeResponse['error']['message']);
}

echo "ล็อกอินสำเร็จ! Balance: " . $authorizeResponse['authorize']['balance'] . " " . $authorizeResponse['authorize']['currency'] . "<br>";

Main($client) ;

// ฟังก์ชันสำหรับส่งคำสั่งซื้อ Rise
function buyRiseFallContract($client, $symbol, $duration, $duration_unit, $amount,$contractType) {
    // ขอราคา
    $request = json_encode([
        'proposal' => 1,
        'amount' => $amount,
        'basis' => 'stake',
        'contract_type' => $contractType, // CALL = Rise
        'currency' => 'USD',
        'duration' => $duration,
        'duration_unit' => $duration_unit, // เช่น 'm' สำหรับนาที, 't' สำหรับ ticks
        'symbol' => $symbol // เช่น 'R_100' สำหรับ Volatility 100 Index
    ]);
    
    $client->send($request);
    $response = json_decode($client->receive(), true);
    
    if (isset($response['error'])) {
        echo "เกิดข้อผิดพลาดในการขอราคา: " . $response['error']['message'] . "<br>";
        return false;
    }
    
    // เก็บ ID ของใบเสนอราคา
    $proposal_id = $response['proposal']['id'];
    
    // ส่งคำสั่งซื้อ
    $buyRequest = json_encode([
        'buy' => $proposal_id,
        'price' => $amount
    ]);
    
    $client->send($buyRequest);
    $buyResponse = json_decode($client->receive(), true);
    
    if (isset($buyResponse['error'])) {
        echo "เกิดข้อผิดพลาดในการซื้อ: " . $buyResponse['error']['message'] . "<br>";
        return false;
    }
    
    return $buyResponse;
}
/*
// ฟังก์ชันสำหรับส่งคำสั่งซื้อ Rise
function buyRiseContract($client, $symbol, $duration, $duration_unit, $amount) {
    // ขอราคา
    $request = json_encode([
        'proposal' => 1,
        'amount' => $amount,
        'basis' => 'stake',
        'contract_type' => 'CALL', // CALL = Rise
        'currency' => 'USD',
        'duration' => $duration,
        'duration_unit' => $duration_unit, // เช่น 'm' สำหรับนาที, 't' สำหรับ ticks
        'symbol' => $symbol // เช่น 'R_100' สำหรับ Volatility 100 Index
    ]);
    
    $client->send($request);
    $response = json_decode($client->receive(), true);
    
    if (isset($response['error'])) {
        echo "เกิดข้อผิดพลาดในการขอราคา: " . $response['error']['message'] . "<br>";
        return false;
    }
    
    // เก็บ ID ของใบเสนอราคา
    $proposal_id = $response['proposal']['id'];
    
    // ส่งคำสั่งซื้อ
    $buyRequest = json_encode([
        'buy' => $proposal_id,
        'price' => $amount
    ]);
    
    $client->send($buyRequest);
    $buyResponse = json_decode($client->receive(), true);
    
    if (isset($buyResponse['error'])) {
        echo "เกิดข้อผิดพลาดในการซื้อ: " . $buyResponse['error']['message'] . "<br>";
        return false;
    }
    
    return $buyResponse;
}

// ฟังก์ชันสำหรับส่งคำสั่งซื้อ Fall
function buyFallContract($client, $symbol, $duration, $duration_unit, $amount) {
    // ใช้ contract_type เป็น 'PUT' สำหรับ Fall
    $request = json_encode([
        'proposal' => 1,
        'amount' => $amount,
        'basis' => 'stake',
        'contract_type' => 'PUT', // PUT = Fall
        'currency' => 'USD',
        'duration' => $duration,
        'duration_unit' => $duration_unit,
        'symbol' => $symbol
    ]);
    
    $client->send($request);
    $response = json_decode($client->receive(), true);
    
    if (isset($response['error'])) {
        echo "เกิดข้อผิดพลาดในการขอราคา: " . $response['error']['message'] . "<br>";
        return false;
    }
    
    // เก็บ ID ของใบเสนอราคา
    $proposal_id = $response['proposal']['id'];
    
    // ส่งคำสั่งซื้อ
    $buyRequest = json_encode([
        'buy' => $proposal_id,
        'price' => $amount
    ]);
    
    $client->send($buyRequest);
    $buyResponse = json_decode($client->receive(), true);
    
    if (isset($buyResponse['error'])) {
        echo "เกิดข้อผิดพลาดในการซื้อ: " . $buyResponse['error']['message'] . "<br>";
        return false;
    }
    
    return $buyResponse;
}
*/
// ฟังก์ชันสำหรับติดตามสถานะออเดอร์
function trackContract($client, $contract_id) {
    $request = json_encode([
        'proposal_open_contract' => 1,
        'contract_id' => $contract_id,
        'subscribe' => 1 // ใช้ subscribe เพื่อให้รับข้อมูลแบบ real-time
    ]);
    
    $client->send($request);
    
    // วนลูปเพื่อแสดงผลข้อมูลออเดอร์ตลอดเวลา
    while (true) {
        try {
            $response = json_decode($client->receive(), true);
            
            if (isset($response['error'])) {
                echo "เกิดข้อผิดพลาด: " . $response['error']['message'] . "<br>";
                break;
            }
            
            if (isset($response['proposal_open_contract'])) {
                $contract = $response['proposal_open_contract'];
                
                // แสดงข้อมูลสำคัญของออเดอร์
                echo "Contract ID: " . $contract['contract_id'] . "<br>";
                echo "Symbol: " . $contract['display_name'] . "<br>";
                echo "Buy price: " . $contract['buy_price'] . "<br>";
                echo "Current spot: " . $contract['current_spot'] . "<br>";
                echo "Entry spot: " . $contract['entry_spot'] . "<br>";
                echo "Current value: " . $contract['bid_price'] . "<br>";
                echo "Profit/Loss: " . ($contract['bid_price'] - $contract['buy_price']) . "<br>";
                
                // ถ้าออเดอร์จบแล้ว ให้หยุดการติดตาม
                if ($contract['status'] === 'sold') {
                    echo "Contract ended. Result: " . ($contract['profit'] >= 0 ? "WIN" : "LOSS") . "<br>";
                    echo "Profit/Loss: " . $contract['profit'] . "<br>";
                    break;
                }
                
                // รอ 1 วินาทีก่อนแสดงข้อมูลใหม่
                sleep(1);
                echo "<br>-------------------------<br>";
            }
        } catch (Exception $e) {
            echo "เกิดข้อผิดพลาดในการรับข้อมูล: " . $e->getMessage() . "<br>";
            break;
        }
    }
}


function trackContractAndSell($client, $contract_id) {
    $request = json_encode([
        'proposal_open_contract' => 1,
        'contract_id' => $contract_id,
        'subscribe' => 1 // ใช้ subscribe เพื่อให้รับข้อมูลแบบ real-time
    ]);
    
    $client->send($request);
    $loopno = 0 ;
    // วนลูปเพื่อติดตามข้อมูลออเดอร์ตลอดเวลา
    while (true) {
        try {
            $response = json_decode($client->receive(), true);
            
            if (isset($response['error'])) {
                echo "เกิดข้อผิดพลาด: " . $response['error']['message'] . "<br>";
                break;
            }
            
            if (isset($response['proposal_open_contract'])) {
                $contract = $response['proposal_open_contract'];
				$loopno++ ;
                
                // แสดงข้อมูลสำคัญของออเดอร์
				echo "รอบที่ : " . $loopno . "<br>";
                echo "Contract ID: " . $contract['contract_id'] . "<br>";
                echo "Symbol: " . $contract['display_name'] . "<br>";
                echo "Buy price: " . $contract['buy_price'] . "<br>";
                echo "Current spot: " . $contract['current_spot'] . "<br>";
                echo "Entry spot: " . $contract['entry_spot'] . "<br>";
                echo "Current value: " . $contract['bid_price'] . "<br>";
                
                // คำนวณกำไร/ขาดทุน
                $profit = $contract['bid_price'] - $contract['buy_price'];
                $profit_percentage = ($profit / $contract['buy_price']) * 100;
                
                echo "Profit/Loss: " . $profit . " (" . number_format($profit_percentage, 2) . "%)<br>";
                
                // ตรวจสอบเงื่อนไขการขาย - ถ้ากำไรมากกว่าหรือเท่ากับ 50%
                if ($profit_percentage >= 50) {
                    echo "กำไรถึง 50% ของเงินลงทุนแล้ว! ทำการขาย Contract อัตโนมัติ<br>";
                    
                    // ส่งคำสั่งขาย Contract
                    $sell_request = json_encode([
                        'sell' => $contract_id,
                        'price' => $contract['bid_price']
                    ]);
                    
                    $client->send($sell_request);
                    
                    // รอรับผลการขาย
                    $sell_response = json_decode($client->receive(), true);
                    
                    if (isset($sell_response['error'])) {
                        echo "เกิดข้อผิดพลาดในการขาย: " . $sell_response['error']['message'] . "<br>";
                    } else if (isset($sell_response['sell'])) {
                        echo "ขาย Contract สำเร็จ!<br>";
                        echo "ขายที่ราคา: " . $sell_response['sell']['sold_for'] . "<br>";
                        echo "กำไร: " . $sell_response['sell']['profit'] . "<br>";
                        break;
                    }
                }
                
                // ถ้าออเดอร์จบแล้ว ให้หยุดการติดตาม
                if ($contract['status'] === 'sold') {
                    echo "Contract ended. Result: " . ($contract['profit'] >= 0 ? "WIN" : "LOSS") . "<br>";
                    echo "Profit/Loss: " . $contract['profit'] . "<br>";
                    break;
                }
                
                // รอ 1 วินาทีก่อนแสดงข้อมูลใหม่
                sleep(1);
                echo "<br>-------------------------<br>";
            }
        } catch (Exception $e) {
            echo "เกิดข้อผิดพลาดในการรับข้อมูล: " . $e->getMessage() . "<br>";
            break;
        }
    }
} // end trackContractAndSell()


function Main($client) {
	// ตัวอย่างการใช้งาน
	try {
		// ซื้อ Rise contract
		$symbol = 'R_100'; // Volatility 100 Index
		$symbol =  $_GET["symbol"];

		$duration = 55;
		$duration_unit = 's'; // minutes
		$amount = 10; // USD
		
		echo "กำลังส่งคำสั่งซื้อ Rise...<br>";
		/*
		$buyResponse = buyRiseContract($client, $symbol, $duration, $duration_unit, $amount);
		*/
        $contractType = 'CALL';
		$contractType = $_GET["action"];

		$buyResponse =buyRiseFallContract($client, $symbol, $duration, $duration_unit, $amount,$contractType) ;
		
		if ($buyResponse) {
			echo "ซื้อสำเร็จ!<br>";
			echo "Contract ID: " . $buyResponse['buy']['contract_id'] . "<br>";
			echo "Price: " . $buyResponse['buy']['buy_price'] . "<br>";
			echo "กำลังติดตามสถานะออเดอร์...<br>";
			
			// ติดตามสถานะออเดอร์
			trackContractAndSell($client, $buyResponse['buy']['contract_id']);
		}
		
		
		
	} catch (Exception $e) {
		echo "เกิดข้อผิดพลาด: " . $e->getMessage() . "<br>";
	} finally {
		// ปิดการเชื่อมต่อ
		$client->close();
		echo "ปิดการเชื่อมต่อ<br>";
	}

} // end func

?>