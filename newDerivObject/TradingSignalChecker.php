<?php

class TradingSignalChecker {
    // ค่า EMA และข้อมูลแท่งเทียน
    private $ema3;
    private $ema5;
    private $emaDiff; // EMA3 - EMA5
    private $candle; // ข้อมูลแท่งเทียนปัจจุบัน
    
    public function __construct($ema3, $ema5, $candle) {
        $this->ema3 = $ema3;
        $this->ema5 = $ema5;
        $this->emaDiff = $ema3 - $ema5;
        $this->candle = $candle;
    }
    
    /**
     * ตรวจสอบว่าตลาดอยู่ในภาวะที่ควรหลีกเลี่ยงการเทรดหรือไม่
     */
    public function shouldAvoidTrading() {
        return $this->isWhipsawMarket() || 
               $this->isDojiOrSpinningTop() || 
               $this->isOverextendedTrend() || 
               $this->isWeakMomentum() || 
               $this->isSidewayMarket() || 
               $this->hasFalseRejection();
    }
    
    /**
     * 1. ตลาดตัดกันบ่อย (Whipsaw)
     */
    private function isWhipsawMarket() {
        // ถ้า EMA3 และ EMA5 ใกล้กันมากและสลับตำแหน่งบ่อย (ในข้อมูลจริงอาจต้องดูหลายช่วงเวลา)
        return abs($this->emaDiff) < ($this->ema5 * 0.001); // ความต่างน้อยกว่า 0.1% ของ EMA5
    }
    
    /**
     * 2. แท่งเทียน Doji หรือ Spinning Top
     */
    private function isDojiOrSpinningTop() {
        $bodySize = abs($this->candle['close'] - $this->candle['open']);
        $totalRange = $this->candle['high'] - $this->candle['low'];
        
        // Doji: body size น้อยมากเมื่อเทียบกับ range
        $isDoji = ($bodySize / $totalRange) < 0.05;
        
        // Spinning Top: body เล็กและมี wick ทั้งสองด้าน
        $upperWick = $this->candle['high'] - max($this->candle['open'], $this->candle['close']);
        $lowerWick = min($this->candle['open'], $this->candle['close']) - $this->candle['low'];
        $isSpinningTop = ($bodySize / $totalRange) < 0.3 && 
                        ($upperWick / $totalRange) > 0.3 && 
                        ($lowerWick / $totalRange) > 0.3;
        
        return $isDoji || $isSpinningTop;
    }
    
    /**
     * 3. แนวโน้มเกินขอบเขต (Overextended)
     */
    private function isOverextendedTrend() {
        $emaDiffPercentage = abs($this->emaDiff) / $this->ema5;
        
        // ถ้า EMA3 ห่างจาก EMA5 มากกว่า 2% (ปรับตามสไตล์การเทรด)
        return $emaDiffPercentage > 0.02;
    }
    
    /**
     * 4. แรงซื้อ/ขายอ่อน (Weak Momentum)
     */
    private function isWeakMomentum() {
        $distanceToEMA3 = abs($this->candle['close'] - $this->ema3);
        $distanceToEMA5 = abs($this->candle['close'] - $this->ema5);
        
        // ปิดใกล้ EMA มาก (น้อยกว่า 0.2% ของราคา)
        return ($distanceToEMA3 / $this->candle['close']) < 0.002 || 
               ($distanceToEMA5 / $this->candle['close']) < 0.002;
    }
    
    /**
     * 5. ตลาด Sideway (EMA แนบกัน)
     */
    private function isSidewayMarket() {
        // EMA3 และ EMA5 ใกล้กันมากและเคลื่อนที่ขนาน
        // (ในทางปฏิบัติอาจต้องดูข้อมูลย้อนหลังหลายช่วง)
        return abs($this->emaDiff) < ($this->ema5 * 0.005); // ความต่างน้อยกว่า 0.5%
    }
    
    /**
     * 6. มีการปฏิเสธ (Rejection) แต่ไม่มีการตามมา
     */
    private function hasFalseRejection() {
        $upperWick = $this->candle['high'] - max($this->candle['open'], $this->candle['close']);
        $lowerWick = min($this->candle['open'], $this->candle['close']) - $this->candle['low'];
        $totalRange = $this->candle['high'] - $this->candle['low'];
        
        // Wick ยาวด้านใดด้านหนึ่ง (มากกว่า 50% ของ range)
        $hasLongUpperWick = ($upperWick / $totalRange) > 0.5;
        $hasLongLowerWick = ($lowerWick / $totalRange) > 0.5;
        
        // แต่ body ไม่ได้แสดงทิศทางที่ชัดเจน
        $bodySize = abs($this->candle['close'] - $this->candle['open']);
        $weakBody = ($bodySize / $totalRange) < 0.3;
        
        return ($hasLongUpperWick || $hasLongLowerWick) && $weakBody;
    }
    
    /**
     * แสดงผลลัพธ์การตรวจสอบ
     */
    public function getAnalysisResult() {
        $reasons = [];
        
        if ($this->isWhipsawMarket()) $reasons[] = "EMA3 และ EMA5 ตัดกันบ่อย (ตลาด Sideway)";
        if ($this->isDojiOrSpinningTop()) $reasons[] = "พบแท่งเทียน Doji/Spinning Top (ตลาดลังเล)";
        if ($this->isOverextendedTrend()) $reasons[] = "แนวโน้มเกินขอบเขต (อาจเกิด Pullback)";
        if ($this->isWeakMomentum()) $reasons[] = "แรงซื้อ/ขายอ่อน (ปิดใกล้เส้น EMA)";
        if ($this->isSidewayMarket()) $reasons[] = "ตลาด Sideway (EMA แนบกัน)";
        if ($this->hasFalseRejection()) $reasons[] = "มีสัญญาณ Rejection แต่ไม่มีการ Confirm";
        
        if (empty($reasons)) {
            return "ไม่พบสัญญาณเสี่ยงหลัก ควรตรวจสอบปัจจัยอื่นเพิ่มเติม";
        } else {
            return "ควรหลีกเลี่ยงการเทรดเนื่องจาก: " . implode(", ", $reasons);
        }
    }
}

// ตัวอย่างการใช้งาน
$currentCandle = [
    'open' => 100.50,
    'high' => 101.20,
    'low' => 100.10,
    'close' => 100.80
];

$ema3 = 100.70;
$ema5 = 100.65;

$signalChecker = new TradingSignalChecker($ema3, $ema5, $currentCandle);

echo "ผลการวิเคราะห์:<br>";
echo $signalChecker->getAnalysisResult();

if ($signalChecker->shouldAvoidTrading()) {
    echo "<br><br>คำแนะนำ: ควรหลีกเลี่ยงการเข้าเทรดในจุดนี้";
} else {
    echo "<br><br>คำแนะนำ: สามารถพิจารณาเข้าเทรด (ตรวจสอบปัจจัยอื่นร่วมด้วย)";
}


/*
ในการวิเคราะห์กราฟด้วย EMA3, EMA5 และความต่างระหว่าง EMA3-EMA5 รวมถึงลักษณะแท่งเทียน (Candlestick Patterns) จุดที่ไม่ควรเข้าเทรด (หรือควรหลีกเลี่ยง) มักมีลักษณะดังนี้:

1. กรณีที่ EMA3 และ EMA5 ตัดกันบ่อย (Whipsaw)
ลักษณะ: เมื่อ EMA3 และ EMA5 ตัดขึ้น-ลงบ่อยในระยะสั้น แสดงว่า ตลาดไม่มีแนวโน้มชัดเจน หรืออยู่ในช่วง Sideway

สัญญาณเสี่ยง: การเข้าเทรดตามสัญญาณตัดกันอาจทำให้ถูก Stop Loss บ่อย

วิธีจัดการ: รอให้ตลาดมีแนวโน้มชัดเจนก่อน (EMA3 และ EMA5 ห่างกันพอสมควร)

2. แท่งเทียนมี Wick ยาวทั้งสองด้าน (Doji, Spinning Top)
ลักษณะ: แท่งเทียนมีร่างกายเล็ก (Small Body) และมี Wick ยาวทั้งบน-ล่าง แสดงว่า ตลาดลังเล ไม่มีทิศทางชัดเจน

สัญญาณเสี่ยง: อาจเกิดการกลับตัวหรือเคลื่อนไหวรุนแรงแบบสุ่ม

ตัวอย่าง:

Doji หลังแนวโน้มขึ้น/ลง อาจหมายถึงการเปลี่ยนแนวโน้ม

Spinning Top ในช่วง Sideway บ่งชี้ความไม่แน่นอน

3. EMA3 และ EMA5 ห่างกันมากเกินไป (Overextended Trend)
ลักษณะ: เมื่อ EMA3 ห่างจาก EMA5 มาก (เช่น EMA3-EMA5 มีค่าสูงมากในขาขึ้น/ต่ำมากในขาลง) แสดงว่า ตลาดอาจ Overbought/Oversold

สัญญาณเสี่ยง: อาจเกิด Pullback หรือ Correction ในทันที

วิธีจัดการ: รอให้เส้น EMA เข้ามาใกล้กันก่อน (Pullback) แล้วค่อยตัดสินใจ

4. แท่งเทียนปิดใกล้กันกับ EMA (ไม่มี Momentum)
ลักษณะ: แท่งเทียนปิดใกล้หรือแทบไม่ห่างจาก EMA3/EMA5 แสดงว่า แรงซื้อ/ขายอ่อนลง

สัญญาณเสี่ยง: อาจไม่มีการเคลื่อนไหวต่อเนื่อง

ตัวอย่าง:

ในขาขึ้น: แท่งเทียนปิดใกล้ EMA5 โดยไม่มีแรงซื้อเพิ่ม

ในขาลง: แท่งเทียนปิดใกล้ EMA5 โดยไม่มีแรงขายเพิ่ม

5. กราฟอยู่ในช่วง Sideway (ไม่มีแนวโน้ม)
ลักษณะ: EMA3 และ EMA5 เคลื่อนที่ขนานกันและใกล้กันมาก แท่งเทียนขึ้น-ลงสลับกันในกรอบแคบ

สัญญาณเสี่ยง: การเข้าเทรดตามสัญญาณตัดกันมักให้ผลลัพธ์ไม่ดี

วิธีจัดการ: รอให้ EMA3 และ EMA5 แยกห่างกันชัดเจน หรือราคา Breakout จากกรอบ Sideway

6. แท่งเทียนมี Wick ยาวด้านเดียว (Rejection) แต่ไม่มีการตามมา
ลักษณะ:

ขาขึ้น: แท่งเทียนมี Wick ยาวด้านบน (การถูกปฏิเสธที่ Resistance) แต่แท่งถัดไปไม่สามารถ突破ได้

ขาลง: แท่งเทียนมี Wick ยาวด้านล่าง (การถูกปฏิเสธที่ Support) แต่แท่งถัดไปไม่สามารถ跌破ได้

สัญญาณเสี่ยง: การเข้าเทรดตามสัญญาณ Rejection อาจ失敗หากไม่มี Confirm เพิ่ม

สรุปจุดที่ไม่ควรเข้าเทรด
ลักษณะ	ความเสี่ยง
EMA3 และ EMA5 ตัดกันบ่อย (Whipsaw)	สัญญาณหลอกในตลาด Sideway
แท่งเทียน Doji/Spinning Top	ตลาดลังเล ไม่มีทิศทาง
EMA3 และ EMA5 ห่างกันมากเกินไป	โอกาส Pullback สูง
แท่งเทียนปิดใกล้เส้น EMA	แรงซื้อ/ขายอ่อน
กราฟ Sideway (EMA แนบกัน)	ไม่มีแนวโน้มชัดเจน
Wick ยาวแต่ไม่มีการ Confirm	อาจเกิด False Signal
คำแนะนำเพิ่มเติม
ใช้ร่วมกับ Indicators อื่น: เช่น RSI (เพื่อดู Overbought/Oversold), Volume (ยืนยันแรงซื้อ/ขาย)

รอ Confirm: หลังจากเห็นสัญญาณเสี่ยง ให้รอแท่งเทียน Confirm ทิศทางก่อน (เช่น แท่งสีเขียวแข็งแรงหลัง Pullback ในขาขึ้น)

หากหลีกเลี่ยงจุดเหล่านี้ได้ จะช่วยลดการเข้าเทรดที่เสี่ยงและเพิ่มโอกาสทำกำไรได้มากขึ้นครับ!

*/
?>

