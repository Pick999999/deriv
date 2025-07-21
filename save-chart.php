<?php
// PHP script to save the chart image (save-chart.php)
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json');

// Set error handling
ini_set('display_errors', 0);
error_reporting(E_ALL);

//$data = json_decode(file_get_contents('php://input'), true); 


try {
    // Check if the request contains image data
    if (!isset($_POST['image'])) {
        throw new Exception('No image data received');
    }
    
    // Get the image data
    $imageData = $_POST['image'];
    
    // Remove the data URL prefix to get just the base64 string
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    
    // Decode the base64 data
    $decodedImage = base64_decode($imageData);
    
    if ($decodedImage === false) {
        throw new Exception('Failed to decode image data');
    }
    
    // Generate a unique filename
    $timestamp = time();
    $filename = 'chart_' . $timestamp . '.png';
    $uploadDir = 'charts/'; // Make sure this directory exists and is writable
    
    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Save the image file
    $result = file_put_contents($uploadDir . $filename, $decodedImage);
    
    if ($result === false) {
        throw new Exception('Failed to save image file');
    }
    
    // Log additional metadata if provided
    $chartType = isset($_POST['chartType']) ? $_POST['chartType'] : 'unknown';
    $timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : date('Y-m-d H:i:s');
    
    // Optional: Save metadata to a database
    // saveToDatabase($filename, $chartType, $timestamp);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Chart saved successfully',
        'filename' => $filename,
        'url' => 'https://thepapers.in/' . $uploadDir . $filename
    ]);
    
} catch (Exception $e) {
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Example function to save metadata to a database
 * Uncomment and customize as needed
 */
/*
function saveToDatabase($filename, $chartType, $timestamp) {
    $db = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
    $stmt = $db->prepare('INSERT INTO chart_images (filename, chart_type, created_at) VALUES (?, ?, ?)');
    $stmt->execute([$filename, $chartType, $timestamp]);
}
*/
?>


