<?php
//searchInFile.php

function searchInFiles($folderPath, $searchWord) {
    // ตรวจสอบว่าโฟลเดอร์มีอยู่จริงหรือไม่
    if (!is_dir($folderPath)) {
        echo "ไม่พบโฟลเดอร์: " . $folderPath . "\n";
        return;
    }

    // สแกนไฟล์ทั้งหมดในโฟลเดอร์
    $files = scandir($folderPath);

    // วนลูปผ่านแต่ละไฟล์
    foreach ($files as $file) {
        // ข้ามโฟลเดอร์ปัจจุบันและโฟลเดอร์ก่อนหน้า
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $folderPath . '/' . $file;

        // ตรวจสอบว่าเป็นไฟล์หรือไม่
        if (is_file($filePath)) {
            // อ่านเนื้อหาของไฟล์
            $fileContent = file_get_contents($filePath);

            // ค้นหาคำที่ต้องการในเนื้อหา
            if (stripos($fileContent, $searchWord) !== false) {
                echo "พบคำว่า '" . $searchWord . "' ในไฟล์: " . $filePath . "\n";
                // คุณสามารถเพิ่มโค้ดเพิ่มเติมเพื่อทำอย่างอื่นเมื่อพบคำได้ เช่น แสดงบรรทัดที่พบ
            }
        }
    }
}

// กำหนดพาธของโฟลเดอร์ที่ต้องการค้นหา
$folderToSearch = './your_folder'; // เปลี่ยนเป็นพาธของโฟลเดอร์ของคุณ
$folderToSearch = ''; // เปลี่ยนเป็นพาธของโฟลเดอร์ของคุณ

// กำหนดคำที่ต้องการค้นหา
$wordToFind = '.updateChart'; // เปลี่ยนเป็นคำที่คุณต้องการค้นหา

// เรียกใช้ฟังก์ชันเพื่อค้นหา
searchInFiles($folderToSearch, $wordToFind);

?>