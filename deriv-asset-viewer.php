<!-- 
ขอฟังก์ชั่น pure javascript สำหรับ ดึงข้อมูล asset ทุกรายการ จาก deriv.com โดยให้แยกเป็นหมวดหมู่ และสร้างปุ่ม button แยกตามชื่อหมวดหมู่ และเมื่อคลิก button ก็ให้ ดึงข้อมูล asset ออกมาพร้อมทั้ง สถานะว่า เปิด/ปิด และเมื่อ คลิกที่ ปุ่ม เปิด ก็ให้ ทำการ load ข้อมูล candle มาวิเคราะห์ ว่า trend เป็นสถานะอะไร เช่น  sideway,weak,strong,very strong,extremly stong โดยใช้ indicator adx พร้อมทั้งแสดงผลของ  graph candlestick+ ema3+ema5 ใน ตารางนั้นๆ ด้วย  https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv.com Asset Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .loading {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .category-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .category-btn {
            padding: 10px 15px;
            background-color: #ff444f;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .category-btn:hover {
            background-color: #e63946;
        }
        .category-btn.active {
            background-color: #0a0e1a;
        }
        .asset-table {
            width: 100%;
            border-collapse: collapse;
        }
        .asset-table th, .asset-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .asset-table th {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        .asset-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-open {
            background-color: #c8e6c9;
            color: #2e7d32;
        }
        .status-closed {
            background-color: #ffcdd2;
            color: #c62828;
        }
        .error-message {
            color: #c62828;
            padding: 20px;
            border: 1px solid #ffcdd2;
            background-color: #ffebee;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Deriv.com Asset Viewer</h1>
    <div id="loading" class="loading">กำลังโหลดข้อมูล Asset...</div>
    <div id="errorContainer"></div>
    <div id="categoryButtons" class="category-buttons"></div>
    <div id="assetContainer">
        <table id="assetTable" class="asset-table">
            <thead>
                <tr>
                    <th>ชื่อ Asset</th>
                    <th>รหัส</th>
                    <th>หมวดหมู่</th>
                    <th>ประเภท</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody id="assetTableBody"></tbody>
        </table>
    </div>

    <script>
        // หมวดหมู่ที่เราจะแสดง
        let categories = {};
        let activeCategory = null;
        let allAssets = [];

        // ฟังก์ชั่นดึงข้อมูล Asset จาก Deriv API
        async function fetchDerivAssets() {
            try {
                // สร้าง WebSocket connection ไปยัง Deriv API
                const ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
                
                return new Promise((resolve, reject) => {
                    ws.onopen = function() {
                        // เมื่อเชื่อมต่อสำเร็จ ส่งคำขอดึงข้อมูล active symbols
                        ws.send(JSON.stringify({
                            active_symbols: 'brief',
                            product_type: 'basic'
                        }));
                    };
                    
                    ws.onmessage = function(msg) {
                        const response = JSON.parse(msg.data);
                        
                        // ตรวจสอบว่าได้รับข้อมูล active_symbols หรือไม่
                        if (response.active_symbols) {
                            ws.close();
                            resolve(response.active_symbols);
                        } else if (response.error) {
                            ws.close();
                            reject(new Error(response.error.message));
                        }
                    };
                    
                    ws.onerror = function(error) {
                        reject(new Error('WebSocket error: ' + JSON.stringify(error)));
                    };
                    
                    // ตั้งเวลา timeout กรณีเชื่อมต่อนานเกินไป
                    setTimeout(() => {
                        if (ws.readyState === WebSocket.OPEN) {
                            ws.close();
                            reject(new Error('Connection timeout'));
                        }
                    }, 10000); // 10 วินาที
                });
            } catch (error) {
                throw new Error('Failed to fetch assets: ' + error.message);
            }
        }

        // จัดกลุ่ม assets ตามหมวดหมู่
        function organizeAssetsByCategory(assets) {
            const categorizedAssets = {};
            
            assets.forEach(asset => {
                const category = asset.market_display_name;
                
                if (!categorizedAssets[category]) {
                    categorizedAssets[category] = [];
                }
                
                categorizedAssets[category].push(asset);
            });
            
            return categorizedAssets;
        }

        // สร้างปุ่มสำหรับแต่ละหมวดหมู่
        function createCategoryButtons(categories) {
            const buttonContainer = document.getElementById('categoryButtons');
            buttonContainer.innerHTML = '';
            
            Object.keys(categories).sort().forEach(category => {
                const button = document.createElement('button');
                button.textContent = `${category} (${categories[category].length})`;
                button.className = 'category-btn';
                button.dataset.category = category;
                
                button.addEventListener('click', () => {
                    // ลบคลาส active จากทุกปุ่ม
                    document.querySelectorAll('.category-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // เพิ่มคลาส active ให้ปุ่มที่ถูกคลิก
                    button.classList.add('active');
                    
                    // แสดง assets ในหมวดหมู่ที่เลือก
                    displayAssetsByCategory(category);
                });
                
                buttonContainer.appendChild(button);
            });
        }

        // แสดง assets ตามหมวดหมู่ที่เลือก
        function displayAssetsByCategory(category) {
            activeCategory = category;
            const tableBody = document.getElementById('assetTableBody');
            tableBody.innerHTML = '';
            
            categories[category].forEach(asset => {
                const row = document.createElement('tr');
                
                // ชื่อ Asset
                const nameCell = document.createElement('td');
                nameCell.textContent = asset.display_name;
                row.appendChild(nameCell);
                
                // รหัส
                const symbolCell = document.createElement('td');
                symbolCell.textContent = asset.symbol;
                row.appendChild(symbolCell);
                
                // หมวดหมู่
                const categoryCell = document.createElement('td');
                categoryCell.textContent = asset.market_display_name;
                row.appendChild(categoryCell);
                
                // ประเภท
                const typeCell = document.createElement('td');
                typeCell.textContent = asset.submarket_display_name;
                row.appendChild(typeCell);
                
                // สถานะ
                const statusCell = document.createElement('td');
                const statusSpan = document.createElement('span');
                statusSpan.className = `asset-status ${asset.exchange_is_open ? 'status-open' : 'status-closed'}`;
                statusSpan.textContent = asset.exchange_is_open ? 'เปิด' : 'ปิด';
                statusCell.appendChild(statusSpan);
                row.appendChild(statusCell);
                
                tableBody.appendChild(row);
            });
        }

        // แสดงข้อความ error
        function showError(message) {
            const errorContainer = document.getElementById('errorContainer');
            errorContainer.innerHTML = `<div class="error-message">${message}</div>`;
        }

        // ฟังก์ชั่นหลักที่จะทำงานเมื่อโหลดหน้าเว็บ
        async function initializeAssetViewer() {
            try {
                document.getElementById('loading').style.display = 'flex';
                
                // ดึงข้อมูล assets จาก Deriv API
                allAssets = await fetchDerivAssets();
                
                // จัดกลุ่ม assets ตามหมวดหมู่
                categories = organizeAssetsByCategory(allAssets);
                
                // สร้างปุ่มสำหรับแต่ละหมวดหมู่
                createCategoryButtons(categories);
                
                // เลือกหมวดหมู่แรกโดยอัตโนมัติ (ถ้ามี)
                const firstCategory = Object.keys(categories).sort()[0];
                if (firstCategory) {
                    const firstButton = document.querySelector(`.category-btn[data-category="${firstCategory}"]`);
                    if (firstButton) {
                        firstButton.click();
                    }
                }
                
                document.getElementById('loading').style.display = 'none';
            } catch (error) {
                document.getElementById('loading').style.display = 'none';
                showError(`เกิดข้อผิดพลาด: ${error.message}`);
                console.error('Error initializing asset viewer:', error);
            }
        }

        // เริ่มต้นทำงานเมื่อโหลดหน้าเว็บ
        window.addEventListener('DOMContentLoaded', initializeAssetViewer);
    </script>
</body>
</html>