<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Document</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> 

  <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tempus Dominus CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    
    <!-- Font Awesome for calendar icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
<style>
table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            cursor: pointer;
        }
        th {
            background-color: #f4f4f4;
        }
        .highlight {
            background-color: yellow;
        }
        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }
 
.sarabun-regular {
  font-family: "Sarabun", sans-serif;
  font-weight: 400;
  font-style: normal;
}

</style>



 </head>
 <body class='sarabun-regular'>
  
  <h1>สารบัญ Font </h1>
<?php
  
   $stData = "";   
   $sFileName = 'dataTest.json';
   $file = fopen($sFileName,"r");
   while(! feof($file))  {
     $stData .= fgets($file) ;
   }
   fclose($file);
  
?>
<textarea id="dataTest" rows="" cols=""><?=$stData;?></textarea>

<div class="container mt-5">
        <h3>เลือกวันที่ (วว/ดด/ปป)</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group date" id="datetimepicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker" placeholder="เลือกวันที่"/>
                    <div class="input-group-append" data-target="#datetimepicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<textarea id="tradeResult" style='width:100%;height:200px' rows="" cols=""></textarea>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>   
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

   <!-- Required JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/th.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Set moment locale to Thai
            moment.locale('th');
            
            // Initialize datepicker
            $('#datetimepicker').datetimepicker({
                format: 'L',
                locale: 'th',
                icons: {
                    time: 'fas fa-clock',
                    date: 'fas fa-calendar',
                    up: 'fas fa-arrow-up',
                    down: 'fas fa-arrow-down',
                    previous: 'fas fa-chevron-left',
                    next: 'fas fa-chevron-right',
                    today: 'fas fa-calendar-check',
                    clear: 'fas fa-trash',
                    close: 'fas fa-times'
                },
                buttons: {
                    showToday: true,
                    showClear: true,
                    showClose: true
                },
                tooltips: {
                    today: 'วันนี้',
                    clear: 'ล้าง',
                    close: 'ปิด',
                    selectMonth: 'เลือกเดือน',
                    prevMonth: 'เดือนก่อนหน้า',
                    nextMonth: 'เดือนถัดไป',
                    selectYear: 'เลือกปี',
                    prevYear: 'ปีก่อนหน้า',
                    nextYear: 'ปีถัดไป',
                    selectDecade: 'เลือกทศวรรษ',
                    prevDecade: 'ทศวรรษก่อนหน้า',
                    nextDecade: 'ทศวรรษถัดไป',
                    prevCentury: 'ศตวรรษก่อนหน้า',
                    nextCentury: 'ศตวรรษถัดไป'
                }
            });
        });
    </script>



<script>
// ข้อมูลตัวอย่าง
/*
const data = [
    { 
        "candleID": 1741138380,  
        "id": "94",
        "timefrom_unix": "08:33",
        "emaAbove": "5",
        "emaConflict": "N",
        "thisColor": "Red",
        "CutPointType": "3->5"
    },
    { 
        "candleID": 1741138381,  
        "id": "95",
        "timefrom_unix": "08:34",
        "emaAbove": "3",
        "emaConflict": "Y",
        "thisColor": "Red",
        "CutPointType": "N"
    },
    { 
        "candleID": 1741138382,  
        "id": "96",
        "timefrom_unix": "08:35",
        "emaAbove": "5",
        "emaConflict": "Y",
        "thisColor": "Green",
        "CutPointType": "5->3"
    }
];
*/
data = JSON.parse(document.getElementById("dataTest").value);

 

// กำหนดค่าคงที่ของระบบ
const MAX_TRADES = 4;                     // จำนวนการเทรดสูงสุดต่อจุดเริ่มเทรด
const INITIAL_STAKE = 1;                  // เงินเริ่มต้น (USD)
let  MARTINGALE_PROGRESSION = [1, 2, 6, 11, 15, 30]; // ตารางเดินเงินเมื่อแพ้
MARTINGALE_PROGRESSION = [1, 1, 1, 1, 1, 1]; // ตารางเดินเงินเมื่อแพ้

// ฟังก์ชันหา SuggestColor จาก CutPointType
function getSuggestColorFromCutPoint(cutPointType) {

    if (cutPointType === '3->5') {
        return 'Red';
    } else if (cutPointType === '5->3') {
        return 'Green';
    }
    return null;
}

// ฟังก์ชันหา SuggestColor จาก emaAbove และ emaConflict
function getSuggestColorFromEma(emaAbove, emaConflict) {

    if (emaConflict === 'Y') {
        return 'Idle';
    } else if (emaAbove === '5') {
        return 'Red';
    } else if (emaAbove === '3') {
        return 'Green';
    }
    return null;
}

// ฟังก์ชันคำนวณเทรด
function calculateTrades(data) {
    const tradeResults = [];
    const tradedIDs = new Set();  // เก็บ ID ที่เทรดไปแล้ว
    let totalIdleCount = 0;
    let idleBeforeWin = 0;
    let idleBeforeLoss = 0;
    
    // ข้อมูลการเงิน
    let initialBalance = 1;  // สมมติว่าเริ่มต้นด้วยเงิน 100 USD
    let currentBalance = initialBalance;
    let totalProfit = 0;
    let maxDrawdown = 0;
    let highestBalance = initialBalance;
    let lowestBalance = initialBalance;
    let consecutiveLosses = 0;  // ใช้ติดตามจำนวนครั้งที่แพ้ติดต่อกัน
    
    // วนลูปหาจุดเริ่มเทรด
    for (let i = 0; i < data.length; i++) {
        const candle = data[i];
        
        // เงื่อนไขที่ 1: ตรวจสอบว่าเป็นจุดเริ่มเทรดหรือไม่ (CutPointType = '3->5' หรือ '5->3')
        if ((candle.CutPointType === '3->5' || candle.CutPointType === '5->3') && !tradedIDs.has(candle.id)) {
            // เงื่อนไขที่ 2: หา SuggestColor จาก CutPointType
            const suggestColor = getSuggestColorFromCutPoint(candle.CutPointType);
            
            // สร้าง object เก็บผลการเทรด
            const tradeResult = {
                entryPoint: {
                    candleID: candle.candleID,
                    id: candle.id,
                    time: candle.timefrom_unix,
                    cutPointType: candle.CutPointType
                },
                suggestColor: suggestColor,
                trades: [],
                actualTradeCount: 0,  // จำนวนการเทรดจริง (ไม่รวม Idle)
                totalEvents: 0,       // จำนวนเหตุการณ์ทั้งหมดรวม Idle
                idleCount: 0,
                result: null,
                financialSummary: {
                    startBalance: currentBalance,
                    endBalance: null,
                    profit: null,
                    stakes: []
                }
            };
            
            // เริ่มตรวจสอบแท่งถัดไป
            let currentSuggestColor = suggestColor;
            let lossCount = 0;
            let idleCount = 0;
            let j = i + 1;
            let isWin = false;
            let actualTradeCount = 0;  // นับจำนวนการเทรดจริง (ไม่รวม Idle)
            let localConsecutiveLosses = consecutiveLosses;  // ใช้ติดตามจำนวนแพ้ติดกันสำหรับการคำนวณเงินเดิมพัน
            
            // เพิ่ม ID ของจุดเริ่มเทรดเข้าไปใน tradedIDs
            tradedIDs.add(candle.id);
            
            while (j < data.length && !isWin && actualTradeCount < MAX_TRADES) {
                const nextCandle = data[j];
                
                // เพิ่ม ID ของแท่งที่กำลังพิจารณาเข้าไปใน tradedIDs
                tradedIDs.add(nextCandle.id);
                
                // คำนวณ SuggestColor สำหรับแท่งนี้
                // ถ้าแท่งก่อนหน้าเป็น Idle และให้ skip การเทรด
                if (currentSuggestColor === 'Idle') {
                    idleCount++;
                    // ปรับ SuggestColor ใหม่สำหรับแท่งถัดไป
                    currentSuggestColor = getSuggestColorFromEma(nextCandle.emaAbove, nextCandle.emaConflict);
                    
                    const idleRecord = {
                        candleID: nextCandle.candleID,
                        id: nextCandle.id,
                        time: nextCandle.timefrom_unix,
                        status: 'Idle',
                        suggestColor: 'Idle',
                        actualColor: nextCandle.thisColor,
                        newSuggestColor: currentSuggestColor
                    };
                    
                    tradeResult.trades.push(idleRecord);
                } else {
                    // บันทึกการเทรดแต่ละครั้ง
                    actualTradeCount++;  // เพิ่มจำนวนการเทรดจริง
                    
                    // คำนวณเงินเดิมพัน
                    const martingaleIndex = Math.min(localConsecutiveLosses, MARTINGALE_PROGRESSION.length - 1);
                    const stakeFactor = MARTINGALE_PROGRESSION[martingaleIndex];
                    const stakeAmount = INITIAL_STAKE * stakeFactor;
                    
                    const isWinThisTrade = currentSuggestColor === nextCandle.thisColor;
                    const profitLoss = isWinThisTrade ? stakeAmount : -stakeAmount;
                    currentBalance += profitLoss;
                    
                    // ตรวจสอบ highest/lowest balance
                    if (currentBalance > highestBalance) {
                        highestBalance = currentBalance;
                    }
                    if (currentBalance < lowestBalance) {
                        lowestBalance = currentBalance;
                        const drawdown = initialBalance - lowestBalance;
                        maxDrawdown = Math.max(maxDrawdown, drawdown);
                    }
                    
                    const tradeAttempt = {
                        candleID: nextCandle.candleID,
                        id: nextCandle.id,
                        time: nextCandle.timefrom_unix,
                        status: 'Trade',
                        tradeNumber: actualTradeCount,  // เพิ่มลำดับของการเทรด
                        suggestColor: currentSuggestColor,
                        actualColor: nextCandle.thisColor,
                        result: isWinThisTrade ? 'Win' : 'Loss',
                        stake: stakeAmount,
                        profitLoss: profitLoss,
                        balance: currentBalance,
                        consecutiveLosses: localConsecutiveLosses
                    };
                    
                    tradeResult.financialSummary.stakes.push({
                        tradeNumber: actualTradeCount,
                        stake: stakeAmount,
                        result: isWinThisTrade ? 'Win' : 'Loss',
                        profitLoss: profitLoss,
                        balance: currentBalance
                    });
                    
                    tradeResult.trades.push(tradeAttempt);
                    
                    // เงื่อนไขที่ 3: ตรวจสอบผลการเทรด
                    if (isWinThisTrade) {
                        // ชนะ
                        isWin = true;
                        tradeResult.result = 'Win';
                        tradeResult.lossBeforeWin = lossCount;
                        tradeResult.idleBeforeWin = idleCount;
                        idleBeforeWin += idleCount;
                        tradeResult.winOnTradeNumber = actualTradeCount;
                        
                        // รีเซ็ตจำนวนครั้งที่แพ้ติดต่อกัน
                        consecutiveLosses = 0;
                    } else {
                        // แพ้ - ปรับ SuggestColor สำหรับแท่งถัดไป
                        lossCount++;
                        consecutiveLosses++;
                        localConsecutiveLosses++;
                        currentSuggestColor = getSuggestColorFromEma(nextCandle.emaAbove, nextCandle.emaConflict);
                        tradeAttempt.newSuggestColor = currentSuggestColor;
                    }
                }
                
                j++;
            }
            
            // ถ้าสิ้นสุดข้อมูลหรือครบ MAX_TRADES แล้วยังไม่ชนะ
            if (!isWin) {
                tradeResult.result = 'No Win';
                tradeResult.lossBeforeWin = lossCount;
                tradeResult.idleBeforeLoss = idleCount;
                idleBeforeLoss += idleCount;
                
                // ระบุสาเหตุที่ไม่ชนะ
                if (actualTradeCount >= MAX_TRADES) {
                    tradeResult.stopReason = `Reached max ${MAX_TRADES} trades`;
                } else {
                    tradeResult.stopReason = "Ran out of data";
                }
            }
            
            tradeResult.totalEvents = tradeResult.trades.length;
            tradeResult.actualTradeCount = actualTradeCount;
            tradeResult.idleCount = idleCount;
            totalIdleCount += idleCount;
            
            // สรุปผลการเงิน
            tradeResult.financialSummary.endBalance = currentBalance;
            tradeResult.financialSummary.profit = currentBalance - tradeResult.financialSummary.startBalance;
            totalProfit += tradeResult.financialSummary.profit;
            
            tradeResults.push(tradeResult);
        }
    }
    
    return {
        tradeResults,
        tradeSummary: {
            initialBalance,
            finalBalance: currentBalance,
            totalProfit,
            maxDrawdown,
            totalIdleCount,
            idleBeforeWin,
            idleBeforeLoss,
            returnOnInvestment: (totalProfit / initialBalance) * 100
        }
    };
}

// คำนวณผลการเทรด
const result = calculateTrades(data);
const tradeResults = result.tradeResults;
const tradeSummary = result.tradeSummary;

// แสดงผลลัพธ์
//console.log(JSON.stringify(tradeResults, null, 2));

// สรุปผล
let totalEvents = 0;        // รวมทั้ง Trade และ Idle
let totalWins = 0;
let totalLosses = 0;
let totalMaxTradesReached = 0;
let totalActualTrades = 0;  // ไม่รวม Idle
let totalIdleEvents = 0;
let totalStakeAmount = 0;
let totalProfitAmount = 0;

tradeResults.forEach(result => {
    if (result.result === 'Win') {
        totalWins++;
    } else {
        totalLosses++;
        if (result.stopReason && result.stopReason.includes("max")) {
            totalMaxTradesReached++;
        }
    }
    
    totalEvents += result.totalEvents;
    totalActualTrades += result.actualTradeCount;
    totalIdleEvents += result.idleCount;
    
    // คำนวณสถิติการเงิน
    result.financialSummary.stakes.forEach(trade => {
        totalStakeAmount += trade.stake;
        totalProfitAmount += trade.profitLoss;
    });
});
document.getElementById("tradeResult").innerHTML = JSON.stringify(tradeResults, null, 2);
 
console.log('สรุปผล:');
console.log(`จำนวนจุดเริ่มเทรดทั้งหมด: ${tradeResults.length}`);
console.log(`จำนวนเหตุการณ์ทั้งหมด (รวม Idle): ${totalEvents}`);
console.log(`จำนวนการเทรดจริง (ไม่รวม Idle): ${totalActualTrades}`);
console.log(`จำนวน Idle ทั้งหมด: ${totalIdleEvents}`);
console.log(`เปอร์เซ็นต์ Idle: ${(totalIdleEvents / totalEvents * 100).toFixed(2)}%`);
console.log(`จำนวนการชนะ: ${totalWins}`);
console.log(`จำนวนการแพ้: ${totalLosses}`);
console.log(`จำนวนที่เทรดครบ ${MAX_TRADES} ครั้งแล้วไม่ชนะ: ${totalMaxTradesReached}`);
console.log(`Win Rate (ทั้งหมด): ${(totalWins / tradeResults.length * 100).toFixed(2)}%`);
//console.log(`Win Rate (เฉพาะที่เทรดครบ ${MAX_TRADES} ครั้งหรือชนะก่อนหน้า): //${winRateByMaxTrades.toFixed(2)}%`);
/*
console.log('\nสถิติ Idle:');
console.log(`จำนวน Idle ทั้งหมด: ${idleStats.totalIdleCount}`);
console.log(`จำนวน Idle ก่อนชนะ: ${idleStats.idleBeforeWin}`);
console.log(`จำนวน Idle ก่อนแพ้หรือครบ ${MAX_TRADES} ครั้ง: ${idleStats.idleBeforeLoss}`);
*/

// สถิติเพิ่มเติมเกี่ยวกับการชนะในแต่ละครั้งของการเทรด
const winByTradeNumber = {};
tradeResults.forEach(result => {
    if (result.result === 'Win') {
        const tradeNumber = result.winOnTradeNumber;
        winByTradeNumber[tradeNumber] = (winByTradeNumber[tradeNumber] || 0) + 1;
    }
});

console.log('\nสถิติการชนะตามจำนวนครั้งการเทรด:');
for (let i = 1; i <= MAX_TRADES; i++) {
    const wins = winByTradeNumber[i] || 0;
    console.log(`ชนะในการเทรดครั้งที่ ${i}: ${wins} ครั้ง (${(wins / totalWins * 100).toFixed(2)}% ของการชนะทั้งหมด)`);
} 

 console.log('Balance',totalProfitAmount)


</script>
  
  


 </body>
</html>
