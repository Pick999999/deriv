<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อินโฟกราฟิก: แนวโน้มอุตสาหกรรมและการวิจัยตลาด Deriv.com</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #F0F4F8; /* Light Blue-Gray */
        }
        .chart-container {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            height: 300px; /* Base height */
            max-height: 400px;
        }
        @media (min-width: 768px) {
            .chart-container {
                height: 350px;
            }
        }
        .stat-card {
            background-color: #FFFFFF;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            text-align: center;
        }
        .section-title {
            font-size: 1.875rem; /* text-3xl */
            font-weight: 700;
            color: #1A202C; /* Dark Slate Gray */
            margin-bottom: 1rem;
            text-align: center;
        }
        .section-intro {
            font-size: 1.125rem; /* text-lg */
            color: #4A5568; /* Medium Slate Gray */
            margin-bottom: 2rem;
            text-align: center;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .card {
            background-color: #FFFFFF;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card-title {
            font-size: 1.25rem; /* text-xl */
            font-weight: 600;
            color: #26A69A; /* Teal */
            margin-bottom: 0.75rem;
        }
        .card-text {
            color: #4A5568; /* Medium Slate Gray */
            font-size: 0.95rem;
        }
        .highlight-stat {
            font-size: 2.5rem;
            font-weight: 700;
            color: #FF7043; /* Vibrant Orange */
        }
        .palette-primary { color: #FF7043; } /* Vibrant Orange */
        .palette-secondary { color: #FFEE58; } /* Bright Yellow */
        .palette-tertiary { color: #26A69A; } /* Teal */
        .palette-quaternary { color: #7E57C2; } /* Deep Purple */
        .palette-accent { color: #42A5F5; } /* Accent Blue */

        .html-flowchart-node {
            background-color: #E0F2F1; /* Light Teal */
            border: 2px solid #26A69A; /* Teal */
            color: #004D40; /* Dark Teal */
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            text-align: center;
            font-weight: 500;
            min-width: 120px;
        }
        .html-flowchart-arrow {
            font-size: 1.5rem;
            color: #26A69A; /* Teal */
            margin: 0 0.5rem;
            display: flex;
            align-items: center;
        }
        .responsive-table {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .responsive-table table {
            width: 100%;
            min-width: 700px; /* Adjust as needed for content */
            border-collapse: collapse;
        }
        .responsive-table th, .responsive-table td {
            padding: 0.75rem;
            border: 1px solid #E2E8F0; /* Light Gray */
            text-align: left;
            font-size: 0.9rem;
        }
        .responsive-table th {
            background-color: #E0F2F1; /* Light Teal */
            color: #004D40; /* Dark Teal */
            font-weight: 600;
        }
        .responsive-table tr:nth-child(even) {
            background-color: #F8FAFC; /* Lighter Gray */
        }
    </style>
</head>
<body class="text-gray-800">

    <div class="container mx-auto p-4 md:p-8">

        <header class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold palette-primary mb-4">อินโฟกราฟิก: ภาพรวมตลาดและการเทรดบน Deriv.com</h1>
            <p class="text-xl text-gray-600">ข้อมูลเชิงลึกจากรายงาน "คู่มือการเทรดและเงื่อนไขการแพ้/ชนะบน Deriv.com"</p>
        </header>

        <section id="intro" class="mb-16">
            <h2 class="section-title">Deriv.com: ผู้นำในโลกการเทรดออนไลน์</h2>
            <p class="section-intro">Deriv.com ได้สร้างชื่อเสียงในฐานะโบรกเกอร์ออนไลน์ที่น่าเชื่อถือและมีประสบการณ์ยาวนานกว่า 25 ปี โดยมีลูกค้ากว่า 3 ล้านรายทั่วโลก ให้บริการเทรดเดอร์ด้วยแพลตฟอร์มและเครื่องมือที่ทันสมัย พร้อมการสนับสนุนลูกค้าตลอด 24/7</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="stat-card">
                    <div class="text-6xl mb-2">👥</div>
                    <div class="highlight-stat">3 ล้าน+</div>
                    <p class="text-gray-600 text-lg">ลูกค้าทั่วโลก</p>
                    <p class="card-text mt-2">ฐานลูกค้าที่กว้างขวางสะท้อนถึงความไว้วางใจและความนิยมในระดับสากล</p>
                </div>
                <div class="stat-card">
                    <div class="text-6xl mb-2">⏳</div>
                    <div class="highlight-stat">25+ ปี</div>
                    <p class="text-gray-600 text-lg">ประสบการณ์ในอุตสาหกรรม</p>
                    <p class="card-text mt-2">ประสบการณ์ที่ยาวนานกว่าสองทศวรรษรับประกันความเชี่ยวชาญและเสถียรภาพ</p>
                </div>
            </div>
        </section>

        <section id="markets" class="mb-16">
            <h2 class="section-title">เข้าถึงตลาดหลากหลาย: โอกาสสำหรับทุกคน</h2>
            <p class="section-intro">Deriv นำเสนอการเข้าถึงตลาดการเงินที่หลากหลาย ช่วยให้เทรดเดอร์สามารถกระจายพอร์ตการลงทุนและเลือกสินทรัพย์ที่ตรงกับกลยุทธ์ของตนเองได้ ความหลากหลายนี้เป็นหนึ่งในปัจจัยสำคัญที่ดึงดูดเทรดเดอร์ทุกระดับ</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="card">
                    <h3 class="card-title">สัดส่วนตลาดหลักที่ให้บริการ (โดยประมาณ)</h3>
                    <div class="chart-container h-[350px] md:h-[400px]">
                        <canvas id="marketDistributionChart"></canvas>
                    </div>
                    <p class="card-text mt-4">แผนภูมินี้แสดงสัดส่วนโดยประมาณของประเภทตลาดหลักที่เทรดเดอร์สามารถเข้าถึงได้บน Deriv สะท้อนให้เห็นถึงความหลากหลายของสินทรัพย์ที่มีให้เลือกเทรด</p>
                </div>
                <div class="space-y-6">
                    <div class="card">
                        <h3 class="card-title"><span class="text-2xl mr-2">💹</span>Forex (ตลาดแลกเปลี่ยนเงินตรา)</h3>
                        <p class="card-text">เทรดคู่สกุลเงินกว่า 50 คู่ ตลอด 24 ชั่วโมง 5 วันทำการ ด้วยเลเวอเรจสูงสุด <strong class="palette-primary">1:1000</strong> สเปรดที่แข่งขันได้ และไม่มีค่าคอมมิชชั่นหรือค่า Swap ในบางคู่สกุลเงิน. ปัจจัยสำคัญที่ส่งผลต่ออัตราแลกเปลี่ยนได้แก่ อัตราดอกเบี้ย, อัตราเงินเฟ้อ, เหตุการณ์ทางภูมิรัฐศาสตร์, ตัวชี้วัดทางเศรษฐกิจ, และการดำเนินการของธนาคารกลาง.</p>
                    </div>
                    <div class="card">
                        <h3 class="card-title"><span class="text-2xl mr-2">📈</span>Derived Indices (ดัชนีสังเคราะห์)</h3>
                        <p class="card-text">สินทรัพย์ที่เป็นเอกลักษณ์ เทรดได้ <strong class="palette-primary">24/7</strong> ไม่ขึ้นกับข่าวตลาดจริง มีความผันผวนหลากหลายรูปแบบ เช่น Volatility Indices, Crash/Boom Indices, Jump Indices, และ Basket Indices (วัดค่าสกุลเงินเทียบกับตะกร้าสกุลเงินโลก).</p>
                    </div>
                     <div class="card">
                        <h3 class="card-title"><span class="text-2xl mr-2">🏛️</span>Stocks & Indices (หุ้นและดัชนีหุ้น)</h3>
                        <p class="card-text">ลงทุนในหุ้นบริษัทชั้นนำระดับโลก (เช่น Apple, Tesla) และดัชนีตลาดหุ้นสำคัญทั่วโลก.</p>
                    </div>
                    <div class="card">
                        <h3 class="card-title"><span class="text-2xl mr-2">🪙</span>สินค้าโภคภัณฑ์ & สกุลเงินดิจิทัล & ETFs</h3>
                        <p class="card-text">เทรดสินค้าโภคภัณฑ์หลัก (ทองคำ, น้ำมัน), สกุลเงินดิจิทัลยอดนิยม (Bitcoin, Ethereum) ตลอด 24 ชั่วโมง, และกองทุนซื้อขายแลกเปลี่ยน (ETFs).</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="platforms" class="mb-16">
            <h2 class="section-title">พลังของแพลตฟอร์ม: เทคโนโลยีเพื่อการเทรด</h2>
            <p class="section-intro">Deriv มีชุดแพลตฟอร์มการเทรดที่ทรงพลังและหลากหลาย เพื่อตอบสนองความต้องการของเทรดเดอร์ทุกสไตล์ ตั้งแต่ผู้เริ่มต้นจนถึงมืออาชีพที่ต้องการเครื่องมือวิเคราะห์ขั้นสูงหรือการเทรดอัตโนมัติ</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card">
                    <h3 class="card-title"><span class="text-2xl mr-2">💻</span>Deriv MT5</h3>
                    <p class="card-text">แพลตฟอร์มยอดนิยมระดับโลก พร้อมเครื่องมือวิเคราะห์กราฟขั้นสูง, ตัวชี้วัดที่หลากหลาย, และความสามารถในการวางคำสั่งเทรดอย่างรวดเร็ว.</p>
                </div>
                <div class="card">
                    <h3 class="card-title"><span class="text-2xl mr-2">📲</span>Deriv GO</h3>
                    <p class="card-text">แอปพลิเคชันมือถือสำหรับการเทรด Multipliers และ Accumulators สะดวกทุกที่ทุกเวลา.</p>
                </div>
                <div class="card">
                    <h3 class="card-title"><span class="text-2xl mr-2">📊</span>Deriv Trader</h3>
                    <p class="card-text">แพลตฟอร์มใช้งานง่ายสำหรับการเทรด Options ในตลาดการเงินและ Derived Indices ตลอด 24/7 โดยมีความเสี่ยงสูงสุดจำกัดที่เงินลงทุนเริ่มต้น.</p>
                </div>
                <div class="card">
                    <h3 class="card-title"><span class="text-2xl mr-2">🤖</span>Deriv Bot</h3>
                    <p class="card-text">สร้างกลยุทธ์เทรดอัตโนมัติด้วยอินเทอร์เฟซแบบลากและวาง โดยไม่ต้องเขียนโค้ด รองรับการตั้งค่า Take Profit และ Stop Loss.</p>
                </div>
                <div class="card">
                    <h3 class="card-title"><span class="text-2xl mr-2">🔗</span>Deriv cTrader</h3>
                    <p class="card-text">โดดเด่นด้วยคุณสมบัติ Copy Trading ในตัว, Timeframes ที่ยืดหยุ่นถึง 54 แบบ, และตัวชี้วัด 70 ตัว.</p>
                </div>
                <div class="card">
                    <h3 class="card-title"><span class="text-2xl mr-2">📈</span>Deriv X</h3>
                    <p class="card-text">เข้าถึงชาร์ต TradingView พร้อมเครื่องมือวิเคราะห์กว่า 110 รายการ ช่วยให้เทรดเดอร์วิเคราะห์ตลาดและดำเนินการเทรดได้ทันที.</p>
                </div>
            </div>
        </section>
        
        <section id="trading-process" class="mb-16">
            <h2 class="section-title">ขั้นตอนการเทรดเบื้องต้น (ตัวอย่างบน Deriv Trader)</h2>
            <p class="section-intro">การเริ่มต้นเทรดบนแพลตฟอร์มของ Deriv สามารถทำได้อย่างง่ายดาย นี่คือตัวอย่างขั้นตอนพื้นฐานสำหรับการเทรด Options บน Deriv Trader ซึ่งแสดงให้เห็นถึงความเรียบง่ายในการเข้าถึงตลาด</p>
            <div class="card md:col-span-2">
                <h3 class="card-title text-center">4 ขั้นตอนง่ายๆ ในการเทรด Options บน Deriv Trader</h3>
                <div class="flex flex-col md:flex-row justify-around items-center space-y-4 md:space-y-0 md:space-x-4 p-4">
                    <div class="html-flowchart-node">1. เลือกประเภทการเทรด</div>
                    <div class="html-flowchart-arrow hidden md:flex">➡️</div>
                    <div class="html-flowchart-arrow flex md:hidden text-3xl transform rotate-90">⬇️</div>
                    <div class="html-flowchart-node">2. เลือกสินทรัพย์</div>
                    <div class="html-flowchart-arrow hidden md:flex">➡️</div>
                     <div class="html-flowchart-arrow flex md:hidden text-3xl transform rotate-90">⬇️</div>
                    <div class="html-flowchart-node">3. ตรวจสอบกราฟ</div>
                    <div class="html-flowchart-arrow hidden md:flex">➡️</div>
                     <div class="html-flowchart-arrow flex md:hidden text-3xl transform rotate-90">⬇️</div>
                    <div class="html-flowchart-node">4. วางคำสั่งเทรด</div>
                </div>
                <p class="card-text mt-6 text-center">กระบวนการนี้ถูกออกแบบมาให้ใช้งานง่าย ช่วยให้ผู้ใช้สามารถตัดสินใจและดำเนินการเทรดได้อย่างรวดเร็ว โดยยังคงสามารถเข้าถึงข้อมูลและเครื่องมือที่จำเป็นได้</p>
            </div>
        </section>

        <section id="cfds" class="mb-16">
            <h2 class="section-title">ทำความเข้าใจ CFDs: เลเวอเรจและความเสี่ยง</h2>
            <p class="section-intro">สัญญาซื้อขายส่วนต่าง (CFDs) เป็นเครื่องมืออนุพันธ์ที่ช่วยให้เทรดเดอร์สามารถเก็งกำไรจากการเคลื่อนไหวของราคาของสินทรัพย์อ้างอิงโดยไม่ต้องเป็นเจ้าของสินทรัพย์นั้นจริง การใช้เลเวอเรจสามารถเพิ่มทั้งโอกาสในการทำกำไรและขนาดของผลขาดทุนได้</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="card">
                    <h3 class="card-title">เปรียบเทียบเลเวอเรจสูงสุดในสินทรัพย์ CFD ต่างๆ</h3>
                    <div class="chart-container h-[400px] md:h-[450px]">
                        <canvas id="leverageChart"></canvas>
                    </div>
                    <p class="card-text mt-4">เลเวอเรจที่สูงขึ้นหมายถึงศักยภาพในการทำกำไรที่มากขึ้นด้วยเงินทุนเริ่มต้นที่น้อยลง แต่ก็มาพร้อมกับความเสี่ยงที่สูงขึ้นเช่นกัน โดยเฉพาะใน Derived Indices ที่มีเลเวอเรจสูงมาก.</p>
                </div>
                <div class="space-y-6">
                    <div class="card">
                        <h3 class="card-title"><span class="text-2xl mr-2">🛡️</span>Zero-Balance Protection</h3>
                        <p class="card-text">Deriv มีระบบป้องกันยอดเงินคงเหลือในบัญชีไม่ให้ติดลบ ทำให้คุณไม่สูญเสียมากกว่าเงินที่ฝากไว้ คุณสมบัตินี้ช่วยจำกัดความเสี่ยงด้านลบที่อาจเกิดขึ้นจากการเทรดด้วยเลเวอเรจสูง.</p>
                    </div>
                    <div class="card">
                        <h3 class="card-title"><span class="text-2xl mr-2">⚙️</span>การจัดการความเสี่ยงด้วย SL/TP</h3>
                        <p class="card-text">ใช้คำสั่ง <strong class="palette-tertiary">Stop-Loss (SL)</strong> เพื่อจำกัดผลขาดทุน และ <strong class="palette-tertiary">Take-Profit (TP)</strong> เพื่อล็อกกำไรอัตโนมัติ นอกจากนี้ยังมี <strong class="palette-tertiary">Trailing Stop-Loss</strong> ที่ปรับระดับราคาตามการเคลื่อนไหวของตลาดในทิศทางที่เป็นประโยชน์.</p>
                    </div>
                     <div class="card">
                        <h3 class="card-title"><span class="text-2xl mr-2">💰</span>สเปรดและค่าธรรมเนียม CFD</h3>
                        <p class="card-text">การเทรด CFD บน Deriv ส่วนใหญ่ <strong class="palette-primary">ไม่มีค่าคอมมิชชั่น</strong> และมีสเปรดที่แข่งขันได้ บางบัญชี Forex ยังปลอดค่า Swap สำหรับการถือครองตำแหน่งข้ามคืน.</p>
                    </div>
                </div>
            </div>

            <div class="card mt-8 md:col-span-2">
                <h3 class="card-title text-center">ตารางเปรียบเทียบลักษณะสำคัญของ CFD ในสินทรัพย์ประเภทต่างๆ บน Deriv</h3>
                <div class="responsive-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ประเภทสินทรัพย์</th>
                                <th>สเปรดขั้นต่ำ</th>
                                <th>เลเวอเรจสูงสุด</th>
                                <th>ค่าคอมมิชชั่น</th>
                                <th>ค่า Swap</th>
                                <th>ชั่วโมงการเทรด</th>
                                <th>Contract Size (หน่วยต่อล็อต)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Forex</td>
                                <td>0.3 pips</td>
                                <td>1:1000</td>
                                <td>0% (สำหรับบางคู่)</td>
                                <td>0% (สำหรับบางคู่)</td>
                                <td>จันทร์ 00:00 - ศุกร์ 20:55 (GMT)</td>
                                <td>100 (AUD Basket)</td>
                            </tr>
                            <tr>
                                <td>Derived Indices</td>
                                <td>0.24 pips</td>
                                <td>1:4000</td>
                                <td>0%</td>
                                <td>มีค่าใช้จ่าย</td>
                                <td>อาทิตย์ 00:00 - เสาร์ 24:00 (GMT)</td>
                                <td>1 (Boom/Crash Indices)</td>
                            </tr>
                            <tr>
                                <td>Stocks</td>
                                <td>0.6 pips</td>
                                <td>1:50</td>
                                <td>0%</td>
                                <td>มีค่าใช้จ่าย</td>
                                <td>ตามเวลาตลาดจริง</td>
                                <td>ไม่ระบุ</td>
                            </tr>
                            <tr>
                                <td>Stock Indices</td>
                                <td>0.6 pips</td>
                                <td>1:100</td>
                                <td>0%</td>
                                <td>มีค่าใช้จ่าย</td>
                                <td>ตามเวลาตลาดจริง</td>
                                <td>ไม่ระบุ</td>
                            </tr>
                            <tr>
                                <td>Commodities</td>
                                <td>0.6 pips</td>
                                <td>1:500</td>
                                <td>0%</td>
                                <td>มีค่าใช้จ่าย</td>
                                <td>ตามเวลาตลาดจริง</td>
                                <td>ไม่ระบุ</td>
                            </tr>
                            <tr>
                                <td>Cryptocurrencies</td>
                                <td>0.8 pips</td>
                                <td>1:100</td>
                                <td>0%</td>
                                <td>มีค่าใช้จ่าย</td>
                                <td>24/7</td>
                                <td>ไม่ระบุ</td>
                            </tr>
                            <tr>
                                <td>ETFs</td>
                                <td>1 pip</td>
                                <td>1:5</td>
                                <td>0%</td>
                                <td>มีค่าใช้จ่าย</td>
                                <td>ตามเวลาตลาดจริง</td>
                                <td>ไม่ระบุ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="card-text mt-4">หมายเหตุ: ข้อมูลค่าคอมมิชชั่นและค่า Swap อาจแตกต่างกันไปตามประเภทบัญชีและเงื่อนไขเฉพาะ. โปรดตรวจสอบตารางเงื่อนไขการเทรดบน Deriv.com สำหรับข้อมูลที่ถูกต้องและเป็นปัจจุบัน.</p>
            </div>
        </section>

        <section id="options" class="mb-16">
            <h2 class="section-title">สำรวจ Options: ความเสี่ยงจำกัด กลยุทธ์หลากหลาย</h2>
            <p class="section-intro">การเทรด Options บน Deriv มีจุดเด่นที่ความเสี่ยงจำกัดอยู่เพียงเงินลงทุนเริ่มต้น (Stake) สามารถเริ่มต้นเทรดด้วยเงินลงทุนเพียง $0.35 และมียอดเงินในบัญชีขั้นต่ำ $5 โดยมีประเภทสัญญาหลากหลายและระยะเวลาที่ยืดหยุ่น (ตั้งแต่ 1 วินาทีถึง 365 วัน) เพื่อตอบโจทย์มุมมองตลาดและเป้าหมายกำไรที่แตกต่างกัน</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card md:col-span-3">
                    <h3 class="card-title palette-quaternary">1. Digital Options</h3>
                    <p class="card-text">เป็นรูปแบบที่ง่ายที่สุดที่ผู้เทรดคาดการณ์ผลลัพธ์แบบไบนารี (ใช่/ไม่ใช่) เกี่ยวกับการเคลื่อนไหวของราคา หากการคาดการณ์ถูกต้อง จะได้รับเงินรางวัลคงที่ที่กำหนดไว้ล่วงหน้า.</p>
                    <ul class="list-disc list-inside mt-2 text-gray-700">
                        <li>**ตัวอย่าง:** Higher/Lower (ราคาจะสูงกว่า/ต่ำกว่า Barrier), Matches/Differs (ตัวเลขหลักสุดท้ายจะตรง/ต่างจากที่เลือก).</li>
                        <li>**เงื่อนไขชนะ:** การคาดการณ์ถูกต้องตามเงื่อนไขของสัญญา (เช่น ราคาอยู่เหนือ Barrier ที่เลือก ณ วันหมดอายุ).</li>
                        <li>**เงื่อนไขแพ้:** การคาดการณ์ไม่ถูกต้อง ขาดทุนจำกัดที่เงินลงทุนเริ่มต้น.</li>
                    </ul>
                </div>
                <div class="card md:col-span-3">
                    <h3 class="card-title palette-primary">2. Accumulator Options</h3>
                    <p class="card-text">อนุญาตให้ผลตอบแทนเพิ่มขึ้นแบบทบต้นสูงสุด 5% ต่อ Tick ตราบใดที่ราคาของสินทรัพย์ยังคงอยู่ในช่วงที่กำหนด (Range) เหมาะสำหรับการทำกำไรจากตลาดที่มีความเสถียรหรือเคลื่อนไหวในกรอบ (Sideways Markets).</p>
                    <ul class="list-disc list-inside mt-2 text-gray-700">
                        <li>**การเทรด:** เลือกอัตราการเติบโต ซึ่งจะกำหนดช่วงราคา (Barriers) สามารถตั้งค่า Take Profit หรือปิดการเทรดด้วยตนเอง.</li>
                        <li>**เงื่อนไขชนะ:** ราคาสินทรัพย์ยังคงอยู่ในช่วงที่กำหนด (ภายใน Upper และ Lower Barriers) การจ่ายเงินจะทบต้นทุก Tick.</li>
                        <li>**เงื่อนไขแพ้:** ราคาสินทรัพย์เคลื่อนไหวแตะหรือทะลุ Barrier ที่กำหนด (Knockout) ขาดทุนจำกัดที่เงินลงทุนเริ่มต้น.</li>
                    </ul>
                </div>
                <div class="card md:col-span-3">
                    <h3 class="card-title palette-tertiary">3. Vanilla Options</h3>
                    <p class="card-text">เป็นสัญญาที่ให้สิทธิ์ (แต่ไม่ใช่ภาระผูกพัน) ในการซื้อ (Call Option) หรือขาย (Put Option) สินทรัพย์อ้างอิงในราคาที่กำหนดไว้ล่วงหน้า (Strike Price) ก่อนวันหมดอายุ.</p>
                    <ul class="list-disc list-inside mt-2 text-gray-700">
                        <li>**การเทรด:** เลือก "Call" หากคาดว่าราคาจะสูงขึ้น หรือ "Put" หากคาดว่าราคาจะลดลง กำหนด Strike Price และระยะเวลาสัญญา.</li>
                        <li>**เงื่อนไขชนะ:** ราคาของสินทรัพย์สูงกว่า Strike Price สำหรับ Call หรือต่ำกว่า Strike Price สำหรับ Put ณ วันหมดอายุ มีศักยภาพกำไรไม่จำกัด.</li>
                        <li>**เงื่อนไขแพ้:** ราคาไม่เคลื่อนไหวในทิศทางที่คาดการณ์ หรือไม่ถึง/เกิน Strike Price ขาดทุนจำกัดที่เงินลงทุนเริ่มต้น.</li>
                    </ul>
                </div>
                <div class="card md:col-span-3">
                    <h3 class="card-title palette-accent">4. Turbo Options</h3>
                    <p class="card-text">คล้ายกับ Vanilla Options แต่มีคุณสมบัติ "Knockout" ได้รับการจ่ายเงินหากการคาดการณ์ถูกต้องและราคา *ไม่แตะหรือทะลุ* Barrier ที่กำหนดไว้ล่วงหน้าตลอดระยะเวลาสัญญา.</p>
                    <ul class="list-disc list-inside mt-2 text-gray-700">
                        <li>**การเทรด:** เลือก "Up" หรือ "Down" กำหนด Barrier ที่ใกล้ราคาปัจจุบันเพื่อศักยภาพกำไรที่สูงขึ้น สามารถตั้งค่า Take Profit.</li>
                        <li>**เงื่อนไขชนะ:** ราคาเคลื่อนไหวในทิศทางที่คาดการณ์ และที่สำคัญคือ ราคาต้อง *ไม่แตะหรือทะลุ* Barrier ที่กำหนดไว้ตลอดระยะเวลาสัญญา.</li>
                        <li>**เงื่อนไขแพ้:** ราคาแตะหรือทะลุ Barrier ที่กำหนดไว้ สัญญาจะถูกยกเลิกก่อนกำหนด ขาดทุนจำกัดที่เงินลงทุนเริ่มต้น.</li>
                    </ul>
                </div>
                <div class="card md:col-span-3">
                    <h3 class="card-title palette-primary">5. Multipliers</h3>
                    <p class="card-text">ผสมผสานประโยชน์ของเลเวอเรจเข้ากับความเสี่ยงที่จำกัด ช่วยขยายศักยภาพกำไรได้สูงสุดถึง 2,000 เท่า หากตลาดเคลื่อนไหวในทิศทางที่คาดการณ์.</p>
                    <ul class="list-disc list-inside mt-2 text-gray-700">
                        <li>**การเทรด:** เลือกค่า Multiplier ที่ต้องการ (เช่น 30x ถึง 2000x) และทิศทางการเคลื่อนไหวของราคา (Up/Down) สามารถตั้งค่า Take Profit, Stop Loss และ Deal Cancellation.</li>
                        <li>**เงื่อนไขชนะ:** ราคาเคลื่อนไหวในทิศทางที่คาดการณ์ กำไรจะถูกคูณด้วยค่า Multiplier.</li>
                        <li>**เงื่อนไขแพ้:** ราคาเคลื่อนไหวสวนทางกับที่คาดการณ์ ขาดทุนจะถูกจำกัดที่เงินลงทุนเริ่มต้น โดยมีฟังก์ชัน Stop-out อัตโนมัติ.</li>
                        <li>**Deal Cancellation:** คุณสมบัติเสริมที่ช่วยให้สามารถยกเลิกการเทรดได้ภายในเวลาที่กำหนด (เช่น 5-60 นาที) หากราคาถึง Stop-out level โดยได้รับเงินลงทุนคืน (มีค่าธรรมเนียม) แต่จะไม่สามารถใช้ Stop-Loss และ Take-Profit ได้เมื่อเปิดใช้งาน.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="advantages" class="mb-16">
            <h2 class="section-title">จุดเด่นที่เป็นเอกลักษณ์ของ Deriv.com</h2>
            <p class="section-intro">นอกเหนือจากความหลากหลายของตลาดและแพลตฟอร์มแล้ว Deriv ยังมีคุณสมบัติและบริการที่เป็นเอกลักษณ์ ซึ่งช่วยเพิ่มความน่าสนใจและโอกาสให้กับเทรดเดอร์</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card text-center">
                    <div class="text-5xl mb-3">⏱️</div>
                    <h3 class="card-title justify-center">Derived Indices 24/7</h3>
                    <p class="card-text">เทรดดัชนีสังเคราะห์ได้ตลอดเวลา แม้ในวันหยุดสุดสัปดาห์ ไม่ขึ้นกับข่าวสารตลาดโลก ทำให้สามารถมุ่งเน้นไปที่การวิเคราะห์ทางเทคนิคได้.</p>
                </div>
                <div class="card text-center">
                    <div class="text-5xl mb-3">🚫🔄</div>
                    <h3 class="card-title justify-center">Swap-Free Accounts</h3>
                    <p class="card-text">บัญชีเทรด Forex บางประเภทไม่มีค่า Swap สำหรับการถือครองตำแหน่งข้ามคืน ช่วยลดต้นทุนสำหรับเทรดเดอร์ที่ถือครองตำแหน่งระยะยาว.</p>
                </div>
                <div class="card text-center">
                    <div class="text-5xl mb-3">🎓</div>
                    <h3 class="card-title justify-center">Deriv Academy</h3>
                    <p class="card-text">แหล่งข้อมูลการเรียนรู้ฟรี ช่วยพัฒนาความรู้และทักษะการเทรดสำหรับทุกระดับ ตั้งแต่พื้นฐานไปจนถึงกลยุทธ์ขั้นสูง.</p>
                </div>
            </div>
        </section>

        <section id="conclusion" class="text-center py-12 bg-gray-100 rounded-lg">
            <h2 class="section-title">สรุป: การเทรดอย่างมีข้อมูลบน Deriv.com</h2>
            <p class="section-intro max-w-3xl">Deriv.com นำเสนอเครื่องมือและโอกาสที่หลากหลายสำหรับการเทรดในตลาดการเงิน ไม่ว่าจะเป็น CFD ที่มีเลเวอเรจสูง หรือ Options ที่มีความเสี่ยงจำกัด ความสำเร็จในการเทรดขึ้นอยู่กับการทำความเข้าใจในแต่ละผลิตภัณฑ์ การบริหารความเสี่ยงอย่างรอบคอบ และการเรียนรู้อย่างต่อเนื่อง Deriv มุ่งมั่นที่จะเป็นแพลตฟอร์มที่สนับสนุนการตัดสินใจลงทุนอย่างมีข้อมูลของเทรดเดอร์ทุกคน</p>
            <a href="https://deriv.com" target="_blank" class="mt-6 inline-block bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-8 rounded-lg shadow-md transition duration-300">
                เรียนรู้เพิ่มเติมที่ Deriv.com
            </a>
        </section>

    </div>

    <script>
        function formatLabel(str, maxLength = 16) {
            if (typeof str !== 'string') return str;
            if (str.length <= maxLength) return str;

            const words = str.split(' ');
            const lines = [];
            let currentLine = '';

            for (const word of words) {
                if ((currentLine + word).length > maxLength && currentLine.length > 0) {
                    lines.push(currentLine.trim());
                    currentLine = '';
                }
                currentLine += word + ' ';
            }
            if (currentLine.trim().length > 0) {
                lines.push(currentLine.trim());
            }
            return lines.length > 0 ? lines : [str];
        }
        
        const tooltipTitleCallback = function(tooltipItems) {
            const item = tooltipItems[0];
            let label = item.chart.data.labels[item.dataIndex];
            if (Array.isArray(label)) {
                return label.join(' ');
            } else {
                return label;
            }
        };

        const commonChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#4A5568'
                    }
                },
                tooltip: {
                    callbacks: {
                        title: tooltipTitleCallback
                    },
                    backgroundColor: 'rgba(26, 32, 44, 0.9)',
                    titleColor: '#FFFFFF',
                    bodyColor: '#E2E8F0',
                    titleFont: { weight: 'bold', size: 14 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 4,
                    displayColors: true
                }
            },
            scales: {
                x: {
                    ticks: { 
                        color: '#4A5568',
                        font: { size: 11 }
                    },
                    grid: { display: false }
                },
                y: {
                    ticks: { 
                        color: '#4A5568',
                        font: { size: 11 }
                    },
                    grid: { color: '#E2E8F0' }
                }
            }
        };
        
        const marketCtx = document.getElementById('marketDistributionChart').getContext('2d');
        const marketData = {
            labels: ['Forex', ['ดัชนี', 'สังเคราะห์'], ['หุ้น &', 'ดัชนีหุ้น'], ['สินค้า', 'โภคภัณฑ์'], ['สกุลเงิน', 'ดิจิทัล'], 'ETFs'].map(label => Array.isArray(label) ? label : formatLabel(label)),
            datasets: [{
                label: 'สัดส่วนตลาด',
                data: [35, 25, 15, 10, 10, 5],
                backgroundColor: [
                    '#FF7043',
                    '#26A69A',
                    '#7E57C2',
                    '#42A5F5',
                    '#FFEE58',
                    '#AB47BC'
                ],
                borderColor: '#FFFFFF',
                borderWidth: 2,
                hoverOffset: 4
            }]
        };
        new Chart(marketCtx, {
            type: 'doughnut',
            data: marketData,
            options: {
                ...commonChartOptions,
                cutout: '60%',
                 scales: { x: { display: false }, y: { display: false } }
            }
        });

        const leverageCtx = document.getElementById('leverageChart').getContext('2d');
        const leverageData = {
            labels: ['Forex', ['ดัชนี', 'สังเคราะห์'], 'หุ้น', ['ดัชนีหุ้น'], ['สินค้า', 'โภคภัณฑ์'], ['สกุลเงิน', 'ดิจิทัล'], 'ETFs'].map(label => Array.isArray(label) ? label : formatLabel(label, 12)),
            datasets: [{
                label: 'เลเวอเรจสูงสุด (เท่า)',
                data: [1000, 4000, 50, 100, 500, 100, 5],
                backgroundColor: [
                    '#FF7043', '#26A69A', '#7E57C2', '#42A5F5', '#FFEE58', '#AB47BC', '#FFCA28'
                ],
                borderColor: [
                    '#E64A19', '#00796B', '#5E35B1', '#1E88E5', '#FBC02D', '#8E24AA', '#FFA000'
                ],
                borderWidth: 1,
                borderRadius: 4,
                barPercentage: 0.7,
                categoryPercentage: 0.8
            }]
        };
        new Chart(leverageCtx, {
            type: 'bar',
            data: leverageData,
            options: {
                ...commonChartOptions,
                indexAxis: 'y',
                 scales: {
                    x: {
                        ticks: { 
                            color: '#4A5568',
                            font: { size: 11 },
                            callback: function(value) { return value.toLocaleString(); }
                        },
                        grid: { color: '#E2E8F0' },
                        title: {
                            display: true,
                            text: 'จำนวนเท่าของเลเวอเรจ',
                            color: '#1A202C',
                            font: { weight: '600' }
                        }
                    },
                    y: {
                        ticks: { 
                            color: '#4A5568',
                            font: { size: 11 }
                        },
                        grid: { display: false }
                    }
                },
                plugins: {
                    ...commonChartOptions.plugins,
                    legend: { display: false }
                }
            }
        });
    </script>

</body>
</html>
