<?php
// การเชื่อมต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'thepaper_lab';
$username = 'thepaper_lab'; 
$password = 'maithong';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ตรวจสอบว่ามีการร้องขอรูปภาพเฉพาะหรือไม่
if (isset($_GET['image_id'])) {
    $imageId = $_GET['image_id'];
    
    // ดึงข้อมูลรูปภาพจากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT imageData FROM CandleImageGraph WHERE id = ?");
    $stmt->execute([$imageId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && $result['imageData']) {
        // กำหนด Content-Type header (สมมุติว่าเป็น PNG, คุณอาจต้องเก็บ MIME type ไว้ในฐานข้อมูลด้วย)
        header('Content-Type: image/png');
        echo $result['imageData'];
        exit;
    } else {
        // ถ้าไม่พบรูปภาพ
        header('HTTP/1.0 404 Not Found');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แสดงรูปภาพ Candle Graph</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .image-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #fafafa;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .image-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .image-card img {
            width: 100%;
            height: auto;
            border-radius: 4px;
            cursor: pointer;
        }
        .image-info {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        .image-info strong {
            color: #333;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
        }
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
        }
        .modal-content img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            color: white;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001;
        }
        .no-images {
            text-align: center;
            color: #666;
            font-size: 18px;
            margin: 40px 0;
        }
        .filter-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .filter-section select, .filter-section input {
            margin: 5px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>รูปภาพ Candle Graph</h1>
        
        <!-- ส่วนกรองข้อมูล -->
        <div class="filter-section">
            <form method="GET">
                <label>Asset Name:</label>
                <select name="asset_filter">
                    <option value="">ทั้งหมด</option>
                    <?php
                    // ดึงรายการ Asset Name ที่ไม่ซ้ำกัน
                    $assetStmt = $pdo->query("SELECT DISTINCT assetName FROM CandleImageGraph ORDER BY assetName");
                    while ($asset = $assetStmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = (isset($_GET['asset_filter']) && $_GET['asset_filter'] === $asset['assetName']) ? 'selected' : '';
                        echo "<option value='{$asset['assetName']}' $selected>{$asset['assetName']}</option>";
                    }
                    ?>
                </select>
                
                <label>Session No:</label>
                <input type="number" name="session_filter" value="<?= isset($_GET['session_filter']) ? htmlspecialchars($_GET['session_filter']) : '' ?>" placeholder="เลข Session">
                
                <button type="submit">กรอง</button>
                <a href="?" style="margin-left: 10px; text-decoration: none; color: #666;">ล้างการกรอง</a>
            </form>
        </div>

        <?php
        // สร้าง SQL query พร้อมเงื่อนไขการกรอง
        $sql = "SELECT id, sessionNo, assetName, timeCandle, created_at FROM CandleImageGraph WHERE 1=1";
        $params = [];

        if (isset($_GET['asset_filter']) && !empty($_GET['asset_filter'])) {
            $sql .= " AND assetName = ?";
            $params[] = $_GET['asset_filter'];
        }

        if (isset($_GET['session_filter']) && !empty($_GET['session_filter'])) {
            $sql .= " AND sessionNo = ?";
            $params[] = $_GET['session_filter'];
        }

        $sql .= " ORDER BY created_at DESC";

        // ดึงข้อมูลรูปภาพทั้งหมด
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($images) > 0) {
            echo '<div class="image-grid">';
            foreach ($images as $image) {
                echo '<div class="image-card">';
                echo '<img src="?image_id=' . $image['id'] . '" alt="Candle Graph" onclick="openModal(this.src)">';
                echo '<div class="image-info">';
                echo '<strong>Asset:</strong> ' . htmlspecialchars($image['assetName']) . '<br>';
                echo '<strong>Session:</strong> ' . htmlspecialchars($image['sessionNo']) . '<br>';
                echo '<strong>Time:</strong> ' . htmlspecialchars($image['timeCandle']) . '<br>';
                echo '<strong>Created:</strong> ' . htmlspecialchars($image['created_at']);
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="no-images">ไม่พบรูปภาพในฐานข้อมูล</div>';
        }
        ?>
    </div>

    <!-- Modal สำหรับแสดงรูปภาพขนาดใหญ่ -->
    <div id="imageModal" class="modal" onclick="closeModal()">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img id="modalImage" src="" alt="Full Size Image">
        </div>
    </div>

    <script>
        function openModal(src) {
            document.getElementById('imageModal').style.display = 'block';
            document.getElementById('modalImage').src = src;
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // ปิด modal เมื่อกด ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>