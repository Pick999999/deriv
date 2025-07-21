<?php
/**
 * PHP Webhook Handler Template
 * สำหรับรับและประมวลผล webhook จาก bot platforms
 */

// ป้องกัน direct access
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    http_response_code(403);
    exit('Access Denied');
}

// Log function
function writeLog($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    file_put_contents('webhook.log', $logMessage, FILE_APPEND | LOCK_EX);
}

// Error handling
function sendErrorResponse($message, $code = 400) {
    http_response_code($code);
    writeLog("ERROR: {$message}");
    echo json_encode(['error' => $message]);
    exit;
}

// Success response
function sendSuccessResponse($message = 'OK') {
    http_response_code(200);
    writeLog("SUCCESS: {$message}");
    echo json_encode(['status' => 'success', 'message' => $message]);
    exit;
}

try {
    // 1. ตรวจสอบ HTTP Method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendErrorResponse('Only POST method allowed', 405);
    }

    // 2. ตรวจสอบ Content-Type
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') === false) {
        sendErrorResponse('Content-Type must be application/json', 415);
    }

    // 3. รับข้อมูล webhook
    $input = file_get_contents('php://input');
    if (empty($input)) {
        sendErrorResponse('Empty request body');
    }

    // 4. แปลง JSON
    $webhookData = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendErrorResponse('Invalid JSON: ' . json_last_error_msg());
    }

    // 5. Log ข้อมูลที่รับมา
    writeLog("Webhook received: " . json_encode($webhookData));

    // 6. ตรวจสอบ webhook signature (ถ้ามี)
    function verifyWebhookSignature($data, $signature, $secret) {
        $expectedSignature = hash_hmac('sha256', $data, $secret);
        return hash_equals($signature, $expectedSignature);
    }

    // ตัวอย่างการตรวจสอบ signature สำหรับ LINE Bot
    /*
    $channelSecret = 'YOUR_CHANNEL_SECRET';
    $signature = $_SERVER['HTTP_X_LINE_SIGNATURE'] ?? '';
    if (!verifyWebhookSignature($input, $signature, $channelSecret)) {
        sendErrorResponse('Invalid signature', 401);
    }
    */

    // 7. ระบุประเภท webhook platform
    $platform = detectPlatform($webhookData);
    writeLog("Platform detected: {$platform}");

    // 8. ประมวลผลตาม platform
    switch ($platform) {
        case 'line':
            handleLineWebhook($webhookData);
            break;
        
        case 'telegram':
            handleTelegramWebhook($webhookData);
            break;
        
        case 'messenger':
            handleMessengerWebhook($webhookData);
            break;
        
        case 'discord':
            handleDiscordWebhook($webhookData);
            break;
        
        default:
            handleGenericWebhook($webhookData);
            break;
    }

} catch (Exception $e) {
    sendErrorResponse('Internal error: ' . $e->getMessage(), 500);
}

// ฟังก์ชันระบุ platform
function detectPlatform($data) {
    // LINE Bot
    if (isset($data['events']) && isset($data['destination'])) {
        return 'line';
    }
    
    // Telegram Bot
    if (isset($data['update_id']) && isset($data['message'])) {
        return 'telegram';
    }
    
    // Facebook Messenger
    if (isset($data['object']) && $data['object'] === 'page') {
        return 'messenger';
    }
    
    // Discord
    if (isset($data['guild_id']) || isset($data['channel_id'])) {
        return 'discord';
    }
    
    return 'generic';
}

// Handler สำหรับ LINE Bot
function handleLineWebhook($data) {
    foreach ($data['events'] as $event) {
        $eventType = $event['type'];
        $userId = $event['source']['userId'] ?? '';
        
        switch ($eventType) {
            case 'message':
                if ($event['message']['type'] === 'text') {
                    $messageText = $event['message']['text'];
                    writeLog("LINE Text: {$messageText} from {$userId}");
                    
                    // ประมวลผลข้อความ
                    processTextMessage($messageText, $userId, 'line');
                }
                break;
                
            case 'follow':
                writeLog("LINE Follow: {$userId}");
                // ประมวลผล follow event
                break;
                
            case 'unfollow':
                writeLog("LINE Unfollow: {$userId}");
                // ประมวลผล unfollow event
                break;
        }
    }
    
    sendSuccessResponse('LINE webhook processed');
}

// Handler สำหรับ Telegram Bot
function handleTelegramWebhook($data) {
    $message = $data['message'];
    $chatId = $message['chat']['id'];
    $text = $message['text'] ?? '';
    $userId = $message['from']['id'];
    
    writeLog("Telegram: {$text} from {$userId} in chat {$chatId}");
    
    // ประมวลผลข้อความ
    processTextMessage($text, $userId, 'telegram');
    
    sendSuccessResponse('Telegram webhook processed');
}

// Handler สำหรับ Facebook Messenger
function handleMessengerWebhook($data) {
    foreach ($data['entry'] as $entry) {
        foreach ($entry['messaging'] as $messaging) {
            $senderId = $messaging['sender']['id'];
            
            if (isset($messaging['message'])) {
                $text = $messaging['message']['text'] ?? '';
                writeLog("Messenger: {$text} from {$senderId}");
                
                // ประมวลผลข้อความ
                processTextMessage($text, $senderId, 'messenger');
            }
        }
    }
    
    sendSuccessResponse('Messenger webhook processed');
}

// Handler สำหรับ Discord
function handleDiscordWebhook($data) {
    $content = $data['content'] ?? '';
    $author = $data['author']['id'] ?? '';
    $channelId = $data['channel_id'] ?? '';
    
    writeLog("Discord: {$content} from {$author} in {$channelId}");
    
    // ประมวลผลข้อความ
    processTextMessage($content, $author, 'discord');
    
    sendSuccessResponse('Discord webhook processed');
}

// Handler ทั่วไป
function handleGenericWebhook($data) {
    writeLog("Generic webhook: " . json_encode($data));
    
    // ประมวลผลข้อมูลทั่วไป
    // ...
    
    sendSuccessResponse('Generic webhook processed');
}

// ฟังก์ชันประมวลผลข้อความ
function processTextMessage($text, $userId, $platform) {
    // ลบช่องว่างและแปลงเป็นพิมพ์เล็ก
    $command = strtolower(trim($text));
    
    // ตัวอย่างการประมวลผล command
    switch ($command) {
        case 'hello':
        case 'hi':
        case 'สวัสดี':
            $response = 'สวัสดีครับ! มีอะไรให้ช่วยไหม?';
            sendMessageToPlatform($response, $userId, $platform);
            break;
            
        case 'help':
        case 'ช่วยเหลือ':
            $response = "คำสั่งที่ใช้ได้:\n- hello: ทักทาย\n- help: ช่วยเหลือ\n- time: เวลาปัจจุบัน";
            sendMessageToPlatform($response, $userId, $platform);
            break;
            
        case 'time':
        case 'เวลา':
            $response = 'เวลาปัจจุบัน: ' . date('Y-m-d H:i:s');
            sendMessageToPlatform($response, $userId, $platform);
            break;
            
        default:
            // บันทึกข้อความที่ไม่รู้จัก
            writeLog("Unknown command: {$text} from {$userId}");
            
            // ตอบกลับข้อความทั่วไป (optional)
            // $response = 'ขออภัย ไม่เข้าใจคำสั่งนี้ พิมพ์ "help" เพื่อดูคำสั่งที่ใช้ได้';
            // sendMessageToPlatform($response, $userId, $platform);
            break;
    }
}

// ฟังก์ชันส่งข้อความกลับ
function sendMessageToPlatform($message, $userId, $platform) {
    switch ($platform) {
        case 'line':
            sendLineMessage($message, $userId);
            break;
            
        case 'telegram':
            sendTelegramMessage($message, $userId);
            break;
            
        case 'messenger':
            sendMessengerMessage($message, $userId);
            break;
            
        default:
            writeLog("Cannot send message to platform: {$platform}");
            break;
    }
}

// ฟังก์ชันส่งข้อความ LINE
function sendLineMessage($message, $userId) {
    $accessToken = 'YOUR_LINE_ACCESS_TOKEN';
    
    $data = [
        'to' => $userId,
        'messages' => [
            [
                'type' => 'text',
                'text' => $message
            ]
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/message/push');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    writeLog("LINE message sent to {$userId}: {$message}");
}

// ฟังก์ชันส่งข้อความ Telegram
function sendTelegramMessage($message, $chatId) {
    $botToken = 'YOUR_TELEGRAM_BOT_TOKEN';
    
    $data = [
        'chat_id' => $chatId,
        'text' => $message
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$botToken}/sendMessage");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    writeLog("Telegram message sent to {$chatId}: {$message}");
}

// ฟังก์ชันส่งข้อความ Messenger
function sendMessengerMessage($message, $userId) {
    $pageAccessToken = 'YOUR_PAGE_ACCESS_TOKEN';
    
    $data = [
        'recipient' => ['id' => $userId],
        'message' => ['text' => $message]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v18.0/me/messages?access_token={$pageAccessToken}");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    writeLog("Messenger message sent to {$userId}: {$message}");
}

// ฟังก์ชันเชื่อมต่อฐานข้อมูล (optional)
function getDatabaseConnection() {
    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=your_database;charset=utf8mb4',
            'username',
            'password',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        writeLog("Database connection failed: " . $e->getMessage());
        return null;
    }
}

// ฟังก์ชันบันทึกข้อมูลลงฐานข้อมูล (optional)
function saveMessageToDatabase($userId, $message, $platform) {
    $pdo = getDatabaseConnection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO messages (user_id, message, platform, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $message, $platform]);
        return true;
    } catch (PDOException $e) {
        writeLog("Database save failed: " . $e->getMessage());
        return false;
    }
}

?>