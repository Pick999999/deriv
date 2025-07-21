<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }
        th {
            background-color: #0052cc;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e6f0ff;
        }
        .category {
            background-color: #e6f7ff;
            font-weight: bold;
        }
        .value {
            font-family: monospace;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">การอธิบาย  proposal_open_contract JSON Response ของการเทรด Deriv</h1>
    
    <table>
        <tr>
            <th>Field</th>
            <th>ค่า</th>
            <th>คำอธิบาย</th>
        </tr>
        
        <!-- ข้อมูลบัญชีและการระบุตัวตน -->
        <tr class="category">
            <td colspan="3">ข้อมูลบัญชีและการระบุตัวตน</td>
        </tr>
        <tr>
            <td>account_id</td>
            <td class="value">191869168</td>
            <td>เลขที่บัญชีของคุณ</td>
        </tr>
        <tr>
            <td>id</td>
            <td class="value">0e5ff485-3156-e6d0-6817-16fd50cf36fb</td>
            <td>ID เฉพาะของสัญญานี้</td>
        </tr>
        <tr>
            <td>contract_id</td>
            <td class="value">280714878868</td>
            <td>รหัสอ้างอิงสัญญา</td>
        </tr>
        <tr>
            <td>transaction_ids.buy</td>
            <td class="value">559545874968</td>
            <td>รหัสธุรกรรมการซื้อ</td>
        </tr>
        
        <!-- ข้อมูลสินทรัพย์ -->
        <tr class="category">
            <td colspan="3">ข้อมูลสินทรัพย์</td>
        </tr>
        <tr>
            <td>underlying</td>
            <td class="value">R_100</td>
            <td>รหัสภายในของสินทรัพย์</td>
        </tr>
        <tr>
            <td>display_name</td>
            <td class="value">Volatility 100 Index</td>
            <td>ชื่อสินทรัพย์ที่แสดงให้ผู้ใช้เห็น</td>
        </tr>
        <tr>
            <td>currency</td>
            <td class="value">USD</td>
            <td>สกุลเงินที่ใช้ในการเทรด</td>
        </tr>
        
        <!-- ประเภทและเงื่อนไขของสัญญา -->
        <tr class="category">
            <td colspan="3">ประเภทและเงื่อนไขของสัญญา</td>
        </tr>
        <tr>
            <td>contract_type</td>
            <td class="value">CALL</td>
            <td>ประเภทออปชั่น (เดิมพันว่าราคาจะขึ้น)</td>
        </tr>
        <tr>
            <td>shortcode</td>
            <td class="value">CALL_R_100_1.95_1746490201_1746490261_S0P_0</td>
            <td>รหัสย่อของสัญญาที่รวมข้อมูลสำคัญไว้</td>
        </tr>
        <tr>
            <td>longcode</td>
            <td class="value">Win payout if Volatility 100 Index is strictly higher than entry spot at 1 minute after contract start time.</td>
            <td>คำอธิบายสัญญาแบบละเอียด (คุณจะชนะถ้าราคาสูงกว่าราคาเริ่มต้น ณ เวลา 1 นาทีหลังจากเริ่มสัญญา)</td>
        </tr>
        
        <!-- ราคาและผลตอบแทน -->
        <tr class="category">
            <td colspan="3">ราคาและผลตอบแทน</td>
        </tr>
        <tr>
            <td>buy_price</td>
            <td class="value">1</td>
            <td>ราคาที่คุณซื้อสัญญานี้ ($1)</td>
        </tr>
        <tr>
            <td>bid_price</td>
            <td class="value">1.93</td>
            <td>ราคาปัจจุบันที่ Deriv เสนอให้ซื้อคืนสัญญาของคุณ ($1.93)</td>
        </tr>
        <tr>
            <td>payout</td>
            <td class="value">1.95</td>
            <td>จำนวนเงินที่คุณจะได้รับหากชนะ ($1.95)</td>
        </tr>
        <tr>
            <td>profit</td>
            <td class="value">0.93</td>
            <td>กำไรปัจจุบัน ($0.93)</td>
        </tr>
        <tr>
            <td>profit_percentage</td>
            <td class="value">93</td>
            <td>เปอร์เซ็นต์กำไรเทียบกับเงินลงทุน (93%)</td>
        </tr>
        
        <!-- เวลาและสถานะ -->
        <tr class="category">
            <td colspan="3">เวลาและสถานะ</td>
        </tr>
        <tr>
            <td>purchase_time</td>
            <td class="value">1746490201</td>
            <td>เวลาที่ซื้อสัญญา (timestamp)</td>
        </tr>
        <tr>
            <td>date_start</td>
            <td class="value">1746490201</td>
            <td>เวลาเริ่มต้นสัญญา</td>
        </tr>
        <tr>
            <td>date_expiry</td>
            <td class="value">1746490261</td>
            <td>เวลาหมดอายุสัญญา</td>
        </tr>
        <tr>
            <td>date_settlement</td>
            <td class="value">1746490261</td>
            <td>เวลาที่จะมีการชำระเงิน</td>
        </tr>
        <tr>
            <td>expiry_time</td>
            <td class="value">1746490261</td>
            <td>เวลาหมดอายุ (ซ้ำกับ date_expiry)</td>
        </tr>
        <tr>
            <td>status</td>
            <td class="value">open</td>
            <td>สถานะปัจจุบันของสัญญา (ยังเปิดอยู่)</td>
        </tr>
        
        <!-- ข้อมูลสปอต -->
        <tr class="category">
            <td colspan="3">ข้อมูลสปอต</td>
        </tr>
        <tr>
            <td>current_spot</td>
            <td class="value">1669.07</td>
            <td>ราคาปัจจุบันของสินทรัพย์</td>
        </tr>
        <tr>
            <td>current_spot_display_value</td>
            <td class="value">1669.07</td>
            <td>ค่าราคาปัจจุบันในรูปแบบข้อความ</td>
        </tr>
        <tr>
            <td>current_spot_time</td>
            <td class="value">1746490200</td>
            <td>เวลาที่อัปเดตราคาล่าสุด</td>
        </tr>
        
        <!-- ข้อมูลเพิ่มเติม -->
        <tr class="category">
            <td colspan="3">ข้อมูลเพิ่มเติม</td>
        </tr>
        <tr>
            <td>barrier_count</td>
            <td class="value">1</td>
            <td>จำนวนเงื่อนไขราคาในสัญญานี้</td>
        </tr>
        <tr>
            <td>is_expired</td>
            <td class="value">0</td>
            <td>สัญญายังไม่หมดอายุ (0=ไม่ใช่, 1=ใช่)</td>
        </tr>
        <tr>
            <td>is_forward_starting</td>
            <td class="value">0</td>
            <td>ไม่ใช่สัญญาที่เริ่มในอนาคต</td>
        </tr>
        <tr>
            <td>is_intraday</td>
            <td class="value">1</td>
            <td>เป็นสัญญาภายในวันเดียว (ใช่)</td>
        </tr>
        <tr>
            <td>is_path_dependent</td>
            <td class="value">0</td>
            <td>ไม่ขึ้นอยู่กับเส้นทางราคา</td>
        </tr>
        <tr>
            <td>is_settleable</td>
            <td class="value">0</td>
            <td>ยังไม่สามารถชำระได้</td>
        </tr>
        <tr>
            <td>is_sold</td>
            <td class="value">0</td>
            <td>สัญญายังไม่ถูกขาย</td>
        </tr>
        <tr>
            <td>is_valid_to_cancel</td>
            <td class="value">0</td>
            <td>ไม่สามารถยกเลิกสัญญาได้</td>
        </tr>
        <tr>
            <td>is_valid_to_sell</td>
            <td class="value">0</td>
            <td>ไม่สามารถขายสัญญาคืนได้ในขณะนี้</td>
        </tr>
        
        <!-- ข้อผิดพลาด -->
        <tr class="category">
            <td colspan="3">ข้อผิดพลาด</td>
        </tr>
        <tr>
            <td>validation_error</td>
            <td class="value">Contract cannot be sold at this time. Please try again.</td>
            <td>แสดงว่าไม่สามารถขายสัญญาได้ในขณะนี้</td>
        </tr>
        <tr>
            <td>validation_error_code</td>
            <td class="value">General</td>
            <td>รหัสข้อผิดพลาด</td>
        </tr>
    </table>
    
    <h2 style="margin-top: 20px;">สรุป</h2>
    <p>
        นี่คือสัญญา CALL (เดิมพันว่าราคาจะขึ้น) บน Volatility 100 Index ที่คุณซื้อในราคา $1 โดยมีระยะเวลา 1 นาที 
        หากชนะคุณจะได้รับ $1.95 (กำไร $0.95) สัญญายังเปิดอยู่และขณะนี้มีกำไร $0.93 (93%) 
        แต่ไม่สามารถขายคืนได้ในขณะนี้ตามข้อความ validation_error
    </p>

	<table>
        <tr>
            <th>Field</th>
            <th>ค่า</th>
            <th>คำอธิบาย</th>
        </tr>
        
        <!-- ข้อมูลบัญชีและการระบุตัวตน -->
        <tr class="category">
            <td colspan="3">ข้อมูลบัญชีและการระบุตัวตน</td>
        </tr>
        <tr>
            <td>account_id</td>
            <td class="value">191869168</td>
            <td>เลขที่บัญชีของคุณ</td>
        </tr>
        <tr>
            <td>id</td>
            <td class="value">0c01d1c0-c7a3-095f-2bf6-7bd7e7b3e985</td>
            <td>ID เฉพาะของสัญญานี้</td>
        </tr>
        <tr>
            <td>contract_id</td>
            <td class="value">280715531568</td>
            <td>รหัสอ้างอิงสัญญา</td>
        </tr>
        <tr>
            <td>transaction_ids.buy</td>
            <td class="value">559547171048</td>
            <td>รหัสธุรกรรมการซื้อ</td>
        </tr>
        
        <!-- ข้อมูลสินทรัพย์ -->
        <tr class="category">
            <td colspan="3">ข้อมูลสินทรัพย์</td>
        </tr>
        <tr>
            <td>underlying</td>
            <td class="value">R_100</td>
            <td>รหัสภายในของสินทรัพย์ (Volatility 100 Index)</td>
        </tr>
        <tr>
            <td>display_name</td>
            <td class="value">Volatility 100 Index</td>
            <td>ชื่อสินทรัพย์ที่แสดงให้ผู้ใช้เห็น</td>
        </tr>
        <tr>
            <td>currency</td>
            <td class="value">USD</td>
            <td>สกุลเงินที่ใช้ในการเทรด</td>
        </tr>
        
        <!-- ประเภทและเงื่อนไขของสัญญา -->
        <tr class="category">
            <td colspan="3">ประเภทและเงื่อนไขของสัญญา</td>
        </tr>
        <tr>
            <td>contract_type</td>
            <td class="value">CALL</td>
            <td>ประเภทออปชั่น (เดิมพันว่าราคาจะขึ้น)</td>
        </tr>
        <tr>
            <td>barrier(ราคาที่เราเข้าซื้อ)</td>
            <td class="value">1674.36</td>
            <td>ระดับราคาที่ใช้เป็นเกณฑ์ในการตัดสินผลลัพธ์ของสัญญา</td>
        </tr>
        <tr>
            <td>barrier_count</td>
            <td class="value">1</td>
            <td>จำนวนเงื่อนไขราคาในสัญญานี้</td>
        </tr>
        <tr>
            <td>shortcode</td>
            <td class="value">CALL_R_100_1.95_1746490826_1746490886_S0P_0</td>
            <td>รหัสย่อของสัญญาที่รวมข้อมูลสำคัญไว้</td>
        </tr>
        <tr>
            <td>longcode</td>
            <td class="value">Win payout if Volatility 100 Index is strictly higher than entry spot at 1 minute after contract start time.</td>
            <td>คำอธิบายสัญญาแบบละเอียด (คุณจะชนะถ้าราคาสูงกว่าราคาเริ่มต้น ณ เวลา 1 นาทีหลังจากเริ่มสัญญา)</td>
        </tr>
        
        <!-- ราคาและผลตอบแทน -->
        <tr class="category">
            <td colspan="3">ราคาและผลตอบแทน</td>
        </tr>
        <tr>
            <td>buy_price</td>
            <td class="value">1</td>
            <td>Money ที่คุณซื้อสัญญานี้ ($1)</td>
        </tr>
        <tr>
            <td>bid_price</td>
            <td class="value">0.95</td>
            <td>ราคาปัจจุบันที่ Deriv เสนอให้ซื้อคืนสัญญาของคุณ ($0.95)</td>
        </tr>
        <tr>
            <td>payout</td>
            <td class="value">1.95</td>
            <td>จำนวนเงินที่คุณจะได้รับหากชนะ ($1.95)</td>
        </tr>
        <tr>
            <td>profit</td>
            <td class="value">-0.05</td>
            <td>กำไรปัจจุบัน (ขาดทุน $0.05)</td>
        </tr>
        <tr>
            <td>profit_percentage</td>
            <td class="value">-5</td>
            <td>เปอร์เซ็นต์กำไรเทียบกับเงินลงทุน (ขาดทุน 5%)</td>
        </tr>
        
        <!-- ราคาเข้าและราคาปัจจุบัน -->
        <tr class="category">
            <td colspan="3">ราคาเข้าและราคาปัจจุบัน</td>
        </tr>
        <tr>
            <td>entry_spot(ราคาที่เราเข้าซื้อ)</td>
            <td class="value">1674.36</td>
            <td>ราคาเริ่มต้นของสัญญา(ราคาที่เราเข้าซื้อ)</td>
        </tr>
        <tr>
            <td>entry_spot_display_value</td>
            <td class="value">1674.36</td>
            <td>ค่าราคาเริ่มต้นในรูปแบบข้อความ</td>
        </tr>
        <tr>
            <td>entry_tick</td>
            <td class="value">1674.36</td>
            <td>ค่าราคา tick แรกของสัญญา</td>
        </tr>
        <tr>
            <td>entry_tick_display_value</td>
            <td class="value">1674.36</td>
            <td>ค่าราคา tick แรกในรูปแบบข้อความ</td>
        </tr>
        <tr>
            <td>entry_tick_time</td>
            <td class="value">1746490828</td>
            <td>เวลาของ tick แรก (timestamp)</td>
        </tr>
        <tr>
            <td>current_spot</td>
            <td class="value">1674.36</td>
            <td>ราคาปัจจุบันของสินทรัพย์</td>
        </tr>
        <tr>
            <td>current_spot_display_value</td>
            <td class="value">1674.36</td>
            <td>ค่าราคาปัจจุบันในรูปแบบข้อความ</td>
        </tr>
        <tr>
            <td>current_spot_time</td>
            <td class="value">1746490828</td>
            <td>เวลาที่อัปเดตราคาล่าสุด (timestamp)</td>
        </tr>
        
        <!-- เวลาและสถานะ -->
        <tr class="category">
            <td colspan="3">เวลาและสถานะ</td>
        </tr>
        <tr>
            <td>purchase_time</td>
            <td class="value">1746490826</td>
            <td>เวลาที่ซื้อสัญญา (timestamp)</td>
        </tr>
        <tr>
            <td>date_start</td>
            <td class="value">1746490826</td>
            <td>เวลาเริ่มต้นสัญญา (timestamp)</td>
        </tr>
        <tr>
            <td>date_expiry</td>
            <td class="value">1746490886</td>
            <td>เวลาหมดอายุสัญญา (timestamp)</td>
        </tr>
        <tr>
            <td>date_settlement</td>
            <td class="value">1746490886</td>
            <td>เวลาที่จะมีการชำระเงิน (timestamp)</td>
        </tr>
        <tr>
            <td>expiry_time</td>
            <td class="value">1746490886</td>
            <td>เวลาหมดอายุ (ซ้ำกับ date_expiry)</td>
        </tr>
        <tr>
            <td>status</td>
            <td class="value">open</td>
            <td>สถานะปัจจุบันของสัญญา (ยังเปิดอยู่)</td>
        </tr>
        
        <!-- สถานะเพิ่มเติม -->
        <tr class="category">
            <td colspan="3">สถานะเพิ่มเติม</td>
        </tr>
        <tr>
            <td>is_expired</td>
            <td class="value">0</td>
            <td>สัญญายังไม่หมดอายุ (0=ไม่ใช่, 1=ใช่)</td>
        </tr>
        <tr>
            <td>is_forward_starting</td>
            <td class="value">0</td>
            <td>ไม่ใช่สัญญาที่เริ่มในอนาคต (0=ไม่ใช่, 1=ใช่)</td>
        </tr>
        <tr>
            <td>is_intraday</td>
            <td class="value">1</td>
            <td>เป็นสัญญาภายในวันเดียว (1=ใช่, 0=ไม่ใช่)</td>
        </tr>
        <tr>
            <td>is_path_dependent</td>
            <td class="value">0</td>
            <td>ไม่ขึ้นอยู่กับเส้นทางราคา (0=ไม่ใช่, 1=ใช่)</td>
        </tr>
        <tr>
            <td>is_settleable</td>
            <td class="value">0</td>
            <td>ยังไม่สามารถชำระได้ (0=ไม่ใช่, 1=ใช่)</td>
        </tr>
        <tr>
            <td>is_sold</td>
            <td class="value">0</td>
            <td>สัญญายังไม่ถูกขาย (0=ไม่ใช่, 1=ใช่)</td>
        </tr>
        <tr>
            <td>is_valid_to_cancel</td>
            <td class="value">0</td>
            <td>ไม่สามารถยกเลิกสัญญาได้ (0=ไม่ใช่, 1=ใช่)</td>
        </tr>
        <tr>
            <td>is_valid_to_sell</td>
            <td class="value">1</td>
            <td>สามารถขายสัญญาคืนได้ (1=ใช่, 0=ไม่ใช่)</td>
        </tr>
    </table>
    
    <h2 style="margin-top: 20px;">สรุป</h2>
    <p>
        นี่คือสัญญา CALL (เดิมพันว่าราคาจะขึ้น) บน Volatility 100 Index ที่คุณซื้อในราคา $1 โดยมีระยะเวลา 1 นาที 
        หากชนะคุณจะได้รับ $1.95 (กำไร $0.95) สัญญายังเปิดอยู่และขณะนี้มีขาดทุน 5% ($0.05) 
        ราคาเริ่มต้นอยู่ที่ 1674.36 และราคาปัจจุบันยังอยู่ที่ระดับเดิมคือ 1674.36
        สัญญานี้สามารถขายคืนได้ในขณะนี้ (is_valid_to_sell = 1)
    </p>
</body>
</html>