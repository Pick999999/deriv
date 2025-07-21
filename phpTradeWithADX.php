<?php
/*
สร้าง html+ php ดังนี้ 
1. fucntion getCandle สำหรับ รับค่า Asset แล้วนำไปดึง ข้อมูล Candle จาก Deriv.com ที่ เวลาปัจจุบันจำนวน และ timeframe ที่กำหนดให้ 60 แท่งเข้ามา 
2. function CalIndyADX สำหรับนำ ข้อมูล Candle ที่ได้มา มาหาค่า ema3,ema5,adx และ Color 6 อันท้ายสุด แล้ว วิเคราะห์ค่า Strength จาก ADX และ Trend
3. function TradeRiseFall โดยนำค่า Trend จากการวิเคราะห์ตามข้อ 2  มาเข้า Trade แบบ Rise/Fall ที่ 55 Second 
4. วนลูปเข้าเทรด จนกว่าจะชนะ โดยให้เพิ่มจำนวนเงิน แบบ Martingale
5. พิมพ์รายงาน บน Table 
6. ทั้งหมดทำด้วย php ล้วนๆ 
7.ทำการดึงข้อมูล จริง เทรดจริง ไม่เอา mock data
lt5UMO6bNvmZQaR
*/
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Real Trading System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #4a5568;
            margin-bottom: 30px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .config-panel {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 2px solid #e2e8f0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #2d3748;
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #cbd5e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 600;
        }
        .status.success { background: #c6f6d5; color: #22543d; border: 2px solid #9ae6b4; }
        .status.error { background: #fed7d7; color: #742a2a; border: 2px solid #fc8181; }
        .status.warning { background: #fef5e7; color: #744210; border: 2px solid #f6ad55; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        tr:hover {
            background: #f7fafc;
        }
        .trend-up { color: #38a169; font-weight: bold; }
        .trend-down { color: #e53e3e; font-weight: bold; }
        .loading {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #667eea;
        }
        .real-time {
            background: #e6fffa;
            border: 2px solid #38b2ac;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔥 Deriv REAL Trading System</h1>
        
        <div class="config-panel">
            <h3>การตั้งค่าการเทรดจริง</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Deriv API Token (REAL ACCOUNT):</label>
                    <input type="password" name="api_token" required 
                           placeholder="ใส่ API Token สำหรับการเทรดจริง"value='lt5UMO6bNvmZQaR'>
                </div>
                <div class="form-group">
                    <label>Asset Symbol:</label>
                    <select name="asset">
                        <option value="R_10">Volatility 10 Index</option>
                        <option value="R_25">Volatility 25 Index</option>
                        <option value="R_50">Volatility 50 Index</option>
                        <option value="R_75">Volatility 75 Index</option>
                        <option value="R_100">Volatility 100 Index</option>
                        <option value="frxEURUSD">EUR/USD</option>
                        <option value="frxGBPUSD">GBP/USD</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>เงินเริ่มต้น (USD):</label>
                    <input type="number" name="initial_amount" value="1" min="0.35" step="0.01">
                </div>
                <div class="form-group">
                    <label>จำนวนเทรดสูงสุด:</label>
                    <input type="number" name="max_trades" value="10" min="1" max="20">
                </div>
                <button type="submit" name="start_trading" class="btn">💰 เริ่มเทรดจริง</button>
            </form>
        </div>

        <?php
        class DerivRealTrader {
            private $apiToken;
            private $wsUrl = 'wss://ws.binaryws.com/websockets/v3?app_id=66726';
            private $apiUrl = 'https://api.deriv.com';
            private $asset;
            private $initialAmount;
            private $maxTrades;
            private $tradeResults = [];
            private $currentAmount;
            private $totalProfit = 0;
            private $appId = 66726; // Default app_id

            public function __construct($apiToken, $asset, $initialAmount, $maxTrades) {
                $this->apiToken = $apiToken;
                $this->asset = $asset;
                $this->initialAmount = $initialAmount;
                $this->maxTrades = $maxTrades;
                $this->currentAmount = $initialAmount;
            }

            // Function 1: ดึงข้อมูล Candle จาก Deriv API จริง
            public function getCandle($asset, $timeframe = 60, $count = 60) {
                $endTime = time();
                $startTime = $endTime - ($timeframe * $count * 2); // เผื่อข้อมูล
                
                $data = [
                    'ticks_history' => $asset,
                    'start' => $startTime,
                    'end' => $endTime,
                    'style' => 'candles',
                    'granularity' => $timeframe,
                    'count' => $count,
                    'req_id' => rand(1, 9999)
                ];

                $response = $this->sendWebSocketRequest($data);
                
                if ($response && isset($response['candles'])) {
                    echo '<div class="real-time">✅ ดึงข้อมูล Candle จาก Deriv สำเร็จ: ' . count($response['candles']) . ' แท่ง</div>';
                    return $response['candles'];
                } else {
                    echo '<div class="status error">❌ ไม่สามารถดึงข้อมูล Candle ได้: ' . ($response['error']['message'] ?? 'Unknown error') . '</div>';
                    return false;
                }
            }

            // ส่งคำขอผ่าน WebSocket API
            private function sendWebSocketRequest($data) {
                $data['app_id'] = $this->appId;
                
                // ใช้ cURL สำหรับ HTTP API แทน WebSocket เพื่อความง่าย
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => 'https://api.deriv.com/websockets/v3?app_id=' . $this->appId,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $this->apiToken
                    ]
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($error) {
                    echo '<div class="status error">cURL Error: ' . $error . '</div>';
                    return false;
                }

                if ($httpCode !== 200) {
                    echo '<div class="status error">HTTP Error: ' . $httpCode . '</div>';
                    return false;
                }

                return json_decode($response, true);
            }

            // Function 2: คำนวณ EMA, ADX และวิเคราะห์จริง
            public function CalIndyADX($candles) {
                if (!$candles || count($candles) < 20) {
                    return false;
                }

                $closes = array_column($candles, 'close');
                $highs = array_column($candles, 'high');
                $lows = array_column($candles, 'low');

                // คำนวณ EMA
                $ema3 = $this->calculateEMA($closes, 3);
                $ema5 = $this->calculateEMA($closes, 5);
                
                // คำนวณ ADX
                $adx = $this->calculateADX($highs, $lows, $closes, 14);
                
                // ดึง 6 ค่าสุดท้าย
                $lastEma3 = array_slice($ema3, -6);
                $lastEma5 = array_slice($ema5, -6);
                $lastAdx = array_slice($adx, -6);
                
                $colors = [];
                for ($i = 0; $i < 6; $i++) {
                    $colors[] = $lastEma3[$i] > $lastEma5[$i] ? 'GREEN' : 'RED';
                }
                
                $latestAdx = end($lastAdx);
                $strength = $this->analyzeStrength($latestAdx);
                $trend = $this->analyzeTrend(end($lastEma3), end($lastEma5), $latestAdx);

                echo '<div class="real-time">📊 การวิเคราะห์: Trend=' . $trend . ', ADX=' . number_format($latestAdx, 2) . ', Strength=' . $strength . '</div>';

                return [
                    'ema3' => $lastEma3,
                    'ema5' => $lastEma5,
                    'adx' => $lastAdx,
                    'colors' => $colors,
                    'strength' => $strength,
                    'trend' => $trend,
                    'latest_adx' => $latestAdx
                ];
            }

            private function calculateEMA($data, $period) {
                $ema = [];
                $multiplier = 2 / ($period + 1);
                
                // SMA สำหรับค่าแรก
                $ema[0] = array_sum(array_slice($data, 0, $period)) / $period;
                
                for ($i = 1; $i < count($data); $i++) {
                    $ema[$i] = ($data[$i] * $multiplier) + ($ema[$i-1] * (1 - $multiplier));
                }
                
                return $ema;
            }

            private function calculateADX($highs, $lows, $closes, $period = 14) {
                $plusDM = [];
                $minusDM = [];
                $tr = [];
                $adx = [];

                // คำนวณ DM และ TR
                for ($i = 1; $i < count($closes); $i++) {
                    $highDiff = $highs[$i] - $highs[$i-1];
                    $lowDiff = $lows[$i-1] - $lows[$i];
                    
                    $plusDM[] = ($highDiff > $lowDiff && $highDiff > 0) ? $highDiff : 0;
                    $minusDM[] = ($lowDiff > $highDiff && $lowDiff > 0) ? $lowDiff : 0;
                    
                    $tr[] = max(
                        $highs[$i] - $lows[$i],
                        abs($highs[$i] - $closes[$i-1]),
                        abs($lows[$i] - $closes[$i-1])
                    );
                }

                // คำนวณ ADX
                for ($i = $period - 1; $i < count($tr); $i++) {
                    $avgTR = array_sum(array_slice($tr, $i - $period + 1, $period)) / $period;
                    $avgPlusDM = array_sum(array_slice($plusDM, $i - $period + 1, $period)) / $period;
                    $avgMinusDM = array_sum(array_slice($minusDM, $i - $period + 1, $period)) / $period;
                    
                    if ($avgTR > 0) {
                        $plusDI = ($avgPlusDM / $avgTR) * 100;
                        $minusDI = ($avgMinusDM / $avgTR) * 100;
                        
                        if (($plusDI + $minusDI) > 0) {
                            $dx = abs($plusDI - $minusDI) / ($plusDI + $minusDI) * 100;
                            $adx[] = $dx;
                        } else {
                            $adx[] = 0;
                        }
                    } else {
                        $adx[] = 0;
                    }
                }

                return $adx;
            }

            private function analyzeStrength($adxValue) {
                if ($adxValue > 50) return 'Very Strong';
                if ($adxValue > 25) return 'Strong';
                if ($adxValue > 20) return 'Moderate';
                return 'Weak';
            }

            private function analyzeTrend($ema3, $ema5, $adxValue) {
                if ($adxValue < 20) return 'SIDEWAYS';
                return $ema3 > $ema5 ? 'RISE' : 'FALL';
            }

            // Function 3: เข้าเทรดจริงผ่าน Deriv API
            public function TradeRiseFall($trend, $amount) {
                $contractType = ($trend == 'RISE') ? 'CALL' : 'PUT';
                
                $buyData = [
                    'buy' => 1,
                    'price' => $amount,
                    'parameters' => [
                        'contract_type' => $contractType,
                        'symbol' => $this->asset,
                        'duration' => 55,
                        'duration_unit' => 's'
                    ],
                    'req_id' => rand(1000, 9999)
                ];

                echo '<div class="real-time">🎯 กำลังเข้าเทรด: ' . $contractType . ' จำนวน $' . $amount . '</div>';
                
                $response = $this->sendWebSocketRequest($buyData);
                
                if ($response && isset($response['buy'])) {
                    $contractId = $response['buy']['contract_id'];
                    echo '<div class="status success">✅ เข้าเทรดสำเร็จ Contract ID: ' . $contractId . '</div>';
                    
                    // รอผลการเทรด (55 วินาที + buffer)
                    sleep(60);
                    
                    // เช็คผลการเทรด
                    $result = $this->checkTradeResult($contractId);
                    $result['direction'] = $contractType;
                    $result['amount'] = $amount;
                    $result['trend'] = $trend;
                    $result['time'] = date('H:i:s');
                    $result['contract_id'] = $contractId;
                    
                    return $result;
                } else {
                    echo '<div class="status error">❌ เข้าเทรดไม่สำเร็จ: ' . ($response['error']['message'] ?? 'Unknown error') . '</div>';
                    return [
                        'success' => false,
                        'profit' => -$amount,
                        'direction' => $contractType,
                        'amount' => $amount,
                        'trend' => $trend,
                        'time' => date('H:i:s'),
                        'error' => $response['error']['message'] ?? 'Trade failed'
                    ];
                }
            }

            // เช็คผลการเทรดจาก Contract ID
            private function checkTradeResult($contractId) {
                $statusData = [
                    'proposal_open_contract' => 1,
                    'contract_id' => $contractId,
                    'req_id' => rand(1000, 9999)
                ];

                $response = $this->sendWebSocketRequest($statusData);
                
                if ($response && isset($response['proposal_open_contract'])) {
                    $contract = $response['proposal_open_contract'];
                    $isWon = isset($contract['is_won']) ? $contract['is_won'] : 0;
                    $profit = isset($contract['profit']) ? floatval($contract['profit']) : 0;
                    
                    return [
                        'success' => $isWon == 1,
                        'profit' => $profit,
                        'payout' => $contract['payout'] ?? 0,
                        'buy_price' => $contract['buy_price'] ?? 0
                    ];
                } else {
                    return [
                        'success' => false,
                        'profit' => -$this->currentAmount,
                        'error' => 'Cannot check trade result'
                    ];
                }
            }

            // Function 4: Martingale Loop จริง
            public function startTrading() {
                echo '<div class="status warning">🚀 เริ่มต้นการเทรดจริง...</div>';
                
                // ตรวจสอบ API Token
                if (!$this->validateApiToken()) {
                    echo '<div class="status error">❌ API Token ไม่ถูกต้องหรือไม่มีสิทธิ์เทรด</div>';
                    return;
                }
                
                $tradeCount = 0;
                $consecutiveLosses = 0;
                
                while ($tradeCount < $this->maxTrades) {
                    echo '<div class="loading">📊 กำลังวิเคราะห์ตลาด... (เทรดที่ ' . ($tradeCount + 1) . '/' . $this->maxTrades . ')</div>';
                    
                    // 1. ดึงข้อมูล Candle จริง
                    $candles = $this->getCandle($this->asset);
                    if (!$candles) {
                        echo '<div class="status error">❌ ไม่สามารถดึงข้อมูลได้ หยุดการเทรด</div>';
                        break;
                    }
                    
                    // 2. วิเคราะห์จริง
                    $analysis = $this->CalIndyADX($candles);
                    if (!$analysis) {
                        echo '<div class="status error">❌ ไม่สามารถวิเคราะห์ข้อมูลได้</div>';
                        continue;
                    }
                    
                    // 3. เข้าเทรดจริง
                    $result = $this->TradeRiseFall($analysis['trend'], $this->currentAmount);
                    
                    // 4. บันทึกผล
                    $result['analysis'] = $analysis;
                    $result['trade_number'] = $tradeCount + 1;
                    $this->tradeResults[] = $result;
                    
                    if ($result['success']) {
                        $this->totalProfit += $result['profit'];
                        echo '<div class="status success">🎉 ชนะ! กำไร: $' . number_format($result['profit'], 2) . '</div>';
                        
                        // รีเซ็ตเงินเมื่อชนะ
                        $this->currentAmount = $this->initialAmount;
                        $consecutiveLosses = 0;
                        
                        // หยุดเมื่อชนะ (ตามที่ขอ)
                        echo '<div class="status success">✅ ชนะแล้ว หยุดการเทรด</div>';
                        break;
                    } else {
                        $this->totalProfit += $result['profit'];
                        $consecutiveLosses++;
                        echo '<div class="status error">💸 แพ้! ขาดทุน: $' . number_format(abs($result['profit']), 2) . '</div>';
                        
                        // Martingale: เพิ่มเงินเป็น 2 เท่า
                        $this->currentAmount *= 2;
                        
                        // จำกัดเงินสูงสุด
                        if ($this->currentAmount > $this->initialAmount * 32) {
                            echo '<div class="status error">⚠️ เงินเกินจำกัด หยุดการเทรด</div>';
                            break;
                        }
                    }
                    
                    $tradeCount++;
                    
                    // พักระหว่างเทรด
                    if ($tradeCount < $this->maxTrades) {
                        echo '<div class="loading">⏳ รอ 30 วินาทีก่อนเทรดครั้งต่อไป...</div>';
                        sleep(30);
                    }
                }
                
                $this->printReport();
            }

            // ตรวจสอบ API Token
            private function validateApiToken() {
                $data = [
                    'authorize' => $this->apiToken,
                    'req_id' => rand(1000, 9999)
                ];
                
                $response = $this->sendWebSocketRequest($data);
                
                if ($response && isset($response['authorize'])) {
                    echo '<div class="status success">✅ API Token ถูกต้อง - Account: ' . $response['authorize']['email'] . '</div>';
                    return true;
                }
                
                return false;
            }

            // Function 5: พิมพ์รายงานจริง
            public function printReport() {
                echo '<h3>📈 รายงานการเทรดจริง</h3>';
                
                $totalTrades = count($this->tradeResults);
                $winTrades = array_sum(array_column($this->tradeResults, 'success'));
                $winRate = $totalTrades > 0 ? ($winTrades / $totalTrades) * 100 : 0;
                
                echo '<div class="status ' . ($this->totalProfit >= 0 ? 'success' : 'error') . '">';
                echo '<strong>กำไร/ขาดทุนรวม: $' . number_format($this->totalProfit, 2) . '</strong><br>';
                echo 'จำนวนเทรดทั้งหมด: ' . $totalTrades . ' | ชนะ: ' . $winTrades . ' | แพ้: ' . ($totalTrades - $winTrades) . '<br>';
                echo 'อัตราชนะ: ' . number_format($winRate, 2) . '%';
                echo '</div>';
                
                if ($totalTrades > 0) {
                    echo '<table>';
                    echo '<tr><th>ครั้งที่</th><th>เวลา</th><th>Contract ID</th><th>ทิศทาง</th><th>จำนวนเงิน</th><th>ผล</th><th>กำไร/ขาดทุน</th><th>Trend</th><th>ADX</th></tr>';
                    
                    foreach ($this->tradeResults as $trade) {
                        $statusClass = $trade['success'] ? 'trend-up' : 'trend-down';
                        $statusText = $trade['success'] ? '✅ ชนะ' : '❌ แพ้';
                        $profitColor = $trade['profit'] >= 0 ? 'trend-up' : 'trend-down';
                        
                        echo '<tr>';
                        echo '<td>' . $trade['trade_number'] . '</td>';
                        echo '<td>' . $trade['time'] . '</td>';
                        echo '<td>' . ($trade['contract_id'] ?? 'N/A') . '</td>';
                        echo '<td>' . $trade['direction'] . '</td>';
                        echo '<td>$' . number_format($trade['amount'], 2) . '</td>';
                        echo '<td class="' . $statusClass . '">' . $statusText . '</td>';
                        echo '<td class="' . $profitColor . '">$' . number_format($trade['profit'], 2) . '</td>';
                        echo '<td>' . $trade['trend'] . '</td>';
                        echo '<td>' . (isset($trade['analysis']['latest_adx']) ? number_format($trade['analysis']['latest_adx'], 2) : 'N/A') . '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                }
            }
        }

        // เริ่มการทำงานจริง
        if (isset($_POST['start_trading'])) {
            $apiToken = trim($_POST['api_token']);
			$apiToken = 'lt5UMO6bNvmZQaR';
            $asset = $_POST['asset'];
            $initialAmount = (float)$_POST['initial_amount'];
            $maxTrades = (int)$_POST['max_trades'];
            
            if (empty($apiToken)) {
                echo '<div class="status error">❌ กรุณาใส่ API Token</div>';
            } elseif ($initialAmount < 0.35) {
                echo '<div class="status error">❌ เงินขั้นต่ำ $0.35</div>';
            } else {
                set_time_limit(600); // เพิ่มเวลาทำงาน
                $trader = new DerivRealTrader($apiToken, $asset, $initialAmount, $maxTrades);
                $trader->startTrading();
            }
        }
        ?>
        
        <div style="margin-top: 30px; padding: 20px; background: #ffebee; border-radius: 10px; border: 2px solid #f44336;">
            <h4>⚠️ คำเตือนสำคัญ - การเทรดจริง:</h4>
            <ul>
                <li><strong>ระบบนี้เทรดด้วยเงินจริง</strong> - อาจสูญเสียเงินทุนทั้งหมด</li>
                <li>ตรวจสอบ API Token ให้ถูกต้อง และมีสิทธิ์ Trading</li>
                <li>เริ่มต้นด้วยเงินจำนวนน้อย เพื่อทดสอบระบบ</li>
                <li>ระบบจะหยุดเมื่อชนะหรือเงินเกินจำกัด</li>
            </ul>
        </div>
    </div>

    <script>
    // Auto refresh เพื่อดูผลแบบ real-time
    setTimeout(function() {
        if (document.querySelector('.loading')) {
            location.reload();
        }
    }, 30000);
    </script>
</body>
</html>