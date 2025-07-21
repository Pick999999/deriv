<?php
 ob_start();
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 //Test(); return ;
 ?>
<script>
// ฟังก์ชันส่ง AJAX แบบ POST
function sendAjaxPost(url, data, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('Accept', 'application/json');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    callback(null, response);
                } catch (e) {
                    callback('JSON Parse Error: ' + e.message, null);
                }
            } else {
                callback('HTTP Error: ' + xhr.status, null);
            }
        }
    };
    
    xhr.send(JSON.stringify(data));
}



function testajax() {

   // ข้อมูล JSON ที่ต้องการส่ง
		var jsonData = {
			"name": "John",
			"age": 30,
			"city": "Bangkok"
		};

		var xhr = new XMLHttpRequest();
		xhr.open('POST', 'https://thepapers.in/deriv/testajax.php', true);

		// ตั้งค่า Headers สำคัญมาก!
		xhr.setRequestHeader('Content-Type', 'application/json');
		xhr.setRequestHeader('Accept', 'application/json');

		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				//alert(xhr.status);
				if (xhr.status === 200) {
					//var response = JSON.parse(xhr.responseText);
					var response = (xhr.responseText);
					console.log('Success:', response);
					alert(response);
					document.getElementById("result").innerHTML = response;
					
					// ใช้ response ต่อไป
				} else {
					console.log('Error:', xhr.status, xhr.statusText);
				}
			}
		};

		// แปลง object เป็น JSON string และส่ง
		xhr.send(JSON.stringify(jsonData));
}
</script>

<?php
 Main(); return ;

 function Test() { 
	 
	 
	 require_once("newutil2.php"); 	 
	 $pdo = getPDONew();
	 $sql = 'select * from chart_images'; 
	 $params = array();	 
	 
	 
	 $rs= pdogetMultiValue2($sql,$params,$pdo) ;	 
	 while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		 echo "<img src='" . htmlspecialchars($row['image_data']) . "'><hr>" ;
		 
			    
	 }
	 
	 


	
 
	 
 
 
 } // end function
 


function Main() { 






// การตั้งค่าการเชื่อมต่อฐานข้อมูลด้วย PDO
$dsn = 'mysql:host=localhost;dbname=thepaper_lab;charset=utf8mb4';
$username = 'thepaper_lab';
$password = 'maithong';

try {
    // สร้างการเชื่อมต่อ PDO
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // คำสั่ง SQL เพื่อดึงข้อมูลทั้งหมดจากตาราง chart_images
    $sql = "SELECT asset, image_data, price_info, timestamp, last_update, file_path, file_size, created_at, updated_at FROM chart_images";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    // เริ่มต้นแสดงผลใน HTML
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chart Images</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            img {
                max-width: 200px;
                height: auto;
            }
        </style>
    </head>
    <body>
        <h2>Chart Images</h2>
		<button type='button' id='' class='mBtn' onclick="testajax()">TestAjax</button>
		<div id="result" class="bordergray flex" style='border:1px solid red;padding:10px'>
		     
		</div>
        <table>
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Image</th>
                    <th>Price Info</th>
                    <th>Timestamp</th>
                    <th>Last Update</th>
                    <th>File Path</th>
                    <th>File Size</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // วนลูปแสดงข้อมูลแต่ละแถว
                foreach ($rows as $row) {
                    // ดึงข้อมูล base64 จาก image_data
                    $imageData = $row['image_data'];

                    // ตรวจสอบว่ามี prefix หรือไม่
                    if (strpos($imageData, 'data:image/png;base64,') === 0) {
                        // ถ้ามี prefix อยู่แล้ว ใช้ข้อมูลได้เลย
                        $imageSrc = $imageData;
						
						//echo "Case A";
                    } else {
                        // ถ้าไม่มี prefix เพิ่ม prefix เข้าไป
						//echo "Case B";
                        $base64Image = $imageData;
                        // ตรวจสอบความถูกต้องของ base64
                        if (base64_encode(base64_decode($base64Image, true)) === $base64Image) {
                            $imageSrc = "data:image/png;base64," . $base64Image;
                        } else {
                            $imageSrc = "";
                            echo "<tr><td colspan='9'>Invalid base64 image data for asset: " . htmlspecialchars($row['asset']) . "</td></tr>";
                            continue;
                        }
                    }
					//$imgSrc = $row['file_path'];
					$imgSrc = htmlspecialchars($row['image_data']);

                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['asset']); ?></td>
                        <td><img src="<?=$imgSrc;?>" alt="Chart Image"></td>
                        <td><?php echo htmlspecialchars($row['price_info'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_update'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['file_path'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['file_size'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


} // end function Main


?>



