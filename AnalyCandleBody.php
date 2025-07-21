<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>蜡烛图(Candlestick)分析表格</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e3f2fd;
        }
        .bullish {
            background-color: rgba(46, 204, 113, 0.1);
        }
        .bearish {
            background-color: rgba(231, 76, 60, 0.1);
        }
        .neutral {
            background-color: rgba(241, 196, 15, 0.1);
        }
        .note {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #3498db;
            margin: 20px 0;
        }
        .example {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <h1>蜡烛图(Candlestick)分析表格</h1>
    <p>根据Body、Upper Wick (UWick)、Lower Wick (LWick)的比例分析</p>
    
    <table>
        <thead>
            <tr>
                <th>รูปแบบ</th>
                <th>Body (%)</th>
                <th>UWick (%)</th>
                <th>LWick (%)</th>
                <th>ความหมาย</th>
                <th>แรงซื้อ/ขาย</th>
                <th>แนวโน้มแท่งถัดไป</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bullish">
                <td><strong>แท่งแข็งแรง (Bullish)</strong></td>
                <td>70%+</td>
                <td>15%</td>
                <td>15%</td>
                <td>ผู้ซื้อควบคุมตลาด</td>
                <td>แรงซื้อสูง</td>
                <td>มีแนวโน้มขึ้นต่อ หรือปรับฐานเล็กน้อย</td>
            </tr>
            <tr class="bearish">
                <td><strong>แท่งอ่อนแอ (Bearish)</strong></td>
                <td>70%+</td>
                <td>15%</td>
                <td>15%</td>
                <td>ผู้ขายควบคุมตลาด</td>
                <td>แรงขายสูง</td>
                <td>มีแนวโน้มลงต่อ หรือ反弹เล็กน้อย</td>
            </tr>
            <tr class="neutral">
                <td><strong>Doji</strong></td>
                <td>&lt;10%</td>
                <td>45%</td>
                <td>45%</td>
                <td>ตลาดลังเล</td>
                <td>ซื้อ-ขายสมดุล</td>
                <td>อาจเปลี่ยนแนวโน้ม (reversal)</td>
            </tr>
            <tr class="bullish">
                <td><strong>Hammer (Bullish Reversal)</strong></td>
                <td>30-40%</td>
                <td>10%</td>
                <td>50-60%</td>
                <td>ผู้ขายดันราคาลงแต่ถูกซื้อกลับ</td>
                <td>แรงซื้อฟื้นตัว</td>
                <td>ขึ้นต่อหากยืนยันด้วยแท่งเขียว</td>
            </tr>
            <tr class="bearish">
                <td><strong>Hanging Man (Bearish Reversal)</strong></td>
                <td>30-40%</td>
                <td>50-60%</td>
                <td>10%</td>
                <td>ผู้ซื้อดันราคาขึ้นแต่ถูกขายกลับ</td>
                <td>แรงขายฟื้นตัว</td>
                <td>ลงต่อหากยืนยันด้วยแท่งแดง</td>
            </tr>
            <tr class="neutral">
                <td><strong>Spinning Top</strong></td>
                <td>30-40%</td>
                <td>30%</td>
                <td>30%</td>
                <td>ตลาดไม่แน่ใจ</td>
                <td>ซื้อ-ขายดุลกัน</td>
                <td>รอสัญญาณชัดเจนจากแท่งถัดไป</td>
            </tr>
            <tr class="bullish">
                <td><strong>Dragonfly Doji</strong></td>
                <td>&lt;5%</td>
                <td>5%</td>
                <td>90%+</td>
                <td>ผู้ขายพยายามแต่ล้มเหลว</td>
                <td>แรงซื้อชนะ</td>
                <td>สัญญาณกลับขึ้น (Bullish)</td>
            </tr>
            <tr class="bearish">
                <td><strong>Gravestone Doji</strong></td>
                <td>&lt;5%</td>
                <td>90%+</td>
                <td>5%</td>
                <td>ผู้ซื้อพยายามแต่ล้มเหลว</td>
                <td>แรงขายชนะ</td>
                <td>สัญญาณกลับลง (Bearish)</td>
            </tr>
        </tbody>
    </table>
    
    <div class="note">
        <h2>คำอธิบายเพิ่มเติมเกี่ยวกับแรงซื้อ-ขายและแนวโน้ม:</h2>
        <ul>
            <li><strong>Body ใหญ่ (≥70%)</strong>:
                <ul>
                    <li><strong>Bullish</strong>: แสดงแรงซื้อรุนแรง แนวโน้มขึ้นต่อ แต่หากอยู่ใน uptrend มานาน อาจเกิด profit-taking ได้</li>
                    <li><strong>Bearish</strong>: แสดงแรงขายรุนแรง แนวโน้มลงต่อ แต่หากอยู่ใน downtrend มานาน อาจเกิด short-covering</li>
                </ul>
            </li>
            <li><strong>Body ปานกลาง (30-50%) + Wick สมดุล</strong>:
                <ul>
                    <li>ตลาดยังไม่ตัดสินใจ มักเกิดในช่วง consolidation หรือก่อน breakout</li>
                </ul>
            </li>
            <li><strong>Upper Wick ยาว (≥50%)</strong>:
                <ul>
                    <li>แสดงว่ามีแรงขายเข้ามาในตลาด แม้ราคาจะขึ้นได้ช่วงหนึ่ง แต่ถูกกดกลับลง (โดยเฉพาะหากอยู่ใน resistance)</li>
                </ul>
            </li>
            <li><strong>Lower Wick ยาว (≥50%)</strong>:
                <ul>
                    <li>แสดงว่ามีแรงซื้อเข้ามาสนับสนุน แม้ราคาจะลงได้ช่วงหนึ่ง แต่ถูกดันกลับขึ้น (โดยเฉพาะหากอยู่ใน support)</li>
                </ul>
            </li>
        </ul>
    </div>
    
    <div class="example">
        <h2>ตัวอย่างการประยุกต์ใช้:</h2>
        <p><strong>รูปแบบ 40-30-30 (Body-UWick-LWick)</strong>:</p>
        <ul>
            <li>เป็น <strong>Spinning Top</strong> ที่ไม่ชัดเจน ต้องดูบริบท:
                <ul>
                    <li>หากอยู่ใน uptrend: อาจหมายถึงแรงซื้อเริ่มอ่อน</li>
                    <li>หากอยู่ใน downtrend: อาจหมายถึงแรงขายเริ่มอ่อน</li>
                </ul>
            </li>
            <li>แนวโน้มถัดไป: รอ confirmation จากแท่งถัดไป (เช่น แท่งเขียว/แดงที่覆盖ราคาแท่งนี้)</li>
        </ul>
        
        <p><strong>รูปแบบ 40-10-50 (Hammer)</strong>:</p>
        <ul>
            <li>สัญญาณกลับขึ้นหากเกิดหลัง downtrend ยืนยันด้วยแท่งเขียวที่ volume สูง</li>
        </ul>
        
        <p><strong>หมายเหตุ</strong>: ควรใช้ร่วมกับเครื่องมืออื่น เช่น <strong>Volume, Support/Resistance, Trendline</strong> เพื่อความแม่นยำ!</p>
    </div>
</body>
</html>