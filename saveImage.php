<?php
/*
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
*/

// วิธีง่ายๆ - อนุญาตทุก origin (ไม่แนะนำใน production)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Only POST method allowed', null, 405);
    }

    // Get JSON input
    $json_input = file_get_contents('php://input');
    if (empty($json_input)) {
        sendResponse(false, 'No data received', null, 400);
    }

    // Decode JSON
    $data = json_decode($json_input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendResponse(false, 'Invalid JSON format: ' . json_last_error_msg(), null, 400);
    }

    // Validate required fields
    $required_fields = ['asset', 'imageData', 'timestamp'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            sendResponse(false, "Missing required field: {$field}", null, 400);
        }
    }

    // Validate and decode base64 image
    $image_base64 = $data['image_data'];
    $image_binary = base64_decode($image_base64, true);
    
    if ($image_binary === false) {
        sendResponse(false, 'Invalid base64 image data', null, 400);
    }

    // Validate image size (max 10MB)
    $max_size = 10 * 1024 * 1024; // 10MB
    if (strlen($image_binary) > $max_size) {
        sendResponse(false, 'Image size too large (max 10MB)', null, 400);
    }

    // Validate PNG format
    $image_info = getimagesizefromstring($image_binary);
    if ($image_info === false || $image_info['mime'] !== 'image/png') {
        sendResponse(false, 'Only PNG images are allowed', null, 400);
    }

    // Create database connection
    $mysqli = getDBConn();
    
    // Get session number
    $sessionNo = getNewSessionNo($mysqli);

    // Prepare data for insertion
    $asset_name = $mysqli->real_escape_string($data['symbol']);
    $time_candle = date('Y-m-d H:i:s', strtotime($data['timestamp']));
    
    // Check if record already exists (prevent duplicates)
    $check_sql = "SELECT id FROM CandleImageGraph WHERE assetName = ? AND timeCandle = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    
    if (!$check_stmt) {
        logMessage("Prepare check statement failed: " . $mysqli->error, 'ERROR');
        sendResponse(false, 'Database prepare error', null, 500);
    }

    $check_stmt->bind_param('ss', $asset_name, $time_candle);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing record
        $existing_row = $check_result->fetch_assoc();
        $update_sql = "UPDATE CandleImageGraph SET imageData = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $update_stmt = $mysqli->prepare($update_sql);
        
        if (!$update_stmt) {
            logMessage("Prepare update statement failed: " . $mysqli->error, 'ERROR');
            sendResponse(false, 'Database prepare error', null, 500);
        }

        $update_stmt->bind_param('bi', $image_binary, $existing_row['id']);
        $update_stmt->send_long_data(0, $image_binary);
        
        if ($update_stmt->execute()) {
            logMessage("Updated chart for {$asset_name} at {$time_candle}", 'INFO');
            sendResponse(true, 'Chart updated successfully', [
                'id' => $existing_row['id'],
                'action' => 'updated',
                'asset_name' => $asset_name,
                'time_candle' => $time_candle,
                'image_size' => strlen($image_binary)
            ]);
        } else {
            logMessage("Update failed: " . $update_stmt->error, 'ERROR');
            sendResponse(false, 'Failed to update chart', null, 500);
        }
    } else {
        // Insert new record
        $insert_sql = "INSERT INTO CandleImageGraph (sessionNo, assetName, timeCandle, imageData) VALUES (?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_sql);
        
        if (!$insert_stmt) {
            logMessage("Prepare insert statement failed: " . $mysqli->error, 'ERROR');
            sendResponse(false, 'Database prepare error', null, 500);
        }

        $insert_stmt->bind_param('issb', $sessionNo, $asset_name, $time_candle, $image_binary);
        $insert_stmt->send_long_data(3, $image_binary);
        
        if ($insert_stmt->execute()) {
            $insert_id = $mysqli->insert_id;
            logMessage("Inserted new chart for {$asset_name} at {$time_candle}, ID: {$insert_id}", 'INFO');
            sendResponse(true, 'Chart saved successfully', [
                'id' => $insert_id,
                'action' => 'inserted',
                'asset_name' => $asset_name,
                'time_candle' => $time_candle,
                'image_size' => strlen($image_binary)
            ]);
        } else {
            logMessage("Insert failed: " . $insert_stmt->error, 'ERROR');
            sendResponse(false, 'Failed to save chart', null, 500);
        }
    }

} catch (Exception $e) {
    logMessage("Exception: " . $e->getMessage(), 'ERROR');
    sendResponse(false, 'Internal server error', null, 500);
} finally {
    // Close database connection
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $mysqli->close();
    }
}

// Fixed: Pass mysqli connection to avoid circular dependency
function getNewSessionNo($mysqli) { 
    $datestamp = date('Y-m-d');
    $sql = 'SELECT COALESCE(MAX(sessionNo), 0) + 1 FROM CandleImageGraph WHERE DATE(timeCandle) = ?';
    
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        logMessage("Prepare session number query failed: " . $mysqli->error, 'ERROR');
        return 1; // Default to 1 if query fails
    }
    
    $stmt->bind_param('s', $datestamp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return (int)$row['COALESCE(MAX(sessionNo), 0) + 1'];
    }
    
    return 1; // Default to 1 if no results
}

// Response function
function sendResponse($success, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

// Log function
function logMessage($message, $type = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logFile = 'saveImage.log';
    $logEntry = "[{$timestamp}] [{$type}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Fixed: Remove circular dependency
function getDBConn() { 
    // Database configuration
    $db_config = [
        'host' => 'localhost',
        'username' => 'thepaper_lab',
        'password' => 'maithong',
        'database' => 'thepaper_lab'
    ];

    $mysqli = new mysqli(
        $db_config['host'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );

    // Check connection
    if ($mysqli->connect_error) {
        logMessage("Database connection failed: " . $mysqli->connect_error, 'ERROR');
        sendResponse(false, 'Database connection failed', null, 500);
    }

    // Set charset
    $mysqli->set_charset('utf8mb4');
    
    return $mysqli;
}

?>