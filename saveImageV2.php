<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once('newutil2.php') ;
// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
} 

Main(); 
return;
    
 

 
// Save image to file system
function saveImageToFile($uploadDir,$imageData, $asset) {

	//return ;
    
    
    // Remove data:image/png;base64, prefix
    $imageData = preg_replace('#^data:image/[^;]*;base64,#', '', $imageData);
    $imageData = base64_decode($imageData);
    
    if ($imageData === false) {
        throw new Exception('Invalid image data');
    }
    
    // Generate filename
    $filename = $asset . '_' .  uniqid() . '.png';
    $filepath = $uploadDir . $filename;
    
    // Save file
    $result = file_put_contents($filepath, $imageData);
    
    if ($result === false) {
        throw new Exception('Failed to save image file');
    }
    
    return [
        'filepath' => $filepath,
        'filename' => $filename,
        'size' => $result,
		'imageData' => $imageData
    ];
}

// Insert chart data
function insertChartData($pdo, $data) {
	
    $sql = "
      REPLACE INTO chart_images (
        asset, 
        image_data, 
        price_info, 
        timestamp, 
        last_update, 
        file_path, 
        file_size
     ) VALUES (?,?,?,?,?,?,?)";
    
	
	$params = array(
	$data['asset'],
	$data['image_data'],$data['price_info'],
    $data['timestamp'],$data['last_update'],
    $data['file_path'],$data['file_size']
	);
	
	//print_r($data);
	if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
	   echo 'Error' ;
	   return false;
	} else {
       echo 'Success' ;
	   return true;
	}
	
	
 
}


 
 



function Main() {  

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
	/*
    $uploadDir = 'uploads/charts/';     
    deleteAllFilesInFolder($uploadDir);
	*/

    $uploadDir = 'uploads/charts/' . $data['asset'] .'/' ;    
    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
	// ตัวอย่างการใช้งาน
    try {
       //$folderPath = __DIR__ . '/uploads'; // เปลี่ยนเป็นพาทโฟลเดอร์ของคุณ

       deleteAllFilesInFolder($uploadDir);
       echo "ลบไฟล์ทั้งหมดใน $uploadDir เรียบร้อยแล้ว!";
    } catch (Exception $e) {
       echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }



// Main processing
try {
    // Get JSON input
    
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $required = ['asset', 'imageData', 'timestamp'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate asset
    $validAssets = ['R_10', 'R_25', 'R_50', 'R_75', 'R_100'];
    if (!in_array($data['asset'], $validAssets)) {
        throw new Exception('Invalid asset');
    }
    
    // Connect to database
    //$pdo = createConnection($config);
	$pdo= getPDONew() ;
	//$dataImage = $data['imageData'];
	
	
	 
    // Save image to file
    $fileInfo = saveImageToFile($uploadDir,$data['imageData'], $data['asset']);
    $imageData = $fileInfo['imageData'] ;
    // Prepare data for database
    $dbData = [
        'asset' => $data['asset'],
        'image_data' => $data['imageData'], // Store base64 as backup
        'price_info' => $data['price'] ?? null,
        'timestamp' => date('Y-m-d H:i:s', strtotime($data['timestamp'])),
        'last_update' => $data['lastUpdate'] ?? date('H:i:s'),
        'file_path' => $fileInfo['filepath'],
        'file_size' => 0
    ];
    
    
    try {
        // Insert data
        $insertResult = insertChartData($pdo, $dbData);
        
        if (!$insertResult) {
            throw new Exception('Failed to insert chart data');
        }
        
        // Success response
        $response = [
            'success' => true,
            'message' => 'Chart image saved successfully',
            'data' => [                
                'asset' => $data['asset'],                
                'timestamp' => $data['timestamp'],
                
                
            ]
        ];
        
        http_response_code(200);
        echo json_encode($response, JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    // Error response
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code(500);
    echo json_encode($response, JSON_PRETTY_PRINT);
    
    // Log error (optional)
    error_log("Chart Image Save Error: " . $e->getMessage());
}



} // end function



/*

ฉันรับ ข้อมูล จาก ajax มาเป็น imageData : "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAs4AAAH....
ซึ่งเมื่อ ทำการประมวลผลด้วย php ดังนี้
// Remove data:image/png;base64, prefix
$imageDataA = preg_replace('#^data:image/[^;]*;base64,#', '', $imageData);
$imageDataC = base64_decode($imageDataA);
แล้วนำ imageDataC ไป บันทึกเป็น ไฟล์ได้ถูกต้อง 
แต่เมื่อนำ imageData ไป บันทึกลง mysql ที่มี type เป็น blob 
และดึงมาใช้ โดย เก็บ ไว้ในตัวแปร imageD และ แสดงผลโดย <img src="<?=$imageD?>"> 
ภาพที่แสดงเป็น สีดำมืด ซึ่งไม่ถูกต้อง จะแก้ปัญหาอย่างไร
*/
?>